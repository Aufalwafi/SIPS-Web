@extends('layouts.app')
@section('title', 'Pengajuan RKT Kelompok Dampingan - SI PS ONLINE')
@push('styles')
<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px; }
.page-header h2 { color: #1b5e20; font-size: 20px; font-weight: 700; margin: 0; }

.summary-chips { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 18px; }
.chip { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 9px 16px; font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 7px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }

.filter-toolbar { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; margin-bottom: 16px; }
.filter-toolbar input, .filter-toolbar select {
    height: 34px; padding: 4px 10px; font-size: 12px;
    border: 1px solid #cbd5e1; border-radius: 6px; background: #fff; color: #334155;
}
.filter-toolbar input { min-width: 240px; }
.filter-toolbar input:focus, .filter-toolbar select:focus { border-color: #2e7d32; outline: none; }

.p-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.p-table th {
    background: #f8fafc; color: #475569; font-weight: 600; text-align: left;
    padding: 10px 12px; border-bottom: 2px solid #e2e8f0;
    font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px;
}
.p-table td { padding: 9px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.p-table tr:hover td { background: #f8fafc; }

.status-badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; }
.status-badge.Menunggu { background: #fef3c7; color: #92400e; }
.status-badge.Disetujui { background: #d1fae5; color: #065f46; }
.status-badge.Ditolak { background: #fee2e2; color: #991b1b; }
.status-badge.Revisi { background: #ffedd5; color: #9a3412; }

.btn-file-pdf { display: inline-flex; align-items: center; gap: 4px; background: #fdf2f8; color: #86198f; border: 1px solid #e9d5ff; padding: 3px 8px; border-radius: 5px; font-size: 11px; font-weight: 600; text-decoration: none; }
.btn-file-pdf:hover { background: #86198f; color: #fff; }
</style>
@endpush

@section('content')
<div class="content">
    @if(session('success'))
    <div class="alert" style="background:#d1fae5;color:#065f46;padding:10px 16px;border-radius:6px;margin-bottom:16px;border-left:4px solid #22c55e">
        <i class="fas fa-check-circle"></i> {!! session('success') !!}
    </div>
    @endif

    {{-- HEADER --}}
    <div class="page-header">
        <div>
            <h2><i class="fas fa-file-signature" style="color:#1565c0"></i> Monitoring RKT Kelompok Dampingan</h2>
            <p style="color:#64748b;font-size:12px;margin:4px 0 0">Status pengajuan Rencana Kerja Tahunan</p>
        </div>
        @if(isset($kelompokList) && $kelompokList->count() > 0)
            @if($kelompokList->count() === 1)
                <a href="{{ route('pendamping.kelompok-saya.masuk', $kelompokList->first()->id) }}" class="btn btn-save" style="font-size:13px;height:36px;padding:0 16px;display:inline-flex;align-items:center;gap:6px" title="Bantu ajukan RKT melalui portal kelompok">
                    <i class="fas fa-file-upload"></i> Ajukan RKT (Bantu Kelompok)
                </a>
            @else
                <button type="button" class="btn btn-save" onclick="document.getElementById('modalPilihKelompokRkt').style.display='flex'" style="font-size:13px;height:36px;padding:0 16px;display:inline-flex;align-items:center;gap:6px;cursor:pointer">
                    <i class="fas fa-file-upload"></i> Ajukan RKT (Bantu Kelompok) <i class="fas fa-chevron-down" style="font-size:10px;margin-left:4px"></i>
                </button>
            @endif
        @endif
    </div>

    {{-- SUMMARY CHIPS --}}
    @php
        $totalAll = count($pengajuan);
        $countMenunggu = $pengajuan->where('status', 'Menunggu')->count();
        $countSetuju = $pengajuan->where('status', 'Disetujui')->count();
        $countTolak = $pengajuan->where('status', 'Ditolak')->count();
    @endphp
    <div class="summary-chips">
        <div class="chip" style="border-left:4px solid #1565c0;color:#1e40af;background:#eff6ff"><i class="fas fa-list"></i> Total: {{ $totalAll }} Dokumen</div>
        <div class="chip" style="border-left:4px solid #f59e0b;color:#92400e;background:#fffbeb"><i class="fas fa-clock"></i> Menunggu: {{ $countMenunggu }}</div>
        <div class="chip" style="border-left:4px solid #22c55e;color:#065f46;background:#f0fdf4"><i class="fas fa-check-circle"></i> Disetujui: {{ $countSetuju }}</div>
        <div class="chip" style="border-left:4px solid #ef4444;color:#991b1b;background:#fef2f2"><i class="fas fa-times-circle"></i> Ditolak: {{ $countTolak }}</div>
    </div>

    {{-- FILTER --}}
    <div class="filter-toolbar">
        <input type="text" id="searchBox" placeholder="🔍 Cari nama kelompok / tahun / no penetapan...">
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
        <table class="p-table" id="tabelPengajuan">
            <thead>
                <tr>
                    <th style="width:40px;text-align:center">No</th>
                    <th>Kelompok</th>
                    <th style="width:75px;text-align:center">Tahun</th>
                    <th>No Penetapan</th>
                    <th style="width:90px;text-align:center">Dokumen</th>
                    <th style="width:110px;text-align:center">Status</th>
                    <th>Catatan Verifikator</th>
                    <th style="width:110px">Tgl Pengajuan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengajuan as $i => $p)
                <tr data-status="{{ $p->status }}">
                    <td style="text-align:center;color:#94a3b8">{{ $i + 1 }}</td>
                    <td><strong style="color:#1e293b">{{ $p->nama_kelompok }}</strong></td>
                    <td style="text-align:center;font-weight:600;color:#1565c0">{{ $p->tahun }}</td>
                    <td style="color:#475569;font-size:11px">{{ $p->no_penetapan ?? '—' }}</td>
                    <td style="text-align:center">
                        @if($p->file_rkt)
                            <a href="{{ asset('uploads/rkt/' . $p->file_rkt) }}" target="_blank" class="btn-file-pdf">
                                <i class="fas fa-file-pdf"></i> Lihat
                            </a>
                        @else
                            <span style="color:#94a3b8">—</span>
                        @endif
                    </td>
                    <td style="text-align:center">
                        <span class="status-badge {{ $p->status }}">{{ $p->status }}</span>
                    </td>
                    <td style="color:#64748b;font-size:11px">{{ $p->catatan_admin ?? '—' }}</td>
                    <td style="color:#64748b;font-size:11px">{{ $p->tanggal_pengajuan ? \Carbon\Carbon::parse($p->tanggal_pengajuan)->format('d/m/Y') : '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:48px 20px;color:#94a3b8">
                        <i class="fas fa-file-signature" style="font-size:36px;display:block;margin-bottom:12px;color:#cbd5e1"></i>
                        <p style="font-size:14px;font-weight:600;color:#64748b;margin:0 0 6px">Belum ada pengajuan RKT</p>
                        <p style="font-size:12px;color:#94a3b8;margin:0 0 16px">Kelompok binaan belum mengajukan dokumen RKT, atau Anda dapat membantu mengajukannya.</p>
                        @if(isset($kelompokList) && $kelompokList->count() > 0)
                            @if($kelompokList->count() === 1)
                                <a href="{{ route('pendamping.kelompok-saya.masuk', $kelompokList->first()->id) }}" class="btn btn-save" style="font-size:12px;padding:8px 18px;display:inline-flex;align-items:center;gap:6px">
                                    <i class="fas fa-file-upload"></i> Bantu Ajukan RKT Sekarang
                                </a>
                            @else
                                <button type="button" class="btn btn-save" onclick="document.getElementById('modalPilihKelompokRkt').style.display='flex'" style="font-size:12px;padding:8px 18px;display:inline-flex;align-items:center;gap:6px;cursor:pointer">
                                    <i class="fas fa-file-upload"></i> Bantu Ajukan RKT Sekarang
                                </button>
                            @endif
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MODAL PILIH KELOMPOK UNTUK AJUKAN RKT --}}
    @if(isset($kelompokList) && $kelompokList->count() > 1)
    <div id="modalPilihKelompokRkt" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;padding:16px">
        <div style="background:#fff;border-radius:12px;width:100%;max-width:480px;box-shadow:0 20px 25px -5px rgba(0,0,0,0.2);overflow:hidden">
            <div style="padding:16px 20px;border-bottom:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center;background:#f8fafc">
                <h3 style="margin:0;font-size:15px;font-weight:700;color:#1e293b;display:flex;align-items:center;gap:8px">
                    <i class="fas fa-file-upload" style="color:#1565c0"></i> Pilih Kelompok untuk Ajukan RKT
                </h3>
                <button type="button" onclick="document.getElementById('modalPilihKelompokRkt').style.display='none'" style="background:none;border:none;font-size:20px;color:#94a3b8;cursor:pointer;line-height:1">&times;</button>
            </div>
            <div style="padding:16px 20px">
                <p style="margin:0 0 14px;font-size:12px;color:#64748b">Pilih kelompok binaan yang ingin Anda bantu ajukan dokumen RKT-nya:</p>
                <div style="display:flex;flex-direction:column;gap:8px">
                    @foreach($kelompokList as $klp)
                    <a href="{{ route('pendamping.kelompok-saya.masuk', $klp->id) }}" style="display:flex;align-items:center;justify-content:space-between;padding:12px 14px;border:1px solid #e2e8f0;border-radius:8px;text-decoration:none;color:#1e293b;background:#fff;transition:all 0.15s ease" onmouseover="this.style.borderColor='#1565c0';this.style.background='#eff6ff'" onmouseout="this.style.borderColor='#e2e8f0';this.style.background='#fff'">
                        <div style="display:flex;align-items:center;gap:10px">
                            <div style="width:34px;height:34px;border-radius:6px;background:#e8f5e9;display:flex;align-items:center;justify-content:center;color:#1565c0;font-size:14px">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <span style="font-weight:600;font-size:13px;display:block">{{ $klp->nama_kelompok }}</span>
                                <span style="font-size:11px;color:#64748b">Masuk ke portal kelompok untuk mengunggah</span>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right" style="color:#94a3b8;font-size:12px"></i>
                    </a>
                    @endforeach
                </div>
            </div>
            <div style="padding:12px 20px;border-top:1px solid #f1f5f9;text-align:right;background:#f8fafc">
                <button type="button" onclick="document.getElementById('modalPilihKelompokRkt').style.display='none'" class="btn btn-outline" style="font-size:12px;padding:6px 14px">Batal</button>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function filterPengajuan() {
    var searchEl = document.getElementById('searchBox');
    var statusEl = document.getElementById('filterStatus');
    var query = searchEl ? searchEl.value.toLowerCase().trim() : '';
    var status = statusEl ? statusEl.value.toLowerCase().trim() : '';
    document.querySelectorAll('#tabelPengajuan tbody tr').forEach(function(row) {
        var text = (row.textContent || row.innerText || '').toLowerCase();
        var rowStatus = (row.getAttribute('data-status') || '').toLowerCase();
        var mText = !query || text.includes(query);
        var mStatus = !status || rowStatus === status;
        var isMatch = mText && mStatus;
        row.style.display = isMatch ? '' : 'none';
        row.dataset.filterHidden = isMatch ? 'false' : 'true';
    });
    if (typeof window.refreshTablePagination === 'function') {
        window.refreshTablePagination(document.getElementById('tabelPengajuan'));
    }
}
if (document.getElementById('searchBox')) {
    document.getElementById('searchBox').addEventListener('input', filterPengajuan);
    document.getElementById('searchBox').addEventListener('keyup', filterPengajuan);
}
if (document.getElementById('filterStatus')) {
    document.getElementById('filterStatus').addEventListener('change', filterPengajuan);
}

// Close modal on outside click or ESC
window.addEventListener('click', function(e) {
    var modal = document.getElementById('modalPilihKelompokRkt');
    if (modal && e.target === modal) {
        modal.style.display = 'none';
    }
});
window.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        var modal = document.getElementById('modalPilihKelompokRkt');
        if (modal) modal.style.display = 'none';
    }
});
</script>
@endpush
