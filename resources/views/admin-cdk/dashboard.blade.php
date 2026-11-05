@extends('layouts.app')
@section('title', 'Dashboard CDK - SI PS ONLINE')
@push('styles')
<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; }
.page-header h2 { color: #1b5e20; font-size: 22px; font-weight: 700; margin: 0; }

.cdk-badge-header { background: #dbeafe; color: #1e40af; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; }

.stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px; }
.scard { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px 18px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.04); transition: transform 0.2s, box-shadow 0.2s; }
.scard:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.07); }
.scard-info h4 { color: #64748b; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 4px; }
.scard-info .val { font-size: 24px; font-weight: 700; color: #1e293b; line-height: 1.2; }
.scard-info .subtext { font-size: 11px; color: #94a3b8; margin-top: 2px; }
.scard-icon { width: 46px; height: 46px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }

.alert-waiting { background: #fffbeb; border: 1px solid #fde68a; border-left: 4px solid #f59e0b; border-radius: 8px; padding: 12px 16px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
.alert-waiting-text { display: flex; align-items: center; gap: 10px; font-size: 13px; color: #92400e; }

.grid-2col { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 22px; }
.section-box { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); overflow: hidden; }
.section-header { padding: 14px 18px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: #fafafa; }
.section-header h3 { margin: 0; font-size: 14px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px; }

.p-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.p-table th {
    background: #f8fafc; color: #475569; font-weight: 600; text-align: left;
    padding: 10px 14px; border-bottom: 2px solid #e2e8f0;
    font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px;
}
.p-table td { padding: 10px 14px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.p-table tr:hover td { background: #f8fafc; }

.quick-menu { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 20px; }
.q-btn { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 14px; text-align: center; text-decoration: none; color: #1e293b; display: flex; flex-direction: column; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; transition: transform 0.15s, border-color 0.15s, box-shadow 0.15s; }
.q-btn i { font-size: 18px; }
.q-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.06); border-color: #2e7d32; }

@media(max-width:1100px) { .stat-grid { grid-template-columns: repeat(2, 1fr); } .quick-menu { grid-template-columns: repeat(2, 1fr); } }
@media(max-width:800px) { .grid-2col { grid-template-columns: 1fr; } }
@media(max-width:600px) { .stat-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="content">
    @if(!$cdkInfo)
        <div class="section-box" style="text-align:center;padding:50px 20px">
            <i class="fas fa-triangle-exclamation" style="font-size:42px;color:#f59e0b;margin-bottom:12px;display:block"></i>
            <h3 style="color:#b45309;margin-bottom:6px">Akun Tidak Terhubung ke Wilayah CDK</h3>
            <p style="color:#64748b;max-width:500px;margin:0 auto 18px;font-size:13px">Akun Anda belum memiliki asosiasi wilayah Cabang Dinas Kehutanan (CDK). Silakan hubungi Administrator Provinsi untuk mengonfigurasi wilayah tugas Anda.</p>
        </div>
    @else
        {{-- HEADER --}}
        <div class="page-header">
            <div>
                <h2><i class="fas fa-building-user" style="color:#1565c0"></i> Dashboard Wilayah {{ $cdkInfo->nama }}</h2>
                <p style="color:#64748b;font-size:13px;margin:4px 0 0">
                    Otoritas & Pengawasan Perhutanan Sosial Wilayah <span class="cdk-badge-header"><i class="fas fa-shield-halved"></i> {{ $cdkInfo->nama }}</span>
                </p>
            </div>
            <div style="display:flex;gap:8px">
                <a href="{{ route('admin-cdk.peta-kelompok') }}" class="btn" style="background:#1565c0;color:#fff;font-size:12px;height:36px;padding:0 14px;border-radius:6px">
                    <i class="fas fa-map-marked-alt"></i> Peta Wilayah
                </a>
                <a href="{{ route('admin-cdk.rekap-wilayah') }}" class="btn btn-save" style="font-size:12px;height:36px;padding:0 14px;border-radius:6px">
                    <i class="fas fa-table"></i> Rekap Wilayah
                </a>
            </div>
        </div>

        {{-- WAITING ALERT --}}
        @if($pengajuanRkps > 0 || $pengajuanRkt > 0)
        <div class="alert-waiting">
            <div class="alert-waiting-text">
                <i class="fas fa-clock" style="font-size:18px;color:#f59e0b"></i>
                <div>
                    <strong>Pengajuan Menunggu Verifikasi di {{ $cdkInfo->nama }}:</strong>
                    @if($pengajuanRkps > 0) <span style="margin-left:5px">{{ $pengajuanRkps }} Dokumen RKPS</span> @endif
                    @if($pengajuanRkps > 0 && $pengajuanRkt > 0) • @endif
                    @if($pengajuanRkt > 0) <span>{{ $pengajuanRkt }} Dokumen RKT</span> @endif
                    memerlukan tindakan verifikasi Anda.
                </div>
            </div>
            <div style="display:flex;gap:6px">
                @if($pengajuanRkps > 0)
                <a href="{{ route('admin-cdk.verifikasi-rkps') }}" style="background:#f59e0b;color:#fff;padding:5px 12px;border-radius:5px;font-size:11px;font-weight:600;text-decoration:none">
                    Verifikasi RKPS
                </a>
                @endif
                @if($pengajuanRkt > 0)
                <a href="{{ route('admin-cdk.verifikasi-rkt') }}" style="background:#1565c0;color:#fff;padding:5px 12px;border-radius:5px;font-size:11px;font-weight:600;text-decoration:none">
                    Verifikasi RKT
                </a>
                @endif
            </div>
        </div>
        @endif

        {{-- QUICK SHORTCUTS --}}
        <div class="quick-menu">
            <a href="{{ route('admin-cdk.data-kelompok') }}" class="q-btn">
                <i class="fas fa-users" style="color:#2e7d32"></i>
                <span>Data Kelompok</span>
            </a>
            <a href="{{ route('admin-cdk.data-pendamping') }}" class="q-btn">
                <i class="fas fa-user-tie" style="color:#00796b"></i>
                <span>Data Pendamping</span>
            </a>
            <a href="{{ route('admin-cdk.verifikasi-rkps') }}" class="q-btn">
                <i class="fas fa-file-alt" style="color:#f59e0b"></i>
                <span>Verifikasi RKPS</span>
            </a>
            <a href="{{ route('admin-cdk.monitoring-nte') }}" class="q-btn">
                <i class="fas fa-chart-line" style="color:#7b1fa2"></i>
                <span>Monitoring NTE</span>
            </a>
        </div>

        {{-- STATS GRID --}}
        <div class="stat-grid">
            <div class="scard" style="border-left:4px solid #2e7d32">
                <div class="scard-info">
                    <h4>Kelompok PS</h4>
                    <div class="val">{{ number_format($totalKelompok) }}</div>
                    <div class="subtext">Unit kelompok dampingan</div>
                </div>
                <div class="scard-icon" style="background:#e8f5e9;color:#2e7d32"><i class="fas fa-users"></i></div>
            </div>
            <div class="scard" style="border-left:4px solid #00796b">
                <div class="scard-info">
                    <h4>Tenaga Pendamping</h4>
                    <div class="val">{{ number_format($totalPendamping) }}</div>
                    <div class="subtext">Pendamping lapangan</div>
                </div>
                <div class="scard-icon" style="background:#e0f2f1;color:#00796b"><i class="fas fa-user-tie"></i></div>
            </div>
            <div class="scard" style="border-left:4px solid #1565c0">
                <div class="scard-info">
                    <h4>Petani Anggota</h4>
                    <div class="val">{{ number_format($totalAnggota) }}</div>
                    <div class="subtext">Pengelola perhutanan sosial</div>
                </div>
                <div class="scard-icon" style="background:#e3f2fd;color:#1565c0"><i class="fas fa-user-friends"></i></div>
            </div>
            <div class="scard" style="border-left:4px solid #7b1fa2">
                <div class="scard-info">
                    <h4>Total Luas Lahan</h4>
                    <div class="val" style="font-size:20px">{{ number_format($totalLuasLahan, 1, ',', '.') }} ha</div>
                    <div class="subtext">Cakupan area kelola</div>
                </div>
                <div class="scard-icon" style="background:#f3e5f5;color:#7b1fa2"><i class="fas fa-map"></i></div>
            </div>
            <div class="scard" style="border-left:4px solid #2e7d32">
                <div class="scard-info">
                    <h4>Nilai Ekonomi (NTE)</h4>
                    <div class="val" style="font-size:17px">Rp {{ number_format($totalNte, 0, ',', '.') }}</div>
                    <div class="subtext">Transaksi komoditas</div>
                </div>
                <div class="scard-icon" style="background:#e8f5e9;color:#2e7d32"><i class="fas fa-money-bill-wave"></i></div>
            </div>
            <div class="scard" style="border-left:4px solid #0284c7">
                <div class="scard-info">
                    <h4>Volume Produksi</h4>
                    <div class="val" style="font-size:20px">{{ number_format($totalVolume, 0, ',', '.') }}</div>
                    <div class="subtext">{{ $komoditasNte }} jenis komoditas</div>
                </div>
                <div class="scard-icon" style="background:#e0f2fe;color:#0284c7"><i class="fas fa-boxes-stacked"></i></div>
            </div>
            <div class="scard" style="border-left:4px solid #f59e0b">
                <div class="scard-info">
                    <h4>RKPS Menunggu</h4>
                    <div class="val" style="color:{{ $pengajuanRkps > 0 ? '#b45309' : '#1e293b' }}">{{ number_format($pengajuanRkps) }}</div>
                    <div class="subtext">Perlu verifikasi CDK</div>
                </div>
                <div class="scard-icon" style="background:#fffbeb;color:#b45309"><i class="fas fa-file-alt"></i></div>
            </div>
            <div class="scard" style="border-left:4px solid #e11d48">
                <div class="scard-info">
                    <h4>RKT Menunggu</h4>
                    <div class="val" style="color:{{ $pengajuanRkt > 0 ? '#e11d48' : '#1e293b' }}">{{ number_format($pengajuanRkt) }}</div>
                    <div class="subtext">Perlu verifikasi CDK</div>
                </div>
                <div class="scard-icon" style="background:#fff1f2;color:#e11d48"><i class="fas fa-file-signature"></i></div>
            </div>
        </div>

        {{-- CHARTS & ACTIVITIES ROW --}}
        <div class="grid-2col">
            {{-- CHART SKEMA PS DI CDK --}}
            <div class="section-box">
                <div class="section-header">
                    <h3><i class="fas fa-chart-pie" style="color:#2e7d32"></i> Sebaran Skema PS di {{ $cdkInfo->nama }}</h3>
                </div>
                <div style="padding:18px;display:flex;justify-content:center;align-items:center;min-height:260px">
                    @if(count($skemaLabels) > 0)
                    <div style="width:100%;max-width:320px;height:240px">
                        <canvas id="chartSkemaCdk"></canvas>
                    </div>
                    @else
                    <div style="text-align:center;color:#94a3b8">
                        <i class="fas fa-chart-pie" style="font-size:32px;display:block;margin-bottom:8px"></i>
                        Belum ada data skema PS untuk wilayah ini.
                    </div>
                    @endif
                </div>
            </div>

            {{-- LAPORAN KEGIATAN TERBARU --}}
            <div class="section-box">
                <div class="section-header">
                    <h3><i class="fas fa-clipboard-list" style="color:#1565c0"></i> Laporan Kegiatan Terbaru di Wilayah</h3>
                    <a href="{{ route('admin-cdk.data-laporan') }}" style="font-size:11px;color:#1565c0;font-weight:600;text-decoration:none">
                        Lihat Semua &rarr;
                    </a>
                </div>
                <div style="overflow-x:auto">
                    <table class="p-table">
                        <thead>
                            <tr>
                                <th>Kelompok</th>
                                <th>Kegiatan</th>
                                <th style="width:100px;text-align:center">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($laporanTerbaru as $l)
                            <tr>
                                <td><strong style="color:#1e293b">{{ $l->nama_kelompok }}</strong></td>
                                <td style="color:#475569">{{ $l->kegiatan }}</td>
                                <td style="text-align:center;color:#64748b;font-size:11px">{{ \Carbon\Carbon::parse($l->tanggal)->format('d/m/Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" style="text-align:center;padding:30px;color:#94a3b8">
                                    Belum ada laporan kegiatan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- DAFTAR KELOMPOK TERBARU --}}
        <div class="section-box">
            <div class="section-header">
                <h3><i class="fas fa-users" style="color:#2e7d32"></i> Kelompok Perhutanan Sosial di {{ $cdkInfo->nama }}</h3>
                <a href="{{ route('admin-cdk.data-kelompok') }}" style="font-size:11px;color:#2e7d32;font-weight:600;text-decoration:none">
                    Buka Data Kelompok &rarr;
                </a>
            </div>
            <div style="overflow-x:auto">
                <table class="p-table">
                    <thead>
                        <tr>
                            <th style="width:40px;text-align:center">No</th>
                            <th>Nama Kelompok</th>
                            <th>Ketua</th>
                            <th>Desa</th>
                            <th>Kecamatan</th>
                            <th>Skema</th>
                            <th style="text-align:right">Luas Lahan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kelompokTerbaru as $i => $k)
                        <tr>
                            <td style="text-align:center;color:#94a3b8">{{ $i + 1 }}</td>
                            <td><strong style="color:#1e293b">{{ $k->nama_kelompok }}</strong></td>
                            <td style="color:#475569">{{ $k->ketua ?? '-' }}</td>
                            <td style="color:#64748b">{{ $k->desa ?? '-' }}</td>
                            <td style="color:#64748b">{{ $k->kecamatan ?? '-' }}</td>
                            <td>
                                @if($k->skema_ps)
                                <span style="background:#e8f5e9;color:#1b5e20;padding:2px 7px;border-radius:4px;font-size:11px;font-weight:600">{{ $k->skema_ps }}</span>
                                @else — @endif
                            </td>
                            <td style="text-align:right;font-weight:600;color:#2e7d32">
                                {{ $k->luas_lahan ? number_format($k->luas_lahan, 2, ',', '.') . ' ha' : '—' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="text-align:center;padding:35px;color:#94a3b8">
                                Belum ada data kelompok di wilayah ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
@if($cdkInfo && count($skemaLabels) > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function(){
    var ctx = document.getElementById('chartSkemaCdk');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: @json($skemaLabels),
                datasets: [{
                    data: @json($skemaValues),
                    backgroundColor: ['#2e7d32', '#1565c0', '#f59e0b', '#7b1fa2', '#e11d48', '#0284c7'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { font: { family: 'Poppins', size: 11 }, boxWidth: 12, padding: 12 }
                    }
                },
                cutout: '65%'
            }
        });
    }
})();
</script>
@endif
@endpush