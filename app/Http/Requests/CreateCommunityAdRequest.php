<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateCommunityAdRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'price' => 'required',
            'image' => 'required|image|max:512000',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul iklan harus diisi.',
            'title.string' => 'Judul iklan harus berupa teks.',
            'title.max' => 'Judul iklan tidak boleh lebih dari 255 karakter.',
            'content.required' => 'Konten iklan harus diisi.',
            'content.string' => 'Konten iklan harus berupa teks.',
            'price.require' => 'Harga Iklan harus diisi.',
            'image.required' => 'Gambar iklan harus diunggah.',
            'image.image' => 'File yang diunggah harus berupa gambar.',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari 1MB.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
        'status' => 400,
        'message' => 'Validasi gagal !',
        'data'    => $validator->errors()
        ], 400));
    }
}
