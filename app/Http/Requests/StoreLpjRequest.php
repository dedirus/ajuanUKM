<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLpjRequest extends FormRequest
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
            'prestasi' => 'nullable|string|max:100',
            'prestasies' => 'nullable|array',
            'prestasies.*.kategori_lomba' => 'nullable|string|max:150',
            'prestasies.*.peringkat_juara' => 'nullable|string|max:100',
            'prestasies.*.tingkat' => 'nullable|string|max:100',
            'prestasies.*.nama_peraih' => 'nullable|string|max:255',
            'jumlah_peserta' => 'nullable|integer|min:0',
            'ringkasan_kegiatan' => 'nullable|string',
            'realisasi_anggaran' => 'nullable|numeric|min:0',
            'pesertas' => 'nullable|array',
            'pesertas.*.nama' => 'nullable|string|max:150',
            'pesertas.*.npm' => 'nullable|string|max:50',
            'pesertas.*.peran' => 'nullable|string|max:50',
            'file_sertifikat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'foto_kegiatan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'foto_penyerahan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'surat_undangan' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ];
    }
}
