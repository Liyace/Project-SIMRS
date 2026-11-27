<?php

namespace Tests\Feature;

use App\Models\Pasien;
use App\Models\Pegawai;
use App\Models\Pendaftaran;
use App\Models\Poli;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoketDanPasienTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_loket_staff_login_redirects_to_loket_pendaftaran()
    {
        $response = $this->post('/login', [
            'nik'      => '6301010006',
            'password' => '12345',
        ]);

        $response->assertRedirect('/loket/pendaftaran');
        $this->assertEquals('loket', session('role_aktif'));
    }

    public function test_non_loket_staff_cannot_access_loket_pendaftaran()
    {
        $perawat = Pegawai::where('role', 'perawat')->first();

        $response = $this->actingAs($perawat)
            ->withSession(['role_aktif' => 'perawat'])
            ->get('/loket/pendaftaran');

        $response->assertRedirect();
    }

    public function test_loket_pendaftaran_renders_successfully()
    {
        $loket = Pegawai::where('role', 'loket')->first();

        $response = $this->actingAs($loket)
            ->withSession(['role_aktif' => 'loket'])
            ->get('/loket/pendaftaran');

        $response->assertStatus(200);
        $response->assertSee('Loket Pendaftaran Pasien');
        $response->assertSee('Form Registrasi Walk-In');
        $response->assertSee('Pasien Lama (Ada RM)');
        $response->assertSee('Pasien Baru (Buat RM)');
        $response->assertSee('Antrean Pasien Hari Ini');
    }

    public function test_loket_ajax_cari_pasien_returns_json()
    {
        $loket = Pegawai::where('role', 'loket')->first();
        $pasien = Pasien::first();

        // 1. Pencarian dengan keyword nama/RM
        $response = $this->actingAs($loket)
            ->withSession(['role_aktif' => 'loket'])
            ->getJson('/loket/cari-pasien?q=' . urlencode(substr($pasien->nama_pasien, 0, 4)));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id_pasien'   => $pasien->id_pasien,
            'rm'          => $pasien->rm,
            'nama_pasien' => $pasien->nama_pasien,
        ]);

        // 2. Pencarian tanpa query (q='') otomatis mengembalikan daftar pasien
        $responseEmpty = $this->actingAs($loket)
            ->withSession(['role_aktif' => 'loket'])
            ->getJson('/loket/cari-pasien?q=');

        $responseEmpty->assertStatus(200);
        $this->assertGreaterThan(0, count($responseEmpty->json()));
    }

    public function test_loket_can_register_existing_patient_walk_in()
    {
        $loket = Pegawai::where('role', 'loket')->first();
        $pasien = Pasien::first();
        $poli = Poli::first();
        $dokter = Pegawai::where('role', 'dokter')->first();

        $response = $this->actingAs($loket)
            ->withSession(['role_aktif' => 'loket'])
            ->post('/loket/daftar', [
                'tipe_pasien'  => 'lama',
                'id_pasien'    => $pasien->id_pasien,
                'id_poli'      => $poli->id_poli,
                'id_dokter'    => $dokter->id_pegawai,
                'cara_bayar'   => 'umum',
                'keluhan_awal' => 'Pusing dan badan lemas sejak kemarin',
            ]);

        $response->assertRedirect('/loket/pendaftaran');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pendaftaran', [
            'id_pasien'      => $pasien->id_pasien,
            'id_poli'        => $poli->id_poli,
            'cara_bayar'     => 'umum',
            'status_antrian' => 'menunggu_skrining',
            'keluhan_awal'   => 'Pusing dan badan lemas sejak kemarin',
        ]);
    }

    public function test_loket_can_register_new_patient_and_auto_generate_rm()
    {
        $loket = Pegawai::where('role', 'loket')->first();
        $poli = Poli::first();

        $response = $this->actingAs($loket)
            ->withSession(['role_aktif' => 'loket'])
            ->post('/loket/daftar', [
                'tipe_pasien'   => 'baru',
                'nik'           => '6301999900010009',
                'nama_pasien'   => 'Pasien Baru Walk-In',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1998-05-15',
                'no_hp'         => '081299887766',
                'alamat'        => 'Jl. Lambung Mangkurat No. 45',
                'id_poli'       => $poli->id_poli,
                'cara_bayar'    => 'bpjs',
                'keluhan_awal'  => 'Batuk kering dan demam tinggi',
            ]);

        $response->assertRedirect('/loket/pendaftaran');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pasien', [
            'nik'         => '6301999900010009',
            'nama_pasien' => 'Pasien Baru Walk-In',
        ]);

        $newPasien = Pasien::where('nik', '6301999900010009')->first();
        $this->assertNotNull($newPasien->rm);

        $this->assertDatabaseHas('pendaftaran', [
            'id_pasien'      => $newPasien->id_pasien,
            'id_poli'        => $poli->id_poli,
            'cara_bayar'     => 'bpjs',
            'status_antrian' => 'menunggu_skrining',
        ]);
    }

    public function test_loket_thermal_karcis_renders_successfully()
    {
        $loket = Pegawai::where('role', 'loket')->first();
        $pendaftaran = Pendaftaran::where('status_antrian', 'menunggu_skrining')->first();

        $response = $this->actingAs($loket)
            ->withSession(['role_aktif' => 'loket'])
            ->get('/loket/karcis/' . $pendaftaran->id_pendaftaran);

        $response->assertStatus(200);
        $response->assertSee('NOMOR ANTREAN POLIKLINIK');
        $response->assertSee($pendaftaran->no_antrian);
        $response->assertSee('Ruang Skrining TTV Perawat');
    }

    public function test_patient_can_book_clinic_appointment_online()
    {
        $pasien = Pasien::create([
            'rm'            => 'RM-8888',
            'nik'           => '6371888899990002',
            'username'      => 'pasienonline',
            'nama_pasien'   => 'Pasien Online Booking',
            'password_hash' => Hash::make('secret123'),
        ]);
        $poli = Poli::first();

        // Login sebagai pasien
        $response = $this->post('/login', [
            'nik'      => 'pasienonline',
            'password' => 'secret123',
        ]);
        $response->assertRedirect('/pasien/dashboard');

        // Reservasi online
        $bookResponse = $this->withSession([
            'user_type' => 'pasien',
            'id_pasien' => $pasien->id_pasien,
        ])->post('/pasien/daftar-antrean', [
            'id_poli'      => $poli->id_poli,
            'cara_bayar'   => 'umum',
            'keluhan_awal' => 'Sakit kepala sebelah kiri dan mata silau',
        ]);

        $bookResponse->assertRedirect('/pasien/dashboard');
        $bookResponse->assertSessionHas('success');

        $this->assertDatabaseHas('pendaftaran', [
            'id_pasien'      => $pasien->id_pasien,
            'id_poli'        => $poli->id_poli,
            'status_antrian' => 'menunggu_skrining',
        ]);
    }

    public function test_patient_registered_at_loket_or_online_appears_in_nurse_screening_and_notification()
    {
        $pasien = Pasien::first();
        $poli = Poli::first();

        // 1. Loket daftarkan pasien
        $pendaftaran = Pendaftaran::create([
            'no_pendaftaran' => 'REG-' . date('Ymd') . '-999',
            'id_pasien'      => $pasien->id_pasien,
            'id_poli'        => $poli->id_poli,
            'no_antrian'     => 'TES-999',
            'tanggal_daftar' => now(),
            'cara_bayar'     => 'umum',
            'status_antrian' => 'menunggu_skrining',
            'keluhan_awal'   => 'Gejala flu berat',
        ]);

        // 2. Perawat buka halaman skrining dengan memilih pasien via param pilih
        $perawat = Pegawai::where('role', 'perawat')->first();

        $nurseQueueResponse = $this->actingAs($perawat)
            ->withSession(['role_aktif' => 'perawat'])
            ->get('/perawat/skrining?pilih=' . $pendaftaran->id_pendaftaran);

        $nurseQueueResponse->assertStatus(200);
        $nurseQueueResponse->assertSee('TES-999');
        $nurseQueueResponse->assertSee($pasien->nama_pasien);

        // 3. Perawat cek endpoint notifikasi
        $nurseNotifResponse = $this->actingAs($perawat)
            ->withSession(['role_aktif' => 'perawat'])
            ->getJson('/perawat/notifikasi');

        $nurseNotifResponse->assertStatus(200);
        $nurseNotifResponse->assertJsonFragment([
            'no_antrian'  => 'TES-999',
            'nama_pasien' => $pasien->nama_pasien,
        ]);
    }

    public function test_loket_can_poll_notifikasi()
    {
        $loket = Pegawai::where('role', 'loket')->first();

        $res = $this->actingAs($loket)
            ->withSession(['role_aktif' => 'loket', 'user_type' => 'pegawai'])
            ->getJson('/loket/notifikasi');

        $res->assertStatus(200);
        $res->assertJsonStructure([
            'success',
            'total_hari_ini',
            'menunggu_skrining',
            'count',
            'pendaftaran',
            'timestamp',
        ]);
    }

    public function test_pasien_can_poll_notifikasi()
    {
        $pasien = Pasien::first();
        $poli = Poli::first();

        // Buat antrean untuk pasien
        $pendaftaran = Pendaftaran::create([
            'no_pendaftaran' => 'REG-' . date('Ymd') . '-777',
            'id_pasien'      => $pasien->id_pasien,
            'id_poli'        => $poli->id_poli,
            'no_antrian'     => 'UMU-777',
            'tanggal_daftar' => now(),
            'cara_bayar'     => 'umum',
            'status_antrian' => 'menunggu_skrining',
        ]);

        $res = $this->withSession([
            'user_type' => 'pasien',
            'id_pasien' => $pasien->id_pasien,
            'nama_pasien' => $pasien->nama_pasien,
        ])->getJson('/pasien/notifikasi');

        $res->assertStatus(200);
        $res->assertJsonFragment([
            'success'     => true,
            'ada_antrean' => true,
            'no_antrian'  => 'UMU-777',
            'status_antrian' => 'menunggu_skrining',
        ]);
    }

    public function test_walk_in_patient_registered_at_loket_has_no_password_and_cannot_login()
    {
        $loket = Pegawai::where('role', 'loket')->first();
        $poli = Poli::first();

        // 1. Loket mendaftarkan pasien baru fisik
        $this->actingAs($loket)
            ->withSession(['role_aktif' => 'loket'])
            ->post('/loket/daftar', [
                'tipe_pasien'   => 'baru',
                'nik'           => '6301888877770001',
                'nama_pasien'   => 'Budi Walk-In Santoso',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1995-01-01',
                'no_hp'         => '081234567890',
                'alamat'        => 'Jl. Loket Fisik No. 1',
                'id_poli'       => $poli->id_poli,
                'cara_bayar'    => 'umum',
            ]);

        $pasien = Pasien::where('nik', '6301888877770001')->first();
        $this->assertNotNull($pasien);
        $this->assertNull($pasien->password_hash);
        $this->assertNull($pasien->username);

        // 2. Pasien walk-in mencoba login langsung di web
        \Illuminate\Support\Facades\Auth::logout();
        session()->flush();

        $loginRes = $this->post('/login', [
            'nik'      => '6301888877770001',
            'password' => '123456',
        ]);

        $loginRes->assertSessionHasErrors('nik');
        $this->assertGuest();
        $this->assertNull(session('user_type'));
    }

    public function test_walk_in_patient_can_self_activate_portal_account_preserving_rm()
    {
        $loket = Pegawai::where('role', 'loket')->first();
        $poli = Poli::first();

        // 1. Loket mendaftarkan pasien fisik
        $this->actingAs($loket)
            ->withSession(['role_aktif' => 'loket'])
            ->post('/loket/daftar', [
                'tipe_pasien'   => 'baru',
                'nik'           => '6301777766660002',
                'nama_pasien'   => 'Siti Walk-In Lestari',
                'jenis_kelamin' => 'P',
                'id_poli'       => $poli->id_poli,
                'cara_bayar'    => 'umum',
            ]);

        $pasien = Pasien::where('nik', '6301777766660002')->first();
        $existingRm = $pasien->rm;
        $this->assertNotNull($existingRm);
        $this->assertNull($pasien->password_hash);

        // 2. Pasien secara mandiri mengakses /register untuk aktivasi akun portal
        \Illuminate\Support\Facades\Auth::logout();
        session()->flush();

        $registerRes = $this->post('/register', [
            'username'              => 'sitilestari',
            'nik'                   => '6301777766660002',
            'nama'                  => 'Siti Walk-In Lestari Updated',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'jenis_kelamin'         => 'P',
        ]);

        $registerRes->assertRedirect('/login');
        $registerRes->assertSessionHas('success');

        // Pastikan nomor RM tetap sama (tidak terduplikasi)
        $pasien->refresh();
        $this->assertEquals($existingRm, $pasien->rm);
        $this->assertEquals('sitilestari', $pasien->username);
        $this->assertNotNull($pasien->password_hash);
        $this->assertTrue(Hash::check('password123', $pasien->password_hash));

        // 3. Pasien login menggunakan akun yang baru diaktivasi
        $loginRes = $this->post('/login', [
            'nik'      => 'sitilestari',
            'password' => 'password123',
        ]);

        $loginRes->assertRedirect(route('pasien.dashboard'));
        $this->assertEquals('pasien', session('user_type'));
        $this->assertEquals($pasien->id_pasien, session('id_pasien'));
    }

    public function test_cannot_register_duplicate_when_portal_account_already_active()
    {
        $pasien = Pasien::create([
            'rm'            => 'RM-7711',
            'nik'           => '6301555544440003',
            'username'      => 'pasienaktif',
            'nama_pasien'   => 'Pasien Aktif',
            'password_hash' => Hash::make('password123'),
        ]);

        $res = $this->post('/register', [
            'username'              => 'pasienbaru2',
            'nik'                   => '6301555544440003',
            'nama'                  => 'Mencoba Dobel Akun',
            'password'              => 'secret999',
            'password_confirmation' => 'secret999',
        ]);

        $res->assertSessionHasErrors('nik');
    }

    public function test_patient_can_view_detailed_medical_record_history()
    {
        $pasien = Pasien::first();
        $pendaftaran = Pendaftaran::where('id_pasien', $pasien->id_pasien)->first();

        $response = $this->withSession([
            'user_type'   => 'pasien',
            'id_pasien'   => $pasien->id_pasien,
            'nama_pasien' => $pasien->nama_pasien,
            'rm'          => $pasien->rm,
        ])->get("/pasien/riwayat/{$pendaftaran->id_pendaftaran}");

        $response->assertStatus(200);
        $response->assertSee('Rekam Pelayanan Medis');
        $response->assertSee('Skrining Tanda Vital');
        $response->assertSee('Diagnosa');
        $response->assertSee('Rekam Medis Dokter');
        $response->assertSee('Resep');
        $response->assertSee('Obat Farmasi');
        $response->assertSee('Pembayaran');
        $response->assertSee('Kuitansi Kasir');
    }

    public function test_patient_cannot_view_other_patients_medical_record()
    {
        $pasien1 = Pasien::first();
        $pasien2 = Pasien::skip(1)->first();
        $pendaftaran2 = Pendaftaran::where('id_pasien', $pasien2->id_pasien)->first();

        // Pasien 1 mencoba mengakses rekam medis pasien 2
        $response = $this->withSession([
            'user_type'   => 'pasien',
            'id_pasien'   => $pasien1->id_pasien,
            'nama_pasien' => $pasien1->nama_pasien,
            'rm'          => $pasien1->rm,
        ])->get("/pasien/riwayat/{$pendaftaran2->id_pendaftaran}");

        $response->assertStatus(404);
    }

    public function test_patient_can_print_kartu_berobat_mandiri()
    {
        $pasien = Pasien::first();

        $response = $this->withSession([
            'user_type'   => 'pasien',
            'id_pasien'   => $pasien->id_pasien,
            'nama_pasien' => $pasien->nama_pasien,
            'rm'          => $pasien->rm,
        ])->get('/pasien/kartu');

        $response->assertStatus(200);
        $response->assertSee('RUMAH SAKIT DELTA');
        $response->assertSee('Kartu Identitas Berobat Pasien');
        $response->assertSee($pasien->rm);
        $response->assertSee($pasien->nama_pasien);
    }

    public function test_patient_can_access_kuitansi_pembayaran()
    {
        $pasien = Pasien::first();
        $pendaftaran = Pendaftaran::where('id_pasien', $pasien->id_pasien)
            ->whereHas('transaksi')
            ->first();

        $response = $this->withSession([
            'user_type'   => 'pasien',
            'id_pasien'   => $pasien->id_pasien,
            'nama_pasien' => $pasien->nama_pasien,
            'rm'          => $pasien->rm,
        ])->get("/pasien/riwayat/{$pendaftaran->id_pendaftaran}/kuitansi");

        $response->assertStatus(200);
        $response->assertSee('Kuitansi Pembayaran Pasien Rawat Jalan');
        $response->assertSee($pendaftaran->transaksi->no_invoice);
        $response->assertSee('LUNAS');
    }

    public function test_patient_can_access_riwayat_semua_list()
    {
        $pasien = Pasien::first();

        $response = $this->withSession([
            'user_type'   => 'pasien',
            'id_pasien'   => $pasien->id_pasien,
            'nama_pasien' => $pasien->nama_pasien,
            'rm'          => $pasien->rm,
        ])->get('/pasien/riwayat');

        $response->assertStatus(200);
        $response->assertSee('Riwayat Seluruh Kunjungan Berobat');
        $response->assertSee('Bandingkan Riwayat Pengobatan');
    }

    public function test_patient_can_access_komparasi_riwayat()
    {
        $pasien = Pasien::first();

        $response = $this->withSession([
            'user_type'   => 'pasien',
            'id_pasien'   => $pasien->id_pasien,
            'nama_pasien' => $pasien->nama_pasien,
            'rm'          => $pasien->rm,
        ])->get('/pasien/komparasi');

        $response->assertStatus(200);
        $response->assertSee('Perbandingan Riwayat Berobat');
        $response->assertSee('Bandingkan');
    }

    public function test_patient_dashboard_renders_balanced_layout_with_guidance_and_history_link()
    {
        // Pasien tanpa antrean aktif hari ini (form booking + panduan alur layanan poliklinik)
        $pasien = Pasien::create([
            'rm'            => 'RM-9901',
            'nik'           => '6371990100010001',
            'username'      => 'pasienlayout',
            'nama_pasien'   => 'Pasien Cek Layout',
            'password_hash' => Hash::make('secret123'),
        ]);

        $response = $this->withSession([
            'user_type'   => 'pasien',
            'id_pasien'   => $pasien->id_pasien,
            'nama_pasien' => $pasien->nama_pasien,
            'rm'          => $pasien->rm,
        ])->get('/pasien/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Daftar Antrean Berobat Hari Ini');
        $response->assertSee('Panduan Alur Pelayanan Poliklinik');
        $response->assertSee('Skrining Tanda Vital');
        $response->assertSee('Identitas Medis Pasien');
        $response->assertSee('Riwayat Rekam Medis Pasien');
        // Pastikan modal pratinjau kartu pasien & tombol cetak tersedia
        $response->assertSee('modal-kartu-pasien');
        $response->assertSee('Pratinjau Kartu Berobat Pasien');
        $response->assertSee('Sisi Depan (Identitas)');
        $response->assertSee('Sisi Belakang (Ketentuan)');
        $response->assertSee('Cetak Kartu Berobat');
        // Pastikan modal edit profil tersedia
        $response->assertSee('modal-edit-profil-pasien');
        $response->assertSee('Pengaturan Profil');
        $response->assertSee('Akun Pasien');
        $response->assertSee('Foto Profil Otomatis');
    }

    public function test_patient_can_update_profile_and_contact_info_without_changing_rm_nik_gender()
    {
        $pasien = Pasien::create([
            'rm'            => 'RM-7722',
            'nik'           => '6371772200010001',
            'username'      => 'pasienedit',
            'nama_pasien'   => 'Siti Awal',
            'jenis_kelamin' => 'P',
            'password_hash' => Hash::make('secret123'),
        ]);

        $response = $this->withSession([
            'user_type'   => 'pasien',
            'id_pasien'   => $pasien->id_pasien,
            'nama_pasien' => $pasien->nama_pasien,
            'rm'          => $pasien->rm,
        ])->post('/pasien/profil', [
            'nama_pasien' => 'Siti Aminah Baru',
            'username'    => 'sitiaminahbaru',
            'no_hp'       => '081299887766',
            'alamat'      => 'Jl. Pangeran Hidayatullah No. 12',
            // Mencoba ubah RM / NIK / Gender via form (harus diabaikan / tidak berubah)
            'rm'          => 'RM-HACKED',
            'nik'         => 'HACKED-NIK',
            'jenis_kelamin' => 'L',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $pasien->refresh();
        $this->assertEquals('Siti Aminah Baru', $pasien->nama_pasien);
        $this->assertEquals('sitiaminahbaru', $pasien->username);
        $this->assertEquals('081299887766', $pasien->no_hp);
        $this->assertEquals('Jl. Pangeran Hidayatullah No. 12', $pasien->alamat);
        // RM, NIK, dan jenis kelamin tetap aman dan sesuai rekam medis semula
        $this->assertEquals('RM-7722', $pasien->rm);
        $this->assertEquals('6371772200010001', $pasien->nik);
        $this->assertEquals('P', $pasien->jenis_kelamin);
    }

    public function test_patient_can_change_password_when_provided()
    {
        $pasien = Pasien::create([
            'rm'            => 'RM-7733',
            'nik'           => '6371773300010001',
            'username'      => 'pasienpw',
            'nama_pasien'   => 'Budi Password',
            'password_hash' => Hash::make('oldpassword'),
        ]);

        $response = $this->withSession([
            'user_type'   => 'pasien',
            'id_pasien'   => $pasien->id_pasien,
            'nama_pasien' => $pasien->nama_pasien,
            'rm'          => $pasien->rm,
        ])->post('/pasien/profil', [
            'nama_pasien'           => 'Budi Password',
            'password'              => 'newsecurepass',
            'password_confirmation' => 'newsecurepass',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $pasien->refresh();
        $this->assertTrue(Hash::check('newsecurepass', $pasien->password_hash));
    }
}
