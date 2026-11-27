<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pasien;
use App\Models\Pegawai;
use App\Models\PermintaanReset;
use App\Models\Poli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminPenggunaController extends Controller
{
    /**
     * Dashboard Pemantauan & Manajemen Pengguna (Khusus Admin IT)
     */
    public function index(Request $request)
    {
        $qPegawai = trim($request->input('q_pegawai', ''));
        $rolePegawai = trim($request->input('role_pegawai', ''));

        $pegawaiQuery = Pegawai::with('poli');
        if ($qPegawai !== '') {
            $pegawaiQuery->where(function ($q) use ($qPegawai) {
                $q->where('nik', 'like', "%{$qPegawai}%")
                  ->orWhere('nama', 'like', "%{$qPegawai}%")
                  ->orWhere('no_hp', 'like', "%{$qPegawai}%")
                  ->orWhere('role', 'like', "%{$qPegawai}%")
                  ->orWhere('spesialisasi', 'like', "%{$qPegawai}%")
                  ->orWhereHas('poli', function ($qp) use ($qPegawai) {
                      $qp->where('nama_poli', 'like', "%{$qPegawai}%")
                         ->orWhere('kode_poli', 'like', "%{$qPegawai}%");
                  });
            });
        }
        if ($rolePegawai !== '') {
            $pegawaiQuery->where('role', $rolePegawai);
        }
        $pegawais = $pegawaiQuery->orderBy('id_pegawai', 'asc')
            ->paginate(10, ['*'], 'page_pegawai')
            ->withQueryString();

        $polis = Poli::orderBy('nama_poli')->get();

        $qPasien = trim($request->input('q_pasien', ''));
        $pasienQuery = Pasien::query();
        if ($qPasien !== '') {
            $pasienQuery->where(function ($q) use ($qPasien) {
                $q->where('rm', 'like', "%{$qPasien}%")
                  ->orWhere('nik', 'like', "%{$qPasien}%")
                  ->orWhere('username', 'like', "%{$qPasien}%")
                  ->orWhere('nama_pasien', 'like', "%{$qPasien}%")
                  ->orWhere('no_hp', 'like', "%{$qPasien}%");
            });
        }
        $pasiens = $pasienQuery->orderBy('id_pasien', 'desc')
            ->paginate(10, ['*'], 'page_pasien')
            ->withQueryString();

        $pendingResetCount = PermintaanReset::where('status', 'menunggu')->count();

        $stats = [
            'total_pegawai'       => Pegawai::count(),
            'total_pasien'        => Pasien::count(),
            'total_2fa'           => Pegawai::where('two_factor_enabled', true)->count(),
            'total_dokter'        => Pegawai::where('role', 'dokter')->count(),
            'total_kasir'         => Pegawai::whereIn('role', ['kasir', 'admin_kasir'])->count(),
            'total_reset_pending' => $pendingResetCount,
        ];

        return view('admin.pengguna.index', compact('pegawais', 'pasiens', 'pendingResetCount', 'stats', 'qPegawai', 'rolePegawai', 'qPasien', 'polis'));
    }

    /**
     * Tambah Akun Staf Rumah Sakit Baru
     */
    public function storePegawai(Request $request)
    {
        $data = $request->validate([
            'nik'           => 'required|string|unique:pegawai,nik',
            'nip'           => 'nullable|string|max:30',
            'nama'          => 'required|string|max:255',
            'role'          => 'required|in:admin,dokter,perawat,kasir,admin_kasir,apoteker,loket,resepsionis',
            'jenis_kelamin' => 'required|in:L,P',
            'id_poli'       => 'nullable|exists:poli,id_poli',
            'spesialisasi'  => 'nullable|string|max:100',
            'no_hp'         => 'nullable|string|max:20',
            'password'      => 'required|string|min:5',
        ], [
            'nik.required'           => 'NIK pegawai wajib diisi.',
            'nik.unique'             => 'NIK pegawai sudah terdaftar dalam sistem.',
            'nama.required'          => 'Nama lengkap staf wajib diisi.',
            'role.required'          => 'Silakan pilih posisi/role staf.',
            'role.in'                => 'Pilihan posisi/role tidak valid.',
            'jenis_kelamin.required' => 'Silakan pilih jenis kelamin staf.',
            'jenis_kelamin.in'       => 'Pilihan jenis kelamin tidak valid.',
            'id_poli.exists'         => 'Poliklinik yang dipilih tidak valid.',
            'password.required'      => 'Password awal staf wajib diisi.',
            'password.min'           => 'Password awal minimal 5 karakter.',
        ]);

        $pegawai = Pegawai::create([
            'nik'           => $data['nik'],
            'nip'           => !empty($data['nip']) ? trim($data['nip']) : null,
            'nama'          => $data['nama'],
            'jenis_kelamin' => $data['jenis_kelamin'] ?? 'L',
            'role'          => $data['role'],
            'id_poli'       => $data['role'] === 'dokter' ? ($data['id_poli'] ?? null) : null,
            'spesialisasi'  => $data['role'] === 'dokter' ? ($data['spesialisasi'] ?? null) : null,
            'no_hp'         => $data['no_hp'] ?? null,
            'password_hash' => Hash::make($data['password']),
            'is_admin'      => $data['role'] === 'admin',
        ]);

        // Siapkan tautan WhatsApp jika nomor HP diisi
        $waUrl = null;
        if (!empty($pegawai->no_hp)) {
            $cleanHp = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $pegawai->no_hp));
            $pesan = "*PEMBERITAHUAN AKUN BARU SIMRS DELTA*\n"
                . "----------------------------------------\n"
                . "Halo, {$pegawai->nama} ({$pegawai->role})\n"
                . "Akun SIMRS Rumah Sakit Delta Anda telah aktif:\n\n"
                . "• *NIK Login:* `{$pegawai->nik}`\n"
                . "• *Password:* `{$data['password']}`\n\n"
                . "Silakan login di sistem dan segera amankan akun Anda di menu profil.";
            $waUrl = "https://wa.me/{$cleanHp}?text=" . urlencode($pesan);
        }

        return back()->with('status_pegawai', [
            'type'    => 'success',
            'message' => "Akun staf {$pegawai->nama} ({$pegawai->role}) berhasil ditambahkan!",
            'wa_url'  => $waUrl,
            'nama'    => $pegawai->nama,
        ]);
    }

    /**
     * Atur Penempatan Poliklinik & Spesialisasi Dokter oleh Administrator
     */
    public function updatePenempatanDokter(Request $request, $id)
    {
        $pegawai = Pegawai::findOrFail($id);

        $data = $request->validate([
            'id_poli'      => 'nullable|exists:poli,id_poli',
            'spesialisasi' => 'nullable|string|max:100',
        ], [
            'id_poli.exists' => 'Poliklinik yang dipilih tidak valid.',
        ]);

        $pegawai->id_poli = !empty($data['id_poli']) ? $data['id_poli'] : null;
        $pegawai->spesialisasi = !empty($data['spesialisasi']) ? trim($data['spesialisasi']) : null;
        $pegawai->save();

        $namaPoli = $pegawai->poli ? $pegawai->poli->nama_poli : 'Rotasi / Belum Ditentukan';

        return back()->with('status_pegawai', [
            'type'    => 'success',
            'message' => "Penempatan dokter {$pegawai->nama} berhasil diatur ke {$namaPoli}!",
            'nama'    => $pegawai->nama,
        ]);
    }

    /**
     * Reset Password Pegawai oleh Admin IT
     */
    public function resetPasswordPegawai(Request $request, $id)
    {
        $pegawai = Pegawai::findOrFail($id);

        // Generate password sementara yang aman dan mudah dibaca
        $tempPassword = 'Delta#' . random_int(1000, 9999);
        $pegawai->password_hash = Hash::make($tempPassword);
        $pegawai->save();

        $waUrl = null;
        if (!empty($pegawai->no_hp)) {
            $cleanHp = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $pegawai->no_hp));
            $pesan = "*RESET PASSWORD SIMRS RUMAH SAKIT DELTA*\n"
                . "----------------------------------------\n"
                . "Halo, {$pegawai->nama}\n"
                . "Password akun SIMRS Anda telah direset oleh Administrator IT:\n\n"
                . "• *NIK Login:* `{$pegawai->nik}`\n"
                . "• *Password Baru:* `{$tempPassword}`\n\n"
                . "Silakan segera masuk ke portal dan perbarui password Anda di menu Profil Saya.";
            $waUrl = "https://wa.me/{$cleanHp}?text=" . urlencode($pesan);
        }

        return back()->with('status_pegawai', [
            'type'          => 'reset_password',
            'message'       => "Password untuk {$pegawai->nama} berhasil direset!",
            'temp_password' => $tempPassword,
            'wa_url'        => $waUrl,
            'nama'          => $pegawai->nama,
            'no_hp'         => $pegawai->no_hp,
        ]);
    }

    /**
     * Reset Status 2FA Pegawai (Jika pegawai lupa kode / HP hilang)
     */
    public function reset2FaPegawai(Request $request, $id)
    {
        $pegawai = Pegawai::findOrFail($id);
        $pegawai->two_factor_enabled = false;
        $pegawai->two_factor_secret = null;
        $pegawai->save();

        $waUrl = null;
        if (!empty($pegawai->no_hp)) {
            $cleanHp = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $pegawai->no_hp));
            $pesan = "*RESET VERIFIKASI 2FA SIMRS DELTA*\n"
                . "----------------------------------------\n"
                . "Halo, {$pegawai->nama}\n"
                . "Verifikasi 2 Langkah (2FA) akun SIMRS Anda telah DINONAKTIFKAN oleh Administrator IT.\n\n"
                . "Anda sekarang dapat masuk kembali menggunakan NIK dan password biasa tanpa kode OTP.\n"
                . "Jika sudah dapat mengakses HP kembali, silakan aktifkan ulang 2FA di menu profil Anda.";
            $waUrl = "https://wa.me/{$cleanHp}?text=" . urlencode($pesan);
        }

        return back()->with('status_pegawai', [
            'type'    => 'reset_2fa',
            'message' => "Verifikasi 2FA untuk {$pegawai->nama} berhasil dinonaktifkan!",
            'wa_url'  => $waUrl,
            'nama'    => $pegawai->nama,
            'no_hp'   => $pegawai->no_hp,
        ]);
    }

    /**
     * Reset Password Pasien oleh Admin IT
     */
    public function resetPasswordPasien(Request $request, $id)
    {
        $pasien = Pasien::findOrFail($id);

        // Jika pasien belum memiliki username (misal pasien loket fisik), buatkan username default dari RM
        if (empty($pasien->username)) {
            $pasien->username = strtolower(str_replace('-', '', $pasien->rm));
        }

        $tempPassword = 'Pasien#' . random_int(1000, 9999);
        $pasien->password_hash = Hash::make($tempPassword);
        $pasien->save();

        $waUrl = null;
        if (!empty($pasien->no_hp)) {
            $cleanHp = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $pasien->no_hp));
            $pesan = "*RESET PASSWORD PORTAL PASIEN RS DELTA*\n"
                . "----------------------------------------\n"
                . "Halo, {$pasien->nama_pasien}\n"
                . "Kata sandi akun pasien Anda telah direset:\n\n"
                . "• *No. RM:* `{$pasien->rm}`\n"
                . "• *NIK / Username:* `{$pasien->username}`\n"
                . "• *Password Baru:* `{$tempPassword}`\n\n"
                . "Silakan login di portal pasien RS Delta.";
            $waUrl = "https://wa.me/{$cleanHp}?text=" . urlencode($pesan);
        }

        return back()->with('status_pasien', [
            'type'          => 'reset_password',
            'message'       => "Password untuk pasien {$pasien->nama_pasien} berhasil direset!",
            'temp_password' => $tempPassword,
            'wa_url'        => $waUrl,
            'nama'          => $pasien->nama_pasien,
            'no_hp'         => $pasien->no_hp,
        ]);
    }

    /**
     * Proses Permintaan Reset Akun (Password / 2FA) yang Diajukan oleh Pengguna
     */
    public function prosesReset(Request $request, $id)
    {
        $permintaan = PermintaanReset::findOrFail($id);

        $user = null;
        $userType = $permintaan->tipe_pengguna;

        if ($userType === 'pegawai') {
            $user = Pegawai::where('nik', $permintaan->nik)->first();
        } else {
            $user = Pasien::where('nik', $permintaan->nik)->first();
        }

        if (!$user) {
            return back()->with('status_pegawai', [
                'type'    => 'error',
                'message' => 'Data pengguna untuk permohonan reset ini tidak ditemukan.',
            ]);
        }

        $tempPassword = null;
        $pesanDetail = [];

        // 1. Reset Password jika diminta
        if (in_array($permintaan->tipe_permintaan, ['password', 'keduanya'])) {
            $tempPassword = ($userType === 'pegawai' ? 'Delta#' : 'Pasien#') . random_int(1000, 9999);
            $user->password_hash = Hash::make($tempPassword);
            $permintaan->password_baru_plain = $tempPassword;
            $pesanDetail[] = "• *Password Baru Sementara:* `{$tempPassword}`";
        }

        // 2. Reset 2FA jika diminta (khusus pegawai)
        if (in_array($permintaan->tipe_permintaan, ['2fa', 'keduanya']) && $userType === 'pegawai') {
            $user->two_factor_enabled = false;
            $user->two_factor_secret = null;
            $pesanDetail[] = "• *Status 2FA:* Telah dinonaktifkan (Anda dapat masuk langsung menggunakan NIK dan password tanpa kode OTP).";
        }

        $user->save();

        // 3. Update status permintaan
        $permintaan->status = 'selesai';
        $permintaan->catatan_admin = $request->input('catatan_admin', 'Reset telah disetujui & diproses oleh Administrator RS.');
        $permintaan->diproses_oleh = auth()->id();
        $permintaan->diproses_pada = now();
        $permintaan->save();

        // 4. Siapkan URL WhatsApp untuk pengiriman ke nomor HP pengguna terdaftar
        $waUrl = null;
        $targetHp = !empty($permintaan->no_hp) ? $permintaan->no_hp : $user->no_hp;

        if (!empty($targetHp)) {
            $cleanHp = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $targetHp));
            $textWa = "*PEMBERITAHUAN RESET AKUN SIMRS RS DELTA*\n"
                . "----------------------------------------\n"
                . "Halo, {$permintaan->nama}\n"
                . "Permohonan bantuan akun Anda ({$permintaan->label_tipe_permintaan}) telah SELESAI DIPROSES oleh Administrator RS Delta:\n\n"
                . "• *Identitas Akun (NIK):* `{$permintaan->nik}`\n"
                . implode("\n", $pesanDetail) . "\n\n"
                . "Silakan segera masuk ke portal SIMRS Delta dan perbarui password/keamanan Anda di menu Profil Saya.\n\n"
                . "Terima kasih.\n_Administrator IT Rumah Sakit Delta_";

            $waUrl = "https://wa.me/{$cleanHp}?text=" . urlencode($textWa);
        }

        return back()->with('status_reset_sukses', [
            'nama'          => $permintaan->nama,
            'nik'           => $permintaan->nik,
            'tipe'          => $permintaan->label_tipe_permintaan,
            'temp_password' => $tempPassword,
            'wa_url'        => $waUrl,
            'no_hp'         => $targetHp,
            'message'       => "Permintaan reset akun untuk {$permintaan->nama} berhasil diproses!",
        ]);
    }

    /**
     * Tolak Permintaan Reset Akun
     */
    public function tolakReset(Request $request, $id)
    {
        $permintaan = PermintaanReset::findOrFail($id);
        $permintaan->status = 'ditolak';
        $permintaan->catatan_admin = $request->input('catatan_admin', 'Permintaan reset ditolak oleh Administrator RS.');
        $permintaan->diproses_oleh = auth()->id();
        $permintaan->diproses_pada = now();
        $permintaan->save();

        return back()->with('status_pegawai', [
            'type'    => 'success',
            'message' => "Permintaan reset untuk {$permintaan->nama} telah ditandai ditolak.",
        ]);
    }

    /**
     * Polling Notifikasi Permintaan Reset Real-time untuk Admin
     */
    public function getNotifikasiReset()
    {
        $pending = PermintaanReset::where('status', 'menunggu')->count();
        $latest = PermintaanReset::where('status', 'menunggu')->latest('id_permintaan')->first();

        return response()->json([
            'count'   => $pending,
            'terbaru' => $latest ? [
                'id'              => $latest->id_permintaan,
                'nama'            => $latest->nama,
                'nik'             => $latest->nik,
                'tipe_permintaan' => $latest->label_tipe_permintaan,
                'waktu'           => $latest->created_at ? $latest->created_at->diffForHumans() : 'baru saja',
            ] : null,
        ]);
    }
}
