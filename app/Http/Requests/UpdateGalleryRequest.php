<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateGalleryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        \Log::info('Validation data: ', $this->all());
        return [
            'photo_url' => 'nullable|image|max:512000',
            'caption' => 'required|string|max:255'
        ];
    }

    public function messages(): array
    {
        return [
            'photo_url.image' => 'File yang diunggah harus berupa gambar.',
            'photo_url.max' => 'Ukuran gambar tidak boleh lebih dari 1MB.',
            'caption.required' => 'Caption harus diisi.',
            'caption.string' => 'Caption harus berupa teks.',
            'caption.max' => 'Caption tidak boleh lebih dari 255 karakter.'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 400,
            'message' => 'Validasi gagal!',
            'data' => $validator->errors()
        ], 400));
    }
}
