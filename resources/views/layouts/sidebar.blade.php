@php
    $role = Auth::user()->role ?? '';
@endphp

<aside class="sidebar" id="sidebar">
    <div class="brand">
        <div class="brand-main">
            <i class="fas fa-leaf"></i>
            <span>SI PS ONLINE</span>
        </div>
        <div class="brand-sub">Sistem Informasi Perhutanan Sosial</div>
        <button class="sidebar-close" id="sidebarClose" type="button" aria-label="Tutup menu">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <nav class="sidebar-nav">
        @if($role === 'admin_cdk')
            @include('layouts._sidebar_admin_cdk')
        @elseif($role === 'admin')
            @include('layouts._sidebar_admin')
        @elseif($role === 'pendamping')
            @if(request()->is('kelompok*'))
                @include('layouts._sidebar_kelompok')
            @else
                @include('pendamping._sidebar_pendamping')
            @endif

        @elseif($role === 'kelompok')
            @include('layouts._sidebar_kelompok')
        @endif
    </nav>
    <script>
        (function() {
            try {
                var saved = sessionStorage.getItem('sidebar_scroll_position');
                var sb = document.getElementById('sidebar');
                if (sb && saved !== null) {
                    sb.scrollTop = parseInt(saved, 10);
                }
            } catch (e) {}
        })();
    </script>
</aside>

