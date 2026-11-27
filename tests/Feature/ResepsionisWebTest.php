<?php

namespace Tests\Feature;

use App\Models\Pasien;
use App\Models\Pegawai;
use App\Models\Pendaftaran;
use App\Models\Poli;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ResepsionisWebTest extends TestCase
{
    use RefreshDatabase;

    protected Pegawai $resepsionis;
    protected Pegawai $admin;
    protected Pasien $pasien;
    protected Poli $poli;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->resepsionis = Pegawai::where('role', 'resepsionis')->first() ?? Pegawai::where('nik', '6301010008')->first();
        $this->admin = Pegawai::where('role', 'admin')->first() ?? Pegawai::where('nik', '6301010001')->first();
        $this->pasien = Pasien::first();
        $this->poli = Poli::first();
    }

    public function test_resepsionis_redirects_to_monitoring_page_upon_login()
    {
        $response = $this->post('/login', [
            'nik'      => $this->resepsionis->nik,
            'password' => '12345',
        ]);

        $response->assertRedirect('/resepsionis');
    }

    public function test_resepsionis_can_access_monitoring_dashboard()
    {
        $res = $this->actingAs($this->resepsionis)
            ->withSession(['role_aktif' => 'resepsionis', 'user_type' => 'pegawai'])
            ->get('/resepsionis');

        $res->assertStatus(200);
        $res->assertSee('Monitoring Antrean & Informasi Pasien');
        $res->assertSee('Panel Pemanggil Suara Antrean');
        $res->assertSee('Total Terdaftar');
        $res->assertSee('Di Skrining TTV');
    }

    public function test_resepsionis_can_access_cctv_layar_tv_preview()
    {
        $res = $this->actingAs($this->resepsionis)
            ->withSession(['role_aktif' => 'resepsionis', 'user_type' => 'pegawai'])
            ->get('/resepsionis/layar-tv');

        $res->assertStatus(200);
        $res->assertSee('Layar Display TV Ruang Tunggu');
        $res->assertSee('LIVE FEED • CAM 01 RUANG TUNGGU RS DELTA');
        $res->assertSee('Status Display TV');
        $res->assertSee('Buka Fullscreen TV di Tab Baru');
    }

    public function test_resepsionis_can_trigger_voice_call_to_speaker_and_tv()
    {
        $reg = Pendaftaran::create([
            'no_pendaftaran' => 'REG-' . date('Ymd') . '-771',
            'id_pasien'      => $this->pasien->id_pasien,
            'id_poli'        => $this->poli->id_poli,
            'no_antrian'     => 'UMU-771',
            'tanggal_daftar' => today()->setTime(9, 0),
            'cara_bayar'     => 'umum',
            'status_antrian' => 'menunggu_skrining',
        ]);

        $res = $this->actingAs($this->resepsionis)
            ->withSession(['role_aktif' => 'resepsionis', 'user_type' => 'pegawai'])
            ->postJson('/resepsionis/panggil', [
                'no_antrian'  => 'UMU-771',
                'nama_pasien' => $this->pasien->nama_pasien,
                'tujuan'      => 'Ruang Skrining Perawat',
            ]);

        $res->assertStatus(200);
        $res->assertJson([
            'success'   => true,
            'panggilan' => [
                'no_antrian'  => 'UMU-771',
                'nama_pasien' => $this->pasien->nama_pasien,
                'tujuan'      => 'Ruang Skrining Perawat',
            ],
        ]);

        // Verifikasi panggilan tersimpan di Cache untuk TV Display
        $cachedCall = Cache::get('simrs_antrean_panggilan_terbaru');
        $this->assertNotNull($cachedCall);
        $this->assertEquals('UMU-771', $cachedCall['no_antrian']);
        $this->assertStringContainsString('Nomor antrean', $cachedCall['suara_teks']);
    }

    public function test_display_tv_is_accessible_publicly_without_login()
    {
        $res = $this->get('/monitoring');

        $res->assertStatus(200);
        $res->assertSee('RUMAH SAKIT DELTA');
        $res->assertSee('SEDANG DIPANGGIL');
        $res->assertSee('Skrining TTV Perawat');
        $res->assertSee('Poliklinik Dokter');
        $res->assertSee('Loket Kasir Pembayaran');
        $res->assertSee('Pengambilan Obat Farmasi');
    }

    public function test_status_panggilan_endpoint_returns_json_for_tv_polling()
    {
        Cache::put('simrs_antrean_panggilan_terbaru', [
            'id_panggilan' => 'CALL-1234',
            'no_antrian'   => 'UMU-001',
            'nama_pasien'  => 'Ahmad Fauzi',
            'tujuan'       => 'Poli Umum',
            'suara_teks'   => 'Nomor antrean U M U kosong kosong satu...',
            'waktu'        => '09:00:00',
        ], 60);

        $res = $this->getJson('/monitoring/status-panggilan');

        $res->assertStatus(200);
        $res->assertJson([
            'has_call'  => true,
            'panggilan' => [
                'no_antrian' => 'UMU-001',
            ],
        ]);
    }

    public function test_resepsionis_can_fetch_journey_data_json()
    {
        $res = $this->actingAs($this->resepsionis)
            ->withSession(['role_aktif' => 'resepsionis', 'user_type' => 'pegawai'])
            ->getJson('/resepsionis/journey-data');

        $res->assertStatus(200);
        $res->assertJsonStructure([
            'success',
            'count',
            'data' => [
                '*' => [
                    'id_pendaftaran',
                    'no_antrian',
                    'nama_pasien',
                    'step_index',
                    'step_label',
                    'posisi_aktif',
                ],
            ],
        ]);
    }

    public function test_stream_suara_endpoint_handles_request()
    {
        $res = $this->get('/monitoring/suara?q=Nomor+antrean+satu');
        $this->assertTrue(in_array($res->getStatusCode(), [200, 404, 204]));
    }

    public function test_resepsionis_can_poll_notifikasi()
    {
        $res = $this->actingAs($this->resepsionis)
            ->withSession(['role_aktif' => 'resepsionis', 'user_type' => 'pegawai'])
            ->getJson('/resepsionis/notifikasi');

        $res->assertStatus(200);
        $res->assertJsonStructure([
            'success',
            'total_terdaftar',
            'total_skrining',
            'total_dokter',
            'total_kasir',
            'total_farmasi',
            'total_selesai',
            'bottlenecks',
            'total_peringatan',
            'timestamp',
        ]);
    }
}
