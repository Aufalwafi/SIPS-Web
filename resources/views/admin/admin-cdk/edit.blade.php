@extends('layouts.app')
@section('title', 'Edit Admin CDK - SI PS ONLINE')
@push('styles')
<style>
.form-group input,.form-group select{width:100%;height:36px;padding:6px 10px;font-size:13px;border:1px solid #ccc;border-radius:4px;background:#fff;color:#333}
.form-group input:focus,.form-group select:focus{border-color:#1b5e20;outline:none;box-shadow:0 0 5px rgba(27,94,32,.3)}
.form-group .error{color:#c62828;font-size:11px;margin-top:3px}
.form-group .help{color:#777;font-size:11px;margin-top:3px}
.form-buttons{display:flex;gap:8px;margin-top:20px}
</style>
@endpush
@section('content')
<div class="content"><div class="box" style="max-width:600px">
<h2><i class="fas fa-building" style="color:#1565c0"></i> Edit Admin CDK</h2>
@if($errors->any())
<div style="background:#f8d7da;color:#721c24;padding:10px;border-radius:4px;margin-bottom:15px;font-size:12px"><ul style="margin:0;padding-left:15px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif
<form method="POST" action="{{ route('admin.admin-cdk.update', $user->id) }}">
@csrf
@method('PUT')
<div class="form-group"><label>Nama Lengkap</label><input type="text" name="nama" value="{{ old('nama', $user->nama) }}" required>@if($errors->has('nama'))<div class="error">{{ $errors->first('nama') }}</div>@endif</div>
<div class="form-group"><label>Username</label><input type="text" name="username" value="{{ old('username', $user->username) }}" required>@if($errors->has('username'))<div class="error">{{ $errors->first('username') }}</div>@endif</div>
<div class="form-group"><label>Password Baru <span class="help">(kosongkan jika tidak diubah)</span></label><input type="password" name="password" placeholder="Minimal 6 karakter">@if($errors->has('password'))<div class="error">{{ $errors->first('password') }}</div>@endif</div>
<div class="form-group"><label>Konfirmasi Password</label><input type="password" name="password_confirmation" placeholder="Ulangi password baru"></div>
<div class="form-group"><label>CDK</label><select name="id_cdk" required><option value="">-- Pilih CDK --</option>@foreach($daftarCdk as $c)<option value="{{ $c->id }}" @selected(old('id_cdk', $user->id_cdk)==$c->id)>{{ $c->nama }}</option>@endforeach</select>@if($errors->has('id_cdk'))<div class="error">{{ $errors->first('id_cdk') }}</div>@endif</div>
<div class="form-buttons"><button type="submit" class="btn btn-save"><i class="fas fa-save"></i> Simpan Perubahan</button></div>
</form>
</div></div>
@endsection
