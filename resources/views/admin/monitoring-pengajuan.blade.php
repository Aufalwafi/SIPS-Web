@extends('layouts.app')
@section('title', 'Monitoring Pengajuan RKPS & RKT - SI PS ONLINE')
@push('styles')
<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px; }
.page-header h2 { color: #1b5e20; font-size: 20px; font-weight: 700; margin: 0; }

.summary-cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px; }
.scard { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 18px; display: flex; align-items: center; gap: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
.scard-icon { width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
.scard-info h4 { color: #64748b; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; margin: 0 0 2px; }
.scard-info .val { font-size: 20px; font-weight: 700; color: #1e293b; }

.toolbar { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid #f1f5f9; }
.toolbar input, .toolbar select {
    height: 34px; padding: 4px 10px; font-size: 12px;
    border: 1px solid #cbd5e1; border-radius: 6px; background: #fff; color: #334155; min-width: 160px;
}
.toolbar input:focus, .toolbar select:focus { border-color: #2e7d32; outline: none; }
.btn-print-mon {
    background: #475569; color: #fff; border: none; padding: 6px 14px;
    border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer;
    display: inline-flex; align-items: center; gap: 6px;
}
.btn-print-mon:hover { background: #334155; }

.mon-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.mon-table th {
    background: #f8fafc; color: #475569; font-weight: 600; text-align: center;
    padding: 9px 12px; border-bottom: 2px solid #e2e8f0;
    font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px;
}
.mon-table th:nth-child(2) { text-align: left; }
.mon-table td { padding: 9px 12px; border-bottom: 1px solid #f1f5f9; text-align: center; }
.mon-table td:nth-child(2) { text-align: left; font-weight: 600; color: #1e293b; }
.mon-table tr:hover td { background: #f8fafc; }

.s-menunggu { background: #fef3c7; color: #92400e; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; }
.s-revisi { background: #e0f2fe; color: #0c4a6e; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; }
.s-disetujui { background: #d1fae5; color: #065f46; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; }
.s-ditolak { background: #fee2e2; color: #7f1d1d; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; }
.s-none { color: #94a3b8; font-size: 11px; }

.kop, .tanggal-print { display: none; }
@media print {
    body { background: white; margin: 0; display: block; }
    .toolbar, .page-header { display: none; }
    .content { margin: 0; padding: 10px 20px; box-shadow: none; border-radius: 0; }
    .summary-cards { display: none; }
    .kop { display: flex; align-items: center; border-bottom: 3px solid black; padding-bottom: 10px; margin-bottom: 10px; }
    .logo-print { width: 70px; height: auto; margin-right: 15px; }
    .judul { text-align: center; flex: 1; }
    .judul h3, .judul h4 { margin: 0; font-size: 14px; }
    .judul h2 { margin: 2px 0; font-size: 18px; }
    .tanggal-print { display: block; text-align: right; font-size: 11px; margin-bottom: 10px; }
    .mon-table th, .mon-table td { border: 1px solid #000; padding: 6px; font-size: 10px; }
}
@media(max-width: 1000px) { .summary-cards { grid-template-columns: repeat(2, 1fr); } }
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
            <p><b>MONITORING PENGAJUAN RKPS &amp; RKT</b></p>
        </div>
    </div>
    <div class="tanggal-print">Tanggal Cetak: {{ now()->format('d F Y') }}</div>

    {{-- HEADER --}}
    <div class="page-header">
        <div>
            <h2><i class="fas fa-chart-bar" style="color:#2e7d32"></i> Monitoring Pengajuan RKPS &amp; RKT</h2>
            <p style="color:#64748b;font-size:12px;margin:4px 0 0">Rekap status pengajuan berkas seluruh kelompok Perhutanan Sosial</p>
        </div>
        <div style="display:flex;gap:8px">
            <a href="{{ route('admin.verifikasi-rkps') }}" style="background:#f59e0b;color:#fff;padding:7px 14px;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px">
                <i class="fas fa-file-signature"></i> Verifikasi RKPS
            </a>
            <a href="{{ route('admin.verifikasi-rkt') }}" style="background:#3b82f6;color:#fff;padding:7px 14px;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px">
                <i class="fas fa-clipboard-check"></i> Verifikasi RKT
            </a>
        </div>
    </div>

    {{-- SUMMARY CARDS --}}
    @php
        $totalKlp = count($kelompok);
        $sudahRkps = collect($rkpsStatus)->filter(fn($v) => $v !== '-')->count();
        $sudahRkt = collect($rktStatus)->flatMap(fn($t) => $t)->filter(fn($v) => $v !== '-')->count();
        $menungguTotal = collect($rkpsStatus)->filter(fn($v) => $v === 'Menunggu')->count() + collect($rktStatus)->flatMap(fn($t) => $t)->filter(fn($v) => $v === 'Menunggu')->count();
    @endphp
    <div class="summary-cards">
        <div class="scard">
            <div class="scard-icon" style="background:#e8f5e9;color:#2e7d32"><i class="fas fa-users"></i></div>
            <div class="scard-info"><h4>Total Kelompok</h4><div class="val">{{ $totalKlp }}</div></div>
        </div>
        <div class="scard">
            <div class="scard-icon" style="background:#fff3e0;color:#e65100"><i class="fas fa-file-signature"></i></div>
            <div class="scard-info"><h4>Pengajuan RKPS</h4><div class="val">{{ $sudahRkps }}</div></div>
        </div>
        <div class="scard">
            <div class="scard-icon" style="background:#e3f2fd;color:#1565c0"><i class="fas fa-clipboard-list"></i></div>
            <div class="scard-info"><h4>Pengajuan RKT</h4><div class="val">{{ $sudahRkt }}</div></div>
        </div>
        <div class="scard">
            <div class="scard-icon" style="background:#fff3cd;color:#856404"><i class="fas fa-hourglass-half"></i></div>
            <div class="scard-info"><h4>Menunggu Verifikasi</h4><div class="val" style="color:#d97706">{{ $menungguTotal }}</div></div>
        </div>
    </div>

    {{-- TOOLBAR --}}
    <div class="toolbar">
        <input type="text" id="searchBox" placeholder="🔍 Cari kelompok...">
        <select id="filterStatus">
            <option value="">Semua Status RKPS</option>
            <option value="Menunggu">Menunggu</option>
            <option value="Disetujui">Disetujui</option>
            <option value="Ditolak">Ditolak</option>
            <option value="Revisi">Revisi</option>
        </select>
        <button onclick="window.print()" class="btn-print-mon"><i class="fas fa-print"></i> Cetak</button>
    </div>

    {{-- TABLE --}}
    <div style="background:#fff;border-radius:8px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,0.04);overflow-x:auto">
        <table class="mon-table" id="tabelMonitoring">
            <thead>
                <tr>
                    <th style="width:40px">No</th>
                    <th style="text-align:left;min-width:180px">Nama Kelompok</th>
                    <th style="width:110px">Status RKPS</th>
                    @foreach($listTahun as $tahun)
                        <th style="width:100px">RKT {{ $tahun }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($kelompok as $no => $k)
                @php $rkps = $rkpsStatus[$k->id] ?? '-'; @endphp
                <tr data-rkps="{{ $rkps }}">
                    <td>{{ $no + 1 }}</td>
                    <td>{{ $k->nama_kelompok }}</td>
                    <td>
                        @if($rkps === '-')
                            <span class="s-none">—</span>
                        @else
                            <span class="s-{{ strtolower($rkps) }}">{{ $rkps }}</span>
                        @endif
                    </td>
                    @foreach($listTahun as $tahun)
                        @php $rkt = $rktStatus[$k->id][$tahun] ?? '-'; @endphp
                        <td>
                            @if($rkt === '-')
                                <span class="s-none">—</span>
                            @else
                                <span class="s-{{ strtolower($rkt) }}">{{ $rkt }}</span>
                            @endif
                        </td>
                    @endforeach
                </tr>
                @empty
                <tr><td colspan="{{ 3 + count($listTahun) }}" style="text-align:center;padding:30px;color:#94a3b8">Belum ada data kelompok.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
var searchBox = document.getElementById('searchBox');
var filterStatus = document.getElementById('filterStatus');
function filterTable() {
    var keyword = searchBox ? searchBox.value.toLowerCase().trim() : '';
    var statusVal = filterStatus ? filterStatus.value.trim() : '';
    document.querySelectorAll('#tabelMonitoring tbody tr').forEach(function(row) {
        var text = (row.textContent || row.innerText || '').toLowerCase();
        var rowRkps = row.getAttribute('data-rkps') || '';
        var matchText = !keyword || text.includes(keyword);
        var matchStatus = !statusVal || rowRkps === statusVal;
        var isMatch = matchText && matchStatus;
        row.style.display = isMatch ? '' : 'none';
        row.dataset.filterHidden = isMatch ? 'false' : 'true';
    });
    if (typeof window.refreshTablePagination === 'function') {
        window.refreshTablePagination(document.getElementById('tabelMonitoring'));
    }
}
if (searchBox) {
    searchBox.addEventListener('input', filterTable);
    searchBox.addEventListener('keyup', filterTable);
}
if (filterStatus) {
    filterStatus.addEventListener('change', filterTable);
}
</script>
@endpush
