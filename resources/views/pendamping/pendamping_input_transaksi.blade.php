@extends('layouts.app')
@section('title', 'Input Transaksi NTE - '.($kelompok->nama_kelompok ?? ''))
@section('content')
<div class="content"><div class="box">
<a href="{{ route('pendamping.kelompok-saya.data-nte',$kelompok->id) }}" class="btn btn-outline" style="margin-bottom:15px"><i class="fas fa-arrow-left"></i> Kembali ke Data NTE</a>
<h2><i class="fas fa-money-bill-wave" style="color:var(--green-header)"></i> Input Transaksi NTE</h2>
<div style="background:#f8f9fa;padding:12px 16px;border-radius:6px;margin-bottom:18px;font-size:13px"><b>Produk:</b> {{ $nte->nama_produk }} | <b>Kelompok:</b> {{ $kelompok->nama_kelompok }} | <b>Tahun:</b> {{ $nte->tahun ?? '-' }}</div>
@if($errors->any())<div class="alert-error"><ul style="margin:0;padding-left:15px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('pendamping.kelompok-saya.simpan-transaksi',[$kelompok->id,$nte->id]) }}">
@csrf
<div class="form-group"><label>Bulan</label><select name="bulan" class="form-control"><option value="">-- Pilih Bulan --</option><option value="1">Januari</option><option value="2">Februari</option><option value="3">Maret</option><option value="4">April</option><option value="5">Mei</option><option value="6">Juni</option><option value="7">Juli</option><option value="8">Agustus</option><option value="9">September</option><option value="10">Oktober</option><option value="11">November</option><option value="12">Desember</option></select></div>
<div class="form-group"><label>Volume *</label><input type="number" step="0.01" name="volume" value="{{ old('volume') }}" required class="form-control" id="inputVolume"></div>
<div class="form-group"><label>Satuan</label><input type="text" name="satuan" value="{{ old('satuan') }}" class="form-control"></div>
<div class="form-group"><label>Harga Satuan *</label><input type="number" step="0.01" name="harga_satuan" value="{{ old('harga_satuan') }}" required class="form-control" id="inputHarga"></div>
<div class="form-group"><label>Nilai Ekonomi (Otomatis)</label><input type="text" readonly class="form-control" id="hitungTotal" value="0"></div>
<div class="form-buttons"><button type="submit" class="btn btn-save"><i class="fas fa-save"></i> Simpan Transaksi</button></div>
</form>
</div></div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded',function(){var v=document.getElementById('inputVolume'),h=document.getElementById('inputHarga'),t=document.getElementById('hitungTotal');function calc(){var vol=parseFloat(v.value)||0,hrg=parseFloat(h.value)||0;t.value=vol*hrg.toLocaleString('id-ID',{minimumFractionDigits:2})}v.addEventListener('input',calc);h.addEventListener('input',calc)});
</script>
@endpush
