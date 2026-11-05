<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class KelompokMapTest extends TestCase
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

    public function test_kelompok_dashboard_renders_with_map_and_layers()
    {
        $user = User::where('role', 'kelompok')->first();
        if (!$user) {
            $this->markTestSkipped('No user with role kelompok found.');
        }

        $response = $this->actingAs($user)->get('/kelompok/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Peta Wilayah Kelompok');
        $response->assertSee('mapKelompokDashboard');
        $response->assertSee('Peta Kegiatan');
        $response->assertSee('Petak Perhutani');
        $response->assertSee('btnGpsLocate');
    }

    public function test_kelompok_peta_renders_with_map_and_gps()
    {
        $user = User::where('role', 'kelompok')->first();
        if (!$user) {
            $this->markTestSkipped('No user with role kelompok found.');
        }

        $response = $this->actingAs($user)->get('/kelompok/peta');
        $response->assertStatus(200);
        $response->assertSee('mapKelompok');
        $response->assertSee('Peta Kegiatan');
        $response->assertSee('Petak Perhutani');
        $response->assertSee('btnGpsLocatePeta');
    }
}
