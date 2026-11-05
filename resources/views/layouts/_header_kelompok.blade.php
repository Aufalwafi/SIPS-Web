@php
    $cdkKelompokHeader = null;
    if (isset($kelompok) && !empty($kelompok->id_cdk)) {
        $c = DB::table('cdk')->where('id', $kelompok->id_cdk)->first();
        if ($c && !empty($c->nama)) {
            $cdkKelompokHeader = $c->nama;
        }
    }
    if (!$cdkKelompokHeader && isset($kelompok) && !empty($kelompok->kabupaten)) {
        $cdkKelompokHeader = 'CDK Wilayah ' . $kelompok->kabupaten;
    }
@endphp
<div class="header">
    <div class="header-left">
        <img src="{{ url('/assets/logo-jatim.png') }}" style="height:40px">
        <div class="instansi">
            <b>Dinas Kehutanan Provinsi Jawa Timur</b><br>
            <span style="font-size:11px;opacity:.9">{{ $cdkKelompokHeader ?? 'CDK Wilayah Jawa Timur' }}</span>
        </div>
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
                    <a href="{{ route('kelompok.pengajuan-rkps') }}" class="notif-item">
                        <span>Pengajuan RKPS Menunggu</span>
                        <b>{{ $rkpsMenunggu ?? 0 }}</b>
                    </a>
                    <a href="{{ route('kelompok.pengajuan-rkt') }}" class="notif-item">
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
