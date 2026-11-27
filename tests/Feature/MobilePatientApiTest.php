<?php

namespace Tests\Feature;

use App\Models\Pasien;
use App\Models\Pendaftaran;
use App\Models\Poli;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MobilePatientApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_patient_can_login_with_username_or_nik_or_rm()
    {
        $pasien = Pasien::first();
        $pasien->username = 'pasien_mobile_1';
        $pasien->password_hash = Hash::make('password123');
        $pasien->save();

        // 1. Login dengan NIK
        $resNik = $this->postJson('/api/v1/auth/login', [
            'username' => $pasien->nik,
            'password' => 'password123',
        ]);
        $resNik->assertStatus(200);
        $this->assertEquals('pasien', $resNik->json('data.user_type'));

        // 2. Login dengan No. RM
        $resRm = $this->postJson('/api/v1/auth/login', [
            'username' => $pasien->rm,
            'password' => 'password123',
        ]);
        $resRm->assertStatus(200);

        // 3. Login dengan Username
        $resUser = $this->postJson('/api/v1/auth/login', [
            'username' => 'pasien_mobile_1',
            'password' => 'password123',
        ]);
        $resUser->assertStatus(200);
        $this->assertNotNull($resUser->json('data.token'));
    }

    public function test_patient_can_get_kartu_pasien_digital_via_api()
    {
        $pasien = Pasien::first();
        $token = $pasien->createToken('test-mobile-token', ['pasien'])->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/mobile/kartu-pasien');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data'    => [
                'no_rm'       => $pasien->rm,
                'nik'         => $pasien->nik,
                'nama_pasien' => $pasien->nama_pasien,
            ],
        ]);
    }

    public function test_patient_can_get_detail_riwayat_kunjungan_via_api()
    {
        $pasien = Pasien::first();
        $token = $pasien->createToken('test-mobile-token', ['pasien'])->plainTextToken;

        $poli = Poli::first();
        $pendaftaran = Pendaftaran::create([
            'no_pendaftaran' => 'REG-TEST-001',
            'id_pasien'      => $pasien->id_pasien,
            'id_poli'        => $poli->id_poli,
            'no_antrian'     => 'UMU-999',
            'tanggal_daftar' => now(),
            'status_antrian' => 'selesai',
            'cara_bayar'     => 'umum',
            'keluhan_awal'   => 'Keluhan batuk pilek',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/v1/mobile/riwayat-kunjungan/{$pendaftaran->id_pendaftaran}");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data'    => [
                'id_pendaftaran' => $pendaftaran->id_pendaftaran,
                'no_antrian'     => 'UMU-999',
            ],
        ]);
    }

    public function test_patient_can_get_komparasi_via_api()
    {
        $pasien = Pasien::first();
        $token = $pasien->createToken('test-mobile-token', ['pasien'])->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/mobile/komparasi');

        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));
    }

    public function test_patient_can_update_profile_and_password_via_api()
    {
        $pasien = Pasien::first();
        $token = $pasien->createToken('test-mobile-token', ['pasien'])->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->putJson('/api/v1/mobile/profil', [
                'nama_pasien'           => 'Nama Baru Pasien Mobile',
                'no_hp'                 => '081234567899',
                'alamat'                => 'Alamat Baru No. 123',
                'password'              => 'sandiBaru123',
                'password_confirmation' => 'sandiBaru123',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data'    => [
                'nama_pasien' => 'Nama Baru Pasien Mobile',
                'no_hp'       => '081234567899',
            ],
        ]);

        $pasien->refresh();
        $this->assertEquals('Nama Baru Pasien Mobile', $pasien->nama_pasien);
        $this->assertTrue(Hash::check('sandiBaru123', $pasien->password_hash));
    }

    public function test_patient_can_submit_password_reset_request_from_mobile_api()
    {
        $pasien = Pasien::first();

        $response = $this->postJson('/api/v1/auth/lupa-password', [
            'identitas'       => $pasien->rm,
            'tipe_permintaan' => 'password',
            'no_hp_darurat'   => '081999888777',
            'catatan'         => 'Lupa sandi aplikasi mobile',
        ]);

        $response->assertStatus(201);
        $this->assertTrue($response->json('success'));

        $this->assertDatabaseHas('permintaan_reset', [
            'nik'           => $pasien->nik,
            'nama'          => $pasien->nama_pasien,
            'no_hp'         => '081999888777',
            'tipe_pengguna' => 'pasien',
            'status'        => 'menunggu',
        ]);
    }
}
