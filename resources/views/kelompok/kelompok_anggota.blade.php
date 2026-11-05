@extends('layouts.app')
@section('title', 'Anggota Kelompok - SI PS ONLINE')
@push('styles')
<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px; }
.page-header h2 { color: #1b5e20; font-size: 20px; font-weight: 700; margin: 0; }

.filter-toolbar { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; margin-bottom: 16px; }
.filter-toolbar input, .filter-toolbar select {
    height: 34px; padding: 4px 10px; font-size: 12px;
    border: 1px solid #cbd5e1; border-radius: 6px; background: #fff; color: #334155;
}
.filter-toolbar input { min-width: 240px; }
.filter-toolbar input:focus, .filter-toolbar select:focus { border-color: #2e7d32; outline: none; }

.ag-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.ag-table th {
    background: #f8fafc; color: #475569; font-weight: 600; text-align: left;
    padding: 10px 12px; border-bottom: 2px solid #e2e8f0;
    font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px;
}
.ag-table td { padding: 9px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.ag-table tr:hover td { background: #f8fafc; }

.jk-badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; }
.jk-laki { background: #e3f2fd; color: #1565c0; }
.jk-perempuan { background: #fce4ec; color: #880e4f; }

.btn-act { padding: 4px 8px; border-radius: 5px; font-size: 11px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
.btn-act-edit { background: #e3f2fd; color: #1565c0; border: 1px solid #bbdefb; }
.btn-act-edit:hover { background: #1565c0; color: #fff; }
.btn-act-del { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; cursor: pointer; }
.btn-act-del:hover { background: #b91c1c; color: #fff; }
</style>
@endpush

@section('content')
<div class="content">
    @if(session('success'))
    <div class="alert" style="background:#d1fae5;color:#065f46;padding:10px 16px;border-radius:6px;margin-bottom:16px;border-left:4px solid #22c55e">
        <i class="fas fa-check-circle"></i> {!! session('success') !!}
    </div>
    @endif
    @if(session('error'))
    <div class="alert-error" style="background:#fee2e2;color:#7f1d1d;padding:10px 16px;border-radius:6px;margin-bottom:16px;border-left:4px solid #ef4444">
        <i class="fas fa-exclamation-circle"></i> {!! session('error') !!}
    </div>
    @endif

    {{-- HEADER --}}
    <div class="page-header">
        <div>
            <h2><i class="fas fa-user-friends" style="color:#1b5e20"></i> Anggota Kelompok</h2>
            <p style="color:#64748b;font-size:12px;margin:4px 0 0">Total: <strong>{{ count($anggota) }}</strong> anggota petani terdaftar</p>
        </div>
        <a href="{{ route('kelompok.anggota.tambah') }}" class="btn btn-save" style="font-size:12px;height:34px;padding:0 14px">
            <i class="fas fa-user-plus"></i> Tambah Anggota
        </a>
    </div>

    {{-- FILTER --}}
    <div class="filter-toolbar">
        <input type="text" id="searchAg" placeholder="🔍 Cari nama / NIK / alamat / komoditas...">
        <select id="filterJK">
            <option value="">Semua Jenis Kelamin</option>
            <option value="Laki-laki">Laki-laki</option>
            <option value="Perempuan">Perempuan</option>
        </select>
    </div>

    {{-- TABLE --}}
    <div style="background:#fff;border-radius:8px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,0.04);overflow-x:auto">
        <table class="ag-table" id="tabelAnggota">
            <thead>
                <tr>
                    <th style="width:40px;text-align:center">No</th>
                    <th>Nama Anggota</th>
                    <th>NIK</th>
                    <th style="text-align:center;width:80px">JK</th>
                    <th>Alamat</th>
                    <th style="text-align:right;width:110px">Luas Garapan</th>
                    <th>Komoditas Utama</th>
                    <th style="width:130px;text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($anggota as $i => $a)
                <tr data-jk="{{ $a->jenis_kelamin ?? '' }}">
                    <td style="text-align:center;color:#94a3b8">{{ $i + 1 }}</td>
                    <td><strong style="color:#1e293b">{{ $a->nama_anggota ?? '-' }}</strong></td>
                    <td style="font-family:monospace;color:#475569;font-size:11px">{{ $a->nik ?? '-' }}</td>
                    <td style="text-align:center">
                        @if($a->jenis_kelamin === 'Laki-laki' || $a->jenis_kelamin === 'L')
                            <span class="jk-badge jk-laki">L</span>
                        @elseif($a->jenis_kelamin === 'Perempuan' || $a->jenis_kelamin === 'P')
                            <span class="jk-badge jk-perempuan">P</span>
                        @else
                            <span style="color:#94a3b8">-</span>
                        @endif
                    </td>
                    <td style="color:#64748b">{{ $a->alamat ?? '-' }}</td>
                    <td style="text-align:right;font-weight:600;color:#2e7d32">
                        {{ $a->luas_garapan ? number_format($a->luas_garapan, 2, ',', '.') . ' ha' : '—' }}
                    </td>
                    <td>
                        @if($a->komoditas_utama)
                        <span style="background:#f1f5f9;color:#475569;padding:2px 8px;border-radius:10px;font-size:11px">{{ $a->komoditas_utama }}</span>
                        @else — @endif
                    </td>
                    <td style="text-align:center">
                        <div style="display:flex;gap:4px;justify-content:center">
                            <a href="{{ route('kelompok.anggota.edit', $a->id) }}" class="btn-act btn-act-edit" title="Edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('kelompok.anggota.hapus', $a->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Yakin ingin menghapus anggota ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-act btn-act-del" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:40px;color:#94a3b8">
                        <i class="fas fa-users-slash" style="font-size:28px;display:block;margin-bottom:8px"></i>
                        Belum ada data anggota.
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
    var searchEl = document.getElementById('searchAg');
    var jkEl = document.getElementById('filterJK');
    var query = searchEl ? searchEl.value.toLowerCase().trim() : '';
    var jk = jkEl ? jkEl.value.toLowerCase().trim() : '';
    document.querySelectorAll('#tabelAnggota tbody tr').forEach(function(row) {
        var text = (row.textContent || row.innerText || '').toLowerCase();
        var rowJk = (row.getAttribute('data-jk') || '').toLowerCase();
        var mText = !query || text.includes(query);
        var mJk = !jk || rowJk.includes(jk);
        var isMatch = mText && mJk;
        row.style.display = isMatch ? '' : 'none';
        row.dataset.filterHidden = isMatch ? 'false' : 'true';
    });
    if (typeof window.refreshTablePagination === 'function') {
        window.refreshTablePagination(document.getElementById('tabelAnggota'));
    }
}
if (document.getElementById('searchAg')) {
    document.getElementById('searchAg').addEventListener('input', filterAnggota);
    document.getElementById('searchAg').addEventListener('keyup', filterAnggota);
}
if (document.getElementById('filterJK')) {
    document.getElementById('filterJK').addEventListener('change', filterAnggota);
}
</script>
@endpush
