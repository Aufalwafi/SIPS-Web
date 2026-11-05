@extends('layouts.app')
@section('title', 'Edit RKPS - SI PS ONLINE')
@push('styles')
<style>
.form-group input,.form-group select{width:100%;height:36px;padding:6px 10px;font-size:13px;border:1px solid #ccc;border-radius:4px}.error{color:#c62828;font-size:11px;margin-top:4px}.help{color:#777;font-size:11px;margin-top:4px}.form-buttons{display:flex;gap:8px;margin-top:20px}
</style>
@endpush
@section('content')
<div class="content"><div class="box" style="max-width:700px"><h2><i class="fas fa-file-pen" style="color:#1565c0"></i> Edit Data RKPS</h2>
@if($errors->any())<div class="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('admin.data-rkps.update', $rkps->id_rkps) }}" enctype="multipart/form-data">@csrf @method('PUT')
<div class="form-group"><label>Kelompok</label><select name="id_kelompok" required><option value="">-- Pilih Kelompok --</option>@foreach($daftarKelompok as $k)<option value="{{ $k->id }}" @selected(old('id_kelompok',$rkps->id_kelompok)==$k->id)>{{ $k->nama_kelompok }}</option>@endforeach</select>@error('id_kelompok')<div class="error">{{ $message }}</div>@enderror</div>
<div class="form-group"><label>Tahun Mulai</label><input type="number" name="tahun_mulai" min="1900" max="2200" value="{{ old('tahun_mulai',$rkps->tahun_mulai) }}" required></div>
<div class="form-group"><label>Tahun Selesai</label><input type="number" name="tahun_selesai" min="1900" max="2200" value="{{ old('tahun_selesai',$rkps->tahun_selesai) }}" required></div>
<div class="form-group"><label>No Penilaian</label><input type="text" name="no_penilaian" value="{{ old('no_penilaian',$rkps->no_penilaian) }}"></div>
<div class="form-group"><label>Tanggal Penilaian</label><input type="date" name="tgl_penilaian" value="{{ old('tgl_penilaian',$rkps->tgl_penilaian) }}"></div>
<div class="form-group"><label>No Pengesahan</label><input type="text" name="no_pengesahan" value="{{ old('no_pengesahan',$rkps->no_pengesahan) }}"></div>
<div class="form-group"><label>Tanggal Pengesahan</label><input type="date" name="tgl_pengesahan" value="{{ old('tgl_pengesahan',$rkps->tgl_pengesahan) }}"></div>
<div class="form-group"><label>Ganti Dokumen RKPS</label><input type="file" name="file_rkps" accept="application/pdf">@if($rkps->file_rkps)<div class="help">Dokumen saat ini: <a href="{{ asset('uploads/rkps/'.$rkps->file_rkps) }}" target="_blank">Lihat dokumen</a>. Kosongkan jika tidak diganti.</div>@else<div class="help">Belum ada dokumen. Format PDF, maksimal 10 MB.</div>@endif</div>
<div class="form-buttons"><button type="submit" class="btn btn-save"><i class="fas fa-save"></i> Simpan Perubahan</button></div>
</form></div></div>
@endsection
