<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Kelompok;

class PendampingController extends Controller
{
    public function dashboard()
    {
        $userId = Auth::id();

        // Ambil ID kelompok yang didampingi
        $kelompokList = DB::table('pendamping_kelompok')
            ->where('id_user', $userId)
            ->pluck('id_kelompok');

        $totalKelompok = $kelompokList->count();

        // Ambil data kelompok dengan eager loading cdk dan desa
        $kelompok = DB::table('kelompok')
            ->leftJoin('cdk', 'kelompok.id_cdk', '=', 'cdk.id')
            ->leftJoin('desa', 'kelompok.desa_id', '=', 'desa.id')
            ->whereIn('kelompok.id', $kelompokList)
            ->orderBy('kelompok.nama_kelompok')
            ->get([
                'kelompok.*',
                'cdk.nama as nama_cdk',
                'desa.nama as nama_desa',
            ]);

        // Hitung total anggota di semua kelompok yang didampingi
        $totalAnggota = DB::table('anggota_kelompok')
            ->whereIn('id_kelompok', $kelompokList)
            ->count();

        // Hitung pengajuan RKPS menunggu
        $rkpsMenunggu = DB::table('pengajuan_rkps')
            ->whereIn('id_kelompok', $kelompokList)
            ->where('status', 'Menunggu')
            ->count();

        // Hitung pengajuan RKT menunggu
        $rktMenunggu = DB::table('pengajuan_rkt')
            ->whereIn('id_kelompok', $kelompokList)
            ->where('status', 'Menunggu')
            ->count();

        $totalNotif = $rkpsMenunggu + $rktMenunggu;

        // Total anggota per kelompok (1 query bukan N query)
        $anggotaPerKelompok = DB::table('anggota_kelompok')
            ->whereIn('id_kelompok', $kelompokList)
            ->select('id_kelompok', DB::raw('COUNT(*) as total'))
            ->groupBy('id_kelompok')
            ->pluck('total', 'id_kelompok')
            ->toArray();

        // Data laporan terakhir
        $laporanTerakhir = DB::table('laporan_kegiatan')
            ->whereIn('laporan_kegiatan.id_kelompok', $kelompokList)
            ->leftJoin('kelompok', 'laporan_kegiatan.id_kelompok', '=', 'kelompok.id')
            ->orderByDesc('laporan_kegiatan.tanggal')
            ->limit(5)
            ->get([
                'laporan_kegiatan.tanggal',
                'laporan_kegiatan.kegiatan',
                'kelompok.nama_kelompok',
            ]);

        // Hitung NTE dan laporan
        $totalNte = DB::table('nte_kelompok')->whereIn('id_kelompok', $kelompokList)->count();
        $totalLaporan = DB::table('laporan_kegiatan')->whereIn('id_kelompok', $kelompokList)->count();

        // Data polygon untuk peta dashboard (hanya kelompok binaan user login)
        $kelompokPeta = DB::table('kelompok')
            ->whereIn('id', $kelompokList)
            ->orderBy('nama_kelompok')
            ->get(['id', 'nama_kelompok', 'desa', 'kecamatan', 'polygon_kelompok']);

        // Ambil nama wilayah CDK pendamping
        $namaCdk = null;
        if (!empty(Auth::user()->id_cdk)) {
            $c = DB::table('cdk')->where('id', Auth::user()->id_cdk)->first();
            if ($c && !empty($c->nama)) {
                $namaCdk = $c->nama;
            }
        }
        if (!$namaCdk) {
            $kc = DB::table('pendamping_kelompok')
                ->join('kelompok', 'pendamping_kelompok.id_kelompok', '=', 'kelompok.id')
                ->leftJoin('cdk', 'kelompok.id_cdk', '=', 'cdk.id')
                ->where('pendamping_kelompok.id_user', $userId)
                ->select('cdk.nama as cdk_nama', 'kelompok.kabupaten')
                ->first();
            if ($kc && !empty($kc->cdk_nama)) {
                $namaCdk = $kc->cdk_nama;
            } elseif ($kc && !empty($kc->kabupaten)) {
                $namaCdk = 'CDK Wilayah ' . $kc->kabupaten;
            }
        }
        $namaCdk = $namaCdk ?? 'CDK Wilayah Jawa Timur';

        return view('pendamping.pendamping_dashboard', compact(
            'kelompok', 'kelompokPeta',
            'totalKelompok',
            'totalAnggota',
            'rkpsMenunggu',
            'rktMenunggu',
            'totalNotif',
            'anggotaPerKelompok',
            'laporanTerakhir',
            'totalNte',
            'totalLaporan',
            'namaCdk'
        ));
    }

