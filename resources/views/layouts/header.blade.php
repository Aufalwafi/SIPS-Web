{{-- Variabel dari HeaderComposer: $rkpsMenunggu, $rktMenunggu, $totalNotif, $headerNamaInstansi, $headerWilayah --}}
@php
    $role = Auth::user()->role ?? '';
@endphp
<div class="header">
    <div class="header-left">
        <button class="sidebar-toggle" id="sidebarToggle" type="button" aria-label="Buka menu">
            <i class="fas fa-bars"></i>
        </button>
        <img src="{{ asset('assets/logo-jatim.png') }}"
             onerror="if(!this.dataset.retry){this.dataset.retry='1';this.src='{{ url('/assets/logo-jatim.png') }}';}else if(this.dataset.retry==='1'){this.dataset.retry='2';this.src='/assets/logo-jatim.png';}else if(this.dataset.retry==='2'){this.dataset.retry='3';this.src='/public/assets/logo-jatim.png';}"
             class="header-logo" alt="Logo Jatim">
        <div class="instansi">
            <b>{{ $headerNamaInstansi ?? 'Dinas Kehutanan Provinsi Jawa Timur' }}</b>
            <span>{{ $headerWilayah ?? 'Pemerintah Provinsi Jawa Timur' }}</span>
        </div>
    </div>
    <div class="header-right">
        {{-- NOTIFICATION BELL --}}
        <div class="notification-wrapper">
            <button class="notification-icon" id="notifBtn" type="button" aria-label="Notifikasi">
                <i class="fas fa-bell"></i>
                @if($totalNotif > 0)
                    <span class="notification-badge">{{ $totalNotif }}</span>
                @endif
            </button>
            <div class="notification-dropdown" id="notifDropdown">
                <div class="notif-header">
                    <span><i class="fas fa-bell" style="margin-right:6px"></i>Notifikasi</span>
                    @if($totalNotif > 0)
                        <span class="notif-total">{{ $totalNotif }} baru</span>
                    @else
                        <span class="notif-total">0 baru</span>
                    @endif
                </div>
                <div class="notif-body">
                    @if($rkpsMenunggu > 0)
                        @php
                            if ($role === 'admin') $rkpsUrl = route('admin.verifikasi-rkps');
                            elseif ($role === 'admin_cdk') $rkpsUrl = route('admin-cdk.verifikasi-rkps');
                            elseif ($role === 'pendamping') $rkpsUrl = route('pendamping.pengajuan-rkps');
                            elseif ($role === 'kelompok') $rkpsUrl = route('kelompok.pengajuan-rkps');
                            else $rkpsUrl = '#';

                            if ($role === 'admin') $rkpsDesc = 'Menunggu verifikasi Admin Provinsi';
                            elseif ($role === 'admin_cdk') $rkpsDesc = 'Perlu diverifikasi di wilayah Anda';
                            elseif ($role === 'pendamping') $rkpsDesc = 'Dari kelompok binaan Anda';
                            elseif ($role === 'kelompok') $rkpsDesc = 'Pengajuan Anda sedang diproses';
                            else $rkpsDesc = '';
                        @endphp
                        <a href="{{ $rkpsUrl }}" class="notif-item">
                            <div class="notif-info">
                                <i class="fas fa-file-alt notif-icon" style="color:#e67e22"></i>
                                <div>
                                    <div class="notif-title">Pengajuan RKPS Menunggu</div>
                                    <div class="notif-desc">{{ $rkpsDesc }}</div>
                                </div>
                            </div>
                            <span class="notif-count">{{ $rkpsMenunggu }}</span>
                        </a>
                    @endif

                    @if($rktMenunggu > 0)
                        @php
                            if ($role === 'admin') $rktUrl = route('admin.verifikasi-rkt');
                            elseif ($role === 'admin_cdk') $rktUrl = route('admin-cdk.verifikasi-rkt');
                            elseif ($role === 'pendamping') $rktUrl = route('pendamping.pengajuan-rkt');
                            elseif ($role === 'kelompok') $rktUrl = route('kelompok.pengajuan-rkt');
                            else $rktUrl = '#';

                            if ($role === 'admin') $rktDesc = 'Menunggu verifikasi Admin Provinsi';
                            elseif ($role === 'admin_cdk') $rktDesc = 'Perlu diverifikasi di wilayah Anda';
                            elseif ($role === 'pendamping') $rktDesc = 'Dari kelompok binaan Anda';
                            elseif ($role === 'kelompok') $rktDesc = 'Pengajuan Anda sedang diproses';
                            else $rktDesc = '';
                        @endphp
                        <a href="{{ $rktUrl }}" class="notif-item">
                            <div class="notif-info">
                                <i class="fas fa-file-signature notif-icon" style="color:#c62828"></i>
                                <div>
                                    <div class="notif-title">Pengajuan RKT Menunggu</div>
                                    <div class="notif-desc">{{ $rktDesc }}</div>
                                </div>
                            </div>
                            <span class="notif-count">{{ $rktMenunggu }}</span>
                        </a>
                    @endif

                    @if($totalNotif === 0)
                        <div class="notif-empty">
                            <i class="fas fa-check-circle" style="font-size:28px;color:#22c55e;margin-bottom:8px"></i>
                            <p style="font-weight:600;color:#1e293b;margin-bottom:2px">Semua Beres!</p>
                            <p style="font-size:11px;color:#94a3b8">Tidak ada pengajuan yang menunggu verifikasi</p>
                        </div>
                    @endif
                </div>

                @if($totalNotif > 0 && ($role === 'admin' || $role === 'admin_cdk'))
                <div style="padding:8px 12px;border-top:1px solid #f1f5f9;background:#f8fafc;border-radius:0 0 10px 10px;display:flex;gap:6px">
                    @if($rkpsMenunggu > 0)
                    <a href="{{ $role === 'admin' ? route('admin.verifikasi-rkps') : route('admin-cdk.verifikasi-rkps') }}"
                       style="flex:1;text-align:center;background:#f59e0b;color:#fff;padding:6px;border-radius:6px;font-size:11px;font-weight:600;text-decoration:none">
                        <i class="fas fa-file-signature"></i> Verifikasi RKPS
                    </a>
                    @endif
                    @if($rktMenunggu > 0)
                    <a href="{{ $role === 'admin' ? route('admin.verifikasi-rkt') : route('admin-cdk.verifikasi-rkt') }}"
                       style="flex:1;text-align:center;background:#3b82f6;color:#fff;padding:6px;border-radius:6px;font-size:11px;font-weight:600;text-decoration:none">
                        <i class="fas fa-clipboard-check"></i> Verifikasi RKT
                    </a>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <span class="hi-user">Hi, <b>{{ Auth::user()->nama ?? Auth::user()->username }}</b></span>
        <span class="divider">|</span>
        <form method="POST" action="{{ route('logout') }}" style="display:inline">
            @csrf
            <button type="submit" class="btn-logout" title="Logout" style="background:#dc2626 !important;color:#ffffff !important;border:1px solid #b91c1c !important;padding:6px 14px !important;border-radius:6px !important;font-weight:600 !important;cursor:pointer !important;display:inline-flex !important;align-items:center !important;gap:6px !important;box-shadow:0 1px 3px rgba(0,0,0,0.2) !important;">
                <i class="fas fa-sign-out-alt" style="color:#ffffff !important"></i> <span class="logout-text" style="color:#ffffff !important">Logout</span>
            </button>
        </form>
    </div>
</div>

<script>
(function(){
    var b=document.getElementById('notifBtn'), d=document.getElementById('notifDropdown');
    if(b && d){
        b.addEventListener('click', function(e){
            e.stopPropagation();
            d.classList.toggle('show');
        });
        d.addEventListener('click', function(e){ e.stopPropagation(); });
        window.addEventListener('click', function(){ d.classList.remove('show'); });
    }
})();
</script>