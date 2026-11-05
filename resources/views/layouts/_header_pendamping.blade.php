@php
    $cdkNameHeader = $wilayahLabel ?? null;
    if (!$cdkNameHeader && Auth::check()) {
        $u = Auth::user();
        if (!empty($u->id_cdk)) {
            $c = DB::table('cdk')->where('id', $u->id_cdk)->first();
            if ($c && !empty($c->nama)) {
                $cdkNameHeader = $c->nama;
            }
        }
        if (!$cdkNameHeader) {
            $kc = DB::table('pendamping_kelompok')
                ->join('kelompok', 'pendamping_kelompok.id_kelompok', '=', 'kelompok.id')
                ->leftJoin('cdk', 'kelompok.id_cdk', '=', 'cdk.id')
                ->where('pendamping_kelompok.id_user', $u->id)
                ->select('cdk.nama as cdk_nama', 'kelompok.kabupaten')
                ->first();
            if ($kc && !empty($kc->cdk_nama)) {
                $cdkNameHeader = $kc->cdk_nama;
            } elseif ($kc && !empty($kc->kabupaten)) {
                $cdkNameHeader = 'CDK Wilayah ' . $kc->kabupaten;
            }
        }
    }
@endphp
<div class="header">
    <div class="header-left">
        <img src="{{ url('/assets/logo-jatim.png') }}" style="height:40px">
        <div class="instansi"><b>Dinas Kehutanan Provinsi Jawa Timur</b><br>{{ $cdkNameHeader ?? 'CDK Wilayah Jawa Timur' }}</div>
    </div>
    <div class="header-right">
        <div class="notification-wrapper">
            <button class="notification-icon" id="notifBtn" type="button">
                <i class="fas fa-bell"></i>
                @php $totalNotif = ($rkpsMenunggu ?? 0) + ($rktMenunggu ?? 0); @endphp
                @if($totalNotif > 0)
                    <span class="notification-badge">{{ $totalNotif }}</span>
                @endif
            </button>
            <div class="notification-dropdown" id="notifDropdown">
                <div class="notif-header">
                    <span>Notifikasi Berkas</span>
                    <span>{{ $totalNotif }} Baru</span>
                </div>
                <div class="notif-body">
                    <a href="{{ route('pendamping.pengajuan-rkps') }}" class="notif-item">
                        <span>Pengajuan RKPS Menunggu</span>
                        <b>{{ $rkpsMenunggu ?? 0 }}</b>
                    </a>
                    <a href="{{ route('pendamping.pengajuan-rkt') }}" class="notif-item">
                        <span>Pengajuan RKT Menunggu</span>
                        <b>{{ $rktMenunggu ?? 0 }}</b>
                    </a>
                </div>
            </div>
        </div>
        <span class="hi-user">Hi, <b>{{ Auth::user()->nama ?? Auth::user()->username }}</b></span>
        <span class="divider">|</span>
        <form method="POST" action="{{ route('logout') }}" style="display:inline">
            @csrf
            <button type="submit" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</button>
        </form>
    </div>
</div>
<script>
(function(){
    var b=document.getElementById('notifBtn'),d=document.getElementById('notifDropdown');
    if(b){b.addEventListener('click',function(e){e.stopPropagation();d.classList.toggle('show')});window.addEventListener('click',function(){d.classList.remove('show')})}
})();
</script>
