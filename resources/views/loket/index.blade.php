@extends('layouts.app')
@php $halamanAktif = 'loket_pendaftaran'; @endphp
@section('title', 'Loket Pendaftaran Pasien - Rumah Sakit Delta')

@push('styles')
<style>
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .stat-info .num {
        font-size: 22px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.2;
    }
    .stat-info .label {
        font-size: 12px;
        color: #64748b;
        margin-top: 2px;
    }
    .grid-layout {
        display: grid;
        grid-template-columns: 1fr 1.25fr;
        gap: 24px;
        align-items: start;
    }
    @media (max-width: 1080px) {
        .grid-layout {
            grid-template-columns: 1fr;
        }
    }
    .content-box {
        background: #ffffff;
        border-radius: 20px;
        padding: 24px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
    }
    .badge-queue {
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 8px;
        background: #eef2ff;
        color: #1e40af;
        border: 1px solid #c7d2fe;
        display: inline-block;
    }
    .badge-status-loket {
        font-size: 11px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 6px;
    }
    .badge-status-menunggu {
        background: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }
    .badge-status-dokter {
        background: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }
    .badge-status-selesai {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }
    .badge-status-batal {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }
    .type-switcher {
        display: flex;
        background: #f1f5f9;
        padding: 4px;
        border-radius: 12px;
        gap: 4px;
        margin-bottom: 20px;
    }
    .type-btn {
        flex: 1;
        text-align: center;
        padding: 9px 12px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 8px;
        cursor: pointer;
        border: none;
        background: transparent;
        color: #64748b;
        transition: all 0.2s ease;
    }
    .type-btn.active {
        background: #ffffff;
        color: #1a4a8d;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
    }
    .autocomplete-results {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
        z-index: 9999;
        max-height: 260px;
        overflow-y: auto;
        display: none;
        margin-top: 4px;
    }
    .autocomplete-header {
        padding: 8px 14px;
        font-size: 11px;
        font-weight: 600;
        color: #64748b;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        border-radius: 12px 12px 0 0;
    }
    .autocomplete-item {
        padding: 10px 14px;
        cursor: pointer;
        border-bottom: 1px solid #f1f5f9;
        font-size: 12px;
        transition: background 0.15s ease;
    }
    .autocomplete-item:last-child {
        border-bottom: none;
    }
    .autocomplete-item:hover {
        background: #f0f6fd;
    }
    .autocomplete-name {
        color: #1e293b;
        font-size: 13px;
        font-weight: 700;
        transition: color 0.15s ease;
    }
    .autocomplete-meta {
        color: #64748b;
        font-size: 11px;
        margin-top: 3px;
    }
    .autocomplete-address {
        color: #94a3b8;
        font-size: 10.5px;
        margin-top: 2px;
    }
    body.dark-mode .autocomplete-results {
        background: #121927 !important;
        border-color: #1e293b !important;
        color: #f1f5f9 !important;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4) !important;
    }
    body.dark-mode .autocomplete-header {
        background: #0b0f19 !important;
        border-bottom-color: #1e293b !important;
        color: #94a3b8 !important;
    }
    body.dark-mode .autocomplete-item {
        border-bottom-color: #1e293b !important;
    }
    body.dark-mode .autocomplete-item:hover {
        background: #1a263d !important;
    }
    body.dark-mode .autocomplete-name {
        color: #f8fafc !important;
    }
    body.dark-mode .autocomplete-item:hover .autocomplete-name {
        color: #60a5fa !important;
    }
    body.dark-mode .autocomplete-meta {
        color: #94a3b8 !important;
    }
    body.dark-mode .autocomplete-meta strong {
        color: #e2e8f0 !important;
    }
    body.dark-mode .autocomplete-address {
        color: #64748b !important;
    }

    /* ── DARK MODE OVERRIDES LOKET PENDAFTARAN ── */
    body.dark-mode .stat-card,
    body.dark-mode .content-box {
        background-color: #121927 !important;
        border-color: #1e293b !important;
        color: #f1f5f9 !important;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3) !important;
    }
    body.dark-mode .stat-info .num {
        color: #f8fafc !important;
    }
    body.dark-mode .stat-info .label {
        color: #94a3b8 !important;
    }
    body.dark-mode h1,
    body.dark-mode h2,
    body.dark-mode h3 {
        color: #f8fafc !important;
    }
    body.dark-mode p {
        color: #94a3b8 !important;
    }
    body.dark-mode label {
        color: #cbd5e1 !important;
    }
    body.dark-mode .type-switcher {
        background-color: #0b0f19 !important;
    }
    body.dark-mode .type-btn {
        color: #94a3b8 !important;
    }
    body.dark-mode .type-btn.active {
        background-color: #1e293b !important;
        color: #60a5fa !important;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.4) !important;
    }
    body.dark-mode input[type="text"],
    body.dark-mode input[type="date"],
    body.dark-mode select,
    body.dark-mode textarea {
        background-color: #0b0f19 !important;
        border-color: #1e293b !important;
        color: #f8fafc !important;
    }
    body.dark-mode .radio-bayar-card {
        background-color: #0b0f19 !important;
        border-color: #1e293b !important;
        color: #cbd5e1 !important;
    }
    body.dark-mode .radio-bayar-card:hover {
        border-color: #3b82f6 !important;
    }
    body.dark-mode #card-preview-pasien {
        background-color: #0b0f19 !important;
        border-color: #1d4ed8 !important;
    }
    body.dark-mode #card-preview-pasien strong {
        color: #f8fafc !important;
    }
    body.dark-mode #card-preview-pasien div {
        color: #94a3b8 !important;
    }
    body.dark-mode #card-preview-pasien span:not(.badge-queue) {
        color: #cbd5e1 !important;
    }
    body.dark-mode thead tr {
        border-bottom-color: #1e293b !important;
        color: #94a3b8 !important;
    }
    body.dark-mode tbody tr {
        border-bottom-color: #1e293b !important;
    }
    body.dark-mode tbody tr:hover td {
        background-color: #0b0f19 !important;
    }
    body.dark-mode tbody tr strong {
        color: #f8fafc !important;
    }
    body.dark-mode .badge-queue {
        background-color: rgba(30, 64, 175, 0.3) !important;
        color: #93c5fd !important;
        border-color: #1d4ed8 !important;
    }
    body.dark-mode .simrs-btn-white {
        background-color: #0b0f19 !important;
        border-color: #1e293b !important;
        color: #cbd5e1 !important;
    }
    body.dark-mode .simrs-btn-white:hover {
        background-color: #1e293b !important;
        color: #f8fafc !important;
    }
