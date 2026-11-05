<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AuthAndRoleAccessTest extends TestCase
{
    /**
     * Halaman publik dapat diakses oleh siapa saja.
     */
    public function test_public_pages_are_accessible(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    /**
     * Halaman berproteksi mengarahkan guest ke /login.
     */
    public function test_guest_is_redirected_to_login_for_protected_routes(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/login');
        $this->get('/admin-cdk/dashboard')->assertRedirect('/login');
        $this->get('/pendamping/dashboard')->assertRedirect('/login');
        $this->get('/kelompok/dashboard')->assertRedirect('/login');
    }

    /**
     * Role kelompok tidak boleh mengakses rute admin.
     */
    public function test_kelompok_role_cannot_access_admin_portal(): void
    {
        $kelompokUser = new User([
            'id'       => 1,
            'nama'     => 'Kelompok User',
            'username' => 'user_kelompok',
            'role'     => 'kelompok',
        ]);

        $response = $this->actingAs($kelompokUser)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    /**
     * Role pendamping tidak boleh mengakses rute admin.
     */
    public function test_pendamping_role_cannot_access_admin_portal(): void
    {
        $pendampingUser = new User([
            'id'       => 2,
            'nama'     => 'Pendamping User',
            'username' => 'user_pendamping',
            'role'     => 'pendamping',
        ]);

        $response = $this->actingAs($pendampingUser)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    /**
     * Role admin_cdk tidak boleh mengakses rute superadmin.
     */
    public function test_admin_cdk_role_cannot_access_admin_portal(): void
    {
        $cdkUser = new User([
            'id'       => 3,
            'nama'     => 'Admin CDK User',
            'username' => 'user_cdk',
            'role'     => 'admin_cdk',
        ]);

        $response = $this->actingAs($cdkUser)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    /**
     * Role pendamping dapat mengakses halaman anggota dan melihat tombol Tambah Anggota.
     */
    public function test_pendamping_can_access_anggota_page(): void
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

        $pendampingUser = User::where('role', 'pendamping')->first();
        if (!$pendampingUser) {
            $this->markTestSkipped('Tidak ada user pendamping di database.');
        }

        $response = $this->actingAs($pendampingUser)->get('/pendamping/anggota');
        $response->assertStatus(200);
        $response->assertSee('Tambah Anggota');
    }

    /**
     * Role pendamping dapat mengakses halaman data-nte dan melihat tombol Input Komoditas NTE.
     */
    public function test_pendamping_can_access_data_nte_page(): void
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

        $pendampingUser = User::where('role', 'pendamping')->first();
        if (!$pendampingUser) {
            $this->markTestSkipped('Tidak ada user pendamping di database.');
        }

        $response = $this->actingAs($pendampingUser)->get('/pendamping/data-nte');
        $response->assertStatus(200);
        $response->assertSee('Input Komoditas NTE');
    }
}
