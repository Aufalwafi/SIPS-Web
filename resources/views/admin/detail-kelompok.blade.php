@extends('layouts.app')
@section('title', 'Detail Kelompok - ' . ($kelompok->nama_kelompok ?? 'SI PS ONLINE'))
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.fullscreen@3.0.2/Control.FullScreen.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css">
<link rel="stylesheet" href="{{ asset('css/map-tools.css') }}">
<style>
/* Fullscreen map */
.map-container:fullscreen,
.map-container:-webkit-full-screen {
    height: 100vh !important;
    width: 100vw !important;
    border-radius: 0;
}
.detail-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }
.detail-header h2 { color: #1b5e20; font-size: 20px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px; }
.detail-header-actions { display: flex; gap: 8px; }

.detail-grid-main { display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 20px; margin-bottom: 20px; }
.detail-card { background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
.detail-card h3 { color: #1b5e20; font-size: 15px; font-weight: 700; margin: 0 0 14px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px; }

.profile-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.profile-table th { width: 160px; background: #f8fafc; color: #475569; text-align: left; padding: 9px 12px; border-bottom: 1px solid #f1f5f9; font-weight: 600; }
.profile-table td { padding: 9px 12px; border-bottom: 1px solid #f1f5f9; color: #1e293b; vertical-align: middle; }

.skema-badge { display: inline-block; padding: 3px 8px; border-radius: 4px; background: #e8f5e9; color: #1b5e20; font-size: 11.5px; font-weight: 700; }
.btn-doc-pdf { display: inline-flex; align-items: center; gap: 4px; background: #fdf2f8; color: #86198f; border: 1px solid #e9d5ff; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; text-decoration: none; }
.btn-doc-pdf:hover { background: #86198f; color: #fff; }

.map-container { height: 340px; border-radius: 6px; border: 1px solid #e2e8f0; z-index: 1; }
.map-empty { display: none; margin-top: 8px; padding: 8px 12px; background: #fffbeb; border: 1px solid #fef3c7; border-radius: 4px; color: #92400e; font-size: 12px; }

.sub-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }

.mini-list-item { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px dashed #e2e8f0; font-size: 12px; }
.mini-list-item:last-child { border-bottom: none; }

@media (max-width: 992px) {
    .detail-grid-main { grid-template-columns: 1fr; }
    .sub-grid { grid-template-columns: 1fr; }
}

.label-petak {
    background: rgba(0, 0, 0, 0.75) !important;
    border: 1px solid #eab308 !important;
    color: #fef08a !important;
    font-size: 9.5px !important;
    font-weight: 700 !important;
    padding: 1px 4px !important;
    border-radius: 3px !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.3) !important;
    pointer-events: none;
}
.map-legend-box {
    background: rgba(255, 255, 255, 0.94);
    backdrop-filter: blur(4px);
    padding: 6px 10px;
    border-radius: 6px;
    border: 1px solid #cbd5e1;
    font-size: 11px;
    line-height: 1.5;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}
.legend-item { display: flex; align-items: center; gap: 6px; }
.legend-swatch { width: 12px; height: 12px; border-radius: 3px; display: inline-block; flex-shrink: 0; }
.petak-popup-table { width: 100%; border-collapse: collapse; font-size: 11.5px; }
.petak-popup-table th { text-align: left; padding: 2px 6px 2px 0; color: #475569; font-weight: 600; white-space: nowrap; }
.petak-popup-table td { padding: 2px 0; color: #0f172a; }
</style>
@endpush

@section('content')
<div class="content">

    {{-- HEADER --}}
    <div class="detail-header">
        <div>
            <h2><i class="fas fa-users" style="color:#2e7d32"></i> {{ $kelompok->nama_kelompok ?? 'Profil Kelompok' }}</h2>
            <p style="color:#64748b;font-size:12.5px;margin:4px 0 0">
                Wilayah: <strong>{{ $kelompok->nama_cdk ?? '-' }}</strong> &bull; Desa {{ $kelompok->desa ?? '-' }}, Kec. {{ $kelompok->kecamatan ?? '-' }}, {{ $kelompok->kabupaten ?? '-' }}
            </p>
        </div>
        <div class="detail-header-actions">
            @php
                $backRoute = Auth::user()->role === 'admin_cdk' ? route('admin-cdk.data-kelompok') : route('admin.data-kelompok');
                $editRoute = Auth::user()->role === 'admin_cdk' ? route('admin-cdk.data-kelompok.edit', $kelompok->id) : route('admin.data-kelompok.edit', $kelompok->id);
            @endphp
            <a href="{{ $backRoute }}" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Kembali</a>
            <a href="{{ $editRoute }}" class="btn btn-edit"><i class="fas fa-edit"></i> Edit Profil & Peta</a>
        </div>
    </div>

    {{-- GRID UTAMA: PROFIL + PETA --}}
    <div class="detail-grid-main">
        
        {{-- PROFIL KELOMPOK --}}
        <div class="detail-card">
            <h3><i class="fas fa-id-card"></i> Informasi Legalitas & Kelembagaan</h3>
            <table class="profile-table">
                <tr>
                    <th>Nama Kelompok</th>
                    <td><strong>{{ $kelompok->nama_kelompok ?? '-' }}</strong></td>
                </tr>
                <tr>
                    <th>Ketua Kelompok</th>
                    <td>{{ $kelompok->ketua ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Wilayah CDK</th>
                    <td>{{ $kelompok->nama_cdk ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Lokasi Administratif</th>
                    <td>Desa {{ $kelompok->desa ?? '-' }}, Kec. {{ $kelompok->kecamatan ?? '-' }}, Kab. {{ $kelompok->kabupaten ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Skema PS</th>
                    <td><span class="skema-badge">{{ $kelompok->skema_ps ?? '-' }}</span></td>
                </tr>
                <tr>
                    <th>Luas Lahan Kelola</th>
                    <td><strong>{{ $kelompok->luas_lahan ? number_format($kelompok->luas_lahan, 2, ',', '.') . ' Ha' : '-' }}</strong></td>
                </tr>
                <tr>
                    <th>Nomor SK Izin</th>
                    <td>{{ $kelompok->no_sk ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Tanggal SK</th>
                    <td>{{ $kelompok->tanggal_sk_formatted ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Dokumen SK PS</th>
                    <td>
                        @if(!empty($kelompok->sk_ps))
                            <a href="{{ route('dokumen.sk-ps', $kelompok->id) }}" target="_blank" class="btn-doc-pdf"><i class="fas fa-file-pdf"></i> Unduh SK PS</a>
                        @else
                            <span style="color:#94a3b8;font-size:12px">Belum diunggah</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>SK Pembentukan</th>
                    <td>
                        @if(!empty($kelompok->sk_kelompok))
                            <a href="{{ route('dokumen.sk-kelompok', $kelompok->id) }}" target="_blank" class="btn-doc-pdf"><i class="fas fa-file-pdf"></i> Unduh SK Kelompok</a>
                        @else
                            <span style="color:#94a3b8;font-size:12px">Belum diunggah</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        {{-- PETA SPASIAL HYBRID --}}
        <div class="detail-card">
            <h3><i class="fas fa-map-marked-alt"></i> Peta Poligon Kawasan Kelola & Petak</h3>
            <div id="mapKelompok" class="map-container"></div>
            <div id="emptyMap" class="map-empty">
                <i class="fas fa-info-circle"></i> Data koordinat poligon wilayah belum tersedia untuk kelompok ini.
                <div style="margin-top:10px">
                    <a href="{{ $editRoute }}" style="display:inline-flex;align-items:center;gap:6px;background:#2e7d32;color:#fff;padding:6px 14px;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none">
                        <i class="fas fa-upload"></i> Unggah Peta (.kml / .geojson)
                    </a>
                </div>
            </div>
        </div>

    </div>

    {{-- GRID KEDUA: RKPS/RKT, ANGGOTA, KUPS --}}
    <div class="sub-grid">
        
        {{-- DOKUMEN KERJA --}}
        <div class="detail-card">
            <h3><i class="fas fa-file-signature"></i> Dokumen Perencanaan</h3>
            <div class="mini-list-item">
                <span><strong>RKPS Terakhir:</strong></span>
                <span>
                    @if($rkpsTerakhir)
                        {{ $rkpsTerakhir->tahun_mulai }} - {{ $rkpsTerakhir->tahun_selesai }}
                        @if(!empty($rkpsTerakhir->file_rkps))
                            <a href="{{ asset('uploads/rkps/' . $rkpsTerakhir->file_rkps) }}" target="_blank" class="btn-doc-pdf" style="margin-left:4px"><i class="fas fa-download"></i> PDF</a>
                        @endif
                    @else
                        <span style="color:#94a3b8">Belum ada</span>
                    @endif
                </span>
            </div>
            <div class="mini-list-item">
                <span><strong>RKT Terakhir:</strong></span>
                <span>
                    @if($rktTerakhir)
                        Tahun {{ $rktTerakhir->tahun }}
                        @if(!empty($rktTerakhir->file_rkt))
                            <a href="{{ asset('uploads/rkt/' . $rktTerakhir->file_rkt) }}" target="_blank" class="btn-doc-pdf" style="margin-left:4px"><i class="fas fa-download"></i> PDF</a>
                        @endif
                    @else
                        <span style="color:#94a3b8">Belum ada</span>
                    @endif
                </span>
            </div>
            <div class="mini-list-item">
                <span><strong>Pendamping Binaan:</strong></span>
                <span>
                    @forelse($pendamping as $pd)
                        <span style="display:block;color:#15803d;font-weight:600"><i class="fas fa-user-check"></i> {{ $pd->nama }}</span>
                    @empty
                        <span style="color:#94a3b8">Belum ditetapkan</span>
                    @endforelse
                </span>
            </div>
        </div>

        {{-- ANGGOTA KELOMPOK --}}
        <div class="detail-card">
            <h3><i class="fas fa-user-friends"></i> Anggota (Total: {{ $totalAnggota }})</h3>
            @forelse($anggota as $ag)
            <div class="mini-list-item">
                <span>{{ $ag->nama_anggota }}</span>
                <span style="color:#64748b;font-size:11px">{{ $ag->komoditas_utama ?? ($ag->luas_garapan ? $ag->luas_garapan . ' Ha' : '-') }}</span>
            </div>
            @empty
            <div style="text-align:center;padding:20px;color:#94a3b8;font-size:12px">Belum ada data anggota.</div>
            @endforelse
        </div>

        {{-- UNIT USAHA KUPS --}}
        <div class="detail-card">
            <h3><i class="fas fa-store"></i> Unit Usaha (KUPS)</h3>
            @forelse($kups as $kp)
            <div class="mini-list-item">
                <span><strong>{{ $kp->nama_kups }}</strong> ({{ $kp->kelas_kups ?? 'Biru' }})</span>
                <span style="color:#15803d;font-size:11px">{{ $kp->jenis_usaha ?? '-' }}</span>
            </div>
            @empty
            <div style="text-align:center;padding:20px;color:#94a3b8;font-size:12px">Belum ada data KUPS.</div>
            @endforelse
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/leaflet.fullscreen@3.0.2/Control.FullScreen.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
<script src="{{ asset('js/map-tools.js') }}"></script>
<script>
(function(){
    var map = L.map('mapKelompok', {
        scrollWheelZoom: true,
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

    var layerKawasan = L.featureGroup().addTo(map);
    var layerPetak = L.featureGroup(); // Tidak aktif di awal agar tidak lagg, aktif saat dicentang

    var baseMaps = {
        'Google Hybrid': hybrid,
        'OpenStreetMap': osm
    };
    var overlayMaps = {
        '🗺️ Kawasan Kelola': layerKawasan,
        '🌳 Petak Perhutani': layerPetak
    };

    L.control.layers(baseMaps, overlayMaps, { position: 'topright' }).addTo(map);

    // Legend Peta
    var LegendControl = L.Control.extend({
        options: { position: 'bottomleft' },
        onAdd: function() {
            var div = L.DomUtil.create('div', 'map-legend-box');
            div.innerHTML =
                '<div style="font-weight:700;margin-bottom:4px;color:#1e293b;border-bottom:1px solid #e2e8f0;padding-bottom:2px">Legenda Peta</div>' +
                '<div class="legend-item"><span class="legend-swatch" style="background:rgba(46,125,50,0.3);border:2px solid #2e7d32"></span> <strong>Kawasan Kelola</strong></div>' +
                '<div class="legend-item" style="margin-top:2px"><span class="legend-swatch" style="background:rgba(255,214,0,0.25);border:2px solid #f59e0b"></span> <strong>Petak Perhutani</strong></div>';
            return div;
        }
    });
    map.addControl(new LegendControl());

    // Tombol Leaflet Pusatkan / Reset Zoom ke Kawasan Kelola
    var ResetZoomControl = L.Control.extend({
        options: { position: 'topleft' },
        onAdd: function() {
            var container = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
            var btn = L.DomUtil.create('a', '', container);
            btn.innerHTML = '<i class="fas fa-compress-arrows-alt" style="font-size:13px;line-height:30px;color:#1e293b;"></i>';
            btn.href = '#';
            btn.title = 'Zoom Pas ke Kawasan Kelola';
            btn.style.width = '30px';
            btn.style.height = '30px';
            btn.style.display = 'block';
            btn.style.textAlign = 'center';
            btn.style.background = '#fff';
            btn.style.cursor = 'pointer';

            L.DomEvent.on(btn, 'click', function(e) {
                L.DomEvent.stopPropagation(e);
                L.DomEvent.preventDefault(e);
                zoomKeKawasan();
            });
            return container;
        }
    });
    map.addControl(new ResetZoomControl());

    // Fitur Tambahan: Tombol Lokasi Berdiri (GPS) dan Mengukur Luasan Poligon
    initMapTools(map, {
        kelompokId: {{ $kelompok->id ?? 'null' }},
        enablePetak: false, // Petak sudah dikelola langsung di view ini
        enableFullscreen: false, // Fullscreen sudah diaktifkan di L.map
        enableLocate: true,
        enableMeasure: true
    });

    function zoomKeKawasan() {
        if (layerKawasan.getLayers().length > 0) {
            map.fitBounds(layerKawasan.getBounds().pad(0.12), { animate: true });
        } else if (layerPetak.getLayers().length > 0) {
            map.fitBounds(layerPetak.getBounds().pad(0.12), { animate: true });
        }
    }

    map.on('fullscreenchange', function() {
        setTimeout(function(){ map.invalidateSize(); }, 200);
    });

    var clr = '#2e7d32';
    var dataPeta = @json($kelompok->polygon_kelompok ?? null);
    var drawn = [];

    function flip(c) { return [c[1], c[0]]; }
    function parseGeometry(geom) {
        if (!geom || !geom.type) return;
        if (geom.type === 'FeatureCollection' && Array.isArray(geom.features)) {
            geom.features.forEach(function(f){ parseGeometry(f.geometry); });
        } else if (geom.type === 'Feature') {
            parseGeometry(geom.geometry);
        } else if (geom.type === 'GeometryCollection' && Array.isArray(geom.geometries)) {
            geom.geometries.forEach(parseGeometry);
        } else if (geom.type === 'Polygon' && Array.isArray(geom.coordinates) && geom.coordinates[0]) {
            drawn.push(geom.coordinates[0].map(flip));
        } else if (geom.type === 'MultiPolygon' && Array.isArray(geom.coordinates)) {
            geom.coordinates.forEach(function(p){ if(p && p[0]) drawn.push(p[0].map(flip)); });
        }
    }

    try {
        if (typeof dataPeta === 'string') dataPeta = JSON.parse(dataPeta);
        parseGeometry(dataPeta);
    } catch(e) {}

    drawn.forEach(function(coords){
        if (coords.length >= 3) {
            var poly = L.polygon(coords, { color: clr, weight: 2.5, fillOpacity: 0.25 }).addTo(layerKawasan);
            poly.bindPopup(
                '<div style="font-size:12px;min-width:180px">' +
                '<b style="color:#1b5e20;font-size:13px">{{ addslashes($kelompok->nama_kelompok ?? "Kawasan Kelola") }}</b><br>' +
                '<span style="color:#64748b">Skema:</span> <b>{{ $kelompok->skema_ps ?? "-" }}</b><br>' +
                '<span style="color:#64748b">Luas Lahan:</span> <b>{{ $kelompok->luas_lahan ? number_format($kelompok->luas_lahan, 2, ",", ".") . " Ha" : "-" }}</b><br>' +
                '<span style="color:#64748b">Nomor SK:</span> {{ addslashes($kelompok->no_sk ?? "-") }}' +
                '</div>'
            );
        }
    });

    if (layerKawasan.getLayers().length > 0) {
        map.fitBounds(layerKawasan.getBounds().pad(0.12));
    } else {
        document.getElementById('emptyMap').style.display = 'block';
    }

    /* =========================================================
       LOAD PETAK PERHUTANI HANYA SAAT DICENTANG (ON-DEMAND)
    ========================================================= */
    var petakLoaded = false;
    var petakLoading = false;

    map.on('overlayadd', function(e) {
        if (e.name === '🌳 Petak Perhutani' || e.layer === layerPetak) {
            loadPetakPerhutani();
        }
    });

    function loadPetakPerhutani() {
        if (petakLoaded || petakLoading) return;
        petakLoading = true;

        fetch("{{ route('kelompok.petak-perhutani', $kelompok->id) }}")
            .then(function(res){
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(function(data){
                petakLoaded = true;
                petakLoading = false;

                data.forEach(function(p){
                    if (!p.geometry) return;
                    try {
                        var geom = typeof p.geometry === 'string' ? JSON.parse(p.geometry) : p.geometry;
                        var petakFeature = {
                            type: 'Feature',
                            properties: p,
                            geometry: geom
                        };

                        // Koreksi datum bila ada pergeseran shapefile Perhutani (27m ke barat, 7m ke selatan)
                        if (typeof turf !== 'undefined') {
                            var geserKiri = 27;
                            var geserBawah = 7;
                            if (geserKiri !== 0) {
                                petakFeature = turf.transformTranslate(petakFeature, geserKiri, 270, { units: 'meters' });
                            }
                            if (geserBawah !== 0) {
                                petakFeature = turf.transformTranslate(petakFeature, geserBawah, 180, { units: 'meters' });
                            }
                        }

                        var petakLayer = L.geoJSON(petakFeature, {
                            style: {
                                color: '#eab308',
                                weight: 1.8,
                                opacity: 0.95,
                                fillColor: '#facc15',
                                fillOpacity: 0.12
                            },
                            onEachFeature: function(feat, lyr) {
                                var popupHtml =
                                    '<div style="font-size:12px;min-width:210px">' +
                                    '<div style="background:#fef9c3;padding:4px 8px;border-radius:4px;border:1px solid #fef08a;margin-bottom:6px;font-weight:700;color:#854d0e">' +
                                    '<i class="fas fa-tree"></i> Petak ' + (p.petak || '-') + ' ' + (p.anak_petak || '') +
                                    (p.kode_toponimi ? ' (' + p.kode_toponimi + ')' : '') +
                                    '</div>' +
                                    '<table class="petak-popup-table">' +
                                    '<tr><th>KPH / BKPH</th><td>' + (p.kph || '-') + ' / ' + (p.bkph || '-') + '</td></tr>' +
                                    '<tr><th>RPH</th><td>' + (p.rph || '-') + '</td></tr>' +
                                    '<tr><th>Fungsi Hutan</th><td>' + (p.fungsi || '-') + ' (' + (p.klas_hutan || '-') + ')</td></tr>' +
                                    '<tr><th>Luas Baku</th><td><b>' + (p.luas_baku ? Number(p.luas_baku).toFixed(2) + ' Ha' : '-') + '</b></td></tr>' +
                                    '<tr><th>Tanaman</th><td>' + (p.jenis_tanaman || '-') + (p.tahun_tanam ? ' (Th ' + p.tahun_tanam + ')' : '') + '</td></tr>' +
                                    '<tr><th>Desa / Kec</th><td>' + (p.desa || '-') + ', ' + (p.kecamatan || '-') + '</td></tr>' +
                                    '</table>' +
                                    '</div>';

                                lyr.bindPopup(popupHtml);

                                if (p.kode_toponimi) {
                                    lyr.bindTooltip(String(p.kode_toponimi), {
                                        permanent: true,
                                        direction: 'center',
                                        className: 'label-petak'
                                    });
                                }

                                lyr.on({
                                    mouseover: function(e) {
                                        e.target.setStyle({ weight: 3, color: '#ffffff', fillOpacity: 0.35 });
                                    },
                                    mouseout: function(e) {
                                        e.target.setStyle({ weight: 1.8, color: '#eab308', fillOpacity: 0.12 });
                                    }
                                });
                            }
                        });

                        petakLayer.addTo(layerPetak);
                    } catch(err) {}
                });
            })
            .catch(function(err){
                petakLoading = false;
                console.error("Gagal memuat petak perhutani:", err);
            });
    }
})();
</script>
@endpush
