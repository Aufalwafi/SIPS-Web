<nav class="sidebar-nav">
    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="fas fa-home"></i>
        <span>Dashboard</span>
    </a>

    <div class="sidebar-category">Data Master</div>
    <a href="{{ route('admin.peta-kelompok') }}" class="{{ request()->routeIs('admin.peta-kelompok') ? 'active' : '' }}">
        <i class="fas fa-map"></i>
        <span>Peta Kelompok</span>
    </a>
    <a href="{{ route('admin.data-pendamping') }}" class="{{ request()->routeIs('admin.data-pendamping*') ? 'active' : '' }}">
        <i class="fas fa-user-tie"></i>
        <span>Data Pendamping</span>
    </a>
    <a href="{{ route('admin.data-kelompok') }}" class="{{ request()->routeIs('admin.data-kelompok') ? 'active' : '' }}">
        <i class="fas fa-users"></i>
        <span>Data Kelompok</span>
    </a>
    <a href="{{ route('admin.data-anggota') }}" class="{{ request()->routeIs('admin.data-anggota') ? 'active' : '' }}">
        <i class="fas fa-user-friends"></i>
        <span>Data Anggota</span>
    </a>
    <a href="{{ route('admin.admin-cdk') }}" class="{{ request()->routeIs('admin.admin-cdk*') ? 'active' : '' }}">
        <i class="fas fa-building-user"></i>
        <span>Admin CDK</span>
    </a>

    <div class="sidebar-category">Dokumen RKPS &amp; RKT</div>
    <a href="{{ route('admin.verifikasi-rkps') }}" class="{{ request()->routeIs('admin.verifikasi-rkps*') ? 'active' : '' }}">
        <i class="fas fa-file-alt"></i>
        <span>Verifikasi RKPS</span>
    </a>
    <a href="{{ route('admin.verifikasi-rkt') }}" class="{{ request()->routeIs('admin.verifikasi-rkt*') ? 'active' : '' }}">
        <i class="fas fa-file-signature"></i>
        <span>Verifikasi RKT</span>
    </a>
    <a href="{{ route('admin.data-rkps') }}" class="{{ request()->routeIs('admin.data-rkps*') ? 'active' : '' }}">
        <i class="fas fa-check-circle"></i>
        <span>Input RKPS Sah</span>
    </a>
    <a href="{{ route('admin.data-rkt') }}" class="{{ request()->routeIs('admin.data-rkt*') ? 'active' : '' }}">
        <i class="fas fa-check-double"></i>
        <span>Input RKT Sah</span>
    </a>

    <div class="sidebar-category">Monitoring</div>
    <a href="{{ route('admin.monitoring-pengajuan') }}" class="{{ request()->routeIs('admin.monitoring-pengajuan') ? 'active' : '' }}">
        <i class="fas fa-clock"></i>
        <span>Monitoring Pengajuan</span>
    </a>
    <a href="{{ route('admin.monitoring-ps') }}" class="{{ request()->routeIs('admin.monitoring-ps') ? 'active' : '' }}">
        <i class="fas fa-desktop"></i>
        <span>Monitoring PS</span>
    </a>
    <a href="{{ route('admin.monitoring-nte') }}" class="{{ request()->routeIs('admin.monitoring-nte') ? 'active' : '' }}">
        <i class="fas fa-chart-line"></i>
        <span>Monitoring NTE</span>
    </a>
    <a href="{{ route('admin.data-laporan') }}" class="{{ request()->routeIs('admin.data-laporan') ? 'active' : '' }}">
        <i class="fas fa-print"></i>
        <span>Laporan</span>
    </a>
    <a href="{{ route('admin.data-user') }}" class="{{ request()->routeIs('admin.data-user*') ? 'active' : '' }}">
        <i class="fas fa-user-cog"></i>
        <span>Data User</span>
    </a>
    <a href="{{ route('admin.aktivitas-user') }}" class="{{ request()->routeIs('admin.aktivitas-user') ? 'active' : '' }}">
        <i class="fas fa-list-check"></i>
        <span>Aktivitas User</span>
    </a>

    <div class="sidebar-category">Pusat Berkas</div>
    <a href="{{ route('admin.google-drive-sync') }}" class="{{ request()->routeIs('admin.google-drive-sync*') ? 'active' : '' }}">
        <i class="fas fa-cloud-arrow-up"></i>
        <span>Sync Google Drive</span>
    </a>

    <div class="sidebar-category">Pemulihan Data</div>
    <a href="{{ route('admin.recycle-bin') }}" class="{{ request()->routeIs('admin.recycle-bin*') ? 'active' : '' }}">
        <i class="fas fa-trash-restore"></i>
        <span>Recycle Bin</span>
    </a>
</nav>