@extends('layouts.app')
@section('title', 'Detail Kegiatan - SI PS ONLINE')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.fullscreen@3.0.2/Control.FullScreen.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css">
<link rel="stylesheet" href="{{ asset('css/map-tools.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
.detail-card { background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 22px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); margin-bottom: 20px; }
.detail-card h2, .detail-card h3 { color: #1b5e20; margin-top: 0; font-weight: 700; display: flex; align-items: center; gap: 8px; }

.info-table { width: 100%; border-collapse: collapse; font-size: 13px; margin-bottom: 15px; }
.info-table th { width: 180px; background: #f8fafc; color: #475569; text-align: left; padding: 10px 14px; border-bottom: 1px solid #f1f5f9; font-weight: 600; }
.info-table td { padding: 10px 14px; border-bottom: 1px solid #f1f5f9; color: #1e293b; vertical-align: top; }

/* UPLOAD & GEOTAG PANEL */
.upload-geotag-box {
    background: #f8fafc;
    border: 2px dashed #cbd5e1;
    border-radius: 8px;
    padding: 20px;
    margin-top: 15px;
}
.upload-geotag-box h4 { margin: 0 0 14px; color: #1b5e20; font-size: 14px; font-weight: 700; display: flex; align-items: center; gap: 6px; }

.photo-source-buttons {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 16px;
}
.btn-source-camera {
    background: #16a34a;
    color: #ffffff;
    border: 1px solid #15803d;
    padding: 10px 18px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 2px 4px rgba(22, 163, 74, 0.2);
    transition: all 0.15s ease;
}
.btn-source-camera:hover { background: #15803d; transform: translateY(-1px); }

.btn-source-gallery {
    background: #0284c7;
    color: #ffffff;
    border: 1px solid #0369a1;
    padding: 10px 18px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 2px 4px rgba(2, 132, 199, 0.2);
    transition: all 0.15s ease;
}
.btn-source-gallery:hover { background: #0369a1; transform: translateY(-1px); }

.geo-form-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-bottom: 14px;
}
.geo-form-group { display: flex; flex-direction: column; gap: 4px; }
.geo-form-group label { font-size: 11.5px; font-weight: 600; color: #475569; }
.geo-form-group input, .geo-form-group textarea {
    padding: 7px 10px;
    font-size: 12.5px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    background: #ffffff;
    outline: none;
}
.geo-form-group input:focus, .geo-form-group textarea:focus { border-color: #2e7d32; box-shadow: 0 0 0 2px rgba(46,125,50,0.15); }

.btn-gps {
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: 0.15s;
}
.btn-gps:hover { background: #0284c7; color: #fff; }

.gps-status {
    font-size: 11.5px;
    color: #64748b;
    margin-top: 4px;
    display: flex;
    align-items: center;
    gap: 5px;
}
.gps-status.active { color: #15803d; font-weight: 600; }

.preview-container {
    margin-top: 18px;
    display: none;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
}
.preview-canvas {
    max-width: 100%;
    max-height: 520px;
    height: auto;
    object-fit: contain;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    border: 1px solid #cbd5e1;
}

/* LIVE CAMERA MODAL (WORKS DIRECTLY ON LAPTOP & HP) */
.camera-live-modal {
    display: none;
    position: fixed;
    z-index: 10000;
    top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.92);
    justify-content: center;
    align-items: center;
    flex-direction: column;
    padding: 15px;
}
.camera-live-modal.show { display: flex; }

.camera-viewport-wrap {
    position: relative;
    max-width: 720px;
    width: 100%;
    max-height: 75vh;
    border-radius: 12px;
    overflow: hidden;
    background: #000;
    box-shadow: 0 10px 30px rgba(0,0,0,0.8);
}
.camera-viewport-wrap video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.camera-overlay-crosshair {
    position: absolute;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    color: rgba(255, 255, 255, 0.4);
    font-size: 36px;
    pointer-events: none;
}
.camera-live-controls {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-top: 18px;
}
.btn-shutter-capture {
    width: 65px;
    height: 65px;
    border-radius: 50%;
    background: #22c55e;
    color: #fff;
    border: 4px solid #ffffff;
    font-size: 24px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 0 15px rgba(34, 197, 94, 0.6);
    transition: transform 0.1s;
}
.btn-shutter-capture:active { transform: scale(0.92); background: #16a34a; }
.btn-cam-control {
    background: rgba(255, 255, 255, 0.2);
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.3);
    padding: 8px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.btn-cam-control:hover { background: rgba(255, 255, 255, 0.35); }

/* GALLERY GRID */
.doc-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 16px;
    margin-top: 15px;
}
.doc-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    display: flex;
    flex-direction: column;
    transition: transform 0.15s;
}
.doc-card:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.08); }
.doc-thumb-wrap {
    position: relative;
    width: 100%;
    height: 165px;
    background: #1e293b;
    cursor: pointer;
}
.doc-thumb-wrap img { width: 100%; height: 100%; object-fit: cover; }
.doc-badge-overlay {
    position: absolute;
    top: 8px; left: 8px;
    background: rgba(15, 23, 42, 0.8);
    backdrop-filter: blur(2px);
    color: #4ade80;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.doc-body { padding: 10px 12px; flex: 1; display: flex; flex-direction: column; justify-content: space-between; gap: 8px; }
.doc-keterangan { font-size: 11.5px; color: #334155; line-height: 1.4; word-break: break-word; }
.doc-actions { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 8px; gap: 6px; }

/* MODAL PROPERTIES & IMAGE VIEWER */
.img-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(15, 23, 42, 0.65);
    backdrop-filter: blur(5px);
    justify-content: center;
    align-items: center;
    padding: 20px;
}
.img-modal.show { display: flex; }

.properties-modal-card {
    background: #ffffff;
    border-radius: 16px;
    max-width: 920px;
    width: 100%;
    max-height: 88vh;
    overflow: hidden;
    display: grid;
    grid-template-columns: 1.15fr 1fr;
    box-shadow: 0 20px 45px rgba(0, 0, 0, 0.2);
    position: relative;
    border: 1px solid #e2e8f0;
}

/* SISI KIRI: Frame Foto Santai & Lembut */
.prop-image-pane {
    background: #f8fafc;
    border-right: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 24px 20px;
    max-height: 88vh;
    overflow: hidden;
}
.prop-frame-box {
    background: #ffffff;
    padding: 8px;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.06);
    border: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    max-width: 100%;
}
.prop-image-pane img {
    max-width: 100%;
    max-height: 64vh;
    object-fit: contain;
    border-radius: 8px;
    display: block;
}
.prop-image-hint {
    margin-top: 12px;
    font-size: 11.5px;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* SISI KANAN: Detail Informasi Bersih & Elegan */
.prop-info-pane {
    padding: 24px 22px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    overflow-y: auto;
    background: #ffffff;
}

.prop-header-box {
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid #f1f5f9;
}
.prop-header-box h3 {
    color: #1b5e20;
    margin: 0 0 4px;
    font-size: 16.5px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 8px;
}
.prop-header-box p {
    margin: 0;
    font-size: 12px;
    color: #64748b;
}

/* LIST ITEM PROPERTY (Bukan Table agar tidak tertimpa th hijau) */
.prop-info-list {
    display: flex;
    flex-direction: column;
    gap: 9px;
    margin-bottom: 16px;
}
.prop-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 12px;
    border-radius: 8px;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    font-size: 12.5px;
    gap: 10px;
}
.prop-item-label {
    color: #64748b;
    font-weight: 500;
    min-width: 110px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    gap: 7px;
}
.prop-item-value {
    color: #1e293b;
    font-weight: 600;
    text-align: right;
    word-break: break-word;
}
.prop-badge-gps {
    background: #ecfdf5;
    color: #065f46;
    border: 1px solid #a7f3d0;
    padding: 2px 7px;
    border-radius: 5px;
    font-family: monospace;
    font-size: 11.5px;
    display: inline-block;
}
.prop-badge-alt {
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    padding: 2px 7px;
    border-radius: 5px;
    font-size: 11.5px;
    display: inline-block;
}

/* TOMBOL AKSI */
.prop-actions-box {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: 8px;
}
.btn-prop-gmaps {
    background: #1a73e8;
    color: #ffffff !important;
    text-align: center;
    font-size: 12.5px;
    font-weight: 600;
    padding: 9px 14px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    text-decoration: none;
    box-shadow: 0 2px 4px rgba(26, 115, 232, 0.25);
    transition: all 0.15s ease;
}
.btn-prop-gmaps:hover {
    background: #1557b0;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(26, 115, 232, 0.35);
}
.btn-prop-download {
    background: #ffffff;
    color: #334155;
    border: 1px solid #cbd5e1;
    text-align: center;
    font-size: 12.5px;
    font-weight: 600;
    padding: 8px 14px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    text-decoration: none;
    transition: all 0.15s ease;
}
.btn-prop-download:hover {
    background: #f1f5f9;
    color: #0f172a;
    border-color: #94a3b8;
}

.modal-close-icon {
    position: absolute;
    top: 14px;
    right: 16px;
    color: #94a3b8;
    font-size: 20px;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.15s;
    z-index: 20;
}
.modal-close-icon:hover {
    background: #f1f5f9;
    color: #0f172a;
}

@media (max-width: 768px) {
    .properties-modal-card { grid-template-columns: 1fr; max-height: 92vh; overflow-y: auto; }
    .prop-image-pane { max-height: 40vh; border-right: none; border-bottom: 1px solid #e2e8f0; }
    .photo-source-buttons { flex-direction: column; }
    .btn-source-camera, .btn-source-gallery { width: 100%; justify-content: center; }
    .geo-form-grid { grid-template-columns: 1fr; }
    .doc-grid { grid-template-columns: 1fr; }
}

/* OFFLINE STATUS & QUEUE STYLES */
.conn-status-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11.5px;
    font-weight: 700;
    padding: 4px 11px;
    border-radius: 20px;
    transition: all 0.25s ease;
}
.conn-status-pill.online {
    background: #ecfdf5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}
.conn-status-pill.offline {
    background: #fffbeb;
    color: #b45309;
    border: 1px solid #fde68a;
    animation: pulseGlow 2s infinite;
}
@keyframes pulseGlow {
    0%, 100% { box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.4); }
    50% { box-shadow: 0 0 0 4px rgba(217, 119, 6, 0.15); }
}

.offline-queue-box {
    background: #fffdf5;
    border: 1.5px solid #fbbf24;
    border-radius: 10px;
    padding: 14px 18px;
    margin-bottom: 18px;
    box-shadow: 0 2px 8px rgba(217, 119, 6, 0.1);
    animation: fadeIn 0.3s ease;
}
.offline-queue-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
}
.offline-queue-title {
    display: flex;
    align-items: center;
    gap: 12px;
}
.offline-queue-title strong {
    color: #92400e;
    font-size: 13.5px;
    display: block;
}
.btn-sync-now {
    background: #d97706;
    color: #ffffff;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    transition: all 0.15s;
    box-shadow: 0 2px 4px rgba(217, 119, 6, 0.25);
}
.btn-sync-now:hover:not(:disabled) {
    background: #b45309;
    transform: translateY(-1px);
}
.btn-sync-now:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.offline-queue-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 10px;
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px dashed #fde68a;
}
.offline-item-card {
    background: #ffffff;
    border: 1px solid #fef3c7;
    border-radius: 8px;
    padding: 8px 10px;
    display: flex;
    gap: 10px;
    align-items: center;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}
.offline-item-card img {
    width: 46px;
    height: 46px;
    object-fit: cover;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    flex-shrink: 0;
}
.offline-item-info {
    flex: 1;
    min-width: 0;
    font-size: 11px;
}
.offline-item-info strong {
    display: block;
    color: #1e293b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-size: 11.5px;
}
.offline-item-info span {
    color: #64748b;
    font-size: 10.5px;
    display: block;
}
.btn-del-offline {
    background: none;
    border: none;
    color: #ef4444;
    cursor: pointer;
    padding: 4px 6px;
    font-size: 13px;
    border-radius: 4px;
    transition: 0.15s;
}
.btn-del-offline:hover {
    background: #fee2e2;
}

/* OFFLINE TOAST NOTIFICATION */
.offline-toast {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 10000;
    background: #1e293b;
    color: #ffffff;
    padding: 12px 18px;
    border-radius: 8px;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    max-width: 400px;
    animation: slideUp 0.3s ease;
}
.offline-toast.success { background: #15803d; }
.offline-toast.warning { background: #b45309; }
.offline-toast.info { background: #0369a1; }

@keyframes fadeIn { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: translateY(0); } }
@keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endpush

@section('content')
<div class="content">
    
    @if(session('success'))<div class="alert">{!! session('success') !!}</div>@endif
    @if(session('error'))<div class="alert-error">{!! session('error') !!}</div>@endif

    {{-- KARTU DETAIL KEGIATAN --}}
    <div class="detail-card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;flex-wrap:wrap;gap:10px">
            <h2><i class="fas fa-calendar-check" style="color:#2e7d32"></i> {{ $kegiatan->nama_kegiatan ?? 'Detail Kegiatan' }}</h2>
            <div style="display:flex;gap:8px;flex-wrap:wrap">
                <a href="{{ route('kelompok.kegiatan') }}" class="btn"><i class="fas fa-arrow-left"></i> Kembali</a>
                <a href="{{ route('kelompok.kegiatan.peta', $kegiatan->id) }}" class="btn" style="background:#16a34a;color:#fff;font-weight:600"><i class="fas fa-map-marked-alt"></i> Peta Sebaran Foto</a>
                <a href="{{ route('kelompok.kegiatan.edit', $kegiatan->id) }}" class="btn btn-edit"><i class="fas fa-pen"></i> Edit Kegiatan</a>
            </div>
        </div>

        <table class="info-table no-paginate">
            <tr>
                <th>Nama Kegiatan</th>
                <td><strong>{{ $kegiatan->nama_kegiatan ?? '-' }}</strong></td>
            </tr>
            <tr>
                <th>Tanggal Pelaksanaan</th>
                <td>{{ $kegiatan->tanggal ? \Carbon\Carbon::parse($kegiatan->tanggal)->translatedFormat('d F Y') : '-' }}</td>
            </tr>
            <tr>
                <th>Lokasi</th>
                <td>{{ $kegiatan->lokasi ?? '-' }}</td>
            </tr>
            <tr>
                <th>Keterangan</th>
                <td>{!! nl2br(e($kegiatan->keterangan ?? '-')) !!}</td>
            </tr>
            <tr>
                <th>File Peta KML</th>
                <td>
                    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                        @if(!empty($kegiatan->kml_file))
                            <a href="{{ asset('storage/kegiatan/' . $kegiatan->kml_file) }}" class="btn btn-view" download>
                                <i class="fas fa-download"></i> Unduh {{ $kegiatan->kml_file }}
                            </a>
                            <a href="{{ route('kelompok.kegiatan.peta', $kegiatan->id) }}" class="btn" style="background:#0284c7;color:#fff;font-size:12px;padding:6px 12px">
                                <i class="fas fa-map-location-dot"></i> Lihat Peta & Poligon KML
                            </a>
                        @else
                            <span style="color:#94a3b8">Tidak ada file KML</span>
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- KARTU DOKUMENTASI FOTO BER-GEOTAG --}}
    <div class="detail-card">
        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;margin-bottom:12px">
            <div>
                <h3 style="margin:0"><i class="fas fa-camera" style="color:#2e7d32"></i> Foto Dokumentasi Kegiatan (Geotag GPS)</h3>
                <p style="color:#64748b;font-size:12px;margin:3px 0 0">
                    Ambil atau unggah foto kegiatan lapangan. Disertai verifikasi posisi di dalam/luar denah poligon kegiatan.
                </p>
            </div>
            <a href="{{ route('kelompok.kegiatan.peta', $kegiatan->id) }}" class="btn" style="background:#16a34a;color:#fff;font-weight:600;display:inline-flex;align-items:center;gap:6px;box-shadow:0 2px 6px rgba(22,163,74,0.25)">
                <i class="fas fa-map-marked-alt"></i> Buka Peta Sebaran Foto
            </a>
        </div>

        {{-- FORM UNGGAH / AMBIL FOTO DENGAN WATERMARK CANVAS --}}
        <div class="upload-geotag-box">
            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;margin-bottom:14px">
                <h4 style="margin:0"><i class="fas fa-camera-retro"></i> Tambah Foto Dokumentasi</h4>
                <span id="connStatusPill" class="conn-status-pill online" title="Status Jaringan Perangkat">
                    <i class="fas fa-wifi"></i> Online
                </span>
            </div>

            {{-- KARTU ANTRIAN FOTO OFFLINE DI HP (MUNCUL OTOMATIS JIKA ADA FOTO DI HP) --}}
            <div id="offlineQueueBox" class="offline-queue-box" style="display:none">
                <div class="offline-queue-header">
                    <div class="offline-queue-title">
                        <i class="fas fa-boxes-packing" style="color:#d97706;font-size:22px"></i>
                        <div>
                            <strong>Antrian Foto Offline di HP (<span id="offlineQueueCount">0</span> Foto)</strong>
                            <p style="margin:2px 0 0;font-size:11.5px;color:#78350f">
                                Foto-foto ini tersimpan aman di memori HP. Begitu HP Anda terhubung internet, foto akan otomatis diunggah ke server &amp; Google Drive, lalu otomatis dibersihkan dari memori HP.
                            </p>
                        </div>
                    </div>
                    <button type="button" class="btn-sync-now" id="btnSyncOfflineNow">
                        <i class="fas fa-cloud-arrow-up"></i> Unggah Sekarang
                    </button>
                </div>
                <div id="offlineSyncStatusText" style="margin-top:8px;font-size:12px;color:#0284c7;font-weight:600;display:none"></div>
                <div id="offlineQueueList" class="offline-queue-list"></div>
            </div>
            
            {{-- TOMBOL PILIHAN SUMBER FOTO --}}
            <div class="photo-source-buttons">
                <button type="button" class="btn-source-camera" id="btnTriggerCamera">
                    <i class="fas fa-camera"></i> Ambil Foto Geotag
                </button>
                <button type="button" class="btn-source-gallery" id="btnTriggerGallery">
                    <i class="fas fa-images"></i> Upload dari Galeri
                </button>
            </div>

            {{-- HIDDEN FILE INPUTS (FALLBACK) --}}
            <input type="file" id="inputCamera" accept="image/*" capture="environment" style="display:none">
            <input type="file" id="inputGallery" accept="image/*" style="display:none">

            <form id="geotagForm" method="POST" action="{{ route('kelompok.kegiatan.dokumentasi.simpan', $kegiatan->id) }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="foto_base64" id="fotoBase64">
                <input type="hidden" name="akurasi" id="inputAkurasi" value="17">

                <div class="geo-form-group" style="margin-bottom:12px">
                    <label>Keterangan / Catatan Foto (Opsional):</label>
                    <input type="text" name="keterangan" id="inputKeterangan" placeholder="Contoh: Penanaman bibit pohon sengon di petak 4">
                </div>

                <div class="geo-form-grid">
                    <div class="geo-form-group">
                        <label>Latitude:</label>
                        <input type="text" name="latitude" id="inputLat" placeholder="-7.xxxxxx">
                    </div>

                    <div class="geo-form-group">
                        <label>Longitude:</label>
                        <input type="text" name="longitude" id="inputLng" placeholder="111.xxxxxx">
                    </div>

                    <div class="geo-form-group">
                        <label>Altitude (Ketinggian mdpl):</label>
                        <input type="text" name="altitude" id="inputAlt" placeholder="Contoh: 450">
                    </div>
                </div>

                <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;flex-wrap:wrap;background:#f8fafc;padding:10px 14px;border-radius:8px;border:1px solid #e2e8f0">
                    <div style="display:flex;align-items:center;gap:8px">
                        <label for="watermarkSizeSelect" style="font-size:12px;font-weight:700;color:#334155;margin:0;display:inline-flex;align-items:center;gap:5px">
                            <i class="fas fa-text-height" style="color:#0284c7"></i> Ukuran Teks Watermark:
                        </label>
                        <select id="watermarkSizeSelect" style="padding:5px 10px;border:1px solid #cbd5e1;border-radius:6px;font-size:12px;background:#fff;color:#1e293b;cursor:pointer;font-weight:600">
                            <option value="extra_small" selected>Super Kecil / Mikro (Pojok Bawah - Tidak Menghalangi Foto)</option>
                            <option value="compact">Kecil / Kompak</option>
                            <option value="medium">Sedang</option>
                        </select>
                    </div>

                    <div style="display:flex;align-items:center;gap:8px">
                        <label for="watermarkLayoutSelect" style="font-size:12px;font-weight:700;color:#334155;margin:0;display:inline-flex;align-items:center;gap:5px">
                            <i class="fas fa-bars-staggered" style="color:#0284c7"></i> Bentuk Tampilan:
                        </label>
                        <select id="watermarkLayoutSelect" style="padding:5px 10px;border:1px solid #cbd5e1;border-radius:6px;font-size:12px;background:#fff;color:#1e293b;cursor:pointer;font-weight:600">
                            <option value="lines_6" selected>6 Baris Lengkap (Font Super Mungil di Pojok)</option>
                            <option value="compact_3">3 Baris Ringkas (Ultra Ramping & Sangat Bersih)</option>
                        </select>
                    </div>
                </div>

                <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
                    <button type="button" class="btn-gps" id="btnGetGps">
                        <i class="fas fa-crosshairs"></i> Perbarui Koordinat GPS
                    </button>
                    <span id="gpsStatus" class="gps-status"><i class="fas fa-info-circle"></i> Mendeteksi koordinat GPS...</span>
                </div>

                {{-- CANVAS PREVIEW --}}
                <div id="previewContainer" class="preview-container">
                    <div style="display:flex;align-items:center;justify-content:center;gap:8px;margin-bottom:12px;color:#1e293b;font-size:13px;font-weight:600">
                        <i class="fas fa-check-circle" style="color:#16a34a"></i> Pratinjau Foto Dokumentasi Geotag
                    </div>
                    <canvas id="watermarkCanvas" class="preview-canvas"></canvas>
                    <div id="fileSizeInfo" style="margin-top:10px;font-size:12px;color:#16a34a;font-weight:600"></div>
                    <div style="margin-top:16px">
                        <button type="submit" class="btn btn-save" style="font-size:13.5px;padding:10px 28px;border-radius:8px;font-weight:700">
                            <i class="fas fa-cloud-upload-alt"></i> Simpan &amp; Unggah Foto Ini
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- DAFTAR FOTO DOKUMENTASI YANG SUDAH DIUNGGAH --}}
        <div style="margin-top:25px">
            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;margin-bottom:14px">
                <h4 style="font-size:14px;color:#1e293b;font-weight:700;margin:0">
                    <i class="fas fa-images" style="color:#1565c0"></i> Galeri Dokumentasi Kegiatan ({{ count($dokumentasi ?? []) }} Foto)
                </h4>
                <a href="{{ config('services.google_drive.folder_url', 'https://drive.google.com/drive/folders/1C7CJuf111ut_L88ar45q1ut4LD8iwxKv?usp=sharing') }}" target="_blank" rel="noopener noreferrer" class="btn" style="background:#0284c7;color:#fff;font-weight:600;font-size:12px;padding:7px 14px;border-radius:6px;display:inline-flex;align-items:center;gap:7px;box-shadow:0 2px 6px rgba(2,132,199,0.25);text-decoration:none" title="Buka folder foto di Google Drive">
                    <i class="fab fa-google-drive" style="font-size:14px"></i>
                    <span>Google Drive</span>
                    <i class="fas fa-arrow-up-right-from-square" style="font-size:11px;opacity:0.8"></i>
                </a>
            </div>

            @if(isset($dokumentasi) && count($dokumentasi) > 0)
                <div class="doc-grid">
                    @foreach($dokumentasi as $doc)
                    <div class="doc-card">
                        <div class="doc-thumb-wrap" onclick="viewPhotoProperties('{{ $doc->foto_url }}', '{{ e($doc->keterangan) }}', '{{ $doc->created_at }}', '{{ $doc->latitude }}', '{{ $doc->longitude }}', '{{ $doc->altitude }}', '{{ $doc->google_drive_url }}', '{{ $doc->akurasi }}')">
                            <img src="{{ $doc->foto_url }}" alt="Dokumentasi Kegiatan" loading="lazy">
                            <div class="doc-badge-overlay"><i class="fas fa-location-dot"></i> Geotag GPS</div>
                        </div>
                        <div class="doc-body">
                            <div class="doc-keterangan">
                                {!! nl2br(e($doc->keterangan ?: 'Dokumentasi kegiatan lapangan')) !!}
                            </div>
                            <div class="doc-actions">
                                <button type="button" class="btn btn-view" style="font-size:10.5px;padding:3px 8px" onclick="viewPhotoProperties('{{ $doc->foto_url }}', '{{ e($doc->keterangan) }}', '{{ $doc->created_at }}', '{{ $doc->latitude }}', '{{ $doc->longitude }}', '{{ $doc->altitude }}', '{{ $doc->google_drive_url }}', '{{ $doc->akurasi }}')">
                                    <i class="fas fa-circle-info"></i> Properties
                                </button>
                                @if($doc->google_drive_url)
                                    <a href="{{ $doc->google_drive_url }}" target="_blank" class="btn" style="background:#0284c7;color:#fff;font-size:10.5px;padding:3px 8px;text-decoration:none;display:inline-flex;align-items:center;gap:4px" title="Lihat di Google Drive">
                                        <i class="fab fa-google-drive"></i> Drive
                                    </a>
                                @endif
                                <a href="{{ $doc->foto_url }}" download class="btn btn-outline" style="font-size:10.5px;padding:3px 8px">
                                    <i class="fas fa-download"></i> Unduh
                                </a>
                                <form method="POST" action="{{ route('kelompok.kegiatan.dokumentasi.hapus', $doc->id) }}" style="display:inline" onsubmit="return confirm('Hapus foto dokumentasi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-hapus" style="font-size:10.5px;padding:3px 8px">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div style="text-align:center;padding:35px;background:#f8fafc;border-radius:8px;border:1px dashed #cbd5e1;color:#94a3b8;font-size:13px">
                    <i class="fas fa-camera" style="font-size:32px;display:block;margin-bottom:8px;color:#cbd5e1"></i>
                    Belum ada foto dokumentasi untuk kegiatan ini. Silakan gunakan tombol di atas untuk mengambil foto geotag atau mengunggah dari galeri.
                </div>
            @endif
        </div>

    </div>

    {{-- KARTU PETA SEBARAN FOTO & AREAL KEGIATAN --}}
    <div class="detail-card">
        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;margin-bottom:14px">
            <div>
                <h3 style="margin:0"><i class="fas fa-map-marked-alt" style="color:#2e7d32"></i> Peta Sebaran Foto & Poligon Lapangan</h3>
                <p style="color:#64748b;font-size:12px;margin:3px 0 0">
                    Memverifikasi posisi setiap foto dokumentasi di dalam atau di luar denah areal kegiatan.
                </p>
            </div>
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                <a href="{{ route('kelompok.kegiatan.peta', $kegiatan->id) }}" target="_blank" class="btn" style="background:#16a34a;color:#fff;font-weight:600;display:inline-flex;align-items:center;gap:6px;box-shadow:0 2px 6px rgba(22,163,74,0.25)">
                    <i class="fas fa-up-right-from-square"></i> Buka Layar Penuh
                </a>
            </div>
        </div>

        <div style="position:relative;border-radius:12px;overflow:hidden;border:1px solid #cbd5e1;box-shadow:0 2px 8px rgba(0,0,0,0.08)">
            <div id="embeddedMap" style="width:100%;height:460px;z-index:1"></div>
            
            {{-- FLOATING INFO MINI-PANEL --}}
            <div class="info-panel" style="position:absolute;z-index:900;right:14px;bottom:14px;background:#ffffff;padding:12px 16px;border-radius:10px;box-shadow:0 4px 18px rgba(0,0,0,0.2);min-width:215px;font-size:12px;border:1px solid rgba(0,0,0,0.06)">
                <div style="font-weight:700;color:#1b5e20;margin-bottom:8px;display:flex;align-items:center;gap:6px">
                    <i class="fas fa-info-circle"></i> Info Sebaran
                </div>
                <div style="display:flex;align-items:center;gap:8px;margin:5px 0;color:#334155">
                    <span style="width:12px;height:12px;border-radius:50%;background:#2e7d32;display:inline-block"></span>
                    <span>Dalam areal:</span>
                    <b id="embedJumlahDalam" style="margin-left:auto;color:#0f172a;font-size:13px">0</b>
                </div>
                <div style="display:flex;align-items:center;gap:8px;margin:5px 0;color:#334155">
                    <span style="width:12px;height:12px;border-radius:50%;background:#d32f2f;display:inline-block"></span>
                    <span>Di luar areal:</span>
                    <b id="embedJumlahLuar" style="margin-left:auto;color:#0f172a;font-size:13px">0</b>
                </div>
                <div style="border-top:1px solid #e2e8f0;margin:7px 0"></div>
                <div style="display:flex;align-items:center;gap:8px;margin:4px 0;font-size:11.5px;color:#64748b">
                    <span style="width:22px;height:3.5px;background:#198754;border-radius:2px;display:inline-block"></span>
                    <span>Batas Kelompok</span>
                </div>
                <div style="display:flex;align-items:center;gap:8px;margin:4px 0;font-size:11.5px;color:#64748b">
                    <span style="width:22px;height:3.5px;background:#1976d2;border-radius:2px;display:inline-block"></span>
                    <span>Batas Kegiatan</span>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- MODAL LIVE CAMERA VIEWFINDER (BERFUNGSI DI LAPTOP & HP) --}}
