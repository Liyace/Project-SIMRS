@extends('layouts.app')
@php $halamanAktif = 'resepsionis_monitoring'; @endphp
@section('title', 'Monitoring Antrean & Informasi Pasien - Rumah Sakit Delta')

@push('styles')
<style>
    .stat-grid-journey {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 14px;
        margin-bottom: 24px;
    }
    .stat-card-journey {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px 18px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
    }
    .stat-card-journey .icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    .stat-card-journey .num {
        font-size: 20px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.2;
    }
    .stat-card-journey .label {
        font-size: 11.5px;
        color: #64748b;
        margin-top: 2px;
    }
    .content-box {
        background: #ffffff;
        border-radius: 20px;
        padding: 24px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        margin-bottom: 24px;
    }
    .caller-box {
        background: linear-gradient(135deg, #1e40af, #1e3a8a);
        color: #ffffff;
        border-radius: 20px;
        padding: 22px 26px;
        margin-bottom: 24px;
        box-shadow: 0 10px 25px rgba(30, 64, 175, 0.25);
    }
    .caller-grid {
        display: grid;
        grid-template-columns: 1fr 1fr auto;
        gap: 14px;
        align-items: end;
        margin-top: 14px;
    }
    @media (max-width: 900px) {
        .caller-grid {
            grid-template-columns: 1fr;
        }
    }
    .caller-input {
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: #ffffff;
        padding: 10px 14px;
        border-radius: 10px;
        width: 100%;
        font-size: 13px;
        outline: none;
    }
    .caller-input::placeholder {
        color: rgba(255, 255, 255, 0.7);
    }
    .caller-input option {
        background: #1e3a8a;
        color: #ffffff;
    }
    .btn-call-sound {
        background: #10b981;
        color: #ffffff;
        border: none;
        padding: 11px 22px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 13px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.4);
        transition: all 0.2s;
        white-space: nowrap;
    }
    .btn-call-sound:hover {
        background: #059669;
        transform: translateY(-1px);
    }
    .filter-tabs {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }
    .tab-btn {
        padding: 8px 16px;
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none;
        color: #64748b;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .tab-btn:hover {
        background: #f1f5f9;
        color: #1e293b;
    }
    .tab-btn.active {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    }

    /* Stepper mini di dalam baris tabel */
    .mini-stepper {
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .step-dot {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 700;
        background: #e2e8f0;
        color: #64748b;
        position: relative;
    }
    .step-dot.done {
        background: #dcfce7;
        color: #15803d;
    }
    .step-dot.active {
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        animation: pulseDot 2s infinite;
    }
    .step-line {
        width: 12px;
        height: 2px;
        background: #e2e8f0;
    }
    .step-line.done {
        background: #86efac;
    }
    @keyframes pulseDot {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.12); }
    }

    /* Badges Journey */
    .badge-journey {
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .badge-skrining { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
    .badge-dokter { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
    .badge-dokter-active { background: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd; }
    .badge-kasir { background: #ffedd5; color: #c2410c; border: 1px solid #fed7aa; }
    .badge-farmasi { background: #f3e8ff; color: #7e22ce; border: 1px solid #e9d5ff; }
    .badge-selesai { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
    .badge-batal { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }

    .table-journey {
        width: 100%;
        border-collapse: collapse;
        font-size: 12.5px;
    }
    .table-journey th {
        background: #f8fafc;
        color: #475569;
        font-weight: 600;
        padding: 12px 14px;
        text-align: left;
        border-bottom: 1px solid #e2e8f0;
        font-size: 11.5px;
    }
    .table-journey td {
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9;
        color: #1e293b;
    }
    .table-journey tr:hover td {
        background: #f8fbfe;
    }

    /* Dark Mode Overrides */
    body.dark-mode .stat-card-journey {
        background: #121927 !important;
        border-color: #1e293b !important;
    }
    body.dark-mode .stat-card-journey .num {
        color: #f8fafc !important;
    }
    body.dark-mode .table-journey th {
        background: #0b0f19 !important;
        color: #94a3b8 !important;
        border-bottom-color: #1e293b !important;
    }
    body.dark-mode .table-journey td {
        border-bottom-color: #1e293b !important;
        color: #f1f5f9 !important;
    }
    body.dark-mode .table-journey tr:hover td {
        background: #0b0f19 !important;
    }
    body.dark-mode .tab-btn {
        background: #0b0f19 !important;
        border-color: #1e293b !important;
        color: #94a3b8 !important;
    }
    body.dark-mode .tab-btn.active {
        background: #2563eb !important;
        color: #ffffff !important;
    }
</style>
@endpush

@section('content')
<div class="content-body" style="padding-bottom: 40px;">

    <!-- HEADER TITLE & LIVE ACTIONS -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 14px;">
        <div>
            <h1 style="font-size: 22px; font-weight: 700; color: #1e293b; margin: 0;">Monitoring Antrean & Informasi Pasien</h1>
            <p style="font-size: 13px; color: #64748b; margin: 4px 0 0;">
                Pelacakan alur perjalanan pasien dari pendaftaran hingga selesai, dan kontrol panggilan antrean suara RS Delta.
            </p>
        </div>

        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <!-- TOMBOL BUKA DISPLAY TV FULLSCREEN -->
            <a href="{{ route('monitoring.display') }}" target="_blank"
               style="background: #0284c7; color: white; padding: 9px 16px; border-radius: 10px; font-size: 12.5px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);">
                <i class="fa-solid fa-tv"></i> Buka Display TV Antrean
            </a>

            <button type="button" onclick="location.reload();" class="simrs-btn-white" style="padding: 9px 14px; font-size: 12px; border-radius: 10px;" title="Refresh Data">
                <i class="fa-solid fa-rotate-right"></i>
            </button>
        </div>
    </div>

    <!-- PANEL PANGGIL ANTREAN SUARA (VOICE CALLER) -->
    <div class="caller-box" id="caller">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <div>
                <strong style="font-size: 16px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-bullhorn" style="color: #60a5fa;"></i> Panel Pemanggil Suara Antrean (Audio Chime RS)
                </strong>
                <span style="font-size: 12px; color: rgba(255, 255, 255, 0.8);">
                    Panggil pasien ke loket atau ruangan periksa dengan suara audio wanita berbahasa Indonesia.
                </span>
            </div>
            <span style="font-size: 11px; background: rgba(255,255,255,0.2); padding: 4px 10px; border-radius: 20px;">
                <i class="fa-solid fa-volume-high"></i> Web Audio TTS Aktif
            </span>
        </div>

        <form id="formPanggilAntrean" onsubmit="event.preventDefault(); jalankanPanggilan();" class="caller-grid">
            <div>
                <label style="display: block; font-size: 11.5px; font-weight: 600; margin-bottom: 5px; color: rgba(255,255,255,0.9);">
                    Pilih Nomor Antrean / Pasien *
                </label>
                <select id="pilihPasienCall" class="caller-input" required onchange="onSelectPasienCall(this)">
                    <option value="">-- Pilih Pasien yang Ingin Dipanggil --</option>
                    @foreach($pasienPanggilan as $p)
                        @if($p->status_antrian !== 'selesai' && $p->status_antrian !== 'dibatalkan')
                            <option value="{{ $p->no_antrian }}" data-nama="{{ $p->nama_pasien }}" data-poli="{{ $p->nama_poli }}" data-status="{{ $p->posisi_aktif }}">
                                [{{ $p->no_antrian }}] {{ $p->nama_pasien }} - {{ $p->posisi_aktif }}
                            </option>
                        @endif
                    @endforeach
                </select>
                <input type="hidden" id="callNamaPasien" value="">
            </div>

            <div>
                <label style="display: block; font-size: 11.5px; font-weight: 600; margin-bottom: 5px; color: rgba(255,255,255,0.9);">
                    Tujuan Panggilan (Loket / Ruangan) *
                </label>
                <select id="callTujuan" class="caller-input" required>
                    <option value="Loket Pendaftaran">Loket Pendaftaran 1</option>
                    <option value="Ruang Skrining Perawat">Ruang Skrining TTV Perawat</option>
                    <option value="Poli Umum">Poliklinik Umum</option>
                    <option value="Poli Gigi dan Mulut">Poliklinik Gigi dan Mulut</option>
                    <option value="Poli Anak">Poliklinik Anak</option>
                    <option value="Loket Kasir Pembayaran">Loket Kasir Pembayaran</option>
                    <option value="Loket Farmasi dan Pengambilan Obat">Loket Farmasi & Pengambilan Obat</option>
                </select>
            </div>

            <div>
                <button type="submit" class="btn-call-sound" id="btnPanggilSuara">
                    <i class="fa-solid fa-volume-high"></i> Panggil Antrean
                </button>
            </div>
        </form>

        <div id="statusPanggilanInfo" style="margin-top: 10px; font-size: 11.5px; color: #a7f3d0; display: none;">
            <i class="fa-solid fa-circle-check"></i> <span id="statusPanggilanText">-</span>
        </div>
    </div>

    <!-- 6 STAT CARDS TAHAPAN JOURNEY -->
    <div class="stat-grid-journey">
        <div class="stat-card-journey">
            <div class="icon" style="background: #eff6ff; color: #2563eb;">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <div class="num">{{ $totalTerdaftar }}</div>
                <div class="label">Total Terdaftar</div>
            </div>
        </div>

        <div class="stat-card-journey">
            <div class="icon" style="background: #fef3c7; color: #b45309;">
                <i class="fa-solid fa-heart-pulse"></i>
            </div>
            <div>
                <div class="num">{{ $totalSkrining }}</div>
                <div class="label">Di Skrining TTV</div>
            </div>
        </div>

        <div class="stat-card-journey">
            <div class="icon" style="background: #e0f2fe; color: #0284c7;">
                <i class="fa-solid fa-user-doctor"></i>
            </div>
            <div>
                <div class="num">{{ $totalDokter }}</div>
                <div class="label">Di Dokter Poli</div>
            </div>
        </div>

        <div class="stat-card-journey">
            <div class="icon" style="background: #ffedd5; color: #ea580c;">
                <i class="fa-solid fa-cash-register"></i>
            </div>
            <div>
                <div class="num">{{ $totalKasir }}</div>
                <div class="label">Di Loket Kasir</div>
            </div>
        </div>

        <div class="stat-card-journey">
            <div class="icon" style="background: #f3e8ff; color: #9333ea;">
                <i class="fa-solid fa-prescription-bottle-medical"></i>
            </div>
            <div>
                <div class="num">{{ $totalFarmasi }}</div>
                <div class="label">Di Farmasi Obat</div>
            </div>
        </div>

        <div class="stat-card-journey">
            <div class="icon" style="background: #dcfce7; color: #15803d;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <div class="num">{{ $totalSelesai }}</div>
                <div class="label">Tuntas Selesai</div>
            </div>
        </div>
    </div>

    <!-- FILTER BAR TAHAPAN & PENCARIAN -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
        <div class="filter-tabs" style="margin-bottom: 0;">
            <a href="{{ route('resepsionis.index', ['tahap' => 'semua', 'poli' => $idPoli, 'q' => $q]) }}" class="tab-btn {{ $tahap === 'semua' ? 'active' : '' }}">
                Semua Pasien ({{ $totalTerdaftar }})
            </a>
            <a href="{{ route('resepsionis.index', ['tahap' => 'skrining', 'poli' => $idPoli, 'q' => $q]) }}" class="tab-btn {{ $tahap === 'skrining' ? 'active' : '' }}">
                Skrining ({{ $totalSkrining }})
            </a>
            <a href="{{ route('resepsionis.index', ['tahap' => 'dokter', 'poli' => $idPoli, 'q' => $q]) }}" class="tab-btn {{ $tahap === 'dokter' ? 'active' : '' }}">
                Dokter ({{ $totalDokter }})
            </a>
            <a href="{{ route('resepsionis.index', ['tahap' => 'kasir', 'poli' => $idPoli, 'q' => $q]) }}" class="tab-btn {{ $tahap === 'kasir' ? 'active' : '' }}">
                Kasir ({{ $totalKasir }})
            </a>
            <a href="{{ route('resepsionis.index', ['tahap' => 'farmasi', 'poli' => $idPoli, 'q' => $q]) }}" class="tab-btn {{ $tahap === 'farmasi' ? 'active' : '' }}">
                Farmasi ({{ $totalFarmasi }})
            </a>
            <a href="{{ route('resepsionis.index', ['tahap' => 'selesai', 'poli' => $idPoli, 'q' => $q]) }}" class="tab-btn {{ $tahap === 'selesai' ? 'active' : '' }}">
                Selesai ({{ $totalSelesai }})
            </a>
        </div>

        <!-- SEARCH BOX & FILTER POLI -->
        <form action="{{ route('resepsionis.index') }}" method="GET" style="display: flex; gap: 8px;">
            <input type="hidden" name="tahap" value="{{ $tahap }}">
            
            <select name="poli" onchange="this.form.submit()" style="padding: 8px 12px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 12px; background: #fff;">
                <option value="semua">-- Semua Poliklinik --</option>
                @foreach($daftarPoli as $pl)
                    <option value="{{ $pl->id_poli }}" {{ $idPoli == $pl->id_poli ? 'selected' : '' }}>{{ $pl->nama_poli }}</option>
                @endforeach
            </select>

            <div style="position: relative;">
                <input type="text" name="q" value="{{ $q }}" placeholder="Cari Antrean / RM / Pasien..."
                       style="padding: 8px 12px 8px 32px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 12.5px; width: 220px;">
                <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 11px; top: 11px; color: #94a3b8; font-size: 12px;"></i>
            </div>
        </form>
    </div>

    <!-- TABEL PATIENT JOURNEY TRACKER -->
    <div class="content-box">
        <div style="overflow-x: auto;">
            <table class="table-journey">
                <thead>
                    <tr>
                        <th style="width: 11%;">NO. ANTREAN</th>
                        <th style="width: 20%;">PASIEN & REKAM MEDIS</th>
                        <th style="width: 17%;">POLIKLINIK & DOKTER</th>
                        <th style="width: 25%;">PROGRESS TAHAPAN PELAYANAN</th>
                        <th style="width: 17%;">POSISI / STATUS SAAT INI</th>
                        <th style="width: 10%; text-align: center;">AKSI PANGGIL</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pasienJourney as $p)
                        <tr>
                            <td>
                                <span class="badge-queue" style="font-size: 13px; font-weight: 800;">
                                    {{ $p->no_antrian }}
                                </span>
                                <div style="font-size: 10.5px; color: #64748b; font-family: monospace; margin-top: 3px;">
                                    {{ $p->waktu_daftar }} WITA
                                </div>
                            </td>
                            <td>
                                <strong style="font-size: 13.5px;">{{ $p->nama_pasien }}</strong>
                                <div style="font-size: 11px; color: #64748b; margin-top: 1px;">
                                    {{ $p->rm }} • {{ $p->jenis_kelamin === 'P' ? 'Perempuan' : 'Laki-laki' }} • <span style="font-weight: 600; color: #2563eb;">{{ $p->cara_bayar }}</span>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #0f172a;">{{ $p->nama_poli }}</div>
                                <div style="font-size: 11px; color: #64748b;">{{ $p->nama_dokter }}</div>
                            </td>
                            <td>
                                <!-- MINI STEPPER 5 TAHAPAN -->
                                <div class="mini-stepper" title="Tahap: {{ $p->step_label }}">
                                    <!-- Step 1: Admisi -->
                                    <div class="step-dot {{ $p->step_index >= 1 ? ($p->step_index == 1 ? 'active' : 'done') : '' }}" title="1. Loket Admisi">1</div>
                                    <div class="step-line {{ $p->step_index > 1 ? 'done' : '' }}"></div>

                                    <!-- Step 2: TTV Perawat -->
                                    <div class="step-dot {{ $p->step_index >= 2 ? ($p->step_index == 2 ? 'active' : 'done') : '' }}" title="2. Skrining TTV Perawat">2</div>
                                    <div class="step-line {{ $p->step_index > 2 ? 'done' : '' }}"></div>

                                    <!-- Step 3: Dokter -->
                                    <div class="step-dot {{ $p->step_index >= 3 ? ($p->step_index == 3 ? 'active' : 'done') : '' }}" title="3. Pemeriksaan Dokter">3</div>
                                    <div class="step-line {{ $p->step_index > 3 ? 'done' : '' }}"></div>

                                    <!-- Step 4: Kasir -->
                                    <div class="step-dot {{ $p->step_index >= 4 ? ($p->step_index == 4 ? 'active' : 'done') : '' }}" title="4. Pembayaran Kasir">4</div>
                                    <div class="step-line {{ $p->step_index > 4 ? 'done' : '' }}"></div>

                                    <!-- Step 5: Farmasi / Selesai -->
                                    <div class="step-dot {{ $p->step_index >= 5 ? ($p->step_index == 5 ? 'active' : 'done') : '' }}" title="5. Pengambilan Obat Farmasi">5</div>
                                </div>
                                <div style="font-size: 10.5px; color: #64748b; margin-top: 4px;">
                                    Tahap {{ $p->step_index }}/5: <strong>{{ $p->step_label }}</strong>
                                </div>
                            </td>
                            <td>
                                <span class="badge-journey {{ $p->badge_class }}">
                                    <i class="fa-solid {{ $p->icon_class }}"></i> {{ $p->badge_text }}
                                </span>
                                <div style="font-size: 11px; color: #475569; margin-top: 3px; font-weight: 500;">
                                    {{ $p->posisi_aktif }}
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <button type="button" onclick="panggilPasienCepat('{{ $p->no_antrian }}', '{{ addslashes($p->nama_pasien) }}', '{{ addslashes($p->posisi_aktif) }}')"
                                        style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 5px;"
                                        title="Panggil Nomor Antrean Pasien Ini">
                                    <i class="fa-solid fa-volume-high"></i> Panggil
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px 14px; color: #64748b;">
                                Tidak ada antrean pasien yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINASI PER 10 PASIEN -->
        <div style="margin-top: 18px;">
            {{ $pasienJourney->links('partials.pagination') }}
        </div>
    </div>

</div>

<!-- AUDIO CHIME RS & SPEECH SYNTHESIS JAVASCRIPT -->
<script>
    // Audio Chime Ding-Dong RS Delta via Web Audio API (Synthesized Bell Chime)
    function playHospitalChime() {
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) return Promise.resolve();

            const ctx = new AudioContext();
            const now = ctx.currentTime;

            // Nada 1: C5 (523.25 Hz)
            const osc1 = ctx.createOscillator();
            const gain1 = ctx.createGain();
            osc1.type = 'sine';
            osc1.frequency.setValueAtTime(523.25, now);
            gain1.gain.setValueAtTime(0.3, now);
            gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.6);
            osc1.connect(gain1);
            gain1.connect(ctx.destination);
            osc1.start(now);
            osc1.stop(now + 0.6);

            // Nada 2: E5 (659.25 Hz)
            const osc2 = ctx.createOscillator();
            const gain2 = ctx.createGain();
            osc2.type = 'sine';
            osc2.frequency.setValueAtTime(659.25, now + 0.25);
            gain2.gain.setValueAtTime(0.3, now + 0.25);
            gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.85);
            osc2.connect(gain2);
            gain2.connect(ctx.destination);
            osc2.start(now + 0.25);
            osc2.stop(now + 0.85);

            // Nada 3: G5 (783.99 Hz)
            const osc3 = ctx.createOscillator();
            const gain3 = ctx.createGain();
            osc3.type = 'sine';
            osc3.frequency.setValueAtTime(783.99, now + 0.5);
            gain3.gain.setValueAtTime(0.3, now + 0.5);
            gain3.gain.exponentialRampToValueAtTime(0.001, now + 1.2);
            osc3.connect(gain3);
            gain3.connect(ctx.destination);
            osc3.start(now + 0.5);
            osc3.stop(now + 1.2);

            return new Promise(resolve => setTimeout(resolve, 1100));
        } catch (e) {
            console.error('Web Audio tidak didukung:', e);
            return Promise.resolve();
        }
    }

    // Suara Panggilan Wanita Bahasa Indonesia Jernih
    let currentSpeechAudio = null;
    function ucapkanPanggilan(teks) {
        playHospitalChime().then(() => {
            const audioUrl = "{{ route('monitoring.suara') }}?q=" + encodeURIComponent(teks);

            if (currentSpeechAudio) {
                currentSpeechAudio.pause();
                currentSpeechAudio.currentTime = 0;
            }

            currentSpeechAudio = new Audio(audioUrl);
            currentSpeechAudio.play().catch(err => {
                console.warn('Audio streaming offline, beralih ke Web Speech API:', err);
                fallbackSpeech(teks);
            });
        });
    }

    // Cadangan Web Speech API jika offline
    function fallbackSpeech(teks) {
        if (!('speechSynthesis' in window)) return;
        window.speechSynthesis.cancel();

        const utter = new SpeechSynthesisUtterance(teks);
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

    // Saat dropdown pasien dipilih di panel pemanggil
    function onSelectPasienCall(selectEl) {
        const selected = selectEl.options[selectEl.selectedIndex];
        if (selected && selected.value) {
            document.getElementById('callNamaPasien').value = selected.dataset.nama || '';
            const status = selected.dataset.status || '';
            // Auto-select tujuan panggilan sesuai posisi
            const tujuanSelect = document.getElementById('callTujuan');
            if (status.includes('Skrining')) {
                tujuanSelect.value = 'Ruang Skrining Perawat';
            } else if (status.includes('Kasir')) {
                tujuanSelect.value = 'Loket Kasir Pembayaran';
            } else if (status.includes('Farmasi') || status.includes('Obat')) {
                tujuanSelect.value = 'Loket Farmasi dan Pengambilan Obat';
            } else if (status.includes('Poli')) {
                tujuanSelect.value = 'Poli Umum';
            }
        }
    }

    // Panggil cepat dari tombol aksi tabel
    function panggilPasienCepat(noAntrian, namaPasien, tujuan) {
        document.getElementById('pilihPasienCall').value = noAntrian;
        document.getElementById('callNamaPasien').value = namaPasien;
        
        let targetTujuan = 'Poli Umum';
        if (tujuan.includes('Skrining')) targetTujuan = 'Ruang Skrining Perawat';
        else if (tujuan.includes('Kasir')) targetTujuan = 'Loket Kasir Pembayaran';
        else if (tujuan.includes('Farmasi') || tujuan.includes('Obat')) targetTujuan = 'Loket Farmasi dan Pengambilan Obat';
        else if (tujuan.includes('Loket')) targetTujuan = 'Loket Pendaftaran';
        
        document.getElementById('callTujuan').value = targetTujuan;
        jalankanPanggilan();
    }

    // Eksekusi Panggilan via API + Audio
    function jalankanPanggilan() {
        const noAntrian = document.getElementById('pilihPasienCall').value;
        const namaPasien = document.getElementById('callNamaPasien').value;
        const tujuan = document.getElementById('callTujuan').value;

        if (!noAntrian || !namaPasien) {
            alert('Silakan pilih pasien yang ingin dipanggil terlebih dahulu.');
            return;
        }

        const btn = document.getElementById('btnPanggilSuara');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyiarkan...';

        fetch("{{ route('resepsionis.panggil') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                no_antrian: noAntrian,
                nama_pasien: namaPasien,
                tujuan: tujuan
            })
        })
        .then(res => res.json())
        .then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-volume-high"></i> Panggil Antrean';

            if (res.success) {
                // Mainkan suara panggilan langsung di browser resepsionis
                ucapkanPanggilan(res.panggilan.suara_teks);

                // Tampilkan info
                const info = document.getElementById('statusPanggilanInfo');
                const infoText = document.getElementById('statusPanggilanText');
                infoText.textContent = `Panggilan berhasil: ${noAntrian} (${namaPasien}) menuju ${tujuan}.`;
                info.style.display = 'block';
                setTimeout(() => { info.style.display = 'none'; }, 8000);
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-volume-high"></i> Panggil Antrean';
            console.error('Gagal mengirim panggilan antrean:', err);
        });
    }

    // Preload voice speech synthesis
    if ('speechSynthesis' in window) {
        window.speechSynthesis.onvoiceschanged = function() {
            window.speechSynthesis.getVoices();
        };
    }
</script>
@endsection
