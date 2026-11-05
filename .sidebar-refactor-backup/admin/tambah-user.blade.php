<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tambah User - SI PS ONLINE</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
.container{margin:25px;background:#fff;padding:20px;border-radius:8px;box-shadow:0 2px 6px rgba(0,0,0,.1);max-width:600px}
h2{margin:0 0 20px;color:#1b5e20;font-size:20px}
.form-group{margin-bottom:15px}
.form-group label{display:block;margin-bottom:5px;font-size:13px;font-weight:600;color:#333}
.form-group input,.form-group select{width:100%;height:36px;padding:6px 10px;font-size:13px;border:1px solid #ccc;border-radius:4px;background:#fff;color:#333}
.form-group input:focus,.form-group select:focus{border-color:#1b5e20;outline:none;box-shadow:0 0 5px rgba(27,94,32,.3)}
.form-group .error{color:#c62828;font-size:11px;margin-top:3px}
.form-buttons{display:flex;gap:8px;margin-top:20px}
.btn{padding:8px 16px;font-size:13px;border-radius:4px;border:none;cursor:pointer;color:#fff;text-decoration:none;display:inline-flex;align-items:center;gap:5px;transition:.2s;height:34px}
.btn:hover{transform:translateY(-1px);box-shadow:0 3px 8px rgba(0,0,0,.2)}
.btn-save{background:#2e7d32}.btn-save:hover{background:#1b5e20}
.btn-back{background:#1b5e20}.btn-back:hover{background:#144e18}
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
<div class="header"><div class="header-left"><img src="{{ url('/assets/logo-jatim.png') }}" class="logo-header"><div><b>Dinas Kehutanan Provinsi Jawa Timur</b><br>CDK Wilayah Kota Madiun</div></div><div class="header-right"><span>Hi, <b>{{ Auth::user()->username }}</b></span> | <form method="POST" action="{{ route('logout') }}" style="display:inline">@csrf<button type="submit" style="background:none;border:0;color:#fff;cursor:pointer">Logout</button></form></div></div>

<div class="container">
<h2><i class="fas fa-user-plus" style="color:#2e7d32"></i> Tambah User Baru</h2>

@if($errors->any())
<div style="background:#f8d7da;color:#721c24;padding:10px;border-radius:4px;margin-bottom:15px;font-size:12px">
<ul style="margin:0;padding-left:15px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('admin.data-user.store') }}">
@csrf

<div class="form-group">
<label>Nama Lengkap</label>
<input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Contoh: Budi Santoso">
@if($errors->has('nama'))<div class="error">{{ $errors->first('nama') }}</div>@endif
</div>

<div class="form-group">
<label>Username</label>
<input type="text" name="username" value="{{ old('username') }}" required placeholder="Contoh: budisantoso">
@if($errors->has('username'))<div class="error">{{ $errors->first('username') }}</div>@endif
</div>

<div class="form-group">
<label>Password</label>
<input type="password" name="password" required placeholder="Minimal 6 karakter">
@if($errors->has('password'))<div class="error">{{ $errors->first('password') }}</div>@endif
</div>

<div class="form-group">
<label>Konfirmasi Password</label>
<input type="password" name="password_confirmation" required placeholder="Ulangi password">
</div>

<div class="form-group">
<label>Role</label>
<select name="role" id="roleSelect" required>
<option value="">-- Pilih Role --</option>
<option value="admin" @selected(old('role')==='admin')>Admin</option>
<option value="pendamping" @selected(old('role')==='pendamping')>Pendamping</option>
<option value="kelompok" @selected(old('role')==='kelompok')>Kelompok</option>
</select>
</div>

<div class="form-group" id="kelompokGroup" style="display:none">
<label>Kelompok</label>
<select name="id_kelompok">
<option value="">-- Pilih Kelompok --</option>
@foreach($daftarKelompok as $k)
<option value="{{ $k->id }}" @selected(old('id_kelompok')==$k->id)>{{ $k->nama_kelompok }}</option>
@endforeach
</select>
</div>

<div class="form-buttons">
<button type="submit" class="btn btn-save"><i class="fas fa-save"></i> Simpan</button>
</div>
</form>
</div></div>

<script>
var sel=document.getElementById('roleSelect');
var grp=document.getElementById('kelompokGroup');
function toggleKelompok(){grp.style.display=sel.value==='kelompok'?'block':'none'}
sel.addEventListener('change',toggleKelompok);
toggleKelompok();
</script>
</body></html>