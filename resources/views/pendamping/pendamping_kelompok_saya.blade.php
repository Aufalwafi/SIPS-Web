@extends('layouts.app')
@section('title', 'Kelompok Saya - SI PS ONLINE')
@push('styles')
<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px; }
.page-header h2 { color: #1b5e20; font-size: 20px; font-weight: 700; margin: 0; }

.filter-toolbar { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; margin-bottom: 16px; }
.filter-toolbar input, .filter-toolbar select {
    height: 34px; padding: 4px 10px; font-size: 12px;
    border: 1px solid #cbd5e1; border-radius: 6px; background: #fff; color: #334155; min-width: 240px;
}
.filter-toolbar input:focus, .filter-toolbar select:focus { border-color: #2e7d32; outline: none; }

.klp-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.klp-table th {
    background: #f8fafc; color: #475569; font-weight: 600; text-align: left;
    padding: 10px 12px; border-bottom: 2px solid #e2e8f0;
    font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px;
}
.klp-table td { padding: 9px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.klp-table tr:hover td { background: #f8fafc; }

.btn-action-detail { background: #e8f5e9; color: #1b5e20; border: 1px solid #c8e6c9; padding: 4px 10px; border-radius: 5px; font-size: 11px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
.btn-action-detail:hover { background: #1b5e20; color: #fff; }

.summary-chips { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 18px; }
.chip { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 9px 16px; font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 7px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
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
            <h2><i class="fas fa-users" style="color:#2e7d32"></i> Kelompok yang Didampingi</h2>
            <p style="color:#64748b;font-size:12px;margin:4px 0 0">Daftar kelompok Perhutanan Sosial di bawah pembinaan Anda</p>
        </div>
        <a href="{{ route('pendamping.peta-kelompok') }}" class="btn" style="background:#1565c0;color:#fff;font-size:12px;height:34px;padding:0 14px;border-radius:6px">
            <i class="fas fa-map-marked-alt"></i> Peta Kelompok Binaan
        </a>
    </div>

    {{-- SUMMARY CHIPS --}}
    @php
        $totalK = count($kelompok);
        $totalA = collect($anggotaCounts)->sum();
    @endphp
    <div class="summary-chips">
        <div class="chip" style="border-left:4px solid #2e7d32;color:#065f46;background:#f0fdf4"><i class="fas fa-users"></i> Total: {{ $totalK }} Kelompok</div>
        <div class="chip" style="border-left:4px solid #1565c0;color:#1e40af;background:#eff6ff"><i class="fas fa-user-friends"></i> Total: {{ $totalA }} Anggota Petani</div>
    </div>

    {{-- FILTER --}}
    <div class="filter-toolbar">
        <input type="text" id="searchKlp" placeholder="🔍 Cari nama kelompok / ketua / desa / kecamatan...">
    </div>

    {{-- TABLE --}}
    <div style="background:#fff;border-radius:8px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,0.04);overflow-x:auto">
        <table class="klp-table" id="tabelKelompok">
            <thead>
                <tr>
                    <th style="width:40px;text-align:center">No</th>
                    <th>Nama Kelompok</th>
                    <th>Ketua</th>
                    <th>CDK</th>
                    <th>Desa</th>
                    <th>Kecamatan</th>
                    <th style="text-align:center;width:90px">Anggota</th>
                    <th style="text-align:center;width:210px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kelompok as $i => $k)
                <tr>
                    <td style="text-align:center;color:#94a3b8">{{ $i + 1 }}</td>
                    <td><strong style="color:#1e293b">{{ $k->nama_kelompok }}</strong></td>
                    <td style="color:#475569">{{ $k->ketua ?? '-' }}</td>
                    <td>
                        @if(!empty($k->nama_cdk) || !empty($k->id_cdk))
                        <span style="background:#dbeafe;color:#1e40af;padding:2px 7px;border-radius:5px;font-size:11px">{{ $k->nama_cdk ?? $k->id_cdk }}</span>
                        @else — @endif
                    </td>
                    <td style="color:#64748b">{{ $k->nama_desa ?? $k->desa ?? '-' }}</td>
                    <td style="color:#64748b">{{ $k->kecamatan ?? '-' }}</td>
                    <td style="text-align:center">
                        <span style="background:#e8f5e9;color:#1b5e20;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:700">
                            {{ $anggotaCounts[$k->id] ?? 0 }}
                        </span>
                    </td>
                    <td style="text-align:center;white-space:nowrap">
                        <div style="display:inline-flex;gap:6px;align-items:center;justify-content:center">
                            <a href="{{ route('pendamping.kelompok-saya.masuk', $k->id) }}" class="btn" style="background:#1b5e20;color:#fff;padding:5px 10px;border-radius:5px;font-size:11px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:5px" title="Buka dan kelola isi kelompok">
                                <i class="fas fa-sign-in-alt"></i> Akses Kelompok
                            </a>
                            <a href="{{ route('pendamping.kelompok-saya.detail', $k->id) }}" class="btn-action-detail" title="Lihat ringkasan kelompok">
                                <i class="fas fa-info-circle"></i> Ringkasan
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:40px;color:#94a3b8">
                        <i class="fas fa-users-slash" style="font-size:28px;display:block;margin-bottom:8px"></i>
                        Belum ada kelompok dampingan.
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
function filterKlp() {
    var searchEl = document.getElementById('searchKlp');
    var query = searchEl ? searchEl.value.toLowerCase().trim() : '';
    document.querySelectorAll('#tabelKelompok tbody tr').forEach(function(row) {
        var text = (row.textContent || row.innerText || '').toLowerCase();
        var isMatch = !query || text.includes(query);
        row.style.display = isMatch ? '' : 'none';
        row.dataset.filterHidden = isMatch ? 'false' : 'true';
    });
    if (typeof window.refreshTablePagination === 'function') {
        window.refreshTablePagination(document.getElementById('tabelKelompok'));
    }
}
if (document.getElementById('searchKlp')) {
    document.getElementById('searchKlp').addEventListener('input', filterKlp);
    document.getElementById('searchKlp').addEventListener('keyup', filterKlp);
}
</script>
@endpush