<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUpcomingEventRequest extends FormRequest
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
    Log::info('Validation data: ', $this->all());

    return [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'start_time' => 'required|date_format:H:i:s',
        'end_time' => 'required|date_format:H:i:s|after:start_time',
        'image' => 'nullable|image|max:512000',
    ];
}

    public function messages(): array
    {
        return [
            'title.required' => 'Judul event harus diisi.',
            'title.string' => 'Judul event harus berupa teks.',
            'title.max' => 'Judul event tidak boleh lebih dari 255 karakter.',
            'content.string' => 'Konten event harus berupa teks.',
            'start_time.required' => 'Waktu mulai event harus diisi.',
            'start_time.date_format' => 'Format waktu mulai harus HH:mm:ss.',
            'end_time.required' => 'Waktu selesai event harus diisi.',
            'end_time.date_format' => 'Format waktu selesai harus HH:mm:ss.',
            'end_time.after' => 'Waktu selesai harus setelah waktu mulai.',
            'image.image' => 'File yang diunggah harus berupa gambar.',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari 1MB.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
        'status' => 400,
        'message' => 'Validasi gagal!',
        'data'    => $validator->errors()
        ], 400));
    }
}
