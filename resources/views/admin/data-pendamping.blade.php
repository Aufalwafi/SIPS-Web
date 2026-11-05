@extends('layouts.app')
@section('title', 'Data Pendamping - SI PS ONLINE')
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
.filter-toolbar input { min-width: 240px; }
.filter-toolbar input:focus, .filter-toolbar select:focus { border-color: #2e7d32; outline: none; }

.pd-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.pd-table th {
    background: #f8fafc; color: #475569; font-weight: 600; text-align: left;
    padding: 9px 12px; border-bottom: 2px solid #e2e8f0;
    font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px;
}
.pd-table td { padding: 9px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.pd-table tr:hover td { background: #f8fafc; }

.status-badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; }
.status-badge.aktif { background: #d1fae5; color: #065f46; }
.status-badge.nonaktif { background: #fee2e2; color: #7f1d1d; }

.action-btns { display: flex; gap: 5px; }
.btn-detail-pd { background: #e0f2f1; color: #00796b; border: 1px solid #b2dfdb; padding: 4px 10px; border-radius: 5px; font-size: 11px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
.btn-detail-pd:hover { background: #00796b; color: #fff; }
.btn-edit-pd { background: #e3f2fd; color: #1565c0; border: 1px solid #bbdefb; padding: 4px 10px; border-radius: 5px; font-size: 11px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
.btn-edit-pd:hover { background: #1565c0; color: #fff; }
.btn-hapus-pd { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; padding: 4px 10px; border-radius: 5px; font-size: 11px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; }
.btn-hapus-pd:hover { background: #b91c1c; color: #fff; }

.cetak-btns { display: flex; gap: 8px; }
.btn-cetak-link { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
.btn-cetak-link:hover { background: #475569; color: #fff; }

.alert-flash { padding: 10px 16px; border-radius: 6px; margin-bottom: 16px; font-size: 13px; display: flex; align-items: center; gap: 8px; border-left: 4px solid; }
.alert-flash.success { background: #d1fae5; color: #065f46; border-color: #22c55e; }
.alert-flash.error { background: #fee2e2; color: #7f1d1d; border-color: #ef4444; }

@media print {
    .page-header, .filter-toolbar, .cetak-btns, .action-btns { display: none; }
    .pd-table th, .pd-table td { border: 1px solid #000; padding: 6px; font-size: 10px; }
}
</style>
@endpush

@section('content')
<div class="content">

    @if(session('success'))
    <div class="alert-flash success"><i class="fas fa-check-circle"></i> {!! session('success') !!}</div>
    @endif
    @if(session('error'))
    <div class="alert-flash error"><i class="fas fa-exclamation-circle"></i> {!! session('error') !!}</div>
    @endif

    {{-- HEADER --}}
    <div class="page-header">
        <div>
            <h2><i class="fas fa-user-tie" style="color:#00796b"></i> Data Pendamping Perhutanan Sosial</h2>
            <p style="color:#64748b;font-size:12px;margin:4px 0 0">Daftar tenaga pendamping aktif beserta kelompok binaannya</p>
        </div>
        <div class="cetak-btns">
            <a href="{{ route('admin.data-pendamping.tambah') }}" style="background:#1b5e20;color:#fff;padding:6px 14px;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                <i class="fas fa-plus"></i> Tambah Pendamping
            </a>
            <a href="{{ route('admin.cetak-kelompok-kosong') }}" target="_blank" class="btn-cetak-link">
                <i class="fas fa-print"></i> Kelompok Tanpa Pendamping
            </a>
            <a href="{{ route('admin.cetak-rekap-pendamping') }}" target="_blank" class="btn-cetak-link">
                <i class="fas fa-print"></i> Rekap Pendamping
            </a>
        </div>
    </div>

    {{-- SUMMARY CHIPS --}}
    @php
        $totalPd = count($pendamping);
        $aktifCount = $pendamping->where('status', 'aktif')->count();
        $nonaktifCount = $pendamping->where('status', '!=', 'aktif')->count();
        $avgBinaan = $totalPd > 0 ? round($pendamping->sum('jumlah_kelompok') / $totalPd, 1) : 0;
    @endphp
    <div class="summary-chips">
        <div class="chip" style="border-left:4px solid #00796b;color:#00695c;background:#e0f2f1"><i class="fas fa-user-tie"></i> Total: {{ $totalPd }} Pendamping</div>
        <div class="chip" style="border-left:4px solid #22c55e;color:#065f46;background:#f0fdf4"><i class="fas fa-check-circle"></i> Aktif: {{ $aktifCount }}</div>
        <div class="chip" style="border-left:4px solid #ef4444;color:#7f1d1d;background:#fef2f2"><i class="fas fa-times-circle"></i> Nonaktif: {{ $nonaktifCount }}</div>
        <div class="chip" style="border-left:4px solid #f59e0b;color:#92400e;background:#fffbeb"><i class="fas fa-users"></i> Rata-rata Binaan: {{ $avgBinaan }} Kelompok</div>
    </div>

    {{-- FILTER --}}
    <div class="filter-toolbar">
        <input type="text" id="searchPd" placeholder="🔍 Cari nama pendamping / NIP / CDK...">
        <select id="filterStatus">
            <option value="">Semua Status</option>
            <option value="aktif">Aktif</option>
            <option value="nonaktif">Nonaktif</option>
        </select>
    </div>

    {{-- TABLE --}}
    <div style="background:#fff;border-radius:8px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,0.04);overflow-x:auto">
        <table class="pd-table" id="tabelPendamping">
            <thead>
                <tr>
                    <th style="width:40px;text-align:center">No</th>
                    <th>Nama Pendamping</th>
                    <th>NIP</th>
                    <th>Username</th>
                    <th>CDK</th>
                    <th style="text-align:center;width:90px">Status</th>
                    <th style="text-align:center;width:100px">Jml. Binaan</th>
                    <th style="width:200px;text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendamping as $i => $p)
                <tr data-status="{{ $p->status }}">
                    <td style="text-align:center;color:#94a3b8">{{ $i + 1 }}</td>
                    <td>
                        <a href="{{ Auth::user()->role === 'admin_cdk' ? route('admin-cdk.data-pendamping.detail', $p->id_pendamping) : route('admin.data-pendamping.detail', $p->id_pendamping) }}" style="color:#1e293b;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:5px">
                            {{ $p->nama_pendamping ?? '-' }}
                            <i class="fas fa-external-link-alt" style="font-size:9px;color:#94a3b8"></i>
                        </a>
                    </td>
                    <td style="color:#475569;font-family:monospace;font-size:11px">{{ $p->nip ?? '-' }}</td>
                    <td style="color:#64748b">{{ $p->username }}</td>
                    <td>
                        @if($p->nama_cdk)
                        <span style="background:#dbeafe;color:#1e40af;padding:2px 7px;border-radius:5px;font-size:11px;font-weight:600">{{ $p->nama_cdk }}</span>
                        @else
                        <span style="color:#94a3b8">—</span>
                        @endif
                    </td>
                    <td style="text-align:center">
                        <span class="status-badge {{ $p->status === 'aktif' ? 'aktif' : 'nonaktif' }}">
                            {{ ucfirst($p->status) }}
                        </span>
                    </td>
                    <td style="text-align:center">
                        @if(($p->jumlah_kelompok ?? 0) > 0)
                        <span style="background:#e8f5e9;color:#1b5e20;padding:2px 10px;border-radius:12px;font-size:12px;font-weight:700">{{ $p->jumlah_kelompok }}</span>
                        @else
                        <span style="color:#94a3b8;font-size:11px">0</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-btns">
                            <a href="{{ Auth::user()->role === 'admin_cdk' ? route('admin-cdk.data-pendamping.detail', $p->id_pendamping) : route('admin.data-pendamping.detail', $p->id_pendamping) }}" class="btn-detail-pd">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                            @if(Auth::user()->role === 'admin')
                            <a href="{{ route('admin.data-pendamping.edit', $p->id_pendamping) }}" class="btn-edit-pd">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('admin.data-pendamping.hapus', $p->id_pendamping) }}" style="display:inline" onsubmit="return confirm('Hapus pendamping {{ $p->nama_pendamping }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-hapus-pd"><i class="fas fa-trash"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:40px;color:#94a3b8">
                        <i class="fas fa-user-slash" style="font-size:28px;display:block;margin-bottom:8px"></i>
                        Data pendamping belum tersedia.
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
function filterPendamping() {
    var searchEl = document.getElementById('searchPd');
    var statusEl = document.getElementById('filterStatus');
    var keyword = searchEl ? searchEl.value.toLowerCase().trim() : '';
    var status = statusEl ? statusEl.value.toLowerCase().trim() : '';
    document.querySelectorAll('#tabelPendamping tbody tr').forEach(function(row) {
        var text = (row.textContent || row.innerText || '').toLowerCase();
        var rowStatus = (row.getAttribute('data-status') || '').toLowerCase();
        var matchText = !keyword || text.includes(keyword);
        var matchStatus = !status || rowStatus === status;
        var isMatch = matchText && matchStatus;
        row.style.display = isMatch ? '' : 'none';
        row.dataset.filterHidden = isMatch ? 'false' : 'true';
    });
    if (typeof window.refreshTablePagination === 'function') {
        window.refreshTablePagination(document.getElementById('tabelPendamping'));
    }
}
if (document.getElementById('searchPd')) {
    document.getElementById('searchPd').addEventListener('input', filterPendamping);
    document.getElementById('searchPd').addEventListener('keyup', filterPendamping);
}
if (document.getElementById('filterStatus')) {
    document.getElementById('filterStatus').addEventListener('change', filterPendamping);
}
</script>
@endpush
