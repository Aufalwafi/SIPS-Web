<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Admin - SI PS ONLINE</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
*{box-sizing:border-box}
body{margin:0;font-family:Arial,sans-serif;background:#f4f6f9;display:flex;min-height:100vh}

/* SIDEBAR */
.sidebar{width:260px;background:#1b5e20;min-height:100vh;color:white;flex-shrink:0;position:fixed;top:0;left:0;bottom:0;overflow-y:auto;z-index:100}
.sidebar h2{text-align:center;padding:20px 10px;margin:0;border-bottom:1px solid rgba(255,255,255,0.2);font-size:18px;background:#144e18}
.sidebar-category{font-size:10px;font-weight:700;text-transform:uppercase;color:#a5d6a7;padding:14px 15px 4px 15px;letter-spacing:0.8px}
.sidebar a{display:flex;align-items:center;justify-content:space-between;padding:10px 15px;color:white;text-decoration:none;font-size:14px;transition:0.2s;position:relative}
.sidebar a:hover{background:rgba(255,255,255,0.15)}
.sidebar a.active{background:rgba(255,255,255,0.2);font-weight:bold}
.sidebar a.active::after{content:'';position:absolute;right:15px;width:6px;height:6px;background:#ffffff;border-radius:50%;box-shadow:0 0 6px #ffffff}

/* MAIN */
.main{margin-left:260px;flex:1;display:flex;flex-direction:column;min-width:0}
.header{background:#2e7d32;color:white;padding:12px 25px;display:flex;justify-content:space-between;align-items:center;width:100%}
.logo-header{height:40px;margin-right:10px}
.header-left{display:flex;align-items:center;gap:10px;font-size:13px}
.header-right{display:flex;align-items:center;gap:15px;font-size:13px}
.header-right a{color:white;text-decoration:none}

/* NOTIFIKASI */
.notification-wrapper{position:relative;display:inline-block}
.notification-icon{position:relative;color:white;cursor:pointer;padding:5px;display:flex;align-items:center;font-size:16px}
.notification-badge{position:absolute;top:-2px;right:-5px;background:#d32f2f;color:white;font-size:10px;padding:2px 5px;border-radius:50%;font-weight:bold}
.notification-dropdown{display:none;position:absolute;right:0;top:35px;width:280px;background:white;color:#333;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.2);z-index:1000;border:1px solid #ddd}
.notification-dropdown.show{display:block}
.notif-header{background:#1b5e20;color:white;padding:10px 15px;font-size:13px;font-weight:bold;display:flex;justify-content:space-between;align-items:center}
.notif-body{padding:10px 0;max-height:200px;overflow-y:auto}
.notif-item{padding:8px 15px;font-size:13px;border-bottom:1px solid #f0f0f0;display:flex;justify-content:space-between;align-items:center;color:#333;text-decoration:none}
.notif-item:hover{background:#f9f9f9}
.notif-footer{padding:8px;text-align:center;background:#f1f8e9;border-top:1px solid #ddd}
.notif-footer a{color:#1b5e20;font-size:12px;font-weight:bold;text-decoration:none}

/* CONTAINER */
.container{margin:25px}
h2{margin-top:0;color:#1b5e20;margin-bottom:20px;font-size:20px}

/* STATS GRID */
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:15px;margin-bottom:25px}
.stat-card{background:white;padding:20px;border-radius:12px;box-shadow:0 2px 6px rgba(0,0,0,0.08);border-left:5px solid #2e7d32;transition:0.2s;display:flex;justify-content:space-between;align-items:center}
.stat-card:hover{transform:translateY(-3px);box-shadow:0 6px 15px rgba(0,0,0,0.12)}
.stat-card .info h3{margin:0 0 5px 0;font-size:12px;color:#666;text-transform:uppercase}
.stat-card .info .number{font-size:20px;font-weight:bold;color:#1b5e20}
.stat-card .icon-box{font-size:28px;color:#2e7d32;background:#e8f5e9;padding:12px;border-radius:10px}

/* QUICK ACTION */
.quick-action-box{background:white;padding:15px 20px;border-radius:12px;box-shadow:0 2px 6px rgba(0,0,0,0.08);margin-bottom:25px;display:flex;gap:10px;flex-wrap:wrap;align-items:center}
.quick-action-box span{font-weight:bold;color:#1b5e20;margin-right:10px;font-size:13px}
.btn-quick{background:#2e7d32;color:white;padding:8px 14px;border-radius:6px;text-decoration:none;font-size:12px;display:inline-flex;align-items:center;gap:6px;transition:0.2s}
.btn-quick:hover{background:#1b5e20;transform:translateY(-1px)}

/* CHARTS & CONTENT GRID */
.charts-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:25px}
@media(max-width:992px){.charts-grid{grid-template-columns:1fr}}
.card-box{background:white;padding:20px;border-radius:12px;box-shadow:0 2px 6px rgba(0,0,0,0.08)}
.card-box h3{margin-top:0;font-size:15px;color:#1b5e20;border-bottom:1px solid #eee;padding-bottom:10px;margin-bottom:15px;display:flex;align-items:center;gap:8px}
#map{width:100%;height:350px;border-radius:8px;z-index:1}
table{width:100%;border-collapse:collapse;font-size:12px}
th,td{padding:9px 12px;border:1px solid #ddd;text-align:left}
th{background:#2e7d32;color:white}
tr:nth-child(even){background:#f9f9f9}
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
<h2>SI PS ONLINE</h2>
<div style="padding:10px 0">
    <a href="{{ route('admin.dashboard') }}" class="active"><span><i class="fas fa-home" style="width:20px"></i> Dashboard</span></a>
    <div class="sidebar-category">Data Master</div>
    <a href="{{ route('admin.data-kelompok') }}"><span><i class="fas fa-users" style="width:20px"></i> Data Kelompok</span></a>
    <a href="{{ route('admin.peta-kelompok') }}"><span><i class="fas fa-map" style="width:20px"></i> Peta Kelompok</span></a>
    <a href="{{ route('admin.data-pendamping') }}"><span><i class="fas fa-user-tie" style="width:20px"></i> Data Pendamping</span></a>
    <a href="{{ route('admin.data-anggota') }}"><span><i class="fas fa-user-friends" style="width:20px"></i> Data Anggota</span></a>
    <div class="sidebar-category">Verifikasi &amp; Berkas</div>
    <a href="{{ route('admin.verifikasi-rkps') }}"><span><i class="fas fa-file-alt" style="width:20px"></i> Verifikasi RKPS</span></a>
    <a href="{{ route('admin.verifikasi-rkt') }}"><span><i class="fas fa-file-signature" style="width:20px"></i> Verifikasi RKT</span></a>
    <a href="{{ route('admin.data-rkps') }}"><span><i class="fas fa-check-circle" style="width:20px"></i> Input RKPS Sah</span></a>
    <a href="{{ route('admin.data-rkt') }}"><span><i class="fas fa-check-double" style="width:20px"></i> Input RKT Sah</span></a>
    <div class="sidebar-category">Monitoring</div>
    <a href="{{ route('admin.monitoring-pengajuan') }}"><span><i class="fas fa-clock" style="width:20px"></i> Monitoring Pengajuan</span></a>
    <a href="{{ route('admin.monitoring-ps') }}"><span><i class="fas fa-desktop" style="width:20px"></i> Monitoring PS</span></a>
    <a href="{{ route('admin.monitoring-nte') }}"><span><i class="fas fa-chart-line" style="width:20px"></i> Monitoring NTE</span></a>
    <div class="sidebar-category">Sistem &amp; Laporan</div>
    <a href="{{ route('admin.data-laporan') }}"><span><i class="fas fa-print" style="width:20px"></i> Laporan</span></a>
    <a href="{{ route('admin.data-user') }}"><span><i class="fas fa-user-cog" style="width:20px"></i> Data User</span></a>
    <a href="{{ route('admin.aktivitas-user') }}"><span><i class="fas fa-list-check" style="width:20px"></i> Aktivitas User</span></a>
</div>
</div>

<div class="main">
<!-- HEADER -->
<div class="header">
    <div class="header-left">
        <img src="{{ url('/assets/logo-jatim.png') }}" class="logo-header">
        <div><b>Dinas Kehutanan Provinsi Jawa Timur</b><br>CDK Wilayah Kota Madiun</div>
    </div>
    <div class="header-right">
        <div class="notification-wrapper">
            <div class="notification-icon" id="notifBtn" title="Notifikasi Berkas">
                <i class="fas fa-bell"></i>
                @php $totalNotif = $pengajuanRkps + $pengajuanRkt; @endphp
                @if($totalNotif > 0)
                    <span class="notification-badge">{{ $totalNotif }}</span>
                @endif
            </div>
            <div class="notification-dropdown" id="notifDropdown">
                <div class="notif-header">
                    <span>Notifikasi Berkas</span>
                    <span style="font-size:11px;background:rgba(0,0,0,0.2);padding:2px 6px;border-radius:4px">{{ $totalNotif }} Baru</span>
                </div>
                <div class="notif-body">
                    <a href="{{ route('admin.verifikasi-rkps') }}" class="notif-item">
                        <span>Pengajuan RKPS Menunggu</span>
                        <span style="background:#d32f2f;color:white;padding:2px 6px;border-radius:10px;font-size:11px;font-weight:bold">{{ $pengajuanRkps }}</span>
                    </a>
                    <a href="{{ route('admin.verifikasi-rkt') }}" class="notif-item">
                        <span>Pengajuan RKT Menunggu</span>
                        <span style="background:#d32f2f;color:white;padding:2px 6px;border-radius:10px;font-size:11px;font-weight:bold">{{ $pengajuanRkt }}</span>
                    </a>
                </div>
                <div class="notif-footer"><a href="{{ route('admin.monitoring-pengajuan') }}">Lihat Semua Pengajuan &rarr;</a></div>
            </div>
        </div>
        <span>Hi, <b>{{ Auth::user()->username }}</b></span> |
        <a href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit()">Logout</a>
        <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display:none">@csrf</form>
    </div>
</div>

<div class="container">
    <h2>Dashboard Administrator</h2>

    <!-- 1. STATS CARDS -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="info"><h3>Total Kelompok</h3><div class="number">{{ number_format($totalKelompok) }}</div></div>
            <div class="icon-box"><i class="fas fa-users"></i></div>
        </div>
        <div class="stat-card">
            <div class="info"><h3>Total Admin</h3><div class="number">{{ number_format($totalAdmin) }}</div></div>
            <div class="icon-box"><i class="fas fa-user-shield"></i></div>
        </div>
        <div class="stat-card">
            <div class="info"><h3>Total Pendamping</h3><div class="number">{{ number_format($totalPendamping) }}</div></div>
            <div class="icon-box"><i class="fas fa-user-tie"></i></div>
        </div>
        <div class="stat-card" style="border-left-color:#d32f2f">
            <div class="info"><h3>RKPS Menunggu</h3><div class="number" style="color:#d32f2f">{{ number_format($pengajuanRkps) }}</div></div>
            <div class="icon-box" style="color:#d32f2f;background:#ffebee"><i class="fas fa-file-alt"></i></div>
        </div>
        <div class="stat-card" style="border-left-color:#d32f2f">
            <div class="info"><h3>RKT Menunggu</h3><div class="number" style="color:#d32f2f">{{ number_format($pengajuanRkt) }}</div></div>
            <div class="icon-box" style="color:#d32f2f;background:#ffebee"><i class="fas fa-file-signature"></i></div>
        </div>
        <div class="stat-card">
            <div class="info"><h3>Total NTE</h3><div class="number" style="font-size:15px">Rp {{ number_format($totalNte,0,',','.') }}</div></div>
            <div class="icon-box"><i class="fas fa-chart-line"></i></div>
        </div>
        <div class="stat-card">
            <div class="info"><h3>Volume Produksi</h3><div class="number" style="font-size:15px">{{ number_format($totalVolume,0,',','.') }} Ton</div></div>
            <div class="icon-box"><i class="fas fa-boxes"></i></div>
        </div>
        <div class="stat-card">
            <div class="info"><h3>Komoditas</h3><div class="number">{{ number_format($komoditasNte) }} Jenis</div></div>
            <div class="icon-box"><i class="fas fa-seedling"></i></div>
        </div>
    </div>

    <!-- 6. QUICK ACTION -->
    <div class="quick-action-box">
        <span><i class="fas fa-bolt"></i> Quick Actions:</span>
        <a href="{{ route('admin.data-kelompok') }}" class="btn-quick"><i class="fas fa-plus"></i> Tambah Kelompok</a>
        <a href="{{ route('admin.data-pendamping') }}" class="btn-quick"><i class="fas fa-user-plus"></i> Tambah Pendamping</a>
        <a href="{{ route('admin.verifikasi-rkps') }}" class="btn-quick"><i class="fas fa-check-circle"></i> Verifikasi RKPS</a>
        <a href="{{ route('admin.verifikasi-rkt') }}" class="btn-quick"><i class="fas fa-check-double"></i> Verifikasi RKT</a>
        <a href="{{ route('admin.data-laporan') }}" class="btn-quick" style="background:#1565c0"><i class="fas fa-print"></i> Cetak Laporan</a>
    </div>

    <!-- 2. GRAFIK DASHBOARD -->
    <div class="charts-grid">
        <div class="card-box">
            <h3><i class="fas fa-chart-bar"></i> Statistik Pengajuan Berkas</h3>
            <canvas id="barChartPengajuan" height="130"></canvas>
        </div>
        <div class="card-box">
            <h3><i class="fas fa-chart-pie"></i> Statistik Komoditas Kelompok</h3>
            <canvas id="pieChartKomoditas" height="130"></canvas>
        </div>
    </div>

    <!-- 7. PETA & 3. AKTIVITAS TERBARU -->
    <div class="charts-grid">
        <div class="card-box">
            <h3><i class="fas fa-map-marked-alt"></i> Peta Persebaran Kelompok PS</h3>
            <div id="map"></div>
        </div>
        <div class="card-box">
            <h3><i class="fas fa-history"></i> Aktivitas Login User Terbaru</h3>
            <table>
                <tr><th>Username</th><th>Role</th><th>Kelompok</th></tr>
                @forelse($userTerakhir as $u)
                <tr>
                    <td>{{ $u->username }}</td>
                    <td>{{ ucfirst($u->role) }}</td>
                    <td>{{ $u->nama_kelompok ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="3">Belum ada aktivitas.</td></tr>
                @endforelse
            </table>
        </div>
    </div>
</div>
</div>

<!-- Chart.js & Leaflet JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// Toggle Notifikasi Header
var notifBtn = document.getElementById('notifBtn');
var notifDropdown = document.getElementById('notifDropdown');
if(notifBtn){
    notifBtn.addEventListener('click', function(e){
        e.stopPropagation();
        notifDropdown.classList.toggle('show');
    });
    window.addEventListener('click', function(){
        if(notifDropdown.classList.contains('show')) notifDropdown.classList.remove('show');
    });
}

// Bar Chart Pengajuan
var ctxBar = document.getElementById('barChartPengajuan').getContext('2d');
new Chart(ctxBar, {
    type: 'bar',
    data: {
        labels: ['Menunggu', 'Disetujui', 'Ditolak'],
        datasets: [{
            label: 'RKPS',
            data: [{{ $pengajuanRkps }}, {{ $rkpsDisetujui }}, {{ $rkpsDitolak }}],
            backgroundColor: '#ffb300'
        }, {
            label: 'RKT',
            data: [{{ $pengajuanRkt }}, {{ $rktDisetujui }}, {{ $rktDitolak }}],
            backgroundColor: '#2e7d32'
        }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
});

// Pie Chart Komoditas
var ctxPie = document.getElementById('pieChartKomoditas').getContext('2d');
new Chart(ctxPie, {
    type: 'doughnut',
    data: {
        labels: ['Kopi', 'Madu', 'Bambu', 'Jati', 'Alpukat', 'Lainnya'],
        datasets: [{
            data: [12, 8, 5, 15, 6, 10],
            backgroundColor: ['#1b5e20', '#2e7d32', '#4caf50', '#81c784', '#a5d6a7', '#c8e6c9']
        }]
    },
    options: { responsive: true }
});

// Inisialisasi Peta Leaflet
var map = L.map('map').setView([-7.6298, 111.5239], 11);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '© OpenStreetMap'
}).addTo(map);
L.marker([-7.6298, 111.5239]).addTo(map)
    .bindPopup('<b>CDK Wilayah Kota Madiun</b><br>Pusat Koordinasi Perhutanan Sosial.');
</script>
</body>
</html>