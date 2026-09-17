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
        Schema::create('lpj_prestasies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lpj_kegiatan_id')->constrained('lpj_kegiatans')->cascadeOnDelete();
            $table->string('kategori_lomba', 150);
            $table->string('peringkat_juara', 100);
            $table->string('tingkat', 100)->nullable()->default('Nasional');
            $table->string('nama_peraih', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lpj_prestasies');
    }
};
