<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermasalahanKelompok extends Model
{
    use SoftDeletes;

    protected $table = 'permasalahan_kelompok';

    public $timestamps = false;

    protected $fillable = [
        'id_kelompok',
        'judul',
        'deskripsi',
        'status',
    ];

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class, 'id_kelompok');
    }
}
