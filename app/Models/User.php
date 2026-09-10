<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'ukm_id',
        'nim_nip',
        'no_hp',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function ukm(): BelongsTo
    {
        return $this->belongsTo(Ukm::class, 'ukm_id');
    }

    public function ajuanKegiatans(): HasMany
    {
        return $this->hasMany(AjuanKegiatan::class, 'user_id');
    }

    public function isMahasiswa(): bool
    {
        return $this->role === 'mahasiswa';
    }

    public function isPembina(): bool
    {
        return $this->role === 'pembina';
    }

    public function isKemahasiswaan(): bool
    {
        return $this->role === 'kemahasiswaan';
    }
}
