@extends('layouts.app')
@section('title', 'Detail Kelompok Binaan - SI PS ONLINE')
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.fullscreen@3.0.2/Control.FullScreen.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css">
<link rel="stylesheet" href="{{ asset('css/map-tools.css') }}">
<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px; }
.page-header h2 { color: #1b5e20; font-size: 20px; font-weight: 700; margin: 0; }

.detail-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 20px; }
.dcard { background: #fff; border-radius: 10px; padding: 18px 20px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
.dcard-header { display: flex; align-items: center; gap: 8px; margin-bottom: 14px; font-size: 15px; font-weight: 700; color: #1e293b; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px; }

.menu-shortcuts { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 18px; }
@media(max-width:900px) { .menu-shortcuts { grid-template-columns: repeat(2, 1fr); } }
.menu-btn { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 10px; text-align: center; text-decoration: none; color: #1e293b; display: flex; flex-direction: column; align-items: center; gap: 6px; transition: transform 0.15s, border-color 0.15s, box-shadow 0.15s; font-size: 12px; font-weight: 600; }
.menu-btn i { font-size: 20px; }
.menu-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.06); border-color: #2e7d32; }

.profile-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.profile-table th { width: 130px; background: #f8fafc; color: #475569; font-weight: 600; text-align: left; padding: 8px 12px; border-bottom: 1px solid #f1f5f9; }
.profile-table td { padding: 8px 12px; border-bottom: 1px solid #f1f5f9; color: #1e293b; }

.map-container { height: 320px; border-radius: 8px; z-index: 1; }
.empty-map { display: none; margin-top: 8px; padding: 8px 12px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 6px; color: #92400e; font-size: 12px; }

@media(max-width:900px) { .detail-layout { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="content">
    <a href="{{ route('pendamping.kelompok-saya') }}" class="btn btn-outline" style="margin-bottom:15px;display:inline-flex;align-items:center;gap:6px">
        <i class="fas fa-arrow-left"></i> Kembali ke Kelompok Binaan
    </a>

    {{-- HEADER --}}
    <div class="page-header">
        <div>
            <h2><i class="fas fa-users" style="color:#1b5e20"></i> {{ $kelompok->nama_kelompok ?? '-' }}</h2>
            <p style="color:#64748b;font-size:12px;margin:4px 0 0">
                {{ $kelompok->desa ?? '' }}{{ ($kelompok->desa && $kelompok->kecamatan) ? ', ' : '' }}{{ $kelompok->kecamatan ?? '' }}
                • CDK: <strong>{{ $kelompok->nama_cdk ?? '-' }}</strong>
            </p>
        </div>
        <a href="{{ route('pendamping.kelompok-saya.masuk', $kelompok->id) }}" class="btn" style="background:#1b5e20;color:#fff;font-size:13px;padding:8px 16px;border-radius:6px;font-weight:700;display:inline-flex;align-items:center;gap:8px;text-decoration:none;box-shadow:0 2px 6px rgba(27,94,32,0.25)">
            <i class="fas fa-sign-in-alt"></i> Akses Seluruh Menu Kelompok Ini
        </a>
    </div>

    {{-- SHORTCUT MENU --}}
    <div class="menu-shortcuts">
        <a href="{{ route('pendamping.kelompok-saya.masuk', $kelompok->id) }}" class="menu-btn" style="background:#f0fdf4;border-color:#bbf7d0;color:#166534">
            <i class="fas fa-door-open" style="color:#166534"></i>
            <span>Portal & Peta Kelompok</span>
        </a>
        <a href="{{ route('pendamping.kelompok-saya.data-nte', $kelompok->id) }}" class="menu-btn">
            <i class="fas fa-chart-line" style="color:#7b1fa2"></i>
            <span>Data NTE</span>
        </a>
        <a href="{{ route('pendamping.kelompok-saya.input-nte', $kelompok->id) }}" class="menu-btn">
            <i class="fas fa-plus-circle" style="color:#2e7d32"></i>
            <span>Input NTE Baru</span>
        </a>
        <a href="{{ route('pendamping.kelompok-saya.laporan-kegiatan', $kelompok->id) }}" class="menu-btn">
            <i class="fas fa-clipboard-list" style="color:#1565c0"></i>
            <span>Laporan Kegiatan</span>
        </a>
    </div>

    {{-- DETAIL GRID --}}
    <div class="detail-layout">
        {{-- PROFIL KELOMPOK --}}
        <div class="dcard">
            <div class="dcard-header">
                <i class="fas fa-id-card" style="color:#2e7d32"></i> Profil Kelompok
            </div>
            <table class="profile-table">
                <tr><th>Nama Kelompok</th><td><strong>{{ $kelompok->nama_kelompok ?? '-' }}</strong></td></tr>
                <tr><th>Ketua</th><td>{{ $kelompok->ketua ?? '-' }}</td></tr>
                <tr><th>CDK Wilayah</th><td>{{ $kelompok->nama_cdk ?? '-' }}</td></tr>
                <tr><th>Desa</th><td>{{ $kelompok->nama_desa ?? $kelompok->desa ?? '-' }}</td></tr>
                <tr><th>Kecamatan</th><td>{{ $kelompok->kecamatan ?? '-' }}</td></tr>
                <tr><th>Kabupaten</th><td>{{ $kelompok->kabupaten ?? '-' }}</td></tr>
                <tr><th>Skema PS</th><td>
                    @if($kelompok->skema_ps)
                    <span style="background:#e8f5e9;color:#1b5e20;padding:2px 7px;border-radius:4px;font-weight:600;font-size:11px">{{ $kelompok->skema_ps }}</span>
                    @else — @endif
                </td></tr>
                <tr><th>Luas Lahan</th><td><strong>{{ $kelompok->luas_lahan ? number_format($kelompok->luas_lahan, 2, ',', '.') . ' Ha' : '—' }}</strong></td></tr>
            </table>

            {{-- ANGGOTA SUMMARY --}}
            <div style="margin-top:14px;padding-top:12px;border-top:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center">
                <div style="font-size:12px;color:#475569">
                    Total Petani / Anggota: <strong style="color:#1e293b;font-size:14px">{{ $totalAnggota }}</strong> orang
                </div>
                <a href="{{ route('pendamping.kelompok-saya.anggota', $kelompok->id) }}" class="btn btn-save" style="font-size:11px;padding:4px 10px">
                    <i class="fas fa-users"></i> Kelola Anggota
                </a>
            </div>
        </div>

        {{-- PETA & DOKUMEN --}}
        <div style="display:flex;flex-direction:column;gap:18px">
            <div class="dcard">
                <div class="dcard-header">
                    <i class="fas fa-map-marked-alt" style="color:#1565c0"></i> Peta Wilayah Kelompok
                </div>
                <div id="mapPendamping" class="map-container"></div>
                <div id="emptyMap" class="empty-map">
                    <i class="fas fa-info-circle"></i> Peta polygon belum diunggah untuk kelompok ini.
                </div>
            </div>

            <div class="dcard">
                <div class="dcard-header">
                    <i class="fas fa-folder-open" style="color:#e65100"></i> Dokumen Perencanaan
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                    <div style="background:#f8fafc;padding:10px 12px;border-radius:6px;border:1px solid #e2e8f0">
                        <div style="font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase">RKPS Terakhir</div>
                        <div style="font-size:13px;font-weight:700;color:#1e293b;margin:3px 0">
                            {{ $rkpsTerakhir ? ($rkpsTerakhir->tahun_mulai . ' - ' . $rkpsTerakhir->tahun_selesai) : 'Belum Ada' }}
                        </div>
                        @if(!empty($rkpsTerakhir?->file_rkps))
                        <a href="{{ asset('uploads/rkps/' . $rkpsTerakhir->file_rkps) }}" target="_blank" style="font-size:11px;color:#1565c0;font-weight:600;text-decoration:none">
                            <i class="fas fa-file-pdf"></i> Unduh File
                        </a>
                        @endif
                    </div>
                    <div style="background:#f8fafc;padding:10px 12px;border-radius:6px;border:1px solid #e2e8f0">
                        <div style="font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase">RKT Terakhir</div>
                        <div style="font-size:13px;font-weight:700;color:#1e293b;margin:3px 0">
                            {{ $rktTerakhir ? $rktTerakhir->tahun : 'Belum Ada' }}
                        </div>
                        @if(!empty($rktTerakhir?->file_rkt))
                        <a href="{{ asset('uploads/rkt/' . $rktTerakhir->file_rkt) }}" target="_blank" style="font-size:11px;color:#1565c0;font-weight:600;text-decoration:none">
                            <i class="fas fa-file-pdf"></i> Unduh File
                        </a>
                        @endif
                    </div>
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
(function(){
    var map = L.map('mapPendamping', {
        scrollWheelZoom: false,
        fullscreenControl: true,
        fullscreenControlOptions: {
            position: 'topleft',
            title: 'Perbesar Layar Penuh (Fullscreen)',
            titleCancel: 'Keluar Layar Penuh'
        }
    }).setView([-7.5, 111.5], 11);
    var hybrid = L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', { maxZoom: 20, subdomains: ['mt0', 'mt1', 'mt2', 'mt3'], attribution: 'Google Hybrid' }).addTo(map);
    var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap contributors' });
    var layersControl = L.control.layers({ 'Google Hybrid': hybrid, 'OpenStreetMap': osm }).addTo(map);

    initMapTools(map, {
        kelompokId: {{ $kelompok->id ?? 'null' }},
        layersControl: layersControl,
        enablePetak: true,
        enableFullscreen: true,
        enableLocate: true,
        enableMeasure: true
    });

    var clr = '#2e7d32';
    var data = @json($kelompok->polygon_kelompok ?? null);
    var drawn = [];

    function flip(c) { return [c[1], c[0]]; }
    function parse(g) {
        if (!g || !g.type) return;
        if (g.type === 'FeatureCollection') (g.features || []).forEach(parse);
        else if (g.type === 'Feature') parse(g.geometry);
        else if (g.type === 'GeometryCollection') (g.geometries || []).forEach(parse);
        else if (g.type === 'Polygon' && g.coordinates && g.coordinates[0]) drawn.push(g.coordinates[0].map(flip));
        else if (g.type === 'MultiPolygon') (g.coordinates || []).forEach(function(p){ if (p[0]) drawn.push(p[0].map(flip)); });
    }

    try {
        if (typeof data === 'string') data = JSON.parse(data);
        parse(data);
    } catch(e) {}

    var layers = [];
    drawn.forEach(function(r){
        if (r.length >= 3) {
            layers.push(L.polygon(r, { color: clr, weight: 2.5, fillOpacity: 0.35 }).addTo(map));
        }
    });

    if (layers.length) {
        map.fitBounds(L.featureGroup(layers).getBounds().pad(0.1));
    } else {
        document.getElementById('emptyMap').style.display = 'block';
    }
    setTimeout(function(){ map.invalidateSize(); }, 300);
})();
</script>
@endpush