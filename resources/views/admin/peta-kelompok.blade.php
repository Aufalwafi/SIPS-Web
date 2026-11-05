@extends('layouts.app')
@section('title', 'Peta Kelompok - SI PS ONLINE')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.fullscreen@3.0.2/Control.FullScreen.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css">
<link rel="stylesheet" href="{{ asset('css/map-tools.css') }}">
<style>
/* GIS Split Layout (Map on Left, Sidebar on Right) */
.gis-layout {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 16px;
    margin-top: 15px;
    height: 640px;
}

/* Sidebar List */
.gis-sidebar {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
.gis-sidebar-header {
    padding: 14px 16px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}
.gis-sidebar-title {
    font-size: 14px;
    font-weight: 700;
    color: #1e293b;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}
.gis-search-input {
    width: 100%;
    height: 36px;
    padding: 6px 12px;
    font-size: 13px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    background: #fff;
    box-sizing: border-box;
    transition: border-color 0.2s;
}
.gis-search-input:focus {
    border-color: #2e7d32;
    outline: none;
    box-shadow: 0 0 0 3px rgba(46,125,50,0.12);
}

.gis-list-container {
    flex: 1;
    overflow-y: auto;
    padding: 10px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.gis-list-container::-webkit-scrollbar {
    width: 6px;
}
.gis-list-container::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

/* Group Card */
.gis-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
}
.gis-card:hover {
    border-color: #2e7d32;
    background: #f8fdf8;
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.06);
}
.gis-card.active {
    border-color: #2e7d32;
    background: #e8f5e9;
    box-shadow: 0 0 0 2px rgba(46,125,50,0.25);
}
.gis-card-name {
    font-size: 13px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 4px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 6px;
}
.gis-card-loc {
    font-size: 11px;
    color: #64748b;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.gis-card-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 11px;
    flex-wrap: wrap;
    gap: 4px;
}
.badge-skema {
    display: inline-block;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 600;
    background: #e8f5e9;
    color: #1b5e20;
}
.badge-peta-ada {
    background: #dcfce7;
    color: #166534;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 600;
}
.badge-peta-kosong {
    background: #fef3c7;
    color: #92400e;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 600;
}

/* Map Column */
.gis-map-wrapper {
    position: relative;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
#map {
    width: 100%;
    height: 100%;
    z-index: 1;
}

/* Map Overlay Legend */
.map-legend-overlay {
    position: absolute;
    bottom: 20px;
    left: 20px;
    background: rgba(255, 255, 255, 0.94);
    backdrop-filter: blur(4px);
    padding: 10px 14px;
    border-radius: 6px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    z-index: 999;
    font-size: 11px;
    border: 1px solid #e2e8f0;
    max-width: 320px;
}
.map-legend-title {
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 6px;
}
.map-legend-items {
    display: flex;
    flex-wrap: wrap;
    gap: 8px 12px;
}
.map-legend-item {
    display: flex;
    align-items: center;
    gap: 5px;
    color: #475569;
}
.map-legend-color {
    width: 12px;
    height: 12px;
    border-radius: 3px;
}

/* Top Stats & Filters */
.top-filter-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    background: #f8fafc;
    padding: 10px 16px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    flex-wrap: wrap;
}
.filter-form-group {
    display: flex;
    gap: 8px;
    align-items: center;
    flex-wrap: wrap;
}
.filter-form-group select {
    height: 34px;
    padding: 4px 10px;
    font-size: 12px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    background: #fff;
}
.btn-filter-submit {
    background: #2e7d32;
    color: #fff;
    border: none;
    height: 34px;
    padding: 0 14px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.btn-filter-submit:hover { background: #1b5e20; }
.btn-filter-reset {
    background: #fff;
    color: #475569;
    border: 1px solid #cbd5e1;
    height: 34px;
    padding: 0 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.btn-filter-reset:hover { background: #f1f5f9; }

/* Popup Styling */
.leaflet-popup-content-wrapper {
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.leaflet-popup-content {
    font-family: inherit;
    font-size: 12px;
    line-height: 1.5;
    margin: 12px 14px;
}
.popup-header {
    font-size: 13px;
    font-weight: 700;
    color: #1b5e20;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 5px;
    margin-bottom: 6px;
}
.popup-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 3px;
    gap: 8px;
}
.popup-label {
    color: #64748b;
    font-weight: 600;
}
.popup-val {
    color: #1e293b;
    font-weight: 600;
    text-align: right;
}

@media (max-width: 992px) {
    .gis-layout {
        grid-template-columns: 1fr;
        height: auto;
    }
    .gis-sidebar {
        height: 350px;
    }
    .gis-map-wrapper {
        height: 480px;
    }
}
</style>
@endpush

@section('content')
<div class="content">
    <div class="box">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;gap:10px;flex-wrap:wrap">
            <div>
                <h2 style="color:#1b5e20;margin:0 0 4px;display:flex;align-items:center;gap:8px">
                    <i class="fas fa-map-marked-alt"></i> Peta Sebaran Kelompok Perhutanan Sosial
                </h2>
                <p style="color:#64748b;font-size:13px;margin:0">
                    Visualisasi spasial poligon dan titik sebaran kelompok PS di Jawa Timur
                </p>
            </div>
            <div style="display:flex;gap:8px;align-items:center">
                <a href="{{ Auth::user()->role === 'admin_cdk' ? route('admin-cdk.dashboard') : route('admin.dashboard') }}" class="btn" style="background:#757575;color:#fff;padding:7px 12px;border-radius:4px;text-decoration:none;font-size:12px">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        {{-- FILTER BAR --}}
        <div class="top-filter-bar">
            <form method="GET" action="{{ Auth::user()->role === 'admin_cdk' ? route('admin-cdk.peta-kelompok') : route('admin.peta-kelompok') }}" class="filter-form-group">
                <select name="skema">
                    <option value="">Semua Skema PS</option>
                    @foreach($daftarSkema as $s)
                        <option value="{{ $s }}" @selected($skema === $s)>{{ $s }}</option>
                    @endforeach
                </select>

                <select name="status_peta">
                    <option value="">Semua Status Peta</option>
                    <option value="ada" @selected($statusPeta === 'ada')>Sudah Ada Poligon</option>
                    <option value="kosong" @selected($statusPeta === 'kosong')>Belum Ada Poligon</option>
                </select>

                <button type="submit" class="btn-filter-submit">
                    <i class="fas fa-filter"></i> Filter
                </button>
                @if(!empty($skema) || !empty($statusPeta) || !empty($search))
                    <a href="{{ Auth::user()->role === 'admin_cdk' ? route('admin-cdk.peta-kelompok') : route('admin.peta-kelompok') }}" class="btn-filter-reset">
                        <i class="fas fa-rotate-left"></i> Reset
                    </a>
                @endif
            </form>

            <div style="display:flex;gap:12px;font-size:12px;color:#64748b;align-items:center">
                <span>Total Ditampilkan: <strong style="color:#1e293b">{{ number_format($kelompok->count()) }}</strong></span>
                <span>Ada Peta: <strong style="color:#166534">{{ number_format($kelompok->filter(fn($k) => !empty($k->polygon_kelompok))->count()) }}</strong></span>
            </div>
        </div>

        {{-- SPLIT GIS LAYOUT (PETA DI KIRI, DAFTAR DI KANAN) --}}
        <div class="gis-layout">
            {{-- MAP PANEL (KIRI) --}}
            <div class="gis-map-wrapper">
                <div id="map"></div>

                {{-- FLOATING LEGEND --}}
                <div class="map-legend-overlay">
                    <div class="map-legend-title"><i class="fas fa-layer-group" style="color:#2e7d32"></i> Legenda Skema &amp; Peta</div>
                    <div class="map-legend-items">
                        <div class="map-legend-item"><div class="map-legend-color" style="background:#2e7d32"></div> HKm / RHPS</div>
                        <div class="map-legend-item"><div class="map-legend-color" style="background:#1565c0"></div> HTR / HIPSA</div>
                        <div class="map-legend-item"><div class="map-legend-color" style="background:#e65100"></div> HD / PHPS</div>
                        <div class="map-legend-item"><div class="map-legend-color" style="background:#7b1fa2"></div> Skema Lain</div>
                        <div class="map-legend-item"><div class="map-legend-color" style="background:#94a3b8"></div> Titik Default</div>
                        <div class="map-legend-item"><div class="map-legend-color" style="background:#f59e0b;border:1px dashed #d97706"></div> Petak Perhutani</div>
                    </div>
                </div>
            </div>

            {{-- SIDEBAR PANEL (KANAN) --}}
            <div class="gis-sidebar">
                <div class="gis-sidebar-header">
                    <div class="gis-sidebar-title">
                        <span><i class="fas fa-list-ul" style="color:#2e7d32"></i> Daftar Kelompok</span>
                        <span style="background:#e8f5e9;color:#1b5e20;font-size:11px;padding:2px 8px;border-radius:10px" id="listCounter">
                            {{ $kelompok->count() }} Kelompok
                        </span>
                    </div>
                    <input type="text" id="liveSearchList" class="gis-search-input" placeholder="Cari nama / desa / kec..." onkeyup="filterSidebarList()">
                </div>

                <div class="gis-list-container" id="gisCardsContainer">
                    @forelse($kelompok as $k)
                    <div class="gis-card" id="card-{{ $k->id }}" onclick="zoomToKelompok({{ $k->id }})" data-name="{{ strtolower($k->nama_kelompok ?? '') }}" data-desa="{{ strtolower($k->desa ?? '') }}" data-kec="{{ strtolower($k->kecamatan ?? '') }}" data-kab="{{ strtolower($k->kabupaten ?? '') }}">
                        <div class="gis-card-name">
                            <span>{{ $k->nama_kelompok ?? '-' }}</span>
                            @if(!empty($k->polygon_kelompok))
                                <span class="badge-peta-ada" title="Poligon peta tersedia"><i class="fas fa-draw-polygon"></i> Ada Peta</span>
                            @else
                                <span class="badge-peta-kosong" title="Belum memiliki data poligon"><i class="fas fa-exclamation-triangle"></i> Belum</span>
                            @endif
                        </div>
                        <div class="gis-card-loc">
                            <i class="fas fa-location-dot" style="color:#94a3b8"></i>
                            <span>{{ $k->desa ?? '-' }}{{ !empty($k->kecamatan) ? ', Kec. '.$k->kecamatan : '' }}</span>
                        </div>
                        <div class="gis-card-meta">
                            <span class="badge-skema">{{ $k->skema_ps ?? 'Tanpa Skema' }}</span>
                            <span style="font-weight:600;color:#334155">{{ $k->luas_lahan !== null ? number_format($k->luas_lahan, 2, ',', '.') . ' Ha' : '- Ha' }}</span>
                        </div>
                    </div>
                    @empty
                    <div style="text-align:center;padding:30px 15px;color:#94a3b8;font-size:13px">
                        <i class="fas fa-map-location-dot" style="font-size:28px;margin-bottom:8px;display:block;color:#cbd5e1"></i>
                        Tidak ada kelompok yang sesuai dengan filter.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/leaflet.fullscreen@3.0.2/Control.FullScreen.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
<script src="{{ asset('js/map-tools.js') }}"></script>
<script>
// INISIALISASI PETA
var map = L.map('map', {
    fullscreenControl: true,
    fullscreenControlOptions: {
        position: 'topleft',
        title: 'Perbesar Layar Penuh (Fullscreen)',
        titleCancel: 'Keluar Layar Penuh'
    }
}).setView([-7.5361, 112.2384], 9);

// Tile Layer Hybrid (Default) & OSM
var hybrid = L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
    maxZoom: 20,
    subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
    attribution: 'Google Hybrid'
}).addTo(map);

var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap contributors'
});

