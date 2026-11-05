<?php

namespace App\Http\Controllers;

use App\Models\Cdk;
use App\Models\Kelompok;
use App\Models\Pendamping;
use App\Models\AnggotaKelompok;
use App\Models\PengajuanRkps;
use App\Models\PengajuanRkt;
use App\Models\LaporanKegiatan;
use App\Models\User;
use App\Services\GeoParserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminCdkController extends Controller
{
    private function cdkId(): int
    {
        abort_unless(Auth::check() && Auth::user()->id_cdk, 403, 'Akses khusus Admin CDK');
        return (int) Auth::user()->id_cdk;
    }

    private function getCdkInfo()
    {
        $user = Auth::user();
        if (!$user || !$user->id_cdk) {
            return null;
        }
        return Cdk::find($user->id_cdk);
    }

    private function cdkScope(): int
    {
        return $this->cdkId();
    }

    /* ─── 1. Dashboard Admin CDK ────────────────────────────── */

    public function dashboard()
    {
        $idCdk = $this->cdkId();
        $cdkInfo = Cdk::find($idCdk);

        if (!$cdkInfo) {
            return view('admin-cdk.dashboard', [
                'cdkInfo'         => null,
                'totalKelompok'   => 0,
                'totalPendamping' => 0,
                'totalAnggota'    => 0,
                'totalLuasLahan'  => 0,
                'totalNteData'    => 0,
                'totalLaporan'    => 0,
                'pengajuanRkps'   => 0,
                'pengajuanRkt'    => 0,
                'totalNte'        => 0,
                'totalVolume'     => 0,
                'komoditasNte'    => 0,
                'skemaLabels'     => [],
                'skemaValues'     => [],
                'laporanTerbaru'  => collect(),
                'kelompokTerbaru' => collect(),
            ]);
        }

        $totalKelompok   = Kelompok::where('id_cdk', $idCdk)->count();
        $totalPendamping = Pendamping::where('id_cdk', $idCdk)->count();
        $totalAnggota    = AnggotaKelompok::whereHas('kelompok', fn ($q) => $q->where('id_cdk', $idCdk))->count();
        $totalLuasLahan  = Kelompok::where('id_cdk', $idCdk)->sum('luas_lahan') ?? 0;

        $totalNteData = DB::table('nte_kelompok as n')
            ->join('kelompok as k', 'n.id_kelompok', '=', 'k.id')
            ->where('k.id_cdk', $idCdk)
            ->count();

        $totalLaporan = LaporanKegiatan::whereHas('kelompok', fn ($q) => $q->where('id_cdk', $idCdk))->count();

        $pengajuanRkps = PengajuanRkps::whereHas('kelompok', fn ($q) => $q->where('id_cdk', $idCdk))
            ->where('status', 'Menunggu')
            ->count();

        $pengajuanRkt = PengajuanRkt::whereHas('kelompok', fn ($q) => $q->where('id_cdk', $idCdk))
            ->where('status', 'Menunggu')
            ->count();

        $totalNte = DB::table('nte_transaksi as t')
            ->join('nte_kelompok as n', 't.id_nte', '=', 'n.id')
            ->join('kelompok as k', 'n.id_kelompok', '=', 'k.id')
            ->where('k.id_cdk', $idCdk)
            ->sum('t.total_nekon') ?? 0;

        $totalVolume = DB::table('nte_transaksi as t')
            ->join('nte_kelompok as n', 't.id_nte', '=', 'n.id')
            ->join('kelompok as k', 'n.id_kelompok', '=', 'k.id')
            ->where('k.id_cdk', $idCdk)
            ->sum('t.volume') ?? 0;

        $komoditasNte = DB::table('nte_kelompok as n')
            ->join('kelompok as k', 'n.id_kelompok', '=', 'k.id')
            ->where('k.id_cdk', $idCdk)
            ->distinct()
            ->count('n.nama_produk');

        // Sebaran skema PS di wilayah CDK
        $skemaData = Kelompok::where('id_cdk', $idCdk)
            ->whereNotNull('skema_ps')
            ->where('skema_ps', '!=', '')
            ->select('skema_ps', DB::raw('COUNT(*) as total'))
            ->groupBy('skema_ps')
            ->get();
        $skemaLabels = $skemaData->pluck('skema_ps')->toArray();
        $skemaValues = $skemaData->pluck('total')->toArray();

        // Laporan terbaru di wilayah CDK
        $laporanTerbaru = DB::table('laporan_kegiatan as l')
            ->join('kelompok as k', 'l.id_kelompok', '=', 'k.id')
            ->where('k.id_cdk', $idCdk)
            ->orderByDesc('l.tanggal')
            ->limit(5)
            ->get(['l.id', 'l.kegiatan', 'l.tanggal', 'k.nama_kelompok', 'l.foto']);

        // Kelompok terbaru di wilayah CDK
        $kelompokTerbaru = Kelompok::where('id_cdk', $idCdk)
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        return view('admin-cdk.dashboard', compact(
            'cdkInfo', 'totalKelompok', 'totalPendamping', 'totalAnggota',
            'totalLuasLahan', 'totalNteData', 'totalLaporan', 'pengajuanRkps',
            'pengajuanRkt', 'totalNte', 'totalVolume', 'komoditasNte',
            'skemaLabels', 'skemaValues', 'laporanTerbaru', 'kelompokTerbaru'
        ));
    }

    /* ─── 2. Data Kelompok (CDK Scope) ───────────────────────── */

    public function dataKelompok()
    {
        $id = $this->cdkId();
        $kelompok = Kelompok::where('id_cdk', $id)->orderBy('nama_kelompok')->get();
        return view('admin.data-kelompok', compact('kelompok'));
    }

    public function editKelompok($id)
    {
        $idCdk = $this->cdkId();
        $kelompok = DB::table('kelompok')
            ->where('id', $id)
            ->where('id_cdk', $idCdk)
            ->first();

        abort_unless($kelompok, 404, 'Data Kelompok tidak ditemukan di wilayah Anda');
        $daftarCdk = DB::table('cdk')->where('id', $idCdk)->get(['id', 'nama', 'kode']);
        return view('admin.edit-kelompok', compact('kelompok', 'daftarCdk'));
    }

    public function updateKelompok(Request $request, $id)
    {
        $idCdk = $this->cdkId();
        $kelompok = DB::table('kelompok')
            ->where('id', $id)
            ->where('id_cdk', $idCdk)
            ->first();

        abort_unless($kelompok, 404, 'Data Kelompok tidak ditemukan di wilayah Anda');

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
            'sk_ps'         => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'sk_kelompok'   => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'file_peta'     => ['nullable', 'file', 'max:20480'],
            'hapus_peta'    => ['nullable', 'boolean'],
        ]);

        $update = [
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

        if ($request->boolean('hapus_peta')) {
            $update['polygon_kelompok'] = null;
        } elseif ($request->hasFile('file_peta')) {
            try {
                $geoService = new GeoParserService();
                $update['polygon_kelompok'] = $geoService->parseToGeoJson($request->file('file_peta'));
            } catch (\Exception $e) {
                return back()->withInput()->withErrors(['file_peta' => 'Gagal membaca berkas peta: ' . $e->getMessage()]);
            }
        }

        DB::table('kelompok')->where('id', $id)->update($update);

        return redirect()->route('admin-cdk.data-kelompok')
            ->with('success', 'Data kelompok berhasil diperbarui.');
    }

    /* ─── 3. Data Pendamping & Binaan ────────────────────────── */

    public function dataPendamping()
    {
        $id = $this->cdkId();
        $pendamping = DB::table('pendamping')
            ->leftJoin('cdk', 'pendamping.id_cdk', '=', 'cdk.id')
            ->leftJoin('users', 'users.username', '=', 'pendamping.username')
            ->leftJoin('pendamping_kelompok', 'pendamping_kelompok.id_user', '=', 'users.id')
            ->where('pendamping.id_cdk', $id)
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

    public function detailPendamping($id)
    {
        $idCdk = $this->cdkId();
        $pendamping = DB::table('pendamping')
            ->leftJoin('cdk', 'pendamping.id_cdk', '=', 'cdk.id')
            ->where('pendamping.id_pendamping', $id)
            ->where('pendamping.id_cdk', $idCdk)
            ->select('pendamping.*', 'cdk.nama as nama_cdk')
            ->first();

        abort_unless($pendamping, 404);

        $user = User::where('username', $pendamping->username)->first();

        // Ambil kelompok binaan dari 2 sumber (pivot & kolom langsung):
        $kelompokPivot = collect();
        if ($user) {
            $kelompokPivot = DB::table('pendamping_kelompok as pk')
                ->join('kelompok as k', 'pk.id_kelompok', '=', 'k.id')
                ->leftJoin('cdk as c', 'k.id_cdk', '=', 'c.id')
                ->where('pk.id_user', $user->id)
                ->where('k.id_cdk', $idCdk)
                ->select('k.*', 'c.nama as nama_cdk')
                ->get();
        }

        $kelompokKolom = DB::table('kelompok as k')
            ->leftJoin('cdk as c', 'k.id_cdk', '=', 'c.id')
            ->where('k.id_pendamping', $pendamping->id_pendamping)
            ->where('k.id_cdk', $idCdk)
            ->select('k.*', 'c.nama as nama_cdk')
            ->get();

        $kelompokBinaan = $kelompokPivot->merge($kelompokKolom)->unique('id')->values();

        $jumlahKelompok = $kelompokBinaan->count();
        $totalLuasLahan = $kelompokBinaan->sum('luas_lahan') ?? 0;

        $binaanIds = $kelompokBinaan->pluck('id')->toArray();
        $kelompokTersedia = Kelompok::where('id_cdk', $idCdk)
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
        $idCdk = $this->cdkId();
        $pendamping = DB::table('pendamping')
            ->where('id_pendamping', $id)
            ->where('id_cdk', $idCdk)
            ->first();
        abort_unless($pendamping, 404);

        $data = $request->validate([
            'id_kelompok' => ['required', 'integer', 'exists:kelompok,id'],
        ]);

        $kelompok = Kelompok::where('id', $data['id_kelompok'])->where('id_cdk', $idCdk)->first();
        abort_unless($kelompok, 403);

        $user = User::where('username', $pendamping->username)->first();

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

        Kelompok::where('id', $data['id_kelompok'])
            ->update(['id_pendamping' => $pendamping->id_pendamping]);

        return redirect()->route('admin-cdk.data-pendamping.detail', $id)
            ->with('success', 'Kelompok binaan berhasil ditambahkan.');
    }

    public function lepasBinaanPendamping(Request $request)
    {
        $idCdk = $this->cdkId();
        $data = $request->validate([
            'pendamping_id' => ['required', 'integer'],
            'kelompok_id'   => ['required', 'integer'],
        ]);

        $pendamping = DB::table('pendamping')
            ->where('id_pendamping', $data['pendamping_id'])
            ->where('id_cdk', $idCdk)
            ->first();
        abort_unless($pendamping, 404);

        $user = User::where('username', $pendamping->username)->first();

        if ($user) {
            DB::table('pendamping_kelompok')
                ->where('id_user', $user->id)
                ->where('id_kelompok', $data['kelompok_id'])
                ->delete();
        }

        Kelompok::where('id', $data['kelompok_id'])
            ->where('id_cdk', $idCdk)
            ->where('id_pendamping', $pendamping->id_pendamping)
            ->update(['id_pendamping' => null]);

        return redirect()->route('admin-cdk.data-pendamping.detail', $data['pendamping_id'])
            ->with('success', 'Relasi kelompok binaan berhasil dilepas.');
    }

    /* ─── 4. Detail Kelompok ─────────────────────────────────── */

    public function detailKelompok($idKelompok)
    {
        $idCdk = $this->cdkId();
        $kelompok = Kelompok::with('cdk')
            ->where('id', $idKelompok)
            ->where('id_cdk', $idCdk)
            ->first();

        abort_unless($kelompok, 404, 'Data Kelompok tidak ditemukan');

        $rkpsTerakhir = DB::table('rkps')
            ->where('id_kelompok', $idKelompok)
            ->orderByDesc('tahun_mulai')
            ->first();

        $rktTerakhir = DB::table('rkt')
            ->where('id_kelompok', $idKelompok)
            ->orderByDesc('tahun')
            ->first();

        $totalAnggota = AnggotaKelompok::where('id_kelompok', $idKelompok)->count();

        $anggota = AnggotaKelompok::where('id_kelompok', $idKelompok)
            ->orderBy('nama_anggota')
            ->limit(10)
            ->get();

        $kups = DB::table('kups')
            ->where('id_kelompok', $idKelompok)
            ->orderBy('nama_kups')
            ->get();

        $potensi = DB::table('potensi_kelompok')
            ->where('id_kelompok', $idKelompok)
            ->orderByDesc('created_at')
            ->get();

        $pendamping = DB::table('pendamping_kelompok as pk')
            ->join('users as u', 'pk.id_user', '=', 'u.id')
            ->where('pk.id_kelompok', $idKelompok)
            ->select('u.nama', 'u.username')
            ->get();

        return view('admin.detail-kelompok', compact(
            'kelompok', 'rkpsTerakhir', 'rktTerakhir', 'totalAnggota', 'anggota',
            'kups', 'potensi', 'pendamping'
        ));
    }

    /* ─── 5. Data Anggota ────────────────────────────────────── */

    public function dataAnggota()
    {
        $id = $this->cdkId();
        $anggota = DB::table('anggota_kelompok as a')
            ->join('kelompok as k', 'a.id_kelompok', '=', 'k.id')
            ->where('k.id_cdk', $id)
            ->orderBy('a.nama_anggota')
            ->get(['a.*', 'k.nama_kelompok']);

        $daftarKelompok = Kelompok::where('id_cdk', $id)
            ->orderBy('nama_kelompok')
            ->get(['id', 'nama_kelompok']);

        return view('admin.data-anggota', compact('anggota', 'daftarKelompok'));
    }

    /* ─── 6. Peta Kelompok (Spasial) ─────────────────────────── */

    public function petaKelompok(Request $request)
    {
        $id = $this->cdkId();
        $skema = $request->query('skema', '');
        $statusPeta = $request->query('status_peta', '');
        $search = $request->query('q', '');

        $query = DB::table('kelompok as k')
            ->leftJoin('users as u', 'k.id_pendamping', '=', 'u.id')
            ->where('k.id_cdk', $id)
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

        $daftarSkema = Kelompok::where('id_cdk', $id)
            ->select('skema_ps')
            ->whereNotNull('skema_ps')
            ->where('skema_ps', '!=', '')
            ->distinct()
            ->orderBy('skema_ps')
            ->pluck('skema_ps');

        return view('admin.peta-kelompok', compact(
            'kelompok', 'daftarSkema', 'skema', 'statusPeta', 'search'
        ));
    }

    /* ─── 7. Data Laporan Kegiatan ───────────────────────────── */

    public function dataLaporan()
    {
        $id = $this->cdkId();
        $data = DB::table('laporan_kegiatan as l')
            ->join('kelompok as k', 'l.id_kelompok', '=', 'k.id')
            ->leftJoin('users as u', 'l.id_user', '=', 'u.id')
            ->where('k.id_cdk', $id)
            ->orderByDesc('l.tanggal')
            ->get(['l.*', 'k.nama_kelompok', 'u.nama as nama_user']);

        $listTahun = DB::table('laporan_kegiatan as l')
            ->join('kelompok as k', 'l.id_kelompok', '=', 'k.id')
            ->where('k.id_cdk', $id)
            ->select(DB::raw('YEAR(l.tanggal) as tahun'))
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        $listUser = User::where('role', 'pendamping')->get(['id', 'nama']);
        $listKelompok = Kelompok::where('id_cdk', $id)->get(['id', 'nama_kelompok']);

        return view('admin.data-laporan', compact('data', 'listTahun', 'listUser', 'listKelompok'));
    }

    /* ─── 8. Monitoring NTE ──────────────────────────────────── */

    public function monitoringNte(Request $request)
    {
        $id = $this->cdkId();
        $tahun = $request->query('tahun', '');
        $bulan = $request->query('bulan', '');
        $komoditas = $request->query('komoditas', '');
        $pendamping = $request->query('pendamping', '');
        $kelompokFilter = $request->query('kelompok', '');

        $query = DB::table('nte_transaksi as t')
            ->join('nte_kelompok as n', 't.id_nte', '=', 'n.id')
            ->join('kelompok as k', 'n.id_kelompok', '=', 'k.id')
            ->leftJoin('users as u', 'n.id_pendamping', '=', 'u.id')
            ->where('k.id_cdk', $id)
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

        $tahunData = DB::table('nte_kelompok as n')
            ->join('kelompok as k', 'n.id_kelompok', '=', 'k.id')
            ->where('k.id_cdk', $id)
            ->select('n.tahun')
            ->distinct()
            ->orderByDesc('n.tahun')
            ->pluck('n.tahun');

        $pendampingData = User::join('pendamping as p', 'users.username', '=', 'p.username')
            ->where('p.id_cdk', $id)
            ->where('users.role', 'pendamping')
            ->orderBy('users.nama')
            ->get(['users.id', 'users.nama']);

        $kelompokData = Kelompok::where('id_cdk', $id)
            ->orderBy('nama_kelompok')
            ->get(['id', 'nama_kelompok']);

        $komoditasData = DB::table('nte_kelompok as n')
            ->join('kelompok as k', 'n.id_kelompok', '=', 'k.id')
            ->where('k.id_cdk', $id)
            ->select('n.nama_produk')
            ->distinct()
            ->orderBy('n.nama_produk')
            ->pluck('n.nama_produk');

        if ($request->query('export') === 'excel') {
            return response()->streamDownload(function () use ($data) {
                echo "<table border='1'><tr><th>No</th><th>Tahun</th><th>Bulan</th><th>Kelompok</th><th>Komoditas</th><th>Satuan</th><th>Total Volume</th><th>Total NTE</th><th>Pendamping</th></tr>";
                foreach ($data as $i => $d) {
                    echo '<tr><td>' . ($i + 1) . '</td><td>' . e($d->tahun) . '</td><td>' . e($d->bulan) . '</td><td>' . e($d->nama_kelompok) . '</td><td>' . e($d->nama_produk) . '</td><td>' . e($d->satuan) . '</td><td>' . e($d->total_volume) . '</td><td>' . e($d->total_nekon) . '</td><td>' . e($d->nama_pendamping ?? '-') . '</td></tr>';
                }
                echo '</table>';
            }, 'monitoring_nte_cdk.xls', ['Content-Type' => 'application/vnd.ms-excel']);
        }

        return view('admin.monitoring-nte', compact(
            'data', 'totalVolume', 'totalNekon', 'tahun', 'bulan', 'komoditas',
            'pendamping', 'kelompokFilter', 'tahunData', 'pendampingData',
            'kelompokData', 'komoditasData'
        ));
    }

    /* ─── 9. Verifikasi RKPS & RKT ───────────────────────────── */

    public function verifikasiRkps()
    {
        $id = $this->cdkId();
        $pengajuanRkps = DB::table('pengajuan_rkps as p')
            ->join('kelompok as k', 'p.id_kelompok', '=', 'k.id')
            ->where('k.id_cdk', $id)
            ->orderByDesc('p.tanggal_pengajuan')
            ->get(['p.*', 'k.nama_kelompok']);

        return view('admin.verifikasi-rkps', compact('pengajuanRkps'));
    }

    public function prosesVerifikasiRkps(Request $request)
    {
        $idCdk = $this->cdkId();

        $data = $request->validate([
            'id'      => ['required', 'integer'],
            'aksi'    => ['required', 'in:Setujui,Revisi,Tolak'],
            'catatan' => ['nullable', 'string'],
        ]);

        $status = [
            'Setujui' => 'Disetujui',
            'Revisi'  => 'Revisi',
            'Tolak'   => 'Ditolak',
        ][$data['aksi']];

        $updated = DB::table('pengajuan_rkps as p')
            ->join('kelompok as k', 'p.id_kelompok', '=', 'k.id')
            ->where('p.id', $data['id'])
            ->where('k.id_cdk', $idCdk)
            ->update([
                'p.status'        => $status,
                'p.catatan_admin' => $data['catatan'] ?? '',
            ]);

        abort_unless($updated, 404);

        return redirect()->route('admin-cdk.verifikasi-rkps')
            ->with('success', "Pengajuan RKPS berhasil diubah menjadi {$status}.");
    }

    public function verifikasiRkt()
    {
        $id = $this->cdkId();
        $pengajuanRkt = DB::table('pengajuan_rkt as p')
            ->join('kelompok as k', 'p.id_kelompok', '=', 'k.id')
            ->where('k.id_cdk', $id)
            ->orderByDesc('p.tanggal_pengajuan')
            ->get(['p.*', 'k.nama_kelompok']);

        return view('admin.verifikasi-rkt', compact('pengajuanRkt'));
    }

    public function prosesVerifikasiRkt(Request $request)
    {
        $idCdk = $this->cdkId();

        $data = $request->validate([
            'id'      => ['required', 'integer'],
            'aksi'    => ['required', 'in:Setujui,Revisi,Tolak'],
            'catatan' => ['nullable', 'string'],
        ]);

        $status = [
            'Setujui' => 'Disetujui',
            'Revisi'  => 'Revisi',
            'Tolak'   => 'Ditolak',
        ][$data['aksi']];

        $updated = DB::table('pengajuan_rkt as p')
            ->join('kelompok as k', 'p.id_kelompok', '=', 'k.id')
            ->where('p.id', $data['id'])
            ->where('k.id_cdk', $idCdk)
            ->update([
                'p.status'        => $status,
                'p.catatan_admin' => $data['catatan'] ?? '',
            ]);

        abort_unless($updated, 404);

        return redirect()->route('admin-cdk.verifikasi-rkt')
            ->with('success', "Pengajuan RKT berhasil diubah menjadi {$status}.");
    }

    /* ─── 10. Rekap Wilayah CDK ──────────────────────────────── */

    public function rekapWilayah()
    {
        $idCdk = $this->cdkId();
        $cdkInfo = Cdk::find($idCdk);
        abort_unless($cdkInfo, 404);

        $jumlahKelompok   = Kelompok::where('id_cdk', $idCdk)->count();
        $jumlahPendamping = Pendamping::where('id_cdk', $idCdk)->count();
        $jumlahAnggota    = AnggotaKelompok::whereHas('kelompok', fn ($q) => $q->where('id_cdk', $idCdk))->count();
        $luasLahan        = Kelompok::where('id_cdk', $idCdk)->sum('luas_lahan') ?? 0;

        $jumlahNte = DB::table('nte_kelompok as n')
            ->join('kelompok as k', 'n.id_kelompok', '=', 'k.id')
            ->where('k.id_cdk', $idCdk)
            ->count();

        $jumlahLaporan = LaporanKegiatan::whereHas('kelompok', fn ($q) => $q->where('id_cdk', $idCdk))->count();
        $jumlahRkps    = PengajuanRkps::whereHas('kelompok', fn ($q) => $q->where('id_cdk', $idCdk))->count();
        $jumlahRkt     = PengajuanRkt::whereHas('kelompok', fn ($q) => $q->where('id_cdk', $idCdk))->count();

        return view('admin-cdk.rekap-wilayah', compact(
            'cdkInfo', 'jumlahKelompok', 'jumlahPendamping', 'jumlahAnggota',
            'luasLahan', 'jumlahNte', 'jumlahLaporan', 'jumlahRkps', 'jumlahRkt'
        ));
    }

    /* ─── 11. Superadmin CRUD Admin CDK (route: admin.admin-cdk) ─ */

    public function index(Request $request)
    {
        $selectedCdk = $request->query('id_cdk');

        $users = User::with('cdk')
            ->where('role', 'admin_cdk')
            ->orderBy('nama')
            ->get();

        $daftarCdk = Cdk::orderBy('nama')->get(['id', 'nama', 'kode']);

        // Rekap per CDK
        $rekapCdk = DB::table('cdk as c')
            ->leftJoin('kelompok as k', 'k.id_cdk', '=', 'c.id')
            ->leftJoinSub(
                DB::table('pendamping')->select('id_cdk', DB::raw('COUNT(*) as total'))->groupBy('id_cdk'),
                'p', 'p.id_cdk', '=', 'c.id'
            )
            ->leftJoinSub(
                DB::table('anggota_kelompok as a')
                    ->join('kelompok as ka', 'a.id_kelompok', '=', 'ka.id')
                    ->select('ka.id_cdk', DB::raw('COUNT(*) as total'))
                    ->groupBy('ka.id_cdk'),
                'a', 'a.id_cdk', '=', 'c.id'
            )
            ->leftJoinSub(
                DB::table('users')
                    ->where('role', 'admin_cdk')
                    ->select('id_cdk', DB::raw('GROUP_CONCAT(nama SEPARATOR ", ") as admin_names'), DB::raw('COUNT(*) as total_admin'))
                    ->groupBy('id_cdk'),
                'u', 'u.id_cdk', '=', 'c.id'
            )
            ->select(
                'c.id',
                'c.kode',
                'c.nama',
                DB::raw('COALESCE(u.total_admin, 0) as jumlah_admin'),
                DB::raw('COALESCE(u.admin_names, "-") as admin_names'),
                DB::raw('COUNT(DISTINCT k.id) as jumlah_kelompok'),
                DB::raw('COALESCE(p.total, 0) as jumlah_pendamping'),
                DB::raw('COALESCE(a.total, 0) as jumlah_anggota'),
                DB::raw('COALESCE(SUM(k.luas_lahan), 0) as total_luas_lahan')
            )
            ->groupBy('c.id', 'c.kode', 'c.nama', 'u.total_admin', 'u.admin_names', 'p.total', 'a.total')
            ->orderBy('c.nama')
            ->get();

        // Global stats
        $totalAdminCdk   = $users->count();
        $totalCdk        = $daftarCdk->count();
        $totalKelompok   = Kelompok::whereNotNull('id_cdk')->count();
        $totalAnggota    = AnggotaKelompok::whereHas('kelompok', fn ($q) => $q->whereNotNull('id_cdk'))->count();
        $totalPendamping = Pendamping::whereNotNull('id_cdk')->count();
        $totalLuasLahan  = Kelompok::whereNotNull('id_cdk')->sum('luas_lahan') ?? 0;

        // Data Anggota di bawah CDK
        $anggotaQuery = DB::table('anggota_kelompok as a')
            ->join('kelompok as k', 'a.id_kelompok', '=', 'k.id')
            ->leftJoin('cdk as c', 'k.id_cdk', '=', 'c.id')
            ->select(
                'a.id',
                'a.nama_anggota',
                'a.nik',
                'a.jenis_kelamin',
                'a.alamat',
                'a.luas_garapan',
                'a.komoditas_utama',
                'k.nama_kelompok',
                'k.desa',
                'k.kecamatan',
                'k.kabupaten',
                'k.id_cdk',
                'c.nama as nama_cdk'
            )
            ->orderBy('c.nama')
            ->orderBy('k.nama_kelompok')
            ->orderBy('a.nama_anggota');

        if (!empty($selectedCdk)) {
            $anggotaQuery->where('k.id_cdk', (int) $selectedCdk);
        }

        $daftarAnggota = $anggotaQuery->get();
        $daftarKelompok = Kelompok::whereNotNull('id_cdk')->orderBy('nama_kelompok')->get(['id', 'nama_kelompok', 'id_cdk']);

        return view('admin.admin-cdk.index', compact(
            'users',
            'daftarCdk',
            'rekapCdk',
            'totalAdminCdk',
            'totalCdk',
            'totalKelompok',
            'totalAnggota',
            'totalPendamping',
            'totalLuasLahan',
            'daftarAnggota',
            'daftarKelompok',
            'selectedCdk'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'     => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'id_cdk'   => ['required', 'integer', 'exists:cdk,id'],
        ]);

        User::create([
            'nama'     => $data['nama'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'role'     => 'admin_cdk',
            'id_cdk'   => $data['id_cdk'],
        ]);

        return redirect()->route('admin.admin-cdk')
            ->with('success', 'Admin CDK berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = User::where('id', $id)->where('role', 'admin_cdk')->first();
        abort_unless($user, 404);

        $daftarCdk = Cdk::orderBy('nama')->get(['id', 'nama']);

        return view('admin.admin-cdk.edit', compact('user', 'daftarCdk'));
    }

    public function update(Request $request, $id)
    {
        $user = User::where('id', $id)->where('role', 'admin_cdk')->first();
        abort_unless($user, 404);

        $data = $request->validate([
            'nama'     => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username,' . $id],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'id_cdk'   => ['required', 'integer', 'exists:cdk,id'],
        ]);

        $update = [
            'nama'     => $data['nama'],
            'username' => $data['username'],
            'id_cdk'   => $data['id_cdk'],
        ];

        if (!empty($data['password'])) {
            $update['password'] = Hash::make($data['password']);
        }

        $user->update($update);

        return redirect()->route('admin.admin-cdk')
            ->with('success', 'Data Admin CDK berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::where('id', $id)->where('role', 'admin_cdk')->first();
        abort_unless($user, 404);

        $user->delete();

        return redirect()->route('admin.admin-cdk')
            ->with('success', 'Admin CDK berhasil dihapus.');
    }
}
