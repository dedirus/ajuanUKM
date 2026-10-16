<?php

namespace App\Http\Controllers;

use App\Models\AjuanKegiatan;
use App\Models\DetailKegiatan;
use App\Models\LpjKegiatan;
use App\Models\LpjPeserta;
use App\Models\LpjPrestasi;
use App\Http\Requests\StoreAjuanKegiatanRequest;
use App\Http\Requests\StoreLpjRequest;
use App\Http\Requests\UpdateAjuanKegiatanRequest;
use App\Models\Satuan;
use App\Services\AjuanKegiatanService;
use App\Services\LpjKegiatanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MahasiswaController extends Controller
{
    /**
     * Tampilkan daftar ajuan kegiatan milik UKM mahasiswa yang sedang login.
     */
    public function index(): View
    {
        $user = Auth::user();
        $ukm = $user->ukm;

        $ajuans = AjuanKegiatan::where('ukm_id', $user->ukm_id)
            ->with(['details', 'pembinaVerifier', 'kemahasiswaanVerifier'])
            ->latest()
            ->paginate(10);

        return view('mahasiswa.index', compact('user', 'ukm', 'ajuans'));
    }

    /**
     * Form pengajuan kegiatan mahasiswa baru.
     */
    public function create(): View
    {
        $user = Auth::user();
        $ukm = $user->ukm;
        $satuans = Satuan::where('is_aktif', true)->orderBy('nama_satuan')->get();

        return view('mahasiswa.create', compact('user', 'ukm', 'satuans'));
    }

    /**
     * Simpan data pengajuan kegiatan beserta detail tabel kebutuhan.
     */
    public function store(StoreAjuanKegiatanRequest $request, AjuanKegiatanService $service): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->ukm_id) {
            return back()->with('error', 'Akun Anda belum ditautkan ke UKM manapun.');
        }

        try {
            $ajuan = $service->createAjuan($user, $request->validated(), $request->file('dokumen_kegiatan'));

            return redirect()->route('mahasiswa.show', $ajuan->id)
                ->with('success', 'Pengajuan kegiatan dan detail kebutuhan berhasil disimpan dan dikirim ke Pembina!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan pengajuan: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan detail ajuan kegiatan beserta tabel rincian kebutuhan.
     */
    public function show(int $id): View
    {
        $user = Auth::user();

        $ajuan = AjuanKegiatan::where('id', $id)
            ->where('ukm_id', $user->ukm_id)
            ->with(['ukm', 'user', 'details', 'pembinaVerifier', 'kemahasiswaanVerifier', 'lpj.pesertas', 'lpj.prestasies', 'riwayats.user'])
            ->firstOrFail();

        return view('mahasiswa.show', compact('ajuan', 'user'));
    }

    /**
     * Unduh file dokumen proposal.
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

    /**
     * Form edit untuk memperbaiki usulan kegiatan yang memerlukan revisi.
     */
    public function edit(int $id): View|RedirectResponse
    {
        $user = Auth::user();

        $ajuan = AjuanKegiatan::where('id', $id)
            ->where('ukm_id', $user->ukm_id)
            ->with(['ukm', 'details'])
            ->firstOrFail();

        if (! $ajuan->isBisaDieditMahasiswa()) {
            return redirect()->route('mahasiswa.show', $ajuan->id)
                ->with('error', 'Usulan kegiatan yang telah disetujui tidak dapat diedit kembali.');
        }

        $ukm = $ajuan->ukm;
        $satuans = Satuan::where('is_aktif', true)->orderBy('nama_satuan')->get();

        return view('mahasiswa.edit', compact('ajuan', 'user', 'ukm', 'satuans'));
    }

    /**
     * Simpan perbaikan usulan dan ajukan kembali (resubmit).
     */
    public function update(UpdateAjuanKegiatanRequest $request, int $id, AjuanKegiatanService $service): RedirectResponse
    {
        $user = Auth::user();

        $ajuan = AjuanKegiatan::where('id', $id)
            ->where('ukm_id', $user->ukm_id)
            ->with('details')
            ->firstOrFail();

        if (! $ajuan->isBisaDieditMahasiswa()) {
            return redirect()->route('mahasiswa.show', $ajuan->id)
                ->with('error', 'Usulan kegiatan yang telah disetujui tidak dapat diedit kembali.');
        }

        try {
            $result = $service->updateAjuan($ajuan, $user, $request->validated(), $request->file('dokumen_kegiatan'));

            return redirect()->route('mahasiswa.show', $ajuan->id)
                ->with('success', $result['message']);
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan revisi: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan formulir pengisian Laporan Pertanggungjawaban (LPJ).
     */
    public function lpjForm(int $id): View|RedirectResponse
    {
        $user = Auth::user();

        $ajuan = AjuanKegiatan::where('id', $id)
            ->where('ukm_id', $user->ukm_id)
            ->with(['ukm', 'details', 'lpj.pesertas', 'lpj.prestasies'])
            ->firstOrFail();

        // Validasi apakah kegiatan berhak mengisi/mengedit LPJ
        if (! $ajuan->isSiapLpj() && ! $ajuan->isRevisiLpj() && ! $ajuan->isSudahDilaksanakan() && ! $ajuan->isMenungguVerifikasiLpj()) {
            return redirect()->route('mahasiswa.show', $ajuan->id)
                ->with('error', 'Kegiatan ini belum memenuhi syarat untuk pengisian LPJ atau tanggal pelaksanaan belum terlewati.');
        }

        $paguDisetujui = (float) ($ajuan->details->where('status_keterangan', 'disetujui')->sum('total') ?: $ajuan->total_anggaran);
        
        $pesertasInitial = old('pesertas');
        if ($pesertasInitial === null) {
            $pesertasInitial = ($ajuan->lpj && $ajuan->lpj->pesertas->count() > 0)
                ? $ajuan->lpj->pesertas->map(function ($p) {
                    return [
                        'nama' => $p->nama,
                        'npm' => $p->npm,
                        'peran' => $p->peran,
                    ];
                })->values()->all()
                : [];
        }

        $prestasiesInitial = old('prestasies');
        if ($prestasiesInitial === null) {
            $prestasiesInitial = ($ajuan->lpj && $ajuan->lpj->prestasies->count() > 0)
                ? $ajuan->lpj->prestasies->map(function ($pr) {
                    return [
                        'kategori_lomba' => $pr->kategori_lomba,
                        'peringkat_juara' => $pr->peringkat_juara,
                        'tingkat' => $pr->tingkat,
                        'nama_peraih' => $pr->nama_peraih,
                    ];
                })->values()->all()
                : [];
        }

        return view('mahasiswa.lpj', compact('ajuan', 'paguDisetujui', 'pesertasInitial', 'prestasiesInitial'));
    }

    public function storeLpj(StoreLpjRequest $request, int $id, LpjKegiatanService $service): RedirectResponse
    {
        $user = Auth::user();

        $ajuan = AjuanKegiatan::where('id', $id)
            ->where('ukm_id', $user->ukm_id)
            ->with(['lpj.prestasies', 'lpj.pesertas'])
            ->firstOrFail();

        try {
            $service->submitLpj($ajuan, $user, $request->validated(), $request);

            return redirect()->route('mahasiswa.show', $ajuan->id)
                ->with('success', 'Laporan Pertanggungjawaban (LPJ) berhasil disimpan dan dikirim ke Kemahasiswaan untuk verifikasi!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan LPJ: ' . $e->getMessage());
        }
    }

    /**
     * Unduh berkas dokumen LPJ.
     */
    public function downloadLpjFile(int $id, string $type): StreamedResponse
    {
        $user = Auth::user();

        $ajuan = AjuanKegiatan::where('id', $id)
            ->where('ukm_id', $user->ukm_id)
            ->with('lpj')
            ->firstOrFail();

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
