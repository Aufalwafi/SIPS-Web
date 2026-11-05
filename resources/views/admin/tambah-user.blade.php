@extends('layouts.app')
@section('title', 'Tambah User - SI PS ONLINE')
@push('styles')
<style>
.form-group input,.form-group select{width:100%;height:36px;padding:6px 10px;font-size:13px;border:1px solid #ccc;border-radius:4px;background:#fff;color:#333}
.form-group input:focus,.form-group select:focus{border-color:#1b5e20;outline:none;box-shadow:0 0 5px rgba(27,94,32,.3)}
.form-group .error{color:#c62828;font-size:11px;margin-top:3px}
.form-buttons{display:flex;gap:8px;margin-top:20px}
</style>
@endpush
@section('content')
<div class="content"><div class="box" style="max-width:600px">
<h2><i class="fas fa-user-plus" style="color:#2e7d32"></i> Tambah User Baru</h2>
@if($errors->any())
<div style="background:#f8d7da;color:#721c24;padding:10px;border-radius:4px;margin-bottom:15px;font-size:12px">
<ul style="margin:0;padding-left:15px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif
<form method="POST" action="{{ route('admin.data-user.store') }}">
@csrf
<div class="form-group"><label>Nama Lengkap</label><input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Contoh: Budi Santoso">@if($errors->has('nama'))<div class="error">{{ $errors->first('nama') }}</div>@endif</div>
<div class="form-group"><label>Username</label><input type="text" name="username" value="{{ old('username') }}" required placeholder="Contoh: budisantoso">@if($errors->has('username'))<div class="error">{{ $errors->first('username') }}</div>@endif</div>
<div class="form-group"><label>Password</label><input type="password" name="password" required placeholder="Minimal 6 karakter">@if($errors->has('password'))<div class="error">{{ $errors->first('password') }}</div>@endif</div>
<div class="form-group"><label>Konfirmasi Password</label><input type="password" name="password_confirmation" required placeholder="Ulangi password"></div>
<div class="form-group"><label>Role</label><select name="role" id="roleSelect" required><option value="">-- Pilih Role --</option><option value="admin" @selected(old('role')==='admin')>Admin Utama</option><option value="admin_cdk" @selected(old('role')==='admin_cdk')>Admin CDK</option><option value="pendamping" @selected(old('role')==='pendamping')>Pendamping</option><option value="kelompok" @selected(old('role')==='kelompok')>Kelompok</option></select></div>
<div class="form-group" id="cdkGroup" style="display:none"><label>CDK Wilayah</label><select name="id_cdk"><option value="">-- Pilih CDK --</option>@foreach($daftarCdk as $c)<option value="{{ $c->id }}" @selected(old('id_cdk')==$c->id)>{{ $c->nama }}</option>@endforeach</select></div>
<div class="form-group" id="kelompokGroup" style="display:none"><label>Kelompok</label><select name="id_kelompok"><option value="">-- Pilih Kelompok --</option>@foreach($daftarKelompok as $k)<option value="{{ $k->id }}" @selected(old('id_kelompok')==$k->id)>{{ $k->nama_kelompok }}</option>@endforeach</select></div>
<div class="form-buttons"><button type="submit" class="btn btn-save"><i class="fas fa-save"></i> Simpan</button><a href="{{ route('admin.data-user') }}" class="btn btn-outline">Batal</a></div>
</form>
</div></div>
@endsection
@push('scripts')
<script>
var sel=document.getElementById('roleSelect');
var grp=document.getElementById('kelompokGroup');
var cdk=document.getElementById('cdkGroup');
function toggleForm(){
    grp.style.display=sel.value==='kelompok'?'block':'none';
    cdk.style.display=sel.value==='admin_cdk'?'block':'none';
}
sel.addEventListener('change',toggleForm);
toggleForm();
</script>
@endpush