@extends('layouts.app')
@section('title', 'Input NTE - '.($kelompok->nama_kelompok ?? ''))
@section('content')
<div class="content"><div class="box">
<a href="{{ route('pendamping.kelompok-saya.data-nte',$kelompok->id) }}" class="btn btn-outline" style="margin-bottom:15px"><i class="fas fa-arrow-left"></i> Kembali ke Data NTE</a>
<h2><i class="fas fa-plus-circle" style="color:var(--green-header)"></i> Input NTE — {{ $kelompok->nama_kelompok }}</h2>
@if($errors->any())<div class="alert-error"><ul style="margin:0;padding-left:15px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('pendamping.kelompok-saya.simpan-nte',$kelompok->id) }}">
@csrf
<div class="form-group"><label>Kelompok</label><input type="text" value="{{ $kelompok->nama_kelompok }}" readonly class="form-control"></div>
<div class="form-group"><label>KUPS (opsional)</label><select name="id_kups" class="form-control"><option value="">-- Pilih KUPS --</option>@foreach($kups as $k)<option value="{{ $k->id }}">{{ $k->nama_kups }}</option>@endforeach</select></div>
<div class="form-group"><label>Nama Produk/Komoditas *</label><input type="text" name="nama_produk" value="{{ old('nama_produk') }}" required class="form-control"></div>
<div class="form-group"><label>Luas Areal</label><input type="text" name="luas_areal" value="{{ old('luas_areal') }}" class="form-control"></div>
<div class="form-group"><label>Pola Pengolahan</label><input type="text" name="pola_pengolahan" value="{{ old('pola_pengolahan') }}" class="form-control"></div>
<div class="form-group"><label>Pola Pengelolaan</label><input type="text" name="pola_pengelolaan" value="{{ old('pola_pengelolaan') }}" class="form-control"></div>
<div class="form-group"><label>Potensi Jasa Lingkungan</label><input type="text" name="potensi_jasa_lingkungan" value="{{ old('potensi_jasa_lingkungan') }}" class="form-control"></div>
<div class="form-group"><label>Sumber Modal</label><input type="text" name="sumber_modal" value="{{ old('sumber_modal') }}" class="form-control"></div>
<div class="form-group"><label>Area Pemasaran</label><input type="text" name="area_pemasaran" value="{{ old('area_pemasaran') }}" class="form-control"></div>
<div class="form-group"><label>Satuan</label><input type="text" name="satuan" value="{{ old('satuan') }}" class="form-control"></div>
<div class="form-group"><label>Tahun</label><input type="number" name="tahun" value="{{ old('tahun', date('Y')) }}" min="1900" max="2200" class="form-control"></div>
<div class="form-buttons"><button type="submit" class="btn btn-save"><i class="fas fa-save"></i> Simpan Data NTE</button></div>
</form>
</div></div>
@endsection
