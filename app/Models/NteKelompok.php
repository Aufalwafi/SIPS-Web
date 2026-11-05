<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NteKelompok extends Model
{
    protected $table = 'nte_kelompok';

    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_kelompok',
        'id_kups',
        'id_pendamping',
        'nama_produk',
        'luas_areal',
        'pola_pengolahan',
        'pola_pengelolaan',
        'potensi_jasa_lingkungan',
        'sumber_modal',
        'area_pemasaran',
        'tahun',
        'satuan',
    ];

    protected $casts = [
        'tahun' => 'integer',
    ];

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class, 'id_kelompok');
    }

    public function kups(): BelongsTo
    {
        return $this->belongsTo(Kups::class, 'id_kups');
    }

    public function pendamping(): BelongsTo
    {
        return $this->belongsTo(Pendamping::class, 'id_pendamping');
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(NteTransaksi::class, 'id_nte_kelompok');
    }
}
