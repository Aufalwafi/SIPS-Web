<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $idCdk = $request->query('id_cdk');
        $scope = function ($query, $column = 'id_cdk') use ($idCdk) {
            if ($idCdk !== null && $idCdk !== '') {
                $query->where($column, (int) $idCdk);
            }
            return $query;
        };

        $totalCdk = DB::table('cdk')->count();
        $totalKelompok = $scope(DB::table('kelompok'))->count();
        $totalAdmin = DB::table('users')->where('role', 'admin')->count();
        $totalPendamping = $scope(DB::table('pendamping'))->count();
        $totalAnggota = $scope(DB::table('anggota_kelompok as a')->join('kelompok as k', 'a.id_kelompok', '=', 'k.id'), 'k.id_cdk')->count();
        $totalLuasLahan = $scope(DB::table('kelompok'))->sum('luas_lahan') ?? 0;
        $totalNteData = $scope(DB::table('nte_kelompok as n')->join('kelompok as k', 'n.id_kelompok', '=', 'k.id'), 'k.id_cdk')->count();
        $totalLaporan = $scope(DB::table('laporan_kegiatan as l')->join('kelompok as k', 'l.id_kelompok', '=', 'k.id'), 'k.id_cdk')->count();

        $pengajuanRkps = $scope(DB::table('pengajuan_rkps as p')->join('kelompok as k', 'p.id_kelompok', '=', 'k.id')->where('p.status', 'Menunggu'), 'k.id_cdk')->count();
        $pengajuanRkt = $scope(DB::table('pengajuan_rkt as p')->join('kelompok as k', 'p.id_kelompok', '=', 'k.id')->where('p.status', 'Menunggu'), 'k.id_cdk')->count();
        $rkpsDisetujui = $scope(DB::table('pengajuan_rkps as p')->join('kelompok as k', 'p.id_kelompok', '=', 'k.id')->where('p.status', 'Disetujui'), 'k.id_cdk')->count();
        $rkpsDitolak = $scope(DB::table('pengajuan_rkps as p')->join('kelompok as k', 'p.id_kelompok', '=', 'k.id')->where('p.status', 'Ditolak'), 'k.id_cdk')->count();
        $rktDisetujui = $scope(DB::table('pengajuan_rkt as p')->join('kelompok as k', 'p.id_kelompok', '=', 'k.id')->where('p.status', 'Disetujui'), 'k.id_cdk')->count();
        $rktDitolak = $scope(DB::table('pengajuan_rkt as p')->join('kelompok as k', 'p.id_kelompok', '=', 'k.id')->where('p.status', 'Ditolak'), 'k.id_cdk')->count();

        $totalNte = $scope(DB::table('nte_transaksi as t')->join('nte_kelompok as n', 't.id_nte', '=', 'n.id')->join('kelompok as k', 'n.id_kelompok', '=', 'k.id'), 'k.id_cdk')->sum('t.total_nekon') ?? 0;
        $totalVolume = $scope(DB::table('nte_transaksi as t')->join('nte_kelompok as n', 't.id_nte', '=', 'n.id')->join('kelompok as k', 'n.id_kelompok', '=', 'k.id'), 'k.id_cdk')->sum('t.volume') ?? 0;
        $komoditasNte = $scope(DB::table('nte_kelompok as n')->join('kelompok as k', 'n.id_kelompok', '=', 'k.id'), 'k.id_cdk')->distinct()->count('n.nama_produk');

        // Sebaran Skema PS
        $sebaranSkema = $scope(DB::table('kelompok'))
            ->select('skema_ps', DB::raw('COUNT(*) as total'), DB::raw('COALESCE(SUM(luas_lahan), 0) as luas'))
            ->whereNotNull('skema_ps')
            ->where('skema_ps', '!=', '')
            ->groupBy('skema_ps')
            ->orderByDesc('total')
            ->get();

        $daftarCdk = DB::table('cdk')->orderBy('nama')->get(['id', 'nama', 'kode']);
        $rekapCdk = DB::table('cdk as c')
            ->leftJoin('kelompok as k', function ($join) use ($idCdk) {
                $join->on('k.id_cdk', '=', 'c.id');
                if ($idCdk !== null && $idCdk !== '') $join->where('c.id', (int) $idCdk);
            })
            ->leftJoinSub(DB::table('pendamping')->select('id_cdk', DB::raw('COUNT(*) as total'))->groupBy('id_cdk'), 'p', 'p.id_cdk', '=', 'c.id')
            ->leftJoinSub(DB::table('anggota_kelompok as a')->join('kelompok as ka', 'a.id_kelompok', '=', 'ka.id')->select('ka.id_cdk', DB::raw('COUNT(*) as total'))->groupBy('ka.id_cdk'), 'a', 'a.id_cdk', '=', 'c.id')
            ->leftJoinSub(DB::table('nte_kelompok as n')->join('kelompok as kn', 'n.id_kelompok', '=', 'kn.id')->select('kn.id_cdk', DB::raw('COUNT(*) as total'))->groupBy('kn.id_cdk'), 'n', 'n.id_cdk', '=', 'c.id')
            ->leftJoinSub(DB::table('laporan_kegiatan as l')->join('kelompok as kl', 'l.id_kelompok', '=', 'kl.id')->select('kl.id_cdk', DB::raw('COUNT(*) as total'))->groupBy('kl.id_cdk'), 'l', 'l.id_cdk', '=', 'c.id')
            ->select('c.id', 'c.nama', 'c.kode', DB::raw('COUNT(k.id) as jumlah_kelompok'), DB::raw('COALESCE(p.total,0) as jumlah_pendamping'), DB::raw('COALESCE(a.total,0) as jumlah_anggota'), DB::raw('COALESCE(SUM(k.luas_lahan),0) as luas_lahan'), DB::raw('COALESCE(n.total,0) as jumlah_nte'), DB::raw('COALESCE(l.total,0) as jumlah_laporan'))
            ->groupBy('c.id', 'c.nama', 'c.kode', 'p.total', 'a.total', 'n.total', 'l.total')->orderBy('c.nama')->get();

        $userTerakhir = DB::table('users')->leftJoin('kelompok', 'users.id_kelompok', '=', 'kelompok.id')->orderByDesc('users.id')->limit(5)->get(['users.username','users.nama','users.role','kelompok.nama_kelompok','users.created_at']);
        return view('admin.dashboard', compact('totalCdk','totalKelompok','totalAdmin','totalPendamping','totalAnggota','totalLuasLahan','totalNteData','totalLaporan','pengajuanRkps','pengajuanRkt','rkpsDisetujui','rkpsDitolak','rktDisetujui','rktDitolak','totalNte','totalVolume','komoditasNte','sebaranSkema','daftarCdk','rekapCdk','idCdk','userTerakhir'));
    }
}
