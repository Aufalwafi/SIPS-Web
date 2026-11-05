<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonitoringController extends Controller
{
    public function verifikasiRkps()
    {
        $pengajuanRkps = DB::table('pengajuan_rkps')
            ->leftJoin('kelompok', 'pengajuan_rkps.id_kelompok', '=', 'kelompok.id')
            ->orderByDesc('pengajuan_rkps.tanggal_pengajuan')
            ->get(['pengajuan_rkps.*', 'kelompok.nama_kelompok']);

        return view('admin.verifikasi-rkps', compact('pengajuanRkps'));
    }

    public function prosesVerifikasiRkps(Request $request)
    {
        $id = $request->input('id');
        $aksi = $request->input('aksi');
        $catatan = $request->input('catatan', '');

        $statuses = ['Setujui' => 'Disetujui', 'Revisi' => 'Revisi', 'Tolak' => 'Ditolak'];
        $status = $statuses[$aksi] ?? null;

        if (!$status || !$id) {
            return redirect()->back()->with('error', 'Data tidak valid.');
        }

        DB::table('pengajuan_rkps')->where('id', $id)->update([
            'status' => $status,
            'catatan_admin' => $catatan,
        ]);

        return redirect()->route('admin.verifikasi-rkps')
            ->with('success', "Pengajuan RKPS berhasil diubah menjadi {$status}.");
    }

    public function verifikasiRkt()
    {
        $pengajuanRkt = DB::table('pengajuan_rkt')
            ->leftJoin('kelompok', 'pengajuan_rkt.id_kelompok', '=', 'kelompok.id')
            ->orderByDesc('pengajuan_rkt.tanggal_pengajuan')
            ->get(['pengajuan_rkt.*', 'kelompok.nama_kelompok']);

        return view('admin.verifikasi-rkt', compact('pengajuanRkt'));
    }

    public function prosesVerifikasiRkt(Request $request)
    {
        $id = $request->input('id');
        $aksi = $request->input('aksi');
        $catatan = $request->input('catatan', '');

        $statuses = ['Setujui' => 'Disetujui', 'Revisi' => 'Revisi', 'Tolak' => 'Ditolak'];
        $status = $statuses[$aksi] ?? null;

        if (!$status || !$id) {
            return redirect()->back()->with('error', 'Data tidak valid.');
        }

        DB::table('pengajuan_rkt')->where('id', $id)->update([
            'status' => $status,
            'catatan_admin' => $catatan,
        ]);

        return redirect()->route('admin.verifikasi-rkt')
            ->with('success', "Pengajuan RKT berhasil diubah menjadi {$status}.");
    }

    public function monitoringPengajuan()
    {
        $kelompok = DB::table('kelompok')->orderBy('nama_kelompok')->get();
        $listTahun = DB::table('pengajuan_rkt')
            ->select('tahun')
            ->distinct()
            ->orderBy('tahun')
            ->pluck('tahun');

        // Batch: status RKPS per kelompok (1 query, bukan N)
        $rkpsRaw = DB::table('pengajuan_rkps')
            ->select('id_kelompok', DB::raw("CASE WHEN SUM(status='Menunggu')>0 THEN 'Menunggu' WHEN SUM(status='Revisi')>0 THEN 'Revisi' WHEN SUM(status='Ditolak')>0 THEN 'Ditolak' WHEN SUM(status='Disetujui')>0 THEN 'Disetujui' ELSE '-' END AS status_rkps"))
            ->groupBy('id_kelompok')
            ->pluck('status_rkps', 'id_kelompok');
        $rkpsStatus = [];
        foreach ($kelompok as $k) {
            $rkpsStatus[$k->id] = $rkpsRaw[$k->id] ?? '-';
        }

        // Batch: status RKT per kelompok per tahun (1 query, bukan N×M)
        $rktRaw = DB::table('pengajuan_rkt')
            ->select('id_kelompok', 'tahun', 'status')
            ->orderByDesc('tanggal_pengajuan')
            ->get()
            ->groupBy('id_kelompok');
        $rktStatus = [];
        foreach ($kelompok as $k) {
            foreach ($listTahun as $tahun) {
                $items = $rktRaw->get($k->id, collect());
                $match = $items->firstWhere('tahun', $tahun);
                $rktStatus[$k->id][$tahun] = $match ? $match->status : '-';
            }
        }

        return view('admin.monitoring-pengajuan', compact('kelompok', 'listTahun', 'rkpsStatus', 'rktStatus'));
    }

    public function monitoringPs()
    {
        $tahunAwal = 2024;
        $tahunAkhir = date('Y') + 10;
        $kelompok = DB::table('kelompok')->orderBy('nama_kelompok')->get();

        // Batch: RKPS count per kelompok (1 query)
        $rkpsRaw = DB::table('rkps')
            ->select('id_kelompok', DB::raw('COUNT(*) as total'))
            ->groupBy('id_kelompok')
            ->pluck('total', 'id_kelompok');
        $rkpsCount = [];
        foreach ($kelompok as $k) {
            $rkpsCount[$k->id] = $rkpsRaw[$k->id] ?? 0;
        }

        // Batch: RKT count per kelompok per tahun (1 query)
        $rktRaw = DB::table('rkt')
            ->whereBetween('tahun', [$tahunAwal, $tahunAkhir])
            ->select('id_kelompok', 'tahun', DB::raw('COUNT(*) as total'))
            ->groupBy('id_kelompok', 'tahun')
            ->get()
            ->groupBy('id_kelompok');
        $rktCount = [];
        foreach ($kelompok as $k) {
            $items = $rktRaw->get($k->id, collect());
            for ($t = $tahunAwal; $t <= $tahunAkhir; $t++) {
                $match = $items->firstWhere('tahun', $t);
                $rktCount[$k->id][$t] = $match ? $match->total : 0;
            }
        }

        return view('admin.monitoring-ps', compact('kelompok', 'tahunAwal', 'tahunAkhir', 'rkpsCount', 'rktCount'));
    }

    public function monitoringNte(Request $request)
    {
        $tahun = $request->query('tahun', '');
        $bulan = $request->query('bulan', '');
        $komoditas = $request->query('komoditas', '');
        $pendamping = $request->query('pendamping', '');
        $kelompokFilter = $request->query('kelompok', '');

        $query = DB::table('nte_transaksi as t')
            ->join('nte_kelompok as n', 't.id_nte', '=', 'n.id')
            ->join('kelompok as k', 'n.id_kelompok', '=', 'k.id')
            ->leftJoin('users as u', 'n.id_pendamping', '=', 'u.id')
            ->select(
                'k.nama_kelompok', 'n.nama_produk', 'n.satuan', 'n.tahun', 't.bulan',
                'u.nama as nama_pendamping',
                DB::raw('SUM(t.volume) as total_volume'),
                DB::raw('SUM(t.total_nekon) as total_nekon')
            )
            ->when($tahun !== '', fn ($q) => $q->where('n.tahun', $tahun))
            ->when($bulan !== '', fn ($q) => $q->where('t.bulan', $bulan))
            ->when($komoditas !== '', fn ($q) => $q->where('n.nama_produk', $komoditas))
            ->when($pendamping !== '', fn ($q) => $q->where('n.id_pendamping', $pendamping))
            ->when($kelompokFilter !== '', fn ($q) => $q->where('n.id_kelompok', $kelompokFilter))
            ->groupBy('n.id', 't.bulan', 'k.nama_kelompok', 'n.nama_produk', 'n.satuan', 'n.tahun', 'u.nama')
            ->havingRaw('SUM(t.volume) > 0 OR SUM(t.total_nekon) > 0')
            ->orderByDesc('n.tahun')
            ->orderByDesc('t.bulan');

        $data = $query->get();
        $totalVolume = $data->sum('total_volume');
        $totalNekon = $data->sum('total_nekon');
        $tahunData = DB::table('nte_kelompok')->select('tahun')->distinct()->orderByDesc('tahun')->pluck('tahun');
        $pendampingData = DB::table('users')->where('role', 'pendamping')->orderBy('nama')->get(['id', 'nama']);
        $kelompokData = DB::table('kelompok')->orderBy('nama_kelompok')->get(['id', 'nama_kelompok']);
        $komoditasData = DB::table('nte_kelompok')->select('nama_produk')->distinct()->orderBy('nama_produk')->pluck('nama_produk');

        if ($request->query('export') === 'excel') {
            return response()->streamDownload(function () use ($data) {
                echo "<table border='1'><tr><th>No</th><th>Tahun</th><th>Bulan</th><th>Kelompok</th><th>Komoditas</th><th>Satuan</th><th>Total Volume</th><th>Total NTE</th><th>Pendamping</th></tr>";
                foreach ($data as $i => $d) {
                    echo '<tr><td>' . ($i + 1) . '</td><td>' . e($d->tahun) . '</td><td>' . e($d->bulan) . '</td><td>' . e($d->nama_kelompok) . '</td><td>' . e($d->nama_produk) . '</td><td>' . e($d->satuan) . '</td><td>' . e($d->total_volume) . '</td><td>' . e($d->total_nekon) . '</td><td>' . e($d->nama_pendamping ?? '-') . '</td></tr>';
                }
                echo '</table>';
            }, 'monitoring_nte.xls', ['Content-Type' => 'application/vnd.ms-excel']);
        }

        return view('admin.monitoring-nte', compact(
            'data', 'totalVolume', 'totalNekon', 'tahun', 'bulan', 'komoditas',
            'pendamping', 'kelompokFilter', 'tahunData', 'pendampingData',
            'kelompokData', 'komoditasData'
        ));
    }

    public function dataLaporan()
    {
        $data = DB::table('laporan_kegiatan as lk')
            ->leftJoin('users as u', 'lk.id_user', '=', 'u.id')
            ->leftJoin('kelompok as k', 'lk.id_kelompok', '=', 'k.id')
            ->orderByDesc('lk.tanggal')
            ->get(['lk.id', 'lk.tanggal', 'lk.kegiatan', 'lk.deskripsi', 'lk.foto', 'k.nama_kelompok', 'u.nama as nama_user']);

        $listTahun = DB::table('laporan_kegiatan')
            ->select(DB::raw('YEAR(tanggal) as tahun'))
            ->distinct()->orderByDesc('tahun')->pluck('tahun');

        $listUser = DB::table('users')
            ->where('role', 'pendamping')
            ->whereNotNull('nama')->where('nama', '!=', '')
            ->orderBy('nama')->get(['id', 'nama']);

        $listKelompok = DB::table('kelompok')->orderBy('nama_kelompok')->get(['id', 'nama_kelompok']);

        return view('admin.data-laporan', compact('data', 'listTahun', 'listUser', 'listKelompok'));
    }
}
