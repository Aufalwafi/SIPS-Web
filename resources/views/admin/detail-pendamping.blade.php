@extends('layouts.app')
@section('title', 'Detail Pendamping - SI PS ONLINE')

@push('styles')
<style>
.detail-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    gap: 15px;
    flex-wrap: wrap;
}
.detail-header h2 {
    color: #1b5e20;
    font-size: 22px;
    font-weight: 700;
    margin: 0 0 4px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.detail-header p {
    color: #78909c;
    font-size: 13px;
    margin: 0;
}
.header-actions {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
}

/* Identitas Grid */
.identity-card {
    background: #fff;
    padding: 20px 24px;
    border-radius: 8px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.08);
    border: 1px solid #e2e8f0;
    margin-bottom: 20px;
}
.identity-card h3 {
    color: #1b5e20;
    font-size: 16px;
    font-weight: 700;
    margin: 0 0 16px;
    padding-bottom: 10px;
    border-bottom: 1px solid #edf2f7;
    display: flex;
    align-items: center;
    gap: 8px;
}
.identity-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 14px 28px;
}
.identity-item {
    display: flex;
    flex-direction: column;
}
.identity-label {
    font-size: 12px;
    font-weight: 600;
    color: #78909c;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 3px;
}
.identity-value {
    font-size: 14px;
    color: #263238;
    font-weight: 500;
}
.identity-value.highlight {
    color: #1b5e20;
    font-size: 16px;
    font-weight: 700;
}

/* Stat Cards */
.stats-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    margin-bottom: 20px;
}
.stat-card-box {
    background: #fff;
    padding: 18px 20px;
    border-radius: 8px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.08);
    border: 1px solid #e2e8f0;
    border-left: 4px solid #2e7d32;
    display: flex;
    align-items: center;
    gap: 16px;
}
.stat-card-box.amber {
    border-left-color: #f57c00;
}
.stat-card-box .stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    background: #e8f5e9;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #2e7d32;
    font-size: 22px;
    flex-shrink: 0;
}
.stat-card-box.amber .stat-icon {
    background: #fff3e0;
    color: #f57c00;
}
.stat-card-box .stat-info h4 {
    color: #78909c;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    margin: 0 0 4px;
}
.stat-card-box .stat-info .stat-num {
    font-size: 24px;
    font-weight: 700;
    color: #263238;
    line-height: 1.2;
}

