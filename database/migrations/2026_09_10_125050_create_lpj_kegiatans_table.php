<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lpj_kegiatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ajuan_kegiatan_id')->constrained('ajuan_kegiatans')->cascadeOnDelete()->unique();

            // Bagian 1: Capaian & Prestasi
            $table->string('prestasi', 100)->nullable();
            $table->unsignedInteger('jumlah_peserta')->nullable()->default(0);
            $table->text('ringkasan_kegiatan')->nullable();

            // Bagian 3: Realisasi Anggaran
            $table->decimal('realisasi_anggaran', 15, 2)->nullable()->default(0);

            // Bagian 4: Berkas Bukti (4 Unggahan Opsional)
            $table->string('file_sertifikat')->nullable();
            $table->string('file_sertifikat_nama')->nullable();

            $table->string('foto_kegiatan')->nullable();
            $table->string('foto_kegiatan_nama')->nullable();

            $table->string('foto_penyerahan')->nullable();
            $table->string('foto_penyerahan_nama')->nullable();

            $table->string('surat_undangan')->nullable();
            $table->string('surat_undangan_nama')->nullable();

            // Verifikasi LPJ oleh Kemahasiswaan
            $table->text('catatan_verifikasi_lpj')->nullable();
            $table->timestamp('verified_lpj_at')->nullable();
            $table->foreignId('verified_lpj_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lpj_kegiatans');
    }
};
