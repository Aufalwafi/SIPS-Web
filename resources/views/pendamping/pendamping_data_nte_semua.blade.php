@extends('layouts.app')
@section('title', 'Data NTE Kelompok Binaan - SI PS ONLINE')
@push('styles')
<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px; }
.page-header h2 { color: #1b5e20; font-size: 20px; font-weight: 700; margin: 0; }

.filter-toolbar { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; margin-bottom: 16px; }
.filter-toolbar input { height: 34px; padding: 4px 10px; font-size: 12px; border: 1px solid #cbd5e1; border-radius: 6px; background: #fff; color: #334155; min-width: 240px; }
.filter-toolbar input:focus { border-color: #2e7d32; outline: none; }

.nte-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.nte-table th {
    background: #f8fafc; color: #475569; font-weight: 600; text-align: left;
    padding: 10px 12px; border-bottom: 2px solid #e2e8f0;
    font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px;
}
.nte-table td { padding: 9px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.nte-table tr:hover td { background: #f8fafc; }

.btn-detail-link { background: #e0f2f1; color: #00796b; border: 1px solid #b2dfdb; padding: 4px 10px; border-radius: 5px; font-size: 11px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
.btn-detail-link:hover { background: #00796b; color: #fff; }
</style>
@endpush

@section('content')
<div class="content">
    {{-- HEADER --}}
    <div class="page-header">
        <div>
            <h2><i class="fas fa-chart-line" style="color:#7b1fa2"></i> Data Nilai Ekonomi (NTE) Kelompok Binaan</h2>
            <p style="color:#64748b;font-size:12px;margin:4px 0 0">Rekap seluruh data produk & komoditas kelompok dampingan</p>
        </div>
        @if(isset($kelompokList) && $kelompokList->count() > 0)
            @if($kelompokList->count() === 1)
                <a href="{{ route('pendamping.kelompok-saya.input-nte', $kelompokList->first()->id) }}" class="btn btn-save" style="font-size:13px;height:36px;padding:0 16px;display:inline-flex;align-items:center;gap:6px">
                    <i class="fas fa-plus"></i> Input Komoditas NTE
                </a>
            @else
                <button type="button" class="btn btn-save" onclick="document.getElementById('modalPilihKelompokNte').style.display='flex'" style="font-size:13px;height:36px;padding:0 16px;display:inline-flex;align-items:center;gap:6px;cursor:pointer">
                    <i class="fas fa-plus"></i> Input Komoditas NTE <i class="fas fa-chevron-down" style="font-size:10px;margin-left:4px"></i>
                </button>
            @endif
        @endif
    </div>

    {{-- FILTER --}}
    <div class="filter-toolbar">
        <input type="text" id="searchNte" placeholder="🔍 Cari kelompok / produk / KUPS...">
    </div>

    {{-- TABLE --}}
    <div style="background:#fff;border-radius:8px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,0.04);overflow-x:auto">
        <table class="nte-table" id="tabelNte">
            <thead>
                <tr>
                    <th style="width:40px;text-align:center">No</th>
                    <th>Kelompok</th>
                    <th>Nama Produk</th>
                    <th>KUPS</th>
                    <th style="text-align:right;width:100px">Luas Areal</th>
                    <th style="text-align:center;width:80px">Satuan</th>
                    <th style="text-align:center;width:75px">Tahun</th>
                    <th style="width:100px;text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($nte as $i => $n)
                <tr>
                    <td style="text-align:center;color:#94a3b8">{{ $i + 1 }}</td>
                    <td><strong style="color:#1e293b">{{ $n->nama_kelompok }}</strong></td>
                    <td style="color:#334155;font-weight:600">{{ $n->nama_produk ?? '-' }}</td>
                    <td>
                        @if($n->nama_kups)
                        <span style="background:#f1f5f9;color:#475569;padding:2px 8px;border-radius:10px;font-size:11px">{{ $n->nama_kups }}</span>
                        @else — @endif
                    </td>
                    <td style="text-align:right;color:#2e7d32;font-weight:600">
                        {{ $n->luas_areal ? number_format($n->luas_areal, 2, ',', '.') . ' ha' : '—' }}
                    </td>
                    <td style="text-align:center;color:#64748b">{{ $n->satuan ?? '—' }}</td>
                    <td style="text-align:center;color:#64748b">{{ $n->tahun ?? '—' }}</td>
                    <td style="text-align:center">
                        <a href="{{ route('pendamping.kelompok-saya.data-nte', $n->id_kelompok) }}" class="btn-detail-link">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:48px 20px;color:#94a3b8">
                        <i class="fas fa-chart-line" style="font-size:36px;display:block;margin-bottom:12px;color:#cbd5e1"></i>
                        <p style="font-size:14px;font-weight:600;color:#64748b;margin:0 0 6px">Belum ada data NTE yang tercatat</p>
                        <p style="font-size:12px;color:#94a3b8;margin:0 0 16px">Mulai catat komoditas usaha dan nilai transaksi ekonomi kelompok dampingan Anda.</p>
                        @if(isset($kelompokList) && $kelompokList->count() > 0)
                            @if($kelompokList->count() === 1)
                                <a href="{{ route('pendamping.kelompok-saya.input-nte', $kelompokList->first()->id) }}" class="btn btn-save" style="font-size:12px;padding:8px 18px;display:inline-flex;align-items:center;gap:6px">
                                    <i class="fas fa-plus"></i> Input Komoditas NTE Sekarang
                                </a>
                            @else
                                <button type="button" class="btn btn-save" onclick="document.getElementById('modalPilihKelompokNte').style.display='flex'" style="font-size:12px;padding:8px 18px;display:inline-flex;align-items:center;gap:6px;cursor:pointer">
                                    <i class="fas fa-plus"></i> Input Komoditas NTE Sekarang
                                </button>
                            @endif
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MODAL PILIH KELOMPOK UNTUK INPUT NTE --}}
    @if(isset($kelompokList) && $kelompokList->count() > 1)
    <div id="modalPilihKelompokNte" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;padding:16px">
        <div style="background:#fff;border-radius:12px;width:100%;max-width:480px;box-shadow:0 20px 25px -5px rgba(0,0,0,0.2);overflow:hidden">
            <div style="padding:16px 20px;border-bottom:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center;background:#f8fafc">
                <h3 style="margin:0;font-size:15px;font-weight:700;color:#1e293b;display:flex;align-items:center;gap:8px">
                    <i class="fas fa-chart-line" style="color:#7b1fa2"></i> Pilih Kelompok untuk Input NTE
                </h3>
                <button type="button" onclick="document.getElementById('modalPilihKelompokNte').style.display='none'" style="background:none;border:none;font-size:20px;color:#94a3b8;cursor:line-height:1;cursor:pointer">&times;</button>
            </div>
            <div style="padding:16px 20px">
                <p style="margin:0 0 14px;font-size:12px;color:#64748b">Silakan pilih kelompok binaan yang ingin diinputkan komoditas NTE-nya:</p>
                <div style="display:flex;flex-direction:column;gap:8px">
                    @foreach($kelompokList as $klp)
                    <a href="{{ route('pendamping.kelompok-saya.input-nte', $klp->id) }}" style="display:flex;align-items:center;justify-content:space-between;padding:12px 14px;border:1px solid #e2e8f0;border-radius:8px;text-decoration:none;color:#1e293b;background:#fff;transition:all 0.15s ease" onmouseover="this.style.borderColor='#7b1fa2';this.style.background='#fdf4ff'" onmouseout="this.style.borderColor='#e2e8f0';this.style.background='#fff'">
                        <div style="display:flex;align-items:center;gap:10px">
                            <div style="width:34px;height:34px;border-radius:6px;background:#f3e8ff;display:flex;align-items:center;justify-content:center;color:#7b1fa2;font-size:14px">
                                <i class="fas fa-store"></i>
                            </div>
                            <span style="font-weight:600;font-size:13px">{{ $klp->nama_kelompok }}</span>
                        </div>
                        <i class="fas fa-chevron-right" style="color:#94a3b8;font-size:12px"></i>
                    </a>
                    @endforeach
                </div>
            </div>
            <div style="padding:12px 20px;border-top:1px solid #f1f5f9;text-align:right;background:#f8fafc">
                <button type="button" onclick="document.getElementById('modalPilihKelompokNte').style.display='none'" class="btn btn-outline" style="font-size:12px;padding:6px 14px">Batal</button>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function filterNte() {
    var searchEl = document.getElementById('searchNte');
    var query = searchEl ? searchEl.value.toLowerCase().trim() : '';
    document.querySelectorAll('#tabelNte tbody tr').forEach(function(row) {
        var text = (row.textContent || row.innerText || '').toLowerCase();
        var isMatch = !query || text.includes(query);
        row.style.display = isMatch ? '' : 'none';
        row.dataset.filterHidden = isMatch ? 'false' : 'true';
    });
    if (typeof window.refreshTablePagination === 'function') {
        window.refreshTablePagination(document.getElementById('tabelNte'));
    }
}
if (document.getElementById('searchNte')) {
    document.getElementById('searchNte').addEventListener('input', filterNte);
    document.getElementById('searchNte').addEventListener('keyup', filterNte);
}

// Close modal on outside click or ESC
window.addEventListener('click', function(e) {
    var modal = document.getElementById('modalPilihKelompokNte');
    if (modal && e.target === modal) {
        modal.style.display = 'none';
    }
});
window.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        var modal = document.getElementById('modalPilihKelompokNte');
        if (modal) modal.style.display = 'none';
    }
});
</script>
@endpush