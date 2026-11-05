@extends('layouts.app')
@section('title', 'Edit KUPS - SI PS ONLINE')

@section('content')
<div class="content">
    <div class="box" style="max-width:750px">
        @if(session('success'))<div class="alert">{!! session('success') !!}</div>@endif
        @if(session('error'))<div class="alert-error">{!! session('error') !!}</div>@endif

        <h2 style="margin-bottom:20px"><i class="fas fa-edit" style="color:#1565c0"></i> Edit KUPS</h2>

        <form method="POST" action="{{ route('kelompok.kups.update', $kups->id) }}">
            @csrf
            @method('PUT')

            <div style="margin-bottom:15px">
                <label style="display:block;margin-bottom:5px;color:#333;font-size:13px;font-weight:600">Nama KUPS <span style="color:red">*</span></label>
                <input type="text" name="nama_kups" value="{{ old('nama_kups', $kups->nama_kups) }}" required style="width:100%;height:36px;padding:6px 10px;font-size:13px;border:1px solid #ccc;border-radius:4px">
                @error('nama_kups')<div style="margin-top:4px;color:#c62828;font-size:11px">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom:15px">
                <label style="display:block;margin-bottom:5px;color:#333;font-size:13px;font-weight:600">Ketua KUPS</label>
                <input type="text" name="ketua_kups" value="{{ old('ketua_kups', $kups->ketua_kups) }}" style="width:100%;height:36px;padding:6px 10px;font-size:13px;border:1px solid #ccc;border-radius:4px">
                @error('ketua_kups')<div style="margin-top:4px;color:#c62828;font-size:11px">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom:15px">
                <label style="display:block;margin-bottom:5px;color:#333;font-size:13px;font-weight:600">Jumlah Anggota</label>
                <input type="number" name="jumlah_anggota" value="{{ old('jumlah_anggota', $kups->jumlah_anggota) }}" min="0" style="width:100%;height:36px;padding:6px 10px;font-size:13px;border:1px solid #ccc;border-radius:4px">
                @error('jumlah_anggota')<div style="margin-top:4px;color:#c62828;font-size:11px">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom:15px">
                <label style="display:block;margin-bottom:5px;color:#333;font-size:13px;font-weight:600">Kelas KUPS</label>
                <select name="kelas_kups" style="width:100%;height:36px;padding:6px 10px;font-size:13px;border:1px solid #ccc;border-radius:4px">
                    <option value="">-- Pilih Kelas --</option>
                    <option value="Biru" {{ old('kelas_kups', $kups->kelas_kups) == 'Biru' ? 'selected' : '' }}>Biru</option>
                    <option value="Perak" {{ old('kelas_kups', $kups->kelas_kups) == 'Perak' ? 'selected' : '' }}>Perak</option>
                    <option value="Emas" {{ old('kelas_kups', $kups->kelas_kups) == 'Emas' ? 'selected' : '' }}>Emas</option>
                    <option value="Platinum" {{ old('kelas_kups', $kups->kelas_kups) == 'Platinum' ? 'selected' : '' }}>Platinum</option>
                </select>
                @error('kelas_kups')<div style="margin-top:4px;color:#c62828;font-size:11px">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom:15px">
                <label style="display:block;margin-bottom:5px;color:#333;font-size:13px;font-weight:600">Tahun Bentuk</label>
                <input type="text" name="tahun_bentuk" value="{{ old('tahun_bentuk', $kups->tahun_bentuk) }}" maxlength="4" placeholder="Contoh: 2024" style="width:100%;height:36px;padding:6px 10px;font-size:13px;border:1px solid #ccc;border-radius:4px">
                @error('tahun_bentuk')<div style="margin-top:4px;color:#c62828;font-size:11px">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom:15px">
                <label style="display:block;margin-bottom:5px;color:#333;font-size:13px;font-weight:600">Jenis Usaha</label>
                <input type="text" name="jenis_usaha" value="{{ old('jenis_usaha', $kups->jenis_usaha) }}" style="width:100%;height:36px;padding:6px 10px;font-size:13px;border:1px solid #ccc;border-radius:4px">
                @error('jenis_usaha')<div style="margin-top:4px;color:#c62828;font-size:11px">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom:15px">
                <label style="display:block;margin-bottom:5px;color:#333;font-size:13px;font-weight:600">Produk</label>
                <input type="text" name="produk" value="{{ old('produk', $kups->produk) }}" style="width:100%;height:36px;padding:6px 10px;font-size:13px;border:1px solid #ccc;border-radius:4px">
                @error('produk')<div style="margin-top:4px;color:#c62828;font-size:11px">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom:15px">
                <label style="display:block;margin-bottom:5px;color:#333;font-size:13px;font-weight:600">Produksi per Tahun</label>
                <input type="number" name="produksi_tahun" value="{{ old('produksi_tahun', $kups->produksi_tahun) }}" min="0" style="width:100%;height:36px;padding:6px 10px;font-size:13px;border:1px solid #ccc;border-radius:4px">
                @error('produksi_tahun')<div style="margin-top:4px;color:#c62828;font-size:11px">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom:15px">
                <label style="display:block;margin-bottom:5px;color:#333;font-size:13px;font-weight:600">Satuan Produksi</label>
                <input type="text" name="satuan_produksi" value="{{ old('satuan_produksi', $kups->satuan_produksi) }}" style="width:100%;height:36px;padding:6px 10px;font-size:13px;border:1px solid #ccc;border-radius:4px">
                @error('satuan_produksi')<div style="margin-top:4px;color:#c62828;font-size:11px">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom:20px">
                <label style="display:block;margin-bottom:5px;color:#333;font-size:13px;font-weight:600">Keterangan</label>
                <textarea name="keterangan" rows="4" style="width:100%;padding:8px 10px;font-size:13px;border:1px solid #ccc;border-radius:4px;resize:vertical">{{ old('keterangan', $kups->keterangan) }}</textarea>
                @error('keterangan')<div style="margin-top:4px;color:#c62828;font-size:11px">{{ $message }}</div>@enderror
            </div>

            <div style="display:flex;gap:10px">
                <button type="submit" class="btn btn-save"><i class="fas fa-save"></i> Simpan</button>
                <a href="{{ route('kelompok.kups') }}" class="btn"><i class="fas fa-arrow-left"></i> Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
