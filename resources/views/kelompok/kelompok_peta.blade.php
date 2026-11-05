@extends('layouts.app')
@section('title', 'Peta Wilayah Kelompok - SI PS ONLINE')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.fullscreen@3.0.2/Control.FullScreen.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css"/>

<style>
/* KONTENER KARTU PETA */
.card-map-wrapper {
    background: #ffffff;
    border-radius: 10px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
    border: 1px solid #e2e8f0;
    padding: 24px;
    margin-bottom: 25px;
}

.card-map-wrapper h3 {
    margin-top: 0;
    margin-bottom: 16px;
    color: #2e7d32;
    font-size: 19px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 8px;
}

#mapKelompok {
    height: 560px;
    border-radius: 8px;
    width: 100%;
    background: #e5e7eb;
}

/* TOMBOL GPS */
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

.leaflet-control-locate.active {
    background: #1976d2;
}

.leaflet-control-locate svg {
    width: 22px;
    height: 22px;
    fill: #1976d2;
}

.leaflet-control-locate.active svg {
    fill: #fff;
}

/* LABEL PETAK */
.label-petak {
    background: transparent;
    border: none;
    box-shadow: none;
    color: #000;
    font-weight: bold;
    font-size: var(--ukuran-toponimi, 11px);
    text-shadow:
        -1px -1px 2px #fff,
         1px -1px 2px #fff,
        -1px  1px 2px #fff,
         1px  1px 2px #fff;
}

/* FULLSCREEN RESPONSIVE */
.card-map-wrapper:fullscreen,
.card-map-wrapper:-webkit-full-screen {
    padding: 0;
    margin: 0;
    border: none;
    border-radius: 0;
    width: 100vw;
    height: 100vh;
    background: #000;
}
.card-map-wrapper:fullscreen #mapKelompok,
.card-map-wrapper:-webkit-full-screen #mapKelompok {
    height: 100vh !important;
    border-radius: 0;
}

@media (max-width: 768px) {
    .card-map-wrapper {
        padding: 16px;
    }
    #mapKelompok {
        height: 480px;
    }
}
</style>
@endpush

