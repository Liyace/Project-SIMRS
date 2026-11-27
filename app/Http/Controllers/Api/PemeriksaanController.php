<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use App\Models\Obat;
use App\Models\Pendaftaran;
use App\Models\RekamMedis;
use App\Models\Resep;
use App\Models\ResepDetail;
use App\Models\ScreeningAwal;
use App\Models\TindakanMedis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PemeriksaanController extends Controller
{
    /**
     * GET /api/v1/poli/{id_poli}/antrian
     * Menampilkan antrian pasien di poli tertentu
     */
    public function antrianPoli(Request $request, $id_poli)
    {
        $antrian = Pendaftaran::with(['pasien', 'screening'])
            ->where('id_poli', $id_poli)
            ->whereDate('tanggal_daftar', today())
            ->whereIn('status_antrian', ['menunggu_skrining', 'menunggu_dokter', 'sedang_diperiksa'])
            ->orderBy('id_pendaftaran')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Antrian pasien di poli berhasil dimuat',
            'data'    => $antrian,
        ]);
    }

    /**
     * POST /api/v1/pemeriksaan/screening
     * Input tanda-tanda vital oleh Perawat
     */
    public function storeScreening(Request $request)
    {
        $validated = $request->validate([
            'id_pendaftaran'  => 'required|exists:pendaftaran,id_pendaftaran',
            'sistol'          => 'required|integer',
            'diastol'         => 'required|integer',
            'suhu_tubuh'      => 'required|numeric',
            'berat_badan'     => 'required|numeric',
            'tinggi_badan'    => 'required|numeric',
            'denyut_nadi'     => 'nullable|integer',
            'catatan_perawat' => 'nullable|string',
        ]);

        $user = $request->user();
        $idPerawat = ($user && isset($user->id_pegawai)) ? $user->id_pegawai : null;

        $screening = ScreeningAwal::updateOrCreate(
            ['id_pendaftaran' => $validated['id_pendaftaran']],
            array_merge($validated, ['id_perawat' => $idPerawat])
        );

        // Update status antrian pasien menjadi siap diperiksa dokter
        $pendaftaran = Pendaftaran::findOrFail($validated['id_pendaftaran']);
        $pendaftaran->update(['status_antrian' => 'menunggu_dokter']);

        return response()->json([
            'success' => true,
            'message' => 'Screening awal tanda vital berhasil disimpan. Status dialihkan ke dokter.',
            'data'    => $screening,
        ]);
    }

    /**
     * POST /api/v1/pemeriksaan/rekam-medis
     * Input diagnosa dan tindakan medis oleh Dokter
     */
    public function storeRekamMedis(Request $request)
    {
        $validated = $request->validate([
            'id_pendaftaran'    => 'required|exists:pendaftaran,id_pendaftaran',
            'anamnesis'         => 'nullable|string',
            'diagnosa_utama'    => 'required|string|max:255',
            'kode_icd10'        => 'nullable|string|max:50',
            'diagnosa_sekunder' => 'nullable|string',
            'terapi_tindakan'   => 'nullable|string',
            'catatan_edukasi'   => 'nullable|string',
            'tindakan'          => 'nullable|array',
            'tindakan.*.id_layanan' => 'required|exists:layanan,id_layanan',
            'tindakan.*.qty'        => 'nullable|integer|min:1',
        ]);

        $user = $request->user();
        $idDokter = ($user && isset($user->id_pegawai)) ? $user->id_pegawai : null;

        DB::beginTransaction();
        try {
            $rekamMedis = RekamMedis::updateOrCreate(
                ['id_pendaftaran' => $validated['id_pendaftaran']],
                [
                    'id_dokter'         => $idDokter,
                    'anamnesis'         => $validated['anamnesis'] ?? null,
                    'diagnosa_utama'    => $validated['diagnosa_utama'],
                    'kode_icd10'        => $validated['kode_icd10'] ?? null,
                    'diagnosa_sekunder' => $validated['diagnosa_sekunder'] ?? null,
                    'terapi_tindakan'   => $validated['terapi_tindakan'] ?? null,
                    'catatan_edukasi'   => $validated['catatan_edukasi'] ?? null,
                ]
            );

            // Simpan rincian tindakan medis bila ada
            if (!empty($validated['tindakan'])) {
                TindakanMedis::where('id_rekam_medis', $rekamMedis->id_rekam_medis)->delete();
                foreach ($validated['tindakan'] as $item) {
                    $layanan = Layanan::find($item['id_layanan']);
                    $qty = $item['qty'] ?? 1;
                    $tarif = $layanan->harga_layanan;
                    TindakanMedis::create([
                        'id_rekam_medis' => $rekamMedis->id_rekam_medis,
                        'id_layanan'     => $layanan->id_layanan,
                        'qty'            => $qty,
                        'tarif_saat_ini' => $tarif,
                        'subtotal'       => $tarif * $qty,
                    ]);
                }
            }

            // Tandai status sedang diperiksa
            Pendaftaran::where('id_pendaftaran', $validated['id_pendaftaran'])
                ->update(['status_antrian' => 'sedang_diperiksa']);

            DB::commit();

            $rekamMedis->load('tindakanMedis.layanan');

            return response()->json([
                'success' => true,
                'message' => 'Rekam medis dan tindakan berhasil disimpan',
                'data'    => $rekamMedis,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /api/v1/pemeriksaan/resep
     * Pembuatan E-Resep oleh Dokter
     */
    public function storeResep(Request $request)
    {
        $validated = $request->validate([
            'id_pendaftaran'  => 'required|exists:pendaftaran,id_pendaftaran',
            'catatan_dokter'  => 'nullable|string',
            'items'           => 'required|array|min:1',
            'items.*.id_obat' => 'required|exists:obat,id_obat',
            'items.*.jumlah'  => 'required|integer|min:1',
            'items.*.aturan_pakai' => 'required|string',
        ]);

        $user = $request->user();
        $idDokter = ($user && isset($user->id_pegawai)) ? $user->id_pegawai : null;

        DB::beginTransaction();
        try {
            $count = Resep::whereDate('created_at', today())->count() + 1;
            $noResep = 'RSP-' . date('Ymd') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

            $resep = Resep::updateOrCreate(
                ['id_pendaftaran' => $validated['id_pendaftaran']],
                [
                    'no_resep'         => $noResep,
                    'id_dokter'        => $idDokter,
                    'status_resep'     => 'menunggu_pembayaran',
                    'catatan_dokter'   => $validated['catatan_dokter'] ?? null,
                    'total_harga_obat' => 0,
                ]
            );

            // Hapus detail lama jika update
            ResepDetail::where('id_resep', $resep->id_resep)->delete();

            $totalObat = 0;
            foreach ($validated['items'] as $item) {
                $obat = Obat::find($item['id_obat']);
                $subtotal = $obat->harga_obat * $item['jumlah'];
                $totalObat += $subtotal;

                ResepDetail::create([
                    'id_resep'     => $resep->id_resep,
                    'id_obat'      => $obat->id_obat,
                    'jumlah'       => $item['jumlah'],
                    'aturan_pakai' => $item['aturan_pakai'],
                    'harga_satuan' => $obat->harga_obat,
                    'subtotal'     => $subtotal,
                ]);
            }

            $resep->update(['total_harga_obat' => $totalObat]);

            DB::commit();

            $resep->load('details.obat');

            return response()->json([
                'success' => true,
                'message' => 'E-Resep berhasil diterbitkan ke Apotik dan Kasir',
                'data'    => $resep,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * PUT /api/v1/pemeriksaan/{id_pendaftaran}/selesai-periksa
     * Dokter menyelesaikan pemeriksaan, antrian diteruskan ke Kasir
     */
    public function selesaiPeriksa($id_pendaftaran)
    {
        $pendaftaran = Pendaftaran::findOrFail($id_pendaftaran);
        $pendaftaran->update(['status_antrian' => 'menunggu_kasir']);

        return response()->json([
            'success' => true,
            'message' => 'Pemeriksaan selesai. Data tagihan siap diproses oleh Kasir.',
            'data'    => $pendaftaran,
        ]);
    }
}
