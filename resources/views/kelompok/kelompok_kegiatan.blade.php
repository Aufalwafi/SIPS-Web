@extends('layouts.app')
@section('title', 'Kegiatan Kelompok - SI PS ONLINE')
@push('styles')
<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px; }
.page-header h2 { color: #1b5e20; font-size: 20px; font-weight: 700; margin: 0; }

.kg-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.kg-table th {
    background: #f8fafc; color: #475569; font-weight: 600; text-align: left;
    padding: 10px 12px; border-bottom: 2px solid #e2e8f0;
    font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px;
}
.kg-table td { padding: 9px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.kg-table tr:hover td { background: #f8fafc; }

.btn-file-kml { display: inline-flex; align-items: center; gap: 4px; background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; padding: 3px 8px; border-radius: 5px; font-size: 11px; font-weight: 600; text-decoration: none; }
.btn-file-kml:hover { background: #065f46; color: #fff; }

.btn-act { padding: 4px 8px; border-radius: 5px; font-size: 11px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
.btn-act-view { background: #e0f2f1; color: #00796b; border: 1px solid #b2dfdb; }
.btn-act-view:hover { background: #00796b; color: #fff; }
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
            <h2><i class="fas fa-calendar-check" style="color:#1565c0"></i> Kegiatan Kelompok</h2>
            <p style="color:#64748b;font-size:12px;margin:4px 0 0">Kelola dokumentasi kegiatan lapangan dan berkas spasial KML</p>
        </div>
        <a href="{{ route('kelompok.kegiatan.tambah') }}" class="btn btn-save" style="font-size:12px;height:34px;padding:0 14px">
            <i class="fas fa-plus"></i> Tambah Kegiatan Baru
        </a>
    </div>

    {{-- TABLE --}}
    <div style="background:#fff;border-radius:8px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,0.04);overflow-x:auto">
        <table class="kg-table">
            <thead>
                <tr>
                    <th style="width:40px;text-align:center">No</th>
                    <th>Nama Kegiatan</th>
                    <th style="width:100px;text-align:center">Tanggal</th>
                    <th>Lokasi</th>
                    <th style="width:100px;text-align:center">Berkas KML</th>
                    <th style="width:160px;text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kegiatanList as $i => $k)
                <tr>
                    <td style="text-align:center;color:#94a3b8">{{ $i + 1 }}</td>
                    <td><strong style="color:#1e293b">{{ $k->nama_kegiatan ?? '-' }}</strong></td>
                    <td style="text-align:center;color:#475569;font-weight:600">{{ $k->tanggal ? \Carbon\Carbon::parse($k->tanggal)->format('d/m/Y') : '—' }}</td>
                    <td style="color:#64748b">{{ $k->lokasi ?? '-' }}</td>
                    <td style="text-align:center">
                        @if($k->kml_file)
                            <a href="{{ asset('storage/kegiatan/' . $k->kml_file) }}" class="btn-file-kml" title="Download KML" download>
                                <i class="fas fa-download"></i> KML
                            </a>
                        @else
                            <span style="color:#94a3b8">—</span>
                        @endif
                    </td>
                    <td style="text-align:center">
                        <div style="display:flex;gap:4px;justify-content:center">
                            <a href="{{ route('kelompok.kegiatan.detail', $k->id) }}" class="btn-act btn-act-view" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('kelompok.kegiatan.edit', $k->id) }}" class="btn-act btn-act-edit" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('kelompok.kegiatan.hapus', $k->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Yakin ingin menghapus kegiatan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-act btn-act-del" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:#94a3b8">
                        <i class="fas fa-calendar-times" style="font-size:28px;display:block;margin-bottom:8px"></i>
                        Belum ada data kegiatan yang dimasukkan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
