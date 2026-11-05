<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * NotificationService — satu tempat untuk menghitung notifikasi seluruh role.
 *
 * Menggantikan:
 * - AdminController::notifikasi()
 * - AdminCdkController::notifikasi()
 * - Query langsung di header.blade.php (line 1-47)
 * - Perhitungan di PendampingController::dashboard()
 */
class NotificationService
{
    private ?array $cache = null;

    /**
     * Hitung notifikasi berdasarkan role user yang sedang login.
     * Hasil di-cache per request agar tidak query berulang.
     */
    public function get(): array
    {
        if ($this->cache !== null) {
            return $this->cache;
        }

        $user = Auth::user();

        if (!$user) {
            return $this->cache = [
                'rkpsMenunggu' => 0,
                'rktMenunggu' => 0,
                'laporanMenunggu' => 0,
                'totalNotif' => 0,
            ];
        }

        $role = $user->role;

        $rkpsMenunggu = 0;
        $rktMenunggu = 0;
        $laporanMenunggu = 0;

        if ($role === 'admin') {
            $rkpsMenunggu = DB::table('pengajuan_rkps')->where('status', 'Menunggu')->count();
            $rktMenunggu = DB::table('pengajuan_rkt')->where('status', 'Menunggu')->count();
            $laporanMenunggu = DB::table('laporan_kegiatan')->count();

        } elseif ($role === 'admin_cdk') {
            $idCdk = $user->id_cdk ?? 0;
            if ($idCdk) {
                $rkpsMenunggu = DB::table('pengajuan_rkps as p')
                    ->join('kelompok as k', 'p.id_kelompok', '=', 'k.id')
                    ->where('k.id_cdk', $idCdk)
                    ->where('p.status', 'Menunggu')->count();
                $rktMenunggu = DB::table('pengajuan_rkt as p')
                    ->join('kelompok as k', 'p.id_kelompok', '=', 'k.id')
                    ->where('k.id_cdk', $idCdk)
                    ->where('p.status', 'Menunggu')->count();
                $laporanMenunggu = DB::table('laporan_kegiatan as l')
                    ->join('kelompok as k', 'l.id_kelompok', '=', 'k.id')
                    ->where('k.id_cdk', $idCdk)->count();
            }

        } elseif ($role === 'pendamping') {
            $ids = DB::table('pendamping_kelompok')->where('id_user', $user->id)->pluck('id_kelompok');
            if ($ids->count()) {
                $rkpsMenunggu = DB::table('pengajuan_rkps')->whereIn('id_kelompok', $ids)->where('status', 'Menunggu')->count();
                $rktMenunggu = DB::table('pengajuan_rkt')->whereIn('id_kelompok', $ids)->where('status', 'Menunggu')->count();
            }

        } elseif ($role === 'kelompok') {
            $idKelompok = $user->id_kelompok ?? 0;
            if ($idKelompok) {
                $rkpsMenunggu = DB::table('pengajuan_rkps')->where('id_kelompok', $idKelompok)->where('status', 'Menunggu')->count();
                $rktMenunggu = DB::table('pengajuan_rkt')->where('id_kelompok', $idKelompok)->where('status', 'Menunggu')->count();
            }
        }

        return $this->cache = [
            'rkpsMenunggu' => $rkpsMenunggu,
            'rktMenunggu' => $rktMenunggu,
            'laporanMenunggu' => $laporanMenunggu,
            'totalNotif' => $rkpsMenunggu + $rktMenunggu,
        ];
    }

    /**
     * Reset cache (berguna untuk testing).
     */
    public function flush(): void
    {
        $this->cache = null;
    }
}
