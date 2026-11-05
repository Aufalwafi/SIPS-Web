<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data User - SI PS ONLINE</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
*{box-sizing:border-box}body{margin:0;font-family:Arial,sans-serif;background:#f4f6f9;display:flex;min-height:100vh}
.sidebar{width:260px;background:#1b5e20;min-height:100vh;color:#fff;flex-shrink:0;position:fixed;top:0;left:0;bottom:0;overflow-y:auto;z-index:100}
.sidebar h2{text-align:center;padding:20px 10px;margin:0;border-bottom:1px solid rgba(255,255,255,.2);font-size:18px;background:#144e18}
.sidebar-category{font-size:10px;font-weight:700;text-transform:uppercase;color:#a5d6a7;padding:14px 15px 4px;letter-spacing:.8px}
.sidebar a{display:flex;align-items:center;justify-content:space-between;padding:10px 15px;color:#fff;text-decoration:none;font-size:14px;transition:.2s;position:relative}
.sidebar a:hover{background:rgba(255,255,255,.15)}
.sidebar a.active{background:rgba(255,255,255,.2);font-weight:bold}
.sidebar a.active:after{content:'';position:absolute;right:15px;width:6px;height:6px;background:#fff;border-radius:50%;box-shadow:0 0 6px #fff}
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
.top-bar{display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;gap:10px;flex-wrap:wrap}
.top-buttons,#formFilter{display:flex;gap:8px;align-items:center;flex-wrap:wrap}
.btn,.btn-edit,.btn-reset,.btn-hapus{display:inline-flex;align-items:center;gap:5px;text-decoration:none;border:0;cursor:pointer;transition:.2s}
.btn{padding:6px 12px;border-radius:4px;font-size:12px;color:#fff;height:32px}
.btn:hover,.btn-edit:hover,.btn-reset:hover,.btn-hapus:hover{transform:translateY(-1px);box-shadow:0 3px 8px rgba(0,0,0,.2)}
.btn-tambah{background:#2e7d32}.btn-back{background:#1b5e20}.btn-print{background:#1565c0}
.btn-edit{background:#1565c0;color:#fff;padding:5px 10px;border-radius:4px;font-size:11px}
.btn-reset{background:#e65100;color:#fff;padding:5px 10px;border-radius:4px;font-size:11px}
.btn-hapus{background:#c62828;color:#fff;padding:5px 10px;border-radius:4px;font-size:11px}
input,select{height:32px;padding:4px 8px;font-size:12px;border:1px solid #ccc;border-radius:4px;background:#fff;color:#333}
input{width:220px}input:focus,select:focus{border-color:#1b5e20;outline:none;box-shadow:0 0 5px rgba(27,94,32,.3)}
table{width:100%;border-collapse:collapse;background:#fff;box-shadow:0 2px 6px rgba(0,0,0,.1);font-size:12px}
th,td{padding:8px 10px;border:1px solid #ddd;color:#333}
th{background:#2e7d32;color:#fff;text-align:center}
tr:nth-child(even){background:#f9f9f9}tr:hover{background:#e8f5e9}
.alert{background:#d4edda;color:#155724;padding:10px;border-radius:4px;margin-bottom:15px;font-size:12px}
.alert-error{background:#f8d7da;color:#721c24;padding:10px;border-radius:4px;margin-bottom:15px;font-size:12px}
.empty{text-align:center;padding:15px}
@media print{.sidebar,.header,.top-bar,.alert,.alert-error,#formFilter{display:none!important}.main{margin-left:0}.container{margin:0;padding:0;box-shadow:none}.print-title{display:block!important;text-align:center;margin-bottom:20px;color:#000}.btn-edit,.btn-reset,.btn-hapus{display:none}th{background:#fff!important;color:#000;border:1px solid #000}td{border:1px solid #000}}
.print-title{display:none}
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
<a href="{{ route('admin.data-user') }}" class="active"><span><i class="fas fa-user-cog" style="width:20px"></i> Data User</span></a>
<a href="{{ route('admin.aktivitas-user') }}"><span><i class="fas fa-list-check" style="width:20px"></i> Aktivitas User</span></a>
</div></div>

<div class="main">
<div class="header"><div class="header-left"><img src="{{ url('/assets/logo-jatim.png') }}" class="logo-header"><div><b>Dinas Kehutanan Provinsi Jawa Timur</b><br>CDK Wilayah Kota Madiun</div></div><div class="header-right"><div class="notification-wrapper"><div class="notification-icon" id="notifBtn"><i class="fas fa-bell"></i>@if($totalNotif>0)<span class="notification-badge">{{ $totalNotif }}</span>@endif</div><div class="notification-dropdown" id="notifDropdown"><div class="notif-header"><span>Notifikasi Berkas</span><span>{{ $totalNotif }} Baru</span></div><div style="padding:10px 0"><a href="{{ route('admin.verifikasi-rkps') }}" class="notif-item"><span>Pengajuan RKPS Menunggu</span><b>{{ $rkpsMenunggu }}</b></a><a href="{{ route('admin.verifikasi-rkt') }}" class="notif-item"><span>Pengajuan RKT Menunggu</span><b>{{ $rktMenunggu }}</b></a></div><div class="notif-footer"><a href="{{ route('admin.monitoring-pengajuan') }}">Lihat Semua Pengajuan &rarr;</a></div></div></div><span>Hi, <b>{{ Auth::user()->username }}</b></span> | <form method="POST" action="{{ route('logout') }}" style="display:inline">@csrf<button type="submit" style="background:none;border:0;color:#fff;cursor:pointer">Logout</button></form></div></div>

<div class="container">

<h2 class="print-title">Data User - SI PS ONLINE</h2>

@if(session('success'))<div class="alert">{!! session('success') !!}</div>@endif
@if(session('error'))<div class="alert-error">{!! session('error') !!}</div>@endif

<div class="top-bar">
<form method="GET" id="formFilter">
<input type="text" name="cari" placeholder="&#128269; Cari nama / username..." value="{{ $cari }}">
<select name="role"><option value="">Semua Role</option><option value="admin" @selected($role==='admin')>Admin</option><option value="operator" @selected($role==='operator')>Operator</option><option value="pendamping" @selected($role==='pendamping')>Pendamping</option><option value="kelompok" @selected($role==='kelompok')>Kelompok</option></select>
</form>
<div class="top-buttons">
<a href="{{ route('admin.data-user.create') }}" class="btn btn-tambah"><i class="fas fa-plus"></i> Tambah User</a>
<button onclick="window.print()" class="btn btn-print"><i class="fas fa-print"></i> Cetak</button>
</div>
</div>

<table>
<thead>
<tr>
<th style="width:40px">No</th>
<th style="text-align:left">Nama</th>
<th style="text-align:left">Username</th>
<th style="width:110px">Password</th>
<th style="width:100px">Role</th>
<th style="width:160px">Aksi</th>
</tr>
</thead>
<tbody>
@forelse($query as $i => $u)
<tr>
<td style="text-align:center">{{ $i+1 }}</td>
<td style="font-weight:600">{{ $u->nama }}</td>
<td>{{ $u->username }}</td>
<td style="text-align:center"><em>(Terenkripsi)</em></td>
<td style="text-align:center">{{ ucfirst($u->role) }}</td>
<td style="text-align:center">
<a href="{{ route('admin.data-user.edit', $u->id) }}" class="btn-edit"><i class="fas fa-edit"></i> Edit</a>
<form method="POST" action="{{ route('admin.data-user.reset-password', $u->id) }}" style="display:inline" onsubmit="return confirm('Reset password ke 123456?')">
@csrf<button type="submit" class="btn-reset"><i class="fas fa-key"></i> Reset</button>
</form>
<form method="POST" action="{{ route('admin.data-user.delete', $u->id) }}" style="display:inline" onsubmit="return confirm('Yakin hapus user ini?')">
@csrf @method('DELETE')<button type="submit" class="btn-hapus"><i class="fas fa-trash"></i> Hapus</button>
</form>
</td>
</tr>
@empty
<tr><td colspan="6" class="empty">Data user belum tersedia.</td></tr>
@endforelse
</tbody>
</table>

</div></div>

<script>
var form=document.getElementById('formFilter');
form.querySelectorAll('select').forEach(function(e){e.addEventListener('change',function(){form.submit()})});
var t;var input=form.querySelector('input');
input.addEventListener('keyup',function(){clearTimeout(t);t=setTimeout(function(){form.submit()},500)});
var b=document.getElementById('notifBtn'),d=document.getElementById('notifDropdown');
if(b){b.addEventListener('click',function(e){e.stopPropagation();d.classList.toggle('show')});window.addEventListener('click',function(){d.classList.remove('show')})}
</script>
</body></html>