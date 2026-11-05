@extends('layouts.app')
@section('title', 'Data Kelompok Saya - SI PS ONLINE')

@push('styles')
<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px; }
.page-header h2 { color: #1b5e20; font-size: 20px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px; }

.section-box { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); margin-bottom: 20px; overflow: hidden; }
.section-header { padding: 14px 18px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: #fafafa; }
.section-header h3 { margin: 0; font-size: 15px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px; }

.info-table { width: 100%; border-collapse: collapse; font-size: 12.5px; }
.info-table th { width: 180px; background: #f8fafc; color: #475569; font-weight: 600; text-align: left; padding: 11px 16px; border-bottom: 1px solid #f1f5f9; }
.info-table td { padding: 11px 16px; border-bottom: 1px solid #f1f5f9; color: #1e293b; }

.btn-file-pdf { display: inline-flex; align-items: center; gap: 6px; background: #fdf2f8; color: #86198f; border: 1px solid #e9d5ff; padding: 4px 10px; border-radius: 5px; font-size: 11.5px; font-weight: 600; text-decoration: none; transition: 0.15s; }
.btn-file-pdf:hover { background: #86198f; color: #fff; }
</style>
@endpush

@section('content')
<div class="content">
    @if(session('success'))
    <div class="alert" style="background:#d1fae5;color:#065f46;padding:10px 16px;border-radius:6px;margin-bottom:16px;border-left:4px solid #22c55e">
        <i class="fas fa-check-circle"></i> {!! session('success') !!}
    </div>
    @endif
    @if(session('error'))
    <div class="alert-error" style="background:#fee2e2;color:#7f1d1d;padding:10px 16px;border-radius:6px;margin-bottom:16px;border-left:4px solid #ef4444">
        <i class="fas fa-exclamation-circle"></i> {!! session('error') !!}
    </div>
    @endif

    {{-- HEADER --}}
    <div class="page-header">
        <div>
            <h2><i class="fas fa-id-card" style="color:#1b5e20"></i> Profil &amp; Legalitas Kelompok</h2>
            <p style="color:#64748b;font-size:12px;margin:4px 0 0">Data identitas kelompok, legalitas izin SK, dan berkas penetapan</p>
        </div>
    </div>

    @if($kelompok)
    {{-- PROFIL TABLE --}}
    <div class="section-box">
        <div class="section-header">
            <h3><i class="fas fa-info-circle" style="color:#2e7d32"></i> Data Identitas &amp; Legalitas</h3>
        </div>
        <table class="info-table">
            <tr><th>Nama Kelompok</th><td><strong style="color:#1e293b;font-size:13.5px">{{ $kelompok->nama_kelompok ?? '-' }}</strong></td></tr>
            <tr><th>Ketua Kelompok</th><td>{{ $kelompok->ketua ?? '-' }}</td></tr>
            <tr><th>CDK Wilayah</th><td>{{ $kelompok->cdk?->nama ?? ($kelompok->id_cdk ?? '-') }}</td></tr>
            <tr><th>Desa / Kelurahan</th><td>{{ $kelompok->desaWilayah?->nama ?? ($kelompok->desa ?? '-') }}</td></tr>
            <tr><th>Kecamatan</th><td>{{ $kelompok->kecamatan ?? '-' }}</td></tr>
            <tr><th>Kabupaten</th><td>{{ $kelompok->kabupaten ?? '-' }}</td></tr>
            <tr><th>Skema Perhutanan Sosial</th><td>
                @if($kelompok->skema_ps)
                <span style="background:#e8f5e9;color:#1b5e20;padding:3px 9px;border-radius:4px;font-size:11.5px;font-weight:700">{{ $kelompok->skema_ps }}</span>
                @else — @endif
            </td></tr>
            <tr><th>Luas Kawasan Izin (Ha)</th><td><strong>{{ $kelompok->luas_ha ? number_format($kelompok->luas_ha, 2, ',', '.') . ' Ha' : ($kelompok->luas_lahan ? number_format($kelompok->luas_lahan, 2, ',', '.') . ' Ha' : '—') }}</strong></td></tr>
            <tr><th>Nomor SK PS</th><td>{{ $kelompok->no_sk ?? '-' }}</td></tr>
            <tr><th>Dokumen SK PS</th><td>
                @if($kelompok->sk_ps)
                    <a href="{{ route('dokumen.sk-ps', $kelompok->id) }}" target="_blank" class="btn-file-pdf">
                        <i class="fas fa-file-pdf"></i> Unduh Berkas SK PS
                    </a>
                @else
                    <span style="color:#94a3b8">Belum diunggah</span>
                @endif
            </td></tr>
            <tr><th>Dokumen SK Pembentukan</th><td>
                @if($kelompok->sk_kelompok)
                    <a href="{{ route('dokumen.sk-kelompok', $kelompok->id) }}" target="_blank" class="btn-file-pdf">
                        <i class="fas fa-file-pdf"></i> Unduh Berkas SK Kelompok
                    </a>
                @else
                    <span style="color:#94a3b8">Belum diunggah</span>
                @endif
            </td></tr>
        </table>
    </div>
    @else
    <div class="section-box" style="padding:40px;text-align:center;color:#94a3b8">
        <i class="fas fa-users-slash" style="font-size:32px;display:block;margin-bottom:8px"></i>
        Belum ada kelompok yang terhubung dengan akun ini.
    </div>
    @endif
</div>
@endsection
