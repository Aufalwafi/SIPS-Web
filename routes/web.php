<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\KelompokController as AdminKelompokController;
use App\Http\Controllers\Admin\PendampingController as AdminPendampingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\MonitoringController as AdminMonitoringController;
use App\Http\Controllers\Admin\RkpsRktController as AdminRkpsRktController;
use App\Http\Controllers\Admin\RecycleBinController as AdminRecycleBinController;
use App\Http\Controllers\Admin\GoogleDriveSyncController as AdminGoogleDriveSyncController;
use App\Http\Controllers\AdminCdkController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\PendampingController;
use App\Http\Controllers\Kelompok\DashboardController as KelompokDashboardController;
use App\Http\Controllers\Kelompok\AnggotaController as KelompokAnggotaController;
use App\Http\Controllers\Kelompok\PengajuanController as KelompokPengajuanController;
use App\Http\Controllers\Kelompok\KupsController as KelompokKupsController;
use App\Http\Controllers\Kelompok\KegiatanController as KelompokKegiatanController;
use App\Http\Controllers\Kelompok\PotensiPermasalahanController as KelompokPotensiPermasalahanController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.process');

Route::post('/logout', [LogoutController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

Route::get('/peta/publik', function () {
    $kelompok = DB::table('kelompok')
        ->orderBy('nama_kelompok')
        ->get(['id', 'nama_kelompok', 'ketua', 'desa', 'kecamatan', 'skema_ps', 'polygon_kelompok']);

    return view('peta.publik', compact('kelompok'));
})->name('peta.publik');

// Akses Dokumen SK PS & SK Pembentukan Kelompok
Route::get('/dokumen/sk-ps/{id}', [\App\Http\Controllers\DokumenController::class, 'unduhSkPs'])->name('dokumen.sk-ps');
Route::get('/dokumen/sk-kelompok/{id}', [\App\Http\Controllers\DokumenController::class, 'unduhSkKelompok'])->name('dokumen.sk-kelompok');

// AJAX Data Petak Perhutani untuk Kelompok (Admin / Admin CDK / Pendamping / Publik)
Route::get('/kelompok/{id}/petak-perhutani', [\App\Http\Controllers\Admin\KelompokController::class, 'getPetakPerhutaniAjax'])->name('kelompok.petak-perhutani');
Route::get('/api/petak-perhutani/bbox', [\App\Http\Controllers\Admin\KelompokController::class, 'getPetakPerhutaniBboxAjax'])->name('petak-perhutani.bbox');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])->name('dashboard');

        // Data Kelompok CRUD
        Route::get('/data-kelompok', [AdminKelompokController::class, 'dataKelompok'])->name('data-kelompok');
        Route::get('/data-kelompok/tambah', [AdminKelompokController::class, 'tambahKelompok'])->name('data-kelompok.tambah');
        Route::post('/data-kelompok', [AdminKelompokController::class, 'storeKelompok'])->name('data-kelompok.simpan');
        Route::get('/data-kelompok/{id}/detail', [AdminKelompokController::class, 'detailKelompok'])->name('data-kelompok.detail');
        Route::get('/data-kelompok/{id}/edit', [AdminKelompokController::class, 'editKelompok'])->name('data-kelompok.edit');
        Route::put('/data-kelompok/{id}', [AdminKelompokController::class, 'updateKelompok'])->name('data-kelompok.update');
        Route::delete('/data-kelompok/{id}', [AdminKelompokController::class, 'hapusKelompok'])->name('data-kelompok.hapus');
        Route::get('/peta-kelompok', [AdminKelompokController::class, 'petaKelompok'])->name('peta-kelompok');

        // Data Pendamping CRUD
        Route::get('/data-pendamping', [AdminPendampingController::class, 'dataPendamping'])->name('data-pendamping');
        Route::get('/data-pendamping/tambah', [AdminPendampingController::class, 'tambahPendamping'])->name('data-pendamping.tambah');
        Route::post('/data-pendamping', [AdminPendampingController::class, 'simpanPendamping'])->name('data-pendamping.simpan');
        Route::post('/data-pendamping/lepas', [AdminPendampingController::class, 'lepasBinaanPendamping'])->name('data-pendamping.lepas');
        Route::get('/data-pendamping/{id}/detail', [AdminPendampingController::class, 'detailPendamping'])->name('data-pendamping.detail');
        Route::post('/data-pendamping/{id}/tambah-binaan', [AdminPendampingController::class, 'tambahBinaanPendamping'])->name('data-pendamping.tambah-binaan');
        Route::get('/data-pendamping/{id}/edit', [AdminPendampingController::class, 'editPendamping'])->name('data-pendamping.edit');
        Route::put('/data-pendamping/{id}', [AdminPendampingController::class, 'updatePendamping'])->name('data-pendamping.update');
        Route::delete('/data-pendamping/{id}', [AdminPendampingController::class, 'hapusPendamping'])->name('data-pendamping.hapus');

        // Data Anggota
        Route::get('/data-anggota', [AdminKelompokController::class, 'dataAnggota'])->name('data-anggota');

        // Verifikasi RKPS
        Route::get('/verifikasi-rkps', [AdminMonitoringController::class, 'verifikasiRkps'])->name('verifikasi-rkps');
        Route::post('/verifikasi-rkps', [AdminMonitoringController::class, 'prosesVerifikasiRkps'])->name('verifikasi-rkps.proses');

        // Verifikasi RKT
        Route::get('/verifikasi-rkt', [AdminMonitoringController::class, 'verifikasiRkt'])->name('verifikasi-rkt');
        Route::post('/verifikasi-rkt', [AdminMonitoringController::class, 'prosesVerifikasiRkt'])->name('verifikasi-rkt.proses');

        // Cetak
        Route::get('/cetak-kelompok-kosong', [AdminKelompokController::class, 'cetakKelompokKosong'])->name('cetak-kelompok-kosong');
        Route::get('/cetak-rekap-pendamping', [AdminPendampingController::class, 'cetakRekapPendamping'])->name('cetak-rekap-pendamping');

        // Monitoring
        Route::get('/monitoring-pengajuan', [AdminMonitoringController::class, 'monitoringPengajuan'])->name('monitoring-pengajuan');
        Route::get('/monitoring-ps', [AdminMonitoringController::class, 'monitoringPs'])->name('monitoring-ps');
        Route::get('/monitoring-nte', [AdminMonitoringController::class, 'monitoringNte'])->name('monitoring-nte');

        // Data Laporan
        Route::get('/data-laporan', [AdminMonitoringController::class, 'dataLaporan'])->name('data-laporan');

        // Data User CRUD
        Route::get('/data-user', [AdminUserController::class, 'dataUser'])->name('data-user');
        Route::get('/data-user/tambah', [AdminUserController::class, 'createUser'])->name('data-user.create');
        Route::post('/data-user', [AdminUserController::class, 'storeUser'])->name('data-user.store');
        Route::get('/data-user/{id}/edit', [AdminUserController::class, 'editUser'])->name('data-user.edit');
        Route::put('/data-user/{id}', [AdminUserController::class, 'updateUser'])->name('data-user.update');
        Route::post('/data-user/{id}/reset-password', [AdminUserController::class, 'resetUserPassword'])->name('data-user.reset-password');
        Route::delete('/data-user/{id}', [AdminUserController::class, 'deleteUser'])->name('data-user.delete');

        // Aktivitas User
        Route::get('/aktivitas-user', [AdminUserController::class, 'aktivitasUser'])->name('aktivitas-user');
        Route::get('/aktivitas-user/export', [AdminUserController::class, 'exportAktivitasUser'])->name('aktivitas-user.export');

        // Data RKPS
        Route::get('/data-rkps', [AdminRkpsRktController::class, 'dataRkps'])->name('data-rkps');
        Route::get('/data-rkps/tambah', [AdminRkpsRktController::class, 'createRkps'])->name('data-rkps.create');
        Route::post('/data-rkps', [AdminRkpsRktController::class, 'storeRkps'])->name('data-rkps.store');
        Route::get('/data-rkps/{id}/edit', [AdminRkpsRktController::class, 'editRkps'])->name('data-rkps.edit');
        Route::put('/data-rkps/{id}', [AdminRkpsRktController::class, 'updateRkps'])->name('data-rkps.update');
        Route::delete('/data-rkps/{id}', [AdminRkpsRktController::class, 'deleteRkps'])->name('data-rkps.delete');

        // Data RKT
        Route::get('/data-rkt', [AdminRkpsRktController::class, 'dataRkt'])->name('data-rkt');
        Route::get('/data-rkt/tambah', [AdminRkpsRktController::class, 'createRkt'])->name('data-rkt.create');
        Route::post('/data-rkt', [AdminRkpsRktController::class, 'storeRkt'])->name('data-rkt.store');
        Route::get('/data-rkt/{id}/edit', [AdminRkpsRktController::class, 'editRkt'])->name('data-rkt.edit');
        Route::put('/data-rkt/{id}', [AdminRkpsRktController::class, 'updateRkt'])->name('data-rkt.update');
        Route::delete('/data-rkt/{id}', [AdminRkpsRktController::class, 'deleteRkt'])->name('data-rkt.delete');

        // Admin CDK CRUD
        Route::get('/admin-cdk', [AdminCdkController::class, 'index'])->name('admin-cdk');
        Route::post('/admin-cdk', [AdminCdkController::class, 'store'])->name('admin-cdk.store');
        Route::get('/admin-cdk/{id}/edit', [AdminCdkController::class, 'edit'])->name('admin-cdk.edit');
        Route::put('/admin-cdk/{id}', [AdminCdkController::class, 'update'])->name('admin-cdk.update');
        Route::delete('/admin-cdk/{id}', [AdminCdkController::class, 'destroy'])->name('admin-cdk.destroy');

        // Recycle Bin / Pemulihan Data
        Route::get('/recycle-bin', [AdminRecycleBinController::class, 'index'])->name('recycle-bin');
        Route::post('/recycle-bin/{type}/{id}/restore', [AdminRecycleBinController::class, 'restore'])->name('recycle-bin.restore');
        Route::delete('/recycle-bin/{type}/{id}/force-delete', [AdminRecycleBinController::class, 'forceDelete'])->name('recycle-bin.force-delete');

        // Google Drive Sync
        Route::get('/google-drive-sync', [AdminGoogleDriveSyncController::class, 'index'])->name('google-drive-sync');
        Route::get('/google-drive-sync/items', [AdminGoogleDriveSyncController::class, 'getItems'])->name('google-drive-sync.items');
        Route::post('/google-drive-sync/process-item', [AdminGoogleDriveSyncController::class, 'processItem'])->name('google-drive-sync.process-item');
    });

