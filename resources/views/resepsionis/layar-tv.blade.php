@extends('layouts.app')
@php $halamanAktif = 'resepsionis_display'; @endphp
@section('title', 'Layar Display TV Ruang Tunggu - CCTV Monitor SIMRS Delta')

@push('styles')
<style>
    /* GRID UTAMA CCTV DASHBOARD */
    .cctv-dashboard-grid {
        display: grid;
        grid-template-columns: 1.6fr 1fr;
        gap: 20px;
        align-items: start;
        margin-top: 14px;
    }

    /* KOLOM KIRI: CONTAINER CCTV & KONTROL PANGGILAN */
    .cctv-left-column {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    /* MONITOR CCTV HARDWARE FRAME */
    .cctv-hardware-frame {
        background: #0b1325;
        border: 2px solid #1e293b;
        border-radius: 18px;
        padding: 16px;
        box-shadow: 0 14px 40px rgba(0, 0, 0, 0.25);
        display: flex;
        flex-direction: column;
        gap: 12px;
        position: relative;
    }

    /* TOP BAR CCTV */
    .cctv-top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 4px 6px;
        color: #94a3b8;
        font-size: 12px;
        font-family: 'Roboto Mono', monospace;
    }
    .cctv-rec-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #ef4444;
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    .cctv-dot-rec {
        width: 10px;
        height: 10px;
        background: #ef4444;
        border-radius: 50%;
        box-shadow: 0 0 10px #ef4444;
        animation: cctvPulse 1.2s infinite;
    }
    @keyframes cctvPulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(1.2); }
    }
    .cctv-clock-display {
        color: #38bdf8;
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    /* SCREEN VIEWPORT CONTAINER (16:9) */
    .cctv-screen-container {
        position: relative;
        width: 100%;
        aspect-ratio: 16 / 9;
        background: #000000;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #334155;
        box-shadow: inset 0 0 25px rgba(0, 0, 0, 0.8);
    }
    .cctv-screen-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 1280px;
        height: 720px;
        border: none;
        transform-origin: top left;
        pointer-events: auto;
    }

    /* BOTTOM CONTROLS MONITOR CCTV */
    .cctv-bottom-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 4px;
        flex-wrap: wrap;
        gap: 10px;
    }
    .cctv-status-signal {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 11.5px;
        color: #10b981;
        font-weight: 600;
    }
    .cctv-controls-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .btn-cctv-ctrl {
        background: #1e293b;
        color: #f1f5f9;
        border: 1px solid #334155;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 11.5px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .btn-cctv-ctrl:hover {
        background: #334155;
        color: #38bdf8;
        transform: translateY(-1px);
    }

    /* CARD UMUM DASHBOARD & INFO */
    .info-panel-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 18px 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        transition: background 0.3s, border-color 0.3s;
    }
    .info-panel-title {
        font-size: 13.5px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    body.dark-mode .info-panel-card {
        background: #111c30;
        border-color: #1e293b;
    }
    body.dark-mode .info-panel-title {
        color: #f1f5f9;
    }

    /* FORM KENDALI PANGGILAN LANGSUNG */
    .cctv-call-form-grid {
        display: grid;
        grid-template-columns: 1.4fr 1.3fr auto;
        gap: 12px;
        align-items: end;
    }
    @media (max-width: 768px) {
        .cctv-call-form-grid {
            grid-template-columns: 1fr;
        }
    }
    .cctv-select-input {
        width: 100%;
        padding: 9px 12px;
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        font-size: 12.5px;
        background: #f8fafc;
        color: #0f172a;
        outline: none;
        transition: border-color 0.2s;
    }
    .cctv-select-input:focus {
        border-color: #2563eb;
        background: #ffffff;
    }
    body.dark-mode .cctv-select-input {
        background: #0b1528;
        border-color: #1e293b;
        color: #f1f5f9;
    }
    .btn-cctv-panggil {
        background: #10b981;
        color: #ffffff;
        border: none;
        padding: 9px 18px;
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
        transition: all 0.2s;
        white-space: nowrap;
        height: 38px;
    }
    .btn-cctv-panggil:hover {
        background: #059669;
        transform: translateY(-1px);
    }

    /* TABEL RIWAYAT PANGGILAN */
    .table-history-call {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }
    .table-history-call th {
        text-align: left;
        padding: 8px 12px;
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
    }
    body.dark-mode .table-history-call th {
        background: #0b1528;
        border-color: #1e293b;
        color: #94a3b8;
    }
    .table-history-call td {
        padding: 10px 12px;
        border-bottom: 1px solid #f1f5f9;
        color: #1e293b;
    }
    body.dark-mode .table-history-call td {
        border-color: #1e293b;
        color: #f1f5f9;
    }
    .btn-recall {
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #bfdbfe;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: 0.2s;
    }
    .btn-recall:hover {
        background: #2563eb;
        color: #ffffff;
    }
    body.dark-mode .btn-recall {
        background: rgba(37, 99, 235, 0.15);
        color: #60a5fa;
        border-color: rgba(96, 165, 250, 0.3);
    }

    /* KOLOM KANAN: PANEL KETERANGAN & INFORMASI */
    .cctv-info-column {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    /* BADGE STATUS OPERASIONAL */
    .status-tv-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #ecfdf5;
        color: #059669;
        font-size: 11.5px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        border: 1px solid #a7f3d0;
    }
    body.dark-mode .status-tv-pill {
        background: rgba(16, 185, 129, 0.15);
        color: #34d399;
        border-color: rgba(52, 211, 153, 0.3);
    }

    /* HERO CALL BOX PREVIEW */
    .hero-call-preview-box {
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
        border: 1px solid #bfdbfe;
        border-radius: 12px;
        padding: 14px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    body.dark-mode .hero-call-preview-box {
        background: linear-gradient(135deg, #0b1b36, #11264b);
        border-color: #1e3a8a;
    }
    .hero-call-num {
        font-family: 'Roboto Mono', monospace;
        font-size: 24px;
        font-weight: 800;
        color: #1d4ed8;
    }
    body.dark-mode .hero-call-num {
        color: #60a5fa;
    }

    /* GRID 4 SEKTOR ANTREAN */
    .sector-mini-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
    .sector-mini-item {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    body.dark-mode .sector-mini-item {
        background: #0b1528;
        border-color: #1e293b;
    }
    .sector-mini-name {
        font-size: 11.5px;
        font-weight: 600;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    body.dark-mode .sector-mini-name {
        color: #94a3b8;
    }
    .sector-mini-count {
        font-size: 13px;
        font-weight: 800;
        color: #0f172a;
    }
    body.dark-mode .sector-mini-count {
        color: #f1f5f9;
    }

    /* URL COPY BOX */
    .copy-url-group {
        display: flex;
        gap: 6px;
        margin-top: 10px;
    }
    .input-copy-url {
        flex: 1;
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        font-size: 12px;
        font-family: monospace;
        background: #f8fafc;
        color: #334155;
    }
    body.dark-mode .input-copy-url {
        background: #0b1528;
        border-color: #1e293b;
        color: #cbd5e1;
    }
    .btn-copy {
        background: #2563eb;
        color: #ffffff;
        border: none;
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: 0.2s;
    }
    .btn-copy:hover {
        background: #1d4ed8;
    }

    /* STEPS LIST SMART TV */
    .step-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .step-list li {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: 12px;
        color: #475569;
        line-height: 1.4;
    }
    body.dark-mode .step-list li {
        color: #94a3b8;
    }
    .step-badge {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #eff6ff;
        color: #2563eb;
        font-weight: 700;
        font-size: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    body.dark-mode .step-badge {
        background: rgba(37, 99, 235, 0.2);
        color: #60a5fa;
    }

    @media (max-width: 1024px) {
        .cctv-dashboard-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div style="padding: 24px 28px;">

    <!-- HEADER TITLE & ACTION BUTTONS -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 14px;">
        <div>
            <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 4px; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-tv" style="color: #2563eb;"></i> Layar Display TV Ruang Tunggu
            </h1>
            <p style="font-size: 13px; color: #64748b; margin: 0;">
                Live Monitor Antrean TV Publik RS Delta (Mode Pengawasan CCTV Sentral & Pemanggil Layar)
            </p>
        </div>

        <div style="display: flex; gap: 10px; align-items: center;">
            <button type="button" onclick="reloadCctv()" class="btn-cctv-ctrl" style="padding: 9px 14px; background: #ffffff; color: #334155; border: 1px solid #cbd5e1; box-shadow: 0 2px 6px rgba(0,0,0,0.03);">
                <i class="fa-solid fa-arrows-rotate"></i> Refresh Sinyal TV
            </button>
            <a href="{{ route('monitoring.display') }}" target="_blank" class="btn-copy" style="padding: 9px 16px; font-size: 12.5px; text-decoration: none;">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Fullscreen TV di Tab Baru
            </a>
        </div>
    </div>

    <!-- MAIN CCTV DASHBOARD GRID -->
    <div class="cctv-dashboard-grid">

        <!-- ============================================== -->
        <!-- KOLOM KIRI: HARDWARE CCTV + KONTROL PANGGILAN -->
        <!-- ============================================== -->
        <div class="cctv-left-column">

            <!-- 1. HARDWARE CCTV MONITOR FRAME -->
            <div class="cctv-hardware-frame">
                <!-- CCTV TOP BAR -->
                <div class="cctv-top-bar">
                    <div class="cctv-rec-badge">
                        <span class="cctv-dot-rec"></span>
                        <span>LIVE FEED • CAM 01 RUANG TUNGGU RS DELTA</span>
                    </div>
                    <div class="cctv-clock-display" id="cctvClock">
                        --:--:-- WITA
                    </div>
                </div>

                <!-- CCTV SCREEN CONTAINER (16:9 RATIO) -->
                <div class="cctv-screen-container" id="cctvScreenContainer">
                    <iframe src="{{ route('monitoring.display') }}?preview=1" id="cctvIframe" title="Live Display TV Antrean RS Delta"></iframe>
                </div>

                <!-- CCTV BOTTOM CONTROLS -->
                <div class="cctv-bottom-bar">
                    <div class="cctv-status-signal">
                        <i class="fa-solid fa-circle-dot" style="font-size: 10px;"></i>
                        <span>Sinyal HDMI Output: 1080p FHD @ 60Hz • Polling 3s Aktif</span>
                    </div>

                    <div class="cctv-controls-group">
                        <button type="button" class="btn-cctv-ctrl" id="btnToggleAudio" onclick="toggleCctvAudio()" title="Aktifkan/Matikan Suara Speaker Preview">
                            <i class="fa-solid fa-volume-xmark" id="audioIcon"></i>
                            <span id="audioText">Suara Senyap (Muted)</span>
                        </button>
                        <button type="button" class="btn-cctv-ctrl" onclick="reloadCctv()" title="Muat Ulang Layar CCTV">
                            <i class="fa-solid fa-rotate-right"></i>
                        </button>
                        <a href="{{ route('monitoring.display') }}" target="_blank" class="btn-cctv-ctrl" title="Buka Fullscreen di Tab Baru">
                            <i class="fa-solid fa-expand"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 2. KONTROL PANGGILAN CEPAT LANGSUNG KE LAYAR TV -->
            <div class="info-panel-card">
                <div class="info-panel-title">
                    <i class="fa-solid fa-microphone-lines" style="color: #10b981;"></i> Panggil Pasien Langsung ke Layar TV
                </div>

                <form id="formPanggilCctv" onsubmit="event.preventDefault(); submitCctvCall();" class="cctv-call-form-grid">
                    <div>
                        <label style="display: block; font-size: 11.5px; font-weight: 600; margin-bottom: 5px; color: #64748b;">
                            Pilih Pasien Aktif *
                        </label>
                        <select id="cctvSelectPasien" class="cctv-select-input" required onchange="onSelectCctvPasien(this)">
                            <option value="">-- Pilih Antrean Pasien --</option>
                            @foreach($pasienPanggilan as $p)
                                <option value="{{ $p->no_antrian }}" data-nama="{{ $p->nama_pasien }}" data-poli="{{ $p->nama_poli }}" data-status="{{ $p->posisi_aktif }}">
                                    [{{ $p->no_antrian }}] {{ $p->nama_pasien }} - {{ $p->posisi_aktif }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" id="cctvCallNama" value="">
                    </div>

                    <div>
                        <label style="display: block; font-size: 11.5px; font-weight: 600; margin-bottom: 5px; color: #64748b;">
                            Tujuan Panggilan (Loket / Ruang) *
                        </label>
                        <select id="cctvSelectTujuan" class="cctv-select-input" required>
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
                        <button type="submit" class="btn-cctv-panggil" id="btnSubmitCall">
                            <i class="fa-solid fa-bullhorn"></i> Panggil ke TV
                        </button>
                    </div>
                </form>

                <div id="cctvCallAlert" style="display: none; margin-top: 12px; font-size: 12px; padding: 8px 12px; border-radius: 8px; background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0;">
                    <i class="fa-solid fa-circle-check"></i> <span id="cctvCallAlertText">Panggilan berhasil disiarkan!</span>
                </div>
            </div>

            <!-- 3. RIWAYAT PEMANGGILAN ANTREAN HARI INI -->
            <div class="info-panel-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <div class="info-panel-title" style="margin-bottom: 0;">
                        <i class="fa-solid fa-clock-rotate-left" style="color: #6366f1;"></i> Riwayat Panggilan Suara Hari Ini
                    </div>
                    <span style="font-size: 11px; color: #64748b;">Maks. 8 Panggilan Terkini</span>
                </div>

                <div style="overflow-x: auto;">
                    <table class="table-history-call" id="tableHistoryCall">
                        <thead>
                            <tr>
                                <th style="width: 14%;">WAKTU</th>
                                <th style="width: 18%;">NO. ANTREAN</th>
                                <th style="width: 30%;">NAMA PASIEN</th>
                                <th style="width: 26%;">TUJUAN LOKET</th>
                                <th style="width: 12%; text-align: center;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyHistoryCall">
                            @forelse($riwayatPanggilan as $r)
                                <tr>
                                    <td style="font-family: monospace; font-size: 11px; color: #64748b;">
                                        {{ $r['waktu'] ?? '--:--' }} WITA
                                    </td>
                                    <td>
                                        <strong style="color: #2563eb; font-family: monospace;">{{ $r['no_antrian'] ?? '-' }}</strong>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600;">{{ $r['nama_pasien'] ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <span style="font-size: 11px; background: #f1f5f9; padding: 2px 8px; border-radius: 4px; color: #475569;">
                                            {{ $r['tujuan'] ?? '-' }}
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <button type="button" class="btn-recall" onclick="recallPasien('{{ $r['no_antrian'] }}', '{{ addslashes($r['nama_pasien']) }}', '{{ addslashes($r['tujuan']) }}')" title="Panggil Ulang Pasien Ini">
                                            <i class="fa-solid fa-volume-high"></i> Ulang
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr id="emptyHistoryRow">
                                    <td colspan="5" style="text-align: center; color: #94a3b8; padding: 20px;">
                                        Belum ada riwayat panggilan antrean hari ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- ============================================== -->
        <!-- KOLOM KANAN: PANEL KETERANGAN & INFORMASI TV   -->
        <!-- ============================================== -->
        <div class="cctv-info-column">

            <!-- CARD 1: STATUS PENAYANGAN -->
            <div class="info-panel-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <div class="info-panel-title" style="margin-bottom: 0;">
                        <i class="fa-solid fa-circle-nodes" style="color: #2563eb;"></i> Status Display TV
                    </div>
                    <span class="status-tv-pill">
                        <i class="fa-solid fa-wifi"></i> Online & Terhubung
                    </span>
                </div>

                <div style="display: flex; flex-direction: column; gap: 8px; font-size: 12px;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #64748b;">Lokasi Fisik TV:</span>
                        <strong style="color: #1e293b;">Dinding Ruang Tunggu Utama Lt. 1</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #64748b;">Resolusi Output:</span>
                        <strong style="color: #1e293b;">1920 × 1080 Full HD (16:9)</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #64748b;">Audio Lonceng & Suara:</span>
                        <strong style="color: #10b981;">Synthesized Chime + TTS ID</strong>
                    </div>
                </div>

                <div style="margin-top: 14px; border-top: 1px solid #f1f5f9; padding-top: 12px;">
                    <label style="display: block; font-size: 11.5px; font-weight: 700; color: #475569; margin-bottom: 4px;">
                        Tautan Publik Layar TV (Tanpa Login):
                    </label>
                    <div class="copy-url-group">
                        <input type="text" id="tvPublicUrl" value="{{ url('/monitoring') }}" readonly class="input-copy-url">
                        <button type="button" onclick="copyTvUrl()" class="btn-copy" id="btnCopyUrl" title="Salin Tautan">
                            <i class="fa-regular fa-copy"></i> Salin
                        </button>
                    </div>
                </div>
            </div>

            <!-- CARD 2: PANGGILAN ANTREAN TERAKHIR DITAYANGKAN -->
            <div class="info-panel-card">
                <div class="info-panel-title">
                    <i class="fa-solid fa-bullhorn" style="color: #ef4444;"></i> Panggilan Terakhir di Layar
                </div>

                <div class="hero-call-preview-box">
                    <div>
                        <div style="font-size: 10.5px; font-weight: 700; color: #64748b; letter-spacing: 0.5px; text-transform: uppercase;">
                            Sedang Dipanggil
                        </div>
                        <div class="hero-call-num" id="sideHeroNum">
                            {{ $panggilanTerbaru['no_antrian'] ?? '---' }}
                        </div>
                        <div style="font-size: 12px; font-weight: 600; color: #1e293b; margin-top: 2px;" id="sideHeroPatient">
                            {{ $panggilanTerbaru['nama_pasien'] ?? 'Belum ada panggilan antrean' }}
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <span style="font-size: 11px; background: #2563eb; color: #fff; padding: 4px 10px; border-radius: 6px; font-weight: 600;" id="sideHeroDest">
                            {{ $panggilanTerbaru['tujuan'] ?? 'Poli & Loket' }}
                        </span>
                        <div style="font-size: 10px; color: #64748b; margin-top: 6px; font-family: monospace;" id="sideHeroTime">
                            {{ $panggilanTerbaru['waktu'] ?? '--:--:--' }} WITA
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD 3: RINGKASAN 4 SEKTOR ANTREAN -->
            <div class="info-panel-card">
                <div class="info-panel-title">
                    <i class="fa-solid fa-layer-group" style="color: #f59e0b;"></i> Antrean Aktif di Display TV
                </div>

                <div class="sector-mini-grid">
                    <div class="sector-mini-item">
                        <span class="sector-mini-name">
                            <i class="fa-solid fa-heart-pulse" style="color: #f59e0b;"></i> Skrining TTV
                        </span>
                        <span class="sector-mini-count" id="countSkrining">{{ $totalSkrining }}</span>
                    </div>

                    <div class="sector-mini-item">
                        <span class="sector-mini-name">
                            <i class="fa-solid fa-user-doctor" style="color: #3b82f6;"></i> Poli Dokter
                        </span>
                        <span class="sector-mini-count" id="countDokter">{{ $totalDokter }}</span>
                    </div>

                    <div class="sector-mini-item">
                        <span class="sector-mini-name">
                            <i class="fa-solid fa-cash-register" style="color: #f97316;"></i> Loket Kasir
                        </span>
                        <span class="sector-mini-count" id="countKasir">{{ $totalKasir }}</span>
                    </div>

                    <div class="sector-mini-item">
                        <span class="sector-mini-name">
                            <i class="fa-solid fa-prescription-bottle-medical" style="color: #10b981;"></i> Farmasi Obat
                        </span>
                        <span class="sector-mini-count" id="countFarmasi">{{ $totalFarmasi }}</span>
                    </div>
                </div>
            </div>

            <!-- CARD 4: PETUNJUK PEMASANGAN SMART TV -->
            <div class="info-panel-card">
                <div class="info-panel-title">
                    <i class="fa-solid fa-circle-question" style="color: #6366f1;"></i> Petunjuk Pemasangan TV
                </div>

                <ol class="step-list">
                    <li>
                        <span class="step-badge">1</span>
                        <span>Hubungkan Smart TV ke jaringan WiFi / LAN rumah sakit.</span>
                    </li>
                    <li>
                        <span class="step-badge">2</span>
                        <span>Buka browser di Smart TV (Android TV, Samsung Tizen, LG webOS).</span>
                    </li>
                    <li>
                        <span class="step-badge">3</span>
                        <span>Ketik alamat: <code style="color: #2563eb; background: #eff6ff; padding: 1px 4px; border-radius: 4px;">{{ url('/monitoring') }}</code></span>
                    </li>
                    <li>
                        <span class="step-badge">4</span>
                        <span>Tekan tombol <strong>Layar Penuh (F11)</strong>. Suara pemanggilan pasien otomatis berbunyi dari speaker TV!</span>
                    </li>
                </ol>
            </div>

        </div>

    </div>

</div>

<!-- SCRIPT AUTOSCALE CCTV, CLOCK, AUDIO CONTROL & QUICK CALL -->
<script>
    // Responsive Scaling untuk Iframe 1280x720 ke aspect-ratio kontainer CCTV
    function resizeCctvViewport() {
        const container = document.getElementById('cctvScreenContainer');
        const iframe = document.getElementById('cctvIframe');
        if (!container || !iframe) return;

        const containerWidth = container.clientWidth;
        const scale = containerWidth / 1280;
        iframe.style.transform = `scale(${scale})`;
    }

    window.addEventListener('resize', resizeCctvViewport);
    window.addEventListener('load', resizeCctvViewport);
    setTimeout(resizeCctvViewport, 200);

    // Update Jam CCTV Monospace Realtime
    function updateCctvClock() {
        const now = new Date();
        const timeStr = now.toLocaleTimeString('id-ID', { hour12: false });
        const clockEl = document.getElementById('cctvClock');
        if (clockEl) {
            clockEl.textContent = timeStr + ' WITA';
        }
    }
    setInterval(updateCctvClock, 1000);
    updateCctvClock();

    // Reload Frame CCTV Preview
    function reloadCctv() {
        const iframe = document.getElementById('cctvIframe');
        if (iframe) {
            iframe.src = "{{ route('monitoring.display') }}?preview=1&t=" + new Date().getTime();
            setTimeout(resizeCctvViewport, 300);
        }
    }

    // Toggle Mute / Unmute Audio Preview
    let cctvMuted = true;
    function toggleCctvAudio() {
        cctvMuted = !cctvMuted;
        const iframe = document.getElementById('cctvIframe');
        const icon = document.getElementById('audioIcon');
        const text = document.getElementById('audioText');

        if (cctvMuted) {
            icon.className = 'fa-solid fa-volume-xmark';
            text.textContent = 'Suara Senyap (Muted)';
        } else {
            icon.className = 'fa-solid fa-volume-high';
            text.textContent = 'Speaker Aktif';
        }

        if (iframe && iframe.contentWindow) {
            iframe.contentWindow.postMessage({ type: 'SET_MUTE', muted: cctvMuted }, '*');
        }
    }

    // Handler Pilih Pasien CCTV Form
    function onSelectCctvPasien(selectEl) {
        const selected = selectEl.options[selectEl.selectedIndex];
        const nama = selected.getAttribute('data-nama') || '';
        const poli = selected.getAttribute('data-poli') || '';
        document.getElementById('cctvCallNama').value = nama;

        // Auto pilih tujuan sesuai poli pasien
        if (poli) {
            const selectTujuan = document.getElementById('cctvSelectTujuan');
            for (let i = 0; i < selectTujuan.options.length; i++) {
                if (selectTujuan.options[i].value.toLowerCase().includes(poli.toLowerCase())) {
                    selectTujuan.selectedIndex = i;
                    break;
                }
            }
        }
    }

    // Kirim Panggilan Langsung ke Layar TV
    function submitCctvCall() {
        const noAntrian = document.getElementById('cctvSelectPasien').value;
        const namaPasien = document.getElementById('cctvCallNama').value;
        const tujuan = document.getElementById('cctvSelectTujuan').value;

        if (!noAntrian || !namaPasien || !tujuan) {
            alert('Mohon pilih nomor antrean pasien dan tujuan panggilan.');
            return;
        }

        executePanggil(noAntrian, namaPasien, tujuan);
    }

    // Panggil Ulang Pasien dari Tabel Riwayat
    function recallPasien(noAntrian, namaPasien, tujuan) {
        if (!confirm(`Panggil ulang nomor antrean [${noAntrian}] ${namaPasien} ke ${tujuan}?`)) {
            return;
        }
        executePanggil(noAntrian, namaPasien, tujuan);
    }

    // Eksekusi Panggilan via Ajax ke Server
    function executePanggil(noAntrian, namaPasien, tujuan) {
        const btn = document.getElementById('btnSubmitCall');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyiarkan...';

        fetch("{{ route('resepsionis.panggil') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                no_antrian: noAntrian,
                nama_pasien: namaPasien,
                tujuan: tujuan
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = originalText;

            if (data.success) {
                // Tampilkan notifikasi sukses
                const alertEl = document.getElementById('cctvCallAlert');
                const textEl = document.getElementById('cctvCallAlertText');
                textEl.textContent = `Panggilan untuk [${noAntrian}] ${namaPasien} berhasil disiarkan ke Layar TV!`;
                alertEl.style.display = 'block';
                setTimeout(() => { alertEl.style.display = 'none'; }, 4000);

                // Tambah baris baru ke tabel riwayat di atas
                addHistoryRow({
                    waktu: new Date().toLocaleTimeString('id-ID', { hour12: false }) + ' WITA',
                    no_antrian: noAntrian,
                    nama_pasien: namaPasien,
                    tujuan: tujuan
                });

                // Perbarui tampilan hero panel kanan
                document.getElementById('sideHeroNum').textContent = noAntrian;
                document.getElementById('sideHeroPatient').textContent = namaPasien;
                document.getElementById('sideHeroDest').textContent = tujuan;
                document.getElementById('sideHeroTime').textContent = new Date().toLocaleTimeString('id-ID', { hour12: false }) + ' WITA';
            } else {
                alert('Gagal menyiarkan panggilan: ' + (data.message || 'Terjadi kesalahan sistem.'));
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            console.error('Call error:', err);
            alert('Koneksi terputus saat memanggil antrean.');
        });
    }

    // Sisipkan Baris Riwayat Baru
    function addHistoryRow(item) {
        const emptyRow = document.getElementById('emptyHistoryRow');
        if (emptyRow) emptyRow.remove();

        const tbody = document.getElementById('tbodyHistoryCall');
        const tr = document.createElement('tr');
        tr.style.animation = 'fadeIn 0.4s';
        tr.innerHTML = `
            <td style="font-family: monospace; font-size: 11px; color: #64748b;">${item.waktu}</td>
            <td><strong style="color: #2563eb; font-family: monospace;">${item.no_antrian}</strong></td>
            <td><div style="font-weight: 600;">${item.nama_pasien}</div></td>
            <td><span style="font-size: 11px; background: #f1f5f9; padding: 2px 8px; border-radius: 4px; color: #475569;">${item.tujuan}</span></td>
            <td style="text-align: center;">
                <button type="button" class="btn-recall" onclick="recallPasien('${item.no_antrian}', '${item.nama_pasien}', '${item.tujuan}')" title="Panggil Ulang Pasien Ini">
                    <i class="fa-solid fa-volume-high"></i> Ulang
                </button>
            </td>
        `;
        tbody.insertBefore(tr, tbody.firstChild);

        // Pertahankan maksimal 8 baris
        while (tbody.children.length > 8) {
            tbody.removeChild(tbody.lastChild);
        }
    }

    // Salin Tautan Display TV
    function copyTvUrl() {
        const input = document.getElementById('tvPublicUrl');
        const btn = document.getElementById('btnCopyUrl');
        if (!input) return;

        navigator.clipboard.writeText(input.value).then(() => {
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-check"></i> Tersalin!';
            btn.style.background = '#10b981';
            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.style.background = '#2563eb';
            }, 2000);
        }).catch(() => {
            input.select();
            document.execCommand('copy');
            alert('Tautan berhasil disalin ke clipboard!');
        });
    }

    // Polling Status Panggilan untuk Panel Samping
    let lastSideCallId = "{{ $panggilanTerbaru['id_panggilan'] ?? '' }}";
    function checkSideCall() {
        fetch("{{ route('monitoring.status-panggilan') }}")
            .then(res => res.json())
            .then(data => {
                if (data.has_call && data.panggilan) {
                    const call = data.panggilan;
                    if (call.id_panggilan && call.id_panggilan !== lastSideCallId) {
                        lastSideCallId = call.id_panggilan;
                        document.getElementById('sideHeroNum').textContent = call.no_antrian;
                        document.getElementById('sideHeroPatient').textContent = call.nama_pasien;
                        document.getElementById('sideHeroDest').textContent = call.tujuan;
                        document.getElementById('sideHeroTime').textContent = (call.waktu || '') + ' WITA';
                    }
                }
            })
            .catch(() => {});
    }
    setInterval(checkSideCall, 3000);
</script>
@endsection
