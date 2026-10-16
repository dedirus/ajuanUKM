<?php

namespace App\Services;

use App\Models\AjuanKegiatan;
use App\Models\DetailKegiatan;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class AjuanKegiatanService
{
    /**
     * Simpan pengajuan usulan kegiatan baru beserta detail tabel kebutuhan.
     */
    public function createAjuan(User $user, array $validated, ?UploadedFile $file): AjuanKegiatan
    {
        return DB::transaction(function () use ($user, $validated, $file) {
            $path = $file ? $file->store('dokumen_proposal', 'public') : '';
            $originalName = $file ? $file->getClientOriginalName() : '';

            $totalAnggaran = 0;
            foreach ($validated['items'] as $item) {
                $durasi = isset($item['durasi']) && (float) $item['durasi'] > 0 ? (float) $item['durasi'] : 1;
                $subtotal = (float) $item['harga_satuan'] * (int) $item['jumlah'] * $durasi;
                $totalAnggaran += $subtotal;
            }

            $ajuan = AjuanKegiatan::create([
                'user_id' => $user->id,
                'ukm_id' => $user->ukm_id,
                'nama_kegiatan' => $validated['nama_kegiatan'],
                'tanggal_kegiatan' => $validated['tanggal_kegiatan'],
                'jenis_kegiatan' => $validated['jenis_kegiatan'],
                'dokumen_proposal' => $path,
                'dokumen_nama_asli' => $originalName,
                'total_anggaran' => $totalAnggaran,
                'status_pembina' => 'belum_disetujui',
                'status_kemahasiswaan' => 'belum_disetujui',
                'status_akhir' => 'menunggu_pembina',
            ]);

            $noUrut = 1;
            foreach ($validated['items'] as $item) {
                $durasi = isset($item['durasi']) && (float) $item['durasi'] > 0 ? (float) $item['durasi'] : 1;
                $subtotal = (float) $item['harga_satuan'] * (int) $item['jumlah'] * $durasi;

                DetailKegiatan::create([
                    'ajuan_kegiatan_id' => $ajuan->id,
                    'no_urut' => $noUrut++,
                    'nama_akun' => trim($item['nama_akun']),
                    'nama_barang' => trim($item['nama_barang']),
                    'harga_satuan' => (float) $item['harga_satuan'],
                    'jumlah' => (int) $item['jumlah'],
                    'satuan' => trim($item['satuan'] ?? 'Unit'),
                    'durasi' => $durasi,
                    'satuan_durasi' => trim($item['satuan_durasi'] ?? 'Hari'),
                    'total' => $subtotal,
                    'status_keterangan' => 'belum_diverifikasi',
                    'catatan' => $item['catatan'] ?? null,
                ]);
            }

            $ajuan->catatRiwayat(
                aksi: 'Pengajuan Usulan Baru',
                statusSesudah: 'menunggu_pembina',
                catatan: 'Usulan kegiatan berhasil diajukan untuk pertama kali.',
                statusSebelumnya: null,
                userId: $user->id,
                role: 'mahasiswa'
            );

            return $ajuan;
        });
    }

    /**
     * Simpan pembaruan atau perbaikan usulan dan ajukan kembali (resubmit).
     *
     * @return array{ajuan: AjuanKegiatan, message: string}
     */
    public function updateAjuan(AjuanKegiatan $ajuan, User $user, array $validated, ?UploadedFile $file): array
    {
        return DB::transaction(function () use ($ajuan, $user, $validated, $file) {
            if ($file) {
                $originalName = $file->getClientOriginalName();
                $path = $file->store('dokumen_proposal', 'public');
                $ajuan->dokumen_proposal = $path;
                $ajuan->dokumen_nama_asli = $originalName;
            }

            $totalAnggaran = 0;
            foreach ($validated['items'] as $item) {
                $durasi = isset($item['durasi']) && (float) $item['durasi'] > 0 ? (float) $item['durasi'] : 1;
                $subtotal = (float) $item['harga_satuan'] * (int) $item['jumlah'] * $durasi;
                $totalAnggaran += $subtotal;
            }

            $ajuan->nama_kegiatan = $validated['nama_kegiatan'];
            $ajuan->tanggal_kegiatan = $validated['tanggal_kegiatan'];
            $ajuan->jenis_kegiatan = $validated['jenis_kegiatan'];
            $ajuan->total_anggaran = $totalAnggaran;

            $statusSebelumnya = $ajuan->status_akhir;

            if ($ajuan->isRevisiPembina()) {
                $ajuan->status_pembina = 'belum_disetujui';
                $ajuan->status_akhir = 'menunggu_pembina';
                $ajuan->revisi_count += 1;
                $pesan = 'Revisi usulan kegiatan berhasil disimpan dan diajukan kembali ke Dosen Pembina!';
                $riwayatAksi = 'Pengajuan Revisi Usulan';
                $riwayatCatatan = 'Perbaikan usulan (Revisi ke-' . $ajuan->revisi_count . ') diajukan kembali ke Dosen Pembina.';
            } elseif ($ajuan->isRevisiKemahasiswaan()) {
                $ajuan->status_kemahasiswaan = 'belum_disetujui';
                $ajuan->status_akhir = 'menunggu_kemahasiswaan';
                $ajuan->revisi_count += 1;
                $pesan = 'Revisi usulan kegiatan berhasil disimpan dan diajukan kembali ke Biro Kemahasiswaan!';
                $riwayatAksi = 'Pengajuan Revisi Usulan';
                $riwayatCatatan = 'Perbaikan usulan (Revisi ke-' . $ajuan->revisi_count . ') diajukan kembali ke Biro Kemahasiswaan.';
            } else {
                $pesan = 'Perubahan data usulan kegiatan dan rincian RAB berhasil disimpan!';
                $riwayatAksi = 'Pembaruan Usulan Kegiatan';
                $riwayatCatatan = 'Perubahan informasi usulan kegiatan dan/atau rincian item RAB disimpan oleh mahasiswa.';
            }

            $ajuan->save();

            $ajuan->catatRiwayat(
                aksi: $riwayatAksi,
                statusSesudah: $ajuan->status_akhir,
                catatan: $riwayatCatatan,
                statusSebelumnya: $statusSebelumnya,
                userId: $user->id,
                role: 'mahasiswa'
            );

            $ajuan->details()->delete();

            $noUrut = 1;
            foreach ($validated['items'] as $item) {
                $durasi = isset($item['durasi']) && (float) $item['durasi'] > 0 ? (float) $item['durasi'] : 1;
                $subtotal = (float) $item['harga_satuan'] * (int) $item['jumlah'] * $durasi;

                DetailKegiatan::create([
                    'ajuan_kegiatan_id' => $ajuan->id,
                    'no_urut' => $noUrut++,
                    'nama_akun' => trim($item['nama_akun']),
                    'nama_barang' => trim($item['nama_barang']),
                    'harga_satuan' => (float) $item['harga_satuan'],
                    'jumlah' => (int) $item['jumlah'],
                    'satuan' => trim($item['satuan'] ?? 'Unit'),
                    'durasi' => $durasi,
                    'satuan_durasi' => trim($item['satuan_durasi'] ?? 'Hari'),
                    'total' => $subtotal,
                    'status_keterangan' => 'belum_diverifikasi',
                    'catatan' => $item['catatan'] ?? null,
                ]);
            }

            return ['ajuan' => $ajuan, 'message' => $pesan];
        });
    }

    /**
     * Verifikasi usulan oleh Pembina.
     *
     * @return array{ajuan: AjuanKegiatan, message: string}
     */
    public function verifyByPembina(AjuanKegiatan $ajuan, User $user, string $status, ?string $catatan): array
    {
        $statusSebelumnya = $ajuan->status_akhir;
        $ajuan->status_pembina = $status;
        $ajuan->catatan_pembina = $catatan;
        $ajuan->verified_pembina_at = now();
        $ajuan->verified_pembina_by = $user->id;

        if ($status === 'disetujui') {
            if ($ajuan->status_kemahasiswaan !== 'disetujui') {
                $ajuan->status_akhir = 'menunggu_kemahasiswaan';
            }
            $pesan = "Ajuan kegiatan '{$ajuan->nama_kegiatan}' berhasil disetujui oleh Pembina!";
            $aksi = 'Persetujuan Pembina';
            $catatanLog = $catatan ?: 'Usulan kegiatan disetujui oleh Dosen Pembina.';
        } elseif ($status === 'revisi') {
            $ajuan->status_akhir = 'revisi_pembina';
            $ajuan->catatan_revisi_terakhir = $catatan;
            $pesan = 'Permintaan revisi berhasil dikirimkan ke mahasiswa dengan catatan.';
            $aksi = 'Permintaan Revisi (Pembina)';
            $catatanLog = $catatan;
        } else {
            $ajuan->status_akhir = 'ditolak';
            $ajuan->catatan_revisi_terakhir = $catatan;
            $pesan = "Ajuan kegiatan '{$ajuan->nama_kegiatan}' telah ditolak.";
            $aksi = 'Penolakan Usulan (Pembina)';
            $catatanLog = $catatan;
        }

        $ajuan->save();

        $ajuan->catatRiwayat(
            aksi: $aksi,
            statusSesudah: $ajuan->status_akhir,
            catatan: $catatanLog,
            statusSebelumnya: $statusSebelumnya,
            userId: $user->id,
            role: 'pembina'
        );

        return ['ajuan' => $ajuan, 'message' => $pesan];
    }

    /**
     * Perbarui rincian item RAB oleh Kemahasiswaan.
     */
    public function updateRabDetails(AjuanKegiatan $ajuan, User $user, array $items): void
    {
        DB::transaction(function () use ($ajuan, $user, $items) {
            $existingIds = [];
            $totalAnggaran = 0;
            $noUrut = 1;

            foreach ($items as $itemData) {
                $detail = null;
                if (! empty($itemData['id'])) {
                    $detail = DetailKegiatan::where('id', $itemData['id'])
                        ->where('ajuan_kegiatan_id', $ajuan->id)
                        ->first();
                }

                if ($detail) {
                    $namaAkun = ! empty($itemData['nama_akun']) ? trim($itemData['nama_akun']) : $detail->nama_akun;
                    $namaBarang = ! empty($itemData['nama_barang']) ? trim($itemData['nama_barang']) : $detail->nama_barang;
                    $hargaSatuan = isset($itemData['harga_satuan']) && $itemData['harga_satuan'] !== '' ? (float) $itemData['harga_satuan'] : (float) $detail->harga_satuan;
                    $jumlah = isset($itemData['jumlah']) && $itemData['jumlah'] !== '' ? (int) $itemData['jumlah'] : (int) $detail->jumlah;
                    $satuan = ! empty($itemData['satuan']) ? trim($itemData['satuan']) : ($detail->satuan ?? 'Unit');
                    $durasi = isset($itemData['durasi']) && (float) $itemData['durasi'] > 0 ? (float) $itemData['durasi'] : ($detail->durasi ?? 1);
                    $satuanDurasi = ! empty($itemData['satuan_durasi']) ? trim($itemData['satuan_durasi']) : ($detail->satuan_durasi ?? 'Hari');
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

            DetailKegiatan::where('ajuan_kegiatan_id', $ajuan->id)
                ->whereNotIn('id', $existingIds)
                ->delete();

            $ajuan->update([
                'total_anggaran' => $totalAnggaran,
            ]);

            $ajuan->catatRiwayat(
                aksi: 'Penyesuaian Detail RAB',
                statusSesudah: $ajuan->status_akhir,
                catatan: 'Rincian item anggaran RAB disesuaikan/diverifikasi oleh Biro Kemahasiswaan (Total: Rp ' . number_format($totalAnggaran, 0, ',', '.') . ').',
                statusSebelumnya: $ajuan->status_akhir,
                userId: $user->id,
                role: 'kemahasiswaan'
            );
        });
    }

    /**
     * Persetujuan akhir atau penolakan/revisi oleh Kemahasiswaan.
     *
     * @return array{ajuan: AjuanKegiatan, message: string}
     */
    public function approveByKemahasiswaan(AjuanKegiatan $ajuan, User $user, string $status, ?string $catatan): array
    {
        $statusSebelumnya = $ajuan->status_akhir;

        $ajuan->status_kemahasiswaan = $status;
        $ajuan->catatan_kemahasiswaan = $catatan;
        $ajuan->verified_kemahasiswaan_at = now();
        $ajuan->verified_kemahasiswaan_by = $user->id;

        if ($status === 'disetujui') {
            $ajuan->status_akhir = 'disetujui';
            $pesan = "Usulan kegiatan '{$ajuan->nama_kegiatan}' telah resmi Disetujui secara keseluruhan oleh Kemahasiswaan!";
            $aksi = 'Persetujuan Kemahasiswaan (Final)';
            $catatanLog = $catatan ?: 'Usulan kegiatan telah disetujui secara keseluruhan oleh Kemahasiswaan.';
        } elseif ($status === 'revisi') {
            $ajuan->status_akhir = 'revisi_kemahasiswaan';
            $ajuan->catatan_revisi_terakhir = $catatan;
            $pesan = 'Permintaan revisi berhasil dikirimkan ke mahasiswa untuk diperbaiki.';
            $aksi = 'Permintaan Revisi (Kemahasiswaan)';
            $catatanLog = $catatan;
        } else {
            $ajuan->status_akhir = 'ditolak';
            $ajuan->catatan_revisi_terakhir = $catatan;
            $pesan = "Usulan kegiatan '{$ajuan->nama_kegiatan}' ditolak oleh Kemahasiswaan.";
            $aksi = 'Penolakan Usulan (Kemahasiswaan)';
            $catatanLog = $catatan;
        }

        $ajuan->save();

        $ajuan->catatRiwayat(
            aksi: $aksi,
            statusSesudah: $ajuan->status_akhir,
            catatan: $catatanLog,
            statusSebelumnya: $statusSebelumnya,
            userId: $user->id,
            role: 'kemahasiswaan'
        );

        return ['ajuan' => $ajuan, 'message' => $pesan];
    }
}