Route::middleware(['auth', 'role:admin_cdk'])
    ->prefix('admin-cdk')
    ->name('admin-cdk.')
    ->group(function () {
        Route::get('/dashboard', [AdminCdkController::class, 'dashboard'])->name('dashboard');
        Route::get('/peta-kelompok', [AdminCdkController::class, 'petaKelompok'])->name('peta-kelompok');
        Route::get('/data-pendamping', [AdminCdkController::class, 'dataPendamping'])->name('data-pendamping');
        Route::get('/data-pendamping/{id}/detail', [AdminCdkController::class, 'detailPendamping'])->name('data-pendamping.detail');
        Route::post('/data-pendamping/{id}/tambah-binaan', [AdminCdkController::class, 'tambahBinaanPendamping'])->name('data-pendamping.tambah-binaan');
        Route::post('/data-pendamping/lepas', [AdminCdkController::class, 'lepasBinaanPendamping'])->name('data-pendamping.lepas');
        Route::get('/data-kelompok', [AdminCdkController::class, 'dataKelompok'])->name('data-kelompok');
        Route::get('/data-kelompok/{id}/detail', [AdminCdkController::class, 'detailKelompok'])->name('data-kelompok.detail');
        Route::get('/data-kelompok/{id}/edit', [AdminCdkController::class, 'editKelompok'])->name('data-kelompok.edit');
        Route::put('/data-kelompok/{id}', [AdminCdkController::class, 'updateKelompok'])->name('data-kelompok.update');
        Route::get('/data-anggota', [AdminCdkController::class, 'dataAnggota'])->name('data-anggota');
        Route::get('/verifikasi-rkps', [AdminCdkController::class, 'verifikasiRkps'])->name('verifikasi-rkps');
        Route::post('/verifikasi-rkps', [AdminCdkController::class, 'prosesVerifikasiRkps'])->name('verifikasi-rkps.proses');
        Route::get('/verifikasi-rkt', [AdminCdkController::class, 'verifikasiRkt'])->name('verifikasi-rkt');
        Route::post('/verifikasi-rkt', [AdminCdkController::class, 'prosesVerifikasiRkt'])->name('verifikasi-rkt.proses');
        Route::get('/data-laporan', [AdminCdkController::class, 'dataLaporan'])->name('data-laporan');
        Route::get('/rekap-wilayah', [AdminCdkController::class, 'rekapWilayah'])->name('rekap-wilayah');
        Route::get('/monitoring-nte', [AdminCdkController::class, 'monitoringNte'])->name('monitoring-nte');
    });


