<?php

namespace App\Http\Controllers;

use App\Models\AjuanKegiatan;
use App\Models\DetailKegiatan;
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
        $ajuan = AjuanKegiatan::with(['ukm', 'user', 'details', 'pembinaVerifier', 'kemahasiswaanVerifier'])
            ->findOrFail($id);

        return view('kemahasiswaan.show', compact('ajuan'));
    }

    /**
     * Perbarui status checklist Keterangan (Disetujui / Tidak Disetujui) dan Catatan pada setiap baris item detail kegiatan.
     */
    public function updateDetailItems(Request $request, int $id): RedirectResponse
    {
        $ajuan = AjuanKegiatan::with('details')->findOrFail($id);

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:detail_kegiatans,id',
            'items.*.status_keterangan' => 'required|in:disetujui,tidak_disetujui,belum_diverifikasi',
            'items.*.catatan' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['items'] as $itemData) {
                $detail = DetailKegiatan::where('id', $itemData['id'])
                    ->where('ajuan_kegiatan_id', $ajuan->id)
                    ->first();

                if ($detail) {
                    $detail->update([
                        'status_keterangan' => $itemData['status_keterangan'],
                        'catatan' => $itemData['catatan'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('kemahasiswaan.show', $ajuan->id)
                ->with('success', 'Status checklist dan catatan item detail kebutuhan berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui item detail: ' . $e->getMessage());
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

        $ajuan->status_kemahasiswaan = $validated['status'];
        $ajuan->catatan_kemahasiswaan = $validated['catatan_kemahasiswaan'];
        $ajuan->verified_kemahasiswaan_at = now();
        $ajuan->verified_kemahasiswaan_by = Auth::id();

        if ($validated['status'] === 'disetujui') {
            $ajuan->status_akhir = 'disetujui';
            $pesan = "Usulan kegiatan '{$ajuan->nama_kegiatan}' telah resmi Disetujui secara keseluruhan oleh Kemahasiswaan!";
        } elseif ($validated['status'] === 'revisi') {
            $ajuan->status_akhir = 'revisi_kemahasiswaan';
            $ajuan->catatan_revisi_terakhir = $validated['catatan_kemahasiswaan'];
            $pesan = "Permintaan revisi berhasil dikirimkan ke mahasiswa untuk diperbaiki.";
        } else {
            $ajuan->status_akhir = 'ditolak';
            $ajuan->catatan_revisi_terakhir = $validated['catatan_kemahasiswaan'];
            $pesan = "Usulan kegiatan '{$ajuan->nama_kegiatan}' ditolak oleh Kemahasiswaan.";
        }

        $ajuan->save();

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
}
