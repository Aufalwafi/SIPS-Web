@extends('layouts.app')
@section('title', 'Rekap Wilayah - SI PS ONLINE')
@push('styles')
<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px; }
.page-header h2 { color: #1b5e20; font-size: 20px; font-weight: 700; margin: 0; }

.summary-cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px; }
.scard { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
.scard-info h4 { color: #64748b; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; margin: 0 0 2px; }
.scard-info .val { font-size: 22px; font-weight: 700; color: #1e293b; }
.scard-icon { width: 44px; height: 44px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }

.section-box { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); overflow: hidden; margin-bottom: 20px; }
.section-header { padding: 14px 18px; border-bottom: 1px solid #f1f5f9; background: #fafafa; }
.section-header h3 { margin: 0; font-size: 14px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px; }

.rekap-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.rekap-table th { width: 260px; background: #f8fafc; color: #475569; font-weight: 600; text-align: left; padding: 11px 16px; border-bottom: 1px solid #f1f5f9; }
.rekap-table td { padding: 11px 16px; border-bottom: 1px solid #f1f5f9; color: #1e293b; }
.rekap-table tr:hover td { background: #fafafa; }

.btn-print { background: #475569; color: #fff; border: none; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; height: 34px; }
.btn-print:hover { background: #334155; }

.kop, .tanggal-print { display: none; }
@media print {
    .page-header, .summary-cards { display: none; }
    .kop { display: flex; align-items: center; border-bottom: 3px solid #000; padding-bottom: 10px; margin-bottom: 15px; }
    .logo-print { width: 70px; height: auto; margin-right: 15px; }
    .judul { text-align: center; flex: 1; }
    .judul h3, .judul h4 { margin: 0; font-size: 14px; }
    .judul h2 { margin: 2px 0; font-size: 18px; }
    .tanggal-print { display: block; text-align: right; font-size: 11px; margin-bottom: 10px; }
    .rekap-table th, .rekap-table td { border: 1px solid #000; padding: 6px; font-size: 11px; }
}
@media(max-width:1000px) { .summary-cards { grid-template-columns: repeat(2, 1fr); } }
@media(max-width:600px) { .summary-cards { grid-template-columns: 1fr; } }
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
            <h4>CABANG DINAS KEHUTANAN WILAYAH {{ strtoupper($cdkInfo->nama) }}</h4>
            <p><b>LEMBAR REKAPITULASI DATA PERHUTANAN SOSIAL</b></p>
        </div>
    </div>
    <div class="tanggal-print">Tanggal Cetak: {{ now()->format('d F Y') }}</div>

    {{-- HEADER --}}
    <div class="page-header">
        <div>
            <h2><i class="fas fa-table" style="color:#2e7d32"></i> Rekapitulasi Wilayah {{ $cdkInfo->nama }}</h2>
            <p style="color:#64748b;font-size:12px;margin:4px 0 0">Ringkasan keseluruhan indikator kinerja Perhutanan Sosial di wilayah kerja</p>
        </div>
        <button onclick="window.print()" class="btn-print"><i class="fas fa-print"></i> Cetak Dokumen</button>
    </div>

    {{-- STATS GRID --}}
    <div class="summary-cards">
        <div class="scard" style="border-left:4px solid #2e7d32">
            <div class="scard-info">
                <h4>Kelompok PS</h4>
                <div class="val">{{ number_format($jumlahKelompok) }}</div>
            </div>
            <div class="scard-icon" style="background:#e8f5e9;color:#2e7d32"><i class="fas fa-users"></i></div>
        </div>
        <div class="scard" style="border-left:4px solid #00796b">
            <div class="scard-info">
                <h4>Pendamping</h4>
                <div class="val">{{ number_format($jumlahPendamping) }}</div>
            </div>
            <div class="scard-icon" style="background:#e0f2f1;color:#00796b"><i class="fas fa-user-tie"></i></div>
        </div>
        <div class="scard" style="border-left:4px solid #1565c0">
            <div class="scard-info">
                <h4>Petani Anggota</h4>
                <div class="val">{{ number_format($jumlahAnggota) }}</div>
            </div>
            <div class="scard-icon" style="background:#e3f2fd;color:#1565c0"><i class="fas fa-user-friends"></i></div>
        </div>
        <div class="scard" style="border-left:4px solid #7b1fa2">
            <div class="scard-info">
                <h4>Total Luas Area</h4>
                <div class="val" style="font-size:18px">{{ number_format($luasLahan, 1, ',', '.') }} ha</div>
            </div>
            <div class="scard-icon" style="background:#f3e5f5;color:#7b1fa2"><i class="fas fa-map"></i></div>
        </div>
    </div>

    {{-- TABEL REKAPITULASI --}}
    <div class="section-box">
        <div class="section-header">
            <h3><i class="fas fa-clipboard-check" style="color:#2e7d32"></i> Rincian Indikator Kinerja Wilayah</h3>
        </div>
        <table class="rekap-table">
            <tr>
                <th>Satuan Wilayah Kerja</th>
                <td><strong style="color:#1565c0">{{ $cdkInfo->nama }}</strong></td>
            </tr>
            <tr>
                <th>Jumlah Kelompok Perhutanan Sosial</th>
                <td><strong>{{ number_format($jumlahKelompok) }}</strong> unit kelompok</td>
            </tr>
            <tr>
                <th>Tenaga Pendamping Aktif</th>
                <td><strong>{{ number_format($jumlahPendamping) }}</strong> personil</td>
            </tr>
            <tr>
                <th>Total Petani / Anggota Terdaftar</th>
                <td><strong>{{ number_format($jumlahAnggota) }}</strong> orang</td>
            </tr>
            <tr>
                <th>Total Luas Lahan Kelola</th>
                <td><strong style="color:#2e7d32">{{ number_format($luasLahan, 2, ',', '.') }} Hektar</strong></td>
            </tr>
            <tr>
                <th>Jumlah Komoditas & Data NTE</th>
                <td><strong>{{ number_format($jumlahNte) }}</strong> unit data komoditas</td>
            </tr>
            <tr>
                <th>Jumlah Laporan Kegiatan Terkirim</th>
                <td><strong>{{ number_format($jumlahLaporan) }}</strong> laporan kegiatan</td>
            </tr>
            <tr>
                <th>Total Pengajuan Dokumen RKPS</th>
                <td><strong>{{ number_format($jumlahRkps) }}</strong> berkas</td>
            </tr>
            <tr>
                <th>Total Pengajuan Dokumen RKT</th>
                <td><strong>{{ number_format($jumlahRkt) }}</strong> berkas</td>
            </tr>
        </table>
    </div>
</div>
@endsection