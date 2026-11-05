{{--
DOKUMENTASI STANDAR KOMPONEN UI SIPSONLINE
File ini tidak untuk di-include, hanya sebagai acuan struktur HTML dan class CSS (app.css).

1. STRUKTUR LAYOUT UTAMA
<body>
    @include('layouts.sidebar')
    <div class="main">
        @include('layouts.header')
        <main class="content">
            <!-- Konten halaman -->
        </main>
    </div>
</body>

2. STRUKTUR SIDEBAR
<aside class="sidebar">
    <div class="brand">SI PS ONLINE</div>
    <nav class="sidebar-nav">
        <a href="..." class="active"><i class="fas fa-home"></i> <span>Dashboard</span></a>
        <div class="sidebar-category">Kategori</div>
        ...
    </nav>
</aside>

3. STRUKTUR HEADER
<div class="header">
    <div class="header-left">
        <img src="{{ url('/assets/logo-jatim.png') }}" style="height:40px">
        <div class="instansi">
            <b>Nama Instansi</b><br>
            <span style="font-size:11px;opacity:.85">Wilayah</span>
        </div>
    </div>
    <div class="header-right">
        <!-- Notifikasi -->
        <div class="notification-wrapper">
            <button class="notification-icon" id="notifBtn" type="button">
                <i class="fas fa-bell"></i>
                <span class="notification-badge">2</span>
            </button>
            <div class="notification-dropdown" id="notifDropdown">
                <div class="notif-header">
                    <span>Notifikasi Berkas</span>
                    <span>2 Baru</span>
                </div>
                <div class="notif-body">
                    <a href="..." class="notif-item">
                        <span>Pengajuan RKPS Menunggu</span>
                        <b>1</b>
                    </a>
                </div>
            </div>
        </div>
        
        <span class="hi-user">Hi, <b>Nama User</b></span>
        <span class="divider">|</span>
        <form method="POST" action="{{ route('logout') }}" style="display:inline">
            @csrf
            <button type="submit" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</button>
        </form>
    </div>
</div>

4. KARTU STATISTIK
<div class="card-grid">
    <div class="stat-card">
        <div class="info">
            <h3>Judul Statistik</h3>
            <div class="number value">0</div>
            <div class="subtext">Keterangan Tambahan</div>
        </div>
        <div class="icon-box"><i class="fas fa-users"></i></div>
    </div>
</div>

5. TABEL DAN TABLE-WRAP
<div class="table-box">
    <h3><i class="fas fa-table"></i> Judul Tabel</h3>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Kolom 1</th>
                    <th>Kolom 2</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Data 1</td>
                    <td>Data 2</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

6. TOMBOL
<a href="#" class="btn btn-add"><i class="fas fa-plus"></i> Tambah Data</a>
<a href="#" class="btn btn-view"><i class="fas fa-eye"></i> Detail</a>
<button class="btn btn-success"><i class="fas fa-save"></i> Simpan</button>
<button class="btn btn-warning"><i class="fas fa-edit"></i> Edit</button>
<button class="btn btn-danger"><i class="fas fa-trash"></i> Hapus</button>

7. FORM GROUP
<form action="...">
    <div class="form-group">
        <label>Nama Lengkap</label>
        <input type="text" class="form-control" name="nama" required>
    </div>
</form>

8. ALERT MESSAGES
<div class="alert success">
    <i class="fas fa-check-circle"></i> Data berhasil disimpan.
</div>
<div class="alert error">
    <i class="fas fa-exclamation-circle"></i> Terjadi kesalahan.
</div>

9. STATUS PENGAJUAN (BADGE)
<span class="status-badge status-Menunggu">Menunggu</span>
<span class="status-badge status-Disetujui">Disetujui</span>
<span class="status-badge status-Revisi">Revisi</span>
<span class="status-badge status-Ditolak">Ditolak</span>

10. BADGE ROLE
<span class="badge role-admin">Admin</span>
<span class="badge role-pendamping">Pendamping</span>
<span class="badge role-kelompok">Kelompok</span>
--}}