    private function kelompokIds()
    {
        return DB::table('pendamping_kelompok')
            ->where('id_user', Auth::id())
            ->pluck('id_kelompok');
    }

    /**
     * Authorization: pastikan kelompok $id adalah kelompok binaan user yang login.
     * Mengembalikan record kelompok (dengan nama cdk/desa) atau abort 403/404.
     */
    private function authorizeKelompokBinaan($id)
    {
        $kelompok = DB::table('kelompok')
            ->leftJoin('cdk', 'kelompok.id_cdk', '=', 'cdk.id')
            ->leftJoin('desa', 'kelompok.desa_id', '=', 'desa.id')
            ->where('kelompok.id', $id)
            ->first([
                'kelompok.*',
                'cdk.nama as nama_cdk',
                'desa.nama as nama_desa',
            ]);
        abort_unless($kelompok, 404);

        $isMyKelompok = DB::table('pendamping_kelompok')
            ->where('id_user', Auth::id())
            ->where('id_kelompok', $id)
            ->exists();
        abort_unless($isMyKelompok, 403);

        return $kelompok;
    }

    private function menuData(): array
    {
        $ids = $this->kelompokIds();
        $rkpsMenunggu = DB::table('pengajuan_rkps')->whereIn('id_kelompok', $ids)->where('status', 'Menunggu')->count();
        $rktMenunggu = DB::table('pengajuan_rkt')->whereIn('id_kelompok', $ids)->where('status', 'Menunggu')->count();

        return compact('rkpsMenunggu', 'rktMenunggu');
    }

    public function petaKelompok()
    {
        $kelompok = DB::table('kelompok')->whereIn('id', $this->kelompokIds())->orderBy('nama_kelompok')->get(['id', 'nama_kelompok', 'polygon_kelompok']);
        return view('pendamping.pendamping_peta_kelompok', compact('kelompok') + $this->menuData());
    }

    public function dataNteSemua()
    {
        $ids = $this->kelompokIds();
        $kelompokList = DB::table('kelompok')
            ->whereIn('id', $ids)
            ->orderBy('nama_kelompok')
            ->get(['id', 'nama_kelompok']);

        $nte = DB::table('nte_kelompok as n')->join('kelompok as k', 'n.id_kelompok', '=', 'k.id')->leftJoin('kups as kp', 'n.id_kups', '=', 'kp.id')->whereIn('n.id_kelompok', $ids)->orderBy('k.nama_kelompok')->orderByDesc('n.tahun')->get(['n.*', 'k.nama_kelompok', 'kp.nama_kups']);
        return view('pendamping.pendamping_data_nte_semua', compact('nte', 'kelompokList') + $this->menuData());
    }

    public function kelompokSaya()
    {
        $kelompok = DB::table('kelompok')
            ->leftJoin('cdk', 'kelompok.id_cdk', '=', 'cdk.id')
            ->leftJoin('desa', 'kelompok.desa_id', '=', 'desa.id')
            ->whereIn('kelompok.id', $this->kelompokIds())
            ->orderBy('kelompok.nama_kelompok')
            ->get([
                'kelompok.*',
                'cdk.nama as nama_cdk',
                'desa.nama as nama_desa',
            ]);

        $anggotaCounts = DB::table('anggota_kelompok')
            ->whereIn('id_kelompok', $this->kelompokIds())
            ->select('id_kelompok', DB::raw('COUNT(*) as total'))
            ->groupBy('id_kelompok')
            ->pluck('total', 'id_kelompok');

        return view('pendamping.pendamping_kelompok_saya', compact('kelompok', 'anggotaCounts') + $this->menuData());
    }

