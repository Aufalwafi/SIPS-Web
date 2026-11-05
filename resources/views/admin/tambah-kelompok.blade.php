@extends('layouts.app')
@section('title', 'Tambah Kelompok - SI PS ONLINE')

@push('styles')
<style>
.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
}
.form-group input, .form-group select, .form-group textarea {
    width: 100%;
    padding: 8px 10px;
    font-size: 13px;
    border: 1px solid #ccc;
    border-radius: 4px;
    background: #fff;
    color: #333;
    box-sizing: border-box;
}
.form-group input:focus, .form-group select:focus, .form-group textarea:focus {
    border-color: #1b5e20;
    outline: none;
    box-shadow: 0 0 5px rgba(27,94,32,.3);
}
.form-group .error {
    color: #c62828;
    font-size: 11px;
    margin-top: 3px;
}
.form-group .help {
    color: #78909c;
    font-size: 11px;
    margin-top: 3px;
}
.form-buttons {
    display: flex;
    gap: 8px;
    margin-top: 24px;
}
@media(max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@section('content')
<div class="content">
    <div class="box" style="max-width:850px">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
            <h2><i class="fas fa-plus-circle" style="color:#2e7d32"></i> Tambah Data Kelompok Baru</h2>
            <a href="{{ route('admin.data-kelompok') }}" class="btn" style="background:#757575;color:#fff;padding:6px 12px;border-radius:4px;text-decoration:none;font-size:12px">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        @if(isset($errors) && $errors->any())
            <div style="background:#f8d7da;color:#721c24;padding:10px 14px;border-radius:4px;margin-bottom:15px;font-size:12px">
                <ul style="margin:0;padding-left:15px">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.data-kelompok.simpan') }}" enctype="multipart/form-data">
            @csrf
            
            <div class="form-grid">
                <div class="form-group" style="grid-column: span 2">
                    <label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">Nama Kelompok *</label>
                    <input type="text" name="nama_kelompok" value="{{ old('nama_kelompok') }}" required placeholder="Contoh: KTH Wono Makmur">
                    @if(isset($errors) && $errors->has('nama_kelompok'))<div class="error">{{ $errors->first('nama_kelompok') }}</div>@endif
                </div>

                <div class="form-group">
                    <label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">Nama Ketua</label>
                    <input type="text" name="ketua" value="{{ old('ketua') }}" placeholder="Nama ketua kelompok">
                </div>

                <div class="form-group">
                    <label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">Wilayah CDK</label>
                    <select name="id_cdk">
                        <option value="">-- Pilih Wilayah CDK --</option>
                        @foreach($daftarCdk as $c)
                            <option value="{{ $c->id }}" @selected(old('id_cdk') == $c->id)>{{ $c->nama }}{{ !empty($c->kode) ? ' ('.$c->kode.')' : '' }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">Desa</label>
                    <input type="text" name="desa" value="{{ old('desa') }}" placeholder="Nama desa">
                </div>

                <div class="form-group">
                    <label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">Kecamatan</label>
                    <input type="text" name="kecamatan" value="{{ old('kecamatan') }}" placeholder="Nama kecamatan">
                </div>

                <div class="form-group">
                    <label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">Kabupaten</label>
                    <input type="text" name="kabupaten" value="{{ old('kabupaten') }}" placeholder="Nama kabupaten">
                </div>

                <div class="form-group">
                    <label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">Skema Perhutanan Sosial</label>
                    <select name="skema_ps">
                        <option value="">-- Pilih Skema --</option>
                        <option value="Hutan Kemasyarakatan (HKm)" @selected(old('skema_ps') == 'Hutan Kemasyarakatan (HKm)')>Hutan Kemasyarakatan (HKm)</option>
                        <option value="Hutan Desa (HD)" @selected(old('skema_ps') == 'Hutan Desa (HD)')>Hutan Desa (HD)</option>
                        <option value="Hutan Tanaman Rakyat (HTR)" @selected(old('skema_ps') == 'Hutan Tanaman Rakyat (HTR)')>Hutan Tanaman Rakyat (HTR)</option>
                        <option value="Kemitraan Kehutanan (KK)" @selected(old('skema_ps') == 'Kemitraan Kehutanan (KK)')>Kemitraan Kehutanan (KK)</option>
                        <option value="Hutan Adat (HA)" @selected(old('skema_ps') == 'Hutan Adat (HA)')>Hutan Adat (HA)</option>
                        <option value="IPHPS" @selected(old('skema_ps') == 'IPHPS')>IPHPS</option>
                        <option value="Kulin KK" @selected(old('skema_ps') == 'Kulin KK')>Kulin KK</option>
                    </select>
                </div>

                <div class="form-group">
                    <label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">Luas Lahan (Ha)</label>
                    <input type="number" step="0.01" min="0" name="luas_lahan" value="{{ old('luas_lahan') }}" placeholder="Contoh: 125.50">
                </div>

                <div class="form-group">
                    <label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">Nomor SK</label>
                    <input type="text" name="no_sk" value="{{ old('no_sk') }}" placeholder="Nomor Surat Keputusan">
                </div>

                <div class="form-group">
                    <label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">Tanggal SK</label>
                    <input type="date" name="tanggal_sk" value="{{ old('tanggal_sk') }}">
                </div>

                <div class="form-group">
                    <label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">File SK PS (PDF / Gambar)</label>
                    <input type="file" name="sk_ps" accept=".pdf,.jpg,.jpeg,.png">
                    <div class="help">Maksimal 5MB (PDF/JPG/PNG) - Opsional</div>
                </div>

                <div class="form-group">
                    <label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">File SK Pembentukan (PDF / Gambar)</label>
                    <input type="file" name="sk_kelompok" accept=".pdf,.jpg,.jpeg,.png">
                    <div class="help">Maksimal 5MB (PDF/JPG/PNG) - Opsional</div>
                </div>

                <div class="form-group" style="grid-column: span 2; background: #f0fdf4; border: 1px dashed #86efac; border-radius: 6px; padding: 12px 14px">
                    <label style="display:block;font-weight:700;margin-bottom:4px;font-size:13px;color:#166534">
                        <i class="fas fa-map-marked-alt"></i> Berkas Batas Kawasan Kelompok (.kml / .geojson / .json)
                    </label>
                    <input type="file" name="file_peta" accept=".kml,.geojson,.json,.txt" style="background:#fff">
                    <div class="help" style="color:#166534;font-size:11px;margin-top:5px;line-height:1.4">
                        <i class="fas fa-info-circle"></i> <strong>Opsional:</strong> Unggah berkas spasial hasil pemetaan/QGIS/Google Earth. Format yang didukung: <code>.kml</code>, <code>.geojson</code>, atau <code>.json</code>. Poligon batas kawasan akan otomatis tampil di <strong>Peta Publik</strong> dan <strong>Peta Wilayah CDK</strong> seperti CDK Madiun.
                    </div>
                    @if(isset($errors) && $errors->has('file_peta'))<div class="error">{{ $errors->first('file_peta') }}</div>@endif
                </div>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn btn-save" style="background:#2e7d32;color:#fff;padding:8px 18px;border-radius:4px;border:none;cursor:pointer;font-weight:600">
                    <i class="fas fa-save"></i> Simpan Kelompok
                </button>
                <a href="{{ route('admin.data-kelompok') }}" class="btn btn-cancel" style="background:#757575;color:#fff;padding:8px 16px;border-radius:4px;text-decoration:none">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
