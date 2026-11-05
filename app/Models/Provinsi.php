<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Provinsi extends Model
{
    protected $table = 'provinsi';

    public $timestamps = false;

    protected $fillable = ['nama'];

    public function cdk(): HasMany
    {
        return $this->hasMany(Cdk::class, 'provinsi_id');
    }

    public function kabupaten(): HasMany
    {
        return $this->hasMany(Kabupaten::class, 'provinsi_id');
    }
}
