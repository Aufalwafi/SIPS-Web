@extends('layouts.app')
@section('title', 'Detail Kelompok Binaan - SI PS ONLINE')
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.fullscreen@3.0.2/Control.FullScreen.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css">
<link rel="stylesheet" href="{{ asset('css/map-tools.css') }}">
<style>
.detail-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:20px}.detail-card{background:#fff;border-radius:8px;padding:18px;box-shadow:0 1px 4px rgba(0,0,0,.09);margin-bottom:20px}.detail-card h2{margin:0 0 14px;color:var(--green-header);font-size:16px}.profile-table th{width:180px;background:#f8f9fa;color:#37474f;text-align:left}.profile-table th,.profile-table td{padding:10px;border-bottom:1px solid #e9ecef;font-size:13px}.menu-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px}.menu-grid a{justify-content:center;text-align:center}.map-pendamping{height:380px;border-radius:8px;position:relative;z-index:1}.empty-map{display:none;margin-top:8px;padding:8px 12px;background:#fff3e0;border:1px solid #ffcc80;border-radius:4px;color:#8d6e63;font-size:13px}@media(max-width:768px){.detail-grid{grid-template-columns:1fr}.menu-grid{grid-template-columns:repeat(2,1fr)}.profile-table th{width:125px}}
</style>
@endpush
@section('content')
<div class="content"><div class="box">
<a href="{{ route('pendamping.kelompok-saya') }}" class="btn btn-outline" style="margin-bottom:15px"><i class="fas fa-arrow-left"></i> Kembali ke Kelompok Binaan</a>
<div class="detail-card"><h2><i class="fas fa-map-marked-alt"></i> Peta Wilayah Kelompok</h2><div id="mapPendamping" class="map-pendamping"></div><div id="emptyMap" class="empty-map">Peta wilayah belum tersedia</div></div>
<div class="detail-card"><h2><i class="fas fa-info-circle"></i> Profil Kelompok</h2><table class="profile-table" style="width:100%;border-collapse:collapse"><tr><th>Nama Kelompok</th><td>{{ $kelompok->nama_kelompok ?? '-' }}</td></tr><tr><th>Ketua</th><td>{{ $kelompok->ketua ?? '-' }}</td></tr><tr><th>CDK</th><td>{{ $kelompok->nama_cdk ?? '-' }}</td></tr><tr><th>Desa</th><td>{{ $kelompok->nama_desa ?? $kelompok->desa ?? '-' }}</td></tr><tr><th>Kecamatan</th><td>{{ $kelompok->kecamatan ?? '-' }}</td></tr><tr><th>Kabupaten</th><td>{{ $kelompok->kabupaten ?? '-' }}</td></tr><tr><th>Skema PS</th><td>{{ $kelompok->skema_ps ?? '-' }}</td></tr><tr><th>Luas Lahan</th><td>{{ $kelompok->luas_lahan ?? '-' }} Ha</td></tr></table></div>
<div class="detail-grid"><div class="detail-card"><h2><i class="fas fa-folder-open"></i> Dokumen Perencanaan</h2><p><b>RKPS:</b> {{ $rkpsTerakhir->tahun_mulai ?? '-' }} - {{ $rkpsTerakhir->tahun_selesai ?? '-' }} @if(!empty($rkpsTerakhir->file_rkps))<a href="{{ asset('uploads/rkps/'.$rkpsTerakhir->file_rkps) }}" target="_blank" class="btn btn-view btn-sm">Lihat</a>@endif</p><p><b>RKT:</b> {{ $rktTerakhir->tahun ?? '-' }} @if(!empty($rktTerakhir->file_rkt))<a href="{{ asset('uploads/rkt/'.$rktTerakhir->file_rkt) }}" target="_blank" class="btn btn-view btn-sm">Lihat</a>@endif</p></div><div class="detail-card"><h2><i class="fas fa-users"></i> Ringkasan Anggota</h2><p>Total anggota: <b>{{ $totalAnggota }}</b></p><a href="{{ route('pendamping.kelompok-saya.anggota',$kelompok->id) }}" class="btn btn-view btn-sm">Lihat Data Anggota</a></div></div>
<div class="detail-card"><h2><i class="fas fa-th-large"></i> Menu Kelompok</h2><div class="menu-grid"><a href="{{ route('pendamping.kelompok-saya.anggota',$kelompok->id) }}" class="btn btn-save"><i class="fas fa-user-friends"></i> Data Anggota</a><a href="{{ route('pendamping.kelompok-saya.detail',$kelompok->id) }}#mapPendamping" class="btn btn-view"><i class="fas fa-map"></i> Peta Wilayah</a><a href="{{ route('pendamping.kelompok-saya.data-nte',$kelompok->id) }}" class="btn btn-save"><i class="fas fa-chart-line"></i> Data NTE</a><a href="{{ route('pendamping.kelompok-saya.input-nte',$kelompok->id) }}" class="btn btn-save"><i class="fas fa-plus"></i> Input NTE</a><a href="{{ route('pendamping.kelompok-saya.laporan',$kelompok->id) }}" class="btn btn-save"><i class="fas fa-clipboard-list"></i> Laporan Kegiatan</a><a href="{{ route('pendamping.pengajuan-rkps') }}" class="btn btn-save"><i class="fas fa-file-alt"></i> Pengajuan RKPS</a><a href="{{ route('pendamping.pengajuan-rkt') }}" class="btn btn-save"><i class="fas fa-file-signature"></i> Pengajuan RKT</a></div></div>
</div></div>
@endsection
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/leaflet.fullscreen@3.0.2/Control.FullScreen.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
<script src="{{ asset('js/map-tools.js') }}"></script>
<script>
var map = L.map('mapPendamping', {
    scrollWheelZoom: false,
    fullscreenControl: true,
    fullscreenControlOptions: {
        position: 'topleft',
        title: 'Perbesar Layar Penuh (Fullscreen)',
        titleCancel: 'Keluar Layar Penuh'
    }
}).setView([-7.5, 111.5], 11);
setTimeout(function(){ map.invalidateSize(); }, 300);

var hybrid = L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
    maxZoom: 20,
    subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
    attribution: 'Google Hybrid'
}).addTo(map);

