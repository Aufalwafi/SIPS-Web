<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
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

        return view('admin.data-user', compact('query', 'cari', 'role', 'notif'));
    }

    public function createUser()
    {
        $daftarKelompok = DB::table('kelompok')->orderBy('nama_kelompok')->get(['id', 'nama_kelompok']);
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

        if ($data['role'] !== 'kelompok') { $data['id_kelompok'] = null; }
        if ($data['role'] !== 'admin_cdk') { $data['id_cdk'] = null; }

        DB::table('users')->insert([
            'nama' => $data['nama'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'id_kelompok' => $data['id_kelompok'] ?? null,
            'id_cdk' => $data['id_cdk'] ?? null,
            'created_at' => now(),
        ]);

        return redirect()->route('admin.data-user')->with('success', 'User berhasil ditambahkan.');
    }

    public function editUser(int $id)
    {
        $user = DB::table('users')->where('id', $id)->first();
        abort_unless($user, 404);

        $daftarKelompok = DB::table('kelompok')->orderBy('nama_kelompok')->get(['id', 'nama_kelompok']);
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

        if ($data['role'] !== 'kelompok') { $data['id_kelompok'] = null; }
        if ($data['role'] !== 'admin_cdk') { $data['id_cdk'] = null; }

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

        return redirect()->route('admin.data-user')->with('success', 'Data user berhasil diperbarui.');
    }

    public function resetUserPassword(int $id)
    {
        $user = DB::table('users')->where('id', $id)->first();
        abort_unless($user, 404);

        DB::table('users')->where('id', $id)->update(['password' => Hash::make('123456')]);

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
                'u.nama', 'u.username', 'u.role', 'l.login_time', 'l.logout_time', 'l.last_activity',
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

        return view('admin.aktivitas-user', compact('logs', 'tanggalAwal', 'tanggalAkhir', 'role'));
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
}
