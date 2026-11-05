@if(Auth::check() && Auth::user()->role === 'pendamping')
<div style="padding:10px 12px;margin:0 10px 12px;background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.3);border-radius:8px;text-align:center">
    <div style="font-size:11px;color:#fca5a5;font-weight:600;margin-bottom:6px">
        <i class="fas fa-user-shield"></i> Mode Pendamping
    </div>
    <a href="{{ route('pendamping.keluar-kelompok') }}" class="btn" style="background:#dc2626;color:#fff;font-size:11px;padding:6px 10px;display:flex;align-items:center;justify-content:center;gap:6px;text-decoration:none;border-radius:6px;font-weight:700">
        <i class="fas fa-arrow-left"></i> Menu Pendamping
    </a>
</div>
@endif

<a href="{{ route('kelompok.dashboard') }}" class="{{ request()->routeIs('kelompok.dashboard') ? 'active' : '' }}">
    <i class="fas fa-home"></i>
    <span>Dashboard</span>
</a>

<div class="sidebar-category">Data Kelompok</div>
<a href="{{ route('kelompok.data-kelompok') }}" class="{{ request()->routeIs('kelompok.data-kelompok') ? 'active' : '' }}">
    <i class="fas fa-users"></i>
    <span>Data Kelompok</span>
</a>
<a href="{{ route('kelompok.peta') }}" class="{{ request()->routeIs('kelompok.peta*') ? 'active' : '' }}">
    <i class="fas fa-map-location-dot"></i>
    <span>Peta Wilayah Kelompok</span>
</a>
<a href="{{ route('kelompok.anggota') }}" class="{{ request()->routeIs('kelompok.anggota*') ? 'active' : '' }}">
    <i class="fas fa-user-friends"></i>
    <span>Anggota</span>
</a>
<a href="{{ route('kelompok.kups') }}" class="{{ request()->routeIs('kelompok.kups*') ? 'active' : '' }}">
    <i class="fas fa-store"></i>
    <span>KUPS</span>
</a>
<a href="{{ route('kelompok.kegiatan') }}" class="{{ request()->routeIs('kelompok.kegiatan*') ? 'active' : '' }}">
    <i class="fas fa-calendar-days"></i>
    <span>Kegiatan</span>
</a>
<a href="{{ route('kelompok.potensi') }}" class="{{ request()->routeIs('kelompok.potensi*') ? 'active' : '' }}">
    <i class="fas fa-seedling"></i>
    <span>Potensi</span>
</a>
<a href="{{ route('kelompok.permasalahan') }}" class="{{ request()->routeIs('kelompok.permasalahan*') ? 'active' : '' }}">
    <i class="fas fa-triangle-exclamation"></i>
    <span>Permasalahan</span>
</a>

<div class="sidebar-category">Pengajuan Berkas</div>
<a href="{{ route('kelompok.pengajuan-rkps') }}" class="{{ request()->routeIs('kelompok.pengajuan-rkps*') ? 'active' : '' }}">
    <i class="fas fa-file-alt"></i>
    <span>Pengajuan RKPS</span>
</a>
<a href="{{ route('kelompok.pengajuan-rkt') }}" class="{{ request()->routeIs('kelompok.pengajuan-rkt*') ? 'active' : '' }}">
    <i class="fas fa-file-signature"></i>
    <span>Pengajuan RKT</span>
</a>
