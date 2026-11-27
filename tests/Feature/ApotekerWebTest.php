<?php

namespace Tests\Feature;

use App\Models\Obat;
use App\Models\Pasien;
use App\Models\Pegawai;
use App\Models\Pendaftaran;
use App\Models\Poli;
use App\Models\Resep;
use App\Models\ResepDetail;
use App\Models\Transaksi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApotekerWebTest extends TestCase
{
    use RefreshDatabase;

    protected Pegawai $apoteker;
    protected Pegawai $dokter;
    protected Pegawai $kasir;
    protected Pasien $pasien;
    protected Poli $poli;
    protected Obat $obat;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->apoteker = Pegawai::where('role', 'apoteker')->first() ?? Pegawai::where('nik', '6301010005')->first();
        $this->dokter = Pegawai::where('role', 'dokter')->first() ?? Pegawai::where('nik', '6301010002')->first();
        $this->kasir = Pegawai::where('role', 'admin_kasir')->first() ?? Pegawai::where('nik', '6301010004')->first();
        $this->pasien = Pasien::first();
        $this->poli = Poli::first();
        $this->obat = Obat::first();
    }

    public function test_apoteker_redirects_to_resep_page_upon_login()
    {
        $response = $this->post('/login', [
            'nik'      => $this->apoteker->nik,
            'password' => '12345',
        ]);

        $response->assertRedirect('/apoteker/resep');
    }

    public function test_apoteker_can_access_antrean_resep_page()
    {
        $response = $this->actingAs($this->apoteker)
            ->withSession(['role_aktif' => 'apoteker', 'user_type' => 'pegawai'])
            ->get('/apoteker/resep');

        $response->assertStatus(200);
        $response->assertSee('Antrean E-Resep Farmasi');
        $response->assertSee('Total E-Resep Hari Ini');
        $response->assertSee('Siap Diracing');
    }

    public function test_apoteker_can_mark_resep_as_selesai_racik()
    {
        // Buat pendaftaran dan resep dengan status siap_racik
        $reg = Pendaftaran::create([
            'no_pendaftaran' => 'REG-' . date('Ymd') . '-991',
            'id_pasien'      => $this->pasien->id_pasien,
            'id_poli'        => $this->poli->id_poli,
            'id_dokter'      => $this->dokter->id_pegawai,
            'no_antrian'     => 'UMU-991',
            'tanggal_daftar' => today()->setTime(9, 0),
            'cara_bayar'     => 'umum',
            'status_antrian' => 'menunggu_obat',
        ]);

        $resep = Resep::create([
            'no_resep'         => 'RSP-' . date('Ymd') . '-991',
            'id_pendaftaran'   => $reg->id_pendaftaran,
            'id_dokter'        => $this->dokter->id_pegawai,
            'total_harga_obat' => 50000,
            'status_resep'     => 'siap_racik',
            'catatan_dokter'   => 'Minum setelah makan',
        ]);

        $res = $this->actingAs($this->apoteker)
            ->withSession(['role_aktif' => 'apoteker', 'user_type' => 'pegawai'])
            ->post("/apoteker/resep/{$resep->id_resep}/racik");

        $res->assertSessionHas('success');
        $this->assertEquals('siap_diambil', $resep->fresh()->status_resep);
        $this->assertEquals($this->apoteker->id_pegawai, $resep->fresh()->id_apoteker);
    }

    public function test_apoteker_cannot_serahkan_obat_before_cashier_payment()
    {
        $reg = Pendaftaran::create([
            'no_pendaftaran' => 'REG-' . date('Ymd') . '-992',
            'id_pasien'      => $this->pasien->id_pasien,
            'id_poli'        => $this->poli->id_poli,
            'id_dokter'      => $this->dokter->id_pegawai,
            'no_antrian'     => 'UMU-992',
            'tanggal_daftar' => today()->setTime(9, 0),
            'cara_bayar'     => 'umum',
            'status_antrian' => 'menunggu_kasir',
        ]);

        $resep = Resep::create([
            'no_resep'         => 'RSP-' . date('Ymd') . '-992',
            'id_pendaftaran'   => $reg->id_pendaftaran,
            'id_dokter'        => $this->dokter->id_pegawai,
            'total_harga_obat' => 25000,
            'status_resep'     => 'menunggu_pembayaran',
        ]);

        ResepDetail::create([
            'id_resep'     => $resep->id_resep,
            'id_obat'      => $this->obat->id_obat,
            'jumlah'       => 5,
            'aturan_pakai' => '3x1 tablet',
            'harga_satuan' => 5000,
            'subtotal'     => 25000,
        ]);

        // Belum ada transaksi kasir atau belum lunas
        $res = $this->actingAs($this->apoteker)
            ->withSession(['role_aktif' => 'apoteker', 'user_type' => 'pegawai'])
            ->post("/apoteker/resep/{$resep->id_resep}/serahkan");

        $res->assertSessionHas('error');
        $this->assertEquals('menunggu_pembayaran', $resep->fresh()->status_resep);
    }

    public function test_apoteker_can_serahkan_obat_after_payment_and_stock_is_deducted()
    {
        $reg = Pendaftaran::create([
            'no_pendaftaran' => 'REG-' . date('Ymd') . '-993',
            'id_pasien'      => $this->pasien->id_pasien,
            'id_poli'        => $this->poli->id_poli,
            'id_dokter'      => $this->dokter->id_pegawai,
            'no_antrian'     => 'UMU-993',
            'tanggal_daftar' => today()->setTime(9, 0),
            'cara_bayar'     => 'umum',
            'status_antrian' => 'menunggu_obat',
        ]);

        $resep = Resep::create([
            'no_resep'         => 'RSP-' . date('Ymd') . '-993',
            'id_pendaftaran'   => $reg->id_pendaftaran,
            'id_dokter'        => $this->dokter->id_pegawai,
            'total_harga_obat' => 20000,
            'status_resep'     => 'siap_diambil',
        ]);

        $stokAwal = $this->obat->stok;
        $qtyBeli = 4;

        ResepDetail::create([
            'id_resep'     => $resep->id_resep,
            'id_obat'      => $this->obat->id_obat,
            'jumlah'       => $qtyBeli,
            'aturan_pakai' => '2x1 tablet sesudah makan',
            'harga_satuan' => $this->obat->harga_obat,
            'subtotal'     => $this->obat->harga_obat * $qtyBeli,
        ]);

        // Simulasikan transaksi kasir telah lunas
        Transaksi::create([
            'no_invoice'        => 'TRX-' . date('Ymd') . '-993',
            'id_pendaftaran'    => $reg->id_pendaftaran,
            'id_pegawai'        => $this->kasir->id_pegawai,
            'total_layanan'     => 50000,
            'total_resep'       => 20000,
            'jasa_perawat'      => 15000,
            'total_bayar'       => 85000,
            'jumlah_dibayar'    => 100000,
            'kembalian'         => 15000,
            'metode_bayar'      => 'TUNAI',
            'status_pembayaran' => 'lunas',
            'waktu_pembayaran'  => now(),
        ]);

        // Eksekusi penyerahan obat oleh apoteker
        $res = $this->actingAs($this->apoteker)
            ->withSession(['role_aktif' => 'apoteker', 'user_type' => 'pegawai'])
            ->post("/apoteker/resep/{$resep->id_resep}/serahkan");

        $res->assertSessionHas('success');
        $this->assertEquals('sudah_diserahkan', $resep->fresh()->status_resep);
        $this->assertEquals('selesai', $reg->fresh()->status_antrian);
        $this->assertEquals($stokAwal - $qtyBeli, $this->obat->fresh()->stok);
    }

    public function test_apoteker_can_view_cetak_etiket_stiker()
    {
        $reg = Pendaftaran::create([
            'no_pendaftaran' => 'REG-' . date('Ymd') . '-994',
            'id_pasien'      => $this->pasien->id_pasien,
            'id_poli'        => $this->poli->id_poli,
            'id_dokter'      => $this->dokter->id_pegawai,
            'no_antrian'     => 'UMU-994',
            'tanggal_daftar' => today()->setTime(9, 0),
            'cara_bayar'     => 'umum',
            'status_antrian' => 'menunggu_obat',
        ]);

        $resep = Resep::create([
            'no_resep'         => 'RSP-' . date('Ymd') . '-994',
            'id_pendaftaran'   => $reg->id_pendaftaran,
            'id_dokter'        => $this->dokter->id_pegawai,
            'total_harga_obat' => 15000,
            'status_resep'     => 'siap_diambil',
        ]);

        ResepDetail::create([
            'id_resep'     => $resep->id_resep,
            'id_obat'      => $this->obat->id_obat,
            'jumlah'       => 3,
            'aturan_pakai' => '3 x Sehari 1 Tablet',
            'harga_satuan' => 5000,
            'subtotal'     => 15000,
        ]);

        $res = $this->actingAs($this->apoteker)
            ->withSession(['role_aktif' => 'apoteker', 'user_type' => 'pegawai'])
            ->get("/apoteker/resep/{$resep->id_resep}/etiket");

        $res->assertStatus(200);
        $res->assertSee('INSTALASI FARMASI RS DELTA');
        $res->assertSee('3 x Sehari 1 Tablet');
        $res->assertSee($this->pasien->nama_pasien);
    }

    public function test_apoteker_can_manage_obat_and_add_stock()
    {
        $resList = $this->actingAs($this->apoteker)
            ->withSession(['role_aktif' => 'apoteker', 'user_type' => 'pegawai'])
            ->get('/apoteker/obat');

        $resList->assertStatus(200);
        $resList->assertSee('Kelola Obat');

        // Tambah master obat baru
        $resStore = $this->actingAs($this->apoteker)
            ->withSession(['role_aktif' => 'apoteker', 'user_type' => 'pegawai'])
            ->post('/apoteker/obat', [
                'nama_obat'    => 'Cetirizine 10mg Box',
                'satuan'       => 'box',
                'harga_beli'   => 20000,
                'harga_obat'   => 35000,
                'stok'         => 50,
                'stok_minimum' => 10,
            ]);

        $resStore->assertSessionHas('success');
        $newObat = Obat::where('nama_obat', 'Cetirizine 10mg Box')->first();
        $this->assertNotNull($newObat);
        $this->assertEquals(50, $newObat->stok);

        // Tambah stok masuk
        $resUpdateStok = $this->actingAs($this->apoteker)
            ->withSession(['role_aktif' => 'apoteker', 'user_type' => 'pegawai'])
            ->post("/apoteker/obat/{$newObat->id_obat}/stok", [
                'tambah_stok' => 25,
            ]);

        $resUpdateStok->assertSessionHas('success');
        $this->assertEquals(75, $newObat->fresh()->stok);
    }

    public function test_apoteker_can_access_riwayat_penyerahan_page()
    {
        $res = $this->actingAs($this->apoteker)
            ->withSession(['role_aktif' => 'apoteker', 'user_type' => 'pegawai'])
            ->get('/apoteker/riwayat');

        $res->assertStatus(200);
        $res->assertSee('Riwayat Penyerahan Resep Obat');
    }

    public function test_apoteker_receives_realtime_notifikasi_json()
    {
        $res = $this->actingAs($this->apoteker)
            ->withSession(['role_aktif' => 'apoteker', 'user_type' => 'pegawai'])
            ->getJson('/apoteker/notifikasi');

        $res->assertStatus(200);
        $res->assertJsonStructure([
            'count_resep',
            'count_menipis',
            'total_notif',
            'resep_antrean',
            'obat_menipis',
        ]);
    }

    public function test_apoteker_can_view_and_download_copy_resep()
    {
        $reg = Pendaftaran::create([
            'no_pendaftaran' => 'REG-' . date('Ymd') . '-995',
            'id_pasien'      => $this->pasien->id_pasien,
            'id_poli'        => $this->poli->id_poli,
            'id_dokter'      => $this->dokter->id_pegawai,
            'no_antrian'     => 'UMU-995',
            'tanggal_daftar' => today()->setTime(9, 0),
            'cara_bayar'     => 'umum',
            'status_antrian' => 'selesai',
        ]);

        $resep = Resep::create([
            'no_resep'         => 'RSP-' . date('Ymd') . '-995',
            'id_pendaftaran'   => $reg->id_pendaftaran,
            'id_dokter'        => $this->dokter->id_pegawai,
            'id_apoteker'      => $this->apoteker->id_pegawai,
            'total_harga_obat' => 30000,
            'status_resep'     => 'sudah_diserahkan',
        ]);

        ResepDetail::create([
            'id_resep'     => $resep->id_resep,
            'id_obat'      => $this->obat->id_obat,
            'jumlah'       => 10,
            'aturan_pakai' => '3x1 sesudah makan',
            'harga_satuan' => 3000,
            'subtotal'     => 30000,
        ]);

        // 1. Tampilan HTML Browser
        $resHtml = $this->actingAs($this->apoteker)
            ->withSession(['role_aktif' => 'apoteker', 'user_type' => 'pegawai'])
            ->get("/apoteker/resep/{$resep->id_resep}/copy-resep");

        $resHtml->assertStatus(200);
        $resHtml->assertSee('SALINAN RESEP');
        $resHtml->assertSee('APOGRAPH');
        $resHtml->assertSee($resep->no_resep);

        // 2. Download Binary PDF
        $resPdf = $this->actingAs($this->apoteker)
            ->withSession(['role_aktif' => 'apoteker', 'user_type' => 'pegawai'])
            ->get("/apoteker/resep/{$resep->id_resep}/copy-resep?download=1");

        $resPdf->assertStatus(200);
        $this->assertEquals('application/pdf', $resPdf->headers->get('content-type'));
    }

    public function test_apoteker_can_view_and_download_stok_pdf()
    {
        // 1. Tampilan HTML Laporan Stok
        $resHtml = $this->actingAs($this->apoteker)
            ->withSession(['role_aktif' => 'apoteker', 'user_type' => 'pegawai'])
            ->get('/apoteker/obat/stok-pdf?status=semua');

        $resHtml->assertStatus(200);
        $resHtml->assertSee('STOK OPNAME');
        $resHtml->assertSee('Valuasi Aset Persediaan');

        // 2. Download Binary PDF
        $resPdf = $this->actingAs($this->apoteker)
            ->withSession(['role_aktif' => 'apoteker', 'user_type' => 'pegawai'])
            ->get('/apoteker/obat/stok-pdf?status=semua&download=1');

        $resPdf->assertStatus(200);
        $this->assertEquals('application/pdf', $resPdf->headers->get('content-type'));
    }

    public function test_apoteker_can_export_stok_excel()
    {
        $res = $this->actingAs($this->apoteker)
            ->withSession(['role_aktif' => 'apoteker', 'user_type' => 'pegawai'])
            ->get('/apoteker/obat/stok-excel?status=semua');

        $res->assertStatus(200);
        $this->assertTrue(
            str_contains($res->headers->get('content-type'), 'spreadsheet') ||
            str_contains($res->headers->get('content-disposition'), '.xlsx')
        );
    }
}
