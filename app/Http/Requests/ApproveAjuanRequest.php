<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveAjuanRequest extends FormRequest
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
            'status' => 'required|in:disetujui,revisi,ditolak',
            'catatan_kemahasiswaan' => $this->input('status') === 'revisi' ? 'required|string|max:1000' : 'nullable|string|max:1000',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'catatan_kemahasiswaan.required' => 'Catatan revisi wajib diisi agar mahasiswa mengetahui bagian apa yang perlu diperbaiki.',
        ];
    }
}
