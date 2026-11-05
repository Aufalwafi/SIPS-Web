@extends('layouts.app')
@section('title', 'Peta Kelompok Binaan - SI PS ONLINE')
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.fullscreen@3.0.2/Control.FullScreen.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css">
<link rel="stylesheet" href="{{ asset('css/map-tools.css') }}">
<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px; }
.page-header h2 { color: #1b5e20; font-size: 20px; font-weight: 700; margin: 0; }

.peta-wrapper {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
#mapPendamping {
    height: 560px;
    border-radius: 8px;
    z-index: 1;
}
.empty-map-alert {
    display: none;
    margin-top: 10px;
    padding: 10px 14px;
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-radius: 6px;
    color: #92400e;
    font-size: 13px;
}
</style>
@endpush

@section('content')
<div class="content">
    {{-- HEADER --}}
    <div class="page-header">
        <div>
            <h2><i class="fas fa-map-marked-alt" style="color:#1b5e20"></i> Peta Wilayah Kelompok Binaan</h2>
            <p style="color:#64748b;font-size:12px;margin:4px 0 0">Sebaran spasial dan batas polygon kelompok dampingan</p>
        </div>
        <a href="{{ route('pendamping.kelompok-saya') }}" class="btn btn-save" style="font-size:12px;height:34px;padding:0 14px">
            <i class="fas fa-list"></i> Daftar Kelompok
        </a>
    </div>

    <div class="peta-wrapper">
        <div id="mapPendamping"></div>
        <div id="emptyMap" class="empty-map-alert">
            <i class="fas fa-info-circle"></i> Peta poligon wilayah belum tersedia untuk kelompok binaan saat ini.
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
(function(){
    var m = L.map('mapPendamping', {
        scrollWheelZoom: false,
        fullscreenControl: true,
        fullscreenControlOptions: {
            position: 'topleft',
            title: 'Perbesar Layar Penuh (Fullscreen)',
            titleCancel: 'Keluar Layar Penuh'
        }
    }).setView([-7.5, 111.5], 10);
    var layers = [];
    var colors = ['#1565c0','#e65100','#2e7d32','#c62828','#6a1b9a','#00838f','#ef6c00','#283593','#558b2f','#ad1457'];

    var hybrid = L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', { maxZoom: 20, subdomains: ['mt0', 'mt1', 'mt2', 'mt3'], attribution: 'Google Hybrid' }).addTo(m);
    var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap contributors' });
    var layersControl = L.control.layers({ 'Google Hybrid': hybrid, 'OpenStreetMap': osm }).addTo(m);

    initMapTools(m, {
        layersControl: layersControl,
        enablePetak: true,
        enableFullscreen: true,
        enableLocate: true,
        enableMeasure: true
    });

    var data = @json($kelompok);

    function flip(c) { return [c[1], c[0]]; }
    function add(c, color) {
        var r = c.map(flip);
        if (r.length >= 3) layers.push(L.polygon(r, { color: color, weight: 2.5, fillOpacity: 0.35 }).addTo(m));
    }
    function parse(g, color) {
        if (!g) return;
        if (typeof g === 'string') {
            try { g = JSON.parse(g); } catch(e) { return; }
        }
        if (g.type === 'FeatureCollection') (g.features || []).forEach(function(f){ parse(f, color); });
        else if (g.type === 'Feature') parse(g.geometry, color);
        else if (g.type === 'GeometryCollection') (g.geometries || []).forEach(function(s){ parse(s, color); });
        else if (g.type === 'Polygon' && g.coordinates && g.coordinates[0]) add(g.coordinates[0], color);
        else if (g.type === 'MultiPolygon') (g.coordinates || []).forEach(function(p){ if (p && p[0]) add(p[0], color); });
    }

    data.forEach(function(k, ci) {
        try {
            var before = layers.length;
            var clr = colors[ci % colors.length];
            parse(k.polygon_kelompok, clr);
            if (layers.length > before) {
                var popup = '<div style="font-family:sans-serif;padding:2px">' +
                    '<strong style="font-size:13px;color:#1b5e20">' + String(k.nama_kelompok || '-').replace(/[<>]/g,'') + '</strong>' +
                    '<div style="font-size:11px;color:#64748b;margin:4px 0 8px">' + (k.desa || '') + ', ' + (k.kecamatan || '') + '</div>' +
                    '<a href="{{ url("/pendamping/kelompok-saya") }}/' + k.id + '/detail" style="display:inline-block;background:#2e7d32;color:#fff;padding:3px 8px;border-radius:4px;font-size:11px;text-decoration:none">Buka Detail Kelompok</a>' +
                    '</div>';
                for (var i = before; i < layers.length; i++) layers[i].bindPopup(popup);
            }
        } catch(e) {}
    });

    if (layers.length) {
        m.fitBounds(L.featureGroup(layers).getBounds().pad(0.15));
    } else {
        document.getElementById('emptyMap').style.display = 'block';
    }
    setTimeout(function(){ m.invalidateSize(); }, 300);
})();
</script>
@endpush