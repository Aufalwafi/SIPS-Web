@extends('layouts.app')
@section('title', 'Data Anggota Kelompok - SI PS ONLINE')
@push('styles')
<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px; }
.page-header h2 { color: #1b5e20; font-size: 20px; font-weight: 700; margin: 0; }

.filter-toolbar { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; margin-bottom: 16px; }
.filter-toolbar input, .filter-toolbar select {
    height: 34px; padding: 4px 10px; font-size: 12px;
    border: 1px solid #cbd5e1; border-radius: 6px; background: #fff; color: #334155;
}
.filter-toolbar input { min-width: 220px; }
.filter-toolbar input:focus, .filter-toolbar select:focus { border-color: #2e7d32; outline: none; }
.btn-print-ag {
    background: #475569; color: #fff; border: none; padding: 6px 14px;
    border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer;
    display: inline-flex; align-items: center; gap: 6px; height: 34px; margin-left: auto;
}
.btn-print-ag:hover { background: #334155; }

.ag-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.ag-table th {
    background: #f8fafc; color: #475569; font-weight: 600; text-align: left;
    padding: 9px 12px; border-bottom: 2px solid #e2e8f0;
    font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px;
}
.ag-table td { padding: 9px 12px; border-bottom: 1px solid #f1f5f9; color: #334155; }
.ag-table tr:hover td { background: #f8fafc; }

.jk-badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; }
.jk-laki { background: #e3f2fd; color: #1565c0; }
.jk-perempuan { background: #fce4ec; color: #880e4f; }

@media print {
    .page-header, .filter-toolbar { display: none; }
    .ag-table th, .ag-table td { border: 1px solid #000; padding: 6px; font-size: 10px; }
}
</style>
@endpush

@section('content')
<div class="content">

    {{-- HEADER --}}
    <div class="page-header">
        <div>
            <h2><i class="fas fa-user-friends" style="color:#7b1fa2"></i> Data Anggota Kelompok</h2>
            <p style="color:#64748b;font-size:12px;margin:4px 0 0">Daftar seluruh anggota aktif kelompok Perhutanan Sosial</p>
        </div>
        <button onclick="window.print()" class="btn-print-ag"><i class="fas fa-print"></i> Cetak</button>
    </div>

    {{-- FILTER --}}
    <div class="filter-toolbar">
        <input type="text" id="search" placeholder="🔍 Cari nama / NIK anggota...">
        <select id="filterKelompok">
            <option value="">Semua Kelompok</option>
            @foreach($daftarKelompok as $k)
            <option value="{{ $k->nama_kelompok }}">{{ $k->nama_kelompok }}</option>
            @endforeach
        </select>
        <select id="filterJK">
            <option value="">Semua Jenis Kelamin</option>
            <option value="Laki-laki">Laki-laki</option>
            <option value="Perempuan">Perempuan</option>
        </select>
    </div>

    {{-- TABLE --}}
    <div style="background:#fff;border-radius:8px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,0.04);overflow-x:auto">
        <table class="ag-table" id="tabelData">
            <thead>
                <tr>
                    <th style="width:40px;text-align:center">No</th>
                    <th>Nama Anggota</th>
                    <th style="width:160px">NIK</th>
                    <th style="text-align:center;width:110px">Jenis Kelamin</th>
                    <th>Kelompok</th>
                    <th style="text-align:right;width:110px">Luas Garapan</th>
                    <th>Komoditas Utama</th>
                </tr>
            </thead>
            <tbody>
                @forelse($anggota as $i => $a)
                <tr>
                    <td style="text-align:center;color:#94a3b8">{{ $i + 1 }}</td>
                    <td><strong style="color:#1e293b">{{ $a->nama_anggota ?? '-' }}</strong></td>
                    <td style="font-family:monospace;font-size:11px;color:#475569">{{ $a->nik ?? '-' }}</td>
                    <td style="text-align:center">
                        @if($a->jenis_kelamin === 'Laki-laki' || $a->jenis_kelamin === 'L')
                            <span class="jk-badge jk-laki"><i class="fas fa-mars"></i> L</span>
                        @elseif($a->jenis_kelamin === 'Perempuan' || $a->jenis_kelamin === 'P')
                            <span class="jk-badge jk-perempuan"><i class="fas fa-venus"></i> P</span>
                        @else
                            <span style="color:#94a3b8">-</span>
                        @endif
                    </td>
                    <td>{{ $a->nama_kelompok ?? '-' }}</td>
                    <td style="text-align:right;font-weight:600;color:#2e7d32">
                        {{ $a->luas_garapan ? number_format($a->luas_garapan, 2, ',', '.') . ' ha' : '-' }}
                    </td>
                    <td>
                        @if($a->komoditas_utama)
                        <span style="background:#f1f5f9;padding:2px 8px;border-radius:10px;font-size:11px;color:#475569">{{ $a->komoditas_utama }}</span>
                        @else -
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:#94a3b8">
                        <i class="fas fa-users-slash" style="font-size:28px;display:block;margin-bottom:8px"></i>
                        Data anggota belum tersedia.
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
function filterAnggota() {
    var searchEl = document.getElementById('search');
    var kelompokEl = document.getElementById('filterKelompok');
    var jkEl = document.getElementById('filterJK');
    var keyword = searchEl ? searchEl.value.toLowerCase().trim() : '';
    var kelompok = kelompokEl ? kelompokEl.value.toLowerCase().trim() : '';
    var jk = jkEl ? jkEl.value.toLowerCase().trim() : '';
    document.querySelectorAll('#tabelData tbody tr').forEach(function(row) {
        var nama = (row.cells[1]?.textContent || row.cells[1]?.innerText || '').toLowerCase();
        var nik = (row.cells[2]?.textContent || row.cells[2]?.innerText || '').toLowerCase();
        var jkCell = (row.cells[3]?.textContent || row.cells[3]?.innerText || '').toLowerCase();
        var klpCell = (row.cells[4]?.textContent || row.cells[4]?.innerText || '').toLowerCase();
        var match = (!keyword || nama.includes(keyword) || nik.includes(keyword))
            && (!kelompok || klpCell.includes(kelompok))
            && (!jk || jkCell.includes(jk));
        row.style.display = match ? '' : 'none';
        row.dataset.filterHidden = match ? 'false' : 'true';
    });
    if (typeof window.refreshTablePagination === 'function') {
        window.refreshTablePagination(document.getElementById('tabelData'));
    }
}
if (document.getElementById('search')) {
    document.getElementById('search').addEventListener('input', filterAnggota);
    document.getElementById('search').addEventListener('keyup', filterAnggota);
}
if (document.getElementById('filterKelompok')) {
    document.getElementById('filterKelompok').addEventListener('change', filterAnggota);
}
if (document.getElementById('filterJK')) {
    document.getElementById('filterJK').addEventListener('change', filterAnggota);
}
</script>
@endpush
