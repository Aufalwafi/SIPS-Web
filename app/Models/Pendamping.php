<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pendamping extends Model
{
    use SoftDeletes;

    protected $table = 'pendamping';

    protected $primaryKey = 'id_pendamping';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = true;

    protected $fillable = [
        'id_cdk',
        'nama_pendamping',
        'nip',
        'username',
        'password',
        'no_hp',
        'email',
        'alamat',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    public function cdk(): BelongsTo
    {
        return $this->belongsTo(Cdk::class, 'id_cdk');
    }

    public function kelompok()
    {
        return Kelompok::whereIn('kelompok.id', function ($query) {
            $query->select('pk.id_kelompok')
                ->from('pendamping_kelompok as pk')
                ->join('users as u', 'u.id', '=', 'pk.id_user')
                ->where('u.username', $this->username);
        })->get();
    }
}
