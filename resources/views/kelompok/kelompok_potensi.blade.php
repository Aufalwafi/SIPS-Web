@extends('layouts.app')
@section('title', 'Potensi Kelompok - SI PS ONLINE')
@push('styles')
<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px; }
.page-header h2 { color: #1b5e20; font-size: 20px; font-weight: 700; margin: 0; }

.pt-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.pt-table th {
    background: #f8fafc; color: #475569; font-weight: 600; text-align: left;
    padding: 10px 12px; border-bottom: 2px solid #e2e8f0;
    font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px;
}
.pt-table td { padding: 9px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.pt-table tr:hover td { background: #f8fafc; }

.foto-thumb { width: 44px; height: 44px; object-fit: cover; border-radius: 6px; cursor: pointer; }
.foto-thumb:hover { transform: scale(1.08); }

.btn-act { padding: 4px 8px; border-radius: 5px; font-size: 11px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
.btn-act-edit { background: #e3f2fd; color: #1565c0; border: 1px solid #bbdefb; }
.btn-act-edit:hover { background: #1565c0; color: #fff; }
.btn-act-del { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; cursor: pointer; }
.btn-act-del:hover { background: #b91c1c; color: #fff; }
</style>
@endpush

@section('content')
<div class="content">
    @if(session('success'))
    <div class="alert" style="background:#d1fae5;color:#065f46;padding:10px 16px;border-radius:6px;margin-bottom:16px;border-left:4px solid #22c55e">
        <i class="fas fa-check-circle"></i> {!! session('success') !!}
    </div>
    @endif
    @if(session('error'))
    <div class="alert-error" style="background:#fee2e2;color:#7f1d1d;padding:10px 16px;border-radius:6px;margin-bottom:16px;border-left:4px solid #ef4444">
        <i class="fas fa-exclamation-circle"></i> {!! session('error') !!}
    </div>
    @endif

    {{-- HEADER --}}
    <div class="page-header">
        <div>
            <h2><i class="fas fa-star" style="color:#d97706"></i> Potensi Wilayah Kelompok</h2>
            <p style="color:#64748b;font-size:12px;margin:4px 0 0">Kelola data potensi komoditas dan tanaman di area perhutanan sosial</p>
        </div>
        <a href="{{ route('kelompok.potensi.tambah') }}" class="btn btn-save" style="font-size:12px;height:34px;padding:0 14px">
            <i class="fas fa-plus"></i> Tambah Potensi Baru
        </a>
    </div>

    {{-- TABLE --}}
    <div style="background:#fff;border-radius:8px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,0.04);overflow-x:auto">
        <table class="pt-table">
            <thead>
                <tr>
                    <th style="width:40px;text-align:center">No</th>
                    <th>Jenis Potensi</th>
                    <th>Detail Potensi</th>
                    <th style="width:90px;text-align:center">Thn Tanam</th>
                    <th style="text-align:right;width:90px">Luasan</th>
                    <th style="width:70px;text-align:center">Satuan</th>
                    <th>Kondisi</th>
                    <th>Lokasi</th>
                    <th style="width:65px;text-align:center">Foto</th>
                    <th style="width:130px;text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($potensiList as $i => $p)
                <tr>
                    <td style="text-align:center;color:#94a3b8">{{ $i + 1 }}</td>
                    <td><strong style="color:#1e293b">{{ $p->jenis_potensi ?? '-' }}</strong></td>
                    <td style="color:#475569">{{ $p->jenis_detail ?? '-' }}</td>
                    <td style="text-align:center;color:#64748b">{{ $p->tahun_tanam ?? '-' }}</td>
                    <td style="text-align:right;font-weight:600;color:#2e7d32">{{ $p->luasan ? number_format($p->luasan, 2, ',', '.') : '—' }}</td>
                    <td style="text-align:center;color:#64748b">{{ $p->satuan ?? '-' }}</td>
                    <td>
                        @if($p->kondisi)
                        <span style="background:#f1f5f9;color:#475569;padding:2px 7px;border-radius:4px;font-size:11px">{{ $p->kondisi }}</span>
                        @else — @endif
                    </td>
                    <td style="color:#64748b;font-size:11px">{{ $p->lokasi ?? '-' }}</td>
                    <td style="text-align:center">
                        @if($p->foto)
                            <a href="{{ asset('storage/potensi/' . $p->foto) }}" target="_blank">
                                <img src="{{ asset('storage/potensi/' . $p->foto) }}" class="foto-thumb" alt="Foto">
                            </a>
                        @else
                            <span style="color:#94a3b8">—</span>
                        @endif
                    </td>
                    <td style="text-align:center">
                        <div style="display:flex;gap:4px;justify-content:center">
                            <a href="{{ route('kelompok.potensi.edit', $p->id) }}" class="btn-act btn-act-edit" title="Edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('kelompok.potensi.hapus', $p->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Yakin ingin menghapus potensi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-act btn-act-del" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" style="text-align:center;padding:40px;color:#94a3b8">
                        <i class="fas fa-star" style="font-size:28px;display:block;margin-bottom:8px"></i>
                        Belum ada data potensi wilayah yang dicatat.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
