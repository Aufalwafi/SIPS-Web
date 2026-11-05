<aside class="sidebar">
    <h2 class="brand">SI PS ONLINE</h2>
    <div style="padding:10px 0">
        <a href="{{ route('pendamping.dashboard') }}" class="{{ request()->routeIs('pendamping.dashboard') ? 'active' : '' }}"><span><i class="fas fa-home" style="width:20px"></i> Dashboard</span></a>
        <div class="sidebar-category">Data Saya</div>
        <a href="{{ route('pendamping.kelompok-saya') }}" class="{{ request()->routeIs('pendamping.kelompok-saya*') ? 'active' : '' }}"><span><i class="fas fa-users" style="width:20px"></i> Kelompok Saya</span></a>
        <a href="{{ route('pendamping.anggota') }}" class="{{ request()->routeIs('pendamping.anggota') ? 'active' : '' }}"><span><i class="fas fa-user-friends" style="width:20px"></i> Anggota</span></a>
        <div class="sidebar-category">Pengajuan</div>
        <a href="{{ route('pendamping.pengajuan-rkps') }}" class="{{ request()->routeIs('pendamping.pengajuan-rkps') ? 'active' : '' }}"><span><i class="fas fa-file-alt" style="width:20px"></i> Pengajuan RKPS</span></a>
        <a href="{{ route('pendamping.pengajuan-rkt') }}" class="{{ request()->routeIs('pendamping.pengajuan-rkt') ? 'active' : '' }}"><span><i class="fas fa-file-signature" style="width:20px"></i> Pengajuan RKT</span></a>
        <div class="sidebar-category">Laporan</div>
        <a href="{{ route('pendamping.laporan-kegiatan') }}" class="{{ request()->routeIs('pendamping.laporan-kegiatan*') ? 'active' : '' }}"><span><i class="fas fa-clipboard-list" style="width:20px"></i> Laporan Kegiatan</span></a>
    </div>
</aside>