var layersControl = L.control.layers({ 'Google Hybrid': hybrid, 'OpenStreetMap': osm }, null, { position: 'topright' }).addTo(map);
var mapTools = null;

// Skema Colors
function getSkemaColor(skema) {
    if (!skema) return '#94a3b8';
    var s = skema.toLowerCase();
    if (s.indexOf('hkm') !== -1 || s.indexOf('rhps') !== -1 || s.indexOf('kemasyarakatan') !== -1) return '#2e7d32';
    if (s.indexOf('htr') !== -1 || s.indexOf('hipsa') !== -1 || s.indexOf('tanaman rakyat') !== -1) return '#1565c0';
    if (s.indexOf('hd') !== -1 || s.indexOf('phps') !== -1 || s.indexOf('hutan desa') !== -1) return '#e65100';
    if (s.indexOf('kemitraan') !== -1 || s.indexOf('adat') !== -1) return '#7b1fa2';
    return '#00897b';
}

var kelompokData = @json($kelompok);
var kelompokLayers = {};

kelompokData.forEach(function(k) {
    var warna = getSkemaColor(k.skema_ps);

    // POPUP HTML
    var popupContent =
        '<div class="popup-header">' + (k.nama_kelompok || '-') + '</div>' +
        '<div class="popup-row"><span class="popup-label">Ketua:</span><span class="popup-val">' + (k.ketua || '-') + '</span></div>' +
        '<div class="popup-row"><span class="popup-label">Desa / Kec:</span><span class="popup-val">' + (k.desa || '-') + ' / ' + (k.kecamatan || '-') + '</span></div>' +
        '<div class="popup-row"><span class="popup-label">Kabupaten:</span><span class="popup-val">' + (k.kabupaten || '-') + '</span></div>' +
        '<div class="popup-row"><span class="popup-label">Skema PS:</span><span class="popup-val">' + (k.skema_ps || '-') + '</span></div>' +
        '<div class="popup-row"><span class="popup-label">Luas Lahan:</span><span class="popup-val">' + (k.luas_lahan ? parseFloat(k.luas_lahan).toLocaleString('id-ID', {minimumFractionDigits:2}) + ' Ha' : '-') + '</span></div>' +
        '<div class="popup-row"><span class="popup-label">Pendamping:</span><span class="popup-val">' + (k.nama_pendamping || '-') + '</span></div>';

    // POLYGON
    if (k.polygon_kelompok) {
        try {
            var geo = typeof k.polygon_kelompok === 'string' ? JSON.parse(k.polygon_kelompok) : k.polygon_kelompok;
            var layer = L.geoJSON(geo, {
                style: {
                    color: warna,
                    weight: 2,
                    opacity: 0.9,
                    fillColor: warna,
                    fillOpacity: 0.3
                }
            }).addTo(map);
            layer.bindPopup(popupContent);
            kelompokLayers[k.id] = layer;

            // Tambahkan centroid circle marker
            if (geo.type === 'Polygon' && geo.coordinates && geo.coordinates[0]) {
                var coords = geo.coordinates[0];
                var sumLat = 0, sumLng = 0;
                coords.forEach(function(c) { sumLng += c[0]; sumLat += c[1]; });
                var midLat = sumLat / coords.length;
                var midLng = sumLng / coords.length;

                var marker = L.circleMarker([midLat, midLng], {
                    radius: 5,
                    color: '#fff',
                    weight: 1.5,
                    fillColor: warna,
                    fillOpacity: 1
                }).addTo(map);
                marker.bindPopup(popupContent);
            }
        } catch (e) {
            // invalid polygon fallback
        }
    }
});

