@extends('layouts.app')
@section('title', 'Data Anggota - '.($kelompok->nama_kelompok ?? ''))
@push('styles')
<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px; }
.page-header h2 { color: #1b5e20; font-size: 20px; font-weight: 700; margin: 0; }

.ag-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.ag-table th {
    background: #f8fafc; color: #475569; font-weight: 600; text-align: left;
    padding: 10px 12px; border-bottom: 2px solid #e2e8f0;
    font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px;
}
.ag-table td { padding: 9px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.ag-table tr:hover td { background: #f8fafc; }

.jk-badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; }
.jk-laki { background: #e3f2fd; color: #1565c0; }
.jk-perempuan { background: #fce4ec; color: #880e4f; }
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
            <h2><i class="fas fa-users" style="color:#1b5e20"></i> Data Anggota — {{ $kelompok->nama_kelompok }}</h2>
            <p style="color:#64748b;font-size:12px;margin:4px 0 0">Total: <strong>{{ count($anggota) }}</strong> anggota terdaftar pada kelompok ini</p>
        </div>
        <a href="{{ route('pendamping.kelompok-saya.anggota.tambah', $kelompok->id) }}" class="btn btn-save" style="font-size:12px;height:34px;padding:0 14px">
            <i class="fas fa-user-plus"></i> Tambah Anggota
        </a>
    </div>

    {{-- TABLE --}}
    <div style="background:#fff;border-radius:8px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,0.04);overflow-x:auto">
        <table class="ag-table">
            <thead>
                <tr>
                    <th style="width:40px;text-align:center">No</th>
                    <th>Nama Anggota</th>
                    <th>NIK</th>
                    <th style="text-align:center;width:90px">JK</th>
                    <th>Alamat</th>
                    <th style="text-align:right;width:110px">Luas Garapan</th>
                    <th>Komoditas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($anggota as $i => $a)
                <tr>
                    <td style="text-align:center;color:#94a3b8">{{ $i + 1 }}</td>
                    <td><strong style="color:#1e293b">{{ $a->nama_anggota ?? '-' }}</strong></td>
                    <td style="font-family:monospace;color:#475569;font-size:11px">{{ $a->nik ?? '-' }}</td>
                    <td style="text-align:center">
                        @if($a->jenis_kelamin === 'Laki-laki' || $a->jenis_kelamin === 'L')
                            <span class="jk-badge jk-laki">L</span>
                        @elseif($a->jenis_kelamin === 'Perempuan' || $a->jenis_kelamin === 'P')
                            <span class="jk-badge jk-perempuan">P</span>
                        @else
                            <span style="color:#94a3b8">-</span>
                        @endif
                    </td>
                    <td style="color:#64748b">{{ $a->alamat ?? '-' }}</td>
                    <td style="text-align:right;font-weight:600;color:#2e7d32">
                        {{ $a->luas_garapan ? number_format($a->luas_garapan, 2, ',', '.') . ' ha' : '-' }}
                    </td>
                    <td>
                        @if($a->komoditas_utama)
                        <span style="background:#f1f5f9;color:#475569;padding:2px 8px;border-radius:10px;font-size:11px">{{ $a->komoditas_utama }}</span>
                        @else — @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:#94a3b8">
                        <i class="fas fa-users-slash" style="font-size:28px;display:block;margin-bottom:8px"></i>
                        Belum ada anggota untuk kelompok ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
