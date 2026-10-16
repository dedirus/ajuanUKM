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
        Schema::table('ajuan_kegiatans', function (Blueprint $table) {
            $table->index(['ukm_id', 'status_akhir'], 'idx_ajuan_ukm_status');
            $table->index(['tanggal_kegiatan', 'status_akhir'], 'idx_ajuan_tgl_status');
            $table->index(['user_id', 'status_akhir'], 'idx_ajuan_user_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ajuan_kegiatans', function (Blueprint $table) {
            $table->dropIndex('idx_ajuan_ukm_status');
            $table->dropIndex('idx_ajuan_tgl_status');
            $table->dropIndex('idx_ajuan_user_status');
        });
    }
};
