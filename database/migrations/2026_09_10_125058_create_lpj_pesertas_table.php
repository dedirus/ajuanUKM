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
        Schema::create('lpj_pesertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lpj_kegiatan_id')->constrained('lpj_kegiatans')->cascadeOnDelete();
            $table->string('nama', 150);
            $table->string('npm', 50);
            $table->string('peran', 50)->nullable()->default('Peserta');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lpj_pesertas');
    }
};
