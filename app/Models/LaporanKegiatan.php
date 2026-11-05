<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model LaporanKegiatan — laporan kegiatan yang dibuat oleh kelompok atau pendamping.
 *
 * Relasi:
 * - kelompok() -> Kelompok (FK: id_kelompok -> kelompok.id, verified)
 * - user() -> User (FK: id_user -> users.id, verified)
 *
 * Kolom `foto` menyimpan nama file foto (bukan path lengkap).
 * Kolom `role_pembuat` berisi 'kelompok' atau 'pendamping'.
 */
class LaporanKegiatan extends Model
{
    protected $table = 'laporan_kegiatan';

    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_kelompok',
        'id_user',
        'role_pembuat',
        'tanggal',
        'kegiatan',
        'deskripsi',
        'foto',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class, 'id_kelompok');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
