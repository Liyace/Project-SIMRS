<?php

use App\Http\Controllers\AntrianController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KelolaController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes SIMRS
|--------------------------------------------------------------------------
*/

// ── Halaman Publik (Landing Page & Auth) ──
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/status-tanggal', [LandingController::class, 'statusTanggal'])->name('status.tanggal');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// ── Verifikasi 2FA Khusus Staf RS ──
Route::get('/login/2fa', [AuthController::class, 'showTwoFactorForm'])->name('login.2fa');
Route::post('/login/2fa', [AuthController::class, 'verifyTwoFactor'])->name('login.2fa.verify');
Route::get('/login/2fa/batal', [AuthController::class, 'cancelTwoFactor'])->name('login.2fa.cancel');

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/bantuan/permintaan-reset', [AuthController::class, 'kirimPermintaanReset'])->name('bantuan.permintaan-reset');

// ── Portal Dashboard Khusus Pasien ──
Route::get('/pasien/dashboard', [\App\Http\Controllers\PasienPortalController::class, 'index'])->name('pasien.dashboard');
Route::get('/pasien/kartu', [\App\Http\Controllers\PasienPortalController::class, 'cetakKartu'])->name('pasien.kartu');
Route::post('/pasien/daftar-antrean', [\App\Http\Controllers\PasienPortalController::class, 'storeAntrean'])->name('pasien.daftar-antrean');
Route::get('/pasien/tiket/{id}', [\App\Http\Controllers\PasienPortalController::class, 'cetakTiket'])->name('pasien.tiket');
Route::get('/pasien/notifikasi', [\App\Http\Controllers\PasienPortalController::class, 'getNotifikasi'])->name('pasien.notifikasi');
Route::get('/pasien/riwayat', [\App\Http\Controllers\PasienPortalController::class, 'riwayatSemua'])->name('pasien.riwayat');
Route::get('/pasien/komparasi', [\App\Http\Controllers\PasienPortalController::class, 'komparasi'])->name('pasien.komparasi');
Route::get('/pasien/riwayat/{id}', [\App\Http\Controllers\PasienPortalController::class, 'detailRiwayat'])->name('pasien.riwayat.detail');
Route::get('/pasien/riwayat/{id}/resume-medis', [\App\Http\Controllers\PasienPortalController::class, 'cetakResumeMedis'])->name('pasien.riwayat.resume-medis');
Route::get('/pasien/riwayat/{id}/kuitansi', [\App\Http\Controllers\PasienPortalController::class, 'cetakKuitansi'])->name('pasien.riwayat.kuitansi');
Route::post('/pasien/profil', [\App\Http\Controllers\PasienPortalController::class, 'updateProfil'])->name('pasien.profil.update');

// ── Wajib login, tapi belum tentu sudah pilih role ──
Route::middleware('auth')->group(function () {
    Route::get('/daftar-role', [RoleController::class, 'tampilkan'])->name('role.daftar');
    Route::get('/proses-role', [RoleController::class, 'proses'])->name('role.proses');
});

// ── Layar Publik Display Monitor Antrean TV Ruang Tunggu RS Delta ──
Route::get('/monitoring', [\App\Http\Controllers\ResepsionisController::class, 'displayTv'])->name('monitoring.display');
Route::get('/monitoring/status-panggilan', [\App\Http\Controllers\ResepsionisController::class, 'statusPanggilan'])->name('monitoring.status-panggilan');
Route::get('/monitoring/suara', [\App\Http\Controllers\ResepsionisController::class, 'streamSuara'])->name('monitoring.suara');

