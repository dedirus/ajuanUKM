<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LpjKegiatan extends Model
{
    use HasFactory;

    protected $table = 'lpj_kegiatans';

    protected $fillable = [
        'ajuan_kegiatan_id',
        'prestasi',
        'jumlah_peserta',
        'ringkasan_kegiatan',
        'realisasi_anggaran',
        'file_sertifikat',
        'file_sertifikat_nama',
        'foto_kegiatan',
        'foto_kegiatan_nama',
        'foto_penyerahan',
        'foto_penyerahan_nama',
        'surat_undangan',
        'surat_undangan_nama',
        'catatan_verifikasi_lpj',
        'verified_lpj_at',
        'verified_lpj_by',
        'submitted_at',
    ];

    protected $casts = [
        'realisasi_anggaran' => 'decimal:2',
        'jumlah_peserta' => 'integer',
        'verified_lpj_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function ajuan(): BelongsTo
    {
        return $this->belongsTo(AjuanKegiatan::class, 'ajuan_kegiatan_id');
    }

    public function pesertas(): HasMany
    {
        return $this->hasMany(LpjPeserta::class, 'lpj_kegiatan_id');
    }

    public function prestasies(): HasMany
    {
        return $this->hasMany(LpjPrestasi::class, 'lpj_kegiatan_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_lpj_by');
    }
}
