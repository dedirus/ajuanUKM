<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('riwayat_kegiatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ajuan_kegiatan_id')->constrained('ajuan_kegiatans')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('role', 50)->default('mahasiswa');
            $table->string('aksi', 100);
            $table->string('status_sebelumnya', 50)->nullable();
            $table->string('status_sesudah', 50)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // Backfill data riwayat untuk ajuan yang sudah ada sebelumnya
        try {
            $existingAjuans = DB::table('ajuan_kegiatans')->get();
            foreach ($existingAjuans as $ajuan) {
                // 1. Log Pengajuan Awal
                DB::table('riwayat_kegiatans')->insert([
                    'ajuan_kegiatan_id' => $ajuan->id,
                    'user_id' => $ajuan->user_id,
                    'role' => 'mahasiswa',
                    'aksi' => 'Pengajuan Usulan Baru',
                    'status_sebelumnya' => null,
                    'status_sesudah' => 'menunggu_pembina',
                    'catatan' => 'Usulan kegiatan berhasil diajukan untuk pertama kali.',
                    'created_at' => $ajuan->created_at ?? now(),
                    'updated_at' => $ajuan->created_at ?? now(),
                ]);

                // 2. Log Verifikasi Pembina
                if ($ajuan->verified_pembina_at) {
                    $aksiPembina = match ($ajuan->status_pembina) {
                        'disetujui' => 'Persetujuan Pembina',
                        'revisi' => 'Permintaan Revisi (Pembina)',
                        'ditolak' => 'Penolakan Usulan (Pembina)',
                        default => 'Verifikasi Pembina',
                    };

                    $statusSesudah = match ($ajuan->status_pembina) {
                        'disetujui' => 'menunggu_kemahasiswaan',
                        'revisi' => 'revisi_pembina',
                        'ditolak' => 'ditolak',
                        default => $ajuan->status_akhir,
                    };

                    DB::table('riwayat_kegiatans')->insert([
                        'ajuan_kegiatan_id' => $ajuan->id,
                        'user_id' => $ajuan->verified_pembina_by,
                        'role' => 'pembina',
                        'aksi' => $aksiPembina,
                        'status_sebelumnya' => 'menunggu_pembina',
                        'status_sesudah' => $statusSesudah,
                        'catatan' => $ajuan->catatan_pembina,
                        'created_at' => $ajuan->verified_pembina_at,
                        'updated_at' => $ajuan->verified_pembina_at,
                    ]);
                }

                // 3. Log Verifikasi Kemahasiswaan
                if ($ajuan->verified_kemahasiswaan_at) {
                    $aksiKemahasiswaan = match ($ajuan->status_kemahasiswaan) {
                        'disetujui' => 'Persetujuan Kemahasiswaan (Final)',
                        'revisi' => 'Permintaan Revisi (Kemahasiswaan)',
                        'ditolak' => 'Penolakan Usulan (Kemahasiswaan)',
                        default => 'Verifikasi Kemahasiswaan',
                    };

                    $statusSesudahKemahasiswaan = match ($ajuan->status_kemahasiswaan) {
                        'disetujui' => 'disetujui',
                        'revisi' => 'revisi_kemahasiswaan',
                        'ditolak' => 'ditolak',
                        default => $ajuan->status_akhir,
                    };

                    DB::table('riwayat_kegiatans')->insert([
                        'ajuan_kegiatan_id' => $ajuan->id,
                        'user_id' => $ajuan->verified_kemahasiswaan_by,
                        'role' => 'kemahasiswaan',
                        'aksi' => $aksiKemahasiswaan,
                        'status_sebelumnya' => 'menunggu_kemahasiswaan',
                        'status_sesudah' => $statusSesudahKemahasiswaan,
                        'catatan' => $ajuan->catatan_kemahasiswaan,
                        'created_at' => $ajuan->verified_kemahasiswaan_at,
                        'updated_at' => $ajuan->verified_kemahasiswaan_at,
                    ]);
                }

                // 4. Log LPJ jika ada
                $lpj = DB::table('lpj_kegiatans')->where('ajuan_kegiatan_id', $ajuan->id)->first();
                if ($lpj) {
                    DB::table('riwayat_kegiatans')->insert([
                        'ajuan_kegiatan_id' => $ajuan->id,
                        'user_id' => $ajuan->user_id,
                        'role' => 'mahasiswa',
                        'aksi' => 'Penyerahan Laporan LPJ',
                        'status_sebelumnya' => 'disetujui',
                        'status_sesudah' => 'menunggu_verifikasi_lpj',
                        'catatan' => 'Laporan Pertanggungjawaban (LPJ) kegiatan dan data peserta diserahkan.',
                        'created_at' => $lpj->created_at ?? now(),
                        'updated_at' => $lpj->created_at ?? now(),
                    ]);

                    if ($lpj->verified_lpj_at) {
                        $isLpjDisetujui = ($ajuan->status_akhir === 'sudah_dilaksanakan');
                        DB::table('riwayat_kegiatans')->insert([
                            'ajuan_kegiatan_id' => $ajuan->id,
                            'user_id' => $lpj->verified_lpj_by,
                            'role' => 'kemahasiswaan',
                            'aksi' => $isLpjDisetujui ? 'Verifikasi LPJ: Disetujui' : 'Verifikasi LPJ: Minta Revisi',
                            'status_sebelumnya' => 'menunggu_verifikasi_lpj',
                            'status_sesudah' => $isLpjDisetujui ? 'sudah_dilaksanakan' : 'revisi_lpj',
                            'catatan' => $lpj->catatan_verifikasi_lpj,
                            'created_at' => $lpj->verified_lpj_at,
                            'updated_at' => $lpj->verified_lpj_at,
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Abaikan error backfill jika database kosong / testing environment baru
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_kegiatans');
    }
};