    public function anggota()
    {
        $kelompokList = DB::table('kelompok')
            ->whereIn('id', $this->kelompokIds())
            ->orderBy('nama_kelompok')
            ->get(['id', 'nama_kelompok']);

        $anggota = DB::table('anggota_kelompok as a')
            ->join('kelompok as k', 'a.id_kelompok', '=', 'k.id')
            ->whereIn('a.id_kelompok', $this->kelompokIds())
            ->orderBy('a.nama_anggota')
            ->get(['a.*', 'k.nama_kelompok']);

        return view('pendamping.pendamping_anggota', compact('anggota', 'kelompokList') + $this->menuData());
    }

    public function tambahAnggotaPerKelompok($id)
    {
        $kelompok = $this->authorizeKelompokBinaan($id);
        return view('pendamping.pendamping_tambah_anggota_kelompok', compact('kelompok') + $this->menuData());
    }

    public function simpanAnggotaPerKelompok(Request $request, $id)
    {
        $kelompok = $this->authorizeKelompokBinaan($id);
        $data = $request->validate([
            'nama_anggota' => ['required', 'string', 'max:100'],
            'nik' => ['nullable', 'string', 'max:20'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'alamat' => ['nullable', 'string'],
            'luas_garapan' => ['nullable', 'numeric'],
            'komoditas_utama' => ['nullable', 'string', 'max:100'],
        ]);
        DB::table('anggota_kelompok')->insert(array_merge($data, [
            'id_kelompok' => $kelompok->id,
            'polygon' => null,
        ]));
        return redirect()->route('pendamping.kelompok-saya.anggota', $kelompok->id)->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function hapusAnggotaPerKelompok($id, $idAnggota)
    {
        $this->authorizeKelompokBinaan($id);
        $deleted = DB::table('anggota_kelompok')->where('id', $idAnggota)->where('id_kelompok', $id)->delete();
        return redirect()->route('pendamping.kelompok-saya.anggota', $id)->with($deleted ? 'success' : 'error', $deleted ? 'Anggota berhasil dihapus.' : 'Anggota tidak ditemukan.');
    }

    public function detailKelompok($id)
    {
        $kelompok = $this->authorizeKelompokBinaan($id);

        // Ambil RKPS terakhir
        $rkpsTerakhir = DB::table('pengajuan_rkps')
            ->where('id_kelompok', $id)
            ->orderByDesc('tanggal_pengajuan')
            ->first();

        // Ambil RKT terakhir
        $rktTerakhir = DB::table('pengajuan_rkt')
            ->where('id_kelompok', $id)
            ->orderByDesc('tanggal_pengajuan')
            ->first();

        // Hitung total anggota
        $totalAnggota = DB::table('anggota_kelompok')
            ->where('id_kelompok', $id)
            ->count();

        // Data anggota ringkas (maks 5, untuk pratinjau di detail)
        $anggota = DB::table('anggota_kelompok')
            ->where('id_kelompok', $id)
            ->orderBy('nama_anggota')
            ->limit(5)
            ->get(['id', 'nama_anggota', 'nik', 'komoditas_utama']);

        $nteCount = DB::table('nte_kelompok')->where('id_kelompok', $id)->count();
        $laporanCount = DB::table('laporan_kegiatan')->where('id_kelompok', $id)->count();

        return view('pendamping.pendamping_detail_kelompok', compact(
            'kelompok', 'rkpsTerakhir', 'rktTerakhir', 'totalAnggota', 'anggota',
            'nteCount', 'laporanCount'
        ) + $this->menuData());
    }

    public function anggotaPerKelompok($id)
    {
        $kelompok = $this->authorizeKelompokBinaan($id);

        $anggota = DB::table('anggota_kelompok')
            ->where('id_kelompok', $id)
            ->orderBy('nama_anggota')
            ->get();

        return view('pendamping.pendamping_anggota_kelompok', compact('kelompok', 'anggota') + $this->menuData());
    }

    public function pengajuanRkps()
    {
        $ids = $this->kelompokIds();
        $kelompokList = DB::table('kelompok')
            ->whereIn('id', $ids)
            ->orderBy('nama_kelompok')
            ->get(['id', 'nama_kelompok']);

        $pengajuan = DB::table('pengajuan_rkps as p')
            ->join('kelompok as k', 'p.id_kelompok', '=', 'k.id')
            ->whereIn('p.id_kelompok', $ids)
            ->orderByDesc('p.tanggal_pengajuan')
            ->get(['p.*', 'k.nama_kelompok']);

        return view('pendamping.pendamping_pengajuan_rkps', compact('pengajuan', 'kelompokList') + $this->menuData());
    }

    public function pengajuanRkt()
    {
        $ids = $this->kelompokIds();
        $kelompokList = DB::table('kelompok')
            ->whereIn('id', $ids)
            ->orderBy('nama_kelompok')
            ->get(['id', 'nama_kelompok']);

        $pengajuan = DB::table('pengajuan_rkt as p')
            ->join('kelompok as k', 'p.id_kelompok', '=', 'k.id')
            ->whereIn('p.id_kelompok', $ids)
            ->orderByDesc('p.tanggal_pengajuan')
            ->get(['p.*', 'k.nama_kelompok']);

        return view('pendamping.pendamping_pengajuan_rkt', compact('pengajuan', 'kelompokList') + $this->menuData());
    }

    /* ─── NTE per kelompok ───────────────────────────────────── */

    private function getPendampingUser()
    {
        return DB::table('pendamping as p')
            ->join('users as u', 'u.username', '=', 'p.username')
            ->where('u.id', Auth::id())
            ->select('p.id_pendamping', 'p.username')
            ->first();
    }

    public function dataNte($id)
    {
        $kelompok = $this->authorizeKelompokBinaan($id);
        $nte = DB::table('nte_kelompok as n')
            ->leftJoin('kups as kp', 'n.id_kups', '=', 'kp.id')
            ->where('n.id_kelompok', $id)
            ->orderByDesc('n.tahun')
            ->orderBy('n.nama_produk')
            ->get(['n.*', 'kp.nama_kups']);

        return view('pendamping.pendamping_data_nte', compact('kelompok', 'nte') + $this->menuData());
    }

    public function tambahNte($id)
    {
        $kelompok = $this->authorizeKelompokBinaan($id);
        $kups = DB::table('kups')->where('id_kelompok', $id)->orderBy('nama_kups')->get();
        return view('pendamping.pendamping_input_nte', compact('kelompok', 'kups') + $this->menuData());
    }

    public function simpanNte(Request $request, $id)
    {
        $kelompok = $this->authorizeKelompokBinaan($id);
        $data = $request->validate([
            'id_kups' => ['nullable', 'integer'],
            'nama_produk' => ['required', 'string', 'max:150'],
            'luas_areal' => ['nullable', 'string', 'max:100'],
            'pola_pengolahan' => ['nullable', 'string', 'max:100'],
            'pola_pengelolaan' => ['nullable', 'string', 'max:100'],
            'potensi_jasa_lingkungan' => ['nullable', 'string', 'max:150'],
            'sumber_modal' => ['nullable', 'string', 'max:100'],
            'area_pemasaran' => ['nullable', 'string', 'max:100'],
            'satuan' => ['nullable', 'string', 'max:50'],
            'tahun' => ['nullable', 'integer', 'between:1900,2200'],
        ]);
        if (!empty($data['id_kups']) && !DB::table('kups')->where('id', $data['id_kups'])->where('id_kelompok', $id)->exists()) {
            return back()->withInput()->withErrors(['id_kups' => 'KUPS bukan milik kelompok ini.']);
        }
        $pendamping = $this->getPendampingUser();
        abort_unless($pendamping, 403);
        DB::table('nte_kelompok')->insert(array_merge($data, [
            'id_kelompok' => $id,
            'id_pendamping' => $pendamping->id_pendamping,
            'created_at' => now(),
        ]));
        return redirect()->route('pendamping.kelompok-saya.data-nte', $id)->with('success', 'Data NTE berhasil disimpan.');
    }

    public function inputTransaksi($id, $idNte)
    {
        $kelompok = $this->authorizeKelompokBinaan($id);
        $nte = DB::table('nte_kelompok')->where('id', $idNte)->where('id_kelompok', $id)->first();
        abort_unless($nte, 404);
        return view('pendamping.pendamping_input_transaksi', compact('kelompok', 'nte') + $this->menuData());
    }

    public function simpanTransaksi(Request $request, $id, $idNte)
    {
        $this->authorizeKelompokBinaan($id);
        $nte = DB::table('nte_kelompok')->where('id', $idNte)->where('id_kelompok', $id)->first();
        abort_unless($nte, 404);
        $data = $request->validate([
            'bulan' => ['nullable', 'integer', 'between:1,12'],
            'volume' => ['required', 'numeric', 'min:0'],
            'satuan' => ['nullable', 'string', 'max:50'],
            'harga_satuan' => ['required', 'numeric', 'min:0'],
        ]);
        $data['id_nte'] = $idNte;
        $data['total_nekon'] = (float) $data['volume'] * (float) $data['harga_satuan'];
        DB::table('nte_transaksi')->insert($data);
        return redirect()->route('pendamping.kelompok-saya.data-nte', $id)->with('success', 'Transaksi NTE berhasil disimpan.');
    }

    public function laporanPerKelompok(Request $request, $id)
    {
        $kelompok = $this->authorizeKelompokBinaan($id);
        $query = DB::table('laporan_kegiatan')->where('id_kelompok', $id)->orderByDesc('tanggal');
        if ($request->filled('tanggal_awal')) $query->where('tanggal', '>=', $request->query('tanggal_awal'));
        if ($request->filled('tanggal_akhir')) $query->where('tanggal', '<=', $request->query('tanggal_akhir'));
        $laporan = $query->get();
        return view('pendamping.pendamping_laporan_kelompok', compact('kelompok', 'laporan') + $this->menuData());
    }

    public function tambahLaporanPerKelompok($id)
    {
        $kelompok = $this->authorizeKelompokBinaan($id);
        return view('pendamping.pendamping_tambah_laporan_kelompok', compact('kelompok') + $this->menuData());
    }

    public function simpanLaporanPerKelompok(Request $request, $id)
    {
        $kelompok = $this->authorizeKelompokBinaan($id);
        $data = $request->validate([
            'tanggal' => ['required', 'date'],
            'kegiatan' => ['required', 'string', 'max:200'],
            'deskripsi' => ['nullable', 'string'],
            'foto' => ['nullable', 'file', 'image', 'max:5120'],
        ]);
        $foto = null;
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $foto = time() . '_' . Str::random(12) . '.' . strtolower($file->getClientOriginalExtension());
            $file->storeAs('laporan', $foto, 'public');
        }
        DB::table('laporan_kegiatan')->insert([
            'id_kelompok' => $id, 'id_user' => Auth::id(), 'role_pembuat' => 'pendamping',
            'tanggal' => $data['tanggal'], 'kegiatan' => $data['kegiatan'],
            'deskripsi' => $data['deskripsi'] ?? null, 'foto' => $foto, 'created_at' => now(),
        ]);
        return redirect()->route('pendamping.kelompok-saya.laporan', $id)->with('success', 'Laporan kegiatan berhasil disimpan.');
    }

    /* ─── Laporan Kegiatan ──────────────────────────────────── */

    public function laporanKegiatan(Request $request)
    {
        $kelompokFilter = $request->query('kelompok', '');
        $tanggalAwal = $request->query('tanggal_awal', '');
        $tanggalAkhir = $request->query('tanggal_akhir', '');
        $kelompokIds = $this->kelompokIds();

        $query = DB::table('laporan_kegiatan as lk')
            ->join('kelompok as k', 'lk.id_kelompok', '=', 'k.id')
            ->whereIn('lk.id_kelompok', $kelompokIds)
            ->orderByDesc('lk.tanggal')
            ->select('lk.*', 'k.nama_kelompok');

        if ($kelompokFilter !== '' && $kelompokIds->contains((int) $kelompokFilter)) {
            $query->where('lk.id_kelompok', (int) $kelompokFilter);
        } elseif ($kelompokFilter !== '') {
            $query->whereRaw('1 = 0');
        }
        if ($tanggalAwal !== '') {
            $query->where('lk.tanggal', '>=', $tanggalAwal);
        }
        if ($tanggalAkhir !== '') {
            $query->where('lk.tanggal', '<=', $tanggalAkhir);
        }

        $laporan = $query->get();
        $daftarKelompok = DB::table('kelompok')
            ->whereIn('id', $this->kelompokIds())
            ->orderBy('nama_kelompok')
            ->get(['id', 'nama_kelompok']);

        return view('pendamping.pendamping_laporan_kegiatan', compact(
            'laporan', 'daftarKelompok', 'kelompokFilter', 'tanggalAwal', 'tanggalAkhir'
        ) + $this->menuData());
    }

    public function tambahLaporan()
    {
        $daftarKelompok = DB::table('kelompok')
            ->whereIn('id', $this->kelompokIds())
            ->orderBy('nama_kelompok')
            ->get(['id', 'nama_kelompok']);

        return view('pendamping.tambah-laporan', compact('daftarKelompok') + $this->menuData());
    }

    public function simpanLaporan(Request $request)
    {
        $data = $request->validate([
            'id_kelompok' => ['required', 'integer', 'exists:kelompok,id'],
            'tanggal'     => ['required', 'date'],
            'kegiatan'    => ['required', 'string', 'max:200'],
            'deskripsi'   => ['nullable', 'string'],
            'foto'        => ['nullable', 'file', 'image', 'max:5120'],
        ]);

        // Pastikan kelompok ini adalah kelompok dampingan
        if (!$this->kelompokIds()->contains($data['id_kelompok'])) {
            return redirect()->route('pendamping.laporan-kegiatan')
                ->with('error', 'Anda tidak memiliki akses ke kelompok ini.');
        }

        $fotoFilename = null;
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $fotoFilename = time() . '_' . Str::random(12) . '.' . strtolower($file->getClientOriginalExtension());
            $file->storeAs('laporan', $fotoFilename, 'public');
        }

        DB::table('laporan_kegiatan')->insert([
            'id_kelompok'   => $data['id_kelompok'],
            'id_user'       => Auth::id(),
            'role_pembuat'  => 'pendamping',
            'tanggal'       => $data['tanggal'],
            'kegiatan'      => $data['kegiatan'],
            'deskripsi'     => $data['deskripsi'] ?? null,
            'foto'          => $fotoFilename,
            'created_at'    => now(),
        ]);

        return redirect()->route('pendamping.laporan-kegiatan')
            ->with('success', 'Laporan kegiatan berhasil disimpan.');
    }

