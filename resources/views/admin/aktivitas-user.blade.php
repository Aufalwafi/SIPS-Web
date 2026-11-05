@extends('layouts.app')
@section('title', 'Monitoring Aktivitas User - SI PS ONLINE')
@push('styles')
<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px; }
.page-header h2 { color: #1b5e20; font-size: 20px; font-weight: 700; margin: 0; }

.filter-form-box { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 18px; margin-bottom: 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
.filter-row { display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end; }
.fg { display: flex; flex-direction: column; gap: 4px; }
.fg label { font-size: 11px; font-weight: 600; color: #475569; text-transform: uppercase; letter-spacing: 0.3px; }
.fg input, .fg select { height: 34px; padding: 4px 10px; font-size: 12px; border: 1px solid #cbd5e1; border-radius: 6px; background: #fff; color: #334155; }
.fg input:focus, .fg select:focus { border-color: #2e7d32; outline: none; }
.btn-filter-go { background: #2e7d32; color: #fff; border: none; padding: 7px 16px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; height: 34px; }
.btn-filter-go:hover { background: #1b5e20; }
.btn-export-xl { background: #1565c0; color: #fff; text-decoration: none; padding: 7px 14px; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; height: 34px; }
.btn-export-xl:hover { background: #0d47a1; }

.summary-chips { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 18px; }
.chip { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 9px 16px; font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 7px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }

.ak-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.ak-table th {
    background: #f8fafc; color: #475569; font-weight: 600; text-align: left;
    padding: 9px 12px; border-bottom: 2px solid #e2e8f0;
    font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px;
}
.ak-table td { padding: 9px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.ak-table tr:hover td { background: #f8fafc; }

.role-badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; }
.role-badge.admin { background: #d1fae5; color: #065f46; }
.role-badge.admin_cdk { background: #dbeafe; color: #1e40af; }
.role-badge.pendamping { background: #fef9c3; color: #713f12; }
.role-badge.kelompok { background: #ffe4e6; color: #9f1239; }

.status-online { display: inline-flex; align-items: center; gap: 5px; color: #065f46; font-weight: 600; font-size: 12px; }
.status-offline { display: inline-flex; align-items: center; gap: 5px; color: #64748b; font-size: 12px; }
.dot-on { width: 8px; height: 8px; border-radius: 50%; background: #22c55e; box-shadow: 0 0 4px #22c55e; display: inline-block; }
.dot-off { width: 8px; height: 8px; border-radius: 50%; background: #94a3b8; display: inline-block; }

.durasi-bar { display: flex; align-items: center; gap: 6px; }
.durasi-val { font-weight: 600; color: #1e293b; min-width: 30px; }
.durasi-bg { flex: 1; background: #f1f5f9; border-radius: 4px; height: 6px; max-width: 80px; }
.durasi-fill { height: 6px; border-radius: 4px; background: linear-gradient(90deg, #2e7d32, #22c55e); }
</style>
@endpush

@section('content')
<div class="content">

    {{-- HEADER --}}
    <div class="page-header">
        <div>
            <h2><i class="fas fa-clock-rotate-left" style="color:#1565c0"></i> Monitoring Aktivitas User</h2>
            <p style="color:#64748b;font-size:12px;margin:4px 0 0">Rekap sesi login dan aktivitas pengguna sistem</p>
        </div>
    </div>

    {{-- FILTER FORM --}}
    <div class="filter-form-box">
        <form method="GET">
            <div class="filter-row">
                <div class="fg">
                    <label>Dari Tanggal</label>
                    <input type="date" name="tgl_awal" value="{{ $tanggalAwal }}">
                </div>
                <div class="fg">
                    <label>Sampai Tanggal</label>
                    <input type="date" name="tgl_akhir" value="{{ $tanggalAkhir }}">
                </div>
                <div class="fg">
                    <label>Role User</label>
                    <select name="role">
                        <option value="">Semua Role</option>
                        <option value="admin" @selected($role === 'admin')>Admin Provinsi</option>
                        <option value="admin_cdk" @selected($role === 'admin_cdk')>Admin CDK</option>
                        <option value="pendamping" @selected($role === 'pendamping')>Pendamping</option>
                        <option value="kelompok" @selected($role === 'kelompok')>Kelompok</option>
                    </select>
                </div>
                <button type="submit" class="btn-filter-go"><i class="fas fa-filter"></i> Filter</button>
                <a href="{{ route('admin.aktivitas-user.export', ['tgl_awal' => $tanggalAwal, 'tgl_akhir' => $tanggalAkhir, 'role' => $role]) }}" class="btn-export-xl">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
            </div>
        </form>
    </div>

    {{-- SUMMARY CHIPS --}}
    @php
        $totalLogs = count($logs);
        $onlineCount = $logs->where('status', 'Online')->count();
        $offlineCount = $totalLogs - $onlineCount;
        $avgDurasi = $totalLogs > 0 ? round($logs->avg('durasi'), 1) : 0;
        $maxDurasi = $totalLogs > 0 ? $logs->max('durasi') : 0;
    @endphp
    <div class="summary-chips">
        <div class="chip" style="border-left:4px solid #1565c0;color:#1e40af;background:#eff6ff"><i class="fas fa-list"></i> Total Sesi: {{ $totalLogs }}</div>
        <div class="chip" style="border-left:4px solid #22c55e;color:#065f46;background:#f0fdf4"><span class="dot-on"></span> Online Sekarang: {{ $onlineCount }}</div>
        <div class="chip" style="border-left:4px solid #94a3b8;color:#475569;background:#f8fafc"><span class="dot-off"></span> Offline: {{ $offlineCount }}</div>
        <div class="chip" style="border-left:4px solid #f59e0b;color:#92400e;background:#fffbeb"><i class="fas fa-clock"></i> Rata-rata Durasi: {{ $avgDurasi }} menit</div>
    </div>

    {{-- TABLE --}}
    <div style="background:#fff;border-radius:8px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,0.04);overflow-x:auto">
        <table class="ak-table">
            <thead>
                <tr>
                    <th style="width:40px;text-align:center">No</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th style="width:120px;text-align:center">Role</th>
                    <th style="width:145px;text-align:center">Waktu Login</th>
                    <th style="width:145px;text-align:center">Waktu Logout</th>
                    <th style="width:130px">Durasi</th>
                    <th style="width:100px;text-align:center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $i => $log)
                <tr>
                    <td style="text-align:center;color:#94a3b8">{{ $i + 1 }}</td>
                    <td><strong style="color:#1e293b">{{ $log->nama ?? '-' }}</strong></td>
                    <td style="color:#475569;font-family:monospace;font-size:11px">{{ $log->username ?? '-' }}</td>
                    <td style="text-align:center">
                        @php $role_val = $log->role ?? ''; @endphp
                        <span class="role-badge {{ $role_val }}">
                            @if($role_val === 'admin') Admin
                            @elseif($role_val === 'admin_cdk') Admin CDK
                            @elseif($role_val === 'pendamping') Pendamping
                            @elseif($role_val === 'kelompok') Kelompok
                            @else {{ ucfirst($role_val) }}
                            @endif
                        </span>
                    </td>
                    <td style="text-align:center;font-size:11px;color:#475569">{{ $log->login_time ?? '—' }}</td>
                    <td style="text-align:center;font-size:11px;color:#475569">{{ $log->logout_time ?? '—' }}</td>
                    <td>
                        @php $dur = $log->durasi ?? 0; @endphp
                        <div class="durasi-bar">
                            <span class="durasi-val">{{ $dur }}m</span>
                            @if($maxDurasi > 0)
                            <div class="durasi-bg">
                                <div class="durasi-fill" style="width:{{ min(100, round($dur / $maxDurasi * 100)) }}%"></div>
                            </div>
                            @endif
                        </div>
                    </td>
                    <td style="text-align:center">
                        @if($log->status === 'Online')
                        <span class="status-online"><span class="dot-on"></span> Online</span>
                        @else
                        <span class="status-offline"><span class="dot-off"></span> Offline</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:40px;color:#94a3b8">
                        <i class="fas fa-clock" style="font-size:28px;display:block;margin-bottom:8px"></i>
                        Belum ada data aktivitas untuk periode yang dipilih.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
