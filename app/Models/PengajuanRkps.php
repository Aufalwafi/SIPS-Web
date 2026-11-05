<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanRkps extends Model
{
    protected $table = 'pengajuan_rkps';

    public $timestamps = false;
    const CREATED_AT = 'tanggal_pengajuan';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_kelompok',
        'tahun_mulai',
        'tahun_selesai',
        'file_rkps',
        'status',
        'catatan_admin',
    ];

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class, 'id_kelompok');
    }
}
