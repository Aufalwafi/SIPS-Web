@extends('layouts.app')
@section('title', 'Data NTE - '.($kelompok->nama_kelompok ?? ''))
@push('styles')
<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px; }
.page-header h2 { color: #1b5e20; font-size: 20px; font-weight: 700; margin: 0; }

.nte-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.nte-table th {
    background: #f8fafc; color: #475569; font-weight: 600; text-align: left;
    padding: 10px 12px; border-bottom: 2px solid #e2e8f0;
    font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px;
}
.nte-table td { padding: 9px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.nte-table tr:hover td { background: #f8fafc; }

.btn-transaksi { background: #e3f2fd; color: #1565c0; border: 1px solid #bbdefb; padding: 4px 10px; border-radius: 5px; font-size: 11px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
.btn-transaksi:hover { background: #1565c0; color: #fff; }
</style>
@endpush

@section('content')
<div class="content">
    <a href="{{ route('pendamping.kelompok-saya.detail', $kelompok->id) }}" class="btn btn-outline" style="margin-bottom:15px;display:inline-flex;align-items:center;gap:6px">
        <i class="fas fa-arrow-left"></i> Kembali ke Detail Kelompok
    </a>

    {{-- HEADER --}}
    <div class="page-header">
        <div>
            <h2><i class="fas fa-chart-line" style="color:#7b1fa2"></i> Data NTE — {{ $kelompok->nama_kelompok }}</h2>
            <p style="color:#64748b;font-size:12px;margin:4px 0 0">Kelola data komoditas dan input transaksi nilai ekonomi</p>
        </div>
        <a href="{{ route('pendamping.kelompok-saya.input-nte', $kelompok->id) }}" class="btn btn-save" style="font-size:12px;height:34px;padding:0 14px">
            <i class="fas fa-plus"></i> Input Komoditas NTE Baru
        </a>
    </div>

    {{-- TABLE --}}
    <div style="background:#fff;border-radius:8px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,0.04);overflow-x:auto">
        <table class="nte-table">
            <thead>
                <tr>
                    <th style="width:35px;text-align:center">No</th>
                    <th>Nama Produk</th>
                    <th>KUPS</th>
                    <th style="text-align:right;width:100px">Luas Areal</th>
                    <th style="text-align:center;width:75px">Satuan</th>
                    <th>Pola Olah</th>
                    <th>Sumber Modal</th>
                    <th style="text-align:center;width:65px">Tahun</th>
                    <th style="width:110px;text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($nte as $i => $n)
                <tr>
                    <td style="text-align:center;color:#94a3b8">{{ $i + 1 }}</td>
                    <td><strong style="color:#1e293b">{{ $n->nama_produk ?? '-' }}</strong></td>
                    <td>
                        @if($n->nama_kups)
                        <span style="background:#f1f5f9;color:#475569;padding:2px 8px;border-radius:10px;font-size:11px">{{ $n->nama_kups }}</span>
                        @else — @endif
                    </td>
                    <td style="text-align:right;color:#2e7d32;font-weight:600">
                        {{ $n->luas_areal ? number_format($n->luas_areal, 2, ',', '.') . ' ha' : '—' }}
                    </td>
                    <td style="text-align:center;color:#64748b">{{ $n->satuan ?? '—' }}</td>
                    <td style="color:#64748b">{{ $n->pola_pengolahan ?? '—' }}</td>
                    <td style="color:#64748b">{{ $n->sumber_modal ?? '—' }}</td>
                    <td style="text-align:center;color:#64748b">{{ $n->tahun ?? '—' }}</td>
                    <td style="text-align:center">
                        <a href="{{ route('pendamping.kelompok-saya.transaksi', [$kelompok->id, $n->id]) }}" class="btn-transaksi">
                            <i class="fas fa-receipt"></i> Transaksi
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:40px;color:#94a3b8">
                        <i class="fas fa-chart-line" style="font-size:28px;display:block;margin-bottom:8px"></i>
                        Belum ada data komoditas NTE untuk kelompok ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
