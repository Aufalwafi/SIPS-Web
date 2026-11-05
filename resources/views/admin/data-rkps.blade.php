@extends('layouts.app')
@section('title', 'Data RKPS - SI PS ONLINE')
@push('styles')
<style>
.top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; gap: 10px; flex-wrap: wrap; }
td.kiri { text-align: left; font-weight: 600; }
td a { color: #1565c0; text-decoration: none; font-weight: 600; }
td a:hover { text-decoration: underline; }
</style>
@endpush
@section('content')
<div class="content">
    <div class="box">
        @if(session('success'))<div style="background:#d4edda;color:#155724;padding:10px;border-radius:4px;margin-bottom:15px;font-size:12px">{{ session('success') }}</div>@endif
        @if(session('error'))<div style="background:#f8d7da;color:#721c24;padding:10px;border-radius:4px;margin-bottom:15px;font-size:12px">{{ session('error') }}</div>@endif

        <div class="top-bar">
            <h2><i class="fas fa-file-alt" style="color:#2e7d32"></i> Data RKPS</h2>
            <div class="top-buttons">
                <a class="btn btn-tambah" href="{{ route('admin.data-rkps.create') }}"><i class="fas fa-plus"></i> Tambah RKPS</a>
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="width:40px;text-align:center">No</th>
                        <th style="text-align:left">Kelompok</th>
                        <th style="width:90px;text-align:center">Periode</th>
                        <th>No Penilaian</th>
                        <th style="width:100px;text-align:center">Tgl Penilaian</th>
                        <th>No Pengesahan</th>
                        <th style="width:100px;text-align:center">Tgl Pengesahan</th>
                        <th style="width:80px;text-align:center">Dokumen</th>
                        <th style="width:130px;text-align:center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rkps as $no => $r)
                    <tr>
                        <td style="text-align:center">{{ $no + 1 }}</td>
                        <td class="kiri">{{ $r->nama_kelompok ?? '-' }}</td>
                        <td style="text-align:center">{{ $r->tahun_mulai }} - {{ $r->tahun_selesai }}</td>
                        <td>{{ $r->no_penilaian ?? '-' }}</td>
                        <td style="text-align:center">{{ $r->tgl_penilaian ?? '-' }}</td>
                        <td>{{ $r->no_pengesahan ?? '-' }}</td>
                        <td style="text-align:center">{{ $r->tgl_pengesahan ?? '-' }}</td>
                        <td style="text-align:center">
                            @if(!empty($r->file_rkps))
                                <a href="{{ asset('uploads/rkps/' . $r->file_rkps) }}" target="_blank"><i class="fas fa-download"></i> Download</a>
                            @else
                                -
                            @endif
                        </td>
                        <td style="text-align:center">
                            <a class="btn btn-edit" href="{{ route('admin.data-rkps.edit', $r->id_rkps) }}"><i class="fas fa-edit"></i> Edit</a>
                            <form method="POST" action="{{ route('admin.data-rkps.delete', $r->id_rkps) }}" style="display:inline" onsubmit="return confirm('Yakin hapus data RKPS ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-hapus"><i class="fas fa-trash"></i> Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" style="text-align:center;padding:30px;color:#94a3b8">Data RKPS belum tersedia.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
