<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anggota Pendampingan - SI PS ONLINE</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Poppins', Arial, sans-serif; background: #f4f6f9; display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background: #1b5e20; min-height: 100vh; color: white; flex-shrink: 0; position: fixed; top: 0; left: 0; bottom: 0; overflow-y: auto; z-index: 100; }
        .sidebar h2 { text-align: center; padding: 20px 10px; margin: 0; border-bottom: 1px solid rgba(255,255,255,0.2); font-size: 18px; background: #144e18; }
        .sidebar-category { font-size: 10px; font-weight: 700; text-transform: uppercase; color: #a5d6a7; padding: 14px 15px 4px 15px; letter-spacing: 0.8px; }
        .sidebar a { display: flex; align-items: center; justify-content: space-between; padding: 10px 15px; color: white; text-decoration: none; font-size: 14px; transition: 0.2s; position: relative; }
        .sidebar a:hover { background: rgba(255,255,255,0.15); }
        .sidebar a.active { background: rgba(255,255,255,0.2); font-weight: bold; }
        .sidebar a.active::after { content: ''; position: absolute; right: 15px; width: 6px; height: 6px; background: #ffffff; border-radius: 50%; box-shadow: 0 0 6px #ffffff; }
        .main { margin-left: 260px; flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .header { background: #2e7d32; color: white; padding: 12px 25px; display: flex; justify-content: space-between; align-items: center; width: 100%; }
        .header-left { display: flex; align-items: center; gap: 10px; font-size: 13px; }
        .header-right { display: flex; align-items: center; gap: 15px; font-size: 13px; }
        .notification-wrapper { position: relative; display: inline-block; }
        .notification-icon { position: relative; color: white; cursor: pointer; padding: 5px; display: flex; align-items: center; font-size: 16px; }
        .notification-badge { position: absolute; top: -2px; right: -5px; background: #d32f2f; color: white; font-size: 10px; padding: 2px 5px; border-radius: 50%; font-weight: bold; }
        .notification-dropdown { display: none; position: absolute; right: 0; top: 35px; width: 280px; background: white; color: #333; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.2); z-index: 1000; border: 1px solid #ddd; }
        .notification-dropdown.show { display: block; }
        .notif-header { background: #1b5e20; color: white; padding: 10px 15px; font-size: 13px; font-weight: bold; display: flex; justify-content: space-between; }
        .notif-item { padding: 8px 15px; font-size: 13px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; color: #333; }
        .content { padding: 25px; }
        .box { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        h2 { color: #1b5e20; margin-top: 0; font-size: 20px; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; margin-top: 10px; }
        th { background: #1b5e20; color: white; padding: 10px; text-align: left; border: 1px solid #ddd; }
        td { padding: 10px; border: 1px solid #ddd; color: #333; vertical-align: middle; }
        tr:nth-child(even) { background: #f9f9f9; }
        tr:hover { background: #e8f5e9; }
        @media (max-width: 700px) { .content { padding: 15px; } }
    </style>
</head>
<body>
    <aside class="sidebar">
        <h2>SI PS ONLINE</h2>
        <div style="padding: 10px 0;">
            <a href="{{ route('pendamping.dashboard') }}">
                <span><i class="fas fa-home" style="width: 20px;"></i> Dashboard</span>
            </a>
            <div class="sidebar-category">Data Saya</div>
            <a href="{{ route('pendamping.kelompok-saya') }}">
                <span><i class="fas fa-users" style="width: 20px;"></i> Kelompok Saya</span>
            </a>
            <a href="{{ route('pendamping.anggota') }}" class="active">
                <span><i class="fas fa-user-friends" style="width: 20px;"></i> Total Anggota</span>
            </a>
            <div class="sidebar-category">Pengajuan</div>
            <a href="{{ route('pendamping.pengajuan-rkps') }}">
                <span><i class="fas fa-file-alt" style="width: 20px;"></i> Pengajuan RKPS</span>
            </a>
            <a href="{{ route('pendamping.pengajuan-rkt') }}">
                <span><i class="fas fa-file-signature" style="width: 20px;"></i> Pengajuan RKT</span>
            </a>
        </div>
    </aside>
    <div class="main">
        <header class="header">
            <div class="header-left">
                <img src="{{ url('/assets/logo-jatim.png') }}" style="height: 40px;">
                <div><b>Dinas Kehutanan Provinsi Jawa Timur</b><br>CDK Wilayah Kota Madiun</div>
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
            <div class="box">
                <h2>Anggota Kelompok Dampingan</h2>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 40px; text-align: center;">No</th>
                            <th>Nama Anggota</th>
                            <th>NIK</th>
                            <th style="text-align: center;">Jenis Kelamin</th>
                            <th>Kelompok</th>
                            <th style="text-align: center;">Luas Garapan</th>
                            <th>Komoditas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($anggota as $i => $a)
                        <tr>
                            <td style="text-align: center;">{{ $i + 1 }}</td>
                            <td><strong>{{ $a->nama_anggota ?? '-' }}</strong></td>
                            <td>{{ $a->nik ?? '-' }}</td>
                            <td style="text-align: center;">{{ $a->jenis_kelamin ?? '-' }}</td>
                            <td>{{ $a->nama_kelompok ?? '-' }}</td>
                            <td style="text-align: center;">{{ $a->luas_garapan ?? '-' }}</td>
                            <td>{{ $a->komoditas_utama ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 20px; color: #777;">Belum ada anggota.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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