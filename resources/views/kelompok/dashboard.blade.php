@extends('layouts.app')
@section('title', 'Dashboard Kelompok - SI PS ONLINE')
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.fullscreen@3.0.2/Control.FullScreen.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css">
<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; }
.page-header h2 { color: #1b5e20; font-size: 22px; font-weight: 700; margin: 0; }

.summary-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 22px; }
.scard { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px 20px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.04); transition: transform 0.2s, box-shadow 0.2s; }
.scard:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.07); }
.scard-info h4 { color: #64748b; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 4px; }
.scard-info .val { font-size: 26px; font-weight: 700; color: #1e293b; line-height: 1.2; }
.scard-info .subtext { font-size: 11px; color: #94a3b8; margin-top: 2px; }
.scard-icon { width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }

/* MAP CARD STYLES */
.map-dashboard-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 18px 20px;
    margin-bottom: 22px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.03);
    position: relative;
}
.map-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 14px;
    flex-wrap: wrap;
    gap: 12px;
}
.map-card-title {
    color: #1b5e20;
    font-size: 18px;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}
.map-quick-controls {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.btn-map-action {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    color: #334155;
    padding: 6px 14px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.15s;
    text-decoration: none;
}
.btn-map-action:hover {
    background: #1b5e20;
    color: #fff;
    border-color: #1b5e20;
}

#mapKelompokDashboard {
    height: 520px;
    width: 100%;
    border-radius: 10px;
    background: #1e293b;
    z-index: 1;
    position: relative;
}

/* TOMBOL GPS PERSIS FILE LAMA */
.leaflet-control-locate {
    width: 42px;
    height: 42px;
    background: #fff;
    border-radius: 50%;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.35);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: .2s;
    margin-top: 8px !important;
}
.leaflet-control-locate:hover {
    background: #f5f5f5;
    transform: scale(1.05);
}
.leaflet-control-locate.tracking-active {
    background: #1976d2;
}
.leaflet-control-locate svg {
    width: 22px;
    height: 22px;
    fill: #1976d2;
}
.leaflet-control-locate.tracking-active svg {
    fill: #fff;
}

/* Denyut lembut selagi mencari sinyal GPS pertama kali */
.leaflet-control-locate.searching {
    animation: gpsPulse 1s ease-in-out infinite;
}
@keyframes gpsPulse {
    0%   { box-shadow: 0 2px 10px rgba(25,118,210,.35); }
    50%  { box-shadow: 0 2px 18px rgba(25,118,210,.75); }
    100% { box-shadow: 0 2px 10px rgba(25,118,210,.35); }
}

/* Marker posisi pengguna (titik biru + halo, gaya Google Maps) */
.gps-user-dot {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #1a73e8;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px rgba(26,115,232,.35), 0 0 10px rgba(0,0,0,.4);
}

/* LABEL PETAK PERSIS FILE LAMA */
.label-petak {
    background: transparent;
    border: none;
    box-shadow: none;
    color: #000;
    font-weight: bold;
    font-size: var(--ukuran-toponimi, 12px);
    text-shadow:
        -1px -1px 2px #fff,
         1px -1px 2px #fff,
        -1px  1px 2px #fff,
         1px  1px 2px #fff;
}

/* POPUP CUSTOM */
.custom-popup .leaflet-popup-content-wrapper {
    border-radius: 8px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25);
}

