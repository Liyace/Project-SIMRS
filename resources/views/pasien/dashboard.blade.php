<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Pasien Mandiri - Rumah Sakit Delta</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logodelta1.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-[#f0f6fd] min-h-screen text-slate-700 flex flex-col">

    <!-- NAVBAR TERPADU PASIEN -->
    @include('partials.navbar-pasien', ['active' => 'dashboard'])

    <!-- MAIN CONTAINER -->
    <main class="max-w-6xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 flex-1">

        <!-- FLASH NOTIFICATION -->
        @if (session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-xl"></i>
                    <span class="text-xs sm:text-sm font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl flex items-center gap-3 shadow-sm">
                <i class="fa-solid fa-triangle-exclamation text-rose-500 text-xl"></i>
                <span class="text-xs sm:text-sm font-medium">{{ session('error') }}</span>
            </div>
        @endif

        <!-- WELCOME BANNER -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-[#e2edfa] mb-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <button type="button" onclick="bukaModalEditProfilPasien()" class="relative group cursor-pointer text-left flex-shrink-0" title="Klik untuk edit data profil">
                    <img src="{{ $pasien->foto_url }}" alt="Avatar" class="w-20 h-20 rounded-full object-cover border-4 border-[#86BAF2] shadow-md group-hover:border-[#1a4a8d] transition">
                    <span class="absolute bottom-0 right-0 w-6 h-6 rounded-full bg-white border border-slate-200 flex items-center justify-center text-[10px] {{ ($pasien->jenis_kelamin ?? 'L') === 'P' ? 'text-pink-500' : 'text-blue-500' }}">
                        <i class="fa-solid {{ ($pasien->jenis_kelamin ?? 'L') === 'P' ? 'fa-venus' : 'fa-mars' }}"></i>
                    </span>
                    <span class="absolute top-0 right-0 w-5 h-5 rounded-full bg-[#1a4a8d] text-white border-2 border-white flex items-center justify-center text-[8px] opacity-0 group-hover:opacity-100 transition shadow">
                        <i class="fa-solid fa-pen"></i>
                    </span>
                </button>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                            Pasien Terdaftar
                        </span>
                        <button type="button" onclick="bukaModalEditProfilPasien()"
                                class="text-[11px] font-semibold text-[#1a4a8d] hover:underline flex items-center gap-1 transition">
                            <i class="fa-solid fa-user-pen text-[10px]"></i> Edit Data
                        </button>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-800 mt-1">
                        {{ $pasien->nama_pasien ?? session('nama_pasien') }}
                    </h1>
                    <div class="flex flex-wrap items-center gap-3 mt-1 text-xs text-slate-500">
                        <span>No. RM: <strong class="text-[#1a4a8d]">{{ $pasien->rm ?? session('rm') }}</strong></span>
                        <span>•</span>
                        <span>NIK: <strong>{{ $pasien->nik }}</strong></span>
                        @if($pasien->no_hp)
                            <span>•</span>
                            <span>WA: <strong>{{ $pasien->no_hp }}</strong></span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="bg-[#f0f6fd] border border-[#d8e7f8] rounded-2xl px-5 py-3 text-center">
                    <span class="text-[11px] text-slate-500 block">Total Kunjungan</span>
                    <span class="text-lg font-bold text-[#1a4a8d]">{{ $totalKunjungan ?? $riwayatKunjungan->count() }} Kali</span>
                </div>
            </div>
        </div>

        <!-- LIVE CALL NOTIFICATION BANNER (MUNCUL OTOMATIS SAAT NOMOR PASIEN DIPANGGIL) - FULL WIDTH -->
        <div id="pasien-call-banner" class="hidden mb-6 bg-gradient-to-r from-amber-500 via-rose-500 to-red-600 text-white p-5 rounded-3xl shadow-2xl border-2 border-white/40 transform transition-all duration-300">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-2xl flex-shrink-0 animate-bounce">
                        <i class="fa-solid fa-bullhorn text-amber-200"></i>
                    </div>
                    <div>
                        <span class="text-[11px] uppercase tracking-wider font-extrabold text-amber-200 block">PANGGILAN ANTREAN AKTIF!</span>
                        <h3 class="text-base sm:text-lg font-black text-white" id="call-banner-text">NOMOR ANTREAN ANDA SEDANG DIPANGGIL!</h3>
                        <p class="text-xs text-white/95 mt-0.5" id="call-banner-dest">Segera menuju ke lokasi ruangan.</p>
                    </div>
                </div>
                <button type="button" onclick="dengarUlangPanggilan()" class="px-4 py-2.5 rounded-xl bg-white text-rose-700 font-bold text-xs hover:bg-amber-50 shadow transition flex items-center gap-2 flex-shrink-0">
                    <i class="fa-solid fa-volume-high"></i> <span class="hidden sm:inline">Dengar Ulang</span>
                </button>
            </div>
        </div>

        <!-- SECTION: ANTREAN AKTIF ATAU FORM PENDAFTARAN BARU -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- KOLOM KIRI (7/12): KARTU ANTREAN AKTIF ATAU FORM BOOKING -->
            <div class="lg:col-span-7 space-y-6">

                @if ($antreanAktif)
                    <!-- DIGITAL E-TICKET PASS -->
                    <div class="bg-gradient-to-br from-[#1a4a8d] via-[#20559f] to-[#153f7c] rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden border border-blue-400/30">
                        <!-- Watermark Icon -->
                        <div class="absolute -right-6 -bottom-6 text-white/5 text-[180px] pointer-events-none">
                            <i class="fa-solid fa-notes-medical"></i>
                        </div>

                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <span class="inline-block text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-full bg-white/20 text-blue-100 backdrop-blur-sm border border-white/20">
                                    Tiket Antrean Berjalan Hari Ini
                                </span>
                                <h2 class="text-xl font-bold text-white mt-2">
                                    {{ $antreanAktif->poli->nama_poli ?? 'Poliklinik' }}
                                </h2>
                                <p class="text-xs text-blue-200 mt-0.5">
                                    {{ $antreanAktif->dokter->nama ?? 'Dokter Poliklinik Bertugas' }}
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="text-[11px] text-blue-200 block">Cara Bayar</span>
                                <span class="text-xs font-bold uppercase px-2.5 py-1 rounded-lg bg-emerald-400 text-emerald-950 inline-block mt-1">
                                    {{ $antreanAktif->cara_bayar }}
                                </span>
                            </div>
                        </div>

                        <!-- BIG QUEUE CODE -->
                        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 text-center border border-white/20 my-5">
                            <span class="text-xs text-blue-200 block uppercase tracking-wider font-semibold">Nomor Antrean Anda</span>
                            <div class="text-5xl sm:text-6xl font-black text-white tracking-widest my-2 drop-shadow-sm">
                                {{ $antreanAktif->no_antrian }}
                            </div>
                            <span class="text-xs text-blue-100 block">
                                Terdaftar pada {{ $antreanAktif->tanggal_daftar ? $antreanAktif->tanggal_daftar->format('d M Y • H:i') : now()->format('d M Y • H:i') }} WITA
                            </span>

                            <!-- LIVE STATUS PILL -->
                            <div id="pasien-live-status-pill" class="mt-4 inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/15 text-white text-xs font-semibold backdrop-blur-md border border-white/25">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span id="pasien-live-status-text">Memantau antrean realtime...</span>
                            </div>
                        </div>

                        <!-- STATUS STEPPER TRACKER -->
                        <div class="mt-6 pt-5 border-t border-white/15">
                            <span class="text-[11px] font-semibold text-blue-200 uppercase tracking-wider block mb-3">
                                Status Pelayanan Terkini:
                            </span>

                            @php
                                $st = $antreanAktif->status_antrian;
                                $step1 = true;
                                $step2 = in_array($st, ['menunggu_dokter', 'sedang_diperiksa', 'menunggu_kasir', 'menunggu_obat', 'selesai']);
                                $step3 = in_array($st, ['sedang_diperiksa', 'menunggu_kasir', 'menunggu_obat', 'selesai']);
                                $step4 = in_array($st, ['selesai']);
                            @endphp

                            <div class="grid grid-cols-4 gap-2 text-center text-[10px]">
                                <div class="flex flex-col items-center">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold mb-1 {{ $step1 ? 'bg-emerald-400 text-emerald-950' : 'bg-white/20 text-white' }}">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                    <span class="font-medium text-blue-100">Pendaftaran</span>
                                </div>
                                <div class="flex flex-col items-center">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold mb-1 {{ $step2 ? 'bg-emerald-400 text-emerald-950' : ($st === 'menunggu_skrining' ? 'bg-amber-400 text-amber-950 ring-4 ring-amber-400/30' : 'bg-white/20 text-white') }}">
                                        <i class="fa-solid {{ $step2 ? 'fa-check' : 'fa-heart-pulse' }}"></i>
                                    </div>
                                    <span class="font-medium {{ $st === 'menunggu_skrining' ? 'text-amber-300 font-bold' : 'text-blue-100' }}">Skrining TTV</span>
                                </div>
                                <div class="flex flex-col items-center">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold mb-1 {{ $step3 ? 'bg-emerald-400 text-emerald-950' : (in_array($st, ['menunggu_dokter', 'sedang_diperiksa']) ? 'bg-amber-400 text-amber-950 ring-4 ring-amber-400/30' : 'bg-white/20 text-white') }}">
                                        <i class="fa-solid {{ $step3 ? 'fa-check' : 'fa-user-doctor' }}"></i>
                                    </div>
                                    <span class="font-medium {{ in_array($st, ['menunggu_dokter', 'sedang_diperiksa']) ? 'text-amber-300 font-bold' : 'text-blue-100' }}">Dokter</span>
                                </div>
                                <div class="flex flex-col items-center">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold mb-1 {{ $step4 ? 'bg-emerald-400 text-emerald-950' : (in_array($st, ['menunggu_kasir', 'menunggu_obat']) ? 'bg-amber-400 text-amber-950 ring-4 ring-amber-400/30' : 'bg-white/20 text-white') }}">
                                        <i class="fa-solid {{ $step4 ? 'fa-check' : 'fa-receipt' }}"></i>
                                    </div>
                                    <span class="font-medium {{ in_array($st, ['menunggu_kasir', 'menunggu_obat']) ? 'text-amber-300 font-bold' : 'text-blue-100' }}">Kasir/Obat</span>
                                </div>
                            </div>
                        </div>

                        <!-- ACTIONS -->
                        <div class="mt-6 flex flex-wrap gap-3">
                            <a href="{{ route('pasien.tiket', $antreanAktif->id_pendaftaran) }}" target="_blank"
                               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-white text-[#1a4a8d] font-bold text-xs hover:bg-blue-50 transition shadow">
                                <i class="fa-solid fa-print"></i> Cetak / Simpan Karcis Antrean
                            </a>
                            <a href="{{ route('pasien.riwayat.detail', $antreanAktif->id_pendaftaran) }}"
                               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white/20 hover:bg-white/30 text-white font-semibold text-xs border border-white/25 transition">
                                <i class="fa-solid fa-notes-medical"></i> Lihat Hasil Medis
                            </a>
                            <button type="button" onclick="location.reload()" 
                                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white/15 hover:bg-white/25 text-white font-semibold text-xs border border-white/20 transition">
                                <i class="fa-solid fa-rotate-right"></i> Perbarui Status
                            </button>
                        </div>
                    </div>

                    <!-- PANDUAN KEDATANGAN -->
                    <div class="bg-white rounded-3xl p-6 border border-[#e2edfa] shadow-sm">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#1a4a8d] flex items-center justify-center text-sm flex-shrink-0 mt-0.5">
                                <i class="fa-solid fa-circle-info"></i>
                            </div>
                            <div class="text-xs text-slate-600 leading-relaxed">
                                <strong class="text-slate-800 text-sm block mb-1">Petunjuk Menuju Ruang Skrining:</strong>
                                Silakan langsung menuju ke <strong>Ruang Skrining Perawat</strong> di Lantai 1 RS Delta. Tunjukkan nomor antrean <strong>{{ $antreanAktif->no_antrian }}</strong> kepada perawat jaga untuk dilakukan pemeriksaan tensi, suhu tubuh, dan tanda vital sebelum dipanggil ke ruang dokter.
                            </div>
                        </div>
                    </div>

                @else
                    <!-- FORM AMBIL ANTREAN MANDIRI ONLINE -->
                    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-[#e2edfa]">
                        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                            <div class="w-10 h-10 rounded-2xl bg-blue-50 text-[#1a4a8d] flex items-center justify-center text-lg">
                                <i class="fa-solid fa-calendar-plus"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-slate-800">Daftar Antrean Berobat Hari Ini</h2>
                                <p class="text-xs text-slate-500">Pilih poliklinik dan dokter untuk reservasi mandiri secara online</p>
                            </div>
                        </div>

                        <form action="{{ route('pasien.daftar-antrean') }}" method="POST" class="space-y-4">
                            @csrf

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pilih Poliklinik Tujuan *</label>
                                <select name="id_poli" id="pasien-select-poli" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-blue-400 bg-white">
                                    <option value="" disabled selected>-- Pilih Poliklinik Rawat Jalan --</option>
                                    @foreach ($polis as $poli)
                                        <option value="{{ $poli->id_poli }}">
                                            {{ $poli->nama_poli }} (Biaya Konsultasi: Rp {{ number_format($poli->biaya_konsultasi, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pilih Dokter Spesialis / Umum</label>
                                <select name="id_dokter" id="pasien-select-dokter" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-blue-400 bg-white">
                                    <option value="">-- Dokter Yang Bertugas Hari Ini --</option>
                                    @foreach ($dokters as $doc)
                                        <option value="{{ $doc->id_pegawai }}" data-poli="{{ $doc->id_poli }}" data-spesialisasi="{{ $doc->spesialisasi }}">
                                            {{ $doc->nama }} {{ $doc->spesialisasi ? '— ' . $doc->spesialisasi : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Penjamin / Cara Pembayaran *</label>
                                <div class="grid grid-cols-3 gap-3">
                                    <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 cursor-pointer text-xs font-medium hover:bg-slate-50">
                                        <input type="radio" name="cara_bayar" value="umum" checked class="text-[#1a4a8d]">
                                        <span>Umum / Mandiri</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 cursor-pointer text-xs font-medium hover:bg-slate-50">
                                        <input type="radio" name="cara_bayar" value="bpjs" class="text-[#1a4a8d]">
                                        <span>BPJS Kesehatan</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 cursor-pointer text-xs font-medium hover:bg-slate-50">
                                        <input type="radio" name="cara_bayar" value="asuransi" class="text-[#1a4a8d]">
                                        <span>Asuransi Swasta</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Keluhan Utama / Gejala yang Dirasakan</label>
                                <textarea name="keluhan_awal" rows="3" placeholder="Contoh: Demam naik turun sejak 3 hari lalu, pusing, batuk dan badan menggigil..."
                                          class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-blue-400"></textarea>
                            </div>

                            <button type="submit" 
                                    class="w-full py-3.5 px-6 rounded-2xl bg-[#1a4a8d] hover:bg-[#143869] text-white text-xs sm:text-sm font-bold shadow-lg transition flex items-center justify-center gap-2">
                                <i class="fa-solid fa-ticket"></i> Ambil Nomor Antrean Berobat Sekarang
                            </button>
                        </form>
                    </div>

                    <!-- PANDUAN ALUR RAWAT JALAN & INFORMASI LAYANAN RS DELTA -->
                    <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-[#e2edfa]">
                        <div class="flex items-center justify-between mb-5 pb-3 border-b border-slate-100">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#1a4a8d] flex items-center justify-center text-base">
                                    <i class="fa-solid fa-clipboard-list"></i>
                                </div>
                                <div>
                                    <h3 class="text-sm sm:text-base font-bold text-slate-800">Panduan Alur Pelayanan Poliklinik</h3>
                                    <p class="text-[11px] text-slate-400">4 tahapan mudah berobat jalan di RS Delta</p>
                                </div>
                            </div>
                            <span class="hidden sm:inline-block text-[10px] font-bold px-2.5 py-1 rounded-full bg-blue-50 text-[#1a4a8d] border border-blue-200">
                                Info Layanan
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div class="flex items-start gap-3 p-3.5 rounded-2xl bg-[#f8fafc] border border-slate-100">
                                <div class="w-7 h-7 rounded-xl bg-blue-100 text-[#1a4a8d] font-bold text-xs flex items-center justify-center flex-shrink-0 mt-0.5">
                                    1
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-800">Daftar Antrean</h4>
                                    <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">Ambil tiket mandiri via portal ini atau cetak nomor di loket utama.</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 p-3.5 rounded-2xl bg-[#f8fafc] border border-slate-100">
                                <div class="w-7 h-7 rounded-xl bg-amber-100 text-amber-800 font-bold text-xs flex items-center justify-center flex-shrink-0 mt-0.5">
                                    2
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-800">Skrining Tanda Vital</h4>
                                    <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">Pemeriksaan tensi darah, nadi, & suhu oleh perawat di Ruang Skrining.</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 p-3.5 rounded-2xl bg-[#f8fafc] border border-slate-100">
                                <div class="w-7 h-7 rounded-xl bg-indigo-100 text-indigo-800 font-bold text-xs flex items-center justify-center flex-shrink-0 mt-0.5">
                                    3
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-800">Konsultasi Dokter</h4>
                                    <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">Pemeriksaan medis poliklinik spesialis, diagnosis, & e-resep farmasi.</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 p-3.5 rounded-2xl bg-[#f8fafc] border border-slate-100">
                                <div class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-800 font-bold text-xs flex items-center justify-center flex-shrink-0 mt-0.5">
                                    4
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-800">Kasir & Farmasi</h4>
                                    <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">Penyelesaian administrasi biaya serta pengambilan obat di farmasi.</p>
                                </div>
                            </div>
                        </div>

                        <!-- JAM OPERASIONAL & BANTUAN -->
                        <div class="mt-4 pt-3.5 border-t border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-[11px] text-slate-500">
                            <div class="flex items-center gap-2">
                                <i class="fa-regular fa-clock text-[#1a4a8d]"></i>
                                <span>Layanan Poliklinik: <strong>Senin - Sabtu (08:00 - 15:00 WITA)</strong></span>
                            </div>
                            <div class="flex items-center gap-2 text-rose-600 font-medium">
                                <i class="fa-solid fa-headset"></i>
                                <span>Bantuan / IGD 24 Jam: (0511) 330-1122</span>
                            </div>
                        </div>
                    </div>
                @endif

            </div>

            <!-- KOLOM KANAN (5/12): RIWAYAT KUNJUNGAN & DATA PASIEN -->
            <div class="lg:col-span-5 space-y-6">

                <!-- KARTU DATA PASIEN & KARTU BEROBAT DIGITAL -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-[#e2edfa]">
                    <div class="flex items-center justify-between gap-2 mb-4 pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-blue-50 text-[#1a4a8d] flex items-center justify-center text-sm">
                                <i class="fa-solid fa-id-card"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-800 leading-tight">Identitas Medis Pasien</h3>
                                <span class="text-[10px] text-slate-400">Kartu Berobat Digital & Rekam Medis</span>
                            </div>
                        </div>
                        <button type="button" onclick="bukaModalKartuPasien()"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 text-[#1a4a8d] hover:bg-[#1a4a8d] hover:text-white font-bold text-[11px] border border-blue-200 transition shadow-sm"
                           title="Lihat Pratinjau & Cetak Kartu Berobat Standar CR80">
                            <i class="fa-solid fa-id-card"></i>
                            <span>Lihat Kartu</span>
                        </button>
                    </div>

                    <!-- MINI DIGITAL CARD DISPLAY -->
                    <div class="bg-gradient-to-br from-[#1a4a8d] via-[#20559f] to-[#12366b] text-white rounded-2xl p-4 mb-4 shadow-md relative overflow-hidden border border-blue-400/20">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('images/logodelta1.svg') }}" alt="Logo" class="w-6 h-6 object-contain">
                                <div>
                                    <span class="text-[10px] font-bold tracking-wider uppercase block text-blue-100 leading-none">Rumah Sakit Delta</span>
                                    <span class="text-[8px] text-blue-200/90 font-light block">Kartu Pasien Rawat Jalan</span>
                                </div>
                            </div>
                            <span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-emerald-400/20 text-emerald-300 border border-emerald-400/30">
                                Aktif
                            </span>
                        </div>

                        <div class="my-3">
                            <span class="text-[10px] text-blue-200 block uppercase font-medium">Nomor Rekam Medis</span>
                            <div class="text-2xl font-black tracking-widest text-white">{{ $pasien->rm }}</div>
                            <div class="text-xs font-semibold text-blue-100 mt-0.5 truncate">{{ $pasien->nama_pasien }}</div>
                        </div>

                        <!-- MINI BARCODE SVG -->
                        <div class="bg-white rounded-lg p-2 flex flex-col items-center justify-center">
                            <div class="w-full flex justify-center overflow-hidden">
                                {!! $barcodeSvg !!}
                            </div>
                            <span class="text-[8px] font-mono font-bold tracking-widest text-slate-700 mt-0.5">{{ $pasien->rm }}</span>
                        </div>
                    </div>
                    
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between py-1.5 border-b border-slate-100">
                            <span class="text-slate-400">NIK (KTP):</span>
                            <span class="font-semibold text-slate-800 font-mono">{{ $pasien->nik }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-100">
                            <span class="text-slate-400">Jenis Kelamin:</span>
                            <span class="font-semibold text-slate-800">{{ ($pasien->jenis_kelamin ?? 'L') === 'P' ? 'Perempuan' : 'Laki-laki' }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-100">
                            <span class="text-slate-400">Tanggal Lahir / Usia:</span>
                            <span class="font-semibold text-slate-800">
                                {{ $pasien->tanggal_lahir ? \Carbon\Carbon::parse($pasien->tanggal_lahir)->translatedFormat('d M Y') : '-' }}
                                @if($pasien->tanggal_lahir)
                                    <span class="text-slate-500 font-normal">({{ \Carbon\Carbon::parse($pasien->tanggal_lahir)->age }} th)</span>
                                @endif
                            </span>
                        </div>
                        @if($pasien->no_hp)
                            <div class="flex justify-between py-1.5 border-b border-slate-100">
                                <span class="text-slate-400">No. WhatsApp / HP:</span>
                                <span class="font-semibold text-slate-800">{{ $pasien->no_hp }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between py-1.5">
                            <span class="text-slate-400">Alamat Domisili:</span>
                            <span class="font-semibold text-slate-800 text-right max-w-[200px] truncate" title="{{ $pasien->alamat }}">{{ $pasien->alamat ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- RIWAYAT KUNJUNGAN PASIEN -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-[#e2edfa]">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            <i class="fa-solid fa-clock-rotate-left text-[#1a4a8d]"></i> Riwayat Rekam Medis Pasien
                        </h3>
                        <a href="{{ route('pasien.riwayat') }}" class="text-[11px] font-semibold text-[#1a4a8d] hover:underline flex items-center gap-1">
                            <span>Lihat Semua</span>
                            <i class="fa-solid fa-chevron-right text-[9px]"></i>
                        </a>
                    </div>

                    <div class="space-y-3">
                        @forelse ($riwayatKunjungan->take(3) as $rw)
                            <a href="{{ route('pasien.riwayat.detail', $rw->id_pendaftaran) }}"
                               class="group block p-3.5 rounded-2xl bg-slate-50 hover:bg-blue-50/60 border border-slate-200/80 hover:border-blue-300 transition shadow-sm">
                                <div class="flex items-center justify-between">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-xs text-[#1a4a8d] group-hover:text-blue-700">{{ $rw->no_antrian }}</span>
                                            <span class="text-[10px] font-semibold text-slate-700">• {{ $rw->poli->nama_poli ?? '-' }}</span>
                                        </div>
                                        <span class="text-[11px] text-slate-400 block">
                                            {{ $rw->tanggal_daftar ? $rw->tanggal_daftar->format('d/m/Y') : '-' }} • {{ $rw->dokter->nama ?? 'Dokter' }}
                                        </span>
                                    </div>
                                    <div class="text-right space-y-1">
                                        @php
                                            $rst = $rw->status_antrian;
                                            $badgeBg = 'bg-emerald-100 text-emerald-800';
                                            $badgeLabel = 'Selesai';
                                            if ($rst === 'menunggu_skrining') {
                                                $badgeBg = 'bg-amber-100 text-amber-800';
                                                $badgeLabel = 'Skrining';
                                            } elseif (in_array($rst, ['menunggu_dokter', 'sedang_diperiksa'])) {
                                                $badgeBg = 'bg-blue-100 text-blue-800';
                                                $badgeLabel = 'Dokter';
                                            } elseif ($rst === 'menunggu_kasir') {
                                                $badgeBg = 'bg-purple-100 text-purple-800';
                                                $badgeLabel = 'Kasir';
                                            } elseif ($rst === 'menunggu_obat') {
                                                $badgeBg = 'bg-cyan-100 text-cyan-800';
                                                $badgeLabel = 'Farmasi';
                                            } elseif ($rst === 'dibatalkan') {
                                                $badgeBg = 'bg-rose-100 text-rose-800';
                                                $badgeLabel = 'Batal';
                                            }
                                        @endphp
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full inline-block {{ $badgeBg }}">
                                            {{ $badgeLabel }}
                                        </span>
                                        <div class="text-[10px] text-[#1a4a8d] font-semibold flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <span>Detail Medis</span>
                                            <i class="fa-solid fa-arrow-right text-[8px]"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-6 text-xs text-slate-400">
                                Belum ada riwayat kunjungan sebelumnya.
                            </div>
                        @endforelse
                    </div>

                    @if (($totalKunjungan ?? $riwayatKunjungan->count()) > 0)
                        <div class="mt-4 pt-3 border-t border-slate-100">
                            <a href="{{ route('pasien.riwayat') }}" class="w-full py-2.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-[#1a4a8d] font-bold text-xs flex items-center justify-center gap-2 transition">
                                <span>Lihat Seluruh Riwayat Medis ({{ $totalKunjungan ?? $riwayatKunjungan->count() }})</span>
                                <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>
                    @endif
                </div>

            </div>

        </div>

    </main>

    <!-- FOOTER SIMPLE -->
    <footer class="py-5 text-center text-xs text-slate-400 border-t border-slate-200/60 bg-white">
        © 2026 Rumah Sakit Delta • Sistem Informasi Manajemen Rumah Sakit Terpadu
    </footer>

    <!-- SCRIPT REALTIME NOTIFIKASI PORTAL PASIEN MANDIRI -->
    <script>
        (function() {
            let lastSpeechText = null;
            let audioPlayer = null;

            async function checkNotifikasiPasien() {
                try {
                    const res = await fetch('{{ route("pasien.notifikasi") }}', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!res.ok) return;

                    const data = await res.json();
                    if (!data.success || !data.ada_antrean) return;

                    // 1. Update Teks Status & Sisa Antrean
                    const statusTextEl = document.getElementById('pasien-live-status-text');
                    if (statusTextEl && data.pesan_notifikasi) {
                        statusTextEl.textContent = data.pesan_notifikasi;
                    }

                    // 2. Deteksi Panggilan Antrean oleh Resepsionis
                    const callBanner = document.getElementById('pasien-call-banner');
                    const bannerText = document.getElementById('call-banner-text');
                    const bannerDest = document.getElementById('call-banner-dest');

                    if (data.sedang_dipanggil) {
                        if (callBanner) callBanner.classList.remove('hidden');
                        if (bannerText) bannerText.textContent = `NOMOR ${data.no_antrian} SEDANG DIPANGGIL!`;
                        if (bannerDest) bannerDest.textContent = `Silakan menuju ke ${data.tujuan_panggilan}.`;

                        // Putar audio jika ada panggilan baru yang belum diputar
                        if (data.suara_teks && data.suara_teks !== lastSpeechText) {
                            lastSpeechText = data.suara_teks;
                            putarAudio(data.suara_teks);
                        }
                    } else {
                        if (callBanner) callBanner.classList.add('hidden');
                    }
                } catch (e) {
                    console.warn('[SIMRS] Gagal polling antrean pasien:', e);
                }
            }

            function putarAudio(teks) {
                try {
                    const audioUrl = `/monitoring/suara?q=${encodeURIComponent(teks)}`;
                    if (!audioPlayer) {
                        audioPlayer = new Audio();
                    }
                    audioPlayer.src = audioUrl;
                    audioPlayer.play().catch(() => {});
                } catch (err) {}
            }

            window.dengarUlangPanggilan = function() {
                if (lastSpeechText) {
                    putarAudio(lastSpeechText);
                }
            };

            // Filter Dokter berdasarkan Poliklinik yang Dipilih
            const pSelectPoli = document.getElementById('pasien-select-poli');
            const pSelectDokter = document.getElementById('pasien-select-dokter');

            function filterDokterPasien() {
                if (!pSelectPoli || !pSelectDokter) return;
                const chosenPoli = pSelectPoli.value;
                const options = pSelectDokter.querySelectorAll('option');

                options.forEach(opt => {
                    if (!opt.value) {
                        opt.style.display = '';
                        return;
                    }
                    const docPoli = opt.getAttribute('data-poli');
                    if (!chosenPoli || !docPoli || docPoli === chosenPoli) {
                        opt.style.display = '';
                        opt.disabled = false;
                    } else {
                        opt.style.display = 'none';
                        opt.disabled = true;
                        if (pSelectDokter.value === opt.value) {
                            pSelectDokter.value = '';
                        }
                    }
                });
            }

            if (pSelectPoli) {
                pSelectPoli.addEventListener('change', filterDokterPasien);
                filterDokterPasien();
            }

            // Polling setiap 4 detik
            checkNotifikasiPasien();
            setInterval(checkNotifikasiPasien, 4000);
        })();
    </script>

</body>
</html>
