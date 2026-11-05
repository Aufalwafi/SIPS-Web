@extends('layouts.app')
@section('title', 'Detail Kegiatan - SI PS ONLINE')

@section('content')
<div class="content">
    <div class="box" style="max-width:800px">
        @if(session('success'))<div class="alert">{!! session('success') !!}</div>@endif
        @if(session('error'))<div class="alert-error">{!! session('error') !!}</div>@endif

        <h2 style="margin-bottom:20px"><i class="fas fa-info-circle" style="color:#1565c0"></i> Detail Kegiatan</h2>

        <table>
            <tr>
                <th style="width:200px">Nama Kegiatan</th>
                <td>{{ $kegiatan->nama_kegiatan ?? '-' }}</td>
            </tr>
            <tr>
                <th>Tanggal</th>
                <td>{{ $kegiatan->tanggal ? \Carbon\Carbon::parse($kegiatan->tanggal)->format('d/m/Y') : '-' }}</td>
            </tr>
            <tr>
                <th>Lokasi</th>
                <td>{{ $kegiatan->lokasi ?? '-' }}</td>
            </tr>
            <tr>
                <th>Keterangan</th>
                <td>{!! nl2br(e($kegiatan->keterangan ?? '-')) !!}</td>
            </tr>
            <tr>
                <th>File KML</th>
                <td>
                    @if($kegiatan->kml_file)
                        <a href="{{ asset('storage/kegiatan/' . $kegiatan->kml_file) }}" class="btn btn-view" download><i class="fas fa-download"></i> {{ $kegiatan->kml_file }}</a>
                    @else
                        <span style="color:#999">Tidak ada file KML</span>
                    @endif
                </td>
            </tr>
        </table>

        @if(isset($dokumentasi) && count($dokumentasi) > 0)
            <div style="margin-top:25px">
                <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;margin-bottom:12px">
                    <h3 style="margin:0"><i class="fas fa-images" style="color:#1565c0"></i> Dokumentasi Kegiatan</h3>
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:14px">
                    @foreach($dokumentasi as $doc)
                        <div style="border:1px solid #cbd5e1;border-radius:8px;overflow:hidden;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,0.05);width:210px">
                            <img src="{{ $doc->foto_url }}" alt="Dokumentasi" style="width:100%;height:140px;object-fit:cover;display:block">
                            <div style="padding:8px 10px;display:flex;justify-content:flex-end;align-items:center;gap:6px">
                                <a href="{{ $doc->foto_url }}" download class="btn btn-outline" style="font-size:11px;padding:3px 8px">
                                    <i class="fas fa-download"></i> Unduh
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif


        <div style="margin-top:25px;display:flex;gap:10px">
            <a href="{{ route('kelompok.kegiatan') }}" class="btn"><i class="fas fa-arrow-left"></i> Kembali</a>
            <a href="{{ route('kelompok.kegiatan.edit', $kegiatan->id) }}" class="btn btn-edit"><i class="fas fa-pen"></i> Edit</a>
        </div>
    </div>
</div>
@endsection
