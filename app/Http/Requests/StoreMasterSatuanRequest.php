<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMasterSatuanRequest extends FormRequest
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
            'nama_satuan' => 'required|string|max:50|unique:satuans,nama_satuan',
            'keterangan' => 'nullable|string|max:255',
            'is_aktif' => 'nullable|boolean',
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
            'nama_satuan.required' => 'Nama satuan wajib diisi.',
            'nama_satuan.unique' => 'Satuan ini sudah terdaftar dalam sistem.',
            'nama_satuan.max' => 'Nama satuan maksimal 50 karakter.',
        ];
    }
}
