@extends('layouts.app')
@section('title', 'Data User - SI PS ONLINE')
@push('styles')
<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px; }
.page-header h2 { color: #1b5e20; font-size: 20px; font-weight: 700; margin: 0; }

.summary-chips { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 18px; }
.chip { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 9px 16px; font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 7px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }

.filter-toolbar { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; margin-bottom: 16px; }
.filter-toolbar input, .filter-toolbar select {
    height: 34px; padding: 4px 10px; font-size: 12px;
    border: 1px solid #cbd5e1; border-radius: 6px; background: #fff; color: #334155;
}
.filter-toolbar input { min-width: 220px; }
.filter-toolbar input:focus, .filter-toolbar select:focus { border-color: #2e7d32; outline: none; }

.btn-tambah-user {
    background: #2e7d32; color: #fff; border: none; padding: 7px 16px;
    border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer;
    display: inline-flex; align-items: center; gap: 6px; text-decoration: none; height: 34px;
}
.btn-tambah-user:hover { background: #1b5e20; color: #fff; }
.btn-print-usr {
    background: #475569; color: #fff; border: none; padding: 6px 14px;
    border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer;
    display: inline-flex; align-items: center; gap: 6px; height: 34px;
}
.btn-print-usr:hover { background: #334155; }

.user-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.user-table th {
    background: #f8fafc; color: #475569; font-weight: 600; text-align: left;
    padding: 9px 12px; border-bottom: 2px solid #e2e8f0;
    font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px;
}
.user-table td { padding: 9px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.user-table tr:hover td { background: #f8fafc; }

.role-badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; }
.role-badge.admin { background: #d1fae5; color: #065f46; }
.role-badge.admin_cdk { background: #dbeafe; color: #1e40af; }
.role-badge.pendamping { background: #fef9c3; color: #713f12; }
.role-badge.kelompok { background: #ffe4e6; color: #9f1239; }

.action-btns { display: flex; gap: 5px; flex-wrap: wrap; }
.btn-edit-user { background: #f0fdf4; color: #2e7d32; border: 1px solid #bbf7d0; padding: 4px 10px; border-radius: 5px; font-size: 11px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
.btn-edit-user:hover { background: #2e7d32; color: #fff; }
.btn-reset-user { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; padding: 4px 10px; border-radius: 5px; font-size: 11px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; }
.btn-reset-user:hover { background: #c2410c; color: #fff; }
.btn-hapus-user { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; padding: 4px 10px; border-radius: 5px; font-size: 11px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; }
.btn-hapus-user:hover { background: #b91c1c; color: #fff; }

.alert-flash { padding: 10px 16px; border-radius: 6px; margin-bottom: 16px; font-size: 13px; display: flex; align-items: center; gap: 8px; border-left: 4px solid; }
.alert-flash.success { background: #d1fae5; color: #065f46; border-color: #22c55e; }
.alert-flash.error { background: #fee2e2; color: #7f1d1d; border-color: #ef4444; }

.print-title { display: none; }
@media print {
    .page-header, .filter-toolbar, .action-btns, .summary-chips { display: none; }
    .print-title { display: block; text-align: center; margin-bottom: 16px; font-size: 18px; }
    .user-table th, .user-table td { border: 1px solid #000; padding: 6px; font-size: 10px; }
}
</style>
@endpush

@section('content')
<div class="content">

    <h2 class="print-title">Data User — SI PS ONLINE</h2>

    @if(session('success'))
    <div class="alert-flash success"><i class="fas fa-check-circle"></i> {!! session('success') !!}</div>
    @endif
    @if(session('error'))
    <div class="alert-flash error"><i class="fas fa-exclamation-circle"></i> {!! session('error') !!}</div>
    @endif

    {{-- HEADER --}}
    <div class="page-header">
        <div>
            <h2><i class="fas fa-users-gear" style="color:#1565c0"></i> Manajemen User</h2>
            <p style="color:#64748b;font-size:12px;margin:4px 0 0">Kelola seluruh akun pengguna sistem SIPSONLINE</p>
        </div>
        <div style="display:flex;gap:8px">
            <a href="{{ route('admin.data-user.create') }}" class="btn-tambah-user">
                <i class="fas fa-plus"></i> Tambah User
            </a>
            <button onclick="window.print()" class="btn-print-usr"><i class="fas fa-print"></i> Cetak</button>
        </div>
    </div>

    {{-- SUMMARY CHIPS --}}
    @php
        $totalAll = count($query);
        $countAdmin = \DB::table('users')->where('role','admin')->count();
        $countCdk = \DB::table('users')->where('role','admin_cdk')->count();
        $countPendamping = \DB::table('users')->where('role','pendamping')->count();
        $countKelompok = \DB::table('users')->where('role','kelompok')->count();
    @endphp
    <div class="summary-chips">
        <div class="chip" style="border-left:4px solid #2e7d32;color:#065f46;background:#f0fdf4"><i class="fas fa-shield-halved"></i> Admin: {{ $countAdmin }}</div>
        <div class="chip" style="border-left:4px solid #1565c0;color:#1e40af;background:#eff6ff"><i class="fas fa-building-user"></i> Admin CDK: {{ $countCdk }}</div>
        <div class="chip" style="border-left:4px solid #eab308;color:#713f12;background:#fefce8"><i class="fas fa-user-tie"></i> Pendamping: {{ $countPendamping }}</div>
        <div class="chip" style="border-left:4px solid #e11d48;color:#9f1239;background:#fff1f2"><i class="fas fa-users"></i> Kelompok: {{ $countKelompok }}</div>
    </div>

    {{-- FILTER --}}
    <form method="GET" id="formFilter">
        <div class="filter-toolbar">
            <input type="text" name="cari" placeholder="&#128269; Cari nama / username..." value="{{ $cari }}">
            <select name="role" onchange="document.getElementById('formFilter').submit()">
                <option value="">Semua Role</option>
                <option value="admin" @selected($role === 'admin')>Admin Provinsi</option>
                <option value="admin_cdk" @selected($role === 'admin_cdk')>Admin CDK</option>
                <option value="pendamping" @selected($role === 'pendamping')>Pendamping</option>
                <option value="kelompok" @selected($role === 'kelompok')>Kelompok</option>
            </select>
        </div>
    </form>

    {{-- TABLE --}}
    <div style="background:#fff;border-radius:8px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,0.04);overflow-x:auto">
        <table class="user-table">
            <thead>
                <tr>
                    <th style="width:40px;text-align:center">No</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th style="text-align:center;width:130px">Role</th>
                    <th>Wilayah / Entitas</th>
                    <th style="width:180px;text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($query as $i => $u)
                <tr>
                    <td style="text-align:center;color:#94a3b8">{{ $i + 1 }}</td>
                    <td>
                        <strong style="color:#1e293b">{{ $u->nama }}</strong>
                    </td>
                    <td style="color:#475569;font-family:monospace">{{ $u->username }}</td>
                    <td style="text-align:center">
                        <span class="role-badge {{ $u->role }}">
                            @if($u->role === 'admin') <i class="fas fa-shield-halved"></i> Admin Provinsi
                            @elseif($u->role === 'admin_cdk') <i class="fas fa-building-user"></i> Admin CDK
                            @elseif($u->role === 'pendamping') <i class="fas fa-user-tie"></i> Pendamping
                            @elseif($u->role === 'kelompok') <i class="fas fa-users"></i> Kelompok
                            @else {{ ucfirst($u->role) }}
                            @endif
                        </span>
                    </td>
                    <td style="color:#64748b;font-size:12px">
                        @if($u->role === 'admin_cdk' && $u->nama_cdk)
                            <span style="background:#dbeafe;color:#1e40af;padding:2px 7px;border-radius:5px;font-size:11px">{{ $u->nama_cdk }}</span>
                        @elseif($u->role === 'kelompok' && $u->nama_kelompok)
                            <span style="background:#ffe4e6;color:#9f1239;padding:2px 7px;border-radius:5px;font-size:11px">{{ $u->nama_kelompok }}</span>
                        @elseif($u->role === 'pendamping')
                            <span style="color:#94a3b8;font-size:11px">Pendamping</span>
                        @else
                            <span style="color:#94a3b8">—</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-btns">
                            <a href="{{ route('admin.data-user.edit', $u->id) }}" class="btn-edit-user">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('admin.data-user.reset-password', $u->id) }}" style="display:inline" onsubmit="return confirm('Reset password {{ $u->username }} ke 123456?')">
                                @csrf
                                <button type="submit" class="btn-reset-user"><i class="fas fa-key"></i> Reset</button>
                            </form>
                            <form method="POST" action="{{ route('admin.data-user.delete', $u->id) }}" style="display:inline" onsubmit="return confirm('Hapus akun {{ $u->username }}? Tindakan ini tidak bisa dibatalkan.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-hapus-user"><i class="fas fa-trash"></i> Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:#94a3b8">
                        <i class="fas fa-users-slash" style="font-size:28px;display:block;margin-bottom:8px"></i>
                        Tidak ada user yang sesuai pencarian.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
var form = document.getElementById('formFilter');
var t;
form.querySelector('input[name=cari]').addEventListener('keyup', function() {
    clearTimeout(t);
    t = setTimeout(function() { form.submit(); }, 500);
});
</script>
@endpush
