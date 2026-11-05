@extends('layouts.app')
@section('title', 'Recycle Bin (Data Terhapus) - SI PS ONLINE')

@push('styles')
<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }
.page-header h2 { color: #1b5e20; font-size: 20px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px; }
.page-header p { color: #64748b; font-size: 13px; margin: 4px 0 0; }

.tab-nav { display: flex; gap: 6px; border-bottom: 2px solid #e2e8f0; margin-bottom: 18px; overflow-x: auto; padding-bottom: 2px; }
.tab-link {
    padding: 9px 14px; font-size: 12px; font-weight: 600; color: #64748b;
    text-decoration: none; border-radius: 6px 6px 0 0; display: inline-flex;
    align-items: center; gap: 6px; white-space: nowrap; transition: all 0.2s;
}
.tab-link:hover { color: #1b5e20; background: #f8fafc; }
.tab-link.active {
    color: #1b5e20; border-bottom: 3px solid #2e7d32; background: #f1f8e9;
    font-weight: 700;
}
.tab-badge {
    background: #e2e8f0; color: #475569; padding: 2px 7px;
    border-radius: 12px; font-size: 10px; font-weight: 700;
}
.tab-link.active .tab-badge {
    background: #2e7d32; color: #fff;
}

.table-card {
    background: #fff; border-radius: 8px; border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04); overflow-x: auto;
}
.bin-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.bin-table th {
    background: #f8fafc; color: #475569; font-weight: 600; text-align: left;
    padding: 10px 14px; border-bottom: 2px solid #e2e8f0;
    font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px; white-space: nowrap;
}
.bin-table td { padding: 10px 14px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.bin-table tr:hover td { background: #f8fafc; }

.btn-restore {
    background: #e8f5e9; color: #1b5e20; border: 1px solid #c8e6c9;
    padding: 5px 10px; border-radius: 5px; font-size: 11px; font-weight: 600;
    cursor: pointer; display: inline-flex; align-items: center; gap: 5px;
    transition: all 0.2s;
}
.btn-restore:hover { background: #1b5e20; color: #fff; border-color: #1b5e20; }

.btn-force-del {
    background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca;
    padding: 5px 10px; border-radius: 5px; font-size: 11px; font-weight: 600;
    cursor: pointer; display: inline-flex; align-items: center; gap: 5px;
    transition: all 0.2s;
}
.btn-force-del:hover { background: #b91c1c; color: #fff; border-color: #b91c1c; }

.empty-state { text-align: center; padding: 48px 20px; color: #64748b; }
.empty-icon { font-size: 44px; color: #cbd5e1; margin-bottom: 12px; }
.empty-title { font-size: 15px; font-weight: 700; color: #334155; margin-bottom: 4px; }
.empty-desc { font-size: 12px; }

.alert-flash {
    padding: 10px 16px; border-radius: 6px; margin-bottom: 16px; font-size: 13px;
    display: flex; align-items: center; gap: 8px; border-left: 4px solid;
}
.alert-flash.success { background: #d1fae5; color: #065f46; border-color: #22c55e; }
.alert-flash.error { background: #fee2e2; color: #7f1d1d; border-color: #ef4444; }
</style>
@endpush

@section('content')
<div class="content">

    @if(session('success'))
    <div class="alert-flash success">
        <i class="fas fa-check-circle"></i> {!! session('success') !!}
    </div>
    @endif
    @if(session('error'))
    <div class="alert-flash error">
        <i class="fas fa-exclamation-circle"></i> {!! session('error') !!}
    </div>
    @endif

    {{-- Header --}}
    <div class="page-header">
        <div>
            <h2><i class="fas fa-trash-restore" style="color:#2e7d32"></i> Recycle Bin (Data Terhapus)</h2>
            <p>Daftar data yang terhapus dari lapangan. Admin dapat memulihkan kembali data yang keliru dihapus.</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="tab-nav">
        <a href="{{ route('admin.recycle-bin', ['tab' => 'kelompok']) }}" class="tab-link {{ $activeTab === 'kelompok' ? 'active' : '' }}">
            <i class="fas fa-users"></i> Kelompok <span class="tab-badge">{{ $counts['kelompok'] }}</span>
        </a>
        <a href="{{ route('admin.recycle-bin', ['tab' => 'anggota']) }}" class="tab-link {{ $activeTab === 'anggota' ? 'active' : '' }}">
            <i class="fas fa-user-friends"></i> Anggota <span class="tab-badge">{{ $counts['anggota'] }}</span>
        </a>
        <a href="{{ route('admin.recycle-bin', ['tab' => 'kegiatan']) }}" class="tab-link {{ $activeTab === 'kegiatan' ? 'active' : '' }}">
            <i class="fas fa-calendar-check"></i> Kegiatan <span class="tab-badge">{{ $counts['kegiatan'] }}</span>
        </a>
        <a href="{{ route('admin.recycle-bin', ['tab' => 'kups']) }}" class="tab-link {{ $activeTab === 'kups' ? 'active' : '' }}">
            <i class="fas fa-store"></i> KUPS <span class="tab-badge">{{ $counts['kups'] }}</span>
        </a>
        <a href="{{ route('admin.recycle-bin', ['tab' => 'potensi']) }}" class="tab-link {{ $activeTab === 'potensi' ? 'active' : '' }}">
            <i class="fas fa-tree"></i> Potensi <span class="tab-badge">{{ $counts['potensi'] }}</span>
        </a>
        <a href="{{ route('admin.recycle-bin', ['tab' => 'permasalahan']) }}" class="tab-link {{ $activeTab === 'permasalahan' ? 'active' : '' }}">
            <i class="fas fa-triangle-exclamation"></i> Permasalahan <span class="tab-badge">{{ $counts['permasalahan'] }}</span>
        </a>
        <a href="{{ route('admin.recycle-bin', ['tab' => 'pendamping']) }}" class="tab-link {{ $activeTab === 'pendamping' ? 'active' : '' }}">
            <i class="fas fa-user-tie"></i> Pendamping <span class="tab-badge">{{ $counts['pendamping'] }}</span>
        </a>
    </div>

    {{-- Table Card --}}
    <div class="table-card">
        @if($items->isEmpty())
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-check-circle" style="color:#22c55e"></i></div>
                <div class="empty-title">Tempat Sampah Bersih</div>
                <div class="empty-desc">Tidak ada data {{ ucfirst($activeTab) }} yang sedang terhapus.</div>
            </div>
        @else
            <table class="bin-table">
                <thead>
                    <tr>
                        <th style="width: 40px; text-align: center;">No</th>
                        @if($activeTab === 'kelompok')
                            <th>Nama Kelompok</th>
                            <th>Ketua &amp; Wilayah</th>
                            <th>CDK</th>
                        @elseif($activeTab === 'anggota')
                            <th>Nama Anggota</th>
                            <th>NIK &amp; Gender</th>
                            <th>Asal Kelompok</th>
                        @elseif($activeTab === 'kegiatan')
                            <th>Nama Kegiatan</th>
                            <th>Tanggal &amp; Lokasi</th>
                            <th>Asal Kelompok</th>
                        @elseif($activeTab === 'kups')
                            <th>Nama KUPS</th>
                            <th>Ketua &amp; Kelas</th>
                            <th>Asal Kelompok</th>
                        @elseif($activeTab === 'potensi')
                            <th>Jenis Potensi</th>
                            <th>Detail &amp; Luasan</th>
                            <th>Asal Kelompok</th>
                        @elseif($activeTab === 'permasalahan')
                            <th>Judul Permasalahan</th>
                            <th>Status Permasalahan</th>
                            <th>Asal Kelompok</th>
                        @elseif($activeTab === 'pendamping')
                            <th>Nama Pendamping</th>
                            <th>NIP / Username</th>
                            <th>Wilayah CDK</th>
                        @endif
                        <th style="width: 150px;">Tanggal Dihapus</th>
                        <th style="width: 200px; text-align: center;">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $i => $item)
                        <tr>
                            <td style="text-align: center; color: #94a3b8;">{{ $i + 1 }}</td>

                            {{-- Data Columns per Tab --}}
                            @if($activeTab === 'kelompok')
                                <td>
                                    <strong style="color: #1e293b;">{{ $item->nama_kelompok }}</strong>
                                    <div style="font-size: 11px; color: #64748b;">Skema: {{ $item->skema_ps ?? '-' }}</div>
                                </td>
                                <td>
                                    <div>Ketua: {{ $item->ketua ?? '-' }}</div>
                                    <div style="font-size: 11px; color: #64748b;">{{ $item->desa }}, Kec. {{ $item->kecamatan }}</div>
                                </td>
                                <td>{{ $item->cdk->nama ?? '-' }}</td>

                            @elseif($activeTab === 'anggota')
                                <td>
                                    <strong style="color: #1e293b;">{{ $item->nama_anggota }}</strong>
                                    <div style="font-size: 11px; color: #64748b;">Komoditas: {{ $item->komoditas_utama ?? '-' }}</div>
                                </td>
                                <td>
                                    <div>NIK: {{ $item->nik ?? '-' }}</div>
                                    <div style="font-size: 11px; color: #64748b;">Gender: {{ $item->jenis_kelamin === 'L' ? 'Laki-laki' : ($item->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}</div>
                                </td>
                                <td>{{ $item->kelompok->nama_kelompok ?? '-' }}</td>

                            @elseif($activeTab === 'kegiatan')
                                <td>
                                    <strong style="color: #1e293b;">{{ $item->nama_kegiatan }}</strong>
                                </td>
                                <td>
                                    <div>{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') : '-' }}</div>
                                    <div style="font-size: 11px; color: #64748b;">Lokasi: {{ $item->lokasi ?? '-' }}</div>
                                </td>
                                <td>{{ $item->kelompok->nama_kelompok ?? '-' }}</td>

                            @elseif($activeTab === 'kups')
                                <td>
                                    <strong style="color: #1e293b;">{{ $item->nama_kups }}</strong>
                                    <div style="font-size: 11px; color: #64748b;">Usaha: {{ $item->jenis_usaha ?? '-' }}</div>
                                </td>
                                <td>
                                    <div>Ketua: {{ $item->ketua_kups ?? '-' }}</div>
                                    <div style="font-size: 11px; color: #64748b;">Kelas: {{ $item->kelas_kups ?? '-' }}</div>
                                </td>
                                <td>{{ $item->kelompok->nama_kelompok ?? '-' }}</td>

                            @elseif($activeTab === 'potensi')
                                <td>
                                    <strong style="color: #1e293b;">{{ $item->jenis_potensi }}</strong>
                                    <div style="font-size: 11px; color: #64748b;">Detail: {{ $item->jenis_detail ?? '-' }}</div>
                                </td>
                                <td>
                                    <div>Luasan: {{ $item->luasan ? $item->luasan . ' ' . ($item->satuan ?? 'Ha') : '-' }}</div>
                                    <div style="font-size: 11px; color: #64748b;">Kondisi: {{ $item->kondisi ?? '-' }}</div>
                                </td>
                                <td>{{ $item->kelompok->nama_kelompok ?? '-' }}</td>

                            @elseif($activeTab === 'permasalahan')
                                <td>
                                    <strong style="color: #1e293b;">{{ $item->judul }}</strong>
                                </td>
                                <td>
                                    <span style="display:inline-block;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:600;background:{{ $item->status === 'Aktif' ? '#fee2e2;color:#991b1b' : '#d1fae5;color:#065f46' }}">
                                        {{ $item->status ?? 'Aktif' }}
                                    </span>
                                </td>
                                <td>{{ $item->kelompok->nama_kelompok ?? '-' }}</td>

                            @elseif($activeTab === 'pendamping')
                                <td>
                                    <strong style="color: #1e293b;">{{ $item->nama_pendamping }}</strong>
                                </td>
                                <td>
                                    <div>NIP: {{ $item->nip ?? '-' }}</div>
                                    <div style="font-size: 11px; color: #64748b;">Username: {{ $item->username ?? '-' }}</div>
                                </td>
                                <td>{{ $item->cdk->nama ?? '-' }}</td>
                            @endif

                            {{-- Deleted At --}}
                            <td style="color: #64748b; font-size: 11px;">
                                <i class="far fa-clock"></i> {{ $item->deleted_at ? \Carbon\Carbon::parse($item->deleted_at)->format('d/m/Y H:i') : '-' }}
                            </td>

                            {{-- Actions --}}
                            <td style="text-align: center;">
                                <div style="display: inline-flex; gap: 6px; justify-content: center;">
                                    {{-- Form Restore --}}
                                    <form action="{{ route('admin.recycle-bin.restore', [$activeTab, $item->id ?? $item->id_pendamping]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn-restore" title="Pulihkan data ini kembali ke aplikasi">
                                            <i class="fas fa-undo"></i> Pulihkan
                                        </button>
                                    </form>

                                    {{-- Form Force Delete --}}
                                    <form action="{{ route('admin.recycle-bin.force-delete', [$activeTab, $item->id ?? $item->id_pendamping]) }}" method="POST" onsubmit="return confirm('PENTING: Apakah Anda yakin ingin menghapus data ini secara PERMANEN? Data tidak dapat dipulihkan lagi setelah dihapus permanen.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-force-del" title="Hapus permanen dari database">
                                            <i class="fas fa-trash-alt"></i> Hapus Permanen
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

</div>
@endsection
