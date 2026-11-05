@extends('layouts.app')
@section('title', 'Dashboard Admin Provinsi - SI PS ONLINE')

@push('styles')
<style>
/* Dashboard Styles */
.dash-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    margin-bottom: 22px;
    flex-wrap: wrap;
}
.dash-title h2 {
    color: #1b5e20;
    font-size: 22px;
    font-weight: 700;
    margin: 0 0 4px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.dash-title p {
    color: #64748b;
    font-size: 13px;
    margin: 0;
}

/* Filter Dropdown */
.cdk-filter-box {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #fff;
    padding: 6px 14px;
    border-radius: 8px;
    border: 1px solid #cbd5e1;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
.cdk-filter-box label {
    font-size: 12px;
    font-weight: 600;
    color: #475569;
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 6px;
}
.cdk-filter-box select {
    height: 34px;
    padding: 2px 10px;
    font-size: 13px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    background: #f8fafc;
    color: #1e293b;
    font-weight: 600;
    cursor: pointer;
}
.cdk-filter-box select:focus {
    border-color: #2e7d32;
    outline: none;
}

/* Alert Verifikasi */
.alert-verif {
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    border: 1px solid #fde68a;
    border-left: 5px solid #f59e0b;
    border-radius: 8px;
    padding: 14px 18px;
    margin-bottom: 22px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.alert-verif-text {
    display: flex;
    align-items: center;
    gap: 12px;
    color: #92400e;
    font-size: 13px;
}
.alert-verif-text i {
    font-size: 22px;
    color: #d97706;
}
.alert-verif-actions {
    display: flex;
    gap: 8px;
}
.btn-verif-action {
    background: #d97706;
    color: #fff;
    padding: 6px 14px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
}
.btn-verif-action:hover {
    background: #b45309;
    color: #fff;
    transform: translateY(-1px);
}

/* Stats Cards Grid */
.stat-cards-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 22px;
}
.stat-card-modern {
    background: #fff;
    padding: 16px 18px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    display: flex;
    align-items: center;
    gap: 14px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.stat-card-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.stat-icon-wrapper {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
}
.stat-card-modern.green .stat-icon-wrapper { background: #e8f5e9; color: #2e7d32; }
.stat-card-modern.teal .stat-icon-wrapper { background: #e0f2f1; color: #00796b; }
.stat-card-modern.blue .stat-icon-wrapper { background: #e3f2fd; color: #1565c0; }
.stat-card-modern.purple .stat-icon-wrapper { background: #f3e5f5; color: #7b1fa2; }
.stat-card-modern.indigo .stat-icon-wrapper { background: #e8eaf6; color: #303f9f; }
.stat-card-modern.amber .stat-icon-wrapper { background: #fff3e0; color: #e65100; }
.stat-card-modern.cyan .stat-icon-wrapper { background: #e0f7fa; color: #00838f; }
.stat-card-modern.red .stat-icon-wrapper { background: #ffebee; color: #c62828; }

.stat-info-wrap h4 {
    color: #64748b;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 0 0 3px;
}
.stat-info-wrap .stat-main-val {
    color: #1e293b;
    font-size: 20px;
    font-weight: 700;
    line-height: 1.2;
}

/* Quick Nav Actions */
.quick-nav-bar {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 22px;
}
.quick-nav-link {
    background: #fff;
    color: #334155;
    border: 1px solid #cbd5e1;
    padding: 8px 14px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03);
}
.quick-nav-link:hover {
    background: #2e7d32;
    color: #fff;
    border-color: #2e7d32;
    transform: translateY(-1px);
}

/* Charts Grid */
.charts-grid {
    display: grid;
    grid-template-columns: 1.25fr 1fr;
    gap: 18px;
    margin-bottom: 22px;
}
.chart-box {
    background: #fff;
    border-radius: 10px;
    padding: 18px 20px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 10px;
}
.chart-header h3 {
    margin: 0;
    font-size: 14px;
    font-weight: 700;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Bottom 2 Columns */
.bottom-grid {
    display: grid;
    grid-template-columns: 1.55fr 1fr;
    gap: 18px;
}
.bottom-box {
    background: #fff;
    border-radius: 10px;
    padding: 18px 20px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.bottom-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 14px;
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 10px;
}
.bottom-header h3 {
    margin: 0;
    font-size: 14px;
    font-weight: 700;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 8px;
}

.table-dash {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
}
.table-dash th {
    background: #f8fafc;
    color: #475569;
    font-weight: 600;
    text-align: left;
    padding: 9px 12px;
    border-bottom: 1px solid #e2e8f0;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.table-dash td {
    padding: 9px 12px;
    border-bottom: 1px solid #f1f5f9;
    color: #334155;
    vertical-align: middle;
}
.table-dash tr:hover td {
    background: #f8fafc;
}

@media(max-width: 1100px) {
    .stat-cards-grid { grid-template-columns: repeat(2, 1fr); }
    .charts-grid { grid-template-columns: 1fr; }
    .bottom-grid { grid-template-columns: 1fr; }
}
@media(max-width: 600px) {
    .stat-cards-grid { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')
<div class="content">

    {{-- DASHBOARD HEADER --}}
    <div class="dash-header">
        <div class="dash-title">
            <h2><i class="fas fa-gauge-high" style="color:#2e7d32"></i> Dashboard Admin Provinsi Jawa Timur</h2>
            <p>Sistem Informasi Perhutanan Sosial Online — Dinas Kehutanan Provinsi Jawa Timur</p>
        </div>

        <form method="GET" action="{{ route('admin.dashboard') }}" class="cdk-filter-box">
            <label><i class="fas fa-sitemap" style="color:#2e7d32"></i> Filter Wilayah CDK:</label>
            <select name="id_cdk" onchange="this.form.submit()">
                <option value="">Seluruh Jawa Timur (Semua CDK)</option>
                @foreach($daftarCdk as $c)
                    <option value="{{ $c->id }}" @selected($idCdk == $c->id)>{{ $c->nama }} ({{ $c->kode }})</option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- ALERT VERIFIKASI PENDING --}}
    @if(($pengajuanRkps + $pengajuanRkt) > 0)
    <div class="alert-verif">
        <div class="alert-verif-text">
            <i class="fas fa-bell"></i>
            <div>
                <strong>Perhatian: Dokumen Pengajuan Menunggu Tindakan Verifikasi!</strong>
                <div>Terdapat <strong>{{ $pengajuanRkps }}</strong> pengajuan RKPS dan <strong>{{ $pengajuanRkt }}</strong> pengajuan RKT yang memerlukan verifikasi Admin.</div>
            </div>
        </div>
        <div class="alert-verif-actions">
            @if($pengajuanRkps > 0)
                <a href="{{ route('admin.verifikasi-rkps') }}" class="btn-verif-action">
                    <i class="fas fa-file-signature"></i> Verifikasi RKPS ({{ $pengajuanRkps }})
                </a>
            @endif
            @if($pengajuanRkt > 0)
                <a href="{{ route('admin.verifikasi-rkt') }}" class="btn-verif-action">
                    <i class="fas fa-clipboard-check"></i> Verifikasi RKT ({{ $pengajuanRkt }})
                </a>
            @endif
        </div>
    </div>
    @endif

    {{-- STATS CARDS GRID --}}
    <div class="stat-cards-grid">
        <div class="stat-card-modern green">
            <div class="stat-icon-wrapper"><i class="fas fa-users"></i></div>
            <div class="stat-info-wrap">
                <h4>Total Kelompok PS</h4>
                <div class="stat-main-val">{{ number_format($totalKelompok) }} <span style="font-size:11px;color:#64748b;font-weight:400">Unit</span></div>
            </div>
        </div>

        <div class="stat-card-modern teal">
            <div class="stat-icon-wrapper"><i class="fas fa-expand"></i></div>
            <div class="stat-info-wrap">
                <h4>Total Luas Lahan</h4>
                <div class="stat-main-val">{{ number_format($totalLuasLahan, 0, ',', '.') }} <span style="font-size:11px;color:#64748b;font-weight:400">Ha</span></div>
            </div>
        </div>

        <div class="stat-card-modern blue">
            <div class="stat-icon-wrapper"><i class="fas fa-user-tie"></i></div>
            <div class="stat-info-wrap">
                <h4>Tenaga Pendamping</h4>
                <div class="stat-main-val">{{ number_format($totalPendamping) }} <span style="font-size:11px;color:#64748b;font-weight:400">Orang</span></div>
            </div>
        </div>

        <div class="stat-card-modern purple">
            <div class="stat-icon-wrapper"><i class="fas fa-user-friends"></i></div>
            <div class="stat-info-wrap">
                <h4>Anggota Petani</h4>
                <div class="stat-main-val">{{ number_format($totalAnggota) }} <span style="font-size:11px;color:#64748b;font-weight:400">Orang</span></div>
            </div>
        </div>

        <div class="stat-card-modern indigo">
            <div class="stat-icon-wrapper"><i class="fas fa-sitemap"></i></div>
            <div class="stat-info-wrap">
                <h4>Wilayah CDK</h4>
                <div class="stat-main-val">{{ number_format($totalCdk) }} <span style="font-size:11px;color:#64748b;font-weight:400">CDK</span></div>
            </div>
        </div>

        <div class="stat-card-modern amber">
            <div class="stat-icon-wrapper"><i class="fas fa-coins"></i></div>
            <div class="stat-info-wrap">
                <h4>Nilai Transaksi NTE</h4>
                <div class="stat-main-val" style="font-size:15px">Rp {{ number_format($totalNte, 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="stat-card-modern cyan">
            <div class="stat-icon-wrapper"><i class="fas fa-clipboard-list"></i></div>
            <div class="stat-info-wrap">
                <h4>Laporan Kegiatan</h4>
                <div class="stat-main-val">{{ number_format($totalLaporan) }} <span style="font-size:11px;color:#64748b;font-weight:400">Laporan</span></div>
            </div>
        </div>

        <div class="stat-card-modern red">
            <div class="stat-icon-wrapper"><i class="fas fa-hourglass-half"></i></div>
            <div class="stat-info-wrap">
                <h4>Pengajuan Menunggu</h4>
                <div class="stat-main-val" style="color:#c62828">{{ number_format($pengajuanRkps + $pengajuanRkt) }} <span style="font-size:11px;color:#64748b;font-weight:400">Berkas</span></div>
            </div>
        </div>
    </div>

    {{-- QUICK NAVIGATION SHORTCUTS --}}
    <div class="quick-nav-bar">
        <a href="{{ route('admin.data-kelompok') }}" class="quick-nav-link"><i class="fas fa-users"></i> Data Kelompok</a>
        <a href="{{ route('admin.data-pendamping') }}" class="quick-nav-link"><i class="fas fa-user-tie"></i> Data Pendamping</a>
        <a href="{{ route('admin.admin-cdk') }}" class="quick-nav-link"><i class="fas fa-building-user"></i> Admin CDK &amp; Rekap</a>
        <a href="{{ route('admin.peta-kelompok') }}" class="quick-nav-link"><i class="fas fa-map-marked-alt"></i> Peta Kelompok</a>
        <a href="{{ route('admin.monitoring-pengajuan') }}" class="quick-nav-link"><i class="fas fa-file-lines"></i> Monitoring Pengajuan</a>
        <a href="{{ route('admin.monitoring-nte') }}" class="quick-nav-link"><i class="fas fa-chart-line"></i> Monitoring NTE</a>
        <a href="{{ route('admin.data-user') }}" class="quick-nav-link"><i class="fas fa-users-gear"></i> Manajemen User</a>
        <a href="{{ route('admin.aktivitas-user') }}" class="quick-nav-link"><i class="fas fa-list-check"></i> Aktivitas User</a>
    </div>

    {{-- CHARTS GRID --}}
    <div class="charts-grid">
        {{-- BAR CHART STATUS PENGAJUAN --}}
        <div class="chart-box">
            <div class="chart-header">
                <h3><i class="fas fa-chart-column" style="color:#2e7d32"></i> Status Pengajuan Berkas (RKPS &amp; RKT)</h3>
                <span style="font-size:11px;color:#64748b">Total Berkas: {{ $pengajuanRkps + $rkpsDisetujui + $rkpsDitolak + $pengajuanRkt + $rktDisetujui + $rktDitolak }}</span>
            </div>
            <div style="height:260px;position:relative">
                <canvas id="barChartPengajuan"></canvas>
            </div>
        </div>

        {{-- DONUT CHART SEBARAN SKEMA --}}
        <div class="chart-box">
            <div class="chart-header">
                <h3><i class="fas fa-chart-pie" style="color:#1565c0"></i> Komposisi Skema Perhutanan Sosial</h3>
                <span style="font-size:11px;color:#64748b">{{ $sebaranSkema->count() }} Skema PS</span>
            </div>
            <div style="height:260px;position:relative;display:flex;align-items:center;justify-content:center">
                @if($sebaranSkema->count() > 0)
                    <canvas id="donutChartSkema"></canvas>
                @else
                    <div style="color:#94a3b8;font-size:12px">Belum ada data skema.</div>
                @endif
            </div>
        </div>
    </div>

    {{-- BOTTOM TABLES GRID --}}
    <div class="bottom-grid">
        {{-- REKAPITULASI WILAYAH CDK --}}
        <div class="bottom-box">
            <div class="bottom-header">
                <h3><i class="fas fa-table-list" style="color:#2e7d32"></i> Rekapitulasi per Wilayah CDK</h3>
                <a href="{{ route('admin.admin-cdk') }}" style="font-size:12px;color:#2e7d32;font-weight:600;text-decoration:none">Lihat Detail &rarr;</a>
            </div>
            <div style="overflow-x:auto">
                <table class="table-dash">
                    <thead>
                        <tr>
                            <th style="width:35px;text-align:center">No</th>
                            <th>Wilayah CDK</th>
                            <th style="text-align:center">Kelompok</th>
                            <th style="text-align:center">Pendamping</th>
                            <th style="text-align:center">Anggota</th>
                            <th style="text-align:right">Luas (Ha)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekapCdk as $i => $r)
                        <tr>
                            <td style="text-align:center;color:#64748b">{{ $i + 1 }}</td>
                            <td>
                                <strong style="color:#1e293b">{{ $r->nama }}</strong>
                                <span style="font-size:10px;background:#f1f5f9;padding:1px 5px;border-radius:3px;color:#475569;margin-left:4px">{{ $r->kode }}</span>
                            </td>
                            <td style="text-align:center;font-weight:600">{{ number_format($r->jumlah_kelompok) }}</td>
                            <td style="text-align:center">{{ number_format($r->jumlah_pendamping) }}</td>
                            <td style="text-align:center">{{ number_format($r->jumlah_anggota) }}</td>
                            <td style="text-align:right;font-weight:600;color:#2e7d32">{{ number_format($r->luas_lahan, 2, ',', '.') }} ha</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align:center;color:#94a3b8;padding:15px">Data CDK belum tersedia.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- USER TERBARU --}}
        <div class="bottom-box">
            <div class="bottom-header">
                <h3><i class="fas fa-clock-rotate-left" style="color:#1565c0"></i> User Terbaru</h3>
                <a href="{{ route('admin.data-user') }}" style="font-size:12px;color:#1565c0;font-weight:600;text-decoration:none">Data User &rarr;</a>
            </div>
            <div style="overflow-x:auto">
                <table class="table-dash">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th style="text-align:center">Role</th>
                            <th>Entitas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($userTerakhir as $u)
                        <tr>
                            <td>
                                <div style="font-weight:600;color:#1e293b">{{ $u->username }}</div>
                                <div style="font-size:10px;color:#94a3b8">{{ $u->nama ?? '-' }}</div>
                            </td>
                            <td style="text-align:center">
                                @if($u->role === 'admin')
                                    <span style="background:#e8f5e9;color:#1b5e20;padding:2px 6px;border-radius:4px;font-size:10px;font-weight:600">Admin</span>
                                @elseif($u->role === 'pendamping')
                                    <span style="background:#e0f2f1;color:#00796b;padding:2px 6px;border-radius:4px;font-size:10px;font-weight:600">Pendamping</span>
                                @elseif($u->role === 'admin_cdk')
                                    <span style="background:#e3f2fd;color:#1565c0;padding:2px 6px;border-radius:4px;font-size:10px;font-weight:600">Admin CDK</span>
                                @else
                                    <span style="background:#f1f5f9;color:#475569;padding:2px 6px;border-radius:4px;font-size:10px;font-weight:600">{{ ucfirst($u->role) }}</span>
                                @endif
                            </td>
                            <td>
                                <span style="font-size:11px;color:#64748b">{{ $u->nama_kelompok ?? '-' }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" style="text-align:center;color:#94a3b8;padding:15px">Belum ada aktivitas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Bar Chart Pengajuan RKPS vs RKT
new Chart(document.getElementById('barChartPengajuan').getContext('2d'), {
    type: 'bar',
    data: {
        labels: ['Menunggu Tindakan', 'Disetujui', 'Ditolak'],
        datasets: [
            {
                label: 'Pengajuan RKPS',
                data: [{{ $pengajuanRkps }}, {{ $rkpsDisetujui }}, {{ $rkpsDitolak }}],
                backgroundColor: ['#f59e0b', '#22c55e', '#ef4444'],
                borderRadius: 4
            },
            {
                label: 'Pengajuan RKT',
                data: [{{ $pengajuanRkt }}, {{ $rktDisetujui }}, {{ $rktDitolak }}],
                backgroundColor: ['#d97706', '#15803d', '#b91c1c'],
                borderRadius: 4
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'top', labels: { boxWidth: 12, font: { family: 'Poppins', size: 11 } } }
        },
        scales: {
            y: { beginAtZero: true, ticks: { precision: 0 } }
        }
    }
});

// Donut Chart Skema PS
@if($sebaranSkema->count() > 0)
new Chart(document.getElementById('donutChartSkema').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: @json($sebaranSkema->pluck('skema_ps')),
        datasets: [{
            data: @json($sebaranSkema->pluck('total')),
            backgroundColor: [
                '#2e7d32', '#1565c0', '#f59e0b', '#7b1fa2', '#00897b',
                '#d81b60', '#3949ab', '#f4511e', '#546e7a'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'right',
                labels: { boxWidth: 12, font: { family: 'Poppins', size: 11 } }
            }
        },
        cutout: '60%'
    }
});
@endif
</script>
@endpush