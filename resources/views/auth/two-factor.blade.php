<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi 2FA Staf - Rumah Sakit Delta</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logodelta1.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}?v={{ filemtime(public_path('css/login.css')) }}">
    <style>
        .otp-input {
            letter-spacing: 12px;
            font-size: 24px !important;
            font-weight: 700;
            text-align: center;
            padding: 12px !important;
        }
        .demo-otp-pill {
            background: rgba(255, 255, 255, 0.2);
            border: 1px dashed rgba(255, 255, 255, 0.5);
            padding: 10px 14px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        
        <!-- SISI KIRI: FORM VERIFIKASI 2FA -->
        <div class="panel-left">
            
            <!-- Tombol Batal & Kembali ke Login -->
            <a href="{{ route('login.2fa.cancel') }}" class="back-home-btn" title="Batalkan & Kembali ke Halaman Login">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Login
            </a>

            <!-- Logo Rumah Sakit Delta -->
            <div class="logo-section">
                <a href="{{ route('landing') }}">
                    <div class="logo-icon">
                        <img src="{{ asset('images/logodelta1.svg') }}" alt="Logo Delta">
                    </div>
                    <div class="logo-text">
                        <h1>Rumah Sakit Delta</h1>
                        <p>Verifikasi 2 Langkah (2FA)</p>
                    </div>
                </a>
            </div>

            <div class="welcome-msg">
                Hai <strong>{{ $nama }}</strong> ({{ ucfirst($role) }}), akun Anda dilindungi verifikasi 2 langkah untuk keamanan data medis rumah sakit.
            </div>

            <!-- Pesan Error Validasi -->
            @error('otp_code')
                <div class="alert-box alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>{{ $message }}</span>
                </div>
            @enderror

            <!-- Panduan Penggunaan Google Authenticator -->
            <div style="background: rgba(255, 255, 255, 0.16); border: 1px solid rgba(255, 255, 255, 0.28); border-radius: 14px; padding: 14px 16px; margin-bottom: 20px; font-size: 12px; color: #ffffff; display: flex; align-items: center; gap: 12px; backdrop-filter: blur(4px);">
                <i class="fa-solid fa-mobile-screen-button" style="font-size: 26px; color: #4ade80; flex-shrink: 0;"></i>
                <div>
                    <strong style="display: block; font-size: 13px; margin-bottom: 2px;">Buka Aplikasi Google Authenticator</strong>
                    Ketik 6-digit kode verifikasi yang tampil untuk akun <strong>{{ $nama }} (SIMRS Delta)</strong> di HP Anda.
                </div>
            </div>

            <!-- Form Input OTP -->
            <form class="auth-form" action="{{ route('login.2fa.verify') }}" method="POST">
                @csrf
                <div class="input-group">
                    <label>Masukkan 6-Digit Kode Verifikasi</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-key"></i>
                        <input type="text" name="otp_code" class="otp-input" placeholder="000000"
                               maxlength="6" pattern="[0-9]{6}" required autofocus autocomplete="one-time-code">
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-shield-check"></i> Verifikasi & Masuk
                </button>
            </form>

            <!-- Bantuan Kendala 2FA / Ganti Perangkat -->
            <div style="margin-top: 14px; text-align: center;">
                <button type="button" onclick="bukaModalPermintaanReset()" 
                        style="background: none; border: none; color: rgba(255, 255, 255, 0.9); font-size: 11.5px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 999px; transition: all 0.2s;"
                        onmouseover="this.style.background='rgba(255,255,255,0.15)';"
                        onmouseout="this.style.background='none';">
                    <i class="fa-solid fa-circle-question" style="color: #fef08a;"></i>
                    <span>Kehilangan Akses 2FA / Ganti HP? <strong style="text-decoration: underline; color: #ffffff;">Minta Reset ke Admin</strong></span>
                </button>
            </div>

            <div class="switch-mode-box">
                Bukan akun Anda? 
                <a href="{{ route('login.2fa.cancel') }}">Batalkan & Ganti Akun</a>
            </div>
        </div>

        <!-- SISI KANAN: ILUSTRASI DOKTER -->
        <div class="panel-right">
            <div class="doctor-container">
                <img src="{{ asset('images/dokt.png') }}?v={{ filemtime(public_path('images/dokt.png')) }}" alt="Dokter RS Delta" class="doctor-img">
                <div class="doctor-shadow"></div>
            </div>

            <div class="doctor-badge-feature">
                <i class="fa-solid fa-lock" style="color: #4D79C1;"></i>
                <span>Proteksi Akses Data Rekam Medis & Keuangan</span>
            </div>
        </div>

    </div>

    <!-- MODAL PERMINTAAN RESET PASSWORD & 2FA -->
    @include('partials.modal-permintaan-reset')

</body>
</html>