.section-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 22px; }
.section-box { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); overflow: hidden; }
.section-header { padding: 14px 18px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: #fafafa; }
.section-header h3 { margin: 0; font-size: 14px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px; }

.info-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.info-table th { width: 120px; background: #f8fafc; color: #475569; font-weight: 600; text-align: left; padding: 9px 14px; border-bottom: 1px solid #f1f5f9; }
.info-table td { padding: 9px 14px; border-bottom: 1px solid #f1f5f9; color: #1e293b; }

.p-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.p-table th {
    background: #f8fafc; color: #475569; font-weight: 600; text-align: left;
    padding: 10px 14px; border-bottom: 2px solid #e2e8f0;
    font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px;
}
.p-table td { padding: 10px 14px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.p-table tr:hover td { background: #f8fafc; }

.status-badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; }
.status-badge.Menunggu { background: #fef3c7; color: #92400e; }
.status-badge.Disetujui { background: #d1fae5; color: #065f46; }
.status-badge.Ditolak { background: #fee2e2; color: #991b1b; }
.status-badge.Revisi { background: #ffedd5; color: #9a3412; }

@media(max-width:900px) {
    .summary-cards { grid-template-columns: 1fr; }
    .section-grid { grid-template-columns: 1fr; }
    #mapKelompokDashboard { height: 420px; }
}
</style>
@endpush

@section('content')
<div class="content">
    @if(session('success'))
    <div class="alert" style="background:#d1fae5;color:#065f46;padding:10px 16px;border-radius:6px;margin-bottom:16px;border-left:4px solid #22c55e">
        <i class="fas fa-check-circle"></i> {!! session('success') !!}
    </div>
    @endif
    @if(session('error'))
    <div class="alert-error" style="background:#fee2e2;color:#7f1d1d;padding:10px 16px;border-radius:6px;margin-bottom:16px;border-left:4px solid #ef4444">
        <i class="fas fa-exclamation-circle"></i> {!! session('error') !!}
    </div>
    @endif

    {{-- HEADER --}}
    <div class="page-header">
        <div>
            <h2><i class="fas fa-users-viewfinder" style="color:#2e7d32"></i> Dashboard Kelompok</h2>
            <p style="color:#475569;font-size:15px;margin:5px 0 0">
                Selamat datang, <b style="color:#1b5e20;font-size:18px;font-weight:700">{{ $kelompok->nama_kelompok ?? (Auth::user()->nama ?? Auth::user()->username) }}</b>
            </p>
        </div>
        <div style="display:flex;gap:8px">
            <a href="{{ route('kelompok.data-kelompok') }}" class="btn btn-save" style="height:36px;padding:0 14px;font-size:12px">
                <i class="fas fa-id-card"></i> Profil Kelompok
            </a>
            <a href="{{ route('kelompok.peta') }}" class="btn" style="background:#1565c0;color:#fff;height:36px;padding:0 14px;font-size:12px">
                <i class="fas fa-map-marked-alt"></i> Peta Wilayah
            </a>
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="summary-cards">
        <div class="scard" style="border-left:4px solid #2e7d32">
            <div class="scard-info">
                <h4>Total Anggota</h4>
                <div class="val">{{ $totalAnggota }}</div>
                <div class="subtext">Petani / pengelola aktif</div>
            </div>
            <div class="scard-icon" style="background:#e8f5e9;color:#2e7d32"><i class="fas fa-users"></i></div>
        </div>
        <div class="scard" style="border-left:4px solid #f59e0b">
            <div class="scard-info">
                <h4>RKPS Menunggu</h4>
                <div class="val" style="color:{{ $rkpsMenunggu > 0 ? '#b45309' : '#1e293b' }}">{{ $rkpsMenunggu }}</div>
                <div class="subtext">Dalam proses verifikasi</div>
            </div>
            <div class="scard-icon" style="background:#fffbeb;color:#b45309"><i class="fas fa-file-alt"></i></div>
        </div>
        <div class="scard" style="border-left:4px solid #1565c0">
            <div class="scard-info">
                <h4>RKT Menunggu</h4>
                <div class="val" style="color:{{ $rktMenunggu > 0 ? '#1565c0' : '#1e293b' }}">{{ $rktMenunggu }}</div>
                <div class="subtext">Dalam proses verifikasi</div>
            </div>
            <div class="scard-icon" style="background:#eff6ff;color:#1565c0"><i class="fas fa-file-signature"></i></div>
        </div>
    </div>

    {{-- PETA WILAYAH KELOMPOK PERSIS FILE LAMA --}}
    <div class="map-dashboard-card" id="mapCardContainer">
        <div class="map-card-header">
            <h3 class="map-card-title">
                <i class="fas fa-map-marked-alt"></i> Peta Wilayah Kelompok
            </h3>
            <div class="map-quick-controls">
                <a href="{{ route('kelompok.peta') }}" class="btn-map-action" style="background:#1b5e20;color:#fff;border-color:#1b5e20" title="Buka Halaman Peta Lengkap">
                    <i class="fas fa-arrow-up-right-from-square"></i> Buka Peta Lengkap
                </a>
            </div>
        </div>

        <div id="mapKelompokDashboard"></div>
    </div>

    {{-- SECTIONS GRID --}}
    <div class="section-grid">
        {{-- PROFIL KELOMPOK --}}
        <div class="section-box">
            <div class="section-header">
                <h3><i class="fas fa-info-circle" style="color:#2e7d32"></i> Informasi Kelompok</h3>
                <a href="{{ route('kelompok.data-kelompok') }}" style="font-size:11px;color:#2e7d32;font-weight:600;text-decoration:none">Selengkapnya &rarr;</a>
            </div>
            <table class="info-table">
                <tr><th>Nama Kelompok</th><td><strong>{{ $kelompok->nama_kelompok ?? '-' }}</strong></td></tr>
                <tr><th>Ketua</th><td>{{ $kelompok->ketua ?? '-' }}</td></tr>
                <tr><th>CDK Wilayah</th><td>{{ $kelompok->cdk->nama ?? ($kelompok->id_cdk ?? '-') }}</td></tr>
                <tr><th>Desa</th><td>{{ $kelompok->desaWilayah->nama ?? ($kelompok->desa ?? '-') }}</td></tr>
                <tr><th>Skema PS</th><td>
                    @if($kelompok->skema_ps)
                    <span style="background:#e8f5e9;color:#1b5e20;padding:2px 7px;border-radius:4px;font-size:11px;font-weight:600">{{ $kelompok->skema_ps }}</span>
                    @else — @endif
                </td></tr>
                <tr><th>Luas Lahan</th><td><strong>{{ $kelompok->luas_lahan ? number_format($kelompok->luas_lahan, 2, ',', '.') . ' Ha' : '—' }}</strong></td></tr>
            </table>
        </div>

        {{-- STATUS PENGAJUAN TERAKHIR --}}
        <div class="section-box">
            <div class="section-header">
                <h3><i class="fas fa-clock-rotate-left" style="color:#1565c0"></i> Status Pengajuan Terakhir</h3>
            </div>
            <table class="p-table">
                <thead>
                    <tr>
                        <th style="width:70px">Dokumen</th>
                        <th style="width:90px">Tanggal</th>
                        <th style="width:100px;text-align:center">Status</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>RKPS</strong></td>
                        <td style="color:#64748b;font-size:11px">
                            @if($rkpsTerakhir)
                                {{ $rkpsTerakhir->tgl_pengesahan ? \Carbon\Carbon::parse($rkpsTerakhir->tgl_pengesahan)->format('d/m/Y') : ($rkpsTerakhir->tgl_penilaian ? \Carbon\Carbon::parse($rkpsTerakhir->tgl_penilaian)->format('d/m/Y') : '—') }}
                            @else
                                <span style="color:#94a3b8">—</span>
                            @endif
                        </td>
                        <td style="text-align:center">
                            @if($rkpsTerakhir)
                                <span class="status-badge {{ $rkpsTerakhir->status }}">{{ $rkpsTerakhir->status }}</span>
                            @else
                                <span style="color:#94a3b8;font-size:11px">—</span>
                            @endif
                        </td>
                        <td style="color:#64748b;font-size:11px">{{ $rkpsTerakhir->catatan_admin ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td><strong>RKT</strong></td>
                        <td style="color:#64748b;font-size:11px">
                            @if($rktTerakhir)
                                {{ $rktTerakhir->tgl_penetapan ? \Carbon\Carbon::parse($rktTerakhir->tgl_penetapan)->format('d/m/Y') : '—' }}
                            @else
                                <span style="color:#94a3b8">—</span>
                            @endif
                        </td>
                        <td style="text-align:center">
                            @if($rktTerakhir)
                                <span class="status-badge {{ $rktTerakhir->status }}">{{ $rktTerakhir->status }}</span>
                            @else
                                <span style="color:#94a3b8;font-size:11px">—</span>
                            @endif
                        </td>
                        <td style="color:#64748b;font-size:11px">{{ $rktTerakhir->catatan_admin ?? '—' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/leaflet.fullscreen@3.0.2/Control.FullScreen.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
<script src="https://unpkg.com/@tmcw/togeojson@5.8.1/dist/togeojson.umd.js"></script>

<script>
(function() {
    var mapContainer = document.getElementById('mapKelompokDashboard');
    if (!mapContainer) return;

    /* =========================================================
       1. MAP UTAMA DENGAN FULLSCREEN KONTROL PERSIS FILE LAMA
    ========================================================= */
    var map = L.map('mapKelompokDashboard', {
        fullscreenControl: true,
        fullscreenControlOptions: {
            position: 'topright',
            title: 'Tampilkan Fullscreen',
            titleCancel: 'Keluar Fullscreen'
        }
    }).setView([-7.612450, 111.518920], 13);

    /* =========================================================
       2. BASEMAP GOOGLE SATELLITE + LABELS (GOOGLE HYBRID)
    ========================================================= */
    var googleSat = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
        maxZoom: 21,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        attribution: 'Google Satellite'
    });

    var googleLabels = L.tileLayer('https://{s}.google.com/vt/lyrs=h&x={x}&y={y}&z={z}', {
        maxZoom: 21,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        attribution: 'Google Labels'
    });

    var googleHybrid = L.layerGroup([
        googleSat,
        googleLabels
    ]).addTo(map);

    /* =========================================================
       3. GPS TRACKING REAL-TIME (titik biru bergerak terus,
          seperti Google Maps) — watchPosition, bukan sekali klik
    ========================================================= */
    var userMarker      = null;   // marker titik biru
    var accuracyCircle  = null;   // lingkaran akurasi
    var currentUserPos  = null;   // [lat, lng] posisi terakhir
    var gpsWatchId      = null;   // id dari watchPosition
    var btnGpsLocate    = null;   // elemen tombol

    var userIcon = L.divIcon({
        className: '',
        html: '<div style="position:relative;"><div class="gps-user-dot"></div></div>',
        iconSize: [18, 18],
        iconAnchor: [9, 9]
    });

    function handleGpsSuccess(pos) {
        var lat = pos.coords.latitude;
        var lng = pos.coords.longitude;
        var acc = pos.coords.accuracy;

        currentUserPos = [lat, lng];

        if (!userMarker) {
            userMarker = L.marker(currentUserPos, { icon: userIcon, zIndexOffset: 1000 })
                .addTo(map)
                .bindPopup('Lokasi Anda', { className: 'custom-popup' });
        } else {
            userMarker.setLatLng(currentUserPos);
        }

        if (!accuracyCircle) {
            accuracyCircle = L.circle(currentUserPos, {
                radius: acc,
                color: '#1a73e8',
                weight: 1,
                fillColor: '#4da3ff',
                fillOpacity: 0.15
            }).addTo(map);
        } else {
            accuracyCircle.setLatLng(currentUserPos);
            accuracyCircle.setRadius(acc);
        }

        if (btnGpsLocate) {
            btnGpsLocate.classList.remove('searching');
            btnGpsLocate.classList.add('tracking-active');
        }
    }

    function handleGpsError(err) {
        if (btnGpsLocate) {
            btnGpsLocate.classList.remove('searching');
            btnGpsLocate.classList.remove('tracking-active');
        }
        alert('GPS tidak dapat digunakan atau izin lokasi ditolak.');
    }

    function startGpsTracking(reCenter) {
        if (!navigator.geolocation) {
            alert('Perangkat/browser Anda tidak mendukung GPS.');
            return;
        }

        if (btnGpsLocate) {
            btnGpsLocate.classList.add('searching');
        }

        // Jika sudah pernah tracking, jangan buat watcher dobel
        if (gpsWatchId === null) {
            gpsWatchId = navigator.geolocation.watchPosition(
                function(pos) {
                    handleGpsSuccess(pos);
                    if (reCenter) {
                        map.flyTo([pos.coords.latitude, pos.coords.longitude], 17, { animate: true, duration: 1.2 });
                        reCenter = false;
                    }
                },
                handleGpsError,
                { enableHighAccuracy: true, timeout: 15000, maximumAge: 3000 }
            );
        } else if (currentUserPos) {
            // Sudah tracking — cukup re-center
            map.flyTo(currentUserPos, 17, { animate: true, duration: 1.2 });
        }
    }

    var LocateControl = L.Control.extend({
        options: { position: 'topright' },
        onAdd: function() {
            var container = L.DomUtil.create('div', 'leaflet-control-locate');
            container.id = 'btnGpsLocateDashboard';
            container.title = 'Lokasi Saya (Real-time)';
            container.innerHTML =
                '<svg viewBox="0 0 24 24">' +
                    '<path d="M12 8a4 4 0 100 8 4 4 0 000-8zm9 3h-2.07A7.97 7.97 0 0013 5.07V3h-2v2.07A7.97 7.97 0 005.07 11H3v2h2.07A7.97 7.97 0 0011 18.93V21h2v-2.07A7.97 7.97 0 0018.93 13H21v-2z"/>' +
                '</svg>';

            L.DomEvent.disableClickPropagation(container);

            btnGpsLocate = container;

            container.onclick = function() {
                if (container.classList.contains('tracking-active') && currentUserPos) {
                    // Sudah tracking → klik lagi cukup zoom ke posisi
                    map.flyTo(currentUserPos, 18, { animate: true, duration: 1.0 });
                    if (userMarker) userMarker.openPopup();
                } else {
                    // Belum tracking → mulai tracking real-time
                    startGpsTracking(true);
                }
            };

            return container;
        }
    });
    map.addControl(new LocateControl());

    // Hentikan tracking otomatis saat berpindah halaman (hemat baterai HP)
    window.addEventListener('beforeunload', function() {
        if (gpsWatchId !== null) {
            navigator.geolocation.clearWatch(gpsWatchId);
        }
    });

    /* =========================================================
       4. FITUR UKUR LUAS & KELILING TURF.JS PERSIS FILE LAMA
    ========================================================= */
    var drawnItems = new L.FeatureGroup().addTo(map);

    var drawControl = new L.Control.Draw({
        position: 'topleft',
        draw: {
            polygon: {
                allowIntersection: false,
                showArea: true,
                shapeOptions: { color: '#ff0000' }
            },
            rectangle: false,
            circle: false,
            marker: false,
            polyline: false,
            circlemarker: false
        },
        edit: { featureGroup: drawnItems }
    });
    map.addControl(drawControl);

    map.on('draw:created', function(e) {
        var layer = e.layer;
        drawnItems.addLayer(layer);

        var geojson = layer.toGeoJSON();
        var luas = turf.area(geojson);
        var hektar = luas / 10000;
        var panjang = turf.length(geojson, { units: 'kilometers' });

        layer.bindPopup(
            "<b>HASIL PENGUKURAN</b><br><br>" +
            "Luas : <b>" + hektar.toFixed(2) + " Ha</b><br>" +
            "Keliling : " + (panjang * 1000).toFixed(0) + " meter"
        ).openPopup();
    });

    /* =========================================================
       5. POLYGON BATAS KELOMPOK PERSIS FILE LAMA
    ========================================================= */
    var wilayah = {!! json_encode($kelompok->polygon_kelompok ?? null) !!};
    var polygonKelompok = { type: "FeatureCollection", features: [] };

    if (wilayah) {
        try {
            if (typeof wilayah === 'string') wilayah = JSON.parse(wilayah);
            if (wilayah.features && Array.isArray(wilayah.features)) {
                wilayah.features.forEach(function(f) {
                    if (f.geometry && f.geometry.type === "Polygon") {
                        polygonKelompok.features.push({
                            type: "Feature",
                            properties: {},
                            geometry: f.geometry
                        });
                    } else if (f.geometry && f.geometry.type === "GeometryCollection") {
                        f.geometry.geometries.forEach(function(g) {
                            if (g.type === "Polygon") {
                                polygonKelompok.features.push({
                                    type: "Feature",
                                    properties: {},
                                    geometry: g
                                });
                            }
                        });
                    }
                });
            }
        } catch(e) {}
    }

    var layerBatasKelompok = L.geoJSON(polygonKelompok, {
        style: {
            color: "green",
            weight: 3,
            fillOpacity: 0.15
        }
    }).addTo(map);

    if (layerBatasKelompok.getLayers().length > 0) {
        map.fitBounds(layerBatasKelompok.getBounds(), { padding: [30, 30] });
    }

    /* =========================================================
       6. LAYER FEATURE GROUPS
    ========================================================= */
    // layerPetak TIDAK di-addTo(map) secara default — hanya muncul saat
    // user mencentang layer "🌳 Petak Perhutani" di layer control.
    var layerPetak = L.featureGroup();                 // Petak Perhutani (Kuning) — default OFF
    var layerKegiatan = L.featureGroup().addTo(map);   // Peta Kegiatan (Biru) — default ON

    /* =========================================================
       7. LAYER CONTROL (POJOK KANAN ATAS PERSIS FILE LAMA)
    ========================================================= */
    L.control.layers(
        null,
        {
            "📍 Peta Kegiatan": layerKegiatan,
            "🌳 Petak Perhutani": layerPetak
        },
        {
            collapsed: false
        }
    ).addTo(map);

    /* =========================================================
       8. LABEL & UKURAN TOOLTIP PETAK (PERSIS FILE LAMA)
    ========================================================= */
    var semuaTooltip = [];

    function updateLabelPetak() {
        var zoom = map.getZoom();
        semuaTooltip.forEach(function(obj) {
            if (zoom >= 16) {
                if (!obj.tampil) {
                    obj.layer.bindTooltip(obj.tooltip);
                    obj.tampil = true;
                }
            } else {
                if (obj.tampil) {
                    obj.layer.unbindTooltip();
                    obj.tampil = false;
                }
            }
        });
    }

    function updateUkuranLabel() {
        var zoom = map.getZoom();
        var ukuran = 11;
        if (window.innerWidth < 768) {
            if (zoom >= 19) ukuran = 22;
            else if (zoom >= 18) ukuran = 20;
            else if (zoom >= 17) ukuran = 18;
            else if (zoom >= 16) ukuran = 16;
            else ukuran = 14;
        } else {
            if (zoom >= 19) ukuran = 16;
            else if (zoom >= 18) ukuran = 15;
            else if (zoom >= 17) ukuran = 14;
            else if (zoom >= 16) ukuran = 12;
            else ukuran = 11;
        }
        document.documentElement.style.setProperty('--ukuran-toponimi', ukuran + 'px');
    }

    map.on("zoomend", function() {
        updateLabelPetak();
        updateUkuranLabel();
    });

    /* =========================================================
       9. PETAK PERHUTANI (KUNING #FFD600) — FETCH AJAX
          Persis kelompok_peta.blade.php: baru dimuat saat layer
          dicentang, dengan koreksi geser + cek intersect ke batas
          kelompok. Data diambil dari endpoint kelompok.get-petak
          yang dikontrol auth sisi server (aman).
    ========================================================= */
    var lastClickedPetak = null;
    var petakLoaded = false;

    map.on('overlayadd', function(e) {
        if (e.name !== '🌳 Petak Perhutani') return;
        if (petakLoaded) return;

        fetch("{{ route('kelompok.get-petak') }}")
            .then(function(res) {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(function(data) {
                console.log('Jumlah petak diterima:', data.length);

                data.forEach(function(p) {
                    var petak = {
                        type: 'Feature',
                        properties: {},
                        geometry: JSON.parse(p.geometry)
                    };

                    /* ==================================
                       KOREKSI POSISI PETAK
                       (geser 27m ke kiri, 7m ke bawah)
                    ================================== */
                    var geserKiri  = 27;
                    var geserBawah = 7;

                    if (geserKiri !== 0) {
                        petak = turf.transformTranslate(petak, geserKiri, 270, { units: 'meters' });
                    }
                    if (geserBawah !== 0) {
                        petak = turf.transformTranslate(petak, geserBawah, 180, { units: 'meters' });
                    }

                    /* ==================================
                       CEK INTERSECT KE BATAS KELOMPOK
                       (filter JS sisi client, aman)
                    ================================== */
                    var kena = false;
                    polygonKelompok.features.forEach(function(pg) {
                        if (turf.booleanIntersects(pg, petak)) {
                            kena = true;
                        }
                    });

                    console.log(p.kode_toponimi, kena);

                    if (!kena) return;

                    /* ==================================
                       TAMPILKAN PETAK DI MAP
                    ================================== */
                    var geo = L.geoJSON(petak, {
                        style: { color: '#FFD600', weight: 2, opacity: 1, fillOpacity: 0 },
                        onEachFeature: function(feature, layer) {
                            var popupContent =
                                '<b>TOPONIMI :</b> ' + p.kode_toponimi +
                                '<br><b>Petak :</b> ' + p.petak +
                                '<br><b>Anak Petak :</b> ' + p.anak_petak +
                                '<br><b>RPH :</b> ' + p.rph +
                                '<br><b>BKPH :</b> ' + p.bkph +
                                '<br><b>KPH :</b> ' + p.kph;

                            layer.bindPopup(popupContent, { className: 'custom-popup' });

                            // Highlight saat hover & saat diklik — setiap petak beda datanya
                            layer.on({
                                mouseover: function(ev) {
                                    if (ev.target !== lastClickedPetak) {
                                        ev.target.setStyle({ weight: 3.5, color: '#FFF59D' });
                                    }
                                },
                                mouseout: function(ev) {
                                    if (ev.target !== lastClickedPetak) {
                                        geo.resetStyle(ev.target);
                                    }
                                },
                                click: function(ev) {
                                    if (lastClickedPetak && lastClickedPetak !== ev.target) {
                                        geo.resetStyle(lastClickedPetak);
                                    }
                                    lastClickedPetak = ev.target;
                                    lastClickedPetak.setStyle({ weight: 4, color: '#FFFFFF' });
                                }
                            });

                            semuaTooltip.push({
                                layer: layer,
                                tooltip: L.tooltip({ permanent: true, direction: 'center', className: 'label-petak' }).setContent(String(p.kode_toponimi)),
                                tampil: false
                            });
                        }
                    });

                    geo.addTo(layerPetak);
                });

                petakLoaded = true;
                updateLabelPetak();
                updateUkuranLabel();
            })
            .catch(function(error) {
                console.error('Gagal mengambil Petak Perhutani:', error);
                alert('Data Petak Perhutani gagal dimuat.');
            });
    });

    map.on('popupclose', function() {
        if (lastClickedPetak) {
            lastClickedPetak.setStyle({ color: '#FFD600', weight: 2 });
            lastClickedPetak = null;
        }
    });

    /* =========================================================
       10. PETA KEGIATAN (WARNA BIRU #1565c0 PERSIS FILE LAMA)
    ========================================================= */
    var dataKegiatanList = {!! json_encode($kegiatanList ?? []) !!} || [];

    if (dataKegiatanList && dataKegiatanList.length > 0) {
        dataKegiatanList.forEach(function(kg, idx) {
            var namaKegiatan = kg.nama_kegiatan || '-';
            var tanggal = kg.tanggal ? kg.tanggal.substring(0, 10) : '-';
            var lokasi = kg.lokasi || '-';
            var keterangan = kg.keterangan || '-';
            var detailUrl = "{{ url('/kelompok/kegiatan') }}/" + kg.id;

            var popupHTML =
                "<div style='min-width:220px;font-size:12.5px;line-height:1.5'>" +
                "<b style='font-size:15px;color:#2e7d32;'>KEGIATAN</b><hr style='border:0;border-top:1px solid #ddd;margin:8px 0'>" +
                "<b>Nama:</b><br>" + namaKegiatan +
                "<br><br><b>Tanggal:</b><br>" + tanggal +
                "<br><br><b>Lokasi:</b><br>" + lokasi +
                "<br><br><b>Keterangan:</b><br>" + keterangan +
                "<br><br><a href='" + detailUrl + "' style='display:inline-block;background:#2e7d32;color:white;padding:8px 14px;border-radius:6px;text-decoration:none;font-weight:bold;font-size:12px;'>📋 Buka Detail Kegiatan</a>" +
                "</div>";

            if (kg.kml_file) {
                var urlKmlStorage = "{{ asset('storage/kegiatan') }}/" + encodeURIComponent(kg.kml_file);
                var urlKmlUploads = "{{ asset('uploads/kml_kegiatan') }}/" + encodeURIComponent(kg.kml_file);

                fetch(urlKmlStorage)
                    .then(function(res) {
                        if (!res.ok) return fetch(urlKmlUploads);
                        return res;
                    })
                    .then(function(res) {
                        if (!res.ok) throw new Error("HTTP " + res.status);
                        return res.text();
                    })
                    .then(function(kmlText) {
                        var parser = new DOMParser();
                        var kml = parser.parseFromString(kmlText, "text/xml");
                        if (kml.getElementsByTagName("parsererror").length > 0) throw new Error("KML invalid");
                        var geojson = toGeoJSON.kml(kml);
                        var kmlLayer = L.geoJSON(geojson, {
                            style: function() {
                                return {
                                    color: "#1565c0",
                                    weight: 3,
                                    opacity: 1,
                                    fillColor: "#42a5f5",
                                    fillOpacity: 0.25
                                };
                            },
                            pointToLayer: function(feature, latlng) {
                                return L.circleMarker(latlng, {
                                    radius: 6,
                                    color: "#1565c0",
                                    weight: 2,
                                    fillColor: "#42a5f5",
                                    fillOpacity: 0.9
                                });
                            },
                            onEachFeature: function(feature, layer) {
                                layer.bindPopup(popupHTML, { className: 'custom-popup' });
                            }
                        });
                        layerKegiatan.addLayer(kmlLayer);
                    })
                    .catch(function(err) {
                        tampilkanFallbackDenah();
                    });
            } else {
                tampilkanFallbackDenah();
            }

            function tampilkanFallbackDenah() {
                var cLat = -7.612450;
                var cLng = 111.518920;
                if (layerBatasKelompok.getLayers().length > 0) {
                    var c = layerBatasKelompok.getBounds().getCenter();
                    cLat = c.lat;
                    cLng = c.lng;
                }

                var offLat = (idx * 0.0022) - 0.0015;
                var offLng = (idx * 0.0022) - 0.0015;
                var lat = cLat + offLat;
                var lng = cLng + offLng;
                var dy = 0.0022;
                var dx = 0.0032;

                var denahCoords = [
                    [lat - dy * 0.6, lng - dx * 0.6],
                    [lat + dy * 0.7, lng - dx * 0.5],
                    [lat + dy * 0.9, lng + dx * 0.3],
                    [lat + dy * 0.2, lng + dx * 0.7],
                    [lat - dy * 0.8, lng + dx * 0.5],
                    [lat - dy * 0.6, lng - dx * 0.6]
                ];

                var polyKegiatan = L.polygon(denahCoords, {
                    color: "#1565c0",       // Biru persis file lama
                    weight: 3,
                    opacity: 1,
                    fillColor: "#42a5f5",   // Fill biru muda persis file lama
                    fillOpacity: 0.25
                });
                polyKegiatan.bindPopup(popupHTML, { className: 'custom-popup' });
                layerKegiatan.addLayer(polyKegiatan);

                // Circle marker titik kegiatan persis file lama
                var circleMarker = L.circleMarker([lat, lng], {
                    radius: 6,
                    color: "#1565c0",
                    weight: 2,
                    fillColor: "#42a5f5",
                    fillOpacity: 0.9
                });
                circleMarker.bindPopup(popupHTML, { className: 'custom-popup' });
                layerKegiatan.addLayer(circleMarker);
            }
        });
    }

    // Fullscreen invalidate size
    map.on('fullscreenchange', function() {
        setTimeout(function() { map.invalidateSize(); }, 200);
    });

    setTimeout(function() { map.invalidateSize(); }, 300);
})();
</script>
@endpush
