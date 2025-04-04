<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Update Booking Status Request
 * 
 * Handles validation for updating booking status
 */
class UpdateBookingStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request
     */
    public function rules(): array
    {
        return [
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status yang dipilih tidak valid. Pilihan yang tersedia: pending, confirmed, completed, cancelled.',
        ];
    }

    /**
     * Get custom attributes for validator errors
     */
    public function attributes(): array
    {
        return [
            'status' => 'status booking',
        ];
    }
}
