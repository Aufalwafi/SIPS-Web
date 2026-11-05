@extends('layouts.app')
@section('title', 'Tambah Laporan Kegiatan - SI PS ONLINE')
@section('content')
<div class="box" style="max-width:700px">
    <h2><i class="fas fa-file-circle-plus" style="color:var(--green-header)"></i> Tambah Laporan Kegiatan</h2>

    @if(session('success'))<div class="alert">{!! session('success') !!}</div>@endif
    @if(session('error'))<div class="alert-error">{!! session('error') !!}</div>@endif
    @if($errors->any())
        <div class="alert-error">
            <ul style="margin:0;padding-left:18px">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('pendamping.laporan-kegiatan.simpan') }}" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label>Kelompok <span style="color:red">*</span></label>
            <select name="id_kelompok" required>
                <option value="">-- Pilih Kelompok --</option>
                @foreach($daftarKelompok as $k)
                    <option value="{{ $k->id }}" @selected(old('id_kelompok') == $k->id)>{{ $k->nama_kelompok }}</option>
                @endforeach
            </select>
            @error('id_kelompok')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label>Tanggal <span style="color:red">*</span></label>
            <input type="date" name="tanggal" value="{{ old('tanggal') }}" required>
            @error('tanggal')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label>Kegiatan <span style="color:red">*</span></label>
            <input type="text" name="kegiatan" value="{{ old('kegiatan') }}" required maxlength="200" placeholder="Masukkan nama kegiatan">
            @error('kegiatan')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="deskripsi" rows="4" placeholder="Deskripsi kegiatan (opsional)">{{ old('deskripsi') }}</textarea>
            @error('deskripsi')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label>Foto</label>
            <input type="file" name="foto" accept="image/*">
            <div class="help">Format gambar (JPG, PNG), maksimal 5 MB. Opsional.</div>
            @error('foto')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="form-buttons">
            <button type="submit" class="btn btn-save"><i class="fas fa-save"></i> Simpan</button>
            <a href="{{ route('pendamping.laporan-kegiatan') }}" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Batal</a>
        </div>
    </form>
</div>
@endsection
