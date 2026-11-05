@extends('layouts.app')
@section('title', 'Laporan Kegiatan - '.($kelompok->nama_kelompok ?? ''))
@section('content')
<div class="content"><div class="box">
<a href="{{ route('pendamping.kelompok-saya.detail',$kelompok->id) }}" class="btn btn-outline" style="margin-bottom:15px"><i class="fas fa-arrow-left"></i> Kembali ke Detail Kelompok</a>
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px"><h2 style="margin:0"><i class="fas fa-clipboard-list" style="color:var(--green-header)"></i> Laporan Kegiatan — {{ $kelompok->nama_kelompok }}</h2><a href="{{ route('pendamping.kelompok-saya.laporan.tambah',$kelompok->id) }}" class="btn btn-save"><i class="fas fa-plus"></i> Tambah Laporan</a></div>
<form method="GET" style="display:flex;gap:10px;margin-bottom:15px;flex-wrap:wrap">
<input type="date" name="tanggal_awal" value="{{ request('tanggal_awal') }}" class="form-control">
<input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}" class="form-control">
<button type="submit" class="btn btn-view">Filter</button>
</form>
<div class="table-wrap"><table>
<thead><tr><th style="width:35px;text-align:center">No</th><th>Tanggal</th><th>Kegiatan</th><th>Deskripsi</th><th>Foto</th><th style="text-align:center">Aksi</th></tr></thead>
<tbody>@forelse($laporan as $i=>$l)<tr>
<td style="text-align:center">{{ $i+1 }}</td><td>{{ \Carbon\Carbon::parse($l->tanggal)->format('d/m/Y') }}</td><td>{{ $l->kegiatan }}</td><td>{{ \Illuminate\Support\Str::limit($l->deskripsi ?? '-',60) }}</td><td>@if($l->foto)<img src="{{ asset('storage/laporan/'.$l->foto) }}" style="height:45px;border-radius:4px" alt="foto">@else-@endif</td><td style="text-align:center"><a href="{{ route('pendamping.laporan-kegiatan.detail',$l->id) }}" class="btn btn-view btn-sm"><i class="fas fa-eye"></i></a> <a href="{{ route('pendamping.laporan-kegiatan.edit',$l->id) }}" class="btn btn-edit btn-sm"><i class="fas fa-edit"></i></a> <form method="POST" action="{{ route('pendamping.laporan-kegiatan.hapus',$l->id) }}" style="display:inline" onsubmit="return confirm('Yakin hapus laporan ini?')">@csrf @method('DELETE')<button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></form></td>
</tr>@empty<tr><td colspan="6" style="text-align:center;padding:30px;color:#999">Belum ada laporan kegiatan.</td></tr>@endforelse</tbody>
</table></div>
</div></div>
@endsection
