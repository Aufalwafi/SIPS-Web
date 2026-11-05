<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

/**
 * LogoutController — menggantikan logout.php dari source code lama.
 *
 * Perilaku yang disamakan:
 * 1. UPDATE user_logs SET logout_time = NOW() WHERE id = ?
 * 2. Session::invalidate() — hapus semua session data
 * 3. Session::regenerateToken() — regenerate CSRF token
 * 4. Auth::logout()
 * 5. Redirect ke login
 */
class LogoutController extends Controller
{
    /**
     * Proses logout.
     * Meniru perilaku logout.php secara persis.
     */
    public function logout(Request $request)
    {
        // 1. Log logout ke user_logs — tiru logout.php:
        // UPDATE user_logs SET logout_time = NOW() WHERE id = ?
        $logId = Session::get('user_log_id');
        if ($logId) {
            DB::table('user_logs')
                ->where('id', $logId)
                ->update(['logout_time' => now()]);
        }

        // 2. Logout via Auth facade
        Auth::logout();

        // 3. Hapus semua session data
        $request->session()->invalidate();

        // 4. Regenerate CSRF token
        $request->session()->regenerateToken();

        // 5. Redirect ke login
        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil logout.');
    }
}
