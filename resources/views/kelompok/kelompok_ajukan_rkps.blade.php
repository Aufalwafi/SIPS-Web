@extends('layouts.app')
@section('title', 'Ajukan RKPS - SI PS ONLINE')

@section('content')
<div class="content">
    <div class="box" style="max-width:700px">
        @if(session('success'))<div class="alert">{!! session('success') !!}</div>@endif
        @if(session('error'))<div class="alert-error">{!! session('error') !!}</div>@endif

        <h2 style="margin-bottom:20px"><i class="fas fa-file-alt" style="color:#1565c0"></i> Ajukan RKPS</h2>

        <form method="POST" action="{{ route('kelompok.pengajuan-rkps.simpan') }}" enctype="multipart/form-data">
            @csrf

            <div style="margin-bottom:15px">
                <label style="display:block;margin-bottom:5px;color:#333;font-size:13px;font-weight:600">Tahun Mulai <span style="color:red">*</span></label>
                <input type="number" name="tahun_mulai" min="1900" max="2200" value="{{ old('tahun_mulai') }}" required style="width:100%;height:36px;padding:6px 10px;font-size:13px;border:1px solid #ccc;border-radius:4px">
                @error('tahun_mulai')<div style="margin-top:4px;color:#c62828;font-size:11px">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom:15px">
                <label style="display:block;margin-bottom:5px;color:#333;font-size:13px;font-weight:600">Tahun Selesai <span style="color:red">*</span></label>
                <input type="number" name="tahun_selesai" min="1900" max="2200" value="{{ old('tahun_selesai') }}" required style="width:100%;height:36px;padding:6px 10px;font-size:13px;border:1px solid #ccc;border-radius:4px">
                @error('tahun_selesai')<div style="margin-top:4px;color:#c62828;font-size:11px">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom:20px">
                <label style="display:block;margin-bottom:5px;color:#333;font-size:13px;font-weight:600">Dokumen RKPS (PDF) <span style="color:red">*</span></label>
                <input type="file" name="file_rkps" accept=".pdf" required style="width:100%;padding:6px 0;font-size:13px;border:1px solid #ccc;border-radius:4px">
                <div style="margin-top:4px;color:#777;font-size:11px">Format PDF, maksimal 10 MB.</div>
                @error('file_rkps')<div style="margin-top:4px;color:#c62828;font-size:11px">{{ $message }}</div>@enderror
            </div>

            <div style="display:flex;gap:10px">
                <button type="submit" class="btn btn-save"><i class="fas fa-paper-plane"></i> Kirim Pengajuan</button>
                <a href="{{ route('kelompok.pengajuan-rkps') }}" class="btn"><i class="fas fa-arrow-left"></i> Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