/* Table Section */
.binaan-card {
    background: #fff;
    padding: 20px 24px;
    border-radius: 8px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.08);
    border: 1px solid #e2e8f0;
}
.binaan-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
    gap: 10px;
    flex-wrap: wrap;
}
.binaan-header h3 {
    color: #1b5e20;
    font-size: 16px;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Badges */
.badge-status {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}
.badge-aktif { background: #c8e6c9; color: #1b5e20; }
.badge-nonaktif { background: #ffcdd2; color: #c62828; }
.badge-skema {
    display: inline-block;
    padding: 2px 6px;
    border-radius: 4px;
    background: #e8f5e9;
    color: #1b5e20;
    font-size: 11px;
    font-weight: 600;
}

/* Buttons */
.btn-back {
    background: #757575;
    color: #fff;
    padding: 8px 14px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.btn-back:hover { background: #616161; color: #fff; }
.btn-edit-header {
    background: #1565c0;
    color: #fff;
    padding: 8px 14px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.btn-edit-header:hover { background: #0d47a1; color: #fff; }
.btn-tambah-binaan {
    background: #2e7d32;
    color: #fff;
    padding: 7px 14px;
    border-radius: 4px;
    border: none;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.btn-tambah-binaan:hover { background: #1b5e20; }
.btn-action-edit {
    background: #1565c0;
    color: #fff;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.btn-action-edit:hover { background: #0d47a1; color: #fff; }
.btn-action-lepas {
    background: #c62828;
    color: #fff;
    border: none;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.btn-action-lepas:hover { background: #b71c1c; }

/* Modal */
.modal-backdrop-custom {
    display: none;
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.modal-backdrop-custom.show {
    display: flex;
}
.modal-box-custom {
    background: #fff;
    border-radius: 10px;
    max-width: 500px;
    width: 100%;
    box-shadow: 0 10px 30px rgba(0,0,0,0.25);
    overflow: hidden;
}
.modal-header-custom {
    padding: 14px 20px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.modal-header-custom h3 {
    margin: 0;
    font-size: 15px;
    color: #1e293b;
    font-weight: 700;
}
.modal-close-btn {
    background: transparent;
    border: none;
    font-size: 18px;
    color: #64748b;
    cursor: pointer;
}
.modal-body-custom {
    padding: 20px;
}

@media(max-width: 768px) {
    .identity-grid { grid-template-columns: 1fr; }
    .stats-row { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')
<div class="content">

    {{-- HEADER --}}
    <div class="detail-header">
        <div>
            <h2><i class="fas fa-id-card-clip" style="color:#2e7d32"></i> Detail Pendamping</h2>
            <p>SI PS Online - Perhutanan Sosial Jawa Timur</p>
        </div>
        <div class="header-actions">
            <a href="{{ Auth::user()->role === 'admin_cdk' ? route('admin-cdk.data-pendamping') : route('admin.data-pendamping') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            @if(Auth::user()->role === 'admin')
            <a href="{{ route('admin.data-pendamping.edit', $pendamping->id_pendamping) }}" class="btn-edit-header">
                <i class="fas fa-edit"></i> Edit Pendamping
            </a>
            @endif
        </div>
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
        <div class="alert" style="background:#e8f5e9;color:#1b5e20;padding:10px 14px;border-radius:4px;margin-bottom:15px;font-size:13px;border-left:4px solid #2e7d32">
            <i class="fas fa-check-circle"></i> {!! session('success') !!}
        </div>
    @endif
    @if(session('error'))
        <div class="alert-error" style="background:#ffebee;color:#c62828;padding:10px 14px;border-radius:4px;margin-bottom:15px;font-size:13px;border-left:4px solid #c62828">
            <i class="fas fa-exclamation-circle"></i> {!! session('error') !!}
        </div>
    @endif

    {{-- IDENTITAS PENDAMPING --}}
    <div class="identity-card">
        <h3><i class="fas fa-user-tie"></i> Identitas Pendamping</h3>
        <div class="identity-grid">
            <div class="identity-item">
                <span class="identity-label">Nama Pendamping</span>
                <span class="identity-value highlight">{{ $pendamping->nama_pendamping }}</span>
            </div>
            <div class="identity-item">
                <span class="identity-label">NIP</span>
                <span class="identity-value">{{ $pendamping->nip ?? '-' }}</span>
            </div>
            <div class="identity-item">
                <span class="identity-label">Username Login</span>
                <span class="identity-value"><code>{{ $pendamping->username }}</code></span>
            </div>
            <div class="identity-item">
                <span class="identity-label">Wilayah CDK</span>
                <span class="identity-value" style="color:#1b5e20;font-weight:700">{{ $pendamping->nama_cdk ?? '-' }}</span>
            </div>
            <div class="identity-item">
                <span class="identity-label">Nomor Handphone</span>
                <span class="identity-value">{{ $pendamping->no_hp ?? '-' }}</span>
            </div>
            <div class="identity-item">
                <span class="identity-label">Email</span>
                <span class="identity-value">{{ $pendamping->email ?? '-' }}</span>
            </div>
            <div class="identity-item">
                <span class="identity-label">Alamat</span>
                <span class="identity-value">{{ $pendamping->alamat ?? '-' }}</span>
            </div>
            <div class="identity-item">
                <span class="identity-label">Status Keaktifan</span>
                <div>
                    <span class="badge-status {{ $pendamping->status === 'aktif' ? 'badge-aktif' : 'badge-nonaktif' }}">
                        {{ ucfirst($pendamping->status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="stats-row">
        <div class="stat-card-box">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-info">
                <h4>Kelompok Binaan</h4>
                <div class="stat-num">{{ number_format($jumlahKelompok) }} <span style="font-size:13px;color:#78909c;font-weight:400">Kelompok</span></div>
            </div>
        </div>
        <div class="stat-card-box amber">
            <div class="stat-icon"><i class="fas fa-chart-area"></i></div>
            <div class="stat-info">
                <h4>Total Luas Lahan Binaan</h4>
                <div class="stat-num">{{ number_format($totalLuasLahan, 2, ',', '.') }} <span style="font-size:13px;color:#78909c;font-weight:400">Ha</span></div>
            </div>
        </div>
    </div>

    {{-- KELOMPOK BINAAN TABLE --}}
    <div class="binaan-card">
        <div class="binaan-header">
            <h3><i class="fas fa-list-check"></i> Kelompok Binaan ({{ $jumlahKelompok }})</h3>
            <button type="button" class="btn-tambah-binaan" onclick="openAddBinaanModal()">
                <i class="fas fa-plus-circle"></i> Tambah Kelompok Binaan
            </button>
        </div>

        <div class="table-wrap" style="overflow-x:auto">
            <table>
                <thead>
                    <tr>
                        <th style="width:35px;text-align:center">No</th>
                        <th style="text-align:left">Nama Kelompok</th>
                        <th style="text-align:left">Ketua</th>
                        <th style="text-align:left">Kabupaten</th>
                        <th style="text-align:left">Kecamatan</th>
                        <th style="text-align:left">Desa</th>
                        <th style="text-align:left">Skema PS</th>
                        <th style="text-align:right">Luas Lahan</th>
                        <th style="text-align:left">No. SK</th>
                        <th style="width:120px;text-align:center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kelompokBinaan as $i => $kb)
                    <tr>
                        <td style="text-align:center">{{ $i + 1 }}</td>
                        <td><strong style="color:#1e293b">{{ $kb->nama_kelompok ?? '-' }}</strong></td>
                        <td>{{ $kb->ketua ?? '-' }}</td>
                        <td>{{ $kb->kabupaten ?? '-' }}</td>
                        <td>{{ $kb->kecamatan ?? '-' }}</td>
                        <td>{{ $kb->desa ?? '-' }}</td>
                        <td>
                            @if(!empty($kb->skema_ps))
                                <span class="badge-skema">{{ $kb->skema_ps }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td style="text-align:right;font-weight:600">
                            {{ $kb->luas_lahan !== null ? number_format($kb->luas_lahan, 2, ',', '.') . ' Ha' : '-' }}
                        </td>
                        <td>{{ $kb->no_sk ?? '-' }}</td>
                        <td style="text-align:center;white-space:nowrap">
                            @if(Auth::user()->role === 'admin')
                            <a href="{{ route('admin.data-kelompok.edit', $kb->id) }}" class="btn-action-edit" title="Detail / Edit Kelompok">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            @endif
                            <form method="POST" action="{{ Auth::user()->role === 'admin_cdk' ? route('admin-cdk.data-pendamping.lepas') : route('admin.data-pendamping.lepas') }}" style="display:inline" onsubmit="return confirm('Apakah Anda yakin ingin melepas kelompok \'{{ $kb->nama_kelompok }}\' dari pendamping ini?')">
                                @csrf
                                <input type="hidden" name="pendamping_id" value="{{ $pendamping->id_pendamping }}">
                                <input type="hidden" name="kelompok_id" value="{{ $kb->id }}">
                                <button type="submit" class="btn-action-lepas" title="Lepas Relasi Kelompok">
                                    <i class="fas fa-unlink"></i> Lepas
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" style="text-align:center;padding:24px;color:#94a3b8">
                            Pendamping ini belum memiliki kelompok binaan. Silakan klik tombol <strong>Tambah Kelompok Binaan</strong> untuk menambahkan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH KELOMPOK BINAAN --}}
<div class="modal-backdrop-custom" id="modalAddBinaan">
    <div class="modal-box-custom">
        <div class="modal-header-custom">
            <h3><i class="fas fa-link" style="color:#2e7d32"></i> Tambah Kelompok Binaan</h3>
            <button type="button" class="modal-close-btn" onclick="closeAddBinaanModal()">&times;</button>
        </div>
        <form method="POST" action="{{ Auth::user()->role === 'admin_cdk' ? route('admin-cdk.data-pendamping.tambah-binaan', $pendamping->id_pendamping) : route('admin.data-pendamping.tambah-binaan', $pendamping->id_pendamping) }}">
            @csrf
            <div class="modal-body-custom">
                <div style="margin-bottom:15px">
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px;color:#475569">
                        Pilih Kelompok <span style="color:#ef4444">*</span>
                    </label>
                    <select name="id_kelompok" required style="width:100%;height:38px;padding:6px 10px;border:1px solid #ccc;border-radius:4px;font-size:13px">
                        <option value="">-- Pilih Kelompok --</option>
                        @foreach($kelompokTersedia as $kt)
                            <option value="{{ $kt->id }}">
                                {{ $kt->nama_kelompok }} (Desa: {{ $kt->desa ?? '-' }}, Kec: {{ $kt->kecamatan ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div style="padding:12px 20px;background:#f8fafc;border-top:1px solid #e2e8f0;display:flex;justify-content:flex-end;gap:8px">
                <button type="button" onclick="closeAddBinaanModal()" style="background:#fff;border:1px solid #ccc;padding:7px 14px;border-radius:4px;cursor:pointer;font-weight:600;font-size:13px">Batal</button>
                <button type="submit" style="background:#2e7d32;color:#fff;border:none;padding:7px 16px;border-radius:4px;cursor:pointer;font-weight:600;font-size:13px"><i class="fas fa-save"></i> Hubungkan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openAddBinaanModal() {
    document.getElementById('modalAddBinaan').classList.add('show');
}
function closeAddBinaanModal() {
    document.getElementById('modalAddBinaan').classList.remove('show');
}
window.addEventListener('click', function(e) {
    var modal = document.getElementById('modalAddBinaan');
    if (e.target === modal) {
        closeAddBinaanModal();
    }
});
</script>
@endpush
