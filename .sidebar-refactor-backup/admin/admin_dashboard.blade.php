<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin — SI PS ONLINE</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #f4f6f8; color: #263238; min-height: 100vh; }
        .layout { display: flex; min-height: 100vh; }

        .sidebar { width: 260px; background: #1b5e20; color: white; flex-shrink: 0; display: flex; flex-direction: column; }
        .brand { padding: 21px 20px; font-size: 18px; font-weight: 700; background: #144e18; border-bottom: 1px solid rgba(255,255,255,.12); }
        .menu { padding: 10px 0; flex: 1; overflow-y: auto; }
        .category { padding: 15px 15px 5px; color: #a5d6a7; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; }
        .menu-item { display: flex; justify-content: space-between; align-items: center; padding: 10px 15px; color: white; text-decoration: none; font-size: 13px; transition: .2s; }
        .menu-item:hover, .menu-item.active { background: rgba(255,255,255,.16); }
        .menu-item i { width: 22px; }
        .badge { background: #d32f2f; color: white; padding: 2px 7px; border-radius: 10px; font-size: 11px; font-weight: 700; }
        .logout-box { border-top: 1px solid rgba(255,255,255,.12); padding: 10px 0; }
        .logout-box button { width: 100%; border: 0; background: transparent; color: #ffcdd2; cursor: pointer; text-align: left; font: inherit; }

        .main { flex: 1; min-width: 0; }
        .header { background: #1b5e20; color: white; padding: 15px 25px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,.1); }
        .header-title { font-size: 16px; font-weight: 700; }
        .header-subtitle { font-size: 12px; font-weight: 400; margin-top: 3px; }
        .header-user { font-size: 13px; }
        .header-user form { display: inline; }
        .header-user button { background: none; border: 0; color: white; cursor: pointer; font-weight: 700; font-size: 13px; }
        .content { padding: 25px; }

        .card-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 18px; margin-bottom: 20px; }
        .card { background: white; padding: 20px; border-radius: 7px; text-align: center; box-shadow: 0 1px 4px rgba(0,0,0,.09); border: 1px solid #e2e8f0; }
        .card h3 { color: #607d8b; font-size: 13px; font-weight: 500; margin-bottom: 9px; }
        .value { color: #1b5e20; font-size: 25px; font-weight: 700; }
        .value.red { color: #d32f2f; }
        .value.money { color: #2e7d32; font-size: 20px; }
        .subtext { color: #78909c; font-size: 11px; margin-top: 4px; }
        .card.green { background: #e8f5e9; }

        .table-box { background: white; border-radius: 7px; padding: 20px; box-shadow: 0 1px 4px rgba(0,0,0,.09); border: 1px solid #e2e8f0; }
        .table-box h2 { color: #263238; font-size: 16px; margin-bottom: 15px; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th { background: #1b5e20; color: white; padding: 10px; text-align: left; font-weight: 500; }
        td { padding: 10px; border-bottom: 1px solid #e2e8f0; }
        tr:hover { background: #f8faf9; }
        .role { display: inline-block; padding: 3px 8px; border-radius: 10px; font-size: 11px; }
        .role-admin { background: #ffebee; color: #c62828; }
        .role-pendamping { background: #e3f2fd; color: #1565c0; }
        .role-kelompok { background: #e8f5e9; color: #2e7d32; }

        @media (max-width: 1000px) { .card-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .sidebar { width: 220px; } }
        @media (max-width: 700px) { .layout { display: block; } .sidebar { width: 100%; min-height: auto; } .menu { max-height: 300px; } .header { padding: 14px 16px; } .header-user { display: none; } .content { padding: 16px; } .card-grid { grid-template-columns: 1fr 1fr; gap: 10px; } .card { padding: 14px 8px; } }
        @media (max-width: 420px) { .card-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="brand">SI PS ONLINE</div>

        <nav class="menu">
            <a href="{{ route('admin.dashboard') }}" class="menu-item active">
                <span><i class="fas fa-home"></i> Dashboard</span>
            </a>

            <div class="category">Data Master</div>
            <a href="{{ route('admin.data-kelompok') }}" class="menu-item"><span><i class="fas fa-users"></i> Data Kelompok</span></a>
            <a href="{{ route('admin.peta-kelompok') }}" class="menu-item"><span><i class="fas fa-map"></i> Peta Kelompok</span></a>
            <a href="{{ route('admin.data-pendamping') }}" class="menu-item"><span><i class="fas fa-user-tie"></i> Data Pendamping</span></a>
            <a href="{{ route('admin.data-anggota') }}" class="menu-item"><span><i class="fas fa-user-friends"></i> Data Anggota</span></a>

            <div class="category">Verifikasi &amp; Berkas</div>
            <a href="{{ route('admin.verifikasi-rkps') }}" class="menu-item"><span><i class="fas fa-file-alt"></i> Verifikasi RKPS</span><span class="badge">{{ $pengajuanRkps }}</span></a>
            <a href="{{ route('admin.verifikasi-rkt') }}" class="menu-item"><span><i class="fas fa-file-signature"></i> Verifikasi RKT</span><span class="badge">{{ $pengajuanRkt }}</span></a>
            <a href="{{ route('admin.data-rkps') }}" class="menu-item"><span><i class="fas fa-check-circle"></i> Input RKPS Sah</span></a>
            <a href="{{ route('admin.data-rkt') }}" class="menu-item"><span><i class="fas fa-check-double"></i> Input RKT Sah</span></a>

            <div class="category">Monitoring</div>
            <a href="{{ route('admin.monitoring-pengajuan') }}" class="menu-item"><span><i class="fas fa-clock"></i> Monitoring Pengajuan</span></a>
            <a href="{{ route('admin.monitoring-ps') }}" class="menu-item"><span><i class="fas fa-desktop"></i> Monitoring PS</span></a>
            <a href="{{ route('admin.monitoring-nte') }}" class="menu-item"><span><i class="fas fa-chart-line"></i> Monitoring NTE</span></a>

            <div class="category">Sistem &amp; Laporan</div>
            <a href="{{ route('admin.data-laporan') }}" class="menu-item"><span><i class="fas fa-print"></i> Laporan</span></a>
            <a href="{{ route('admin.data-user') }}" class="menu-item"><span><i class="fas fa-user-cog"></i> Data User</span></a>
            <a href="{{ route('admin.aktivitas-user') }}" class="menu-item"><span><i class="fas fa-list-check"></i> Aktivitas User</span></a>
        </nav>

        <div class="logout-box">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="menu-item"><span><i class="fas fa-sign-out-alt"></i> Logout</span></button>
            </form>
        </div>
    </aside>

    <main class="main">
        <header class="header">
            <div>
                <div class="header-title">Dinas Kehutanan Provinsi Jawa Timur</div>
                <div class="header-subtitle">CDK Wilayah Kota Madiun</div>
            </div>
            <div class="header-user">
                Selamat datang <strong>{{ Auth::user()->username }}</strong> |
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </div>
        </header>

        <section class="content">
            <div class="card-grid">
                <div class="card"><h3>Total Kelompok</h3><div class="value">{{ number_format($totalKelompok, 0, ',', '.') }}</div></div>
                <div class="card"><h3>Total Admin</h3><div class="value">{{ number_format($totalAdmin, 0, ',', '.') }}</div></div>
                <div class="card"><h3>Total Pendamping</h3><div class="value">{{ number_format($totalPendamping, 0, ',', '.') }}</div></div>
                <div class="card"><h3>Pengajuan RKPS</h3><div class="value red">{{ number_format($pengajuanRkps, 0, ',', '.') }}</div><div class="subtext">Menunggu Verifikasi</div></div>
            </div>

            <div class="card-grid">
                <div class="card"><h3>Pengajuan RKT</h3><div class="value red">{{ number_format($pengajuanRkt, 0, ',', '.') }}</div><div class="subtext">Menunggu Verifikasi</div></div>
                <div class="card green"><h3>Total Nilai Ekonomi (NTE)</h3><div class="value money">Rp {{ number_format($totalNte, 0, ',', '.') }}</div></div>
                <div class="card"><h3>Total Volume Produksi</h3><div class="value">{{ number_format($totalVolume, 0, ',', '.') }}</div></div>
                <div class="card"><h3>Komoditas NTE</h3><div class="value">{{ number_format($komoditasNte, 0, ',', '.') }}</div></div>
            </div>

            <div class="table-box">
                <h2>5 User Terakhir</h2>
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>No</th><th>Username</th><th>Role</th><th>Kelompok</th></tr></thead>
                        <tbody>
                        @forelse($userTerakhir as $index => $user)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $user->username }}</td>
                                <td><span class="role role-{{ $user->role }}">{{ $user->role }}</span></td>
                                <td>{{ $user->nama_kelompok ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4">Belum ada data user.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
</div>
</body>
</html>