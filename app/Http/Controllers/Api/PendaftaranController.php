<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pasien;
use App\Models\Pendaftaran;
use App\Models\Poli;
use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
    /**
     * POST /api/v1/pendaftaran
     * Mendaftarkan pasien ke Poli untuk berobat (baik dari Loket maupun Mobile)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_pasien'    => 'required|exists:pasien,id_pasien',
            'id_poli'      => 'required|exists:poli,id_poli',
            'id_dokter'    => 'nullable|exists:pegawai,id_pegawai',
            'cara_bayar'   => 'nullable|string|in:umum,bpjs,asuransi',
            'keluhan_awal' => 'nullable|string',
        ]);

        $poli = Poli::findOrFail($validated['id_poli']);
        $kodePoli = $poli->kode_poli ?? 'POL';

        // Hitung antrian hari ini di poli tersebut untuk generate nomor antrian
        $countPoliToday = Pendaftaran::where('id_poli', $poli->id_poli)
            ->whereDate('tanggal_daftar', today())
            ->count() + 1;
        $noAntrian = $kodePoli . '-' . str_pad($countPoliToday, 3, '0', STR_PAD_LEFT);
        while (Pendaftaran::where('id_poli', $poli->id_poli)->whereDate('tanggal_daftar', today())->where('no_antrian', $noAntrian)->exists()) {
            $countPoliToday++;
            $noAntrian = $kodePoli . '-' . str_pad($countPoliToday, 3, '0', STR_PAD_LEFT);
        }

        $totalToday = Pendaftaran::whereDate('tanggal_daftar', today())->count() + 1;
        $noPendaftaran = 'REG-' . date('Ymd') . '-' . str_pad($totalToday, 3, '0', STR_PAD_LEFT);
        while (Pendaftaran::where('no_pendaftaran', $noPendaftaran)->exists()) {
            $totalToday++;
            $noPendaftaran = 'REG-' . date('Ymd') . '-' . str_pad($totalToday, 3, '0', STR_PAD_LEFT);
        }

        $pendaftaran = Pendaftaran::create([
            'no_pendaftaran' => $noPendaftaran,
            'id_pasien'      => $validated['id_pasien'],
            'id_poli'        => $validated['id_poli'],
            'id_dokter'      => $validated['id_dokter'] ?? null,
            'no_antrian'     => $noAntrian,
            'tanggal_daftar' => now(),
            'cara_bayar'     => $validated['cara_bayar'] ?? 'umum',
            'status_antrian' => 'menunggu_skrining',
            'keluhan_awal'   => $validated['keluhan_awal'] ?? null,
        ]);

        $pendaftaran->load(['pasien', 'poli', 'dokter']);

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran antrian berhasil dibuat dengan nomor antrian ' . $noAntrian,
            'data'    => $pendaftaran,
        ], 201);
    }

    /**
     * GET /api/v1/antrian/hari-ini
     * Mengambil ringkasan antrian yang sedang berjalan di rumah sakit hari ini
     */
    public function antrianHariIni()
    {
        $antrian = Pendaftaran::with(['pasien', 'poli', 'dokter'])
            ->whereDate('tanggal_daftar', today())
            ->orderBy('id_pendaftaran')
            ->get();

        $grouped = $antrian->groupBy('id_poli')->map(function ($items) {
            $current = $items->firstWhere('status_antrian', 'sedang_diperiksa') 
                ?? $items->firstWhere('status_antrian', 'menunggu_dokter')
                ?? $items->firstWhere('status_antrian', 'menunggu_skrining');

            return [
                'nama_poli'         => $items->first()->poli->nama_poli ?? '-',
                'total_antrian'     => $items->count(),
                'sedang_dilayani'   => $current ? $current->no_antrian : '-',
                'sisa_antrian'      => $items->whereNotIn('status_antrian', ['selesai', 'dibatalkan'])->count(),
                'daftar_antrian'    => $items,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Status antrian hari ini berhasil dimuat',
            'data'    => $grouped,
        ]);
    }

    /**
     * GET /api/v1/pendaftaran/{id}
     * Menampilkan seluruh rekapan journey pasien dari pendaftaran hingga rekam medis & resep
     */
    public function show($id)
    {
        $pendaftaran = Pendaftaran::with([
            'pasien',
            'poli',
            'dokter',
            'screening.perawat',
            'rekamMedis.tindakanMedis.layanan',
            'resep.details.obat',
            'transaksi.details',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Detail alur kunjungan pasien berhasil dimuat',
            'data'    => $pendaftaran,
        ]);
    }
}
