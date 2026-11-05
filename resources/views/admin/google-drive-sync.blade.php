@extends('layouts.app')

@section('title', 'Google Drive Sync - SIPSOnline')

@push('styles')
<style>
    .sync-container {
        padding: 20px 24px;
        max-width: 1280px;
        margin: 0 auto;
    }

    /* Header */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .page-header-title h2 {
        color: #1b5e20;
        font-size: 20px;
        font-weight: 700;
        margin: 0 0 4px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .page-header-title p {
        color: #64748b;
        font-size: 13px;
        margin: 0;
    }

    .page-header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 20px;
    }

    .status-pill.connected {
        background: #e8f5e9;
        color: #1b5e20;
        border: 1px solid #c8e6c9;
    }

    .status-pill.disconnected {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .btn-open-drive {
        background: #ffffff;
        color: #1b5e20;
        border: 1.5px solid #2e7d32;
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }

    .btn-open-drive:hover {
        background: #1b5e20;
        color: #ffffff;
    }

    /* Main Card */
    .sync-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        padding: 20px;
        margin-bottom: 20px;
    }

    /* Stats Strip */
    .stats-strip {
        display: flex;
        align-items: center;
        gap: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid #f1f5f9;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .stat-item {
        display: flex;
        align-items: baseline;
        gap: 6px;
        font-size: 13px;
        color: #64748b;
    }

    .stat-item strong {
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
    }

    .stat-item.synced strong { color: #16a34a; }
    .stat-item.pending strong { color: #d97706; }
    .stat-item.failed strong { color: #dc2626; }

    /* Action Toolbar (Filter & Sync Button) */
    .toolbar-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }

    .filter-form {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        flex: 1;
    }

    .filter-search-input {
        height: 36px;
        padding: 4px 12px;
        font-size: 12.5px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        background: #ffffff;
        color: #1e293b;
        outline: none;
        min-width: 220px;
    }

    .filter-search-input:focus {
        border-color: #2e7d32;
        box-shadow: 0 0 0 2px rgba(46, 125, 50, 0.1);
    }

    .filter-select {
        height: 36px;
        padding: 4px 10px;
        font-size: 12.5px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        background: #f8fafc;
        color: #1e293b;
        outline: none;
        max-width: 200px;
    }

    .filter-select:focus {
        border-color: #2e7d32;
        background: #fff;
    }

    .btn-filter {
        background: #1b5e20;
        color: #ffffff;
        border: none;
        height: 36px;
        padding: 0 14px;
        border-radius: 6px;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background 0.2s;
    }

    .btn-filter:hover {
        background: #2e7d32;
    }

    .btn-reset {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
        height: 36px;
        padding: 0 12px;
        border-radius: 6px;
        font-size: 12.5px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .btn-reset:hover {
        background: #e2e8f0;
        color: #1e293b;
    }

    .sync-controls {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-start-sync {
        background: #1b5e20;
        color: #ffffff;
        border: none;
        height: 36px;
        padding: 0 18px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        box-shadow: 0 2px 4px rgba(27, 94, 32, 0.2);
    }

    .btn-start-sync:hover:not(:disabled) {
        background: #2e7d32;
    }

    .btn-start-sync:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Progress Banner (Slim) */
    .progress-banner {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 16px;
        display: none;
    }

    .progress-banner-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12.5px;
        margin-bottom: 6px;
    }

    .progress-bar-track {
        height: 8px;
        background: #e2e8f0;
        border-radius: 4px;
        overflow: hidden;
    }

    .progress-bar-fill {
        height: 100%;
        width: 0%;
        background: #16a34a;
        border-radius: 4px;
        transition: width 0.3s;
    }

    .progress-banner-file {
        font-size: 11.5px;
        color: #64748b;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .progress-banner-file strong {
        color: #0f172a;
    }

    /* Table */
    .sync-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12.5px;
    }

    .sync-table th {
        background: #f8fafc;
        color: #475569;
        font-weight: 600;
        text-align: left;
        padding: 10px 14px;
        border-bottom: 2px solid #e2e8f0;
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        white-space: nowrap;
    }

    .sync-table td {
        padding: 10px 14px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        color: #334155;
    }

    .sync-table tr:hover td {
        background: #f8fafc;
    }

    .table-row-processing td {
        background: #f0fdf4 !important;
    }

    .badge-sync {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
    }

    .badge-sync.synced {
        background: #dcfce7;
        color: #15803d;
    }

    .badge-sync.pending {
        background: #fef3c7;
        color: #b45309;
    }

    .badge-sync.failed {
        background: #fee2e2;
        color: #b91c1c;
    }

    .btn-table-drive {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        color: #1e293b;
        font-size: 11px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 4px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .btn-table-drive:hover {
        background: #e2e8f0;
        color: #15803d;
    }

    /* Minimal Log Accordion */
    .log-accordion-toggle {
        font-size: 12px;
        color: #64748b;
        background: none;
        border: none;
        cursor: pointer;
        padding: 8px 0 0;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .log-accordion-toggle:hover {
        color: #1e293b;
    }

    .log-container {
        display: none;
        background: #0f172a;
        color: #e2e8f0;
        border-radius: 6px;
        padding: 12px 14px;
        font-family: 'Consolas', monospace;
        font-size: 11.5px;
        max-height: 180px;
        overflow-y: auto;
        margin-top: 10px;
        line-height: 1.5;
    }

    .log-line {
        margin-bottom: 2px;
    }
    .log-time { color: #64748b; }
    .log-skip { color: #38bdf8; }
    .log-success { color: #4ade80; }
    .log-fail { color: #f87171; }
</style>
@endpush

@section('content')
<div class="sync-container">

    {{-- HEADER HALAMAN --}}
    <div class="page-header">
        <div class="page-header-title">
            <h2>
                <i class="fab fa-google-drive"></i>
                <span>Google Drive Sync</span>
            </h2>
            <p>Sinkronisasi berkas dokumentasi foto kegiatan Perhutanan Sosial ke Google Drive secara otomatis.</p>
        </div>

        <div class="page-header-actions">
            @if($isDriveConnected ?? false)
                <span class="status-pill connected">
                    <i class="fas fa-check-circle"></i> Google Drive Terhubung
                </span>
            @else
                <span class="status-pill disconnected">
                    <i class="fas fa-exclamation-circle"></i> Belum Terhubung
                </span>
            @endif

            <a href="{{ $driveFolderUrl ?? config('services.google_drive.folder_url') }}" target="_blank" rel="noopener noreferrer" class="btn-open-drive" title="Buka Google Drive">
                <i class="fas fa-external-link-alt"></i> Buka Drive
            </a>
        </div>
    </div>

    {{-- KARTU UTAMA --}}
    <div class="sync-card">

        {{-- 1. RINGKASAN STATISTIK --}}
        <div class="stats-strip">
            <div class="stat-item">
                <span>Total:</span>
                <strong id="countTotal">{{ $stats['total'] }}</strong>
                <span>Foto</span>
            </div>
            <div class="stat-item synced">
                <span>Tersinkron:</span>
                <strong id="countSynced">{{ $stats['synced'] }}</strong>
            </div>
            <div class="stat-item pending">
                <span>Menunggu:</span>
                <strong id="countPending">{{ $stats['pending'] }}</strong>
            </div>
            <div class="stat-item failed">
                <span>Gagal:</span>
                <strong id="countFailed">{{ $stats['failed'] }}</strong>
            </div>
        </div>

        {{-- 2. TOOLBAR: FILTER & KONTROL SINKRONISASI --}}
        <div class="toolbar-row">
            {{-- Filter Form --}}
            <form method="GET" action="{{ route('admin.google-drive-sync') }}" class="filter-form" id="formFilterKegiatan">
                <input type="text" name="q" id="inputSearch" value="{{ $search ?? '' }}" placeholder="Cari foto, kegiatan, kelompok..." class="filter-search-input">

                <select name="id_cdk" id="selectCdk" class="filter-select" title="Filter CDK Wilayah">
                    <option value="">Semua CDK Wilayah</option>
                    @foreach($cdkList as $cdk)
                        <option value="{{ $cdk->id }}" {{ (string)$selectedCdk === (string)$cdk->id ? 'selected' : '' }}>
                            {{ $cdk->nama }}
                        </option>
                    @endforeach
                </select>

                <select name="id_kelompok" id="selectKelompok" class="filter-select" title="Filter Kelompok">
                    <option value="">Semua Kelompok</option>
                    @foreach($kelompokList as $kel)
                        <option value="{{ $kel->id }}" data-cdk="{{ $kel->id_cdk }}" {{ (string)$selectedKelompok === (string)$kel->id ? 'selected' : '' }}>
                            {{ $kel->nama_kelompok }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn-filter">
                    <i class="fas fa-search"></i> Cari
                </button>

                <a href="{{ route('admin.google-drive-sync') }}" class="btn-reset" id="btnResetFilter" title="Reset filter ke awal" style="{{ (!empty($search) || !empty($selectedCdk) || !empty($selectedKelompok)) ? '' : 'display: none;' }}">
                    Reset
                </a>
            </form>

            {{-- Tombol Aksi Sinkronisasi --}}
            <div class="sync-controls">
                <button type="button" class="btn-start-sync" id="btnStart">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <span>Mulai Sinkronisasi</span>
                </button>
            </div>
        </div>

        {{-- 3. PROGRESS SLIM BANNER (Hanya tampil saat proses berlangsung) --}}
        <div class="progress-banner" id="progressWrapper">
            <div class="progress-banner-meta">
                <span id="statusText" style="font-weight: 600; color: #1e293b;">Menyiapkan...</span>
                <span id="progressRatio" style="font-family: monospace; font-weight: 700; color: #16a34a;">0 / {{ $stats['total'] }}</span>
            </div>
            <div class="progress-bar-track">
                <div class="progress-bar-fill" id="progressBar"></div>
            </div>
            <div class="progress-banner-file">
                <span>Berkas saat ini:</span>
                <strong id="fotoText">-</strong>
            </div>
        </div>

        {{-- 4. TABEL DAFTAR FOTO DOKUMENTASI --}}
        <div style="overflow-x: auto;">
            <table class="sync-table" id="tabelDokumentasi">
                <thead>
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th>Foto / Berkas</th>
                        <th>Kelompok &amp; Kegiatan</th>
                        <th>Folder Google Drive</th>
                        <th style="width: 130px;">Status</th>
                        <th style="width: 80px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tabelDokumentasiBody">
                    @forelse($dokumentasiList as $idx => $doc)
                        @php
                            $kelName = $doc->kegiatan && $doc->kegiatan->kelompok ? $doc->kegiatan->kelompok->nama_kelompok : '-';
                            $cdkName = $doc->kegiatan && $doc->kegiatan->kelompok && $doc->kegiatan->kelompok->cdk ? $doc->kegiatan->kelompok->cdk->nama : 'CDK Wilayah';
                            $cdkId = $doc->kegiatan && $doc->kegiatan->kelompok ? (string)$doc->kegiatan->kelompok->id_cdk : '';
                            $kelId = $doc->kegiatan ? (string)$doc->kegiatan->id_kelompok : '';
                            $kegId = (string)($doc->id_kegiatan ?? '');
                            $kegName = $doc->kegiatan ? $doc->kegiatan->nama_kegiatan : '-';
                            $filename = $doc->nama_file ?: basename($doc->file_path ?? 'foto.jpg');
                            $isSynced = in_array($doc->google_drive_status, ['success', 'synced']);
                            $isError = in_array($doc->google_drive_status, ['error', 'failed', 'gagal']);
                            $statusSlug = $isSynced ? 'synced' : ($isError ? 'failed' : 'pending');
                            $searchData = strtolower(implode(' ', array_filter([
                                $filename,
                                $kegName,
                                $kelName,
                                $cdkName,
                                $doc->keterangan,
                                $doc->judul,
                                $doc->kegiatan?->lokasi,
                                $doc->kegiatan?->kelompok?->desa,
                                $doc->kegiatan?->kelompok?->kecamatan,
                                $doc->kegiatan?->kelompok?->kabupaten,
                            ])));
                        @endphp
                        <tr id="rowDoc-{{ $doc->id }}" 
                            class="doc-table-row" 
                            data-id="{{ $doc->id }}"
                            data-cdk="{{ $cdkId }}"
                            data-kelompok="{{ $kelId }}"
                            data-kegiatan="{{ $kegId }}"
                            data-status="{{ $statusSlug }}"
                            data-search="{{ $searchData }}">
                            <td class="row-num">{{ $idx + 1 }}</td>
                            <td>
                                <div style="font-weight: 600; color: #0f172a;">
                                    <i class="fas fa-image" style="color: #94a3b8; margin-right: 4px;"></i>
                                    {{ $filename }}
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #1e293b;">{{ $kelName }}</div>
                                <div style="font-size: 11.5px; color: #64748b;">{{ $kegName }}</div>
                            </td>
                            <td>
                                <span style="font-size: 11px; background: #f1f5f9; padding: 2px 6px; border-radius: 4px; color: #475569;">
                                    CDK / {{ $cdkName }} / {{ $kelName }} / {{ $kegName }} / Foto
                                </span>
                            </td>
                            <td id="statusCell-{{ $doc->id }}">
                                @if($isSynced)
                                    <span class="badge-sync synced"><i class="fas fa-check"></i> Tersinkron</span>
                                @elseif($isError)
                                    <span class="badge-sync failed" title="{{ $doc->google_drive_error }}"><i class="fas fa-times"></i> Gagal</span>
                                @else
                                    <span class="badge-sync pending"><i class="fas fa-clock"></i> Menunggu</span>
                                @endif
                            </td>
                            <td style="text-align: center;" id="actionCell-{{ $doc->id }}">
                                @if($doc->google_drive_file_id)
                                    <a href="https://drive.google.com/file/d/{{ $doc->google_drive_file_id }}/view" target="_blank" rel="noopener noreferrer" class="btn-table-drive">
                                        <i class="fab fa-google-drive" style="color: #16a34a;"></i> Lihat
                                    </a>
                                @else
                                    <span style="color: #cbd5e1; font-size: 11px;">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr id="rowEmptyInit">
                            <td colspan="6" style="text-align: center; padding: 36px; color: #94a3b8;">
                                <i class="fas fa-folder-open" style="font-size: 24px; margin-bottom: 8px; display: block; opacity: 0.5;"></i>
                                Tidak ada foto dokumentasi ditemukan.
                            </td>
                        </tr>
                    @endforelse
                    <tr id="rowEmptySearch" style="display: none;">
                        <td colspan="6" style="text-align: center; padding: 36px; color: #94a3b8;">
                            <i class="fas fa-search" style="font-size: 24px; margin-bottom: 8px; display: block; opacity: 0.5;"></i>
                            Tidak ada foto dokumentasi yang cocok dengan filter yang dipilih.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- 5. LOG RINGKAS (COLLAPSIBLE) --}}
        <div style="margin-top: 14px; border-top: 1px solid #f1f5f9; padding-top: 10px;">
            <button type="button" class="log-accordion-toggle" id="btnToggleLog">
                <i class="fas fa-terminal"></i>
                <span id="toggleLogText">Lihat Log Aktivitas</span>
            </button>
            <div class="log-container" id="logConsole">
                <div class="log-line"><span class="log-time">[{{ date('H.i.s') }}]</span> Sistem siap.</div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputSearch = document.getElementById('inputSearch');
    const selectCdk = document.getElementById('selectCdk');
    const selectKelompok = document.getElementById('selectKelompok');
    const btnResetFilter = document.getElementById('btnResetFilter');

    const countTotalEl = document.getElementById('countTotal');
    const countSyncedEl = document.getElementById('countSynced');
    const countPendingEl = document.getElementById('countPending');
    const countFailedEl = document.getElementById('countFailed');

    const btnStart = document.getElementById('btnStart');
    const progressWrapper = document.getElementById('progressWrapper');
    const progressBar = document.getElementById('progressBar');
    const progressRatio = document.getElementById('progressRatio');
    const statusText = document.getElementById('statusText');
    const fotoText = document.getElementById('fotoText');
    const btnToggleLog = document.getElementById('btnToggleLog');
    const logConsole = document.getElementById('logConsole');
    const toggleLogText = document.getElementById('toggleLogText');

    let isRunning = false;
    let items = [];
    let currentIndex = 0;
    let totalItems = 0;

    let syncedCount = parseInt(countSyncedEl ? countSyncedEl.textContent : 0) || 0;
    let pendingCount = parseInt(countPendingEl ? countPendingEl.textContent : 0) || 0;
    let failedCount = parseInt(countFailedEl ? countFailedEl.textContent : 0) || 0;

    function getNowTime() {
        const now = new Date();
        const hh = String(now.getHours()).padStart(2, '0');
        const mm = String(now.getMinutes()).padStart(2, '0');
        const ss = String(now.getSeconds()).padStart(2, '0');
        return `${hh}.${mm}.${ss}`;
    }

    function appendLog(text, type = '') {
        if (!logConsole) return;
        const div = document.createElement('div');
        div.className = 'log-line';
        
        let typeClass = '';
        if (type === 'skip') typeClass = 'log-skip';
        else if (type === 'success') typeClass = 'log-success';
        else if (type === 'fail') typeClass = 'log-fail';

        div.innerHTML = `<span class="log-time">[${getNowTime()}]</span> <span class="${typeClass}">${text}</span>`;
        logConsole.appendChild(div);
        logConsole.scrollTop = logConsole.scrollHeight;
    }

    const tableRows = Array.from(document.querySelectorAll('#tabelDokumentasiBody tr.doc-table-row'));
    const rowEmptySearch = document.getElementById('rowEmptySearch');
    const rowEmptyInit = document.getElementById('rowEmptyInit');

    // 1. CASCADING FILTER DROPDOWNS (CDK -> KELOMPOK)
    if (selectCdk && selectKelompok) {
        const allKelompokData = Array.from(selectKelompok.options).slice(1).map(opt => ({
            value: opt.value,
            text: opt.textContent,
            cdkId: opt.getAttribute('data-cdk') || ''
        }));

        function updateKelompokOptions(preserveCurrent = true) {
            const selectedCdk = selectCdk.value;
            const currentKelVal = preserveCurrent ? selectKelompok.value : '';

            selectKelompok.innerHTML = '<option value="">Semua Kelompok</option>';
            let kelFound = false;

            allKelompokData.forEach(item => {
                if (!selectedCdk || item.cdkId === selectedCdk) {
                    const opt = document.createElement('option');
                    opt.value = item.value;
                    opt.textContent = item.text;
                    opt.setAttribute('data-cdk', item.cdkId);
                    if (currentKelVal && item.value === currentKelVal) {
                        opt.selected = true;
                        kelFound = true;
                    }
                    selectKelompok.appendChild(opt);
                }
            });

            if (!kelFound && currentKelVal) {
                selectKelompok.value = '';
            }
        }

        selectCdk.addEventListener('change', function () {
            updateKelompokOptions(false);
            applyFilters();
        });

        selectKelompok.addEventListener('change', function () {
            if (selectKelompok.value && !selectCdk.value) {
                const matchedKel = allKelompokData.find(k => k.value === selectKelompok.value);
                if (matchedKel && matchedKel.cdkId) {
                    selectCdk.value = matchedKel.cdkId;
                }
            }
            applyFilters();
        });

        // Inisialisasi cascading saat load pertama
        if (selectCdk.value) {
            updateKelompokOptions(true);
        }
    }

    // 2. LIVE REACTIVE FILTERING
    function applyFilters() {
        const query = (inputSearch?.value || '').toLowerCase().trim();
        const searchTerms = query ? query.split(/\s+/) : [];
        const cdkVal = selectCdk?.value || '';
        const kelVal = selectKelompok?.value || '';

        const hasActiveFilter = (query !== '' || cdkVal !== '' || kelVal !== '');
        if (btnResetFilter) {
            btnResetFilter.style.display = hasActiveFilter ? 'inline-flex' : 'none';
        }

        let visibleCount = 0;
        let visibleSynced = 0;
        let visiblePending = 0;
        let visibleFailed = 0;

        tableRows.forEach(row => {
            const rowSearch = (row.getAttribute('data-search') || '').toLowerCase();
            const rowCdk = row.getAttribute('data-cdk') || '';
            const rowKel = row.getAttribute('data-kelompok') || '';
            const rowStatus = row.getAttribute('data-status') || '';

            const matchSearch = (searchTerms.length === 0) || searchTerms.every(term => rowSearch.includes(term));
            const matchCdk = !cdkVal || rowCdk === cdkVal;
            const matchKel = !kelVal || rowKel === kelVal;

            const isMatch = matchSearch && matchCdk && matchKel;

            if (isMatch) {
                row.style.display = '';
                visibleCount++;
                const rowNumEl = row.querySelector('.row-num');
                if (rowNumEl) rowNumEl.textContent = visibleCount;

                if (rowStatus === 'synced') visibleSynced++;
                else if (rowStatus === 'failed') visibleFailed++;
                else visiblePending++;
            } else {
                row.style.display = 'none';
            }
        });

        // Update Counter Stats
        if (countTotalEl) countTotalEl.textContent = visibleCount;
        if (countSyncedEl) countSyncedEl.textContent = visibleSynced;
        if (countPendingEl) countPendingEl.textContent = visiblePending;
        if (countFailedEl) countFailedEl.textContent = visibleFailed;

        // Tampilkan atau sembunyikan pesan kosong
        if (rowEmptySearch) {
            rowEmptySearch.style.display = (visibleCount === 0 && tableRows.length > 0) ? '' : 'none';
        }
        if (rowEmptyInit) {
            rowEmptyInit.style.display = (tableRows.length === 0) ? '' : 'none';
        }

        // Update tombol Mulai Sinkronisasi
        if (btnStart && !isRunning) {
            btnStart.disabled = (visibleCount === 0);
        }
    }

    if (inputSearch) {
        inputSearch.addEventListener('input', applyFilters);
        inputSearch.addEventListener('keyup', applyFilters);
    }

    // Tombol Reset Filter
    if (btnResetFilter) {
        btnResetFilter.addEventListener('click', function (e) {
            e.preventDefault();
            if (inputSearch) inputSearch.value = '';
            if (selectCdk) selectCdk.value = '';
            if (selectKelompok) selectKelompok.value = '';

            if (selectCdk && selectKelompok) {
                const allKelompokData = Array.from(selectKelompok.options).slice(1).map(opt => ({
                    value: opt.value,
                    text: opt.textContent,
                    cdkId: opt.getAttribute('data-cdk') || ''
                }));
                selectKelompok.innerHTML = '<option value="">Semua Kelompok</option>';
                allKelompokData.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value = item.value;
                    opt.textContent = item.text;
                    opt.setAttribute('data-cdk', item.cdkId);
                    selectKelompok.appendChild(opt);
                });
            }

            applyFilters();

            if (window.history && window.history.replaceState) {
                window.history.replaceState(null, document.title, window.location.pathname);
            }
            appendLog('Filter dibersihkan. Menampilkan semua berkas.');
        });
    }

    // Jalankan filter awal saat DOM selesai
    applyFilters();

    // 3. TOGGLE LOG
    if (btnToggleLog && logConsole) {
        btnToggleLog.addEventListener('click', function () {
            const isShown = logConsole.style.display === 'block';
            logConsole.style.display = isShown ? 'none' : 'block';
            if (toggleLogText) {
                toggleLogText.textContent = isShown ? 'Lihat Log Aktivitas' : 'Tutup Log Aktivitas';
            }
        });
    }

    // 4. WORKER SINKRONISASI REAL-TIME
    const csrfToken = document.querySelector('meta[name="csrf-token"]') ? 
        document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';

    btnStart.addEventListener('click', async function () {
        if (isRunning) return;

        btnStart.disabled = true;
        isRunning = true;

        if (progressWrapper) progressWrapper.style.display = 'block';

        statusText.textContent = 'Menyiapkan daftar foto...';
        appendLog('Mengambil daftar foto sesuai filter yang aktif...');

        // Ambil nilai filter LANGSUNG dari elemen saat tombol diklik
        const currentQ = (inputSearch?.value || '').trim();
        const currentCdk = selectCdk?.value || '';
        const currentKel = selectKelompok?.value || '';

        const queryParams = new URLSearchParams({
            q: currentQ,
            id_cdk: currentCdk,
            id_kelompok: currentKel,
        });

        try {
            const res = await fetch(`{{ route('admin.google-drive-sync.items') }}?${queryParams.toString()}`);
            const data = await res.json();

            if (!data.success || !data.items) {
                appendLog('Gagal mengambil daftar foto: ' + (data.message || 'Error'), 'fail');
                statusText.textContent = 'Gagal memuat data foto.';
                btnStart.disabled = false;
                isRunning = false;
                return;
            }

            items = data.items;
            totalItems = items.length;
            currentIndex = 0;

            if (totalItems === 0) {
                appendLog('Tidak ada foto yang perlu disinkronkan untuk filter ini.');
                statusText.textContent = 'Tidak ada foto untuk disinkronkan.';
                progressRatio.textContent = '0 / 0';
                progressBar.style.width = '100%';
                btnStart.disabled = false;
                isRunning = false;
                return;
            }

            appendLog(`Ditemukan ${totalItems} foto sesuai kriteria filter. Memulai sinkronisasi ke Google Drive...`);
            processNextItem();

        } catch (err) {
            appendLog('Terjadi kesalahan jaringan: ' + err.message, 'fail');
            statusText.textContent = 'Error koneksi jaringan.';
            btnStart.disabled = false;
            isRunning = false;
        }
    });

    async function processNextItem() {
        if (!isRunning) return;

        if (currentIndex >= totalItems) {
            isRunning = false;
            btnStart.disabled = false;
            statusText.textContent = 'Sinkronisasi selesai!';
            fotoText.textContent = 'Semua foto selesai diperiksa';
            progressBar.style.width = '100%';
            progressRatio.textContent = `${totalItems} / ${totalItems}`;
            appendLog(`Selesai! Seluruh ${totalItems} foto telah selesai diproses.`, 'success');
            return;
        }

        const item = items[currentIndex];
        const itemNumber = currentIndex + 1;

        statusText.textContent = `Memproses foto ${itemNumber} dari ${totalItems}...`;
        fotoText.textContent = item.nama_file;
        progressRatio.textContent = `${itemNumber} / ${totalItems}`;
        const pct = Math.round((itemNumber / totalItems) * 100);
        progressBar.style.width = `${pct}%`;

        const tableRow = document.getElementById(`rowDoc-${item.id}`);
        if (tableRow) {
            tableRow.classList.add('table-row-processing');
        }

        try {
            const response = await fetch(`{{ route('admin.google-drive-sync.process-item') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ id: item.id })
            });

            const result = await response.json();

            const statusCell = document.getElementById(`statusCell-${item.id}`);
            const actionCell = document.getElementById(`actionCell-${item.id}`);

            if (result.status === 'skipped') {
                if (item.google_drive_status !== 'success') {
                    syncedCount++;
                    if (pendingCount > 0) pendingCount--;
                }
                appendLog(`[${itemNumber}/${totalItems}] SKIP: ${item.nama_file} (${result.message})`, 'skip');

                if (statusCell) {
                    statusCell.innerHTML = '<span class="badge-sync synced"><i class="fas fa-check"></i> Tersinkron</span>';
                }
                if (tableRow) {
                    tableRow.setAttribute('data-status', 'synced');
                }
            } else if (result.status === 'uploaded') {
                syncedCount++;
                if (pendingCount > 0) pendingCount--;
                if (item.google_drive_status === 'error' && failedCount > 0) failedCount--;
                appendLog(`[${itemNumber}/${totalItems}] BERHASIL: ${item.nama_file} terunggah. ID: ${result.file_id}`, 'success');

                if (statusCell) {
                    statusCell.innerHTML = '<span class="badge-sync synced"><i class="fas fa-check"></i> Tersinkron</span>';
                }
                if (actionCell && result.file_id) {
                    actionCell.innerHTML = `<a href="https://drive.google.com/file/d/${result.file_id}/view" target="_blank" rel="noopener noreferrer" class="btn-table-drive"><i class="fab fa-google-drive" style="color:#16a34a;"></i> Lihat</a>`;
                }
                if (tableRow) {
                    tableRow.setAttribute('data-status', 'synced');
                }
            } else {
                failedCount++;
                if (item.google_drive_status !== 'error' && pendingCount > 0) pendingCount--;
                appendLog(`[${itemNumber}/${totalItems}] GAGAL: ${item.nama_file} - ${result.message || 'Error'}`, 'fail');

                if (statusCell) {
                    statusCell.innerHTML = `<span class="badge-sync failed" title="${result.message}"><i class="fas fa-times"></i> Gagal</span>`;
                }
                if (tableRow) {
                    tableRow.setAttribute('data-status', 'failed');
                }
            }

            if (countSyncedEl) countSyncedEl.textContent = syncedCount;
            if (countPendingEl) countPendingEl.textContent = pendingCount;
            if (countFailedEl) countFailedEl.textContent = failedCount;

        } catch (err) {
            failedCount++;
            if (pendingCount > 0) pendingCount--;
            if (countFailedEl) countFailedEl.textContent = failedCount;
            if (countPendingEl) countPendingEl.textContent = pendingCount;
            appendLog(`[${itemNumber}/${totalItems}] ERROR: ${item.nama_file} (${err.message})`, 'fail');
        } finally {
            if (tableRow) {
                tableRow.classList.remove('table-row-processing');
            }
        }

        currentIndex++;
        setTimeout(processNextItem, 120);
    }
});
</script>
@endpush
