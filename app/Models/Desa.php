<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Desa extends Model
{
    protected $table = 'desa';

    public $timestamps = false;

    protected $fillable = ['kecamatan_id', 'kode', 'kode_2013', 'kode_2012', 'nama'];

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id');
    }

    public function kelompok(): HasMany
    {
        return $this->hasMany(Kelompok::class, 'desa_id');
    }
}
