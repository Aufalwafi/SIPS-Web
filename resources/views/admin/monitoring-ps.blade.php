@extends('layouts.app')
@section('title', 'Monitoring Perencanaan Perhutanan Sosial - SI PS ONLINE')
@push('styles')
<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px; }
.page-header h2 { color: #1b5e20; font-size: 20px; font-weight: 700; margin: 0; }

.summary-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 20px; }
.scard { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 18px; display: flex; align-items: center; gap: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
.scard-icon { width: 44px; height: 44px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
.scard-info h4 { color: #64748b; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; margin: 0 0 2px; }
.scard-info .val { font-size: 20px; font-weight: 700; color: #1e293b; }

.filter-toolbar { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; margin-bottom: 16px; }
.filter-toolbar input { height: 34px; padding: 4px 10px; font-size: 12px; border: 1px solid #cbd5e1; border-radius: 6px; background: #fff; color: #334155; min-width: 240px; }
.filter-toolbar input:focus { border-color: #2e7d32; outline: none; }
.btn-cetak-ps { background: #475569; color: #fff; border: none; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; height: 34px; }
.btn-cetak-ps:hover { background: #334155; }

.ps-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.ps-table th {
    background: #f8fafc; color: #475569; font-weight: 600; text-align: center;
    padding: 9px 12px; border-bottom: 2px solid #e2e8f0;
    font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px;
}
.ps-table th:nth-child(2) { text-align: left; }
.ps-table td { padding: 8px 12px; border-bottom: 1px solid #f1f5f9; text-align: center; }
.ps-table td:nth-child(2) { text-align: left; font-weight: 600; color: #1e293b; }
.ps-table tr:hover td { background: #f8fafc; }

.check-ok { display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; border-radius: 50%; background: #d1fae5; color: #065f46; font-size: 13px; }
.check-no { display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; border-radius: 50%; background: #f1f5f9; color: #94a3b8; font-size: 13px; }

.kop, .tanggal-print { display: none; }
@media print {
    .page-header, .filter-toolbar, .summary-cards { display: none; }
    .kop { display: flex; align-items: center; border-bottom: 3px solid black; padding-bottom: 10px; margin-bottom: 10px; }
    .logo-print { width: 70px; height: auto; margin-right: 15px; }
    .judul { text-align: center; flex: 1; }
    .judul h3, .judul h4 { margin: 0; font-size: 14px; }
    .judul h2 { margin: 2px 0; font-size: 18px; }
    .tanggal-print { display: block; text-align: right; font-size: 11px; margin-bottom: 10px; }
    .ps-table th, .ps-table td { border: 1px solid #000; padding: 6px; font-size: 10px; }
    .check-ok, .check-no { border-radius: 0; background: none; font-size: 12px; }
}
@media(max-width:900px) { .summary-cards { grid-template-columns: 1fr; } }
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
            <p><b>MONITORING PERENCANAAN PERHUTANAN SOSIAL</b></p>
        </div>
    </div>
    <div class="tanggal-print">Tanggal Cetak: {{ now()->format('d F Y') }}</div>

    {{-- HEADER --}}
    <div class="page-header">
        <div>
            <h2><i class="fas fa-leaf" style="color:#2e7d32"></i> Monitoring Perencanaan PS</h2>
            <p style="color:#64748b;font-size:12px;margin:4px 0 0">Periode RKT {{ $tahunAwal }} — {{ $tahunAkhir }}</p>
        </div>
        <button onclick="window.print()" class="btn-cetak-ps"><i class="fas fa-print"></i> Cetak</button>
    </div>

    {{-- SUMMARY CARDS --}}
    @php
        $totalKlp = count($kelompok);
        $sudahRkps = collect($rkpsCount)->filter(fn($v) => $v > 0)->count();
        $belumRkps = $totalKlp - $sudahRkps;
        $totalRkt = 0;
        foreach($rktCount as $byTahun) {
            foreach($byTahun as $v) {
                if ($v > 0) $totalRkt++;
            }
        }
    @endphp
    <div class="summary-cards">
        <div class="scard" style="border-left:4px solid #2e7d32">
            <div class="scard-icon" style="background:#e8f5e9;color:#2e7d32"><i class="fas fa-users"></i></div>
            <div class="scard-info"><h4>Total Kelompok</h4><div class="val">{{ $totalKlp }}</div></div>
        </div>
        <div class="scard" style="border-left:4px solid #22c55e">
            <div class="scard-icon" style="background:#d1fae5;color:#065f46"><i class="fas fa-file-signature"></i></div>
            <div class="scard-info"><h4>Sudah Ada RKPS</h4><div class="val">{{ $sudahRkps }} <span style="font-size:12px;color:#64748b">/ {{ $totalKlp }}</span></div></div>
        </div>
        <div class="scard" style="border-left:4px solid #ef4444">
            <div class="scard-icon" style="background:#fee2e2;color:#b91c1c"><i class="fas fa-triangle-exclamation"></i></div>
            <div class="scard-info"><h4>Belum Ada RKPS</h4><div class="val" style="color:#b91c1c">{{ $belumRkps }}</div></div>
        </div>
    </div>

    {{-- FILTER --}}
    <div class="filter-toolbar">
        <input type="text" id="search" placeholder="🔍 Cari kelompok...">
        <select id="filterRkps" style="height:34px;padding:4px 10px;font-size:12px;border:1px solid #cbd5e1;border-radius:6px;background:#fff">
            <option value="">Semua Status RKPS</option>
            <option value="ok">Sudah RKPS ✔</option>
            <option value="no">Belum RKPS ✖</option>
        </select>
    </div>

    {{-- TABLE --}}
    <div style="background:#fff;border-radius:8px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,0.04);overflow-x:auto">
        <table class="ps-table" id="myTable">
            <thead>
                <tr>
                    <th style="width:40px">No</th>
                    <th style="text-align:left;min-width:180px">Nama Kelompok</th>
                    <th style="width:90px">RKPS</th>
                    @for($t = $tahunAwal; $t <= $tahunAkhir; $t++)
                        <th style="width:70px">RKT {{ $t }}</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @forelse($kelompok as $no => $k)
                @php $hasRkps = ($rkpsCount[$k->id] ?? 0) > 0; @endphp
                <tr data-rkps="{{ $hasRkps ? 'ok' : 'no' }}">
                    <td>{{ $no + 1 }}</td>
                    <td>{{ $k->nama_kelompok }}</td>
                    <td>
                        @if($hasRkps)
                            <span class="check-ok"><i class="fas fa-check"></i></span>
                        @else
                            <span class="check-no"><i class="fas fa-minus"></i></span>
                        @endif
                    </td>
                    @for($t = $tahunAwal; $t <= $tahunAkhir; $t++)
                    <td>
                        @if(($rktCount[$k->id][$t] ?? 0) > 0)
                            <span class="check-ok"><i class="fas fa-check"></i></span>
                        @else
                            <span class="check-no"><i class="fas fa-minus"></i></span>
                        @endif
                    </td>
                    @endfor
                </tr>
                @empty
                <tr>
                    <td colspan="{{ 3 + ($tahunAkhir - $tahunAwal + 1) }}" style="text-align:center;padding:30px;color:#94a3b8">
                        Belum ada data kelompok.
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
function filterPs() {
    var searchEl = document.getElementById('search');
    var rkpsEl = document.getElementById('filterRkps');
    var keyword = searchEl ? searchEl.value.toLowerCase().trim() : '';
    var rkps = rkpsEl ? rkpsEl.value.trim() : '';
    document.querySelectorAll('#myTable tbody tr').forEach(function(row) {
        var text = (row.textContent || row.innerText || '').toLowerCase();
        var rowRkps = row.getAttribute('data-rkps') || '';
        var matchText = !keyword || text.includes(keyword);
        var matchRkps = !rkps || rowRkps === rkps;
        var isMatch = matchText && matchRkps;
        row.style.display = isMatch ? '' : 'none';
        row.dataset.filterHidden = isMatch ? 'false' : 'true';
    });
    if (typeof window.refreshTablePagination === 'function') {
        window.refreshTablePagination(document.getElementById('myTable'));
    }
}
if (document.getElementById('search')) {
    document.getElementById('search').addEventListener('input', filterPs);
    document.getElementById('search').addEventListener('keyup', filterPs);
}
if (document.getElementById('filterRkps')) {
    document.getElementById('filterRkps').addEventListener('change', filterPs);
}
</script>
@endpush
