<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Monitoring Aktivitas User - SI PS ONLINE</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<meta http-equiv="refresh" content="300">
<style>
*{box-sizing:border-box}body{margin:0;font-family:Arial,sans-serif;background:#f4f6f9;display:flex;min-height:100vh}
.sidebar{width:260px;background:#1b5e20;min-height:100vh;color:#fff;flex-shrink:0;position:fixed;top:0;left:0;bottom:0;overflow-y:auto;z-index:100}
.sidebar h2{text-align:center;padding:20px 10px;margin:0;border-bottom:1px solid rgba(255,255,255,.2);font-size:18px;background:#144e18}
.sidebar-category{font-size:10px;font-weight:700;text-transform:uppercase;color:#a5d6a7;padding:14px 15px 4px;letter-spacing:.8px}
.sidebar a{display:flex;align-items:center;justify-content:space-between;padding:10px 15px;color:#fff;text-decoration:none;font-size:14px;transition:.2s;position:relative}
.sidebar a:hover{background:rgba(255,255,255,.15)}
.sidebar a.active{background:rgba(255,255,255,.2);font-weight:bold}
.sidebar a.active::after{content:'';position:absolute;right:15px;width:6px;height:6px;background:#fff;border-radius:50%;box-shadow:0 0 6px #fff}
.main{margin-left:260px;flex:1;display:flex;flex-direction:column;min-width:0}
.header{background:#2e7d32;color:#fff;padding:12px 25px;display:flex;justify-content:space-between;align-items:center}
.logo-header{height:40px;margin-right:10px}
.header-left{display:flex;align-items:center;gap:10px;font-size:13px}
.header-right{display:flex;align-items:center;gap:15px;font-size:13px}
.notification-wrapper{position:relative;display:inline-block}
.notification-icon{position:relative;color:#fff;cursor:pointer;padding:5px;font-size:16px}
.notification-badge{position:absolute;top:-2px;right:-5px;background:#d32f2f;color:#fff;font-size:10px;padding:2px 5px;border-radius:50%;font-weight:bold}
.notification-dropdown{display:none;position:absolute;right:0;top:35px;width:280px;background:#fff;color:#333;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,.2);z-index:1000;border:1px solid #ddd}
.notification-dropdown.show{display:block}
.notif-header{background:#1b5e20;color:#fff;padding:10px 15px;font-size:13px;font-weight:bold;display:flex;justify-content:space-between}
.notif-item{padding:8px 15px;font-size:13px;border-bottom:1px solid #f0f0f0;display:flex;justify-content:space-between;color:#333;text-decoration:none}
.notif-footer{padding:8px;text-align:center;background:#f1f8e9}
.notif-footer a{color:#1b5e20;font-size:12px;font-weight:bold;text-decoration:none}
.container{margin:25px;background:#fff;padding:20px;border-radius:8px;box-shadow:0 2px 6px rgba(0,0,0,.1)}
h2{margin:0 0 15px;color:#1b5e20;font-size:20px}
.filter-box{background:#f9f9f9;padding:12px 15px;margin-bottom:15px;border-radius:6px;border:1px solid #ddd;font-size:12px}
.filter-box form{display:flex;flex-wrap:wrap;align-items:center;gap:8px}
.filter-box label{font-weight:600}
input[type="date"],select{height:32px;padding:6px 10px;font-size:12px;border:1px solid #ccc;border-radius:4px;background:#fff;color:#333}
input[type="date"]:focus,select:focus{border-color:#1b5e20;outline:none;box-shadow:0 0 5px rgba(27,94,32,.3)}
.btn{padding:6px 14px;font-size:12px;border-radius:4px;border:none;cursor:pointer;color:#fff;text-decoration:none;display:inline-flex;align-items:center;gap:5px;transition:.2s;height:32px}
.btn:hover{transform:translateY(-1px);box-shadow:0 3px 8px rgba(0,0,0,.2)}
.btn-filter{background:#2e7d32}.btn-filter:hover{background:#1b5e20}
.btn-export{background:#1565c0}.btn-export:hover{background:#0d47a1}
table{width:100%;border-collapse:collapse;font-size:12px}
th{background:#2e7d32;color:#fff;padding:8px 10px;border:1px solid #ddd;text-align:center}
td{padding:8px 10px;border:1px solid #ddd;text-align:center;color:#333}
tr:nth-child(even){background:#f9f9f9}tr:hover{background:#e8f5e9}
.online{color:#2e7d32;font-weight:bold}
.offline{color:#c62828;font-weight:bold}
.status-dot{display:inline-block;width:8px;height:8px;border-radius:50%;margin-right:5px}
.status-dot.on{background:#2e7d32;box-shadow:0 0 4px #2e7d32}
.status-dot.off{background:#c62828}
.empty{text-align:center;padding:15px}
</style>
</head>
<body>
<div class="sidebar"><h2>SI PS ONLINE</h2><div style="padding:10px 0">
<a href="{{ route('admin.dashboard') }}"><span><i class="fas fa-home" style="width:20px"></i> Dashboard</span></a>
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
<a href="{{ route('admin.aktivitas-user') }}" class="active"><span><i class="fas fa-list-check" style="width:20px"></i> Aktivitas User</span></a>
</div></div>

<div class="main">
<div class="header"><div class="header-left"><img src="{{ url('/assets/logo-jatim.png') }}" class="logo-header"><div><b>Dinas Kehutanan Provinsi Jawa Timur</b><br>CDK Wilayah Kota Madiun</div></div><div class="header-right"><div class="notification-wrapper"><div class="notification-icon" id="notifBtn"><i class="fas fa-bell"></i>@if($totalNotif>0)<span class="notification-badge">{{ $totalNotif }}</span>@endif</div><div class="notification-dropdown" id="notifDropdown"><div class="notif-header"><span>Notifikasi Berkas</span><span>{{ $totalNotif }} Baru</span></div><div style="padding:10px 0"><a href="{{ route('admin.verifikasi-rkps') }}" class="notif-item"><span>Pengajuan RKPS Menunggu</span><b>{{ $rkpsMenunggu }}</b></a><a href="{{ route('admin.verifikasi-rkt') }}" class="notif-item"><span>Pengajuan RKT Menunggu</span><b>{{ $rktMenunggu }}</b></a></div><div class="notif-footer"><a href="{{ route('admin.monitoring-pengajuan') }}">Lihat Semua Pengajuan &rarr;</a></div></div></div><span>Hi, <b>{{ Auth::user()->username }}</b></span> | <form method="POST" action="{{ route('logout') }}" style="display:inline">@csrf<button type="submit" style="background:none;border:0;color:#fff;cursor:pointer">Logout</button></form></div></div>

<div class="container">

<h2><i class="fas fa-chart-bar" style="color:#2e7d32"></i> Monitoring Aktivitas User</h2>

<div class="filter-box">
<form method="GET">
<label>Tanggal:</label>
<input type="date" name="tgl_awal" value="{{ $tanggalAwal }}">
<label>s/d</label>
<input type="date" name="tgl_akhir" value="{{ $tanggalAkhir }}">
<label>Role:</label>
<select name="role">
<option value="">Semua</option>
<option value="admin" @selected($role==='admin')>Admin</option>
<option value="pendamping" @selected($role==='pendamping')>Pendamping</option>
<option value="kelompok" @selected($role==='kelompok')>Kelompok</option>
</select>
<button type="submit" class="btn btn-filter"><i class="fas fa-filter"></i> Filter</button>
<a href="{{ route('admin.aktivitas-user.export', ['tgl_awal'=>$tanggalAwal, 'tgl_akhir'=>$tanggalAkhir, 'role'=>$role]) }}" class="btn btn-export"><i class="fas fa-file-excel"></i> Export Excel</a>
</form>
</div>

<table>
<thead>
<tr>
<th style="text-align:left">Nama</th>
<th style="text-align:left">Username</th>
<th style="width:100px">Role</th>
<th style="width:140px">Login</th>
<th style="width:140px">Logout</th>
<th style="width:90px">Durasi (m)</th>
<th style="width:80px">Status</th>
</tr>
</thead>
<tbody>
@forelse($logs as $log)
<tr>
<td style="text-align:left;font-weight:600">{{ $log->nama ?? '-' }}</td>
<td style="text-align:left">{{ $log->username ?? '-' }}</td>
<td>{{ ucfirst($log->role ?? '-') }}</td>
<td>{{ $log->login_time ?? '-' }}</td>
<td>{{ $log->logout_time ?? '-' }}</td>
<td>{{ $log->durasi ?? 0 }}</td>
<td>
@if($log->status === 'Online')
<span class="online"><span class="status-dot on"></span>Online</span>
@else
<span class="offline"><span class="status-dot off"></span>Offline</span>
@endif
</td>
</tr>
@empty
<tr><td colspan="7" class="empty">Belum ada data aktivitas user.</td></tr>
@endforelse
</tbody>
</table>

</div></div>

<script>
var b=document.getElementById('notifBtn'),d=document.getElementById('notifDropdown');
if(b){b.addEventListener('click',function(e){e.stopPropagation();d.classList.toggle('show')});window.addEventListener('click',function(){d.classList.remove('show')})}
</script>
</body></html>