<div id="liveCameraModal" class="camera-live-modal">
    <div style="color:#fff;font-size:15px;font-weight:700;margin-bottom:10px;display:flex;align-items:center;gap:8px">
        <i class="fas fa-camera" style="color:#22c55e"></i> Kamera Langsung Geotag
    </div>
    <div class="camera-viewport-wrap">
        <video id="liveVideo" autoplay playsinline muted></video>
        <div class="camera-overlay-crosshair"><i class="fas fa-crosshairs"></i></div>
    </div>
    <div class="camera-live-controls">
        <button type="button" class="btn-cam-control" id="btnSwitchCam" title="Ganti Kamera">
            <i class="fas fa-sync-alt"></i> Ganti Kamera
        </button>
        <button type="button" class="btn-shutter-capture" id="btnShutter" title="Jepret Foto">
            <i class="fas fa-camera"></i>
        </button>
        <button type="button" class="btn-cam-control" id="btnCloseCam" title="Batal">
            <i class="fas fa-times"></i> Tutup
        </button>
    </div>
</div>

{{-- MODAL PROPERTIES & DETAIL GEOTAG (EXIF INFO) --}}
<div id="propertiesModal" class="img-modal" onclick="closePropertiesModal(event)">
    <div class="properties-modal-card" onclick="event.stopPropagation()">
        <span class="modal-close-icon" onclick="closePropertiesModal()" title="Tutup">&times;</span>
        
        {{-- SISI KIRI: Frame Foto Santai & Bersih --}}
        <div class="prop-image-pane">
            <div class="prop-frame-box">
                <img id="propModalImg" src="" alt="Pratinjau Foto Dokumentasi">
            </div>
            <div class="prop-image-hint">
                <i class="fas fa-location-dot" style="color:#16a34a"></i> Foto Dokumentasi Ber-Geotag
            </div>
        </div>

        {{-- SISI KANAN: Detail Informasi Rapi & Elegan --}}
        <div class="prop-info-pane">
            <div>
                <div class="prop-header-box">
                    <h3><i class="fas fa-info-circle"></i> Detail &amp; Geotag Foto</h3>
                    <p>Informasi waktu dan titik koordinat dokumentasi</p>
                </div>

                <div class="prop-info-list">
                    <div class="prop-item">
                        <span class="prop-item-label"><i class="fas fa-calendar-check" style="color:#16a34a"></i> Kegiatan</span>
                        <span class="prop-item-value">{{ $kegiatan->nama_kegiatan ?? '-' }}</span>
                    </div>

                    <div class="prop-item">
                        <span class="prop-item-label"><i class="fas fa-users" style="color:#0284c7"></i> Kelompok</span>
                        <span class="prop-item-value">{{ $kelompok->nama_kelompok ?? '-' }}</span>
                    </div>

                    <div class="prop-item">
                        <span class="prop-item-label"><i class="fas fa-map-pin" style="color:#ea580c"></i> Lokasi</span>
                        <span class="prop-item-value">{{ $kegiatan->lokasi ?? '-' }}</span>
                    </div>

                    <div class="prop-item">
                        <span class="prop-item-label"><i class="fas fa-crosshairs" style="color:#16a34a"></i> Titik GPS</span>
                        <span class="prop-item-value">
                            <span class="prop-badge-gps"><span id="propLat">-</span>, <span id="propLng">-</span></span>
                        </span>
                    </div>

                    <div class="prop-item">
                        <span class="prop-item-label"><i class="fas fa-bullseye" style="color:#ef4444"></i> Akurasi GPS</span>
                        <span class="prop-item-value">
                            <span class="prop-badge-gps" id="propAcc">±17 m</span>
                        </span>
                    </div>

                    <div class="prop-item">
                        <span class="prop-item-label"><i class="fas fa-mountain" style="color:#0284c7"></i> Ketinggian</span>
                        <span class="prop-item-value">
                            <span class="prop-badge-alt" id="propAlt">-</span>
                        </span>
                    </div>

                    <div class="prop-item">
                        <span class="prop-item-label"><i class="fas fa-clock" style="color:#64748b"></i> Waktu Unggah</span>
                        <span class="prop-item-value" id="propTime" style="font-size:11.5px">-</span>
                    </div>

                    <div class="prop-item" id="propNoteContainer">
                        <span class="prop-item-label"><i class="fas fa-comment-alt" style="color:#8b5cf6"></i> Catatan</span>
                        <span class="prop-item-value" id="propNote" style="font-weight:normal;color:#475569;font-size:12px">-</span>
                    </div>
                </div>
            </div>

            <div class="prop-actions-box">
                <a id="propGmapsLink" href="#" target="_blank" class="btn-prop-gmaps">
                    <i class="fas fa-map-location-dot"></i> Buka Titik di Google Maps
                </a>
                <a id="propDriveLink" href="#" target="_blank" class="btn" style="display:none;background:#0284c7;color:#fff;padding:9px 16px;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;align-items:center;gap:6px">
                    <i class="fab fa-google-drive"></i> Lihat di Google Drive
                </a>
                <a id="propDownloadLink" href="#" download class="btn-prop-download">
                    <i class="fas fa-download"></i> Unduh Foto Asli
                </a>
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
<script src="https://unpkg.com/leaflet-omnivore@0.3.4/leaflet-omnivore.min.js"></script>
<script>
(function() {
    var btnTriggerCamera = document.getElementById('btnTriggerCamera');
    var btnTriggerGallery = document.getElementById('btnTriggerGallery');
    var inputCamera = document.getElementById('inputCamera');
    var inputGallery = document.getElementById('inputGallery');
    var inputLat = document.getElementById('inputLat');
    var inputLng = document.getElementById('inputLng');
    var inputAlt = document.getElementById('inputAlt');
    var inputKeterangan = document.getElementById('inputKeterangan');
    var btnGetGps = document.getElementById('btnGetGps');
    var gpsStatus = document.getElementById('gpsStatus');
    var previewContainer = document.getElementById('previewContainer');
    var canvas = document.getElementById('watermarkCanvas');
    var fotoBase64 = document.getElementById('fotoBase64');

    // Live Camera Elements
    var liveCameraModal = document.getElementById('liveCameraModal');
    var liveVideo = document.getElementById('liveVideo');
    var btnShutter = document.getElementById('btnShutter');
    var btnSwitchCam = document.getElementById('btnSwitchCam');
    var btnCloseCam = document.getElementById('btnCloseCam');

    var currentLoadedImage = null;
    var currentStream = null;
    var useFacingMode = 'environment';

    var namaCdk = @json(strtoupper($kelompok->cdk->nama ?? (auth()->user()->cdk->nama ?? 'CDK WILAYAH JAWA TIMUR')));
    var namaKelompok = @json($kelompok->nama_kelompok ?? 'Kelompok PS');
    var namaKegiatan = @json($kegiatan->nama_kegiatan ?? 'Kegiatan Lapangan');
    var lokasiKegiatan = @json($kegiatan->lokasi ?? ($kelompok->desa ?? 'Jawa Timur'));
    var inputAkurasi = document.getElementById('inputAkurasi');

    // GPS Helper (Fetches Latitude, Longitude, Altitude, & Accuracy)
    function fetchGps(callback) {
        if (!navigator.geolocation) {
            gpsStatus.textContent = 'Geolocation tidak didukung pada browser ini.';
            gpsStatus.className = 'gps-status';
            if (callback) callback();
            return;
        }

        gpsStatus.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mendeteksi koordinat GPS &amp; Ketinggian...';
        gpsStatus.className = 'gps-status';

        navigator.geolocation.getCurrentPosition(
            function(pos) {
                var lat = pos.coords.latitude.toFixed(8);
                var lng = pos.coords.longitude.toFixed(8);
                var acc = Math.round(pos.coords.accuracy);
                var alt = pos.coords.altitude !== null ? parseFloat(pos.coords.altitude).toFixed(2) : null;

                inputLat.value = lat;
                inputLng.value = lng;
                if (inputAkurasi) {
                    inputAkurasi.value = acc;
                }
                window._currentGpsAccuracy = acc;

                if (alt !== null && !inputAlt.value) {
                    inputAlt.value = alt;
                }

                var altText = alt !== null ? ' | Alt: ' + alt + ' m mdpl' : '';
                gpsStatus.innerHTML = '<i class="fas fa-check-circle"></i> Lokasi terdeteksi: Lat ' + lat + ', Long ' + lng + altText + ' (Akurasi ±' + acc + 'm)';
                gpsStatus.className = 'gps-status active';

                if (currentLoadedImage) {
                    stampWatermark();
                }
                if (callback) callback();
            },
            function(err) {
                gpsStatus.innerHTML = '<i class="fas fa-exclamation-triangle"></i> GPS tidak terdeteksi otomatis: ' + err.message + '. Silakan isi manual.';
                gpsStatus.className = 'gps-status';
                if (callback) callback();
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    }

    // Start Live WebCam / Phone Camera
    function startLiveCamera() {
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            fetchGps();

            if (currentStream) {
                currentStream.getTracks().forEach(function(t) { t.stop(); });
            }

            var constraints = {
                video: {
                    facingMode: useFacingMode,
                    width: { ideal: 1920 },
                    height: { ideal: 1080 }
                },
                audio: false
            };

            navigator.mediaDevices.getUserMedia(constraints)
                .then(function(stream) {
                    currentStream = stream;
                    liveVideo.srcObject = stream;
                    liveVideo.play();
                    liveCameraModal.classList.add('show');
                })
                .catch(function(err) {
                    // Fallback to standard input camera
                    inputCamera.click();
                });
        } else {
            inputCamera.click();
            fetchGps();
        }
    }

    function stopLiveCamera() {
        if (currentStream) {
            currentStream.getTracks().forEach(function(t) { t.stop(); });
            currentStream = null;
        }
        liveCameraModal.classList.remove('show');
    }

    // 1. Tombol "Ambil Foto Geotag"
    btnTriggerCamera.addEventListener('click', function() {
        startLiveCamera();
    });

    // 2. Jepret dari Live Video Feed (Laptop Webcam / HP)
    btnShutter.addEventListener('click', function() {
        if (!liveVideo.videoWidth) return;

        var snapCanvas = document.createElement('canvas');
        snapCanvas.width = liveVideo.videoWidth;
        snapCanvas.height = liveVideo.videoHeight;
        var snapCtx = snapCanvas.getContext('2d');
        snapCtx.drawImage(liveVideo, 0, 0, snapCanvas.width, snapCanvas.height);

        var snapImg = new Image();
        snapImg.onload = function() {
            currentLoadedImage = snapImg;
            stampWatermark();
            stopLiveCamera();
        };
        snapImg.src = snapCanvas.toDataURL('image/jpeg', 0.95);
    });

    // Switch camera mode (front / back)
    btnSwitchCam.addEventListener('click', function() {
        useFacingMode = (useFacingMode === 'environment') ? 'user' : 'environment';
        startLiveCamera();
    });

    // Close live camera
    btnCloseCam.addEventListener('click', function() {
        stopLiveCamera();
    });

    // 3. Tombol "Upload dari Galeri"
    btnTriggerGallery.addEventListener('click', function() {
        inputGallery.click();
        if (!inputLat.value) {
            fetchGps();
        }
    });

    btnGetGps.addEventListener('click', function() {
        fetchGps();
    });

    // Initial GPS fetch
    fetchGps();

    // 4. File Handler Function (dari Galeri atau Input Camera Fallback)
    function processImageFile(file) {
        if (!file) return;

        var reader = new FileReader();
        reader.onload = function(evt) {
            var img = new Image();
            img.onload = function() {
                currentLoadedImage = img;
                stampWatermark();
            };
            img.src = evt.target.result;
        };
        reader.readAsDataURL(file);
    }

    inputCamera.addEventListener('change', function(e) {
        processImageFile(e.target.files[0]);
    });

    inputGallery.addEventListener('change', function(e) {
        processImageFile(e.target.files[0]);
    });

    var watermarkSizeSelect = document.getElementById('watermarkSizeSelect');
    var watermarkLayoutSelect = document.getElementById('watermarkLayoutSelect');
    [inputLat, inputLng, inputAlt, inputKeterangan, inputAkurasi, watermarkSizeSelect, watermarkLayoutSelect].forEach(function(el) {
        if (el) {
            el.addEventListener('input', function() {
                if (currentLoadedImage) stampWatermark();
            });
            el.addEventListener('change', function() {
                if (currentLoadedImage) stampWatermark();
            });
        }
    });

    // 5. Draw Canvas with Geotag Metadata Watermark (Latitude, Longitude, Altitude, Akurasi, CDK)
    function stampWatermark() {
        if (!currentLoadedImage) return;

        var ctx = canvas.getContext('2d');
        var maxDim = 1600;
        var w = currentLoadedImage.width;
        var h = currentLoadedImage.height;

        if (w > maxDim || h > maxDim) {
            if (w > h) {
                h = Math.round((h * maxDim) / w);
                w = maxDim;
            } else {
                w = Math.round((w * maxDim) / h);
                h = maxDim;
            }
        }

        canvas.width = w;
        canvas.height = h;

        // Draw source image
        ctx.drawImage(currentLoadedImage, 0, 0, w, h);

        // ─── WATERMARK RESMI SIPSONLINE (SUPER MUNGIL & TIDAK MENGHALANGI FOTO) ───
        var sizeSelect = document.getElementById('watermarkSizeSelect');
        var sizeMode = sizeSelect ? sizeSelect.value : 'extra_small';

        var layoutSelect = document.getElementById('watermarkLayoutSelect');
        var layoutMode = layoutSelect ? layoutSelect.value : 'lines_6';

        // Hitung skala font berdasarkan sisi terkecil (min(w, h)) agar di kamera webcam 640x480 tidak raksasa
        var baseDim = Math.min(w, h);
        var scaleFactor = 0.014; // default: extra_small (super mungil di pojok)
        if (sizeMode === 'extra_small') {
            scaleFactor = 0.014;
        } else if (sizeMode === 'compact') {
            scaleFactor = 0.020;
        } else if (sizeMode === 'medium') {
            scaleFactor = 0.026;
        }

        var fontSize = Math.max(7.5, Math.min(13, Math.round(baseDim * scaleFactor)));
        var titleSize = Math.max(8, Math.round(fontSize * 1.05));
        var lineSpacing = Math.round(fontSize * 1.25);

        // Siapkan data teks
        var cdkText = (namaCdk || 'CDK WILAYAH JAWA TIMUR').toUpperCase();
        var latNum = parseFloat(inputLat.value);
        var lngNum = parseFloat(inputLng.value);
        var coordStr = (!isNaN(latNum) && !isNaN(lngNum))
            ? latNum.toFixed(8) + ', ' + lngNum.toFixed(8)
            : (inputLat.value && inputLng.value ? inputLat.value + ', ' + inputLng.value : '-7.43602920, 111.31012720');
        var accVal = window._currentGpsAccuracy || (inputAkurasi && inputAkurasi.value ? inputAkurasi.value : 17);
        var altNum = inputAlt.value ? parseFloat(inputAlt.value) : 104.18;
        var altValStr = (!isNaN(altNum) ? altNum.toFixed(2) : '104.18') + ' m';

        var monthsIndo = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        var now = new Date();
        var timeStr = now.getDate() + ' ' +
            monthsIndo[now.getMonth()] + ' ' +
            now.getFullYear() + ' ' +
            String(now.getHours()).padStart(2, '0') + '.' +
            String(now.getMinutes()).padStart(2, '0') + '.' +
            String(now.getSeconds()).padStart(2, '0');

        var lines = [];
        if (layoutMode === 'compact_3') {
            lines.push({ text: cdkText + '  •  SI PS ONLINE', font: 'bold ' + titleSize + "px 'Segoe UI', Arial, sans-serif" });
            lines.push({ text: '📍 ' + coordStr + '   🎯 ±' + accVal + ' m   ⛰️ ' + altValStr, font: 'normal ' + fontSize + "px 'Segoe UI Emoji', 'Segoe UI', Arial, sans-serif" });
            lines.push({ text: '🕒 ' + timeStr, font: 'normal ' + fontSize + "px 'Segoe UI Emoji', 'Segoe UI', Arial, sans-serif" });
        } else {
            lines.push({ text: cdkText, font: 'bold ' + titleSize + "px 'Segoe UI', Arial, sans-serif" });
            lines.push({ text: 'SI PS ONLINE', font: 'bold ' + titleSize + "px 'Segoe UI', Arial, sans-serif" });
            lines.push({ text: '📍 ' + coordStr, font: 'normal ' + fontSize + "px 'Segoe UI Emoji', 'Segoe UI', Arial, sans-serif" });
            lines.push({ text: '🎯 Akurasi : ±' + accVal + ' m', font: 'normal ' + fontSize + "px 'Segoe UI Emoji', 'Segoe UI', Arial, sans-serif" });
            lines.push({ text: '⛰️ Altitude : ' + altValStr, font: 'normal ' + fontSize + "px 'Segoe UI Emoji', 'Segoe UI', Arial, sans-serif" });
            lines.push({ text: '🕒 ' + timeStr, font: 'normal ' + fontSize + "px 'Segoe UI Emoji', 'Segoe UI', Arial, sans-serif" });
        }

        var numLines = lines.length;
        var totalTextHeight = (numLines - 1) * lineSpacing + titleSize;
        var padLeft = Math.max(8, Math.round(w * 0.016));
        var padBottom = Math.max(8, Math.round(h * 0.018));

        // Gradasi gelap transparan ramping dan halus hanya setinggi area teks (sangat minimalis)
        var bannerHeight = totalTextHeight + Math.round(fontSize * 1.4);
        var bannerY = h - bannerHeight;

        var grad = ctx.createLinearGradient(0, bannerY, 0, h);
        grad.addColorStop(0, 'rgba(0, 0, 0, 0.0)');
        grad.addColorStop(0.35, 'rgba(0, 0, 0, 0.35)');
        grad.addColorStop(1, 'rgba(0, 0, 0, 0.72)');
        ctx.fillStyle = grad;
        ctx.fillRect(0, bannerY, w, bannerHeight);

        ctx.textAlign = 'left';
        ctx.textBaseline = 'alphabetic';
        ctx.shadowColor = 'rgba(0, 0, 0, 0.95)';
        ctx.shadowBlur = 3;
        ctx.shadowOffsetX = 1;
        ctx.shadowOffsetY = 1;
        ctx.fillStyle = '#ffffff';

        var curY = (h - padBottom) - ((numLines - 1) * lineSpacing);
        for (var i = 0; i < lines.length; i++) {
            ctx.font = lines[i].font;
            ctx.fillText(lines[i].text, padLeft, curY);
            curY += lineSpacing;
        }

        // Convert canvas to base64 JPEG with Adaptive Quality Compressor (< 300 KB threshold)
        var maxTargetBytes = 295 * 1024; // 295 KB
        var quality = 0.85;
        var dataUrl = '';
        var actualBytes = 0;

        do {
            dataUrl = canvas.toDataURL('image/jpeg', quality);
            var base64Length = dataUrl.length - (dataUrl.indexOf(',') + 1);
            actualBytes = Math.round((base64Length * 3) / 4);
            quality -= 0.08;
        } while (actualBytes > maxTargetBytes && quality >= 0.35);

        fotoBase64.value = dataUrl;

        var kbSize = Math.round(actualBytes / 1024);
        var sizeInfoEl = document.getElementById('fileSizeInfo');
        if (sizeInfoEl) {
            sizeInfoEl.innerHTML = '<i class="fas fa-check-circle" style="color:#22c55e"></i> Ukuran Foto: <strong>' + kbSize + ' KB</strong> (Otomatis dikompres di bawah 300 KB ✅)';
        }

        previewContainer.style.display = 'block';
        previewContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Modal Properties & Detail Viewer
    window.viewPhotoProperties = function(src, ket, time, directLat, directLng, directAlt, driveUrl, directAcc) {
        var modal = document.getElementById('propertiesModal');
        var modalImg = document.getElementById('propModalImg');
        var propLat = document.getElementById('propLat');
        var propLng = document.getElementById('propLng');
        var propAlt = document.getElementById('propAlt');
        var propAcc = document.getElementById('propAcc');
        var propTime = document.getElementById('propTime');
        var propNote = document.getElementById('propNote');
        var gmapsLink = document.getElementById('propGmapsLink');
        var driveLink = document.getElementById('propDriveLink');
        var downloadLink = document.getElementById('propDownloadLink');

        modalImg.src = src;
        downloadLink.href = src;
        propTime.textContent = time || 'Sesuai Timestamp Foto';

        var lat = '-';
        var lng = '-';
        var alt = '-';
        var acc = (directAcc && directAcc !== 'null' && directAcc !== '') ? ('±' + Math.round(directAcc) + ' m') : '±17 m';

        if (propAcc) {
            propAcc.textContent = acc;
        }

        if (driveLink) {
            if (driveUrl && driveUrl !== 'null' && driveUrl.trim() !== '') {
                driveLink.href = driveUrl;
                driveLink.style.display = 'inline-flex';
            } else {
                driveLink.style.display = 'none';
            }
        }

        // 1. Cek koordinat dari kolom database langsung jika tersedia
        if (directLat && directLng && directLat !== 'null' && directLng !== 'null') {
            lat = directLat + '°';
            lng = directLng + '°';
        }
        if (directAlt && directAlt !== 'null') {
            alt = directAlt + ' m mdpl';
        }

        // 2. Parse koordinat & altitude dari string keterangan jika masih '-'
        if (ket) {
            if (lat === '-') {
                var latMatch = ket.match(/Lat\s*([-\d.]+)/i) || ket.match(/GPS:\s*([-\d.]+)/i);
                if (latMatch && latMatch[1]) lat = latMatch[1] + '°';
            }
            if (lng === '-') {
                var lngMatch = ket.match(/Long\s*([-\d.]+)/i) || ket.match(/,\s*([-\d.]+)/i);
                if (lngMatch && lngMatch[1]) lng = lngMatch[1] + '°';
            }
            if (alt === '-') {
                var altMatch = ket.match(/Alt:\s*([\d.]+)/i);
                if (altMatch && altMatch[1]) alt = altMatch[1] + ' m mdpl';
            }
        }

        propLat.textContent = lat;
        propLng.textContent = lng;
        propAlt.textContent = alt;

        // Bersihkan teks catatan dari redundansi string GPS
        var note = ket || 'Dokumentasi kegiatan lapangan';
        if (note.indexOf('📍 GPS:') !== -1) {
            note = note.split('📍 GPS:')[0].replace(/\|\s*$/, '').trim();
        }
        propNote.textContent = note || 'Dokumentasi kegiatan lapangan';

        if (lat !== '-' && lng !== '-') {
            var rawLat = lat.replace('°', '');
            var rawLng = lng.replace('°', '');
            gmapsLink.href = 'https://www.google.com/maps?q=' + encodeURIComponent(rawLat + ',' + rawLng);
            gmapsLink.style.display = 'flex';
        } else {
            gmapsLink.style.display = 'none';
        }

        modal.classList.add('show');
    };

    window.closePropertiesModal = function(e) {
        if (!e || e.target.id === 'propertiesModal' || e.target.classList.contains('modal-close-icon')) {
            document.getElementById('propertiesModal').classList.remove('show');
        }
    };

    /* ========================================================
       OFFLINE GEOTAG QUEUE & AUTO-SYNC ENGINE (IndexedDB)
    ======================================================== */
    var SipsOfflineDB = (function() {
        var DB_NAME = 'SIPSONLINE_OFFLINE_GEOTAG_DB';
        var DB_VERSION = 1;
        var STORE_NAME = 'photo_queue';
        var db = null;

        function openDB() {
            return new Promise(function(resolve, reject) {
                if (db) return resolve(db);
                if (!window.indexedDB) {
                    return reject(new Error('IndexedDB tidak didukung pada browser ini.'));
                }
                var req = window.indexedDB.open(DB_NAME, DB_VERSION);
                req.onupgradeneeded = function(e) {
                    var d = e.target.result;
                    if (!d.objectStoreNames.contains(STORE_NAME)) {
                        var store = d.createObjectStore(STORE_NAME, { keyPath: 'id', autoIncrement: true });
                        store.createIndex('id_kegiatan', 'id_kegiatan', { unique: false });
                        store.createIndex('created_at', 'created_at', { unique: false });
                    }
                };
                req.onsuccess = function(e) {
                    db = e.target.result;
                    resolve(db);
                };
                req.onerror = function(e) {
                    reject(e.target.error);
                };
            });
        }

        function addPhoto(item) {
            return openDB().then(function(d) {
                return new Promise(function(resolve, reject) {
                    var tx = d.transaction(STORE_NAME, 'readwrite');
                    var store = tx.objectStore(STORE_NAME);
                    var req = store.add(item);
                    req.onsuccess = function() { resolve(req.result); };
                    req.onerror = function(e) { reject(e.target.error); };
                });
            });
        }

        function getAllPhotos() {
            return openDB().then(function(d) {
                return new Promise(function(resolve, reject) {
                    var tx = d.transaction(STORE_NAME, 'readonly');
                    var store = tx.objectStore(STORE_NAME);
                    var req = store.getAll();
                    req.onsuccess = function() { resolve(req.result || []); };
                    req.onerror = function(e) { reject(e.target.error); };
                });
            });
        }

        function deletePhoto(id) {
            return openDB().then(function(d) {
                return new Promise(function(resolve, reject) {
                    var tx = d.transaction(STORE_NAME, 'readwrite');
                    var store = tx.objectStore(STORE_NAME);
                    var req = store.delete(id);
                    req.onsuccess = function() { resolve(true); };
                    req.onerror = function(e) { reject(e.target.error); };
                });
            });
        }

        return {
            addPhoto: addPhoto,
            getAllPhotos: getAllPhotos,
            deletePhoto: deletePhoto
        };
    })();

    // Helper Toast Notification
    function showOfflineToast(message, type) {
        var existing = document.getElementById('offlineToastEl');
        if (existing) existing.remove();

        var toast = document.createElement('div');
        toast.id = 'offlineToastEl';
        toast.className = 'offline-toast ' + (type || 'info');
        var icon = 'fa-info-circle';
        if (type === 'success') icon = 'fa-check-circle';
        if (type === 'warning') icon = 'fa-exclamation-triangle';

        toast.innerHTML = '<i class="fas ' + icon + '" style="font-size:16px"></i><span>' + message + '</span>';
        document.body.appendChild(toast);

        setTimeout(function() {
            if (toast && toast.parentNode) {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.4s';
                setTimeout(function() { toast.remove(); }, 400);
            }
        }, 5000);
    }

    // Monitor Status Koneksi
    var connStatusPill = document.getElementById('connStatusPill');
    function updateConnectionStatus() {
        var isOnline = navigator.onLine;
        if (connStatusPill) {
            if (isOnline) {
                connStatusPill.className = 'conn-status-pill online';
                connStatusPill.innerHTML = '<i class="fas fa-wifi"></i> Online';
                connStatusPill.title = 'Terhubung ke Internet';
            } else {
                connStatusPill.className = 'conn-status-pill offline';
                connStatusPill.innerHTML = '<i class="fas fa-plane-slash"></i> Offline (Mode Lapangan)';
                connStatusPill.title = 'Tidak Ada Sinyal Internet. Foto akan disimpan di HP.';
            }
        }
    }
    updateConnectionStatus();

    window.addEventListener('online', function() {
        updateConnectionStatus();
        showOfflineToast('Sinyal internet kembali aktif! Memulai pengunggahan antrian foto...', 'info');
        syncOfflineQueue();
    });

    window.addEventListener('offline', function() {
        updateConnectionStatus();
        showOfflineToast('Mode Offline aktif. Foto yang diambil akan disimpan sementara di memori HP.', 'warning');
    });

    // Render Antrian Offline di UI
    var offlineQueueBox = document.getElementById('offlineQueueBox');
    var offlineQueueCount = document.getElementById('offlineQueueCount');
    var offlineQueueList = document.getElementById('offlineQueueList');
    var btnSyncOfflineNow = document.getElementById('btnSyncOfflineNow');
    var offlineSyncStatusText = document.getElementById('offlineSyncStatusText');

    function renderOfflineQueue() {
        SipsOfflineDB.getAllPhotos().then(function(items) {
            var currentKegiatanId = '{{ $kegiatan->id }}';
            var filtered = items.filter(function(i) {
                return String(i.id_kegiatan) === String(currentKegiatanId);
            });

            if (filtered.length === 0) {
                if (offlineQueueBox) offlineQueueBox.style.display = 'none';
                return;
            }

            if (offlineQueueBox) offlineQueueBox.style.display = 'block';
            if (offlineQueueCount) offlineQueueCount.textContent = filtered.length;

            if (offlineQueueList) {
                offlineQueueList.innerHTML = '';
                filtered.forEach(function(item) {
                    var card = document.createElement('div');
                    card.className = 'offline-item-card';
                    var timeStr = item.created_at ? new Date(item.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-';
                    card.innerHTML = 
                        '<img src="' + item.foto_base64 + '" alt="Thumbnail">' +
                        '<div class="offline-item-info">' +
                            '<strong>' + (item.keterangan || 'Foto Dokumentasi') + '</strong>' +
                            '<span>📍 ' + (item.latitude || '-') + ', ' + (item.longitude || '-') + '</span>' +
                            '<span>🕒 ' + timeStr + ' (Tersimpan di HP)</span>' +
                        '</div>' +
                        '<button type="button" class="btn-del-offline" title="Hapus foto dari HP" data-id="' + item.id + '">' +
                            '<i class="fas fa-trash"></i>' +
                        '</button>';
                    offlineQueueList.appendChild(card);
                });

                offlineQueueList.querySelectorAll('.btn-del-offline').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        var photoId = parseInt(this.getAttribute('data-id'));
                        if (confirm('Hapus foto ini dari antrian offline HP?')) {
                            SipsOfflineDB.deletePhoto(photoId).then(function() {
                                renderOfflineQueue();
                                showOfflineToast('Foto dihapus dari antrian HP.', 'info');
                            });
                        }
                    });
                });
            }
        }).catch(function(err) {
            console.warn('[OfflineDB] Error load queue:', err);
        });
    }

    // Auto-Sync Worker
    var isSyncingOffline = false;
    async function syncOfflineQueue() {
        if (isSyncingOffline) return;
        if (!navigator.onLine) {
            showOfflineToast('Belum ada koneksi internet. Foto tetap tersimpan aman di HP.', 'warning');
            return;
        }

        try {
            var items = await SipsOfflineDB.getAllPhotos();
            var currentKegiatanId = '{{ $kegiatan->id }}';
            var filtered = items.filter(function(i) {
                return String(i.id_kegiatan) === String(currentKegiatanId);
            });

            if (filtered.length === 0) {
                renderOfflineQueue();
                return;
            }

            isSyncingOffline = true;
            if (btnSyncOfflineNow) {
                btnSyncOfflineNow.disabled = true;
                btnSyncOfflineNow.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengunggah...';
            }
            if (offlineSyncStatusText) {
                offlineSyncStatusText.style.display = 'block';
                offlineSyncStatusText.textContent = 'Mengunggah 0 dari ' + filtered.length + ' foto ke server...';
            }

            var successUploaded = 0;
            var csrfToken = document.querySelector('meta[name="csrf-token"]') 
                ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') 
                : (document.querySelector('input[name="_token"]') ? document.querySelector('input[name="_token"]').value : '');

            for (var idx = 0; idx < filtered.length; idx++) {
                var item = filtered[idx];
                if (offlineSyncStatusText) {
                    offlineSyncStatusText.textContent = 'Mengunggah foto ' + (idx + 1) + ' dari ' + filtered.length + ' ke server & Google Drive...';
                }

                var formData = new FormData();
                formData.append('_token', csrfToken);
                formData.append('foto_base64', item.foto_base64);
                formData.append('keterangan', item.keterangan || '');
                formData.append('latitude', item.latitude || '');
                formData.append('longitude', item.longitude || '');
                formData.append('altitude', item.altitude || '');
                formData.append('akurasi', item.akurasi || '17');

                try {
                    var response = await fetch('{{ route("kelompok.kegiatan.dokumentasi.simpan", $kegiatan->id) }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        // SUKSES TERUNGGAH KE SERVER -> OTOMATIS HAPUS DARI MEMORI HP!
                        await SipsOfflineDB.deletePhoto(item.id);
                        successUploaded++;
                    }
                } catch (postErr) {
                    console.warn('[OfflineSync] Gagal upload item id ' + item.id + ':', postErr);
                    break;
                }
            }

            isSyncingOffline = false;
            if (btnSyncOfflineNow) {
                btnSyncOfflineNow.disabled = false;
                btnSyncOfflineNow.innerHTML = '<i class="fas fa-cloud-arrow-up"></i> Unggah Sekarang';
            }

            renderOfflineQueue();

            if (successUploaded > 0) {
                showOfflineToast('🎉 ' + successUploaded + ' foto antrian berhasil diunggah ke server dan Google Drive!', 'success');
                setTimeout(function() {
                    window.location.reload();
                }, 1500);
            }
        } catch (err) {
            console.error('[OfflineSync] Exception:', err);
            isSyncingOffline = false;
            if (btnSyncOfflineNow) {
                btnSyncOfflineNow.disabled = false;
                btnSyncOfflineNow.innerHTML = '<i class="fas fa-cloud-arrow-up"></i> Unggah Sekarang';
            }
        }
    }

    if (btnSyncOfflineNow) {
        btnSyncOfflineNow.addEventListener('click', syncOfflineQueue);
    }

    // Intersep Pengiriman Form Foto (GeotagForm)
    var geotagForm = document.getElementById('geotagForm');
    if (geotagForm) {
        geotagForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            if (!fotoBase64.value && !inputCamera.files[0] && !inputGallery.files[0]) {
                alert('Silakan ambil foto dengan kamera atau pilih dari galeri terlebih dahulu.');
                return;
            }

            var submitBtn = geotagForm.querySelector('button[type="submit"]');
            var originalBtnHtml = submitBtn ? submitBtn.innerHTML : '';

            // 1. JIKA OFFLINE (TIDAK ADA SINYAL INTERNET): SIMPAN DI HP
            if (!navigator.onLine) {
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan di HP...';
                }

                var offlineItem = {
                    id_kegiatan: '{{ $kegiatan->id }}',
                    nama_kegiatan: namaKegiatan,
                    nama_kelompok: namaKelompok,
                    foto_base64: fotoBase64.value,
                    keterangan: inputKeterangan.value,
                    latitude: inputLat.value,
                    longitude: inputLng.value,
                    altitude: inputAlt.value,
                    akurasi: inputAkurasi ? inputAkurasi.value : 17,
                    created_at: new Date().toISOString()
                };

                try {
                    await SipsOfflineDB.addPhoto(offlineItem);
                    showOfflineToast('✅ Foto berhasil disimpan di memori HP! Akan otomatis diunggah ke server & Google Drive saat tersambung internet.', 'success');
                    
                    previewContainer.style.display = 'none';
                    fotoBase64.value = '';
                    inputKeterangan.value = '';
                    currentLoadedImage = null;

                    renderOfflineQueue();
                } catch (dbErr) {
                    alert('Gagal menyimpan foto di HP: ' + dbErr.message);
                } finally {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnHtml;
                    }
                }
                return;
            }

            // 2. JIKA ONLINE: COBA KIRIM LANGSUNG KE SERVER VIA AJAX
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengunggah ke Server & Google Drive...';
            }

            var formData = new FormData(geotagForm);
            try {
                var response = await fetch(geotagForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    var jsonRes = await response.json();
                    showOfflineToast('✅ ' + (jsonRes.message || 'Foto berhasil diunggah!'), 'success');
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    throw new Error('Server merespon dengan status: ' + response.status);
                }
            } catch (netErr) {
                // JIKA GAGAL KARENA SINYAL HILANG / TIMEOUT -> SIMPAN OTOMATIS KE HP!
                console.warn('[Upload] Gagal upload online, mengalihkan ke penyimpanan offline HP:', netErr);
                var fallbackItem = {
                    id_kegiatan: '{{ $kegiatan->id }}',
                    nama_kegiatan: namaKegiatan,
                    nama_kelompok: namaKelompok,
                    foto_base64: fotoBase64.value,
                    keterangan: inputKeterangan.value,
                    latitude: inputLat.value,
                    longitude: inputLng.value,
                    altitude: inputAlt.value,
                    akurasi: inputAkurasi ? inputAkurasi.value : 17,
                    created_at: new Date().toISOString()
                };

                try {
                    await SipsOfflineDB.addPhoto(fallbackItem);
                    showOfflineToast('Sinyal terputus saat upload. Foto berhasil diamankan di memori HP dan akan otomatis diunggah saat sinyal stabil.', 'warning');
                    
                    previewContainer.style.display = 'none';
                    fotoBase64.value = '';
                    inputKeterangan.value = '';
                    currentLoadedImage = null;

                    renderOfflineQueue();
                } catch (fallbackDbErr) {
                    alert('Gagal menyimpan cadangan foto di HP: ' + fallbackDbErr.message);
                } finally {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnHtml;
                    }
                }
            }
        });
    }

    // Inisialisasi awal antrian offline saat halaman dibuka
    renderOfflineQueue();
    // Jika saat load awal online dan ada antrian, langsung coba auto-sync
    if (navigator.onLine) {
        setTimeout(syncOfflineQueue, 2000);
    }
    // Cek berkala setiap 30 detik jika ada koneksi untuk sync antrian
    setInterval(function() {
        if (navigator.onLine && !isSyncingOffline) {
            syncOfflineQueue();
        }
    }, 30000);
})();

