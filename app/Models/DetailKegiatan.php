<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailKegiatan extends Model
{
    use HasFactory;

    protected $table = 'detail_kegiatans';

    protected $fillable = [
        'ajuan_kegiatan_id',
        'no_urut',
        'nama_akun',
        'nama_barang',
        'harga_satuan',
        'jumlah',
        'satuan',
        'durasi',
        'satuan_durasi',
        'total',
        'status_keterangan',
        'catatan',
    ];

    protected $casts = [
        'harga_satuan' => 'decimal:2',
        'jumlah' => 'integer',
        'durasi' => 'float',
        'total' => 'decimal:2',
        'no_urut' => 'integer',
    ];

    public function ajuanKegiatan(): BelongsTo
    {
        return $this->belongsTo(AjuanKegiatan::class, 'ajuan_kegiatan_id');
    }
}
