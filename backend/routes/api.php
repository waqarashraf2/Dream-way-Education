<?php

use App\Http\Controllers\Api\ContactInquiryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Dream Way Education API Routes
|
*/

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'Dream Way Education API',
        'timestamp' => now()->toIso8601String(),
    ]);
});

// Contact Form submission endpoint
Route::post('/contact', [ContactInquiryController::class, 'store']);

// Inquiries Management & Stats
Route::prefix('contact-inquiries')->group(function () {
    Route::get('/', [ContactInquiryController::class, 'index']);
    Route::get('/stats', [ContactInquiryController::class, 'stats']);
    Route::get('/{id}', [ContactInquiryController::class, 'show']);
    Route::patch('/{id}/status', [ContactInquiryController::class, 'updateStatus']);
    Route::delete('/{id}', [ContactInquiryController::class, 'destroy']);
});
