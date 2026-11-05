<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="SI PS ONLINE - Sistem Informasi Perhutanan Sosial Online, Dinas Kehutanan Provinsi Jawa Timur">
    <title>@yield('title', 'SI PS ONLINE')</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    @stack('styles')
</head>
<body>
    {{-- Sidebar overlay (mobile backdrop) --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    @include('layouts.sidebar')

    <div class="main">
        @include('layouts.header')
        <main class="content fade-in">
            @if(Auth::check() && Auth::user()->role === 'pendamping' && request()->is('kelompok*'))
                @php
                    $currKId = session('akses_kelompok_id');
                    $currKelompok = $currKId ? \App\Models\Kelompok::find($currKId) : null;
                @endphp
                <div style="background: linear-gradient(135deg, #1e3a8a, #2563eb); color: #fff; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; box-shadow: 0 3px 10px rgba(37,99,235,0.18);">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="background: rgba(255,255,255,0.2); width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div>
                            <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.85; font-weight: 600;">Mode Akses Pendamping</div>
                            <div style="font-size: 14px; font-weight: 700;">
                                Mengakses & Mengelola Data Kelompok: <span style="color:#fef08a">{{ $currKelompok->nama_kelompok ?? 'Kelompok Dampingan' }}</span>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('pendamping.keluar-kelompok') }}" style="background: #fff; color: #1e3a8a; padding: 7px 14px; border-radius: 6px; font-size: 12px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <i class="fas fa-arrow-left"></i> Kembali ke Menu Pendamping
                    </a>
                </div>
            @endif
            @yield('content')
        </main>
    </div>

    {{-- Toast notification container --}}
    <div class="toast-container" id="toastContainer">
        @if(session('success'))
        <div class="toast toast-success" role="alert">
            <i class="fas fa-check-circle toast-icon"></i>
            <span class="toast-message">{{ session('success') }}</span>
            <button class="toast-close" onclick="this.parentElement.classList.add('toast-out');setTimeout(()=>this.parentElement.remove(),300)">&times;</button>
        </div>
        @endif
        @if(session('error'))
        <div class="toast toast-error" role="alert">
            <i class="fas fa-exclamation-circle toast-icon"></i>
            <span class="toast-message">{{ session('error') }}</span>
            <button class="toast-close" onclick="this.parentElement.classList.add('toast-out');setTimeout(()=>this.parentElement.remove(),300)">&times;</button>
        </div>
        @endif
        @if(session('warning'))
        <div class="toast toast-warning" role="alert">
            <i class="fas fa-exclamation-triangle toast-icon"></i>
            <span class="toast-message">{{ session('warning') }}</span>
            <button class="toast-close" onclick="this.parentElement.classList.add('toast-out');setTimeout(()=>this.parentElement.remove(),300)">&times;</button>
        </div>
        @endif
    </div>

    {{-- Sidebar toggle script --}}
    <script>
    (function(){
        var sidebar = document.querySelector('.sidebar');
        var overlay = document.getElementById('sidebarOverlay');
        var toggleBtn = document.getElementById('sidebarToggle');
        var closeBtn = document.getElementById('sidebarClose');

        function openSidebar() {
            sidebar.classList.add('open');
            overlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
        function closeSidebar() {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
        }

        if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
        if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
        if (overlay) overlay.addEventListener('click', closeSidebar);

        // =======================================================
        // PERSISTENSI POSISI SCROLL SIDEBAR (MENCEGAH RESET KE ATAS)
        // =======================================================
        var scrollKey = 'sidebar_scroll_position';

        function restoreSidebarScroll() {
            try {
                if (!sidebar) return;
                var saved = sessionStorage.getItem(scrollKey);
                if (saved !== null) {
                    sidebar.scrollTop = parseInt(saved, 10);
                } else {
                    var activeItem = sidebar.querySelector('a.active, .active');
                    if (activeItem) {
                        activeItem.scrollIntoView({ block: 'nearest' });
                    }
                }
            } catch (e) {}
        }

        if (sidebar) {
            // Catat posisi scroll saat pengguna menggeser sidebar
            var scrollTimer;
            sidebar.addEventListener('scroll', function() {
                clearTimeout(scrollTimer);
                scrollTimer = setTimeout(function() {
                    try {
                        sessionStorage.setItem(scrollKey, sidebar.scrollTop);
                    } catch (e) {}
                }, 30);
            }, { passive: true });

            // Simpan segera saat salah satu link di sidebar diklik
            sidebar.querySelectorAll('a').forEach(function(link) {
                link.addEventListener('click', function() {
                    try {
                        sessionStorage.setItem(scrollKey, sidebar.scrollTop);
                    } catch (e) {}
                });
            });

            // Simpan posisi saat halaman hendak berpindah (refresh / navigasi)
            window.addEventListener('beforeunload', function() {
                try {
                    sessionStorage.setItem(scrollKey, sidebar.scrollTop);
                } catch (e) {}
            });

            // Pulihkan posisi scroll saat dokumen siap & setelah load
            restoreSidebarScroll();
            window.addEventListener('load', restoreSidebarScroll);
        }

        // Auto-dismiss toasts after 5 seconds
        document.querySelectorAll('.toast').forEach(function(toast) {
            setTimeout(function() {
                toast.classList.add('toast-out');
                setTimeout(function() { toast.remove(); }, 300);
            }, 5000);
        });
    })();
    </script>


    <script src="{{ asset('js/table-pagination.js') }}"></script>
    @stack('scripts')
</body>
</html>


