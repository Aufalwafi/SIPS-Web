@extends('layouts.app')
@section('title', 'Permasalahan Kelompok - SI PS ONLINE')
@push('styles')
<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px; }
.page-header h2 { color: #1b5e20; font-size: 20px; font-weight: 700; margin: 0; }

.pm-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.pm-table th {
    background: #f8fafc; color: #475569; font-weight: 600; text-align: left;
    padding: 10px 12px; border-bottom: 2px solid #e2e8f0;
    font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px;
}
.pm-table td { padding: 9px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.pm-table tr:hover td { background: #f8fafc; }

.status-badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; }
.status-Aktif { background: #fee2e2; color: #991b1b; }
.status-Selesai { background: #d1fae5; color: #065f46; }

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
            <h2><i class="fas fa-triangle-exclamation" style="color:#e65100"></i> Permasalahan & Kendala Kelompok</h2>
            <p style="color:#64748b;font-size:12px;margin:4px 0 0">Pencatatan kendala lapangan untuk koordinasi pendampingan dan solusi</p>
        </div>
        <a href="{{ route('kelompok.permasalahan.tambah') }}" class="btn btn-save" style="font-size:12px;height:34px;padding:0 14px">
            <i class="fas fa-plus"></i> Laporkan Masalah Baru
        </a>
    </div>

    {{-- TABLE --}}
    <div style="background:#fff;border-radius:8px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,0.04);overflow-x:auto">
        <table class="pm-table">
            <thead>
                <tr>
                    <th style="width:40px;text-align:center">No</th>
                    <th>Judul Permasalahan</th>
                    <th style="width:100px;text-align:center">Status</th>
                    <th style="width:120px">Tanggal Lapor</th>
                    <th style="width:130px;text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($permasalahanList as $i => $p)
                <tr>
                    <td style="text-align:center;color:#94a3b8">{{ $i + 1 }}</td>
                    <td><strong style="color:#1e293b">{{ $p->judul ?? '-' }}</strong></td>
                    <td style="text-align:center">
                        <span class="status-badge status-{{ $p->status }}">
                            {{ $p->status ?? '-' }}
                        </span>
                    </td>
                    <td style="color:#64748b;font-size:11px">{{ $p->created_at ? $p->created_at->format('d/m/Y') : '—' }}</td>
                    <td style="text-align:center">
                        <div style="display:flex;gap:4px;justify-content:center">
                            <a href="{{ route('kelompok.permasalahan.edit', $p->id) }}" class="btn-act btn-act-edit" title="Edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('kelompok.permasalahan.hapus', $p->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Yakin ingin menghapus data permasalahan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-act btn-act-del" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:40px;color:#94a3b8">
                        <i class="fas fa-check-circle" style="font-size:28px;display:block;margin-bottom:8px;color:#22c55e"></i>
                        Tidak ada permasalahan yang tercatat.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
