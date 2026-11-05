@extends('layouts.app')
@section('title', 'Anggota Pendampingan - SI PS ONLINE')
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
</style>
@endpush

@section('content')
<div class="content">
    @if(session('success'))
    <div class="alert" style="background:#d1fae5;color:#065f46;padding:10px 16px;border-radius:6px;margin-bottom:16px;border-left:4px solid #22c55e">
        <i class="fas fa-check-circle"></i> {!! session('success') !!}
    </div>
    @endif

    {{-- HEADER --}}
    <div class="page-header">
        <div>
            <h2><i class="fas fa-user-friends" style="color:#1b5e20"></i> Anggota Kelompok Dampingan</h2>
            <p style="color:#64748b;font-size:12px;margin:4px 0 0">Total: <strong>{{ count($anggota) }}</strong> petani binaan terdaftar</p>
        </div>
        @if(isset($kelompokList) && $kelompokList->count() > 0)
            @if($kelompokList->count() === 1)
                <a href="{{ route('pendamping.kelompok-saya.anggota.tambah', $kelompokList->first()->id) }}" class="btn btn-save" style="font-size:13px;height:36px;padding:0 16px;display:inline-flex;align-items:center;gap:6px">
                    <i class="fas fa-user-plus"></i> Tambah Anggota
                </a>
            @else
                <button type="button" class="btn btn-save" onclick="document.getElementById('modalPilihKelompokAnggota').style.display='flex'" style="font-size:13px;height:36px;padding:0 16px;display:inline-flex;align-items:center;gap:6px;cursor:pointer">
                    <i class="fas fa-user-plus"></i> Tambah Anggota <i class="fas fa-chevron-down" style="font-size:10px;margin-left:4px"></i>
                </button>
            @endif
        @endif
    </div>

    {{-- FILTER --}}
    <div class="filter-toolbar">
        <input type="text" id="searchAg" placeholder="🔍 Cari nama / NIK / alamat / komoditas...">
        <select id="filterKelompok">
            <option value="">Semua Kelompok</option>
            @foreach($anggota->pluck('nama_kelompok')->unique()->filter() as $klp)
            <option value="{{ $klp }}">{{ $klp }}</option>
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
        <table class="ag-table" id="tabelAnggota">
            <thead>
                <tr>
                    <th style="width:40px;text-align:center">No</th>
                    <th>Nama Anggota</th>
                    <th>NIK</th>
                    <th style="text-align:center;width:90px">JK</th>
                    <th>Alamat</th>
                    <th style="text-align:right;width:110px">Luas Garapan</th>
                    <th>Komoditas</th>
                    <th>Kelompok</th>
                    <th style="text-align:center;width:90px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($anggota as $i => $a)
                <tr data-kelompok="{{ $a->nama_kelompok ?? '' }}" data-jk="{{ $a->jenis_kelamin ?? '' }}">
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
                    <td style="color:#64748b;max-width:200px">{{ $a->alamat ?? '-' }}</td>
                    <td style="text-align:right;font-weight:600;color:#2e7d32">
                        {{ $a->luas_garapan ? number_format($a->luas_garapan, 2, ',', '.') . ' ha' : '-' }}
                    </td>
                    <td>
                        @if($a->komoditas_utama)
                        <span style="background:#f1f5f9;color:#475569;padding:2px 8px;border-radius:10px;font-size:11px">{{ $a->komoditas_utama }}</span>
                        @else — @endif
                    </td>
                    <td><span style="background:#e8f5e9;color:#1b5e20;padding:2px 7px;border-radius:4px;font-size:11px;font-weight:600">{{ $a->nama_kelompok ?? '-' }}</span></td>
                    <td style="text-align:center">
                        <div style="display:inline-flex;gap:4px">
                            <a href="{{ route('pendamping.kelompok-saya.anggota', $a->id_kelompok) }}" title="Buka Anggota Kelompok" class="btn btn-outline" style="padding:4px 8px;font-size:11px;border-radius:4px;color:#1b5e20">
                                <i class="fas fa-users"></i>
                            </a>
                            <form method="POST" action="{{ route('pendamping.kelompok-saya.anggota.hapus', ['id' => $a->id_kelompok, 'idAnggota' => $a->id]) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus anggota ini?')" style="display:inline;margin:0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Hapus Anggota" style="background:#fee2e2;border:1px solid #fecaca;color:#dc2626;padding:4px 8px;border-radius:4px;font-size:11px;cursor:pointer">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:48px 20px;color:#94a3b8">
                        <i class="fas fa-user-slash" style="font-size:36px;display:block;margin-bottom:12px;color:#cbd5e1"></i>
                        <p style="font-size:14px;font-weight:600;color:#64748b;margin:0 0 6px">Belum ada data anggota</p>
                        <p style="font-size:12px;color:#94a3b8;margin:0 0 16px">Anda belum mendaftarkan anggota petani untuk kelompok binaan Anda.</p>
                        @if(isset($kelompokList) && $kelompokList->count() > 0)
                            @if($kelompokList->count() === 1)
                                <a href="{{ route('pendamping.kelompok-saya.anggota.tambah', $kelompokList->first()->id) }}" class="btn btn-save" style="font-size:12px;padding:8px 18px;display:inline-flex;align-items:center;gap:6px">
                                    <i class="fas fa-user-plus"></i> Tambah Anggota Sekarang
                                </a>
                            @else
                                <button type="button" class="btn btn-save" onclick="document.getElementById('modalPilihKelompokAnggota').style.display='flex'" style="font-size:12px;padding:8px 18px;display:inline-flex;align-items:center;gap:6px;cursor:pointer">
                                    <i class="fas fa-user-plus"></i> Tambah Anggota Sekarang
                                </button>
                            @endif
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MODAL PILIH KELOMPOK UNTUK TAMBAH ANGGOTA --}}
    @if(isset($kelompokList) && $kelompokList->count() > 1)
    <div id="modalPilihKelompokAnggota" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;padding:16px">
        <div style="background:#fff;border-radius:12px;width:100%;max-width:480px;box-shadow:0 20px 25px -5px rgba(0,0,0,0.2);overflow:hidden">
            <div style="padding:16px 20px;border-bottom:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center;background:#f8fafc">
                <h3 style="margin:0;font-size:15px;font-weight:700;color:#1e293b;display:flex;align-items:center;gap:8px">
                    <i class="fas fa-user-plus" style="color:#1b5e20"></i> Pilih Kelompok Binaan
                </h3>
                <button type="button" onclick="document.getElementById('modalPilihKelompokAnggota').style.display='none'" style="background:none;border:none;font-size:20px;color:#94a3b8;cursor:pointer;line-height:1">&times;</button>
            </div>
            <div style="padding:16px 20px">
                <p style="margin:0 0 14px;font-size:12px;color:#64748b">Silakan pilih kelompok binaan yang ingin ditambahkan data anggotanya:</p>
                <div style="display:flex;flex-direction:column;gap:8px">
                    @foreach($kelompokList as $klp)
                    <a href="{{ route('pendamping.kelompok-saya.anggota.tambah', $klp->id) }}" style="display:flex;align-items:center;justify-content:space-between;padding:12px 14px;border:1px solid #e2e8f0;border-radius:8px;text-decoration:none;color:#1e293b;background:#fff;transition:all 0.15s ease" onmouseover="this.style.borderColor='#2e7d32';this.style.background='#f0fdf4'" onmouseout="this.style.borderColor='#e2e8f0';this.style.background='#fff'">
                        <div style="display:flex;align-items:center;gap:10px">
                            <div style="width:34px;height:34px;border-radius:6px;background:#e8f5e9;display:flex;align-items:center;justify-content:center;color:#1b5e20;font-size:14px">
                                <i class="fas fa-users"></i>
                            </div>
                            <span style="font-weight:600;font-size:13px">{{ $klp->nama_kelompok }}</span>
                        </div>
                        <i class="fas fa-chevron-right" style="color:#94a3b8;font-size:12px"></i>
                    </a>
                    @endforeach
                </div>
            </div>
            <div style="padding:12px 20px;border-top:1px solid #f1f5f9;text-align:right;background:#f8fafc">
                <button type="button" onclick="document.getElementById('modalPilihKelompokAnggota').style.display='none'" class="btn btn-outline" style="font-size:12px;padding:6px 14px">Batal</button>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function filterData() {
    var searchEl = document.getElementById('searchAg');
    var kelEl = document.getElementById('filterKelompok');
    var jkEl = document.getElementById('filterJK');
    var query = searchEl ? searchEl.value.toLowerCase().trim() : '';
    var kel = kelEl ? kelEl.value.toLowerCase().trim() : '';
    var jk = jkEl ? jkEl.value.toLowerCase().trim() : '';
    document.querySelectorAll('#tabelAnggota tbody tr').forEach(function(row) {
        var text = (row.textContent || row.innerText || '').toLowerCase();
        var rowKel = (row.getAttribute('data-kelompok') || '').toLowerCase();
        var rowJk = (row.getAttribute('data-jk') || '').toLowerCase();
        var mText = !query || text.includes(query);
        var mKel = !kel || rowKel === kel;
        var mJk = !jk || rowJk.includes(jk);
        var isMatch = mText && mKel && mJk;
        row.style.display = isMatch ? '' : 'none';
        row.dataset.filterHidden = isMatch ? 'false' : 'true';
    });
    if (typeof window.refreshTablePagination === 'function') {
        window.refreshTablePagination(document.getElementById('tabelAnggota'));
    }
}
if (document.getElementById('searchAg')) {
    document.getElementById('searchAg').addEventListener('input', filterData);
    document.getElementById('searchAg').addEventListener('keyup', filterData);
}
if (document.getElementById('filterKelompok')) {
    document.getElementById('filterKelompok').addEventListener('change', filterData);
}
if (document.getElementById('filterJK')) {
    document.getElementById('filterJK').addEventListener('change', filterData);
}

// Close modal on outside click or ESC
window.addEventListener('click', function(e) {
    var modal = document.getElementById('modalPilihKelompokAnggota');
    if (modal && e.target === modal) {
        modal.style.display = 'none';
    }
});
window.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        var modal = document.getElementById('modalPilihKelompokAnggota');
        if (modal) modal.style.display = 'none';
    }
});
</script>
@endpush
