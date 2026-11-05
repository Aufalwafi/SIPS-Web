# Roadmap Pengembangan SIPSONLINE

Berdasarkan review workspace pada direktori `c:\xampp\htdocs\SIPSONLINE`, berikut adalah hasil analisis dan roadmap langkah selanjutnya untuk menyelesaikan aplikasi ini.

## Hasil Review Workspace

1. **Stack Teknologi**: Proyek ini menggunakan **Laravel 12** dengan PHP 8.2+, dan terintegrasi dengan **Tailwind CSS v4** serta **Vite** untuk frontend.
2. **Struktur Routing**: File `routes/web.php` sudah memiliki kerangka yang sangat baik dan terstruktur dengan middleware autentikasi dan otorisasi berbasis peran (role).
3. **Peran (Roles)**: Terdapat 3 peran pengguna utama yang diakomodasi dalam rute:
   - **Admin**: Memiliki akses penuh untuk verifikasi RKPS/RKT, manajemen data kelompok, pendamping, anggota, user, laporan, dan pemantauan (monitoring).
   - **Pendamping**: Dapat melihat kelompok yang didampingi, data anggota, serta daftar pengajuan RKPS & RKT.
   - **Kelompok**: Dapat mengelola data anggotanya sendiri dan melakukan pengajuan RKPS & RKT.
4. **Konteks Aplikasi**: Dilihat dari penamaan seperti RKPS (Rencana Kerja Perhutanan Sosial), RKT (Rencana Kerja Tahunan), Kelompok, dan Pendamping, ini adalah sistem informasi terkait kehutanan / perhutanan sosial (Sistem Informasi Perhutanan Sosial Online).

---

> [!IMPORTANT]
> **User Review Required**
> Mohon periksa roadmap di bawah ini. Apakah prioritas tahapan pengembangan sudah sesuai dengan yang Anda harapkan? Jika ada fitur yang terlewat (misalnya integrasi peta/GIS yang spesifik), mohon beritahu saya agar bisa disesuaikan.

## Open Questions

1. **Database & Migrations**: Apakah skema database (tabel kelompok, pendamping, rkps, rkt, dll) sudah dibuat file migration-nya, atau kita harus mendesain dan membuatnya dari awal?
2. **Desain UI/UX**: Apakah ada referensi template admin panel tertentu yang ingin digunakan, atau kita akan membangun desain kustom yang elegan dengan Tailwind CSS v4 dari awal?

---

## Proposed Roadmap (Langkah Selanjutnya)

### Fase 1: Setup Basis Data dan Autentikasi
Fase ini fokus pada persiapan struktur data dasar dan memastikan login sesuai role berjalan dengan baik.
- [ ] Mendesain dan membuat skema tabel (Migrations) untuk `users`, `kelompok`, `pendamping`, `anggota`, `rkps`, `rkt`, dll.
- [ ] Membuat Models dan relasi antar tabel (Eloquent Relationships).
- [ ] Membuat Seeders untuk akun default (Admin, dummy Pendamping, dummy Kelompok).
- [ ] Menyempurnakan logika `LoginController` dan middleware `role` agar pengguna diarahkan ke dashboard masing-masing sesuai hak aksesnya.

### Fase 2: Master Layout & Landing Page
Membuat antarmuka dasar yang akan digunakan di seluruh aplikasi.
- [ ] Membuat layout utama menggunakan Tailwind CSS (Navbar, Sidebar, Footer) yang responsif dan modern.
- [ ] Membangun halaman Landing Page (`/`) dan integrasi Peta Publik (`/peta/publik`).
- [ ] Membangun halaman Login yang menarik secara visual.

### Fase 3: Modul Admin (Inti Aplikasi)
Menyelesaikan logika dan antarmuka untuk Admin berdasarkan rute yang sudah ada.
- [ ] Membangun Dashboard Admin dengan ringkasan statistik (jumlah kelompok, status pengajuan RKPS/RKT).
- [ ] Membuat fitur CRUD Data Master (User, Pendamping, Kelompok, Anggota).
- [ ] Membuat fitur Verifikasi Dokumen Pengajuan (RKPS dan RKT).
- [ ] Mengembangkan Peta Kelompok Admin (Integrasi dengan library map seperti Leaflet atau Mapbox).
- [ ] Membuat modul Laporan dan Monitoring (cetak rekap, laporan aktivitas).

### Fase 4: Modul Kelompok & Pendamping
Memberikan akses kepada pengguna akhir untuk berinteraksi dengan sistem.
- [ ] **Kelompok**: Membuat fitur form pengajuan RKPS & RKT lengkap dengan unggah dokumen, serta manajemen anggota kelompok.
- [ ] **Pendamping**: Membuat fitur melihat detail kelompok yang didampingi dan memonitor proses pengajuan dokumen (RKPS/RKT) dari kelompok tersebut.

### Fase 5: Finishing & Testing
- [ ] Validasi form secara menyeluruh (Backend & Frontend).
- [ ] Optimalisasi query database (N+1 query checks).
- [ ] Menambahkan notifikasi interaktif (Flash messages / Toast) untuk setiap aksi (sukses, error).
- [ ] Pengujian manual pada seluruh alur aplikasi dan perbaikan bug.

## Verification Plan

### Manual Verification
- Melakukan login sebagai 3 peran berbeda (Admin, Pendamping, Kelompok) untuk memastikan middleware otorisasi memblokir akses ke rute yang tidak berhak.
- Melakukan simulasi alur lengkap: Kelompok membuat pengajuan RKPS -> Admin/Pendamping melihat pengajuan -> Admin memverifikasi -> Status pengajuan berubah pada dashboard Kelompok.
- Memastikan tampilan UI responsif pada perangkat desktop dan mobile (Mobile First dengan Tailwind).
