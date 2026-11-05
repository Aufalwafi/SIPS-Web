<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pendamping - SI PS ONLINE</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *{box-sizing:border-box}
        body{margin:0;font-family:'Poppins',Arial,sans-serif;background:#f4f6f9;display:flex;min-height:100vh}
        .sidebar{width:260px;background:#1b5e20;min-height:100vh;color:white;flex-shrink:0;position:fixed;top:0;left:0;bottom:0;overflow-y:auto;z-index:100}
        .sidebar h2{text-align:center;padding:20px 10px;margin:0;border-bottom:1px solid rgba(255,255,255,.2);font-size:18px;background:#144e18}
        .sidebar-category{font-size:10px;font-weight:700;text-transform:uppercase;color:#a5d6a7;padding:14px 15px 4px;letter-spacing:.8px}
        .sidebar a{display:flex;align-items:center;justify-content:space-between;padding:10px 15px;color:white;text-decoration:none;font-size:14px;position:relative}
        .sidebar a:hover,.sidebar a.active{background:rgba(255,255,255,.15)}
        .sidebar a.active::after{content:'';position:absolute;right:15px;width:6px;height:6px;background:#fff;border-radius:50%}
        .main{margin-left:260px;flex:1;min-width:0}
        .header{background:#2e7d32;color:white;padding:12px 25px;display:flex;justify-content:space-between;align-items:center}
        .header-left{display:flex;align-items:center;gap:10px;font-size:13px}
        .header-right{display:flex;align-items:center;gap:15px;font-size:13px}
        .notification-wrapper{position:relative}
        .notification-icon{position:relative;color:white;cursor:pointer;padding:5px;display:flex;align-items:center;font-size:16px}
        .notification-badge{position:absolute;top:-2px;right:-5px;background:#d32f2f;color:white;font-size:10px;padding:2px 5px;border-radius:50%;font-weight:bold}
        .notification-dropdown{display:none;position:absolute;right:0;top:35px;width:280px;background:white;color:#333;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,.2);z-index:1000;border:1px solid #ddd}
        .notification-dropdown.show{display:block}
        .notif-header{background:#1b5e20;color:white;padding:10px 15px;font-size:13px;font-weight:bold;display:flex;justify-content:space-between}
        .notif-item{padding:8px 15px;font-size:13px;border-bottom:1px solid #f0f0f0;display:flex;justify-content:space-between;align-items:center;color:#333}
        .content{padding:25px}
        .card-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;margin-bottom:20px}
        .card{background:white;padding:20px;border-radius:7px;text-align:center;box-shadow:0 1px 4px rgba(0,0,0,.09);border:1px solid #e2e8f0}
        .card h3{color:#607d8b;font-size:13px;font-weight:500;margin-bottom:9px}
        .value{color:#1b5e20;font-size:25px;font-weight:700}
        .value.red{color:#d32f2f}
        .subtext{color:#78909c;font-size:11px;margin-top:4px}
        .table-box{background:white;border-radius:7px;padding:20px;box-shadow:0 1px 4px rgba(0,0,0,.09);border:1px solid #e2e8f0;margin-bottom:20px}
        .table-box h2{color:#263238;font-size:16px;margin-bottom:15px}
        table{width:100%;border-collapse:collapse;font-size:12px}
        th{background:#1b5e20;color:white;padding:10px;text-align:left;font-weight:500}
        td{padding:10px;border-bottom:1px solid #e2e8f0}
        tr:hover{background:#f8faf9}
        @media(max-width:1000px){.card-grid{grid-template-columns:repeat(2,1fr)}}
        @media(max-width:700px){.card-grid{grid-template-columns:1fr}}
    </style>
</head>
<body>
    <aside class="sidebar">
        <h2>SI PS ONLINE</h2>
        <div style="padding:10px 0">
            <a href="{{ route('pendamping.dashboard') }}" class="active"><span><i class="fas fa-home" style="width:20px"></i> Dashboard</span></a>
            <div class="sidebar-category">Data Saya</div>
            <a href="{{ route('pendamping.kelompok-saya') }}"><span><i class="fas fa-users" style="width:20px"></i> Kelompok Saya</span></a>
            <a href="{{ route('pendamping.anggota') }}"><span><i class="fas fa-user-friends" style="width:20px"></i> Total Anggota</span></a>
            <div class="sidebar-category">Pengajuan</div>
            <a href="{{ route('pendamping.pengajuan-rkps') }}"><span><i class="fas fa-file-alt" style="width:20px"></i> Pengajuan RKPS</span></a>
            <a href="{{ route('pendamping.pengajuan-rkt') }}"><span><i class="fas fa-file-signature" style="width:20px"></i> Pengajuan RKT</span></a>
        </div>
    </aside>
    <div class="main">
        <header class="header">
            <div class="header-left">
                <img src="{{ url('/assets/logo-jatim.png') }}" style="height:40px">
                <div><b>Dinas Kehutanan Provinsi Jawa Timur</b><br>CDK Wilayah Kota Madiun</div>
            </div>
            <div class="header-right">
                <div class="notification-wrapper">
                    <div class="notification-icon" id="notifBtn"><i class="fas fa-bell"></i>@if($totalNotif>0)<span class="notification-badge">{{ $totalNotif }}</span>@endif</div>
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
                    <button type="submit" style="background:none;border:0;color:white;cursor:pointer;font-weight:bold">Logout</button>
                </form>
            </div>
        </header>
        <div class="content">
            <h2 style="color:#1b5e20;margin-bottom:5px">Dashboard Pendamping</h2>
            <p style="color:#78909c;margin-bottom:20px">Selamat datang, {{ Auth::user()->nama }}</p>
            <div class="card-grid">
                <div class="card"><h3>Kelompok Dampingan</h3><div class="value">{{ $totalKelompok }}</div></div>
                <div class="card"><h3>Total Anggota</h3><div class="value">{{ $totalAnggota }}</div></div>
                <div class="card"><h3>RKPS Menunggu</h3><div class="value red">{{ $rkpsMenunggu }}</div><div class="subtext">Menunggu Verifikasi</div></div>
                <div class="card"><h3>RKT Menunggu</h3><div class="value red">{{ $rktMenunggu }}</div><div class="subtext">Menunggu Verifikasi</div></div>
            </div>
            <div class="table-box">
                <h2>Kelompok yang Dampingi</h2>
                <table>
                    <thead><tr><th>No</th><th>Nama Kelompok</th><th>Desa</th><th>Kecamatan</th><th>Skema</th><th style="text-align:center">Jumlah Anggota</th></tr></thead>
                    <tbody>
                        @forelse($kelompok as $i => $k)
                            <tr><td>{{ $i+1 }}</td><td style="font-weight:600">{{ $k->nama_kelompok }}</td><td>{{ $k->desa ?? '-' }}</td><td>{{ $k->kecamatan ?? '-' }}</td><td>{{ $k->skema_ps ?? '-' }}</td><td style="text-align:center">{{ $anggotaPerKelompok[$k->id] ?? 0 }}</td></tr>
                        @empty
                            <tr><td colspan="6" style="text-align:center">Anda belum mendampingi kelompok manapun.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($laporanTerakhir->count())
                <div class="table-box">
                    <h2>Laporan Kegiatan Terakhir</h2>
                    <table>
                        <thead><tr><th>Tanggal</th><th>Kelompok</th><th>Kegiatan</th></tr></thead>
                        <tbody>
                            @foreach($laporanTerakhir as $l)
                                <tr><td>{{ $l->tanggal }}</td><td>{{ $l->nama_kelompok }}</td><td>{{ $l->kegiatan ?? '-' }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
    <script>
        const notifBtn = document.getElementById('notifBtn');
        const notifDropdown = document.getElementById('notifDropdown');
        if (notifBtn) {
            notifBtn.addEventListener('click', function(event) {
                event.stopPropagation();
                notifDropdown.classList.toggle('show');
            });
            window.addEventListener('click', function() {
                notifDropdown.classList.remove('show');
            });
        }
    </script>
</body>
</html>