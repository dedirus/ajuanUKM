<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRabDetailItemsRequest extends FormRequest
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
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|integer',
            'items.*.nama_akun' => 'nullable|string|max:255',
            'items.*.nama_barang' => 'nullable|string|max:255',
            'items.*.harga_satuan' => 'nullable|numeric|min:0',
            'items.*.jumlah' => 'nullable|integer|min:1',
            'items.*.satuan' => 'nullable|string|max:50',
            'items.*.durasi' => 'nullable|numeric|min:1',
            'items.*.satuan_durasi' => 'nullable|string|max:50',
            'items.*.status_keterangan' => 'required|in:disetujui,tidak_disetujui,belum_diverifikasi',
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
            'items.required' => 'Rincian item kebutuhan kegiatan (RAB) wajib diisi.',
            'items.min' => 'Rincian item kebutuhan kegiatan (RAB) wajib minimal 1 baris.',
            'items.*.harga_satuan.min' => 'Harga satuan tidak boleh bernilai negatif.',
            'items.*.jumlah.min' => 'Jumlah barang minimal 1.',
            'items.*.durasi.min' => 'Durasi minimal 1.',
            'items.*.status_keterangan.required' => 'Status persetujuan checklist wajib dipilih.',
        ];
    }
}
