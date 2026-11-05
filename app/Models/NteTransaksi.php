<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NteTransaksi extends Model
{
    protected $table = 'nte_transaksi';

    public $timestamps = false;

    protected $fillable = [
        'id_nte_kelompok',
        'tanggal',
        'komoditas',
        'volume',
        'satuan',
        'harga_satuan',
        'total_harga',
        'keterangan',
    ];

    protected $casts = [
        'volume' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
        'total_harga' => 'decimal:2',
        'tanggal' => 'date',
    ];

    public function nteKelompok(): BelongsTo
    {
        return $this->belongsTo(NteKelompok::class, 'id_nte_kelompok');
    }
}
