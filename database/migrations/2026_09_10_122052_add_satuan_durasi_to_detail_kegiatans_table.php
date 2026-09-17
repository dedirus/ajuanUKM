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
        Schema::table('detail_kegiatans', function (Blueprint $table) {
            $table->string('satuan_durasi', 50)->default('Hari')->after('durasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_kegiatans', function (Blueprint $table) {
            $table->dropColumn('satuan_durasi');
        });
    }
};
