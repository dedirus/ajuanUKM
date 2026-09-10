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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['mahasiswa', 'pembina', 'kemahasiswaan'])->default('mahasiswa')->after('email');
            $table->foreignId('ukm_id')->nullable()->after('role')->constrained('ukms')->nullOnDelete();
            $table->string('nim_nip')->nullable()->after('ukm_id');
            $table->string('no_hp')->nullable()->after('nim_nip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['ukm_id']);
            $table->dropColumn(['role', 'ukm_id', 'nim_nip', 'no_hp']);
        });
    }
};
