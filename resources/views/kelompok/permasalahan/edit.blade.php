@extends('layouts.app')
@section('title', 'Edit Permasalahan - SI PS ONLINE')

@section('content')
<div class="content">
    <div class="box" style="max-width:750px">
        @if(session('success'))<div class="alert">{!! session('success') !!}</div>@endif
        @if(session('error'))<div class="alert-error">{!! session('error') !!}</div>@endif

        <h2 style="margin-bottom:20px"><i class="fas fa-edit" style="color:#e65100"></i> Edit Permasalahan</h2>

        <form method="POST" action="{{ route('kelompok.permasalahan.update', $permasalahan->id) }}">
            @csrf
            @method('PUT')

            <div style="margin-bottom:15px">
                <label style="display:block;margin-bottom:5px;color:#333;font-size:13px;font-weight:600">Judul <span style="color:red">*</span></label>
                <input type="text" name="judul" value="{{ old('judul', $permasalahan->judul) }}" required style="width:100%;height:36px;padding:6px 10px;font-size:13px;border:1px solid #ccc;border-radius:4px">
                @error('judul')<div style="margin-top:4px;color:#c62828;font-size:11px">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom:15px">
                <label style="display:block;margin-bottom:5px;color:#333;font-size:13px;font-weight:600">Deskripsi</label>
                <textarea name="deskripsi" rows="4" style="width:100%;padding:8px 10px;font-size:13px;border:1px solid #ccc;border-radius:4px;resize:vertical">{{ old('deskripsi', $permasalahan->deskripsi) }}</textarea>
                @error('deskripsi')<div style="margin-top:4px;color:#c62828;font-size:11px">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom:20px">
                <label style="display:block;margin-bottom:5px;color:#333;font-size:13px;font-weight:600">Status</label>
                <select name="status" style="width:100%;height:36px;padding:6px 10px;font-size:13px;border:1px solid #ccc;border-radius:4px">
                    <option value="Aktif" {{ old('status', $permasalahan->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Selesai" {{ old('status', $permasalahan->status) == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
                @error('status')<div style="margin-top:4px;color:#c62828;font-size:11px">{{ $message }}</div>@enderror
            </div>

            <div style="display:flex;gap:10px">
                <button type="submit" class="btn btn-save"><i class="fas fa-save"></i> Simpan</button>
                <a href="{{ route('kelompok.permasalahan') }}" class="btn"><i class="fas fa-arrow-left"></i> Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
