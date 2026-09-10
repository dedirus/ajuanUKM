<?php

namespace App\Http\Controllers;

use App\Exports\LaporanRealisasiUkmExport;
use App\Models\AjuanKegiatan;
use App\Models\Ukm;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LaporanKemahasiswaanController extends Controller
{
    /**
     * Tampilkan Dashboard & Rekapitulasi Laporan Realisasi Usulan per UKM.
     */
    public function index(Request $request): View
    {
        $query = Ukm::with(['ajuanKegiatans.details', 'ajuanKegiatans.user', 'users']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_ukm', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%")
                  ->orWhere('pembina_nama', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $ukms = $query->orderBy('nama_ukm')->get();

        $ukmStats = $this->calculateUkmStats($ukms);
        $summary = $this->calculateGlobalSummary();
        $kategoris = Ukm::distinct()->whereNotNull('kategori')->pluck('kategori');

        return view('kemahasiswaan.laporan.index', compact('ukmStats', 'summary', 'kategoris'));
    }

    /**
     * Tampilkan detail rincian kegiatan khusus untuk satu UKM terpilih.
     */
    public function detail(int $ukm_id): View
    {
        $ukm = Ukm::with([
            'users',
            'ajuanKegiatans' => function ($q) {
                $q->latest();
            },
            'ajuanKegiatans.user',
            'ajuanKegiatans.details',
            'ajuanKegiatans.pembinaVerifier',
            'ajuanKegiatans.kemahasiswaanVerifier',
        ])->findOrFail($ukm_id);

        $totalUsulan = $ukm->ajuanKegiatans->count();
        $disetujuiCount = $ukm->ajuanKegiatans->where('status_akhir', 'disetujui')->count();
        $prosesCount = $ukm->ajuanKegiatans->filter(function ($a) {
            return in_array($a->status_akhir, ['menunggu_pembina', 'menunggu_kemahasiswaan', 'revisi_pembina', 'revisi_kemahasiswaan']);
        })->count();
        $ditolakCount = $ukm->ajuanKegiatans->where('status_akhir', 'ditolak')->count();

        $totalAnggaranDiajukan = (float) $ukm->ajuanKegiatans->sum('total_anggaran');
        $totalAnggaranDisetujui = (float) $ukm->ajuanKegiatans->where('status_akhir', 'disetujui')->sum(function ($a) {
            $approvedDetails = $a->details->where('status_keterangan', 'disetujui')->sum('total');
            return $approvedDetails > 0 ? $approvedDetails : $a->total_anggaran;
        });

        $persenRealisasi = $totalUsulan > 0 ? round(($disetujuiCount / $totalUsulan) * 100, 1) : 0;

        $stats = [
            'total_usulan' => $totalUsulan,
            'disetujui_count' => $disetujuiCount,
            'proses_count' => $prosesCount,
            'ditolak_count' => $ditolakCount,
            'total_anggaran_diajukan' => $totalAnggaranDiajukan,
            'total_anggaran_disetujui' => $totalAnggaranDisetujui,
            'persentase_realisasi' => $persenRealisasi,
        ];

        return view('kemahasiswaan.laporan.detail', compact('ukm', 'stats'));
    }

    /**
     * Download laporan rekapitulasi ke file Excel (.xlsx).
     */
    public function exportExcel(Request $request): BinaryFileResponse
    {
        $ukms = Ukm::with(['ajuanKegiatans.details', 'ajuanKegiatans.user'])->orderBy('nama_ukm')->get();
        $ukmStats = $this->calculateUkmStats($ukms);
        $allAjuans = AjuanKegiatan::with(['ukm', 'user', 'details'])->latest()->get();
        $summary = $this->calculateGlobalSummary();

        $fileName = 'Laporan_Realisasi_UKM_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(new LaporanRealisasiUkmExport($ukmStats, $allAjuans, $summary), $fileName);
    }

    /**
     * Helper kalkulasi statistik array UKM.
     */
    protected function calculateUkmStats($ukms)
    {
        return $ukms->map(function ($ukm) {
            $totalUsulan = $ukm->ajuanKegiatans->count();
            $disetujuiCount = $ukm->ajuanKegiatans->where('status_akhir', 'disetujui')->count();
            $prosesCount = $ukm->ajuanKegiatans->filter(function ($a) {
                return in_array($a->status_akhir, ['menunggu_pembina', 'menunggu_kemahasiswaan', 'revisi_pembina', 'revisi_kemahasiswaan']);
            })->count();
            $ditolakCount = $ukm->ajuanKegiatans->where('status_akhir', 'ditolak')->count();

            $totalAnggaranDiajukan = (float) $ukm->ajuanKegiatans->sum('total_anggaran');
            $totalAnggaranDisetujui = (float) $ukm->ajuanKegiatans->where('status_akhir', 'disetujui')->sum(function ($a) {
                $approvedDetails = $a->details->where('status_keterangan', 'disetujui')->sum('total');
                return $approvedDetails > 0 ? $approvedDetails : $a->total_anggaran;
            });

            $persen = $totalUsulan > 0 ? round(($disetujuiCount / $totalUsulan) * 100, 1) : 0;

            return [
                'id' => $ukm->id,
                'kode' => $ukm->kode,
                'nama_ukm' => $ukm->nama_ukm,
                'kategori' => $ukm->kategori ?? 'Umum',
                'pembina_nama' => $ukm->pembina_nama ?? '-',
                'total_usulan' => $totalUsulan,
                'disetujui_count' => $disetujuiCount,
                'proses_count' => $prosesCount,
                'ditolak_count' => $ditolakCount,
                'total_anggaran_diajukan' => $totalAnggaranDiajukan,
                'total_anggaran_disetujui' => $totalAnggaranDisetujui,
                'persentase_realisasi' => $persen,
            ];
        });
    }

    /**
     * Helper kalkulasi KPI agregasi universitas.
     */
    protected function calculateGlobalSummary(): array
    {
        $allAjuans = AjuanKegiatan::with(['details'])->get();
        $totalUsulan = $allAjuans->count();
        $disetujuiCount = $allAjuans->where('status_akhir', 'disetujui')->count();
        $prosesCount = $allAjuans->filter(function ($a) {
            return in_array($a->status_akhir, ['menunggu_pembina', 'menunggu_kemahasiswaan', 'revisi_pembina', 'revisi_kemahasiswaan']);
        })->count();
        $ditolakCount = $allAjuans->where('status_akhir', 'ditolak')->count();

        $anggaranDiajukan = (float) $allAjuans->sum('total_anggaran');
        $anggaranDisetujui = (float) $allAjuans->where('status_akhir', 'disetujui')->sum(function ($a) {
            $approvedDetails = $a->details->where('status_keterangan', 'disetujui')->sum('total');
            return $approvedDetails > 0 ? $approvedDetails : $a->total_anggaran;
        });

        $persen = $totalUsulan > 0 ? round(($disetujuiCount / $totalUsulan) * 100, 1) : 0;

        return [
            'total_ukm' => Ukm::count(),
            'total_usulan' => $totalUsulan,
            'total_disetujui' => $disetujuiCount,
            'total_proses' => $prosesCount,
            'total_ditolak' => $ditolakCount,
            'persentase_realisasi' => $persen,
            'total_anggaran_diajukan' => $anggaranDiajukan,
            'total_anggaran_disetujui' => $anggaranDisetujui,
        ];
    }
}