// ── Wajib login DAN sudah pilih role aktif ──
// (dulu: proteksi role_aktif di index.php, sekarang jadi 1 middleware group)
Route::middleware(['auth', 'role.aktif'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profil', [ProfilController::class, 'edit'])->name('profil');
    Route::post('/profil', [ProfilController::class, 'update'])->name('profil.update');
    Route::post('/profil/toggle-2fa', [ProfilController::class, 'toggleTwoFactor'])->name('profil.toggle2fa');

    // ── Khusus Super Admin RS (Manajemen Pengguna, Master Layanan & Poliklinik) ──
    Route::prefix('admin')->name('admin.')->middleware('role:administrator,admin')->group(function () {
        // 0. Dashboard Statistik & Eksekutif RS Delta
        Route::get('/statistik', [\App\Http\Controllers\Admin\AdminStatistikController::class, 'index'])->name('statistik');
        Route::get('/statistik/cetak', [\App\Http\Controllers\Admin\AdminStatistikController::class, 'cetakLaporan'])->name('statistik.cetak');

        // 1. Master Pengguna & Keamanan (Pegawai & Pasien)
        Route::get('/pengguna', [\App\Http\Controllers\Admin\AdminPenggunaController::class, 'index'])->name('pengguna');
        Route::post('/pengguna/tambah', [\App\Http\Controllers\Admin\AdminPenggunaController::class, 'storePegawai'])->name('pengguna.store');
        Route::post('/pengguna/{id}/reset-password', [\App\Http\Controllers\Admin\AdminPenggunaController::class, 'resetPasswordPegawai'])->name('pengguna.reset-password');
        Route::post('/pengguna/{id}/reset-2fa', [\App\Http\Controllers\Admin\AdminPenggunaController::class, 'reset2FaPegawai'])->name('pengguna.reset-2fa');
        Route::post('/pengguna/{id}/atur-poli', [\App\Http\Controllers\Admin\AdminPenggunaController::class, 'updatePenempatanDokter'])->name('pengguna.atur-poli');
        Route::post('/pasien/{id}/reset-password', [\App\Http\Controllers\Admin\AdminPenggunaController::class, 'resetPasswordPasien'])->name('pasien.reset-password');
        Route::get('/pasien/{id}/kartu', [\App\Http\Controllers\LoketController::class, 'cetakKartuPasien'])->name('pasien.kartu');

        // 1b. Menu Khusus: Permintaan Reset Password & 2FA Pengguna
        Route::get('/permintaan-reset', [\App\Http\Controllers\Admin\AdminResetController::class, 'index'])->name('reset');
        Route::post('/permintaan-reset/{id}/proses', [\App\Http\Controllers\Admin\AdminResetController::class, 'prosesReset'])->name('reset.proses');
        Route::post('/permintaan-reset/{id}/tolak', [\App\Http\Controllers\Admin\AdminResetController::class, 'tolakReset'])->name('reset.tolak');
        Route::get('/permintaan-reset/notifikasi', [\App\Http\Controllers\Admin\AdminResetController::class, 'getNotifikasiReset'])->name('reset.notifikasi');

        // Alias kompatibilitas rute lama
        Route::post('/pengguna/reset-request/{id}/proses', [\App\Http\Controllers\Admin\AdminResetController::class, 'prosesReset'])->name('pengguna.reset-request.proses');
        Route::post('/pengguna/reset-request/{id}/tolak', [\App\Http\Controllers\Admin\AdminResetController::class, 'tolakReset'])->name('pengguna.reset-request.tolak');
        Route::get('/pengguna/notifikasi-reset', [\App\Http\Controllers\Admin\AdminResetController::class, 'getNotifikasiReset'])->name('pengguna.notifikasi-reset');

        // 2. Master Tarif & Layanan Medis
        Route::get('/layanan', [KelolaController::class, 'index'])->name('layanan');
        Route::post('/layanan', [KelolaController::class, 'store'])->name('layanan.store');
        Route::put('/layanan/{layanan}', [KelolaController::class, 'update'])->name('layanan.update');
        Route::delete('/layanan/{layanan}', [KelolaController::class, 'destroy'])->name('layanan.destroy');

        // 3. Master Ruangan & Poliklinik
        Route::get('/poli', [\App\Http\Controllers\Admin\AdminPoliController::class, 'index'])->name('poli');
        Route::post('/poli', [\App\Http\Controllers\Admin\AdminPoliController::class, 'store'])->name('poli.store');
        Route::put('/poli/{poli}', [\App\Http\Controllers\Admin\AdminPoliController::class, 'update'])->name('poli.update');
        Route::delete('/poli/{poli}', [\App\Http\Controllers\Admin\AdminPoliController::class, 'destroy'])->name('poli.destroy');

        // 4. Cadangan & Backup Database
        Route::get('/backup', [\App\Http\Controllers\Admin\AdminBackupController::class, 'index'])->name('backup');
        Route::get('/backup/download-instant', [\App\Http\Controllers\Admin\AdminBackupController::class, 'downloadInstant'])->name('backup.download-instant');
        Route::post('/backup/generate', [\App\Http\Controllers\Admin\AdminBackupController::class, 'generate'])->name('backup.generate');
        Route::get('/backup/file/{filename}', [\App\Http\Controllers\Admin\AdminBackupController::class, 'downloadFile'])->name('backup.download-file');
        Route::delete('/backup/file/{filename}', [\App\Http\Controllers\Admin\AdminBackupController::class, 'deleteFile'])->name('backup.delete-file');
    });

    // Alias kompatibilitas jika mengakses rute lama /kelola (dialihkan ke /admin/layanan)
    Route::get('/kelola', function () {
        return redirect()->route('admin.layanan');
    })->middleware('role:administrator,admin')->name('kelola');

    // ── Menu Pelayanan Poliklinik Khusus Perawat (Screening TTV) ──
    Route::middleware('role:perawat')->prefix('perawat')->name('perawat.')->group(function () {
        Route::get('/skrining', [\App\Http\Controllers\PerawatController::class, 'index'])->name('skrining');
        Route::get('/notifikasi', [\App\Http\Controllers\PerawatController::class, 'getNotifikasi'])->name('notifikasi');
        Route::post('/skrining', [\App\Http\Controllers\PerawatController::class, 'store'])->name('skrining.store');
    });

    // ── Menu Pelayanan Poliklinik Khusus Dokter (Pemeriksaan Rekam Medis & E-Resep) ──
    Route::middleware('role:dokter')->prefix('dokter')->name('dokter.')->group(function () {
        Route::get('/pelayanan', [\App\Http\Controllers\DokterController::class, 'index'])->name('pelayanan');
        Route::get('/notifikasi', [\App\Http\Controllers\DokterController::class, 'getNotifikasi'])->name('notifikasi');
        Route::get('/periksa/{id}', [\App\Http\Controllers\DokterController::class, 'periksa'])->name('periksa');
        Route::post('/periksa/{id}', [\App\Http\Controllers\DokterController::class, 'simpanPemeriksaan'])->name('periksa.store');
        Route::get('/surat-sakit/{id}', [\App\Http\Controllers\DokterController::class, 'cetakSuratSakit'])->name('surat-sakit');
        Route::get('/resume-medis/{id}', [\App\Http\Controllers\DokterController::class, 'cetakResumeMedis'])->name('resume-medis');
    });

    // ── Menu Operasional Kasir (Kasir Biasa & Kasir Admin / Utama) ──
    Route::middleware('role:kasir,admin_kasir,admin kasir,kasir_utama')->group(function () {
        Route::get('/antrian', [AntrianController::class, 'index'])->name('antrian');
        Route::get('/antrian/data', [AntrianController::class, 'getData'])->name('antrian.data');
        Route::get('/antrian/notifikasi', [AntrianController::class, 'getNotifikasi'])->name('antrian.notifikasi');
        Route::post('/antrian/bayar', [AntrianController::class, 'prosesBayar'])->name('antrian.bayar');
        Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat');
    });

    // ── Menu Loket Pendaftaran Pasien (Admisi Walk-In) ──
    Route::middleware('role:loket')->prefix('loket')->name('loket.')->group(function () {
        Route::get('/pendaftaran', [\App\Http\Controllers\LoketController::class, 'index'])->name('index');
        Route::get('/cari-pasien', [\App\Http\Controllers\LoketController::class, 'cariPasien'])->name('cari-pasien');
        Route::post('/daftar', [\App\Http\Controllers\LoketController::class, 'store'])->name('store');
        Route::get('/karcis/{id}', [\App\Http\Controllers\LoketController::class, 'cetakKarcis'])->name('karcis');
        Route::get('/kartu-pasien/{id}', [\App\Http\Controllers\LoketController::class, 'cetakKartuPasien'])->name('kartu-pasien');
        Route::delete('/batal/{id}', [\App\Http\Controllers\LoketController::class, 'batal'])->name('batal');
        Route::get('/notifikasi', [\App\Http\Controllers\LoketController::class, 'getNotifikasi'])->name('notifikasi');
    });

    // ── Menu Farmasi & Apoteker ──
    Route::middleware('role:apoteker,admin,administrator')->prefix('apoteker')->name('apoteker.')->group(function () {
        Route::get('/resep', [\App\Http\Controllers\ApotekerController::class, 'index'])->name('resep');
        Route::get('/notifikasi', [\App\Http\Controllers\ApotekerController::class, 'getNotifikasi'])->name('notifikasi');
        Route::post('/resep/{id}/racik', [\App\Http\Controllers\ApotekerController::class, 'prosesRacik'])->name('racik');
        Route::post('/resep/{id}/serahkan', [\App\Http\Controllers\ApotekerController::class, 'serahkanObat'])->name('serahkan');
        Route::get('/resep/{id}/etiket', [\App\Http\Controllers\ApotekerController::class, 'cetakEtiket'])->name('etiket');
        Route::get('/resep/{id}/copy-resep', [\App\Http\Controllers\ApotekerController::class, 'cetakCopyResep'])->name('copy-resep');
        Route::get('/obat', [\App\Http\Controllers\ApotekerController::class, 'obat'])->name('obat');
        Route::get('/obat/stok-pdf', [\App\Http\Controllers\ApotekerController::class, 'cetakStokPdf'])->name('obat.stok-pdf');
        Route::get('/obat/stok-excel', [\App\Http\Controllers\ApotekerController::class, 'exportStokExcel'])->name('obat.stok-excel');
        Route::post('/obat', [\App\Http\Controllers\ApotekerController::class, 'obatStore'])->name('obat.store');
        Route::post('/obat/{id}/stok', [\App\Http\Controllers\ApotekerController::class, 'obatUpdateStok'])->name('obat.update-stok');
        Route::get('/riwayat', [\App\Http\Controllers\ApotekerController::class, 'riwayat'])->name('riwayat');
    });

    // ── Menu Resepsionis & Monitoring Antrean End-to-End RS Delta ──
    Route::middleware('role:resepsionis,admin,administrator,loket')->prefix('resepsionis')->name('resepsionis.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ResepsionisController::class, 'index'])->name('index');
        Route::get('/layar-tv', [\App\Http\Controllers\ResepsionisController::class, 'layarTv'])->name('layar-tv');
        Route::get('/journey-data', [\App\Http\Controllers\ResepsionisController::class, 'dataJourney'])->name('journey-data');
        Route::post('/panggil', [\App\Http\Controllers\ResepsionisController::class, 'panggilAntrean'])->name('panggil');
        Route::get('/notifikasi', [\App\Http\Controllers\ResepsionisController::class, 'getNotifikasi'])->name('notifikasi');
    });

    // ── Khusus Kasir Admin / Kasir Utama (Laporan Keuangan Kasir & Rekapitulasi) ──
    Route::middleware('role:admin_kasir,kasir_utama,admin kasir')->group(function () {
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
        Route::get('/laporan/cetak', [LaporanController::class, 'cetak'])->name('laporan.cetak');
        Route::get('/laporan/excel', [LaporanController::class, 'exportExcel'])->name('laporan.excel');
    });
});
