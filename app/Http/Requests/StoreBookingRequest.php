<?php

namespace App\Http\Requests;

use App\Enums\BookingStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Store Booking Request
 * 
 * Handles validation for booking creation following Single Responsibility Principle
 */
class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization logic can be moved to policy if needed
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'date' => ['required', 'date', 'after:today'],
            'time' => ['required', 'date_format:H:i'],
            'package_type' => ['required', 'string', 'in:basic,premium,deluxe'],
            'participants' => ['required', 'integer', 'min:1', 'max:50'],
            'special_requests' => ['nullable', 'string', 'max:1000'],
            'terms_accepted' => ['required', 'accepted'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'date.required' => 'Tanggal booking wajib dipilih.',
            'date.after' => 'Tanggal booking harus setelah hari ini.',
            'time.required' => 'Waktu booking wajib dipilih.',
            'time.date_format' => 'Format waktu tidak valid.',
            'package_type.required' => 'Paket booking wajib dipilih.',
            'package_type.in' => 'Paket yang dipilih tidak valid.',
            'participants.required' => 'Jumlah peserta wajib diisi.',
            'participants.min' => 'Minimal 1 peserta.',
            'participants.max' => 'Maksimal 50 peserta.',
            'special_requests.max' => 'Permintaan khusus maksimal 1000 karakter.',
            'terms_accepted.accepted' => 'Anda harus menyetujui syarat dan ketentuan.',
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama lengkap',
            'email' => 'email',
            'phone' => 'nomor telepon',
            'date' => 'tanggal booking',
            'time' => 'waktu booking',
            'package_type' => 'paket booking',
            'participants' => 'jumlah peserta',
            'special_requests' => 'permintaan khusus',
            'terms_accepted' => 'syarat dan ketentuan',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean phone number
        if ($this->has('phone')) {
            $phone = preg_replace('/[^0-9+]/', '', $this->phone);
            $this->merge(['phone' => $phone]);
        }

        // Convert participants to integer
        if ($this->has('participants')) {
            $this->merge(['participants' => (int) $this->participants]);
        }
    }

    /**
     * Get the booking data ready for service layer
     */
    public function getBookingData(): array
    {
        return $this->only([
            'name',
            'email', 
            'phone',
            'date',
            'time',
            'package_type',
            'participants',
            'special_requests'
        ]);
    }
}
