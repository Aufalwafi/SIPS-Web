<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PendampingController extends Controller
{
    public function dataPendamping()
    {
        $pendamping = DB::table('pendamping')
            ->leftJoin('cdk', 'pendamping.id_cdk', '=', 'cdk.id')
            ->leftJoin('users', 'users.username', '=', 'pendamping.username')
            ->leftJoin('pendamping_kelompok', 'pendamping_kelompok.id_user', '=', 'users.id')
            ->select(
                'pendamping.id_pendamping', 'pendamping.nama_pendamping', 'pendamping.nip',
                'pendamping.username', 'pendamping.status', 'cdk.nama as nama_cdk',
                DB::raw('COUNT(pendamping_kelompok.id_kelompok) as jumlah_kelompok')
            )
            ->groupBy('pendamping.id_pendamping', 'pendamping.nama_pendamping', 'pendamping.nip', 'pendamping.username', 'pendamping.status', 'cdk.nama')
            ->orderBy('pendamping.nama_pendamping')
            ->get();

        return view('admin.data-pendamping', compact('pendamping'));
    }

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

        DB::table('users')->insert([
            'nama'       => $data['nama_pendamping'],
            'username'   => $data['username'],
            'password'   => $hash,
            'role'       => 'pendamping',
            'id_cdk'     => $data['id_cdk'],
            'created_at' => now(),
        ]);

        DB::table('pendamping')->insert([
            'id_cdk'          => $data['id_cdk'],
            'nama_pendamping' => $data['nama_pendamping'],
            'nip'             => $data['nip'] ?? null,
            'username'        => $data['username'],
            'password'        => $hash,
            'no_hp'           => $data['no_hp'] ?? null,
            'email'           => $data['email'] ?? null,
            'alamat'          => $data['alamat'] ?? null,
            'status'          => 'aktif',
            'created_at'      => now(),
            'updated_at'      => now(),
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
            DB::table('users')->where('username', $pendamping->username)->where('role', 'pendamping')->update([
                'password' => $hash,
            ]);
        }

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

        $kelompokPivot = collect();
        if ($user) {
            $kelompokPivot = DB::table('pendamping_kelompok as pk')
                ->join('kelompok as k', 'pk.id_kelompok', '=', 'k.id')
                ->leftJoin('cdk as c', 'k.id_cdk', '=', 'c.id')
                ->where('pk.id_user', $user->id)
                ->select('k.*', 'c.nama as nama_cdk')
                ->get();
        }

        $kelompokKolom = DB::table('kelompok as k')
            ->leftJoin('cdk as c', 'k.id_cdk', '=', 'c.id')
            ->where('k.id_pendamping', $pendamping->id_pendamping)
            ->select('k.*', 'c.nama as nama_cdk')
            ->get();

        $kelompokBinaan = $kelompokPivot->merge($kelompokKolom)->unique('id')->values();

        $jumlahKelompok = $kelompokBinaan->count();
        $totalLuasLahan = $kelompokBinaan->sum('luas_lahan') ?? 0;

        $binaanIds = $kelompokBinaan->pluck('id')->toArray();
        $kelompokTersedia = DB::table('kelompok')
            ->whereNotIn('id', $binaanIds)
            ->orderBy('nama_kelompok')
            ->get(['id', 'nama_kelompok', 'desa', 'kecamatan', 'kabupaten']);

        return view('admin.detail-pendamping', compact(
            'pendamping', 'user', 'kelompokBinaan',
            'jumlahKelompok', 'totalLuasLahan', 'kelompokTersedia'
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
}
