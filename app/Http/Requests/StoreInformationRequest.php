<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Store Information Request
 * 
 * Handles validation for creating new information entries
 */
class StoreInformationRequest extends FormRequest
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
            'title' => 'required|string|max:255|unique:information,title',
            'description' => 'nullable|string|max:500',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published',
            'type' => 'required|in:news,information',
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul wajib diisi.',
            'title.unique' => 'Judul sudah digunakan, silakan pilih judul lain.',
            'title.max' => 'Judul maksimal 255 karakter.',
            'description.max' => 'Deskripsi maksimal 500 karakter.',
            'content.required' => 'Konten wajib diisi.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar yang diizinkan: JPEG, PNG, JPG, GIF.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
            'type.required' => 'Tipe wajib dipilih.',
            'type.in' => 'Tipe tidak valid.',
        ];
    }

    /**
     * Prepare the data for validation
     */
    protected function prepareForValidation(): void
    {
        // Add any data preparation logic here
        if ($this->has('title')) {
            $this->merge([
                'title' => trim($this->title),
            ]);
        }
    }
}
