<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LpjPrestasi extends Model
{
    use HasFactory;

    protected $table = 'lpj_prestasies';

    protected $fillable = [
        'lpj_kegiatan_id',
        'kategori_lomba',
        'peringkat_juara',
        'tingkat',
        'nama_peraih',
    ];

    public function lpj(): BelongsTo
    {
        return $this->belongsTo(LpjKegiatan::class, 'lpj_kegiatan_id');
    }
}
