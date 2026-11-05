<?php

namespace App\Http\Controllers\Kelompok;

use App\Models\AnggotaKelompok;
use App\Models\Kegiatan;
use App\Models\PengajuanRkps;
use App\Models\PengajuanRkt;
use Illuminate\Http\Request;

class DashboardController extends BaseKelompokController
{
    /* ─── 1. Dashboard ──────────────────────────────────────── */

    public function dashboard()
    {
        $kelompok = $this->getKelompok();
        if (!$kelompok) {
            return view('kelompok.dashboard', [
                'kelompok'     => null,
                'totalAnggota' => 0,
                'rkpsMenunggu' => 0,
                'rktMenunggu'  => 0,
                'totalNotif'   => 0,
                'kegiatanList' => collect()
            ]);
        }
        $id = $kelompok->id;

        $totalAnggota = AnggotaKelompok::where('id_kelompok', $id)->count();
        $rkpsMenunggu = PengajuanRkps::where('id_kelompok', $id)->where('status', 'Menunggu')->count();
        $rktMenunggu  = PengajuanRkt::where('id_kelompok', $id)->where('status', 'Menunggu')->count();
        $rkpsTerakhir = PengajuanRkps::where('id_kelompok', $id)->orderBy('id', 'desc')->first();
        $rktTerakhir  = PengajuanRkt::where('id_kelompok', $id)->orderBy('id', 'desc')->first();

        $kegiatanList = Kegiatan::with('dokumentasi')->where('id_kelompok', $id)->get();

        return view('kelompok.dashboard', compact(
            'kelompok', 'totalAnggota', 'rkpsMenunggu', 'rktMenunggu', 'rkpsTerakhir', 'rktTerakhir',
            'kegiatanList'
        ) + $this->dataNavigasi());
    }

    /**
     * ENDPOINT AJAX — PETAK PERHUTANI (untuk peta wilayah kelompok)
     */
    public function getPetakKelompokAjax(Request $request)
    {
        $kelompok = $this->authorizeKelompok();

        $petakPerhutani = $this->queryPetakPerhutani($kelompok);

        $hasil = $petakPerhutani->map(function ($p) {
            return [
                'id'            => $p->id,
                'kode_toponimi' => $p->kode_toponimi,
                'petak'         => $p->petak,
                'anak_petak'    => $p->anak_petak,
                'rph'           => $p->rph,
                'bkph'          => $p->bkph,
                'kph'           => $p->kph,
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

    /* ─── 2. Data Kelompok (Profil) ─────────────────────────── */

    public function dataKelompok()
    {
        $kelompok = $this->authorizeKelompok();
        return view('kelompok.kelompok_data_kelompok', compact('kelompok') + $this->dataNavigasi());
    }

    /* ─── 3. Peta Wilayah ───────────────────────────────────── */

    public function peta()
    {
        $kelompok = $this->authorizeKelompok();

        $kegiatanList = Kegiatan::with('dokumentasi')->where('id_kelompok', $kelompok->id)->get();
        $anggota = AnggotaKelompok::where('id_kelompok', $kelompok->id)
            ->whereNotNull('polygon')
            ->get();

        return view('kelompok.kelompok_peta', compact('kelompok', 'kegiatanList', 'anggota') + $this->dataNavigasi());
    }
}
