@extends('layouts.app')
@section('title', 'Edit RKT - SI PS ONLINE')
@push('styles')
<style>
.form-group input, .form-group select {
    width: 100%; height: 36px; padding: 6px 10px; font-size: 13px;
    border: 1px solid #ccc; border-radius: 4px; background: #fff; color: #333;
}
.form-group input:focus, .form-group select:focus {
    border-color: #1b5e20; outline: none; box-shadow: 0 0 5px rgba(27,94,32,.3);
}
.error { color: #c62828; font-size: 11px; margin-top: 4px; }
.help { color: #777; font-size: 11px; margin-top: 4px; }
.form-buttons { display: flex; gap: 8px; margin-top: 20px; }
</style>
@endpush
@section('content')
<div class="content">
    <div class="box" style="max-width:700px">
        <h2><i class="fas fa-file-pen" style="color:#1565c0"></i> Edit Data RKT</h2>
        
        @if($errors->any())
        <div class="alert">
            <ul>
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.data-rkt.update', $rkt->id_rkt) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Kelompok</label>
                <select name="id_kelompok" id="kelompokSelect" required>
                    <option value="">-- Pilih Kelompok --</option>
                    @foreach($daftarKelompok as $k)
                    <option value="{{ $k->id }}" @selected(old('id_kelompok', $rkt->id_kelompok) == $k->id)>{{ $k->nama_kelompok }}</option>
                    @endforeach
                </select>
                @error('id_kelompok')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>RKPS Terkait <span style="font-weight:400;color:#777">(opsional)</span></label>
                <select name="id_rkps" id="rkpsSelect">
                    <option value="">-- Tidak Ada --</option>
                    @foreach($daftarRkps as $r)
                    <option value="{{ $r->id_rkps }}" data-kelompok="{{ $r->id_kelompok }}" @selected(old('id_rkps', $rkt->id_rkps) == $r->id_rkps)>
                        {{ $r->nama_kelompok }} — Periode {{ $r->tahun_mulai }}-{{ $r->tahun_selesai }}
                    </option>
                    @endforeach
                </select>
                @error('id_rkps')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>Tahun</label>
                <input type="number" name="tahun" min="1900" max="2200" value="{{ old('tahun', $rkt->tahun) }}" required>
                @error('tahun')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>No Penetapan</label>
                <input type="text" name="no_penetapan" value="{{ old('no_penetapan', $rkt->no_penetapan) }}" placeholder="Contoh: 0374/CDK/2025">
                @error('no_penetapan')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>Tanggal Penetapan</label>
                <input type="date" name="tgl_penetapan" value="{{ old('tgl_penetapan', $rkt->tgl_penetapan) }}">
                @error('tgl_penetapan')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>Ganti Dokumen RKT</label>
                <input type="file" name="file_rkt" accept="application/pdf">
                @if($rkt->file_rkt)
                <div class="help">Dokumen saat ini: <a href="{{ asset('uploads/rkt/' . $rkt->file_rkt) }}" target="_blank">Lihat dokumen</a>. Kosongkan jika tidak diganti.</div>
                @else
                <div class="help">Belum ada dokumen. Format PDF, maksimal 10 MB.</div>
                @endif
                @error('file_rkt')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn btn-save"><i class="fas fa-save"></i> Simpan Perubahan</button>
                <a href="{{ route('admin.data-rkt') }}" class="btn btn-back"><i class="fas fa-arrow-left"></i> Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
var ks = document.getElementById('kelompokSelect'), rs = document.getElementById('rkpsSelect');
if (ks && rs) {
    ks.addEventListener('change', function() {
        var v = ks.value;
        rs.querySelectorAll('option[data-kelompok]').forEach(function(o) {
            o.style.display = (!v || o.getAttribute('data-kelompok') === v) ? '' : 'none';
        });
    });
    // Trigger initial filter for current selected kelompok
    var initVal = ks.value;
    if (initVal) {
        rs.querySelectorAll('option[data-kelompok]').forEach(function(o) {
            o.style.display = (o.getAttribute('data-kelompok') === initVal) ? '' : 'none';
        });
    }
}
</script>
@endpush
