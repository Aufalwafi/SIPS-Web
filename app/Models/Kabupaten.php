<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Kabupaten extends Model
{
    protected $table = 'kabupaten';

    public $timestamps = false;

    protected $fillable = ['provinsi_id', 'kode', 'nama'];

    public function provinsi(): BelongsTo
    {
        return $this->belongsTo(Provinsi::class, 'provinsi_id');
    }

    public function kecamatan(): HasMany
    {
        return $this->hasMany(Kecamatan::class, 'kabupaten_id');
    }

    public function cdk(): BelongsToMany
    {
        return $this->belongsToMany(Cdk::class, 'cdk_kabupaten', 'kabupaten_id', 'cdk_id');
    }
}
