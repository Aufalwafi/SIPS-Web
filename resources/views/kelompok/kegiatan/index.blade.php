@extends('layouts.app')
@section('title', 'Kegiatan - SI PS ONLINE')

@section('content')
<div class="content">
    <div class="box">
        @if(session('success'))<div class="alert">{!! session('success') !!}</div>@endif
        @if(session('error'))<div class="alert-error">{!! session('error') !!}</div>@endif

        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px">
            <h2 style="margin:0"><i class="fas fa-calendar-check" style="color:#1565c0"></i> Kegiatan Kelompok</h2>
            <a href="{{ route('kelompok.kegiatan.tambah') }}" class="btn btn-tambah"><i class="fas fa-plus"></i> Tambah Kegiatan</a>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="width:50px">No</th>
                        <th>Nama Kegiatan</th>
                        <th>Tanggal</th>
                        <th>Lokasi</th>
                        <th>KML</th>
                        <th style="width:160px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kegiatanList as $i => $k)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $k->nama_kegiatan ?? '-' }}</td>
                            <td>{{ $k->tanggal ? \Carbon\Carbon::parse($k->tanggal)->format('d/m/Y') : '-' }}</td>
                            <td>{{ $k->lokasi ?? '-' }}</td>
                            <td>
                                @if($k->kml_file)
                                    <a href="{{ asset('storage/kegiatan/' . $k->kml_file) }}" class="btn btn-view" title="Download KML" download><i class="fas fa-download"></i> KML</a>
                                @else
                                    <span style="color:#999">-</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('kelompok.kegiatan.detail', $k->id) }}" class="btn btn-view" title="Detail"><i class="fas fa-eye"></i> Detail</a>
                                <a href="{{ route('kelompok.kegiatan.edit', $k->id) }}" class="btn btn-edit" title="Edit"><i class="fas fa-pen"></i> Edit</a>
                                <form action="{{ route('kelompok.kegiatan.hapus', $k->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Yakin ingin menghapus kegiatan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-delete" title="Hapus"><i class="fas fa-trash"></i> Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty">Belum ada data kegiatan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
