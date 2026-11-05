<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model AnggotaKelompok — data anggota dari setiap kelompok tani hutan.
 *
 * Polygon per anggota adalah GeoJSON (text), bukan kolom spatial native.
 * Relasi ke kelompok melalui id_kelompok -> kelompok.id (FK verified).
 */
class AnggotaKelompok extends Model
{
    use SoftDeletes;

    protected $table = 'anggota_kelompok';

    public $timestamps = false;

    protected $fillable = [
        'id_kelompok',
        'nama_anggota',
        'nik',
        'jenis_kelamin',
        'alamat',
        'luas_garapan',
        'komoditas_utama',
        'polygon',
    ];

    protected $casts = [
        'luas_garapan' => 'decimal:2',
        'polygon' => 'array', // GeoJSON -> array PHP
    ];

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class, 'id_kelompok');
    }
}
