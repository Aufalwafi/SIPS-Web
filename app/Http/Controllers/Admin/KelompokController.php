<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GeoParserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KelompokController extends Controller
{
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

        abort_unless($kelompok, 404);

        $rkpsTerakhir = DB::table('rkps')
            ->where('id_kelompok', $id)
            ->orderByDesc('tahun_mulai')
            ->first();

        $rktTerakhir = DB::table('rkt')
            ->where('id_kelompok', $id)
            ->orderByDesc('tahun')
            ->first();

        $totalAnggota = DB::table('anggota_kelompok')->where('id_kelompok', $id)->count();

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

    public function getPetakPerhutaniAjax($id)
    {
        $kelompok = DB::table('kelompok')->where('id', $id)->first();
        if (!$kelompok) {
            return response()->json([]);
        }

        $petakPerhutani = \App\Models\PetakPerhutani::getForKelompok($kelompok);

        $hasil = $petakPerhutani->map(function ($p) {
            return [
                'id'            => $p->id,
                'kode_toponimi' => $p->kode_toponimi,
                'petak'         => $p->petak,
                'anak_petak'    => $p->anak_petak,
                'rph'           => $p->rph,
                'bkph'          => $p->bkph,
                'kph'           => $p->kph,
                'desa'          => $p->desa,
                'kecamatan'     => $p->kecamatan,
                'kabupaten'     => $p->kabupaten,
                'fungsi'        => $p->fungsi,
                'klas_hutan'    => $p->klas_hutan,
                'luas_baku'     => $p->luas_baku,
                'jenis_tanaman' => $p->jenis_tanaman,
                'tahun_tanam'   => $p->tahun_tanam,
                'geometry'      => $p->geojson,
            ];
        });

        return response()->json($hasil->values());
    }

    public function getPetakPerhutaniBboxAjax(Request $request)
    {
        $bbox = $request->query('bbox'); // minLng,minLat,maxLng,maxLat
        if (!$bbox) {
            return response()->json(['status' => 'empty', 'data' => []]);
        }

        $coords = explode(',', $bbox);
        if (count($coords) !== 4) {
            return response()->json(['status' => 'invalid', 'data' => []]);
        }

        $minX = (float) $coords[0];
        $minY = (float) $coords[1];
        $maxX = (float) $coords[2];
        $maxY = (float) $coords[3];

        // Cegah query terlalu luas jika user masih zoom out jauh (lebih dari 1.2 derajat)
        if (abs($maxX - $minX) > 1.2 || abs($maxY - $minY) > 1.2) {
            return response()->json([
                'status' => 'too_large',
                'message' => 'Silakan perbesar peta (zoom in) untuk memuat garis petak perhutani.',
                'data' => []
            ]);
        }

        try {
            $sql = "
                SELECT id, kode_toponimi, petak, anak_petak, rph, bkph, kph,
                       desa, kecamatan, kabupaten, fungsi, klas_hutan, luas_baku,
                       jenis_tanaman, tahun_tanam,
                       ST_AsGeoJSON(geometry) AS geojson
                FROM petak_perhutani
                WHERE MBRIntersects(
                    geometry,
                    ST_GeomFromText('POLYGON(($minX $minY, $maxX $minY, $maxX $maxY, $minX $maxY, $minX $minY))')
                )
                LIMIT 500
            ";

            $rows = DB::select($sql);

            $hasil = collect($rows)->map(function ($p) {
                return [
                    'id'            => $p->id,
                    'kode_toponimi' => $p->kode_toponimi,
                    'petak'         => $p->petak,
                    'anak_petak'    => $p->anak_petak,
                    'rph'           => $p->rph,
                    'bkph'          => $p->bkph,
                    'kph'           => $p->kph,
                    'desa'          => $p->desa,
                    'kecamatan'     => $p->kecamatan,
                    'kabupaten'     => $p->kabupaten,
                    'fungsi'        => $p->fungsi,
                    'klas_hutan'    => $p->klas_hutan,
                    'luas_baku'     => $p->luas_baku,
                    'jenis_tanaman' => $p->jenis_tanaman,
                    'tahun_tanam'   => $p->tahun_tanam,
                    'geometry'      => $p->geojson,
                ];
            });

            return response()->json([
                'status' => 'success',
                'count'  => count($hasil),
                'data'   => $hasil->values()
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'data'    => []
            ], 500);
        }
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
            'file_peta'     => ['nullable', 'file', 'max:20480'],
        ]);

        $skPsPath = null;
        if ($request->hasFile('sk_ps')) {
            $skPsPath = $request->file('sk_ps')->store('sk_ps', 'public');
        }

        $skKelompokPath = null;
        if ($request->hasFile('sk_kelompok')) {
            $skKelompokPath = $request->file('sk_kelompok')->store('sk_kelompok', 'public');
        }

        $polygonKelompok = null;
        if ($request->hasFile('file_peta')) {
            try {
                $geoService = new GeoParserService();
                $polygonKelompok = $geoService->parseToGeoJson($request->file('file_peta'));
            } catch (\Exception $e) {
                return back()->withInput()->withErrors(['file_peta' => 'Gagal membaca berkas peta: ' . $e->getMessage()]);
            }
        }

        DB::table('kelompok')->insert([
            'id_cdk'           => $data['id_cdk'] ?? null,
            'nama_kelompok'    => $data['nama_kelompok'],
            'ketua'            => $data['ketua'] ?? null,
            'desa'             => $data['desa'] ?? null,
            'kecamatan'        => $data['kecamatan'] ?? null,
            'kabupaten'        => $data['kabupaten'] ?? null,
            'skema_ps'         => $data['skema_ps'] ?? null,
            'luas_lahan'       => $data['luas_lahan'] ?? 0,
            'no_sk'            => $data['no_sk'] ?? null,
            'tanggal_sk'       => $data['tanggal_sk'] ?? null,
            'id_pendamping'    => 0,
            'sk_ps'            => $skPsPath,
            'sk_kelompok'      => $skKelompokPath,
            'polygon_kelompok' => $polygonKelompok,
            'created_at'       => now(),
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
            'file_peta'     => ['nullable', 'file', 'max:20480'],
            'hapus_peta'    => ['nullable', 'boolean'],
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

    public function dataAnggota()
    {
        $anggota = DB::table('anggota_kelompok')
            ->leftJoin('kelompok', 'anggota_kelompok.id_kelompok', '=', 'kelompok.id')
            ->orderBy('anggota_kelompok.nama_anggota')
            ->get(['anggota_kelompok.*', 'kelompok.nama_kelompok']);

        $daftarKelompok = DB::table('kelompok')->orderBy('nama_kelompok')->get();

        return view('admin.data-anggota', compact('anggota', 'daftarKelompok'));
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
}
