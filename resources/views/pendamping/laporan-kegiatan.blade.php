@extends('layouts.app')
@section('title', 'Laporan Kegiatan Kelompok Binaan - SI PS ONLINE')
@push('styles')
<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px; }
.page-header h2 { color: #1b5e20; font-size: 20px; font-weight: 700; margin: 0; }

.filter-form-box { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 18px; margin-bottom: 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
.filter-row { display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end; }
.fg { display: flex; flex-direction: column; gap: 4px; }
.fg label { font-size: 11px; font-weight: 600; color: #475569; text-transform: uppercase; letter-spacing: 0.3px; }
.fg input, .fg select { height: 34px; padding: 4px 10px; font-size: 12px; border: 1px solid #cbd5e1; border-radius: 6px; background: #fff; color: #334155; }
.fg input:focus, .fg select:focus { border-color: #2e7d32; outline: none; }
.btn-filter-go { background: #2e7d32; color: #fff; border: none; padding: 7px 16px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; height: 34px; }
.btn-filter-go:hover { background: #1b5e20; }

.lap-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.lap-table th {
    background: #f8fafc; color: #475569; font-weight: 600; text-align: left;
    padding: 10px 12px; border-bottom: 2px solid #e2e8f0;
    font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px;
}
.lap-table td { padding: 9px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.lap-table tr:hover td { background: #f8fafc; }

.foto-thumb { width: 50px; height: 50px; object-fit: cover; border-radius: 6px; cursor: pointer; transition: transform 0.15s; }
.foto-thumb:hover { transform: scale(1.08); box-shadow: 0 4px 10px rgba(0,0,0,0.15); }

.foto-modal { display: none; position: fixed; z-index: 9999; inset: 0; background: rgba(0,0,0,0.75); justify-content: center; align-items: center; }
.foto-modal-inner { background: #fff; padding: 12px; border-radius: 10px; max-width: 90vw; max-height: 90vh; }
.foto-modal-inner img { max-width: 100%; max-height: 80vh; object-fit: contain; border-radius: 6px; }
.close-modal-btn { position: absolute; top: 18px; right: 24px; color: #fff; font-size: 28px; font-weight: 700; cursor: pointer; background: none; border: none; }

.btn-act { padding: 4px 8px; border-radius: 5px; font-size: 11px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
.btn-act-view { background: #e0f2f1; color: #00796b; border: 1px solid #b2dfdb; }
.btn-act-view:hover { background: #00796b; color: #fff; }
.btn-act-edit { background: #e3f2fd; color: #1565c0; border: 1px solid #bbdefb; }
.btn-act-edit:hover { background: #1565c0; color: #fff; }
.btn-act-del { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; cursor: pointer; }
.btn-act-del:hover { background: #b91c1c; color: #fff; }
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
            <h2><i class="fas fa-clipboard-list" style="color:#2e7d32"></i> Laporan Kegiatan</h2>
            <p style="color:#64748b;font-size:12px;margin:4px 0 0">Kelola dan input laporan kegiatan kelompok dampingan</p>
        </div>
        <a href="{{ route('pendamping.laporan-kegiatan.tambah') }}" class="btn btn-save" style="height:34px;padding:0 14px;font-size:12px">
            <i class="fas fa-plus"></i> Tambah Laporan
        </a>
    </div>

    {{-- FILTER FORM --}}
    <div class="filter-form-box">
        <form method="GET" action="{{ route('pendamping.laporan-kegiatan') }}">
            <div class="filter-row">
                <div class="fg" style="flex:1;min-width:180px">
                    <label>Kelompok</label>
                    <select name="kelompok">
                        <option value="">Semua Kelompok</option>
                        @foreach($daftarKelompok as $k)
                            <option value="{{ $k->id }}" @selected($kelompokFilter == $k->id)>{{ $k->nama_kelompok }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="fg">
                    <label>Tanggal Awal</label>
                    <input type="date" name="tanggal_awal" value="{{ $tanggalAwal }}">
                </div>
                <div class="fg">
                    <label>Tanggal Akhir</label>
                    <input type="date" name="tanggal_akhir" value="{{ $tanggalAkhir }}">
                </div>
                <button type="submit" class="btn-filter-go"><i class="fas fa-filter"></i> Filter</button>
            </div>
        </form>
    </div>

    {{-- TABLE --}}
    <div style="background:#fff;border-radius:8px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,0.04);overflow-x:auto">
        <table class="lap-table">
            <thead>
                <tr>
                    <th style="width:40px;text-align:center">No</th>
                    <th style="width:100px;text-align:center">Tanggal</th>
                    <th>Kelompok</th>
                    <th>Kegiatan</th>
                    <th style="width:75px;text-align:center">Foto</th>
                    <th style="width:180px;text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($laporan as $i => $l)
                <tr>
                    <td style="text-align:center;color:#94a3b8">{{ $i + 1 }}</td>
                    <td style="text-align:center;color:#475569;font-weight:600">{{ \Carbon\Carbon::parse($l->tanggal)->format('d/m/Y') }}</td>
                    <td><strong style="color:#1e293b">{{ $l->nama_kelompok ?? '-' }}</strong></td>
                    <td>
                        <span style="background:#f1f5f9;color:#334155;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:600">
                            {{ $l->kegiatan }}
                        </span>
                    </td>
                    <td style="text-align:center">
                        @if($l->foto)
                            <img src="{{ asset('storage/laporan/' . $l->foto) }}" class="foto-thumb" onclick="openFotoModal(this.src)" title="Klik untuk memperbesar">
                        @else
                            <span style="color:#94a3b8">—</span>
                        @endif
                    </td>
                    <td style="text-align:center">
                        <div style="display:flex;gap:4px;justify-content:center">
                            <a href="{{ route('pendamping.laporan-kegiatan.detail', $l->id) }}" class="btn-act btn-act-view">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                            <a href="{{ route('pendamping.laporan-kegiatan.edit', $l->id) }}" class="btn-act btn-act-edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('pendamping.laporan-kegiatan.hapus', $l->id) }}" style="display:inline" onsubmit="return confirm('Yakin ingin menghapus laporan ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-act btn-act-del"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:#94a3b8">
                        <i class="fas fa-clipboard" style="font-size:28px;display:block;margin-bottom:8px"></i>
                        Belum ada data laporan kegiatan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- FOTO MODAL --}}
<div id="fotoModal" class="foto-modal" onclick="closeFotoModal()">
    <button class="close-modal-btn" onclick="closeFotoModal()">&times;</button>
    <div class="foto-modal-inner" onclick="event.stopPropagation()">
        <img id="imgModal" src="" alt="Foto Kegiatan">
    </div>
</div>
@endsection

@push('scripts')
<script>
function openFotoModal(src) {
    document.getElementById('imgModal').src = src;
    document.getElementById('fotoModal').style.display = 'flex';
}
function closeFotoModal() {
    document.getElementById('fotoModal').style.display = 'none';
}
</script>
@endpush
