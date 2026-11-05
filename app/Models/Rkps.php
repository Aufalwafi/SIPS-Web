<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rkps extends Model
{
    protected $table = 'rkps';

    protected $primaryKey = 'id_rkps';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_kelompok',
        'tahun_mulai',
        'tahun_selesai',
        'no_penilaian',
        'tgl_penilaian',
        'no_pengesahan',
        'tgl_pengesahan',
        'file_rkps',
    ];

    protected $casts = [
        'tahun_mulai' => 'integer',
        'tahun_selesai' => 'integer',
        'tgl_penilaian' => 'date',
        'tgl_pengesahan' => 'date',
    ];

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class, 'id_kelompok');
    }

    public function rkt(): HasMany
    {
        return $this->hasMany(Rkt::class, 'id_rkps');
    }
}
