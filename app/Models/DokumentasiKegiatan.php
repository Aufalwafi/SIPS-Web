<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumentasiKegiatan extends Model
{
    protected $table = 'dokumentasi_kegiatan';

    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $guarded = [];

    protected $casts = [
        'created_at'             => 'datetime',
        'google_drive_synced_at' => 'datetime',
    ];

    protected $appends = ['foto', 'foto_url', 'google_drive_url'];

    /**
     * Accessor URL langsung Google Drive
     */
    public function getGoogleDriveUrlAttribute(): ?string
    {
        if ($this->google_drive_file_id) {
            return "https://drive.google.com/file/d/{$this->google_drive_file_id}/view";
        }
        return null;
    }

    /**
     * Accessor agar pemanggilan $doc->foto tetap berjalan lancar
     */
    public function getFotoAttribute()
    {
        return $this->nama_file ?? basename($this->file_path ?? '');
    }

    /**
     * Accessor URL lengkap foto untuk pratinjau di Blade
     */
    public function getFotoUrlAttribute()
    {
        if (!empty($this->file_path) && file_exists(public_path($this->file_path))) {
            return asset($this->file_path);
        }
        $filename = $this->nama_file ?? basename($this->file_path ?? '');
        if ($filename) {
            if (file_exists(public_path('storage/kegiatan/' . $filename))) {
                return asset('storage/kegiatan/' . $filename);
            }
            if (file_exists(public_path('uploads/dokumentasi/' . $filename))) {
                return asset('uploads/dokumentasi/' . $filename);
            }
            if (file_exists(public_path('uploads/dokumentasi_kegiatan/' . $filename))) {
                return asset('uploads/dokumentasi_kegiatan/' . $filename);
            }
        }
        return asset('storage/kegiatan/' . ($filename ?: 'default.jpg'));
    }

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class, 'id_kegiatan');
    }
}
