<?php

namespace Tests\Feature;

use App\Models\Pasien;
use App\Models\Pegawai;
use App\Models\Pendaftaran;
use App\Models\Poli;
use App\Models\Transaksi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminEksekutifDanKartuTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_can_access_statistik_dashboard_with_kpi_and_charts()
    {
        $admin = Pegawai::whereIn('role', ['admin', 'administrator'])->first();

        $response = $this->actingAs($admin)
            ->withSession(['role_aktif' => 'admin'])
            ->get('/admin/statistik');

        $response->assertStatus(200);
        $response->assertSee('Dashboard Statistik');
        $response->assertSee('Eksekutif RS Delta');
        $response->assertSee('Kunjungan Pasien Bulan Ini');
        $response->assertSee('Pendapatan Kasir Bulan Ini');
        $response->assertSee('Tren Kunjungan Pasien');
        $response->assertSee('Distribusi per Poliklinik');
        $response->assertSee('Top 8 Diagnosa Penyakit');
    }

    public function test_admin_can_access_cetak_laporan_page()
    {
        $admin = Pegawai::whereIn('role', ['admin', 'administrator'])->first();

        $response = $this->actingAs($admin)
            ->withSession(['role_aktif' => 'admin'])
            ->get('/admin/statistik/cetak');

        $response->assertStatus(200);
        $response->assertSee('RUMAH SAKIT DELTA BANJARMASIN');
        $response->assertSee('LAPORAN EKSEKUTIF INDIKATOR');
        $response->assertSee('REKAPITULASI PELAYANAN RS');
        $response->assertSee('RINGKASAN INDIKATOR KINERJA UTAMA');
        $response->assertSee('DISTRIBUSI KUNJUNGAN PASIEN PER POLIKLINIK');
        $response->assertSee('TOP 8 DIAGNOSA PENYAKIT TERBANYAK');
        $response->assertSee('Direktur Rumah Sakit Delta');
    }

    public function test_non_admin_cannot_access_statistik_dashboard()
    {
        $perawat = Pegawai::where('role', 'perawat')->first();

        $response = $this->actingAs($perawat)
            ->withSession(['role_aktif' => 'perawat'])
            ->get('/admin/statistik');

        $response->assertRedirect();
    }

    public function test_admin_can_access_backup_page()
    {
        $admin = Pegawai::whereIn('role', ['admin', 'administrator'])->first();

        $response = $this->actingAs($admin)
            ->withSession(['role_aktif' => 'admin'])
            ->get('/admin/backup');

        $response->assertStatus(200);
        $response->assertSee('Pusat Cadangan');
        $response->assertSee('Backup Database RS Delta');
        $response->assertSee('Simpan Backup di Server');
        $response->assertSee('Unduh Langsung (.SQL)');
    }

    public function test_admin_can_download_instant_database_backup()
    {
        $admin = Pegawai::whereIn('role', ['admin', 'administrator'])->first();

        $response = $this->actingAs($admin)
            ->withSession(['role_aktif' => 'admin'])
            ->get('/admin/backup/download-instant');

        $response->assertStatus(200);
        $this->assertStringContainsString('BACKUP DATABASE SISTEM INFORMASI MANAJEMEN RUMAH SAKIT', $response->getContent());
        $this->assertStringContainsString('RUMAH SAKIT DELTA', $response->getContent());
    }

    public function test_admin_can_generate_backup_file_on_server()
    {
        $admin = Pegawai::whereIn('role', ['admin', 'administrator'])->first();

        $response = $this->actingAs($admin)
            ->withSession(['role_aktif' => 'admin'])
            ->post('/admin/backup/generate');

        $response->assertRedirect('/admin/backup');
        $response->assertSessionHas('status_backup');
    }

    public function test_loket_can_render_patient_card_with_barcode()
    {
        $loket = Pegawai::where('role', 'loket')->first();
        $pasien = Pasien::first();

        $response = $this->actingAs($loket)
            ->withSession(['role_aktif' => 'loket'])
            ->get('/loket/kartu-pasien/' . $pasien->id_pasien);

        $response->assertStatus(200);
        $response->assertSee('Kartu Identitas Berobat Pasien');
        $response->assertSee($pasien->rm);
        $response->assertSee($pasien->nama_pasien);
        $response->assertSee('Ketentuan Penggunaan Kartu');
        // Pastikan SVG barcode ter-render
        $response->assertSee('<svg', false);
    }

    public function test_admin_can_render_patient_card()
    {
        $admin = Pegawai::whereIn('role', ['admin', 'administrator'])->first();
        $pasien = Pasien::first();

        $response = $this->actingAs($admin)
            ->withSession(['role_aktif' => 'admin'])
            ->get('/admin/pasien/' . $pasien->id_pasien . '/kartu');

        $response->assertStatus(200);
        $response->assertSee('RUMAH SAKIT DELTA');
        $response->assertSee($pasien->rm);
    }
}
