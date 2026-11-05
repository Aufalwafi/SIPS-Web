<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kegiatan extends Model
{
    use SoftDeletes;

    protected $table = 'kegiatan';

    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_kelompok',
        'nama_kegiatan',
        'tanggal',
        'lokasi',
        'keterangan',
        'kml_file',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class, 'id_kelompok');
    }

    public function dokumentasiKegiatan(): HasMany
    {
        return $this->hasMany(DokumentasiKegiatan::class, 'id_kegiatan');
    }

    public function dokumentasi(): HasMany
    {
        return $this->hasMany(DokumentasiKegiatan::class, 'id_kegiatan');
    }
}
