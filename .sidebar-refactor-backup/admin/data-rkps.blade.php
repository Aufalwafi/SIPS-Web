<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data RKPS - SI PS ONLINE</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
*{box-sizing:border-box}
body{margin:0;font-family:'Poppins',Arial,sans-serif;background:#f4f6f9;display:flex;min-height:100vh}
.sidebar{width:260px;background:#1b5e20;min-height:100vh;color:#fff;position:fixed;top:0;left:0;bottom:0;overflow-y:auto;z-index:100}
.sidebar h2{text-align:center;padding:20px 10px;margin:0;border-bottom:1px solid rgba(255,255,255,0.2);font-size:18px;background:#144e18}
.sidebar-category{font-size:10px;font-weight:700;text-transform:uppercase;color:#a5d6a7;padding:14px 15px 4px;letter-spacing:0.8px}
.sidebar a{display:flex;align-items:center;justify-content:space-between;padding:10px 15px;color:#fff;text-decoration:none;font-size:14px;position:relative}
.sidebar a:hover{background:rgba(255,255,255,0.15)}
.sidebar a.active{background:rgba(255,255,255,0.2);font-weight:bold}
.sidebar a.active::after{content:'';position:absolute;right:15px;width:6px;height:6px;background:#fff;border-radius:50%;box-shadow:0 0 6px #fff}
.badge{background:#d32f2f;color:#fff;padding:2px 7px;border-radius:10px;font-size:11px;font-weight:700}
.main{margin-left:260px;flex:1;display:flex;flex-direction:column;min-width:0}
.header{background:#2e7d32;color:#fff;padding:12px 25px;display:flex;justify-content:space-between;align-items:center;width:100%}
.header-left{display:flex;align-items:center;gap:10px;font-size:13px}
.header-right{display:flex;align-items:center;gap:15px;font-size:13px}
.header-right a{color:#fff;text-decoration:none}
.notification-wrapper{position:relative;display:inline-block}
.notification-icon{position:relative;color:#fff;cursor:pointer;padding:5px;display:flex;align-items:center;font-size:16px}
.notification-badge{position:absolute;top:-2px;right:-5px;background:#d32f2f;color:#fff;font-size:10px;padding:2px 5px;border-radius:50%;font-weight:bold}
.notification-dropdown{display:none;position:absolute;right:0;top:35px;width:280px;background:#fff;color:#333;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.2);z-index:1000;border:1px solid #ddd}
.notification-dropdown.show{display:block}
.notif-header{background:#1b5e20;color:#fff;padding:10px 15px;font-size:13px;font-weight:bold;display:flex;justify-content:space-between;align-items:center}
.notif-body{padding:10px 0;max-height:200px;overflow-y:auto}
.notif-item{padding:8px 15px;font-size:13px;border-bottom:1px solid #f0f0f0;display:flex;justify-content:space-between;align-items:center;color:#333;text-decoration:none}
.notif-item:hover{background:#f9f9f9}
.notif-footer{padding:8px;text-align:center;background:#f1f8e9;border-top:1px solid #ddd}
.notif-footer a{color:#1b5e20;font-size:12px;font-weight:bold;text-decoration:none}
.container{margin:25px;background:#fff;padding:20px;border-radius:8px;box-shadow:0 2px 6px rgba(0,0,0,0.1)}
h2{margin-top:0;color:#1b5e20;margin-bottom:15px;font-size:20px}
.btn{display:inline-block;padding:6px 12px;border-radius:4px;color:#fff;text-decoration:none;font-size:12px;cursor:pointer;border:none;transition:0.2s}
.btn:hover{transform:translateY(-1px);box-shadow:0 3px 8px rgba(0,0,0,0.2);opacity:0.9}
.btn-tambah{background:#2e7d32;margin-bottom:15px}
.btn-edit{background:#1565c0;font-size:11px;margin:2px}
.btn-hapus{background:#c62828;font-size:11px;margin:2px}
table{width:100%;border-collapse:collapse;font-size:12px;margin-top:10px}
th{background:#2e7d32;color:#fff;padding:10px;border:1px solid #ddd;text-align:center}
td{padding:8px 10px;border:1px solid #ddd;text-align:center;color:#333}
td.kiri{text-align:left;font-weight:600}
tr:nth-child(even){background:#f9f9f9}
tr:hover{background:#e8f5e9}
td a{color:#1565c0;text-decoration:none;font-weight:600}
td a:hover{text-decoration:underline}
@media print{.sidebar,.header,.btn{display:none}.main{margin-left:0}.container{margin:0;padding:10px 20px;box-shadow:none}th,td{border:1px solid #000;font-size:11px}}
</style>
</head>
<body>

<aside class="sidebar">
    <h2>SI PS ONLINE</h2>
    <div style="padding:10px 0">
        <a href="{{ route('admin.dashboard') }}"><span><i class="fas fa-home" style="width:20px"></i> Dashboard</span></a>
        <div class="sidebar-category">Data Master</div>
        <a href="{{ route('admin.data-kelompok') }}"><span><i class="fas fa-users" style="width:20px"></i> Data Kelompok</span></a>
        <a href="{{ route('admin.peta-kelompok') }}"><span><i class="fas fa-map" style="width:20px"></i> Peta Kelompok</span></a>
        <a href="{{ route('admin.data-pendamping') }}"><span><i class="fas fa-user-tie" style="width:20px"></i> Data Pendamping</span></a>
        <a href="{{ route('admin.data-anggota') }}"><span><i class="fas fa-user-friends" style="width:20px"></i> Data Anggota</span></a>
        <div class="sidebar-category">Verifikasi &amp; Berkas</div>
        <a href="{{ route('admin.verifikasi-rkps') }}"><span><i class="fas fa-file-alt" style="width:20px"></i> Verifikasi RKPS</span><span class="badge">{{ $rkpsMenunggu }}</span></a>
        <a href="{{ route('admin.verifikasi-rkt') }}"><span><i class="fas fa-file-signature" style="width:20px"></i> Verifikasi RKT</span><span class="badge">{{ $rktMenunggu }}</span></a>
        <a href="{{ route('admin.data-rkps') }}" class="active"><span><i class="fas fa-check-circle" style="width:20px"></i> Input RKPS Sah</span></a>
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
                    <div class="notif-header"><span>Notifikasi Berkas</span><span>{{ $totalNotif }} Baru</span></div>
                    <div class="notif-body">
                        <a href="{{ route('admin.verifikasi-rkps') }}" class="notif-item"><span>Pengajuan RKPS Menunggu</span><span style="background:#d32f2f;color:#fff;padding:2px 6px;border-radius:10px;font-size:11px;font-weight:bold">{{ $rkpsMenunggu }}</span></a>
                        <a href="{{ route('admin.verifikasi-rkt') }}" class="notif-item"><span>Pengajuan RKT Menunggu</span><span style="background:#d32f2f;color:#fff;padding:2px 6px;border-radius:10px;font-size:11px;font-weight:bold">{{ $rktMenunggu }}</span></a>
                    </div>
                    <div class="notif-footer"><a href="{{ route('admin.monitoring-pengajuan') }}">Lihat Semua Pengajuan &rarr;</a></div>
                </div>
            </div>
            <span>Hi, <b>{{ Auth::user()->username }}</b></span> |
            <form method="POST" action="{{ route('logout') }}" style="display:inline">@csrf<button type="submit" style="background:none;color:#fff;border:0;cursor:pointer;font-weight:bold;font-size:13px">Logout</button></form>
        </div>
    </header>

    <div class="container">

        @if(session('success'))<div style="background:#d4edda;color:#155724;padding:10px;border-radius:4px;margin-bottom:15px;font-size:12px">{{ session('success') }}</div>@endif
        @if(session('error'))<div style="background:#f8d7da;color:#721c24;padding:10px;border-radius:4px;margin-bottom:15px;font-size:12px">{{ session('error') }}</div>@endif

        <h2>Data RKPS</h2>

        <a class="btn btn-tambah" href="{{ route('admin.data-rkps.create') }}"><i class="fas fa-plus"></i> Tambah RKPS</a>

        <table>
            <thead>
                <tr>
                    <th style="width:40px">No</th>
                    <th style="text-align:left">Kelompok</th>
                    <th style="width:90px">Periode</th>
                    <th>No Penilaian</th>
                    <th style="width:90px">Tgl Penilaian</th>
                    <th>No Pengesahan</th>
                    <th style="width:90px">Tgl Pengesahan</th>
                    <th style="width:70px">Dokumen</th>
                    <th style="width:110px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rkps as $no => $r)
                <tr>
                    <td>{{ $no + 1 }}</td>
                    <td class="kiri">{{ $r->nama_kelompok ?? '-' }}</td>
                    <td>{{ $r->tahun_mulai }} - {{ $r->tahun_selesai }}</td>
                    <td>{{ $r->no_penilaian ?? '-' }}</td>
                    <td>{{ $r->tgl_penilaian ?? '-' }}</td>
                    <td>{{ $r->no_pengesahan ?? '-' }}</td>
                    <td>{{ $r->tgl_pengesahan ?? '-' }}</td>
                    <td>
                        @if(!empty($r->file_rkps))
                            <a href="{{ asset('uploads/rkps/' . $r->file_rkps) }}" target="_blank"><i class="fas fa-download"></i> Download</a>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <a class="btn btn-edit" href="{{ route('admin.data-rkps.edit', $r->id_rkps) }}"><i class="fas fa-edit"></i> Edit</a>
                        <form method="POST" action="{{ route('admin.data-rkps.delete', $r->id_rkps) }}" style="display:inline" onsubmit="return confirm('Yakin hapus data?')">@csrf @method('DELETE')<button type="submit" class="btn btn-hapus"><i class="fas fa-trash"></i> Hapus</button></form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9">Data belum tersedia</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
var notifBtn=document.getElementById('notifBtn'),notifDropdown=document.getElementById('notifDropdown');
if(notifBtn){notifBtn.addEventListener('click',function(e){e.stopPropagation();notifDropdown.classList.toggle('show')});window.addEventListener('click',function(){if(notifDropdown.classList.contains('show'))notifDropdown.classList.remove('show')})}
</script>
</body>
</html>