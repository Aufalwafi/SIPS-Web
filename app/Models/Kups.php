<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Kups — Kelompok Usaha Perhutanan Sosial.
 *
 * Relasi:
 * - kelompok() -> Kelompok (FK: id_kelompok -> kelompok.id, verified)
 * - nteKelompok() -> HasMany NteKelompok (FK: id_kups -> kups.id, verified)
 *
 * Kolom kelas_kups: enum('Biru','Perak','Emas','Platinum').
 * Kolom tahun_bentuk: year(4), MySQL YEAR type.
 */
class Kups extends Model
{
    use SoftDeletes;

    protected $table = 'kups';

    public $timestamps = false;

    protected $fillable = [
        'id_kelompok',
        'nama_kups',
        'ketua_kups',
        'jumlah_anggota',
        'kelas_kups',
        'tahun_bentuk',
        'jenis_usaha',
        'produk',
        'produksi_tahun',
        'satuan_produksi',
        'keterangan',
    ];

    protected $casts = [
        'tahun_bentuk' => 'integer', // MySQL YEAR -> integer
        'jumlah_anggota' => 'integer',
        'produksi_tahun' => 'integer',
    ];

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class, 'id_kelompok');
    }

    public function nteKelompok(): HasMany
    {
        return $this->hasMany(NteKelompok::class, 'id_kups');
    }
}
