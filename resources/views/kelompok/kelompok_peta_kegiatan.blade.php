<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Peta Kegiatan - {{ $kegiatan->nama_kegiatan ?? 'Kegiatan' }}</title>

    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.fullscreen@3.0.2/Control.FullScreen.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css">
    <link rel="stylesheet" href="{{ asset('css/map-tools.css') }}">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/leaflet.fullscreen@3.0.2/Control.FullScreen.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
    <script src="{{ asset('js/map-tools.js') }}"></script>

    <!-- Leaflet Omnivore KML -->
    <script src="https://unpkg.com/leaflet-omnivore@0.3.4/leaflet-omnivore.min.js"></script>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family: 'Poppins', Arial, sans-serif;
            overflow: hidden;
            background: #eef3ef;
        }

        /* HEADER TOP BAR */
        .header {
            height: 70px;
            background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 100%);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 24px;
            position: relative;
            z-index: 1000;
            box-shadow: 0 3px 12px rgba(0,0,0,0.22);
        }

        .header-title {
            display: flex;
            flex-direction: column;
        }

        .header-title strong {
            font-size: 20px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            letter-spacing: -0.2px;
        }

        .header-title small {
            opacity: 0.92;
            font-size: 13px;
            font-weight: 400;
            margin-top: 2px;
        }

        .btn-back {
            color: #fff;
            text-decoration: none;
            background: rgba(255, 255, 255, 0.16);
            border: 1px solid rgba(255, 255, 255, 0.25);
            padding: 9px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.28);
            transform: translateY(-1px);
        }

        /* MAP FULLSCREEN */
        #map {
            position: absolute;
            top: 70px;
            left: 0;
            right: 0;
            bottom: 0;
        }

        /* INFORMASI PETA (FLOATING BOTTOM RIGHT) */
        .info-panel {
            position: absolute;
            z-index: 900;
            right: 18px;
            bottom: 25px;
            background: #ffffff;
            padding: 15px 18px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.22);
            min-width: 240px;
            border: 1px solid rgba(0,0,0,0.06);
        }

        .info-panel h3 {
            color: #1b5e20;
            margin: 0 0 12px;
            font-size: 15px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 7px 0;
            font-size: 13px;
            color: #334155;
        }

        .status-row b {
            margin-left: auto;
            font-size: 14px;
            color: #0f172a;
        }

        .dot {
            width: 13px;
            height: 13px;
            border-radius: 50%;
            display: inline-block;
            flex-shrink: 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }

        .dot-green {
            background: #2e7d32;
        }

        .dot-red {
            background: #d32f2f;
        }

        .line-green {
            width: 26px;
            height: 4px;
            background: #198754;
            border-radius: 2px;
            display: inline-block;
            flex-shrink: 0;
        }

        .line-blue {
            width: 26px;
            height: 4px;
            background: #1976d2;
            border-radius: 2px;
            display: inline-block;
            flex-shrink: 0;
        }

        .info-divider {
            border-top: 1px solid #e2e8f0;
            margin: 10px 0;
        }

        /* POPUP STYLING */
        .leaflet-popup-content-wrapper {
            border-radius: 12px;
            box-shadow: 0 6px 24px rgba(0,0,0,0.25);
            padding: 0;
            overflow: hidden;
        }

        .leaflet-popup-content {
            margin: 14px 16px;
            line-height: 1.45;
        }

        .popup-foto {
            width: 250px;
            max-width: 80vw;
            font-size: 12px;
            color: #334155;
        }

        .popup-foto img {
            width: 100%;
            max-height: 180px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: transform 0.15s ease;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }

        .popup-foto img:hover {
            transform: scale(1.02);
        }

        .popup-header {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
        }

        .badge-status {
            padding: 7px 10px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 11.5px;
            margin: 8px 0;
            display: flex;
            align-items: center;
            gap: 6px;
            letter-spacing: 0.2px;
        }

        .badge-dalam {
            background: #e8f5e9;
            color: #1b5e20;
            border: 1px solid #a5d6a7;
        }

        .badge-luar {
            background: #ffebee;
            color: #b71c1c;
            border: 1px solid #ffcdd2;
        }

        .popup-info-line {
            display: flex;
            align-items: center;
            gap: 6px;
            margin: 4px 0;
            font-size: 12px;
        }

        .popup-info-line b {
            min-width: 72px;
            color: #475569;
        }

        /* MODAL FULLSCREEN FOTO */
        #modalFoto {
            display: none;
            position: fixed;
            z-index: 99999;
            inset: 0;
            background: rgba(0, 0, 0, 0.92);
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        #modalFoto img {
            max-width: 95vw;
            max-height: 92vh;
            width: auto;
            height: auto;
            object-fit: contain;
            background: #fff;
            padding: 8px;
            border: 4px solid #fff;
            border-radius: 10px;
            box-shadow: 0 5px 30px rgba(0,0,0,0.7);
        }

        #btnTutupFoto {
            position: absolute;
            top: 15px;
            right: 20px;
            width: 44px;
            height: 44px;
            border: none;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.25);
            color: #fff;
            font-size: 26px;
            cursor: pointer;
            z-index: 100000;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
        }

        #btnTutupFoto:hover {
            background: rgba(255, 255, 255, 0.45);
        }

        @media(max-width: 600px) {
            .header {
                height: 60px;
                padding: 8px 14px;
            }
            .header-title strong { font-size: 16px; }
            .header-title small { font-size: 11px; }
            .btn-back { padding: 7px 12px; font-size: 12px; }
            #map { top: 60px; }
            .info-panel {
                right: 10px;
                bottom: 15px;
                min-width: 200px;
                padding: 10px 14px;
                font-size: 12px;
            }
            .info-panel h3 { font-size: 13.5px; }
        }
    </style>
