<?php

namespace Tests\Feature;

use App\Models\DokumentasiKegiatan;
use App\Models\GoogleDriveOAuth;
use App\Models\User;
use App\Services\GoogleDriveService;
use Tests\TestCase;

class GoogleDriveTest extends TestCase
{
    public function test_google_drive_service_returns_configured_folder_url()
    {
        $service = app(GoogleDriveService::class);
        $folderUrl = $service->getFolderUrl();
        $this->assertStringContainsString('1C7CJuf111ut_L88ar45q1ut4LD8iwxKv', $folderUrl);
    }

    public function test_dokumentasi_kegiatan_has_google_drive_url_accessor()
    {
        $doc = new DokumentasiKegiatan([
            'google_drive_file_id' => '14-FrcL1rrbpWP5q9EVrkc8IzFqTQ2egT',
        ]);

        $this->assertEquals(
            'https://drive.google.com/file/d/14-FrcL1rrbpWP5q9EVrkc8IzFqTQ2egT/view',
            $doc->google_drive_url
        );

        $docNoDrive = new DokumentasiKegiatan([
            'google_drive_file_id' => null,
        ]);
        $this->assertNull($docNoDrive->google_drive_url);
    }

    public function test_google_drive_sync_route_requires_auth()
    {
        $response = $this->post(route('kelompok.kegiatan.dokumentasi.sync', 1));
        $response->assertRedirect('/login');
    }
}
