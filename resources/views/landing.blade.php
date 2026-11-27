<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rumah Sakit Delta - Dedikasi Untuk Kesembuhan</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logodelta1.svg') }}">
    
    <!-- Google Fonts Poppins (Konsisten dengan Halaman Login) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        delta: {
                            light: '#86BAF2',
                            primary: '#4D79C1',
                            dark: '#1a4a8d',
                            darker: '#143869',
                            bg: '#f4f8fd',
                            card: '#eaf2fb',
                        },
                    },
                    fontFamily: {
                        sans: ['"Poppins"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        html { scroll-behavior: smooth; }
        body { font-family: 'Poppins', sans-serif; }
        
        /* Gradient Khas Rumah Sakit Delta (dari login.css) */
        .gradient-delta {
            background: linear-gradient(135deg, #86BAF2 0%, #4D79C1 100%);
        }
        .gradient-delta-dark {
            background: linear-gradient(135deg, #4D79C1 0%, #1a4a8d 100%);
        }
        .bg-soft-pattern {
            background-color: #f4f8fd;
            background-image: radial-gradient(#d3e5f8 1.2px, transparent 1.2px);
            background-size: 24px 24px;
        }
        @keyframes pulse-ring {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 14px rgba(37, 211, 102, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(37, 211, 102, 0); }
        }
        .wa-float {
            animation: pulse-ring 2.5s infinite;
        }
    </style>
</head>
<body class="font-sans text-slate-700 bg-white antialiased">

    @if (session('status'))
        <div class="bg-emerald-600 text-white text-xs font-semibold py-2.5 px-4 text-center flex items-center justify-center gap-2 shadow-sm">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- ======================================================== -->
    <!-- 1. TOPBAR & NAVBAR (Tema RS Delta)                       -->
    <!-- ======================================================== -->
    <div class="bg-[#122e54] text-blue-200 text-xs py-2 px-4 hidden md:block border-b border-white/10">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-6">
                <span><i class="fa-solid fa-phone text-emerald-400 mr-2"></i>Emergency Call: <strong class="text-white">+62 (511) 765-4321</strong></span>
                <span><i class="fa-solid fa-envelope text-blue-300 mr-2"></i>info@rs-delta.poliban.ac.id</span>
                <span><i class="fa-solid fa-clock text-blue-300 mr-2"></i>IGD & Pelayanan Siaga 24 Jam Nonstop</span>
            </div>
            <div class="flex items-center gap-4 text-blue-200">
                <a href="#lokasi" class="hover:text-white transition">Peta & Rute</a>
                <span>•</span>
                <a href="#download-apk" class="hover:text-white transition">Unduh Aplikasi</a>
            </div>
        </div>
    </div>

    <!-- Navbar dengan Background Gradient Biru RS Delta agar Logo Putih Menonjol Jelas -->
    <nav class="sticky top-0 z-50 bg-gradient-to-r from-[#1a4a8d] via-[#2a5fa5] to-[#4D79C1] text-white shadow-lg border-b border-white/10 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                
                <!-- Logo Menggunakan logodelta1.svg (Tampil Jelas di atas Warna Biru) -->
                <a href="#" class="flex items-center gap-3.5 group">
                    <div class="w-12 h-12 flex items-center justify-center transition-transform group-hover:scale-105">
                        <img src="{{ asset('images/logodelta1.svg') }}" alt="Logo Rumah Sakit Delta" class="w-12 h-12 object-contain drop-shadow">
                    </div>
                    <div>
                        <span class="text-xl font-bold text-white tracking-tight block leading-tight">Rumah Sakit Delta</span>
                        <span class="block text-[11px] font-light text-blue-100">Dedikasi Untuk Kesembuhan</span>
                    </div>
                </a>

                <!-- Nav Menu -->
                <div class="hidden lg:flex items-center gap-7 text-sm font-medium text-blue-100">
                    <a href="#beranda" class="hover:text-white transition">Beranda</a>
                    <a href="#antrian" class="hover:text-white transition">Live Antrian</a>
                    <a href="#layanan" class="hover:text-white transition">Poli & Dokter</a>
                    <a href="#download-apk" class="text-white font-semibold flex items-center gap-1.5 hover:text-emerald-300 transition bg-white/10 px-3 py-1.5 rounded-xl border border-white/15">
                        <i class="fa-brands fa-android text-emerald-400 text-base"></i> Mobile App
                    </a>
                    <a href="#kontak" class="hover:text-white transition">Kontak & Lokasi</a>
                </div>

                <!-- CTA Actions -->
                <div class="flex items-center gap-3">
                    <a href="https://wa.me/6281234567890?text=Halo%20Admin%20RS%20Delta%2C%20saya%20ingin%20konsultasi%20pelayanan%20kesehatan" 
                       target="_blank"
                       class="hidden sm:inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-white font-semibold text-xs shadow-md shadow-emerald-500/20 transition">
                        <i class="fa-brands fa-whatsapp text-base"></i> Chat WhatsApp
                    </a>

                    @if (session('user_type') === 'pasien')
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-blue-100 hidden sm:inline">
                                Halo, <strong>{{ session('nama_pasien') }}</strong> ({{ session('rm') }})
                            </span>
                            <a href="{{ route('logout') }}" 
                               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-2xl bg-white/15 hover:bg-white/25 text-white font-semibold text-xs border border-white/20 transition">
                                <i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar
                            </a>
                        </div>
                    @elseif (auth()->check())
                        <div class="flex items-center gap-2">
                            <a href="{{ route('dashboard') }}" 
                               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white hover:bg-blue-50 text-[#1a4a8d] font-bold text-xs shadow-md transition">
                                <i class="fa-solid fa-gauge-high"></i> Dashboard
                            </a>
                            <a href="{{ route('logout') }}" 
                               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-2xl bg-white/15 hover:bg-white/25 text-white font-semibold text-xs border border-white/20 transition">
                                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            </a>
                        </div>
                    @else
                        <a href="{{ route('login') }}" 
                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-white hover:bg-blue-50 text-[#1a4a8d] font-bold text-xs shadow-md transition transform hover:-translate-y-0.5">
                            <i class="fa-solid fa-user-lock"></i> Portal RS
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- ======================================================== -->
    <!-- 2. HERO SECTION (Warna Gradient Khas Login RS Delta)     -->
    <!-- ======================================================== -->
    <section id="beranda" class="relative bg-soft-pattern pt-12 pb-24 overflow-hidden border-b border-[#e2edfa]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                <div class="lg:col-span-7 space-y-6">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#e8f2fc] border border-[#bcd7f7] text-[#1a4a8d] text-xs font-semibold">
                        SISTEM INFORMASI MANAJEMEN RUMAH SAKIT TERPADU
                    </div>
                    
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-800 leading-[1.15] tracking-tight">
                        Pelayanan Medis <span class="text-[#4D79C1]">Modern & Nyaman</span> Bersama RS Delta.
                    </h1>
                    
                    <p class="text-slate-600 text-base sm:text-lg max-w-xl leading-relaxed font-light">
                        Komitmen kami memberikan kemudahan akses pendaftaran online, konsultasi dokter spesialis, transparansi kasir pembayaran, hingga penyerahan e-resep farmasi tanpa antre berbelit.
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-wrap gap-4 pt-2">
                        <a href="#antrian" 
                           class="inline-flex items-center gap-3 px-6 py-3.5 rounded-2xl bg-[#1a4a8d] hover:bg-[#143869] text-white font-semibold text-sm shadow-xl shadow-[#1a4a8d]/25 transition transform hover:-translate-y-0.5">
                            <i class="fa-solid fa-ticket"></i> Cek Antrian Hari Ini
                        </a>
                        <a href="#download-apk" 
                           class="inline-flex items-center gap-3 px-6 py-3.5 rounded-2xl gradient-delta hover:opacity-95 text-white font-semibold text-sm shadow-md transition transform hover:-translate-y-0.5">
                            <i class="fa-brands fa-google-play text-white"></i> Unduh APK Pasien
                        </a>
                    </div>

                    <!-- Mini Stats Hero -->
                    <div class="grid grid-cols-3 gap-6 pt-6 border-t border-[#d8e7f8]">
                        <div>
                            <span class="block text-2xl font-bold text-[#1a4a8d]">{{ count($polis) }}</span>
                            <span class="text-xs font-medium text-slate-500">Poli Spesialis</span>
                        </div>
                        <div>
                            <span class="block text-2xl font-bold text-[#1a4a8d]">{{ count($dokters) }}</span>
                            <span class="text-xs font-medium text-slate-500">Dokter Siaga</span>
                        </div>
                        <div>
                            <span class="block text-2xl font-bold text-emerald-600">24 Jam</span>
                            <span class="text-xs font-medium text-slate-500">Layanan IGD</span>
                        </div>
                    </div>
                </div>

                <!-- Sisi Kanan: Kartu Status & Kalender (Nuansa Blue-White Lembut) -->
                <div class="lg:col-span-5 relative">
                    <div class="relative mx-auto max-w-md bg-white rounded-3xl p-6 shadow-xl border border-[#d8e7f8]">
                        
                        <!-- Header Card -->
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-2xl gradient-delta text-white flex items-center justify-center font-bold shadow-md shadow-[#4D79C1]/30">
                                    <i class="fa-solid fa-hospital-user text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-800">Status Layanan Hari Ini</h4>
                                    <p class="text-[11px] text-slate-500" id="live-clock"></p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
                                <i class="fa-solid fa-circle text-[7px] mr-1.5 animate-pulse"></i> AKTIF
                            </span>
                        </div>

                        <!-- Date / Kalender Widget Berwarna Tema Delta & Interaktif -->
                        <div class="mt-4 p-4 rounded-2xl bg-[#eef5fc] border border-[#d2e4f7]">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-bold text-[#1a4a8d] flex items-center gap-1.5 shrink-0">
                                    <i class="fa-regular fa-calendar-days text-[#4D79C1]"></i> Kalender Kunjungan
                                </span>
                                <span id="selected-date-badge" class="text-[11px] font-semibold text-[#1a4a8d] bg-white px-2.5 py-0.5 rounded-full border border-[#bcd7f7] shadow-xs truncate">
                                    {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
                                </span>
                            </div>

                            @php
                                $startOfWeek = now()->startOfWeek(\Carbon\Carbon::SUNDAY);
                                $daysInWeek = [];
                                for ($i = 0; $i < 7; $i++) {
                                    $daysInWeek[] = $startOfWeek->copy()->addDays($i);
                                }
                            @endphp

                            <div class="mt-3 grid grid-cols-7 gap-1 text-center text-xs">
                                <span class="text-[10px] font-bold text-slate-400">Min</span>
                                <span class="text-[10px] font-bold text-slate-400">Sen</span>
                                <span class="text-[10px] font-bold text-slate-400">Sel</span>
                                <span class="text-[10px] font-bold text-slate-400">Rab</span>
                                <span class="text-[10px] font-bold text-slate-400">Kam</span>
                                <span class="text-[10px] font-bold text-slate-400">Jum</span>
                                <span class="text-[10px] font-bold text-slate-400">Sab</span>

                                @foreach ($daysInWeek as $dayDate)
                                    @php
                                        $isToday = $dayDate->isToday();
                                    @endphp
                                    <button type="button"
                                            onclick="pilihTanggal(this, '{{ $dayDate->locale('id')->isoFormat('dddd, D MMMM Y') }}', '{{ $dayDate->format('Y-m-d') }}', '{{ $dayDate->locale('id')->isoFormat('D MMM') }}', {{ $isToday ? 'true' : 'false' }})"
                                            class="date-cell py-1.5 rounded-xl font-semibold text-xs transition cursor-pointer {{ $isToday ? 'gradient-delta text-white shadow-md active-date font-bold' : 'text-slate-700 hover:bg-white hover:text-[#1a4a8d]' }}"
                                            title="Klik untuk melihat data {{ $dayDate->locale('id')->isoFormat('dddd, D MMMM Y') }}">
                                        {{ $dayDate->day }}
                                    </button>
                                @endforeach
                            </div>

                            <!-- Interactive status pill based on selected date -->
                            <div id="visit-status-info" class="mt-2.5 pt-2 border-t border-[#dce9f7] text-[11px] text-[#1a4a8d] flex items-center justify-center font-medium">
                                <i class="fa-solid fa-circle text-[7px] text-emerald-500 mr-1.5 animate-pulse"></i>
                                <span>Layanan Poliklinik Hari Ini Buka (08.00 - 21.00 WITA)</span>
                            </div>
                        </div>

                        <!-- Antrian Stats (Dinamis Sesuai Tanggal Terpilih) -->
                        <div class="mt-5 space-y-3">
                            <div class="flex justify-between items-center p-3 rounded-2xl bg-[#f8fbfe] border border-[#e2edfa] transition-all">
                                <span class="text-xs font-medium text-slate-600">
                                    <i class="fa-solid fa-users text-[#4D79C1] mr-2"></i>
                                    <span id="label-total-pendaftar">Total Pendaftar Hari Ini:</span>
                                </span>
                                <span class="text-sm font-bold text-[#1a4a8d]" id="stat-total-pendaftar">{{ $totalAntrianHariIni }} Pasien</span>
                            </div>
                            <div class="flex justify-between items-center p-3 rounded-2xl bg-[#f8fbfe] border border-[#e2edfa] transition-all">
                                <span class="text-xs font-medium text-slate-600">
                                    <i id="icon-status-periksa" class="fa-solid fa-stethoscope text-emerald-500 mr-2"></i>
                                    <span id="label-status-periksa">Sedang Diperiksa Dokter:</span>
                                </span>
                                <span class="text-sm font-bold text-emerald-600" id="stat-status-periksa">{{ $sedangDilayani }} Pasien</span>
                            </div>
                        </div>

                        <div class="mt-5">
                            <a href="#kontak" class="w-full py-3 block text-center rounded-2xl bg-[#1a4a8d] hover:bg-[#143869] text-white font-semibold text-xs shadow-md transition">
                                <i class="fa-solid fa-paper-plane mr-1.5"></i> Hubungi Customer Care RS Delta
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ======================================================== -->
    <!-- 3. LIVE MONITOR ANTRIAN & POLI                           -->
    <!-- ======================================================== -->
    <section id="antrian" class="py-20 bg-white border-b border-[#e2edfa]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-xs font-bold text-[#1a4a8d] tracking-wider uppercase bg-[#e8f2fc] px-3.5 py-1 rounded-full border border-[#bcd7f7]">
                    TRANSPARANSI PELAYANAN
                </span>
                <h2 class="text-3xl font-extrabold text-slate-800 mt-3">Monitor Antrian Poli Real-Time</h2>
                <p class="text-slate-500 text-sm mt-2 font-light">
                    Pantau nomor antrian yang sedang dipanggil di masing-masing unit poliklinik secara langsung dari smartphone Anda.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($polis as $poli)
                    <div class="bg-white rounded-3xl p-6 border border-[#d8e7f8] shadow-sm hover:shadow-md transition">
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="inline-block px-3 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-[#e8f2fc] text-[#1a4a8d] border border-[#bcd7f7]">
                                    {{ $poli->kode_poli ?? 'POL' }}
                                </span>
                                <h3 class="text-lg font-bold text-slate-800 mt-3">{{ $poli->nama_poli }}</h3>
                            </div>
                            <div class="w-11 h-11 rounded-2xl bg-[#eef5fc] text-[#4D79C1] flex items-center justify-center text-lg">
                                <i class="fa-solid fa-heart-pulse"></i>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                            <div>
                                <span class="text-[11px] font-medium text-slate-400 block">Antrian Terdaftar</span>
                                <span class="text-2xl font-bold text-[#1a4a8d]">{{ $poli->antrian_hari_ini }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[11px] font-medium text-slate-400 block">Tarif Konsultasi</span>
                                <span class="text-sm font-bold text-emerald-600">Rp {{ number_format($poli->biaya_konsultasi, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="mt-5">
                            <a href="#kontak" class="w-full py-2.5 block text-center rounded-2xl bg-[#f4f8fd] hover:bg-[#e8f2fc] text-[#1a4a8d] font-semibold text-xs transition border border-[#d8e7f8]">
                                Tanya Jadwal Dokter Poli Ini
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ======================================================== -->
    <!-- 4. SECTION DOWNLOAD APLIKASI MOBILE FLUTTER (APK)        -->
    <!-- (Warna Disesuaikan dengan Gradient Signature RS Delta)   -->
    <!-- ======================================================== -->
    <section id="download-apk" class="py-20 gradient-delta text-white relative overflow-hidden">
        <div class="absolute -right-20 -bottom-20 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                <div class="lg:col-span-7 space-y-6">
                    <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/20 text-white text-xs font-semibold backdrop-blur-sm border border-white/30">
                        <i class="fa-brands fa-android text-base"></i> APLIKASI PASIEN RESMI (FLUTTER DART)
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight leading-tight">
                        Unduh Aplikasi Mobile Rumah Sakit Delta
                    </h2>
                    <p class="text-blue-50 text-sm sm:text-base leading-relaxed font-light">
                        Kemudahan pendaftaran antrian online dari rumah, notifikasi real-time saat nomor Anda dipanggil, akses riwayat rekam medis, dan bukti pembayaran resmi langsung di genggaman Anda.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-white/10 backdrop-blur-sm border border-white/20">
                            <i class="fa-solid fa-qrcode text-emerald-300 text-2xl"></i>
                            <div>
                                <h4 class="text-xs font-bold text-white">Ambil Antrian Online</h4>
                                <p class="text-[11px] text-blue-100">Pilih poli dan dokter tanpa antre</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-white/10 backdrop-blur-sm border border-white/20">
                            <i class="fa-solid fa-file-waveform text-yellow-300 text-2xl"></i>
                            <div>
                                <h4 class="text-xs font-bold text-white">E-Rekam Medis & Resep</h4>
                                <p class="text-[11px] text-blue-100">Catatan riwayat konsultasi aman</p>
                            </div>
                        </div>
                    </div>

                    <!-- DOWNLOAD BUTTONS -->
                    <div class="pt-4 flex flex-wrap items-center gap-4">
                        <!-- Play Store Button -->
                        <a href="https://play.google.com/store/apps" 
                           target="_blank"
                           class="inline-flex items-center gap-3 px-6 py-3.5 rounded-2xl bg-white text-[#1a4a8d] font-bold hover:bg-slate-50 transition shadow-xl">
                            <i class="fa-brands fa-google-play text-2xl text-emerald-600"></i>
                            <div class="text-left">
                                <span class="block text-[10px] text-slate-400 leading-none">TEMUKAN DI</span>
                                <span class="block text-sm leading-tight font-extrabold">Google Play Store</span>
                            </div>
                        </a>

                        <!-- Direct APK Download Button -->
                        <a href="#" 
                           onclick="alert('Aplikasi Mobile Flutter sedang dalam tahap finalisasi build APK. Segera hadir!'); return false;"
                           class="inline-flex items-center gap-3 px-6 py-3.5 rounded-2xl bg-[#1a4a8d] text-white font-bold hover:bg-[#143869] transition shadow-lg">
                            <i class="fa-solid fa-download text-2xl text-blue-300"></i>
                            <div class="text-left">
                                <span class="block text-[10px] text-blue-200 leading-none">DOWNLOAD LANGSUNG</span>
                                <span class="block text-sm leading-tight font-extrabold">RS Delta Pasien .APK</span>
                            </div>
                        </a>
                    </div>
                    <p class="text-[11px] text-blue-100 italic">
                        * Tautan APK dan Play Store di atas siap kamu hubungkan dengan file build Flutter kamu.
                    </p>
                </div>

                <div class="lg:col-span-5 flex justify-center">
                    <div class="w-68 sm:w-80 bg-white/15 backdrop-blur-md p-4 sm:p-5 rounded-[44px] border-4 border-white/30 shadow-2xl relative select-none">
                        
                        <!-- Speaker / Notch & Status Bar -->
                        <div class="flex items-center justify-between px-3 pt-1 pb-2">
                            <span class="text-[10px] text-white/90 font-semibold" id="mockup-clock">09:15</span>
                            <div class="h-4 w-24 bg-white/20 rounded-full mx-auto"></div>
                            <div class="flex items-center gap-1.5 text-[10px] text-white/90">
                                <i class="fa-solid fa-wifi text-[9px]"></i>
                                <i class="fa-solid fa-battery-three-quarters text-[10px]"></i>
                            </div>
                        </div>

                        <!-- Header Mockup App -->
                        <div class="bg-gradient-to-r from-[#1a4a8d] to-[#4D79C1] rounded-2xl p-4 text-white text-center shadow-lg border border-white/20 relative overflow-hidden">
                            <div class="absolute -right-4 -top-4 w-16 h-16 bg-white/10 rounded-full blur-sm pointer-events-none"></div>
                            
                            <img src="{{ asset('images/logodelta1.svg') }}" alt="Logo Delta" class="w-10 h-10 mx-auto mb-1.5 drop-shadow">
                            <h4 class="font-bold text-sm text-white tracking-tight">RS Delta Mobile</h4>
                            <p class="text-[10px] text-blue-100">
                                <i class="fa-solid fa-circle text-[6px] text-emerald-400 mr-1 animate-pulse"></i>
                                Live Antrian Sistem SIMRS
                            </p>

                            <!-- Badge Nama Pasien & Poli -->
                            <div class="mt-2.5 pt-2 border-t border-white/15 flex items-center justify-between text-[10px]">
                                <span class="bg-white/20 text-white font-medium px-2.5 py-0.5 rounded-full truncate max-w-[130px]" id="mockup-patient">
                                    <i class="fa-solid fa-user text-[9px] mr-1"></i> <span id="mockup-patient-name">{{ $antrianMockup[0]['nama_pasien'] ?? 'Pasien' }}</span>
                                </span>
                                <span class="text-blue-100 font-semibold bg-[#122e54]/50 px-2 py-0.5 rounded-md text-[9px]" id="mockup-poli">
                                    {{ $antrianMockup[0]['poli'] ?? 'Poli Umum' }}
                                </span>
                            </div>
                        </div>

                        <!-- Card Detail Antrian Pasien Aktif -->
                        <div id="mockup-card-container" class="mt-3.5 space-y-2 text-white transition-opacity duration-300">
                            
                            <!-- Nomor Antrian -->
                            <div class="p-3 bg-white/15 rounded-2xl flex items-center justify-between text-xs backdrop-blur-sm border border-white/15 hover:bg-white/20 transition">
                                <span class="text-blue-100 flex items-center gap-2">
                                    <i class="fa-solid fa-ticket text-yellow-300 text-sm"></i> No. Antrian Anda
                                </span>
                                <span class="font-extrabold text-yellow-300 text-sm tracking-wider" id="mockup-antrian">
                                    {{ $antrianMockup[0]['no_antrian'] ?? '-' }}
                                </span>
                            </div>

                            <!-- Status Antrian -->
                            <div class="p-3 bg-white/15 rounded-2xl flex items-center justify-between text-xs backdrop-blur-sm border border-white/15 hover:bg-white/20 transition">
                                <span class="text-blue-100 flex items-center gap-2">
                                    <i class="fa-solid fa-stethoscope text-emerald-300 text-sm"></i> Status Antrian
                                </span>
                                <span class="{{ $antrianMockup[0]['status_color'] ?? 'text-white' }} text-right" id="mockup-status">
                                    {{ $antrianMockup[0]['status_antrian'] ?? '-' }}
                                </span>
                            </div>

                            <!-- Pembayaran Kasir -->
                            <div class="p-3 bg-white/15 rounded-2xl flex items-center justify-between text-xs backdrop-blur-sm border border-white/15 hover:bg-white/20 transition">
                                <span class="text-blue-100 flex items-center gap-2">
                                    <i class="fa-solid fa-receipt text-cyan-300 text-sm"></i> Pembayaran Kasir
                                </span>
                                <span class="{{ $antrianMockup[0]['bayar_color'] ?? 'text-blue-100' }} text-right text-[11px]" id="mockup-bayar">
                                    {{ $antrianMockup[0]['pembayaran'] ?? '-' }}
                                </span>
                            </div>
                        </div>

                        <!-- Mockup Carousel Navigation / Status Footer -->
                        <div class="mt-3.5 pt-2 border-t border-white/20 flex items-center justify-between px-1 text-[10px] text-blue-100">
                            <span id="mockup-queue-index" class="font-medium">
                                Antrian <strong class="text-white" id="mockup-current-num">1</strong> dari <span id="mockup-total-num">{{ count($antrianMockup) }}</span>
                            </span>
                            <div class="flex items-center gap-1.5" id="mockup-dots">
                                @foreach ($antrianMockup as $idx => $item)
                                    <button type="button" 
                                            onclick="setMockupIndex({{ $idx }})" 
                                            class="mockup-dot w-2 h-2 rounded-full transition-all {{ $idx === 0 ? 'bg-white w-4' : 'bg-white/40 hover:bg-white/70' }}" 
                                            title="Lihat antrian {{ $item['nama_pasien'] }}">
                                    </button>
                                @endforeach
                            </div>
                            <span class="text-[9px] text-blue-200 flex items-center gap-1">
                                <i class="fa-solid fa-arrows-rotate text-[8px] animate-spin"></i> Auto
                            </span>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ======================================================== -->
    <!-- 5. CONTACT US, WHATSAPP API & GOOGLE MAPS                -->
    <!-- (Warna Lembut Serasi dengan Login)                       -->
    <!-- ======================================================== -->
    <section id="kontak" class="py-24 bg-[#f8fbfe]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
                
                <!-- SISI KIRI: FORM CONTACT US -->
                <div class="lg:col-span-6 bg-white p-8 sm:p-10 rounded-3xl border border-[#d8e7f8] shadow-sm">
                    <h2 class="text-3xl font-bold text-slate-800">Hubungi Kami</h2>
                    <p class="text-slate-500 text-sm mt-1 mb-8 font-light">
                        Kirimkan pertanyaan atau pesan Anda, staf Rumah Sakit Delta akan segera merespons via WhatsApp atau Telepon.
                    </p>

                    <form id="contact-form" onsubmit="handleContactSubmit(event)" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Depan *</label>
                                <input type="text" id="first-name" required placeholder="Contoh: Ahmad" 
                                       class="w-full px-4 py-3 rounded-2xl border border-[#d8e7f8] focus:border-[#4D79C1] focus:ring-2 focus:ring-[#86BAF2]/30 outline-none text-sm bg-[#f8fbfe]">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Belakang</label>
                                <input type="text" id="last-name" placeholder="Contoh: Fauzi" 
                                       class="w-full px-4 py-3 rounded-2xl border border-[#d8e7f8] focus:border-[#4D79C1] focus:ring-2 focus:ring-[#86BAF2]/30 outline-none text-sm bg-[#f8fbfe]">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Email Anda *</label>
                                <input type="email" id="email" required placeholder="nama@email.com" 
                                       class="w-full px-4 py-3 rounded-2xl border border-[#d8e7f8] focus:border-[#4D79C1] focus:ring-2 focus:ring-[#86BAF2]/30 outline-none text-sm bg-[#f8fbfe]">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor WhatsApp *</label>
                                <input type="tel" id="whatsapp-num" required placeholder="08xxxxxxxxxx" 
                                       class="w-full px-4 py-3 rounded-2xl border border-[#d8e7f8] focus:border-[#4D79C1] focus:ring-2 focus:ring-[#86BAF2]/30 outline-none text-sm bg-[#f8fbfe]">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Poli / Subjek Yang Dituju</label>
                            <select id="subject" class="w-full px-4 py-3 rounded-2xl border border-[#d8e7f8] focus:border-[#4D79C1] focus:ring-2 focus:ring-[#86BAF2]/30 outline-none text-sm bg-[#f8fbfe]">
                                <option value="Informasi Antrian & Pendaftaran">Informasi Antrian & Pendaftaran Loket</option>
                                <option value="Konsultasi Poli Umum">Konsultasi Poli Umum</option>
                                <option value="Konsultasi Poli Gigi">Konsultasi Poli Gigi</option>
                                <option value="Konsultasi Poli Anak">Konsultasi Poli Anak</option>
                                <option value="Informasi Kasir & Pembayaran">Informasi Kasir & Pembayaran</option>
                                <option value="Informasi Resep & Obat Apotik">Informasi Resep & Obat Apotik</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Pesan / Keluhan Anda *</label>
                            <textarea id="message" rows="4" required placeholder="Tuliskan pesan atau pertanyaan Anda di sini..." 
                                      class="w-full px-4 py-3 rounded-2xl border border-[#d8e7f8] focus:border-[#4D79C1] focus:ring-2 focus:ring-[#86BAF2]/30 outline-none text-sm bg-[#f8fbfe]"></textarea>
                        </div>

                        <button type="submit" 
                                class="w-full py-4 rounded-2xl bg-[#1a4a8d] hover:bg-[#143869] text-white font-semibold text-sm shadow-md shadow-[#1a4a8d]/20 transition flex items-center justify-center gap-2">
                            <span>Kirim Langsung ke WhatsApp RS</span>
                            <i class="fa-brands fa-whatsapp text-lg"></i>
                        </button>
                    </form>
                </div>

                <!-- SISI KANAN: NEED ANY HELP CARDS & GOOGLE MAPS -->
                <div class="lg:col-span-6 space-y-6">
                    <div>
                        <h2 class="text-3xl font-bold text-slate-800">Butuh Bantuan Cepat?</h2>
                        <p class="text-slate-500 text-sm mt-1 font-light">
                            Hubungi langsung pusat bantuan Rumah Sakit Delta melalui saluran resmi berikut:
                        </p>
                    </div>

                    <!-- 3 Cards: Phone, Email, Location -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Telepon / WA -->
                        <a href="https://wa.me/6281234567890?text=Halo%20Admin%20RS%20Delta%2C%20saya%20butuh%20bantuan%20cepat" 
                           target="_blank"
                           class="p-5 rounded-2xl bg-white border border-[#d8e7f8] hover:border-[#4D79C1] transition group text-center sm:text-left shadow-sm">
                            <div class="w-10 h-10 rounded-2xl gradient-delta text-white flex items-center justify-center mb-3 mx-auto sm:mx-0 group-hover:scale-110 transition">
                                <i class="fa-solid fa-phone text-sm"></i>
                            </div>
                            <h4 class="text-xs font-bold text-slate-800">WhatsApp / Call</h4>
                            <p class="text-[11px] text-slate-500 mt-1">+62 812-3456-7890</p>
                            <p class="text-[11px] text-slate-500">+62 (511) 765-4321</p>
                        </a>

                        <!-- Email -->
                        <div class="p-5 rounded-2xl bg-white border border-[#d8e7f8] text-center sm:text-left shadow-sm">
                            <div class="w-10 h-10 rounded-2xl gradient-delta text-white flex items-center justify-center mb-3 mx-auto sm:mx-0">
                                <i class="fa-solid fa-envelope text-sm"></i>
                            </div>
                            <h4 class="text-xs font-bold text-slate-800">Email Resmi</h4>
                            <p class="text-[11px] text-slate-500 mt-1">info@rs-delta.poliban.ac.id</p>
                            <p class="text-[11px] text-slate-500">care@rs-delta.com</p>
                        </div>

                        <!-- Lokasi -->
                        <div class="p-5 rounded-2xl bg-white border border-[#d8e7f8] text-center sm:text-left shadow-sm">
                            <div class="w-10 h-10 rounded-2xl gradient-delta text-white flex items-center justify-center mb-3 mx-auto sm:mx-0">
                                <i class="fa-solid fa-location-dot text-sm"></i>
                            </div>
                            <h4 class="text-xs font-bold text-slate-800">Alamat RS</h4>
                            <p class="text-[11px] text-slate-500 mt-1 leading-tight">
                                Jl. Brigjen H. Hasan Basri, Banjarmasin, Kalsel
                            </p>
                        </div>
                    </div>

                    <!-- GOOGLE MAPS EMBED -->
                    <div id="lokasi" class="rounded-3xl overflow-hidden border border-[#d8e7f8] shadow-md h-80 relative">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3983.1970220268593!2d114.58288827473076!3d-3.298910096675765!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2de423a633192305%3A0x7d013f9c6566c7dd!2sPoliteknik%20Negeri%20Banjarmasin!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid" 
                            width="100%" 
                            height="100%" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ======================================================== -->
    <!-- 6. FOOTER LENGKAP (Tema Warna RS Delta)                  -->
    <!-- ======================================================== -->
    <footer class="bg-[#143869] text-white pt-16 pb-12 border-t border-[#1a4a8d]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 pb-12 border-b border-white/10">
                
                <!-- Col 1: Brand Info dengan logodelta1.svg (Tanpa Kotak Putih, Menonjol di Background Biru Gelap) -->
                <div class="lg:col-span-2 space-y-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 flex items-center justify-center">
                            <img src="{{ asset('images/logodelta1.svg') }}" alt="Logo Delta" class="w-12 h-12 object-contain drop-shadow">
                        </div>
                        <div>
                            <span class="text-xl font-bold tracking-tight block leading-tight text-white">Rumah Sakit Delta</span>
                            <span class="block text-xs font-light text-blue-200">Dedikasi Untuk Kesembuhan</span>
                        </div>
                    </div>
                    <p class="text-blue-100/80 text-xs leading-relaxed max-w-sm font-light">
                        Sistem Informasi Manajemen Rumah Sakit (SIMRS) Delta terintegrasi dengan arsitektur microservices API untuk 4 modul kelompok dan aplikasi mobile Flutter.
                    </p>
                    <div class="flex items-center gap-3 pt-2">
                        <a href="#" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-xs text-white transition"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="https://wa.me/6281234567890?text=Halo%20Admin%20RS%20Delta" target="_blank" class="w-8 h-8 rounded-full bg-emerald-500/20 hover:bg-emerald-500 flex items-center justify-center text-xs text-emerald-400 hover:text-white transition" title="WhatsApp Resmi RS Delta"><i class="fa-brands fa-whatsapp text-sm"></i></a>
                        <a href="#" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-xs text-white transition"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-xs text-white transition"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>

                <!-- Col 2: Nav Links -->
                <div>
                    <h4 class="text-sm font-bold tracking-wider uppercase mb-4 text-white">Navigasi</h4>
                    <ul class="space-y-2.5 text-xs text-blue-100/80 font-light">
                        <li><a href="#beranda" class="hover:text-white transition">Beranda Utama</a></li>
                        <li><a href="#antrian" class="hover:text-white transition">Cek Antrian Live</a></li>
                        <li><a href="#layanan" class="hover:text-white transition">Poliklinik Spesialis</a></li>
                        <li><a href="#download-apk" class="hover:text-white transition">Aplikasi Mobile Flutter</a></li>
                        <li><a href="#kontak" class="hover:text-white transition">Kontak & Lokasi</a></li>
                    </ul>
                </div>

                <!-- Col 3: Modul SIMRS -->
                <div>
                    <h4 class="text-sm font-bold tracking-wider uppercase mb-4 text-white">Modul Sistem</h4>
                    <ul class="space-y-2.5 text-xs text-blue-100/80 font-light">
                        <li><span class="text-white font-semibold">Bagian 1:</span> Auth & Pendaftaran</li>
                        <li><span class="text-white font-semibold">Bagian 2:</span> Poli & E-Resep</li>
                        <li><span class="text-white font-semibold">Bagian 3:</span> Kasir & Pembayaran</li>
                        <li><span class="text-white font-semibold">Bagian 4:</span> Apotik & Farmasi</li>
                    </ul>
                </div>

                <!-- Col 4: Emergency Contacts -->
                <div>
                    <h4 class="text-sm font-bold tracking-wider uppercase mb-4 text-white">Kontak Darurat</h4>
                    <ul class="space-y-2 text-xs text-blue-100/80 font-light">
                        <li><strong class="text-white">Call Center:</strong> (0511) 765-4321</li>
                        <li><strong class="text-white">WhatsApp:</strong> +62 812-3456-7890</li>
                        <li><strong class="text-white">Email:</strong> info@rs-delta.poliban.ac.id</li>
                        <li class="pt-2 text-[11px] text-blue-200">Banjarmasin, Kalimantan Selatan</li>
                    </ul>
                </div>

            </div>

            <div class="pt-8 flex flex-col sm:flex-row justify-between items-center text-xs text-blue-200 font-light">
                <p>&copy; 2026 Rumah Sakit Delta Enterprise - PBL Prodi TI Politeknik Negeri Banjarmasin.</p>
                <div class="flex gap-4 mt-4 sm:mt-0">
                    <a href="#" class="hover:text-white">Privasi Pasien</a>
                    <span>&bull;</span>
                    <a href="#" class="hover:text-white">Dokumentasi API SIMRS</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- ======================================================== -->
    <!-- 7. FLOATING WHATSAPP BUTTON (PULSE ANIMATION)             -->
    <!-- ======================================================== -->
    <a href="https://wa.me/6281234567890?text=Halo%20Admin%20RS%20Delta%2C%20saya%20ingin%20bertanya%20seputar%20pelayanan%20rumah%20sakit" 
       target="_blank"
       title="Hubungi Admin RS Delta via WhatsApp"
       class="fixed bottom-6 right-6 z-50 flex items-center gap-2.5 bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-3 rounded-full shadow-2xl wa-float transition transform hover:scale-105">
        <i class="fa-brands fa-whatsapp text-2xl"></i>
        <span class="text-xs font-semibold pr-1 hidden sm:inline-block">Chat WhatsApp 24 Jam</span>
    </a>

    <!-- SCRIPT CLOCK & WHATSAPP FORM & INTERACTIVE CALENDAR -->
    <script>
        // Fitur klik pilih tanggal kalender kunjungan (Tarik Data Real dari Database)
        async function pilihTanggal(btn, fullDateStr, ymd, shortDate, isToday) {
            // Style active cell
            document.querySelectorAll('.date-cell').forEach(el => {
                el.classList.remove('gradient-delta', 'text-white', 'shadow-md', 'active-date', 'font-bold');
                el.classList.add('text-slate-700', 'hover:bg-white', 'hover:text-[#1a4a8d]');
            });

            btn.classList.add('gradient-delta', 'text-white', 'shadow-md', 'active-date', 'font-bold');
            btn.classList.remove('text-slate-700', 'hover:bg-white', 'hover:text-[#1a4a8d]');

            const badge = document.getElementById('selected-date-badge');
            if (badge) {
                badge.innerText = fullDateStr;
            }

            const info = document.getElementById('visit-status-info');
            const labelPendaftar = document.getElementById('label-total-pendaftar');
            const statPendaftar = document.getElementById('stat-total-pendaftar');
            const labelPeriksa = document.getElementById('label-status-periksa');
            const statPeriksa = document.getElementById('stat-status-periksa');
            const iconPeriksa = document.getElementById('icon-status-periksa');

            // Feedback visual saat memuat data dari database
            if (statPendaftar) statPendaftar.innerText = '...';
            if (statPeriksa) statPeriksa.innerText = '...';

            try {
                const response = await fetch(`{{ url('/status-tanggal') }}?tanggal=${ymd}`);
                const res = await response.json();

                if (res.success) {
                    if (res.is_today) {
                        labelPendaftar.innerText = 'Total Pendaftar Hari Ini:';
                        statPendaftar.innerText = `${res.total_pendaftar} Pasien`;
                        labelPeriksa.innerText = 'Sedang Diperiksa Dokter:';
                        statPeriksa.innerText = `${res.sedang_diperiksa} Pasien`;
                        statPeriksa.className = 'text-sm font-bold text-emerald-600';
                        if (iconPeriksa) iconPeriksa.className = 'fa-solid fa-stethoscope text-emerald-500 mr-2';

                        if (info) {
                            info.innerHTML = '<i class="fa-solid fa-circle text-[7px] text-emerald-500 mr-1.5 animate-pulse"></i><span>Layanan Poliklinik Hari Ini Buka (08.00 - 21.00 WITA)</span>';
                        }
                    } else if (res.is_past) {
                        labelPendaftar.innerText = `Total Kunjungan (${shortDate}):`;
                        statPendaftar.innerText = `${res.total_pendaftar} Pasien`;
                        labelPeriksa.innerText = 'Sudah Selesai Diperiksa:';
                        statPeriksa.innerText = `${res.selesai_diperiksa} Pasien`;
                        statPeriksa.className = 'text-sm font-bold text-[#1a4a8d]';
                        if (iconPeriksa) iconPeriksa.className = 'fa-solid fa-circle-check text-blue-500 mr-2';

                        if (info) {
                            info.innerHTML = `<i class="fa-solid fa-clock-rotate-left text-[#4D79C1] mr-1.5"></i><span>Rekap Pelayanan Selesai Tanggal ${fullDateStr}</span>`;
                        }
                    } else {
                        // Future date (Booking antrian online)
                        labelPendaftar.innerText = `Antrian Terbooking (${shortDate}):`;
                        statPendaftar.innerText = `${res.total_pendaftar} Pasien`;
                        labelPeriksa.innerText = 'Status Kuota Poli:';
                        statPeriksa.innerText = res.total_pendaftar > 0 ? `${res.total_pendaftar} Booking Masuk` : 'Kuota Tersedia';
                        statPeriksa.className = 'text-sm font-bold text-emerald-600';
                        if (iconPeriksa) iconPeriksa.className = 'fa-solid fa-calendar-plus text-emerald-500 mr-2';

                        if (info) {
                            info.innerHTML = `<i class="fa-solid fa-calendar-check text-[#4D79C1] mr-1.5"></i><span>Pendaftaran Online untuk ${fullDateStr} Siap Dipesan</span>`;
                        }
                    }
                }
            } catch (err) {
                console.error('Gagal mengambil data tanggal:', err);
                if (statPendaftar) statPendaftar.innerText = '0 Pasien';
                if (statPeriksa) statPeriksa.innerText = '0 Pasien';
            }
        }

        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            const clockEl = document.getElementById('live-clock');
            if (clockEl) {
                clockEl.innerText = 'WITA • ' + timeStr;
            }
            const mockClock = document.getElementById('mockup-clock');
            if (mockClock) {
                mockClock.innerText = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            }
        }
        setInterval(updateClock, 1000);
        updateClock();

        // ========================================================
        // SIMULASI LIVE FEED ANTRIAN PASIEN PADA MOCKUP MOBILE
        // Berubah-ubah secara dinamis mengambil data real SIMRS
        // ========================================================
        const rawAntrianData = @json($antrianMockup);
        let currentMockupIdx = 0;
        let mockupInterval = null;

        function renderMockupItem(idx) {
            if (!rawAntrianData || rawAntrianData.length === 0) return;
            const item = rawAntrianData[idx];

            const card = document.getElementById('mockup-card-container');
            const pName = document.getElementById('mockup-patient-name');
            const pPoli = document.getElementById('mockup-poli');
            const pAntrian = document.getElementById('mockup-antrian');
            const pStatus = document.getElementById('mockup-status');
            const pBayar = document.getElementById('mockup-bayar');
            const curNum = document.getElementById('mockup-current-num');

            if (card) {
                card.style.opacity = '0.2';
                card.style.transform = 'scale(0.98)';
            }

            setTimeout(() => {
                if (pName) pName.innerText = item.nama_pasien;
                if (pPoli) pPoli.innerText = item.poli;
                if (pAntrian) pAntrian.innerText = item.no_antrian;
                if (pStatus) {
                    pStatus.innerText = item.status_antrian;
                    pStatus.className = (item.status_color || 'text-white') + ' text-right';
                }
                if (pBayar) {
                    pBayar.innerText = item.pembayaran;
                    pBayar.className = (item.bayar_color || 'text-blue-100') + ' text-right text-[11px]';
                }
                if (curNum) curNum.innerText = idx + 1;

                // Update visual dots
                document.querySelectorAll('.mockup-dot').forEach((dot, dIdx) => {
                    if (dIdx === idx) {
                        dot.className = 'mockup-dot w-4 h-2 rounded-full bg-white transition-all';
                    } else {
                        dot.className = 'mockup-dot w-2 h-2 rounded-full bg-white/40 hover:bg-white/70 transition-all';
                    }
                });

                if (card) {
                    card.style.opacity = '1';
                    card.style.transform = 'scale(1)';
                }
            }, 250);
        }

        function setMockupIndex(idx) {
            currentMockupIdx = idx;
            renderMockupItem(currentMockupIdx);
            resetMockupTimer();
        }

        function nextMockupItem() {
            if (!rawAntrianData || rawAntrianData.length <= 1) return;
            currentMockupIdx = (currentMockupIdx + 1) % rawAntrianData.length;
            renderMockupItem(currentMockupIdx);
        }

        function resetMockupTimer() {
            if (mockupInterval) clearInterval(mockupInterval);
            mockupInterval = setInterval(nextMockupItem, 3800);
        }

        // Jalankan rotasi otomatis jika terdapat lebih dari 1 antrian di database
        if (rawAntrianData && rawAntrianData.length > 1) {
            resetMockupTimer();
        }

        function handleContactSubmit(e) {
            e.preventDefault();
            const firstName = document.getElementById('first-name').value.trim();
            const lastName = document.getElementById('last-name').value.trim();
            const email = document.getElementById('email').value.trim();
            const waNum = document.getElementById('whatsapp-num').value.trim();
            const subject = document.getElementById('subject').value;
            const msg = document.getElementById('message').value.trim();

            const textTemplate = 
`*Pesan Baru dari Pasien RS Delta*
---------------------------------------
*Nama:* ${firstName} ${lastName}
*WhatsApp:* ${waNum}
*Email:* ${email}
*Perihal:* ${subject}
*Pesan:*
${msg}
---------------------------------------`;

            const encoded = encodeURIComponent(textTemplate);
            const waTarget = "6281234567890"; // Nomor WA Rumah Sakit
            const url = `https://wa.me/${waTarget}?text=${encoded}`;
            
            window.open(url, '_blank');
        }
    </script>
</body>
</html>
