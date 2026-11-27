<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Antrean Ruang Tunggu - Rumah Sakit Delta</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logodelta1.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800;900&family=Roboto+Mono:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: #090e17;
            color: #ffffff;
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            user-select: none;
        }

        /* HEADER TV */
        .tv-header {
            background: linear-gradient(90deg, #0b1528, #112345, #0b1528);
            border-bottom: 2px solid #1d4ed8;
            padding: 14px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.5);
            flex-shrink: 0;
        }
        .rs-brand {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .rs-logo-box {
            display: flex;
            align-items: center;
            justify-content: center;
            animation: deltaLogoHeartbeat 4s ease-in-out infinite;
            will-change: transform, filter;
        }
        .rs-logo-img {
            width: 46px;
            height: 46px;
            object-fit: contain;
            display: block;
        }
        @keyframes deltaLogoHeartbeat {
            0%, 100% {
                transform: translateY(0px) scale(1);
                filter: drop-shadow(0 2px 6px rgba(255, 255, 255, 0.25));
            }
            30% {
                transform: translateY(-2px) scale(1.04);
                filter: drop-shadow(0 4px 14px rgba(255, 255, 255, 0.65));
            }
            45% {
                transform: translateY(-1px) scale(1.01);
                filter: drop-shadow(0 3px 8px rgba(255, 255, 255, 0.35));
            }
            60% {
                transform: translateY(-2.5px) scale(1.05);
                filter: drop-shadow(0 5px 16px rgba(255, 255, 255, 0.7));
            }
            80% {
                transform: translateY(-1px) scale(1.01);
                filter: drop-shadow(0 2px 6px rgba(255, 255, 255, 0.3));
            }
        }
        .rs-title {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: 0.5px;
            line-height: 1.1;
        }
        .rs-tagline {
            font-size: 12px;
            color: #93c5fd;
            font-weight: 500;
        }
        .header-meta {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .clock-display {
            text-align: right;
        }
        .clock-time {
            font-size: 26px;
            font-weight: 800;
            font-family: 'Roboto Mono', monospace;
            color: #38bdf8;
            line-height: 1;
        }
        .clock-date {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 3px;
        }
        .btn-fullscreen {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 13px;
            cursor: pointer;
            transition: 0.2s;
        }
        .btn-fullscreen:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        /* MAIN CONTENT GRID */
        .tv-body {
            flex: 1;
            padding: 20px 28px;
            display: grid;
            grid-template-columns: 1.1fr 1.9fr;
            gap: 24px;
            overflow: hidden;
        }

        /* LEFT HERO: NOMOR SEDANG DIPANGGIL */
        .hero-call-card {
            background: linear-gradient(145deg, #0f1d38, #0a1426);
            border: 2px solid #2563eb;
            border-radius: 24px;
            padding: 28px 24px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            box-shadow: 0 10px 40px rgba(37, 99, 235, 0.25);
            position: relative;
            overflow: hidden;
        }
        .hero-call-card::before {
            content: '';
            position: absolute;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.25), transparent 70%);
            top: -50px;
            right: -50px;
            pointer-events: none;
        }
        .call-badge-live {
            background: #ef4444;
            color: #ffffff;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 0 15px rgba(239, 68, 68, 0.6);
            margin-bottom: 14px;
            animation: pulseLive 1.5s infinite;
        }
        @keyframes pulseLive {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.85; transform: scale(1.05); }
        }
        .call-title {
            font-size: 15px;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .call-number {
            font-family: 'Roboto Mono', monospace;
            font-size: 76px;
            font-weight: 900;
            color: #60a5fa;
            letter-spacing: 2px;
            margin: 8px 0;
            text-shadow: 0 0 30px rgba(96, 165, 250, 0.5);
            line-height: 1.1;
        }
        .call-patient {
            font-size: 24px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 12px;
            max-width: 90%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .call-destination {
            background: #1e3a8a;
            color: #ffffff;
            border: 1px solid #3b82f6;
            padding: 10px 24px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(30, 58, 138, 0.4);
        }

        /* RIGHT: 4 SECTOR QUEUE COLUMNS */
        .sectors-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-template-rows: repeat(2, 1fr);
            gap: 16px;
        }
        .sector-box {
            background: #0f172a;
            border: 1px solid #1e293b;
            border-radius: 18px;
            padding: 16px 18px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
        .sector-box-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #1e293b;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .sector-title {
            font-size: 14px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .sector-badge-count {
            font-size: 11px;
            font-weight: 700;
            background: #1e293b;
            color: #94a3b8;
            padding: 2px 8px;
            border-radius: 6px;
        }
        .queue-items-list {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 8px;
            overflow: hidden;
        }
        .queue-item-row {
            background: #1e293b;
            border-radius: 10px;
            padding: 8px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .queue-num {
            font-family: 'Roboto Mono', monospace;
            font-size: 15px;
            font-weight: 800;
            color: #38bdf8;
        }
        .queue-name {
            font-size: 13px;
            font-weight: 600;
            color: #ffffff;
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .queue-room {
            font-size: 11px;
            color: #94a3b8;
        }

        /* RUNNING TICKER FOOTER */
        .tv-ticker {
            background: #000000;
            border-top: 1px solid #1e293b;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            flex-shrink: 0;
            overflow: hidden;
        }
        .ticker-badge {
            background: #2563eb;
            color: #ffffff;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            padding: 4px 10px;
            border-radius: 6px;
            white-space: nowrap;
        }
        .ticker-content {
            flex: 1;
            overflow: hidden;
            white-space: nowrap;
        }
        .ticker-text {
            display: inline-block;
            font-size: 13px;
            color: #e2e8f0;
            animation: marquee 30s linear infinite;
        }
        @keyframes marquee {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
    </style>
</head>
<body>

    <!-- HEADER TV MONITOR -->
    <header class="tv-header">
        <div class="rs-brand">
            <div class="rs-logo-box">
                <img src="{{ asset('images/logodelta1.svg') }}" alt="Logo Delta" class="rs-logo-img">
            </div>
            <div>
                <div class="rs-title">RUMAH SAKIT DELTA</div>
                <div class="rs-tagline">Dedikasi Untuk Kesembuhan • Sistem Antrean Terpadu</div>
            </div>
        </div>

        <div class="header-meta">
            <div class="clock-display">
                <div class="clock-time" id="liveClock">00:00:00</div>
                <div class="clock-date" id="liveDate">Kamis, 26 November 2026</div>
            </div>
            <button class="btn-fullscreen" onclick="toggleFullScreen()" title="Layar Penuh (F11)">
                <i class="fa-solid fa-expand"></i>
            </button>
        </div>
    </header>

    <!-- BODY TV MONITOR -->
    <main class="tv-body">

        <!-- KOLOM KIRI: HERO NOMOR ANTREAN SEDANG DIPANGGIL -->
        <div class="hero-call-card" id="heroCallCard">
            <div class="call-badge-live">
                <i class="fa-solid fa-bullhorn"></i> SEDANG DIPANGGIL
            </div>
            <div class="call-title">Nomor Antrean Pasien</div>
            <div class="call-number" id="displayHeroNumber">
                {{ $panggilanTerbaru['no_antrian'] ?? '---' }}
            </div>
            <div class="call-patient" id="displayHeroPatient">
                {{ $panggilanTerbaru['nama_pasien'] ?? 'Menunggu Panggilan...' }}
            </div>
            <div class="call-destination" id="displayHeroDest">
                <i class="fa-solid fa-location-dot"></i>
                <span id="displayHeroDestText">{{ $panggilanTerbaru['tujuan'] ?? 'Poli & Loket RS Delta' }}</span>
            </div>
        </div>

        <!-- KOLOM KANAN: 4 GRID SEKTOR ANTREAN RS -->
        <div class="sectors-grid">

            <!-- 1. ADMISI & SKRINING TTV -->
            <div class="sector-box" style="border-top: 3px solid #f59e0b;">
                <div class="sector-box-header">
                    <div class="sector-title" style="color: #fbbf24;">
                        <i class="fa-solid fa-heart-pulse"></i> 1. Skrining TTV Perawat
                    </div>
                    <span class="sector-badge-count">{{ count($posSkrining) }} Pasien</span>
                </div>
                <div class="queue-items-list">
                    @forelse($posSkrining as $item)
                        <div class="queue-item-row">
                            <span class="queue-num">{{ $item->no_antrian }}</span>
                            <span class="queue-name">{{ $item->pasien?->nama_pasien ?? 'Pasien' }}</span>
                            <span class="queue-room">{{ $item->poli?->nama_poli }}</span>
                        </div>
                    @empty
                        <div style="color: #64748b; font-size: 12px; text-align: center; margin-top: 20px;">
                            Tidak ada antrean skrining saat ini
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- 2. POLIKLINIK DOKTER -->
            <div class="sector-box" style="border-top: 3px solid #3b82f6;">
                <div class="sector-box-header">
                    <div class="sector-title" style="color: #60a5fa;">
                        <i class="fa-solid fa-user-doctor"></i> 2. Poliklinik Dokter
                    </div>
                    <span class="sector-badge-count">{{ count($posDokter) }} Pasien</span>
                </div>
                <div class="queue-items-list">
                    @forelse($posDokter as $item)
                        <div class="queue-item-row">
                            <span class="queue-num">{{ $item->no_antrian }}</span>
                            <span class="queue-name">{{ $item->pasien?->nama_pasien ?? 'Pasien' }}</span>
                            <span class="queue-room">{{ $item->poli?->nama_poli }}</span>
                        </div>
                    @empty
                        <div style="color: #64748b; font-size: 12px; text-align: center; margin-top: 20px;">
                            Semua pemeriksaan dokter tuntas
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- 3. KASIR PEMBAYARAN -->
            <div class="sector-box" style="border-top: 3px solid #ea580c;">
                <div class="sector-box-header">
                    <div class="sector-title" style="color: #fb923c;">
                        <i class="fa-solid fa-cash-register"></i> 3. Loket Kasir Pembayaran
                    </div>
                    <span class="sector-badge-count">{{ count($posKasir) }} Pasien</span>
                </div>
                <div class="queue-items-list">
                    @forelse($posKasir as $item)
                        <div class="queue-item-row">
                            <span class="queue-num">{{ $item->no_antrian }}</span>
                            <span class="queue-name">{{ $item->pasien?->nama_pasien ?? 'Pasien' }}</span>
                            <span class="queue-room">Kasir Utama</span>
                        </div>
                    @empty
                        <div style="color: #64748b; font-size: 12px; text-align: center; margin-top: 20px;">
                            Tidak ada antrean menunggu kasir
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- 4. PENGAMBILAN OBAT FARMASI -->
            <div class="sector-box" style="border-top: 3px solid #10b981;">
                <div class="sector-box-header">
                    <div class="sector-title" style="color: #34d399;">
                        <i class="fa-solid fa-prescription-bottle-medical"></i> 4. Pengambilan Obat Farmasi
                    </div>
                    <span class="sector-badge-count">{{ count($posFarmasi) }} Pasien</span>
                </div>
                <div class="queue-items-list">
                    @forelse($posFarmasi as $item)
                        <div class="queue-item-row">
                            <span class="queue-num">{{ $item->no_antrian }}</span>
                            <span class="queue-name">{{ $item->pasien?->nama_pasien ?? 'Pasien' }}</span>
                            <span class="queue-room">Apotek RS</span>
                        </div>
                    @empty
                        <div style="color: #64748b; font-size: 12px; text-align: center; margin-top: 20px;">
                            Tidak ada antrean resep obat
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </main>

    <!-- FOOTER RUNNING TICKER -->
    <footer class="tv-ticker">
        <span class="ticker-badge">INFO RS DELTA</span>
        <div class="ticker-content">
            <span class="ticker-text">
                Selamat Datang di Rumah Sakit Delta Banjarmasin • Harap menjaga ketertiban dan mendengarkan panggilan nomor antrean Anda • Tersedia layanan Pendaftaran Mandiri Online melalui portal pasien • Demi kenyamanan bersama, mohon tidak merokok di area rumah sakit.
            </span>
        </div>
    </footer>

    <!-- REALTIME SCRIPT: JAM, POLLING PANGGILAN & AUDIO SUARA -->
    <script>
        // Update Jam Digital Realtime
        function updateLiveClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour12: false });
            const dateStr = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            
            document.getElementById('liveClock').textContent = timeStr;
            document.getElementById('liveDate').textContent = dateStr;
        }
        setInterval(updateLiveClock, 1000);
        updateLiveClock();

        // Toggle Fullscreen Layar TV
        function toggleFullScreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {
                    console.error('Fullscreen error:', err);
                });
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }

        // Chime Suara Ding-Dong RS
        function playChime() {
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return Promise.resolve();
                const ctx = new AudioCtx();
                const now = ctx.currentTime;

                const notes = [523.25, 659.25, 783.99]; // C5, E5, G5
                notes.forEach((freq, i) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, now + (i * 0.22));
                    gain.gain.setValueAtTime(0.3, now + (i * 0.22));
                    gain.gain.exponentialRampToValueAtTime(0.001, now + (i * 0.22) + 0.6);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(now + (i * 0.22));
                    osc.stop(now + (i * 0.22) + 0.6);
                });

                return new Promise(resolve => setTimeout(resolve, 900));
            } catch (e) {
                return Promise.resolve();
            }
        }

        // Pemutar Audio Panggilan Suara Jernih Bahasa Indonesia
        let currentAudio = null;
        function speakAnnouncement(text) {
            playChime().then(() => {
                const audioUrl = "{{ route('monitoring.suara') }}?q=" + encodeURIComponent(text);

                if (currentAudio) {
                    currentAudio.pause();
                    currentAudio.currentTime = 0;
                }

                currentAudio = new Audio(audioUrl);
                currentAudio.play().catch(err => {
                    console.warn('Audio streaming offline, beralih ke Web Speech API:', err);
                    fallbackWebSpeech(text);
                });
            });
        }

        // Cadangan Suara via Web Speech API jika offline
        function fallbackWebSpeech(text) {
            if (!('speechSynthesis' in window)) return;
            window.speechSynthesis.cancel();

            const utter = new SpeechSynthesisUtterance(text);
            utter.lang = 'id-ID';
            utter.rate = 0.88;
            utter.pitch = 1.0;

            const voices = window.speechSynthesis.getVoices();
            const idVoice = voices.find(v => 
                v.lang === 'id-ID' || 
                v.lang.toLowerCase().startsWith('id') || 
                v.name.toLowerCase().includes('indonesia') ||
                v.name.toLowerCase().includes('gadis') ||
                v.name.toLowerCase().includes('ardi')
            );
            if (idVoice) utter.voice = idVoice;

            window.speechSynthesis.speak(utter);
        }

        if ('speechSynthesis' in window) {
            window.speechSynthesis.onvoiceschanged = () => {
                window.speechSynthesis.getVoices();
            };
        }

        // Polling Panggilan Antrean Baru dari Resepsionis
        let lastCallId = "{{ $panggilanTerbaru['id_panggilan'] ?? '' }}";
        const urlParams = new URLSearchParams(window.location.search);
        const isPreview = urlParams.get('preview') === '1';
        let isMuted = isPreview;

        window.addEventListener('message', (event) => {
            if (event.data && event.data.type === 'SET_MUTE') {
                isMuted = !!event.data.muted;
            }
        });

        function checkNewCall() {
            fetch("{{ route('monitoring.status-panggilan') }}")
                .then(res => res.json())
                .then(data => {
                    if (data.has_call && data.panggilan) {
                        const call = data.panggilan;
                        if (call.id_panggilan && call.id_panggilan !== lastCallId) {
                            lastCallId = call.id_panggilan;

                            // Update tampilan Hero Box
                            document.getElementById('displayHeroNumber').textContent = call.no_antrian;
                            document.getElementById('displayHeroPatient').textContent = call.nama_pasien;
                            document.getElementById('displayHeroDestText').textContent = call.tujuan;

                            // Animasi flash
                            const hero = document.getElementById('heroCallCard');
                            hero.style.transform = 'scale(1.02)';
                            hero.style.borderColor = '#10b981';
                            setTimeout(() => {
                                hero.style.transform = 'scale(1)';
                                hero.style.borderColor = '#2563eb';
                            }, 3000);

                            // Mainkan audio suara (hanya jika tidak dimute)
                            if (!isMuted) {
                                speakAnnouncement(call.suara_teks);
                            }
                        }
                    }
                })
                .catch(err => console.error('Polling error:', err));
        }

        // Jalankan polling setiap 3 detik
        setInterval(checkNewCall, 3000);

        // Auto reload seluruh halaman tiap 60 detik untuk memperbarui daftar antrean
        setInterval(() => {
            location.reload();
        }, 60000);
    </script>
</body>
</html>
