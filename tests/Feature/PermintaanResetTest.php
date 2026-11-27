<?php

namespace Tests\Feature;

use App\Models\Pasien;
use App\Models\Pegawai;
use App\Models\PermintaanReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PermintaanResetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_login_page_renders_reset_request_modal_link()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Lupa Password / Terkendala 2FA?');
        $response->assertSee('Hubungi Admin RS');
        $response->assertSee('modal-permintaan-reset');
        $response->assertSee('Bantuan Akun');
        $response->assertSee('Reset Akses');
    }

    public function test_two_factor_page_renders_reset_2fa_request_modal_link()
    {
        $pegawai = Pegawai::where('role', 'admin')->first();
        $pegawai->two_factor_enabled = true;
        $pegawai->two_factor_secret = 'JBSWY3DPEHPK3PXP';
        $pegawai->save();

        $response = $this->withSession([
            '2fa_pegawai_id' => $pegawai->id_pegawai,
            '2fa_nama'       => $pegawai->nama,
            '2fa_role'       => $pegawai->role,
        ])->get('/login/2fa');

        $response->assertStatus(200);
        $response->assertSee('Kehilangan Akses 2FA / Ganti HP?');
        $response->assertSee('modal-permintaan-reset');
    }

    public function test_staff_can_submit_password_and_2fa_reset_request()
    {
        $dokter = Pegawai::where('role', 'dokter')->first();

        $response = $this->post('/bantuan/permintaan-reset', [
            'identitas'       => $dokter->nik,
            'tipe_permintaan' => 'keduanya',
            'no_hp_darurat'   => '081299998888',
            'catatan'         => 'HP saya hilang, tidak bisa scan Google Authenticator dan lupa sandi',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('permintaan_reset', [
            'tipe_pengguna'   => 'pegawai',
            'id_pengguna'     => $dokter->id_pegawai,
            'nik'             => $dokter->nik,
            'nama'            => $dokter->nama,
            'no_hp'           => '081299998888',
            'tipe_permintaan' => 'keduanya',
            'status'          => 'menunggu',
        ]);
    }

    public function test_patient_can_submit_password_reset_request_using_rm_or_username()
    {
        $pasien = Pasien::first();

        $response = $this->post('/bantuan/permintaan-reset', [
            'identitas'       => $pasien->rm,
            'tipe_permintaan' => 'password',
            'catatan'         => 'Saya lupa kata sandi akun portal pasien',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('permintaan_reset', [
            'tipe_pengguna'   => 'pasien',
            'id_pengguna'     => $pasien->id_pasien,
            'nik'             => $pasien->nik,
            'nama'            => $pasien->nama_pasien,
            'tipe_permintaan' => 'password',
            'status'          => 'menunggu',
        ]);
    }

    public function test_submit_reset_fails_if_identity_not_found()
    {
        $response = $this->post('/bantuan/permintaan-reset', [
            'identitas'       => 'NIK-TIDAK-ADA-999999',
            'tipe_permintaan' => 'password',
        ]);

        $response->assertSessionHasErrors('nik');
        $this->assertDatabaseMissing('permintaan_reset', [
            'nik' => 'NIK-TIDAK-ADA-999999',
        ]);
    }

    public function test_submit_reset_prevents_duplicate_pending_requests()
    {
        $dokter = Pegawai::where('role', 'dokter')->first();

        // Permintaan pertama
        $this->post('/bantuan/permintaan-reset', [
            'identitas'       => $dokter->nik,
            'tipe_permintaan' => 'password',
        ]);

        $this->assertEquals(1, PermintaanReset::where('nik', $dokter->nik)->count());

        // Permintaan kedua saat yang pertama masih status 'menunggu'
        $response2 = $this->post('/bantuan/permintaan-reset', [
            'identitas'       => $dokter->nik,
            'tipe_permintaan' => '2fa',
        ]);

        $response2->assertSessionHas('status');
        $this->assertEquals(1, PermintaanReset::where('nik', $dokter->nik)->count());
    }

    public function test_admin_can_view_reset_requests_in_dedicated_page()
    {
        $admin = Pegawai::where('role', 'admin')->first();
        $dokter = Pegawai::where('role', 'dokter')->first();

        PermintaanReset::create([
            'tipe_pengguna'   => 'pegawai',
            'id_pengguna'     => $dokter->id_pegawai,
            'nik'             => $dokter->nik,
            'nama'            => $dokter->nama,
            'no_hp'           => '08123456789',
            'tipe_permintaan' => '2fa',
            'catatan'         => 'Ganti HP baru',
            'status'          => 'menunggu',
        ]);

        // 1. Cek di menu terpisah /admin/permintaan-reset
        $response = $this->actingAs($admin)
            ->withSession(['role_aktif' => 'admin'])
            ->get('/admin/permintaan-reset');

        $response->assertStatus(200);
        $response->assertSee('Permintaan Reset Password');
        $response->assertSee($dokter->nama);
        $response->assertSee('Ganti HP baru');
        $response->assertSee('Proses & Reset', false);

        // 2. Cek bahwa di halaman /admin/pengguna terdapat link card menuju /admin/permintaan-reset
        $penggunaResponse = $this->actingAs($admin)
            ->withSession(['role_aktif' => 'admin'])
            ->get('/admin/pengguna');
        $penggunaResponse->assertStatus(200);
        $penggunaResponse->assertSee(route('admin.reset'));
    }

    public function test_admin_can_process_reset_request_for_staff_and_generate_new_password_and_disable_2fa()
    {
        $admin = Pegawai::where('role', 'admin')->first();
        $dokter = Pegawai::where('role', 'dokter')->first();
        $dokter->two_factor_enabled = true;
        $dokter->two_factor_secret = 'OLD_SECRET_KEY';
        $dokter->save();

        $req = PermintaanReset::create([
            'tipe_pengguna'   => 'pegawai',
            'id_pengguna'     => $dokter->id_pegawai,
            'nik'             => $dokter->nik,
            'nama'            => $dokter->nama,
            'no_hp'           => '081233445566',
            'tipe_permintaan' => 'keduanya',
            'status'          => 'menunggu',
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['role_aktif' => 'admin'])
            ->post("/admin/pengguna/reset-request/{$req->id_permintaan}/proses");

        $response->assertRedirect();
        $response->assertSessionHas('status_reset_sukses');

        $req->refresh();
        $this->assertEquals('selesai', $req->status);
        $this->assertNotNull($req->password_baru_plain);
        $this->assertEquals($admin->id_pegawai, $req->diproses_oleh);

        $dokter->refresh();
        // 2FA harus dinonaktifkan
        $this->assertFalse((bool)$dokter->two_factor_enabled);
        $this->assertNull($dokter->two_factor_secret);
        // Password baru harus cocok dengan hash
        $this->assertTrue(Hash::check($req->password_baru_plain, $dokter->password_hash));

        // Flash session harus memuat link WhatsApp
        $sessionData = session('status_reset_sukses');
        $this->assertStringContainsString('https://wa.me/6281233445566', $sessionData['wa_url']);
    }

    public function test_admin_can_reject_reset_request()
    {
        $admin = Pegawai::where('role', 'admin')->first();
        $dokter = Pegawai::where('role', 'dokter')->first();

        $req = PermintaanReset::create([
            'tipe_pengguna'   => 'pegawai',
            'id_pengguna'     => $dokter->id_pegawai,
            'nik'             => $dokter->nik,
            'nama'            => $dokter->nama,
            'tipe_permintaan' => 'password',
            'status'          => 'menunggu',
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['role_aktif' => 'admin'])
            ->post("/admin/pengguna/reset-request/{$req->id_permintaan}/tolak", [
                'catatan_admin' => 'Data tidak terverifikasi.',
            ]);

        $response->assertRedirect();

        $req->refresh();
        $this->assertEquals('ditolak', $req->status);
        $this->assertEquals($admin->id_pegawai, $req->diproses_oleh);
    }

    public function test_admin_can_poll_reset_notification_api()
    {
        $admin = Pegawai::where('role', 'admin')->first();

        PermintaanReset::create([
            'tipe_pengguna'   => 'pegawai',
            'nik'             => '6301010001',
            'nama'            => 'Staf Demo',
            'tipe_permintaan' => '2fa',
            'status'          => 'menunggu',
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['role_aktif' => 'admin'])
            ->getJson('/admin/pengguna/notifikasi-reset');

        $response->assertStatus(200);
        $response->assertJson([
            'count'   => 1,
            'terbaru' => [
                'nama'            => 'Staf Demo',
                'nik'             => '6301010001',
                'tipe_permintaan' => 'Reset 2FA',
            ],
        ]);
    }
}
