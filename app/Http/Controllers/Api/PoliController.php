<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Poli;
use Illuminate\Http\Request;

class PoliController extends Controller
{
    /**
     * GET /api/v1/poli
     * Menampilkan seluruh poli beserta dokter yang aktif bertugas
     */
    public function index()
    {
        $polis = Poli::withCount(['pendaftaran as antrian_aktif_count' => function ($q) {
            $q->whereDate('tanggal_daftar', today())
              ->whereNotIn('status_antrian', ['selesai', 'dibatalkan']);
        }])->get();

        $dokters = Pegawai::where('role', 'dokter')->get(['id_pegawai', 'nama', 'nik']);

        return response()->json([
            'success' => true,
            'message' => 'Daftar poli berhasil dimuat',
            'data'    => [
                'poli'   => $polis,
                'dokter' => $dokters,
            ],
        ]);
    }

    /**
     * GET /api/v1/poli/{id}
     */
    public function show($id)
    {
        $poli = Poli::findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Detail poli berhasil dimuat',
            'data'    => $poli,
        ]);
    }
}
