<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cdk extends Model
{
    protected $table = 'cdk';

    public $timestamps = false;

    protected $fillable = ['provinsi_id', 'kode', 'nama', 'keterangan'];

    public function provinsi(): BelongsTo
    {
        return $this->belongsTo(Provinsi::class, 'provinsi_id');
    }

    public function kabupaten(): BelongsToMany
    {
        return $this->belongsToMany(Kabupaten::class, 'cdk_kabupaten', 'cdk_id', 'kabupaten_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'id_cdk');
    }

    public function kelompok(): HasMany
    {
        return $this->hasMany(Kelompok::class, 'id_cdk');
    }
}