@section('content')
<div style="padding-bottom: 24px">
    <div class="card-map-wrapper" id="cardMapContainer">
        <h3><i class="fas fa-map-marked-alt"></i> Peta Wilayah Kelompok</h3>
        <div id="mapKelompok"></div>
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
    var map = L.map('mapKelompok', {
        fullscreenControl: true,
        fullscreenControlOptions: {
            position: 'topright',
            title: 'Tampilkan Fullscreen',
            titleCancel: 'Keluar Fullscreen'
        }
    }).setView([-7.612450, 111.518920], 13);

    var markerGPS = null;
    var circleGPS = null;
    var locateButton = null;

    var LocateControl = L.Control.extend({
        options: {
            position: 'topright'
        },

        onAdd: function(map) {
            var container = L.DomUtil.create(
                'div',
                'leaflet-control-locate'
            );
            container.id = 'btnGpsLocatePeta';

            locateButton = container;

            container.innerHTML = `
                <svg viewBox="0 0 24 24">
                    <path d="
                    M12 8a4 4 0 100 8
                    4 4 0 000-8zm9 3h-2.07
                    A7.97 7.97 0 0013 5.07V3h-2v2.07
                    A7.97 7.97 0 005.07 11H3v2h2.07
                    A7.97 7.97 0 0011 18.93V21h2
                    v-2.07A7.97 7.97 0 0018.93 13H21v-2z"/>
                </svg>
            `;

            container.title = "Lokasi Saya";

            L.DomEvent.disableClickPropagation(container);

            container.onclick = function() {
                container.classList.add("active");

                navigator.geolocation.getCurrentPosition(
                    function(pos) {
                        var lat = pos.coords.latitude;
                        var lng = pos.coords.longitude;
                        var acc = pos.coords.accuracy;

                        if (markerGPS)
                            map.removeLayer(markerGPS);

                        if (circleGPS)
                            map.removeLayer(circleGPS);

                        var blueIcon = L.divIcon({
                            className: "",
                            html: `
                                <div style="
                                    width:18px;
                                    height:18px;
                                    border-radius:50%;
                                    background:#1a73e8;
                                    border:3px solid #fff;
                                    box-shadow:0 0 10px rgba(0,0,0,.5);
                                "></div>
                            `,
                            iconSize: [18, 18]
                        });

                        markerGPS = L.marker(
                            [lat, lng],
                            {
                                icon: blueIcon
                            }
                        ).addTo(map);

                        circleGPS = L.circle(
                            [lat, lng],
                            {
                                radius: acc,
                                color: "#1a73e8",
                                weight: 1,
                                fillColor: "#4da3ff",
                                fillOpacity: .25
                            }
                        ).addTo(map);

                        map.flyTo(
                            [lat, lng],
                            19,
                            {
                                duration: 1.5
                            }
                        );
                    },
                    function(err) {
                        container.classList.remove("active");
                        alert("GPS tidak dapat digunakan.");
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 15000,
                        maximumAge: 0
                    }
                );
            };

            return container;
        }
    });

    /* =========================================================
       FITUR UKUR LUAS
    ========================================================= */
    var drawnItems = new L.FeatureGroup();
    map.addLayer(drawnItems);

    var drawControl = new L.Control.Draw({
        position: 'topleft',
        draw: {
            polygon: {
                allowIntersection: false,
                showArea: true,
                shapeOptions: {
                    color: '#ff0000'
                }
            },
            rectangle: false,
            circle: false,
            marker: false,
            polyline: false,
            circlemarker: false
        },
        edit: {
            featureGroup: drawnItems
        }
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
       BASEMAP GOOGLE SATELLITE
    ========================================================= */
    var googleSat = L.tileLayer(
        'https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}',
        {
            maxZoom: 21,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        }
    );

    /* =========================================================
       GOOGLE LABEL
    ========================================================= */
    var googleLabels = L.tileLayer(
        'https://{s}.google.com/vt/lyrs=h&x={x}&y={y}&z={z}',
        {
            maxZoom: 21,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        }
    );

    /* =========================================================
       GOOGLE HYBRID
    ========================================================= */
    var googleHybrid = L.layerGroup([
        googleSat,
        googleLabels
    ]).addTo(map);

    /* =========================================================
       POLYGON KELOMPOK
    ========================================================= */
    @php
        $polyK = is_array($kelompok->polygon_kelompok ?? null) 
            ? json_encode($kelompok->polygon_kelompok) 
            : (!empty($kelompok->polygon_kelompok) ? $kelompok->polygon_kelompok : 'null');
    @endphp
    var wilayah = <?php echo !empty($polyK) ? $polyK : 'null'; ?>;
    if (typeof wilayah === 'string') {
        try {
            wilayah = JSON.parse(wilayah);
        } catch(e) {
            wilayah = null;
        }
    }

    var polygonKelompok = {
        type: "FeatureCollection",
        features: []
    };

    if (wilayah && wilayah.features) {
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
            } else if (f.geometry && f.geometry.type === "MultiPolygon") {
                polygonKelompok.features.push({
                    type: "Feature",
                    properties: {},
                    geometry: f.geometry
                });
            }
        });
    }

    /* =========================================================
       TAMPILKAN POLYGON KELOMPOK
    ========================================================= */
    var layer = L.geoJSON(
        polygonKelompok,
        {
            style: {
                color: "green",
                weight: 3,
                fillOpacity: 0.15
            }
        }
    ).addTo(map);

    if (layer.getLayers().length > 0) {
        map.fitBounds(
            layer.getBounds()
        );
    }

    /* =========================================================
       LAYER PETAK PERHUTANI
    ========================================================= */
    var layerPetak = L.layerGroup();
    var petakLoaded = false;

    /* =========================================================
       LAYER PETA KEGIATAN
    ========================================================= */
    var layerKegiatan = L.layerGroup();
    var kegiatanLoaded = false;

    /* =========================================================
       LAYER CONTROL
    ========================================================= */
    L.control.layers(
        null,
        {
            "📍 Peta Kegiatan": layerKegiatan,
            "🌳 Petak Perhutani": layerPetak
        },
        {
            collapsed: true
        }
    ).addTo(map);

    /* =========================================================
       POLYGON ANGGOTA
    ========================================================= */
    @php
        $daftarAnggota = $anggota ?? ($kelompok ? $kelompok->anggotaKelompok()->whereNotNull('polygon')->get() : collect());
    @endphp
    @if(!empty($daftarAnggota) && count($daftarAnggota) > 0)
    @foreach($daftarAnggota as $a)
    @php
        $polyAnggota = is_array($a->polygon) ? json_encode($a->polygon) : ($a->polygon ?? 'null');
    @endphp
    @if(!empty($polyAnggota) && $polyAnggota !== 'null')
    (function() {
        var anggota = <?php echo $polyAnggota; ?>;
        if (typeof anggota === 'string') {
            try {
                anggota = JSON.parse(anggota);
            } catch(e) {
                anggota = null;
            }
        }
        if (anggota) {
            L.geoJSON(
                anggota,
                {
                    style: {
                        color: "orange",
                        weight: 2,
                        fillOpacity: 0.5
                    }
                }
            ).addTo(map)
            .bindPopup(
                <?php echo json_encode($a->nama_anggota); ?>
            );
        }
    })();
    @endif
    @endforeach
    @endif

    /* =========================================================
       GPS CONTROL
    ========================================================= */
    map.addControl(
        new LocateControl()
    );

    /* =========================================================
       FULLSCREEN
    ========================================================= */
    map.on('fullscreenchange', function() {
        setTimeout(function() {
            map.invalidateSize();
        }, 200);
    });

    /* =========================================================
       LABEL PETAK PERHUTANI
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

        document.documentElement.style.setProperty(
            '--ukuran-toponimi',
            ukuran + 'px'
        );
    }

    map.on('zoomend', function() {
        updateLabelPetak();
        updateUkuranLabel();
    });
    window.addEventListener('resize', updateUkuranLabel);

    /* ======================================================
       PETAK PERHUTANI DARI DATABASE
    ====================================================== */
    function loadPetakPerhutani() {
        if (petakLoaded) return;

        fetch("{{ route('kelompok.get-petak') }}")
            .then(res => res.json())
            .then(function(data) {
                console.log("Jumlah petak :", data.length);

                data.forEach(function(p) {
                    var petak = {
                        type: "Feature",
                        properties: {},
                        geometry: JSON.parse(p.geometry)
                    };

                    /* ==================================
                       KOREKSI POSISI PETAK
                    ================================== */
                    var geserKiri = 27;
                    var geserBawah = 7;

                    if (geserKiri !== 0) {
                        petak = turf.transformTranslate(
                            petak,
                            geserKiri,
                            270,
                            { units: 'meters' }
                        );
                    }

                    if (geserBawah !== 0) {
                        petak = turf.transformTranslate(
                            petak,
                            geserBawah,
                            180,
                            { units: 'meters' }
                        );
                    }

                    /* ==================================
                       CEK INTERSECT KELOMPOK
                    ================================== */
                    var kena = false;

                    if (polygonKelompok && polygonKelompok.features && polygonKelompok.features.length > 0) {
                        polygonKelompok.features.forEach(function(pg) {
                            if (turf.booleanIntersects(pg, petak)) {
                                kena = true;
                            }
                        });
                    } else {
                        kena = true;
                    }

                    console.log(p.kode_toponimi, kena);

                    if (!kena) {
                        return;
                    }

                    /* ==================================
                       TAMPILKAN PETAK
                    ================================== */
                    var geo = L.geoJSON(
                        petak,
                        {
                            style: {
                                color: "#FFD600",
                                weight: 2,
                                opacity: 1,
                                fillOpacity: 0
                            },
                            onEachFeature: function(feature, layer) {
                                layer.bindPopup(
                                    "<b>TOPONIMI :</b> " + (p.kode_toponimi || '-') +
                                    "<br><b>Petak :</b> " + (p.petak || '-') +
                                    "<br><b>Anak Petak :</b> " + (p.anak_petak || '-') +
                                    "<br><b>RPH :</b> " + (p.rph || '-') +
                                    "<br><b>BKPH :</b> " + (p.bkph || '-') +
                                    "<br><b>KPH :</b> " + (p.kph || '-')
                                );

                                semuaTooltip.push({
                                    layer: layer,
                                    tooltip: L.tooltip({
                                        permanent: true,
                                        direction: "center",
                                        className: "label-petak"
                                    }).setContent(
                                        String(p.kode_toponimi ?? '')
                                    ),
                                    tampil: false
                                });
                            }
                        }
                    );

                    geo.addTo(layerPetak);
                });

                petakLoaded = true;
                updateLabelPetak();
                updateUkuranLabel();
            })
            .catch(function(error) {
                console.error("Gagal mengambil Petak Perhutani:", error);
                alert("Data Petak Perhutani gagal dimuat.");
            });
    }

    map.on('overlayadd', function(e) {
        if (e.layer === layerPetak || e.name === "🌳 Petak Perhutani") {
            loadPetakPerhutani();
        }
    });

    /* ======================================================
       PETA KEGIATAN DARI KML
    ====================================================== */
    function loadKegiatanKml() {
        if (kegiatanLoaded) return;

        @if(!empty($kegiatanList) && count($kegiatanList) > 0)
        @foreach($kegiatanList as $kp)
        @php
            $idKegiatan = intval($kp->id);
            $namaKegiatan = $kp->nama_kegiatan;
            $tanggal = !empty($kp->tanggal) ? date('d-m-Y', strtotime($kp->tanggal)) : '-';
            $lokasi = $kp->lokasi ?? '';
            $keterangan = $kp->keterangan ?? '';
            $fileKml = $kp->kml_file ?? '';
        @endphp

        (function() {
            var idKegiatan = <?php echo $idKegiatan; ?>;
            var namaKegiatan = <?php echo json_encode($namaKegiatan); ?>;
            var tanggal = <?php echo json_encode($tanggal); ?>;
            var lokasi = <?php echo json_encode($lokasi); ?>;
            var keterangan = <?php echo json_encode($keterangan); ?>;
            var fileKml = <?php echo json_encode($fileKml); ?>;

            if (!fileKml) {
                return;
            }

            /* ==========================================
               URL KML (Coba storage lalu fallback ke uploads)
            ========================================== */
            var urlKmlStorage = "{{ asset('storage/kegiatan') }}/" + encodeURIComponent(fileKml);
            var urlKmlUploads = "{{ asset('uploads/kml_kegiatan') }}/" + encodeURIComponent(fileKml);

            console.log("Memuat KML:", namaKegiatan, urlKmlStorage);

            /* ==========================================
               FETCH KML
            ========================================== */
            fetch(urlKmlStorage)
                .then(function(res) {
                    if (!res.ok) {
                        return fetch(urlKmlUploads);
                    }
                    return res;
                })
                .then(function(response) {
                    if (!response.ok) {
                        throw new Error("HTTP " + response.status + " - KML tidak ditemukan");
                    }
                    return response.text();
                })
                .then(function(kmlText) {
                    /* ======================================
                       PARSE XML
                    ====================================== */
                    var parser = new DOMParser();
                    var kml = parser.parseFromString(kmlText, "text/xml");

                    /* ======================================
                       CEK ERROR XML
                    ====================================== */
                    var parserError = kml.getElementsByTagName("parsererror");
                    if (parserError.length > 0) {
                        throw new Error("Format KML tidak valid");
                    }

                    /* ======================================
                       KML → GEOJSON
                    ====================================== */
                    var geojson = toGeoJSON.kml(kml);

                    /* ======================================
                       LAYER KML
                    ====================================== */
                    var kegiatanLayer = L.geoJSON(
                        geojson,
                        {
                            style: function() {
                                return {
                                    color: "#1565c0",
                                    weight: 3,
                                    opacity: 1,
                                    fillColor: "#4da3ff",
                                    fillOpacity: 0.25
                                };
                            },
                            pointToLayer: function(feature, latlng) {
                                return L.circleMarker(
                                    latlng,
                                    {
                                        radius: 6,
                                        color: "#1565c0",
                                        weight: 2,
                                        fillColor: "#4da3ff",
                                        fillOpacity: 0.9
                                    }
                                );
                            },
                            onEachFeature: function(feature, layer) {
                                /* ==================================
                                   POPUP KEGIATAN
                                ================================== */
                                var detailUrl = "{{ url('/kelompok/kegiatan') }}/" + idKegiatan;
                                var popupHTML =
                                    "<div style='min-width:220px;'>" +
                                    "<b style='font-size:16px;color:#2e7d32;'>KEGIATAN</b>" +
                                    "<hr>" +
                                    "<b>Nama:</b><br>" + namaKegiatan +
                                    "<br><br>" +
                                    "<b>Tanggal:</b><br>" + tanggal +
                                    "<br><br>" +
                                    "<b>Lokasi:</b><br>" + (lokasi !== "" ? lokasi : "-") +
                                    "<br><br>" +
                                    "<b>Keterangan:</b><br>" + (keterangan !== "" ? keterangan : "-") +
                                    "<br><br>" +
                                    "<a href='" + detailUrl + "' " +
                                    "style='" +
                                    "display:inline-block;" +
                                    "background:#2e7d32;" +
                                    "color:white;" +
                                    "padding:9px 14px;" +
                                    "border-radius:6px;" +
                                    "text-decoration:none;" +
                                    "font-weight:bold;" +
                                    "'>" +
                                    "📋 Buka Detail Kegiatan" +
                                    "</a>" +
                                    "</div>";

                                layer.bindPopup(popupHTML);
                            }
                        }
                    );

                    kegiatanLayer.addTo(layerKegiatan);
                })
                .catch(function(err) {
                    console.warn("Gagal memuat KML:", namaKegiatan, err);
                });
        })();
        @endforeach
        @endif

        kegiatanLoaded = true;
    }

    map.on('overlayadd', function(e) {
        if (e.layer === layerKegiatan || e.name === "📍 Peta Kegiatan") {
            loadKegiatanKml();
        }
    });

    if (map.hasLayer(layerPetak)) {
        loadPetakPerhutani();
    }
    if (map.hasLayer(layerKegiatan)) {
        loadKegiatanKml();
    }

    setTimeout(function() { map.invalidateSize(); }, 250);
    setTimeout(function() { map.invalidateSize(); }, 600);
})();
</script>
@endpush