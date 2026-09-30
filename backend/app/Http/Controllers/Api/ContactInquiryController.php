<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactInquiryRequest;
use App\Models\ContactInquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class ContactInquiryController extends Controller
{
    /**
     * Store a newly created contact inquiry in storage.
     *
     * @param StoreContactInquiryRequest $request
     * @return JsonResponse
     */
    public function store(StoreContactInquiryRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $validated['ip_address'] = $request->ip();
            $validated['user_agent'] = $request->userAgent();
            $validated['status'] = 'unread';

            $inquiry = ContactInquiry::create($validated);

            Log::info("New contact inquiry received from {$inquiry->name} ({$inquiry->email}) [ID: {$inquiry->id}]");

            return response()->json([
                'success' => true,
                'message' => 'Thank you for contacting Dream Way Education! Our counselors will review your profile and get back to you shortly.',
                'data' => [
                    'id' => $inquiry->id,
                    'name' => $inquiry->name,
                    'email' => $inquiry->email,
                    'phone' => $inquiry->phone,
                    'country' => $inquiry->country,
                    'service' => $inquiry->service,
                    'created_at' => $inquiry->created_at->toIso8601String(),
                ],
            ], 201);
        } catch (Throwable $e) {
            Log::error('Failed to save contact inquiry: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting your inquiry. Please try again or reach us via WhatsApp.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Display a listing of inquiries with optional search and status filtering.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $status = $request->query('status');
        $search = $request->query('search');
        $perPage = min((int) ($request->query('per_page', 15)), 100);

        $inquiries = ContactInquiry::query()
            ->status($status)
            ->search($search)
            ->latest('id')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $inquiries->items(),
            'pagination' => [
                'total' => $inquiries->total(),
                'per_page' => $inquiries->perPage(),
                'current_page' => $inquiries->currentPage(),
                'last_page' => $inquiries->lastPage(),
            ],
        ]);
    }

    /**
     * Display the specified contact inquiry.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $inquiry = ContactInquiry::find($id);

        if (!$inquiry) {
            return response()->json([
                'success' => false,
                'message' => 'Contact inquiry not found.',
            ], 404);
        }

        // Auto mark as read if it was unread
        if ($inquiry->status === 'unread') {
            $inquiry->update(['status' => 'read']);
        }

        return response()->json([
            'success' => true,
            'data' => $inquiry,
        ]);
    }

    /**
     * Update status of an inquiry (e.g. unread, read, in_progress, contacted, closed).
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|string|in:unread,read,in_progress,contacted,closed',
        ]);

        $inquiry = ContactInquiry::find($id);

        if (!$inquiry) {
            return response()->json([
                'success' => false,
                'message' => 'Contact inquiry not found.',
            ], 404);
        }

        $inquiry->update(['status' => $request->input('status')]);

        return response()->json([
            'success' => true,
            'message' => "Inquiry status updated to '{$inquiry->status}'.",
            'data' => $inquiry,
        ]);
    }

    /**
     * Delete an inquiry.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $inquiry = ContactInquiry::find($id);

        if (!$inquiry) {
            return response()->json([
                'success' => false,
                'message' => 'Contact inquiry not found.',
            ], 404);
        }

        $inquiry->delete();

        return response()->json([
            'success' => true,
            'message' => 'Inquiry deleted successfully.',
        ]);
    }

    /**
     * Get statistics summary for dashboard / reporting.
     *
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total' => ContactInquiry::count(),
            'unread' => ContactInquiry::where('status', 'unread')->count(),
            'read' => ContactInquiry::where('status', 'read')->count(),
            'in_progress' => ContactInquiry::where('status', 'in_progress')->count(),
            'contacted' => ContactInquiry::where('status', 'contacted')->count(),
            'closed' => ContactInquiry::where('status', 'closed')->count(),
            'recent_today' => ContactInquiry::whereDate('created_at', today())->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
