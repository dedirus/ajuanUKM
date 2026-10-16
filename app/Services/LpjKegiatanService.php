<?php

namespace App\Services;

use App\Models\AjuanKegiatan;
use App\Models\LpjKegiatan;
use App\Models\LpjPeserta;
use App\Models\LpjPrestasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LpjKegiatanService
{
    /**
     * Simpan atau perbarui data LPJ beserta prestasi, peserta, dan berkas.
     */
    public function submitLpj(AjuanKegiatan $ajuan, User $user, array $validated, Request $request): LpjKegiatan
    {
        return DB::transaction(function () use ($ajuan, $user, $validated, $request) {
            $lpj = $ajuan->lpj ?: new LpjKegiatan(['ajuan_kegiatan_id' => $ajuan->id]);

            $lpj->jumlah_peserta = isset($validated['jumlah_peserta']) ? (int) $validated['jumlah_peserta'] : 0;
            $lpj->ringkasan_kegiatan = $validated['ringkasan_kegiatan'] ?? null;
            $lpj->realisasi_anggaran = isset($validated['realisasi_anggaran']) ? (float) $validated['realisasi_anggaran'] : 0;

            if ($request->hasFile('file_sertifikat')) {
                $file = $request->file('file_sertifikat');
                $lpj->file_sertifikat_nama = $file->getClientOriginalName();
                $lpj->file_sertifikat = $file->store('dokumen_lpj/sertifikat', 'public');
            }

            if ($request->hasFile('foto_kegiatan')) {
                $file = $request->file('foto_kegiatan');
                $lpj->foto_kegiatan_nama = $file->getClientOriginalName();
                $lpj->foto_kegiatan = $file->store('dokumen_lpj/foto_kegiatan', 'public');
            }

            if ($request->hasFile('foto_penyerahan')) {
                $file = $request->file('foto_penyerahan');
                $lpj->foto_penyerahan_nama = $file->getClientOriginalName();
                $lpj->foto_penyerahan = $file->store('dokumen_lpj/foto_penyerahan', 'public');
            }

            if ($request->hasFile('surat_undangan')) {
                $file = $request->file('surat_undangan');
                $lpj->surat_undangan_nama = $file->getClientOriginalName();
                $lpj->surat_undangan = $file->store('dokumen_lpj/surat_undangan', 'public');
            }

            $lpj->submitted_at = now();
            $lpj->save();

            // Sinkronisasi Multi-Prestasi Lomba
            $lpj->prestasies()->delete();
            $validPrestasies = [];
            if (! empty($validated['prestasies']) && is_array($validated['prestasies'])) {
                foreach ($validated['prestasies'] as $pr) {
                    if (! empty($pr['kategori_lomba']) || ! empty($pr['peringkat_juara'])) {
                        $newPrestasi = LpjPrestasi::create([
                            'lpj_kegiatan_id' => $lpj->id,
                            'kategori_lomba' => trim($pr['kategori_lomba'] ?? 'Umum'),
                            'peringkat_juara' => trim($pr['peringkat_juara'] ?? 'Juara'),
                            'tingkat' => trim($pr['tingkat'] ?? 'Nasional'),
                            'nama_peraih' => trim($pr['nama_peraih'] ?? ''),
                        ]);
                        $validPrestasies[] = $newPrestasi;
                    }
                }
            }

            if (count($validPrestasies) > 0) {
                if (count($validPrestasies) === 1) {
                    $lpj->prestasi = $validPrestasies[0]->peringkat_juara . ' - ' . $validPrestasies[0]->kategori_lomba;
                } else {
                    $lpj->prestasi = count($validPrestasies) . ' Juara (' . collect($validPrestasies)->map(function ($p) {
                        return $p->peringkat_juara;
                    })->take(2)->implode(', ') . (count($validPrestasies) > 2 ? ', dll' : '') . ')';
                }
            } elseif (! empty($validated['prestasi'])) {
                $lpj->prestasi = $validated['prestasi'];
            } else {
                $lpj->prestasi = 'Kegiatan Non-Lomba (Terlaksana Sukses)';
            }
            $lpj->save();

            // Sinkronisasi Peserta SIMKATMAWA
            $lpj->pesertas()->delete();
            if (! empty($validated['pesertas']) && is_array($validated['pesertas'])) {
                foreach ($validated['pesertas'] as $p) {
                    if (! empty($p['nama']) || ! empty($p['npm'])) {
                        LpjPeserta::create([
                            'lpj_kegiatan_id' => $lpj->id,
                            'nama' => trim($p['nama'] ?? '-'),
                            'npm' => trim($p['npm'] ?? '-'),
                            'peran' => trim($p['peran'] ?? 'Peserta'),
                        ]);
                    }
                }
            }

            $statusSebelumnya = $ajuan->status_akhir;
            $isLpjPerbaikan = ($statusSebelumnya === 'revisi_lpj');

            $ajuan->status_akhir = 'menunggu_verifikasi_lpj';
            $ajuan->save();

            $ajuan->catatRiwayat(
                aksi: $isLpjPerbaikan ? 'Perbaikan Laporan LPJ' : 'Penyerahan Laporan LPJ',
                statusSesudah: 'menunggu_verifikasi_lpj',
                catatan: $isLpjPerbaikan
                    ? 'Laporan LPJ yang telah diperbaiki diserahkan kembali untuk verifikasi.'
                    : 'Laporan Pertanggungjawaban (LPJ) kegiatan dan data peserta SIMKATMAWA diserahkan.',
                statusSebelumnya: $statusSebelumnya,
                userId: $user->id,
                role: 'mahasiswa'
            );

            return $lpj;
        });
    }

    /**
     * Verifikasi LPJ oleh Kemahasiswaan.
     *
     * @return array{lpj: LpjKegiatan, message: string}
     */
    public function verifyLpj(AjuanKegiatan $ajuan, User $user, string $status, ?string $catatan): array
    {
        $lpj = $ajuan->lpj;
        $statusSebelumnya = $ajuan->status_akhir;

        if ($status === 'disetujui') {
            $ajuan->status_akhir = 'sudah_dilaksanakan';
            $pesan = "LPJ kegiatan '{$ajuan->nama_kegiatan}' berhasil disetujui. Status kegiatan kini Selesai Dilaksanakan!";
            $aksi = 'Verifikasi LPJ: Disetujui';
            $catatanLog = $catatan ?: 'LPJ disetujui. Kegiatan resmi dinyatakan Selesai Dilaksanakan.';
        } else {
            $ajuan->status_akhir = 'revisi_lpj';
            $pesan = "Permintaan revisi LPJ untuk kegiatan '{$ajuan->nama_kegiatan}' berhasil dikirim ke mahasiswa.";
            $aksi = 'Verifikasi LPJ: Minta Revisi';
            $catatanLog = $catatan ?: 'Laporan LPJ memerlukan perbaikan atau kelengkapan berkas.';
        }

        $lpj->catatan_verifikasi_lpj = $catatan;
        $lpj->verified_lpj_at = now();
        $lpj->verified_lpj_by = $user->id;
        $lpj->save();

        $ajuan->save();

        $ajuan->catatRiwayat(
            aksi: $aksi,
            statusSesudah: $ajuan->status_akhir,
            catatan: $catatanLog,
            statusSebelumnya: $statusSebelumnya,
            userId: $user->id,
            role: 'kemahasiswaan'
        );

        return ['lpj' => $lpj, 'message' => $pesan];
    }
}
