@extends('layouts.app')
@section('title', 'Permasalahan - SI PS ONLINE')

@section('content')
<div class="content">
    <div class="box">
        @if(session('success'))<div class="alert">{!! session('success') !!}</div>@endif
        @if(session('error'))<div class="alert-error">{!! session('error') !!}</div>@endif

        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px">
            <h2 style="margin:0"><i class="fas fa-exclamation-triangle" style="color:#e65100"></i> Permasalahan Kelompok</h2>
            <a href="{{ route('kelompok.permasalahan.tambah') }}" class="btn btn-tambah"><i class="fas fa-plus"></i> Tambah Permasalahan</a>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="width:50px">No</th>
                        <th>Judul</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th style="width:130px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($permasalahanList as $i => $p)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $p->judul ?? '-' }}</td>
                            <td>
                                @if($p->status == 'Aktif')
                                    <span style="display:inline-block;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:700;color:#fff;background:#2e7d32">{{ $p->status }}</span>
                                @elseif($p->status == 'Selesai')
                                    <span style="display:inline-block;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:700;color:#fff;background:#757575">{{ $p->status }}</span>
                                @else
                                    <span style="display:inline-block;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:700;color:#fff;background:#757575">{{ $p->status ?? '-' }}</span>
                                @endif
                            </td>
                            <td>{{ $p->created_at ? $p->created_at->format('d/m/Y') : '-' }}</td>
                            <td>
                                <a href="{{ route('kelompok.permasalahan.edit', $p->id) }}" class="btn btn-edit" title="Edit"><i class="fas fa-pen"></i> Edit</a>
                                <form action="{{ route('kelompok.permasalahan.hapus', $p->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Yakin ingin menghapus permasalahan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-delete" title="Hapus"><i class="fas fa-trash"></i> Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="empty">Belum ada data permasalahan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
