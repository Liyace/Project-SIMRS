<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pasien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasienController extends Controller
{
    /**
     * GET /api/v1/pasien
     * Pencarian pasien berdasarkan NIK, No. RM, atau Nama
     */
    public function index(Request $request)
    {
        $query = Pasien::query();

        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_pasien', 'like', "%{$keyword}%")
                  ->orWhere('nik', 'like', "%{$keyword}%")
                  ->orWhere('rm', 'like', "%{$keyword}%");
            });
        }

        $pasiens = $query->orderBy('nama_pasien')->paginate($request->query('per_page', 20));

        return response()->json([
            'success' => true,
            'message' => 'Data pasien berhasil dimuat',
            'data'    => $pasiens,
        ]);
    }

    /**
     * GET /api/v1/pasien/{id}
     */
    public function show($id)
    {
        $pasien = Pasien::with(['pendaftaran' => function ($q) {
            $q->with(['poli', 'dokter'])->orderByDesc('created_at')->limit(5);
        }])->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Detail pasien berhasil dimuat',
            'data'    => $pasien,
        ]);
    }

    /**
     * POST /api/v1/pasien
     * Registrasi pasien langsung di loket pendaftaran (Walk-in)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik'           => 'required|string|unique:pasien,nik',
            'nama_pasien'   => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'no_hp'         => 'nullable|string|max:20',
            'alamat'        => 'nullable|string',
            'password'      => 'nullable|string|min:4',
        ]);

        $count = Pasien::count() + 1;
        $noRm = 'RM-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        $pasien = Pasien::create([
            'rm'            => $noRm,
            'nik'           => $validated['nik'],
            'nama_pasien'   => $validated['nama_pasien'],
            'password_hash' => !empty($validated['password']) ? Hash::make($validated['password']) : Hash::make('12345'),
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
            'no_hp'         => $validated['no_hp'] ?? null,
            'alamat'        => $validated['alamat'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pasien berhasil didaftarkan di sistem loket',
            'data'    => $pasien,
        ], 201);
    }

    /**
     * PUT /api/v1/pasien/{id}
     */
    public function update(Request $request, $id)
    {
        $pasien = Pasien::findOrFail($id);

        $validated = $request->validate([
            'nama_pasien'   => 'sometimes|string|max:255',
            'jenis_kelamin' => 'sometimes|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'no_hp'         => 'nullable|string|max:20',
            'alamat'        => 'nullable|string',
        ]);

        $pasien->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data pasien berhasil diperbarui',
            'data'    => $pasien,
        ]);
    }
}
