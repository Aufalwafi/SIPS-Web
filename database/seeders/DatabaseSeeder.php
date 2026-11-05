<?php

namespace Database\Seeders;

use App\Models\Cdk;
use App\Models\Kelompok;
use App\Models\Pendamping;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed sample CDK if table exists and is empty
        $defaultCdkId = null;
        if (Schema::hasTable('cdk')) {
            $cdk = Cdk::firstOrCreate(
                ['kode' => 'CDK-MDN'],
                ['nama' => 'CDK Wilayah Madiun']
            );
            $defaultCdkId = $cdk->id;

            Cdk::firstOrCreate(
                ['kode' => 'CDK-BJN'],
                ['nama' => 'CDK Wilayah Bojonegoro']
            );
        }

        // 2. Seed sample Kelompok if table exists and is empty
        $defaultKelompokId = null;
        if (Schema::hasTable('kelompok')) {
            $kelompok = Kelompok::firstOrCreate(
                ['nama_kelompok' => 'KTH Rimba Jaya'],
                [
                    'ketua'        => 'Budi Santoso',
                    'desa'         => 'Hargomulyo',
                    'kecamatan'    => 'Ngrambe',
                    'kabupaten'    => 'Ngawi',
                    'skema_ps'     => 'HKm',
                    'luas_lahan'   => 25.5,
                    'id_cdk'       => $defaultCdkId,
                ]
            );
            $defaultKelompokId = $kelompok->id;
        }

        // 3. Seed Default Users
        if (Schema::hasTable('users')) {
            // Super Admin
            User::firstOrCreate(
                ['username' => 'admin'],
                [
                    'nama'        => 'Administrator Utama',
                    'password'    => Hash::make('password'),
                    'role'        => 'admin',
                    'id_cdk'      => null,
                    'id_kelompok' => null,
                ]
            );

            // Admin CDK
            User::firstOrCreate(
                ['username' => 'admin_cdk'],
                [
                    'nama'        => 'Admin CDK Madiun',
                    'password'    => Hash::make('password'),
                    'role'        => 'admin_cdk',
                    'id_cdk'      => $defaultCdkId,
                    'id_kelompok' => null,
                ]
            );

            // Pendamping
            $pendampingUser = User::firstOrCreate(
                ['username' => 'pendamping'],
                [
                    'nama'        => 'Pendamping Lapangan 1',
                    'password'    => Hash::make('password'),
                    'role'        => 'pendamping',
                    'id_cdk'      => $defaultCdkId,
                    'id_kelompok' => null,
                ]
            );

            // Sync pendamping profile & binaan
            if (Schema::hasTable('pendamping')) {
                Pendamping::firstOrCreate(
                    ['username' => 'pendamping'],
                    [
                        'nama_pendamping' => 'Pendamping Lapangan 1',
                        'nip'             => '198501012010011001',
                        'id_cdk'          => $defaultCdkId,
                        'status'          => 'Aktif',
                    ]
                );
            }

            if (Schema::hasTable('pendamping_kelompok') && $defaultKelompokId) {
                DB::table('pendamping_kelompok')->updateOrInsert(
                    ['id_user' => $pendampingUser->id, 'id_kelompok' => $defaultKelompokId],
                    ['id_user' => $pendampingUser->id, 'id_kelompok' => $defaultKelompokId]
                );
            }

            // Kelompok
            User::firstOrCreate(
                ['username' => 'kelompok'],
                [
                    'nama'        => 'Akun KTH Rimba Jaya',
                    'password'    => Hash::make('password'),
                    'role'        => 'kelompok',
                    'id_cdk'      => null,
                    'id_kelompok' => $defaultKelompokId,
                ]
            );
        }
    }
}
