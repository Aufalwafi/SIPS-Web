<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CdkKabupaten extends Model
{
    protected $table = 'cdk_kabupaten';

    public $timestamps = false;

    protected $fillable = ['cdk_id', 'kabupaten_id'];

    public function cdk(): BelongsTo
    {
        return $this->belongsTo(Cdk::class, 'cdk_id');
    }

    public function kabupaten(): BelongsTo
    {
        return $this->belongsTo(Kabupaten::class, 'kabupaten_id');
    }
}
