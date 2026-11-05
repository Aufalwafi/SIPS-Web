@extends('layouts.app')
@section('title', 'KUPS - SI PS ONLINE')

@section('content')
<div class="content">
    <div class="box">
        @if(session('success'))<div class="alert">{!! session('success') !!}</div>@endif
        @if(session('error'))<div class="alert-error">{!! session('error') !!}</div>@endif

        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px">
            <h2 style="margin:0"><i class="fas fa-seedling" style="color:#2e7d32"></i> KUPS Kelompok</h2>
            <a href="{{ route('kelompok.kups.tambah') }}" class="btn btn-tambah"><i class="fas fa-plus"></i> Tambah KUPS</a>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="width:50px">No</th>
                        <th>Nama KUPS</th>
                        <th>Ketua</th>
                        <th>Jumlah Anggota</th>
                        <th>Kelas</th>
                        <th>Tahun Bentuk</th>
                        <th>Jenis Usaha</th>
                        <th>Produk</th>
                        <th style="width:130px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kupsList as $i => $k)
                        @php
                            $warnaKelas = [
                                'Biru' => '#1565c0',
                                'Perak' => '#9e9e9e',
                                'Emas' => '#f9a825',
                                'Platinum' => '#37474f',
                            ];
                            $warna = $warnaKelas[$k->kelas_kups] ?? '#777';
                        @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $k->nama_kups ?? '-' }}</td>
                            <td>{{ $k->ketua_kups ?? '-' }}</td>
                            <td>{{ $k->jumlah_anggota ?? '-' }}</td>
                            <td>
                                <span style="display:inline-block;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:700;color:#fff;background:{{ $warna }}">{{ $k->kelas_kups ?? '-' }}</span>
                            </td>
                            <td>{{ $k->tahun_bentuk ?? '-' }}</td>
                            <td>{{ $k->jenis_usaha ?? '-' }}</td>
                            <td>{{ $k->produk ?? '-' }}</td>
                            <td>
                                <a href="{{ route('kelompok.kups.edit', $k->id) }}" class="btn btn-edit" title="Edit"><i class="fas fa-pen"></i> Edit</a>
                                <form action="{{ route('kelompok.kups.hapus', $k->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Yakin ingin menghapus KUPS ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-delete" title="Hapus"><i class="fas fa-trash"></i> Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="empty">Belum ada data KUPS.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
