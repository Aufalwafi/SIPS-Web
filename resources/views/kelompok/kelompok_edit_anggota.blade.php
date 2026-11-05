@extends('layouts.app')
@section('title', 'Edit Anggota - SI PS ONLINE')
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
<h2><i class="fas fa-user-edit" style="color:#1565c0"></i> Edit Anggota</h2>
@if($errors->any())
<div style="background:#f8d7da;color:#721c24;padding:10px;border-radius:4px;margin-bottom:15px;font-size:12px">
<ul style="margin:0;padding-left:15px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif
<form method="POST" action="{{ route('kelompok.anggota.update', $anggota->id) }}">
@csrf
@method('PUT')
<div class="form-group" style="margin-bottom:12px">
<label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">Nama Anggota *</label>
<input type="text" name="nama_anggota" value="{{ old('nama_anggota', $anggota->nama_anggota) }}" required>
@if($errors->has('nama_anggota'))<div class="error">{{ $errors->first('nama_anggota') }}</div>@endif
</div>
<div class="form-group" style="margin-bottom:12px">
<label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">NIK</label>
<input type="text" name="nik" value="{{ old('nik', $anggota->nik) }}" placeholder="Maks 20 karakter">
</div>
<div class="form-group" style="margin-bottom:12px">
<label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">Jenis Kelamin</label>
<select name="jenis_kelamin">
<option value="">-- Pilih --</option>
<option value="L" @selected(old('jenis_kelamin', $anggota->jenis_kelamin)==='L')>Laki-laki</option>
<option value="P" @selected(old('jenis_kelamin', $anggota->jenis_kelamin)==='P')>Perempuan</option>
</select>
</div>
<div class="form-group" style="margin-bottom:12px">
<label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">Alamat</label>
<textarea name="alamat" rows="3">{{ old('alamat', $anggota->alamat) }}</textarea>
</div>
<div class="form-group" style="margin-bottom:12px">
<label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">Luas Garapan (ha)</label>
<input type="number" step="0.01" name="luas_garapan" value="{{ old('luas_garapan', $anggota->luas_garapan) }}">
</div>
<div class="form-group" style="margin-bottom:12px">
<label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">Komoditas Utama</label>
<input type="text" name="komoditas_utama" value="{{ old('komoditas_utama', $anggota->komoditas_utama) }}">
</div>
<div class="form-buttons">
<button type="submit" class="btn btn-save"><i class="fas fa-save"></i> Simpan Perubahan</button>
<a href="{{ route('kelompok.anggota') }}" style="background:#757575;color:#fff;padding:8px 16px;border-radius:4px;text-decoration:none;display:inline-flex;align-items:center;gap:4px"><i class="fas fa-arrow-left"></i> Batal</a>
</div>
</form>
</div></div>
@endsection