</head>
<body>

    <!-- TOP BAR -->
    <div class="header">
        <div class="header-title">
            <strong>
                <i class="bi bi-map-fill"></i>
                {{ $kegiatan->nama_kegiatan ?? 'Peta Kegiatan' }}
            </strong>
            <small>
                {{ $kelompok->nama_kelompok ?? 'Kelompok' }}
            </small>
        </div>
        <a class="btn-back" href="{{ route('kelompok.kegiatan.detail', $kegiatan->id) }}">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- MAP -->
    <div id="map"></div>

    <!-- FLOATING INFO PANEL -->
    <div class="info-panel">
        <h3>
            <i class="bi bi-info-circle-fill"></i>
            Informasi Peta
        </h3>
        <div class="status-row">
            <span class="dot dot-green"></span>
            <span>Foto dalam areal:</span>
            <b id="jumlahDalam">0</b>
        </div>
        <div class="status-row">
            <span class="dot dot-red"></span>
            <span>Foto di luar areal:</span>
            <b id="jumlahLuar">0</b>
        </div>
        <div class="info-divider"></div>
        <div class="status-row">
            <span class="line-green"></span>
            <span>Batas areal kelompok</span>
        </div>
        <div class="status-row">
            <span class="line-blue"></span>
            <span>Batas areal kegiatan</span>
        </div>
    </div>

    <!-- MODAL FULL FOTO -->
    <div id="modalFoto">
        <button id="btnTutupFoto" type="button" title="Tutup">&times;</button>
        <img id="fotoFull" src="" alt="Foto Geotagging">
    </div>

    <script>
        /* ==========================================
           DATA DARI PHP / LARAVEL
        ========================================== */
        const kmlUrl = @json($kmlUrl);
        const dataFoto = @json($dokumentasi);
        const dataPolygonKelompok = @json($kelompok->polygon_kelompok ?? '');

        /* ==========================================
           INISIALISASI LEAFLET MAP
        ========================================== */
        const map = L.map('map', {
            zoomControl: true,
            fullscreenControl: true,
            fullscreenControlOptions: {
                position: 'topleft',
                title: 'Perbesar Layar Penuh (Fullscreen)',
                titleCancel: 'Keluar Layar Penuh'
            }
        });

        // Google Hybrid Tile (Satelit + Jalan + Label)
        const hybrid = L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
            maxZoom: 21,
            attribution: 'Leaflet | Google Hybrid'
        });
        hybrid.addTo(map);

        const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        });

        // Layers
        const layerKelompok = L.layerGroup().addTo(map);
        const layerKegiatan = L.layerGroup().addTo(map);
        const layerFoto = L.layerGroup().addTo(map);

        const layersControl = L.control.layers({
            'Google Hybrid': hybrid,
            'OpenStreetMap': osm
        }, {
            '🗺️ Batas Kelompok': layerKelompok,
            '📍 Batas Kegiatan': layerKegiatan,
            '📷 Foto Geotagging': layerFoto
        }, { position: 'topright' }).addTo(map);

        // Inisialisasi Fitur: Fullscreen, Lokasi Berdiri GPS, Ukur Luas, Petak Perhutani
        initMapTools(map, {
            kelompokId: {{ $kegiatan->id_kelompok ?? ($kelompok->id ?? 'null') }},
            layersControl: layersControl,
            enablePetak: true,
            enableFullscreen: true,
            enableLocate: true,
            enableMeasure: true
        });

        let polygonKegiatanList = [];
        let semuaBounds = [];

        /* ==========================================
           ALGORITMA POINT IN POLYGON (RAY CASTING)
        ========================================== */
        function pointInPolygon(lat, lng, polygon) {
            let inside = false;
            for (let i = 0, j = polygon.length - 1; i < polygon.length; j = i++) {
                const xi = polygon[i][0];
                const yi = polygon[i][1];
                const xj = polygon[j][0];
                const yj = polygon[j][1];

                const intersect = ((yi > lng) !== (yj > lng)) &&
                    (lat < (xj - xi) * (lng - yi) / (yj - yi) + xi);

                if (intersect) {
                    inside = !inside;
                }
            }
            return inside;
        }

        function cekDalamPolygonKegiatan(lat, lng) {
            for (const polygon of polygonKegiatanList) {
                if (pointInPolygon(lat, lng, polygon)) {
                    return true;
                }
            }
            return false;
        }

        /* ==========================================
           EKSTRAKSI POLYGON DARI LAYER
        ========================================== */
        function ambilPolygonDariLayer(layer, targetArray) {
            if (!layer) return;

            if (layer instanceof L.Polygon) {
                const latlngs = layer.getLatLngs();

                function proses(arr) {
                    if (!arr || arr.length === 0) return;
                    if (arr[0] instanceof L.LatLng) {
                        const polygon = arr.map(p => [p.lat, p.lng]);
                        if (polygon.length >= 3) {
                            targetArray.push(polygon);
                        }
                    } else {
                        arr.forEach(sub => proses(sub));
                    }
                }

                proses(latlngs);
            }

            if (layer.eachLayer) {
                layer.eachLayer(child => {
                    ambilPolygonDariLayer(child, targetArray);
                });
            }
        }

        /* ==========================================
           GAMBAR POLYGON KELOMPOK
        ========================================== */
        function gambarGeoJSONKelompok(geojson) {
            if (!geojson) return;
            let data = geojson;
            if (typeof data === 'string') {
                try {
                    data = JSON.parse(data);
                } catch (e) {
                    console.error('GeoJSON kelompok tidak valid', e);
                    return;
                }
            }

            try {
                const layer = L.geoJSON(data, {
                    style: function() {
                        return {
                            color: '#198754',
                            weight: 3.5,
                            opacity: 0.9,
                            fillColor: '#198754',
                            fillOpacity: 0.08
                        };
                    }
                });
                layer.addTo(layerKelompok);

                const bounds = layer.getBounds();
                if (bounds.isValid()) {
                    semuaBounds.push(bounds);
                }
            } catch (e) {
                console.error('Gagal menampilkan polygon kelompok:', e);
            }
        }

        /* ==========================================
           MARKER FOTO DENGAN VERIFIKASI SPASIAL
        ========================================== */
        function buatMarkerFoto(foto, dalam) {
            const lat = parseFloat(foto.latitude);
            const lng = parseFloat(foto.longitude);

            if (isNaN(lat) || isNaN(lng)) return;

            const warna = dalam ? '#2e7d32' : '#d32f2f';

            const icon = L.divIcon({
                className: '',
                html: `
                    <div style="
                        width: 34px;
                        height: 34px;
                        border-radius: 50%;
                        background: ${warna};
                        border: 3px solid #ffffff;
                        box-shadow: 0 3px 8px rgba(0,0,0,0.45);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: #ffffff;
                        font-size: 15px;
                    ">
                        <i class="fas fa-camera"></i>
                    </div>
                `,
                iconSize: [34, 34],
                iconAnchor: [17, 17]
            });

            const marker = L.marker([lat, lng], { icon: icon });

            // Resolusi URL Foto
            let fotoUrl = foto.foto_url;
            if (!fotoUrl && foto.nama_file) {
                fotoUrl = `/storage/kegiatan/${encodeURIComponent(foto.nama_file)}`;
            }

            let statusHtml;
            if (dalam) {
                statusHtml = `
                    <div class="badge-status badge-dalam">
                        <i class="bi bi-check-circle-fill"></i> DALAM AREAL KEGIATAN
                    </div>
                `;
            } else {
                statusHtml = `
                    <div class="badge-status badge-luar">
                        <i class="bi bi-exclamation-triangle-fill"></i> DI LUAR AREAL KEGIATAN
                    </div>
                `;
            }

            const akurasi = foto.akurasi ? `${foto.akurasi} m` : '-';
            const altitude = foto.altitude ? `${foto.altitude} m` : '-';
            const tanggal = foto.tanggal_foto || foto.created_at || '-';

            const popup = `
                <div class="popup-foto">
                    ${fotoUrl ? `
                        <img src="${fotoUrl}" alt="Foto Geotagging" onclick="bukaFotoFull('${fotoUrl}')" onerror="this.style.display='none'">
                    ` : ''}
                    <div class="popup-header">
                        <i class="bi bi-camera-fill" style="color:#2e7d32"></i>
                        Foto Geotag
                    </div>
                    ${statusHtml}
                    <div style="border-top: 1px solid #e2e8f0; margin: 8px 0;"></div>
                    <div class="popup-info-line">
                        <i class="bi bi-geo-alt-fill" style="color:#ef4444"></i>
                        <b>Latitude:</b>
                        <span>${lat.toFixed(8)}</span>
                    </div>
                    <div class="popup-info-line">
                        <i class="bi bi-geo-alt-fill" style="color:#ef4444"></i>
                        <b>Longitude:</b>
                        <span>${lng.toFixed(8)}</span>
                    </div>
                    <div class="popup-info-line">
                        <i class="bi bi-bullseye" style="color:#0284c7"></i>
                        <b>Akurasi:</b>
                        <span>${akurasi}</span>
                    </div>
                    <div class="popup-info-line">
                        <i class="bi bi-triangle-half" style="color:#8b5cf6"></i>
                        <b>Altitude:</b>
                        <span>${altitude}</span>
                    </div>
                    <div class="popup-info-line">
                        <i class="bi bi-calendar-event" style="color:#f59e0b"></i>
                        <b>Tanggal:</b>
                        <span>${tanggal}</span>
                    </div>
                    ${foto.keterangan ? `
                        <div style="margin-top: 6px; font-size: 11.5px; color: #475569; background: #f8fafc; padding: 6px 8px; border-radius: 6px;">
                            <i class="bi bi-chat-text"></i> ${escapeHtml(foto.keterangan)}
                        </div>
                    ` : ''}
                </div>
            `;

            marker.bindPopup(popup);
            marker.addTo(layerFoto);

            semuaBounds.push(L.latLngBounds([[lat, lng], [lat, lng]]));
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text ?? '';
            return div.innerHTML;
        }

        /* ==========================================
           PROSES SEMUA FOTO
        ========================================== */
        function prosesFoto() {
            let dalam = 0;
            let luar = 0;

            if (Array.isArray(dataFoto)) {
                dataFoto.forEach(foto => {
                    const lat = parseFloat(foto.latitude);
                    const lng = parseFloat(foto.longitude);

                    if (isNaN(lat) || isNaN(lng)) return;

                    let status = false;
                    if (polygonKegiatanList.length > 0) {
                        status = cekDalamPolygonKegiatan(lat, lng);
                    } else {
                        status = false;
                    }

                    if (status) {
                        dalam++;
                    } else {
                        luar++;
                    }

                    buatMarkerFoto(foto, status);
                });
            }

            document.getElementById('jumlahDalam').textContent = dalam;
            document.getElementById('jumlahLuar').textContent = luar;
        }

        /* ==========================================
           FIT ZOOM DAN BOUNDS PETA
        ========================================== */
        function fitSemuaPeta() {
            if (semuaBounds.length === 0) {
                map.setView([-7.6, 111.5], 12);
                return;
            }

            let bounds = semuaBounds[0];
            for (let i = 1; i < semuaBounds.length; i++) {
                bounds = bounds.extend(semuaBounds[i]);
            }

            if (bounds.isValid()) {
                map.fitBounds(bounds, {
                    padding: [35, 35]
                });
            }
        }

        /* ==========================================
           RENDER POLYGON KELOMPOK & KML KEGIATAN
        ========================================== */
        gambarGeoJSONKelompok(dataPolygonKelompok);

        if (kmlUrl) {
            const layerKML = omnivore.kml(kmlUrl);

            layerKML.on('ready', function() {
                layerKML.eachLayer(layer => {
                    if (layer.setStyle) {
                        layer.setStyle({
                            color: '#1976d2',
                            weight: 3.5,
                            opacity: 0.95,
                            fillColor: '#1976d2',
                            fillOpacity: 0.15
                        });
                    }
                });

                layerKML.eachLayer(layer => {
                    layerKegiatan.addLayer(layer);
                });

                polygonKegiatanList = [];
                layerKML.eachLayer(layer => {
                    ambilPolygonDariLayer(layer, polygonKegiatanList);
                });

                const bounds = layerKML.getBounds();
                if (bounds.isValid()) {
                    semuaBounds.push(bounds);
                }

                prosesFoto();
                fitSemuaPeta();
            });

            layerKML.on('error', function(err) {
                console.warn('Gagal memuat KML kegiatan:', err);
                prosesFoto();
                fitSemuaPeta();
            });
        } else {
            prosesFoto();
            fitSemuaPeta();
        }

        /* ==========================================
           MODAL FULL FOTO
        ========================================== */
        const modalFoto = document.getElementById("modalFoto");
        const fotoFull = document.getElementById("fotoFull");
        const btnTutupFoto = document.getElementById("btnTutupFoto");

        function bukaFotoFull(url) {
            if (!url) return;
            fotoFull.src = url;
            modalFoto.style.display = "flex";
        }

        function tutupFotoFull() {
            modalFoto.style.display = "none";
            fotoFull.src = "";
        }

        btnTutupFoto.addEventListener("click", tutupFotoFull);
        modalFoto.addEventListener("click", function(e) {
            if (e.target === modalFoto) tutupFotoFull();
        });
        document.addEventListener("keydown", function(e) {
            if (e.key === "Escape" && modalFoto.style.display === "flex") {
                tutupFotoFull();
            }
        });
    </script>
</body>
</html>
