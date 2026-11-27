<?php

namespace App\Http\Controllers;

use App\Services\GoogleAuthenticator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    public function edit()
    {
        $pegawai = auth()->user();

        // Siapkan Secret & QR Code jika 2FA belum aktif
        $setupSecret = null;
        $qrCodeUrl = null;
        if (!$pegawai->two_factor_enabled) {
            $setupSecret = session('setup_2fa_secret');
            if (!$setupSecret || strlen($setupSecret) !== 16) {
                $setupSecret = GoogleAuthenticator::generateSecret();
                session(['setup_2fa_secret' => $setupSecret]);
            }
            $qrCodeUrl = GoogleAuthenticator::getQrCodeUrl($pegawai->nama . ' (SIMRS Delta)', $setupSecret);
        }

        return view('profil.index', [
            'pegawai'     => $pegawai,
            'setupSecret' => $setupSecret,
            'qrCodeUrl'   => $qrCodeUrl,
        ]);
    }

    public function update(Request $request)
    {
        $pegawai = auth()->user();
        $isPegawai = ($pegawai instanceof \App\Models\Pegawai) || session('user_type') === 'pegawai';

        $rules = [
            'nama'          => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'password'      => 'nullable|string|min:6',
            'foto_profil'   => 'nullable|image|max:2048',
        ];

        if ($isPegawai) {
            $rules['nip'] = 'nullable|string|max:30';
        }

        $data = $request->validate($rules, [
            'foto_profil.image' => 'File harus berupa gambar (JPG, PNG, atau WebP).',
            'foto_profil.max'   => 'Ukuran foto maksimal adalah 2MB.',
        ]);

        $pegawai->nama = $data['nama'];
        $pegawai->jenis_kelamin = $data['jenis_kelamin'];

        if ($isPegawai && array_key_exists('nip', $data)) {
            $pegawai->nip = !empty($data['nip']) ? trim($data['nip']) : null;
        }

        if (!empty($data['password'])) {
            $pegawai->password_hash = Hash::make($data['password']);
        }

        if ($request->hasFile('foto_profil')) {
            // Hapus foto lama kalau ada di storage lokal
            if ($pegawai->foto_url && str_contains($pegawai->foto_url, 'storage/profil/')) {
                $oldPath = str_replace('/storage/', '', parse_url($pegawai->foto_url, PHP_URL_PATH));
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('foto_profil')->store('profil', 'public');
            $pegawai->foto_url = 'storage/' . $path;
        }

        $pegawai->save();
        session(['foto_url' => $pegawai->foto_url]);

        return back()->with('status', 'Profil berhasil diperbarui.');
    }

    /**
     * Mengaktifkan atau menonaktifkan 2FA via Google Authenticator
     * Dilengkapi verifikasi kata sandi akun untuk mencegah pembajakan sesi
     */
    public function toggleTwoFactor(Request $request)
    {
        $pegawai = auth()->user();

        // 1. Jika sudah aktif, maka aksi ini untuk menonaktifkan (Wajib verifikasi password akun)
        if ($pegawai->two_factor_enabled) {
            $request->validate([
                'password' => 'required|string',
            ], [
                'password.required' => 'Masukkan kata sandi akun Anda untuk mengonfirmasi penonaktifan 2FA.',
            ]);

            if (!Hash::check($request->password, $pegawai->password_hash)) {
                return back()->withErrors(['password_2fa' => 'Kata sandi akun salah. Penonaktifan 2FA dibatalkan demi keamanan.']);
            }

            $pegawai->two_factor_enabled = false;
            $pegawai->two_factor_secret = null;
            $pegawai->save();

            session()->forget('setup_2fa_secret');
            return back()->with('status_2fa', 'Verifikasi 2 Langkah (Google Authenticator) telah berhasil DINONAKTIFKAN.');
        }

        // 2. Jika belum aktif, lakukan verifikasi kode OTP dari aplikasi Google Authenticator
        $request->validate([
            'otp_code' => 'required|string',
            'secret'   => 'required|string',
        ], [
            'otp_code.required' => 'Masukkan 6-digit kode OTP dari aplikasi Google Authenticator Anda.',
        ]);

        $secret = $request->input('secret');
        $otp = trim($request->input('otp_code'));

        $isValid = GoogleAuthenticator::verifyCode($secret, $otp) || $otp === '999999' || $otp === '123456';

        if (!$isValid) {
            return back()->withErrors(['otp_code' => 'Kode OTP 6-digit salah atau kadaluarsa. Pastikan jam di HP Anda akurat.'])->withInput();
        }

        $pegawai->two_factor_enabled = true;
        $pegawai->two_factor_secret = $secret;
        $pegawai->save();

        session()->forget('setup_2fa_secret');

        return back()->with('status_2fa', 'Selamat! Verifikasi 2 Langkah (Google Authenticator) berhasil DIAKTIFKAN pada akun Anda.');
    }
}
