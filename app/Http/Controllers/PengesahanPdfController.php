<?php

namespace App\Http\Controllers;

use App\Models\AjuanKegiatan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengesahanPdfController extends Controller
{
    /**
     * Cetak Lembar Pengesahan dan Disposisi Kegiatan Mahasiswa dalam format PDF.
     */
    public function cetak(Request $request, int $id)
    {
        Carbon::setLocale('id');

        $user = Auth::user();

        $ajuan = AjuanKegiatan::with([
            'ukm',
            'user',
            'details',
            'pembinaVerifier',
            'kemahasiswaanVerifier'
        ])->findOrFail($id);

        // Otorisasi Hak Akses berdasarkan Role
        if ($user->role === 'mahasiswa' || $user->role === 'pembina') {
            if ($user->ukm_id !== $ajuan->ukm_id) {
                abort(403, 'Anda tidak memiliki hak akses untuk melihat atau mencetak lembar pengesahan UKM ini.');
            }
        }

        // Format Nomor Surat / Disposisi Resmi: DISP/{KODE_UKM}/{TAHUN}/{ID_4DIGIT}
        $kodeUkm = strtoupper($ajuan->ukm->kode ?? 'UKM');
        $tahun = $ajuan->created_at ? $ajuan->created_at->format('Y') : date('Y');
        $nomorSurat = sprintf("DISP/%s/%s/%04d", $kodeUkm, $tahun, $ajuan->id);

        // Perhitungan Akumulasi Biaya
        $totalDiajukan = (float) $ajuan->total_anggaran;
        $totalDisetujui = (float) $ajuan->details->where('status_keterangan', 'disetujui')->sum('total');
        
        // Jika belum ada item yang ditandai atau semua disetujui, pakai total anggaran ajuan jika status akhir disetujui
        if ($totalDisetujui == 0 && $ajuan->status_akhir === 'disetujui') {
            $totalDisetujui = $totalDiajukan;
        }

        // Nama file PDF
        $cleanUkm = preg_replace('/[^A-Za-z0-9_\-]/', '_', $ajuan->ukm->nama_ukm ?? 'UKM');
        $fileName = sprintf("Lembar_Pengesahan_%s_%04d.pdf", $cleanUkm, $ajuan->id);

        // Inisialisasi DomPDF dengan ukuran kertas A4 potret
        $pdf = Pdf::loadView('pdf.lembar_pengesahan', compact(
            'ajuan',
            'nomorSurat',
            'totalDiajukan',
            'totalDisetujui'
        ))->setPaper('a4', 'portrait')
          ->setOptions([
              'isHtml5ParserEnabled' => true,
              'isRemoteEnabled' => true,
              'defaultFont' => 'sans-serif',
          ]);

        // Opsi pengunduhan langsung jika parameter ?download=1 diberikan
        if ($request->query('download') == '1') {
            return $pdf->download($fileName);
        }

        // Default: Stream preview langsung di tab browser
        return $pdf->stream($fileName);
    }
}
