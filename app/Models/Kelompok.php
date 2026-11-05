<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelompok extends Model
{
    use SoftDeletes;

    protected $table = 'kelompok';

    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_cdk',
        'desa_id',
        'nama_kelompok',
        'ketua',
        'desa',
        'kecamatan',
        'kabupaten',
        'skema_ps',
        'luas_lahan',
        'no_sk',
        'tanggal_sk',
        'id_pendamping',
        'created_at',
        'sk_ps',
        'sk_kelompok',
        'polygon_kelompok',
        'geometry',
    ];

    protected $casts = [
        'luas_lahan' => 'decimal:2',
        'tanggal_sk' => 'date',
        'polygon_kelompok' => 'array',
    ];

    protected $hidden = [
        'geometry',
    ];

    public function cdk(): BelongsTo
    {
        return $this->belongsTo(Cdk::class, 'id_cdk');
    }

    public function desaWilayah(): BelongsTo
    {
        return $this->belongsTo(Desa::class, 'desa_id');
    }

    public function pendamping()
    {
        return Pendamping::whereIn('pendamping.id_pendamping', function ($query) {
            $query->select('p.id_pendamping')
                ->from('pendamping as p')
                ->join('users as u', 'u.username', '=', 'p.username')
                ->join('pendamping_kelompok as pk', 'pk.id_user', '=', 'u.id')
                ->where('pk.id_kelompok', $this->id);
        })->get();
    }

    public function anggotaKelompok(): HasMany
    {
        return $this->hasMany(AnggotaKelompok::class, 'id_kelompok');
    }

    public function kups(): HasMany
    {
        return $this->hasMany(Kups::class, 'id_kelompok');
    }

    public function kegiatan(): HasMany
    {
        return $this->hasMany(Kegiatan::class, 'id_kelompok');
    }

    public function petakPerhutani(): HasMany
    {
        return $this->hasMany(PetakPerhutani::class, 'id_kelompok');
    }

    public function laporanKegiatan(): HasMany
    {
        return $this->hasMany(LaporanKegiatan::class, 'id_kelompok');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'id_kelompok');
    }

    public function getTanggalSkFormattedAttribute(): string
    {
        if ($this->tanggal_sk) {
            try {
                $date = \Carbon\Carbon::parse($this->tanggal_sk);
                if ($date->year > 1900) {
                    return $date->translatedFormat('d F Y');
                }
            } catch (\Throwable $e) {}
        }

        if (!empty($this->polygon_kelompok)) {
            try {
                $geom = is_array($this->polygon_kelompok)
                    ? $this->polygon_kelompok
                    : json_decode($this->polygon_kelompok, true);

                $tglProp = $geom['features'][0]['properties']['TGL_SK_PPH']
                    ?? $geom['features'][0]['properties']['TGL_SK_PAK']
                    ?? null;

                if (!empty($tglProp)) {
                    $date = \Carbon\Carbon::parse($tglProp);
                    if ($date->year > 1900) {
                        return $date->translatedFormat('d F Y');
                    }
                }
            } catch (\Throwable $e) {}
        }

        return '-';
    }
}
