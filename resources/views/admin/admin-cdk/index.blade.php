@extends('layouts.app')
@section('title', 'Admin CDK & Rekap Anggota - SI PS ONLINE')

@push('styles')
<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    gap: 15px;
    flex-wrap: wrap;
}
.page-header h2 {
    color: #1b5e20;
    font-size: 22px;
    font-weight: 700;
    margin: 0 0 4px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.page-header p {
    color: #78909c;
    font-size: 13px;
    margin: 0;
}
.header-actions {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
}

/* Stat Cards Grid */
.stat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}
.cdk-stat-card {
    background: #fff;
    padding: 18px 20px;
    border-radius: 10px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    border: 1px solid #e2e8f0;
    border-left: 4px solid #2e7d32;
    display: flex;
    align-items: center;
    gap: 16px;
    transition: transform 0.2s, box-shadow 0.2s;
}
.cdk-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.cdk-stat-card .stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: #e8f5e9;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #2e7d32;
    font-size: 20px;
    flex-shrink: 0;
}
.cdk-stat-card.blue { border-left-color: #1976d2; }
.cdk-stat-card.blue .stat-icon { background: #e3f2fd; color: #1976d2; }
.cdk-stat-card.amber { border-left-color: #f57c00; }
.cdk-stat-card.amber .stat-icon { background: #fff3e0; color: #f57c00; }
.cdk-stat-card.purple { border-left-color: #7b1fa2; }
.cdk-stat-card.purple .stat-icon { background: #f3e5f5; color: #7b1fa2; }
.cdk-stat-card.teal { border-left-color: #00897b; }
.cdk-stat-card.teal .stat-icon { background: #e0f2f1; color: #00897b; }
.cdk-stat-card.indigo { border-left-color: #3949ab; }
.cdk-stat-card.indigo .stat-icon { background: #e8eaf6; color: #3949ab; }

.cdk-stat-card .stat-info h3 {
    color: #78909c;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
    margin: 0 0 4px;
}
.cdk-stat-card .stat-info .stat-value {
    color: #263238;
    font-size: 22px;
    font-weight: 700;
    line-height: 1.2;
}

/* Nav Tabs */
.tab-container {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    border: 1px solid #e2e8f0;
    overflow: hidden;
}
.tab-header {
    display: flex;
    border-bottom: 2px solid #e8f5e9;
    background: #fafafa;
    padding: 0 15px;
    gap: 10px;
    overflow-x: auto;
}
.tab-btn {
    padding: 14px 20px;
    background: transparent;
    border: none;
    border-bottom: 3px solid transparent;
    font-size: 14px;
    font-weight: 600;
    color: #607d8b;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
    white-space: nowrap;
}
.tab-btn:hover {
    color: #2e7d32;
    background: rgba(46,125,50,0.04);
}
.tab-btn.active {
    color: #1b5e20;
    border-bottom-color: #2e7d32;
    background: #fff;
}
.tab-btn .badge-count {
    background: #e8f5e9;
    color: #2e7d32;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 700;
}
.tab-btn.active .badge-count {
    background: #2e7d32;
    color: #fff;
}

.tab-pane {
    display: none;
    padding: 24px;
}
.tab-pane.active {
    display: block;
}

/* Filter & Controls */
.controls-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
    flex-wrap: wrap;
    background: #f8fafc;
    padding: 14px 18px;
    border-radius: 8px;
    border: 1px solid #edf2f7;
}
.filter-inputs {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    align-items: center;
}
.filter-inputs input, .filter-inputs select {
    height: 38px;
    padding: 6px 12px;
    font-size: 13px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    background: #fff;
    color: #334155;
    transition: border-color 0.2s;
}
.filter-inputs input:focus, .filter-inputs select:focus {
    border-color: #2e7d32;
    outline: none;
    box-shadow: 0 0 0 3px rgba(46,125,50,0.15);
}
.filter-inputs input {
    min-width: 220px;
}

/* Badges & Tags */
.badge-cdk {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    background: #e8f5e9;
    color: #1b5e20;
    border: 1px solid #c8e6c9;
}
.badge-admin {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 12px;
    background: #e3f2fd;
    color: #1565c0;
    font-weight: 500;
}
.badge-gender {
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}
.badge-gender.l { background: #e3f2fd; color: #1976d2; }
.badge-gender.p { background: #fce4ec; color: #c2185b; }

/* Buttons */
.btn-primary-custom {
    background: #2e7d32;
    color: #fff;
    border: none;
    padding: 9px 16px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: background 0.2s;
}
.btn-primary-custom:hover {
    background: #1b5e20;
    color: #fff;
}
.btn-outline-custom {
    background: #fff;
    color: #37474f;
    border: 1px solid #cfd8dc;
    padding: 9px 16px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: all 0.2s;
}
.btn-outline-custom:hover {
    background: #eceff1;
    border-color: #b0bec5;
}

.btn-view-member {
    background: #e8f5e9;
    color: #2e7d32;
    border: 1px solid #c8e6c9;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.2s;
}
.btn-view-member:hover {
    background: #2e7d32;
    color: #fff;
}

/* Modal Styling */
.modal-backdrop-custom {
    display: none;
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.modal-backdrop-custom.show {
    display: flex;
}
.modal-box-custom {
    background: #fff;
    border-radius: 12px;
    max-width: 520px;
    width: 100%;
    box-shadow: 0 10px 30px rgba(0,0,0,0.25);
    overflow: hidden;
    animation: modalIn 0.25s ease-out;
}
@keyframes modalIn {
    from { opacity: 0; transform: translateY(-20px) scale(0.95); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
.modal-header-custom {
    padding: 16px 20px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.modal-header-custom h3 {
    margin: 0;
    font-size: 16px;
    color: #1e293b;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 8px;
}
.modal-close-btn {
    background: transparent;
    border: none;
    font-size: 18px;
    color: #64748b;
    cursor: pointer;
    padding: 4px 8px;
    border-radius: 4px;
}
.modal-close-btn:hover {
    background: #e2e8f0;
    color: #0f172a;
}
.modal-body-custom {
    padding: 20px;
}
.form-group-custom {
    margin-bottom: 15px;
}
.form-group-custom label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #475569;
    margin-bottom: 6px;
}
.form-group-custom input, .form-group-custom select {
    width: 100%;
    height: 38px;
    padding: 6px 12px;
    font-size: 13px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    box-sizing: border-box;
}
.form-group-custom input:focus, .form-group-custom select:focus {
    border-color: #2e7d32;
    outline: none;
    box-shadow: 0 0 0 3px rgba(46,125,50,0.15);
}

/* Print Rules */
@media print {
    .page-header p, .header-actions, .tab-header, .controls-bar, .modal-backdrop-custom, .btn, .btn-primary-custom, .btn-outline-custom, .btn-view-member, .btn-edit, .btn-hapus {
        display: none !important;
    }
    .tab-pane {
        display: block !important;
        padding: 0 !important;
    }
    .tab-pane:not(.active) {
        display: none !important;
    }
    .stat-grid {
        grid-template-columns: repeat(3, 1fr) !important;
    }
    .tab-container {
        border: none !important;
        box-shadow: none !important;
    }
    table {
        width: 100% !important;
        border-collapse: collapse !important;
    }
    th, td {
        border: 1px solid #333 !important;
        padding: 6px 8px !important;
        font-size: 11px !important;
    }
    th {
        background: #eee !important;
        color: #000 !important;
    }
}
</style>
@endpush

@section('content')
<div class="content">

    {{-- HEADER --}}
    <div class="page-header">
        <div>
            <h2><i class="fas fa-building-user" style="color:#2e7d32"></i> Admin CDK &amp; Rekapitulasi Anggota</h2>
            <p>Manajemen akun Admin CDK, rekapitulasi data wilayah per CDK, dan daftar seluruh anggota kelompok binaan.</p>
        </div>
        <div class="header-actions">
            <button type="button" class="btn-outline-custom" onclick="window.print()">
                <i class="fas fa-print"></i> Cetak Rekap
            </button>
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="stat-grid">
        <div class="cdk-stat-card">
            <div class="stat-icon"><i class="fas fa-user-shield"></i></div>
            <div class="stat-info">
                <h3>Total Admin CDK</h3>
                <div class="stat-value">{{ number_format($totalAdminCdk) }} <span style="font-size:12px;color:#78909c;font-weight:400">Akun</span></div>
            </div>
        </div>

        <div class="cdk-stat-card blue">
            <div class="stat-icon"><i class="fas fa-sitemap"></i></div>
            <div class="stat-info">
                <h3>Wilayah CDK</h3>
                <div class="stat-value">{{ number_format($totalCdk) }} <span style="font-size:12px;color:#78909c;font-weight:400">Wilayah</span></div>
            </div>
        </div>

        <div class="cdk-stat-card amber">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-info">
                <h3>Kelompok Binaan</h3>
                <div class="stat-value">{{ number_format($totalKelompok) }} <span style="font-size:12px;color:#78909c;font-weight:400">KUPS/KTH</span></div>
            </div>
        </div>

        <div class="cdk-stat-card purple">
            <div class="stat-icon"><i class="fas fa-user-friends"></i></div>
            <div class="stat-info">
                <h3>Anggota Kelompok</h3>
                <div class="stat-value">{{ number_format($totalAnggota) }} <span style="font-size:12px;color:#78909c;font-weight:400">Orang</span></div>
            </div>
        </div>

        <div class="cdk-stat-card teal">
            <div class="stat-icon"><i class="fas fa-user-tie"></i></div>
            <div class="stat-info">
                <h3>Pendamping</h3>
                <div class="stat-value">{{ number_format($totalPendamping) }} <span style="font-size:12px;color:#78909c;font-weight:400">Petugas</span></div>
            </div>
        </div>

        <div class="cdk-stat-card indigo">
            <div class="stat-icon"><i class="fas fa-chart-area"></i></div>
            <div class="stat-info">
                <h3>Total Luas Lahan</h3>
                <div class="stat-value">{{ number_format($totalLuasLahan, 0, ',', '.') }} <span style="font-size:12px;color:#78909c;font-weight:400">Ha</span></div>
            </div>
        </div>
    </div>

    {{-- MAIN TAB CONTAINER --}}
    <div class="tab-container">
        <div class="tab-header">
            <button type="button" class="tab-btn active" onclick="switchTab('tab-rekap', this)">
                <i class="fas fa-table-list"></i> Rekapitulasi per CDK
                <span class="badge-count">{{ $rekapCdk->count() }}</span>
            </button>
            <button type="button" class="tab-btn" onclick="switchTab('tab-anggota', this)" id="btnTabAnggota">
                <i class="fas fa-user-friends"></i> Data Anggota CDK
                <span class="badge-count" id="badgeTotalAnggota">{{ $daftarAnggota->count() }}</span>
            </button>
            <button type="button" class="tab-btn" onclick="switchTab('tab-admin', this)">
                <i class="fas fa-id-badge"></i> Akun Admin CDK
                <span class="badge-count">{{ $users->count() }}</span>
            </button>
        </div>

        {{-- TAB 1: REKAPITULASI PER WILAYAH CDK --}}
        <div class="tab-pane active" id="tab-rekap">
            <div class="controls-bar">
                <div class="filter-inputs">
                    <input type="text" id="searchRekap" placeholder="Cari nama CDK / Petugas..." onkeyup="filterRekapTable()">
                </div>
                <div style="font-size:13px;color:#64748b">
                    <i class="fas fa-info-circle" style="color:#2e7d32"></i> Klik <strong>Lihat Anggota</strong> pada baris wilayah untuk menyaring data anggota.
                </div>
            </div>

            <div class="table-wrap">
                <table id="tableRekapCdk">
                    <thead>
                        <tr>
                            <th style="width:45px;text-align:center">No</th>
                            <th style="text-align:left">Wilayah CDK</th>
                            <th style="text-align:left">Petugas Admin CDK</th>
                            <th style="text-align:center">Kelompok</th>
                            <th style="text-align:center">Anggota</th>
                            <th style="text-align:center">Pendamping</th>
                            <th style="text-align:right">Luas Lahan (Ha)</th>
                            <th style="width:130px;text-align:center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekapCdk as $idx => $r)
                        <tr>
                            <td style="text-align:center;font-weight:600;color:#64748b">{{ $idx + 1 }}</td>
                            <td>
                                <span class="badge-cdk">{{ $r->kode }}</span>
                                <strong style="color:#1e293b;margin-left:6px">{{ $r->nama }}</strong>
                            </td>
                            <td>
                                @if($r->jumlah_admin > 0)
                                    <span class="badge-admin"><i class="fas fa-user-check"></i> {{ $r->admin_names }}</span>
                                @else
                                    <span style="color:#94a3b8;font-size:12px;font-style:italic">Belum ada akun</span>
                                @endif
                            </td>
                            <td style="text-align:center;font-weight:600">{{ number_format($r->jumlah_kelompok) }}</td>
                            <td style="text-align:center">
                                <span style="display:inline-block;padding:2px 8px;border-radius:10px;background:#e8f5e9;color:#1b5e20;font-weight:700">
                                    {{ number_format($r->jumlah_anggota) }}
                                </span>
                            </td>
                            <td style="text-align:center;font-weight:600">{{ number_format($r->jumlah_pendamping) }}</td>
                            <td style="text-align:right;font-weight:600">{{ number_format($r->total_luas_lahan, 2, ',', '.') }} ha</td>
                            <td style="text-align:center">
                                <button type="button" class="btn-view-member" onclick="filterByCdk('{{ $r->nama }}')">
                                    <i class="fas fa-users-viewfinder"></i> Lihat Anggota
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" style="text-align:center;color:#94a3b8;padding:24px">Data CDK belum tersedia.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TAB 2: DATA ANGGOTA DI BAWAH CDK --}}
        <div class="tab-pane" id="tab-anggota">
            <div class="controls-bar">
                <div class="filter-inputs">
                    <input type="text" id="searchAnggota" placeholder="Cari nama anggota / NIK / desa..." onkeyup="filterAnggotaTable()">
                    
                    <select id="filterCdkAnggota" onchange="filterAnggotaTable()">
                        <option value="">Semua Wilayah CDK</option>
                        @foreach($daftarCdk as $c)
                        <option value="{{ $c->nama }}">{{ $c->nama }}</option>
                        @endforeach
                    </select>

                    <select id="filterKelompokAnggota" onchange="filterAnggotaTable()">
                        <option value="">Semua Kelompok</option>
                        @foreach($daftarKelompok as $k)
                        <option value="{{ $k->nama_kelompok }}">{{ $k->nama_kelompok }}</option>
                        @endforeach
                    </select>

                    <select id="filterGenderAnggota" onchange="filterAnggotaTable()">
                        <option value="">Semua Gender</option>
                        <option value="Laki-laki">Laki-laki (L)</option>
                        <option value="Perempuan">Perempuan (P)</option>
                    </select>

                    <button type="button" class="btn-outline-custom" style="padding:6px 12px;height:38px" onclick="resetFilterAnggota()">
                        <i class="fas fa-rotate-left"></i> Reset
                    </button>
                </div>
                <div style="font-size:13px;color:#64748b">
                    Menampilkan <strong id="countFilteredAnggota">{{ $daftarAnggota->count() }}</strong> anggota
                </div>
            </div>

            <div class="table-wrap">
                <table id="tableAnggotaCdk">
                    <thead>
                        <tr>
                            <th style="width:40px;text-align:center">No</th>
                            <th style="text-align:left">Nama Anggota</th>
                            <th style="text-align:left">NIK</th>
                            <th style="text-align:center">L/P</th>
                            <th style="text-align:left">Wilayah CDK</th>
                            <th style="text-align:left">Kelompok</th>
                            <th style="text-align:left">Desa / Kecamatan</th>
                            <th style="text-align:center">Luas Garapan</th>
                            <th style="text-align:left">Komoditas Utama</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($daftarAnggota as $idx => $a)
                        <tr>
                            <td style="text-align:center;color:#64748b">{{ $idx + 1 }}</td>
                            <td><strong style="color:#1e293b">{{ $a->nama_anggota ?? '-' }}</strong></td>
                            <td style="font-family:monospace;font-size:12px;color:#475569">{{ $a->nik ?? '-' }}</td>
                            <td style="text-align:center">
                                @if(stripos($a->jenis_kelamin, 'P') === 0 || stripos($a->jenis_kelamin, 'Wanita') !== false || stripos($a->jenis_kelamin, 'Perempuan') !== false)
                                    <span class="badge-gender p">P</span>
                                @else
                                    <span class="badge-gender l">L</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge-cdk">{{ $a->nama_cdk ?? 'CDK -' }}</span>
                            </td>
                            <td><strong>{{ $a->nama_kelompok ?? '-' }}</strong></td>
                            <td>{{ $a->desa ?? '-' }}{{ !empty($a->kecamatan) ? ', Kec. '.$a->kecamatan : '' }}</td>
                            <td style="text-align:center;font-weight:600">{{ $a->luas_garapan ? $a->luas_garapan.' ha' : '-' }}</td>
                            <td>{{ $a->komoditas_utama ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" style="text-align:center;color:#94a3b8;padding:24px">Data anggota kelompok belum tersedia.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TAB 3: AKUN ADMIN CDK --}}
        <div class="tab-pane" id="tab-admin">
            <div class="controls-bar">
                <div class="filter-inputs">
                    <input type="text" id="searchAdmin" placeholder="Cari nama / username admin..." onkeyup="filterAdminTable()">
                </div>
                <div style="font-size:13px;color:#64748b">
                    <i class="fas fa-info-circle" style="color:#2e7d32"></i> Penambahan akun Admin CDK dilakukan melalui menu <strong>Data User</strong>.
                </div>
            </div>

            <div class="table-wrap">
                <table id="tableAdminUsers">
                    <thead>
                        <tr>
                            <th style="width:45px;text-align:center">No</th>
                            <th style="text-align:left">Nama Lengkap</th>
                            <th style="text-align:left">Username</th>
                            <th style="text-align:left">Wilayah CDK Ditugaskan</th>
                            <th style="width:140px;text-align:center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $idx => $u)
                        <tr>
                            <td style="text-align:center;color:#64748b">{{ $idx + 1 }}</td>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px">
                                    <div style="width:34px;height:34px;border-radius:50%;background:#e8f5e9;display:flex;align-items:center;justify-content:center;color:#2e7d32;font-weight:700">
                                        {{ strtoupper(substr($u->nama, 0, 1)) }}
                                    </div>
                                    <div>
                                        <strong style="color:#1e293b">{{ $u->nama }}</strong>
                                        <div style="font-size:11px;color:#94a3b8">Role: Admin CDK</div>
                                    </div>
                                </div>
                            </td>
                            <td><code style="background:#f1f5f9;padding:3px 6px;border-radius:4px;color:#0f172a">{{ $u->username }}</code></td>
                            <td>
                                @if(!empty($u->nama_cdk))
                                    <span class="badge-cdk">{{ $u->nama_cdk }}</span>
                                @else
                                    <span style="color:#ef4444;font-size:12px;font-weight:600">Belum diset</span>
                                @endif
                            </td>
                            <td style="text-align:center">
                                <a href="{{ route('admin.admin-cdk.edit', $u->id) }}" class="btn-edit" title="Edit Admin CDK">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form method="POST" action="{{ route('admin.admin-cdk.destroy', $u->id) }}" style="display:inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun Admin CDK ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-hapus" title="Hapus Admin CDK">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align:center;color:#94a3b8;padding:24px">Belum ada data akun Admin CDK.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Switch Tabs
function switchTab(tabId, el) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
    
    el.classList.add('active');
    const target = document.getElementById(tabId);
    if (target) {
        target.classList.add('active');
    }
}

// Filter Rekap Table
function filterRekapTable() {
    const q = document.getElementById('searchRekap').value.toLowerCase();
    const rows = document.querySelectorAll('#tableRekapCdk tbody tr');
    rows.forEach(r => {
        const text = r.innerText.toLowerCase();
        r.style.display = text.includes(q) ? '' : 'none';
    });
}

// Filter Anggota Table
function filterAnggotaTable() {
    const q = (document.getElementById('searchAnggota').value || '').toLowerCase();
    const cdk = (document.getElementById('filterCdkAnggota').value || '').toLowerCase();
    const kelompok = (document.getElementById('filterKelompokAnggota').value || '').toLowerCase();
    const gender = (document.getElementById('filterGenderAnggota').value || '').toLowerCase();

    const rows = document.querySelectorAll('#tableAnggotaCdk tbody tr');
    let visibleCount = 0;

    rows.forEach(r => {
        if (r.cells.length < 9) return; // empty row fallback

        const nama = r.cells[1].innerText.toLowerCase();
        const nik = r.cells[2].innerText.toLowerCase();
        const gen = r.cells[3].innerText.toLowerCase();
        const cdkName = r.cells[4].innerText.toLowerCase();
        const klpName = r.cells[5].innerText.toLowerCase();
        const desa = r.cells[6].innerText.toLowerCase();
        const komoditas = r.cells[8].innerText.toLowerCase();

        const matchQ = !q || nama.includes(q) || nik.includes(q) || desa.includes(q) || komoditas.includes(q);
        const matchCdk = !cdk || cdkName.includes(cdk);
        const matchKlp = !kelompok || klpName.includes(kelompok);
        
        let matchGen = true;
        if (gender === 'laki-laki') {
            matchGen = gen.includes('l') || gen.includes('laki');
        } else if (gender === 'perempuan') {
            matchGen = gen.includes('p') || gen.includes('perempuan') || gen.includes('wanita');
        }

        if (matchQ && matchCdk && matchKlp && matchGen) {
            r.style.display = '';
            visibleCount++;
        } else {
            r.style.display = 'none';
        }
    });

    const countEl = document.getElementById('countFilteredAnggota');
    if (countEl) countEl.innerText = visibleCount;
}

function resetFilterAnggota() {
    document.getElementById('searchAnggota').value = '';
    document.getElementById('filterCdkAnggota').value = '';
    document.getElementById('filterKelompokAnggota').value = '';
    document.getElementById('filterGenderAnggota').value = '';
    filterAnggotaTable();
}

// Filter directly from Rekap table to Anggota Tab
function filterByCdk(cdkName) {
    const btnAnggota = document.getElementById('btnTabAnggota');
    if (btnAnggota) {
        switchTab('tab-anggota', btnAnggota);
    }
    const selectCdk = document.getElementById('filterCdkAnggota');
    if (selectCdk) {
        selectCdk.value = cdkName;
        filterAnggotaTable();
    }
}

// Filter Admin User Table
function filterAdminTable() {
    const q = document.getElementById('searchAdmin').value.toLowerCase();
    const rows = document.querySelectorAll('#tableAdminUsers tbody tr');
    rows.forEach(r => {
        const text = r.innerText.toLowerCase();
        r.style.display = text.includes(q) ? '' : 'none';
    });
}
</script>
@endpush
