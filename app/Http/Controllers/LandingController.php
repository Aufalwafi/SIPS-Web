<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LandingController extends Controller
{
    public function index()
    {
        if (! Schema::hasTable('kelompok') || ! Schema::hasTable('users') || ! Schema::hasTable('nte_transaksi')) {
            return view('landing', [
                'kelompok' => 0,
                'luasLahan' => 0,
                'pendamping' => 0,
                'anggota' => 0,
                'cdkCount' => 0,
                'nte' => 0,
            ]);
        }

        $kelompok = DB::table('kelompok')->count();
        $luasLahan = DB::table('kelompok')->sum('luas_lahan') ?? 0;
        $pendamping = DB::table('pendamping')->count();
        $anggota = DB::table('anggota_kelompok')->count();
        $cdkCount = DB::table('cdk')->count();
        $nte = DB::table('nte_transaksi')->sum('total_nekon') ?? 0;

        return view('landing', compact('kelompok', 'luasLahan', 'pendamping', 'anggota', 'cdkCount', 'nte'));
    }
}