<a href="{{ route('admin-cdk.dashboard') }}" class="{{ request()->routeIs('admin-cdk.dashboard') ? 'active' : '' }}">
    <i class="fas fa-home"></i>
    <span>Dashboard</span>
</a>

<div class="sidebar-category">Wilayah CDK</div>
<a href="{{ route('admin-cdk.peta-kelompok') }}" class="{{ request()->routeIs('admin-cdk.peta-kelompok') ? 'active' : '' }}">
    <i class="fas fa-map-marked-alt"></i>
    <span>Peta Kelompok</span>
</a>
<a href="{{ route('admin-cdk.data-kelompok') }}" class="{{ request()->routeIs('admin-cdk.data-kelompok') ? 'active' : '' }}">
    <i class="fas fa-users"></i>
    <span>Data Kelompok</span>
</a>
<a href="{{ route('admin-cdk.data-pendamping') }}" class="{{ request()->routeIs('admin-cdk.data-pendamping') ? 'active' : '' }}">
    <i class="fas fa-user-tie"></i>
    <span>Data Pendamping</span>
</a>
<a href="{{ route('admin-cdk.data-anggota') }}" class="{{ request()->routeIs('admin-cdk.data-anggota') ? 'active' : '' }}">
    <i class="fas fa-user-friends"></i>
    <span>Data Anggota</span>
</a>

<div class="sidebar-category">Verifikasi & Berkas</div>
<a href="{{ route('admin-cdk.verifikasi-rkps') }}" class="{{ request()->routeIs('admin-cdk.verifikasi-rkps') ? 'active' : '' }}">
    <i class="fas fa-file-alt"></i>
    <span>Verifikasi RKPS</span>
</a>
<a href="{{ route('admin-cdk.verifikasi-rkt') }}" class="{{ request()->routeIs('admin-cdk.verifikasi-rkt') ? 'active' : '' }}">
    <i class="fas fa-file-signature"></i>
    <span>Verifikasi RKT</span>
</a>
<a href="{{ route('admin-cdk.rekap-wilayah') }}" class="{{ request()->routeIs('admin-cdk.rekap-wilayah') ? 'active' : '' }}">
    <i class="fas fa-table"></i>
    <span>Rekap Wilayah</span>
</a>

<div class="sidebar-category">Monitoring & Laporan</div>
<a href="{{ route('admin-cdk.monitoring-nte') }}" class="{{ request()->routeIs('admin-cdk.monitoring-nte') ? 'active' : '' }}">
    <i class="fas fa-chart-line"></i>
    <span>Monitoring NTE</span>
</a>
<a href="{{ route('admin-cdk.data-laporan') }}" class="{{ request()->routeIs('admin-cdk.data-laporan') ? 'active' : '' }}">
    <i class="fas fa-clipboard-list"></i>
    <span>Data Laporan</span>
</a>

<div class="sidebar-category">Pusat Berkas</div>
<a href="{{ config('services.google_drive.folder_url', 'https://drive.google.com/drive/folders/1C7CJuf111ut_L88ar45q1ut4LD8iwxKv?usp=sharing') }}" target="_blank" rel="noopener noreferrer" title="Buka Google Drive di tab baru">
    <i class="fab fa-google-drive"></i>
    <span>Google Drive</span>
    <i class="fas fa-arrow-up-right-from-square" style="font-size: 11px; opacity: 0.6; min-width: auto; width: auto;"></i>
</a>