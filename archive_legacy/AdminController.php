<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminController extends Controller
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

    public function dataKelompok()
    {
        $kelompok = DB::table('kelompok')
            ->leftJoin('cdk', 'kelompok.id_cdk', '=', 'cdk.id')
            ->select('kelompok.*', 'cdk.nama as nama_cdk')
            ->orderBy('kelompok.nama_kelompok')
            ->get();

        return view('admin.data-kelompok', compact('kelompok'));
    }

    public function detailKelompok($id)
    {
        $kelompok = DB::table('kelompok')
            ->leftJoin('cdk', 'kelompok.id_cdk', '=', 'cdk.id')
            ->select('kelompok.*', 'cdk.nama as nama_cdk')
            ->where('kelompok.id', $id)
            ->first();

        abort_unless($kelompok, 404, 'Data Kelompok tidak ditemukan');

        $rkpsTerakhir = DB::table('rkps')
            ->where('id_kelompok', $id)
            ->orderByDesc('tahun_mulai')
            ->first();

        $rktTerakhir = DB::table('rkt')
            ->where('id_kelompok', $id)
            ->orderByDesc('tahun')
            ->first();

        $totalAnggota = DB::table('anggota_kelompok')
            ->where('id_kelompok', $id)
            ->count();

        $anggota = DB::table('anggota_kelompok')
            ->where('id_kelompok', $id)
            ->orderBy('nama_anggota')
            ->limit(10)
            ->get();

        $kups = DB::table('kups')
            ->where('id_kelompok', $id)
            ->orderBy('nama_kups')
            ->get();

        $potensi = DB::table('potensi_kelompok')
            ->where('id_kelompok', $id)
            ->orderByDesc('created_at')
            ->get();

        $pendamping = DB::table('pendamping_kelompok as pk')
            ->join('users as u', 'pk.id_user', '=', 'u.id')
            ->where('pk.id_kelompok', $id)
            ->select('u.nama', 'u.username')
            ->get();

        return view('admin.detail-kelompok', compact(
            'kelompok', 'rkpsTerakhir', 'rktTerakhir', 'totalAnggota', 'anggota',
            'kups', 'potensi', 'pendamping'
        ));
    }

    public function tambahKelompok()
    {
        $daftarCdk = DB::table('cdk')->orderBy('nama')->get(['id', 'nama', 'kode']);
        return view('admin.tambah-kelompok', compact('daftarCdk'));
    }

    public function storeKelompok(Request $request)
    {
        $data = $request->validate([
            'nama_kelompok' => ['required', 'string', 'max:150'],
            'ketua'         => ['nullable', 'string', 'max:100'],
            'desa'          => ['nullable', 'string', 'max:100'],
            'kecamatan'     => ['nullable', 'string', 'max:100'],
            'kabupaten'     => ['nullable', 'string', 'max:100'],
            'skema_ps'      => ['nullable', 'string', 'max:50'],
            'luas_lahan'    => ['nullable', 'numeric', 'min:0'],
            'no_sk'         => ['nullable', 'string', 'max:100'],
            'tanggal_sk'    => ['nullable', 'date'],
            'id_cdk'        => ['nullable', 'integer', 'exists:cdk,id'],
            'sk_ps'         => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'sk_kelompok'   => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $skPsPath = null;
        if ($request->hasFile('sk_ps')) {
            $skPsPath = $request->file('sk_ps')->store('sk_ps', 'public');
        }

        $skKelompokPath = null;
        if ($request->hasFile('sk_kelompok')) {
            $skKelompokPath = $request->file('sk_kelompok')->store('sk_kelompok', 'public');
        }

        DB::table('kelompok')->insert([
            'id_cdk'        => $data['id_cdk'] ?? null,
            'nama_kelompok' => $data['nama_kelompok'],
            'ketua'         => $data['ketua'] ?? null,
            'desa'          => $data['desa'] ?? null,
            'kecamatan'     => $data['kecamatan'] ?? null,
            'kabupaten'     => $data['kabupaten'] ?? null,
            'skema_ps'      => $data['skema_ps'] ?? null,
            'luas_lahan'    => $data['luas_lahan'] ?? 0,
            'no_sk'         => $data['no_sk'] ?? null,
            'tanggal_sk'    => $data['tanggal_sk'] ?? null,
            'id_pendamping' => 0,
            'sk_ps'         => $skPsPath,
            'sk_kelompok'   => $skKelompokPath,
            'created_at'    => now(),
        ]);

        return redirect()->route('admin.data-kelompok')
            ->with('success', 'Data kelompok berhasil ditambahkan.');
    }

    public function editKelompok($id)
    {
        $kelompok = DB::table('kelompok')->where('id', $id)->first();
        abort_unless($kelompok, 404);

        $daftarCdk = DB::table('cdk')->orderBy('nama')->get(['id', 'nama', 'kode']);

        return view('admin.edit-kelompok', compact('kelompok', 'daftarCdk'));
    }

    public function updateKelompok(Request $request, $id)
    {
        $kelompok = DB::table('kelompok')->where('id', $id)->first();
        abort_unless($kelompok, 404);

        $data = $request->validate([
            'nama_kelompok' => ['required', 'string', 'max:150'],
            'ketua'         => ['nullable', 'string', 'max:100'],
            'desa'          => ['nullable', 'string', 'max:100'],
            'kecamatan'     => ['nullable', 'string', 'max:100'],
            'kabupaten'     => ['nullable', 'string', 'max:100'],
            'skema_ps'      => ['nullable', 'string', 'max:50'],
            'luas_lahan'    => ['nullable', 'numeric', 'min:0'],
            'no_sk'         => ['nullable', 'string', 'max:100'],
            'tanggal_sk'    => ['nullable', 'date'],
            'id_cdk'        => ['nullable', 'integer', 'exists:cdk,id'],
            'sk_ps'         => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'sk_kelompok'   => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $update = [
            'id_cdk'        => $data['id_cdk'] ?? null,
            'nama_kelompok' => $data['nama_kelompok'],
            'ketua'         => $data['ketua'] ?? null,
            'desa'          => $data['desa'] ?? null,
            'kecamatan'     => $data['kecamatan'] ?? null,
            'kabupaten'     => $data['kabupaten'] ?? null,
            'skema_ps'      => $data['skema_ps'] ?? null,
            'luas_lahan'    => $data['luas_lahan'] ?? 0,
            'no_sk'         => $data['no_sk'] ?? null,
            'tanggal_sk'    => $data['tanggal_sk'] ?? null,
        ];

        if ($request->hasFile('sk_ps')) {
            if ($kelompok->sk_ps && Storage::disk('public')->exists($kelompok->sk_ps)) {
                Storage::disk('public')->delete($kelompok->sk_ps);
            }
            $update['sk_ps'] = $request->file('sk_ps')->store('sk_ps', 'public');
        }

        if ($request->hasFile('sk_kelompok')) {
            if ($kelompok->sk_kelompok && Storage::disk('public')->exists($kelompok->sk_kelompok)) {
                Storage::disk('public')->delete($kelompok->sk_kelompok);
            }
            $update['sk_kelompok'] = $request->file('sk_kelompok')->store('sk_kelompok', 'public');
        }

        DB::table('kelompok')->where('id', $id)->update($update);

        return redirect()->route('admin.data-kelompok')
            ->with('success', 'Data kelompok berhasil diperbarui.');
    }

    public function hapusKelompok($id)
    {
        $kelompok = DB::table('kelompok')->where('id', $id)->first();
        abort_unless($kelompok, 404);

        if ($kelompok->sk_ps && Storage::disk('public')->exists($kelompok->sk_ps)) {
            Storage::disk('public')->delete($kelompok->sk_ps);
        }
        if ($kelompok->sk_kelompok && Storage::disk('public')->exists($kelompok->sk_kelompok)) {
            Storage::disk('public')->delete($kelompok->sk_kelompok);
        }

        DB::table('kelompok')->where('id', $id)->delete();

        return redirect()->route('admin.data-kelompok')
            ->with('success', 'Data kelompok berhasil dihapus.');
    }

    public function dataPendamping()
    {
        $pendamping = DB::table('pendamping')
            ->leftJoin('cdk', 'pendamping.id_cdk', '=', 'cdk.id')
            ->leftJoin('users', 'users.username', '=', 'pendamping.username')
            ->leftJoin('pendamping_kelompok', 'pendamping_kelompok.id_user', '=', 'users.id')
            ->select(
                'pendamping.id_pendamping',
                'pendamping.nama_pendamping',
                'pendamping.nip',
                'pendamping.username',
                'pendamping.status',
                'cdk.nama as nama_cdk',
                DB::raw('COUNT(pendamping_kelompok.id_kelompok) as jumlah_kelompok')
            )
            ->groupBy('pendamping.id_pendamping', 'pendamping.nama_pendamping', 'pendamping.nip', 'pendamping.username', 'pendamping.status', 'cdk.nama')
            ->orderBy('pendamping.nama_pendamping')
            ->get();

        return view('admin.data-pendamping', compact('pendamping'));
    }

    public function dataAnggota()
    {
        $anggota = DB::table('anggota_kelompok')
            ->leftJoin('kelompok', 'anggota_kelompok.id_kelompok', '=', 'kelompok.id')
            ->orderBy('anggota_kelompok.nama_anggota')
            ->get([
                'anggota_kelompok.*',
                'kelompok.nama_kelompok',
            ]);

        $daftarKelompok = DB::table('kelompok')->orderBy('nama_kelompok')->get();

        return view('admin.data-anggota', compact('anggota', 'daftarKelompok'));
    }

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

    public function cetakKelompokKosong()
    {
        $kelompokKosong = DB::table('kelompok')
            ->where(function ($q) {
                $q->whereNull('id_pendamping')->orWhere('id_pendamping', 0);
            })
            ->orderBy('nama_kelompok')
            ->get();

        return view('admin.cetak-kelompok-kosong', compact('kelompokKosong'));
    }

    public function cetakRekapPendamping()
    {
        $rekapPendamping = DB::table('users')
            ->leftJoin('pendamping_kelompok', 'users.id', '=', 'pendamping_kelompok.id_user')
            ->leftJoin('kelompok', 'pendamping_kelompok.id_kelompok', '=', 'kelompok.id')
            ->where('users.role', 'pendamping')
            ->select('users.nama', 'users.username', 'kelompok.nama_kelompok', 'kelompok.desa')
            ->orderBy('users.nama')
            ->orderBy('kelompok.nama_kelompok')
            ->get();

        return view('admin.cetak-rekap-pendamping', compact('rekapPendamping'));
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
                'k.nama_kelompok',
                'n.nama_produk',
                'n.satuan',
                'n.tahun',
                't.bulan',
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

    public function petaKelompok(Request $request)
    {
        $skema = $request->query('skema', '');
        $statusPeta = $request->query('status_peta', '');
        $search = $request->query('q', '');

        $query = DB::table('kelompok as k')
            ->leftJoin('users as u', 'k.id_pendamping', '=', 'u.id')
            ->orderBy('k.nama_kelompok');

        if ($skema !== '') {
            $query->where('k.skema_ps', $skema);
        }
        if ($statusPeta === 'ada') {
            $query->whereNotNull('k.polygon_kelompok')->where('k.polygon_kelompok', '!=', '');
        } elseif ($statusPeta === 'kosong') {
            $query->where(function ($q) {
                $q->whereNull('k.polygon_kelompok')->orWhere('k.polygon_kelompok', '');
            });
        }
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('k.nama_kelompok', 'like', "%{$search}%")
                  ->orWhere('k.desa', 'like', "%{$search}%")
                  ->orWhere('k.kecamatan', 'like', "%{$search}%");
            });
        }

        $kelompok = $query->get([
            'k.id', 'k.nama_kelompok', 'k.ketua', 'k.desa', 'k.kecamatan',
            'k.kabupaten', 'k.skema_ps', 'k.luas_lahan', 'k.id_pendamping',
            'k.polygon_kelompok', 'u.nama as nama_pendamping',
        ]);

        $daftarSkema = DB::table('kelompok')
            ->select('skema_ps')
            ->whereNotNull('skema_ps')->where('skema_ps', '!=', '')
            ->distinct()->orderBy('skema_ps')
            ->pluck('skema_ps');

        return view(
            'admin.peta-kelompok',
            compact('kelompok', 'daftarSkema', 'skema', 'statusPeta', 'search')
        );
    }

    public function dataLaporan()
    {
        $data = DB::table('laporan_kegiatan as lk')
            ->leftJoin('users as u', 'lk.id_user', '=', 'u.id')
            ->leftJoin('kelompok as k', 'lk.id_kelompok', '=', 'k.id')
            ->orderByDesc('lk.tanggal')
            ->get([
                'lk.id',
                'lk.tanggal',
                'lk.kegiatan',
                'lk.deskripsi',
                'lk.foto',
                'k.nama_kelompok',
                'u.nama as nama_user',
            ]);

        $listTahun = DB::table('laporan_kegiatan')
            ->select(DB::raw('YEAR(tanggal) as tahun'))
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        $listUser = DB::table('users')
            ->where('role', 'pendamping')
            ->whereNotNull('nama')
            ->where('nama', '!=', '')
            ->orderBy('nama')
            ->get(['id', 'nama']);

        $listKelompok = DB::table('kelompok')
            ->orderBy('nama_kelompok')
            ->get(['id', 'nama_kelompok']);

        return view(
            'admin.data-laporan',
            compact(
                'data',
                'listTahun',
                'listUser',
                'listKelompok'
            )
        );
    }

    public function dataUser(Request $request)
    {
        $cari = trim($request->query('cari', ''));
        $role = $request->query('role', '');
        $allowedRoles = ['admin', 'admin_cdk', 'pendamping', 'kelompok'];

        $query = DB::table('users')
            ->leftJoin('cdk', 'users.id_cdk', '=', 'cdk.id')
            ->leftJoin('kelompok', 'users.id_kelompok', '=', 'kelompok.id')
            ->select('users.id', 'users.nama', 'users.username', 'users.role', 'cdk.nama as nama_cdk', 'kelompok.nama_kelompok')
            ->when($cari !== '', function ($q) use ($cari) {
                $q->where(function ($subQuery) use ($cari) {
                    $subQuery->where('users.nama', 'like', '%' . $cari . '%')
                        ->orWhere('users.username', 'like', '%' . $cari . '%');
                });
            })
            ->when(in_array($role, $allowedRoles, true), function ($q) use ($role) {
                $q->where('users.role', $role);
            })
            ->orderBy('users.nama')
            ->get();

        $notif = session('success') ?? session('error');
        if ($request->query('reset') !== null) {
            $notif = 'Password user berhasil direset menjadi <b>123456</b>.';
        } elseif ($request->query('hapus') !== null) {
            $notif = 'User berhasil dihapus!';
        }

        return view(
            'admin.data-user',
            compact('query', 'cari', 'role', 'notif')
        );
    }

    public function createUser()
    {
        $daftarKelompok = DB::table('kelompok')
            ->orderBy('nama_kelompok')
            ->get(['id', 'nama_kelompok']);
        $daftarCdk = DB::table('cdk')->orderBy('nama')->get(['id', 'nama']);

        return view('admin.tambah-user', compact('daftarKelompok', 'daftarCdk'));
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role' => ['required', 'in:admin,admin_cdk,pendamping,kelompok'],
            'id_kelompok' => ['nullable', 'integer', 'exists:kelompok,id'],
            'id_cdk' => ['nullable', 'integer', 'exists:cdk,id'],
        ]);

        if ($data['role'] !== 'kelompok') {
            $data['id_kelompok'] = null;
        }
        if ($data['role'] !== 'admin_cdk') {
            $data['id_cdk'] = null;
        }

        DB::table('users')->insert([
            'nama' => $data['nama'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'id_kelompok' => $data['id_kelompok'] ?? null,
            'id_cdk' => $data['id_cdk'] ?? null,
            'created_at' => now(),
        ]);

        return redirect()->route('admin.data-user')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function editUser(int $id)
    {
        $user = DB::table('users')->where('id', $id)->first();
        abort_unless($user, 404);

        $daftarKelompok = DB::table('kelompok')
            ->orderBy('nama_kelompok')
            ->get(['id', 'nama_kelompok']);
        $daftarCdk = DB::table('cdk')->orderBy('nama')->get(['id', 'nama']);

        return view('admin.edit-user', compact('user', 'daftarKelompok', 'daftarCdk'));
    }

    public function updateUser(Request $request, int $id)
    {
        $user = DB::table('users')->where('id', $id)->first();
        abort_unless($user, 404);

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username,' . $id],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'role' => ['required', 'in:admin,admin_cdk,pendamping,kelompok'],
            'id_kelompok' => ['nullable', 'integer', 'exists:kelompok,id'],
            'id_cdk' => ['nullable', 'integer', 'exists:cdk,id'],
        ]);

        if ($data['role'] !== 'kelompok') {
            $data['id_kelompok'] = null;
        }
        if ($data['role'] !== 'admin_cdk') {
            $data['id_cdk'] = null;
        }

        $update = [
            'nama' => $data['nama'],
            'username' => $data['username'],
            'role' => $data['role'],
            'id_kelompok' => $data['id_kelompok'] ?? null,
            'id_cdk' => $data['id_cdk'] ?? null,
        ];

        if (!empty($data['password'])) {
            $update['password'] = Hash::make($data['password']);
        }

        DB::table('users')->where('id', $id)->update($update);

        return redirect()->route('admin.data-user')
            ->with('success', 'Data user berhasil diperbarui.');
    }

    public function resetUserPassword(int $id)
    {
        $user = DB::table('users')->where('id', $id)->first();
        abort_unless($user, 404);

        DB::table('users')->where('id', $id)->update([
            'password' => Hash::make('123456'),
        ]);

        return redirect()->route('admin.data-user')
            ->with('success', 'Password user berhasil direset menjadi 123456.');
    }

    public function deleteUser(int $id)
    {
        if (auth()->id() === $id) {
            return redirect()->route('admin.data-user')
                ->with('error', 'Akun yang sedang digunakan tidak dapat dihapus.');
        }

        $deleted = DB::table('users')->where('id', $id)->delete();

        return redirect()->route('admin.data-user')
            ->with($deleted ? 'success' : 'error', $deleted ? 'User berhasil dihapus.' : 'User tidak ditemukan.');
    }

    public function aktivitasUser(Request $request)
    {
        $tanggalAwal = $request->query('tgl_awal', '');
        $tanggalAkhir = $request->query('tgl_akhir', '');
        $role = $request->query('role', '');
        $allowedRoles = ['admin', 'pendamping', 'kelompok'];

        $logs = DB::table('user_logs as l')
            ->join('users as u', 'l.user_id', '=', 'u.id')
            ->select(
                'u.nama',
                'u.username',
                'u.role',
                'l.login_time',
                'l.logout_time',
                'l.last_activity',
                DB::raw('TIMESTAMPDIFF(MINUTE, l.login_time, COALESCE(l.logout_time, l.last_activity)) as durasi')
            )
            ->when($tanggalAwal !== '' && $tanggalAkhir !== '', function ($q) use ($tanggalAwal, $tanggalAkhir) {
                $q->whereBetween(DB::raw('DATE(l.login_time)'), [$tanggalAwal, $tanggalAkhir]);
            })
            ->when(in_array($role, $allowedRoles, true), function ($q) use ($role) {
                $q->where('u.role', $role);
            })
            ->orderByDesc('l.login_time')
            ->get()
            ->map(function ($log) {
                $online = empty($log->logout_time)
                    && $log->last_activity
                    && strtotime($log->last_activity) >= now()->subMinutes(5)->timestamp;
                $log->status = $online ? 'Online' : 'Offline';
                return $log;
            });

        return view(
            'admin.aktivitas-user',
            compact('logs', 'tanggalAwal', 'tanggalAkhir', 'role')
        );
    }

    public function exportAktivitasUser(Request $request)
    {
        $tanggalAwal = $request->query('tgl_awal', '');
        $tanggalAkhir = $request->query('tgl_akhir', '');
        $role = $request->query('role', '');
        $allowedRoles = ['admin', 'pendamping', 'kelompok'];

        $logs = DB::table('user_logs as l')
            ->join('users as u', 'l.user_id', '=', 'u.id')
            ->select('u.nama', 'u.username', 'u.role', 'l.login_time', 'l.logout_time', 'l.last_activity', DB::raw('TIMESTAMPDIFF(MINUTE, l.login_time, COALESCE(l.logout_time, l.last_activity)) as durasi'))
            ->when($tanggalAwal !== '' && $tanggalAkhir !== '', fn ($q) => $q->whereBetween(DB::raw('DATE(l.login_time)'), [$tanggalAwal, $tanggalAkhir]))
            ->when(in_array($role, $allowedRoles, true), fn ($q) => $q->where('u.role', $role))
            ->orderByDesc('l.login_time')
            ->get();

        return response()->streamDownload(function () use ($logs) {
            echo "<table border='1'><tr><th>Nama</th><th>Username</th><th>Role</th><th>Login</th><th>Logout</th><th>Durasi (Menit)</th></tr>";
            foreach ($logs as $log) {
                echo '<tr><td>' . e($log->nama) . '</td><td>' . e($log->username) . '</td><td>' . e(ucfirst($log->role)) . '</td><td>' . e($log->login_time ?? '-') . '</td><td>' . e($log->logout_time ?? '-') . '</td><td>' . e($log->durasi ?? 0) . '</td></tr>';
            }
            echo '</table>';
        }, 'aktivitas-user.xls', ['Content-Type' => 'application/vnd.ms-excel']);
    }

    /* ─── Pendamping CRUD ───────────────────────────────────── */

    public function tambahPendamping()
    {
        $daftarCdk = DB::table('cdk')->orderBy('nama')->get(['id', 'nama']);
        return view('admin.tambah-pendamping', compact('daftarCdk'));
    }

    public function simpanPendamping(Request $request)
    {
        $data = $request->validate([
            'nama_pendamping' => ['required', 'string', 'max:150'],
            'nip'             => ['nullable', 'string', 'max:30'],
            'username'        => ['required', 'string', 'max:50', 'unique:users,username', 'unique:pendamping,username'],
            'password'        => ['required', 'string', 'min:6', 'confirmed'],
            'no_hp'           => ['nullable', 'string', 'max:30'],
            'email'           => ['nullable', 'string', 'max:150', 'email'],
            'alamat'          => ['nullable', 'string'],
            'id_cdk'          => ['required', 'integer', 'exists:cdk,id'],
        ]);

        $hash = Hash::make($data['password']);

        // Insert ke tabel users
        DB::table('users')->insert([
            'nama'       => $data['nama_pendamping'],
            'username'   => $data['username'],
            'password'   => $hash,
            'role'       => 'pendamping',
            'id_cdk'     => $data['id_cdk'],
            'created_at' => now(),
        ]);

        // Insert ke tabel pendamping
        DB::table('pendamping')->insert([
            'id_cdk'         => $data['id_cdk'],
            'nama_pendamping' => $data['nama_pendamping'],
            'nip'            => $data['nip'] ?? null,
            'username'       => $data['username'],
            'password'       => $hash,
            'no_hp'          => $data['no_hp'] ?? null,
            'email'          => $data['email'] ?? null,
            'alamat'         => $data['alamat'] ?? null,
            'status'         => 'aktif',
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return redirect()->route('admin.data-pendamping')
            ->with('success', 'Data pendamping berhasil ditambahkan.');
    }

    public function editPendamping($id)
    {
        $pendamping = DB::table('pendamping')->where('id_pendamping', $id)->first();
        abort_unless($pendamping, 404);
        $daftarCdk = DB::table('cdk')->orderBy('nama')->get(['id', 'nama']);
        return view('admin.edit-pendamping', compact('pendamping', 'daftarCdk'));
    }

    public function updatePendamping(Request $request, $id)
    {
        $pendamping = DB::table('pendamping')->where('id_pendamping', $id)->first();
        abort_unless($pendamping, 404);

        $data = $request->validate([
            'nama_pendamping' => ['required', 'string', 'max:150'],
            'nip'             => ['nullable', 'string', 'max:30'],
            'username'        => ['required', 'string', 'max:50', 'unique:pendamping,username,' . $id . ',id_pendamping', 'unique:users,username,' . ($pendamping->username ? '' : '999999999') . ',username'],
            'password'        => ['nullable', 'string', 'min:6', 'confirmed'],
            'no_hp'           => ['nullable', 'string', 'max:30'],
            'email'           => ['nullable', 'string', 'max:150', 'email'],
            'alamat'          => ['nullable', 'string'],
            'id_cdk'          => ['required', 'integer', 'exists:cdk,id'],
            'status'          => ['required', 'in:aktif,nonaktif'],
        ]);

        $update = [
            'nama_pendamping' => $data['nama_pendamping'],
            'nip'             => $data['nip'] ?? null,
            'username'        => $data['username'],
            'no_hp'           => $data['no_hp'] ?? null,
            'email'           => $data['email'] ?? null,
            'alamat'          => $data['alamat'] ?? null,
            'id_cdk'          => $data['id_cdk'],
            'status'          => $data['status'],
            'updated_at'      => now(),
        ];

        if (!empty($data['password'])) {
            $hash = Hash::make($data['password']);
            $update['password'] = $hash;
            // Update juga di tabel users
            DB::table('users')->where('username', $pendamping->username)->where('role', 'pendamping')->update([
                'password' => $hash,
            ]);
        }

        // Update juga di tabel users (nama, username)
        DB::table('users')->where('username', $pendamping->username)->where('role', 'pendamping')->update([
            'nama'     => $data['nama_pendamping'],
            'username' => $data['username'],
            'id_cdk'   => $data['id_cdk'],
        ]);

        DB::table('pendamping')->where('id_pendamping', $id)->update($update);

        return redirect()->route('admin.data-pendamping')
            ->with('success', 'Data pendamping berhasil diperbarui.');
    }

    public function hapusPendamping($id)
    {
        $pendamping = DB::table('pendamping')->where('id_pendamping', $id)->first();
        abort_unless($pendamping, 404);

        // Hapus dari users juga
        DB::table('users')->where('username', $pendamping->username)->where('role', 'pendamping')->delete();
        DB::table('pendamping')->where('id_pendamping', $id)->delete();

        return redirect()->route('admin.data-pendamping')
            ->with('success', 'Data pendamping berhasil dihapus.');
    }

    public function detailPendamping($id)
    {
        $pendamping = DB::table('pendamping')
            ->leftJoin('cdk', 'pendamping.id_cdk', '=', 'cdk.id')
            ->where('pendamping.id_pendamping', $id)
            ->select('pendamping.*', 'cdk.nama as nama_cdk')
            ->first();

        abort_unless($pendamping, 404);

        $user = DB::table('users')->where('username', $pendamping->username)->first();

        // Ambil kelompok binaan dari 2 sumber:
        // 1. Via pivot pendamping_kelompok.id_user = $user->id
        $kelompokPivot = collect();
        if ($user) {
            $kelompokPivot = DB::table('pendamping_kelompok as pk')
                ->join('kelompok as k', 'pk.id_kelompok', '=', 'k.id')
                ->leftJoin('cdk as c', 'k.id_cdk', '=', 'c.id')
                ->where('pk.id_user', $user->id)
                ->select('k.*', 'c.nama as nama_cdk')
                ->get();
        }

        // 2. Via kolom kelompok.id_pendamping = $pendamping->id_pendamping
        $kelompokKolom = DB::table('kelompok as k')
            ->leftJoin('cdk as c', 'k.id_cdk', '=', 'c.id')
            ->where('k.id_pendamping', $pendamping->id_pendamping)
            ->select('k.*', 'c.nama as nama_cdk')
            ->get();

        // Gabungkan & unique by id
        $kelompokBinaan = $kelompokPivot->merge($kelompokKolom)->unique('id')->values();

        $jumlahKelompok = $kelompokBinaan->count();
        $totalLuasLahan = $kelompokBinaan->sum('luas_lahan') ?? 0;

        $binaanIds = $kelompokBinaan->pluck('id')->toArray();
        $kelompokTersedia = DB::table('kelompok')
            ->whereNotIn('id', $binaanIds)
            ->orderBy('nama_kelompok')
            ->get(['id', 'nama_kelompok', 'desa', 'kecamatan', 'kabupaten']);

        return view('admin.detail-pendamping', compact(
            'pendamping',
            'user',
            'kelompokBinaan',
            'jumlahKelompok',
            'totalLuasLahan',
            'kelompokTersedia'
        ));
    }

    public function tambahBinaanPendamping(Request $request, $id)
    {
        $pendamping = DB::table('pendamping')->where('id_pendamping', $id)->first();
        abort_unless($pendamping, 404);

        $data = $request->validate([
            'id_kelompok' => ['required', 'integer', 'exists:kelompok,id'],
        ]);

        $user = DB::table('users')->where('username', $pendamping->username)->first();

        if ($user) {
            $exists = DB::table('pendamping_kelompok')
                ->where('id_user', $user->id)
                ->where('id_kelompok', $data['id_kelompok'])
                ->exists();

            if (!$exists) {
                DB::table('pendamping_kelompok')->insert([
                    'id_user'     => $user->id,
                    'id_kelompok' => $data['id_kelompok'],
                ]);
            }
        }

        DB::table('kelompok')->where('id', $data['id_kelompok'])->update([
            'id_pendamping' => $pendamping->id_pendamping,
        ]);

        return redirect()->route('admin.data-pendamping.detail', $id)
            ->with('success', 'Kelompok binaan berhasil ditambahkan.');
    }

    public function lepasBinaanPendamping(Request $request)
    {
        $data = $request->validate([
            'pendamping_id' => ['required', 'integer', 'exists:pendamping,id_pendamping'],
            'kelompok_id'   => ['required', 'integer', 'exists:kelompok,id'],
        ]);

        $pendamping = DB::table('pendamping')->where('id_pendamping', $data['pendamping_id'])->first();
        abort_unless($pendamping, 404);

        $user = DB::table('users')->where('username', $pendamping->username)->first();

        if ($user) {
            DB::table('pendamping_kelompok')
                ->where('id_user', $user->id)
                ->where('id_kelompok', $data['kelompok_id'])
                ->delete();
        }

        DB::table('kelompok')
            ->where('id', $data['kelompok_id'])
            ->where('id_pendamping', $pendamping->id_pendamping)
            ->update(['id_pendamping' => 0]);

        return redirect()->route('admin.data-pendamping.detail', $data['pendamping_id'])
            ->with('success', 'Kelompok binaan berhasil dilepas.');
    }

    public function dataRkps()
    {
        $rkps = DB::table('rkps')
            ->leftJoin('kelompok', 'rkps.id_kelompok', '=', 'kelompok.id')
            ->orderByDesc('rkps.tahun_mulai')
            ->get(['rkps.*', 'kelompok.nama_kelompok']);

        return view('admin.data-rkps', compact('rkps'));
    }

    public function createRkps()
    {
        $daftarKelompok = DB::table('kelompok')
            ->orderBy('nama_kelompok')
            ->get(['id', 'nama_kelompok']);

        return view('admin.tambah-rkps', compact('daftarKelompok'));
    }

    public function storeRkps(Request $request)
    {
        $data = $request->validate([
            'id_kelompok' => ['required', 'integer', 'exists:kelompok,id'],
            'tahun_mulai' => ['required', 'integer', 'between:1900,2200'],
            'tahun_selesai' => ['required', 'integer', 'gte:tahun_mulai', 'between:1900,2200'],
            'no_penilaian' => ['nullable', 'string', 'max:100'],
            'tgl_penilaian' => ['nullable', 'date'],
            'no_pengesahan' => ['nullable', 'string', 'max:100'],
            'tgl_pengesahan' => ['nullable', 'date'],
            'file_rkps' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $filename = null;
        if ($request->hasFile('file_rkps')) {
            $filename = $this->storeRkpsFile($request->file('file_rkps'));
        }

        DB::table('rkps')->insert([
            'id_kelompok' => $data['id_kelompok'],
            'tahun_mulai' => $data['tahun_mulai'],
            'tahun_selesai' => $data['tahun_selesai'],
            'no_penilaian' => $data['no_penilaian'] ?? null,
            'tgl_penilaian' => $data['tgl_penilaian'] ?? null,
            'no_pengesahan' => $data['no_pengesahan'] ?? null,
            'tgl_pengesahan' => $data['tgl_pengesahan'] ?? null,
            'file_rkps' => $filename,
            'created_at' => now(),
        ]);

        return redirect()->route('admin.data-rkps')->with('success', 'Data RKPS berhasil ditambahkan.');
    }

    public function editRkps(int $id)
    {
        $rkps = DB::table('rkps')->where('id_rkps', $id)->first();
        abort_unless($rkps, 404);

        $daftarKelompok = DB::table('kelompok')
            ->orderBy('nama_kelompok')
            ->get(['id', 'nama_kelompok']);

        return view('admin.edit-rkps', compact('rkps', 'daftarKelompok'));
    }

    public function updateRkps(Request $request, int $id)
    {
        $rkps = DB::table('rkps')->where('id_rkps', $id)->first();
        abort_unless($rkps, 404);

        $data = $request->validate([
            'id_kelompok' => ['required', 'integer', 'exists:kelompok,id'],
            'tahun_mulai' => ['required', 'integer', 'between:1900,2200'],
            'tahun_selesai' => ['required', 'integer', 'gte:tahun_mulai', 'between:1900,2200'],
            'no_penilaian' => ['nullable', 'string', 'max:100'],
            'tgl_penilaian' => ['nullable', 'date'],
            'no_pengesahan' => ['nullable', 'string', 'max:100'],
            'tgl_pengesahan' => ['nullable', 'date'],
            'file_rkps' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $filename = $rkps->file_rkps;
        if ($request->hasFile('file_rkps')) {
            $filename = $this->storeRkpsFile($request->file('file_rkps'));
            $this->deleteRkpsFile($rkps->file_rkps);
        }

        DB::table('rkps')->where('id_rkps', $id)->update([
            'id_kelompok' => $data['id_kelompok'],
            'tahun_mulai' => $data['tahun_mulai'],
            'tahun_selesai' => $data['tahun_selesai'],
            'no_penilaian' => $data['no_penilaian'] ?? null,
            'tgl_penilaian' => $data['tgl_penilaian'] ?? null,
            'no_pengesahan' => $data['no_pengesahan'] ?? null,
            'tgl_pengesahan' => $data['tgl_pengesahan'] ?? null,
            'file_rkps' => $filename,
        ]);

        return redirect()->route('admin.data-rkps')->with('success', 'Data RKPS berhasil diperbarui.');
    }

    public function deleteRkps(int $id)
    {
        $rkps = DB::table('rkps')->where('id_rkps', $id)->first();
        abort_unless($rkps, 404);

        DB::table('rkps')->where('id_rkps', $id)->delete();
        $this->deleteRkpsFile($rkps->file_rkps);

        return redirect()->route('admin.data-rkps')->with('success', 'Data RKPS berhasil dihapus.');
    }

    private function storeRkpsFile($file): string
    {
        $directory = public_path('uploads/rkps');
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = time() . '_' . Str::random(12) . '.' . strtolower($file->getClientOriginalExtension());
        $file->move($directory, $filename);

        return $filename;
    }

    private function deleteRkpsFile(?string $filename): void
    {
        if (! $filename || basename($filename) !== $filename) {
            return;
        }

        $path = public_path('uploads/rkps/' . $filename);
        if (is_file($path)) {
            unlink($path);
        }
    }

    public function createRkt()
    {
        $daftarKelompok = DB::table('kelompok')
            ->orderBy('nama_kelompok')
            ->get(['id', 'nama_kelompok']);
        $daftarRkps = DB::table('rkps')
            ->leftJoin('kelompok', 'rkps.id_kelompok', '=', 'kelompok.id')
            ->orderByDesc('rkps.tahun_mulai')
            ->get(['rkps.id_rkps', 'rkps.id_kelompok', 'rkps.tahun_mulai', 'rkps.tahun_selesai', 'kelompok.nama_kelompok']);

        return view('admin.tambah-rkt', compact('daftarKelompok', 'daftarRkps'));
    }

    public function storeRkt(Request $request)
    {
        $data = $request->validate([
            'id_kelompok' => ['required', 'integer', 'exists:kelompok,id'],
            'id_rkps' => ['nullable', 'integer', 'exists:rkps,id_rkps'],
            'tahun' => ['required', 'integer', 'between:1900,2200'],
            'no_penetapan' => ['nullable', 'string', 'max:100'],
            'tgl_penetapan' => ['nullable', 'date'],
            'file_rkt' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        if (!empty($data['id_rkps'])) {
            $rkpsBelongs = DB::table('rkps')->where('id_rkps', $data['id_rkps'])->where('id_kelompok', $data['id_kelompok'])->exists();
            if (! $rkpsBelongs) {
                return back()->withInput()->withErrors(['id_rkps' => 'RKPS yang dipilih bukan milik kelompok tersebut.']);
            }
        }

        $filename = $request->hasFile('file_rkt') ? $this->storeRktFile($request->file('file_rkt')) : null;

        DB::table('rkt')->insert([
            'id_kelompok' => $data['id_kelompok'],
            'id_rkps' => $data['id_rkps'] ?? null,
            'tahun' => $data['tahun'],
            'no_penetapan' => $data['no_penetapan'] ?? null,
            'tgl_penetapan' => $data['tgl_penetapan'] ?? null,
            'file_rkt' => $filename,
            'created_at' => now(),
        ]);

        return redirect()->route('admin.data-rkt')->with('success', 'Data RKT berhasil ditambahkan.');
    }

    public function editRkt(int $id)
    {
        $rkt = DB::table('rkt')->where('id_rkt', $id)->first();
        abort_unless($rkt, 404);
        $daftarKelompok = DB::table('kelompok')->orderBy('nama_kelompok')->get(['id', 'nama_kelompok']);
        $daftarRkps = DB::table('rkps')->leftJoin('kelompok', 'rkps.id_kelompok', '=', 'kelompok.id')->orderByDesc('rkps.tahun_mulai')->get(['rkps.id_rkps', 'rkps.id_kelompok', 'rkps.tahun_mulai', 'rkps.tahun_selesai', 'kelompok.nama_kelompok']);

        return view('admin.edit-rkt', compact('rkt', 'daftarKelompok', 'daftarRkps'));
    }

    public function updateRkt(Request $request, int $id)
    {
        $rkt = DB::table('rkt')->where('id_rkt', $id)->first();
        abort_unless($rkt, 404);
        $data = $request->validate([
            'id_kelompok' => ['required', 'integer', 'exists:kelompok,id'],
            'id_rkps' => ['nullable', 'integer', 'exists:rkps,id_rkps'],
            'tahun' => ['required', 'integer', 'between:1900,2200'],
            'no_penetapan' => ['nullable', 'string', 'max:100'],
            'tgl_penetapan' => ['nullable', 'date'],
            'file_rkt' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);
        if (!empty($data['id_rkps'])) {
            $rkpsBelongs = DB::table('rkps')->where('id_rkps', $data['id_rkps'])->where('id_kelompok', $data['id_kelompok'])->exists();
            if (! $rkpsBelongs) return back()->withInput()->withErrors(['id_rkps' => 'RKPS yang dipilih bukan milik kelompok tersebut.']);
        }
        $filename = $rkt->file_rkt;
        if ($request->hasFile('file_rkt')) {
            $filename = $this->storeRktFile($request->file('file_rkt'));
            $this->deleteRktFile($rkt->file_rkt);
        }
        DB::table('rkt')->where('id_rkt', $id)->update([
            'id_kelompok' => $data['id_kelompok'], 'id_rkps' => $data['id_rkps'] ?? null, 'tahun' => $data['tahun'],
            'no_penetapan' => $data['no_penetapan'] ?? null, 'tgl_penetapan' => $data['tgl_penetapan'] ?? null, 'file_rkt' => $filename,
        ]);
        return redirect()->route('admin.data-rkt')->with('success', 'Data RKT berhasil diperbarui.');
    }

    public function deleteRkt(int $id)
    {
        $rkt = DB::table('rkt')->where('id_rkt', $id)->first();
        abort_unless($rkt, 404);
        DB::table('rkt')->where('id_rkt', $id)->delete();
        $this->deleteRktFile($rkt->file_rkt);
        return redirect()->route('admin.data-rkt')->with('success', 'Data RKT berhasil dihapus.');
    }

    private function storeRktFile($file): string
    {
        $directory = public_path('uploads/rkt');
        if (! is_dir($directory)) mkdir($directory, 0755, true);
        $filename = time() . '_' . Str::random(12) . '.' . strtolower($file->getClientOriginalExtension());
        $file->move($directory, $filename);
        return $filename;
    }

    private function deleteRktFile(?string $filename): void
    {
        if (! $filename || basename($filename) !== $filename) return;
        $path = public_path('uploads/rkt/' . $filename);
        if (is_file($path)) unlink($path);
    }

    public function dataRkt()
    {
        $rkt = DB::table('rkt')
            ->leftJoin('kelompok', 'rkt.id_kelompok', '=', 'kelompok.id')
            ->orderByDesc('rkt.tahun')
            ->get(['rkt.*', 'kelompok.nama_kelompok']);

        return view('admin.data-rkt', compact('rkt'));
    }
}