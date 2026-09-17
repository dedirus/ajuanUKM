<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LpjPeserta extends Model
{
    use HasFactory;

    protected $table = 'lpj_pesertas';

    protected $fillable = [
        'lpj_kegiatan_id',
        'nama',
        'npm',
        'peran',
    ];

    public function lpj(): BelongsTo
    {
        return $this->belongsTo(LpjKegiatan::class, 'lpj_kegiatan_id');
    }
}
