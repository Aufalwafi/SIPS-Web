@extends('layouts.app')
@section('title', 'Tambah Anggota - '.($kelompok->nama_kelompok ?? ''))
@section('content')
<div class="content"><div class="box" style="max-width:700px">
<a href="{{ route('pendamping.kelompok-saya.anggota',$kelompok->id) }}" class="btn btn-outline" style="margin-bottom:15px"><i class="fas fa-arrow-left"></i> Kembali ke Data Anggota</a>
<h2><i class="fas fa-user-plus" style="color:var(--green-header)"></i> Tambah Anggota — {{ $kelompok->nama_kelompok }}</h2>
@if($errors->any())<div class="alert-error"><ul style="margin:0;padding-left:18px">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('pendamping.kelompok-saya.anggota.simpan',$kelompok->id) }}">@csrf
<div class="form-group"><label>Nama Anggota *</label><input type="text" name="nama_anggota" value="{{ old('nama_anggota') }}" required maxlength="100"></div>
<div class="form-group"><label>NIK</label><input type="text" name="nik" value="{{ old('nik') }}" maxlength="20"></div>
<div class="form-group"><label>Jenis Kelamin</label><select name="jenis_kelamin"><option value="">-- Pilih --</option><option value="L" @selected(old('jenis_kelamin')==='L')>Laki-laki</option><option value="P" @selected(old('jenis_kelamin')==='P')>Perempuan</option></select></div>
<div class="form-group"><label>Alamat</label><textarea name="alamat" rows="3">{{ old('alamat') }}</textarea></div>
<div class="form-group"><label>Luas Garapan (ha)</label><input type="number" step="0.01" name="luas_garapan" value="{{ old('luas_garapan') }}"></div>
<div class="form-group"><label>Komoditas Utama</label><input type="text" name="komoditas_utama" value="{{ old('komoditas_utama') }}" maxlength="100"></div>
<div class="form-buttons"><button type="submit" class="btn btn-save"><i class="fas fa-save"></i> Simpan</button><a href="{{ route('pendamping.kelompok-saya.anggota',$kelompok->id) }}" class="btn btn-outline">Batal</a></div>
</form></div></div>
@endsection