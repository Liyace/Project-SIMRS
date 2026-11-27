<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Pasien Baru - Rumah Sakit Delta</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logodelta1.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}?v={{ filemtime(public_path('css/login.css')) }}">
</head>
<body>
    <div class="auth-wrapper">
        
        <!-- SISI KIRI: FORM REGISTRASI PASIEN -->
        <div class="panel-left">
            
            <!-- Tombol Kembali ke Halaman Login -->
            <a href="{{ route('login') }}" class="back-home-btn" title="Kembali ke Halaman Login">
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
                        <p>Dedikasi Untuk Kesembuhan</p>
                    </div>
                </a>
            </div>

            <div class="welcome-msg" style="line-height: 1.5;">
                <div style="font-size: 16px; font-weight: 700; color: #1e293b;">Pendaftaran & Aktivasi Akun Pasien</div>
                <div style="font-size: 12px; color: #64748b; font-weight: 400; margin-top: 4px;">
                    Pasien baru atau pasien yang pernah berobat via loket RS Delta dapat memasukkan NIK di bawah untuk membuat username & password portal mandiri.
                </div>
            </div>

            <!-- Pesan Error Validasi -->
            @if ($errors->any())
                <div class="alert-box alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <div>
                        @foreach ($errors->all() as $err)
                            <div>• {{ $err }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Form Registrasi Pasien -->
            <form class="auth-form" action="{{ route('register') }}" method="POST">
                @csrf
                
                <div class="input-grid-2">
                    <!-- Username -->
                    <div class="input-group">
                        <label>Username *</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-at"></i>
                            <input type="text" name="username" placeholder="Contoh: ahmadfauzi"
                                   value="{{ old('username') }}" required autocomplete="username">
                        </div>
                    </div>

                    <!-- NIK (16 Digit) -->
                    <div class="input-group">
                        <label>NIK KTP (16 Digit) *</label>
                        <div class="input-wrapper">
                            <i class="fa-regular fa-id-card"></i>
                            <input type="text" name="nik" placeholder="16 digit nomor NIK" maxlength="16"
                                   value="{{ old('nik') }}" required>
                        </div>
                    </div>
                </div>

                <!-- Nama Lengkap -->
                <div class="input-group">
                    <label>Nama Lengkap Pasien *</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" name="nama" placeholder="Masukkan nama lengkap sesuai KTP"
                               value="{{ old('nama') }}" required>
                    </div>
                </div>

                <!-- Jenis Kelamin -->
                <div class="input-group">
                    <label>Jenis Kelamin</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-venus-mars"></i>
                        <select name="jenis_kelamin">
                            <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                </div>

                <!-- Password 1 & Password 2 (Konfirmasi) -->
                <div class="input-grid-2">
                    <div class="input-group">
                        <label>Password *</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" name="password" autocomplete="new-password" placeholder="Minimal 6 karakter" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label>Ulangi Password (Password 2) *</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-shield-halved"></i>
                            <input type="password" name="password_confirmation" autocomplete="new-password" placeholder="Ketik ulang password" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-user-plus"></i> Buat Akun Pasien Baru
                </button>
            </form>

            <!-- Switcher ke Halaman Login -->
            <div class="switch-mode-box">
                Sudah memiliki akun terdaftar? 
                <a href="{{ route('login') }}">Masuk di Sini</a>
            </div>
        </div>

        <!-- SISI KANAN: ILUSTRASI DOKTER DENGAN ANIMASI TURUN-NAIK -->
        <div class="panel-right">
            <div class="doctor-container">
                <img src="{{ asset('images/dokt.png') }}?v={{ filemtime(public_path('images/dokt.png')) }}" alt="Dokter RS Delta" class="doctor-img">
                <div class="doctor-shadow"></div>
            </div>

            <div class="doctor-badge-feature">
                <i class="fa-solid fa-file-medical" style="color: #4D79C1;"></i>
                <span>Nomor Rekam Medis (RM) Dibuat Otomatis</span>
            </div>
        </div>

    </div>
</body>
</html>
