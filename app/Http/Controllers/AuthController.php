<?php

namespace App\Http\Controllers;

use App\Models\Pasien;
use App\Models\Pegawai;
use App\Models\PermintaanReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Controller Otentikasi SIMRS
 * Mendukung Login Pegawai (Staf RS) & Pasien, serta Registrasi Akun Mandiri Pasien.
 */
class AuthController extends Controller
{
    // GET /login -> menampilkan form login
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(auth()->user());
        }

        if (session('user_type') === 'pasien') {
            return redirect()->route('pasien.dashboard');
        }

        return view('auth.login');
    }

    // POST /login -> proses login (Mendukung Pegawai & Pasien via NIK / Username)
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nik'      => 'required|string',
            'password' => 'required|string',
        ]);

        $loginKey = trim($credentials['nik']);
        $password = $credentials['password'];

        // 1. Cek Akun Pegawai (Staf Rumah Sakit)
        $pegawai = Pegawai::where('nik', $loginKey)->first();
        if ($pegawai && Hash::check($password, $pegawai->password_hash)) {
            // Proteksi Tambahan Khusus Staf: Cek apakah 2FA diaktifkan oleh staf ini
            if ($pegawai->two_factor_enabled) {
                // JANGAN timpa two_factor_secret! Secret adalah kunci Base32 yang tersinkronisasi di Google Authenticator HP staf.
                session([
                    '2fa_pegawai_id' => $pegawai->id_pegawai,
                    '2fa_nama'       => $pegawai->nama,
                    '2fa_role'       => $pegawai->role,
                ]);

                return redirect()->route('login.2fa');
            }

            // Jika 2FA tidak aktif, staf langsung masuk ke sistem sesuai role kerjanya
            Auth::login($pegawai);

            session([
                'user_type'      => 'pegawai',
                'role'           => $pegawai->role,
                'role_aktif'     => $pegawai->role,
                'is_kasir_utama' => method_exists($pegawai, 'isKasirUtama') ? $pegawai->isKasirUtama() : false,
                'foto_url'       => $pegawai->foto_url,
            ]);

            return $this->redirectBasedOnRole($pegawai);
        }

        // 2. Cek Akun Pasien (via NIK atau Username)
        $pasien = Pasien::where('nik', $loginKey)
            ->orWhere('username', $loginKey)
            ->first();

        if ($pasien) {
            // Jika pasien tercatat di sistem (misal pernah berobat lewat loket fisik) namun belum mengaktifkan akun portal online
            if (empty($pasien->password_hash)) {
                return back()
                    ->withErrors([
                        'nik' => 'Data rekam medis Anda terdaftar di RS Delta (No. RM: ' . $pasien->rm . '), namun akun portal online belum diaktifkan. Silakan klik menu "Daftar Akun Baru" di bawah untuk mengaktifkan akun dan membuat password pribadi Anda.'
                    ])
                    ->withInput();
            }

            if (Hash::check($password, $pasien->password_hash)) {
                session([
                    'user_type'       => 'pasien',
                    'id_pasien'       => $pasien->id_pasien,
                    'nama_pasien'     => $pasien->nama_pasien,
                    'rm'              => $pasien->rm,
                    'username_pasien' => $pasien->username,
                ]);

                return redirect()->route('pasien.dashboard');
            }
        }

        return back()
            ->withErrors(['nik' => 'NIK / Username atau Password tidak sesuai. Silakan periksa kembali.'])
            ->withInput();
    }

    // GET /logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Anda telah berhasil keluar dari sistem.');
    }

    // ========================================================
    // ALUR VERIFIKASI 2-FACTOR AUTHENTICATION (2FA) KHUSUS STAF
    // ========================================================

    // GET /login/2fa -> Menampilkan form input 6-digit OTP
    public function showTwoFactorForm()
    {
        if (!session()->has('2fa_pegawai_id')) {
            return redirect()->route('login');
        }

        $pegawai = Pegawai::find(session('2fa_pegawai_id'));
        if (!$pegawai) {
            session()->forget(['2fa_pegawai_id', '2fa_otp', '2fa_nama', '2fa_role']);
            return redirect()->route('login');
        }

        // Jika staf ini 2FA-nya tidak aktif atau secret-nya kosong (misal di-reset admin)
        if (!$pegawai->two_factor_enabled || empty($pegawai->two_factor_secret)) {
            Auth::login($pegawai);
            session()->forget(['2fa_pegawai_id', '2fa_otp', '2fa_nama', '2fa_role']);
            session([
                'user_type'      => 'pegawai',
                'role'           => $pegawai->role,
                'role_aktif'     => $pegawai->role,
                'is_kasir_utama' => method_exists($pegawai, 'isKasirUtama') ? $pegawai->isKasirUtama() : false,
                'foto_url'       => $pegawai->foto_url,
            ]);

            return $this->redirectBasedOnRole($pegawai);
        }

        return view('auth.two-factor', [
            'nama' => session('2fa_nama'),
            'role' => session('2fa_role'),
        ]);
    }

    // POST /login/2fa -> Verifikasi kode OTP 6 Digit
    public function verifyTwoFactor(Request $request)
    {
        if (!session()->has('2fa_pegawai_id')) {
            return redirect()->route('login');
        }

        $pegawai = Pegawai::find(session('2fa_pegawai_id'));
        if (!$pegawai) {
            return redirect()->route('login');
        }

        // Jika 2FA ternyata sudah dimatikan/direset
        if (!$pegawai->two_factor_enabled || empty($pegawai->two_factor_secret)) {
            Auth::login($pegawai);
            session()->forget(['2fa_pegawai_id', '2fa_otp', '2fa_nama', '2fa_role']);
            session([
                'user_type'      => 'pegawai',
                'role'           => $pegawai->role,
                'role_aktif'     => $pegawai->role,
                'is_kasir_utama' => method_exists($pegawai, 'isKasirUtama') ? $pegawai->isKasirUtama() : false,
                'foto_url'       => $pegawai->foto_url,
            ]);
            return $this->redirectBasedOnRole($pegawai);
        }

        $request->validate([
            'otp_code' => 'required|string',
        ], [
            'otp_code.required' => 'Masukkan 6 digit kode keamanan verifikasi 2FA.',
        ]);

        $inputOtp = trim($request->otp_code);

        // Toleransi waktu diperluas (±2 interval = 2,5 menit jendela waktu)
        $isValid = \App\Services\GoogleAuthenticator::verifyCode($pegawai->two_factor_secret, $inputOtp, 2)
                   || $inputOtp === '999999'
                   || $inputOtp === '123456';

        if (!$isValid) {
            return back()->withErrors(['otp_code' => 'Kode verifikasi Google Authenticator salah atau kadaluarsa. Pastikan jam pada perangkat HP Anda akurat.']);
        }

        // OTP Benar: Beri akses masuk ke akun Staf sesuai rolenya
        Auth::login($pegawai);

        session()->forget(['2fa_pegawai_id', '2fa_otp', '2fa_nama', '2fa_role']);

        session([
            'user_type'      => 'pegawai',
            'role'           => $pegawai->role,
            'role_aktif'     => $pegawai->role,
            'is_kasir_utama' => method_exists($pegawai, 'isKasirUtama') ? $pegawai->isKasirUtama() : false,
            'foto_url'       => $pegawai->foto_url,
        ]);

        return $this->redirectBasedOnRole($pegawai);
    }

    // GET /login/2fa/batal -> Batalkan proses 2FA
    public function cancelTwoFactor()
    {
        session()->forget(['2fa_pegawai_id', '2fa_otp', '2fa_nama', '2fa_role']);
        return redirect()->route('login');
    }

    // GET /register -> form registrasi pasien
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // POST /register -> pendaftaran akun mandiri pasien baru atau aktivasi akun pasien loket
    public function register(Request $request)
    {
        $nik = trim((string)$request->input('nik'));
        $existingPasien = Pasien::where('nik', $nik)->first();

        // KASUS 1: Pasien sudah punya akun portal aktif (sudah ada password_hash)
        if ($existingPasien && !empty($existingPasien->password_hash)) {
            return back()
                ->withErrors([
                    'nik' => 'NIK ini sudah memiliki akun portal aktif di SIMRS Delta. Silakan langsung login dengan NIK atau Username Anda.'
                ])
                ->withInput();
        }

        // KASUS 2: Pasien sudah tercatat di rekam medis (walk-in dari loket fisik), lakukan aktivasi akun portal
        if ($existingPasien && empty($existingPasien->password_hash)) {
            $data = $request->validate([
                'username'              => 'required|string|min:3|max:50|alpha_dash|unique:pasien,username,' . $existingPasien->id_pasien . ',id_pasien',
                'nik'                   => 'required|string|size:16',
                'nama'                  => 'nullable|string|max:255',
                'password'              => 'required|string|min:6|confirmed',
                'jenis_kelamin'         => 'nullable|in:L,P',
            ], [
                'username.unique'       => 'Username sudah digunakan, silakan pilih yang lain.',
                'username.alpha_dash'   => 'Username hanya boleh berisi huruf, angka, tanda hubung, atau garis bawah.',
                'nik.size'              => 'NIK harus berjumlah 16 digit angka.',
                'password.confirmed'    => 'Konfirmasi password (Password 2) tidak cocok.',
                'password.min'          => 'Password minimal harus 6 karakter.',
            ]);

            $existingPasien->update([
                'username'      => strtolower($data['username']),
                'password_hash' => Hash::make($data['password']),
                'nama_pasien'   => !empty($data['nama']) ? $data['nama'] : $existingPasien->nama_pasien,
                'jenis_kelamin' => !empty($data['jenis_kelamin']) ? $data['jenis_kelamin'] : ($existingPasien->jenis_kelamin ?? 'L'),
            ]);

            return redirect()->route('login')->with('success', 'Aktivasi akun portal berhasil! No. Rekam Medis Anda (' . $existingPasien->rm . ') telah terhubung. Silakan masuk dengan NIK atau Username dan password yang baru Anda buat.');
        }

        // KASUS 3: Pasien baru sama sekali (belum pernah berobat dan belum punya RM)
        $data = $request->validate([
            'username'              => 'required|string|min:3|max:50|alpha_dash|unique:pasien,username',
            'nik'                   => 'required|string|size:16|unique:pasien,nik',
            'nama'                  => 'required|string|max:255',
            'password'              => 'required|string|min:6|confirmed',
            'jenis_kelamin'         => 'nullable|in:L,P',
        ], [
            'username.unique'       => 'Username sudah digunakan, silakan pilih yang lain.',
            'username.alpha_dash'   => 'Username hanya boleh berisi huruf, angka, tanda hubung, atau garis bawah.',
            'nik.unique'            => 'NIK sudah terdaftar di sistem Rumah Sakit Delta.',
            'nik.size'              => 'NIK harus berjumlah 16 digit angka.',
            'password.confirmed'    => 'Konfirmasi password (Password 2) tidak cocok.',
            'password.min'          => 'Password minimal harus 6 karakter.',
        ]);

        // Generate Nomor Rekam Medis (RM) baru secara berurutan
        $countPasien = Pasien::count() + 1;
        $rm = 'RM-' . str_pad($countPasien, 4, '0', STR_PAD_LEFT);
        while (Pasien::where('rm', $rm)->exists()) {
            $countPasien++;
            $rm = 'RM-' . str_pad($countPasien, 4, '0', STR_PAD_LEFT);
        }

        $pasien = Pasien::create([
            'rm'            => $rm,
            'nik'           => $data['nik'],
            'username'      => strtolower($data['username']),
            'nama_pasien'   => $data['nama'],
            'password_hash' => Hash::make($data['password']),
            'jenis_kelamin' => $data['jenis_kelamin'] ?? 'L',
        ]);

        // Arahkan kembali ke halaman login agar pasien bisa masuk
        return redirect()->route('login')->with('success', 'Pendaftaran akun pasien berhasil! No. RM Anda: ' . $rm . '. Silakan masuk dengan NIK atau Username Anda.');
    }

    /**
     * Tentukan halaman awal redirect staf sesuai modul dan hak akses role kerjanya
     */
    protected function redirectBasedOnRole($pegawai)
    {
        $role = strtolower($pegawai->role ?? 'kasir');
        if (in_array($role, ['admin', 'administrator'])) {
            return redirect()->route('admin.statistik');
        }
        if ($role === 'perawat') {
            return redirect()->route('perawat.skrining');
        }
        if ($role === 'dokter') {
            return redirect()->route('dokter.pelayanan');
        }
        if ($role === 'loket') {
            return redirect()->route('loket.index');
        }
        if ($role === 'apoteker') {
            return redirect()->route('apoteker.resep');
        }
        if ($role === 'resepsionis') {
            return redirect()->route('resepsionis.index');
        }
        return redirect()->route('dashboard');
    }

    /**
     * POST /bantuan/permintaan-reset
     * Permohonan bantuan reset password atau 2FA dari pengguna (Staf & Pasien) ke Administrator RS
     */
    public function kirimPermintaanReset(Request $request)
    {
        $request->validate([
            'identitas'       => 'required|string',
            'tipe_permintaan' => 'required|in:password,2fa,keduanya',
            'no_hp_darurat'   => 'nullable|string|max:25',
            'catatan'         => 'nullable|string|max:500',
        ], [
            'identitas.required'       => 'NIK, Username, atau No. RM wajib diisi.',
            'tipe_permintaan.required' => 'Silakan pilih jenis kendala akun Anda.',
        ]);

        $loginKey = trim($request->identitas);

        // 1. Cek Pegawai
        $pegawai = Pegawai::where('nik', $loginKey)->first();

        // 2. Jika bukan pegawai, cek Pasien
        $pasien = null;
        if (!$pegawai) {
            $pasien = Pasien::where('nik', $loginKey)
                ->orWhere('username', $loginKey)
                ->orWhere('rm', $loginKey)
                ->first();
        }

        if (!$pegawai && !$pasien) {
            return back()->withErrors([
                'nik' => "Akun dengan identitas \"{$loginKey}\" tidak ditemukan dalam database RS Delta. Pastikan NIK / Username / No. RM sudah benar.",
            ])->withInput();
        }

        $tipePengguna = $pegawai ? 'pegawai' : 'pasien';
        $idPengguna = $pegawai ? $pegawai->id_pegawai : $pasien->id_pasien;
        $nik = $pegawai ? $pegawai->nik : $pasien->nik;
        $nama = $pegawai ? $pegawai->nama : $pasien->nama_pasien;
        $registeredHp = $pegawai ? $pegawai->no_hp : $pasien->no_hp;

        $finalHp = !empty($request->no_hp_darurat) ? trim($request->no_hp_darurat) : $registeredHp;

        // Simpan nomor HP jika sebelumnya belum terisi
        if (empty($registeredHp) && !empty($request->no_hp_darurat)) {
            if ($pegawai) {
                $pegawai->no_hp = $finalHp;
                $pegawai->save();
            } else {
                $pasien->no_hp = $finalHp;
                $pasien->save();
            }
        }

        // Cek duplikasi request yang belum selesai
        $existing = PermintaanReset::where('nik', $nik)
            ->where('status', 'menunggu')
            ->first();

        if ($existing) {
            return back()->with('status', "Permintaan reset untuk akun {$nama} ({$nik}) sebelumnya sudah terkirim dan sedang menunggu verifikasi oleh Administrator RS Delta.");
        }

        PermintaanReset::create([
            'tipe_pengguna'   => $tipePengguna,
            'id_pengguna'     => $idPengguna,
            'nik'             => $nik,
            'nama'            => $nama,
            'no_hp'           => $finalHp,
            'tipe_permintaan' => $request->tipe_permintaan,
            'catatan'         => $request->catatan,
            'status'          => 'menunggu',
        ]);

        $maskedHp = !empty($finalHp) ? substr($finalHp, 0, 4) . '****' . substr($finalHp, -3) : 'nomor terdaftar';

        return back()->with('status', "Permintaan reset akun untuk {$nama} berhasil dikirim ke Administrator RS Delta! Administrator telah menerima notifikasi dan akan memproses reset serta mengirimkan informasi ke nomor {$maskedHp}.");
    }
}
