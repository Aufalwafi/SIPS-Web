<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rkt extends Model
{
    protected $table = 'rkt';

    protected $primaryKey = 'id_rkt';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_kelompok',
        'id_rkps',
        'tahun',
        'file_rkt',
        'no_penetapan',
        'tgl_penetapan',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'tgl_penetapan' => 'date',
    ];

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class, 'id_kelompok');
    }

    public function rkps(): BelongsTo
    {
        return $this->belongsTo(Rkps::class, 'id_rkps', 'id_rkps');
    }
}
