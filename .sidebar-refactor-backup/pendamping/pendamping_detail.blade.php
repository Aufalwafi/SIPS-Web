<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Kelompok - SI PS ONLINE</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Poppins', Arial, sans-serif; background: #f4f6f9; display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background: #1b5e20; min-height: 100vh; color: white; flex-shrink: 0; position: fixed; top: 0; left: 0; bottom: 0; overflow-y: auto; z-index: 100; }
        .sidebar h2 { text-align: center; padding: 20px 10px; margin: 0; border-bottom: 1px solid rgba(255,255,255,0.2); font-size: 18px; background: #144e18; }
        .sidebar-category { font-size: 10px; font-weight: 700; text-transform: uppercase; color: #a5d6a7; padding: 14px 15px 4px; letter-spacing: 0.8px; }
        .sidebar a { display: flex; align-items: center; justify-content: space-between; padding: 10px 15px; color: white; text-decoration: none; font-size: 14px; transition: 0.2s; position: relative; }
        .sidebar a:hover { background: rgba(255,255,255,0.15); }
        .sidebar a.active { background: rgba(255,255,255,0.2); font-weight: bold; }
        .sidebar a.active::after { content: ''; position: absolute; right: 15px; width: 6px; height: 6px; background: #ffffff; border-radius: 50%; box-shadow: 0 0 6px #ffffff; }
        .sidebar .logout-box { border-top: 1px solid rgba(255,255,255,0.2); padding: 10px 0; }
        .sidebar .logout-box button { width: 100%; border: 0; background: transparent; color: #ffcdd2; cursor: pointer; text-align: left; padding: 10px 15px; display: flex; align-items: center; gap: 10px; font-size: 14px; }
        .main { margin-left: 260px; flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .header { background: #2e7d32; color: white; padding: 12px 25px; display: flex; justify-content: space-between; align-items: center; width: 100%; }
        .header-left { display: flex; align-items: center; gap: 10px; font-size: 13px; }
        .header-right { display: flex; align-items: center; gap: 15px; font-size: 13px; }
        .content { padding: 25px; }
        .section { background: white; border-radius: 8px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
        .section h2 { color: #1b5e20; font-size: 20px; margin-top: 0; margin-bottom: 15px; }
        .section h3 { color: #1b5e20; font-size: 16px; margin-top: 0; }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table th { padding: 10px; text-align: left; font-weight: 600; color: #333; width: 180px; }
        .info-table td { padding: 10px; color: #555; }
        .info-table tr { border-bottom: 1px solid #f0f0f0; }
        .doc-box { border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px; margin-top: 10px; }
        .doc-box h3 { font-size: 14px; margin: 0 0 10px; }
        .doc-item { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f5f5f5; }
        .doc-item:last-child { border-bottom: none; }
        .doc-label { font-weight: 600; color: #333; }
        .doc-value { color: #555; }
        .btn-view { display: inline-block; background: #1565c0; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 12px; transition: 0.2s; }
        .btn-view:hover { background: #0d47a1; }
        .btn-back { display: inline-block; background: #1b5e20; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-size: 13px; transition: 0.2s; }
        .btn-back:hover { background: #144e18; }
        #map { width: 100%; height: 350px; border-radius: 8px; margin-top: 10px; }
        .menu-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-top: 15px; }
        .menu-btn { display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 16px; border-radius: 6px; color: white; text-decoration: none; font-size: 13px; font-weight: 500; transition: 0.2s; text-align: center; }
        .menu-btn:hover { opacity: 0.9; transform: translateY(-1px); }
        .btn-green { background: #2e7d32; }
        .btn-blue { background: #1565c0; }
        .btn-orange { background: #e65100; }
        .btn-red { background: #c62828; }
        .btn-teal { background: #00695c; }
        @media (max-width: 900px) {
            .menu-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>

<aside class="sidebar">
    <h2>SI PS ONLINE</h2>
    <div style="padding: 10px 0;">
        <a href="{{ route('pendamping.dashboard') }}">
            <span><i class="fas fa-home" style="width:20px"></i> Dashboard</span>
        </a>
        <div class="sidebar-category">Data Saya</div>
        <a href="{{ route('pendamping.kelompok-saya') }}" class="active">
            <span><i class="fas fa-users" style="width:20px"></i> Kelompok Saya</span>
        </a>
        <a href="{{ route('pendamping.anggota') }}">
            <span><i class="fas fa-user-friends" style="width:20px"></i> Total Anggota</span>
        </a>
        <div class="sidebar-category">Pengajuan</div>
        <a href="{{ route('pendamping.pengajuan-rkps') }}">
            <span><i class="fas fa-file-alt" style="width:20px"></i> Pengajuan RKPS</span>
        </a>
        <a href="{{ route('pendamping.pengajuan-rkt') }}">
            <span><i class="fas fa-file-signature" style="width:20px"></i> Pengajuan RKT</span>
        </a>
    </div>
    <div class="logout-box">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"><i class="fas fa-sign-out-alt"></i> Logout</button>
        </form>
    </div>
</aside>

<div class="main">
    <header class="header">
        <div class="header-left">
            <img src="{{ url('/assets/logo-jatim.png') }}" style="height:40px">
            <div>
                <b>Dinas Kehutanan Provinsi Jawa Timur</b><br>
                CDK Wilayah Kota Madiun
            </div>
        </div>
        <div class="header-right">
            <div class="notification-wrapper">
                <div class="notification-icon" id="notifBtn">
                    <i class="fas fa-bell"></i>
                    @if(($rkpsMenunggu+$rktMenunggu) > 0)
                        <span class="notification-badge">{{ $rkpsMenunggu+$rktMenunggu }}</span>
                    @endif
                </div>
                <div class="notification-dropdown" id="notifDropdown">
                    <div class="notif-header">
                        <span>Status Pengajuan</span>
                        <span>{{ $rkpsMenunggu+$rktMenunggu }} Baru</span>
                    </div>
                    <div class="notif-item"><span>RKPS Menunggu</span><b>{{ $rkpsMenunggu }}</b></div>
                    <div class="notif-item"><span>RKT Menunggu</span><b>{{ $rktMenunggu }}</b></div>
                </div>
            </div>
            <span>Hi, <b>{{ Auth::user()->nama }}</b></span>
            <span>|</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="background:none;border:0;color:white;cursor:pointer;font-weight:bold">Logout</button>
            </form>
        </div>
    </header>

    <div class="content">

        <a href="{{ route('pendamping.kelompok-saya') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Kembali ke Kelompok Saya
        </a>

        <!-- PETA WILAYAH -->
        <div class="section" style="margin-top:20px">
            <h2><i class="fas fa-map-marked-alt"></i> Peta Wilayah Kelompok</h2>
            <div id="map"></div>
        </div>

        <!-- PROFIL KELOMPOK -->
        <div class="section">
            <h2>Profil Kelompok</h2>
            <table class="info-table">
                <tr><th>Nama Kelompok</th><td>{{ $kelompok->nama_kelompok ?? '-' }}</td></tr>
                <tr><th>Ketua</th><td>{{ $kelompok->ketua ?? '-' }}</td></tr>
                <tr><th>Desa</th><td>{{ $kelompok->desa ?? '-' }}</td></tr>
                <tr><th>Kecamatan</th><td>{{ $kelompok->kecamatan ?? '-' }}</td></tr>
                <tr><th>Kabupaten</th><td>{{ $kelompok->kabupaten ?? '-' }}</td></tr>
                <tr><th>Skema PS</th><td>{{ $kelompok->skema_ps ?? '-' }}</td></tr>
                <tr><th>Luas Lahan</th><td>{{ $kelompok->luas_lahan ?? '-' }} Ha</td></tr>
            </table>
        </div>

        <!-- DOKUMEN PERENCANAAN -->
        <div class="section">
            <h2>Dokumen Perencanaan</h2>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
                <div class="doc-box">
                    <h3>RKPS</h3>
                    <div class="doc-item">
                        <div><span class="doc-label">Periode</span><br><span class="doc-value">{{ $rkpsTerakhir->tahun_mulai ?? '-' }} - {{ $rkpsTerakhir->tahun_selesai ?? '-' }}</span></div>
                        @if(!empty($rkpsTerakhir->file_rkps))
                            <a href="{{ asset('uploads/rkps/' . $rkpsTerakhir->file_rkps) }}" target="_blank" class="btn-view">
                                <i class="fas fa-eye"></i> Lihat
                            </a>
                        @else
                            <span style="color:#999;font-size:12px">Belum ada dokumen</span>
                        @endif
                    </div>
                </div>
                <div class="doc-box">
                    <h3>RKT</h3>
                    <div class="doc-item">
                        <div><span class="doc-label">Tahun</span><br><span class="doc-value">{{ $rktTerakhir->tahun ?? '-' }}</span></div>
                        @if(!empty($rktTerakhir->file_rkt))
                            <a href="{{ asset('uploads/rkt/' . $rktTerakhir->file_rkt) }}" target="_blank" class="btn-view">
                                <i class="fas fa-eye"></i> Lihat
                            </a>
                        @else
                            <span style="color:#999;font-size:12px">Belum ada dokumen</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- MENU KELOMPOK -->
        <div class="section">
            <h2>Menu Kelompok</h2>
            <div class="menu-grid">
                <a href="{{ route('pendamping.anggota') }}" class="menu-btn btn-green">
                    <i class="fas fa-user-friends"></i> Data Anggota
                </a>
                <a href="{{ route('pendamping.pengajuan-rkps') }}" class="menu-btn btn-green">
                    <i class="fas fa-file-alt"></i> Pengajuan RKPS
                </a>
                <a href="{{ route('pendamping.pengajuan-rkt') }}" class="menu-btn btn-green">
                    <i class="fas fa-file-signature"></i> Pengajuan RKT
                </a>
                <a href="{{ route('pendamping.dashboard') }}" class="menu-btn btn-blue">
                    <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>

    </div>
</div>

<script>
    // PETA KELOMPOK
    var map = L.map('map').setView([-7.6298, 111.5239], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 18
    }).addTo(map);

    @if(!empty($kelompok->polygon_kelompok))
        var polygonData = {!! $kelompok->polygon_kelompok !!};
        if (polygonData) {
            L.polygon(polygonData.coordinates[0], {color: '#2e7d32', fillOpacity: 0.3}).addTo(map);
        }
    @endif

    @if(!empty($kelompok->lat) && !empty($kelompok->lng))
        L.marker([{{ $kelompok->lat }}, {{ $kelompok->lng }}]).addTo(map)
            .bindPopup('{{ $kelompok->nama_kelompok }}');
    @endif

    // NOTIFIKASI
    var notifBtn = document.getElementById('notifBtn');
    var notifDropdown = document.getElementById('notifDropdown');
    if (notifBtn) {
        notifBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            notifDropdown.classList.toggle('show');
        });
        window.addEventListener('click', function() {
            notifDropdown.classList.remove('show');
        });
    }
</script>

</body>
</html>