@php
    $roleAktif = strtolower(session('role_aktif', ''));
    $user = auth()->user();
    $namaUser = $user->nama ?? (session('nama_pasien') ?? 'Pengguna');
    $rawRole = session('role_aktif', 'Staff');
    $isKasirUtama = session('is_kasir_utama') ?? ($user && method_exists($user, 'isKasirUtama') ? $user->isKasirUtama() : false);

    if (in_array(strtolower($rawRole), ['admin', 'administrator'])) {
        $displayRole = 'Super Admin RS';
    } elseif ($isKasirUtama || in_array(strtolower($rawRole), ['admin_kasir', 'kasir_utama', 'admin kasir'])) {
        $displayRole = 'Kasir Admin';
    } else {
        $displayRole = ucwords($rawRole);
    }

    $defaultAvatar = (($user->jenis_kelamin ?? 'L') === 'P') ? asset('images/avatar_female.jpg') : asset('images/avatar_male.jpg');
    $avatarUrl = $user->foto_url ?? $defaultAvatar;
@endphp

<aside class="sidebar" id="appSidebar">
    <!-- ATAS: IDENTITAS PENGGUNA (STATIS) & TOGGLE COLLAPSE -->
    <div class="sidebar-header">
        <div class="sidebar-user" title="Akun Masuk: {{ $namaUser }} ({{ $displayRole }})">
            <div class="sidebar-avatar-wrapper">
                <img src="{{ $avatarUrl }}" alt="{{ $namaUser }}" class="sidebar-avatar" onerror="this.onerror=null; this.src='{{ $defaultAvatar }}';">
            </div>
            <div class="sidebar-user-info">
                <span class="sidebar-user-name">{{ $namaUser }}</span>
                <span class="sidebar-user-role">{{ $displayRole }}</span>
            </div>
        </div>
        <button type="button" class="sidebar-toggle-btn" id="btn-toggle-sidebar" title="Tutup / Buka Sidebar">
            <i class="fa-solid fa-chevron-left" id="sidebar-toggle-icon"></i>
        </button>
    </div>

    <!-- TENGAH: DAFTAR MENU UTAMA (FOKUS OPERASIONAL) -->
    <div class="menu-group">
        <p class="menu-title">Menu Utama</p>
        <ul class="sidebar-nav">
            {{-- Menu Khusus Super Admin RS --}}
            @if (in_array($roleAktif, ['admin', 'administrator']))
                <li class="{{ ($halamanAktif ?? '') == 'admin_statistik' ? 'active' : '' }}">
                    <a href="{{ route('admin.statistik') }}" title="Dashboard Statistik & Indikator RS">
                        <i class="fa-solid fa-chart-line"></i>
                        <span class="nav-label">Dashboard Eksekutif</span>
                        <span class="active-indicator"></span>
                    </a>
                </li>

                <li class="{{ ($halamanAktif ?? '') == 'admin_pengguna' ? 'active' : '' }}">
                    <a href="{{ route('admin.pengguna') }}" title="Kelola Pengguna & Keamanan">
                        <i class="fa-solid fa-users-gear"></i>
                        <span class="nav-label">Kelola Pengguna</span>
                        <span class="active-indicator"></span>
                    </a>
                </li>

                <li class="{{ ($halamanAktif ?? '') == 'admin_reset' ? 'active' : '' }}">
                    <a href="{{ route('admin.reset') }}" title="Permintaan Reset Password & 2FA">
                        <i class="fa-solid fa-key"></i>
                        <span class="nav-label">Permintaan Reset</span>
                        @php
                            $badgeResetPending = \App\Models\PermintaanReset::where('status', 'menunggu')->count();
                        @endphp
                        <span class="sidebar-notif-pill" id="badge-admin-reset" style="{{ $badgeResetPending > 0 ? '' : 'display: none;' }}">{{ $badgeResetPending }}</span>
                        <span class="active-indicator"></span>
                    </a>
                </li>

                <li class="{{ ($halamanAktif ?? '') == 'admin_layanan' ? 'active' : '' }}">
                    <a href="{{ route('admin.layanan') }}" title="Master Tarif & Layanan Medis">
                        <i class="fa-solid fa-notes-medical"></i>
                        <span class="nav-label">Kelola Layanan</span>
                        <span class="active-indicator"></span>
                    </a>
                </li>

                <li class="{{ ($halamanAktif ?? '') == 'admin_poli' ? 'active' : '' }}">
                    <a href="{{ route('admin.poli') }}" title="Master Ruangan & Poliklinik">
                        <i class="fa-solid fa-hospital"></i>
                        <span class="nav-label">Kelola Poliklinik</span>
                        <span class="active-indicator"></span>
                    </a>
                </li>

                <li class="{{ ($halamanAktif ?? '') == 'admin_backup' ? 'active' : '' }}">
                    <a href="{{ route('admin.backup') }}" title="Cadangan & Backup Database">
                        <i class="fa-solid fa-database"></i>
                        <span class="nav-label">Backup Database</span>
                        <span class="active-indicator"></span>
                    </a>
                </li>
            {{-- Menu Staf Kasir (Kasir Biasa & Kasir Admin) --}}
            @elseif (in_array($roleAktif, ['kasir', 'admin kasir', 'admin_kasir', 'kasir_utama']))
                <li class="{{ ($halamanAktif ?? '') == 'dashboard' ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" title="Dashboard Kasir">
                        <i class="fa-solid fa-house"></i>
                        <span class="nav-label">Dashboard</span>
                        <span class="active-indicator"></span>
                    </a>
                </li>

                <li class="{{ ($halamanAktif ?? '') == 'antrian' ? 'active' : '' }}">
                    <a href="{{ route('antrian') }}" title="Antrian Kasir">
                        <i class="fa-solid fa-list-ol"></i>
                        <span class="nav-label">Antrian Kasir</span>
                        <span class="sidebar-notif-pill" id="sidebar-notif-count" style="display: none;">0</span>
                        <span class="active-indicator"></span>
                    </a>
                </li>

                <li class="{{ ($halamanAktif ?? '') == 'riwayat' ? 'active' : '' }}">
                    <a href="{{ route('riwayat') }}" title="Riwayat Transaksi">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        <span class="nav-label">Riwayat Transaksi</span>
                        <span class="active-indicator"></span>
                    </a>
                </li>

                {{-- Khusus Kasir Utama (Laporan Keuangan Kasir) --}}
                @if ($isKasirUtama)
                    <li class="{{ ($halamanAktif ?? '') == 'laporan' ? 'active' : '' }}">
                        <a href="{{ route('laporan') }}" title="Laporan Kasir">
                            <i class="fa-solid fa-chart-simple"></i>
                            <span class="nav-label">Laporan Kasir</span>
                            <span class="active-indicator"></span>
                        </a>
                    </li>
                @endif

            {{-- Menu Staf Perawat (Screening TTV Poliklinik) --}}
            @elseif ($roleAktif === 'perawat')
                <li class="{{ ($halamanAktif ?? '') == 'perawat_skrining' ? 'active' : '' }}">
                    <a href="{{ route('perawat.skrining') }}" title="Skrining TTV Pasien">
                        <i class="fa-solid fa-heart-pulse"></i>
                        <span class="nav-label">Skrining TTV</span>
                        <span class="sidebar-notif-pill" id="sidebar-notif-count-perawat" style="display: none;">0</span>
                        <span class="active-indicator"></span>
                    </a>
                </li>

            {{-- Menu Dokter (Pelayanan Poli, Rekam Medis & E-Resep) --}}
            @elseif ($roleAktif === 'dokter')
                <li class="{{ ($halamanAktif ?? '') == 'dokter_pelayanan' ? 'active' : '' }}">
                    <a href="{{ route('dokter.pelayanan') }}" title="Pelayanan Pasien Poli">
                        <i class="fa-solid fa-user-doctor"></i>
                        <span class="nav-label">Pelayanan Poli</span>
                        <span class="sidebar-notif-pill" id="sidebar-notif-count-dokter" style="display: none;">0</span>
                        <span class="active-indicator"></span>
                    </a>
                </li>

            {{-- Menu Petugas Loket Pendaftaran (Admisi Pasien Walk-In) --}}
            @elseif ($roleAktif === 'loket')
                <li class="{{ ($halamanAktif ?? '') == 'loket_pendaftaran' ? 'active' : '' }}">
                    <a href="{{ route('loket.index') }}" title="Loket Pendaftaran Pasien">
                        <i class="fa-solid fa-id-card-clip"></i>
                        <span class="nav-label">Loket Pendaftaran</span>
                        <span class="active-indicator"></span>
                    </a>
                </li>

            {{-- Menu Apoteker & Staf Farmasi --}}
            @elseif ($roleAktif === 'apoteker')
                <li class="{{ ($halamanAktif ?? '') == 'apoteker_resep' ? 'active' : '' }}">
                    <a href="{{ route('apoteker.resep') }}" title="Antrean E-Resep Farmasi">
                        <i class="fa-solid fa-prescription-bottle-medical"></i>
                        <span class="nav-label">Antrean E-Resep</span>
                        <span class="sidebar-notif-pill" id="sidebar-notif-count-apoteker" style="display: none;">0</span>
                        <span class="active-indicator"></span>
                    </a>
                </li>

                <li class="{{ ($halamanAktif ?? '') == 'apoteker_obat' ? 'active' : '' }}">
                    <a href="{{ route('apoteker.obat') }}" title="Kelola Inventori & Stok Obat">
                        <i class="fa-solid fa-boxes-stacked"></i>
                        <span class="nav-label">Kelola Obat & Stok</span>
                        <span class="active-indicator"></span>
                    </a>
                </li>

                <li class="{{ ($halamanAktif ?? '') == 'apoteker_riwayat' ? 'active' : '' }}">
                    <a href="{{ route('apoteker.riwayat') }}" title="Riwayat Penyerahan Obat">
                        <i class="fa-solid fa-clipboard-check"></i>
                        <span class="nav-label">Riwayat Penyerahan</span>
                        <span class="active-indicator"></span>
                    </a>
                </li>

            {{-- Menu Resepsionis & Informasi Antrean RS --}}
            @elseif ($roleAktif === 'resepsionis')
                <li class="{{ ($halamanAktif ?? '') == 'resepsionis_monitoring' ? 'active' : '' }}">
                    <a href="{{ route('resepsionis.index') }}" title="Monitoring Antrean Pasien">
                        <i class="fa-solid fa-route"></i>
                        <span class="nav-label">Monitoring Pasien</span>
                        <span class="active-indicator"></span>
                    </a>
                </li>

                <li class="{{ ($halamanAktif ?? '') == 'resepsionis_display' ? 'active' : '' }}">
                    <a href="{{ route('resepsionis.layar-tv') }}" title="Monitoring Layar Display TV Ruang Tunggu">
                        <i class="fa-solid fa-tv"></i>
                        <span class="nav-label">Layar Display TV</span>
                        <span class="active-indicator"></span>
                    </a>
                </li>
            @endif
        </ul>
    </div>

    <!-- BAWAH: PENGATURAN PROFIL, TEMA GELAP/TERANG & KELUAR -->
    <div class="sidebar-bottom">
        {{-- Akses Profil & 2FA (Setting) --}}
        <a href="{{ route('profil') }}" class="sidebar-action-btn setting-btn {{ ($halamanAktif ?? '') == 'profil' ? 'active-action' : '' }}" title="Pengaturan Profil & Keamanan 2FA">
            <div class="action-icon">
                <i class="fa-solid fa-gear"></i>
            </div>
            <span class="action-label">Pengaturan Profil</span>
        </a>

        {{-- Toggle Mode Gelap / Terang --}}
        <button type="button" class="sidebar-action-btn theme-btn" id="btn-toggle-theme" title="Ganti Mode Gelap / Terang">
            <div class="action-icon">
                <i class="fa-solid fa-moon" id="theme-toggle-icon"></i>
            </div>
            <span class="action-label" id="theme-toggle-text">Mode Gelap</span>
            <div class="theme-switch-indicator"></div>
        </button>

        {{-- Tombol Keluar Langsung ke Login --}}
        <a href="{{ route('logout') }}" class="sidebar-action-btn logout-action-btn" title="Keluar ke Halaman Login">
            <div class="action-icon">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </div>
            <span class="action-label">Keluar</span>
        </a>
    </div>
</aside>
