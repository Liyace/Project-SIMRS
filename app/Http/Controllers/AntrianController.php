<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\Pendaftaran;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AntrianController extends Controller
{
    /**
     * Tampilkan antrian kasir dengan integrasi sistem lokal RS Delta
     */
    public function index()
    {
        $layananMaster = Layanan::orderBy('nama_layanan')->get();

        return view('antrian.index', compact('layananMaster'));
    }

    /**
     * Endpoint API internal: Mengambil antrian pasien aktif dan master layanan
     */
    public function getData()
    {
        $antrian = $this->getAntrianQuery()->get()->map(function ($p) {
            $resep = $p->resep;
            $itemsObat = [];
            $totalObat = 0;

            if ($resep && $resep->details) {
                foreach ($resep->details as $d) {
                    $harga = (int) ($d->harga_satuan ?? ($d->obat?->harga_jual ?? 0));
                    $jumlah = (int) $d->jumlah;
                    $subtotal = (int) ($d->subtotal ?? ($harga * $jumlah));
                    $totalObat += $subtotal;

                    $itemsObat[] = [
                        'id_obat'      => $d->id_obat,
                        'nama_obat'    => $d->obat?->nama_obat ?? "Obat #{$d->id_obat}",
                        'jumlah'       => $jumlah,
                        'harga_satuan' => $harga,
                        'subtotal'     => $subtotal,
                    ];
                }
            }

            // Tindakan Medis & Layanan dari Pemeriksaan Dokter di Poliklinik
            $itemsLayanan = [];
            $totalLayananDokter = 0;
            if ($p->rekamMedis && $p->rekamMedis->tindakanMedis) {
                foreach ($p->rekamMedis->tindakanMedis as $tm) {
                    $tarif = (int) ($tm->subtotal ?? (($tm->tarif_saat_ini ?? ($tm->layanan?->harga_layanan ?? 0)) * ($tm->qty ?? 1)));
                    $totalLayananDokter += $tarif;
                    $itemsLayanan[] = [
                        'id'     => $tm->id_layanan,
                        'nama'   => $tm->layanan?->nama_layanan ?? "Tindakan Medis #{$tm->id_layanan}",
                        'harga'  => $tarif,
                        'qty'    => $tm->qty ?? 1,
                        'sumber' => 'dokter',
                    ];
                }
            }

            return [
                'id_pendaftaran'       => $p->id_pendaftaran,
                'no_antrian'           => $p->no_antrian ?? ('Q-' . str_pad($p->id_pendaftaran, 3, '0', STR_PAD_LEFT)),
                'nama_pasien'          => $p->pasien?->nama_pasien ?? 'Pasien Umum',
                'no_rm'                => $p->pasien?->rm ?? 'RM-0000',
                'nama_poli'            => $p->poli?->nama_poli ?? 'Poli Umum',
                'id_poli'              => $p->id_poli,
                'status_antrian'       => $p->status_antrian,
                'id_resep'             => $resep ? "RSP-{$resep->id_resep}" : '-',
                'raw_id_resep'         => $resep?->id_resep,
                'obat'                 => $itemsObat,
                'total_obat'           => $totalObat,
                'layanan_dokter'       => $itemsLayanan,
                'total_layanan_dokter' => $totalLayananDokter,
            ];
        });

        $layanan = Layanan::orderBy('nama_layanan')->get();

        return response()->json([
            'success' => true,
            'antrian' => $antrian,
            'layanan' => $layanan,
        ]);
    }

    /**
     * Endpoint API internal: Mengambil ringkasan notifikasi antrean pasien yang sedang menunggu pembayaran kasir
     */
    public function getNotifikasi()
    {
        $pasienList = $this->getAntrianQuery()->get()->map(function ($p) {
            return [
                'id_pendaftaran' => $p->id_pendaftaran,
                'no_antrian'     => $p->no_antrian,
                'nama_pasien'    => $p->pasien?->nama_pasien ?? 'Pasien',
                'no_rm'          => $p->pasien?->rm ?? '-',
                'nama_poli'      => $p->poli?->nama_poli ?? 'Poli Umum',
                'waktu_daftar'   => $p->tanggal_daftar ? $p->tanggal_daftar->format('H:i') : '-',
                'url_layani'     => route('antrian', ['pilih' => $p->id_pendaftaran]),
            ];
        });

        return response()->json([
            'success'   => true,
            'count'     => $pasienList->count(),
            'pasien'    => $pasienList,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Endpoint API internal: Memproses pembayaran kasir & cetak nota
     */
    public function prosesBayar(Request $request)
    {
        $data = $request->validate([
            'id_pendaftaran' => 'required|exists:pendaftaran,id_pendaftaran',
            'id_layanan'     => 'nullable|exists:layanan,id_layanan',
            'total_bayar'    => 'required|numeric|min:0',
            'total_layanan'  => 'nullable|numeric|min:0',
            'total_resep'    => 'nullable|numeric|min:0',
            'jasa_perawat'   => 'nullable|numeric|min:0',
            'jumlah_dibayar' => 'required|numeric|min:0',
            'metode_bayar'   => 'nullable|string',
        ]);

        $pendaftaran = Pendaftaran::with(['pasien', 'poli', 'resep'])->findOrFail($data['id_pendaftaran']);
        $totalBayar = (float) $data['total_bayar'];
        $tunai = (float) $data['jumlah_dibayar'];

        if ($tunai < $totalBayar) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah uang tunai tidak mencukupi total pembayaran!',
            ], 422);
        }

        $kembalian = $tunai - $totalBayar;

        DB::beginTransaction();
        try {
            $countToday = Transaksi::whereDate('created_at', now()->toDateString())->count() + 1;
            $noInvoice = 'TRX-' . date('Ymd') . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

            $transaksi = Transaksi::create([
                'no_invoice'        => $noInvoice,
                'id_pendaftaran'    => $pendaftaran->id_pendaftaran,
                'id_resep'          => $pendaftaran->resep?->id_resep,
                'id_layanan'        => $data['id_layanan'] ?? null,
                'id_pegawai'        => auth()->id(),
                'total_layanan'     => $data['total_layanan'] ?? 0,
                'total_resep'       => $data['total_resep'] ?? 0,
                'jasa_perawat'      => $data['jasa_perawat'] ?? 50000,
                'total_bayar'       => $totalBayar,
                'jumlah_dibayar'    => $tunai,
                'kembalian'         => $kembalian,
                'metode_bayar'      => strtoupper($data['metode_bayar'] ?? 'TUNAI'),
                'status_pembayaran' => 'lunas',
                'waktu_pembayaran'  => now(),
            ]);

            // Jika pasien memiliki resep obat, teruskan antrean ke Farmasi/Apotek
            if ($pendaftaran->resep) {
                $pendaftaran->update(['status_antrian' => 'menunggu_obat']);
                $pendaftaran->resep->update(['status_resep' => 'siap_racik']);
            } else {
                // Pasien tanpa resep langsung selesai
                $pendaftaran->update(['status_antrian' => 'selesai']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil diproses dan disimpan ke sistem!',
                'data'    => [
                    'no_invoice'  => $transaksi->no_invoice,
                    'pasien'      => $pendaftaran->pasien?->nama_pasien ?? 'Pasien Umum',
                    'total_bayar' => $totalBayar,
                    'tunai'       => $tunai,
                    'kembalian'   => $kembalian,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pembayaran: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Query antrian pasien yang siap melakukan pembayaran di loket kasir
     * Hanya mengambil pasien yang berstatus 'menunggu_kasir' dan belum lunas
     */
    private function getAntrianQuery()
    {
        $query = Pendaftaran::with(['pasien', 'poli', 'resep.details.obat', 'transaksi', 'rekamMedis.tindakanMedis.layanan'])
            ->where(function ($q) {
                $q->whereDoesntHave('transaksi', function ($t) {
                    $t->where('status_pembayaran', 'lunas');
                });
            })
            ->where('status_antrian', 'menunggu_kasir');

        // Prioritaskan antrean pelayanan hari ini agar tidak ada nomor antrean ganda dari tanggal lampau
        $countHariIni = (clone $query)->whereDate('tanggal_daftar', today())->count();
        if ($countHariIni > 0) {
            $query->whereDate('tanggal_daftar', today());
        }

        return $query->orderBy('no_antrian', 'asc');
    }
}
