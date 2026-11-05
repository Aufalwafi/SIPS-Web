@extends('layouts.app')
@section('title', 'KUPS Kelompok - SI PS ONLINE')
@push('styles')
<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px; }
.page-header h2 { color: #1b5e20; font-size: 20px; font-weight: 700; margin: 0; }

.kups-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.kups-table th {
    background: #f8fafc; color: #475569; font-weight: 600; text-align: left;
    padding: 10px 12px; border-bottom: 2px solid #e2e8f0;
    font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px;
}
.kups-table td { padding: 9px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.kups-table tr:hover td { background: #f8fafc; }

.kelas-badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 700; }
.kelas-Biru { background: #dbeafe; color: #1e40af; }
.kelas-Perak { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
.kelas-Emas { background: #fef3c7; color: #92400e; }
.kelas-Platinum { background: #f3e8ff; color: #6b21a8; }

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
            <h2><i class="fas fa-seedling" style="color:#2e7d32"></i> Kelompok Usaha Perhutanan Sosial (KUPS)</h2>
            <p style="color:#64748b;font-size:12px;margin:4px 0 0">Kelola unit usaha dan produk usaha kelompok</p>
        </div>
        <a href="{{ route('kelompok.kups.tambah') }}" class="btn btn-save" style="font-size:12px;height:34px;padding:0 14px">
            <i class="fas fa-plus"></i> Tambah KUPS Baru
        </a>
    </div>

    {{-- TABLE --}}
    <div style="background:#fff;border-radius:8px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,0.04);overflow-x:auto">
        <table class="kups-table">
            <thead>
                <tr>
                    <th style="width:40px;text-align:center">No</th>
                    <th>Nama KUPS</th>
                    <th>Ketua KUPS</th>
                    <th style="text-align:center;width:90px">Jml Anggota</th>
                    <th style="text-align:center;width:100px">Kelas</th>
                    <th style="text-align:center;width:80px">Thn Bentuk</th>
                    <th>Jenis Usaha</th>
                    <th>Produk Utama</th>
                    <th style="width:130px;text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kupsList as $i => $k)
                <tr>
                    <td style="text-align:center;color:#94a3b8">{{ $i + 1 }}</td>
                    <td><strong style="color:#1e293b">{{ $k->nama_kups ?? '-' }}</strong></td>
                    <td style="color:#475569">{{ $k->ketua_kups ?? '-' }}</td>
                    <td style="text-align:center">
                        <span style="background:#f1f5f9;color:#334155;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:700">
                            {{ $k->jumlah_anggota ?? 0 }}
                        </span>
                    </td>
                    <td style="text-align:center">
                        @php $kls = $k->kelas_kups ?? ''; @endphp
                        <span class="kelas-badge kelas-{{ $kls }}">
                            {{ $kls ?: '-' }}
                        </span>
                    </td>
                    <td style="text-align:center;color:#64748b">{{ $k->tahun_bentuk ?? '-' }}</td>
                    <td style="color:#475569">{{ $k->jenis_usaha ?? '-' }}</td>
                    <td>
                        @if($k->produk)
                        <span style="background:#e8f5e9;color:#1b5e20;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:600">{{ $k->produk }}</span>
                        @else — @endif
                    </td>
                    <td style="text-align:center">
                        <div style="display:flex;gap:4px;justify-content:center">
                            <a href="{{ route('kelompok.kups.edit', $k->id) }}" class="btn-act btn-act-edit" title="Edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('kelompok.kups.hapus', $k->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Yakin ingin menghapus KUPS ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-act btn-act-del" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:40px;color:#94a3b8">
                        <i class="fas fa-seedling" style="font-size:28px;display:block;margin-bottom:8px"></i>
                        Belum ada data KUPS yang tercatat.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
