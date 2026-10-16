<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMasterUkmRequest extends FormRequest
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
        $ukmId = $this->route('id');

        return [
            'kode' => ['required', 'string', 'max:20', 'alpha_dash', Rule::unique('ukms', 'kode')->ignore($ukmId)],
            'nama_ukm' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'deskripsi' => 'nullable|string|max:1000',
            'pembina_user_id' => 'nullable|exists:users,id',
            'pembina_nama_manual' => 'nullable|string|max:255',
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
            'kode.unique' => 'Kode UKM sudah digunakan oleh UKM lain.',
            'kode.alpha_dash' => 'Kode UKM hanya boleh berisi huruf, angka, dan tanda strip/garis bawah.',
        ];
    }
}
