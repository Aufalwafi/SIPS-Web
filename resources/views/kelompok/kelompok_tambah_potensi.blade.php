@extends('layouts.app')
@section('title', 'Tambah Potensi - SI PS ONLINE')

@section('content')
<div class="content">
    <div class="box" style="max-width:750px">
        @if(session('success'))<div class="alert">{!! session('success') !!}</div>@endif
        @if(session('error'))<div class="alert-error">{!! session('error') !!}</div>@endif

        <h2 style="margin-bottom:20px"><i class="fas fa-plus-circle" style="color:#f9a825"></i> Tambah Potensi</h2>

        <form method="POST" action="{{ route('kelompok.potensi.simpan') }}" enctype="multipart/form-data">
            @csrf

            <div style="margin-bottom:15px">
                <label style="display:block;margin-bottom:5px;color:#333;font-size:13px;font-weight:600">Jenis Potensi <span style="color:red">*</span></label>
                <select name="jenis_potensi" required style="width:100%;height:36px;padding:6px 10px;font-size:13px;border:1px solid #ccc;border-radius:4px">
                    <option value="">-- Pilih Jenis Potensi --</option>
                    @foreach(['Kayu', 'Non Kayu', 'Jasa Lingkungan'] as $jp)
                        <option value="{{ $jp }}" @selected(old('jenis_potensi') === $jp)>{{ $jp }}</option>
                    @endforeach
                </select>
                @error('jenis_potensi')<div style="margin-top:4px;color:#c62828;font-size:11px">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom:15px">
                <label style="display:block;margin-bottom:5px;color:#333;font-size:13px;font-weight:600">Jenis Detail <span style="color:red">*</span></label>
                <input type="text" name="jenis_detail" value="{{ old('jenis_detail') }}" required style="width:100%;height:36px;padding:6px 10px;font-size:13px;border:1px solid #ccc;border-radius:4px">
                @error('jenis_detail')<div style="margin-top:4px;color:#c62828;font-size:11px">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom:15px">
                <label style="display:block;margin-bottom:5px;color:#333;font-size:13px;font-weight:600">Tahun Tanam</label>
                <input type="number" name="tahun_tanam" min="1900" max="2155" placeholder="cth: 2020" value="{{ old('tahun_tanam') }}" style="width:100%;height:36px;padding:6px 10px;font-size:13px;border:1px solid #ccc;border-radius:4px">
                @error('tahun_tanam')<div style="margin-top:4px;color:#c62828;font-size:11px">{{ $message }}</div>@enderror
            </div>

            <div style="display:flex;gap:15px;margin-bottom:15px">
                <div style="flex:1">
                    <label style="display:block;margin-bottom:5px;color:#333;font-size:13px;font-weight:600">Luasan</label>
                    <input type="number" name="luasan" step="0.01" min="0" placeholder="cth: 12.5" value="{{ old('luasan') }}" style="width:100%;height:36px;padding:6px 10px;font-size:13px;border:1px solid #ccc;border-radius:4px">
                    @error('luasan')<div style="margin-top:4px;color:#c62828;font-size:11px">{{ $message }}</div>@enderror
                </div>
                <div style="flex:1">
                    <label style="display:block;margin-bottom:5px;color:#333;font-size:13px;font-weight:600">Satuan</label>
                    <input type="text" name="satuan" placeholder="cth: Ha" value="{{ old('satuan') }}" style="width:100%;height:36px;padding:6px 10px;font-size:13px;border:1px solid #ccc;border-radius:4px">
                    @error('satuan')<div style="margin-top:4px;color:#c62828;font-size:11px">{{ $message }}</div>@enderror
                </div>
            </div>

            <div style="display:flex;gap:15px;margin-bottom:15px">
                <div style="flex:1">
                    <label style="display:block;margin-bottom:5px;color:#333;font-size:13px;font-weight:600">Kondisi</label>
                    <input type="text" name="kondisi" placeholder="cth: Baik" value="{{ old('kondisi') }}" style="width:100%;height:36px;padding:6px 10px;font-size:13px;border:1px solid #ccc;border-radius:4px">
                    @error('kondisi')<div style="margin-top:4px;color:#c62828;font-size:11px">{{ $message }}</div>@enderror
                </div>
                <div style="flex:1">
                    <label style="display:block;margin-bottom:5px;color:#333;font-size:13px;font-weight:600">Lokasi</label>
                    <input type="text" name="lokasi" value="{{ old('lokasi') }}" style="width:100%;height:36px;padding:6px 10px;font-size:13px;border:1px solid #ccc;border-radius:4px">
                    @error('lokasi')<div style="margin-top:4px;color:#c62828;font-size:11px">{{ $message }}</div>@enderror
                </div>
            </div>

            <div style="margin-bottom:15px">
                <label style="display:block;margin-bottom:5px;color:#333;font-size:13px;font-weight:600">Keterangan</label>
                <textarea name="keterangan" rows="4" style="width:100%;padding:8px 10px;font-size:13px;border:1px solid #ccc;border-radius:4px;resize:vertical">{{ old('keterangan') }}</textarea>
                @error('keterangan')<div style="margin-top:4px;color:#c62828;font-size:11px">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom:20px">
                <label style="display:block;margin-bottom:5px;color:#333;font-size:13px;font-weight:600">Foto</label>
                <input type="file" name="foto" accept="image/*" style="width:100%;height:36px;padding:6px 10px;font-size:13px;border:1px solid #ccc;border-radius:4px">
                @error('foto')<div style="margin-top:4px;color:#c62828;font-size:11px">{{ $message }}</div>@enderror
            </div>

            <div style="display:flex;gap:10px">
                <button type="submit" class="btn btn-save"><i class="fas fa-save"></i> Simpan</button>
                <a href="{{ route('kelompok.potensi') }}" class="btn"><i class="fas fa-arrow-left"></i> Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
