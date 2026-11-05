<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Kelompok;
use Tests\TestCase;

class RecycleBinTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'sips',
        ]);

        try {
            \Illuminate\Support\Facades\DB::connection('mysql')->getPdo();
        } catch (\Throwable $e) {
            $this->markTestSkipped('MySQL database is not running or accessible.');
        }
    }
    public function test_guest_cannot_access_recycle_bin(): void
    {
        $this->get('/admin/recycle-bin')->assertRedirect('/login');
    }

    public function test_non_admin_cannot_access_recycle_bin(): void
    {
        $kelompokUser = new User([
            'id'       => 10,
            'nama'     => 'Kelompok User',
            'username' => 'kelompok_test',
            'role'     => 'kelompok',
        ]);

        $this->actingAs($kelompokUser)->get('/admin/recycle-bin')->assertStatus(403);
    }

    public function test_admin_can_access_recycle_bin(): void
    {
        $adminUser = User::where('role', 'admin')->first();
        if (!$adminUser) {
            $adminUser = new User([
                'id'       => 1,
                'nama'     => 'Administrator',
                'username' => 'admin_test',
                'role'     => 'admin',
            ]);
        }

        $response = $this->actingAs($adminUser)->get('/admin/recycle-bin');
        $response->assertStatus(200);
        $response->assertSee('Recycle Bin');
        $response->assertSee('Data Terhapus');
    }
}
