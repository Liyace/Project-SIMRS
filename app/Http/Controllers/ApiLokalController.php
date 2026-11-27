<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\Obat;
use App\Models\Pasien;
use App\Models\Pendaftaran;
use App\Models\Poli;
use App\Models\Resep;
use App\Models\Transaksi;
use Illuminate\Http\Request;

/**
 * Backend API lokal, dibuat untuk MENGGANTIKAN seluruh endpoint eksternal
 * yang tadinya dipanggil langsung dari JavaScript di tiap halaman
 * (kasir4d06.infinityfree.me, kasir4d06.vps-poliban.my.id,
 * daftar4d06.vps-poliban.my.id, farmasi4d06.vps-poliban.my.id) - yang
 * semuanya sedang bermasalah/tidak bisa diakses.
 *
 * Bentuk responsnya sengaja dibuat SAMA (`{success: true, data: [...]}`)
 * seperti API eksternal aslinya, supaya JS yang sudah ada di masing-masing
 * halaman (antrian, riwayat, laporan, kelola, dashboard) tidak perlu
 * ditulis ulang - cukup ganti konstanta URL API_* di setiap file .blade.php
 * agar menunjuk ke sini.
 *
 * Data yang dikembalikan adalah DATA DUMMY (lihat database/seeders/
 * DummyDataSeeder.php), bukan data asli dari tim lain.
 */
class ApiLokalController extends Controller
{
    // ===================== LAYANAN (pengganti kasir4d06/backend/layanan) =====================

    public function layananIndex()
    {
        return response()->json([
            'success' => true,
            'data' => Layanan::orderBy('id_layanan')->get(),
        ]);
    }

    public function layananStore(Request $request)
    {
        $data = $request->validate([
            'nama_layanan'  => 'required|string|max:255',
            'harga_layanan' => 'required|numeric|min:0',
        ]);

        $layanan = Layanan::create($data);

        return response()->json(['success' => true, 'message' => 'Layanan berhasil ditambahkan', 'data' => $layanan]);
    }

    public function layananUpdate(Request $request, $id)
    {
        $layanan = Layanan::findOrFail($id);

        $data = $request->validate([
            'nama_layanan'  => 'required|string|max:255',
            'harga_layanan' => 'required|numeric|min:0',
        ]);

        $layanan->update($data);

        return response()->json(['success' => true, 'message' => 'Layanan berhasil diperbarui', 'data' => $layanan]);
    }

    public function layananDestroy($id)
    {
        $layanan = Layanan::findOrFail($id);
        $layanan->delete();

        return response()->json(['success' => true, 'message' => 'Layanan berhasil dihapus']);
    }

    // ===================== TRANSAKSI (pengganti kasir4d06/backend/transaksi) =====================

    public function transaksiIndex()
    {
        return response()->json([
            'success' => true,
            'data' => Transaksi::orderByDesc('id_transaksi')->get(),
        ]);
    }

    public function transaksiStore(Request $request)
    {
        $data = $request->validate([
            'id_pendaftaran'     => 'nullable',
            'id_resep'           => 'nullable',
            'id_layanan'         => 'nullable|exists:layanan,id_layanan',
            'total_bayar'        => 'nullable|numeric',
            'total_resep'        => 'nullable|numeric',
            'jasa_perawat'       => 'nullable|numeric',
            'status_pembayaran'  => 'nullable|string',
        ]);

        $data['id_pegawai'] = auth()->id();
        $data['status_pembayaran'] = $data['status_pembayaran'] ?? 'lunas';

        $transaksi = Transaksi::create($data);

        return response()->json(['success' => true, 'message' => 'Transaksi berhasil disimpan', 'data' => $transaksi]);
    }

    // GET /api-lokal/laporan-harian?tanggal=YYYY-MM-DD (pengganti kasir4d06/backend/laporan-harian)
    public function laporanHarian(Request $request)
    {
        $tanggal = $request->query('tanggal', today()->toDateString());

        $totalPendapatan = Transaksi::whereDate('created_at', $tanggal)
            ->where('status_pembayaran', 'lunas')
            ->sum('total_bayar');

        return response()->json([
            'success' => true,
            'data' => [
                'tanggal' => $tanggal,
                'total_pendapatan' => (float) $totalPendapatan,
            ],
        ]);
    }

    // ===================== DATA TIM PENDAFTARAN (dummy) =====================

    public function pasienIndex()
    {
        return response()->json(['success' => true, 'data' => Pasien::all()]);
    }

    public function pendaftaranIndex()
    {
        return response()->json(['success' => true, 'data' => Pendaftaran::all()]);
    }

    public function poliIndex()
    {
        return response()->json(['success' => true, 'data' => Poli::all()]);
    }

    // Daftar role pegawai (dummy statis, dulu dari daftar4d06/api/role)
    public function roleIndex()
    {
        return response()->json([
            'success' => true,
            'data' => [
                ['id_role' => 1, 'nama_role' => 'admin'],
                ['id_role' => 2, 'nama_role' => 'kasir'],
                ['id_role' => 3, 'nama_role' => 'dokter'],
            ],
        ]);
    }

    // ===================== DATA TIM FARMASI (dummy) =====================

    public function obatIndex()
    {
        return response()->json(['success' => true, 'data' => Obat::all()]);
    }

    public function resepIndex()
    {
        return response()->json(['success' => true, 'data' => Resep::with('obat')->get()]);
    }
}
