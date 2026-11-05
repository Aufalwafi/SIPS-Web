<?php

namespace Tests\Feature;

use App\Models\Kelompok;
use App\Models\User;
use Tests\TestCase;

class KelompokDetailTest extends TestCase
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
    
    public function test_detail_kelompok_page_renders_with_dokumen_and_petak()
    {
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $this->markTestSkipped('No user with role admin found.');
        }

        $kelompok = Kelompok::find(3);
        if (!$kelompok) {
            $this->markTestSkipped('Kelompok with ID 3 not found.');
        }

        $response = $this->actingAs($admin)->get('/admin/data-kelompok/3/detail');
        $response->assertStatus(200);

        // Pastikan route dokumen muncul
        $response->assertSee(route('dokumen.sk-ps', 3));
        $response->assertSee(route('dokumen.sk-kelompok', 3));

        // Pastikan tanggal tidak menampilkan -0001
        $response->assertDontSee('-0001');

        // Pastikan script petak perhutani dimuat
        $response->assertSee('layerPetak');
        $response->assertSee(route('kelompok.petak-perhutani', 3));
        $response->assertSee('Petak Perhutani');
    }

    public function test_download_sk_ps_endpoint_returns_pdf()
    {
        $kelompok = Kelompok::find(3);
        if (!$kelompok || !$kelompok->sk_ps) {
            $this->markTestSkipped('Kelompok 3 or SK PS not available.');
        }

        $response = $this->get('/dokumen/sk-ps/3');
        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_download_sk_kelompok_endpoint_returns_pdf()
    {
        $kelompok = Kelompok::find(3);
        if (!$kelompok || !$kelompok->sk_kelompok) {
            $this->markTestSkipped('Kelompok 3 or SK Kelompok not available.');
        }

        $response = $this->get('/dokumen/sk-kelompok/3');
        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_ajax_petak_perhutani_returns_json()
    {
        $response = $this->get('/kelompok/3/petak-perhutani');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'kode_toponimi',
                'petak',
                'anak_petak',
                'rph',
                'bkph',
                'kph',
                'geometry'
            ]
        ]);
    }
}