// ZOOM TO KELOMPOK SAAT KLIK KARTU SIDEBAR
function zoomToKelompok(id) {
    // Highlight Active Card
    document.querySelectorAll('.gis-card').forEach(c => c.classList.remove('active'));
    var activeCard = document.getElementById('card-' + id);
    if (activeCard) {
        activeCard.classList.add('active');
    }

    if (kelompokLayers[id]) {
        map.fitBounds(kelompokLayers[id].getBounds(), { padding: [50, 50], maxZoom: 15 });
        kelompokLayers[id].openPopup();
        if (mapTools && mapTools.layerPetak && map.hasLayer(mapTools.layerPetak)) {
            mapTools.loadPetakByKelompok(id);
        }
    } else {
        alert('Kelompok ini belum memiliki data koordinat/poligon spasial pada sistem.');
    }
}

// FIT ALL LAYERS ON LOAD
var allLayers = Object.values(kelompokLayers);
if (allLayers.length) {
    map.fitBounds(L.featureGroup(allLayers).getBounds(), { padding: [30, 30] });
}

// Inisialisasi Fitur Lengkap: Fullscreen, Lokasi Berdiri GPS, Ukur Luas Poligon, Petak Perhutani
try {
    mapTools = initMapTools(map, {
        layersControl: layersControl,
        enablePetak: true,
        enableFullscreen: true,
        enableLocate: true,
        enableMeasure: true
    });
} catch(err) {
    console.warn('Map tools init error:', err);
}

// LIVE FILTER SIDEBAR LIST
function filterSidebarList() {
    var query = (document.getElementById('liveSearchList').value || '').toLowerCase();
    var cards = document.querySelectorAll('.gis-card');
    var visibleCount = 0;

    cards.forEach(function(card) {
        var name = card.getAttribute('data-name') || '';
        var desa = card.getAttribute('data-desa') || '';
        var kec = card.getAttribute('data-kec') || '';
        var kab = card.getAttribute('data-kab') || '';

        var match = name.includes(query) || desa.includes(query) || kec.includes(query) || kab.includes(query);
        if (match) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    var counter = document.getElementById('listCounter');
    if (counter) counter.innerText = visibleCount + ' Kelompok';
}
</script>
@endpush
