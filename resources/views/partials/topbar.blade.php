<header class="topbar">
    <div class="brand">
        <div class="logo-icon">
            <img src="{{ asset('images/logodelta1.svg') }}" alt="Logo Delta" class="logo-img">
        </div>
        <div class="brand-text">
            <h1>Rumah Sakit Delta</h1>
            <p>Dedikasi Untuk Kesembuhan</p>
        </div>
    </div>

    <!-- Info Operasional, Notifikasi Kasir & Jam Realtime RS Delta -->
    <div class="topbar-status-area">
        @php
            $roleSekarang = strtolower(session('role_aktif', ''));
            $isKasir = in_array($roleSekarang, ['kasir', 'admin kasir', 'admin_kasir', 'kasir_utama']);
            $isPerawat = $roleSekarang === 'perawat';
            $isDokter = $roleSekarang === 'dokter';
            $isApoteker = $roleSekarang === 'apoteker';
            $isLoket = in_array($roleSekarang, ['loket', 'petugas_loket']);
            $isResepsionis = in_array($roleSekarang, ['resepsionis']);
            $isAdmin = in_array($roleSekarang, ['admin', 'administrator']);
        @endphp

        @if ($isKasir)
            <div class="notif-kasir-container" id="notif-kasir-wrapper">
                <button type="button" class="topbar-notif-btn" id="btn-topbar-notif" title="Antrean Pasien Menunggu Pembayaran" aria-label="Notifikasi Kasir">
                    <i class="fa-solid fa-bell"></i>
                    <span class="topbar-notif-badge" id="topbar-notif-count" style="display: none;">0</span>
                    <span class="topbar-notif-pulse" id="topbar-notif-pulse" style="display: none;"></span>
                </button>

                <!-- Dropdown Popover Antrean Kasir -->
                <div class="notif-kasir-dropdown" id="dropdown-notif-kasir" style="display: none;">
                    <div class="notif-dropdown-header">
                        <div class="notif-header-title">
                            <i class="fa-solid fa-bell-concierge" style="color: #2563eb;"></i>
                            <span>Antrean Siap Bayar</span>
                        </div>
                        <span class="notif-header-badge" id="dropdown-header-count">0 Pasien</span>
                    </div>

                    <div class="notif-dropdown-body" id="dropdown-notif-list">
                        <div class="notif-loading-state">
                            <i class="fa-solid fa-spinner fa-spin"></i> Memeriksa antrean kasir...
                        </div>
                    </div>

                    <div class="notif-dropdown-footer">
                        <a href="{{ route('antrian') }}" class="btn-all-antrian">
                            Buka Halaman Antrian Kasir <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        @elseif ($isPerawat)
            <div class="notif-kasir-container" id="notif-perawat-wrapper">
                <button type="button" class="topbar-notif-btn" id="btn-topbar-notif-perawat" title="Antrean Pasien Menunggu Skrining TTV" aria-label="Notifikasi Perawat">
                    <i class="fa-solid fa-bell"></i>
                    <span class="topbar-notif-badge" id="topbar-notif-count-perawat" style="display: none;">0</span>
                    <span class="topbar-notif-pulse" id="topbar-notif-pulse-perawat" style="display: none;"></span>
                </button>

                <!-- Dropdown Popover Antrean Skrining Perawat -->
                <div class="notif-kasir-dropdown" id="dropdown-notif-perawat" style="display: none;">
                    <div class="notif-dropdown-header">
                        <div class="notif-header-title">
                            <i class="fa-solid fa-heart-pulse" style="color: #2563eb;"></i>
                            <span>Menunggu Skrining TTV</span>
                        </div>
                        <span class="notif-header-badge" id="dropdown-header-count-perawat">0 Pasien</span>
                    </div>

                    <div class="notif-dropdown-body" id="dropdown-notif-list-perawat">
                        <div class="notif-loading-state">
                            <i class="fa-solid fa-spinner fa-spin"></i> Memeriksa antrean perawat...
                        </div>
                    </div>

                    <div class="notif-dropdown-footer">
                        <a href="{{ route('perawat.skrining') }}" class="btn-all-antrian">
                            Buka Antrean Skrining <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        @elseif ($isDokter)
            <div class="notif-kasir-container" id="notif-dokter-wrapper">
                <button type="button" class="topbar-notif-btn" id="btn-topbar-notif-dokter" title="Antrean Pasien Siap Diperiksa Dokter" aria-label="Notifikasi Dokter">
                    <i class="fa-solid fa-bell"></i>
                    <span class="topbar-notif-badge" id="topbar-notif-count-dokter" style="display: none;">0</span>
                    <span class="topbar-notif-pulse" id="topbar-notif-pulse-dokter" style="display: none;"></span>
                </button>

                <!-- Dropdown Popover Antrean Pasien Siap Diperiksa Dokter -->
                <div class="notif-kasir-dropdown" id="dropdown-notif-dokter" style="display: none;">
                    <div class="notif-dropdown-header">
                        <div class="notif-header-title">
                            <i class="fa-solid fa-user-doctor" style="color: #2563eb;"></i>
                            <span>Pasien Siap Diperiksa</span>
                        </div>
                        <span class="notif-header-badge" id="dropdown-header-count-dokter">0 Pasien</span>
                    </div>

                    <div class="notif-dropdown-body" id="dropdown-notif-list-dokter">
                        <div class="notif-loading-state">
                            <i class="fa-solid fa-spinner fa-spin"></i> Memeriksa antrean dokter...
                        </div>
                    </div>

                    <div class="notif-dropdown-footer">
                        <a href="{{ route('dokter.pelayanan') }}" class="btn-all-antrian">
                            Buka Antrean Poliklinik <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        @elseif ($isApoteker)
            <div class="notif-kasir-container" id="notif-apoteker-wrapper">
                <button type="button" class="topbar-notif-btn" id="btn-topbar-notif-apoteker" title="E-Resep Siap Diracing & Diserahkan" aria-label="Notifikasi Apoteker">
                    <i class="fa-solid fa-bell"></i>
                    <span class="topbar-notif-badge" id="topbar-notif-count-apoteker" style="display: none;">0</span>
                    <span class="topbar-notif-pulse" id="topbar-notif-pulse-apoteker" style="display: none;"></span>
                </button>

                <!-- Dropdown Popover Antrean E-Resep Farmasi -->
                <div class="notif-kasir-dropdown" id="dropdown-notif-apoteker" style="display: none;">
                    <div class="notif-dropdown-header">
                        <div class="notif-header-title">
                            <i class="fa-solid fa-prescription-bottle-medical" style="color: #2563eb;"></i>
                            <span>E-Resep Siap Racik</span>
                        </div>
                        <span class="notif-header-badge" id="dropdown-header-count-apoteker">0 Resep</span>
                    </div>

                    <div class="notif-dropdown-body" id="dropdown-notif-list-apoteker">
                        <div class="notif-loading-state">
                            <i class="fa-solid fa-spinner fa-spin"></i> Memeriksa antrean resep...
                        </div>
                    </div>

                    <div class="notif-dropdown-footer">
                        <a href="{{ route('apoteker.resep') }}" class="btn-all-antrian">
                            Buka Antrean E-Resep <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        @elseif ($isLoket)
            <div class="notif-kasir-container" id="notif-loket-wrapper">
                <button type="button" class="topbar-notif-btn" id="btn-topbar-notif-loket" title="Pendaftaran Pasien Hari Ini" aria-label="Notifikasi Loket">
                    <i class="fa-solid fa-bell"></i>
                    <span class="topbar-notif-badge" id="topbar-notif-count-loket" style="display: none;">0</span>
                    <span class="topbar-notif-pulse" id="topbar-notif-pulse-loket" style="display: none;"></span>
                </button>

                <!-- Dropdown Popover Pendaftaran Loket -->
                <div class="notif-kasir-dropdown" id="dropdown-notif-loket" style="display: none;">
                    <div class="notif-dropdown-header">
                        <div class="notif-header-title">
                            <i class="fa-solid fa-id-card" style="color: #4338ca;"></i>
                            <span>Pendaftaran Hari Ini</span>
                        </div>
                        <span class="notif-header-badge" id="dropdown-header-count-loket">0 Pasien</span>
                    </div>

                    <div class="notif-dropdown-body" id="dropdown-notif-list-loket">
                        <div class="notif-loading-state">
                            <i class="fa-solid fa-spinner fa-spin"></i> Memeriksa data pendaftaran...
                        </div>
                    </div>

                    <div class="notif-dropdown-footer">
                        <a href="{{ route('loket.index') }}" class="btn-all-antrian">
                            Buka Loket Pendaftaran <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        @elseif ($isResepsionis)
            <div class="notif-kasir-container" id="notif-resepsionis-wrapper">
                <button type="button" class="topbar-notif-btn" id="btn-topbar-notif-resepsionis" title="Peringatan Lonjakan & Monitoring Antrean" aria-label="Notifikasi Resepsionis">
                    <i class="fa-solid fa-bell"></i>
                    <span class="topbar-notif-badge" id="topbar-notif-count-resepsionis" style="display: none;">0</span>
                    <span class="topbar-notif-pulse" id="topbar-notif-pulse-resepsionis" style="display: none;"></span>
                </button>

                <!-- Dropdown Popover Peringatan Antrean Resepsionis -->
                <div class="notif-kasir-dropdown" id="dropdown-notif-resepsionis" style="display: none;">
                    <div class="notif-dropdown-header">
                        <div class="notif-header-title">
                            <i class="fa-solid fa-chart-line" style="color: #2563eb;"></i>
                            <span>Monitoring Alur Antrean</span>
                        </div>
                        <span class="notif-header-badge" id="dropdown-header-count-resepsionis">Memeriksa...</span>
                    </div>

                    <div class="notif-dropdown-body" id="dropdown-notif-list-resepsionis">
                        <div class="notif-loading-state">
                            <i class="fa-solid fa-spinner fa-spin"></i> Memeriksa status pos antrean...
                        </div>
                    </div>

                    <div class="notif-dropdown-footer">
                        <a href="{{ route('resepsionis.index') }}" class="btn-all-antrian">
                            Buka Monitoring Pasien <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        @elseif ($isAdmin)
            <div class="notif-kasir-container" id="notif-admin-wrapper">
                <button type="button" class="topbar-notif-btn" id="btn-topbar-notif-admin" title="Permintaan Reset Password & 2FA Pengguna" aria-label="Notifikasi Administrator">
                    <i class="fa-solid fa-bell"></i>
                    <span class="topbar-notif-badge" id="topbar-notif-count-admin" style="display: none;">0</span>
                    <span class="topbar-notif-pulse" id="topbar-notif-pulse-admin" style="display: none;"></span>
                </button>

                <!-- Dropdown Popover Permintaan Reset Administrator -->
                <div class="notif-kasir-dropdown" id="dropdown-notif-admin" style="display: none;">
                    <div class="notif-dropdown-header">
                        <div class="notif-header-title">
                            <i class="fa-solid fa-key" style="color: #1C4D8D;"></i>
                            <span>Permintaan Reset Akun</span>
                        </div>
                        <span class="notif-header-badge" id="dropdown-header-count-admin">0 Permintaan</span>
                    </div>

                    <div class="notif-dropdown-body" id="dropdown-notif-list-admin">
                        <div class="notif-loading-state">
                            <i class="fa-solid fa-spinner fa-spin"></i> Memeriksa permohonan reset...
                        </div>
                    </div>

                    <div class="notif-dropdown-footer">
                        <a href="{{ route('admin.reset') }}" class="btn-all-antrian">
                            Buka Menu Permintaan Reset <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <div class="topbar-clock" id="simrs-live-clock">
            <i class="fa-regular fa-clock" style="margin-right: 6px; opacity: 0.9;"></i>
            <span id="clock-date-time">{{ date('d M Y') }}</span>
        </div>
    </div>
</header>
