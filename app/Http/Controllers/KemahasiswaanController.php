<?php

namespace App\Http\Controllers;

use App\Models\AjuanKegiatan;
use App\Models\DetailKegiatan;
use App\Models\Satuan;
use App\Models\Ukm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KemahasiswaanController extends Controller
{
    /**
     * Tampilkan ajuan kegiatan dari seluruh UKM dengan filter Nama UKM dan Tanggal Kegiatan.
     * Kolom tabel: Nama UKM, Nama Kegiatan, Tanggal Kegiatan, Verifikasi Pembina, Keterangan, Catatan, Link/Tombol Detail.
     */
    public function index(Request $request): View
    {
        $query = AjuanKegiatan::with(['ukm', 'user', 'details', 'pembinaVerifier', 'kemahasiswaanVerifier']);

        // Filter Nama UKM
        if ($request->filled('ukm_id')) {
            $query->where('ukm_id', $request->ukm_id);
        }

        // Filter Tanggal Kegiatan
        if ($request->filled('tanggal_kegiatan')) {
            $query->whereDate('tanggal_kegiatan', $request->tanggal_kegiatan);
        }

        // Filter Pencarian Nama Kegiatan
        if ($request->filled('search')) {
            $query->where('nama_kegiatan', 'like', '%' . $request->search . '%');
        }

        // Filter Status Pembina
        if ($request->filled('status_pembina')) {
            $query->where('status_pembina', $request->status_pembina);
        }

        $ajuans = $query->latest()->paginate(10)->withQueryString();
        $ukms = Ukm::orderBy('nama_ukm')->get();

        return view('kemahasiswaan.index', compact('ajuans', 'ukms'));
    }

    /**
     * Tampilkan detail ajuan kegiatan terpilih beserta tabel detail kebutuhan kegiatan untuk diperiksa Kemahasiswaan.
     */
    public function show(int $id): View
    {
        $ajuan = AjuanKegiatan::with(['ukm', 'user', 'details', 'pembinaVerifier', 'kemahasiswaanVerifier', 'lpj.pesertas', 'lpj.prestasies', 'riwayats.user'])
            ->findOrFail($id);
        $satuans = Satuan::where('is_aktif', true)->orderBy('nama_satuan')->get();

        return view('kemahasiswaan.show', compact('ajuan', 'satuans'));
    }

    /**
     * Perbarui status checklist Keterangan, Catatan, dan data detail RAB (Nama Akun, Nama Barang, Harga Satuan, Jumlah, Durasi, Satuan).
     */
    public function updateDetailItems(Request $request, int $id): RedirectResponse
    {
        $ajuan = AjuanKegiatan::with('details')->findOrFail($id);

        $validated = $request->validate([
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
        ], [
            'items.required' => 'Rincian item kebutuhan kegiatan (RAB) wajib diisi.',
            'items.min' => 'Rincian item kebutuhan kegiatan (RAB) wajib minimal 1 baris.',
            'items.*.harga_satuan.min' => 'Harga satuan tidak boleh bernilai negatif.',
            'items.*.jumlah.min' => 'Jumlah barang minimal 1.',
            'items.*.durasi.min' => 'Durasi minimal 1.',
            'items.*.status_keterangan.required' => 'Status persetujuan checklist wajib dipilih.',
        ]);

        DB::beginTransaction();
        try {
            $existingIds = [];
            $totalAnggaran = 0;
            $noUrut = 1;

            foreach ($validated['items'] as $itemData) {
                $detail = null;
                if (!empty($itemData['id'])) {
                    $detail = DetailKegiatan::where('id', $itemData['id'])
                        ->where('ajuan_kegiatan_id', $ajuan->id)
                        ->first();
                }

                if ($detail) {
                    $namaAkun = !empty($itemData['nama_akun']) ? trim($itemData['nama_akun']) : $detail->nama_akun;
                    $namaBarang = !empty($itemData['nama_barang']) ? trim($itemData['nama_barang']) : $detail->nama_barang;
                    $hargaSatuan = isset($itemData['harga_satuan']) && $itemData['harga_satuan'] !== '' ? (float) $itemData['harga_satuan'] : (float) $detail->harga_satuan;
                    $jumlah = isset($itemData['jumlah']) && $itemData['jumlah'] !== '' ? (int) $itemData['jumlah'] : (int) $detail->jumlah;
                    $satuan = !empty($itemData['satuan']) ? trim($itemData['satuan']) : ($detail->satuan ?? 'Unit');
                    $durasi = isset($itemData['durasi']) && (float) $itemData['durasi'] > 0 ? (float) $itemData['durasi'] : ($detail->durasi ?? 1);
                    $satuanDurasi = !empty($itemData['satuan_durasi']) ? trim($itemData['satuan_durasi']) : ($detail->satuan_durasi ?? 'Hari');
                    $subtotal = $hargaSatuan * $jumlah * $durasi;
                    $totalAnggaran += $subtotal;

                    $detail->update([
                        'no_urut' => $noUrut++,
                        'nama_akun' => $namaAkun,
                        'nama_barang' => $namaBarang,
                        'harga_satuan' => $hargaSatuan,
                        'jumlah' => $jumlah,
                        'satuan' => $satuan,
                        'durasi' => $durasi,
                        'satuan_durasi' => $satuanDurasi,
                        'total' => $subtotal,
                        'status_keterangan' => $itemData['status_keterangan'],
                        'catatan' => $itemData['catatan'] ?? null,
                    ]);
                    $existingIds[] = $detail->id;
                } else {
                    $durasi = isset($itemData['durasi']) && (float) $itemData['durasi'] > 0 ? (float) $itemData['durasi'] : 1;
                    $hargaSatuan = (float) ($itemData['harga_satuan'] ?? 0);
                    $jumlah = (int) ($itemData['jumlah'] ?? 1);
                    $subtotal = $hargaSatuan * $jumlah * $durasi;
                    $totalAnggaran += $subtotal;

                    $newDetail = DetailKegiatan::create([
                        'ajuan_kegiatan_id' => $ajuan->id,
                        'no_urut' => $noUrut++,
                        'nama_akun' => trim($itemData['nama_akun'] ?? 'Kebutuhan'),
                        'nama_barang' => trim($itemData['nama_barang'] ?? 'Item Baru'),
                        'harga_satuan' => $hargaSatuan,
                        'jumlah' => $jumlah,
                        'satuan' => trim($itemData['satuan'] ?? 'Unit'),
                        'durasi' => $durasi,
                        'satuan_durasi' => trim($itemData['satuan_durasi'] ?? 'Hari'),
                        'total' => $subtotal,
                        'status_keterangan' => $itemData['status_keterangan'],
                        'catatan' => $itemData['catatan'] ?? null,
                    ]);
                    $existingIds[] = $newDetail->id;
                }
            }

            // Hapus baris item yang dibuang dari tabel oleh Kemahasiswaan
            DetailKegiatan::where('ajuan_kegiatan_id', $ajuan->id)
                ->whereNotIn('id', $existingIds)
                ->delete();

            // Sinkronkan total anggaran ajuan dengan total RAB terkini
            $ajuan->update([
                'total_anggaran' => $totalAnggaran,
            ]);

            // Catat log penyesuaian detail RAB oleh Kemahasiswaan
            $ajuan->catatRiwayat(
                aksi: 'Penyesuaian Detail RAB',
                statusSesudah: $ajuan->status_akhir,
                catatan: 'Rincian item anggaran RAB disesuaikan/diverifikasi oleh Biro Kemahasiswaan (Total: Rp ' . number_format($totalAnggaran, 0, ',', '.') . ').',
                statusSebelumnya: $ajuan->status_akhir,
                userId: Auth::id(),
                role: 'kemahasiswaan'
            );

            DB::commit();

            return redirect()->route('kemahasiswaan.show', $ajuan->id)
                ->with('success', 'Rincian data RAB kegiatan dan status checklist berhasil disimpan & diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui item detail RAB: ' . $e->getMessage());
        }
    }

    /**
     * Tombol Disetujui final untuk menandai bahwa usulan kegiatan secara keseluruhan disetujui.
     */
    public function approveOverall(Request $request, int $id): RedirectResponse
    {
        $ajuan = AjuanKegiatan::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:disetujui,revisi,ditolak',
            'catatan_kemahasiswaan' => $request->status === 'revisi' ? 'required|string|max:1000' : 'nullable|string|max:1000',
        ], [
            'catatan_kemahasiswaan.required' => 'Catatan revisi wajib diisi agar mahasiswa mengetahui bagian apa yang perlu diperbaiki.',
        ]);

        $statusSebelumnya = $ajuan->status_akhir;

        $ajuan->status_kemahasiswaan = $validated['status'];
        $ajuan->catatan_kemahasiswaan = $validated['catatan_kemahasiswaan'];
        $ajuan->verified_kemahasiswaan_at = now();
        $ajuan->verified_kemahasiswaan_by = Auth::id();

        if ($validated['status'] === 'disetujui') {
            $ajuan->status_akhir = 'disetujui';
            $pesan = "Usulan kegiatan '{$ajuan->nama_kegiatan}' telah resmi Disetujui secara keseluruhan oleh Kemahasiswaan!";
            $aksi = 'Persetujuan Kemahasiswaan (Final)';
            $catatan = $validated['catatan_kemahasiswaan'] ?: 'Usulan kegiatan telah disetujui secara keseluruhan oleh Kemahasiswaan.';
        } elseif ($validated['status'] === 'revisi') {
            $ajuan->status_akhir = 'revisi_kemahasiswaan';
            $ajuan->catatan_revisi_terakhir = $validated['catatan_kemahasiswaan'];
            $pesan = "Permintaan revisi berhasil dikirimkan ke mahasiswa untuk diperbaiki.";
            $aksi = 'Permintaan Revisi (Kemahasiswaan)';
            $catatan = $validated['catatan_kemahasiswaan'];
        } else {
            $ajuan->status_akhir = 'ditolak';
            $ajuan->catatan_revisi_terakhir = $validated['catatan_kemahasiswaan'];
            $pesan = "Usulan kegiatan '{$ajuan->nama_kegiatan}' ditolak oleh Kemahasiswaan.";
            $aksi = 'Penolakan Usulan (Kemahasiswaan)';
            $catatan = $validated['catatan_kemahasiswaan'];
        }

        $ajuan->save();

        // Catat log riwayat verifikasi Kemahasiswaan
        $ajuan->catatRiwayat(
            aksi: $aksi,
            statusSesudah: $ajuan->status_akhir,
            catatan: $catatan,
            statusSebelumnya: $statusSebelumnya,
            userId: Auth::id(),
            role: 'kemahasiswaan'
        );

        return redirect()->route('kemahasiswaan.show', $ajuan->id)->with('success', $pesan);
    }

    /**
     * Unduh file dokumen proposal kegiatan.
     */
    public function downloadDokumen(int $id): StreamedResponse
    {
        $ajuan = AjuanKegiatan::findOrFail($id);

        if (! Storage::disk('public')->exists($ajuan->dokumen_proposal)) {
            abort(404, 'File dokumen proposal tidak ditemukan di server.');
        }

        return Storage::disk('public')->download($ajuan->dokumen_proposal, $ajuan->dokumen_nama_asli);
    }

    /**
     * Verifikasi Laporan Pertanggungjawaban (LPJ) oleh Biro Kemahasiswaan.
     */
    public function verifyLpj(Request $request, int $id): RedirectResponse
    {
        $user = Auth::user();
        $ajuan = AjuanKegiatan::with('lpj')->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:disetujui,revisi',
            'catatan' => 'nullable|string',
        ], [
            'status.required' => 'Status verifikasi LPJ wajib dipilih.',
            'status.in' => 'Status verifikasi LPJ tidak valid.',
        ]);

        $lpj = $ajuan->lpj;
        if (! $lpj) {
            return back()->with('error', 'Data LPJ tidak ditemukan.');
        }

        $statusSebelumnya = $ajuan->status_akhir;

        if ($validated['status'] === 'disetujui') {
            $ajuan->status_akhir = 'sudah_dilaksanakan';
            $pesan = "LPJ kegiatan '{$ajuan->nama_kegiatan}' berhasil disetujui. Status kegiatan kini Selesai Dilaksanakan!";
            $aksi = 'Verifikasi LPJ: Disetujui';
            $catatan = $validated['catatan'] ?: 'LPJ disetujui. Kegiatan resmi dinyatakan Selesai Dilaksanakan.';
        } else {
            $ajuan->status_akhir = 'revisi_lpj';
            $pesan = "Permintaan revisi LPJ untuk kegiatan '{$ajuan->nama_kegiatan}' berhasil dikirim ke mahasiswa.";
            $aksi = 'Verifikasi LPJ: Minta Revisi';
            $catatan = $validated['catatan'] ?: 'Laporan LPJ memerlukan perbaikan atau kelengkapan berkas.';
        }

        $lpj->catatan_verifikasi_lpj = $validated['catatan'] ?? null;
        $lpj->verified_lpj_at = now();
        $lpj->verified_lpj_by = $user->id;
        $lpj->save();

        $ajuan->save();

        // Catat log riwayat verifikasi LPJ
        $ajuan->catatRiwayat(
            aksi: $aksi,
            statusSesudah: $ajuan->status_akhir,
            catatan: $catatan,
            statusSebelumnya: $statusSebelumnya,
            userId: $user->id,
            role: 'kemahasiswaan'
        );

        return redirect()->route('kemahasiswaan.show', $ajuan->id)->with('success', $pesan);
    }

    /**
     * Unduh berkas dokumen LPJ untuk Biro Kemahasiswaan.
     */
    public function downloadLpjFile(int $id, string $type): StreamedResponse
    {
        $ajuan = AjuanKegiatan::with('lpj')->findOrFail($id);

        $lpj = $ajuan->lpj;
        if (! $lpj) {
            abort(404, 'Data LPJ belum tersedia.');
        }

        $fieldMap = [
            'sertifikat' => ['path' => $lpj->file_sertifikat, 'name' => $lpj->file_sertifikat_nama],
            'foto_kegiatan' => ['path' => $lpj->foto_kegiatan, 'name' => $lpj->foto_kegiatan_nama],
            'foto_penyerahan' => ['path' => $lpj->foto_penyerahan, 'name' => $lpj->foto_penyerahan_nama],
            'surat_undangan' => ['path' => $lpj->surat_undangan, 'name' => $lpj->surat_undangan_nama],
        ];

        if (! isset($fieldMap[$type]) || ! $fieldMap[$type]['path'] || ! Storage::disk('public')->exists($fieldMap[$type]['path'])) {
            abort(404, 'Berkas LPJ tidak ditemukan di server.');
        }

        return Storage::disk('public')->download($fieldMap[$type]['path'], $fieldMap[$type]['name'] ?? 'dokumen_lpj');
    }
}
