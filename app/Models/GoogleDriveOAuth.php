<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model GoogleDriveOAuth — token OAuth Google Drive.
 *
 * Token ini bersama seluruh sistem, tidak terhubung ke user/pendamping manapun.
 * Struktur aktual dari database sips:
 * - email, access_token, refresh_token, expires_at, created_at, updated_at
 */
class GoogleDriveOAuth extends Model
{
    protected $table = 'google_drive_oauth';

    public $timestamps = true;

    protected $fillable = [
        'email',
        'access_token',
        'refresh_token',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
