<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AjuanKegiatan extends Model
{
    use HasFactory;

    protected $table = 'ajuan_kegiatans';

    protected $fillable = [
        'user_id',
        'ukm_id',
        'nama_kegiatan',
        'tanggal_kegiatan',
        'jenis_kegiatan',
        'dokumen_proposal',
        'dokumen_nama_asli',
        'total_anggaran',
        'status_pembina',
        'catatan_pembina',
        'verified_pembina_at',
        'verified_pembina_by',
        'status_kemahasiswaan',
        'catatan_kemahasiswaan',
        'verified_kemahasiswaan_at',
        'verified_kemahasiswaan_by',
        'status_akhir',
        'revisi_count',
        'catatan_revisi_terakhir',
    ];

    protected $casts = [
        'tanggal_kegiatan' => 'date',
        'verified_pembina_at' => 'datetime',
        'verified_kemahasiswaan_at' => 'datetime',
        'total_anggaran' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ukm(): BelongsTo
    {
        return $this->belongsTo(Ukm::class, 'ukm_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(DetailKegiatan::class, 'ajuan_kegiatan_id')->orderBy('no_urut', 'asc');
    }

    public function pembinaVerifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_pembina_by');
    }

    public function kemahasiswaanVerifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_kemahasiswaan_by');
    }

    public function recalculateTotal(): void
    {
        $this->total_anggaran = $this->details()->sum('total');
        $this->save();
    }

    public function isBisaDieditMahasiswa(): bool
    {
        return ! in_array($this->status_akhir, ['disetujui', 'sudah_dilaksanakan', 'menunggu_verifikasi_lpj', 'revisi_lpj']);
    }

    public function isPerluRevisi(): bool
    {
        return in_array($this->status_akhir, ['revisi_pembina', 'revisi_kemahasiswaan', 'ditolak'])
            || $this->status_pembina === 'ditolak'
            || $this->status_kemahasiswaan === 'ditolak'
            || $this->status_pembina === 'revisi'
            || $this->status_kemahasiswaan === 'revisi';
    }

    public function isRevisiPembina(): bool
    {
        return $this->status_akhir === 'revisi_pembina'
            || $this->status_pembina === 'ditolak'
            || $this->status_pembina === 'revisi';
    }

    public function isRevisiKemahasiswaan(): bool
    {
        return $this->status_akhir === 'revisi_kemahasiswaan'
            || $this->status_kemahasiswaan === 'ditolak'
            || $this->status_kemahasiswaan === 'revisi'
            || ($this->status_akhir === 'ditolak' && $this->status_pembina !== 'ditolak');
    }

    public function lpj(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(LpjKegiatan::class, 'ajuan_kegiatan_id');
    }

    public function isSiapLpj(): bool
    {
        return $this->status_akhir === 'disetujui'
            && $this->tanggal_kegiatan->lessThanOrEqualTo(today());
    }

    public function isMenungguVerifikasiLpj(): bool
    {
        return $this->status_akhir === 'menunggu_verifikasi_lpj';
    }

    public function isRevisiLpj(): bool
    {
        return $this->status_akhir === 'revisi_lpj';
    }

    public function isSudahDilaksanakan(): bool
    {
        return $this->status_akhir === 'sudah_dilaksanakan';
    }

    public function riwayats(): HasMany
    {
        return $this->hasMany(RiwayatKegiatan::class, 'ajuan_kegiatan_id')->orderBy('created_at', 'asc')->orderBy('id', 'asc');
    }

    /**
     * Catat jejak riwayat aktivitas usulan kegiatan.
     */
    public function catatRiwayat(
        string $aksi,
        ?string $statusSesudah = null,
        ?string $catatan = null,
        ?string $statusSebelumnya = null,
        ?int $userId = null,
        ?string $role = null
    ): RiwayatKegiatan {
        $user = $userId ? User::find($userId) : auth()->user();
        $roleName = $role ?? ($user?->role ?? 'sistem');

        return $this->riwayats()->create([
            'user_id' => $user?->id,
            'role' => $roleName,
            'aksi' => $aksi,
            'status_sebelumnya' => $statusSebelumnya,
            'status_sesudah' => $statusSesudah ?? $this->status_akhir,
            'catatan' => $catatan,
        ]);
    }
}
