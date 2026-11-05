<aside class="sidebar">
    <h2>SI PS ONLINE</h2>
    <div style="padding:10px 0">
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><span><i class="fas fa-home" style="width:20px"></i> Dashboard</span></a>

        <div class="sidebar-category">Data Master</div>
        <a href="{{ route('admin.data-kelompok') }}" class="{{ request()->routeIs('admin.data-kelompok') ? 'active' : '' }}"><span><i class="fas fa-users" style="width:20px"></i> Data Kelompok</span></a>
        <a href="{{ route('admin.peta-kelompok') }}" class="{{ request()->routeIs('admin.peta-kelompok') ? 'active' : '' }}"><span><i class="fas fa-map" style="width:20px"></i> Peta Kelompok</span></a>
        <a href="{{ route('admin.data-pendamping') }}" class="{{ request()->routeIs('admin.data-pendamping') ? 'active' : '' }}"><span><i class="fas fa-user-tie" style="width:20px"></i> Data Pendamping</span></a>
        <a href="{{ route('admin.data-anggota') }}" class="{{ request()->routeIs('admin.data-anggota') ? 'active' : '' }}"><span><i class="fas fa-user-friends" style="width:20px"></i> Data Anggota</span></a>

        <div class="sidebar-category">Verifikasi &amp; Berkas</div>
        <a href="{{ route('admin.verifikasi-rkps') }}" class="{{ request()->routeIs('admin.verifikasi-rkps') ? 'active' : '' }}"><span><i class="fas fa-file-alt" style="width:20px"></i> Verifikasi RKPS</span></a>
        <a href="{{ route('admin.verifikasi-rkt') }}" class="{{ request()->routeIs('admin.verifikasi-rkt') ? 'active' : '' }}"><span><i class="fas fa-file-signature" style="width:20px"></i> Verifikasi RKT</span></a>
        <a href="{{ route('admin.data-rkps') }}" class="{{ request()->routeIs('admin.data-rkps*') ? 'active' : '' }}"><span><i class="fas fa-check-circle" style="width:20px"></i> Input RKPS Sah</span></a>
        <a href="{{ route('admin.data-rkt') }}" class="{{ request()->routeIs('admin.data-rkt*') ? 'active' : '' }}"><span><i class="fas fa-check-double" style="width:20px"></i> Input RKT Sah</span></a>

        <div class="sidebar-category">Monitoring</div>
        <a href="{{ route('admin.monitoring-pengajuan') }}" class="{{ request()->routeIs('admin.monitoring-pengajuan') ? 'active' : '' }}"><span><i class="fas fa-clock" style="width:20px"></i> Monitoring Pengajuan</span></a>
        <a href="{{ route('admin.monitoring-ps') }}" class="{{ request()->routeIs('admin.monitoring-ps') ? 'active' : '' }}"><span><i class="fas fa-desktop" style="width:20px"></i> Monitoring PS</span></a>
        <a href="{{ route('admin.monitoring-nte') }}" class="{{ request()->routeIs('admin.monitoring-nte') ? 'active' : '' }}"><span><i class="fas fa-chart-line" style="width:20px"></i> Monitoring NTE</span></a>

        <div class="sidebar-category">Sistem &amp; Laporan</div>
        <a href="{{ route('admin.data-laporan') }}" class="{{ request()->routeIs('admin.data-laporan') ? 'active' : '' }}"><span><i class="fas fa-print" style="width:20px"></i> Laporan</span></a>
        <a href="{{ route('admin.data-user*') }}" class="{{ request()->routeIs('admin.data-user*') ? 'active' : '' }}"><span><i class="fas fa-user-cog" style="width:20px"></i> Data User</span></a>
        <a href="{{ route('admin.aktivitas-user') }}" class="{{ request()->routeIs('admin.aktivitas-user') ? 'active' : '' }}"><span><i class="fas fa-list-check" style="width:20px"></i> Aktivitas User</span></a>
    </div>

    <div class="logout-box">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"><i class="fas fa-sign-out-alt"></i> Logout</button>
        </form>
    </div>
</aside>