@extends('layouts.app')
@section('title', 'Detail Laporan Kegiatan - SI PS ONLINE')
@section('content')
<div class="box" style="max-width:800px">
    <h2><i class="fas fa-clipboard-check" style="color:var(--green-header)"></i> Detail Laporan Kegiatan</h2>

    @if(session('success'))<div class="alert">{!! session('success') !!}</div>@endif
    @if(session('error'))<div class="alert-error">{!! session('error') !!}</div>@endif

    <table style="width:100%;border-collapse:collapse">
        <tr>
            <th style="width:180px;padding:12px 15px;text-align:left;background:#f8f9fa;border-bottom:1px solid #e9ecef;font-size:13px">Nama Kelompok</th>
            <td style="padding:12px 15px;border-bottom:1px solid #e9ecef;font-size:13px">{{ $laporan->nama_kelompok ?? '-' }}</td>
        </tr>
        <tr>
            <th style="padding:12px 15px;text-align:left;background:#f8f9fa;border-bottom:1px solid #e9ecef;font-size:13px">Tanggal</th>
            <td style="padding:12px 15px;border-bottom:1px solid #e9ecef;font-size:13px">{{ \Carbon\Carbon::parse($laporan->tanggal)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <th style="padding:12px 15px;text-align:left;background:#f8f9fa;border-bottom:1px solid #e9ecef;font-size:13px">Kegiatan</th>
            <td style="padding:12px 15px;border-bottom:1px solid #e9ecef;font-size:13px;font-weight:600">{{ $laporan->kegiatan }}</td>
        </tr>
        <tr>
            <th style="padding:12px 15px;text-align:left;background:#f8f9fa;border-bottom:1px solid #e9ecef;font-size:13px">Deskripsi</th>
            <td style="padding:12px 15px;border-bottom:1px solid #e9ecef;font-size:13px">{{ $laporan->deskripsi ?? '-' }}</td>
        </tr>
        <tr>
            <th style="padding:12px 15px;text-align:left;background:#f8f9fa;border-bottom:1px solid #e9ecef;font-size:13px">Foto</th>
            <td style="padding:12px 15px;border-bottom:1px solid #e9ecef">
                @if($laporan->foto)
                    <img src="{{ asset('storage/laporan/' . $laporan->foto) }}" alt="Foto Laporan" style="max-width:100%;border-radius:8px;border:1px solid #ddd">
                @else
                    <span style="color:#999;font-size:13px">Tidak ada foto</span>
                @endif
            </td>
        </tr>
        <tr>
            <th style="padding:12px 15px;text-align:left;background:#f8f9fa;border-bottom:1px solid #e9ecef;font-size:13px">Dibuat Oleh</th>
            <td style="padding:12px 15px;border-bottom:1px solid #e9ecef;font-size:13px">{{ ucfirst($laporan->role_pembuat ?? '-') }}</td>
        </tr>
    </table>

    <div class="form-buttons" style="margin-top:20px">
        <a href="{{ route('pendamping.laporan-kegiatan') }}" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Kembali</a>
        <a href="{{ route('pendamping.laporan-kegiatan.edit', $laporan->id) }}" class="btn btn-edit"><i class="fas fa-edit"></i> Edit</a>
    </div>
</div>
@endsection