    public function detailLaporan($id)
    {
        $laporan = DB::table('laporan_kegiatan as lk')
            ->join('kelompok as k', 'lk.id_kelompok', '=', 'k.id')
            ->where('lk.id', $id)
            ->whereIn('lk.id_kelompok', $this->kelompokIds())
            ->first(['lk.*', 'k.nama_kelompok']);

        if (!$laporan) {
            return redirect()->route('pendamping.laporan-kegiatan')
                ->with('error', 'Laporan tidak ditemukan.');
        }

        return view('pendamping.detail-laporan', compact('laporan') + $this->menuData());
    }

    public function editLaporan($id)
    {
        $laporan = DB::table('laporan_kegiatan')
            ->where('id', $id)
            ->whereIn('id_kelompok', $this->kelompokIds())
            ->first();

        if (!$laporan) {
            return redirect()->route('pendamping.laporan-kegiatan')
                ->with('error', 'Laporan tidak ditemukan.');
        }

        $daftarKelompok = DB::table('kelompok')
            ->whereIn('id', $this->kelompokIds())
            ->orderBy('nama_kelompok')
            ->get(['id', 'nama_kelompok']);

        return view('pendamping.edit-laporan', compact('laporan', 'daftarKelompok') + $this->menuData());
    }

    public function updateLaporan(Request $request, $id)
    {
        $laporan = DB::table('laporan_kegiatan')
            ->where('id', $id)
            ->whereIn('id_kelompok', $this->kelompokIds())
            ->first();

        if (!$laporan) {
            return redirect()->route('pendamping.laporan-kegiatan')
                ->with('error', 'Laporan tidak ditemukan.');
        }

        $data = $request->validate([
            'id_kelompok' => ['required', 'integer', 'exists:kelompok,id'],
            'tanggal'     => ['required', 'date'],
            'kegiatan'    => ['required', 'string', 'max:200'],
            'deskripsi'   => ['nullable', 'string'],
            'foto'        => ['nullable', 'file', 'image', 'max:5120'],
        ]);

        abort_unless($this->kelompokIds()->contains((int) $data['id_kelompok']), 403);
        $fotoFilename = $laporan->foto;
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $fotoFilename = time() . '_' . Str::random(12) . '.' . strtolower($file->getClientOriginalExtension());
            $file->storeAs('laporan', $fotoFilename, 'public');
            if ($laporan->foto) {
                Storage::disk('public')->delete('laporan/' . $laporan->foto);
            }
        }

