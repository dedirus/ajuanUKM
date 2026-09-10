<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ukm extends Model
{
    use HasFactory;

    protected $table = 'ukms';

    protected $fillable = [
        'nama_ukm',
        'kode',
        'kategori',
        'deskripsi',
        'pembina_nama',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function ajuanKegiatans(): HasMany
    {
        return $this->hasMany(AjuanKegiatan::class);
    }
}
