@extends('layouts.app')
@section('title', 'Dashboard Pendamping - SI PS ONLINE')
@push('styles')
<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; }
.page-header h2 { color: #1b5e20; font-size: 22px; font-weight: 700; margin: 0; }

.summary-cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px; }
.scard { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px 18px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.04); transition: transform 0.2s, box-shadow 0.2s; }
.scard:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.07); }
.scard-info h4 { color: #64748b; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 4px; }
.scard-info .val { font-size: 24px; font-weight: 700; color: #1e293b; line-height: 1.2; }
.scard-info .subtext { font-size: 11px; color: #94a3b8; margin-top: 2px; }
.scard-icon { width: 46px; height: 46px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }

.section-box { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); margin-bottom: 22px; overflow: hidden; }
.section-header { padding: 14px 18px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: #fafafa; }
.section-header h3 { margin: 0; font-size: 15px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px; }

.p-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.p-table th {
    background: #f8fafc; color: #475569; font-weight: 600; text-align: left;
    padding: 10px 14px; border-bottom: 2px solid #e2e8f0;
    font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px;
}
.p-table td { padding: 10px 14px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.p-table tr:hover td { background: #f8fafc; }

.btn-detail-link { background: #e8f5e9; color: #1b5e20; border: 1px solid #c8e6c9; padding: 4px 10px; border-radius: 5px; font-size: 11px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
.btn-detail-link:hover { background: #1b5e20; color: #fff; }

.alert-waiting { background: #fffbeb; border: 1px solid #fde68a; border-left: 4px solid #f59e0b; border-radius: 8px; padding: 12px 16px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
.alert-waiting-text { display: flex; align-items: center; gap: 10px; font-size: 13px; color: #92400e; }

@media(max-width:1100px) { .summary-cards { grid-template-columns: repeat(2, 1fr); } }
@media(max-width:600px) { .summary-cards { grid-template-columns: 1fr; } }
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
            <h2><i class="fas fa-chalkboard-user" style="color:#2e7d32"></i> Dashboard Pendamping</h2>
            <p style="color:#475569;font-size:15px;margin:5px 0 0">
                Selamat datang kembali, <b style="color:#1b5e20;font-size:18px;font-weight:700">{{ Auth::user()->nama ?? Auth::user()->username }}</b>
            </p>
        </div>
        <div style="display:flex;gap:8px">
            <a href="{{ route('pendamping.kelompok-saya') }}" class="btn btn-save" style="height:36px;padding:0 14px;font-size:12px">
                <i class="fas fa-users"></i> Kelompok Binaan
            </a>
            <a href="{{ route('pendamping.laporan-kegiatan.tambah') }}" class="btn" style="background:#1565c0;color:#fff;height:36px;padding:0 14px;font-size:12px">
                <i class="fas fa-plus"></i> Tambah Laporan
            </a>
        </div>
    </div>

    {{-- WAITING ALERT --}}
    @if($rkpsMenunggu > 0 || $rktMenunggu > 0)
    <div class="alert-waiting">
        <div class="alert-waiting-text">
            <i class="fas fa-clock" style="font-size:18px;color:#f59e0b"></i>
            <div>
                <strong>Pengajuan Menunggu:</strong>
                @if($rkpsMenunggu > 0) <span style="margin-left:5px">{{ $rkpsMenunggu }} RKPS</span> @endif
                @if($rkpsMenunggu > 0 && $rktMenunggu > 0) • @endif
                @if($rktMenunggu > 0) <span>{{ $rktMenunggu }} RKT</span> @endif
                sedang diproses verifikasi oleh Admin.
            </div>
        </div>
        <div style="display:flex;gap:6px">
            @if($rkpsMenunggu > 0)
            <a href="{{ route('pendamping.pengajuan-rkps') }}" style="background:#f59e0b;color:#fff;padding:4px 10px;border-radius:5px;font-size:11px;font-weight:600;text-decoration:none">Lihat RKPS</a>
            @endif
            @if($rktMenunggu > 0)
            <a href="{{ route('pendamping.pengajuan-rkt') }}" style="background:#1565c0;color:#fff;padding:4px 10px;border-radius:5px;font-size:11px;font-weight:600;text-decoration:none">Lihat RKT</a>
            @endif
        </div>
    </div>
    @endif

    {{-- STATS GRID --}}
    <div class="summary-cards">
        <div class="scard" style="border-left:4px solid #2e7d32">
            <div class="scard-info">
                <h4>Kelompok Binaan</h4>
                <div class="val">{{ $totalKelompok }}</div>
                <div class="subtext">Total kelompok didampingi</div>
            </div>
            <div class="scard-icon" style="background:#e8f5e9;color:#2e7d32"><i class="fas fa-users"></i></div>
        </div>
        <div class="scard" style="border-left:4px solid #1565c0">
            <div class="scard-info">
                <h4>Total Anggota</h4>
                <div class="val">{{ $totalAnggota }}</div>
                <div class="subtext">Petani / anggota aktif</div>
            </div>
            <div class="scard-icon" style="background:#e3f2fd;color:#1565c0"><i class="fas fa-user-friends"></i></div>
        </div>
        <div class="scard" style="border-left:4px solid #7b1fa2">
            <div class="scard-info">
                <h4>Data NTE</h4>
                <div class="val">{{ $totalNte ?? 0 }}</div>
                <div class="subtext">Komoditas & nilai ekonomi</div>
            </div>
            <div class="scard-icon" style="background:#f3e5f5;color:#7b1fa2"><i class="fas fa-chart-line"></i></div>
        </div>
        <div class="scard" style="border-left:4px solid #e65100">
            <div class="scard-info">
                <h4>Laporan Kegiatan</h4>
                <div class="val">{{ $totalLaporan ?? 0 }}</div>
                <div class="subtext">Aktivitas terlaporkan</div>
            </div>
            <div class="scard-icon" style="background:#fff3e0;color:#e65100"><i class="fas fa-clipboard-list"></i></div>
        </div>
    </div>

    {{-- KELOMPOK DAMPINGAN TABLE --}}
    <div class="section-box">
        <div class="section-header">
            <h3><i class="fas fa-users" style="color:#2e7d32"></i> Kelompok yang Didampingi</h3>
            <a href="{{ route('pendamping.kelompok-saya') }}" style="font-size:12px;color:#2e7d32;font-weight:600;text-decoration:none">
                Lihat Semua <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div style="overflow-x:auto">
            <table class="p-table">
                <thead>
                    <tr>
                        <th style="width:40px;text-align:center">No</th>
                        <th>Nama Kelompok</th>
                        <th>CDK</th>
                        <th>Desa</th>
                        <th>Kecamatan</th>
                        <th>Skema</th>
                        <th style="text-align:center;width:90px">Anggota</th>
                        <th style="text-align:center;width:100px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kelompok as $i => $k)
                        <tr>
                            <td style="text-align:center;color:#94a3b8">{{ $i + 1 }}</td>
                            <td><strong style="color:#1e293b">{{ $k->nama_kelompok }}</strong></td>
                            <td>
                                @if($k->nama_cdk)
                                <span style="background:#dbeafe;color:#1e40af;padding:2px 7px;border-radius:5px;font-size:11px">{{ $k->nama_cdk }}</span>
                                @else — @endif
                            </td>
                            <td style="color:#64748b">{{ $k->nama_desa ?? $k->desa ?? '-' }}</td>
                            <td style="color:#64748b">{{ $k->kecamatan ?? '-' }}</td>
                            <td>
                                @if($k->skema_ps)
                                <span style="background:#e8f5e9;color:#1b5e20;padding:2px 7px;border-radius:4px;font-size:11px;font-weight:600">{{ $k->skema_ps }}</span>
                                @else — @endif
                            </td>
                            <td style="text-align:center">
                                <span style="font-weight:700;color:#1e293b">{{ $anggotaPerKelompok[$k->id] ?? 0 }}</span>
                            </td>
                            <td style="text-align:center">
                                <a href="{{ route('pendamping.kelompok-saya.detail', $k->id) }}" class="btn-detail-link">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center;padding:35px;color:#94a3b8">
                                <i class="fas fa-users-slash" style="font-size:28px;display:block;margin-bottom:8px"></i>
                                Anda belum mendampingi kelompok manapun.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- LAPORAN TERAKHIR --}}
    @if($laporanTerakhir->count())
    <div class="section-box">
        <div class="section-header">
            <h3><i class="fas fa-history" style="color:#1565c0"></i> Laporan Kegiatan Terakhir</h3>
            <a href="{{ route('pendamping.laporan-kegiatan') }}" style="font-size:12px;color:#1565c0;font-weight:600;text-decoration:none">
                Lihat Semua Laporan <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div style="overflow-x:auto">
            <table class="p-table">
                <thead>
                    <tr>
                        <th style="width:110px">Tanggal</th>
                        <th>Kelompok</th>
                        <th>Kegiatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($laporanTerakhir as $l)
                        <tr>
                            <td style="color:#64748b">{{ \Carbon\Carbon::parse($l->tanggal)->format('d M Y') }}</td>
                            <td><strong style="color:#1e293b">{{ $l->nama_kelompok }}</strong></td>
                            <td style="color:#334155">{{ $l->kegiatan ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
