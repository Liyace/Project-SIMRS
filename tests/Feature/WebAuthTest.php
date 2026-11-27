<?php

namespace Tests\Feature;

use App\Models\Pasien;
use App\Models\Pegawai;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WebAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_login_page_renders_successfully()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Kembali ke Beranda');
        $response->assertSee('Masuk ke Sistem');
    }

    public function test_register_page_renders_successfully()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Kembali ke Login');
        $response->assertSee('Username *');
        $response->assertSee('NIK KTP');
        $response->assertSee('Ulangi Password (Password 2)');
    }

    public function test_patient_can_register_and_redirects_to_login()
    {
        $response = $this->post('/register', [
            'username'              => 'pasienbaru',
            'nik'                   => '6371999988880001',
            'nama'                  => 'Pasien Baru Web',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'jenis_kelamin'         => 'L',
        ]);

        // Diarahkan kembali ke halaman login dengan notifikasi sukses
        $response->assertRedirect('/login');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pasien', [
            'username'    => 'pasienbaru',
            'nik'         => '6371999988880001',
            'nama_pasien' => 'Pasien Baru Web',
        ]);
    }

    public function test_patient_can_login_with_username()
    {
        Pasien::create([
            'rm'            => 'RM-9999',
            'nik'           => '6371888899990001',
            'username'      => 'pasientest',
            'nama_pasien'   => 'Pasien Testing',
            'password_hash' => Hash::make('secret123'),
        ]);

        $response = $this->post('/login', [
            'nik'      => 'pasientest',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/pasien/dashboard');
        $this->assertEquals(session('user_type'), 'pasien');
        $this->assertEquals(session('nama_pasien'), 'Pasien Testing');

        // Bisa membuka dashboard pasien
        $dashRes = $this->get('/pasien/dashboard');
        $dashRes->assertStatus(200);
        $dashRes->assertSee('Portal Pasien Terpadu');
        $dashRes->assertSee('RM-9999');
    }

    public function test_pegawai_can_login_without_2fa()
    {
        // 1. Dokter login langsung diarahkan ke workstation pelayanan dokter
        $dokter = Pegawai::where('role', 'dokter')->first();
        $dokter->update(['two_factor_enabled' => false]);

        $resDokter = $this->post('/login', [
            'nik'      => $dokter->nik,
            'password' => '12345',
        ]);
        $resDokter->assertRedirect('/dokter/pelayanan');
        $this->assertEquals(session('user_type'), 'pegawai');
        $this->assertEquals(session('role_aktif'), 'dokter');

        // 2. Perawat login langsung diarahkan ke skrining TTV perawat
        $perawat = Pegawai::where('role', 'perawat')->first();
        $perawat->update(['two_factor_enabled' => false]);

        $resPerawat = $this->post('/login', [
            'nik'      => $perawat->nik,
            'password' => '12345',
        ]);
        $resPerawat->assertRedirect('/perawat/skrining');
        $this->assertEquals(session('role_aktif'), 'perawat');

        // 3. Kasir login diarahkan ke dashboard kasir
        $kasir = Pegawai::where('role', 'kasir')->first();
        $kasir->update(['two_factor_enabled' => false]);

        $resKasir = $this->post('/login', [
            'nik'      => $kasir->nik,
            'password' => '12345',
        ]);
        $resKasir->assertRedirect('/dashboard');
        $this->assertEquals(session('role_aktif'), 'kasir');

        // 4. Perawat yang mencoba membuka /dashboard otomatis dialihkan ke /perawat/skrining
        $dashPerawat = $this->actingAs($perawat)
            ->withSession(['role_aktif' => 'perawat', 'user_type' => 'pegawai'])
            ->get('/dashboard');
        $dashPerawat->assertRedirect('/perawat/skrining');
    }

    public function test_admin_redirects_directly_to_user_management()
    {
        $admin = Pegawai::where('role', 'admin')->first();
        $admin->update(['two_factor_enabled' => false]);

        $response = $this->post('/login', [
            'nik'      => $admin->nik,
            'password' => '12345',
        ]);

        $response->assertRedirect('/admin/statistik');
        $this->assertEquals(session('role_aktif'), 'admin');
    }

    public function test_administrator_role_redirects_to_user_management_and_renders_super_admin_rs()
    {
        $pegawai = Pegawai::create([
            'nik'                => '6301019999',
            'nama'               => 'Admin IT Test',
            'role'               => 'administrator',
            'password_hash'      => Hash::make('12345'),
            'two_factor_enabled' => false,
            'is_admin'           => true,
        ]);

        $response = $this->post('/login', [
            'nik'      => '6301019999',
            'password' => '12345',
        ]);

        $response->assertRedirect('/admin/statistik');

        $adminPage = $this->get('/admin/pengguna');
        $adminPage->assertStatus(200);
        $adminPage->assertSee('Super Admin RS');
        $adminPage->assertSee('Kelola Pengguna');
        $adminPage->assertSee('Kelola Layanan');
        $adminPage->assertSee('Kelola Poliklinik');

        // Buka Master Tarif & Layanan Medis
        $layananRes = $this->get('/admin/layanan');
        $layananRes->assertStatus(200);
        $layananRes->assertSee('Master Tarif & Layanan Medis');

        // Buka Master Ruangan & Poliklinik
        $poliRes = $this->get('/admin/poli');
        $poliRes->assertStatus(200);
        $poliRes->assertSee('Master Ruangan & Poliklinik');
    }

    public function test_pegawai_with_2fa_enabled_requires_otp_verification()
    {
        $pegawai = Pegawai::where('role', 'dokter')->first();
        $secret = \App\Services\GoogleAuthenticator::generateSecret();
        $pegawai->update([
            'two_factor_enabled' => true,
            'two_factor_secret'  => $secret,
        ]);

        // Tahap 1: Masukkan NIK dan Password
        $loginRes = $this->post('/login', [
            'nik'      => $pegawai->nik,
            'password' => '12345',
        ]);

        // Diarahkan ke halaman verifikasi 2FA
        $loginRes->assertRedirect('/login/2fa');
        $this->assertEquals($pegawai->id_pegawai, session('2fa_pegawai_id'));

        // Form 2FA bisa dibuka
        $form2fa = $this->get('/login/2fa');
        $form2fa->assertStatus(200);
        $form2fa->assertSee('Verifikasi 2 Langkah (2FA)');

        // Tahap 2: Input OTP salah -> ditolak
        $wrongOtp = $this->post('/login/2fa', ['otp_code' => '000000']);
        $wrongOtp->assertSessionHasErrors('otp_code');

        // Tahap 3: Input TOTP benar dari Google Authenticator -> login berhasil ke workstation dokter
        $code = \App\Services\GoogleAuthenticator::getCode($secret);
        $validOtp = $this->post('/login/2fa', ['otp_code' => $code]);
        $validOtp->assertRedirect('/dokter/pelayanan');
        $this->assertEquals('pegawai', session('user_type'));
    }

    public function test_pegawai_can_toggle_2fa_in_profile()
    {
        $pegawai = Pegawai::where('role', 'dokter')->first();
        $secret = \App\Services\GoogleAuthenticator::generateSecret();
        $code = \App\Services\GoogleAuthenticator::getCode($secret);

        // 1. Gagal aktifkan 2FA jika kode OTP salah
        $failedToggle = $this->actingAs($pegawai)
            ->withSession(['role_aktif' => 'Dokter', 'setup_2fa_secret' => $secret])
            ->post('/profil/toggle-2fa', [
                'secret'   => $secret,
                'otp_code' => '000000',
            ]);
        $failedToggle->assertSessionHasErrors('otp_code');
        $this->assertFalse((bool)$pegawai->fresh()->two_factor_enabled);

        // 2. Berhasil aktifkan 2FA secara cepat cukup dengan kode OTP valid (tanpa perlu ketik password agar tidak kehabisan waktu OTP)
        $toggleOn = $this->actingAs($pegawai)
            ->withSession(['role_aktif' => 'Dokter', 'setup_2fa_secret' => $secret])
            ->post('/profil/toggle-2fa', [
                'secret'   => $secret,
                'otp_code' => $code,
            ]);
        $toggleOn->assertRedirect();
        $this->assertTrue((bool)$pegawai->fresh()->two_factor_enabled);
        $this->assertEquals($secret, $pegawai->fresh()->two_factor_secret);

        // 3. Gagal nonaktifkan 2FA jika password akun salah
        $failedToggleOff = $this->actingAs($pegawai)
            ->withSession(['role_aktif' => 'Dokter'])
            ->post('/profil/toggle-2fa', [
                'password' => 'WrongPassword#999',
            ]);
        $failedToggleOff->assertSessionHasErrors('password_2fa');
        $this->assertTrue((bool)$pegawai->fresh()->two_factor_enabled);

        // 4. Berhasil nonaktifkan 2FA dengan password akun yang benar
        $toggleOff = $this->actingAs($pegawai)
            ->withSession(['role_aktif' => 'Dokter'])
            ->post('/profil/toggle-2fa', [
                'password' => '12345',
            ]);
        $toggleOff->assertRedirect();
        $this->assertFalse((bool)$pegawai->fresh()->two_factor_enabled);
    }

    public function test_logout_redirects_to_login_with_status_message()
    {
        $admin = Pegawai::where('role', 'admin')->first();

        $response = $this->actingAs($admin)
            ->withSession(['role_aktif' => 'admin', 'user_type' => 'pegawai'])
            ->get('/logout');

        $response->assertRedirect('/login');
        $response->assertSessionHas('status', 'Anda telah berhasil keluar dari sistem.');

        // Login page displays status banner
        $loginPage = $this->get('/login');
        $loginPage->assertStatus(200);
    }

    public function test_admin_page_renders_collapsible_sidebar_and_dark_mode_controls()
    {
        $admin = Pegawai::where('role', 'admin')->first();

        $response = $this->actingAs($admin)
            ->withSession(['role_aktif' => 'admin', 'user_type' => 'pegawai'])
            ->get('/admin/pengguna');

        $response->assertStatus(200);
        $response->assertSee('btn-toggle-sidebar', false);
        $response->assertSee('btn-toggle-theme', false);
        $response->assertSee('Pengaturan Profil');
        $response->assertSee('Mode Gelap');
        $response->assertSee('Keluar');
        $response->assertSee('simrs-live-clock', false);
        $response->assertSee('simrs-ui.js');
    }

    public function test_admin_can_search_pegawai_and_pasien_with_pagination()
    {
        $admin = Pegawai::where('role', 'admin')->first();

        // 1. Tes pencarian staf
        $searchPegawai = $this->actingAs($admin)
            ->withSession(['role_aktif' => 'admin', 'user_type' => 'pegawai'])
            ->get('/admin/pengguna?q_pegawai=Budi');
        $searchPegawai->assertStatus(200);
        $searchPegawai->assertSee('Budi');

        // 2. Tes pencarian pasien
        $searchPasien = $this->actingAs($admin)
            ->withSession(['role_aktif' => 'admin', 'user_type' => 'pegawai'])
            ->get('/admin/pengguna?q_pasien=RM-0001');
        $searchPasien->assertStatus(200);
        $searchPasien->assertSee('RM-0001');
        $searchPasien->assertSee('toggleFormTambah', false);
    }

    public function test_kasir_utama_can_access_all_five_cashier_menus_and_sees_kasir_utama_role()
    {
        $kasirUtama = Pegawai::where('nik', '6301010004')->first();

        // Login sebagai Kasir Utama
        $loginRes = $this->post('/login', [
            'nik'      => $kasirUtama->nik,
            'password' => '12345',
        ]);
        $loginRes->assertRedirect('/dashboard');
        $this->assertTrue(session('is_kasir_utama'));

        // Dashboard & Sidebar: Kasir Admin punya Antrian, Riwayat, dan Laporan Kasir
        $dashRes = $this->get('/dashboard');
        $dashRes->assertStatus(200);
        $dashRes->assertSee('Kasir Admin');
        $dashRes->assertSee('Antrian Kasir');
        $dashRes->assertSee('Riwayat Transaksi');
        $dashRes->assertDontSee('Kelola Layanan');
        $dashRes->assertSee('Laporan Kasir');

        // Buka Antrian Kasir
        $antrianRes = $this->get('/antrian');
        $antrianRes->assertStatus(200);

        // Buka Riwayat Transaksi
        $riwayatRes = $this->get('/riwayat');
        $riwayatRes->assertStatus(200);
        $riwayatRes->assertSee('Riwayat Transaksi');
        $riwayatRes->assertSee('Daftar semua transaksi yang telah dilakukan.');
        $riwayatRes->assertSee('NO. TRANSAKSI');
        $riwayatRes->assertSee('Pilih Tanggal');
        $riwayatRes->assertSee('Cari Pasien');

        // Buka Riwayat dengan Filter Pencarian Pasien
        $filterRes = $this->get('/riwayat?q=Ahmad');
        $filterRes->assertStatus(200);
        $filterRes->assertSee('Ahmad Fauzi');

        // Dicegah membuka Kelola Layanan (karena wewenang eksklusif Super Admin RS)
        $kelolaRes = $this->get('/kelola');
        $kelolaRes->assertRedirect('/dashboard');
        $kelolaRes->assertSessionHas('error');

        // Buka Laporan Kasir
        $laporanRes = $this->get('/laporan');
        $laporanRes->assertStatus(200);
        $laporanRes->assertSee('Laporan Keuangan Kasir');
    }

    public function test_kasir_biasa_can_only_access_operational_menus_and_is_blocked_from_management()
    {
        $kasirBiasa = Pegawai::where('nik', '6301010007')->first();

        // Login sebagai Kasir Biasa
        $loginRes = $this->post('/login', [
            'nik'      => $kasirBiasa->nik,
            'password' => '12345',
        ]);
        $loginRes->assertRedirect('/dashboard');
        $this->assertFalse((bool) session('is_kasir_utama'));

        // Dashboard & Sidebar: Menampilkan 'Kasir', punya Antrian & Riwayat, TAPI TIDAK ADA Kelola & Laporan
        $dashRes = $this->get('/dashboard');
        $dashRes->assertStatus(200);
        $dashRes->assertSee('Kasir');
        $dashRes->assertSee('Antrian Kasir');
        $dashRes->assertSee('Riwayat Transaksi');
        $dashRes->assertDontSee('Kelola Layanan');
        $dashRes->assertDontSee('Laporan Kasir');

        // Bisa buka Antrian Kasir & Riwayat Transaksi
        $antrianRes = $this->get('/antrian');
        $antrianRes->assertStatus(200);

        $riwayatRes = $this->get('/riwayat');
        $riwayatRes->assertStatus(200);

        // Dicegah membuka Kelola Layanan & Laporan Kasir (dialihkan ke dashboard tanpa logout)
        $kelolaRes = $this->get('/kelola');
        $kelolaRes->assertRedirect('/dashboard');
        $kelolaRes->assertSessionHas('error');

        $laporanRes = $this->get('/laporan');
        $laporanRes->assertRedirect('/dashboard');
        $laporanRes->assertSessionHas('error');
    }

    public function test_admin_can_create_kasir_biasa_and_admin_kasir_with_automatic_rbac_separation()
    {
        $admin = Pegawai::where('role', 'admin')->first();

        // 1. Admin membuat Kasir Baru (Loket Biasa)
        $resKasir = $this->actingAs($admin)
            ->withSession(['role_aktif' => 'admin', 'user_type' => 'pegawai'])
            ->post('/admin/pengguna/tambah', [
                'nik'           => '6301019001',
                'nama'          => 'Staf Loket Baru',
                'role'          => 'kasir',
                'jenis_kelamin' => 'L',
                'password'      => '12345',
            ]);
        $resKasir->assertRedirect();
        $this->assertDatabaseHas('pegawai', ['nik' => '6301019001', 'role' => 'kasir']);

        // 2. Admin membuat Kasir Admin Baru (Supervisor)
        $resAdminKasir = $this->actingAs($admin)
            ->withSession(['role_aktif' => 'admin', 'user_type' => 'pegawai'])
            ->post('/admin/pengguna/tambah', [
                'nik'           => '6301019002',
                'nama'          => 'Supervisor Loket Baru',
                'role'          => 'admin_kasir',
                'jenis_kelamin' => 'P',
                'password'      => '12345',
            ]);
        $resAdminKasir->assertRedirect();
        $this->assertDatabaseHas('pegawai', ['nik' => '6301019002', 'role' => 'admin_kasir']);

        // 3. Login sebagai Kasir Admin Baru -> Bisa buka Laporan
        $newAdminKasir = Pegawai::where('nik', '6301019002')->first();
        $this->post('/login', ['nik' => '6301019002', 'password' => '12345']);
        $dashRes = $this->get('/dashboard');
        $dashRes->assertSee('Kasir Admin');
        $dashRes->assertSee('Laporan Kasir');
        $laporanRes = $this->get('/laporan');
        $laporanRes->assertStatus(200);

        // 4. Login sebagai Kasir Biasa Baru -> Tidak bisa buka Laporan
        $this->post('/logout');
        $this->post('/login', ['nik' => '6301019001', 'password' => '12345']);
        $dashRes2 = $this->get('/dashboard');
        $dashRes2->assertSee('Kasir');
        $dashRes2->assertDontSee('Laporan Kasir');
        $laporanRes2 = $this->get('/laporan');
        $laporanRes2->assertRedirect('/dashboard');
    }

    public function test_kasir_can_fetch_antrian_data_and_process_payment_locally()
    {
        $kasir = Pegawai::where('nik', '6301010004')->first();

        // 1. Ambil data antrian lokal via API internal
        $dataRes = $this->actingAs($kasir)
            ->withSession(['role_aktif' => 'admin_kasir', 'user_type' => 'pegawai', 'is_kasir_utama' => true])
            ->getJson('/antrian/data');
        $dataRes->assertStatus(200);
        $dataRes->assertJsonStructure([
            'success',
            'antrian',
            'layanan',
        ]);
        $this->assertTrue($dataRes->json('success'));

        // 2. Ambil antrian pasien yang menunggu kasir
        $antrianPertama = collect($dataRes->json('antrian'))->firstWhere('status_antrian', 'menunggu_kasir');
        $this->assertNotNull($antrianPertama);

        // 3. Proses bayar transaksi
        $totalBayar = 100000;
        $tunai = 150000;
        $bayarRes = $this->actingAs($kasir)
            ->withSession(['role_aktif' => 'admin_kasir', 'user_type' => 'pegawai', 'is_kasir_utama' => true])
            ->postJson('/antrian/bayar', [
                'id_pendaftaran' => $antrianPertama['id_pendaftaran'],
                'total_bayar'    => $totalBayar,
                'total_layanan'  => 0,
                'total_resep'    => 50000,
                'jasa_perawat'   => 50000,
                'jumlah_dibayar' => $tunai,
                'metode_bayar'   => 'TUNAI',
            ]);

        $bayarRes->assertStatus(200);
        $bayarRes->assertJson([
            'success' => true,
            'data'    => [
                'total_bayar' => $totalBayar,
                'tunai'       => $tunai,
                'kembalian'   => 50000,
            ],
        ]);

        // 4. Verifikasi status pendaftaran dialihkan ke apotek farmasi untuk pengambilan obat
        $this->assertDatabaseHas('pendaftaran', [
            'id_pendaftaran' => $antrianPertama['id_pendaftaran'],
            'status_antrian' => 'menunggu_obat',
        ]);

        // 5. Verifikasi transaksi tersimpan di database
        $this->assertDatabaseHas('transaksi', [
            'id_pendaftaran'    => $antrianPertama['id_pendaftaran'],
            'total_bayar'       => $totalBayar,
            'status_pembayaran' => 'lunas',
        ]);
    }

    public function test_laporan_kasir_period_filtering_and_exports()
    {
        $adminKasir = Pegawai::where('nik', '6301010004')->first();
        $kasirBiasa = Pegawai::where('nik', '6301010007')->first();
        $administrator = Pegawai::where('nik', '6301010001')->first();

        // 1. Kasir Biasa TIDAK BISA akses Laporan
        $resBiasa = $this->actingAs($kasirBiasa)
            ->withSession(['role_aktif' => 'kasir', 'user_type' => 'pegawai'])
            ->get('/laporan');
        $resBiasa->assertRedirect('/dashboard');

        // 2. Administrator TIDAK BISA akses Laporan Kasir (hanya Kasir Admin)
        $resAdmin = $this->actingAs($administrator)
            ->withSession(['role_aktif' => 'administrator', 'user_type' => 'pegawai'])
            ->get('/laporan');
        $resAdmin->assertRedirect('/dashboard');

        // 3. Kasir Admin BISA akses Laporan Harian, Bulanan, dan Tahunan
        $resHarian = $this->actingAs($adminKasir)
            ->withSession(['role_aktif' => 'admin_kasir', 'user_type' => 'pegawai', 'is_kasir_utama' => true])
            ->get('/laporan?periode=harian');
        $resHarian->assertStatus(200);
        $resHarian->assertSee('Laporan Kasir');
        $resHarian->assertSee('Harian');

        $resBulanan = $this->actingAs($adminKasir)
            ->withSession(['role_aktif' => 'admin_kasir', 'user_type' => 'pegawai', 'is_kasir_utama' => true])
            ->get('/laporan?periode=bulanan&bulan=11&tahun=2026');
        $resBulanan->assertStatus(200);
        $resBulanan->assertSee('Bulan November 2026');

        $resTahunan = $this->actingAs($adminKasir)
            ->withSession(['role_aktif' => 'admin_kasir', 'user_type' => 'pegawai', 'is_kasir_utama' => true])
            ->get('/laporan?periode=tahunan&tahun=2026');
        $resTahunan->assertStatus(200);
        $resTahunan->assertSee('Tahun 2026');

        // 4. Kasir Admin BISA buka halaman Cetak PDF resmi
        $resCetak = $this->actingAs($adminKasir)
            ->withSession(['role_aktif' => 'admin_kasir', 'user_type' => 'pegawai', 'is_kasir_utama' => true])
            ->get('/laporan/cetak?periode=bulanan&bulan=11&tahun=2026');
        $resCetak->assertStatus(200);
        $resCetak->assertSee('RUMAH SAKIT DELTA BANJARMASIN');
        $resCetak->assertSee('Kepala Bagian Keuangan RS Delta');

        // 5. Kasir Admin BISA unduh Excel
        $resExcel = $this->actingAs($adminKasir)
            ->withSession(['role_aktif' => 'admin_kasir', 'user_type' => 'pegawai', 'is_kasir_utama' => true])
            ->get('/laporan/excel?periode=bulanan&bulan=11&tahun=2026');
        $resExcel->assertStatus(200);
        $resExcel->assertHeader('content-type', 'application/vnd.ms-excel; charset=utf-8');
        $this->assertStringContainsString('attachment; filename="Laporan_Kasir_RS_Delta_bulanan_November_2026.xls"', $resExcel->headers->get('content-disposition'));
    }

    public function test_end_to_end_payment_reflects_in_riwayat_dashboard_and_laporan()
    {
        $adminKasir = Pegawai::where('nik', '6301010004')->first();

        // 1. Ambil antrian aktif (Siti Aminah UMU-002)
        $dashBefore = $this->actingAs($adminKasir)
            ->withSession(['role_aktif' => 'admin_kasir', 'user_type' => 'pegawai', 'is_kasir_utama' => true])
            ->get('/dashboard');
        $dashBefore->assertStatus(200);
        $dashBefore->assertSee('Antrean Pasien Menunggu Pembayaran');
        $dashBefore->assertSee('Layani Kasir');
        $dashBefore->assertSee('Siti Aminah');

        $antrianRes = $this->actingAs($adminKasir)
            ->withSession(['role_aktif' => 'admin_kasir', 'user_type' => 'pegawai', 'is_kasir_utama' => true])
            ->getJson('/antrian/data');
        $antrianList = $antrianRes->json('antrian');
        $siti = collect($antrianList)->firstWhere('nama_pasien', 'Siti Aminah');
        $this->assertNotNull($siti);

        // 2. Bayar tagihan Siti Aminah
        $totalBayar = 100000;
        $bayarRes = $this->actingAs($adminKasir)
            ->withSession(['role_aktif' => 'admin_kasir', 'user_type' => 'pegawai', 'is_kasir_utama' => true])
            ->postJson('/antrian/bayar', [
                'id_pendaftaran' => $siti['id_pendaftaran'],
                'total_bayar'    => $totalBayar,
                'total_layanan'  => 0,
                'total_resep'    => 50000,
                'jasa_perawat'   => 50000,
                'jumlah_dibayar' => 100000,
                'metode_bayar'   => 'TUNAI',
            ]);
        $bayarRes->assertStatus(200);

        // 3. Verifikasi Siti Aminah langsung muncul di Riwayat Transaksi
        $riwayatRes = $this->actingAs($adminKasir)
            ->withSession(['role_aktif' => 'admin_kasir', 'user_type' => 'pegawai', 'is_kasir_utama' => true])
            ->get('/riwayat');
        $riwayatRes->assertStatus(200);
        $riwayatRes->assertSee('Siti Aminah');
        $riwayatRes->assertSee('100.000');

        // 4. Verifikasi data langsung muncul di Dashboard Kasir
        $dashRes = $this->actingAs($adminKasir)
            ->withSession(['role_aktif' => 'admin_kasir', 'user_type' => 'pegawai', 'is_kasir_utama' => true])
            ->get('/dashboard');
        $dashRes->assertStatus(200);
        $dashRes->assertSee('Siti Aminah');

        // 5. Verifikasi data langsung muncul di Laporan Kasir
        $laporanRes = $this->actingAs($adminKasir)
            ->withSession(['role_aktif' => 'admin_kasir', 'user_type' => 'pegawai', 'is_kasir_utama' => true])
            ->get('/laporan?periode=harian');
        $laporanRes->assertStatus(200);
        $laporanRes->assertSee('Siti Aminah');
    }

    public function test_kasir_can_receive_realtime_notifications_for_pending_patients()
    {
        $adminKasir = Pegawai::where('nik', '6301010004')->first();

        // 1. Verifikasi topbar menampilkan lonceng notifikasi untuk kasir
        $dashRes = $this->actingAs($adminKasir)
            ->withSession(['role_aktif' => 'admin_kasir', 'user_type' => 'pegawai', 'is_kasir_utama' => true])
            ->get('/dashboard');
        $dashRes->assertStatus(200);
        $dashRes->assertSee('btn-topbar-notif');
        $dashRes->assertSee('dropdown-notif-kasir');
        $dashRes->assertSee('sidebar-notif-count');

        // 2. Akses endpoint notifikasi internal
        $notifRes = $this->actingAs($adminKasir)
            ->withSession(['role_aktif' => 'admin_kasir', 'user_type' => 'pegawai', 'is_kasir_utama' => true])
            ->getJson('/antrian/notifikasi');
        $notifRes->assertStatus(200);
        $notifRes->assertJsonStructure([
            'success',
            'count',
            'pasien' => [
                '*' => [
                    'id_pendaftaran',
                    'no_antrian',
                    'nama_pasien',
                    'no_rm',
                    'nama_poli',
                    'waktu_daftar',
                    'url_layani',
                ],
            ],
            'timestamp',
        ]);
        $this->assertTrue($notifRes->json('success'));
        $this->assertIsInt($notifRes->json('count'));

        // 3. Verifikasi halaman cetak laporan kasir menggunakan logo logodelta1.svg dan menampilkan NIP kasir
        $cetakRes = $this->actingAs($adminKasir)
            ->withSession(['role_aktif' => 'admin_kasir', 'user_type' => 'pegawai', 'is_kasir_utama' => true])
            ->get('/laporan/cetak?periode=harian');
        $cetakRes->assertStatus(200);
        $cetakRes->assertSee('logodelta1.svg');
        $cetakRes->assertSee('NIP. 19920415 201801 1 003');
    }

    public function test_pegawai_nip_management_in_admin_and_self_profile()
    {
        $superAdmin = Pegawai::where('role', 'admin')->first();
        $adminKasir = Pegawai::where('nik', '6301010004')->first();

        // 1. Admin dapat menambahkan akun staf baru dengan NIP (opsional)
        $uniqNik = '630101' . rand(1000, 9999);
        $storeRes = $this->actingAs($superAdmin)
            ->withSession(['role_aktif' => 'admin', 'user_type' => 'pegawai'])
            ->post('/admin/pengguna/tambah', [
                'nik'           => $uniqNik,
                'nip'           => '19951010 202001 1 007',
                'nama'          => 'Staf Uji NIP',
                'role'          => 'kasir',
                'jenis_kelamin' => 'L',
                'no_hp'         => '081299998888',
                'password'      => 'Password#123',
            ]);
        $storeRes->assertRedirect();
        $this->assertDatabaseHas('pegawai', [
            'nik'  => $uniqNik,
            'nip'  => '19951010 202001 1 007',
            'nama' => 'Staf Uji NIP',
        ]);

        // 2. Admin dapat menambahkan staf tanpa NIP (dibiarkan kosong)
        $uniqNik2 = '630101' . rand(1000, 9999);
        $storeRes2 = $this->actingAs($superAdmin)
            ->withSession(['role_aktif' => 'admin', 'user_type' => 'pegawai'])
            ->post('/admin/pengguna/tambah', [
                'nik'           => $uniqNik2,
                'nip'           => '',
                'nama'          => 'Staf Tanpa NIP Awal',
                'role'          => 'kasir',
                'jenis_kelamin' => 'P',
                'no_hp'         => '081277776666',
                'password'      => 'Password#123',
            ]);
        $storeRes2->assertRedirect();
        $this->assertDatabaseHas('pegawai', [
            'nik'  => $uniqNik2,
            'nip'  => null,
            'nama' => 'Staf Tanpa NIP Awal',
        ]);

        // 3. Staf dapat melihat field NIP di Pengaturan Profil dan mengedit NIP mandiri
        $profilViewRes = $this->actingAs($adminKasir)
            ->withSession(['role_aktif' => 'admin_kasir', 'user_type' => 'pegawai', 'is_kasir_utama' => true])
            ->get('/profil');
        $profilViewRes->assertStatus(200);
        $profilViewRes->assertSee('NIP (Nomor Induk Pegawai)');

        // Staf mengedit NIP mereka sendiri
        $newNip = '19920415 201801 1 999';
        $updateRes = $this->actingAs($adminKasir)
            ->withSession(['role_aktif' => 'admin_kasir', 'user_type' => 'pegawai', 'is_kasir_utama' => true])
            ->post('/profil', [
                'nama'          => $adminKasir->nama,
                'jenis_kelamin' => $adminKasir->jenis_kelamin ?? 'L',
                'nip'           => $newNip,
            ]);
        $updateRes->assertRedirect();
        $this->assertDatabaseHas('pegawai', [
            'id_pegawai' => $adminKasir->id_pegawai,
            'nip'        => $newNip,
        ]);
    }

    public function test_perawat_can_view_skrining_queue_and_submit_ttv()
    {
        $perawat = Pegawai::where('role', 'perawat')->first();
        $pendaftaran = \App\Models\Pendaftaran::where('status_antrian', 'menunggu_skrining')->first();
        $this->assertNotNull($pendaftaran);

        // 1. Perawat membuka antrean skrining
        $response = $this->actingAs($perawat)
            ->withSession(['role_aktif' => 'perawat', 'user_type' => 'pegawai'])
            ->get('/perawat/skrining');
        $response->assertStatus(200);
        $response->assertSee('Skrining TTV Poliklinik');
        $response->assertSee($pendaftaran->no_antrian);
        $response->assertSee('simrs-pagination-nav');

        // Verifikasi endpoint notifikasi real-time untuk perawat
        $notifRes = $this->actingAs($perawat)
            ->withSession(['role_aktif' => 'perawat', 'user_type' => 'pegawai'])
            ->getJson('/perawat/notifikasi');
        $notifRes->assertStatus(200);
        $notifRes->assertJsonStructure([
            'success',
            'count',
            'pasien' => [
                '*' => [
                    'id_pendaftaran',
                    'no_antrian',
                    'nama_pasien',
                    'no_rm',
                    'nama_poli',
                    'keluhan',
                    'waktu_daftar',
                    'url_skrining',
                ]
            ],
            'timestamp'
        ]);
        $this->assertGreaterThanOrEqual(1, $notifRes->json('count'));

        // 2. Perawat submit data tanda-tanda vital (TTV)
        $submitRes = $this->actingAs($perawat)
            ->withSession(['role_aktif' => 'perawat', 'user_type' => 'pegawai'])
            ->post('/perawat/skrining', [
                'id_pendaftaran'  => $pendaftaran->id_pendaftaran,
                'sistol'          => 120,
                'diastol'         => 80,
                'suhu_tubuh'      => 36.7,
                'berat_badan'     => 58.0,
                'tinggi_badan'    => 162.0,
                'denyut_nadi'     => 78,
                'catatan_perawat' => 'Pasien tampak stabil, keluhan pusing ringan.',
            ]);

        $submitRes->assertRedirect(route('perawat.skrining'));
        $submitRes->assertSessionHas('status_skrining');

        // 3. Pastikan database mencatat skrining dan status berubah ke 'menunggu_dokter'
        $this->assertDatabaseHas('screening_awal', [
            'id_pendaftaran' => $pendaftaran->id_pendaftaran,
            'id_perawat'     => $perawat->id_pegawai,
            'sistol'         => 120,
            'diastol'        => 80,
        ]);

        $this->assertDatabaseHas('pendaftaran', [
            'id_pendaftaran' => $pendaftaran->id_pendaftaran,
            'status_antrian' => 'menunggu_dokter',
        ]);
    }

    public function test_dokter_can_examine_patient_record_diagnoses_procedures_and_prescription()
    {
        $dokter = Pegawai::where('role', 'dokter')->first();
        $pendaftaran = \App\Models\Pendaftaran::where('status_antrian', 'menunggu_dokter')->first();
        $this->assertNotNull($pendaftaran);

        $layanan = \App\Models\Layanan::first();
        $obat = \App\Models\Obat::first();

        // 1. Dokter membuka antrean pelayanan dan memeriksa endpoint notifikasi
        $pelayananRes = $this->actingAs($dokter)
            ->withSession(['role_aktif' => 'dokter', 'user_type' => 'pegawai'])
            ->get('/dokter/pelayanan');
        $pelayananRes->assertStatus(200);
        $pelayananRes->assertSee('btn-topbar-notif-dokter');
        $pelayananRes->assertSee('sidebar-notif-count-dokter');

        $notifDokter = $this->actingAs($dokter)
            ->withSession(['role_aktif' => 'dokter', 'user_type' => 'pegawai'])
            ->getJson('/dokter/notifikasi');
        $notifDokter->assertStatus(200);
        $notifDokter->assertJsonStructure([
            'success',
            'count',
            'pasien' => [
                '*' => [
                    'id_pendaftaran',
                    'no_antrian',
                    'nama_pasien',
                    'no_rm',
                    'nama_poli',
                    'keluhan',
                    'ttv',
                    'waktu_daftar',
                    'url_periksa',
                ]
            ],
            'timestamp'
        ]);
        $this->assertGreaterThanOrEqual(1, $notifDokter->json('count'));

        // 2. Dokter membuka workstation pemeriksaan
        $viewRes = $this->actingAs($dokter)
            ->withSession(['role_aktif' => 'dokter', 'user_type' => 'pegawai'])
            ->get('/dokter/periksa/' . $pendaftaran->id_pendaftaran);
        $viewRes->assertStatus(200);
        $viewRes->assertSee('PASIEN POLIKLINIK SEDANG DIPERIKSA');
        $viewRes->assertSee($pendaftaran->no_antrian);

        // 2. Dokter simpan hasil pemeriksaan: Rekam Medis + Tindakan Medis + E-Resep
        $submitRes = $this->actingAs($dokter)
            ->withSession(['role_aktif' => 'dokter', 'user_type' => 'pegawai'])
            ->post('/dokter/periksa/' . $pendaftaran->id_pendaftaran, [
                'anamnesis'         => 'Keluhan pusing dan luka tergores sejak pagi.',
                'diagnosa_utama'    => 'Vulnus Laceratum Regio Manus',
                'kode_icd10'        => 'T14.1',
                'diagnosa_sekunder' => 'Cephalgia Tension',
                'catatan_edukasi'   => 'Jaga kebersihan luka, jangan basah selama 3 hari.',
                'tindakan'          => [$layanan->id_layanan],
                'obat'              => [
                    [
                        'id_obat' => $obat->id_obat,
                        'jumlah'  => 10,
                        'aturan'  => '3 x 1 tablet sesudah makan',
                    ]
                ],
                'catatan_resep'     => 'Habiskan antibiotik sesuai instruksi.',
            ]);

        $submitRes->assertRedirect(route('dokter.pelayanan'));
        $submitRes->assertSessionHas('status_pemeriksaan');

        // 3. Verifikasi rekam medis, tindakan, dan resep tersimpan
        $this->assertDatabaseHas('rekam_medis', [
            'id_pendaftaran' => $pendaftaran->id_pendaftaran,
            'id_dokter'      => $dokter->id_pegawai,
            'kode_icd10'     => 'T14.1',
        ]);

        $this->assertDatabaseHas('tindakan_medis', [
            'id_layanan' => $layanan->id_layanan,
        ]);

        $this->assertDatabaseHas('resep', [
            'id_pendaftaran'   => $pendaftaran->id_pendaftaran,
            'id_dokter'        => $dokter->id_pegawai,
            'status_resep'     => 'menunggu_pembayaran',
        ]);

        $this->assertDatabaseHas('resep_detail', [
            'id_obat' => $obat->id_obat,
            'jumlah'  => 10,
        ]);

        // Status antrean otomatis beralih ke 'menunggu_kasir'
        $this->assertDatabaseHas('pendaftaran', [
            'id_pendaftaran' => $pendaftaran->id_pendaftaran,
            'status_antrian' => 'menunggu_kasir',
        ]);
    }

    public function test_cashier_receives_doctor_actions_and_prescription_accurately()
    {
        $adminKasir = Pegawai::where('nik', '6301010004')->first();

        // 1. Kasir membuka halaman antrian kasir
        $response = $this->actingAs($adminKasir)
            ->withSession(['role_aktif' => 'admin_kasir', 'user_type' => 'pegawai', 'is_kasir_utama' => true])
            ->get('/antrian');
        $response->assertStatus(200);
        $response->assertSee('Daftar Antrian Pasien');

        // 2. Endpoint /antrian/data mengembalikan daftar pasien menunggu_kasir lengkap dengan obat & tindakan
        $dataRes = $this->actingAs($adminKasir)
            ->withSession(['role_aktif' => 'admin_kasir', 'user_type' => 'pegawai', 'is_kasir_utama' => true])
            ->getJson('/antrian/data');
        $dataRes->assertStatus(200);
        $dataRes->assertJsonStructure([
            'success',
            'antrian',
            'layanan',
        ]);

        $pendaftaranKasir = \App\Models\Pendaftaran::where('status_antrian', 'menunggu_kasir')->first();
        $this->assertNotNull($pendaftaranKasir);

        // 3. Pasien menunggu_kasir juga muncul di Dashboard Kasir
        $dashRes = $this->actingAs($adminKasir)
            ->withSession(['role_aktif' => 'admin_kasir', 'user_type' => 'pegawai', 'is_kasir_utama' => true])
            ->get('/dashboard');
        $dashRes->assertStatus(200);
        $dashRes->assertSee($pendaftaranKasir->no_antrian);
        $dashRes->assertSee('Layani Kasir');

        // 4. Kasir memproses transaksi pembayaran untuk pasien tersebut
        $bayarRes = $this->actingAs($adminKasir)
            ->withSession(['role_aktif' => 'admin_kasir', 'user_type' => 'pegawai', 'is_kasir_utama' => true])
            ->postJson('/antrian/bayar', [
                'id_pendaftaran' => $pendaftaranKasir->id_pendaftaran,
                'total_bayar'    => 100000,
                'total_layanan'  => 50000,
                'total_resep'    => 50000,
                'jasa_perawat'   => 50000,
                'jumlah_dibayar' => 100000,
                'metode_bayar'   => 'TUNAI',
            ]);
        $bayarRes->assertStatus(200);
        $bayarRes->assertJson(['success' => true]);
    }

    public function test_admin_can_register_new_resepsionis_staff_and_renders_badge()
    {
        $superAdmin = Pegawai::where('role', 'admin')->first();
        $uniqNik = '630101' . rand(2000, 9999);

        // 1. Admin daftarkan staf resepsionis baru
        $response = $this->actingAs($superAdmin)
            ->withSession(['role_aktif' => 'admin', 'user_type' => 'pegawai'])
            ->post('/admin/pengguna/tambah', [
                'nik'           => $uniqNik,
                'nip'           => '19980101 202301 2 001',
                'nama'          => 'Resepsionis Baru',
                'role'          => 'resepsionis',
                'jenis_kelamin' => 'P',
                'no_hp'         => '081299887766',
                'password'      => 'Password#123',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $this->assertDatabaseHas('pegawai', [
            'nik'  => $uniqNik,
            'nama' => 'Resepsionis Baru',
            'role' => 'resepsionis',
        ]);

        // 2. Buka halaman pengguna dan pastikan badge badge-role-resepsionis tampil
        $adminView = $this->actingAs($superAdmin)
            ->withSession(['role_aktif' => 'admin', 'user_type' => 'pegawai'])
            ->get('/admin/pengguna?q_pegawai=' . $uniqNik);

        $adminView->assertStatus(200);
        $adminView->assertSee('badge-role-resepsionis');
        $adminView->assertSee('RESEPSIONIS');
    }

    public function test_admin_can_register_new_dokter_with_poli_and_spesialisasi()
    {
        $superAdmin = Pegawai::where('role', 'admin')->first();
        $poliAnak = \App\Models\Poli::where('kode_poli', 'ANK')->first() ?? \App\Models\Poli::first();
        $uniqNik = '630101' . rand(2000, 9999);

        // 1. Admin daftarkan dokter spesialis anak baru
        $response = $this->actingAs($superAdmin)
            ->withSession(['role_aktif' => 'admin', 'user_type' => 'pegawai'])
            ->post('/admin/pengguna/tambah', [
                'nik'           => $uniqNik,
                'nip'           => '19910101 201801 1 008',
                'nama'          => 'dr. Zulfikar, Sp.A',
                'role'          => 'dokter',
                'jenis_kelamin' => 'L',
                'id_poli'       => $poliAnak->id_poli,
                'spesialisasi'  => 'Spesialis Neonatologi',
                'no_hp'         => '081299881122',
                'password'      => 'Password#123',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $this->assertDatabaseHas('pegawai', [
            'nik'          => $uniqNik,
            'nama'         => 'dr. Zulfikar, Sp.A',
            'role'         => 'dokter',
            'id_poli'      => $poliAnak->id_poli,
            'spesialisasi' => 'Spesialis Neonatologi',
        ]);

        // 2. Buka halaman kelola pengguna admin dan pastikan nama poli dan spesialisasi dirender
        $adminView = $this->actingAs($superAdmin)
            ->withSession(['role_aktif' => 'admin', 'user_type' => 'pegawai'])
            ->get('/admin/pengguna?q_pegawai=' . $uniqNik);

        $adminView->assertStatus(200);
        $adminView->assertSee('dr. Zulfikar, Sp.A');
        $adminView->assertSee($poliAnak->nama_poli);
        $adminView->assertSee('Spesialis Neonatologi');
    }

    public function test_admin_can_update_penempatan_dokter_via_atur_poli()
    {
        $superAdmin = Pegawai::where('role', 'admin')->first();
        $dokter = Pegawai::where('role', 'dokter')->first();
        $poliMata = \App\Models\Poli::where('kode_poli', 'MAT')->first() ?? \App\Models\Poli::first();

        // 1. Admin atur penempatan dokter ke Poli Mata via form modal Atur Poli
        $response = $this->actingAs($superAdmin)
            ->withSession(['role_aktif' => 'admin', 'user_type' => 'pegawai'])
            ->post("/admin/pengguna/{$dokter->id_pegawai}/atur-poli", [
                'id_poli'      => $poliMata->id_poli,
                'spesialisasi' => 'Spesialis Vitreoretina',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('pegawai', [
            'id_pegawai'   => $dokter->id_pegawai,
            'id_poli'      => $poliMata->id_poli,
            'spesialisasi' => 'Spesialis Vitreoretina',
        ]);

        // 2. Pastikan halaman pengguna menampilkan tombol Gear Aksi dan data penempatan baru
        $adminView = $this->actingAs($superAdmin)
            ->withSession(['role_aktif' => 'admin', 'user_type' => 'pegawai'])
            ->get('/admin/pengguna?q_pegawai=' . $dokter->nik);

        $adminView->assertStatus(200);
        $adminView->assertSee('btn-gear-action');
        $adminView->assertSee('fa-gear');
        $adminView->assertSee('dropdown-action-menu');
        $adminView->assertSee('Atur Poli');
        $adminView->assertSee('Reset Password');
        $adminView->assertSee($poliMata->nama_poli);
        $adminView->assertSee('Spesialis Vitreoretina');
    }

    public function test_admin_can_assign_dokters_in_master_poli()
    {
        $superAdmin = Pegawai::where('role', 'admin')->first();
        $dokter = Pegawai::where('role', 'dokter')->first();
        $poliGigi = \App\Models\Poli::where('kode_poli', 'GIG')->first() ?? \App\Models\Poli::first();

        // 1. Admin memperbarui poliklinik dan menugaskan dokter langsung dari Master Poli
        $response = $this->actingAs($superAdmin)
            ->withSession(['role_aktif' => 'admin', 'user_type' => 'pegawai'])
            ->put("/admin/poli/{$poliGigi->id_poli}", [
                'kode_poli'        => $poliGigi->kode_poli,
                'nama_poli'        => $poliGigi->nama_poli,
                'biaya_konsultasi' => $poliGigi->biaya_konsultasi,
                'dokter_ids'       => [$dokter->id_pegawai],
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.poli'));

        $this->assertEquals($poliGigi->id_poli, $dokter->fresh()->id_poli);

        // 2. Buka Master Poliklinik dan pastikan kolom DOKTER BERTUGAS menampilkan nama dokter
        $view = $this->actingAs($superAdmin)
            ->withSession(['role_aktif' => 'admin', 'user_type' => 'pegawai'])
            ->get(route('admin.poli'));

        $view->assertStatus(200);
        $view->assertSee('DOKTER BERTUGAS');
        $view->assertSee($dokter->nama);
    }
}
