<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PotensiKelompok extends Model
{
    use SoftDeletes;

    protected $table = 'potensi_kelompok';

    public $timestamps = false;

    protected $fillable = [
        'id_kelompok',
        'jenis_potensi',
        'jenis_detail',
        'tahun_tanam',
        'luasan',
        'satuan',
        'kondisi',
        'lokasi',
        'keterangan',
        'foto',
    ];

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class, 'id_kelompok');
    }
}
