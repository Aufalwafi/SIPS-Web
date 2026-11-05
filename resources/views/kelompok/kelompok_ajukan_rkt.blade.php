@extends('layouts.app')
@section('title', 'Ajukan RKT - SI PS ONLINE')

@section('content')
<div class="content">
    <div class="box" style="max-width:700px">
        @if(session('success'))<div class="alert">{!! session('success') !!}</div>@endif
        @if(session('error'))<div class="alert-error">{!! session('error') !!}</div>@endif

        <h2 style="margin-bottom:20px"><i class="fas fa-file-signature" style="color:#1565c0"></i> Ajukan RKT</h2>

        <form method="POST" action="{{ route('kelompok.pengajuan-rkt.simpan') }}" enctype="multipart/form-data">
            @csrf

            <div style="margin-bottom:15px">
                <label style="display:block;margin-bottom:5px;color:#333;font-size:13px;font-weight:600">Tahun <span style="color:red">*</span></label>
                <input type="number" name="tahun" min="1900" max="2200" value="{{ old('tahun') }}" required style="width:100%;height:36px;padding:6px 10px;font-size:13px;border:1px solid #ccc;border-radius:4px">
                @error('tahun')<div style="margin-top:4px;color:#c62828;font-size:11px">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom:20px">
                <label style="display:block;margin-bottom:5px;color:#333;font-size:13px;font-weight:600">Dokumen RKT (PDF) <span style="color:red">*</span></label>
                <input type="file" name="file_rkt" accept=".pdf" required style="width:100%;padding:6px 0;font-size:13px;border:1px solid #ccc;border-radius:4px">
                <div style="margin-top:4px;color:#777;font-size:11px">Format PDF, maksimal 10 MB.</div>
                @error('file_rkt')<div style="margin-top:4px;color:#c62828;font-size:11px">{{ $message }}</div>@enderror
            </div>

            <div style="display:flex;gap:10px">
                <button type="submit" class="btn btn-save"><i class="fas fa-paper-plane"></i> Kirim Pengajuan</button>
                <a href="{{ route('kelompok.pengajuan-rkt') }}" class="btn"><i class="fas fa-arrow-left"></i> Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
