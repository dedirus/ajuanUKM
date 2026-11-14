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
            $table->string('tempat_kegiatan', 255)->nullable()->after('jenis_kegiatan');
            $table->string('link_undangan', 500)->nullable()->after('tempat_kegiatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ajuan_kegiatans', function (Blueprint $table) {
            $table->dropColumn(['tempat_kegiatan', 'link_undangan']);
        });
    }
};
