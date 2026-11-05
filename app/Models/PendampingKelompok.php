<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model PendampingKelompok — pivot table relasi many-to-many antara User (pendamping) dan Kelompok.
 *
 * Relasi:
 * - user() -> User (FK: id_user -> users.id, verified)
 * - kelompok() -> Kelompok (FK: id_kelompok -> kelompok.id, verified)
 *
 * Unique constraint: (id_user, id_kelompok) — sudah divalidasi 0 duplikat.
 * Tidak ada timestamp di struktur asli.
 */
class PendampingKelompok extends Model
{
    protected $table = 'pendamping_kelompok';

    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'id_kelompok',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class, 'id_kelompok');
    }
}
