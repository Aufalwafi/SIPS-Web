<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Perencanaan Perhutanan Sosial - SI PS ONLINE</title>
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
        .sidebar .badge { background: #d32f2f; color: white; padding: 2px 7px; border-radius: 10px; font-size: 11px; font-weight: 700; }
        .main { margin-left: 260px; flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .header { background: #2e7d32; color: white; padding: 12px 25px; display: flex; justify-content: space-between; align-items: center; width: 100%; }
        .header-left { display: flex; align-items: center; gap: 10px; font-size: 13px; }
        .header-right { display: flex; align-items: center; gap: 15px; font-size: 13px; }
        .header-right a { color: white; text-decoration: none; }
        .notification-wrapper { position: relative; display: inline-block; }
        .notification-icon { position: relative; color: white; cursor: pointer; padding: 5px; display: flex; align-items: center; font-size: 16px; }
        .notification-badge { position: absolute; top: -2px; right: -5px; background: #d32f2f; color: white; font-size: 10px; padding: 2px 5px; border-radius: 50%; font-weight: bold; }
        .notification-dropdown { display: none; position: absolute; right: 0; top: 35px; width: 280px; background: white; color: #333; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.2); z-index: 1000; border: 1px solid #ddd; }
        .notification-dropdown.show { display: block; }
        .notif-header { background: #1b5e20; color: white; padding: 10px 15px; font-size: 13px; font-weight: bold; display: flex; justify-content: space-between; align-items: center; }
        .notif-body { padding: 10px 0; max-height: 200px; overflow-y: auto; }
        .notif-item { padding: 8px 15px; font-size: 13px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; color: #333; text-decoration: none; }
        .notif-item:hover { background: #f9f9f9; }
        .notif-footer { padding: 8px; text-align: center; background: #f1f8e9; border-top: 1px solid #ddd; }
        .notif-footer a { color: #1b5e20; font-size: 12px; font-weight: bold; text-decoration: none; }
        .container { margin: 25px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        .toolbar { margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
        .toolbar div { display: flex; align-items: center; gap: 8px; }
        .search-box { padding: 6px 12px; width: 250px; border: 1px solid #ccc; border-radius: 4px; font-size: 12px; font-family: 'Poppins', sans-serif; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th { background: #2e7d32; color: white; padding: 10px; text-align: center; vertical-align: middle; border: 1px solid #ddd; }
        th.kelompok { text-align: left; }
        td { padding: 10px; border: 1px solid #ddd; text-align: center; color: #333; vertical-align: middle; }
        td.nama { text-align: left; font-weight: 600; }
        tr:nth-child(even) { background: #f9f9f9; }
        tr:hover { background: #e8f5e9; }
        .ok { color: #2e7d32; font-weight: bold; font-size: 14px; }
        .tidak { color: #c62828; font-weight: bold; font-size: 14px; }
        .btn { padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; color: white; text-decoration: none; display: inline-block; transition: 0.2s; font-size: 12px; }
        .btn:hover { opacity: 0.9; transform: translateY(-1px); }
        .btn-print { background: #1565c0; }
        .kop, .tanggal-print { display: none; }
        @media print {
            body { background: white; margin: 0; display: block; }
            .sidebar, .header, .toolbar { display: none; }
            .container { margin: 0; padding: 10px 20px; box-shadow: none; border-radius: 0; }
            .kop { display: flex; align-items: center; border-bottom: 3px solid black; padding-bottom: 10px; margin-bottom: 10px; }
            .logo-print { width: 70px; height: auto; margin-right: 15px; }
            .judul { text-align: center; flex: 1; }
            .judul h3 { margin: 0; font-size: 14px; }
            .judul h2 { margin: 2px 0; font-size: 18px; }
            .judul h4 { margin: 2px 0; font-size: 14px; }
            .judul p { margin: 0; font-size: 12px; }
            .tanggal-print { display: block; text-align: right; font-size: 11px; margin-bottom: 10px; }
            table { box-shadow: none; }
            th, td { border: 1px solid #000; padding: 6px; font-size: 11px; }
        }
    </style>
</head>
<body>

<aside class="sidebar">
    <h2>SI PS ONLINE</h2>
    <div style="padding: 10px 0;">
        <a href="{{ route('admin.dashboard') }}">
            <span><i class="fas fa-home" style="width: 20px;"></i> Dashboard</span>
        </a>
        <div class="sidebar-category">Data Master</div>
        <a href="{{ route('admin.data-kelompok') }}">
            <span><i class="fas fa-users" style="width: 20px;"></i> Data Kelompok</span>
        </a>
        <a href="{{ route('admin.peta-kelompok') }}"><span><i class="fas fa-map" style="width: 20px;"></i> Peta Kelompok</span></a>
        <a href="{{ route('admin.data-pendamping') }}">
            <span><i class="fas fa-user-tie" style="width: 20px;"></i> Data Pendamping</span>
        </a>
        <a href="{{ route('admin.data-anggota') }}">
            <span><i class="fas fa-user-friends" style="width: 20px;"></i> Data Anggota</span>
        </a>
        <div class="sidebar-category">Verifikasi &amp; Berkas</div>
        <a href="{{ route('admin.verifikasi-rkps') }}">
            <span><i class="fas fa-file-alt" style="width: 20px;"></i> Verifikasi RKPS</span><span class="badge">{{ $rkpsMenunggu }}</span>
        </a>
        <a href="{{ route('admin.verifikasi-rkt') }}">
            <span><i class="fas fa-file-signature" style="width: 20px;"></i> Verifikasi RKT</span><span class="badge">{{ $rktMenunggu }}</span>
        </a>
        <a href="{{ route('admin.data-rkps') }}"><span><i class="fas fa-check-circle" style="width: 20px;"></i> Input RKPS Sah</span></a>
        <a href="{{ route('admin.data-rkt') }}"><span><i class="fas fa-check-double" style="width: 20px;"></i> Input RKT Sah</span></a>
        <div class="sidebar-category">Monitoring</div>
        <a href="{{ route('admin.monitoring-pengajuan') }}">
            <span><i class="fas fa-clock" style="width: 20px;"></i> Monitoring Pengajuan</span>
        </a>
        <a href="{{ route('admin.monitoring-ps') }}" class="active">
            <span><i class="fas fa-desktop" style="width: 20px;"></i> Monitoring PS</span>
        </a>
        <a href="{{ route('admin.monitoring-nte') }}"><span><i class="fas fa-chart-line" style="width: 20px;"></i> Monitoring NTE</span></a>
        <div class="sidebar-category">Sistem &amp; Laporan</div>
        <a href="{{ route('admin.data-laporan') }}"><span><i class="fas fa-print" style="width: 20px;"></i> Laporan</span></a>
        <a href="{{ route('admin.data-user') }}"><span><i class="fas fa-user-cog" style="width: 20px;"></i> Data User</span></a><a href="{{ route('admin.aktivitas-user') }}"><span><i class="fas fa-list-check" style="width: 20px;"></i> Aktivitas User</span></a>
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
                <div class="notification-icon" id="notifBtn" title="Notifikasi Berkas">
                    <i class="fas fa-bell"></i>
                    @if($totalNotif > 0)
                        <span class="notification-badge">{{ $totalNotif }}</span>
                    @endif
                </div>
                <div class="notification-dropdown" id="notifDropdown">
                    <div class="notif-header">
                        <span>Notifikasi Berkas</span>
                        <span style="font-size: 11px; background: rgba(0,0,0,0.2); padding: 2px 6px; border-radius: 4px;">{{ $totalNotif }} Baru</span>
                    </div>
                    <div class="notif-body">
                        <a href="{{ route('admin.verifikasi-rkps') }}" class="notif-item">
                            <span>Pengajuan RKPS Menunggu</span>
                            <span style="background: #d32f2f; color: white; padding: 2px 6px; border-radius: 10px; font-size: 11px; font-weight: bold;">{{ $rkpsMenunggu }}</span>
                        </a>
                        <a href="{{ route('admin.verifikasi-rkt') }}" class="notif-item">
                            <span>Pengajuan RKT Menunggu</span>
                            <span style="background: #d32f2f; color: white; padding: 2px 6px; border-radius: 10px; font-size: 11px; font-weight: bold;">{{ $rktMenunggu }}</span>
                        </a>
                    </div>
                    <div class="notif-footer"><a href="{{ route('admin.monitoring-pengajuan') }}">Lihat Semua Pengajuan &rarr;</a></div>
                </div>
            </div>
            <span>Hi, <b>{{ Auth::user()->username }}</b></span> |
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit" style="background:none; border:0; color:white; cursor:pointer; font-weight:bold; font-size:13px;">Logout</button>
            </form>
        </div>
    </header>

    <div class="container">
        <div class="kop">
            <img src="{{ url('/assets/logo-jatim.png') }}" class="logo-print">
            <div class="judul">
                <h3>PEMERINTAH PROVINSI JAWA TIMUR</h3>
                <h2>DINAS KEHUTANAN</h2>
                <h4>CABANG DINAS KEHUTANAN WILAYAH KOTA MADIUN</h4>
                <p><b>MONITORING PERENCANAAN PERHUTANAN SOSIAL</b></p>
            </div>
        </div>
        <div class="tanggal-print">Tanggal Cetak: {{ now()->format('d F Y') }}</div>

        <h2 style="color:#1b5e20; margin-top:0;">Monitoring Perencanaan Perhutanan Sosial</h2>
        <p style="color:#555; font-size:13px;">Periode RKT {{ $tahunAwal }} - {{ $tahunAkhir }}</p>

        <div class="toolbar">
            <div>
                <input type="text" id="search" class="search-box" placeholder="🔍 Cari kelompok...">
            </div>
            <div>
                <button class="btn btn-print" onclick="window.print()"><i class="fas fa-print"></i> Cetak</button>
            </div>
        </div>

        <table id="myTable">
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th class="kelompok">Kelompok</th>
                    <th style="width: 70px;">RKPS</th>
                    @for($t = $tahunAwal; $t <= $tahunAkhir; $t++)
                        <th style="width: 60px;">{{ $t }}</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @forelse($kelompok as $no => $k)
                <tr>
                    <td>{{ $no + 1 }}</td>
                    <td class="nama">{{ $k->nama_kelompok }}</td>
                    <td>
                        @if(($rkpsCount[$k->id] ?? 0) > 0)
                            <span class="ok">✔</span>
                        @else
                            <span class="tidak">✖</span>
                        @endif
                    </td>
                    @for($t = $tahunAwal; $t <= $tahunAkhir; $t++)
                        <td>
                            @if(($rktCount[$k->id][$t] ?? 0) > 0)
                                <span class="ok">✔</span>
                            @else
                                <span class="tidak">✖</span>
                            @endif
                        </td>
                    @endfor
                </tr>
                @empty
                <tr><td colspan="{{ 3 + ($tahunAkhir - $tahunAwal + 1) }}" style="text-align: center;">Belum ada data kelompok.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
var notifBtn = document.getElementById('notifBtn');
var notifDropdown = document.getElementById('notifDropdown');
if (notifBtn) {
    notifBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        notifDropdown.classList.toggle('show');
    });
    window.addEventListener('click', function() {
        if (notifDropdown.classList.contains('show')) {
            notifDropdown.classList.remove('show');
        }
    });
}

document.getElementById("search").addEventListener("keyup", function() {
    var filter = this.value.toLowerCase();
    var rows = document.querySelectorAll("#myTable tbody tr");
    rows.forEach(function(row) {
        var text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
    });
});
</script>
</body>
</html>