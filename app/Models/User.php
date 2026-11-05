<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    protected $table = 'users';

    public $timestamps = true;
    const UPDATED_AT = null;

    protected $fillable = [
        'nama',
        'username',
        'password',
        'role',
        'id_cdk',
        'id_kelompok',
        'created_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'role' => 'string',
        'created_at' => 'datetime',
    ];

    public const ROLE_ADMIN = 'admin';
    public const ROLE_ADMIN_CDK = 'admin_cdk';
    public const ROLE_PENDAMPING = 'pendamping';
    public const ROLE_KELOMPOK = 'kelompok';

    public function cdk(): BelongsTo
    {
        return $this->belongsTo(Cdk::class, 'id_cdk');
    }

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class, 'id_kelompok');
    }

    public function kelompokBinaan()
    {
        return Kelompok::whereIn('kelompok.id', function ($query) {
            $query->select('pk.id_kelompok')
                ->from('pendamping_kelompok as pk')
                ->where('pk.id_user', $this->id);
        })->get();
    }

    public function userLogs(): HasMany
    {
        return $this->hasMany(UserLog::class, 'id_user');
    }

    public function laporanKegiatan(): HasMany
    {
        return $this->hasMany(LaporanKegiatan::class, 'id_user');
    }
}
