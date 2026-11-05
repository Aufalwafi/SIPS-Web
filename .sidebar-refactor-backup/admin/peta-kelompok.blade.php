<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Peta Kelompok - SI PS ONLINE</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:Arial,sans-serif;background:#f4f6f9;display:flex;min-height:100vh}

/* SIDEBAR */
.sidebar{width:260px;background:#1b5e20;min-height:100vh;color:#fff;flex-shrink:0;position:fixed;top:0;left:0;bottom:0;overflow-y:auto;z-index:100}
.sidebar h2{text-align:center;padding:20px 10px;margin:0;border-bottom:1px solid rgba(255,255,255,.2);font-size:18px;background:#144e18}
.sidebar-category{font-size:10px;font-weight:700;text-transform:uppercase;color:#a5d6a7;padding:14px 15px 4px;letter-spacing:.8px}
.sidebar a{display:flex;align-items:center;justify-content:space-between;padding:10px 15px;color:#fff;text-decoration:none;font-size:14px;transition:.2s;position:relative}
.sidebar a:hover{background:rgba(255,255,255,.15)}
.sidebar a.active{background:rgba(255,255,255,.2);font-weight:bold}
.sidebar a.active::after{content:'';position:absolute;right:15px;width:6px;height:6px;background:#fff;border-radius:50%;box-shadow:0 0 6px #fff}

/* MAIN */
.main{margin-left:260px;flex:1;display:flex;flex-direction:column;min-width:0}
.header{background:#2e7d32;color:#fff;padding:12px 25px;display:flex;justify-content:space-between;align-items:center;width:100%}
.logo-header{height:40px;margin-right:10px}
.header-left{display:flex;align-items:center;gap:10px;font-size:13px}
.header-right{display:flex;align-items:center;gap:15px;font-size:13px}
.header-right a{color:#fff;text-decoration:none}

/* NOTIFIKASI */
.notification-wrapper{position:relative;display:inline-block}
.notification-icon{position:relative;color:#fff;cursor:pointer;padding:5px;display:flex;align-items:center;font-size:16px}
.notification-badge{position:absolute;top:-2px;right:-5px;background:#d32f2f;color:#fff;font-size:10px;padding:2px 5px;border-radius:50%;font-weight:bold}
.notification-dropdown{display:none;position:absolute;right:0;top:35px;width:280px;background:#fff;color:#333;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,.2);z-index:1000;border:1px solid #ddd}
.notification-dropdown.show{display:block}
.notif-header{background:#1b5e20;color:#fff;padding:10px 15px;font-size:13px;font-weight:bold;display:flex;justify-content:space-between}
.notif-body{padding:10px 0;max-height:200px;overflow-y:auto}
.notif-item{padding:8px 15px;font-size:13px;border-bottom:1px solid #f0f0f0;display:flex;justify-content:space-between;align-items:center;color:#333;text-decoration:none}
.notif-item:hover{background:#f9f9f9}
.notif-footer{padding:8px;text-align:center;background:#f1f8e9;border-top:1px solid #ddd}
.notif-footer a{color:#1b5e20;font-size:12px;font-weight:bold;text-decoration:none}

/* CONTENT */
.container{margin:25px;background:#fff;padding:20px;border-radius:8px;box-shadow:0 2px 6px rgba(0,0,0,.1)}
.top-bar{display:flex;justify-content:space-between;align-items:center;margin-bottom:15px}
.top-bar h2{margin:0;color:#1b5e20;font-size:20px}
.top-buttons{display:flex;gap:8px}
.btn-back{background:#1b5e20;color:#fff;padding:8px 14px;border-radius:4px;text-decoration:none;font-size:13px;display:inline-flex;align-items:center;gap:5px;transition:.2s}
.btn-back:hover{background:#144e18;transform:translateY(-1px);box-shadow:0 3px 8px rgba(0,0,0,.2)}

/* MAP */
#map{width:100%;height:520px;border:1px solid #ddd;border-radius:6px;z-index:1}

/* LEGEND */
.legend-box{display:flex;gap:15px;margin-top:12px;flex-wrap:wrap}
.legend-item{display:flex;align-items:center;gap:5px;font-size:12px;color:#555}
.legend-color{width:16px;height:12px;border-radius:3px;border:1px solid #bbb}

/* POPUP */
.leaflet-popup-content{font-family:Arial,sans-serif;font-size:13px;line-height:1.6}
.leaflet-popup-content b{color:#1b5e20}
.popup-title{font-size:14px;font-weight:bold;color:#1b5e20;margin-bottom:4px;border-bottom:1px solid #eee;padding-bottom:4px}
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
<h2>SI PS ONLINE</h2>
<div style="padding:10px 0">
<a href="{{ route('admin.dashboard') }}"><span><i class="fas fa-home" style="width:20px"></i> Dashboard</span></a>
<div class="sidebar-category">Data Master</div>
<a href="{{ route('admin.data-kelompok') }}"><span><i class="fas fa-users" style="width:20px"></i> Data Kelompok</span></a>
<a href="{{ route('admin.peta-kelompok') }}" class="active"><span><i class="fas fa-map" style="width:20px"></i> Peta Kelompok</span></a>
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
<a href="#"><span><i class="fas fa-print" style="width:20px"></i> Laporan</span></a>
<a href="#"><span><i class="fas fa-user-cog" style="width:20px"></i> Data User</span></a>
</div>
</div>

<!-- MAIN -->
<div class="main">

<!-- HEADER -->
<div class="header">
<div class="header-left">
<img src="{{ url('/assets/logo-jatim.png') }}" class="logo-header">
<div><b>Dinas Kehutanan Provinsi Jawa Timur</b><br>CDK Wilayah Kota Madiun</div>
</div>
<div class="header-right">
<div class="notification-wrapper">
<div class="notification-icon" id="notifBtn">
<i class="fas fa-bell"></i>
@if($totalNotif > 0)<span class="notification-badge">{{ $totalNotif }}</span>@endif
</div>
<div class="notification-dropdown" id="notifDropdown">
<div class="notif-header"><span>Notifikasi Berkas</span><span>{{ $totalNotif }} Baru</span></div>
<div class="notif-body">
<a href="{{ route('admin.verifikasi-rkps') }}" class="notif-item"><span>Pengajuan RKPS Menunggu</span><b>{{ $rkpsMenunggu }}</b></a>
<a href="{{ route('admin.verifikasi-rkt') }}" class="notif-item"><span>Pengajuan RKT Menunggu</span><b>{{ $rktMenunggu }}</b></a>
</div>
<div class="notif-footer"><a href="{{ route('admin.monitoring-pengajuan') }}">Lihat Semua Pengajuan &rarr;</a></div>
</div>
</div>
<span>Hi, <b>{{ Auth::user()->username }}</b></span> |
<form method="POST" action="{{ route('logout') }}" style="display:inline">@csrf<button type="submit" style="background:none;border:0;color:#fff;cursor:pointer">Logout</button></form>
</div>
</div>

<!-- CONTAINER -->
<div class="container">
<div class="top-bar">
<h2><i class="fas fa-map-marked-alt"></i> Peta Kelompok Perhutanan Sosial</h2>
<div class="top-buttons">
<a class="btn-back" href="{{ route('admin.dashboard') }}"><i class="fas fa-arrow-left"></i> Kembali</a>
</div>
</div>

<div id="map"></div>

<div class="legend-box" style="margin-top:12px">
<div class="legend-item"><div class="legend-color" style="background:#2e7d32"></div> RHPS</div>
<div class="legend-item"><div class="legend-color" style="background:#1565c0"></div> HIPSA</div>
<div class="legend-item"><div class="legend-color" style="background:#e65100"></div> PHPS</div>
<div class="legend-item"><div class="legend-color" style="background:#7b1fa2"></div> Skema Lain</div>
<div class="legend-item"><div class="legend-color" style="background:#999"></div> Tanpa Data Koordinat</div>
</div>

<!-- TABEL RINGKASAN -->
<div style="margin-top:18px;overflow-x:auto">
<table style="width:100%;border-collapse:collapse;font-size:12px">
<thead>
<tr>
<th style="background:#2e7d32;color:#fff;padding:10px;border:1px solid #ddd;text-align:center;width:40px">No</th>
<th style="background:#2e7d32;color:#fff;padding:10px;border:1px solid #ddd;text-align:left">Nama Kelompok</th>
<th style="background:#2e7d32;color:#fff;padding:10px;border:1px solid #ddd;text-align:left">Desa</th>
<th style="background:#2e7d32;color:#fff;padding:10px;border:1px solid #ddd;text-align:left">Kecamatan</th>
<th style="background:#2e7d32;color:#fff;padding:10px;border:1px solid #ddd;text-align:center">Skema</th>
<th style="background:#2e7d32;color:#fff;padding:10px;border:1px solid #ddd;text-align:center">Luas (ha)</th>
<th style="background:#2e7d32;color:#fff;padding:10px;border:1px solid #ddd;text-align:center">Status Peta</th>
</tr>
</thead>
<tbody>
@forelse($kelompok as $i => $k)
<tr style="cursor:pointer" onclick="zoomToKelompok({{ $k->id }})">
<td style="padding:8px;border:1px solid #ddd;text-align:center">{{ $i + 1 }}</td>
<td style="padding:8px;border:1px solid #ddd;font-weight:600;text-align:left">{{ $k->nama_kelompok ?? '-' }}</td>
<td style="padding:8px;border:1px solid #ddd;text-align:left">{{ $k->desa ?? '-' }}</td>
<td style="padding:8px;border:1px solid #ddd;text-align:left">{{ $k->kecamatan ?? '-' }}</td>
<td style="padding:8px;border:1px solid #ddd;text-align:center">{{ $k->skema_ps ?? '-' }}</td>
<td style="padding:8px;border:1px solid #ddd;text-align:center">{{ $k->luas_lahan ?? '-' }}</td>
<td style="padding:8px;border:1px solid #ddd;text-align:center">
@if(!empty($k->polygon_kelompok))
<span style="background:#e8f5e9;color:#2e7d32;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:600">Tersedia</span>
@else
<span style="background:#fff3e0;color:#e65100;padding:2px 8px;border-radius:10px;font-size:11px">Belum Ada</span>
@endif
</td>
</tr>
@empty
<tr><td colspan="7" style="padding:12px;border:1px solid #ddd;text-align:center">Belum ada data kelompok.</td></tr>
@endforelse
</tbody>
</table>
</div>

</div><!-- /container -->
</div><!-- /main -->

<script>
// INISIALISASI PETA
var map = L.map('map').setView([-7.6298, 111.5239], 11);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors',
    maxZoom: 18
}).addTo(map);

// WARNA BERDASARKAN SKEMA
function getSkemaColor(skema) {
    if (!skema) return '#999';
    var s = skema.toLowerCase();
    if (s.indexOf('rhps') !== -1 || s.indexOf('hutan rakyat') !== -1) return '#2e7d32';
    if (s.indexOf('hipsa') !== -1) return '#1565c0';
    if (s.indexOf('phps') !== -1) return '#e65100';
    return '#7b1fa2';
}

// DATA KELOMPOK DARI BLADE
var kelompokData = @json($kelompok);

// SIMPAN LAYER POLYGON PER KELOMPOK
var kelompokLayers = {};

kelompokData.forEach(function(k) {
    var warna = getSkemaColor(k.skema_ps);

    // POPUP
    var popupContent =
        '<div class="popup-title">' + (k.nama_kelompok || '-') + '</div>' +
        '<b>Ketua:</b> ' + (k.ketua || '-') + '<br>' +
        '<b>Desa:</b> ' + (k.desa || '-') + '<br>' +
        '<b>Kecamatan:</b> ' + (k.kecamatan || '-') + '<br>' +
        '<b>Kabupaten:</b> ' + (k.kabupaten || '-') + '<br>' +
        '<b>Skema:</b> ' + (k.skema_ps || '-') + '<br>' +
        '<b>Luas:</b> ' + (k.luas_lahan ? k.luas_lahan + ' ha' : '-') + '<br>' +
        '<b>Pendamping:</b> ' + (k.nama_pendamping || '-');

    // POLYGON
    if (k.polygon_kelompok) {
        try {
            var geo = typeof k.polygon_kelompok === 'string' ? JSON.parse(k.polygon_kelompok) : k.polygon_kelompok;
            var layer = L.geoJSON(geo, {
                style: {
                    color: warna,
                    weight: 2,
                    opacity: 0.85,
                    fillColor: warna,
                    fillOpacity: 0.25
                }
            }).addTo(map);
            layer.bindPopup(popupContent);
            kelompokLayers[k.id] = layer;
        } catch (e) {
            // polygon tidak valid, lewati
        }
    }

    // MARKER (centroid atau fallback desa)
    if (k.polygon_kelompok) {
        try {
            var geo2 = typeof k.polygon_kelompok === 'string' ? JSON.parse(k.polygon_kelompok) : k.polygon_kelompok;
            if (geo2.type === 'Polygon' && geo2.coordinates && geo2.coordinates[0]) {
                var coords = geo2.coordinates[0];
                var sumLat = 0, sumLng = 0;
                coords.forEach(function(c) { sumLng += c[0]; sumLat += c[1]; });
                var midLat = sumLat / coords.length;
                var midLng = sumLng / coords.length;

                var marker = L.circleMarker([midLat, midLng], {
                    radius: 5,
                    color: '#fff',
                    weight: 1,
                    fillColor: warna,
                    fillOpacity: 1
                }).addTo(map);
                marker.bindPopup(popupContent);
            }
        } catch (e) {
            // skip marker
        }
    }
});

// ZOOM KE KELOMPOK SAAT KLIK TABEL
function zoomToKelompok(id) {
    if (kelompokLayers[id]) {
        map.fitBounds(kelompokLayers[id].getBounds(), { padding: [50, 50], maxZoom: 15 });
        kelompokLayers[id].openPopup();
    }
}

// NOTIFIKASI DROPDOWN
var b = document.getElementById('notifBtn');
var d = document.getElementById('notifDropdown');
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