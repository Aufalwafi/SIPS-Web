@extends('layouts.app')
@section('title', 'Edit Kegiatan - SI PS ONLINE')

@section('content')
<div class="content">
    <div class="box" style="max-width:750px">
        @if(session('success'))<div class="alert">{!! session('success') !!}</div>@endif
        @if(session('error'))<div class="alert-error">{!! session('error') !!}</div>@endif

        <h2 style="margin-bottom:20px"><i class="fas fa-edit" style="color:#1565c0"></i> Edit Kegiatan</h2>

        <form method="POST" action="{{ route('kelompok.kegiatan.update', $kegiatan->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div style="margin-bottom:15px">
                <label style="display:block;margin-bottom:5px;color:#333;font-size:13px;font-weight:600">Nama Kegiatan <span style="color:red">*</span></label>
                <input type="text" name="nama_kegiatan" value="{{ old('nama_kegiatan', $kegiatan->nama_kegiatan) }}" maxlength="200" required style="width:100%;height:36px;padding:6px 10px;font-size:13px;border:1px solid #ccc;border-radius:4px">
                @error('nama_kegiatan')<div style="margin-top:4px;color:#c62828;font-size:11px">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom:15px">
                <label style="display:block;margin-bottom:5px;color:#333;font-size:13px;font-weight:600">Tanggal <span style="color:red">*</span></label>
                <input type="date" name="tanggal" value="{{ old('tanggal', $kegiatan->tanggal) }}" required style="width:100%;height:36px;padding:6px 10px;font-size:13px;border:1px solid #ccc;border-radius:4px">
                @error('tanggal')<div style="margin-top:4px;color:#c62828;font-size:11px">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom:15px">
                <label style="display:block;margin-bottom:5px;color:#333;font-size:13px;font-weight:600">Lokasi</label>
                <input type="text" name="lokasi" value="{{ old('lokasi', $kegiatan->lokasi) }}" style="width:100%;height:36px;padding:6px 10px;font-size:13px;border:1px solid #ccc;border-radius:4px">
                @error('lokasi')<div style="margin-top:4px;color:#c62828;font-size:11px">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom:15px">
                <label style="display:block;margin-bottom:5px;color:#333;font-size:13px;font-weight:600">Keterangan</label>
                <textarea name="keterangan" rows="4" style="width:100%;padding:8px 10px;font-size:13px;border:1px solid #ccc;border-radius:4px;resize:vertical">{{ old('keterangan', $kegiatan->keterangan) }}</textarea>
                @error('keterangan')<div style="margin-top:4px;color:#c62828;font-size:11px">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom:20px">
                <label style="display:block;margin-bottom:5px;color:#333;font-size:13px;font-weight:600">File KML</label>
                @if($kegiatan->kml_file)
                    <div style="margin-bottom:8px;padding:8px 12px;background:#e8f5e9;border-radius:4px;font-size:13px">
                        <i class="fas fa-file-code" style="color:#2e7d32"></i>
                        File saat ini: <a href="{{ asset('storage/kegiatan/' . $kegiatan->kml_file) }}" target="_blank">{{ $kegiatan->kml_file }}</a>
                    </div>
                @endif
                <input type="file" name="kml_file" accept=".kml" style="width:100%;padding:6px 0;font-size:13px;border:1px solid #ccc;border-radius:4px">
                <div style="margin-top:4px;color:#777;font-size:11px">Format .kml. Kosongkan jika tidak ingin mengganti.</div>
                @error('kml_file')<div style="margin-top:4px;color:#c62828;font-size:11px">{{ $message }}</div>@enderror
            </div>

            <div style="display:flex;gap:10px">
                <button type="submit" class="btn btn-save"><i class="fas fa-save"></i> Simpan</button>
                <a href="{{ route('kelompok.kegiatan') }}" class="btn"><i class="fas fa-arrow-left"></i> Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
