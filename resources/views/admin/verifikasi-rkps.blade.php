@extends('layouts.app')
@section('title', 'Verifikasi Pengajuan RKPS - SI PS ONLINE')
@push('styles')
<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px; }
.page-header h2 { color: #1b5e20; font-size: 20px; font-weight: 700; margin: 0; }

.summary-chips { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 18px; }
.chip { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 18px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
.chip-warn { border-left: 4px solid #f59e0b; color: #92400e; background: #fffbeb; }
.chip-success { border-left: 4px solid #22c55e; color: #14532d; background: #f0fdf4; }
.chip-danger { border-left: 4px solid #ef4444; color: #7f1d1d; background: #fef2f2; }
.chip-blue { border-left: 4px solid #3b82f6; color: #1e3a5f; background: #eff6ff; }

.filter-bar { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin-bottom: 16px; }
.filter-bar input, .filter-bar select {
    height: 34px; padding: 4px 10px; font-size: 12px;
    border: 1px solid #cbd5e1; border-radius: 6px; background: #fff; color: #334155;
}
.filter-bar input:focus, .filter-bar select:focus { border-color: #2e7d32; outline: none; }

/* Verif Table */
.verif-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.verif-table th {
    background: #f8fafc; color: #475569; font-weight: 600;
    text-align: left; padding: 9px 12px;
    border-bottom: 2px solid #e2e8f0;
    font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px;
}
.verif-table td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.verif-table tr:hover td { background: #f8fafc; }

.status-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600;
}
.status-badge.menunggu { background: #fef3c7; color: #92400e; }
.status-badge.disetujui { background: #d1fae5; color: #065f46; }
.status-badge.ditolak { background: #fee2e2; color: #7f1d1d; }
.status-badge.revisi { background: #e0f2fe; color: #0c4a6e; }

.doc-link {
    display: inline-flex; align-items: center; gap: 5px;
    color: #1565c0; font-size: 11px; font-weight: 600;
    text-decoration: none; background: #e3f2fd; padding: 3px 8px; border-radius: 5px;
}
.doc-link:hover { background: #bbdefb; }

.aksi-form { display: flex; flex-direction: column; gap: 6px; }
.aksi-form textarea {
    width: 100%; padding: 5px 7px; font-size: 11px;
    border: 1px solid #cbd5e1; border-radius: 5px; resize: vertical;
    font-family: 'Poppins', sans-serif; min-height: 48px;
    background: #f8fafc;
}
.aksi-buttons { display: flex; gap: 4px; }
.btn-aksi {
    padding: 4px 10px; border-radius: 5px; font-size: 11px; font-weight: 600;
    border: none; cursor: pointer; transition: all 0.15s;
}
.btn-setujui { background: #d1fae5; color: #065f46; }
.btn-setujui:hover { background: #22c55e; color: #fff; }
.btn-revisi { background: #fef9c3; color: #713f12; }
.btn-revisi:hover { background: #eab308; color: #fff; }
.btn-tolak { background: #fee2e2; color: #7f1d1d; }
.btn-tolak:hover { background: #ef4444; color: #fff; }

.empty-state { text-align: center; padding: 40px; color: #94a3b8; }
.empty-state i { font-size: 36px; margin-bottom: 10px; display: block; }
</style>
@endpush

@section('content')
<div class="content">

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
    <div style="background:#d1fae5;color:#065f46;padding:10px 16px;border-radius:6px;margin-bottom:16px;font-size:13px;border-left:4px solid #22c55e;display:flex;align-items:center;gap:8px">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div style="background:#fee2e2;color:#7f1d1d;padding:10px 16px;border-radius:6px;margin-bottom:16px;font-size:13px;border-left:4px solid #ef4444;display:flex;align-items:center;gap:8px">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
    @endif

    {{-- HEADER --}}
    <div class="page-header">
        <div>
            <h2><i class="fas fa-file-signature" style="color:#f59e0b"></i> Verifikasi Pengajuan RKPS</h2>
            <p style="color:#64748b;font-size:12px;margin:4px 0 0">Rencana Kerja Pengelolaan Sumber Daya Hutan — tinjau dan beri keputusan</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" style="background:#f1f5f9;color:#475569;border:1px solid #cbd5e1;padding:7px 14px;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px">
            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>

    {{-- SUMMARY CHIPS --}}
    @php
        $totalRkps = count($pengajuanRkps);
        $menunggu = $pengajuanRkps->where('status','Menunggu')->count();
        $disetujui = $pengajuanRkps->where('status','Disetujui')->count();
        $ditolak = $pengajuanRkps->where('status','Ditolak')->count();
    @endphp
    <div class="summary-chips">
        <div class="chip chip-blue"><i class="fas fa-layer-group"></i> Total: {{ $totalRkps }} Pengajuan</div>
        <div class="chip chip-warn"><i class="fas fa-hourglass-half"></i> Menunggu: {{ $menunggu }}</div>
        <div class="chip chip-success"><i class="fas fa-check-circle"></i> Disetujui: {{ $disetujui }}</div>
        <div class="chip chip-danger"><i class="fas fa-times-circle"></i> Ditolak: {{ $ditolak }}</div>
    </div>

    {{-- FILTER --}}
    <div class="filter-bar">
        <input type="text" id="searchBox" placeholder="🔍 Cari nama kelompok...">
        <select id="filterStatus">
            <option value="">Semua Status</option>
            <option value="Menunggu">Menunggu</option>
            <option value="Disetujui">Disetujui</option>
            <option value="Ditolak">Ditolak</option>
            <option value="Revisi">Revisi</option>
        </select>
    </div>

    {{-- TABLE --}}
    <div style="background:#fff;border-radius:8px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,0.04);overflow-x:auto">
        <table class="verif-table" id="tabelVerif">
            <thead>
                <tr>
                    <th style="width:42px;text-align:center">No</th>
                    <th>Nama Kelompok</th>
                    <th style="text-align:center;width:120px">Periode RKPS</th>
                    <th style="text-align:center;width:110px">Dokumen</th>
                    <th style="text-align:center;width:110px">Status</th>
                    <th>Catatan Admin</th>
                    <th style="width:200px">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengajuanRkps as $no => $r)
                <tr data-status="{{ $r->status }}">
                    <td style="text-align:center;color:#64748b">{{ $no + 1 }}</td>
                    <td><strong style="color:#1e293b">{{ $r->nama_kelompok ?? '-' }}</strong></td>
                    <td style="text-align:center">{{ $r->periode ?? '-' }}</td>
                    <td style="text-align:center">
                        @if(!empty($r->file_rkps))
                        <a href="{{ asset('uploads/' . $r->file_rkps) }}" target="_blank" class="doc-link">
                            <i class="fas fa-file-pdf"></i> Lihat
                        </a>
                        @else
                        <span style="color:#94a3b8;font-size:11px">—</span>
                        @endif
                    </td>
                    <td style="text-align:center">
                        <span class="status-badge {{ strtolower($r->status) }}">
                            <i class="fas fa-circle" style="font-size:7px"></i> {{ $r->status }}
                        </span>
                    </td>
                    <td style="color:#475569;font-size:11px">{{ $r->catatan_admin ?? '—' }}</td>
                    <td>
                        @if($r->status === 'Menunggu')
                        <form action="{{ route('admin.verifikasi-rkps.proses') }}" method="POST" class="aksi-form">
                            @csrf
                            <input type="hidden" name="id" value="{{ $r->id }}">
                            <textarea name="catatan" placeholder="Tulis catatan (opsional)..." rows="2"></textarea>
                            <div class="aksi-buttons">
                                <button type="submit" name="aksi" value="Setujui" class="btn-aksi btn-setujui">
                                    <i class="fas fa-check"></i> Setujui
                                </button>
                                <button type="submit" name="aksi" value="Revisi" class="btn-aksi btn-revisi">
                                    <i class="fas fa-redo"></i> Revisi
                                </button>
                                <button type="submit" name="aksi" value="Tolak" class="btn-aksi btn-tolak" onclick="return confirm('Yakin tolak pengajuan ini?')">
                                    <i class="fas fa-times"></i> Tolak
                                </button>
                            </div>
                        </form>
                        @else
                        <span style="color:#94a3b8;font-size:11px">Sudah diproses</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            Belum ada pengajuan RKPS.
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
var searchBox = document.getElementById('searchBox');
var filterStatus = document.getElementById('filterStatus');

function filterTable() {
    var keyword = searchBox ? searchBox.value.toLowerCase().trim() : '';
    var statusVal = filterStatus ? filterStatus.value.trim() : '';
    var rows = document.querySelectorAll('#tabelVerif tbody tr');
    rows.forEach(function(row) {
        var text = (row.textContent || row.innerText || '').toLowerCase();
        var rowStatus = row.getAttribute('data-status') || '';
        var matchText = !keyword || text.includes(keyword);
        var matchStatus = !statusVal || rowStatus === statusVal;
        var isMatch = matchText && matchStatus;
        row.style.display = isMatch ? '' : 'none';
        row.dataset.filterHidden = isMatch ? 'false' : 'true';
    });
    if (typeof window.refreshTablePagination === 'function') {
        window.refreshTablePagination(document.getElementById('tabelVerif'));
    }
}

if (searchBox) {
    searchBox.addEventListener('input', filterTable);
    searchBox.addEventListener('keyup', filterTable);
}
if (filterStatus) {
    filterStatus.addEventListener('change', filterTable);
}
</script>
@endpush
