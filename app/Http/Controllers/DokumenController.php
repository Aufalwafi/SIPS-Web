<?php

namespace App\Http\Controllers;

use App\Models\Kelompok;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DokumenController extends Controller
{
    /**
     * Buka / Unduh Berkas SK PS
     */
    public function unduhSkPs($id)
    {
        $kelompok = Kelompok::findOrFail($id);
        return $this->serveFile($kelompok->sk_ps, 'SK_PS_' . str_replace(['/', '\\', ' '], '_', $kelompok->nama_kelompok));
    }

    /**
     * Buka / Unduh Berkas SK Pembentukan Kelompok
     */
    public function unduhSkKelompok($id)
    {
        $kelompok = Kelompok::findOrFail($id);
        return $this->serveFile($kelompok->sk_kelompok, 'SK_Pembentukan_' . str_replace(['/', '\\', ' '], '_', $kelompok->nama_kelompok));
    }

    /**
     * Cari berkas di berbagai kemungkinan lokasi penyimpanan lalu stream inline
     */
    protected function serveFile(?string $filename, string $downloadName)
    {
        if (empty($filename)) {
            abort(404, 'Dokumen belum diunggah.');
        }

        $base = basename($filename);

        $candidates = [
            // 1. Direct path di storage Laravel
            storage_path('app/public/' . $filename),
            storage_path('app/public/sk_ps/' . $base),
            storage_path('app/public/sk_kelompok/' . $base),
            // 2. Direct path di public storage
            public_path('storage/' . $filename),
            public_path('storage/sk_ps/' . $base),
            public_path('storage/sk_kelompok/' . $base),
            // 3. Folder legacy junction / symlink
            public_path('legacy_storage/' . $base),
            // 4. Folder legacy fisik di Drive D
            'D:/sipsonline/uploads/' . $base,
            'D:/sipsonline/uploads/' . $filename,
            // 5. Public uploads
            public_path('uploads/' . $filename),
            public_path('uploads/' . $base),
        ];

        foreach ($candidates as $path) {
            if (file_exists($path) && is_file($path)) {
                $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                $mimeType = match($ext) {
                    'pdf' => 'application/pdf',
                    'jpg', 'jpeg' => 'image/jpeg',
                    'png' => 'image/png',
                    default => mime_content_type($path) ?: 'application/octet-stream',
                };

                return response()->file($path, [
                    'Content-Type' => $mimeType,
                    'Content-Disposition' => 'inline; filename="' . $downloadName . '.' . $ext . '"',
                    'Cache-Control' => 'public, max-age=86400',
                ]);
            }
        }

        abort(404, 'Berkas dokumen (' . $base . ') tidak ditemukan di server.');
    }
}
