<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pasien;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * POST /api/v1/auth/login
     * Single auth endpoint: bisa login Pegawai (Admin, Dokter, Perawat, Kasir, Apoteker)
     * atau Pasien (untuk Mobile App).
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string', // NIK pegawai atau NIK/No. RM pasien
            'password' => 'required|string',
        ]);

        $username = $request->username;
        $password = $request->password;

        // 1. Cek di tabel Pegawai (berdasarkan NIK)
        $pegawai = Pegawai::where('nik', $username)->first();
        if ($pegawai && Hash::check($password, $pegawai->password_hash)) {
            $token = $pegawai->createToken('pegawai-token', [$pegawai->role])->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil sebagai pegawai (' . $pegawai->role . ')',
                'data'    => [
                    'token'      => $token,
                    'token_type' => 'Bearer',
                    'user_type'  => 'pegawai',
                    'user'       => [
                        'id_pegawai' => $pegawai->id_pegawai,
                        'nik'        => $pegawai->nik,
                        'nama'       => $pegawai->nama,
                        'role'       => $pegawai->role,
                        'is_admin'   => (bool) $pegawai->is_admin,
                        'foto_url'   => $pegawai->foto_url,
                    ],
                ],
            ]);
        }

        // 2. Cek di tabel Pasien (berdasarkan NIK, No. RM, atau Username)
        $pasien = Pasien::where('nik', $username)
            ->orWhere('rm', $username)
            ->orWhere('username', $username)
            ->first();
        if ($pasien && $pasien->password_hash && Hash::check($password, $pasien->password_hash)) {
            $token = $pasien->createToken('pasien-token', ['pasien'])->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil sebagai pasien',
                'data'    => [
                    'token'      => $token,
                    'token_type' => 'Bearer',
                    'user_type'  => 'pasien',
                    'user'       => [
                        'id_pasien'     => $pasien->id_pasien,
                        'no_rm'         => $pasien->rm,
                        'nik'           => $pasien->nik,
                        'username'      => $pasien->username,
                        'nama_pasien'   => $pasien->nama_pasien,
                        'jenis_kelamin' => $pasien->jenis_kelamin,
                        'no_hp'         => $pasien->no_hp,
                        'role'          => 'pasien',
                    ],
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'NIK / Nomor RM atau Password salah!',
        ], 401);
    }

    /**
     * POST /api/v1/auth/register-pasien
     * Registrasi mandiri oleh pasien dari Aplikasi Mobile Flutter.
     */
    public function registerPasien(Request $request)
    {
        $validated = $request->validate([
            'nik'           => 'required|string|size:16|unique:pasien,nik',
            'nama_pasien'   => 'required|string|max:255',
            'password'      => 'required|string|min:4',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'no_hp'         => 'nullable|string|max:20',
            'alamat'        => 'nullable|string',
        ]);

        // Generate No. Rekam Medis otomatis (RM-XXXX)
        $count = Pasien::count() + 1;
        $noRm = 'RM-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        $pasien = Pasien::create([
            'rm'            => $noRm,
            'nik'           => $validated['nik'],
            'nama_pasien'   => $validated['nama_pasien'],
            'password_hash' => Hash::make($validated['password']),
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
            'no_hp'         => $validated['no_hp'] ?? null,
            'alamat'        => $validated['alamat'] ?? null,
        ]);

        $token = $pasien->createToken('pasien-token', ['pasien'])->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran akun pasien berhasil. Nomor Rekam Medis Anda adalah ' . $noRm,
            'data'    => [
                'token'      => $token,
                'token_type' => 'Bearer',
                'user_type'  => 'pasien',
                'user'       => [
                    'id_pasien'     => $pasien->id_pasien,
                    'no_rm'         => $pasien->rm,
                    'nik'           => $pasien->nik,
                    'nama_pasien'   => $pasien->nama_pasien,
                    'role'          => 'pasien',
                ],
            ],
        ], 201);
    }

    /**
     * GET /api/v1/auth/me
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'message' => 'Informasi pengguna berhasil dimuat',
            'data'    => $user,
        ]);
    }

    /**
     * POST /api/v1/auth/logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil. Token telah dihapus.',
        ]);
    }

    /**
     * POST /api/v1/auth/lupa-password
     * Pasien (atau staf) mengajukan bantuan reset password dari Aplikasi Mobile
     */
    public function lupaPassword(Request $request)
    {
        $request->validate([
            'identitas'       => 'required|string',
            'tipe_permintaan' => 'nullable|in:password,2fa,keduanya',
            'no_hp_darurat'   => 'nullable|string|max:25',
            'catatan'         => 'nullable|string|max:500',
        ]);

        $loginKey = trim($request->identitas);
        $user = Pasien::where('nik', $loginKey)
            ->orWhere('username', $loginKey)
            ->orWhere('rm', $loginKey)
            ->first();

        if (!$user) {
            $pegawai = Pegawai::where('nik', $loginKey)->first();
            if ($pegawai) {
                $userType = 'pegawai';
                $nama = $pegawai->nama;
                $nik = $pegawai->nik;
                $idUser = $pegawai->id_pegawai;
                $targetHp = $request->no_hp_darurat ?? $pegawai->no_hp;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Identitas (NIK, Username, atau No. RM) tidak terdaftar dalam sistem SIMRS Delta.',
                ], 404);
            }
        } else {
            $userType = 'pasien';
            $nama = $user->nama_pasien;
            $nik = $user->nik;
            $idUser = $user->id_pasien;
            $targetHp = $request->no_hp_darurat ?? $user->no_hp;
        }

        // Cek jika sudah ada permohonan yang berstatus menunggu
        $existing = \App\Models\PermintaanReset::where('nik', $nik)
            ->where('status', 'menunggu')
            ->first();

        if ($existing) {
            return response()->json([
                'success' => true,
                'message' => 'Permohonan bantuan akun Anda sebelumnya sudah tercatat dan sedang menunggu verifikasi Administrator RS.',
                'data'    => $existing,
            ]);
        }

        $permintaan = \App\Models\PermintaanReset::create([
            'tipe_pengguna'   => $userType,
            'id_pengguna'     => $idUser,
            'nik'             => $nik,
            'nama'            => $nama,
            'no_hp'           => $targetHp,
            'tipe_permintaan' => $request->tipe_permintaan ?? 'password',
            'catatan'         => $request->catatan ?? 'Pengajuan lupa password dari aplikasi mobile',
            'status'          => 'menunggu',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permohonan reset password berhasil dikirim ke Administrator Rumah Sakit Delta.',
            'data'    => $permintaan,
        ], 201);
    }
}
