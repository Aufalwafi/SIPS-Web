@extends('layouts.app')
@section('title', 'Dashboard Admin — SI PS ONLINE')

@section('content')
<div class="card-grid">
                <div class="card"><h3>Total Kelompok</h3><div class="value">{{ number_format($totalKelompok, 0, ',', '.') }}</div></div>
                <div class="card"><h3>Total Admin</h3><div class="value">{{ number_format($totalAdmin, 0, ',', '.') }}</div></div>
                <div class="card"><h3>Total Pendamping</h3><div class="value">{{ number_format($totalPendamping, 0, ',', '.') }}</div></div>
                <div class="card"><h3>Pengajuan RKPS</h3><div class="value red">{{ number_format($pengajuanRkps, 0, ',', '.') }}</div><div class="subtext">Menunggu Verifikasi</div></div>
            </div>

            <div class="card-grid">
                <div class="card"><h3>Pengajuan RKT</h3><div class="value red">{{ number_format($pengajuanRkt, 0, ',', '.') }}</div><div class="subtext">Menunggu Verifikasi</div></div>
                <div class="card green"><h3>Total Nilai Ekonomi (NTE)</h3><div class="value money">Rp {{ number_format($totalNte, 0, ',', '.') }}</div></div>
                <div class="card"><h3>Total Volume Produksi</h3><div class="value">{{ number_format($totalVolume, 0, ',', '.') }}</div></div>
                <div class="card"><h3>Komoditas NTE</h3><div class="value">{{ number_format($komoditasNte, 0, ',', '.') }}</div></div>
            </div>

            <div class="table-box">
                <h2>5 User Terakhir</h2>
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>No</th><th>Username</th><th>Role</th><th>Kelompok</th></tr></thead>
                        <tbody>
                        @forelse($userTerakhir as $index => $user)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $user->username }}</td>
                                <td><span class="role role-{{ $user->role }}">{{ $user->role }}</span></td>
                                <td>{{ $user->nama_kelompok ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4">Belum ada data user.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
@endsection
@push('scripts')
@endpush
