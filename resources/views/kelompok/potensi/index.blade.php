@extends('layouts.app')
@section('title', 'Potensi Kelompok - SI PS ONLINE')
@section('content')
<div class="content"><div class="box">
@if(session('success'))<div class="alert">{!! session('success') !!}</div>@endif
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px"><h2 style="margin:0">Potensi Kelompok</h2><a href="{{ route('kelompok.potensi.tambah') }}" class="btn btn-tambah">Tambah Potensi</a></div>
<div class="table-wrap"><table><thead><tr><th>No</th><th>Jenis Potensi</th><th>Detail Potensi</th><th>Tahun Tanam</th><th>Luasan</th><th>Satuan</th><th>Kondisi</th><th>Lokasi</th><th>Keterangan</th><th>Foto</th><th>Aksi</th></tr></thead><tbody>
@forelse($potensiList as $i => $p)<tr><td>{{ $i + 1 }}</td><td>{{ $p->jenis_potensi ?? '-' }}</td><td>{{ $p->jenis_detail ?? '-' }}</td><td>{{ $p->tahun_tanam ?? '-' }}</td><td>{{ $p->luasan ?? '-' }}</td><td>{{ $p->satuan ?? '-' }}</td><td>{{ $p->kondisi ?? '-' }}</td><td>{{ $p->lokasi ?? '-' }}</td><td>{{ \Str::limit($p->keterangan ?? '-', 50) }}</td><td>@if($p->foto)<img src="{{ asset('storage/potensi/' . $p->foto) }}" alt="Foto potensi" style="width:50px;height:50px;object-fit:cover">@else-@endif</td><td><a href="{{ route('kelompok.potensi.edit', $p->id) }}" class="btn btn-edit">Edit</a><form action="{{ route('kelompok.potensi.hapus', $p->id) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button class="btn btn-delete">Hapus</button></form></td></tr>
@empty<tr><td colspan="11" class="empty">Belum ada data potensi.</td></tr>@endforelse
</tbody></table></div></div></div>
@endsection