Route::middleware(['auth', 'role:pendamping'])
    ->prefix('pendamping')
    ->name('pendamping.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [PendampingController::class, 'dashboard'])->name('dashboard');

        Route::get('/peta-kelompok', [PendampingController::class, 'petaKelompok'])->name('peta-kelompok');
        Route::get('/data-nte', [PendampingController::class, 'dataNteSemua'])->name('data-nte');

        // Kelompok Saya
        Route::get('/kelompok-saya', [PendampingController::class, 'kelompokSaya'])->name('kelompok-saya');
        // Kelompok Saya — per kelompok
        Route::get('/kelompok-saya/{id}/detail', [PendampingController::class, 'detailKelompok'])->whereNumber('id')->name('kelompok-saya.detail');

        // Kelompok Binaan — fitur per-kelompok
        Route::get('/kelompok-saya/{id}/anggota', [PendampingController::class, 'anggotaPerKelompok'])->whereNumber('id')->name('kelompok-saya.anggota');
        Route::get('/kelompok-saya/{id}/anggota/tambah', [PendampingController::class, 'tambahAnggotaPerKelompok'])->whereNumber('id')->name('kelompok-saya.anggota.tambah');
        Route::post('/kelompok-saya/{id}/anggota', [PendampingController::class, 'simpanAnggotaPerKelompok'])->whereNumber('id')->name('kelompok-saya.anggota.simpan');
        Route::delete('/kelompok-saya/{id}/anggota/{idAnggota}', [PendampingController::class, 'hapusAnggotaPerKelompok'])->whereNumber('id')->whereNumber('idAnggota')->name('kelompok-saya.anggota.hapus');
        Route::get('/kelompok-saya/{id}/data-nte', [PendampingController::class, 'dataNte'])->name('kelompok-saya.data-nte');
        Route::get('/kelompok-saya/{id}/input-nte', [PendampingController::class, 'tambahNte'])->name('kelompok-saya.input-nte');
        Route::post('/kelompok-saya/{id}/input-nte', [PendampingController::class, 'simpanNte'])->name('kelompok-saya.simpan-nte');
        Route::get('/kelompok-saya/{id}/transaksi/{idNte}', [PendampingController::class, 'inputTransaksi'])->name('kelompok-saya.transaksi');
        Route::post('/kelompok-saya/{id}/transaksi/{idNte}', [PendampingController::class, 'simpanTransaksi'])->name('kelompok-saya.simpan-transaksi');
        Route::get('/kelompok-saya/{id}/laporan', [PendampingController::class, 'laporanPerKelompok'])->name('kelompok-saya.laporan');
        Route::get('/kelompok-saya/{id}/laporan/tambah', [PendampingController::class, 'tambahLaporanPerKelompok'])->name('kelompok-saya.laporan.tambah');
        Route::post('/kelompok-saya/{id}/laporan', [PendampingController::class, 'simpanLaporanPerKelompok'])->name('kelompok-saya.laporan.simpan');

        // Laporan kegiatan per kelompok (URL yang lebih deskriptif; route lama dipertahankan)
        Route::get('/kelompok-saya/{id}/laporan-kegiatan', [PendampingController::class, 'laporanPerKelompok'])->whereNumber('id')->name('kelompok-saya.laporan-kegiatan');
        Route::get('/kelompok-saya/{id}/laporan-kegiatan/tambah', [PendampingController::class, 'tambahLaporanPerKelompok'])->whereNumber('id')->name('kelompok-saya.laporan-kegiatan.tambah');
        Route::post('/kelompok-saya/{id}/laporan-kegiatan', [PendampingController::class, 'simpanLaporanPerKelompok'])->whereNumber('id')->name('kelompok-saya.laporan-kegiatan.simpan');

        // Anggota
        Route::get('/anggota', [PendampingController::class, 'anggota'])->name('anggota');

        // Pengajuan RKPS
        Route::get('/pengajuan-rkps', [PendampingController::class, 'pengajuanRkps'])->name('pengajuan-rkps');

        // Pengajuan RKT
        Route::get('/pengajuan-rkt', [PendampingController::class, 'pengajuanRkt'])->name('pengajuan-rkt');

        // Laporan Kegiatan CRUD
        Route::get('/laporan-kegiatan', [PendampingController::class, 'laporanKegiatan'])->name('laporan-kegiatan');
        Route::get('/laporan-kegiatan/tambah', [PendampingController::class, 'tambahLaporan'])->name('laporan-kegiatan.tambah');
        Route::post('/laporan-kegiatan', [PendampingController::class, 'simpanLaporan'])->name('laporan-kegiatan.simpan');
        Route::get('/laporan-kegiatan/{id}', [PendampingController::class, 'detailLaporan'])->name('laporan-kegiatan.detail');
        Route::get('/laporan-kegiatan/{id}/edit', [PendampingController::class, 'editLaporan'])->name('laporan-kegiatan.edit');
        Route::put('/laporan-kegiatan/{id}', [PendampingController::class, 'updateLaporan'])->name('laporan-kegiatan.update');
        Route::delete('/laporan-kegiatan/{id}', [PendampingController::class, 'hapusLaporan'])->name('laporan-kegiatan.hapus');

        // Akses Masuk / Keluar Portal Kelompok Dampingan
        Route::get('/kelompok-saya/{id}/masuk', [PendampingController::class, 'masukKelompok'])->whereNumber('id')->name('kelompok-saya.masuk');
        Route::get('/keluar-kelompok', [PendampingController::class, 'keluarKelompok'])->name('keluar-kelompok');
    });

