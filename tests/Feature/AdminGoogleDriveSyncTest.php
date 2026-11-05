<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\GoogleDriveService;
use Tests\TestCase;

class AdminGoogleDriveSyncTest extends TestCase
{
    public function test_guest_is_redirected_from_google_drive_sync_page()
    {
        $response = $this->get(route('admin.google-drive-sync'));
        $response->assertRedirect('/login');
    }

    public function test_non_admin_cannot_access_google_drive_sync_page()
    {
        $kelompokUser = new User([
            'id'       => 999,
            'nama'     => 'Kelompok User',
            'username' => 'user_kelompok',
            'role'     => 'kelompok',
        ]);

        $response = $this->actingAs($kelompokUser)->get(route('admin.google-drive-sync'));
        $response->assertStatus(403);
    }

    public function test_google_drive_service_has_cdk_hierarchy_methods()
    {
        $service = app(GoogleDriveService::class);
        $this->assertTrue(method_exists($service, 'findFolder'));
        $this->assertTrue(method_exists($service, 'createFolder'));
        $this->assertTrue(method_exists($service, 'getOrCreateFolder'));
        $this->assertTrue(method_exists($service, 'getOrCreateHierarchy'));
        $this->assertTrue(method_exists($service, 'checkFileExists'));
        $this->assertTrue(method_exists($service, 'syncDokumentasiDetailed'));
    }

    public function test_admin_can_access_google_drive_sync_page()
    {
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'sips',
        ]);

        try {
            \Illuminate\Support\Facades\DB::connection('mysql')->getPdo();
        } catch (\Throwable $e) {
            $this->markTestSkipped('MySQL database is not running or accessible.');
        }

        $adminUser = User::where('role', 'admin')->first();
        if (!$adminUser) {
            $adminUser = new User([
                'id'       => 1,
                'nama'     => 'Administrator',
                'username' => 'admin_test',
                'role'     => 'admin',
            ]);
        }

        $response = $this->actingAs($adminUser)->get(route('admin.google-drive-sync'));
        $response->assertStatus(200);
        $response->assertSee('Google Drive Sync');
        $response->assertSee('CDK Wilayah');
    }

    public function test_admin_can_fetch_items_endpoint()
    {
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'sips',
        ]);

        try {
            \Illuminate\Support\Facades\DB::connection('mysql')->getPdo();
        } catch (\Throwable $e) {
            $this->markTestSkipped('MySQL database is not running or accessible.');
        }

        $adminUser = User::where('role', 'admin')->first();
        if (!$adminUser) {
            $adminUser = new User([
                'id'       => 1,
                'nama'     => 'Administrator',
                'username' => 'admin_test',
                'role'     => 'admin',
            ]);
        }

        $response = $this->actingAs($adminUser)->getJson(route('admin.google-drive-sync.items'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'total',
            'items',
        ]);
    }

    public function test_admin_process_item_validation()
    {
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'sips',
        ]);

        try {
            \Illuminate\Support\Facades\DB::connection('mysql')->getPdo();
        } catch (\Throwable $e) {
            $this->markTestSkipped('MySQL database is not running or accessible.');
        }

        $adminUser = User::where('role', 'admin')->first();
        if (!$adminUser) {
            $adminUser = new User([
                'id'       => 1,
                'nama'     => 'Administrator',
                'username' => 'admin_test',
                'role'     => 'admin',
            ]);
        }

        // Without ID
        $response = $this->actingAs($adminUser)->postJson(route('admin.google-drive-sync.process-item'), []);
        $response->assertStatus(400);

        // With non-existent ID
        $response = $this->actingAs($adminUser)->postJson(route('admin.google-drive-sync.process-item'), ['id' => 9999999]);
        $response->assertStatus(404);
    }

    public function test_admin_can_access_google_drive_sync_with_filter()
    {
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'sips',
        ]);

        try {
            \Illuminate\Support\Facades\DB::connection('mysql')->getPdo();
        } catch (\Throwable $e) {
            $this->markTestSkipped('MySQL database is not running or accessible.');
        }

        $adminUser = User::where('role', 'admin')->first();
        if (!$adminUser) {
            $adminUser = new User([
                'id'       => 1,
                'nama'     => 'Administrator',
                'username' => 'admin_test',
                'role'     => 'admin',
            ]);
        }

        $response = $this->actingAs($adminUser)->get(route('admin.google-drive-sync', ['id_kegiatan' => 'all']));
        $response->assertStatus(200);
        $response->assertSee('Sync Google Drive');
        $response->assertSee('Mulai Sinkronisasi');
        $response->assertSee('Folder Google Drive');
    }

    public function test_filter_by_search_cdk_and_status()
    {
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'sips',
        ]);

        try {
            \Illuminate\Support\Facades\DB::connection('mysql')->getPdo();
        } catch (\Throwable $e) {
            $this->markTestSkipped('MySQL database is not running or accessible.');
        }

        $adminUser = User::where('role', 'admin')->first();
        if (!$adminUser) {
            $adminUser = new User([
                'id'       => 1,
                'nama'     => 'Administrator',
                'username' => 'admin_test',
                'role'     => 'admin',
            ]);
        }

        // Search CDK Madiun
        $response = $this->actingAs($adminUser)->get(route('admin.google-drive-sync', ['q' => 'Madiun']));
        $response->assertStatus(200);
        $response->assertViewHas('dokumentasiList', function ($list) {
            return count($list) > 0;
        });

        // Filter items endpoint with status failed
        $resItemsFailed = $this->actingAs($adminUser)->getJson(route('admin.google-drive-sync.items', ['status' => 'failed']));
        $resItemsFailed->assertStatus(200);
        $failedData = $resItemsFailed->json();
        $this->assertTrue($failedData['success']);
        foreach ($failedData['items'] as $item) {
            $this->assertEquals('error', $item['google_drive_status']);
        }

        // Filter items endpoint with status synced
        $resItemsSynced = $this->actingAs($adminUser)->getJson(route('admin.google-drive-sync.items', ['status' => 'synced']));
        $resItemsSynced->assertStatus(200);
        $syncedData = $resItemsSynced->json();
        $this->assertTrue($syncedData['success']);
        foreach ($syncedData['items'] as $item) {
            $this->assertEquals('success', $item['google_drive_status']);
        }
    }
}

