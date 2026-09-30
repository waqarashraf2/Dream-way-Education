<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreContactInquiryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['required', 'string', 'min:5', 'max:50'],
            'country' => ['nullable', 'string', 'max:100'],
            'service' => ['nullable', 'string', 'max:150'],
            'message' => ['nullable', 'string', 'max:5000'],
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please provide your full name.',
            'name.min' => 'Your name must be at least 2 characters.',
            'email.required' => 'Please provide a valid email address.',
            'email.email' => 'Please enter a valid email format (e.g. name@example.com).',
            'phone.required' => 'Phone or WhatsApp number is required.',
            'phone.min' => 'Phone number must contain at least 5 digits.',
            'message.max' => 'Message cannot exceed 5000 characters.',
        ];
    }

    /**
     * Handle a failed validation attempt and return a structured JSON response.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Please correct the errors in the form.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
