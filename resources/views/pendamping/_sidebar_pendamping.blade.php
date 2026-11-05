<a href="{{ route('pendamping.dashboard') }}" class="{{ request()->routeIs('pendamping.dashboard') ? 'active' : '' }}">
    <i class="fas fa-home"></i><span>Dashboard</span>
</a>

<div class="sidebar-category">Data Pendamping</div>
<a href="{{ route('pendamping.kelompok-saya') }}" class="{{ request()->routeIs('pendamping.kelompok-saya*') ? 'active' : '' }}">
    <i class="fas fa-users"></i><span>Kelompok Binaan</span>
</a>
<a href="{{ route('pendamping.anggota') }}" class="{{ request()->routeIs('pendamping.anggota') ? 'active' : '' }}">
    <i class="fas fa-user-friends"></i><span>Anggota</span>
</a>
<a href="{{ route('pendamping.peta-kelompok') }}" class="{{ request()->routeIs('pendamping.peta-kelompok') ? 'active' : '' }}">
    <i class="fas fa-map-marked-alt"></i><span>Peta Kelompok</span>
</a>

<div class="sidebar-category">Ekonomi PS</div>
<a href="{{ route('pendamping.data-nte') }}" class="{{ request()->routeIs('pendamping.data-nte') ? 'active' : '' }}">
    <i class="fas fa-chart-line"></i><span>Data NTE</span>
</a>

<div class="sidebar-category">Pengajuan Berkas</div>
<a href="{{ route('pendamping.pengajuan-rkps') }}" class="{{ request()->routeIs('pendamping.pengajuan-rkps*') ? 'active' : '' }}">
    <i class="fas fa-file-alt"></i><span>Pengajuan RKPS</span>
</a>
<a href="{{ route('pendamping.pengajuan-rkt') }}" class="{{ request()->routeIs('pendamping.pengajuan-rkt*') ? 'active' : '' }}">
    <i class="fas fa-file-signature"></i><span>Pengajuan RKT</span>
</a>

<div class="sidebar-category">Laporan</div>
<a href="{{ route('pendamping.laporan-kegiatan') }}" class="{{ request()->routeIs('pendamping.laporan-kegiatan*') ? 'active' : '' }}">
    <i class="fas fa-clipboard-list"></i><span>Laporan Kegiatan</span>
</a>

<div class="sidebar-category">Pusat Berkas</div>
<a href="{{ config('services.google_drive.folder_url', 'https://drive.google.com/drive/folders/1C7CJuf111ut_L88ar45q1ut4LD8iwxKv?usp=sharing') }}" target="_blank" rel="noopener noreferrer" title="Buka Google Drive di tab baru">
    <i class="fab fa-google-drive"></i><span>Google Drive</span>
    <i class="fas fa-arrow-up-right-from-square" style="font-size: 11px; opacity: 0.6; min-width: auto; width: auto;"></i>
</a>

<form method="POST" action="{{ route('logout') }}" style="margin:0">
    @csrf
    <button type="submit" class="sidebar-logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></button>
</form>