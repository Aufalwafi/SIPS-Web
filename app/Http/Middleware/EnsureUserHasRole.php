<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

/**
 * Middleware: EnsureUserHasRole + Idle Timeout 15 menit.
 *
 * Menggantikan source code lama:
 * - Pengecekan role manual di setiap halaman (admin/cek_role.php, dll)
 * - cek_session.php (idle timeout 15 menit + update last_activity)
 *
 * Penggunaan di route:
 * - middleware('role:admin')
 * - middleware('role:pendamping')
 * - middleware('role:kelompok')
 * - middleware('role:admin,pendamping,kelompok') (multi-role)
 */
class EnsureUserHasRole
{
    /**
     * Idle timeout dalam menit. Sesuai source code lama (cek_session.php).
     */
    private const IDLE_TIMEOUT_MINUTES = 15;

    public function handle(Request $request, Closure $next, string ...$roles)
    {
        // 1. Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        // 2. Idle timeout check — tiru cek_session.php
        $lastActivity = Session::get('last_activity');
        $now = time();

        if ($lastActivity && ($now - $lastActivity) > (self::IDLE_TIMEOUT_MINUTES * 60)) {
            // Idle timeout terlampaui → logout paksa
            $this->logLogout();
            Auth::logout();
            Session::invalidate();
            Session::regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Sesi habis karena tidak aktif selama ' . self::IDLE_TIMEOUT_MINUTES . ' menit. Silakan login kembali.');
        }

        // Update last_activity
        Session::put('last_activity', $now);

        // Update user_logs.last_activity di database
        $this->updateLastActivity();

        // 3. Role check — hanya dieksekusi jika role tertentu diminta
        if (!empty($roles)) {
            $userRole = Auth::user()->role;
            if (!in_array($userRole, $roles)) {
                abort(403, 'Anda tidak memiliki akses ke halaman ini.');
            }
        }

        return $next($request);
    }

    /**
     * Update last_activity di tabel user_logs.
     * Meniru perilaku cek_session.php: UPDATE user_logs SET last_activity = NOW() WHERE id = ?
     */
    private function updateLastActivity(): void
    {
        $logId = Session::get('user_log_id');
        if ($logId) {
            \DB::table('user_logs')
                ->where('id', $logId)
                ->update(['last_activity' => now()]);
        }
    }

    /**
     * Log logout ke tabel user_logs.
     * Meniru perilaku logout.php: UPDATE user_logs SET logout_time = NOW() WHERE id = ?
     */
    private function logLogout(): void
    {
        $logId = Session::get('user_log_id');
        if ($logId) {
            \DB::table('user_logs')
                ->where('id', $logId)
                ->update(['logout_time' => now()]);
        }
    }
}