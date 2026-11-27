<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use App\Models\Resep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApotikApiController extends Controller
{
    /**
     * GET /api/v1/apotik/antrian-resep
     * List antrian E-Resep yang masuk ke apotik
     */
    public function antrianResep(Request $request)
    {
        $query = Resep::with([
            'pendaftaran.pasien',
            'pendaftaran.poli',
            'dokter',
            'details.obat',
            'pendaftaran.transaksi',
        ]);

        if ($request->filled('status')) {
            $query->where('status_resep', $request->status);
        }

        $reseps = $query->orderByDesc('id_resep')->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar antrian resep apotik berhasil dimuat',
            'data'    => $reseps,
        ]);
    }

    /**
     * GET /api/v1/apotik/resep/{id_resep}
     */
    public function detailResep($id_resep)
    {
        $resep = Resep::with([
            'pendaftaran.pasien',
            'pendaftaran.poli',
            'dokter',
            'apoteker',
            'details.obat',
            'pendaftaran.transaksi',
        ])->findOrFail($id_resep);

        return response()->json([
            'success' => true,
            'message' => 'Detail resep berhasil dimuat',
            'data'    => $resep,
        ]);
    }

    /**
     * PUT /api/v1/apotik/resep/{id_resep}/verifikasi
     * Apoteker memverifikasi bahwa obat tersedia dan siap diproses
     */
    public function verifikasiResep(Request $request, $id_resep)
    {
        $resep = Resep::with('details.obat')->findOrFail($id_resep);

        // Validasi stok obat mencukupi
        foreach ($resep->details as $item) {
            if ($item->obat->stok < $item->jumlah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok obat ' . $item->obat->nama_obat . ' tidak mencukupi! Tersedia: ' . $item->obat->stok . ', Dibutuhkan: ' . $item->jumlah,
                ], 422);
            }
        }

        $user = $request->user();
        $idApoteker = ($user && isset($user->id_pegawai)) ? $user->id_pegawai : null;

        $resep->update([
            'id_apoteker'  => $idApoteker,
            'status_resep' => 'siap_racik',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Resep berhasil diverifikasi oleh apoteker',
            'data'    => $resep,
        ]);
    }

    /**
     * PUT /api/v1/apotik/resep/{id_resep}/serahkan
     * Penyerahan obat ke pasien:
     * SYARAT UTAMA: Pembayaran di Kasir WAJIB LUNAS!
     * Otomatis memotong stok obat di gudang farmasi.
     */
    public function serahkanObat(Request $request, $id_resep)
    {
        $resep = Resep::with(['details.obat', 'pendaftaran.transaksi'])->findOrFail($id_resep);

        // Pengecekan status bayar dari kelompok 3 (Kasir)
        $transaksi = $resep->pendaftaran?->transaksi;
        if (!$transaksi || $transaksi->status_pembayaran !== 'lunas') {
            return response()->json([
                'success' => false,
                'message' => 'Gagal serahkan obat! Pasien belum menyelesaikan pembayaran di Kasir.',
            ], 403);
        }

        DB::beginTransaction();
        try {
            // Potong stok obat
            foreach ($resep->details as $item) {
                $obat = $item->obat;
                $obat->stok = max(0, $obat->stok - $item->jumlah);
                $obat->save();
            }

            // Update status resep
            $resep->update(['status_resep' => 'sudah_diserahkan']);

            // Selesaikan journey antrian pasien di SIMRS
            if ($resep->pendaftaran) {
                $resep->pendaftaran->update(['status_antrian' => 'selesai']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Obat telah diserahkan kepada pasien. Stok obat otomatis terpotong.',
                'data'    => $resep,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /api/v1/obat
     */
    public function obatIndex(Request $request)
    {
        $query = Obat::query();
        if ($request->filled('q')) {
            $query->where('nama_obat', 'like', '%' . $request->q . '%')
                  ->orWhere('kode_obat', 'like', '%' . $request->q . '%');
        }

        $obats = $query->orderBy('nama_obat')->get();

        return response()->json([
            'success' => true,
            'message' => 'Data obat berhasil dimuat',
            'data'    => $obats,
        ]);
    }

    /**
     * POST /api/v1/obat
     */
    public function obatStore(Request $request)
    {
        $validated = $request->validate([
            'kode_obat'     => 'nullable|string|unique:obat,kode_obat',
            'nama_obat'     => 'required|string|max:255',
            'satuan'        => 'required|string',
            'harga_beli'    => 'nullable|numeric|min:0',
            'harga_obat'    => 'required|numeric|min:0',
            'stok'          => 'required|integer|min:0',
            'stok_minimum'  => 'nullable|integer|min:0',
        ]);

        $obat = Obat::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Obat baru berhasil ditambahkan',
            'data'    => $obat,
        ], 201);
    }
}
