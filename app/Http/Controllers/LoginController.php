<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

/**
 * LoginController — menggantikan login.php dari source code lama.
 *
 * Perilaku yang disamakan:
 * 1. Query: SELECT * FROM users WHERE username = ?
 * 2. Verifikasi: password_verify(input, hash_db)
 * 3. Session: set user id, role
 * 4. Logging: INSERT INTO user_logs (user_id, login_time, last_activity, ip_address)
 * 5. Redirect berdasarkan role
 */
class LoginController extends Controller
{
    /**
     * Tampilkan form login.
     */
    public function showLoginForm()
    {
        // Kalau sudah login, redirect ke dashboard sesuai role
        if (Auth::check()) {
            return $this->redirectByRole();
        }

        return view('auth.login');
    }

    /**
     * Proses login.
     * Meniru perilaku login.php secara persis.
     */
    public function login(Request $request)
    {
        // 1. Validasi input
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // 2. Cari user berdasarkan username
        // Meniru: SELECT * FROM users WHERE username = ?
        $user = \App\Models\User::where('username', $credentials['username'])->first();

        if (!$user) {
            return back()
                ->withInput($request->only('username'))
                ->with('error', 'Username atau password salah.');
        }

        // 3. Verifikasi password
        // Meniru: password_verify($input_password, $user['password'])
        if (!password_verify($credentials['password'], $user->password)) {
            return back()
                ->withInput($request->only('username'))
                ->with('error', 'Username atau password salah.');
        }

        // 4. Login via Auth facade
        Auth::login($user);

        // 5. Regenerate session (keamanan, tiru session_regenerate_id(true))
        $request->session()->regenerate();

        // 6. Log ke user_logs — tiru login.php:
        // INSERT INTO user_logs (user_id, login_time, last_activity, ip_address) VALUES (?, NOW(), NOW(), ?)
        $logId = DB::table('user_logs')->insertGetId([
            'user_id' => $user->id,
            'login_time' => now(),
            'last_activity' => now(),
            'ip_address' => $request->ip(),
        ]);

        // Simpan log_id di session untuk idle timeout check di middleware
        Session::put('user_log_id', $logId);

        // Simpan last_activity di session untuk idle timeout check
        Session::put('last_activity', time());

        // 7. Redirect berdasarkan role
        return $this->redirectByRole();
    }

    /**
     * Redirect ke dashboard berdasarkan role user.
     * Meniru redirect di login.php:
     * - admin → /admin/dashboard
     * - pendamping → /pendamping/dashboard
     * - kelompok → /kelompok/dashboard
     */
    private function redirectByRole()
    {
        $role = Auth::user()->role;

        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'admin_cdk' => redirect()->route('admin-cdk.dashboard'),
            'pendamping' => redirect()->route('pendamping.dashboard'),
            'kelompok' => redirect()->route('kelompok.dashboard'),
            default => redirect()->route('login'),
        };
    }
}
