<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApproveAjuanRequest;
use App\Http\Requests\UpdateRabDetailItemsRequest;
use App\Http\Requests\VerifyLpjRequest;
use App\Models\AjuanKegiatan;
use App\Models\DetailKegiatan;
use App\Models\Satuan;
use App\Models\Ukm;
use App\Services\AjuanKegiatanService;
use App\Services\LpjKegiatanService;
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

    public function updateDetailItems(UpdateRabDetailItemsRequest $request, int $id, AjuanKegiatanService $service): RedirectResponse
    {
        $ajuan = AjuanKegiatan::with('details')->findOrFail($id);

        try {
            $service->updateRabDetails($ajuan, Auth::user(), $request->validated()['items']);

            return redirect()->route('kemahasiswaan.show', $ajuan->id)
                ->with('success', 'Rincian data RAB kegiatan dan status checklist berhasil disimpan & diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui item detail RAB: ' . $e->getMessage());
        }
    }

    public function approveOverall(ApproveAjuanRequest $request, int $id, AjuanKegiatanService $service): RedirectResponse
    {
        $ajuan = AjuanKegiatan::findOrFail($id);

        $validated = $request->validated();
        $result = $service->approveByKemahasiswaan($ajuan, Auth::user(), $validated['status'], $validated['catatan_kemahasiswaan'] ?? null);

        return redirect()->route('kemahasiswaan.show', $ajuan->id)->with('success', $result['message']);
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

    public function verifyLpj(VerifyLpjRequest $request, int $id, LpjKegiatanService $service): RedirectResponse
    {
        $user = Auth::user();
        $ajuan = AjuanKegiatan::with('lpj')->findOrFail($id);

        if (! $ajuan->lpj) {
            return back()->with('error', 'Data LPJ tidak ditemukan.');
        }

        $validated = $request->validated();
        $result = $service->verifyLpj($ajuan, $user, $validated['status'], $validated['catatan'] ?? null);

        return redirect()->route('kemahasiswaan.show', $ajuan->id)->with('success', $result['message']);
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
