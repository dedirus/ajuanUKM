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
        Schema::create('ajuan_kegiatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('ukm_id')->constrained('ukms')->cascadeOnDelete();
            $table->string('nama_kegiatan');
            $table->date('tanggal_kegiatan');
            $table->string('dokumen_proposal');
            $table->string('dokumen_nama_asli');
            $table->decimal('total_anggaran', 15, 2)->default(0);

            // Verifikasi Pembina
            $table->string('status_pembina', 50)->default('belum_disetujui');
            $table->text('catatan_pembina')->nullable();
            $table->timestamp('verified_pembina_at')->nullable();
            $table->foreignId('verified_pembina_by')->nullable()->constrained('users')->nullOnDelete();

            // Verifikasi Kemahasiswaan
            $table->string('status_kemahasiswaan', 50)->default('belum_disetujui');
            $table->text('catatan_kemahasiswaan')->nullable();
            $table->timestamp('verified_kemahasiswaan_at')->nullable();
            $table->foreignId('verified_kemahasiswaan_by')->nullable()->constrained('users')->nullOnDelete();

            // Status Keseluruhan & Tracking Revisi
            $table->string('status_akhir', 50)->default('menunggu_pembina');
            $table->unsignedInteger('revisi_count')->default(0);
            $table->text('catatan_revisi_terakhir')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ajuan_kegiatans');
    }
};
