@extends('layouts.app')
@section('title', 'Detail Pengajuan RKPS - SI PS ONLINE')

@section('content')
<div class="content">
    <div class="box" style="max-width:800px">
        @if(session('success'))<div class="alert">{!! session('success') !!}</div>@endif
        @if(session('error'))<div class="alert-error">{!! session('error') !!}</div>@endif

        <h2 style="margin-bottom:20px"><i class="fas fa-file-alt" style="color:#1565c0"></i> Detail Pengajuan RKPS</h2>

        @php
            $statusClass = match($pengajuan->status ?? '') {
                'Menunggu' => 'status-menunggu',
                'Disetujui' => 'status-disetujui',
                'Revisi' => 'status-revisi',
                'Ditolak' => 'status-ditolak',
                default => '',
            };
        @endphp

        <table>
            <tr>
                <th style="width:200px">Tanggal Pengajuan</th>
                <td>{{ $pengajuan->tanggal_pengajuan ? \Carbon\Carbon::parse($pengajuan->tanggal_pengajuan)->format('d/m/Y H:i') : ($pengajuan->created_at ? $pengajuan->created_at->format('d/m/Y H:i') : '-') }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <span class="{{ $statusClass }}" style="display:inline-block;padding:4px 14px;border-radius:12px;font-size:12px;font-weight:700;background:#fff3e0;color:#e67e22">
                        {{ $pengajuan->status ?? '-' }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>File PDF</th>
                <td>
                    @if($pengajuan->file_rkps)
                        <a href="{{ asset('storage/rkps/' . $pengajuan->file_rkps) }}" target="_blank" class="btn btn-view"><i class="fas fa-file-pdf"></i> Lihat PDF</a>
                        <iframe src="{{ asset('storage/rkps/' . $pengajuan->file_rkps) }}" style="width:100%;height:400px;border:1px solid #ddd;border-radius:4px;margin-top:10px"></iframe>
                    @else
                        <span style="color:#999">Tidak ada file</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Catatan Verifikasi</th>
                <td>{!! nl2br(e($pengajuan->catatan_admin ?? '-')) !!}</td>
            </tr>
            <tr>
                <th>Verified At</th>
                <td>{{ $pengajuan->verified_at ? \Carbon\Carbon::parse($pengajuan->verified_at)->format('d/m/Y H:i') : '-' }}</td>
            </tr>
        </table>

        @if(in_array($pengajuan->status ?? '', ['Ditolak', 'Revisi']))
            <div style="margin-top:20px;padding:15px;border-radius:6px;background:{{ $pengajuan->status == 'Ditolak' ? '#fdecea' : '#fff3e0' }};border:1px solid {{ $pengajuan->status == 'Ditolak' ? '#c62828' : '#e65100' }}">
                <h4 style="margin:0 0 8px;color:{{ $pengajuan->status == 'Ditolak' ? '#c62828' : '#e65100' }}">
                    <i class="fas fa-exclamation-circle"></i> Pengajuan {{ $pengajuan->status }}
                </h4>
                <p style="margin:0 0 12px;font-size:13px;color:#555">{{ $pengajuan->catatan_admin ?? 'Tidak ada catatan.' }}</p>
                <a href="{{ route('kelompok.pengajuan-rkps.ajukan-ulang', $pengajuan->id) }}" class="btn btn-save"><i class="fas fa-redo"></i> Ajukan Ulang</a>
            </div>
        @endif

        <div style="margin-top:25px">
            <a href="{{ route('kelompok.pengajuan-rkps') }}" class="btn"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
    </div>
</div>
@endsection
