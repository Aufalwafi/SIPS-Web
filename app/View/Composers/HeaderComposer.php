<?php

namespace App\View\Composers;

use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * HeaderComposer — menyiapkan semua data untuk layouts.header.
 *
 * Menggantikan query langsung di header.blade.php:
 * - Notifikasi (rkpsMenunggu, rktMenunggu, totalNotif)
 * - Label wilayah & nama instansi
 */
class HeaderComposer
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function compose(View $view): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        // 1. Notifikasi — dari service (di-cache per request)
        $notif = $this->notificationService->get();
        $view->with($notif);

        // 2. Label instansi & wilayah
        $role = $user->role;
        $namaKelompok = 'Dinas Kehutanan Provinsi Jawa Timur';
        $wilayah = 'Pemerintah Provinsi Jawa Timur';

        if ($role === 'admin') {
            $wilayah = 'Pemerintah Provinsi Jawa Timur';

        } elseif ($role === 'admin_cdk') {
            $wilayah = $user->nama ?? 'Cabang Dinas Kehutanan';
            // Coba ambil nama CDK jika ada
            if ($user->id_cdk) {
                $cdk = DB::table('cdk')->where('id', $user->id_cdk)->value('nama');
                if ($cdk) {
                    $wilayah = $cdk;
                }
            }

        } elseif ($role === 'pendamping') {
            $wilayah = $this->resolveWilayahPendamping($user);

        } elseif ($role === 'kelompok') {
            $wilayah = $this->resolveWilayahKelompok($user);
        }

        $view->with('headerNamaInstansi', $namaKelompok);
        $view->with('headerWilayah', $wilayah);
    }

    /**
     * Tentukan label wilayah untuk pendamping.
     * Logika diambil dari header.blade.php lama (line 65-102).
     */
    private function resolveWilayahPendamping($user): string
    {
        $cdkName = null;

        // Cek dari kelompok yang sedang diakses (mode akses pendamping)
        if (request()->is('kelompok*') && session('akses_kelompok_id')) {
            $kCurrent = DB::table('kelompok')->where('id', session('akses_kelompok_id'))->first();
            if ($kCurrent && !empty($kCurrent->id_cdk)) {
                $c = DB::table('cdk')->where('id', $kCurrent->id_cdk)->first();
                if ($c && !empty($c->nama)) {
                    $cdkName = $c->nama;
                }
            }
            if (!$cdkName && $kCurrent && !empty($kCurrent->kabupaten)) {
                $cdkName = 'CDK Wilayah ' . $kCurrent->kabupaten;
            }
        }

        // Fallback: dari id_cdk user
        if (!$cdkName && !empty($user->id_cdk)) {
            $c = DB::table('cdk')->where('id', $user->id_cdk)->first();
            if ($c && !empty($c->nama)) {
                $cdkName = $c->nama;
            }
        }

        // Fallback: dari kelompok binaan
        if (!$cdkName) {
            $kc = DB::table('pendamping_kelompok')
                ->join('kelompok', 'pendamping_kelompok.id_kelompok', '=', 'kelompok.id')
                ->leftJoin('cdk', 'kelompok.id_cdk', '=', 'cdk.id')
                ->where('pendamping_kelompok.id_user', $user->id)
                ->select('cdk.nama as cdk_nama', 'kelompok.kabupaten')
                ->first();
            if ($kc && !empty($kc->cdk_nama)) {
                $cdkName = $kc->cdk_nama;
            } elseif ($kc && !empty($kc->kabupaten)) {
                $cdkName = 'CDK Wilayah ' . $kc->kabupaten;
            }
        }

        return $cdkName ?? 'CDK Wilayah Jawa Timur';
    }

    /**
     * Tentukan label wilayah untuk kelompok.
     * Logika diambil dari header.blade.php lama (line 103-119).
     */
    private function resolveWilayahKelompok($user): string
    {
        $kelompok = null;
        if (!empty($user->id_kelompok)) {
            $kelompok = DB::table('kelompok')->where('id', $user->id_kelompok)->first();
        }

        $cdkKelompok = null;
        if ($kelompok && !empty($kelompok->id_cdk)) {
            $c = DB::table('cdk')->where('id', $kelompok->id_cdk)->first();
            if ($c && !empty($c->nama)) {
                $cdkKelompok = $c->nama;
            }
        }
        if (!$cdkKelompok && $kelompok && !empty($kelompok->kabupaten)) {
            $cdkKelompok = 'CDK Wilayah ' . $kelompok->kabupaten;
        }

        return $cdkKelompok ?? 'CDK Wilayah Jawa Timur';
    }
}
