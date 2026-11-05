@extends('layouts.app')
@section('title', 'Tambah Anggota - SI PS ONLINE')
@push('styles')
<style>
.form-group input,.form-group select,.form-group textarea{width:100%;padding:8px 10px;font-size:13px;border:1px solid #ccc;border-radius:4px;background:#fff;color:#333;box-sizing:border-box}
.form-group input:focus,.form-group select:focus,.form-group textarea:focus{border-color:#1b5e20;outline:none;box-shadow:0 0 5px rgba(27,94,32,.3)}
.form-group .error{color:#c62828;font-size:11px;margin-top:3px}
.form-buttons{display:flex;gap:8px;margin-top:20px}
</style>
@endpush
@section('content')
<div class="content"><div class="box" style="max-width:700px">
<h2><i class="fas fa-user-plus" style="color:#2e7d32"></i> Tambah Anggota Baru</h2>
@if($errors->any())
<div style="background:#f8d7da;color:#721c24;padding:10px;border-radius:4px;margin-bottom:15px;font-size:12px">
<ul style="margin:0;padding-left:15px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif
<form method="POST" action="{{ route('kelompok.anggota.simpan') }}">
@csrf
<div class="form-group" style="margin-bottom:12px">
<label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">Nama Anggota *</label>
<input type="text" name="nama_anggota" value="{{ old('nama_anggota') }}" required placeholder="Nama lengkap anggota">
@if($errors->has('nama_anggota'))<div class="error">{{ $errors->first('nama_anggota') }}</div>@endif
</div>
<div class="form-group" style="margin-bottom:12px">
<label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">NIK</label>
<input type="text" name="nik" value="{{ old('nik') }}" placeholder="Nomor Induk Kependudukan (maks 20 karakter)">
@if($errors->has('nik'))<div class="error">{{ $errors->first('nik') }}</div>@endif
</div>
<div class="form-group" style="margin-bottom:12px">
<label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">Jenis Kelamin</label>
<select name="jenis_kelamin">
<option value="">-- Pilih --</option>
<option value="L" @selected(old('jenis_kelamin')==='L')>Laki-laki</option>
<option value="P" @selected(old('jenis_kelamin')==='P')>Perempuan</option>
</select>
</div>
<div class="form-group" style="margin-bottom:12px">
<label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">Alamat</label>
<textarea name="alamat" rows="3" placeholder="Alamat lengkap">{{ old('alamat') }}</textarea>
</div>
<div class="form-group" style="margin-bottom:12px">
<label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">Luas Garapan (ha)</label>
<input type="number" step="0.01" name="luas_garapan" value="{{ old('luas_garapan') }}" placeholder="Contoh: 2.5">
@if($errors->has('luas_garapan'))<div class="error">{{ $errors->first('luas_garapan') }}</div>@endif
</div>
<div class="form-group" style="margin-bottom:12px">
<label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">Komoditas Utama</label>
<input type="text" name="komoditas_utama" value="{{ old('komoditas_utama') }}" placeholder="Contoh: Jati, Mahoni">
@if($errors->has('komoditas_utama'))<div class="error">{{ $errors->first('komoditas_utama') }}</div>@endif
</div>
<div class="form-buttons">
<button type="submit" class="btn btn-save"><i class="fas fa-save"></i> Simpan</button>
<a href="{{ route('kelompok.anggota') }}" style="background:#757575;color:#fff;padding:8px 16px;border-radius:4px;text-decoration:none;display:inline-flex;align-items:center;gap:4px"><i class="fas fa-arrow-left"></i> Batal</a>
</div>
</form>
</div></div>
@endsection
