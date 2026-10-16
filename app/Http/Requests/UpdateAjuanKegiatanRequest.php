<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAjuanKegiatanRequest extends FormRequest
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
            'nama_kegiatan' => 'required|string|max:255',
            'tanggal_kegiatan' => 'required|date',
            'jenis_kegiatan' => 'required|in:Lomba,Pelatihan,Lainnya',
            'dokumen_kegiatan' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'items' => 'required|array|min:1',
            'items.*.nama_akun' => 'required|string|max:255',
            'items.*.nama_barang' => 'required|string|max:255',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.satuan' => 'required|string|max:50',
            'items.*.durasi' => 'required|numeric|min:1',
            'items.*.satuan_durasi' => 'required|string|max:50',
            'items.*.catatan' => 'nullable|string|max:500',
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
            'nama_kegiatan.required' => 'Nama kegiatan wajib diisi.',
            'tanggal_kegiatan.required' => 'Tanggal kegiatan wajib diisi.',
            'jenis_kegiatan.required' => 'Jenis kegiatan wajib dipilih.',
            'jenis_kegiatan.in' => 'Pilihan jenis kegiatan harus salah satu dari: Lomba, Pelatihan, atau Lainnya.',
            'dokumen_kegiatan.mimes' => 'Format dokumen harus berformat PDF, DOC, atau DOCX.',
            'dokumen_kegiatan.max' => 'Ukuran dokumen maksimal 10MB.',
            'items.required' => 'Tabel detail kebutuhan kegiatan wajib diisi minimal 1 baris.',
            'items.min' => 'Tabel detail kebutuhan kegiatan wajib diisi minimal 1 baris.',
            'items.*.nama_akun.required' => 'Nama akun pada baris detail wajib diisi.',
            'items.*.nama_barang.required' => 'Nama barang pada baris detail wajib diisi.',
            'items.*.harga_satuan.required' => 'Harga satuan wajib diisi.',
            'items.*.jumlah.required' => 'Jumlah barang wajib diisi.',
            'items.*.satuan.required' => 'Satuan barang wajib dipilih.',
            'items.*.durasi.required' => 'Durasi wajib diisi.',
            'items.*.durasi.min' => 'Durasi minimal 1.',
            'items.*.satuan_durasi.required' => 'Satuan durasi wajib dipilih.',
        ];
    }
}
