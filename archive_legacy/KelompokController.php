<?php

namespace App\Http\Controllers;

use App\Models\Kelompok;
use App\Models\AnggotaKelompok;
use App\Models\Kups;
use App\Models\Kegiatan;
use App\Models\DokumentasiKegiatan;
use App\Models\PotensiKelompok;
use App\Models\PermasalahanKelompok;
use App\Models\PengajuanRkps;
use App\Models\PengajuanRkt;
use App\Models\Rkps;
use App\Models\Rkt;
use App\Models\PetakPerhutani;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KelompokController extends Controller
{
    /* ─── helpers ────────────────────────────────────────────── */

    private function getKelompok()
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        if ($user->role === 'kelompok') {
            return Kelompok::with(['cdk', 'desaWilayah'])->find($user->id_kelompok);
        }

        if ($user->role === 'pendamping') {
            $kelompokId = request('id_kelompok') ?? session('akses_kelompok_id');
            if (!$kelompokId) {
                $kelompokId = DB::table('pendamping_kelompok')
                    ->where('id_user', $user->id)
                    ->value('id_kelompok');
            }

            if ($kelompokId) {
                $isAllowed = DB::table('pendamping_kelompok')
                    ->where('id_user', $user->id)
                    ->where('id_kelompok', $kelompokId)
                    ->exists();

                if ($isAllowed) {
                    session(['akses_kelompok_id' => $kelompokId]);
                    return Kelompok::with(['cdk', 'desaWilayah'])->find($kelompokId);
                }
            }
        }

        if (in_array($user->role, ['admin', 'admin_cdk']) && (request('id_kelompok') || session('akses_kelompok_id'))) {
            $kelompokId = request('id_kelompok') ?? session('akses_kelompok_id');
            return Kelompok::with(['cdk', 'desaWilayah'])->find($kelompokId);
        }

        return null;
    }

    private function authorizeKelompok()
    {
        $kelompok = $this->getKelompok();
        if (!$kelompok) {
            abort(403, 'Anda tidak memiliki akses ke kelompok ini atau belum memilih kelompok binaan.');
        }
        return $kelompok;
    }

    private function checkOwnership($kelompokId, $itemId, $table, $pk = 'id')
    {
        return DB::table($table)->where($pk, $itemId)->where('id_kelompok', $kelompokId)->first();
    }

    private function dataNavigasi(): array
    {
        $kelompok = $this->getKelompok();
        if (!$kelompok) {
            return ['totalAnggota' => 0, 'rkpsMenunggu' => 0, 'rktMenunggu' => 0, 'totalNotif' => 0];
        }
        $id = $kelompok->id;
        $totalAnggota = AnggotaKelompok::where('id_kelompok', $id)->count();
        $rkpsMenunggu = PengajuanRkps::where('id_kelompok', $id)->where('status', 'Menunggu')->count();
        $rktMenunggu = PengajuanRkt::where('id_kelompok', $id)->where('status', 'Menunggu')->count();

        return [
            'totalAnggota' => $totalAnggota,
            'rkpsMenunggu' => $rkpsMenunggu,
            'rktMenunggu' => $rktMenunggu,
            'totalNotif' => $rkpsMenunggu + $rktMenunggu,
        ];
    }

    /**
     * =========================================================
     * QUERY PETAK PERHUTANI (dipakai bersama oleh halaman biasa
     * ATAUPUN endpoint AJAX) — logika fallback persis yang lama:
     * coba filter per desa dulu, lalu kecamatan, lalu kabupaten,
     * lalu kalau tetap kosong ambil default kabupaten Ngawi,
     * dan kalau masih kosong juga, ambil apa saja (limit 60).
     * =========================================================
     */
    private function queryPetakPerhutani($kelompok)
    {
        if (!$kelompok) {
            return collect();
        }

        $kolomSelect = [
            'id', 'kode_toponimi', 'petak', 'anak_petak', 'rph', 'bkph', 'kph',
            'desa', 'kecamatan', 'kabupaten', 'fungsi', 'klas_hutan', 'luas_baku',
            'jenis_tanaman', 'tahun_tanam', 'atribut',
            DB::raw('ST_AsGeoJSON(geometry) as geojson'),
        ];

        // 1. Prioritas Utama: Gunakan Bounding Box Polygon Kelompok (MBRIntersects) persis sistem lama
        if (!empty($kelompok->polygon_kelompok)) {
            try {
                $json = is_array($kelompok->polygon_kelompok)
                    ? $kelompok->polygon_kelompok
                    : json_decode($kelompok->polygon_kelompok, true);

                $minX = 999; $minY = 999; $maxX = -999; $maxY = -999;
                $scan = function ($obj) use (&$scan, &$minX, &$minY, &$maxX, &$maxY) {
                    if (is_array($obj)) {
                        if (isset($obj[0], $obj[1]) && is_numeric($obj[0]) && is_numeric($obj[1])) {
                            $x = (float)$obj[0];
                            $y = (float)$obj[1];
                            if ($x < $minX) $minX = $x;
                            if ($x > $maxX) $maxX = $x;
                            if ($y < $minY) $minY = $y;
                            if ($y > $maxY) $maxY = $y;
                            return;
                        }
                        foreach ($obj as $v) {
                            $scan($v);
                        }
                    }
                };
                $scan($json);

                if ($minX < 999) {
                    $buffer = 0.002;
                    $minX -= $buffer;
                    $minY -= $buffer;
                    $maxX += $buffer;
                    $maxY += $buffer;

                    $sql = "
                        SELECT id, kode_toponimi, petak, anak_petak, rph, bkph, kph,
                               desa, kecamatan, kabupaten, fungsi, klas_hutan, luas_baku,
                               jenis_tanaman, tahun_tanam, atribut,
                               ST_AsGeoJSON(geometry) AS geojson
                        FROM petak_perhutani
                        WHERE MBRIntersects(
                            geometry,
                            ST_GeomFromText('POLYGON(($minX $minY, $maxX $minY, $maxX $maxY, $minX $maxY, $minX $minY))')
                        )
                        ORDER BY kode_toponimi
                    ";

                    $petakSpatial = DB::select($sql);
                    if (!empty($petakSpatial)) {
                        return collect($petakSpatial);
                    }
                }
            } catch (\Throwable $e) {
                // Lanjut ke fallback jika spasial query gagal
            }
        }

        // 2. Fallback: Filter per desa, kecamatan, kabupaten
        $petakPerhutani = collect();

        try {
            $petakQuery = DB::table('petak_perhutani');

            if (!empty($kelompok->desa)) {
                $petakQuery->where('desa', 'LIKE', '%' . trim($kelompok->desa) . '%');
            } elseif (!empty($kelompok->kecamatan)) {
                $petakQuery->where('kecamatan', 'LIKE', '%' . trim($kelompok->kecamatan) . '%');
            } elseif (!empty($kelompok->kabupaten)) {
                $petakQuery->where('kabupaten', 'LIKE', '%' . trim($kelompok->kabupaten) . '%');
            }

            $petakPerhutani = $petakQuery->select($kolomSelect)->limit(100)->get();

            if ($petakPerhutani->isEmpty()) {
                $kab = !empty($kelompok->kabupaten) ? trim($kelompok->kabupaten) : 'Ngawi';
                $petakPerhutani = DB::table('petak_perhutani')
                    ->where('kabupaten', 'LIKE', '%' . $kab . '%')
                    ->select($kolomSelect)
                    ->limit(80)
                    ->get();
            }

            if ($petakPerhutani->isEmpty()) {
                $petakPerhutani = DB::table('petak_perhutani')
                    ->select($kolomSelect)
                    ->limit(60)
                    ->get();
            }
        } catch (\Throwable $e) {
            $petakPerhutani = collect();
        }

        return $petakPerhutani;
    }


    private function getSpasialKelompok($kelompok): array
    {
        if (!$kelompok) {
            return [
                'petakPerhutani' => collect(),
                'kegiatanList'   => collect(),
            ];
        }

        $petakPerhutani = $this->queryPetakPerhutani($kelompok);
        $kegiatanList = Kegiatan::with('dokumentasi')->where('id_kelompok', $kelompok->id)->get();

        return [
            'petakPerhutani' => $petakPerhutani,
            'kegiatanList'   => $kegiatanList,
        ];
    }

    /* ─── 1. Dashboard ──────────────────────────────────────── */

    public function dashboard()
    {
        $kelompok = $this->getKelompok();
        if (!$kelompok) {
            return view('kelompok.dashboard', ['kelompok' => null, 'totalAnggota' => 0, 'rkpsMenunggu' => 0, 'rktMenunggu' => 0, 'totalNotif' => 0, 'kegiatanList' => collect()]);
        }
        $id = $kelompok->id;

        $totalAnggota = AnggotaKelompok::where('id_kelompok', $id)->count();
        $rkpsMenunggu = PengajuanRkps::where('id_kelompok', $id)->where('status', 'Menunggu')->count();
        $rktMenunggu = PengajuanRkt::where('id_kelompok', $id)->where('status', 'Menunggu')->count();
        $rkpsTerakhir = PengajuanRkps::where('id_kelompok', $id)->orderBy('id', 'desc')->first();
        $rktTerakhir = PengajuanRkt::where('id_kelompok', $id)->orderBy('id', 'desc')->first();

        /*
         * CATATAN: Petak Perhutani TIDAK lagi diambil di sini.
         * Data petak sekarang dimuat lewat AJAX (lihat method
         * getPetakKelompokAjax() di bawah) supaya dashboard tetap
         * ringan saat pertama dibuka, dan petak baru di-query saat
         * user mencentang layer "🌳 Petak Perhutani" di peta —
         * persis perilaku file PHP lama.
         */
        $kegiatanList = Kegiatan::with('dokumentasi')->where('id_kelompok', $id)->get();

        return view('kelompok.dashboard', compact(
            'kelompok', 'totalAnggota', 'rkpsMenunggu', 'rktMenunggu', 'rkpsTerakhir', 'rktTerakhir',
            'kegiatanList'
        ) + $this->dataNavigasi());
    }

    /**
     * =========================================================
     * ENDPOINT AJAX — PETAK PERHUTANI (untuk peta wilayah kelompok)
     * =========================================================
     *
     * Dipanggil dari dashboard.blade.php lewat fetch() saat user
     * mencentang layer "🌳 Petak Perhutani":
     *
     *      fetch("{{ route('kelompok.get-petak') }}")
     *
     * Kelompok ditentukan otomatis dari akun yang sedang login
     * (Auth::user()->id_kelompok), bukan dari parameter URL —
     * supaya satu kelompok tidak bisa mengintip data kelompok lain.
     *
     * Response: array of object, dengan "geometry" berisi STRING
     * GeoJSON (hasil ST_AsGeoJSON), siap langsung di-JSON.parse()
     * di sisi JS, sama seperti perilaku file PHP lama.
     * =========================================================
     */
    public function getPetakKelompokAjax(Request $request)
    {
        $kelompok = $this->authorizeKelompok();

        $petakPerhutani = $this->queryPetakPerhutani($kelompok);

        $hasil = $petakPerhutani->map(function ($p) {
            return [
                'id'             => $p->id,
                'kode_toponimi'  => $p->kode_toponimi,
                'petak'          => $p->petak,
                'anak_petak'     => $p->anak_petak,
                'rph'            => $p->rph,
                'bkph'           => $p->bkph,
                'kph'            => $p->kph,
                'fungsi'         => $p->fungsi,
                'klas_hutan'     => $p->klas_hutan,
                'luas_baku'      => $p->luas_baku,
                'jenis_tanaman'  => $p->jenis_tanaman,
                'tahun_tanam'    => $p->tahun_tanam,
                // geojson (dari ST_AsGeoJSON) dikirim sebagai "geometry"
                // supaya cocok dengan JSON.parse(p.geometry) di JS peta.
                'geometry'       => $p->geojson,
            ];
        });

        return response()->json($hasil->values());
    }

    /* ─── 2. Data Kelompok ──────────────────────────────────── */

    public function dataKelompok()
    {
        $kelompok = $this->authorizeKelompok();
        return view('kelompok.kelompok_data_kelompok', compact('kelompok') + $this->dataNavigasi());
    }

    /* ─── 3-4. Data Anggota CRUD ────────────────────────────── */

    public function dataAnggota()
    {
        $kelompok = $this->authorizeKelompok();
        $anggota = AnggotaKelompok::where('id_kelompok', $kelompok->id)->orderBy('nama_anggota')->get();
        return view('kelompok.kelompok_anggota', compact('anggota', 'kelompok') + $this->dataNavigasi());
    }

    public function tambahAnggota()
    {
        $kelompok = $this->authorizeKelompok();
        return view('kelompok.kelompok_tambah_anggota', compact('kelompok') + $this->dataNavigasi());
    }

    public function simpanAnggota(Request $request)
    {
        $kelompok = $this->authorizeKelompok();

        $data = $request->validate([
            'nama_anggota'    => ['required', 'string', 'max:100'],
            'nik'             => ['nullable', 'string', 'max:20'],
            'jenis_kelamin'   => ['nullable', 'in:L,P'],
            'alamat'          => ['nullable', 'string'],
            'luas_garapan'    => ['nullable', 'numeric'],
            'komoditas_utama' => ['nullable', 'string', 'max:100'],
        ]);

        AnggotaKelompok::create(array_merge($data, [
            'id_kelompok' => $kelompok->id,
            'polygon'     => null,
        ]));

        return redirect()->route('kelompok.anggota')->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function editAnggota($id)
    {
        $kelompok = $this->authorizeKelompok();
        $anggota = $this->checkOwnership($kelompok->id, $id, 'anggota_kelompok');
        if (!$anggota) {
            return redirect()->route('kelompok.anggota')->with('error', 'Anggota tidak ditemukan.');
        }
        return view('kelompok.kelompok_edit_anggota', compact('anggota', 'kelompok') + $this->dataNavigasi());
    }

    public function updateAnggota(Request $request, $id)
    {
        $kelompok = $this->authorizeKelompok();
        $anggota = $this->checkOwnership($kelompok->id, $id, 'anggota_kelompok');
        if (!$anggota) {
            return redirect()->route('kelompok.anggota')->with('error', 'Anggota tidak ditemukan.');
        }

        $data = $request->validate([
            'nama_anggota'    => ['required', 'string', 'max:100'],
            'nik'             => ['nullable', 'string', 'max:20'],
            'jenis_kelamin'   => ['nullable', 'in:L,P'],
            'alamat'          => ['nullable', 'string'],
            'luas_garapan'    => ['nullable', 'numeric'],
            'komoditas_utama' => ['nullable', 'string', 'max:100'],
        ]);

        AnggotaKelompok::where('id', $id)->update($data);

        return redirect()->route('kelompok.anggota')->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function hapusAnggota($id)
    {
        $kelompok = $this->authorizeKelompok();
        $anggota = $this->checkOwnership($kelompok->id, $id, 'anggota_kelompok');
        if (!$anggota) {
            return redirect()->route('kelompok.anggota')->with('error', 'Anggota tidak ditemukan.');
        }
        AnggotaKelompok::where('id', $id)->delete();

        return redirect()->route('kelompok.anggota')->with('success', 'Anggota berhasil dihapus.');
    }

    /* ─── 5. Pengajuan RKPS ─────────────────────────────────── */

    public function pengajuanRkps()
    {
        $kelompok = $this->authorizeKelompok();
        $id = $kelompok->id;

        $rkpsList = Rkps::where('id_kelompok', $id)->orderByDesc('tahun_mulai')->get();
        $pengajuan = PengajuanRkps::where('id_kelompok', $id)->orderByDesc('tanggal_pengajuan')->get();
        $adaMenunggu = $pengajuan->contains('status', 'Menunggu');

        return view('kelompok.kelompok_pengajuan_rkps', compact('kelompok', 'rkpsList', 'pengajuan', 'adaMenunggu') + $this->dataNavigasi());
    }

    public function ajukanRkps()
    {
        $kelompok = $this->authorizeKelompok();
        return view('kelompok.kelompok_ajukan_rkps', compact('kelompok') + $this->dataNavigasi());
    }

    public function simpanAjukanRkps(Request $request)
    {
        $kelompok = $this->authorizeKelompok();

        $data = $request->validate([
            'tahun_mulai'   => ['required', 'integer', 'between:1900,2200'],
            'tahun_selesai' => ['required', 'integer', 'gte:tahun_mulai', 'between:1900,2200'],
            'file_rkps'     => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $file = $request->file('file_rkps');
        $filename = time() . '_' . Str::random(12) . '.pdf';
        $file->storeAs('rkps', $filename, 'public');

        PengajuanRkps::create([
            'id_kelompok'   => $kelompok->id,
            'tahun_mulai'   => $data['tahun_mulai'],
            'tahun_selesai' => $data['tahun_selesai'],
            'file_rkps'     => $filename,
            'status'        => 'Menunggu',
        ]);

        return redirect()->route('kelompok.pengajuan-rkps')
            ->with('success', 'Pengajuan RKPS berhasil dikirim dan menunggu verifikasi admin.');
    }

    /* ─── 7. Pengajuan RKT ──────────────────────────────────── */

    public function pengajuanRkt()
    {
        $kelompok = $this->authorizeKelompok();
        $id = $kelompok->id;

        $rktList = Rkt::where('id_kelompok', $id)->orderByDesc('tahun')->get();
        $pengajuan = PengajuanRkt::where('id_kelompok', $id)->orderByDesc('tanggal_pengajuan')->get();
        $adaMenunggu = $pengajuan->contains('status', 'Menunggu');

        return view('kelompok.kelompok_pengajuan_rkt', compact('kelompok', 'rktList', 'pengajuan', 'adaMenunggu') + $this->dataNavigasi());
    }

    public function ajukanRkt()
    {
        $kelompok = $this->authorizeKelompok();
        $daftarRkps = Rkps::where('id_kelompok', $kelompok->id)->orderByDesc('tahun_mulai')->get();
        return view('kelompok.kelompok_ajukan_rkt', compact('kelompok', 'daftarRkps') + $this->dataNavigasi());
    }

    public function simpanAjukanRkt(Request $request)
    {
        $kelompok = $this->authorizeKelompok();

        $data = $request->validate([
            'tahun'    => ['required', 'integer', 'between:1900,2200'],
            'file_rkt' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $file = $request->file('file_rkt');
        $filename = time() . '_' . Str::random(12) . '.pdf';
        $file->storeAs('rkt', $filename, 'public');

        PengajuanRkt::create([
            'id_kelompok' => $kelompok->id,
            'tahun'       => $data['tahun'],
            'file_rkt'    => $filename,
            'status'      => 'Menunggu',
        ]);

        return redirect()->route('kelompok.pengajuan-rkt')
            ->with('success', 'Pengajuan RKT berhasil dikirim dan menunggu verifikasi admin.');
    }

    /* ─── 9-10. Detail Pengajuan ────────────────────────────── */

    public function detailPengajuanRkps($id)
    {
        $kelompok = $this->authorizeKelompok();
        $pengajuan = PengajuanRkps::where('id', $id)->where('id_kelompok', $kelompok->id)->first();
        if (!$pengajuan) {
            return redirect()->route('kelompok.pengajuan-rkps')->with('error', 'Pengajuan tidak ditemukan.');
        }
        return view('kelompok.kelompok_detail_pengajuan_rkps', compact('kelompok', 'pengajuan') + $this->dataNavigasi());
    }

    public function detailPengajuanRkt($id)
    {
        $kelompok = $this->authorizeKelompok();
        $pengajuan = PengajuanRkt::where('id', $id)->where('id_kelompok', $kelompok->id)->first();
        if (!$pengajuan) {
            return redirect()->route('kelompok.pengajuan-rkt')->with('error', 'Pengajuan tidak ditemukan.');
        }
        return view('kelompok.kelompok_detail_pengajuan_rkt', compact('kelompok', 'pengajuan') + $this->dataNavigasi());
    }

    /* ─── 11-12. Ajukan Ulang ───────────────────────────────── */

    public function ajukanUlangRkps($id)
    {
        $kelompok = $this->authorizeKelompok();
        $pengajuan = PengajuanRkps::where('id', $id)->where('id_kelompok', $kelompok->id)->first();
        if (!$pengajuan || !in_array($pengajuan->status, ['Ditolak', 'Revisi'])) {
            return redirect()->route('kelompok.pengajuan-rkps')->with('error', 'Pengajuan tidak bisa diajukan ulang.');
        }
        return view('kelompok.kelompok_ajukan_ulang_rkps', compact('kelompok', 'pengajuan') + $this->dataNavigasi());
    }

    public function simpanAjukanUlangRkps(Request $request, $id)
    {
        $kelompok = $this->authorizeKelompok();
        $pengajuan = PengajuanRkps::where('id', $id)->where('id_kelompok', $kelompok->id)->first();
        if (!$pengajuan || !in_array($pengajuan->status, ['Ditolak', 'Revisi'])) {
            return redirect()->route('kelompok.pengajuan-rkps')->with('error', 'Pengajuan tidak bisa diajukan ulang.');
        }

        $data = $request->validate([
            'tahun_mulai'   => ['required', 'integer', 'between:1900,2200'],
            'tahun_selesai' => ['required', 'integer', 'gte:tahun_mulai', 'between:1900,2200'],
            'file_rkps'     => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $file = $request->file('file_rkps');
        $filename = time() . '_' . Str::random(12) . '.pdf';
        $file->storeAs('rkps', $filename, 'public');

        PengajuanRkps::create([
            'id_kelompok'   => $kelompok->id,
            'tahun_mulai'   => $data['tahun_mulai'],
            'tahun_selesai' => $data['tahun_selesai'],
            'file_rkps'     => $filename,
            'status'        => 'Menunggu',
        ]);

        return redirect()->route('kelompok.pengajuan-rkps')
            ->with('success', 'Pengajuan RKPS berhasil diajukan ulang.');
    }

    public function ajukanUlangRkt($id)
    {
        $kelompok = $this->authorizeKelompok();
        $pengajuan = PengajuanRkt::where('id', $id)->where('id_kelompok', $kelompok->id)->first();
        if (!$pengajuan || !in_array($pengajuan->status, ['Ditolak', 'Revisi'])) {
            return redirect()->route('kelompok.pengajuan-rkt')->with('error', 'Pengajuan tidak bisa diajukan ulang.');
        }
        $daftarRkps = Rkps::where('id_kelompok', $kelompok->id)->orderByDesc('tahun_mulai')->get();
        return view('kelompok.kelompok_ajukan_ulang_rkt', compact('kelompok', 'pengajuan', 'daftarRkps') + $this->dataNavigasi());
    }

    public function simpanAjukanUlangRkt(Request $request, $id)
    {
        $kelompok = $this->authorizeKelompok();
        $pengajuan = PengajuanRkt::where('id', $id)->where('id_kelompok', $kelompok->id)->first();
        if (!$pengajuan || !in_array($pengajuan->status, ['Ditolak', 'Revisi'])) {
            return redirect()->route('kelompok.pengajuan-rkt')->with('error', 'Pengajuan tidak bisa diajukan ulang.');
        }

        $data = $request->validate([
            'tahun'    => ['required', 'integer', 'between:1900,2200'],
            'file_rkt' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $file = $request->file('file_rkt');
        $filename = time() . '_' . Str::random(12) . '.pdf';
        $file->storeAs('rkt', $filename, 'public');

        PengajuanRkt::create([
            'id_kelompok' => $kelompok->id,
            'tahun'       => $data['tahun'],
            'file_rkt'    => $filename,
            'status'      => 'Menunggu',
        ]);

        return redirect()->route('kelompok.pengajuan-rkt')
            ->with('success', 'Pengajuan RKT berhasil diajukan ulang.');
    }

    /* ─── 13-14. KUPS CRUD ──────────────────────────────────── */

    public function kups()
    {
        $kelompok = $this->authorizeKelompok();
        $kupsList = Kups::where('id_kelompok', $kelompok->id)->orderBy('nama_kups')->get();
        return view('kelompok.kelompok_kups', compact('kelompok', 'kupsList') + $this->dataNavigasi());
    }

    public function tambahKups()
    {
        $kelompok = $this->authorizeKelompok();
        return view('kelompok.kelompok_tambah_kups', compact('kelompok') + $this->dataNavigasi());
    }

    public function simpanKups(Request $request)
    {
        $kelompok = $this->authorizeKelompok();

        $data = $request->validate([
            'nama_kups'         => ['required', 'string', 'max:200'],
            'ketua_kups'        => ['nullable', 'string', 'max:200'],
            'jumlah_anggota'    => ['nullable', 'integer'],
            'kelas_kups'        => ['nullable', 'in:Biru,Perak,Emas,Platinum'],
            'tahun_bentuk'      => ['nullable', 'digits:4'],
            'jenis_usaha'       => ['nullable', 'string', 'max:200'],
            'produk'            => ['nullable', 'string', 'max:200'],
            'produksi_tahun'    => ['nullable', 'integer'],
            'satuan_produksi'   => ['nullable', 'string', 'max:50'],
            'keterangan'        => ['nullable', 'string'],
        ]);

        Kups::create(array_merge($data, ['id_kelompok' => $kelompok->id]));

        return redirect()->route('kelompok.kups')->with('success', 'KUPS berhasil ditambahkan.');
    }

    public function editKups($id)
    {
        $kelompok = $this->authorizeKelompok();
        $kups = $this->checkOwnership($kelompok->id, $id, 'kups');
        if (!$kups) {
            return redirect()->route('kelompok.kups')->with('error', 'KUPS tidak ditemukan.');
        }
        return view('kelompok.kelompok_edit_kups', compact('kelompok', 'kups') + $this->dataNavigasi());
    }

    public function updateKups(Request $request, $id)
    {
        $kelompok = $this->authorizeKelompok();
        $kups = $this->checkOwnership($kelompok->id, $id, 'kups');
        if (!$kups) {
            return redirect()->route('kelompok.kups')->with('error', 'KUPS tidak ditemukan.');
        }

        $data = $request->validate([
            'nama_kups'         => ['required', 'string', 'max:200'],
            'ketua_kups'        => ['nullable', 'string', 'max:200'],
            'jumlah_anggota'    => ['nullable', 'integer'],
            'kelas_kups'        => ['nullable', 'in:Biru,Perak,Emas,Platinum'],
            'tahun_bentuk'      => ['nullable', 'digits:4'],
            'jenis_usaha'       => ['nullable', 'string', 'max:200'],
            'produk'            => ['nullable', 'string', 'max:200'],
            'produksi_tahun'    => ['nullable', 'integer'],
            'satuan_produksi'   => ['nullable', 'string', 'max:50'],
            'keterangan'        => ['nullable', 'string'],
        ]);

        Kups::where('id', $id)->update($data);

        return redirect()->route('kelompok.kups')->with('success', 'KUPS berhasil diperbarui.');
    }

    public function hapusKups($id)
    {
        $kelompok = $this->authorizeKelompok();
        $kups = $this->checkOwnership($kelompok->id, $id, 'kups');
        if (!$kups) {
            return redirect()->route('kelompok.kups')->with('error', 'KUPS tidak ditemukan.');
        }
        Kups::where('id', $id)->delete();

        return redirect()->route('kelompok.kups')->with('success', 'KUPS berhasil dihapus.');
    }

    /* ─── 15-17. Kegiatan CRUD ──────────────────────────────── */

    public function kegiatan()
    {
        $kelompok = $this->authorizeKelompok();
        $kegiatanList = Kegiatan::where('id_kelompok', $kelompok->id)->orderByDesc('tanggal')->get();
        return view('kelompok.kelompok_kegiatan', compact('kelompok', 'kegiatanList') + $this->dataNavigasi());
    }

    public function tambahKegiatan()
    {
        $kelompok = $this->authorizeKelompok();
        return view('kelompok.kelompok_tambah_kegiatan', compact('kelompok') + $this->dataNavigasi());
    }

    public function simpanKegiatan(Request $request)
    {
        $kelompok = $this->authorizeKelompok();

        $data = $request->validate([
            'nama_kegiatan' => ['required', 'string', 'max:200'],
            'tanggal'       => ['required', 'date'],
            'lokasi'        => ['nullable', 'string', 'max:255'],
            'keterangan'    => ['nullable', 'string'],
            'kml_file'      => ['nullable', 'file', 'mimes:kml', 'max:10240'],
        ]);

        $kmlFilename = null;
        if ($request->hasFile('kml_file')) {
            $file = $request->file('kml_file');
            $kmlFilename = time() . '_' . Str::random(12) . '.kml';
            $file->storeAs('kegiatan', $kmlFilename, 'public');
        }

        Kegiatan::create(array_merge($data, [
            'id_kelompok' => $kelompok->id,
            'kml_file'    => $kmlFilename,
        ]));

        return redirect()->route('kelompok.kegiatan')->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function detailKegiatan($id)
    {
        $kelompok = $this->authorizeKelompok();
        $kegiatan = $this->checkOwnership($kelompok->id, $id, 'kegiatan');
        if (!$kegiatan) {
            return redirect()->route('kelompok.kegiatan')->with('error', 'Kegiatan tidak ditemukan.');
        }
        $dokumentasi = DokumentasiKegiatan::where('id_kegiatan', $id)->get();

        $kmlUrl = '';
        if (!empty($kegiatan->kml_file)) {
            if (file_exists(public_path('storage/kegiatan/' . $kegiatan->kml_file))) {
                $kmlUrl = asset('storage/kegiatan/' . $kegiatan->kml_file);
            } elseif (file_exists(public_path('uploads/kml_kegiatan/' . $kegiatan->kml_file))) {
                $kmlUrl = asset('uploads/kml_kegiatan/' . rawurlencode($kegiatan->kml_file));
            } elseif (file_exists(storage_path('app/public/kegiatan/' . $kegiatan->kml_file))) {
                $kmlUrl = asset('storage/kegiatan/' . $kegiatan->kml_file);
            }
        }

        return view('kelompok.kelompok_detail_kegiatan', compact('kelompok', 'kegiatan', 'dokumentasi', 'kmlUrl') + $this->dataNavigasi());
    }

    public function petaKegiatan($id)
    {
        $kelompok = $this->authorizeKelompok();
        $kegiatan = $this->checkOwnership($kelompok->id, $id, 'kegiatan');
        if (!$kegiatan) {
            return redirect()->route('kelompok.kegiatan')->with('error', 'Kegiatan tidak ditemukan.');
        }

        $dokumentasi = DokumentasiKegiatan::where('id_kegiatan', $id)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('id', 'asc')
            ->get();

        $kmlUrl = '';
        if (!empty($kegiatan->kml_file)) {
            if (file_exists(public_path('storage/kegiatan/' . $kegiatan->kml_file))) {
                $kmlUrl = asset('storage/kegiatan/' . $kegiatan->kml_file);
            } elseif (file_exists(public_path('uploads/kml_kegiatan/' . $kegiatan->kml_file))) {
                $kmlUrl = asset('uploads/kml_kegiatan/' . rawurlencode($kegiatan->kml_file));
            } elseif (file_exists(storage_path('app/public/kegiatan/' . $kegiatan->kml_file))) {
                $kmlUrl = asset('storage/kegiatan/' . $kegiatan->kml_file);
            }
        }

        return view('kelompok.kelompok_peta_kegiatan', compact('kelompok', 'kegiatan', 'dokumentasi', 'kmlUrl') + $this->dataNavigasi());
    }

    public function editKegiatan($id)
    {
        $kelompok = $this->authorizeKelompok();
        $kegiatan = $this->checkOwnership($kelompok->id, $id, 'kegiatan');
        if (!$kegiatan) {
            return redirect()->route('kelompok.kegiatan')->with('error', 'Kegiatan tidak ditemukan.');
        }
        return view('kelompok.kelompok_edit_kegiatan', compact('kelompok', 'kegiatan') + $this->dataNavigasi());
    }

    public function updateKegiatan(Request $request, $id)
    {
        $kelompok = $this->authorizeKelompok();
        $kegiatan = $this->checkOwnership($kelompok->id, $id, 'kegiatan');
        if (!$kegiatan) {
            return redirect()->route('kelompok.kegiatan')->with('error', 'Kegiatan tidak ditemukan.');
        }

        $data = $request->validate([
            'nama_kegiatan' => ['required', 'string', 'max:200'],
            'tanggal'       => ['required', 'date'],
            'lokasi'        => ['nullable', 'string', 'max:255'],
            'keterangan'    => ['nullable', 'string'],
            'kml_file'      => ['nullable', 'file', 'mimes:kml', 'max:10240'],
        ]);

        $kmlFilename = $kegiatan->kml_file;
        if ($request->hasFile('kml_file')) {
            // hapus file lama
            if ($kegiatan->kml_file) {
                Storage::disk('public')->delete('kegiatan/' . $kegiatan->kml_file);
            }
            $file = $request->file('kml_file');
            $kmlFilename = time() . '_' . Str::random(12) . '.kml';
            $file->storeAs('kegiatan', $kmlFilename, 'public');
        }

        $data['kml_file'] = $kmlFilename;
        Kegiatan::where('id', $id)->update($data);

        return redirect()->route('kelompok.kegiatan')->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function hapusKegiatan($id)
    {
        $kelompok = $this->authorizeKelompok();
        $kegiatan = $this->checkOwnership($kelompok->id, $id, 'kegiatan');
        if (!$kegiatan) {
            return redirect()->route('kelompok.kegiatan')->with('error', 'Kegiatan tidak ditemukan.');
        }

        // hapus file KML
        if ($kegiatan->kml_file) {
            Storage::disk('public')->delete('kegiatan/' . $kegiatan->kml_file);
        }

        // hapus dokumentasi terkait
        DokumentasiKegiatan::where('id_kegiatan', $id)->delete();
        Kegiatan::where('id', $id)->delete();

        return redirect()->route('kelompok.kegiatan')->with('success', 'Kegiatan berhasil dihapus.');
    }

    public function simpanDokumentasi(Request $request, $idKegiatan)
    {
        $kelompok = $this->authorizeKelompok();
        $kegiatan = $this->checkOwnership($kelompok->id, $idKegiatan, 'kegiatan');
        if (!$kegiatan) {
            return redirect()->route('kelompok.kegiatan')->with('error', 'Kegiatan tidak ditemukan.');
        }

        $request->validate([
            'foto'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:15360'],
            'foto_base64' => ['nullable', 'string'],
            'keterangan'  => ['nullable', 'string', 'max:500'],
            'latitude'    => ['nullable', 'string', 'max:50'],
            'longitude'   => ['nullable', 'string', 'max:50'],
        ]);

        $filename = null;

        // If base64 photo with stamped canvas watermark is provided
        if ($request->filled('foto_base64')) {
            $base64Data = $request->input('foto_base64');
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
                $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
                $imageData = base64_decode($base64Data);
                if ($imageData !== false) {
                    // Pastikan ukuran di bawah 300 KB (307.200 bytes)
                    if (strlen($imageData) > 307200 && extension_loaded('gd')) {
                        $src = @imagecreatefromstring($imageData);
                        if ($src) {
                            $quality = 75;
                            do {
                                ob_start();
                                imagejpeg($src, null, $quality);
                                $comp = ob_get_clean();
                                $quality -= 10;
                            } while (strlen($comp) > 307200 && $quality >= 30);
                            imagedestroy($src);
                            if (strlen($comp) > 0) {
                                $imageData = $comp;
                            }
                        }
                    }

                    $filename = time() . '_' . Str::random(12) . '.jpg';
                    Storage::disk('public')->put('kegiatan/' . $filename, $imageData);
                }
            }
        }

        // Fallback to uploaded file
        if (!$filename && $request->hasFile('foto')) {
            $file = $request->file('foto');
            $rawContent = file_get_contents($file->getRealPath());

            // Kompresi jika di atas 300 KB
            if (strlen($rawContent) > 307200 && extension_loaded('gd')) {
                $src = @imagecreatefromstring($rawContent);
                if ($src) {
                    $quality = 75;
                    do {
                        ob_start();
                        imagejpeg($src, null, $quality);
                        $comp = ob_get_clean();
                        $quality -= 10;
                    } while (strlen($comp) > 307200 && $quality >= 30);
                    imagedestroy($src);
                    if (strlen($comp) > 0) {
                        $rawContent = $comp;
                    }
                }
            }

            $filename = time() . '_' . Str::random(12) . '.jpg';
            Storage::disk('public')->put('kegiatan/' . $filename, $rawContent);
        }

        if (!$filename) {
            return back()->with('error', 'Gagal memproses foto. Pastikan Anda telah memilih atau mengambil foto.');
        }

        // Pastikan file juga tersalin ke public/storage/kegiatan/
        $pubKegiatanDir = public_path('storage/kegiatan');
        if (!is_dir($pubKegiatanDir)) {
            @mkdir($pubKegiatanDir, 0777, true);
        }
        $pubKegiatanFile = $pubKegiatanDir . '/' . $filename;
        if (!file_exists($pubKegiatanFile)) {
            @copy(storage_path('app/public/kegiatan/' . $filename), $pubKegiatanFile);
        }

        // Format keterangan / geotag info (Latitude, Longitude, Altitude)
        $ket = $request->input('keterangan', '');
        $lat = $request->input('latitude');
        $lng = $request->input('longitude');
        $alt = $request->input('altitude');

        $geoParts = [];
        if ($lat && $lng) {
            $geoParts[] = "📍 GPS: {$lat}, {$lng}";
        }
        if ($alt) {
            $geoParts[] = "🏔️ Alt: {$alt} m mdpl";
        }
        if (count($geoParts) > 0) {
            $ket = ($ket ? $ket . ' | ' : '') . implode(' | ', $geoParts);
        }

        DokumentasiKegiatan::create([
            'id_kegiatan'         => $idKegiatan,
            'id_user'             => Auth::id() ?? 0,
            'foto_uuid'           => (string) Str::uuid(),
            'nama_file'           => $filename,
            'file_path'           => 'storage/kegiatan/' . $filename,
            'file_type'           => 'image/jpeg',
            'latitude'            => $lat ? (float)$lat : null,
            'longitude'           => $lng ? (float)$lng : null,
            'altitude'            => $alt ? (float)$alt : null,
            'tanggal_foto'        => now(),
            'keterangan'          => $ket,
            'status_geotag'       => ($lat && $lng) ? 'KAMERA' : 'TIDAK_ADA',
            'google_drive_status' => 'pending',
            'created_at'          => now(),
        ]);

        return redirect()->route('kelompok.kegiatan.detail', $idKegiatan)
            ->with('success', 'Foto dokumentasi kegiatan ber-geotag berhasil diunggah.');
    }

    public function hapusDokumentasi($idDokumentasi)
    {
        $kelompok = $this->authorizeKelompok();
        $doc = DokumentasiKegiatan::find($idDokumentasi);
        if (!$doc) {
            return back()->with('error', 'Dokumentasi tidak ditemukan.');
        }

        $kegiatan = $this->checkOwnership($kelompok->id, $doc->id_kegiatan, 'kegiatan');
        if (!$kegiatan) {
            return back()->with('error', 'Akses ditolak.');
        }

        $filename = $doc->nama_file ?? $doc->foto;
        if ($filename) {
            Storage::disk('public')->delete('kegiatan/' . $filename);
            if (file_exists(public_path('storage/kegiatan/' . $filename))) {
                @unlink(public_path('storage/kegiatan/' . $filename));
            }
        }
        if ($doc->file_path && file_exists(public_path($doc->file_path))) {
            @unlink(public_path($doc->file_path));
        }
        $doc->delete();

        return back()->with('success', 'Foto dokumentasi berhasil dihapus.');
    }

    /* ─── Peta Wilayah ───────────────────────────────────────── */

    public function peta()
    {
        $kelompok = $this->authorizeKelompok();

        /*
         * Petak Perhutani TIDAK diambil di sini.
         * Data petak dimuat lewat AJAX (getPetakKelompokAjax) saat
         * user mencentang layer "🌳 Petak Perhutani" di peta —
         * supaya halaman peta ringan saat pertama dibuka.
         */
        $kegiatanList = Kegiatan::with('dokumentasi')->where('id_kelompok', $kelompok->id)->get();
        $anggota = AnggotaKelompok::where('id_kelompok', $kelompok->id)
            ->whereNotNull('polygon')
            ->get();

        return view('kelompok.kelompok_peta', compact('kelompok', 'kegiatanList', 'anggota') + $this->dataNavigasi());
    }

    /* ─── 18-19. Potensi CRUD ───────────────────────────────── */

    public function potensi()
    {
        $kelompok = $this->authorizeKelompok();
        $potensiList = PotensiKelompok::where('id_kelompok', $kelompok->id)
            ->orderBy('jenis_potensi', 'asc')
            ->orderBy('jenis_detail', 'asc')
            ->get();
        return view('kelompok.kelompok_potensi', compact('kelompok', 'potensiList') + $this->dataNavigasi());
    }

    public function tambahPotensi()
    {
        $kelompok = $this->authorizeKelompok();
        return view('kelompok.kelompok_tambah_potensi', compact('kelompok') + $this->dataNavigasi());
    }

    public function simpanPotensi(Request $request)
    {
        $kelompok = $this->authorizeKelompok();
        $data = $request->validate([
            'jenis_potensi' => ['required', 'in:Kayu,Non Kayu,Jasa Lingkungan'],
            'jenis_detail' => ['required', 'string', 'max:150'],
            'tahun_tanam' => ['nullable', 'digits:4'],
            'luasan' => ['nullable', 'numeric'],
            'satuan' => ['nullable', 'string', 'max:30'],
            'kondisi' => ['nullable', 'string', 'max:100'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string'],
            'foto' => ['nullable', 'image', 'max:5120'],
        ]);
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('potensi', 'public');
            $data['foto'] = basename($data['foto']);
        }
        PotensiKelompok::create(array_merge($data, ['id_kelompok' => $kelompok->id]));
        return redirect()->route('kelompok.potensi')->with('success', 'Potensi berhasil ditambahkan.');
    }

    public function editPotensi($id)
    {
        $kelompok = $this->authorizeKelompok();
        $potensi = PotensiKelompok::where('id', $id)->where('id_kelompok', $kelompok->id)->firstOrFail();
        return view('kelompok.kelompok_edit_potensi', compact('kelompok', 'potensi') + $this->dataNavigasi());
    }

    public function updatePotensi(Request $request, $id)
    {
        $kelompok = $this->authorizeKelompok();
        $potensi = PotensiKelompok::where('id', $id)->where('id_kelompok', $kelompok->id)->firstOrFail();
        $data = $request->validate([
            'jenis_potensi' => ['required', 'in:Kayu,Non Kayu,Jasa Lingkungan'],
            'jenis_detail' => ['required', 'string', 'max:150'],
            'tahun_tanam' => ['nullable', 'digits:4'],
            'luasan' => ['nullable', 'numeric'],
            'satuan' => ['nullable', 'string', 'max:30'],
            'kondisi' => ['nullable', 'string', 'max:100'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string'],
            'foto' => ['nullable', 'image', 'max:5120'],
        ]);
        if ($request->hasFile('foto')) {
            if ($potensi->foto) Storage::disk('public')->delete('potensi/' . $potensi->foto);
            $data['foto'] = basename($request->file('foto')->store('potensi', 'public'));
        }
        $potensi->update($data);
        return redirect()->route('kelompok.potensi')->with('success', 'Potensi berhasil diperbarui.');
    }

    public function hapusPotensi($id)
    {
        $kelompok = $this->authorizeKelompok();
        $potensi = PotensiKelompok::where('id', $id)->where('id_kelompok', $kelompok->id)->firstOrFail();
        if ($potensi->foto) Storage::disk('public')->delete('potensi/' . $potensi->foto);
        $potensi->delete();
        return redirect()->route('kelompok.potensi')->with('success', 'Potensi berhasil dihapus.');
    }

    /* ─── 20-22. Permasalahan CRUD ──────────────────────────── */

    public function permasalahan()
    {
        $kelompok = $this->authorizeKelompok();
        $permasalahanList = PermasalahanKelompok::where('id_kelompok', $kelompok->id)->orderByDesc('created_at')->get();
        return view('kelompok.kelompok_permasalahan', compact('kelompok', 'permasalahanList') + $this->dataNavigasi());
    }

    public function tambahPermasalahan()
    {
        $kelompok = $this->authorizeKelompok();
        return view('kelompok.kelompok_tambah_permasalahan', compact('kelompok') + $this->dataNavigasi());
    }

    public function simpanPermasalahan(Request $request)
    {
        $kelompok = $this->authorizeKelompok();

        $data = $request->validate([
            'judul'    => ['required', 'string', 'max:200'],
            'deskripsi' => ['nullable', 'string'],
            'status'   => ['nullable', 'in:Aktif,Selesai'],
        ]);

        PermasalahanKelompok::create(array_merge($data, [
            'id_kelompok' => $kelompok->id,
            'status'      => $data['status'] ?? 'Aktif',
        ]));

        return redirect()->route('kelompok.permasalahan')->with('success', 'Permasalahan berhasil ditambahkan.');
    }

    public function updatePermasalahan(Request $request, $id)
    {
        $kelompok = $this->authorizeKelompok();
        $permasalahan = $this->checkOwnership($kelompok->id, $id, 'permasalahan_kelompok');
        if (!$permasalahan) {
            return redirect()->route('kelompok.permasalahan')->with('error', 'Permasalahan tidak ditemukan.');
        }

        $data = $request->validate([
            'judul'    => ['required', 'string', 'max:200'],
            'deskripsi' => ['nullable', 'string'],
            'status'   => ['required', 'in:Aktif,Selesai'],
        ]);

        PermasalahanKelompok::where('id', $id)->update($data);

        return redirect()->route('kelompok.permasalahan')->with('success', 'Permasalahan berhasil diperbarui.');
    }

    public function hapusPermasalahan($id)
    {
        $kelompok = $this->authorizeKelompok();
        $permasalahan = $this->checkOwnership($kelompok->id, $id, 'permasalahan_kelompok');
        if (!$permasalahan) {
            return redirect()->route('kelompok.permasalahan')->with('error', 'Permasalahan tidak ditemukan.');
        }
        PermasalahanKelompok::where('id', $id)->delete();

        return redirect()->route('kelompok.permasalahan')->with('success', 'Permasalahan berhasil dihapus.');
    }
}