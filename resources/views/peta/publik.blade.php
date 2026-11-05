<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Peta Sebaran Kelompok Perhutanan Sosial — Provinsi Jawa Timur</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.fullscreen@3.0.2/Control.FullScreen.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css">
    <link rel="stylesheet" href="{{ asset('css/map-tools.css') }}">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Poppins', sans-serif; }
        body { color: #1e293b; background: #f8fafc; overflow-x: hidden; }

        header {
            padding: 12px 24px;
            background: #0d3813;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .header-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #fff;
        }
        .header-brand img {
            width: 34px;
            height: auto;
        }
        .header-brand h1 {
            font-size: 15px;
            font-weight: 700;
            line-height: 1.1;
        }
        .header-brand span {
            font-size: 10px;
            color: #86efac;
        }
        .btn-back {
            background: rgba(255,255,255,0.15);
            color: #fff;
            text-decoration: none;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s;
        }
        .btn-back:hover { background: rgba(255,255,255,0.25); }

        main {
            max-width: 1400px;
            margin: 16px auto;
            padding: 0 16px;
        }
        .intro-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            flex-wrap: wrap;
            gap: 10px;
        }
        .intro-box h2 {
            font-size: 18px;
            font-weight: 700;
            color: #1b5e20;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .intro-box p {
            font-size: 12px;
            color: #64748b;
        }

        .map-layout {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 14px;
            height: calc(100vh - 150px);
            min-height: 520px;
        }
        .map-container {
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        #map {
            width: 100%;
            height: 100%;
        }

        .sidebar-list {
            background: #fff;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .sidebar-header {
            padding: 12px 14px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
        .search-box {
            width: 100%;
            height: 34px;
            padding: 0 10px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 12px;
            margin-top: 8px;
        }
        .search-box:focus {
            outline: none;
            border-color: #2e7d32;
        }

        .cards-scroll {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .k-card {
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            background: #fff;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 12px;
        }
        .k-card:hover {
            border-color: #2e7d32;
            background: #f0fdf4;
            transform: translateY(-1px);
        }
        .k-card-title {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 2px;
        }
        .k-card-sub {
            color: #64748b;
            font-size: 11px;
            margin-bottom: 4px;
        }
        .k-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            background: #e8f5e9;
            color: #1b5e20;
        }

        .map-legend {
            position: absolute;
            bottom: 20px;
            left: 20px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(4px);
            padding: 8px 12px;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            font-size: 11px;
            z-index: 999;
            border: 1px solid #e2e8f0;
        }
        .legend-item { display: flex; align-items: center; gap: 6px; margin-bottom: 3px; color: #475569; }
        .swatch { width: 12px; height: 12px; border-radius: 3px; }

        @media(max-width: 900px) {
            .map-layout { grid-template-columns: 1fr; height: auto; }
            .map-container { height: 450px; }
            .sidebar-list { height: 350px; }
        }

        @media(max-width: 600px) {
            header { padding: 10px 14px; gap: 8px; }
            .header-brand { gap: 8px; }
            .header-brand img { width: 28px; }
            .header-brand h1 { font-size: 13px; }
            .header-brand span { display: none; }
            .btn-back { padding: 6px 12px; font-size: 11px; }
            main { margin: 10px auto; padding: 0 10px; }
            .intro-box h2 { font-size: 15px; }
            .map-container { height: 360px; }
            .sidebar-list { height: 280px; }
            .map-legend { bottom: 10px; left: 10px; font-size: 10px; padding: 6px 10px; }
        }
    </style>
</head>
<body>

<header>
    <a href="{{ route('landing') }}" class="header-brand">
        <img src="{{ asset('assets/logo-jatim.png') }}"
             onerror="if(!this.dataset.retry){this.dataset.retry='1';this.src='{{ url('/assets/logo-jatim.png') }}';}else if(this.dataset.retry==='1'){this.dataset.retry='2';this.src='/assets/logo-jatim.png';}else if(this.dataset.retry==='2'){this.dataset.retry='3';this.src='/public/assets/logo-jatim.png';}"
             alt="Logo Jatim">
        <div>
            <h1>SI PS ONLINE</h1>
            <span>Dinas Kehutanan Pemerintah Provinsi Jawa Timur</span>
        </div>
    </a>
    <a href="{{ route('landing') }}" class="btn-back">
        <i class="fas fa-arrow-left"></i> <span>Beranda</span>
    </a>
</header>

<main>
    <div class="intro-box">
        <div>
            <h2><i class="fas fa-map-marked-alt"></i> Peta Spasial Kelompok Perhutanan Sosial</h2>
            <p>Sebaran titik koordinat dan poligon kawasan kelompok PS di seluruh Jawa Timur.</p>
        </div>
        <div style="font-size:12px;color:#64748b">
            Total Ditampilkan: <strong style="color:#1b5e20">{{ count($kelompok) }}</strong> Kelompok
        </div>
    </div>

    <div class="map-layout">
        <div class="map-container">
            <div id="map"></div>
            <div class="map-legend">
                <div style="font-weight:700;margin-bottom:5px;color:#1e293b">Skema PS:</div>
                <div class="legend-item"><span class="swatch" style="background:#2e7d32"></span> HKm / Hutan Rakyat</div>
                <div class="legend-item"><span class="swatch" style="background:#1565c0"></span> HTR / HIPSA</div>
                <div class="legend-item"><span class="swatch" style="background:#e65100"></span> HD / PHPS</div>
                <div class="legend-item"><span class="swatch" style="background:#7b1fa2"></span> Skema Lainnya</div>
                <div class="legend-item"><span class="swatch" style="background:#f59e0b;border:1px dashed #d97706"></span> Petak Perhutani</div>
            </div>
        </div>

        <div class="sidebar-list">
            <div class="sidebar-header">
                <strong style="font-size:13px;color:#1e293b"><i class="fas fa-list-ul"></i> Daftar Kelompok</strong>
                <input type="text" id="searchInput" class="search-box" placeholder="Cari nama / desa / kecamatan..." onkeyup="filterList()">
            </div>
            <div class="cards-scroll" id="cardsContainer">
                @forelse($kelompok as $k)
                <div class="k-card" onclick="zoomToGroup({{ $k->id }})" data-search="{{ strtolower(($k->nama_kelompok ?? '').' '.($k->desa ?? '').' '.($k->kecamatan ?? '')) }}">
                    <div class="k-card-title">{{ $k->nama_kelompok }}</div>
                    <div class="k-card-sub"><i class="fas fa-location-dot"></i> {{ $k->desa ?? '-' }}{{ !empty($k->kecamatan) ? ', Kec. '.$k->kecamatan : '' }}</div>
                    <span class="k-badge">{{ $k->skema_ps ?? 'Tanpa Skema' }}</span>
                </div>
                @empty
                <div style="text-align:center;padding:30px;color:#94a3b8;font-size:12px">Belum ada data kelompok.</div>
                @endforelse
            </div>
        </div>
    </div>
</main>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/leaflet.fullscreen@3.0.2/Control.FullScreen.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
<script src="{{ asset('js/map-tools.js') }}"></script>
<script>
var map = L.map('map', {
    fullscreenControl: true,
    fullscreenControlOptions: {
        position: 'topleft',
        title: 'Perbesar Layar Penuh (Fullscreen)',
        titleCancel: 'Keluar Layar Penuh'
    }
}).setView([-7.5361, 112.2384], 8);

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

var mapTools = initMapTools(map, {
    layersControl: layersControl,
    enablePetak: true,
    enableFullscreen: true,
    enableLocate: true,
    enableMeasure: true
});

const kelompok = @json($kelompok);
const groupLayers = {};

function color(skema) {
    const s = String(skema || '').toLowerCase();
    if (s.includes('hkm') || s.includes('rhps') || s.includes('kemasyarakatan')) return '#2e7d32';
    if (s.includes('htr') || s.includes('hipsa') || s.includes('tanaman')) return '#1565c0';
    if (s.includes('hd') || s.includes('phps') || s.includes('desa')) return '#e65100';
    return '#7b1fa2';
}

kelompok.forEach(k => {
    if (!k.polygon_kelompok) return;
    try {
        const geometry = typeof k.polygon_kelompok === 'string' ? JSON.parse(k.polygon_kelompok) : k.polygon_kelompok;
        const clr = color(k.skema_ps);
        const layer = L.geoJSON(geometry, {
            style: { color: clr, weight: 2, fillOpacity: .35, fillColor: clr }
        });
        const popup = `
            <div style="font-family:Poppins,sans-serif;font-size:12px;line-height:1.4">
                <strong style="color:#1b5e20;font-size:13px">${k.nama_kelompok || '-'}</strong><br>
                <b>Ketua:</b> ${k.ketua || '-'}<br>
                <b>Desa/Kec:</b> ${k.desa || '-'} / ${k.kecamatan || '-'}<br>
                <b>Skema:</b> ${k.skema_ps || '-'}
            </div>
        `;
        layer.bindPopup(popup);
        layer.addTo(map);
        groupLayers[k.id] = layer;
    } catch (e) { }
});

const allLayers = Object.values(groupLayers);
if (allLayers.length) {
    map.fitBounds(L.featureGroup(allLayers).getBounds(), { padding: [30, 30] });
}

function zoomToGroup(id) {
    if (groupLayers[id]) {
        map.fitBounds(groupLayers[id].getBounds(), { padding: [50, 50], maxZoom: 15 });
        groupLayers[id].openPopup();
    } else {
        alert('Kelompok ini belum memiliki data koordinat poligon.');
    }
}

function filterList() {
    const q = (document.getElementById('searchInput').value || '').toLowerCase();
    document.querySelectorAll('.k-card').forEach(card => {
        const text = card.getAttribute('data-search') || '';
        card.style.display = text.includes(q) ? '' : 'none';
    });
}
</script>
</body>
</html>
