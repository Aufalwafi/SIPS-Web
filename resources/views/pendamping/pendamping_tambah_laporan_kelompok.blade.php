@extends('layouts.app')
@section('title', 'Tambah Laporan - '.($kelompok->nama_kelompok ?? ''))
@section('content')
<div class="content"><div class="box">
<a href="{{ route('pendamping.kelompok-saya.laporan',$kelompok->id) }}" class="btn btn-outline" style="margin-bottom:15px"><i class="fas fa-arrow-left"></i> Kembali ke Laporan</a>
<h2><i class="fas fa-plus-circle" style="color:var(--green-header)"></i> Tambah Laporan — {{ $kelompok->nama_kelompok }}</h2>
@if($errors->any())<div class="alert-error"><ul style="margin:0;padding-left:15px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('pendamping.kelompok-saya.laporan.simpan',$kelompok->id) }}" enctype="multipart/form-data">
@csrf
<div class="form-group"><label>Tanggal *</label><input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required class="form-control"></div>
<div class="form-group"><label>Nama Kegiatan *</label><input type="text" name="kegiatan" value="{{ old('kegiatan') }}" required maxlength="200" class="form-control"></div>
<div class="form-group"><label>Deskripsi</label><textarea name="deskripsi" rows="4" class="form-control">{{ old('deskripsi') }}</textarea></div>
<div class="form-group"><label>Foto (opsional)</label><input type="file" name="foto" accept="image/*" class="form-control"></div>
<div class="form-buttons"><button type="submit" class="btn btn-save"><i class="fas fa-save"></i> Simpan Laporan</button></div>
</form>
</div></div>
@endsection
