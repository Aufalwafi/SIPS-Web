@extends('layouts.app')
@section('title', 'Monitoring Nilai Ekonomi (NTE) - SI PS ONLINE')
@push('styles')
<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px; }
.page-header h2 { color: #1b5e20; font-size: 20px; font-weight: 700; margin: 0; }

.summary-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 20px; }
.scard { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 18px; display: flex; align-items: center; gap: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
.scard-icon { width: 44px; height: 44px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
.scard-info h4 { color: #64748b; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; margin: 0 0 2px; }
.scard-info .val { font-size: 18px; font-weight: 700; color: #1e293b; }

.filter-box { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 18px; margin-bottom: 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
.filter-row { display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end; }
.fg { display: flex; flex-direction: column; gap: 4px; }
.fg label { font-size: 11px; font-weight: 600; color: #475569; text-transform: uppercase; letter-spacing: 0.3px; }
.fg select { height: 34px; padding: 4px 10px; font-size: 12px; border: 1px solid #cbd5e1; border-radius: 6px; background: #fff; color: #334155; min-width: 150px; }
.fg select:focus { border-color: #2e7d32; outline: none; }
.btn-filter-go { background: #2e7d32; color: #fff; border: none; padding: 7px 16px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; height: 34px; }
.btn-filter-go:hover { background: #1b5e20; }
.btn-export-excel { background: #1565c0; color: #fff; text-decoration: none; padding: 7px 14px; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; height: 34px; }
.btn-export-excel:hover { background: #0d47a1; }
.btn-export-pdf { background: #475569; color: #fff; text-decoration: none; padding: 7px 14px; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; height: 34px; cursor: pointer; border: none; }
.btn-export-pdf:hover { background: #334155; }

.nte-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.nte-table th {
    background: #f8fafc; color: #475569; font-weight: 600; text-align: left;
    padding: 9px 12px; border-bottom: 2px solid #e2e8f0;
    font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px;
}
.nte-table td { padding: 9px 12px; border-bottom: 1px solid #f1f5f9; color: #334155; }
.nte-table tr:hover td { background: #f8fafc; }
.text-right { text-align: right; }
.text-center { text-align: center; }

.kop, .tanggal-print { display: none; }
@media print {
    body { background: white; margin: 0; display: block; }
    .page-header, .filter-box { display: none; }
    .summary-cards { display: none; }
    .kop { display: flex; align-items: center; border-bottom: 3px solid #000; padding-bottom: 10px; margin-bottom: 15px; }
    .logo-print { width: 70px; height: auto; margin-right: 15px; }
    .judul { text-align: center; flex: 1; }
    .judul h3, .judul h4 { margin: 0; font-size: 14px; }
    .judul h2 { margin: 2px 0; font-size: 18px; }
    .tanggal-print { display: block; text-align: right; font-size: 11px; margin-bottom: 10px; }
    .nte-table th, .nte-table td { border: 1px solid #000; padding: 6px; font-size: 10px; }
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
            <p><b>LAPORAN MONITORING NILAI EKONOMI (NTE)</b></p>
        </div>
    </div>
    <div class="tanggal-print">Tanggal Cetak: {{ now()->format('d F Y') }}</div>

    {{-- HEADER --}}
    <div class="page-header">
        <div>
            <h2><i class="fas fa-chart-line" style="color:#2e7d32"></i> Monitoring Nilai Ekonomi (NTE)</h2>
            <p style="color:#64748b;font-size:12px;margin:4px 0 0">Rekap transaksi dan nilai ekonomi komoditas Perhutanan Sosial</p>
        </div>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="summary-cards">
        <div class="scard" style="border-left:4px solid #2e7d32">
            <div class="scard-icon" style="background:#e8f5e9;color:#2e7d32"><i class="fas fa-money-bill-wave"></i></div>
            <div class="scard-info">
                <h4>Total Nilai Ekonomi</h4>
                <div class="val" style="font-size:15px">Rp {{ number_format($totalNekon, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="scard" style="border-left:4px solid #1565c0">
            <div class="scard-icon" style="background:#e3f2fd;color:#1565c0"><i class="fas fa-boxes-stacked"></i></div>
            <div class="scard-info">
                <h4>Total Volume Produksi</h4>
                <div class="val">{{ number_format($totalVolume, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="scard" style="border-left:4px solid #7b1fa2">
            <div class="scard-icon" style="background:#f3e5f5;color:#7b1fa2"><i class="fas fa-leaf"></i></div>
            <div class="scard-info">
                <h4>Jumlah Transaksi</h4>
                <div class="val">{{ number_format(count($data)) }}</div>
            </div>
        </div>
    </div>

    {{-- FILTER FORM --}}
    <div class="filter-box">
        <form method="GET">
            <div class="filter-row">
                <div class="fg">
                    <label>Tahun</label>
                    <select name="tahun">
                        <option value="">Semua Tahun</option>
                        @foreach($tahunData as $t)
                            <option value="{{ $t }}" @selected($tahun == $t)>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="fg">
                    <label>Bulan</label>
                    <select name="bulan">
                        <option value="">Semua Bulan</option>
                        @foreach([1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'] as $b => $nama)
                            <option value="{{ $b }}" @selected($bulan == $b)>{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="fg">
                    <label>Komoditas</label>
                    <select name="komoditas">
                        <option value="">Semua Komoditas</option>
                        @foreach($komoditasData as $k)
                            <option value="{{ $k }}" @selected($komoditas == $k)>{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="fg">
                    <label>Pendamping</label>
                    <select name="pendamping">
                        <option value="">Semua Pendamping</option>
                        @foreach($pendampingData as $p)
                            <option value="{{ $p->id }}" @selected($pendamping == $p->id)>{{ $p->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="fg">
                    <label>Kelompok</label>
                    <select name="kelompok">
                        <option value="">Semua Kelompok</option>
                        @foreach($kelompokData as $k)
                            <option value="{{ $k->id }}" @selected($kelompokFilter == $k->id)>{{ $k->nama_kelompok }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-filter-go"><i class="fas fa-filter"></i> Filter</button>
                <button type="submit" name="export" value="excel" class="btn-export-excel"><i class="fas fa-file-excel"></i> Export Excel</button>
                <button type="button" onclick="window.print()" class="btn-export-pdf"><i class="fas fa-print"></i> Cetak PDF</button>
            </div>
        </form>
    </div>

    {{-- DATA TABLE --}}
    <div style="background:#fff;border-radius:8px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,0.04);overflow-x:auto">
        <table class="nte-table">
            <thead>
                <tr>
                    <th style="width:40px;text-align:center">No</th>
                    <th style="width:60px;text-align:center">Tahun</th>
                    <th style="width:60px;text-align:center">Bulan</th>
                    <th>Kelompok</th>
                    <th>Komoditas</th>
                    <th style="width:70px;text-align:center">Satuan</th>
                    <th style="text-align:right">Volume</th>
                    <th style="text-align:right">Total NTE</th>
                    <th>Pendamping</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $i => $d)
                <tr>
                    <td class="text-center" style="color:#94a3b8">{{ $i + 1 }}</td>
                    <td class="text-center">{{ $d->tahun }}</td>
                    <td class="text-center">{{ $d->bulan }}</td>
                    <td><strong>{{ $d->nama_kelompok }}</strong></td>
                    <td>{{ $d->nama_produk }}</td>
                    <td class="text-center">{{ $d->satuan }}</td>
                    <td class="text-right" style="font-weight:600">{{ number_format($d->total_volume, 0, ',', '.') }}</td>
                    <td class="text-right" style="font-weight:600;color:#2e7d32">Rp {{ number_format($d->total_nekon, 0, ',', '.') }}</td>
                    <td style="color:#64748b">{{ $d->nama_pendamping ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:30px;color:#94a3b8">
                        <i class="fas fa-inbox" style="font-size:28px;display:block;margin-bottom:8px"></i>
                        Belum ada data NTE untuk filter yang dipilih.
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if(count($data) > 0)
            <tfoot>
                <tr style="background:#f8fafc;font-weight:700">
                    <td colspan="6" style="padding:10px 12px;text-align:right;color:#475569;font-size:12px">TOTAL :</td>
                    <td class="text-right" style="padding:10px 12px;color:#1565c0">{{ number_format($totalVolume, 0, ',', '.') }}</td>
                    <td class="text-right" style="padding:10px 12px;color:#2e7d32">Rp {{ number_format($totalNekon, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
