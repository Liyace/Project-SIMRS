<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pasien;
use App\Models\Pendaftaran;
use App\Models\Poli;
use Illuminate\Http\Request;

class MobileApiController extends Controller
{
    /**
     * GET /api/v1/mobile/jadwal-poli
     * Menampilkan daftar poli dan antrian yang tersedia untuk pasien
     */
    public function jadwalPoli()
    {
        $polis = Poli::all()->map(function ($poli) {
            $antrianHariIni = Pendaftaran::where('id_poli', $poli->id_poli)
                ->whereDate('tanggal_daftar', today())
                ->count();

            return [
                'id_poli'          => $poli->id_poli,
                'kode_poli'        => $poli->kode_poli,
                'nama_poli'        => $poli->nama_poli,
                'biaya_konsultasi' => (float) $poli->biaya_konsultasi,
                'total_antrian_saat_ini' => $antrianHariIni,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Jadwal dan kuota poli berhasil dimuat',
            'data'    => $polis,
        ]);
    }

    /**
     * POST /api/v1/mobile/daftar-online
     * Pasien mengambil nomor antrian secara online dari HP
     */
    public function daftarOnline(Request $request)
    {
        $user = $request->user();
        if (!$user instanceof Pasien) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya akun pasien yang dapat melakukan pendaftaran online.',
            ], 403);
        }

        $validated = $request->validate([
            'id_poli'      => 'required|exists:poli,id_poli',
            'keluhan_awal' => 'required|string|max:500',
            'cara_bayar'   => 'nullable|in:umum,bpjs,asuransi',
        ]);

        $poli = Poli::findOrFail($validated['id_poli']);
        $kodePoli = $poli->kode_poli ?? 'POL';

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
            'id_pasien'      => $user->id_pasien,
            'id_poli'        => $validated['id_poli'],
            'no_antrian'     => $noAntrian,
            'tanggal_daftar' => now(),
            'cara_bayar'     => $validated['cara_bayar'] ?? 'umum',
            'status_antrian' => 'menunggu_skrining',
            'keluhan_awal'   => $validated['keluhan_awal'],
        ]);

        $pendaftaran->load('poli');

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran online berhasil! Nomor antrian Anda adalah ' . $noAntrian,
            'data'    => [
                'id_pendaftaran' => $pendaftaran->id_pendaftaran,
                'no_pendaftaran' => $pendaftaran->no_pendaftaran,
                'no_antrian'     => $pendaftaran->no_antrian,
                'nama_poli'      => $pendaftaran->poli->nama_poli,
                'status_antrian' => $pendaftaran->status_antrian,
                'tanggal_daftar' => $pendaftaran->tanggal_daftar->format('Y-m-d H:i:s'),
            ],
        ], 201);
    }

    /**
     * GET /api/v1/mobile/tiket-antrian-aktif
     * Cek status tiket antrian aktif milik pasien yang sedang login
     */
    public function tiketAntrianAktif(Request $request)
    {
        $user = $request->user();
        if (!$user instanceof Pasien) {
            return response()->json(['success' => false, 'message' => 'Akses khusus pasien'], 403);
        }

        $pendaftaran = Pendaftaran::with(['poli', 'dokter'])
            ->where('id_pasien', $user->id_pasien)
            ->whereDate('tanggal_daftar', today())
            ->whereNotIn('status_antrian', ['selesai', 'dibatalkan'])
            ->latest('id_pendaftaran')
            ->first();

        if (!$pendaftaran) {
            return response()->json([
                'success' => true,
                'message' => 'Tidak ada tiket antrian aktif hari ini',
                'data'    => null,
            ]);
        }

        // Cek nomor antrian yang sedang dipanggil di poli tersebut
        $currentServing = Pendaftaran::where('id_poli', $pendaftaran->id_poli)
            ->whereDate('tanggal_daftar', today())
            ->where('status_antrian', 'sedang_diperiksa')
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'Tiket antrian aktif berhasil dimuat',
            'data'    => [
                'tiket'           => $pendaftaran,
                'nomor_dipanggil' => $currentServing ? $currentServing->no_antrian : '-',
            ],
        ]);
    }

    /**
     * GET /api/v1/mobile/riwayat-kunjungan
     * Rekam jejak medis pasien: diagnosa, obat, dan tagihan
     */
    public function riwayatKunjungan(Request $request)
    {
        $user = $request->user();
        if (!$user instanceof Pasien) {
            return response()->json(['success' => false, 'message' => 'Akses khusus pasien'], 403);
        }

        $riwayat = Pendaftaran::with([
            'poli',
            'dokter',
            'screening',
            'rekamMedis.tindakanMedis.layanan',
            'resep.details.obat',
            'transaksi.details',
        ])
        ->where('id_pasien', $user->id_pasien)
        ->orderByDesc('tanggal_daftar')
        ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Riwayat kunjungan rekam medis berhasil dimuat',
            'data'    => $riwayat,
        ]);
    }

    /**
     * GET /api/v1/mobile/riwayat-kunjungan/{id}
     * Detail lengkap satu riwayat kunjungan medis pasien
     */
    public function detailRiwayatKunjungan(Request $request, $id)
    {
        $user = $request->user();
        if (!$user instanceof Pasien) {
            return response()->json(['success' => false, 'message' => 'Akses khusus pasien'], 403);
        }

        $kunjungan = Pendaftaran::with([
            'poli',
            'dokter',
            'screening',
            'rekamMedis.tindakanMedis.layanan',
            'resep.details.obat',
            'transaksi.details',
        ])
        ->where('id_pasien', $user->id_pasien)
        ->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Detail riwayat kunjungan berhasil dimuat',
            'data'    => $kunjungan,
        ]);
    }

    /**
     * GET /api/v1/mobile/kartu-pasien
     * Data kartu pasien digital CR80 untuk aplikasi mobile
     */
    public function kartuPasien(Request $request)
    {
        $user = $request->user();
        if (!$user instanceof Pasien) {
            return response()->json(['success' => false, 'message' => 'Akses khusus pasien'], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data kartu pasien digital berhasil dimuat',
            'data'    => [
                'id_pasien'        => $user->id_pasien,
                'no_rm'            => $user->rm,
                'nik'              => $user->nik,
                'username'         => $user->username,
                'nama_pasien'      => $user->nama_pasien,
                'jenis_kelamin'    => $user->jenis_kelamin,
                'gender_label'     => $user->jenis_kelamin === 'P' ? 'Perempuan' : 'Laki-laki',
                'tanggal_lahir'    => $user->tanggal_lahir,
                'no_hp'            => $user->no_hp,
                'alamat'           => $user->alamat,
                'foto_url'         => $user->foto_url,
                'barcode_code'     => $user->rm,
                'terdaftar_sejak'  => $user->created_at ? $user->created_at->format('d M Y') : '-',
                'nama_rs'          => 'Rumah Sakit Delta',
                'tagline_rs'       => 'Dedikasi Untuk Kesembuhan',
            ],
        ]);
    }

    /**
     * GET /api/v1/mobile/komparasi
     * Data komparasi perkembangan kesehatan dan rekam medis kunjungan pasien
     */
    public function komparasi(Request $request)
    {
        $user = $request->user();
        if (!$user instanceof Pasien) {
            return response()->json(['success' => false, 'message' => 'Akses khusus pasien'], 403);
        }

        $kunjunganList = Pendaftaran::with(['screening', 'rekamMedis', 'poli', 'dokter', 'resep.details.obat'])
            ->where('id_pasien', $user->id_pasien)
            ->whereNotNull('status_antrian')
            ->where('status_antrian', '!=', 'dibatalkan')
            ->orderByDesc('tanggal_daftar')
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data komparasi kunjungan berhasil dimuat',
            'data'    => $kunjunganList,
        ]);
    }

    /**
     * PUT /api/v1/mobile/profil
     * Update data profil dan ubah password dari aplikasi mobile
     */
    public function updateProfil(Request $request)
    {
        $user = $request->user();
        if (!$user instanceof Pasien) {
            return response()->json(['success' => false, 'message' => 'Akses khusus pasien'], 403);
        }

        $validated = $request->validate([
            'nama_pasien' => 'nullable|string|max:255',
            'username'    => 'nullable|string|min:3|max:50|alpha_dash|unique:pasien,username,' . $user->id_pasien . ',id_pasien',
            'no_hp'       => 'nullable|string|max:20',
            'alamat'      => 'nullable|string|max:500',
            'password'    => 'nullable|string|min:4|confirmed',
        ]);

        if (!empty($validated['nama_pasien'])) {
            $user->nama_pasien = $validated['nama_pasien'];
        }
        if (!empty($validated['username'])) {
            $user->username = strtolower($validated['username']);
        }
        if (isset($validated['no_hp'])) {
            $user->no_hp = $validated['no_hp'];
        }
        if (isset($validated['alamat'])) {
            $user->alamat = $validated['alamat'];
        }
        if (!empty($validated['password'])) {
            $user->password_hash = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil pasien berhasil diperbarui',
            'data'    => [
                'id_pasien'     => $user->id_pasien,
                'no_rm'         => $user->rm,
                'nik'           => $user->nik,
                'username'      => $user->username,
                'nama_pasien'   => $user->nama_pasien,
                'jenis_kelamin' => $user->jenis_kelamin,
                'no_hp'         => $user->no_hp,
                'alamat'        => $user->alamat,
            ],
        ]);
    }
}
