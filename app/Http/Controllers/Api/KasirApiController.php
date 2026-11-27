<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\Resep;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasirApiController extends Controller
{
    /**
     * GET /api/v1/kasir/antrian
     * List pasien yang sudah selesai diperiksa dan menunggu pembayaran di kasir
     */
    public function antrianKasir()
    {
        $antrian = Pendaftaran::with(['pasien', 'poli', 'dokter', 'rekamMedis.tindakanMedis.layanan', 'resep.details.obat'])
            ->where('status_antrian', 'menunggu_kasir')
            ->orderBy('id_pendaftaran')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Antrian kasir berhasil dimuat',
            'data'    => $antrian,
        ]);
    }

    /**
     * GET /api/v1/kasir/tagihan/{id_pendaftaran}
     * Menghitung rincian tagihan (biaya administrasi/poli + tindakan dokter + obat)
     */
    public function getTagihan($id_pendaftaran)
    {
        $pendaftaran = Pendaftaran::with([
            'pasien',
            'poli',
            'dokter',
            'rekamMedis.tindakanMedis.layanan',
            'resep.details.obat',
        ])->findOrFail($id_pendaftaran);

        $biayaPoli = (float) ($pendaftaran->poli->biaya_konsultasi ?? 0);
        
        $totalTindakan = 0;
        $rincianTindakan = [];
        if ($pendaftaran->rekamMedis && $pendaftaran->rekamMedis->tindakanMedis) {
            foreach ($pendaftaran->rekamMedis->tindakanMedis as $tm) {
                $sub = (float) $tm->subtotal;
                $totalTindakan += $sub;
                $rincianTindakan[] = [
                    'nama_layanan' => $tm->layanan->nama_layanan ?? 'Tindakan Medis',
                    'tarif'        => (float) $tm->tarif_saat_ini,
                    'qty'          => $tm->qty,
                    'subtotal'     => $sub,
                ];
            }
        }

        $totalObat = 0;
        $rincianObat = [];
        if ($pendaftaran->resep && $pendaftaran->resep->details) {
            foreach ($pendaftaran->resep->details as $rd) {
                $sub = (float) $rd->subtotal;
                $totalObat += $sub;
                $rincianObat[] = [
                    'nama_obat'    => $rd->obat->nama_obat ?? 'Obat',
                    'aturan_pakai' => $rd->aturan_pakai,
                    'harga_satuan' => (float) $rd->harga_satuan,
                    'jumlah'       => $rd->jumlah,
                    'subtotal'     => $sub,
                ];
            }
        }

        $jasaPerawat = 15000; // Standar jasa perawat / skrining
        $grandTotal = $biayaPoli + $totalTindakan + $totalObat + $jasaPerawat;

        return response()->json([
            'success' => true,
            'message' => 'Rincian tagihan berhasil dihitung',
            'data'    => [
                'id_pendaftaran'   => $pendaftaran->id_pendaftaran,
                'no_pendaftaran'   => $pendaftaran->no_pendaftaran,
                'no_antrian'       => $pendaftaran->no_antrian,
                'pasien'           => [
                    'id_pasien'   => $pendaftaran->pasien->id_pasien,
                    'no_rm'       => $pendaftaran->pasien->rm,
                    'nama_pasien' => $pendaftaran->pasien->nama_pasien,
                ],
                'biaya_poli'       => $biayaPoli,
                'total_tindakan'   => $totalTindakan,
                'rincian_tindakan' => $rincianTindakan,
                'jasa_perawat'     => $jasaPerawat,
                'total_obat'       => $totalObat,
                'rincian_obat'     => $rincianObat,
                'grand_total'      => $grandTotal,
            ],
        ]);
    }

    /**
     * POST /api/v1/kasir/bayar
     * Proses pelunasan pembayaran kasir dan terbitkan invoice
     */
    public function bayar(Request $request)
    {
        $validated = $request->validate([
            'id_pendaftaran' => 'required|exists:pendaftaran,id_pendaftaran',
            'metode_bayar'   => 'required|in:tunai,qris,transfer',
            'jumlah_dibayar' => 'required|numeric|min:0',
        ]);

        $pendaftaran = Pendaftaran::with(['poli', 'rekamMedis.tindakanMedis.layanan', 'resep.details.obat'])
            ->findOrFail($validated['id_pendaftaran']);

        $biayaPoli = (float) ($pendaftaran->poli->biaya_konsultasi ?? 0);
        $totalTindakan = (float) ($pendaftaran->rekamMedis?->tindakanMedis?->sum('subtotal') ?? 0);
        $totalObat = (float) ($pendaftaran->resep?->total_harga_obat ?? 0);
        $jasaPerawat = 15000;
        $grandTotal = $biayaPoli + $totalTindakan + $totalObat + $jasaPerawat;

        if ($validated['jumlah_dibayar'] < $grandTotal) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah pembayaran kurang! Tagihan: Rp ' . number_format($grandTotal, 0, ',', '.') . ', Dibayar: Rp ' . number_format($validated['jumlah_dibayar'], 0, ',', '.'),
            ], 422);
        }

        $user = $request->user();
        $idKasir = ($user && isset($user->id_pegawai)) ? $user->id_pegawai : null;

        DB::beginTransaction();
        try {
            $countInv = Transaksi::whereDate('created_at', today())->count() + 1;
            $noInvoice = 'INV-' . date('Ymd') . '-' . str_pad($countInv, 4, '0', STR_PAD_LEFT);
            $kembalian = $validated['jumlah_dibayar'] - $grandTotal;

            $transaksi = Transaksi::create([
                'no_invoice'        => $noInvoice,
                'id_pendaftaran'    => $pendaftaran->id_pendaftaran,
                'id_resep'          => $pendaftaran->resep?->id_resep,
                'id_pegawai'        => $idKasir,
                'total_layanan'     => $biayaPoli + $totalTindakan,
                'total_resep'       => $totalObat,
                'jasa_perawat'      => $jasaPerawat,
                'total_bayar'       => $grandTotal,
                'jumlah_dibayar'    => $validated['jumlah_dibayar'],
                'kembalian'         => $kembalian,
                'metode_bayar'      => $validated['metode_bayar'],
                'status_pembayaran' => 'lunas',
                'waktu_pembayaran'  => now(),
            ]);

            // Catat detail invoice
            TransaksiDetail::create([
                'id_transaksi' => $transaksi->id_transaksi,
                'jenis_item'   => 'layanan',
                'deskripsi'    => 'Konsultasi ' . ($pendaftaran->poli->nama_poli ?? 'Poli'),
                'tarif'        => $biayaPoli,
                'qty'          => 1,
                'subtotal'     => $biayaPoli,
            ]);

            if ($pendaftaran->rekamMedis && $pendaftaran->rekamMedis->tindakanMedis) {
                foreach ($pendaftaran->rekamMedis->tindakanMedis as $tm) {
                    TransaksiDetail::create([
                        'id_transaksi' => $transaksi->id_transaksi,
                        'jenis_item'   => 'layanan',
                        'deskripsi'    => $tm->layanan->nama_layanan ?? 'Tindakan',
                        'tarif'        => $tm->tarif_saat_ini,
                        'qty'          => $tm->qty,
                        'subtotal'     => $tm->subtotal,
                    ]);
                }
            }

            if ($totalObat > 0) {
                TransaksiDetail::create([
                    'id_transaksi' => $transaksi->id_transaksi,
                    'jenis_item'   => 'obat',
                    'deskripsi'    => 'Resep Obat Farmasi (' . ($pendaftaran->resep->no_resep ?? '-') . ')',
                    'tarif'        => $totalObat,
                    'qty'          => 1,
                    'subtotal'     => $totalObat,
                ]);

                // Update status resep jadi siap diracik/disiapkan apotik
                $pendaftaran->resep->update(['status_resep' => 'siap_racik']);
                // Status antrian pasien diarahkan ke apotik
                $pendaftaran->update(['status_antrian' => 'menunggu_obat']);
            } else {
                // Jika tidak ada resep obat, kunjungan langsung selesai
                $pendaftaran->update(['status_antrian' => 'selesai']);
            }

            DB::commit();

            $transaksi->load('details');

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil diverifikasi. Bukti bayar ' . $noInvoice . ' telah diterbitkan.',
                'data'    => $transaksi,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /api/v1/kasir/laporan-harian
     */
    public function laporanHarian(Request $request)
    {
        $tanggal = $request->query('tanggal', today()->toDateString());

        $transaksi = Transaksi::with(['pendaftaran.pasien'])
            ->whereDate('waktu_pembayaran', $tanggal)
            ->where('status_pembayaran', 'lunas')
            ->get();

        $totalPendapatan = $transaksi->sum('total_bayar');

        return response()->json([
            'success' => true,
            'message' => 'Laporan kasir harian berhasil dimuat',
            'data'    => [
                'tanggal'          => $tanggal,
                'total_transaksi'  => $transaksi->count(),
                'total_pendapatan' => (float) $totalPendapatan,
                'daftar_transaksi' => $transaksi,
            ],
        ]);
    }
}
