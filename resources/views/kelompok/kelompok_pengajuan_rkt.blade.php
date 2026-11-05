@extends('layouts.app')
@section('title', 'Pengajuan RKT - SI PS ONLINE')
@push('styles')
<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px; }
.page-header h2 { color: #1b5e20; font-size: 20px; font-weight: 700; margin: 0; }

.section-box { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); margin-bottom: 22px; overflow: hidden; }
.section-header { padding: 14px 18px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: #fafafa; }
.section-header h3 { margin: 0; font-size: 14px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px; }

.p-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.p-table th {
    background: #f8fafc; color: #475569; font-weight: 600; text-align: left;
    padding: 10px 12px; border-bottom: 2px solid #e2e8f0;
    font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px;
}
.p-table td { padding: 9px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.p-table tr:hover td { background: #f8fafc; }

.status-badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; }
.status-badge.Menunggu { background: #fef3c7; color: #92400e; }
.status-badge.Disetujui { background: #d1fae5; color: #065f46; }
.status-badge.Ditolak { background: #fee2e2; color: #991b1b; }
.status-badge.Revisi { background: #ffedd5; color: #9a3412; }

.btn-file-pdf { display: inline-flex; align-items: center; gap: 4px; background: #fdf2f8; color: #86198f; border: 1px solid #e9d5ff; padding: 3px 8px; border-radius: 5px; font-size: 11px; font-weight: 600; text-decoration: none; }
.btn-file-pdf:hover { background: #86198f; color: #fff; }

.btn-detail { background: #e0f2f1; color: #00796b; border: 1px solid #b2dfdb; padding: 4px 8px; border-radius: 5px; font-size: 11px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
.btn-detail:hover { background: #00796b; color: #fff; }

.alert-waiting { background: #fffbeb; border: 1px solid #fde68a; border-left: 4px solid #f59e0b; border-radius: 8px; padding: 12px 16px; margin-bottom: 18px; font-size: 13px; color: #92400e; display: flex; align-items: center; gap: 10px; }
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
            <h2><i class="fas fa-file-signature" style="color:#1565c0"></i> Pengajuan RKT</h2>
            <p style="color:#64748b;font-size:12px;margin:4px 0 0">Rencana Kerja Tahunan (RKT) Perhutanan Sosial</p>
        </div>
        @if(!$adaMenunggu)
            <a href="{{ route('kelompok.pengajuan-rkt.ajukan') }}" class="btn btn-save" style="font-size:12px;height:34px;padding:0 14px">
                <i class="fas fa-plus"></i> Ajukan RKT Baru
            </a>
        @endif
    </div>

    @if($adaMenunggu)
    <div class="alert-waiting">
        <i class="fas fa-clock" style="font-size:18px;color:#f59e0b"></i>
        <div>
            <strong>Pengajuan Sedang Diproses:</strong> Masih ada pengajuan RKT yang menunggu verifikasi. Pengajuan baru dapat diajukan setelah berkas selesai diverifikasi.
        </div>
    </div>
    @endif

    {{-- TABEL DAFTAR RKT --}}
    <div class="section-box">
        <div class="section-header">
            <h3><i class="fas fa-list-check" style="color:#2e7d32"></i> Dokumen RKT yang Disetujui</h3>
        </div>
        <div style="overflow-x:auto">
            <table class="p-table">
                <thead>
                    <tr>
                        <th style="width:40px;text-align:center">No</th>
                        <th style="width:100px;text-align:center">Tahun RKT</th>
                        <th>No Penetapan</th>
                        <th style="width:100px;text-align:center">Dokumen</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rktList as $i => $r)
                        <tr>
                            <td style="text-align:center;color:#94a3b8">{{ $i + 1 }}</td>
                            <td style="text-align:center;font-weight:600;color:#1565c0">{{ $r->tahun ?? '-' }}</td>
                            <td style="color:#475569;font-size:11px">{{ $r->no_penetapan ?? '—' }}</td>
                            <td style="text-align:center">
                                @if($r->file_rkt)
                                    <a href="{{ asset('storage/rkt/' . $r->file_rkt) }}" target="_blank" class="btn-file-pdf">
                                        <i class="fas fa-file-pdf"></i> Unduh PDF
                                    </a>
                                @else
                                    <span style="color:#94a3b8">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align:center;padding:30px;color:#94a3b8">Belum ada data RKT yang disahkan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- TABEL RIWAYAT PENGAJUAN --}}
    <div class="section-box">
        <div class="section-header">
            <h3><i class="fas fa-history" style="color:#1565c0"></i> Riwayat Pengajuan RKT</h3>
        </div>
        <div style="overflow-x:auto">
            <table class="p-table">
                <thead>
                    <tr>
                        <th style="width:40px;text-align:center">No</th>
                        <th style="width:110px">Tanggal</th>
                        <th style="width:110px;text-align:center">Status</th>
                        <th>Catatan Verifikator</th>
                        <th style="width:90px;text-align:center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengajuan as $i => $p)
                        <tr>
                            <td style="text-align:center;color:#94a3b8">{{ $i + 1 }}</td>
                            <td style="color:#64748b;font-size:11px">{{ $p->tanggal_pengajuan ? \Carbon\Carbon::parse($p->tanggal_pengajuan)->format('d/m/Y') : ($p->created_at ? $p->created_at->format('d/m/Y') : '—') }}</td>
                            <td style="text-align:center">
                                <span class="status-badge {{ $p->status }}">{{ $p->status ?? '-' }}</span>
                            </td>
                            <td style="color:#64748b;font-size:11px">{{ $p->catatan_admin ?? '—' }}</td>
                            <td style="text-align:center">
                                <a href="{{ route('kelompok.pengajuan-rkt.detail', $p->id) }}" class="btn-detail" title="Detail">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align:center;padding:30px;color:#94a3b8">Belum ada riwayat pengajuan RKT.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