var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap contributors'
});

var layersControl = L.control.layers({ 'Google Hybrid': hybrid, 'OpenStreetMap': osm }).addTo(map);

initMapTools(map, {
    kelompokId: {{ $kelompok->id ?? 'null' }},
    layersControl: layersControl,
    enablePetak: true,
    enableFullscreen: true,
    enableLocate: true,
    enableMeasure: true
});

var colors = ['#1565c0','#e65100','#2e7d32','#c62828','#6a1b9a','#00838f','#ef6c00','#283593','#558b2f','#ad1457'];
var clr = colors[Math.floor(Math.random()*colors.length)], data = @json($kelompok->polygon_kelompok ?? null), layers = [];
function flip(c){ return [c[1], c[0]]; }
function parse(g){
    if(!g||!g.type) return;
    if(g.type==='FeatureCollection') (g.features||[]).forEach(function(f){ parse(f); });
    else if(g.type==='Feature') parse(g.geometry);
    else if(g.type==='GeometryCollection') (g.geometries||[]).forEach(parse);
    else if(g.type==='Polygon'&&g.coordinates&&g.coordinates[0]) layers.push(g.coordinates[0].map(flip));
    else if(g.type==='MultiPolygon') (g.coordinates||[]).forEach(function(p){ if(p[0]) layers.push(p[0].map(flip)); });
}
try {
    if(typeof data==='string') data = JSON.parse(data);
    parse(data);
} catch(e){}

layers.forEach(function(r){
    if(r.length>=3) L.polygon(r,{color:clr,weight:2,fillOpacity:.35}).addTo(map);
});
if(layers.length) map.fitBounds(L.featureGroup(layers).getBounds().pad(.1));
else document.getElementById('emptyMap').style.display='block';
</script>
@endpush
