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
        Schema::create('detail_kegiatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ajuan_kegiatan_id')->constrained('ajuan_kegiatans')->cascadeOnDelete();
            $table->unsignedInteger('no_urut')->default(1);
            $table->string('nama_akun');
            $table->string('nama_barang');
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->unsignedInteger('jumlah')->default(1);
            $table->decimal('total', 15, 2)->default(0);
            $table->enum('status_keterangan', ['belum_diverifikasi', 'disetujui', 'tidak_disetujui'])->default('belum_diverifikasi');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_kegiatans');
    }
};
