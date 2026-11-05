@php
    $role = Auth::user()->role ?? '';
@endphp

<aside class="sidebar">
    <div class="brand">
        <div class="brand-main">
            <i class="fas fa-leaf"></i>
            <span>SI PS ONLINE</span>
        </div>
        <div class="brand-sub">Sistem Informasi Perhutanan Sosial</div>
    </div>
    <nav class="sidebar-menu" aria-label="Navigasi utama">
        @if($role === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i><span>Dashboard</span>
            </a>

            <div class="sidebar-category">Data Master</div>
            <a href="{{ route('admin.data-kelompok') }}" class="{{ request()->routeIs('admin.data-kelompok') ? 'active' : '' }}"><i class="fas fa-users"></i><span>Data Kelompok</span></a>
            <a href="{{ route('admin.peta-kelompok') }}" class="{{ request()->routeIs('admin.peta-kelompok') ? 'active' : '' }}"><i class="fas fa-map"></i><span>Peta Kelompok</span></a>
            <a href="{{ route('admin.data-pendamping') }}" class="{{ request()->routeIs('admin.data-pendamping') ? 'active' : '' }}"><i class="fas fa-user-tie"></i><span>Data Pendamping</span></a>
            <a href="{{ route('admin.data-anggota') }}" class="{{ request()->routeIs('admin.data-anggota') ? 'active' : '' }}"><i class="fas fa-user-friends"></i><span>Data Anggota</span></a>
            <a href="{{ route('admin.admin-cdk') }}" class="{{ request()->routeIs('admin.admin-cdk*') ? 'active' : '' }}"><i class="fas fa-building-user"></i><span>Admin CDK</span></a>

            <div class="sidebar-category">Verifikasi &amp; Berkas</div>
            <a href="{{ route('admin.verifikasi-rkps') }}" class="{{ request()->routeIs('admin.verifikasi-rkps*') ? 'active' : '' }}"><i class="fas fa-file-alt"></i><span>Verifikasi RKPS</span></a>
            <a href="{{ route('admin.verifikasi-rkt') }}" class="{{ request()->routeIs('admin.verifikasi-rkt*') ? 'active' : '' }}"><i class="fas fa-file-signature"></i><span>Verifikasi RKT</span></a>
            <a href="{{ route('admin.data-rkps') }}" class="{{ request()->routeIs('admin.data-rkps*') ? 'active' : '' }}"><i class="fas fa-check-circle"></i><span>Input RKPS Sah</span></a>
            <a href="{{ route('admin.data-rkt') }}" class="{{ request()->routeIs('admin.data-rkt*') ? 'active' : '' }}"><i class="fas fa-check-double"></i><span>Input RKT Sah</span></a>

            <div class="sidebar-category">Monitoring</div>
            <a href="{{ route('admin.monitoring-pengajuan') }}" class="{{ request()->routeIs('admin.monitoring-pengajuan') ? 'active' : '' }}"><i class="fas fa-clock"></i><span>Monitoring Pengajuan</span></a>
            <a href="{{ route('admin.monitoring-ps') }}" class="{{ request()->routeIs('admin.monitoring-ps') ? 'active' : '' }}"><i class="fas fa-desktop"></i><span>Monitoring PS</span></a>
            <a href="{{ route('admin.monitoring-nte') }}" class="{{ request()->routeIs('admin.monitoring-nte') ? 'active' : '' }}"><i class="fas fa-chart-line"></i><span>Monitoring NTE</span></a>

            <div class="sidebar-category">Sistem &amp; Laporan</div>
            <a href="{{ route('admin.data-laporan') }}" class="{{ request()->routeIs('admin.data-laporan') ? 'active' : '' }}"><i class="fas fa-print"></i><span>Laporan</span></a>
            <a href="{{ route('admin.data-user') }}" class="{{ request()->routeIs('admin.data-user*') ? 'active' : '' }}"><i class="fas fa-user-cog"></i><span>Data User</span></a>
            <a href="{{ route('admin.aktivitas-user') }}" class="{{ request()->routeIs('admin.aktivitas-user*') ? 'active' : '' }}"><i class="fas fa-list-check"></i><span>Aktivitas User</span></a>
            <a href="{{ route('admin.recycle-bin') }}" class="{{ request()->routeIs('admin.recycle-bin*') ? 'active' : '' }}"><i class="fas fa-trash-restore"></i><span>Recycle Bin</span></a>
            <div class="sidebar-category">Pusat Berkas</div>
            <a href="{{ config('services.google_drive.folder_url', 'https://drive.google.com/drive/folders/1C7CJuf111ut_L88ar45q1ut4LD8iwxKv?usp=sharing') }}" target="_blank" rel="noopener noreferrer"><i class="fab fa-google-drive"></i><span>Google Drive</span></a>
        @elseif($role === 'pendamping')
            <a href="{{ route('pendamping.dashboard') }}" class="{{ request()->routeIs('pendamping.dashboard') ? 'active' : '' }}"><i class="fas fa-home"></i><span>Dashboard</span></a>
            <div class="sidebar-category">Data Saya</div>
            <a href="{{ route('pendamping.kelompok-saya') }}" class="{{ request()->routeIs('pendamping.kelompok-saya*') ? 'active' : '' }}"><i class="fas fa-users"></i><span>Kelompok Saya</span></a>
            <a href="{{ route('pendamping.anggota') }}" class="{{ request()->routeIs('pendamping.anggota') ? 'active' : '' }}"><i class="fas fa-user-friends"></i><span>Total Anggota</span></a>
            <div class="sidebar-category">Pengajuan</div>
            <a href="{{ route('pendamping.pengajuan-rkps') }}" class="{{ request()->routeIs('pendamping.pengajuan-rkps*') ? 'active' : '' }}"><i class="fas fa-file-alt"></i><span>Pengajuan RKPS</span></a>
            <a href="{{ route('pendamping.pengajuan-rkt') }}" class="{{ request()->routeIs('pendamping.pengajuan-rkt*') ? 'active' : '' }}"><i class="fas fa-file-signature"></i><span>Pengajuan RKT</span></a>
            <div class="sidebar-category">Pusat Berkas</div>
            <a href="{{ config('services.google_drive.folder_url', 'https://drive.google.com/drive/folders/1C7CJuf111ut_L88ar45q1ut4LD8iwxKv?usp=sharing') }}" target="_blank" rel="noopener noreferrer"><i class="fab fa-google-drive"></i><span>Google Drive</span></a>
        @elseif($role === 'kelompok')
            <a href="{{ route('kelompok.dashboard') }}" class="{{ request()->routeIs('kelompok.dashboard') ? 'active' : '' }}"><i class="fas fa-home"></i><span>Dashboard</span></a>
            <div class="sidebar-category">Data Kelompok</div>
            <a href="{{ route('kelompok.data-kelompok') }}" class="{{ request()->routeIs('kelompok.data-kelompok') ? 'active' : '' }}"><i class="fas fa-users"></i><span>Data Kelompok Saya</span></a>
            <a href="{{ route('kelompok.peta') }}" class="{{ request()->routeIs('kelompok.peta*') ? 'active' : '' }}"><i class="fas fa-map-location-dot"></i><span>Peta Wilayah Kelompok</span></a>
            <a href="{{ route('kelompok.anggota') }}" class="{{ request()->routeIs('kelompok.anggota') ? 'active' : '' }}"><i class="fas fa-user-friends"></i><span>Anggota Kelompok</span></a>
            <div class="sidebar-category">Pengajuan Berkas</div>
            <a href="{{ route('kelompok.pengajuan-rkps') }}" class="{{ request()->routeIs('kelompok.pengajuan-rkps*') ? 'active' : '' }}"><i class="fas fa-file-alt"></i><span>Pengajuan RKPS</span></a>
            <a href="{{ route('kelompok.pengajuan-rkt') }}" class="{{ request()->routeIs('kelompok.pengajuan-rkt*') ? 'active' : '' }}"><i class="fas fa-file-signature"></i><span>Pengajuan RKT</span></a>
        @elseif($role === 'admin_cdk' || $role === 'super_admin')
            {{-- TODO: role admin_cdk/super_admin belum memiliki route khusus; gunakan menu admin bila diaktifkan. --}}
        @endif
    </nav>
</aside>
