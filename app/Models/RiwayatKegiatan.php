<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatKegiatan extends Model
{
    use HasFactory;

    protected $table = 'riwayat_kegiatans';

    protected $fillable = [
        'ajuan_kegiatan_id',
        'user_id',
        'role',
        'aksi',
        'status_sebelumnya',
        'status_sesudah',
        'catatan',
    ];

    public function ajuanKegiatan(): BelongsTo
    {
        return $this->belongsTo(AjuanKegiatan::class, 'ajuan_kegiatan_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Ikon visual yang mewakili aksi.
     */
    public function getIconAttribute(): string
    {
        return match (true) {
            str_contains(strtolower($this->aksi), 'baru') => '📝',
            str_contains(strtolower($this->aksi), 'pembaruan') => '✏️',
            str_contains(strtolower($this->aksi), 'revisi usulan') => '🔄',
            str_contains(strtolower($this->aksi), 'rab') => '💰',
            str_contains(strtolower($this->aksi), 'persetujuan') => '✅',
            str_contains(strtolower($this->aksi), 'penolakan') => '❌',
            str_contains(strtolower($this->aksi), 'permintaan revisi') => '⚠️',
            str_contains(strtolower($this->aksi), 'penyerahan') || str_contains(strtolower($this->aksi), 'perbaikan laporan') => '📑',
            str_contains(strtolower($this->aksi), 'verifikasi lpj') && str_contains(strtolower($this->aksi), 'setuju') => '🏆',
            str_contains(strtolower($this->aksi), 'verifikasi lpj') && str_contains(strtolower($this->aksi), 'revisi') => '⚠️',
            default => '📌',
        };
    }

    /**
     * Warna background bundaran ikon di timeline.
     */
    public function getIconBgClassAttribute(): string
    {
        return match (true) {
            str_contains(strtolower($this->aksi), 'penolakan') => 'bg-rose-100 text-rose-600 border-rose-200',
            str_contains(strtolower($this->aksi), 'revisi') => 'bg-amber-100 text-amber-700 border-amber-200',
            str_contains(strtolower($this->aksi), 'setuju') => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            str_contains(strtolower($this->aksi), 'rab') => 'bg-purple-100 text-purple-700 border-purple-200',
            str_contains(strtolower($this->aksi), 'lpj') => 'bg-indigo-100 text-indigo-700 border-indigo-200',
            str_contains(strtolower($this->aksi), 'pembaruan') => 'bg-sky-100 text-sky-700 border-sky-200',
            default => 'bg-blue-100 text-blue-700 border-blue-200',
        };
    }

    /**
     * Badge label role pelaku aksi.
     */
    public function getRoleBadgeClassAttribute(): string
    {
        return match (strtolower($this->role)) {
            'pembina' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            'kemahasiswaan' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'sistem' => 'bg-gray-100 text-gray-700 border-gray-200',
            default => 'bg-blue-50 text-blue-700 border-blue-200',
        };
    }

    /**
     * Format label nama role pengguna.
     */
    public function getRoleLabelAttribute(): string
    {
        return match (strtolower($this->role)) {
            'pembina' => 'Dosen Pembina UKM',
            'kemahasiswaan' => 'Biro Kemahasiswaan',
            'sistem' => 'Sistem Otomatis',
            default => 'Mahasiswa / Pengusul',
        };
    }
}
