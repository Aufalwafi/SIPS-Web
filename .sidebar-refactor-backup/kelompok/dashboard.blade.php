<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kelompok - SI PS ONLINE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f4f6f9; display: flex; min-height: 100vh; }
        .sidebar { width: 260px; min-height: 100vh; position: fixed; top: 0; left: 0; bottom: 0; overflow-y: auto; background: #1b5e20; color: #fff; z-index: 100; }
        .sidebar h2 { text-align: center; padding: 20px 10px; margin: 0; background: #144e18; border-bottom: 1px solid rgba(255,255,255,.2); font-size: 18px; }
        .sidebar-category { padding: 14px 15px 4px; color: #a5d6a7; font-size: 10px; font-weight: 700; letter-spacing: .8px; text-transform: uppercase; }
        .sidebar a { display: flex; align-items: center; gap: 8px; padding: 10px 15px; color: #fff; text-decoration: none; font-size: 14px; position: relative; transition: .2s; }
        .sidebar a:hover { background: rgba(255,255,255,.15); }
        .sidebar a.active { background: rgba(255,255,255,.2); font-weight: 700; }
        .sidebar a.active::after { content: ''; position: absolute; right: 15px; width: 6px; height: 6px; background: #fff; border-radius: 50%; box-shadow: 0 0 6px #fff; }
        .main { margin-left: 260px; flex: 1; min-width: 0; }
        .header { background: #2e7d32; color: #fff; padding: 12px 25px; display: flex; justify-content: space-between; align-items: center; }
        .header-left, .header-right { display: flex; align-items: center; }
        .header-left { gap: 10px; font-size: 13px; }
        .header-right { gap: 15px; font-size: 13px; }
        .header-right form { margin: 0; }
        .notification-wrapper { position: relative; }
        .notification-icon { position: relative; padding: 5px; cursor: pointer; font-size: 16px; }
        .notification-badge { position: absolute; top: -2px; right: -5px; min-width: 16px; padding: 2px 5px; background: #d32f2f; color: #fff; border-radius: 50%; font-size: 10px; font-weight: 700; text-align: center; }
        .notification-dropdown { display: none; position: absolute; top: 35px; right: 0; width: 280px; background: #fff; color: #333; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,.2); z-index: 1000; }
        .notification-dropdown.show { display: block; }
        .notif-header { display: flex; justify-content: space-between; padding: 10px 15px; background: #1b5e20; color: #fff; font-size: 13px; font-weight: 700; }
        .notif-item { display: flex; justify-content: space-between; padding: 10px 15px; border-bottom: 1px solid #eee; font-size: 12px; }
        .content { padding: 25px; }
        h2 { margin: 0 0 20px; color: #1b5e20; font-size: 20px; }
        .card-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin: 20px 0 25px; }
        .stat-card { display: flex; justify-content: space-between; align-items: center; padding: 20px; background: #fff; border-left: 5px solid #2e7d32; border-radius: 7px; box-shadow: 0 2px 6px rgba(0,0,0,.08); }
        .stat-card h3 { margin: 0 0 9px; color: #607d8b; font-size: 12px; }
        .value { font-size: 25px; font-weight: 700; color: #1b5e20; }
        .value.red { color: #d32f2f; }
        .subtext { color: #78909c; font-size: 11px; margin-top: 4px; }
        .table-box { margin-bottom: 20px; padding: 20px; background: #fff; border: 1px solid #e2e8f0; border-radius: 7px; box-shadow: 0 2px 6px rgba(0,0,0,.09); }
        .table-box h2 { margin-bottom: 15px; color: #263238; font-size: 16px; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th { padding: 10px; background: #1b5e20; color: #fff; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #e2e8f0; }
        tr:hover { background: #f8faf9; }
        .status { font-weight: 700; }
        .status-Menunggu { color: #e67e22; }
        .status-Disetujui { color: #2e7d32; }
        .status-Ditolak { color: #c62828; }
        .status-Revisi { color: #e65100; }
        @media (max-width: 950px) { .card-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 700px) { .card-grid { grid-template-columns: 1fr; } .content { padding: 15px; } }
    </style>
</head>
<body>

<aside class="sidebar">
    <h2>SI PS ONLINE</h2>
    <div style="padding: 10px 0;">
        <a href="{{ route('kelompok.dashboard') }}" class="active"><i class="fas fa-home" style="width:20px"></i> Dashboard</a>
        <div class="sidebar-category">Data Kelompok</div>
        <a href="{{ route('kelompok.data-kelompok') }}"><i class="fas fa-users" style="width:20px"></i> Data Kelompok Saya</a>
        <a href="{{ route('kelompok.anggota') }}"><i class="fas fa-user-friends" style="width:20px"></i> Anggota Kelompok</a>
        <div class="sidebar-category">Pengajuan Berkas</div>
        <a href="{{ route('kelompok.pengajuan-rkps') }}"><i class="fas fa-file-alt" style="width:20px"></i> Pengajuan RKPS</a>
        <a href="{{ route('kelompok.pengajuan-rkt') }}"><i class="fas fa-file-signature" style="width:20px"></i> Pengajuan RKT</a>
    </div>
</aside>

<main class="main">
    <header class="header">
        <div class="header-left">
            <img src="{{ url('/assets/logo-jatim.png') }}" style="height:40px">
            <div><b>Dinas Kehutanan Provinsi Jawa Timur</b><br>CDK Wilayah Kota Madiun</div>
        </div>
        <div class="header-right">
            <div class="notification-wrapper">
                <div class="notification-icon" id="notifBtn"><i class="fas fa-bell"></i>
                    @if($totalNotif > 0)<span class="notification-badge">{{ $totalNotif }}</span>@endif
                </div>
                <div class="notification-dropdown" id="notifDropdown">
                    <div class="notif-header"><span>Status Pengajuan</span><span>{{ $totalNotif }} Baru</span></div>
                    <div class="notif-item"><span>RKPS Menunggu</span><b>{{ $rkpsMenunggu }}</b></div>
                    <div class="notif-item"><span>RKT Menunggu</span><b>{{ $rktMenunggu }}</b></div>
                </div>
            </div>
            <span>Hi, <b>{{ Auth::user()->nama }}</b></span>
            <span>|</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="background:none;border:0;color:#fff;cursor:pointer;font-weight:bold">Logout</button>
            </form>
        </div>
    </header>

    <div class="content">
        <h2>Dashboard Kelompok</h2>
        <p style="color:#78909c;margin-top:-10px">{{ $kelompok->nama_kelompok ?? 'Kelompok belum terhubung' }}</p>

        <div class="card-grid">
            <div class="stat-card"><div><h3>Total Anggota</h3><div class="value">{{ $totalAnggota }}</div></div><i class="fas fa-user-friends" style="font-size:30px;color:#a5d6a7"></i></div>
            <div class="stat-card" style="border-left-color:#d32f2f"><div><h3>RKPS Menunggu</h3><div class="value red">{{ $rkpsMenunggu }}</div></div><i class="fas fa-file-alt" style="font-size:30px;color:#ffcdd2"></i></div>
            <div class="stat-card" style="border-left-color:#d32f2f"><div><h3>RKT Menunggu</h3><div class="value red">{{ $rktMenunggu }}</div></div><i class="fas fa-file-signature" style="font-size:30px;color:#ffcdd2"></i></div>
            <div class="stat-card"><div><h3>Desa</h3><div class="value" style="font-size:17px">{{ $kelompok->desa ?? '-' }}</div><div class="subtext">{{ $kelompok->kecamatan ?? '-' }}</div></div><i class="fas fa-map-marker-alt" style="font-size:30px;color:#a5d6a7"></i></div>
        </div>

        <div class="table-box">
            <h2>Informasi Kelompok</h2>
            <table>
                <tr><th style="width:180px">Nama Kelompok</th><td>{{ $kelompok->nama_kelompok ?? '-' }}</td></tr>
                <tr><th>Ketua</th><td>{{ $kelompok->ketua ?? '-' }}</td></tr>
                <tr><th>Desa / Kecamatan</th><td>{{ $kelompok->desa ?? '-' }} / {{ $kelompok->kecamatan ?? '-' }}</td></tr>
                <tr><th>Skema</th><td>{{ $kelompok->skema_ps ?? '-' }}</td></tr>
                <tr><th>Luas Lahan</th><td>{{ $kelompok->luas_lahan ?? '-' }} ha</td></tr>
            </table>
        </div>

        <div class="table-box">
            <h2>Status Pengajuan Terakhir</h2>
            <table>
                <thead><tr><th>Jenis</th><th>Tanggal</th><th>Status</th><th>Catatan</th></tr></thead>
                <tbody>
                    <tr><td>RKPS</td><td>{{ $rkpsTerakhir->tanggal_pengajuan ?? '-' }}</td><td class="status status-{{ $rkpsTerakhir->status ?? '' }}">{{ $rkpsTerakhir->status ?? '-' }}</td><td>{{ $rkpsTerakhir->catatan_admin ?? '-' }}</td></tr>
                    <tr><td>RKT</td><td>{{ $rktTerakhir->tanggal_pengajuan ?? '-' }}</td><td class="status status-{{ $rktTerakhir->status ?? '' }}">{{ $rktTerakhir->status ?? '-' }}</td><td>{{ $rktTerakhir->catatan_admin ?? '-' }}</td></tr>
                </tbody>
            </table>
        </div>

        <div class="table-box">
            <h2>Daftar Anggota Kelompok</h2>
            <table>
                <thead><tr><th>No</th><th>Nama Anggota</th><th>NIK</th><th>Jenis Kelamin</th><th>Komoditas</th></tr></thead>
                <tbody>
                    @forelse($anggota as $i => $a)
                        <tr><td>{{ $i + 1 }}</td><td>{{ $a->nama_anggota ?? '-' }}</td><td>{{ $a->nik ?? '-' }}</td><td>{{ $a->jenis_kelamin ?? '-' }}</td><td>{{ $a->komoditas_utama ?? '-' }}</td></tr>
                    @empty
                        <tr><td colspan="5" style="text-align:center">Belum ada anggota.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</main>

<script>
    var b = document.getElementById('notifBtn'),
        d = document.getElementById('notifDropdown');
    if (b) {
        b.addEventListener('click', function(e) {
            e.stopPropagation();
            d.classList.toggle('show');
        });
        window.addEventListener('click', function() {
            d.classList.remove('show');
        });
    }
</script>
</body>
</html>