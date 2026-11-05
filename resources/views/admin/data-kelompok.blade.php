@extends('layouts.app')
@section('title', 'Data Kelompok - SI PS ONLINE')
@push('styles')
<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px; }
.page-header h2 { color: #1b5e20; font-size: 20px; font-weight: 700; margin: 0; }

.filter-toolbar { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; margin-bottom: 16px; }
.filter-toolbar input, .filter-toolbar select {
    height: 34px; padding: 4px 10px; font-size: 12px;
    border: 1px solid #cbd5e1; border-radius: 6px; background: #fff; color: #334155;
}
.filter-toolbar input { min-width: 280px; }
.filter-toolbar input:focus, .filter-toolbar select:focus { border-color: #2e7d32; outline: none; }

.klp-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.klp-table th {
    background: #f8fafc; color: #475569; font-weight: 600; text-align: left;
    padding: 9px 12px; border-bottom: 2px solid #e2e8f0;
    font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px;
    white-space: nowrap;
}
.klp-table td { padding: 9px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.klp-table tr:hover td { background: #f8fafc; }

.nama-klp-link { color: #1e293b; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
.nama-klp-link:hover { color: #2e7d32; text-decoration: underline; }

.skema-badge { display: inline-block; padding: 2px 7px; border-radius: 4px; background: #e8f5e9; color: #1b5e20; font-size: 11px; font-weight: 600; }

.btn-detail-klp { background: #e8f5e9; color: #1b5e20; border: 1px solid #c8e6c9; padding: 4px 8px; border-radius: 5px; font-size: 11px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
.btn-detail-klp:hover { background: #1b5e20; color: #fff; border-color: #1b5e20; }

.btn-edit-klp { background: #e3f2fd; color: #1565c0; border: 1px solid #bbdefb; padding: 4px 8px; border-radius: 5px; font-size: 11px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
.btn-edit-klp:hover { background: #1565c0; color: #fff; }

.btn-hapus-klp { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; padding: 4px 8px; border-radius: 5px; font-size: 11px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; }
.btn-hapus-klp:hover { background: #b91c1c; color: #fff; }

.btn-cetak-klp { background: #475569; color: #fff; border: none; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; height: 34px; }
.btn-cetak-klp:hover { background: #334155; }

.alert-flash { padding: 10px 16px; border-radius: 6px; margin-bottom: 16px; font-size: 13px; display: flex; align-items: center; gap: 8px; border-left: 4px solid; }
.alert-flash.success { background: #d1fae5; color: #065f46; border-color: #22c55e; }
.alert-flash.error { background: #fee2e2; color: #7f1d1d; border-color: #ef4444; }

@media print {
    .page-header, .filter-toolbar { display: none; }
    .klp-table th, .klp-table td { border: 1px solid #000; padding: 5px; font-size: 9px; }
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
            <h2><i class="fas fa-users" style="color:#2e7d32"></i> Data Kelompok Perhutanan Sosial</h2>
            <p style="color:#64748b;font-size:12px;margin:4px 0 0">Total: <strong>{{ number_format($kelompok->count()) }}</strong> kelompok terdaftar</p>
        </div>
        <div style="display:flex;gap:8px">
            @if(Auth::user()->role === 'admin')
            <a href="{{ route('admin.data-kelompok.tambah') }}" class="btn btn-tambah"><i class="fas fa-plus"></i> Tambah Kelompok</a>
            @endif
            <button onclick="window.print()" class="btn-cetak-klp"><i class="fas fa-print"></i> Cetak</button>
        </div>
    </div>

    {{-- FILTER --}}
    <div class="filter-toolbar">
        <input type="text" id="search" placeholder="🔍 Cari nama kelompok / ketua / desa / kecamatan / kabupaten...">
        <select id="filterSkema">
            <option value="">Semua Skema</option>
            @foreach($kelompok->pluck('skema_ps')->unique()->filter()->sort() as $skema)
            <option value="{{ $skema }}">{{ $skema }}</option>
            @endforeach
        </select>
    </div>

    {{-- TABLE --}}
    <div style="background:#fff;border-radius:8px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,0.04);overflow-x:auto">
        <table class="klp-table" id="tabelData">
            <thead>
                <tr>
                    <th style="width:35px;text-align:center">No</th>
                    <th style="min-width:180px">Nama Kelompok</th>
                    <th style="min-width:110px">Ketua</th>
                    <th>Desa</th>
                    <th>Kecamatan</th>
                    <th>Kabupaten</th>
                    <th style="width:90px;text-align:center">Skema</th>
                    <th style="text-align:right;width:105px">Luas Lahan</th>
                    <th style="width:75px;text-align:center">Peta</th>
                    <th style="width:140px;text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kelompok as $i => $k)
                @php
                    $detailUrl = Auth::user()->role === 'admin_cdk' ? route('admin-cdk.data-kelompok.detail', $k->id) : route('admin.data-kelompok.detail', $k->id);
                    $editUrl = Auth::user()->role === 'admin_cdk' ? route('admin-cdk.data-kelompok.edit', $k->id) : route('admin.data-kelompok.edit', $k->id);
                @endphp
                <tr data-skema="{{ $k->skema_ps }}">
                    <td style="text-align:center;color:#94a3b8">{{ $i + 1 }}</td>
                    <td>
                        <a href="{{ $detailUrl }}" class="nama-klp-link" title="Buka Profil Detail Kelompok">
                            <i class="fas fa-users" style="color:#2e7d32;font-size:11px"></i>
                            {{ $k->nama_kelompok ?? '-' }}
                        </a>
                    </td>
                    <td style="color:#475569">{{ $k->ketua ?? '-' }}</td>
                    <td style="color:#64748b">{{ $k->desa ?? '-' }}</td>
                    <td style="color:#64748b">{{ $k->kecamatan ?? '-' }}</td>
                    <td style="color:#64748b">{{ $k->kabupaten ?? '-' }}</td>
                    <td style="text-align:center">
                        @if(!empty($k->skema_ps))
                        <span class="skema-badge">{{ $k->skema_ps }}</span>
                        @else
                        <span style="color:#94a3b8">—</span>
                        @endif
                    </td>
                    <td style="text-align:right;font-weight:600;color:#2e7d32">
                        {{ $k->luas_lahan !== null ? number_format($k->luas_lahan, 2, ',', '.') . ' Ha' : '—' }}
                    </td>
                    <td style="text-align:center">
                        @if(!empty($k->polygon_kelompok))
                        <span style="background:#dcfce7;color:#166534;padding:2px 7px;border-radius:4px;font-size:10px;font-weight:700;display:inline-flex;align-items:center;gap:3px;border:1px solid #bbf7d0" title="Peta Poligon Tersedia">
                            <i class="fas fa-map-marker-alt"></i> Ada
                        </span>
                        @else
                        <span style="color:#cbd5e1;font-size:11px" title="Belum Ada Peta">—</span>
                        @endif
                    </td>
                    <td style="text-align:center">
                        <div style="display:flex;gap:4px;justify-content:center">
                            <a href="{{ $detailUrl }}" class="btn-detail-klp" title="Lihat Profil Lengkap">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                            <a href="{{ $editUrl }}" class="btn-edit-klp" title="Edit Data & Peta">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            @if(Auth::user()->role === 'admin')
                            <form method="POST" action="{{ route('admin.data-kelompok.hapus', $k->id) }}" style="display:inline" onsubmit="return confirm('Hapus kelompok {{ $k->nama_kelompok }}? Data terkait (anggota, laporan, pengajuan) akan terpengaruh.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-hapus-klp" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" style="text-align:center;padding:40px;color:#94a3b8">
                        <i class="fas fa-folder-open" style="font-size:28px;display:block;margin-bottom:8px"></i>
                        Data kelompok belum tersedia.
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
function filterKelompok() {
    var searchEl = document.getElementById('search');
    var skemaEl = document.getElementById('filterSkema');
    var keyword = searchEl ? searchEl.value.toLowerCase().trim() : '';
    var skema = skemaEl ? skemaEl.value.toLowerCase().trim() : '';
    document.querySelectorAll('#tabelData tbody tr').forEach(function(row) {
        var text = (row.textContent || row.innerText || '').toLowerCase();
        var rowSkema = (row.getAttribute('data-skema') || '').toLowerCase();
        var matchText = !keyword || text.includes(keyword);
        var matchSkema = !skema || rowSkema === skema;
        var isMatch = matchText && matchSkema;
        row.style.display = isMatch ? '' : 'none';
        row.dataset.filterHidden = isMatch ? 'false' : 'true';
    });
    if (typeof window.refreshTablePagination === 'function') {
        window.refreshTablePagination(document.getElementById('tabelData'));
    }
}
if (document.getElementById('search')) {
    document.getElementById('search').addEventListener('input', filterKelompok);
    document.getElementById('search').addEventListener('keyup', filterKelompok);
}
if (document.getElementById('filterSkema')) {
    document.getElementById('filterSkema').addEventListener('change', filterKelompok);
}
</script>
@endpush