        DB::table('laporan_kegiatan')->where('id', $id)->update([
            'id_kelompok' => $data['id_kelompok'],
            'tanggal'     => $data['tanggal'],
            'kegiatan'    => $data['kegiatan'],
            'deskripsi'   => $data['deskripsi'] ?? null,
            'foto'        => $fotoFilename,
        ]);

        return redirect()->route('pendamping.laporan-kegiatan')
            ->with('success', 'Laporan kegiatan berhasil diperbarui.');
    }

    public function hapusLaporan($id)
    {
        $laporan = DB::table('laporan_kegiatan')
            ->where('id', $id)
            ->whereIn('id_kelompok', $this->kelompokIds())
            ->first();

        if (!$laporan) {
            return redirect()->route('pendamping.laporan-kegiatan')
                ->with('error', 'Laporan tidak ditemukan.');
        }

        // Hapus foto dari storage
        if ($laporan->foto) {
            Storage::disk('public')->delete('laporan/' . $laporan->foto);
        }

        DB::table('laporan_kegiatan')->where('id', $id)->delete();

        return redirect()->route('pendamping.laporan-kegiatan')
            ->with('success', 'Laporan kegiatan berhasil dihapus.');
    }

    /**
     * Masuk ke portal kelompok (akses menu & data kelompok binaan)
     */
    public function masukKelompok($id)
    {
        $kelompok = $this->authorizeKelompokBinaan($id);
        session(['akses_kelompok_id' => $kelompok->id]);

        return redirect()->route('kelompok.dashboard')
            ->with('success', 'Anda sekarang mengakses kelompok: <strong>' . e($kelompok->nama_kelompok) . '</strong>');
    }

    /**
     * Keluar dari portal kelompok dan kembali ke dashboard/menu pendamping
     */
    public function keluarKelompok()
    {
        session()->forget('akses_kelompok_id');

        return redirect()->route('pendamping.kelompok-saya')
            ->with('info', 'Kembali ke portal pendamping.');
    }
}