</style>
@endpush

@section('content')
<div class="content-body" style="padding-bottom: 40px;">

    <!-- HEADER TITLE & DESKRIPSI -->
    <div style="margin-bottom: 24px;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
            <div>
                <h1 style="font-size: 22px; font-weight: 700; color: #1e293b; margin: 0;">Loket Pendaftaran Pasien</h1>
                <p style="font-size: 13px; color: #64748b; margin: 4px 0 0;">
                    Pelayanan admisi pasien rawat jalan walk-in, pemilihan poliklinik tujuan, penerbitan tiket karcis antrean, dan integrasi skrining TTV perawat.
                </p>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="button" onclick="location.reload();" class="simrs-btn-white" style="font-size: 12px; padding: 8px 16px; border-radius: 10px; border: 1px solid #cbd5e1; background: #fff; cursor: pointer;">
                    <i class="fa-solid fa-rotate-right" style="margin-right: 6px;"></i> Refresh Antrean
                </button>
            </div>
        </div>
    </div>

    <!-- FLASH MESSAGE & CETAK KARCIS POPUP BANNER -->
    @if (session('success'))
        <div style="background: #ffffff; border-radius: 16px; border: 1px solid #86efac; padding: 18px 22px; margin-bottom: 24px; box-shadow: 0 4px 15px rgba(22, 101, 52, 0.06); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 42px; height: 42px; border-radius: 10px; background: #dcfce7; color: #15803d; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <strong style="color: #15803d; font-size: 13px; display: block;">Berhasil Terdaftar!</strong>
                    <span style="font-size: 12px; color: #334155;">{{ session('success') }}</span>
                </div>
            </div>
            @if (session('cetak_id'))
                <a href="{{ route('loket.karcis', session('cetak_id')) }}" target="_blank"
                   style="background: #1a4a8d; color: #fff; padding: 10px 18px; border-radius: 10px; text-decoration: none; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 8px rgba(26, 74, 141, 0.25);">
                    <i class="fa-solid fa-print"></i> Cetak Karcis Antrean ({{ session('cetak_antrian') }})
                </a>
            @endif
        </div>
    @endif

    @if (session('error'))
        <div style="background: #fef2f2; border-radius: 16px; border: 1px solid #fca5a5; padding: 16px 20px; margin-bottom: 24px; color: #991b1b; font-size: 13px; display: flex; align-items: center; gap: 12px;">
            <i class="fa-solid fa-circle-exclamation text-base"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- STATS CARDS -->
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: #e0f2fe; color: #0284c7;">
                <i class="fa-solid fa-id-card-clip"></i>
            </div>
            <div class="stat-info">
                <div class="num">{{ $totalHariIni }}</div>
                <div class="label">Total Pendaftaran Hari Ini</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #fef3c7; color: #d97706;">
                <i class="fa-solid fa-user-clock"></i>
            </div>
            <div class="stat-info">
                <div class="num">{{ $menungguSkrining }}</div>
                <div class="label">Menunggu Skrining TTV</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #dcfce7; color: #16a34a;">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <div class="stat-info">
                <div class="num">{{ $pasienUmum }}</div>
                <div class="label">Pasien Umum / Mandiri</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #ede9fe; color: #7c3aed;">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div class="stat-info">
                <div class="num">{{ $pasienBpjs }}</div>
                <div class="label">Pasien BPJS / Asuransi</div>
            </div>
        </div>
    </div>

    <!-- MAIN TWO-COLUMN GRID -->
    <div class="grid-layout">
        
        <!-- KOLOM KIRI: FORM PENDAFTARAN PASIEN -->
        <div class="content-box">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
                <div style="width: 36px; height: 36px; border-radius: 10px; background: #e0f2fe; color: #0369a1; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                    <i class="fa-solid fa-clipboard-user"></i>
                </div>
                <div>
                    <h2 style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0;">Form Registrasi Walk-In</h2>
                    <p style="font-size: 11px; color: #64748b; margin: 2px 0 0;">Daftarkan pasien ke antrean poliklinik</p>
                </div>
            </div>

            <!-- TAB SWITCHER: PASIEN LAMA VS PASIEN BARU -->
            <div class="type-switcher">
                <button type="button" class="type-btn active" id="btn-tipe-lama" onclick="switchTipe('lama')">
                    <i class="fa-solid fa-user-check" style="margin-right: 6px;"></i> Pasien Lama (Ada RM)
                </button>
                <button type="button" class="type-btn" id="btn-tipe-baru" onclick="switchTipe('baru')">
                    <i class="fa-solid fa-user-plus" style="margin-right: 6px;"></i> Pasien Baru (Buat RM)
                </button>
            </div>

            <form action="{{ route('loket.store') }}" method="POST" id="form-pendaftaran">
                @csrf
                <input type="hidden" name="tipe_pasien" id="input-tipe-pasien" value="lama">

                <!-- BAGIAN 1A: CARI PASIEN LAMA -->
                <div id="section-pasien-lama" style="display: block; position: relative; z-index: 50;">
                    <div style="position: relative; margin-bottom: 14px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 5px;">
                            Cari Pasien Terdaftar *
                            <span style="font-weight: 400; color: #94a3b8; font-size: 11px;">(Ketik No. RM, NIK, atau Nama)</span>
                        </label>
                        <div style="position: relative;">
                            <input type="text" id="input-cari-pasien" placeholder="Ketik No. RM, NIK, atau Nama Pasien..." autocomplete="off"
                                   style="width: 100%; padding: 10px 42px 10px 38px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 13px;">
                            <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 14px; top: 13px; color: #94a3b8; font-size: 13px;"></i>
                            <button type="button" id="btn-toggle-dropdown" onclick="toggleDropdownPasien()"
                                    style="position: absolute; right: 8px; top: 6px; border: none; background: transparent; color: #64748b; cursor: pointer; padding: 6px 10px; font-size: 13px;" title="Lihat Daftar Pasien">
                                <i class="fa-solid fa-chevron-down" id="icon-toggle-dropdown"></i>
                            </button>
                            <div id="spinner-cari" style="display: none; position: absolute; right: 38px; top: 12px; font-size: 13px; color: #1a4a8d;">
                                <i class="fa-solid fa-circle-notch fa-spin"></i>
                            </div>
                        </div>
                        <div id="autocomplete-box" class="autocomplete-results"></div>
                        <input type="hidden" name="id_pasien" id="selected-id-pasien">
                    </div>

                    <!-- CARD PREVIEW PASIEN TERPILIH -->
                    <div id="card-preview-pasien" style="display: none; background: #f8fbfe; border: 1.5px dashed #93c5fd; border-radius: 12px; padding: 14px; margin-bottom: 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div>
                                <span class="badge-queue" id="preview-rm">RM-0000</span>
                                <strong style="display: block; font-size: 14px; color: #1e293b; margin-top: 6px;" id="preview-nama">-</strong>
                                <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                    NIK: <span id="preview-nik">-</span> • <span id="preview-jk">-</span> • Usia: <span id="preview-usia">-</span>
                                </div>
                                <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                    <i class="fa-solid fa-location-dot" style="margin-right: 4px;"></i> <span id="preview-alamat">-</span>
                                </div>
                            </div>
                            <button type="button" onclick="resetPilihPasien()" style="border: none; background: #fee2e2; color: #dc2626; width: 26px; height: 26px; border-radius: 6px; cursor: pointer;" title="Ganti Pasien">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- BAGIAN 1B: FORM PASIEN BARU -->
                <div id="section-pasien-baru" style="display: none;">
                    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 10px 14px; font-size: 11px; color: #166534; margin-bottom: 14px;">
                        <i class="fa-solid fa-circle-info" style="margin-right: 4px;"></i> Pasien baru akan otomatis dibuatkan Nomor Rekam Medis (RM) baru dan akun portal pasien.
                    </div>

                    <div style="margin-bottom: 12px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 5px;">NIK Pasien (16 Digit) *</label>
                        <input type="text" name="nik" id="input-nik-baru" maxlength="16" placeholder="Contoh: 6301012345670001"
                               style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 13px;">
                    </div>

                    <div style="margin-bottom: 12px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 5px;">Nama Lengkap Pasien *</label>
                        <input type="text" name="nama_pasien" id="input-nama-baru" placeholder="Contoh: Ahmad Fauzan"
                               style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 13px;">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 12px;">
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 5px;">Jenis Kelamin *</label>
                            <select name="jenis_kelamin" id="input-jk-baru" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 13px; background: #fff;">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 5px;">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" id="input-tgl-baru"
                                   style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 13px; background: #fff;">
                        </div>
                    </div>

                    <div style="margin-bottom: 12px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 5px;">Nomor HP / WhatsApp</label>
                        <input type="text" name="no_hp" id="input-hp-baru" placeholder="Contoh: 081234567890"
                               style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 13px;">
                    </div>

                    <div style="margin-bottom: 14px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 5px;">Alamat Domisili</label>
                        <textarea name="alamat" id="input-alamat-baru" rows="2" placeholder="Contoh: Jl. Ahmad Yani No. 12, Banjarmasin"
                                  style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 13px;"></textarea>
                    </div>
                </div>

                <!-- BAGIAN 2: POLIKLINIK, DOKTER & METODE BAYAR -->
                <div style="border-top: 1px solid #e2e8f0; padding-top: 14px; margin-top: 6px;">
                    <div style="margin-bottom: 12px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 5px;">Poliklinik Tujuan *</label>
                        <select name="id_poli" id="select-poli" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 13px; background: #fff;">
                            <option value="" disabled selected>-- Pilih Poliklinik --</option>
                            @foreach ($polis as $poli)
                                <option value="{{ $poli->id_poli }}" data-kode="{{ $poli->kode_poli }}">
                                    {{ $poli->nama_poli }} ({{ $poli->kode_poli }}) - Rp {{ number_format($poli->biaya_konsultasi, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div style="margin-bottom: 12px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 5px;">Dokter Pemeriksa</label>
                        <select name="id_dokter" id="select-dokter" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 13px; background: #fff;">
                            <option value="">-- Dokter Yang Bertugas (Opsional) --</option>
                            @foreach ($dokters as $doc)
                                <option value="{{ $doc->id_pegawai }}" data-poli="{{ $doc->id_poli }}" data-spesialisasi="{{ $doc->spesialisasi }}">
                                    {{ $doc->nama }} {{ $doc->spesialisasi ? '— ' . $doc->spesialisasi : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div style="margin-bottom: 12px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 5px;">Metode Pembayaran *</label>
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px;">
                            <label class="radio-bayar-card" style="display: flex; align-items: center; gap: 6px; padding: 9px 10px; border: 1px solid #cbd5e1; border-radius: 8px; cursor: pointer; font-size: 12px; font-weight: 500;">
                                <input type="radio" name="cara_bayar" value="umum" checked>
                                <span>Umum</span>
                            </label>
                            <label class="radio-bayar-card" style="display: flex; align-items: center; gap: 6px; padding: 9px 10px; border: 1px solid #cbd5e1; border-radius: 8px; cursor: pointer; font-size: 12px; font-weight: 500;">
                                <input type="radio" name="cara_bayar" value="bpjs">
                                <span>BPJS</span>
                            </label>
                            <label class="radio-bayar-card" style="display: flex; align-items: center; gap: 6px; padding: 9px 10px; border: 1px solid #cbd5e1; border-radius: 8px; cursor: pointer; font-size: 12px; font-weight: 500;">
                                <input type="radio" name="cara_bayar" value="asuransi">
                                <span>Asuransi</span>
                            </label>
                        </div>
                    </div>

                    <div style="margin-bottom: 18px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 5px;">Keluhan Utama Pasien</label>
                        <textarea name="keluhan_awal" rows="2" placeholder="Contoh: Demam sejak 3 hari, sakit tenggorokan dan batuk kering..."
                                  style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 13px;"></textarea>
                    </div>

                    <button type="submit" class="simrs-btn-primary" style="width: 100%; padding: 12px 18px; border-radius: 12px; font-size: 13px; font-weight: 600; background: #1a4a8d; color: white; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 12px rgba(26, 74, 141, 0.25);">
                        <i class="fa-solid fa-print"></i> Daftarkan Pasien & Terbitkan Karcis
                    </button>
                </div>
            </form>
        </div>

        <!-- KOLOM KANAN: TABEL PENDAFTARAN HARI INI -->
        <div class="content-box">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 16px;">
                <div>
                    <h2 style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0;">Antrean Pasien Hari Ini</h2>
                    <p style="font-size: 11px; color: #64748b; margin: 2px 0 0;">Daftar nomor antrean poliklinik terbitan loket</p>
                </div>
                <div style="display: flex; gap: 8px;">
                    <form action="{{ route('loket.index') }}" method="GET" style="display: flex; gap: 6px;">
                        <input type="text" name="cari" value="{{ $search }}" placeholder="Cari antrean / nama..."
                               style="padding: 7px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 12px; width: 170px;">
                        <select name="poli" onchange="this.form.submit()" style="padding: 7px 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 12px; background: #fff;">
                            <option value="">Semua Poli</option>
                            @foreach ($polis as $poli)
                                <option value="{{ $poli->id_poli }}" {{ $filterPoli == $poli->id_poli ? 'selected' : '' }}>
                                    {{ $poli->nama_poli }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" style="padding: 7px 12px; border-radius: 8px; border: none; background: #f1f5f9; color: #334155; cursor: pointer; font-size: 12px;">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e2e8f0; color: #475569; text-align: left;">
                            <th style="padding: 10px 12px;">Antrean</th>
                            <th style="padding: 10px 12px;">Pasien & No. RM</th>
                            <th style="padding: 10px 12px;">Poliklinik</th>
                            <th style="padding: 10px 12px;">Bayar</th>
                            <th style="padding: 10px 12px;">Status</th>
                            <th style="padding: 10px 12px; text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pendaftarans as $p)
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px 10px;">
                                    <span class="badge-queue">{{ $p->no_antrian }}</span>
                                    <div style="font-size: 10px; color: #94a3b8; margin-top: 3px;">
                                        {{ $p->tanggal_daftar ? $p->tanggal_daftar->format('H:i') : '-' }} WITA
                                    </div>
                                </td>
                                <td style="padding: 12px 10px;">
                                    <strong style="color: #1e293b; font-size: 13px; display: block;">{{ $p->pasien->nama_pasien ?? '-' }}</strong>
                                    <span style="font-size: 11px; color: #64748b;">
                                        {{ $p->pasien->rm ?? '-' }} • {{ ($p->pasien->jenis_kelamin ?? 'L') === 'P' ? 'P' : 'L' }}
                                    </span>
                                </td>
                                <td style="padding: 12px 10px;">
                                    <span style="font-weight: 600; color: #1a4a8d;">{{ $p->poli->nama_poli ?? '-' }}</span>
                                    <div style="font-size: 10px; color: #64748b; margin-top: 2px;">
                                        {{ $p->dokter->nama ?? 'Dokter Bertugas' }}
                                    </div>
                                </td>
                                <td style="padding: 12px 10px;">
                                    <span style="text-transform: uppercase; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 4px; 
                                          {{ $p->cara_bayar === 'bpjs' ? 'background: #ede9fe; color: #6d28d9;' : ($p->cara_bayar === 'asuransi' ? 'background: #e0f2fe; color: #0369a1;' : 'background: #dcfce7; color: #15803d;') }}">
                                        {{ $p->cara_bayar }}
                                    </span>
                                </td>
                                <td style="padding: 12px 10px;">
                                    @php
                                        $st = $p->status_antrian;
                                        $badgeClass = 'badge-status-menunggu';
                                        $statusText = 'Menunggu Skrining';
                                        if ($st === 'menunggu_dokter') {
                                            $badgeClass = 'badge-status-dokter';
                                            $statusText = 'Ke Dokter';
                                        } elseif ($st === 'sedang_diperiksa') {
                                            $badgeClass = 'badge-status-dokter';
                                            $statusText = 'Diperiksa';
                                        } elseif ($st === 'menunggu_kasir') {
                                            $badgeClass = 'badge-status-menunggu';
                                            $statusText = 'Menunggu Kasir';
                                        } elseif ($st === 'menunggu_obat') {
                                            $badgeClass = 'badge-status-menunggu';
                                            $statusText = 'Farmasi';
                                        } elseif ($st === 'selesai') {
                                            $badgeClass = 'badge-status-selesai';
                                            $statusText = 'Selesai';
                                        } elseif ($st === 'dibatalkan') {
                                            $badgeClass = 'badge-status-batal';
                                            $statusText = 'Dibatalkan';
                                        }
                                    @endphp
                                    <span class="badge-status-loket {{ $badgeClass }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                                <td style="padding: 12px 10px; text-align: right;">
                                    <div style="display: inline-flex; align-items: center; gap: 6px;">
                                        <a href="{{ route('loket.karcis', $p->id_pendaftaran) }}" target="_blank"
                                           class="simrs-btn-white" style="font-size: 11px; padding: 5px 9px; border-radius: 6px; border: 1px solid #cbd5e1; background: #fff; color: #1e293b; text-decoration: none;" title="Cetak Karcis Thermal">
                                            <i class="fa-solid fa-print"></i>
                                        </a>

                                        @if ($p->pasien)
                                            <a href="{{ route('loket.kartu-pasien', $p->id_pasien) }}" target="_blank"
                                               class="simrs-btn-white" style="font-size: 11px; padding: 5px 8px; border-radius: 6px; border: 1px solid #bae6fd; background: #f0f9ff; color: #0284c7; text-decoration: none;" title="Cetak Kartu Pasien (CR80 Barcode)">
                                                <i class="fa-solid fa-id-card"></i>
                                            </a>
                                        @endif

                                        @if ($p->status_antrian === 'menunggu_skrining')
                                            <form action="{{ route('loket.batal', $p->id_pendaftaran) }}" method="POST" onsubmit="return confirm('Batalkan pendaftaran antrean {{ $p->no_antrian }}?')" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" style="font-size: 11px; padding: 5px 8px; border-radius: 6px; border: 1px solid #fecaca; background: #fff; color: #dc2626; cursor: pointer;" title="Batalkan Antrean">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding: 30px; text-align: center; color: #94a3b8;">
                                    <i class="fa-solid fa-user-slash" style="font-size: 24px; margin-bottom: 8px; display: block;"></i>
                                    Belum ada antrean pendaftaran untuk kriteria ini hari ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div style="margin-top: 18px;">
                {{ $pendaftarans->links('partials.pagination') }}
            </div>
        </div>

    </div>

</div>

<!-- SCRIPT PENCARIAN AUTOCOMPLETE & FORM SWITCHER -->
<script>
    function switchTipe(tipe) {
        const btnLama = document.getElementById('btn-tipe-lama');
        const btnBaru = document.getElementById('btn-tipe-baru');
        const secLama = document.getElementById('section-pasien-lama');
        const secBaru = document.getElementById('section-pasien-baru');
        const inputTipe = document.getElementById('input-tipe-pasien');
        const nikBaru = document.getElementById('input-nik-baru');
        const namaBaru = document.getElementById('input-nama-baru');

        if (tipe === 'lama') {
            btnLama.classList.add('active');
            btnBaru.classList.remove('active');
            secLama.style.display = 'block';
            secBaru.style.display = 'none';
            inputTipe.value = 'lama';
            nikBaru.removeAttribute('required');
            namaBaru.removeAttribute('required');
        } else {
            btnBaru.classList.add('active');
            btnLama.classList.remove('active');
            secBaru.style.display = 'block';
            secBaru.style.display = 'block';
            secLama.style.display = 'none';
            inputTipe.value = 'baru';
            nikBaru.setAttribute('required', 'required');
            namaBaru.setAttribute('required', 'required');
            autoBox.style.display = 'none';
        }
    }

    // Live search pasien lama
    const inputCari = document.getElementById('input-cari-pasien');
    const autoBox = document.getElementById('autocomplete-box');
    const spinner = document.getElementById('spinner-cari');
    let debounceTimer = null;
    let daftarPasienCache = [];

    // Tampilkan daftar pasien saat klik atau fokus di kotak pencarian
    inputCari.addEventListener('focus', function() {
        if (!document.getElementById('selected-id-pasien').value) {
            cariPasienAjax(this.value.trim());
        }
    });

    inputCari.addEventListener('click', function() {
        if (autoBox.style.display !== 'block') {
            cariPasienAjax(this.value.trim());
        }
    });

    function toggleDropdownPasien() {
        if (autoBox.style.display === 'block') {
            autoBox.style.display = 'none';
        } else {
            inputCari.focus();
            cariPasienAjax(inputCari.value.trim());
        }
    }

    inputCari.addEventListener('input', function() {
        const val = this.value.trim();
        document.getElementById('selected-id-pasien').value = '';
        document.getElementById('card-preview-pasien').style.display = 'none';

        clearTimeout(debounceTimer);
        spinner.style.display = 'block';

        debounceTimer = setTimeout(() => {
            cariPasienAjax(val);
        }, 200);
    });

    function cariPasienAjax(val) {
        spinner.style.display = 'block';

        // Menggunakan relative path '/loket/cari-pasien' agar selalu cocok dengan host/port aktif
        fetch(`/loket/cari-pasien?q=${encodeURIComponent(val)}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => {
            if (!res.ok) throw new Error('Respon server error: ' + res.status);
            return res.json();
        })
        .then(data => {
            spinner.style.display = 'none';
            daftarPasienCache = data || [];

            if (!data || data.length === 0) {
                autoBox.innerHTML = `
                    <div style="padding: 14px; font-size: 12px; color: #64748b; text-align: center;">
                        <i class="fa-solid fa-circle-question" style="font-size: 18px; color: #94a3b8; display: block; margin-bottom: 4px;"></i>
                        Pasien tidak ditemukan. Silakan pilih tab <strong>"Pasien Baru (Buat RM)"</strong> jika pasien baru pertama kali berobat.
                    </div>
                `;
                autoBox.style.display = 'block';
                return;
            }

            let html = `
                <div class="autocomplete-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <span>${val ? 'Hasil Pencarian Pasien' : 'Daftar Pasien Terdaftar'} (${data.length})</span>
                    <span style="font-size: 10px; color: #94a3b8;">Klik untuk memilih</span>
                </div>
            `;

            data.forEach((p, index) => {
                html += `
                    <div class="autocomplete-item" onmousedown="event.preventDefault(); pilihPasienByIndex(${index})">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <strong class="autocomplete-name">${p.nama_pasien}</strong>
                            <span class="badge-queue" style="font-size: 11px; padding: 2px 7px;">${p.rm}</span>
                        </div>
                        <div class="autocomplete-meta">
                            NIK: <strong>${p.nik}</strong> • ${p.jenis_kelamin === 'P' ? 'Perempuan' : 'Laki-laki'} • Usia: ${p.usia}
                        </div>
                        <div class="autocomplete-address">
                            <i class="fa-solid fa-location-dot" style="margin-right: 3px;"></i> ${p.alamat || '-'}
                        </div>
                    </div>
                `;
            });

            autoBox.innerHTML = html;
            autoBox.style.display = 'block';
        })
        .catch(err => {
            spinner.style.display = 'none';
            console.error('Gagal mengambil data pasien:', err);
        });
    }

    function pilihPasienByIndex(index) {
        const p = daftarPasienCache[index];
        if (!p) return;

        document.getElementById('selected-id-pasien').value = p.id_pasien;
        document.getElementById('preview-rm').textContent = p.rm;
        document.getElementById('preview-nama').textContent = p.nama_pasien;
        document.getElementById('preview-nik').textContent = p.nik;
        document.getElementById('preview-jk').textContent = p.jenis_kelamin === 'P' ? 'Perempuan' : 'Laki-laki';
        document.getElementById('preview-usia').textContent = p.usia;
        document.getElementById('preview-alamat').textContent = p.alamat || '-';

        document.getElementById('card-preview-pasien').style.display = 'block';
        autoBox.style.display = 'none';
        inputCari.value = `${p.nama_pasien} (${p.rm})`;
        inputCari.style.borderColor = '#cbd5e1';
    }

    function resetPilihPasien() {
        document.getElementById('selected-id-pasien').value = '';
        document.getElementById('card-preview-pasien').style.display = 'none';
        inputCari.value = '';
        inputCari.focus();
        cariPasienAjax('');
    }

    // Validasi form saat submit
    document.getElementById('form-pendaftaran').addEventListener('submit', function(e) {
        const tipe = document.getElementById('input-tipe-pasien').value;
        const idPasien = document.getElementById('selected-id-pasien').value;

        if (tipe === 'lama' && !idPasien) {
            e.preventDefault();
            inputCari.style.borderColor = '#ef4444';
            inputCari.focus();
            alert('Silakan pilih salah satu pasien terdaftar dari hasil pencarian terlebih dahulu!');
            cariPasienAjax(inputCari.value.trim());
            return false;
        }
    });

    // Filter Dokter Sesuai Poliklinik yang Dipilih
    const selectPoli = document.getElementById('select-poli');
    const selectDokter = document.getElementById('select-dokter');

    function filterDokterByPoli() {
        if (!selectPoli || !selectDokter) return;
        const selectedPoliId = selectPoli.value;
        const doctorOptions = selectDokter.querySelectorAll('option');

        doctorOptions.forEach(opt => {
            if (!opt.value) {
                opt.style.display = '';
                return;
            }
            const docPoliId = opt.getAttribute('data-poli');
            if (!selectedPoliId || !docPoliId || docPoliId === selectedPoliId) {
                opt.style.display = '';
                opt.disabled = false;
            } else {
                opt.style.display = 'none';
                opt.disabled = true;
                if (selectDokter.value === opt.value) {
                    selectDokter.value = '';
                }
            }
        });
    }

    if (selectPoli) {
        selectPoli.addEventListener('change', filterDokterByPoli);
        filterDokterByPoli();
    }

    // Tutup dropdown jika klik di luar
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#section-pasien-lama')) {
            autoBox.style.display = 'none';
        }
    });
</script>
@endsection
