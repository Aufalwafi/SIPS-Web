<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanRkt extends Model
{
    protected $table = 'pengajuan_rkt';

    public $timestamps = false;
    const CREATED_AT = 'tanggal_pengajuan';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_kelompok',
        'tahun',
        'file_rkt',
        'status',
        'catatan_admin',
    ];

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class, 'id_kelompok');
    }
}
