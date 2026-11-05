@extends('layouts.app')
@section('title', 'Edit Kelompok - SI PS ONLINE')

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
.file-current {
    margin-top: 5px;
    font-size: 12px;
}
.file-current a {
    color: #1565c0;
    text-decoration: underline;
    font-weight: 600;
}
@media(max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@php
    $isAdminCdk = Auth::user()->role === 'admin_cdk';
    $actionUrl = $isAdminCdk 
        ? route('admin-cdk.data-kelompok.update', $kelompok->id) 
        : route('admin.data-kelompok.update', $kelompok->id);
    $backUrl = $isAdminCdk 
        ? route('admin-cdk.data-kelompok') 
        : route('admin.data-kelompok');
@endphp

@section('content')
<div class="content">
    <div class="box" style="max-width:850px">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
            <h2><i class="fas fa-edit" style="color:#1565c0"></i> Edit Data Kelompok</h2>
            <a href="{{ $backUrl }}" class="btn" style="background:#757575;color:#fff;padding:6px 12px;border-radius:4px;text-decoration:none;font-size:12px">
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

        <form method="POST" action="{{ $actionUrl }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="form-grid">
                <div class="form-group" style="grid-column: span 2">
                    <label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">Nama Kelompok *</label>
                    <input type="text" name="nama_kelompok" value="{{ old('nama_kelompok', $kelompok->nama_kelompok) }}" required placeholder="Contoh: KTH Wono Makmur">
                    @if(isset($errors) && $errors->has('nama_kelompok'))<div class="error">{{ $errors->first('nama_kelompok') }}</div>@endif
                </div>

                <div class="form-group">
                    <label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">Nama Ketua</label>
                    <input type="text" name="ketua" value="{{ old('ketua', $kelompok->ketua) }}" placeholder="Nama ketua kelompok">
                </div>

                <div class="form-group">
                    <label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">Wilayah CDK</label>
                    @if($isAdminCdk)
                        @php
                            $userCdk = $daftarCdk->firstWhere('id', $kelompok->id_cdk);
                        @endphp
                        <input type="text" value="{{ $userCdk->nama ?? 'Wilayah CDK Anda' }}" disabled style="background:#f1f5f9;color:#64748b;cursor:not-allowed">
                        <input type="hidden" name="id_cdk" value="{{ $kelompok->id_cdk }}">
                    @else
                        <select name="id_cdk">
                            <option value="">-- Pilih Wilayah CDK --</option>
                            @foreach($daftarCdk as $c)
                                <option value="{{ $c->id }}" @selected(old('id_cdk', $kelompok->id_cdk) == $c->id)>{{ $c->nama }}{{ !empty($c->kode) ? ' ('.$c->kode.')' : '' }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <div class="form-group">
                    <label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">Desa</label>
                    <input type="text" name="desa" value="{{ old('desa', $kelompok->desa) }}" placeholder="Nama desa">
                </div>

                <div class="form-group">
                    <label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">Kecamatan</label>
                    <input type="text" name="kecamatan" value="{{ old('kecamatan', $kelompok->kecamatan) }}" placeholder="Nama kecamatan">
                </div>

                <div class="form-group">
                    <label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">Kabupaten</label>
                    <input type="text" name="kabupaten" value="{{ old('kabupaten', $kelompok->kabupaten) }}" placeholder="Nama kabupaten">
                </div>

                <div class="form-group">
                    <label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">Skema Perhutanan Sosial</label>
                    <select name="skema_ps">
                        <option value="">-- Pilih Skema --</option>
                        @php
                            $skemaList = [
                                'Hutan Kemasyarakatan (HKm)',
                                'Hutan Desa (HD)',
                                'Hutan Tanaman Rakyat (HTR)',
                                'Kemitraan Kehutanan (KK)',
                                'Hutan Adat (HA)',
                                'IPHPS',
                                'Kulin KK'
                            ];
                            $currSkema = old('skema_ps', $kelompok->skema_ps);
                        @endphp
                        @foreach($skemaList as $s)
                            <option value="{{ $s }}" @selected($currSkema == $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">Luas Lahan (Ha)</label>
                    <input type="number" step="0.01" min="0" name="luas_lahan" value="{{ old('luas_lahan', $kelompok->luas_lahan) }}" placeholder="Contoh: 125.50">
                </div>

                <div class="form-group">
                    <label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">Nomor SK</label>
                    <input type="text" name="no_sk" value="{{ old('no_sk', $kelompok->no_sk) }}" placeholder="Nomor Surat Keputusan">
                </div>

                <div class="form-group">
                    <label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">Tanggal SK</label>
                    <input type="date" name="tanggal_sk" value="{{ old('tanggal_sk', $kelompok->tanggal_sk) }}">
                </div>

                <div class="form-group">
                    <label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">File SK PS (PDF / Gambar)</label>
                    <input type="file" name="sk_ps" accept=".pdf,.jpg,.jpeg,.png">
                    <div class="help">Kosongkan jika tidak ingin mengubah file lama. Maksimal 5MB.</div>
                    @if(!empty($kelompok->sk_ps))
                        <div class="file-current">
                            File saat ini: <a href="{{ route('dokumen.sk-ps', $kelompok->id) }}" target="_blank"><i class="fas fa-file-pdf"></i> Lihat SK PS</a>
                        </div>
                    @endif
                </div>

                <div class="form-group">
                    <label style="display:block;font-weight:600;margin-bottom:4px;font-size:13px">File SK Pembentukan (PDF / Gambar)</label>
                    <input type="file" name="sk_kelompok" accept=".pdf,.jpg,.jpeg,.png">
                    <div class="help">Kosongkan jika tidak ingin mengubah file lama. Maksimal 5MB.</div>
                    @if(!empty($kelompok->sk_kelompok))
                        <div class="file-current">
                            File saat ini: <a href="{{ route('dokumen.sk-kelompok', $kelompok->id) }}" target="_blank"><i class="fas fa-file-pdf"></i> Lihat SK Pembentukan</a>
                        </div>
                    @endif
                </div>

                {{-- BERKAS PETA SPASIAL KML / GEOJSON --}}
                <div class="form-group" style="grid-column: span 2; background: #f0fdf4; border: 1px dashed #86efac; border-radius: 6px; padding: 14px 16px">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;flex-wrap:wrap;gap:8px">
                        <label style="display:block;font-weight:700;margin:0;font-size:13px;color:#166534">
                            <i class="fas fa-map-marked-alt"></i> Berkas Batas Kawasan Kelompok (.kml / .geojson / .json)
                        </label>
                        @if(!empty($kelompok->polygon_kelompok))
                            <span style="background:#dcfce7;color:#166534;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;display:inline-flex;align-items:center;gap:5px;border:1px solid #bbf7d0">
                                <i class="fas fa-check-circle"></i> Peta Poligon Tersimpan
                            </span>
                        @else
                            <span style="background:#f1f5f9;color:#64748b;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;display:inline-flex;align-items:center;gap:5px;border:1px solid #e2e8f0">
                                <i class="fas fa-exclamation-circle"></i> Belum Ada Peta
                            </span>
                        @endif
                    </div>

                    <input type="file" name="file_peta" accept=".kml,.geojson,.json,.txt" style="background:#fff">
                    <div class="help" style="color:#166534;font-size:11px;margin-top:6px;line-height:1.4">
                        <i class="fas fa-info-circle"></i> Unggah berkas peta baru (<code>.kml</code>, <code>.geojson</code>, atau <code>.json</code>) untuk mengganti/menambahkan poligon kawasan kelompok. Batas kawasan ini otomatis tampil di <strong>Peta Publik</strong> & <strong>Peta Wilayah CDK</strong>. Kosongkan jika tidak ingin mengubah.
                    </div>
                    @if(isset($errors) && $errors->has('file_peta'))<div class="error">{{ $errors->first('file_peta') }}</div>@endif

                    @if(!empty($kelompok->polygon_kelompok))
                    <div style="margin-top:10px;padding-top:10px;border-top:1px dashed #bbf7d0">
                        <label style="display:inline-flex;align-items:center;gap:6px;font-size:12px;color:#b91c1c;cursor:pointer;font-weight:600">
                            <input type="checkbox" name="hapus_peta" value="1" id="hapus_peta">
                            <i class="fas fa-trash-alt"></i> Hapus data peta spasial kelompok ini
                        </label>
                    </div>
                    @endif
                </div>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn btn-save" style="background:#1565c0;color:#fff;padding:8px 18px;border-radius:4px;border:none;cursor:pointer;font-weight:600">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <a href="{{ $backUrl }}" class="btn btn-cancel" style="background:#757575;color:#fff;padding:8px 16px;border-radius:4px;text-decoration:none">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
