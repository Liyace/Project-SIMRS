<?php

namespace Tests\Feature;

use App\Models\Pasien;
use App\Models\Pegawai;
use App\Models\Pendaftaran;
use App\Models\Poli;
use App\Models\RekamMedis;
use App\Models\ScreeningAwal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DokterWebTest extends TestCase
{
    use RefreshDatabase;

    protected Pegawai $dokter;
    protected Pasien $pasien;
    protected Poli $poli;
    protected Pendaftaran $pendaftaran;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->dokter = Pegawai::where('role', 'dokter')->first() ?? Pegawai::where('nik', '6301010002')->first();
        $this->pasien = Pasien::first();
        $this->poli = Poli::first();

        $this->pendaftaran = Pendaftaran::create([
            'no_pendaftaran' => 'REG-' . date('Ymd') . '-881',
            'id_pasien'      => $this->pasien->id_pasien,
            'id_poli'        => $this->poli->id_poli,
            'id_dokter'      => $this->dokter->id_pegawai,
            'no_antrian'     => 'UMU-881',
            'tanggal_daftar' => today()->setTime(8, 30),
            'cara_bayar'     => 'umum',
            'status_antrian' => 'selesai',
        ]);

        ScreeningAwal::create([
            'id_pendaftaran'  => $this->pendaftaran->id_pendaftaran,
            'id_perawat'      => $this->dokter->id_pegawai,
            'sistol'          => 120,
            'diastol'         => 80,
            'denyut_nadi'     => 80,
            'suhu_tubuh'      => 36.6,
            'berat_badan'     => 65,
            'tinggi_badan'    => 170,
            'catatan_perawat' => 'Demam dan flu sejak 2 hari lalu',
        ]);

        RekamMedis::create([
            'id_pendaftaran'  => $this->pendaftaran->id_pendaftaran,
            'id_dokter'       => $this->dokter->id_pegawai,
            'anamnesis'       => 'Demam dan flu sejak 2 hari lalu',
            'diagnosa_utama'  => 'Acute Upper Respiratory Tract Infection (ISPA)',
            'kode_icd10'      => 'J06.9',
            'terapi_tindakan' => 'Pemeriksaan fisik dan konsultasi dokter',
            'catatan_edukasi' => 'Banyak minum air putih dan istirahat',
        ]);
    }

    public function test_dokter_can_view_and_download_surat_sakit()
    {
        // 1. Tampilan HTML Browser
        $resHtml = $this->actingAs($this->dokter)
            ->withSession(['role_aktif' => 'dokter', 'user_type' => 'pegawai'])
            ->get("/dokter/surat-sakit/{$this->pendaftaran->id_pendaftaran}?hari=3");

        $resHtml->assertStatus(200);
        $resHtml->assertSee('SURAT KETERANGAN SAKIT');
        $resHtml->assertSee($this->pasien->nama_pasien);
        $resHtml->assertSee('3 Hari');

        // 2. Download Binary PDF
        $resPdf = $this->actingAs($this->dokter)
            ->withSession(['role_aktif' => 'dokter', 'user_type' => 'pegawai'])
            ->get("/dokter/surat-sakit/{$this->pendaftaran->id_pendaftaran}?hari=3&download=1");

        $resPdf->assertStatus(200);
        $this->assertEquals('application/pdf', $resPdf->headers->get('content-type'));
    }

    public function test_dokter_can_view_and_download_resume_medis()
    {
        // 1. Tampilan HTML Resume Medis
        $resHtml = $this->actingAs($this->dokter)
            ->withSession(['role_aktif' => 'dokter', 'user_type' => 'pegawai'])
            ->get("/dokter/resume-medis/{$this->pendaftaran->id_pendaftaran}");

        $resHtml->assertStatus(200);
        $resHtml->assertSee('RINGKASAN RESUME MEDIS RAWAT JALAN');
        $resHtml->assertSee('DISCHARGE CLINICAL SUMMARY');
        $resHtml->assertSee($this->pasien->nama_pasien);
        $resHtml->assertSee('120/80');

        // 2. Download Binary PDF
        $resPdf = $this->actingAs($this->dokter)
            ->withSession(['role_aktif' => 'dokter', 'user_type' => 'pegawai'])
            ->get("/dokter/resume-medis/{$this->pendaftaran->id_pendaftaran}?download=1");

        $resPdf->assertStatus(200);
        $this->assertEquals('application/pdf', $resPdf->headers->get('content-type'));
    }

    public function test_dokter_dashboard_and_notifikasi_filters_by_poliklinik()
    {
        // 1. Dashboard Dokter menampilkan Poli Aktif & Switcher Filter
        $res = $this->actingAs($this->dokter)
            ->withSession(['role_aktif' => 'dokter', 'user_type' => 'pegawai'])
            ->get('/dokter/pelayanan');

        $res->assertStatus(200);
        $res->assertSee('Pelayanan Poliklinik Dokter');
        $res->assertSee('Filter Poliklinik:');
        $res->assertSee('Semua Poliklinik');

        // 2. Endpoint Notifikasi Dokter menghormati filter poli
        $notifRes = $this->actingAs($this->dokter)
            ->withSession(['role_aktif' => 'dokter', 'user_type' => 'pegawai'])
            ->getJson('/dokter/notifikasi');

        $notifRes->assertStatus(200);
        $notifRes->assertJsonStructure(['success', 'count', 'pasien', 'timestamp']);
    }
}
