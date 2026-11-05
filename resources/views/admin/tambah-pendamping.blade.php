@extends('layouts.app')
@section('title', 'Tambah Pendamping - SI PS ONLINE')
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
<h2><i class="fas fa-user-plus" style="color:#2e7d32"></i> Tambah Pendamping Baru</h2>
@if($errors->any())
<div style="background:#f8d7da;color:#721c24;padding:10px;border-radius:4px;margin-bottom:15px;font-size:12px">
<ul style="margin:0;padding-left:15px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif
<form method="POST" action="{{ route('admin.data-pendamping.simpan') }}">
@csrf
<div class="form-group" style="margin-bottom:12px">
<label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">Nama Pendamping *</label>
<input type="text" name="nama_pendamping" value="{{ old('nama_pendamping') }}" required placeholder="Nama lengkap pendamping">
@if($errors->has('nama_pendamping'))<div class="error">{{ $errors->first('nama_pendamping') }}</div>@endif
</div>
<div class="form-group" style="margin-bottom:12px">
<label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">NIP</label>
<input type="text" name="nip" value="{{ old('nip') }}" placeholder="Nomor Induk Pegawai">
</div>
<div class="form-group" style="margin-bottom:12px">
<label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">Username *</label>
<input type="text" name="username" value="{{ old('username') }}" required placeholder="Username untuk login">
@if($errors->has('username'))<div class="error">{{ $errors->first('username') }}</div>@endif
</div>
<div class="form-group" style="margin-bottom:12px">
<label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">Password *</label>
<input type="password" name="password" required placeholder="Minimal 6 karakter">
@if($errors->has('password'))<div class="error">{{ $errors->first('password') }}</div>@endif
</div>
<div class="form-group" style="margin-bottom:12px">
<label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">Konfirmasi Password *</label>
<input type="password" name="password_confirmation" required placeholder="Ulangi password">
</div>
<div class="form-group" style="margin-bottom:12px">
<label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">No HP</label>
<input type="text" name="no_hp" value="{{ old('no_hp') }}" placeholder="Nomor handphone">
</div>
<div class="form-group" style="margin-bottom:12px">
<label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">Email</label>
<input type="email" name="email" value="{{ old('email') }}" placeholder="Email aktif">
</div>
<div class="form-group" style="margin-bottom:12px">
<label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">Alamat</label>
<textarea name="alamat" rows="3" placeholder="Alamat lengkap">{{ old('alamat') }}</textarea>
</div>
<div class="form-group" style="margin-bottom:12px">
<label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">CDK *</label>
<select name="id_cdk" required>
<option value="">-- Pilih CDK --</option>
@foreach($daftarCdk as $c)
<option value="{{ $c->id }}" @selected(old('id_cdk')==$c->id)>{{ $c->nama }}</option>
@endforeach
</select>
@if($errors->has('id_cdk'))<div class="error">{{ $errors->first('id_cdk') }}</div>@endif
</div>
<div class="form-buttons">
<button type="submit" class="btn btn-save"><i class="fas fa-save"></i> Simpan</button>
<a href="{{ route('admin.data-pendamping') }}" class="btn btn-cancel" style="background:#757575;color:#fff;padding:8px 16px;border-radius:4px;text-decoration:none"><i class="fas fa-arrow-left"></i> Batal</a>
</div>
</form>
</div></div>
@endsection
