<header class="header">
    <div class="header-left">
        <img src="{{ url('/assets/logo-jatim.png') }}" alt="Logo" class="logo-header">
        <div class="instansi"><b>Dinas Kehutanan Provinsi Jawa Timur</b><br>{{ $wilayahLabel ?? ($kelompok->nama_kelompok ?? 'CDK Pusat') }}</div>
    </div>
    <div class="header-right">
        @php
            $role = Auth::user()->role ?? '';
            $totalNotif = ($rkpsMenunggu ?? 0) + ($rktMenunggu ?? 0);
            $notificationRoutePrefix = $role === 'kelompok' ? 'kelompok' : ($role === 'pendamping' ? 'pendamping' : 'admin');
        @endphp
        @if($role !== 'admin' || isset($rkpsMenunggu) || isset($rktMenunggu))
            <div class="notification-wrapper">
                <button class="notification-icon" id="notifBtn" type="button" aria-label="Notifikasi">
                    <i class="fas fa-bell"></i>
                    @if($totalNotif > 0)<span class="notification-badge">{{ $totalNotif }}</span>@endif
                </button>
                <div class="notification-dropdown" id="notifDropdown">
                    <div class="notif-header"><span>Notifikasi Berkas</span><span>{{ $totalNotif }} Baru</span></div>
                    <div class="notif-body">
                        <a href="{{ route($notificationRoutePrefix . '.pengajuan-rkps') }}" class="notif-item"><span>Pengajuan RKPS Menunggu</span><b>{{ $rkpsMenunggu ?? 0 }}</b></a>
                        <a href="{{ route($notificationRoutePrefix . '.pengajuan-rkt') }}" class="notif-item"><span>Pengajuan RKT Menunggu</span><b>{{ $rktMenunggu ?? 0 }}</b></a>
                    </div>
                </div>
            </div>
        @endif
        <span class="hi-user">Hi, <b>{{ Auth::user()->nama ?? Auth::user()->username }}</b></span>
        <span class="divider">|</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</button>
        </form>
    </div>
</header>
@once
@push('scripts')
<script>
(function () {
    const button = document.getElementById('notifBtn');
    const dropdown = document.getElementById('notifDropdown');
    if (!button || !dropdown) return;
    button.addEventListener('click', function (event) { event.stopPropagation(); dropdown.classList.toggle('show'); });
    window.addEventListener('click', function () { dropdown.classList.remove('show'); });
})();
</script>
@endpush
@endonce
