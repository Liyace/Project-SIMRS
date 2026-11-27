<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Rumah Sakit Delta</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logodelta1.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}?v={{ filemtime(public_path('css/login.css')) }}">
</head>
<body>
    <div class="auth-wrapper">
        
        <!-- SISI KIRI: FORM LOGIN -->
        <div class="panel-left">
            
            <!-- Tombol Kembali ke Landing Page -->
            <a href="{{ route('landing') }}" class="back-home-btn" title="Kembali ke Halaman Beranda">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
            </a>

            <!-- Logo Rumah Sakit Delta -->
            <div class="logo-section">
                <a href="{{ route('landing') }}">
                    <div class="logo-icon">
                        <img src="{{ asset('images/logodelta1.svg') }}" alt="Logo Delta">
                    </div>
                    <div class="logo-text">
                        <h1>Rumah Sakit Delta</h1>
                        <p>Dedikasi Untuk Kesembuhan</p>
                    </div>
                </a>
            </div>

            <div class="welcome-msg">
                Selamat Datang di Portal Pelayanan Terpadu Rumah Sakit Delta
            </div>

            <!-- Pesan Sukses (Setelah Registrasi Pasien) -->
            @if (session('success'))
                <div class="alert-box alert-success">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('status'))
                <div class="alert-box alert-success">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- Pesan Error Validasi -->
            @error('nik')
                <div class="alert-box alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>{{ $message }}</span>
                </div>
            @enderror

            <!-- Form Login (Pegawai & Pasien) -->
            <form class="auth-form" action="{{ route('login') }}" method="POST" autocomplete="off">
                @csrf
                <div class="input-group">
                    <label>NIK atau Username</label>
                    <div class="input-wrapper">
                        <i class="fa-regular fa-id-card"></i>
                        <input type="text" name="nik" placeholder="Masukkan NIK atau Username"
                               value="{{ old('nik') }}" required autofocus autocomplete="off">
                    </div>
                </div>

                <div class="input-group">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password" placeholder="Masukkan Password Anda" 
                               required autocomplete="new-password" value=""
                               readonly onfocus="this.removeAttribute('readonly');">
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-right-to-bracket"></i> Masuk ke Sistem
                </button>
            </form>

            <!-- Bantuan Lupa Password / Kendala 2FA -->
            <div style="margin-top: 14px; text-align: center;">
                <button type="button" onclick="bukaModalPermintaanReset()" 
                        style="background: none; border: none; color: rgba(255, 255, 255, 0.9); font-size: 11.5px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 999px; transition: all 0.2s;"
                        onmouseover="this.style.background='rgba(255,255,255,0.15)';"
                        onmouseout="this.style.background='none';">
                    <i class="fa-solid fa-headset" style="color: #fef08a;"></i>
                    <span>Lupa Password / Terkendala 2FA? <strong style="text-decoration: underline; color: #ffffff;">Hubungi Admin RS</strong></span>
                </button>
            </div>

            <!-- Switcher ke Registrasi Pasien -->
            <div class="switch-mode-box">
                Belum memiliki akun pasien? 
                <a href="{{ route('register') }}">Daftar Akun Pasien Baru</a>
            </div>
        </div>

        <!-- SISI KANAN: ILUSTRASI DOKTER DENGAN ANIMASI TURUN-NAIK -->
        <div class="panel-right">
            <div class="doctor-container">
                <img src="{{ asset('images/dokt.png') }}?v={{ filemtime(public_path('images/dokt.png')) }}" alt="Dokter RS Delta" class="doctor-img">
                <div class="doctor-shadow"></div>
            </div>

            <div class="doctor-badge-feature">
                <i class="fa-solid fa-shield-heart" style="color: #10b981;"></i>
                <span>Pelayanan Medis Siaga & Terpercaya 24 Jam</span>
            </div>
        </div>

    </div>

    <!-- MODAL PERMINTAAN RESET PASSWORD & 2FA -->
    @include('partials.modal-permintaan-reset')

</body>
</html>
