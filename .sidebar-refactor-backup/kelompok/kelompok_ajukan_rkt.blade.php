<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajukan RKT - SI PS ONLINE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f4f6f9; display: flex; min-height: 100vh; }
        .sidebar { width: 260px; min-height: 100vh; position: fixed; top: 0; left: 0; bottom: 0; overflow-y: auto; background: #1b5e20; color: #fff; z-index: 100; }
        .sidebar h2 { margin: 0; padding: 20px 10px; background: #144e18; border-bottom: 1px solid rgba(255,255,255,.2); text-align: center; font-size: 18px; }
        .sidebar-category { padding: 14px 15px 4px; color: #a5d6a7; font-size: 10px; font-weight: 700; letter-spacing: .8px; text-transform: uppercase; }
        .sidebar a { display: flex; align-items: center; gap: 8px; padding: 10px 15px; color: #fff; text-decoration: none; font-size: 14px; position: relative; transition: .2s; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,.2); }
        .sidebar a.active { font-weight: 700; }
        .sidebar a.active::after { content: ''; position: absolute; right: 15px; width: 6px; height: 6px; background: #fff; border-radius: 50%; box-shadow: 0 0 6px #fff; }
        .main { flex: 1; min-width: 0; margin-left: 260px; }
        .header { display: flex; align-items: center; justify-content: space-between; min-height: 64px; padding: 12px 25px; background: #2e7d32; color: #fff; }
        .header-left, .header-right { display: flex; align-items: center; }
        .header-left { gap: 10px; font-size: 13px; }
        .header-right { gap: 15px; font-size: 13px; }
        .header-right form { margin: 0; }
        .notification-wrapper { position: relative; }
        .notification-icon { position: relative; padding: 5px; cursor: pointer; font-size: 16px; }
        .notification-badge { position: absolute; top: -2px; right: -5px; min-width: 16px; padding: 2px 5px; background: #d32f2f; color: #fff; border-radius: 50%; font-size: 10px; font-weight: 700; text-align: center; }
        .notification-dropdown { display: none; position: absolute; top: 35px; right: 0; width: 280px; overflow: hidden; background: #fff; color: #333; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,.2); z-index: 1000; }
        .notification-dropdown.show { display: block; }
        .notif-header { display: flex; justify-content: space-between; padding: 10px 15px; background: #1b5e20; color: #fff; font-size: 13px; font-weight: 700; }
        .notif-item { display: flex; justify-content: space-between; padding: 10px 15px; border-bottom: 1px solid #eee; font-size: 12px; }
        .content { padding: 25px; }
        .box { max-width: 700px; padding: 25px; background: #fff; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,.1); }
        h2 { margin: 0 0 20px; color: #1b5e20; font-size: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; color: #333; font-size: 13px; font-weight: 600; }
        .form-group input { width: 100%; height: 36px; padding: 7px 10px; border: 1px solid #ccc; border-radius: 4px; }
        .help { margin-top: 4px; color: #777; font-size: 11px; }
        .btn { padding: 8px 14px; border: 0; border-radius: 4px; background: #2e7d32; color: #fff; cursor: pointer; font-size: 12px; }
        .alert { margin-bottom: 15px; padding: 10px; border-radius: 4px; background: #f8d7da; color: #721c24; font-size: 12px; }
        .error { margin-top: 4px; color: #c62828; font-size: 11px; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <h2>SI PS ONLINE</h2>
        <div style="padding: 10px 0;">
            <a href="{{ route('kelompok.dashboard') }}"><i class="fas fa-home" style="width:20px"></i> Dashboard</a>
            <div class="sidebar-category">Data Kelompok</div>
            <a href="{{ route('kelompok.data-kelompok') }}"><i class="fas fa-users" style="width:20px"></i> Data Kelompok Saya</a>
            <a href="{{ route('kelompok.anggota') }}"><i class="fas fa-user-friends" style="width:20px"></i> Anggota Kelompok</a>
            <div class="sidebar-category">Pengajuan Berkas</div>
            <a href="{{ route('kelompok.pengajuan-rkps') }}"><i class="fas fa-file-alt" style="width:20px"></i> Pengajuan RKPS</a>
            <a href="{{ route('kelompok.pengajuan-rkt') }}" class="active"><i class="fas fa-file-signature" style="width:20px"></i> Pengajuan RKT</a>
        </div>
    </aside>

    <main class="main">
        <header class="header">
            <div class="header-left"><img src="{{ url('/assets/logo-jatim.png') }}" style="height:40px"><div><b>Dinas Kehutanan Provinsi Jawa Timur</b><br>CDK Wilayah Kota Madiun</div></div>
            <div class="header-right">
                <div class="notification-wrapper">
                    <div class="notification-icon" id="notifBtn"><i class="fas fa-bell"></i>@if($totalNotif>0)<span class="notification-badge">{{ $totalNotif }}</span>@endif</div>
                    <div class="notification-dropdown" id="notifDropdown"><div class="notif-header"><span>Status Pengajuan</span><span>{{ $totalNotif }} Baru</span></div><div class="notif-item"><span>RKPS Menunggu</span><b>{{ $rkpsMenunggu }}</b></div><div class="notif-item"><span>RKT Menunggu</span><b>{{ $rktMenunggu }}</b></div></div>
                </div>
                <span>Hi, <b>{{ Auth::user()->nama }}</b></span><span>|</span>
                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" style="background:none;border:0;color:#fff;cursor:pointer;font-weight:bold">Logout</button></form>
            </div>
        </header>

        <div class="content">
            <div class="box">
                <h2>Ajukan RKT</h2>
                @if($errors->any())
                    <div class="alert"><ul style="margin:0;padding-left:18px">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                <form method="POST" action="{{ route('kelompok.pengajuan-rkt.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group"><label>Tahun</label><input type="number" name="tahun" min="1900" max="2200" value="{{ old('tahun') }}" required>@error('tahun')<div class="error">{{ $message }}</div>@enderror</div>
                    <div class="form-group"><label>No Penetapan (opsional)</label><input type="text" name="no_penetapan" value="{{ old('no_penetapan') }}"></div>
                    <div class="form-group"><label>Tanggal Penetapan (opsional)</label><input type="date" name="tgl_penetapan" value="{{ old('tgl_penetapan') }}"></div>
                    <div class="form-group"><label>Dokumen RKT</label><input type="file" name="file_rkt" accept="application/pdf" required><div class="help">Format PDF, maksimal 10 MB.</div>@error('file_rkt')<div class="error">{{ $message }}</div>@enderror</div>
                    <button type="submit" class="btn"><i class="fas fa-paper-plane"></i> Kirim Pengajuan</button>
                </form>
            </div>
        </div>
    </main>

    <script>
        const notifBtn = document.getElementById('notifBtn');
        const notifDropdown = document.getElementById('notifDropdown');
        if (notifBtn) {
            notifBtn.addEventListener('click', function(event) {
                event.stopPropagation();
                notifDropdown.classList.toggle('show');
            });
            window.addEventListener('click', function() {
                notifDropdown.classList.remove('show');
            });
        }
    </script>
</body>
</html>