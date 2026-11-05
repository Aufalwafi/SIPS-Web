<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Laporan Kegiatan Kelompok - SI PS ONLINE</title>
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
.notification-wrapper{position:relative;display:inline-block}
.notification-icon{position:relative;color:#fff;cursor:pointer;padding:5px;font-size:16px}
.notification-badge{position:absolute;top:-2px;right:-5px;background:#d32f2f;color:#fff;font-size:10px;padding:2px 5px;border-radius:50%;font-weight:bold}
.notification-dropdown{display:none;position:absolute;right:0;top:35px;width:280px;background:#fff;color:#333;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,.2);z-index:1000;border:1px solid #ddd}
.notification-dropdown.show{display:block}
.notif-header{background:#1b5e20;color:#fff;padding:10px 15px;font-size:13px;font-weight:bold;display:flex;justify-content:space-between}
.notif-item{padding:8px 15px;font-size:13px;border-bottom:1px solid #f0f0f0;display:flex;justify-content:space-between;color:#333;text-decoration:none}
.notif-footer{padding:8px;text-align:center;background:#f1f8e9}
.notif-footer a{color:#1b5e20;font-size:12px;font-weight:bold;text-decoration:none}
.container{margin:25px;background:#fff;padding:20px;border-radius:8px;box-shadow:0 2px 6px rgba(0,0,0,1)}
h2{margin:0 0 15px;color:#1b5e20;font-size:20px}
.toolbar{display:flex;gap:8px;flex-wrap:wrap;align-items:center;margin-bottom:15px}
.toolbar input,.toolbar select{height:32px;padding:4px 8px;font-size:12px;border:1px solid #ccc;border-radius:4px;background:#fff;color:#333}
.toolbar input:focus,.toolbar select:focus{border-color:#1b5e20;outline:none;box-shadow:0 0 5px rgba(27,94,32,.3)}
.btn{padding:6px 12px;font-size:12px;border-radius:4px;border:none;cursor:pointer;color:#fff;text-decoration:none;display:inline-flex;align-items:center;gap:5px;transition:.2s;height:32px}
.btn:hover{transform:translateY(-1px);box-shadow:0 3px 8px rgba(0,0,0,.2)}
.cetak{background:#2e7d32}.cetak:hover{background:#1b5e20}
table{width:100%;border-collapse:collapse;background:#fff;box-shadow:0 2px 6px rgba(0,0,0,.1);font-size:12px}
th{background:#2e7d32;color:#fff;padding:10px;text-align:center;border:1px solid #ddd;font-size:12px}
td{padding:8px 10px;border:1px solid #ddd;text-align:center;vertical-align:top;color:#333}
tr:nth-child(even){background:#f9f9f9}tr:hover{background:#e8f5e9}
.col-no{width:40px}.col-tanggal{width:95px}.col-kelompok{width:140px}.col-kegiatan{width:120px;text-align:left}.col-deskripsi{text-align:left}.col-pembuat{width:130px;text-align:left}.col-foto{width:80px}
.foto-thumb{width:60px;border-radius:4px;cursor:pointer;transition:.2s}
.foto-thumb:hover{transform:scale(1.05);box-shadow:0 3px 8px rgba(0,0,0,.2)}
.modal{display:none;position:fixed;z-index:999;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,.7);justify-content:center;align-items:center}
.modal-content{background:#fff;padding:10px;border-radius:8px;max-width:90vw;max-height:90vh;display:flex;justify-content:center;align-items:center}
.modal-content img{max-width:100%;max-height:80vh;width:auto;height:auto;object-fit:contain;border-radius:6px}
.close{position:absolute;top:20px;right:30px;color:#fff;font-size:30px;font-weight:bold;cursor:pointer}
.kop,.tanggal-print{display:none}
@media print{.sidebar,.header,.toolbar{display:none}body{background:#fff;margin:0}.main{margin-left:0}.container{padding:0;box-shadow:none;margin:0}.kop{display:flex;align-items:center;border-bottom:3px solid #000;padding-bottom:10px;margin-bottom:10px}.logo{width:70px;margin-right:15px}.judul{text-align:center;flex:1}.judul h3{margin:0;font-size:14px}.judul h2{margin:2px 0;font-size:18px}.judul h4{margin:2px 0;font-size:14px}.judul p{margin:0;font-size:12px}.tanggal-print{display:block;text-align:right;font-size:11px;margin-bottom:10px}th{background:#fff!important;color:#000;border:1px solid #000}td{border:1px solid #000;color:#000}.foto-thumb{width:40px}}
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
<a href="{{ route('admin.data-laporan') }}" class="active"><span><i class="fas fa-print" style="width:20px"></i> Laporan</span></a>
<a href="{{ route('admin.data-user') }}"><span><i class="fas fa-user-cog" style="width:20px"></i> Data User</span></a>
<a href="{{ route('admin.aktivitas-user') }}"><span><i class="fas fa-list-check" style="width:20px"></i> Aktivitas User</span></a>
</div></div>
<div class="main"><div class="header"><div class="header-left"><img src="{{ url('/assets/logo-jatim.png') }}" class="logo-header"><div><b>Dinas Kehutanan Provinsi Jawa Timur</b><br>CDK Wilayah Kota Madiun</div></div><div class="header-right"><div class="notification-wrapper"><div class="notification-icon" id="notifBtn"><i class="fas fa-bell"></i>@if($totalNotif>0)<span class="notification-badge">{{ $totalNotif }}</span>@endif</div><div class="notification-dropdown" id="notifDropdown"><div class="notif-header"><span>Notifikasi Berkas</span><span>{{ $totalNotif }} Baru</span></div><div style="padding:10px 0"><a href="{{ route('admin.verifikasi-rkps') }}" class="notif-item"><span>Pengajuan RKPS Menunggu</span><b>{{ $rkpsMenunggu }}</b></a><a href="{{ route('admin.verifikasi-rkt') }}" class="notif-item"><span>Pengajuan RKT Menunggu</span><b>{{ $rktMenunggu }}</b></a></div><div class="notif-footer"><a href="{{ route('admin.monitoring-pengajuan') }}">Lihat Semua Pengajuan &rarr;</a></div></div></div><span>Hi, <b>{{ Auth::user()->username }}</b></span> | <form method="POST" action="{{ route('logout') }}" style="display:inline">@csrf<button type="submit" style="background:none;border:0;color:#fff;cursor:pointer">Logout</button></form></div></div>
<div class="container">

<h2>Data Laporan</h2>

<div class="toolbar">
<input type="text" id="searchBox" placeholder="🔍 Cari...">
<select id="filterTahun"><option value="">Semua Tahun</option>@foreach($listTahun as $t)<option value="{{ $t }}">{{ $t }}</option>@endforeach</select>
<select id="filterBulan"><option value="">Semua Bulan</option><option value="01">Bulan 1</option><option value="02">Bulan 2</option><option value="03">Bulan 3</option><option value="04">Bulan 4</option><option value="05">Bulan 5</option><option value="06">Bulan 6</option><option value="07">Bulan 7</option><option value="08">Bulan 8</option><option value="09">Bulan 9</option><option value="10">Bulan 10</option><option value="11">Bulan 11</option><option value="12">Bulan 12</option></select>
<select id="filterUser"><option value="">Semua Pendamping</option>@foreach($listUser as $u)<option value="{{ $u->nama }}">{{ $u->nama }}</option>@endforeach</select>
<select id="filterKelompok"><option value="">Semua Kelompok</option>@foreach($listKelompok as $k)<option value="{{ $k->nama_kelompok }}">{{ $k->nama_kelompok }}</option>@endforeach</select>
<button class="btn cetak" onclick="window.print()"><i class="fas fa-print"></i> Cetak</button>
</div>

<div class="kop">
<img src="{{ url('/assets/logo-jatim.png') }}" class="logo">
<div class="judul">
<h3>PEMERINTAH PROVINSI JAWA TIMUR</h3>
<h2>DINAS KEHUTANAN</h2>
<h4>CABANG DINAS KEHUTANAN WILAYAH KOTA MADIUN</h4>
<p><b>LAPORAN KEGIATAN KELOMPOK</b></p>
</div>
</div>

<div class="tanggal-print">Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d F Y') }}</div>

<table id="tabelLaporan">
<tr>
<th class="col-no">No</th>
<th class="col-tanggal">Tanggal</th>
<th class="col-kelompok">Kelompok</th>
<th class="col-kegiatan">Kegiatan</th>
<th class="col-deskripsi">Deskripsi</th>
<th class="col-pembuat">Pembuat</th>
<th class="col-foto">Foto</th>
</tr>
@forelse($data as $i => $d)
@php
$tgl = $d->tanggal;
$tahun = \Carbon\Carbon::parse($tgl)->format('Y');
$bulan = \Carbon\Carbon::parse($tgl)->format('m');
@endphp
<tr data-tahun="{{ $tahun }}" data-bulan="{{ $bulan }}">
<td>{{ $i + 1 }}</td>
<td>{{ $tgl }}</td>
<td style="font-weight:600">{{ $d->nama_kelompok ?? '-' }}</td>
<td class="col-kegiatan">{{ $d->kegiatan ?? '-' }}</td>
<td class="col-deskripsi">{{ $d->deskripsi ?? '-' }}</td>
<td class="col-pembuat">{{ $d->nama_user ?? '-' }}</td>
<td>
@if(!empty($d->foto))
<img src="{{ asset('uploads/kegiatan/' . $d->foto) }}" class="foto-thumb" onclick="openModal(this.src)">
@else -
@endif
</td>
</tr>
@empty
<tr><td colspan="7" style="padding:15px">Data laporan belum tersedia.</td></tr>
@endforelse
</table>

</div></div>

<!-- MODAL FOTO -->
<div id="modalFoto" class="modal" onclick="closeModal()">
<span class="close">&times;</span>
<div class="modal-content"><img id="imgModal"></div>
</div>

<script>
function openModal(src){document.getElementById('modalFoto').style.display='flex';document.getElementById('imgModal').src=src}
function closeModal(){document.getElementById('modalFoto').style.display='none'}
function filterData(){
var s=document.getElementById('searchBox').value.toLowerCase();
var t=document.getElementById('filterTahun').value;
var b=document.getElementById('filterBulan').value;
var u=document.getElementById('filterUser').value.toLowerCase();
var k=document.getElementById('filterKelompok').value.toLowerCase();
var rows=document.querySelectorAll('#tabelLaporan tr');
rows.forEach(function(row,i){
if(i===0)return;
var txt=row.innerText.toLowerCase();
var rT=row.getAttribute('data-tahun');
var rB=row.getAttribute('data-bulan');
var show=true;
if(s&&txt.indexOf(s)===-1)show=false;
if(t&&rT!==t)show=false;
if(b&&rB!==b)show=false;
if(u&&txt.indexOf(u)===-1)show=false;
if(k&&txt.indexOf(k)===-1)show=false;
row.style.display=show?'':'none';
});
}
document.querySelectorAll('#searchBox,#filterTahun,#filterBulan,#filterUser,#filterKelompok').forEach(function(el){el.addEventListener('change',filterData)});
document.getElementById('searchBox').addEventListener('keyup',filterData);
var b=document.getElementById('notifBtn'),d=document.getElementById('notifDropdown');
if(b){b.addEventListener('click',function(e){e.stopPropagation();d.classList.toggle('show')});window.addEventListener('click',function(){d.classList.remove('show')})}
</script>
</body></html>