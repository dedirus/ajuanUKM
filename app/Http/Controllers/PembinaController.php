<?php

namespace App\Http\Controllers;

use App\Models\AjuanKegiatan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PembinaController extends Controller
{
    /**
     * Tampilkan tabel ajuan kegiatan khusus UKM yang dibina oleh Pembina login.
     * Kolom: Nama Kegiatan, Tanggal Kegiatan, Total Jumlah Ajuan, Keterangan, Catatan, Link Detail.
     */
    public function index(): View
    {
        $user = Auth::user();
        $ukm = $user->ukm;

        $ajuans = AjuanKegiatan::where('ukm_id', $user->ukm_id)
            ->with(['user', 'details'])
            ->latest()
            ->paginate(10);

        return view('pembina.index', compact('user', 'ukm', 'ajuans'));
    }

    /**
     * Tampilkan rincian ajuan kegiatan dan tabel detail kebutuhan kegiatan untuk diperiksa pembina.
     */
    public function show(int $id): View
    {
        $user = Auth::user();

        $ajuan = AjuanKegiatan::where('id', $id)
            ->where('ukm_id', $user->ukm_id)
            ->with(['ukm', 'user', 'details', 'pembinaVerifier', 'kemahasiswaanVerifier'])
            ->firstOrFail();

        return view('pembina.show', compact('user', 'ajuan'));
    }

    /**
     * Aksi verifikasi Pembina untuk menekan tombol "Disetujui" (atau tolak) beserta catatan.
     */
    public function verify(Request $request, int $id): RedirectResponse
    {
        $user = Auth::user();

        $ajuan = AjuanKegiatan::where('id', $id)
            ->where('ukm_id', $user->ukm_id)
            ->firstOrFail();

        $validated = $request->validate([
            'status' => 'required|in:disetujui,revisi,ditolak',
            'catatan' => $request->status === 'revisi' ? 'required|string|max:1000' : 'nullable|string|max:1000',
        ], [
            'catatan.required' => 'Catatan revisi wajib diisi agar mahasiswa mengetahui bagian apa yang perlu diperbaiki.',
        ]);

        $ajuan->status_pembina = $validated['status'];
        $ajuan->catatan_pembina = $validated['catatan'];
        $ajuan->verified_pembina_at = now();
        $ajuan->verified_pembina_by = $user->id;

        if ($validated['status'] === 'disetujui') {
            // Jika sebelumnya belum disetujui kemahasiswaan, ubah status akhir menjadi menunggu kemahasiswaan
            if ($ajuan->status_kemahasiswaan !== 'disetujui') {
                $ajuan->status_akhir = 'menunggu_kemahasiswaan';
            }
            $pesan = "Ajuan kegiatan '{$ajuan->nama_kegiatan}' berhasil disetujui oleh Pembina!";
        } elseif ($validated['status'] === 'revisi') {
            $ajuan->status_akhir = 'revisi_pembina';
            $ajuan->catatan_revisi_terakhir = $validated['catatan'];
            $pesan = "Permintaan revisi berhasil dikirimkan ke mahasiswa dengan catatan.";
        } else {
            $ajuan->status_akhir = 'ditolak';
            $ajuan->catatan_revisi_terakhir = $validated['catatan'];
            $pesan = "Ajuan kegiatan '{$ajuan->nama_kegiatan}' telah ditolak.";
        }

        $ajuan->save();

        return redirect()->route('pembina.show', $ajuan->id)->with('success', $pesan);
    }

    /**
     * Unduh file dokumen proposal kegiatan.
     */
    public function downloadDokumen(int $id): StreamedResponse
    {
        $user = Auth::user();
        $ajuan = AjuanKegiatan::where('id', $id)
            ->where('ukm_id', $user->ukm_id)
            ->firstOrFail();

        if (! Storage::disk('public')->exists($ajuan->dokumen_proposal)) {
            abort(404, 'File dokumen proposal tidak ditemukan di server.');
        }

        return Storage::disk('public')->download($ajuan->dokumen_proposal, $ajuan->dokumen_nama_asli);
    }
}
