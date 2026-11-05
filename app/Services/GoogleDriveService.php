<?php

namespace App\Services;

use App\Models\DokumentasiKegiatan;
use App\Models\GoogleDriveOAuth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleDriveService
{
    /**
     * Dapatkan Folder ID tujuan dari konfigurasi
     */
    public function getFolderId(): ?string
    {
        return config('services.google_drive.folder_id') ?: null;
    }

    /**
     * Dapatkan Link Folder Google Drive publik
     */
    public function getFolderUrl(): string
    {
        return config('services.google_drive.folder_url') 
            ?: 'https://drive.google.com/drive/folders/' . $this->getFolderId();
    }

    /**
     * Cek apakah Google Drive terhubung dengan token akses valid
     */
    public function isConnected(): bool
    {
        return !empty($this->getAccessToken());
    }

    /**
     * Dapatkan token akses aktif (auto-refresh jika expired)
     */
    public function getAccessToken(): ?string
    {
        $oauth = GoogleDriveOAuth::first();
        if (!$oauth) {
            Log::warning('[GoogleDrive] Record google_drive_oauth tidak ditemukan di database.');
            return null;
        }

        // Cek apakah access_token masih berlaku
        if ($oauth->access_token && $oauth->expires_at && $oauth->expires_at->isFuture()) {
            return $oauth->access_token;
        }

        // Jika expired, coba refresh token
        $clientId = config('services.google_drive.client_id');
        $clientSecret = config('services.google_drive.client_secret');

        if (!$oauth->refresh_token) {
            Log::warning('[GoogleDrive] Refresh token tidak tersedia di database.');
            return $oauth->access_token; // Gunakan fallback access token yang ada jika masih bisa
        }

        if (!$clientId || !$clientSecret) {
            // Coba perbarui token menggunakan OAuth Playground refresh endpoint
            try {
                $pgResponse = Http::timeout(15)->post('https://developers.google.com/oauthplayground/refreshAccessToken', [
                    'token_uri'     => 'https://oauth2.googleapis.com/token',
                    'refresh_token' => $oauth->refresh_token,
                ]);

                if ($pgResponse->successful() && $pgResponse->json('success') && $pgResponse->json('access_token')) {
                    $oauth->access_token = $pgResponse->json('access_token');
                    $expiresIn = $pgResponse->json('expires_in') ?? 3600;
                    $oauth->expires_at = now()->addSeconds($expiresIn);
                    $oauth->save();

                    Log::info('[GoogleDrive] Access token berhasil diperbarui secara otomatis.');
                    return $oauth->access_token;
                }
            } catch (\Throwable $pe) {
                Log::warning('[GoogleDrive] Refresh via playground endpoint gagal: ' . $pe->getMessage());
            }

            Log::info('[GoogleDrive] GOOGLE_CLIENT_ID / GOOGLE_CLIENT_SECRET belum dikonfigurasi di .env. Menggunakan access_token yang tersedia.');
            return $oauth->access_token;
        }

        try {
            $response = Http::asForm()->timeout(15)->post('https://oauth2.googleapis.com/token', [
                'client_id'     => $clientId,
                'client_secret' => $clientSecret,
                'refresh_token' => $oauth->refresh_token,
                'grant_type'    => 'refresh_token',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $oauth->access_token = $data['access_token'];
                $expiresIn = $data['expires_in'] ?? 3600;
                $oauth->expires_at = now()->addSeconds($expiresIn);
                $oauth->save();

                Log::info('[GoogleDrive] Access token berhasil diperbarui.');
                return $oauth->access_token;
            }

            Log::error('[GoogleDrive] Gagal refresh token Google: ' . $response->body());
        } catch (\Throwable $e) {
            Log::error('[GoogleDrive] Exception saat refresh token: ' . $e->getMessage());
        }

        return $oauth->access_token;
    }

    /**
     * Upload file ke Google Drive (REST API v3 multipart upload)
     *
     * @param string $absoluteFilePath Path absolut file di server lokal
     * @param string $fileName Nama file yang akan ditampilkan di Google Drive
     * @param string $mimeType Mime type file (default image/jpeg)
     * @param string|null $folderId ID folder tujuan (opsional)
     * @return array ['success' => bool, 'file_id' => ?string, 'web_view_link' => ?string, 'error' => ?string]
     */
    public function uploadFile(string $absoluteFilePath, string $fileName, string $mimeType = 'image/jpeg', ?string $folderId = null): array
    {
        if (!file_exists($absoluteFilePath)) {
            return [
                'success' => false,
                'error'   => 'File fisik tidak ditemukan pada path: ' . $absoluteFilePath,
            ];
        }

        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return [
                'success' => false,
                'error'   => 'Tidak ada token akses Google Drive yang aktif.',
            ];
        }

        $targetFolder = $folderId ?: $this->getFolderId();

        try {
            $fileContent = file_get_contents($absoluteFilePath);
            if ($fileContent === false) {
                return ['success' => false, 'error' => 'Gagal membaca konten file.'];
            }

            $metadata = ['name' => $fileName];
            if ($targetFolder) {
                $metadata['parents'] = [$targetFolder];
            }

            $boundary = '-------' . md5(time() . microtime());
            $delimiter = "\r\n--" . $boundary . "\r\n";
            $closeDelimiter = "\r\n--" . $boundary . "--";

            $multipartBody =
                $delimiter .
                "Content-Type: application/json; charset=UTF-8\r\n\r\n" .
                json_encode($metadata) .
                $delimiter .
                "Content-Type: " . $mimeType . "\r\n" .
                "Content-Transfer-Encoding: base64\r\n\r\n" .
                base64_encode($fileContent) .
                $closeDelimiter;

            $response = Http::withHeaders([
                'Authorization'  => 'Bearer ' . $accessToken,
                'Content-Type'   => 'multipart/related; boundary=' . $boundary,
                'Content-Length' => strlen($multipartBody),
            ])->timeout(30)
              ->withBody($multipartBody, 'multipart/related; boundary=' . $boundary)
              ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart');

            if ($response->successful()) {
                $data = $response->json();
                $fileId = $data['id'] ?? null;
                $webViewLink = $fileId ? "https://drive.google.com/file/d/{$fileId}/view" : null;

                Log::info("[GoogleDrive] Berhasil mengunggah {$fileName} ke Google Drive. File ID: {$fileId}");

                return [
                    'success'       => true,
                    'file_id'       => $fileId,
                    'web_view_link' => $webViewLink,
                ];
            }

            $errorMsg = $response->json('error.message') ?? $response->body();
            Log::error("[GoogleDrive] Gagal upload file {$fileName}: " . $errorMsg);

            return [
                'success' => false,
                'error'   => $errorMsg,
            ];
        } catch (\Throwable $e) {
            Log::error("[GoogleDrive] Exception saat upload {$fileName}: " . $e->getMessage());
            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }

    /**
     * Cache folder ID di Google Drive agar tidak memanggil API berulang-ulang
     */
    protected static array $folderCache = [];

    /**
     * Cari path absolut file fisik lokal untuk record DokumentasiKegiatan
     */
    public function findPhysicalPath(DokumentasiKegiatan $doc): ?string
    {
        if (!empty($doc->file_path) && file_exists(public_path($doc->file_path))) {
            return public_path($doc->file_path);
        }

        $filename = $doc->nama_file ?? basename($doc->file_path ?? '');
        if (!$filename) {
            return null;
        }

        $possiblePaths = [
            public_path('storage/kegiatan/' . $filename),
            storage_path('app/public/kegiatan/' . $filename),
            public_path('uploads/dokumentasi/' . $filename),
            public_path('uploads/dokumentasi_kegiatan/' . $filename),
            storage_path('app/' . $filename),
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Cari folder berdasarkan nama dan parent ID di Google Drive
     */
    public function findFolder(string $name, ?string $parentId = null): ?string
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return null;
        }

        $cleanName = str_replace("'", "\\'", trim($name));
        $query = "mimeType = 'application/vnd.google-apps.folder' and trashed = false and name = '{$cleanName}'";
        if ($parentId) {
            $query .= " and '{$parentId}' in parents";
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->timeout(15)->get('https://www.googleapis.com/drive/v3/files', [
                'q'        => $query,
                'fields'   => 'files(id, name)',
                'pageSize' => 1,
            ]);

            if ($response->successful()) {
                $files = $response->json('files');
                if (!empty($files) && isset($files[0]['id'])) {
                    return $files[0]['id'];
                }
            }
        } catch (\Throwable $e) {
            Log::error("[GoogleDrive] Gagal mencari folder {$name}: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Buat folder baru di Google Drive
     */
    public function createFolder(string $name, ?string $parentId = null): ?string
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return null;
        }

        $body = [
            'name'     => trim($name),
            'mimeType' => 'application/vnd.google-apps.folder',
        ];

        if ($parentId) {
            $body['parents'] = [$parentId];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type'  => 'application/json',
            ])->timeout(20)->post('https://www.googleapis.com/drive/v3/files', $body);

            if ($response->successful()) {
                $folderId = $response->json('id');
                Log::info("[GoogleDrive] Folder berhasil dibuat: {$name} ({$folderId}) di parent: {$parentId}");
                return $folderId;
            }

            Log::error("[GoogleDrive] Gagal membuat folder {$name}: " . $response->body());
        } catch (\Throwable $e) {
            Log::error("[GoogleDrive] Exception membuat folder {$name}: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Cari atau buat folder secara otomatis (dengan in-memory cache)
     */
    public function getOrCreateFolder(string $name, ?string $parentId = null): ?string
    {
        $cacheKey = ($parentId ?: 'root') . ':' . trim($name);
        if (isset(self::$folderCache[$cacheKey])) {
            return self::$folderCache[$cacheKey];
        }

        $folderId = $this->findFolder($name, $parentId);
        if (!$folderId) {
            $folderId = $this->createFolder($name, $parentId);
        }

        if ($folderId) {
            self::$folderCache[$cacheKey] = $folderId;
        }

        return $folderId;
    }

    /**
     * Dapatkan atau buat rantai hierarki folder bertingkat di Google Drive
     * Contoh: ['CDK Wilayah Madiun', 'KTH YASA WANA LESTARI', 'FAPE 2026', 'Foto']
     */
    public function getOrCreateHierarchy(array $segments, ?string $rootParentId = null): ?string
    {
        $currentParentId = $rootParentId ?: $this->getFolderId();

        foreach ($segments as $segment) {
            $segment = trim((string) $segment);
            if ($segment === '') {
                continue;
            }

            $currentParentId = $this->getOrCreateFolder($segment, $currentParentId);
            if (!$currentParentId) {
                Log::error("[GoogleDrive] Gagal membuat hierarki pada level: {$segment}");
                return null;
            }
        }

        return $currentParentId;
    }

    /**
     * Cek apakah suatu file ID masih ada dan aktif (tidak trashed) di Google Drive
     */
    public function checkFileExists(?string $fileId): bool
    {
        if (empty($fileId)) {
            return false;
        }

        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->timeout(10)->get("https://www.googleapis.com/drive/v3/files/{$fileId}", [
                'fields' => 'id, trashed',
            ]);

            if ($response->successful()) {
                $trashed = $response->json('trashed') ?? false;
                return !$trashed;
            }
        } catch (\Throwable $e) {
            Log::warning("[GoogleDrive] Cek file {$fileId} error: " . $e->getMessage());
        }

        return false;
    }

    /**
     * Sinkronisasi sebuah record DokumentasiKegiatan ke struktur hierarki Google Drive:
     * CDK Wilayah -> Nama Kelompok -> Nama Kegiatan -> Foto
     *
     * @return array ['success' => bool, 'status' => 'skipped'|'uploaded'|'failed', 'file_id' => ?string, 'message' => string]
     */
    public function syncDokumentasiDetailed(DokumentasiKegiatan $doc, bool $forceVerify = false): array
    {
        $doc->loadMissing(['kegiatan.kelompok.cdk']);

        $kelompok = $doc->kegiatan ? $doc->kegiatan->kelompok : null;
        
        // 1. Tentukan CDK Wilayah
        $cdkName = 'CDK Wilayah';
        if ($kelompok && $kelompok->cdk && !empty($kelompok->cdk->nama)) {
            $cdkName = trim($kelompok->cdk->nama);
        }

        $kelompokName = $kelompok && !empty($kelompok->nama_kelompok)
            ? trim($kelompok->nama_kelompok)
            : 'Kelompok Lainnya';

        $kegiatanName = $doc->kegiatan && !empty($doc->kegiatan->nama_kegiatan)
            ? trim($doc->kegiatan->nama_kegiatan)
            : 'Kegiatan Lainnya';

        // 2. Cek jika foto sudah pernah disinkronkan sebelumnya
        if ($doc->google_drive_status === 'success' && !empty($doc->google_drive_file_id)) {
            if ($forceVerify) {
                $accessToken = $this->getAccessToken();
                if (!$accessToken) {
                    return [
                        'success' => false,
                        'status'  => 'failed',
                        'file_id' => $doc->google_drive_file_id,
                        'message' => 'Token akses Google Drive tidak aktif untuk verifikasi file.',
                    ];
                }

                $stillExists = $this->checkFileExists($doc->google_drive_file_id);
                if ($stillExists) {
                    return [
                        'success' => true,
                        'status'  => 'skipped',
                        'file_id' => $doc->google_drive_file_id,
                        'message' => "File sudah ada di Google Drive (Folder: CDK / {$cdkName} / {$kelompokName} / {$kegiatanName} / Foto)",
                    ];
                }
                // Jika sudah tidak ada di Drive (hilang/terhapus), lanjutkan untuk upload ulang
            } else {
                return [
                    'success' => true,
                    'status'  => 'skipped',
                    'file_id' => $doc->google_drive_file_id,
                    'message' => "Status sudah sinkron (ID: {$doc->google_drive_file_id})",
                ];
            }
        }

        // 3. Pastikan file fisik lokal tersedia di server
        $filePath = $this->findPhysicalPath($doc);
        if (!$filePath) {
            $errorMsg = 'File fisik tidak ditemukan pada server lokal.';
            $doc->update([
                'google_drive_status' => 'error',
                'google_drive_error'  => $errorMsg,
            ]);

            return [
                'success' => false,
                'status'  => 'failed',
                'file_id' => null,
                'message' => $errorMsg,
            ];
        }

        // Pastikan token akses tersedia sebelum membuat hierarki atau upload
        if (!$this->getAccessToken()) {
            $errorMsg = 'Token akses Google Drive tidak aktif. Pastikan konfigurasi OAuth Google Drive sudah diatur.';
            $doc->update([
                'google_drive_status' => 'error',
                'google_drive_error'  => $errorMsg,
            ]);

            return [
                'success' => false,
                'status'  => 'failed',
                'file_id' => null,
                'message' => $errorMsg,
            ];
        }

        // 4. Bangun struktur folder: CDK / [CDK Wilayah] / [Nama Kelompok] / [Nama Kegiatan] / Foto
        $targetFolderId = $this->getOrCreateHierarchy([
            'CDK',
            $cdkName,
            $kelompokName,
            $kegiatanName,
            'Foto',
        ]);

        if (!$targetFolderId) {
            $errorMsg = 'Gagal membuat atau mengakses struktur folder Google Drive.';
            $doc->update([
                'google_drive_status' => 'error',
                'google_drive_error'  => $errorMsg,
            ]);

            return [
                'success' => false,
                'status'  => 'failed',
                'file_id' => null,
                'message' => $errorMsg,
            ];
        }

        // 5. Unggah file ke folder target
        $fileName = $doc->nama_file ?: basename($filePath);
        $result = $this->uploadFile($filePath, $fileName, $doc->file_type ?: 'image/jpeg', $targetFolderId);

        if ($result['success']) {
            $doc->update([
                'google_drive_status'    => 'success',
                'google_drive_file_id'   => $result['file_id'],
                'google_drive_synced_at' => now(),
                'google_drive_error'     => null,
            ]);

            return [
                'success' => true,
                'status'  => 'uploaded',
                'file_id' => $result['file_id'],
                'message' => "Berhasil diunggah ke Google Drive (CDK / {$cdkName} / {$kelompokName} / {$kegiatanName} / Foto)",
            ];
        }

        $errorMsg = substr($result['error'] ?? 'Gagal upload', 0, 1000);
        $doc->update([
            'google_drive_status' => 'error',
            'google_drive_error'  => $errorMsg,
        ]);

        return [
            'success' => false,
            'status'  => 'failed',
            'file_id' => null,
            'message' => $errorMsg,
        ];
    }

    /**
     * Sinkronisasi sebuah record DokumentasiKegiatan ke Google Drive (Metode standar)
     */
    public function syncDokumentasi(DokumentasiKegiatan $doc): bool
    {
        $res = $this->syncDokumentasiDetailed($doc, false);
        return $res['success'];
    }
}
