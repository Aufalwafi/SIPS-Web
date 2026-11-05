/**
 * Map Tools Helper - SI PS ONLINE
 * Menambahkan fitur GIS terpadu untuk semua peta:
 * 1. Fullscreen Control
 * 2. Tombol Lokasi Berdiri (GPS Geolocation)
 * 3. Alat Mengukur Luasan Poligon (Turf.js / Geodesic)
 * 4. Layer Petak Perhutani (On-demand Spatial)
 */
(function(window) {
    'use strict';

    function initMapTools(map, options) {
        if (!map) return;
        try {
            options = options || {};

            var opt = {
                kelompokId: options.kelompokId || null,
                enablePetak: options.enablePetak !== false,
                enableFullscreen: options.enableFullscreen !== false,
                enableLocate: options.enableLocate !== false,
                enableMeasure: options.enableMeasure !== false,
                layersControl: options.layersControl || null,
                petakUrl: options.petakUrl || null,
                bboxUrl: options.bboxUrl || '/api/petak-perhutani/bbox',
                position: options.position || 'topleft'
            };

        /* =========================================================
           1. FULLSCREEN CONTROL
        ========================================================= */
        if (opt.enableFullscreen) {
            var container = map.getContainer ? map.getContainer() : null;
            var hasFsControl = !!(map.fullscreenControl || (map.options && map.options.fullscreenControl) || (container && container.querySelector('.leaflet-control-fullscreen')));

            if (!hasFsControl && typeof L.Control.FullScreen !== 'undefined') {
                var fsControl = new L.Control.FullScreen({
                    position: opt.position,
                    title: 'Perbesar Layar Penuh (Fullscreen)',
                    titleCancel: 'Keluar Layar Penuh'
                });
                map.addControl(fsControl);
            } else if (!hasFsControl && container && !container.querySelector('.leaflet-control-fullscreen')) {
                // Fallback Native Fullscreen Button
                var NativeFsControl = L.Control.extend({
                    options: { position: opt.position },
                    onAdd: function() {
                        var container = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
                        var btn = L.DomUtil.create('a', 'leaflet-control-locate-btn', container);
                        btn.innerHTML = '<i class="fas fa-expand"></i>';
                        btn.href = '#';
                        btn.title = 'Layar Penuh (Fullscreen)';
                        L.DomEvent.disableClickPropagation(btn);
                        L.DomEvent.on(btn, 'click', function(e) {
                            L.DomEvent.preventDefault(e);
                            var elem = map.getContainer();
                            if (!document.fullscreenElement) {
                                if (elem.requestFullscreen) elem.requestFullscreen();
                                else if (elem.webkitRequestFullscreen) elem.webkitRequestFullscreen();
                                btn.innerHTML = '<i class="fas fa-compress"></i>';
                            } else {
                                if (document.exitFullscreen) document.exitFullscreen();
                                else if (document.webkitExitFullscreen) document.webkitExitFullscreen();
                                btn.innerHTML = '<i class="fas fa-expand"></i>';
                            }
                        });
                        return container;
                    }
                });
                map.addControl(new NativeFsControl());
            }

            map.on('fullscreenchange', function() {
                setTimeout(function() { map.invalidateSize(); }, 250);
            });
        }

        /* =========================================================
           2. TOMBOL LOKASI BERDIRI (GPS LOCATE ME)
        ========================================================= */
        if (opt.enableLocate) {
            var userGpsMarker = null;
            var userGpsCircle = null;

            var LocateControl = L.Control.extend({
                options: { position: opt.position },
                onAdd: function() {
                    var container = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
                    var btn = L.DomUtil.create('a', 'leaflet-control-locate-btn', container);
                    btn.innerHTML = '<i class="fas fa-crosshairs"></i>';
                    btn.href = '#';
                    btn.title = 'Cari Lokasi Berdiri Saya (GPS)';

                    L.DomEvent.disableClickPropagation(btn);
                    L.DomEvent.on(btn, 'click', function(e) {
                        L.DomEvent.preventDefault(e);

                        if (!navigator.geolocation) {
                            alert('Browser / perangkat Anda tidak mendukung fitur lokasi GPS.');
                            return;
                        }

                        btn.classList.add('searching');
                        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                        navigator.geolocation.getCurrentPosition(
                            function(pos) {
                                btn.classList.remove('searching');
                                btn.classList.add('active');
                                btn.innerHTML = '<i class="fas fa-crosshairs"></i>';

                                var lat = pos.coords.latitude;
                                var lng = pos.coords.longitude;
                                var acc = pos.coords.accuracy;

                                if (userGpsMarker) map.removeLayer(userGpsMarker);
                                if (userGpsCircle) map.removeLayer(userGpsCircle);

                                var userIcon = L.divIcon({
                                    className: '',
                                    html: '<div class="user-gps-dot"></div>',
                                    iconSize: [16, 16],
                                    iconAnchor: [8, 8]
                                });

                                userGpsMarker = L.marker([lat, lng], {
                                    icon: userIcon,
                                    zIndexOffset: 1500
                                }).addTo(map);

                                userGpsCircle = L.circle([lat, lng], {
                                    radius: acc,
                                    color: '#2563eb',
                                    weight: 1.5,
                                    fillColor: '#3b82f6',
                                    fillOpacity: 0.15
                                }).addTo(map);

                                userGpsMarker.bindPopup(
                                    '<div style="font-size:12px;min-width:180px">' +
                                    '<b style="color:#1d4ed8;font-size:13px"><i class="fas fa-location-dot"></i> Posisi Anda Berdiri</b><br>' +
                                    '<span style="color:#64748b">Koordinat:</span> ' + lat.toFixed(6) + ', ' + lng.toFixed(6) + '<br>' +
                                    '<span style="color:#64748b">Akurasi GPS:</span> ±' + Math.round(acc) + ' meter' +
                                    '</div>'
                                ).openPopup();

                                map.flyTo([lat, lng], 17, { duration: 1.5 });
                            },
                            function(err) {
                                btn.classList.remove('searching');
                                btn.classList.remove('active');
                                btn.innerHTML = '<i class="fas fa-crosshairs"></i>';

                                var msg = 'Gagal mendeteksi lokasi GPS.';
                                if (err.code === 1) msg = 'Izin akses lokasi ditolak oleh browser/perangkat.';
                                else if (err.code === 2) msg = 'Posisi tidak dapat ditemukan (sinyal GPS lemah).';
                                else if (err.code === 3) msg = 'Waktu permintaan lokasi habis (timeout).';
                                alert(msg);
                            },
                            {
                                enableHighAccuracy: true,
                                timeout: 15000,
                                maximumAge: 0
                            }
                        );
                    });

                    return container;
                }
            });

            map.addControl(new LocateControl());
        }

        /* =========================================================
           3. FITUR MEMBUAT POLIGON UNTUK MENGUKUR LUASAN
        ========================================================= */
        if (opt.enableMeasure) {
            var drawnMeasureItems = new L.FeatureGroup();
            map.addLayer(drawnMeasureItems);

            // Cek apakah Leaflet.draw tersedia
            if (typeof L.Control.Draw !== 'undefined') {
                var drawControl = new L.Control.Draw({
                    position: opt.position,
                    draw: {
                        polygon: {
                            allowIntersection: false,
                            showArea: true,
                            shapeOptions: {
                                color: '#dc2626',
                                weight: 2.5,
                                fillColor: '#ef4444',
                                fillOpacity: 0.25,
                                dashArray: '4, 4'
                            }
                        },
                        rectangle: {
                            shapeOptions: {
                                color: '#dc2626',
                                weight: 2.5,
                                fillColor: '#ef4444',
                                fillOpacity: 0.25
                            }
                        },
                        polyline: false,
                        circle: false,
                        marker: false,
                        circlemarker: false
                    },
                    edit: {
                        featureGroup: drawnMeasureItems,
                        remove: true
                    }
                });
                map.addControl(drawControl);

                map.on(L.Draw.Event.CREATED, function(e) {
                    var layer = e.layer;
                    drawnMeasureItems.addLayer(layer);
                    tampilkanHasilUkur(layer);
                });
            } else {
                // Standalone Polygon Measure Tool (tanpa ketergantungan plugin Leaflet.Draw)
                var MeasureControl = L.Control.extend({
                    options: { position: opt.position },
                    onAdd: function() {
                        var container = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
                        var btn = L.DomUtil.create('a', 'leaflet-control-measure-btn', container);
                        btn.innerHTML = '<i class="fas fa-draw-polygon"></i>';
                        btn.href = '#';
                        btn.title = 'Ukur Luasan Poligon (Klik untuk mulai/selesai)';

                        var isMeasuring = false;
                        var currentPoints = [];
                        var tempPolyline = null;
                        var tempPolygon = null;

                        L.DomEvent.disableClickPropagation(btn);
                        L.DomEvent.on(btn, 'click', function(e) {
                            L.DomEvent.preventDefault(e);
                            isMeasuring = !isMeasuring;
                            if (isMeasuring) {
                                btn.classList.add('active');
                                map.getContainer().style.cursor = 'crosshair';
                                alert('Mode Ukur Luas Aktif:\n1. Klik pada peta untuk membuat titik poligon.\n2. Klik titik awal atau klik 2x untuk menyelesaikan pengukuran.');
                            } else {
                                resetMeasure();
                            }
                        });

                        function resetMeasure() {
                            isMeasuring = false;
                            btn.classList.remove('active');
                            map.getContainer().style.cursor = '';
                            currentPoints = [];
                            if (tempPolyline) { map.removeLayer(tempPolyline); tempPolyline = null; }
                            if (tempPolygon) { map.removeLayer(tempPolygon); tempPolygon = null; }
                        }

                        map.on('click', function(e) {
                            if (!isMeasuring) return;
                            currentPoints.push([e.latlng.lat, e.latlng.lng]);

                            if (currentPoints.length >= 2) {
                                if (tempPolyline) map.removeLayer(tempPolyline);
                                if (tempPolygon) map.removeLayer(tempPolygon);

                                tempPolygon = L.polygon(currentPoints, {
                                    color: '#dc2626',
                                    weight: 2,
                                    fillColor: '#ef4444',
                                    fillOpacity: 0.2,
                                    dashArray: '3, 4'
                                }).addTo(map);
                            }
                        });

                        map.on('dblclick', function(e) {
                            if (!isMeasuring) return;
                            L.DomEvent.preventDefault(e);
                            if (currentPoints.length >= 3) {
                                var finalPoly = L.polygon(currentPoints, {
                                    color: '#dc2626',
                                    weight: 2.5,
                                    fillColor: '#ef4444',
                                    fillOpacity: 0.25
                                }).addTo(drawnMeasureItems);
                                tampilkanHasilUkur(finalPoly);
                            }
                            resetMeasure();
                        });

                        return container;
                    }
                });
                map.addControl(new MeasureControl());
            }

            function tampilkanHasilUkur(layer) {
                var luasM2 = 0;
                var kelilingM = 0;

                // Hitung pakai Turf.js jika ada
                if (typeof turf !== 'undefined' && layer.toGeoJSON) {
                    try {
                        var gj = layer.toGeoJSON();
                        luasM2 = turf.area(gj);
                        kelilingM = turf.length(gj, { units: 'kilometers' }) * 1000;
                    } catch(err) {}
                }

                // Fallback Spherical Geodesic jika Turf belum dimuat
                if (luasM2 === 0 && layer.getLatLngs) {
                    var lls = layer.getLatLngs();
                    var pts = Array.isArray(lls[0]) ? lls[0] : lls;
                    luasM2 = hitungLuasSpherical(pts);
                    kelilingM = hitungKelilingSpherical(pts);
                }

                var hektar = luasM2 / 10000;
                var formatHa = hektar.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' Ha';
                var formatM2 = Math.round(luasM2).toLocaleString('id-ID') + ' m²';
                var formatKeliling = Math.round(kelilingM).toLocaleString('id-ID') + ' meter';

                var popupHtml =
                    '<div class="measure-popup-box">' +
                    '<div class="measure-popup-title"><i class="fas fa-ruler-combined"></i> Hasil Pengukuran Luas</div>' +
                    '<div class="measure-stat-item"><span class="measure-stat-label">Luas Area:</span> <span class="measure-stat-val" style="color:#166534;font-size:13px">' + formatHa + '</span></div>' +
                    '<div class="measure-stat-item"><span class="measure-stat-label">Meter Persegi:</span> <span class="measure-stat-val">' + formatM2 + '</span></div>' +
                    '<div class="measure-stat-item"><span class="measure-stat-label">Keliling:</span> <span class="measure-stat-val">' + formatKeliling + '</span></div>' +
                    '<button type="button" class="measure-del-btn" onclick="hapusLayerUkur(this)">Hapus Pengukuran</button>' +
                    '</div>';

                layer.bindPopup(popupHtml).openPopup();
                layer._measureContainer = drawnMeasureItems;
            }

            window.hapusLayerUkur = function(btn) {
                var el = btn.closest('.leaflet-popup');
                if (el) {
                    map.closePopup();
                }
                drawnMeasureItems.clearLayers();
            };

            function hitungLuasSpherical(latLngs) {
                var R = 6378137;
                var area = 0;
                var len = latLngs.length;
                if (len < 3) return 0;
                for (var i = 0; i < len; i++) {
                    var p1 = latLngs[i];
                    var p2 = latLngs[(i + 1) % len];
                    area += (p2.lng - p1.lng) * (Math.PI / 180) * (2 + Math.sin(p1.lat * (Math.PI / 180)) + Math.sin(p2.lat * (Math.PI / 180)));
                }
                return Math.abs(area * R * R / 2);
            }

            function hitungKelilingSpherical(latLngs) {
                var len = latLngs.length;
                var total = 0;
                if (len < 2) return 0;
                for (var i = 0; i < len; i++) {
                    var p1 = latLngs[i];
                    var p2 = latLngs[(i + 1) % len];
                    total += p1.distanceTo(p2);
                }
                return total;
            }
        }

        /* =========================================================
           4. LAYER PETAK PERHUTANI
        ========================================================= */
        if (opt.enablePetak) {
            var layerPetak = L.featureGroup();
            var petakLoaded = false;
            var petakLoading = false;
            var loadedPetakIds = {};

            // Masukkan ke layers control jika ada
            if (opt.layersControl && typeof opt.layersControl.addOverlay === 'function') {
                opt.layersControl.addOverlay(layerPetak, '🌳 Petak Perhutani');
            } else {
                var container = map.getContainer ? map.getContainer() : null;
                if (container && !container.querySelector('.leaflet-control-layers')) {
                    L.control.layers(null, { '🌳 Petak Perhutani': layerPetak }, { position: 'topright' }).addTo(map);
                }
            }

            // Dengarkan event saat layer Petak diaktifkan
            map.on('overlayadd', function(e) {
                if (e.name === '🌳 Petak Perhutani' || e.layer === layerPetak) {
                    loadPetakData();
                }
            });

            // Jika user geser/zoom peta saat petak aktif, reload BBOX
            map.on('moveend', function() {
                if (map.hasLayer(layerPetak) && !opt.kelompokId) {
                    loadPetakBbox();
                }
            });

            function showToast(text, duration) {
                duration = duration || 3000;
                var toast = document.querySelector('.petak-loading-toast');
                if (!toast) {
                    toast = document.createElement('div');
                    toast.className = 'petak-loading-toast';
                    document.body.appendChild(toast);
                }
                toast.innerHTML = text;
                toast.style.display = 'flex';
                toast.style.opacity = '1';
                setTimeout(function() {
                    toast.style.opacity = '0';
                    setTimeout(function() { toast.style.display = 'none'; }, 300);
                }, duration);
            }

            function loadPetakData() {
                if (opt.kelompokId) {
                    loadPetakByKelompok(opt.kelompokId);
                } else {
                    loadPetakBbox();
                }
            }

            function loadPetakByKelompok(kid) {
                if (petakLoaded || petakLoading) return;
                petakLoading = true;
                showToast('<i class="fas fa-spinner fa-spin"></i> Memuat Petak Perhutani...', 4000);

                var targetUrl = opt.petakUrl || ('/kelompok/' + kid + '/petak-perhutani');
                fetch(targetUrl)
                    .then(function(res) {
                        if (!res.ok) throw new Error('HTTP ' + res.status);
                        return res.json();
                    })
                    .then(function(data) {
                        petakLoaded = true;
                        petakLoading = false;
                        renderPetakFeatures(data);
                        showToast('<i class="fas fa-check-circle" style="color:#4ade80"></i> ' + (data.length || 0) + ' Petak Perhutani ditampilkan');
                    })
                    .catch(function(err) {
                        petakLoading = false;
                        showToast('<i class="fas fa-exclamation-triangle" style="color:#f87171"></i> Gagal memuat petak perhutani');
                    });
            }

            function loadPetakBbox() {
                var zoom = map.getZoom();
                if (zoom < 12) {
                    showToast('<i class="fas fa-search-plus"></i> Perbesar peta (zoom in) untuk memuat garis petak perhutani', 2500);
                    return;
                }

                var bounds = map.getBounds();
                var bbox = [
                    bounds.getWest().toFixed(6),
                    bounds.getSouth().toFixed(6),
                    bounds.getEast().toFixed(6),
                    bounds.getNorth().toFixed(6)
                ].join(',');

                if (petakLoading) return;
                petakLoading = true;

                fetch(opt.bboxUrl + '?bbox=' + bbox)
                    .then(function(res) { return res.json(); })
                    .then(function(res) {
                        petakLoading = false;
                        if (res.status === 'too_large') {
                            showToast('<i class="fas fa-search-plus"></i> ' + res.message, 2500);
                            return;
                        }
                        if (res.data && res.data.length > 0) {
                            renderPetakFeatures(res.data);
                        }
                    })
                    .catch(function() {
                        petakLoading = false;
                    });
            }

            function renderPetakFeatures(petakList) {
                if (!Array.isArray(petakList)) return;

                petakList.forEach(function(p) {
                    if (!p.geometry || loadedPetakIds[p.id]) return;
                    loadedPetakIds[p.id] = true;

                    try {
                        var geom = typeof p.geometry === 'string' ? JSON.parse(p.geometry) : p.geometry;
                        var feature = {
                            type: 'Feature',
                            properties: p,
                            geometry: geom
                        };

                        // Koreksi datum bila ada pergeseran shapefile Perhutani
                        if (typeof turf !== 'undefined') {
                            feature = turf.transformTranslate(feature, 27, 270, { units: 'meters' });
                            feature = turf.transformTranslate(feature, 7, 180, { units: 'meters' });
                        }

                        var poly = L.geoJSON(feature, {
                            style: {
                                color: '#d97706',
                                weight: 1.8,
                                opacity: 0.9,
                                fillColor: '#f59e0b',
                                fillOpacity: 0.22,
                                dashArray: '4, 3'
                            }
                        }).addTo(layerPetak);

                        var luas = p.luas_baku ? (parseFloat(p.luas_baku).toFixed(2) + ' Ha') : '-';
                        var popupHtml =
                            '<div class="petak-popup">' +
                            '<div class="petak-popup-header"><i class="fas fa-tree"></i> Petak Perhutani: ' + (p.petak || '-') + (p.anak_petak ? (' / ' + p.anak_petak) : '') + '</div>' +
                            '<div class="petak-popup-row"><span class="petak-popup-label">Kode Toponimi:</span><span class="petak-popup-value">' + (p.kode_toponimi || '-') + '</span></div>' +
                            '<div class="petak-popup-row"><span class="petak-popup-label">KPH / BKPH:</span><span class="petak-popup-value">' + (p.kph || '-') + ' / ' + (p.bkph || '-') + '</span></div>' +
                            '<div class="petak-popup-row"><span class="petak-popup-label">RPH:</span><span class="petak-popup-value">' + (p.rph || '-') + '</span></div>' +
                            '<div class="petak-popup-row"><span class="petak-popup-label">Desa / Kec:</span><span class="petak-popup-value">' + (p.desa || '-') + ', ' + (p.kecamatan || '-') + '</span></div>' +
                            '<div class="petak-popup-row"><span class="petak-popup-label">Fungsi Hutan:</span><span class="petak-popup-value">' + (p.fungsi || '-') + '</span></div>' +
                            '<div class="petak-popup-row"><span class="petak-popup-label">Klas Hutan:</span><span class="petak-popup-value">' + (p.klas_hutan || '-') + '</span></div>' +
                            '<div class="petak-popup-row"><span class="petak-popup-label">Luas Baku:</span><span class="petak-popup-value" style="color:#b45309">' + luas + '</span></div>' +
                            '<div class="petak-popup-row"><span class="petak-popup-label">Jenis Tanaman:</span><span class="petak-popup-value">' + (p.jenis_tanaman || '-') + (p.tahun_tanam ? (' (' + p.tahun_tanam + ')') : '') + '</span></div>' +
                            '</div>';

                        poly.bindPopup(popupHtml);
                    } catch(e) {}
                });
            }
        }

        return {
            map: map,
            layerPetak: layerPetak || null,
            loadPetakByKelompok: loadPetakByKelompok,
            loadPetakBbox: loadPetakBbox
        };
        } catch(err) {
            console.error('Error in initMapTools:', err);
            return { map: map, layerPetak: null };
        }
    }

    window.initMapTools = initMapTools;
})(window);
