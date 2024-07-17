<?php

namespace App\Http\Requests;

use GPBMetadata\Google\Api\Http;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateGalleryRequest extends FormRequest
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
        'photo_url' => 'required|image|max:5120', 
        'caption' => 'required|string|max:255',
        // 'creator_id' => 'required|uuid',
        ];
    }
    
    public function messages(): array
    {
        return [
            'photo_url.required' => 'Gambar foto harus diunggah',
            'photo_url.image' => 'File yang diunggah harus berupa gambar.',
            'photo_url.max' => 'Ukuran gambar tidak boleh lebih dari 5MB.',
            'caption.required' => 'Caption harus diisi.',
            'caption.string' => 'Caption harus berupa teks.',
            'caption.max' => 'Caption tidak boleh lebih dari 255 karakter.',

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