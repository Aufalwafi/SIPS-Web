@extends('layouts.app')
@section('title', 'Dashboard Pendamping - SI PS ONLINE')
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.fullscreen@3.0.2/Control.FullScreen.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css">
<link rel="stylesheet" href="{{ asset('css/map-tools.css') }}">
<style>
.card-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;margin-bottom:20px}.card{background:white;padding:20px;border-radius:7px;text-align:center;box-shadow:0 1px 4px rgba(0,0,0,.09);border:1px solid #e2e8f0}.card h3{color:#607d8b;font-size:13px;font-weight:500;margin-bottom:9px}.value{color:#1b5e20;font-size:25px;font-weight:700}.value.red{color:#d32f2f}.subtext{color:#78909c;font-size:11px;margin-top:4px}.table-box{background:white;border-radius:7px;padding:20px;box-shadow:0 1px 4px rgba(0,0,0,.09);border:1px solid #e2e8f0;margin-bottom:20px}.table-box h2{color:#263238;font-size:16px;margin-bottom:15px}#dashboardMap{height:420px;width:100%;border-radius:8px;position:relative;z-index:1}.map-empty{display:none;margin-top:8px;padding:8px 12px;background:#fff3e0;border:1px solid #ffcc80;border-radius:4px;color:#8d6e63;font-size:13px}@media(max-width:1000px){.card-grid{grid-template-columns:repeat(2,1fr)}}@media(max-width:700px){.card-grid{grid-template-columns:1fr}#dashboardMap{height:360px}}
</style>
@endpush
@section('content')
<h2 style="color:#1b5e20;margin-bottom:6px;font-size:22px;font-weight:700">Dashboard Pendamping</h2>
<p style="color:#475569;font-size:15px;margin-bottom:22px">
    Selamat datang, <b style="color:#1b5e20;font-size:18px;font-weight:700">{{ Auth::user()->nama ?? Auth::user()->username }}</b>
</p>
<div class="card-grid">
    <div class="card"><h3>Kelompok Dampingan</h3><div class="value">{{ $totalKelompok }}</div></div>
    <div class="card"><h3>Total Anggota</h3><div class="value">{{ $totalAnggota }}</div></div>
    <div class="card"><h3>RKPS Menunggu</h3><div class="value red">{{ $rkpsMenunggu }}</div><div class="subtext">Menunggu Verifikasi</div></div>
    <div class="card"><h3>RKT Menunggu</h3><div class="value red">{{ $rktMenunggu }}</div><div class="subtext">Menunggu Verifikasi</div></div>
</div>
<div class="table-box">
    <h2><i class="fas fa-map-marked-alt" style="color:#2e7d32"></i> Peta Kelompok yang Didampingi</h2>
    <div id="dashboardMap"></div>
    <div id="dashboardMapEmpty" class="map-empty">Belum ada kelompok yang memiliki peta wilayah</div>
</div>
@if($laporanTerakhir->count())
<div class="table-box"><h2>Laporan Kegiatan Terakhir</h2><table><thead><tr><th>Tanggal</th><th>Kelompok</th><th>Kegiatan</th></tr></thead><tbody>@foreach($laporanTerakhir as $l)<tr><td>{{ $l->tanggal }}</td><td>{{ $l->nama_kelompok }}</td><td>{{ $l->kegiatan ?? '-' }}</td></tr>@endforeach</tbody></table></div>
@endif
@endsection
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/leaflet.fullscreen@3.0.2/Control.FullScreen.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
<script src="{{ asset('js/map-tools.js') }}"></script>
<script>
(function(){
var map=L.map('dashboardMap',{
    scrollWheelZoom:false,
    fullscreenControl: true,
    fullscreenControlOptions: {
        position: 'topleft',
        title: 'Perbesar Layar Penuh (Fullscreen)',
        titleCancel: 'Keluar Layar Penuh'
    }
}).setView([-7.63,111.52],11),layers=[];
var hybrid = L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}',{maxZoom:20,subdomains:['mt0','mt1','mt2','mt3'],attribution:'Google Hybrid'}).addTo(map);
var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap contributors' });
var layersControl = L.control.layers({ 'Google Hybrid': hybrid, 'OpenStreetMap': osm }).addTo(map);

initMapTools(map, {
    layersControl: layersControl,
    enablePetak: true,
    enableFullscreen: true,
    enableLocate: true,
    enableMeasure: true
});
var colors=['#1565c0','#e65100','#2e7d32','#c62828','#6a1b9a','#00838f','#ef6c00','#283593','#558b2f','#ad1457','#00695c','#4e342e','#37474f','#827717','#1a237e'];
function flip(c){return[c[1],c[0]]}
function addRing(coords,color){if(Array.isArray(coords)&&coords.length>=3){var ring=coords.map(flip);layers.push(L.polygon(ring,{color:color,weight:2,fillOpacity:.35}).addTo(map))}}
function parseGeometry(g,color){if(!g||!g.type)return;if(g.type==='GeometryCollection')(g.geometries||[]).forEach(function(s){parseGeometry(s,color)});else if(g.type==='Polygon'&&g.coordinates&&g.coordinates[0])addRing(g.coordinates[0],color);else if(g.type==='MultiPolygon')(g.coordinates||[]).forEach(function(p){if(p&&p[0])addRing(p[0],color)})}
function parse(data,color){if(!data)return;if(typeof data==='string'){try{data=JSON.parse(data)}catch(e){return}}if(data.type==='FeatureCollection')(data.features||[]).forEach(function(f){parse(f,color)});else if(data.type==='Feature')parseGeometry(data.geometry,color);else parseGeometry(data,color)}
var groups=@json($kelompokPeta);
groups.forEach(function(k,ci){try{var before=layers.length,clr=colors[ci%colors.length];parse(k.polygon_kelompok,clr);if(layers.length>before){var popup='<b>'+String(k.nama_kelompok||'-').replace(/[<>]/g,'')+'</b><br>'+String(k.desa||'-').replace(/[<>]/g,'')+', '+String(k.kecamatan||'-').replace(/[<>]/g,'')+'<div style="margin-top:6px"><a href="{{ url("/pendamping/kelompok-saya") }}/'+k.id+'/masuk" style="color:#1b5e20;font-weight:700">👉 Akses Kelompok</a> &bull; <a href="{{ url("/pendamping/kelompok-saya") }}/'+k.id+'/detail" style="color:#1565c0">Detail</a></div>';for(var i=before;i<layers.length;i++)layers[i].bindPopup(popup)}}catch(e){}});
if(layers.length)map.fitBounds(L.featureGroup(layers).getBounds().pad(.2));else document.getElementById('dashboardMapEmpty').style.display='block';
setTimeout(function(){map.invalidateSize()},300);
})();
</script>
@endpush