/*
|--------------------------------------------------------------------------
| Kelompok Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:kelompok,pendamping'])
    ->prefix('kelompok')
    ->name('kelompok.')
    ->group(function () {
        // Dashboard & Profil
        Route::get('/dashboard', [KelompokDashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/data-kelompok', [KelompokDashboardController::class, 'dataKelompok'])->name('data-kelompok');

        // Peta Wilayah & Spasial
        Route::get('/peta', [KelompokDashboardController::class, 'peta'])->name('peta');
        Route::get('/get-petak', [KelompokDashboardController::class, 'getPetakKelompokAjax'])->name('get-petak');

        // Anggota CRUD
        Route::get('/anggota', [KelompokAnggotaController::class, 'dataAnggota'])->name('anggota');
        Route::get('/anggota/tambah', [KelompokAnggotaController::class, 'tambahAnggota'])->name('anggota.tambah');
        Route::post('/anggota', [KelompokAnggotaController::class, 'simpanAnggota'])->name('anggota.simpan');
        Route::get('/anggota/{id}/edit', [KelompokAnggotaController::class, 'editAnggota'])->name('anggota.edit');
        Route::put('/anggota/{id}', [KelompokAnggotaController::class, 'updateAnggota'])->name('anggota.update');
        Route::delete('/anggota/{id}', [KelompokAnggotaController::class, 'hapusAnggota'])->name('anggota.hapus');

        // Pengajuan RKPS
        Route::get('/pengajuan-rkps', [KelompokPengajuanController::class, 'pengajuanRkps'])->name('pengajuan-rkps');
        Route::get('/pengajuan-rkps/ajukan', [KelompokPengajuanController::class, 'ajukanRkps'])->name('pengajuan-rkps.ajukan');
        Route::post('/pengajuan-rkps', [KelompokPengajuanController::class, 'simpanAjukanRkps'])->name('pengajuan-rkps.simpan');
        Route::get('/pengajuan-rkps/{id}', [KelompokPengajuanController::class, 'detailPengajuanRkps'])->name('pengajuan-rkps.detail');
        Route::get('/pengajuan-rkps/{id}/ajukan-ulang', [KelompokPengajuanController::class, 'ajukanUlangRkps'])->name('pengajuan-rkps.ajukan-ulang');
        Route::post('/pengajuan-rkps/{id}/ajukan-ulang', [KelompokPengajuanController::class, 'simpanAjukanUlangRkps'])->name('pengajuan-rkps.simpan-ulang');

        // Pengajuan RKT
        Route::get('/pengajuan-rkt', [KelompokPengajuanController::class, 'pengajuanRkt'])->name('pengajuan-rkt');
        Route::get('/pengajuan-rkt/ajukan', [KelompokPengajuanController::class, 'ajukanRkt'])->name('pengajuan-rkt.ajukan');
        Route::post('/pengajuan-rkt', [KelompokPengajuanController::class, 'simpanAjukanRkt'])->name('pengajuan-rkt.simpan');
        Route::get('/pengajuan-rkt/{id}', [KelompokPengajuanController::class, 'detailPengajuanRkt'])->name('pengajuan-rkt.detail');
        Route::get('/pengajuan-rkt/{id}/ajukan-ulang', [KelompokPengajuanController::class, 'ajukanUlangRkt'])->name('pengajuan-rkt.ajukan-ulang');
        Route::post('/pengajuan-rkt/{id}/ajukan-ulang', [KelompokPengajuanController::class, 'simpanAjukanUlangRkt'])->name('pengajuan-rkt.simpan-ulang');

        // KUPS CRUD
        Route::get('/kups', [KelompokKupsController::class, 'kups'])->name('kups');
        Route::get('/kups/tambah', [KelompokKupsController::class, 'tambahKups'])->name('kups.tambah');
        Route::post('/kups', [KelompokKupsController::class, 'simpanKups'])->name('kups.simpan');
        Route::get('/kups/{id}/edit', [KelompokKupsController::class, 'editKups'])->name('kups.edit');
        Route::put('/kups/{id}', [KelompokKupsController::class, 'updateKups'])->name('kups.update');
        Route::delete('/kups/{id}', [KelompokKupsController::class, 'hapusKups'])->name('kups.hapus');

        // Kegiatan CRUD
        Route::get('/kegiatan', [KelompokKegiatanController::class, 'kegiatan'])->name('kegiatan');
        Route::get('/kegiatan/tambah', [KelompokKegiatanController::class, 'tambahKegiatan'])->name('kegiatan.tambah');
        Route::post('/kegiatan', [KelompokKegiatanController::class, 'simpanKegiatan'])->name('kegiatan.simpan');
        Route::get('/kegiatan/{id}', [KelompokKegiatanController::class, 'detailKegiatan'])->name('kegiatan.detail');
        Route::get('/kegiatan/{id}/peta', [KelompokKegiatanController::class, 'petaKegiatan'])->whereNumber('id')->name('kegiatan.peta');
        Route::get('/kegiatan/{id}/edit', [KelompokKegiatanController::class, 'editKegiatan'])->name('kegiatan.edit');
        Route::put('/kegiatan/{id}', [KelompokKegiatanController::class, 'updateKegiatan'])->name('kegiatan.update');
        Route::delete('/kegiatan/{id}', [KelompokKegiatanController::class, 'hapusKegiatan'])->name('kegiatan.hapus');
        Route::post('/kegiatan/{id}/dokumentasi', [KelompokKegiatanController::class, 'simpanDokumentasi'])->name('kegiatan.dokumentasi.simpan');
        Route::delete('/kegiatan/dokumentasi/{id}', [KelompokKegiatanController::class, 'hapusDokumentasi'])->name('kegiatan.dokumentasi.hapus');
        Route::post('/kegiatan/dokumentasi/{id}/sync', [KelompokKegiatanController::class, 'syncDriveDokumentasi'])->name('kegiatan.dokumentasi.sync');

        // Potensi CRUD
        Route::get('/potensi', [KelompokPotensiPermasalahanController::class, 'potensi'])->name('potensi');
        Route::get('/potensi/tambah', [KelompokPotensiPermasalahanController::class, 'tambahPotensi'])->name('potensi.tambah');
        Route::post('/potensi', [KelompokPotensiPermasalahanController::class, 'simpanPotensi'])->name('potensi.simpan');
        Route::get('/potensi/{id}/edit', [KelompokPotensiPermasalahanController::class, 'editPotensi'])->name('potensi.edit');
        Route::put('/potensi/{id}', [KelompokPotensiPermasalahanController::class, 'updatePotensi'])->name('potensi.update');
        Route::delete('/potensi/{id}', [KelompokPotensiPermasalahanController::class, 'hapusPotensi'])->name('potensi.hapus');

        // Permasalahan CRUD
        Route::get('/permasalahan', [KelompokPotensiPermasalahanController::class, 'permasalahan'])->name('permasalahan');
        Route::get('/permasalahan/tambah', [KelompokPotensiPermasalahanController::class, 'tambahPermasalahan'])->name('permasalahan.tambah');
        Route::post('/permasalahan', [KelompokPotensiPermasalahanController::class, 'simpanPermasalahan'])->name('permasalahan.simpan');
        Route::get('/permasalahan/{id}/edit', [KelompokPotensiPermasalahanController::class, 'editPermasalahan'])->name('permasalahan.edit');
        Route::put('/permasalahan/{id}', [KelompokPotensiPermasalahanController::class, 'updatePermasalahan'])->name('permasalahan.update');
        Route::delete('/permasalahan/{id}', [KelompokPotensiPermasalahanController::class, 'hapusPermasalahan'])->name('permasalahan.hapus');
    });