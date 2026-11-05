@extends('layouts.app')
@section('title', 'Data Laporan Kegiatan - SI PS ONLINE')
@push('styles')
<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px; }
.page-header h2 { color: #1b5e20; font-size: 20px; font-weight: 700; margin: 0; }

.filter-toolbar { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; margin-bottom: 16px; }
.filter-toolbar input, .filter-toolbar select {
    height: 34px; padding: 4px 10px; font-size: 12px;
    border: 1px solid #cbd5e1; border-radius: 6px; background: #fff; color: #334155; min-width: 140px;
}
.filter-toolbar input:focus, .filter-toolbar select:focus { border-color: #2e7d32; outline: none; }
.btn-print-laporan {
    background: #475569; color: #fff; border: none; padding: 6px 14px;
    border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer;
    display: inline-flex; align-items: center; gap: 6px; height: 34px;
}
.btn-print-laporan:hover { background: #334155; }

.lap-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.lap-table th {
    background: #f8fafc; color: #475569; font-weight: 600; text-align: left;
    padding: 9px 12px; border-bottom: 2px solid #e2e8f0;
    font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px;
}
.lap-table td { padding: 9px 12px; border-bottom: 1px solid #f1f5f9; color: #334155; vertical-align: middle; }
.lap-table tr:hover td { background: #f8fafc; }

.foto-thumb { width: 54px; height: 54px; object-fit: cover; border-radius: 6px; cursor: pointer; transition: transform 0.15s, box-shadow 0.15s; }
.foto-thumb:hover { transform: scale(1.08); box-shadow: 0 4px 12px rgba(0,0,0,0.2); }

.foto-modal { display: none; position: fixed; z-index: 9999; inset: 0; background: rgba(0,0,0,0.75); justify-content: center; align-items: center; }
.foto-modal-inner { background: #fff; padding: 12px; border-radius: 10px; max-width: 90vw; max-height: 90vh; }
.foto-modal-inner img { max-width: 100%; max-height: 80vh; object-fit: contain; border-radius: 6px; }
.close-modal-btn { position: absolute; top: 18px; right: 24px; color: #fff; font-size: 28px; font-weight: 700; cursor: pointer; background: none; border: none; }

.kop, .tanggal-print { display: none; }
@media print {
    .page-header, .filter-toolbar { display: none; }
    .kop { display: flex; align-items: center; border-bottom: 3px solid #000; padding-bottom: 10px; margin-bottom: 10px; }
    .logo-print { width: 70px; margin-right: 15px; }
    .judul { text-align: center; flex: 1; }
    .judul h3, .judul h4 { margin: 0; font-size: 14px; }
    .judul h2 { margin: 2px 0; font-size: 18px; }
    .tanggal-print { display: block; text-align: right; font-size: 11px; margin-bottom: 10px; }
    .lap-table th, .lap-table td { border: 1px solid #000; padding: 6px; font-size: 10px; }
    .foto-thumb { width: 36px; height: 36px; }
}
</style>
@endpush

@section('content')
<div class="content">
    {{-- PRINT KOP --}}
    <div class="kop">
        <img src="{{ url('/assets/logo-jatim.png') }}" class="logo-print">
        <div class="judul">
            <h3>PEMERINTAH PROVINSI JAWA TIMUR</h3>
            <h2>DINAS KEHUTANAN</h2>
            <h4>CABANG DINAS KEHUTANAN WILAYAH PROVINSI JAWA TIMUR</h4>
            <p><b>LAPORAN KEGIATAN KELOMPOK</b></p>
        </div>
    </div>
    <div class="tanggal-print">Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d F Y') }}</div>

    {{-- HEADER --}}
    <div class="page-header">
        <div>
            <h2><i class="fas fa-clipboard-list" style="color:#7b1fa2"></i> Data Laporan Kegiatan</h2>
            <p style="color:#64748b;font-size:12px;margin:4px 0 0">Rekap laporan kegiatan seluruh kelompok Perhutanan Sosial</p>
        </div>
        <button onclick="window.print()" class="btn-print-laporan"><i class="fas fa-print"></i> Cetak</button>
    </div>

    {{-- FILTER TOOLBAR --}}
    <div class="filter-toolbar">
        <input type="text" id="searchBox" placeholder="🔍 Cari kelompok / kegiatan...">
        <select id="filterTahun">
            <option value="">Semua Tahun</option>
            @foreach($listTahun as $t)
                <option value="{{ $t }}">{{ $t }}</option>
            @endforeach
        </select>
        <select id="filterBulan">
            <option value="">Semua Bulan</option>
            <option value="01">Januari</option><option value="02">Februari</option><option value="03">Maret</option>
            <option value="04">April</option><option value="05">Mei</option><option value="06">Juni</option>
            <option value="07">Juli</option><option value="08">Agustus</option><option value="09">September</option>
            <option value="10">Oktober</option><option value="11">November</option><option value="12">Desember</option>
        </select>
        <select id="filterUser">
            <option value="">Semua Pembuat</option>
            @foreach($listUser as $u)
                <option value="{{ $u->nama }}">{{ $u->nama }}</option>
            @endforeach
        </select>
        <select id="filterKelompok">
            <option value="">Semua Kelompok</option>
            @foreach($listKelompok as $k)
                <option value="{{ $k->nama_kelompok }}">{{ $k->nama_kelompok }}</option>
            @endforeach
        </select>
    </div>

    {{-- TABLE --}}
    <div style="background:#fff;border-radius:8px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,0.04);overflow-x:auto">
        <table class="lap-table" id="tabelLaporan">
            <thead>
                <tr>
                    <th style="width:40px;text-align:center">No</th>
                    <th style="width:90px;text-align:center">Tanggal</th>
                    <th>Kelompok</th>
                    <th>Kegiatan</th>
                    <th>Deskripsi</th>
                    <th>Pembuat</th>
                    <th style="width:75px;text-align:center">Foto</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $i => $d)
                @php
                    $tgl = $d->tanggal;
                    $tahun = \Carbon\Carbon::parse($tgl)->format('Y');
                    $bulan = \Carbon\Carbon::parse($tgl)->format('m');
                @endphp
                <tr data-tahun="{{ $tahun }}" data-bulan="{{ $bulan }}">
                    <td style="text-align:center;color:#94a3b8">{{ $i + 1 }}</td>
                    <td style="text-align:center;color:#475569">{{ \Carbon\Carbon::parse($tgl)->format('d/m/Y') }}</td>
                    <td><strong style="color:#1e293b">{{ $d->nama_kelompok ?? '-' }}</strong></td>
                    <td>
                        <span style="background:#f1f5f9;color:#475569;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:600">
                            {{ $d->kegiatan ?? '-' }}
                        </span>
                    </td>
                    <td style="max-width:260px;color:#64748b;font-size:11px;line-height:1.5">{{ $d->deskripsi ?? '-' }}</td>
                    <td style="color:#475569">{{ $d->nama_user ?? '-' }}</td>
                    <td style="text-align:center">
                        @if(!empty($d->foto))
                        <img src="{{ asset('uploads/kegiatan/' . $d->foto) }}" class="foto-thumb" onclick="openFotoModal(this.src)" title="Klik untuk perbesar">
                        @else
                        <span style="color:#94a3b8;font-size:11px">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:#94a3b8">
                        <i class="fas fa-folder-open" style="font-size:28px;display:block;margin-bottom:8px"></i>
                        Data laporan belum tersedia.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- FOTO MODAL --}}
<div id="fotoModal" class="foto-modal" onclick="closeFotoModal()">
    <button class="close-modal-btn" onclick="closeFotoModal()">&times;</button>
    <div class="foto-modal-inner" onclick="event.stopPropagation()">
        <img id="imgModal" src="" alt="Foto Kegiatan">
    </div>
</div>
@endsection

@push('scripts')
<script>
function openFotoModal(src) {
    document.getElementById('imgModal').src = src;
    document.getElementById('fotoModal').style.display = 'flex';
}
function closeFotoModal() {
    document.getElementById('fotoModal').style.display = 'none';
}

function filterData() {
    var s = (document.getElementById('searchBox')?.value || '').toLowerCase().trim();
    var t = document.getElementById('filterTahun')?.value || '';
    var b = document.getElementById('filterBulan')?.value || '';
    var u = (document.getElementById('filterUser')?.value || '').toLowerCase().trim();
    var k = (document.getElementById('filterKelompok')?.value || '').toLowerCase().trim();
    var rows = document.querySelectorAll('#tabelLaporan tbody tr');
    rows.forEach(function(row) {
        var txt = (row.textContent || row.innerText || '').toLowerCase();
        var rT = row.getAttribute('data-tahun');
        var rB = row.getAttribute('data-bulan');
        var show = true;
        if (s && !txt.includes(s)) show = false;
        if (t && rT !== t) show = false;
        if (b && rB !== b) show = false;
        if (u && !txt.includes(u)) show = false;
        if (k && !txt.includes(k)) show = false;
        row.style.display = show ? '' : 'none';
        row.dataset.filterHidden = show ? 'false' : 'true';
    });
    if (typeof window.refreshTablePagination === 'function') {
        window.refreshTablePagination(document.getElementById('tabelLaporan'));
    }
}
['searchBox','filterTahun','filterBulan','filterUser','filterKelompok'].forEach(function(id) {
    var el = document.getElementById(id);
    if (el) {
        el.addEventListener('change', filterData);
        el.addEventListener('input', filterData);
        el.addEventListener('keyup', filterData);
    }
});
</script>
@endpush
