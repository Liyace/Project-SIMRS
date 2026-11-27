@extends('layouts.app')
@php $halamanAktif = 'profil'; @endphp
@section('title', 'Pengaturan Profil & Keamanan 2FA - Rumah Sakit Delta')

@push('styles')
<style>
    .profil-container {
        max-width: 960px;
        margin: 0 auto;
        padding-bottom: 50px;
    }
    .profil-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 28px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
    }
    .profil-header-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #eef4ff;
        color: #3b82f6;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }
    .profil-card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
    }
    .profil-card-header h3 {
        font-size: 16px;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
    }
    .profil-card-header p {
        font-size: 11px;
        color: #94a3b8;
        margin: 2px 0 0;
    }
    .gender-radio-card {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        background: #f8fafc;
        color: #475569;
        transition: all 0.2s ease;
    }
    .gender-radio-card.active-gender-l {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #93c5fd;
    }
    .gender-radio-card.active-gender-p {
        background: #fdf2f8;
        color: #be185d;
        border-color: #fbcfe8;
    }
    .step-box-2fa {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 18px;
        margin-bottom: 20px;
    }
    .step-box-2fa .step-title-2fa {
        font-size: 12px;
        font-weight: 700;
        color: #1e293b;
        display: block;
        margin-bottom: 8px;
    }
    .step-box-2fa p {
        font-size: 11.5px;
        color: #64748b;
        margin-bottom: 14px;
        line-height: 1.6;
    }
    .step-box-2fa p strong {
        display: inline !important;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0;
    }
    .qr-holder {
        display: inline-block;
        background: #ffffff;
        padding: 10px;
        border-radius: 14px;
        border: 1px solid #cbd5e1;
        box-shadow: 0 4px 10px rgba(0,0,0,0.04);
    }
    .secret-code-text {
        font-size: 13px;
        font-weight: 700;
        color: #1a4a8d;
        background: #e0f2fe;
        padding: 4px 10px;
        border-radius: 6px;
        letter-spacing: 2px;
        display: inline-block;
        margin-top: 4px;
        border: 1px solid #bae6fd;
    }
    .otp-input-field {
        width: 100%;
        padding: 12px 14px;
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        font-size: 18px;
        font-weight: 700;
        text-align: center;
        letter-spacing: 6px;
        font-family: monospace;
        outline: none;
        background: #ffffff;
        color: #0f172a;
    }
    .profil-input {
        width: 100%;
        padding: 10px 14px;
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        font-size: 13px;
        outline: none;
        background: #ffffff;
        color: #0f172a;
    }
    .profil-input:disabled {
        background: #f8fafc;
        color: #64748b;
        border-color: #e2e8f0;
    }
    .badge-role-tag {
        background: #e0f2fe;
        color: #0284c7;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        border: 1px solid #bae6fd;
    }

    /* ── DARK MODE OVERRIDES PROFIL ── */
    body.dark-mode .profil-card {
        background-color: #121927 !important;
        border-color: #1e293b !important;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3) !important;
    }
    body.dark-mode .profil-card-header {
        border-bottom-color: #1e293b !important;
    }
    body.dark-mode .profil-card-header h3 {
        color: #f8fafc !important;
    }
    body.dark-mode .profil-header-icon {
        background-color: #0b0f19 !important;
        color: #60a5fa !important;
    }
    body.dark-mode .badge-role-tag {
        background-color: #0b0f19 !important;
        color: #38bdf8 !important;
        border-color: #0369a1 !important;
    }
    body.dark-mode .gender-radio-card {
        background-color: #0b0f19 !important;
        border-color: #1e293b !important;
        color: #94a3b8 !important;
    }
    body.dark-mode .gender-radio-card.active-gender-l {
        background-color: rgba(30, 64, 175, 0.25) !important;
        border-color: #2563eb !important;
        color: #93c5fd !important;
    }
    body.dark-mode .gender-radio-card.active-gender-p {
        background-color: rgba(219, 39, 119, 0.25) !important;
        border-color: #db2777 !important;
        color: #f472b6 !important;
    }
    body.dark-mode .profil-input {
        background-color: #0b0f19 !important;
        border-color: #1e293b !important;
        color: #f8fafc !important;
    }
    body.dark-mode .profil-input:disabled {
        background-color: #0b0f19 !important;
        border-color: #1e293b !important;
        color: #64748b !important;
    }
    body.dark-mode label {
        color: #cbd5e1 !important;
    }
    body.dark-mode .step-box-2fa {
        background-color: #0b0f19 !important;
        border-color: #1e293b !important;
    }
    body.dark-mode .step-box-2fa .step-title-2fa {
        color: #f8fafc !important;
    }
    body.dark-mode .step-box-2fa p {
        color: #94a3b8 !important;
    }
    body.dark-mode .step-box-2fa p strong {
        color: #f8fafc !important;
        display: inline !important;
    }
    body.dark-mode .step-box-2fa small,
    body.dark-mode .step-box-2fa div {
        color: #94a3b8 !important;
    }
    body.dark-mode .qr-holder {
        background: #ffffff !important; /* QR image stays crisp white for phone camera scanner */
        border-color: #334155 !important;
        box-shadow: 0 4px 15px rgba(0,0,0,0.4) !important;
    }
    body.dark-mode .secret-code-text {
        background-color: #0b0f19 !important;
        color: #38bdf8 !important;
        border-color: #0284c7 !important;
    }
    body.dark-mode .otp-input-field {
        background-color: #0b0f19 !important;
        border-color: #1e293b !important;
        color: #f8fafc !important;
    }
    body.dark-mode .card-2fa-active-info {
        background-color: #0b0f19 !important;
        border-color: #1e293b !important;
    }
    body.dark-mode .card-2fa-active-info strong {
        color: #f8fafc !important;
    }
