<?php

namespace Tests\Feature;

use App\Models\Layanan;
use App\Models\Obat;
use App\Models\Pasien;
use App\Models\Pegawai;
use App\Models\Pendaftaran;
use App\Models\Poli;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimrsApiFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_full_simrs_end_to_end_journey()
    {
        // 1. TEST LOGIN PEGAWAI (DOKTER)
        $loginRes = $this->postJson('/api/v1/auth/login', [
            'username' => '6301010002', // dr. Budi Santoso
            'password' => '12345',
        ]);
        $loginRes->assertStatus(200);
        $this->assertTrue($loginRes->json('success'));
        $dokterToken = $loginRes->json('data.token');

        // 2. TEST LOGIN PEGAWAI (KASIR)
        $loginKasir = $this->postJson('/api/v1/auth/login', [
            'username' => '6301010004',
            'password' => '12345',
        ]);
        $loginKasir->assertStatus(200);
        $kasirToken = $loginKasir->json('data.token');

        // 3. TEST LOGIN PEGAWAI (APOTEKER)
        $loginApoteker = $this->postJson('/api/v1/auth/login', [
            'username' => '6301010005',
            'password' => '12345',
        ]);
        $loginApoteker->assertStatus(200);
        $apotekerToken = $loginApoteker->json('data.token');

        // 4. TEST REGISTER PASIEN BARU DARI MOBILE APP
        $nikTest = '6371999999990001';
        $regPasienRes = $this->postJson('/api/v1/auth/register-pasien', [
            'nik'           => $nikTest,
            'nama_pasien'   => 'Pasien Testing Mobile',
            'password'      => 'rahasia123',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '1998-08-17',
            'no_hp'         => '081299998888',
            'alamat'        => 'Jl. Testing Mobile No. 1',
        ]);
        $regPasienRes->assertStatus(201);
        $pasienToken = $regPasienRes->json('data.token');
        $idPasien = $regPasienRes->json('data.user.id_pasien');

        // 5. TEST DAFTAR POLI ONLINE OLEH PASIEN
        $poli = Poli::first();
        $daftarOnlineRes = $this->withHeader('Authorization', "Bearer {$pasienToken}")
            ->postJson('/api/v1/mobile/daftar-online', [
                'id_poli'      => $poli->id_poli,
                'keluhan_awal' => 'Batuk dan radang tenggorokan',
                'cara_bayar'   => 'umum',
            ]);
        $daftarOnlineRes->assertStatus(201);
        $idPendaftaran = $daftarOnlineRes->json('data.id_pendaftaran');

        // 6. TEST INPUT SCREENING PERAWAT
        $screeningRes = $this->withHeader('Authorization', "Bearer {$dokterToken}")
            ->postJson('/api/v1/pemeriksaan/screening', [
                'id_pendaftaran'  => $idPendaftaran,
                'sistol'          => 120,
                'diastol'         => 80,
                'suhu_tubuh'      => 37.2,
                'berat_badan'     => 60.5,
                'tinggi_badan'    => 165.0,
                'denyut_nadi'     => 80,
                'catatan_perawat' => 'Pasien tampak stabil',
            ]);
        $screeningRes->assertStatus(200);

        // 7. TEST REKAM MEDIS & TINDAKAN DOKTER
        $layanan = Layanan::first();
        $rekamMedisRes = $this->withHeader('Authorization', "Bearer {$dokterToken}")
            ->postJson('/api/v1/pemeriksaan/rekam-medis', [
                'id_pendaftaran'  => $idPendaftaran,
                'anamnesis'       => 'Batuk berdahak 3 hari',
                'diagnosa_utama'  => 'Faringitis Akut',
                'kode_icd10'      => 'J02.9',
                'tindakan'        => [
                    ['id_layanan' => $layanan->id_layanan, 'qty' => 1],
                ],
            ]);
        $rekamMedisRes->assertStatus(200);

        // 8. TEST TERBITKAN E-RESEP DOKTER
        $obat = Obat::first();
        $resepRes = $this->withHeader('Authorization', "Bearer {$dokterToken}")
            ->postJson('/api/v1/pemeriksaan/resep', [
                'id_pendaftaran' => $idPendaftaran,
                'catatan_dokter' => 'Minum sesudah makan',
                'items'          => [
                    [
                        'id_obat'      => $obat->id_obat,
                        'jumlah'       => 5,
                        'aturan_pakai' => '3x1 tablet sesudah makan',
                    ],
                ],
            ]);
        $resepRes->assertStatus(201);
        $idResep = $resepRes->json('data.id_resep');

        // Selesaikan pemeriksaan dokter
        $selesaiRes = $this->withHeader('Authorization', "Bearer {$dokterToken}")
            ->putJson("/api/v1/pemeriksaan/{$idPendaftaran}/selesai-periksa");
        $selesaiRes->assertStatus(200);

        // 9. TEST HITUNG TAGIHAN DI KASIR
        $tagihanRes = $this->withHeader('Authorization', "Bearer {$kasirToken}")
            ->getJson("/api/v1/kasir/tagihan/{$idPendaftaran}");
        $tagihanRes->assertStatus(200);
        $grandTotal = $tagihanRes->json('data.grand_total');
        $this->assertGreaterThan(0, $grandTotal);

        // 10. TEST APOTEK COBA SERAHKAN OBAT SEBELUM BAYAR (HARUS DITOLAK 403)
        $serahkanGagalRes = $this->withHeader('Authorization', "Bearer {$apotekerToken}")
            ->putJson("/api/v1/apotik/resep/{$idResep}/serahkan");
        $serahkanGagalRes->assertStatus(403);
        $this->assertFalse($serahkanGagalRes->json('success'));

        // 11. TEST KASIR PROSES PEMBAYARAN LUNAS
        $bayarRes = $this->withHeader('Authorization', "Bearer {$kasirToken}")
            ->postJson('/api/v1/kasir/bayar', [
                'id_pendaftaran' => $idPendaftaran,
                'metode_bayar'   => 'tunai',
                'jumlah_dibayar' => $grandTotal + 10000,
            ]);
        $bayarRes->assertStatus(201);
        $this->assertEquals(10000, $bayarRes->json('data.kembalian'));
        $this->assertEquals('lunas', $bayarRes->json('data.status_pembayaran'));

        // 12. TEST APOTEK SERAHKAN OBAT SETELAH LUNAS (SEKARANG HARUS BERHASIL)
        $stokAwal = $obat->fresh()->stok;
        $serahkanSuksesRes = $this->withHeader('Authorization', "Bearer {$apotekerToken}")
            ->putJson("/api/v1/apotik/resep/{$idResep}/serahkan");
        $serahkanSuksesRes->assertStatus(200);
        $this->assertTrue($serahkanSuksesRes->json('success'));
        $this->assertEquals($stokAwal - 5, $obat->fresh()->stok);

        // 13. TEST PASIEN CEK RIWAYAT KUNJUNGAN DI MOBILE APP
        $riwayatRes = $this->withHeader('Authorization', "Bearer {$pasienToken}")
            ->getJson('/api/v1/mobile/riwayat-kunjungan');
        $riwayatRes->assertStatus(200);
        $this->assertNotEmpty($riwayatRes->json('data.data'));
    }
}
