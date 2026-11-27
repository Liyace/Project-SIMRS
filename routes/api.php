<?php

use App\Http\Controllers\Api\ApotikApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KasirApiController;
use App\Http\Controllers\Api\LayananController;
use App\Http\Controllers\Api\MobileApiController;
use App\Http\Controllers\Api\PasienController;
use App\Http\Controllers\Api\PemeriksaanController;
use App\Http\Controllers\Api\PendaftaranController;
use App\Http\Controllers\Api\PoliController;
use App\Http\Controllers\Api\TransaksiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes SIMRS (V1)
|--------------------------------------------------------------------------
| Terintegrasi untuk 4 Kelompok (Pendaftaran, Poli, Kasir, Apotik)
| dan Aplikasi Flutter Mobile.
*/

// Legacy routes (tetap dipertahankan untuk kompatibilitas)
Route::apiResource('/layanan', LayananController::class);
Route::apiResource('/transaksi', TransaksiController::class);

Route::prefix('v1')->group(function () {

    // ==========================================
    // 1. PUBLIC AUTH & MASTER DATA
    // ==========================================
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register-pasien', [AuthController::class, 'registerPasien']);
    Route::post('/auth/lupa-password', [AuthController::class, 'lupaPassword']);

    Route::get('/poli', [PoliController::class, 'index']);
    Route::get('/poli/{id}', [PoliController::class, 'show']);
    Route::get('/antrian/hari-ini', [PendaftaranController::class, 'antrianHariIni']);

    // ==========================================
    // 2. AUTHENTICATED ROUTES (Bearer Token)
    // ==========================================
    Route::middleware('auth:sanctum')->group(function () {

        // User info & Logout
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // ---- BAGIAN 1: PASIEN & PENDAFTARAN LOKET ----
        Route::get('/pasien', [PasienController::class, 'index']);
        Route::get('/pasien/{id}', [PasienController::class, 'show']);
        Route::post('/pasien', [PasienController::class, 'store']);
        Route::put('/pasien/{id}', [PasienController::class, 'update']);

        Route::post('/pendaftaran', [PendaftaranController::class, 'store']);
        Route::get('/pendaftaran/{id}', [PendaftaranController::class, 'show']);

        // ---- BAGIAN 2: POLI (PERAWAT & DOKTER) ----
        Route::get('/poli/{id_poli}/antrian', [PemeriksaanController::class, 'antrianPoli']);
        Route::post('/pemeriksaan/screening', [PemeriksaanController::class, 'storeScreening']);
        Route::post('/pemeriksaan/rekam-medis', [PemeriksaanController::class, 'storeRekamMedis']);
        Route::post('/pemeriksaan/resep', [PemeriksaanController::class, 'storeResep']);
        Route::put('/pemeriksaan/{id_pendaftaran}/selesai-periksa', [PemeriksaanController::class, 'selesaiPeriksa']);

        // ---- BAGIAN 3: KASIR & PEMBAYARAN ----
        Route::get('/kasir/antrian', [KasirApiController::class, 'antrianKasir']);
        Route::get('/kasir/tagihan/{id_pendaftaran}', [KasirApiController::class, 'getTagihan']);
        Route::post('/kasir/bayar', [KasirApiController::class, 'bayar']);
        Route::get('/kasir/laporan-harian', [KasirApiController::class, 'laporanHarian']);

        // ---- BAGIAN 4: APOTIK / FARMASI ----
        Route::get('/apotik/antrian-resep', [ApotikApiController::class, 'antrianResep']);
        Route::get('/apotik/resep/{id_resep}', [ApotikApiController::class, 'detailResep']);
        Route::put('/apotik/resep/{id_resep}/verifikasi', [ApotikApiController::class, 'verifikasiResep']);
        Route::put('/apotik/resep/{id_resep}/serahkan', [ApotikApiController::class, 'serahkanObat']);
        Route::get('/obat', [ApotikApiController::class, 'obatIndex']);
        Route::post('/obat', [ApotikApiController::class, 'obatStore']);

        // ---- INTEGRASI FLUTTER MOBILE (PASIEN) ----
        Route::get('/mobile/jadwal-poli', [MobileApiController::class, 'jadwalPoli']);
        Route::post('/mobile/daftar-online', [MobileApiController::class, 'daftarOnline']);
        Route::get('/mobile/tiket-antrian-aktif', [MobileApiController::class, 'tiketAntrianAktif']);
        Route::get('/mobile/kartu-pasien', [MobileApiController::class, 'kartuPasien']);
        Route::get('/mobile/riwayat-kunjungan', [MobileApiController::class, 'riwayatKunjungan']);
        Route::get('/mobile/riwayat-kunjungan/{id}', [MobileApiController::class, 'detailRiwayatKunjungan']);
        Route::get('/mobile/komparasi', [MobileApiController::class, 'komparasi']);
        Route::put('/mobile/profil', [MobileApiController::class, 'updateProfil']);
    });
});