</style>
@endpush

@section('content')
<div class="profil-container">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 12px;">
        <div>
            <h2 style="font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 4px;">Pengaturan Profil & Keamanan</h2>
            <p style="font-size: 13px; color: #64748b;">Kelola informasi profil staf, foto profil, dan proteksi Google Authenticator.</p>
        </div>
        <span class="badge-role-tag">
            <i class="fa-solid fa-id-badge"></i> {{ strtolower($pegawai->role) === 'admin' ? 'Super Admin RS' : ucfirst($pegawai->role) . ' RS Delta' }}
        </span>
    </div>

    @if (session('status'))
        <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 14px 18px; border-radius: 14px; margin-bottom: 20px; font-size: 13px; display: flex; align-items: center; gap: 10px; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.08);">
            <i class="fa-solid fa-circle-check" style="font-size: 18px; color: #10b981;"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @if (session('status_2fa'))
        <div style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 14px 18px; border-radius: 14px; margin-bottom: 20px; font-size: 13px; display: flex; align-items: center; gap: 10px; box-shadow: 0 2px 8px rgba(59, 130, 246, 0.08);">
            <i class="fa-solid fa-shield-halved" style="font-size: 18px; color: #3b82f6;"></i>
            <span>{{ session('status_2fa') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 14px 18px; border-radius: 14px; margin-bottom: 20px; font-size: 13px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; align-items: start;">
        
        <!-- KARTU 1: DATA DIRI & UPLOAD FOTO PROFIL -->
        <div class="profil-card">
            <div class="profil-card-header">
                <div class="profil-header-icon">
                    <i class="fa-solid fa-user-pen"></i>
                </div>
                <div>
                    <h3>Data Diri</h3>
                    <p>Klik avatar untuk mengunggah foto profil Anda</p>
                </div>
            </div>

            <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- INTERAKTIF AVATAR UPLOAD (KLIK FOTO UNTUK UPLOAD) -->
                <div style="display: flex; flex-direction: column; align-items: center; margin-bottom: 24px;">
                    <div style="position: relative; width: 100px; height: 100px; cursor: pointer;" onclick="document.getElementById('inputFotoProfil').click();" title="Klik untuk mengganti foto profil">
                        <img id="previewAvatar" src="{{ $pegawai->foto_url }}" alt="Avatar Staf" 
                             style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #86BAF2; box-shadow: 0 4px 12px rgba(77, 121, 193, 0.2); transition: opacity 0.2s;">
                        
                        <!-- Badge Ikon Kamera / Edit -->
                        <div style="position: absolute; bottom: 2px; right: 2px; width: 32px; height: 32px; background: #1C4D8D; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid #ffffff; box-shadow: 0 2px 6px rgba(0,0,0,0.2);">
                            <i class="fa-solid fa-camera" style="font-size: 13px;"></i>
                        </div>
                    </div>
                    
                    <input type="file" name="foto_profil" id="inputFotoProfil" style="display: none;" accept="image/jpeg,image/png,image/webp" onchange="previewImage(this)">
                    
                    <span style="font-size: 11px; color: #64748b; margin-top: 8px; font-weight: 500;">
                        Klik foto untuk memilih gambar (Maks. 2MB)
                    </span>
                </div>

                <!-- PILIHAN GENDER STAF -->
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 6px;">Jenis Kelamin</label>
                    <div style="display: flex; gap: 14px;">
                        <label id="lbl-gender-l" class="gender-radio-card {{ ($pegawai->jenis_kelamin ?? 'L') === 'L' ? 'active-gender-l' : '' }}">
                            <input type="radio" name="jenis_kelamin" value="L" {{ ($pegawai->jenis_kelamin ?? 'L') === 'L' ? 'checked' : '' }} onchange="onGenderChange('L')" style="accent-color: #1a4a8d;">
                            <i class="fa-solid fa-mars" style="color: #3b82f6;"></i> Laki-laki
                        </label>
                        <label id="lbl-gender-p" class="gender-radio-card {{ ($pegawai->jenis_kelamin ?? '') === 'P' ? 'active-gender-p' : '' }}">
                            <input type="radio" name="jenis_kelamin" value="P" {{ ($pegawai->jenis_kelamin ?? '') === 'P' ? 'checked' : '' }} onchange="onGenderChange('P')" style="accent-color: #ec4899;">
                            <i class="fa-solid fa-venus" style="color: #ec4899;"></i> Perempuan
                        </label>
                    </div>
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 6px;">NIK (Tidak dapat diubah)</label>
                    <input type="text" value="{{ $pegawai->nik }}" disabled class="profil-input">
                </div>

                @if (($pegawai instanceof \App\Models\Pegawai) || session('user_type') === 'pegawai')
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 6px;">
                            NIP (Nomor Induk Pegawai)
                            <span style="font-weight: 400; color: #64748b; font-size: 11px;">(Opsional)</span>
                        </label>
                        <input type="text" name="nip" value="{{ old('nip', $pegawai->nip ?? '') }}" placeholder="Contoh: 19920415 201801 1 003" class="profil-input">
                        <span style="font-size: 11px; color: #64748b; display: block; margin-top: 4px;">
                            *NIP akan digunakan pada lembar tanda tangan cetak laporan resmi kasir.
                        </span>
                    </div>
                @endif

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 6px;">Nama Lengkap</label>
                    <input type="text" name="nama" value="{{ $pegawai->nama }}" required class="profil-input">
                </div>

                <div style="margin-bottom: 22px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 6px;">Password Baru (Kosongkan jika tetap)</label>
                    <input type="password" name="password" autocomplete="new-password" placeholder="Minimal 6 karakter" class="profil-input">
                </div>

                <button type="submit" style="width: 100%; background: #1a4a8d; color: white; border: none; padding: 12px 20px; border-radius: 12px; font-weight: 600; font-size: 13px; cursor: pointer; transition: 0.2s;">
                    <i class="fa-solid fa-floppy-disk" style="margin-right: 6px;"></i> Simpan Perubahan Profil
                </button>
            </form>
        </div>

        <!-- KARTU 2: TWO-FACTOR AUTHENTICATION DENGAN GOOGLE AUTHENTICATOR -->
        <div class="profil-card">
            
            <div class="profil-card-header">
                <div class="profil-header-icon" style="background: {{ $pegawai->two_factor_enabled ? '#ecfdf5' : '#fef2f2' }}; color: {{ $pegawai->two_factor_enabled ? '#10b981' : '#ef4444' }};">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <h3>Google Authenticator (2FA)</h3>
                    <p>Proteksi kode OTP dinamis via aplikasi ponsel</p>
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <span style="font-size: 11px; color: #64748b; display: block; margin-bottom: 4px;">Status 2FA Akun Anda Saat Ini:</span>
                @if ($pegawai->two_factor_enabled)
                    <span style="display: inline-flex; align-items: center; gap: 6px; background: #dcfce7; color: #15803d; border: 1px solid #86efac; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700;">
                        <i class="fa-solid fa-circle-check"></i> AKTIF & TERLINDUNGI
                    </span>
                @else
                    <span style="display: inline-flex; align-items: center; gap: 6px; background: #fef2f2; color: #b91c1c; border: 1px solid #fca5a5; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                        <i class="fa-solid fa-circle-xmark"></i> NONAKTIF
                    </span>
                @endif
            </div>

            @if ($pegawai->two_factor_enabled)
                <!-- TAMPILAN SAAT 2FA AKTIF -->
                <div class="card-2fa-active-info" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 18px; margin-bottom: 20px; font-size: 12px; color: #475569; line-height: 1.6;">
                    <div style="display: flex; gap: 12px; align-items: flex-start;">
                        <i class="fa-solid fa-mobile-screen-button" style="font-size: 24px; color: #10b981; margin-top: 2px;"></i>
                        <div>
                            <strong style="color: #0f172a; font-size: 13px; display: block; margin-bottom: 4px;">Aplikasi Google Authenticator Terhubung</strong>
                            Akun staf Anda kini aman. Setiap kali Anda masuk ke sistem SIMRS Delta, sistem akan memvalidasi 6-digit kode OTP yang terus berputar setiap 30 detik di aplikasi HP Anda.
                        </div>
                    </div>
                </div>

                <!-- FORM NONAKTIFKAN 2FA (WAJIB MASUKKAN PASSWORD) -->
                <form action="{{ route('profil.toggle2fa') }}" method="POST" autocomplete="off" style="border: 1px solid #fca5a5; border-radius: 14px; padding: 18px;" class="step-box-2fa">
                    @csrf
                    <div style="margin-bottom: 14px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #991b1b; margin-bottom: 6px;">
                            <i class="fa-solid fa-lock" style="margin-right: 4px;"></i> Konfirmasi Kata Sandi Akun untuk Menonaktifkan:
                        </label>
                        <input type="password" name="password" required value="" autocomplete="new-password"
                               readonly onfocus="this.removeAttribute('readonly');"
                               placeholder="Ketik kata sandi akun Anda..."
                               class="profil-input" style="border-color: #fca5a5;">
                        <small style="color: #64748b; font-size: 11px; display: block; margin-top: 4px;">
                            <i class="fa-solid fa-shield-halved" style="color: #ef4444;"></i> Diperlukan verifikasi sandi pemilik sah agar 2FA tidak dapat dimatikan oleh pihak lain.
                        </small>
                        @error('password_2fa')
                            <span style="color: #dc2626; font-size: 11px; display: block; margin-top: 5px; font-weight: 600;">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" onclick="return confirm('Apakah Anda benar-benar yakin ingin menonaktifkan proteksi Google Authenticator?');"
                            style="width: 100%; background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; padding: 11px 20px; border-radius: 12px; font-weight: 600; font-size: 13px; cursor: pointer; transition: 0.2s;">
                        <i class="fa-solid fa-lock-open" style="margin-right: 6px;"></i> Konfirmasi & Nonaktifkan Google Authenticator
                    </button>
                </form>
            @else
                <!-- TAMPILAN SETUP GOOGLE AUTHENTICATOR (SCAN QR CODE) -->
                <div class="step-box-2fa">
                    <strong class="step-title-2fa">Langkah 1: Pindai Kode QR dengan Google Authenticator</strong>
                    <p>Buka aplikasi <strong>Google Authenticator</strong> di HP Anda, tekan tombol <strong>+</strong> lalu pilih <strong>Pindai Kode QR</strong>:</p>

                    <!-- Gambar QR Code -->
                    <div style="text-align: center; margin-bottom: 14px;">
                        <div class="qr-holder">
                            <img src="{{ $qrCodeUrl }}" alt="QR Code Google Authenticator" style="width: 160px; height: 160px; display: block;">
                        </div>
                    </div>

                    <!-- Kunci Rahasia Manual -->
                    <div style="font-size: 11px; color: #64748b; text-align: center;">
                        Atau masukkan kunci manual: <br>
                        <code class="secret-code-text">
                            {{ chunk_split($setupSecret, 4, ' ') }}
                        </code>
                    </div>
                </div>

                <!-- FORM KONFIRMASI AKTIVASI -->
                <form action="{{ route('profil.toggle2fa') }}" method="POST">
                    @csrf
                    <input type="hidden" name="secret" value="{{ $setupSecret }}">

                    <div style="margin-bottom: 18px;">
                        <label class="step-title-2fa" style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px;">
                            Langkah 2: Masukkan 6-Digit Kode dari Aplikasi
                        </label>
                        <input type="text" name="otp_code" maxlength="6" pattern="[0-9]{6}" required placeholder="Contoh: 849201"
                               class="otp-input-field">
                        <small style="color: #64748b; font-size: 11px; display: block; margin-top: 5px;">
                            <i class="fa-solid fa-clock-rotate-left" style="color: #0284c7;"></i> Masukkan kode 6 digit sebelum timer di aplikasi berganti.
                        </small>
                        @error('otp_code')
                            <span style="color: #dc2626; font-size: 11px; display: block; margin-top: 4px; font-weight: 600;">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit"
                            style="width: 100%; background: #059669; color: white; border: none; padding: 12px 20px; border-radius: 12px; font-weight: 600; font-size: 13px; cursor: pointer; transition: 0.2s; box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25);">
                        <i class="fa-solid fa-shield-check" style="margin-right: 6px;"></i> Verifikasi & Aktifkan 2FA Sekarang
                    </button>
                </form>
            @endif

        </div>

    </div>

</div>

<script>
    // Preview gambar saat staf memilih file foto profil
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewAvatar').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Ubah avatar default gender jika belum ada upload foto khusus
    const avatarMale = "{{ asset('images/avatar_male.jpg') }}";
    const avatarFemale = "{{ asset('images/avatar_female.jpg') }}";
    const currentFotoUrl = "{{ $pegawai->foto_url }}";

    function onGenderChange(gender) {
        const fileInput = document.getElementById('inputFotoProfil');
        const lblL = document.getElementById('lbl-gender-l');
        const lblP = document.getElementById('lbl-gender-p');

        if (gender === 'L') {
            lblL.classList.add('active-gender-l');
            lblP.classList.remove('active-gender-p');
        } else {
            lblP.classList.add('active-gender-p');
            lblL.classList.remove('active-gender-l');
        }

        // Hanya ganti preview default jika belum memilih file kustom baru
        if (!fileInput.files || fileInput.files.length === 0) {
            if (!currentFotoUrl.includes('/storage/profil/')) {
                document.getElementById('previewAvatar').src = (gender === 'P') ? avatarFemale : avatarMale;
            }
        }
    }
</script>
@endsection