/* ========================================================
   EMBEDDED MAP: SEBARAN FOTO & VERIFIKASI POLIGON KEGIATAN
======================================================== */
(function() {
    var mapEl = document.getElementById('embeddedMap');
    if (!mapEl) return;

    var embedKmlUrl = @json($kmlUrl ?? '');
    var embedDataFoto = @json($dokumentasi ?? []);
    var embedPolygonKelompok = @json($kelompok->polygon_kelompok ?? '');

    var embedMap = L.map('embeddedMap', {
        zoomControl: true,
        scrollWheelZoom: false,
        fullscreenControl: true,
        fullscreenControlOptions: {
            position: 'topleft',
            title: 'Perbesar Layar Penuh (Fullscreen)',
            titleCancel: 'Keluar Layar Penuh'
        }
    });

    var hybrid = L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
        maxZoom: 21,
        attribution: 'Leaflet | Google Hybrid'
    }).addTo(embedMap);

    var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    });

    var layerKelompok = L.layerGroup().addTo(embedMap);
    var layerKegiatan = L.layerGroup().addTo(embedMap);
    var layerFoto = L.layerGroup().addTo(embedMap);

    var layersControl = L.control.layers({
        'Google Hybrid': hybrid,
        'OpenStreetMap': osm
    }, {
        '🗺️ Batas Kelompok': layerKelompok,
        '📍 Batas Kegiatan': layerKegiatan,
        '📷 Foto Geotagging': layerFoto
    }, { position: 'topright' }).addTo(embedMap);

    initMapTools(embedMap, {
        kelompokId: {{ $kegiatan->id_kelompok ?? ($kelompok->id ?? 'null') }},
        layersControl: layersControl,
        enablePetak: true,
        enableFullscreen: true,
        enableLocate: true,
        enableMeasure: true
    });

    var polygonKegiatanList = [];
    var semuaBounds = [];

    function pointInPolygon(lat, lng, polygon) {
        var inside = false;
        for (var i = 0, j = polygon.length - 1; i < polygon.length; j = i++) {
            var xi = polygon[i][0], yi = polygon[i][1];
            var xj = polygon[j][0], yj = polygon[j][1];
            var intersect = ((yi > lng) !== (yj > lng)) &&
                (lat < (xj - xi) * (lng - yi) / (yj - yi) + xi);
            if (intersect) inside = !inside;
        }
        return inside;
    }

    function cekDalamPolygonKegiatan(lat, lng) {
        for (var k = 0; k < polygonKegiatanList.length; k++) {
            if (pointInPolygon(lat, lng, polygonKegiatanList[k])) return true;
        }
        return false;
    }

    function ambilPolygonDariLayer(layer, targetArray) {
        if (!layer) return;
        if (layer instanceof L.Polygon) {
            var latlngs = layer.getLatLngs();
            function proses(arr) {
                if (!arr || arr.length === 0) return;
                if (arr[0] instanceof L.LatLng) {
                    var poly = arr.map(function(p) { return [p.lat, p.lng]; });
                    if (poly.length >= 3) targetArray.push(poly);
                } else {
                    arr.forEach(function(sub) { proses(sub); });
                }
            }
            proses(latlngs);
        }
        if (layer.eachLayer) {
            layer.eachLayer(function(child) {
                ambilPolygonDariLayer(child, targetArray);
            });
        }
    }

    // Gambar GeoJSON Kelompok
    if (embedPolygonKelompok) {
        try {
            var dataGeo = typeof embedPolygonKelompok === 'string' ? JSON.parse(embedPolygonKelompok) : embedPolygonKelompok;
            var lGeo = L.geoJSON(dataGeo, {
                style: function() {
                    return { color: '#198754', weight: 3, opacity: 0.9, fillColor: '#198754', fillOpacity: 0.08 };
                }
            }).addTo(layerKelompok);
            if (lGeo.getBounds().isValid()) semuaBounds.push(lGeo.getBounds());
        } catch(e) {}
    }

    function buatMarkerFoto(foto, dalam) {
        var lat = parseFloat(foto.latitude);
        var lng = parseFloat(foto.longitude);
        if (isNaN(lat) || isNaN(lng)) return;

        var warna = dalam ? '#2e7d32' : '#d32f2f';
        var icon = L.divIcon({
            className: '',
            html: '<div style="width:32px;height:32px;border-radius:50%;background:' + warna + ';border:3px solid #fff;box-shadow:0 2px 7px rgba(0,0,0,0.45);display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;"><i class="fas fa-camera"></i></div>',
            iconSize: [32, 32],
            iconAnchor: [16, 16]
        });

        var marker = L.marker([lat, lng], { icon: icon });
        var fotoUrl = foto.foto_url || ('/storage/kegiatan/' + encodeURIComponent(foto.nama_file || ''));

        var statusBadge = dalam 
            ? '<div style="background:#e8f5e9;color:#1b5e20;border:1px solid #a5d6a7;padding:6px 8px;border-radius:6px;margin:7px 0;font-weight:700;font-size:11px;display:flex;align-items:center;gap:5px;"><i class="bi bi-check-circle-fill"></i> DALAM AREAL KEGIATAN</div>'
            : '<div style="background:#ffebee;color:#b71c1c;border:1px solid #ffcdd2;padding:6px 8px;border-radius:6px;margin:7px 0;font-weight:700;font-size:11px;display:flex;align-items:center;gap:5px;"><i class="bi bi-exclamation-triangle-fill"></i> DI LUAR AREAL KEGIATAN</div>';

        var popupHtml = '<div style="width:230px;max-width:75vw;font-size:12px;color:#334155;">'
            + (fotoUrl ? '<img src="' + fotoUrl + '" style="width:100%;max-height:160px;object-fit:cover;border-radius:6px;margin-bottom:8px;cursor:pointer;" onclick="viewPhotoProperties(\'' + fotoUrl + '\', \'' + (foto.keterangan || '').replace(/'/g, "\\'") + '\', \'' + (foto.created_at || '') + '\', \'' + lat + '\', \'' + lng + '\', \'' + (foto.altitude || '-') + '\', \'' + (foto.google_drive_url || '') + '\')">' : '')
            + '<div style="font-weight:700;color:#1e293b;display:flex;align-items:center;gap:5px;"><i class="fas fa-camera" style="color:#2e7d32"></i> Foto Geotag</div>'
            + statusBadge
            + '<div style="border-top:1px solid #e2e8f0;margin:6px 0;"></div>'
            + '<div style="margin:3px 0"><b>Lat:</b> ' + lat.toFixed(7) + '</div>'
            + '<div style="margin:3px 0"><b>Lng:</b> ' + lng.toFixed(7) + '</div>'
            + (foto.altitude ? '<div style="margin:3px 0"><b>Alt:</b> ' + foto.altitude + ' m</div>' : '')
            + (foto.created_at ? '<div style="margin:3px 0;font-size:11px;color:#64748b">' + foto.created_at + '</div>' : '')
            + '</div>';

        marker.bindPopup(popupHtml);
        marker.addTo(layerFoto);
        semuaBounds.push(L.latLngBounds([[lat, lng], [lat, lng]]));
    }

    function prosesFoto() {
        var dalam = 0, luar = 0;
        if (Array.isArray(embedDataFoto)) {
            embedDataFoto.forEach(function(foto) {
                var lat = parseFloat(foto.latitude);
                var lng = parseFloat(foto.longitude);
                if (isNaN(lat) || isNaN(lng)) return;

                var inside = polygonKegiatanList.length > 0 ? cekDalamPolygonKegiatan(lat, lng) : false;
                if (inside) dalam++; else luar++;
                buatMarkerFoto(foto, inside);
            });
        }
        var elDalam = document.getElementById('embedJumlahDalam');
        var elLuar = document.getElementById('embedJumlahLuar');
        if (elDalam) elDalam.textContent = dalam;
        if (elLuar) elLuar.textContent = luar;
    }

    function fitPeta() {
        if (semuaBounds.length === 0) {
            embedMap.setView([-7.6, 111.5], 12);
            return;
        }
        var b = semuaBounds[0];
        for (var i = 1; i < semuaBounds.length; i++) {
            b = b.extend(semuaBounds[i]);
        }
        if (b.isValid()) {
            embedMap.fitBounds(b, { padding: [25, 25] });
        }
    }

    if (embedKmlUrl) {
        var layerKML = omnivore.kml(embedKmlUrl);
        layerKML.on('ready', function() {
            layerKML.eachLayer(function(l) {
                if (l.setStyle) {
                    l.setStyle({ color: '#1976d2', weight: 3.5, opacity: 0.95, fillColor: '#1976d2', fillOpacity: 0.15 });
                }
                layerKegiatan.addLayer(l);
            });
            polygonKegiatanList = [];
            layerKML.eachLayer(function(l) {
                ambilPolygonDariLayer(l, polygonKegiatanList);
            });
            if (layerKML.getBounds().isValid()) {
                semuaBounds.push(layerKML.getBounds());
            }
            prosesFoto();
            fitPeta();
        });
        layerKML.on('error', function() {
            prosesFoto();
            fitPeta();
        });
    } else {
        prosesFoto();
        fitPeta();
    }
})();
</script>
@endpush
