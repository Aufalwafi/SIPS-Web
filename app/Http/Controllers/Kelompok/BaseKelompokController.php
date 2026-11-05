<?php

namespace App\Http\Controllers\Kelompok;

use App\Http\Controllers\Controller;
use App\Models\Kelompok;
use App\Models\AnggotaKelompok;
use App\Models\PengajuanRkps;
use App\Models\PengajuanRkt;
use App\Models\Kegiatan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

abstract class BaseKelompokController extends Controller
{
    /* ─── helpers ────────────────────────────────────────────── */

    protected function getKelompok()
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

    protected function authorizeKelompok()
    {
        $kelompok = $this->getKelompok();
        if (!$kelompok) {
            abort(403, 'Anda tidak memiliki akses ke kelompok ini atau belum memilih kelompok binaan.');
        }
        return $kelompok;
    }

    protected function checkOwnership($kelompokId, $itemId, $table, $pk = 'id')
    {
        return DB::table($table)->where($pk, $itemId)->where('id_kelompok', $kelompokId)->first();
    }

    protected function dataNavigasi(): array
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
            'rktMenunggu'  => $rktMenunggu,
            'totalNotif'   => $rkpsMenunggu + $rktMenunggu,
        ];
    }

    /**
     * Query Petak Perhutani
     */
    protected function queryPetakPerhutani($kelompok)
    {
        return \App\Models\PetakPerhutani::getForKelompok($kelompok);
    }

    protected function getSpasialKelompok($kelompok): array
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
}
