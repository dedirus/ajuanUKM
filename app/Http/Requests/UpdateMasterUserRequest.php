<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMasterUserRequest extends FormRequest
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
        $userId = $this->route('id');

        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:mahasiswa,pembina,kemahasiswaan',
            'ukm_id' => [
                'nullable',
                Rule::requiredIf(fn () => in_array($this->role, ['mahasiswa', 'pembina'])),
                'exists:ukms,id',
            ],
            'nim_nip' => 'nullable|string|max:50',
            'no_hp' => 'nullable|string|max:25',
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
            'ukm_id.required_if' => 'UKM wajib dipilih untuk role Mahasiswa atau Pembina.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'password.min' => 'Kata sandi baru minimal berjumlah 8 karakter.',
            'email.unique' => 'Alamat email sudah terdaftar di sistem.',
        ];
    }
}
