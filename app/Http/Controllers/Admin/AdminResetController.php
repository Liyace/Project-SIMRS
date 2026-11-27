<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pasien;
use App\Models\Pegawai;
use App\Models\PermintaanReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminResetController extends Controller
{
    /**
     * Halaman Utama: Daftar Permintaan Reset Password & 2FA Pengguna
     */
    public function index(Request $request)
    {
        $q = trim($request->input('q', ''));
        $status = $request->input('status', 'semua');
        $tipe = $request->input('tipe', 'semua');

        $query = PermintaanReset::with(['adminPemroses']);

        if (!empty($q)) {
            $query->where(function ($sub) use ($q) {
                $sub->where('nama', 'like', "%{$q}%")
                    ->orWhere('nik', 'like', "%{$q}%")
                    ->orWhere('no_hp', 'like', "%{$q}%")
                    ->orWhere('catatan', 'like', "%{$q}%");
            });
        }

        if (in_array($status, ['menunggu', 'selesai', 'ditolak'])) {
            $query->where('status', $status);
        }

        if (in_array($tipe, ['pegawai', 'pasien'])) {
            $query->where('tipe_pengguna', $tipe);
        }

        $permintaanResets = $query->orderByRaw("CASE WHEN status = 'menunggu' THEN 0 ELSE 1 END")
            ->orderByDesc('id_permintaan')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total'    => PermintaanReset::count(),
            'menunggu' => PermintaanReset::where('status', 'menunggu')->count(),
            'selesai'  => PermintaanReset::where('status', 'selesai')->count(),
            'ditolak'  => PermintaanReset::where('status', 'ditolak')->count(),
        ];

        $halamanAktif = 'admin_reset';

        return view('admin.reset.index', compact(
            'permintaanResets',
            'stats',
            'q',
            'status',
            'tipe',
            'halamanAktif'
        ));
    }

    /**
     * Proses Permintaan Reset Akun (Password / 2FA) yang Diajukan Pengguna
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
                'message' => 'Data pengguna untuk permohonan reset ini tidak ditemukan dalam database.',
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
            $pesanDetail[] = "• *Status 2FA:* Telah dinonaktifkan (Anda dapat masuk langsung menggunakan NIK dan password baru tanpa kode OTP).";
        }

        $user->save();

        // 3. Update status permohonan
        $permintaan->status = 'selesai';
        $permintaan->catatan_admin = $request->input('catatan_admin', 'Reset telah disetujui & diproses oleh Administrator RS.');
        $permintaan->diproses_oleh = auth()->id();
        $permintaan->diproses_pada = now();
        $permintaan->save();

        // 4. Siapkan URL WhatsApp untuk pengiriman ke nomor HP pengguna
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
                . "Silakan segera masuk ke portal SIMRS Delta dan perbarui password/keamanan Anda di menu Pengaturan Profil.\n\n"
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
            'message' => "Permintaan reset untuk {$permintaan->nama} telah ditolak.",
        ]);
    }

    /**
     * Polling Notifikasi Permintaan Reset Real-time untuk Admin
     */
    public function getNotifikasiReset()
    {
        $pending = PermintaanReset::where('status', 'menunggu')->count();
        $latest = PermintaanReset::where('status', 'menunggu')->latest('id_permintaan')->first();

        $pendingList = PermintaanReset::where('status', 'menunggu')
            ->latest('id_permintaan')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'id'              => $item->id_permintaan,
                    'nama'            => $item->nama,
                    'nik'             => $item->nik,
                    'tipe_pengguna'   => $item->tipe_pengguna === 'pegawai' ? 'Staf RS' : 'Pasien',
                    'tipe_permintaan' => $item->label_tipe_permintaan,
                    'no_hp'           => $item->no_hp ?? '-',
                    'waktu'           => $item->created_at ? $item->created_at->diffForHumans() : 'baru saja',
                ];
            });

        return response()->json([
            'count'   => $pending,
            'list'    => $pendingList,
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
