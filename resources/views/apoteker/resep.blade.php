@extends('layouts.app')
@php $halamanAktif = 'apoteker_resep'; @endphp
@section('title', 'Antrean E-Resep Farmasi - Rumah Sakit Delta')

@push('styles')
<style>
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
    .content-box {
        background: #ffffff;
        border-radius: 20px;
        padding: 24px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        margin-bottom: 24px;
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
    .tab-count {
        font-size: 11px;
        padding: 2px 7px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.2);
    }
    .tab-btn:not(.active) .tab-count {
        background: #e2e8f0;
        color: #475569;
    }
    .resep-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 16px;
        transition: all 0.2s ease;
    }
    .resep-card:hover {
        border-color: #93c5fd;
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.06);
    }
    .resep-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 12px;
        padding-bottom: 14px;
        border-bottom: 1px solid #f1f5f9;
        margin-bottom: 14px;
    }
    .badge-resep {
        font-family: monospace;
        font-weight: 700;
        font-size: 12px;
        padding: 3px 8px;
        border-radius: 6px;
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }
    .badge-status-resep {
        font-size: 11.5px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .badge-menunggu-kasir {
        background: #fffbeb;
        color: #b45309;
        border: 1px solid #fde68a;
    }
    .badge-siap-racik {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }
    .badge-siap-diambil {
        background: #f0fdf4;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }
    .badge-selesai-diserahkan {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }
    .badge-kasir-lunas {
        background: #dcfce7;
        color: #15803d;
        font-weight: 700;
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 6px;
    }
    .badge-kasir-belum {
        background: #fee2e2;
        color: #b91c1c;
        font-weight: 700;
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 6px;
    }
    .obat-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12.5px;
        margin-top: 10px;
    }
    .obat-table th {
        background: #f8fafc;
        color: #475569;
        font-weight: 600;
        padding: 8px 12px;
        text-align: left;
        border-bottom: 1px solid #e2e8f0;
        font-size: 11.5px;
    }
    .obat-table td {
        padding: 10px 12px;
        border-bottom: 1px solid #f1f5f9;
        color: #1e293b;
    }

    /* Dark Mode Overrides */
    body.dark-mode .resep-card {
        background: #121927 !important;
        border-color: #1e293b !important;
    }
    body.dark-mode .resep-card:hover {
        border-color: #3b82f6 !important;
    }
    body.dark-mode .resep-header {
        border-bottom-color: #1e293b !important;
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
    body.dark-mode .obat-table th {
        background: #0b0f19 !important;
        color: #94a3b8 !important;
        border-bottom-color: #1e293b !important;
    }
    body.dark-mode .obat-table td {
        border-bottom-color: #1e293b !important;
        color: #f1f5f9 !important;
    }
    body.dark-mode .badge-resep {
        background: rgba(37, 99, 235, 0.2) !important;
        color: #93c5fd !important;
        border-color: #1d4ed8 !important;
    }
</style>
@endpush

@section('content')
<div class="content-body" style="padding-bottom: 40px;">

    <!-- HEADER TITLE & FILTER TANGGAL -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 14px;">
        <div>
            <h1 style="font-size: 22px; font-weight: 700; color: #1e293b; margin: 0;">Antrean E-Resep Farmasi</h1>
            <p style="font-size: 13px; color: #64748b; margin: 4px 0 0;">
                Validasi resep dokter poliklinik, peracikan obat, dan penyerahan obat ke pasien lunas.
            </p>
        </div>

        <!-- FORM FILTER TANGGAL & SEARCH -->
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <form action="{{ route('apoteker.resep') }}" method="GET" style="display: flex; gap: 8px;">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()"
                       style="padding: 8px 12px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 12.5px; outline: none; background: #fff; cursor: pointer;">
                
                <div style="position: relative;">
                    <input type="text" name="q" value="{{ $q }}" placeholder="Cari Resep / Pasien / RM..."
                           style="padding: 8px 12px 8px 34px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 12.5px; width: 220px;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 11px; color: #94a3b8; font-size: 12px;"></i>
                </div>

                @if(!empty($q))
                    <a href="{{ route('apoteker.resep', ['tanggal' => $tanggal, 'tab' => $tab]) }}" class="simrs-btn-white" style="padding: 8px 12px; font-size: 12px;" title="Reset Pencarian">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </form>

            <a href="{{ route('apoteker.obat') }}" style="background: #0284c7; color: white; padding: 8px 14px; border-radius: 10px; font-size: 12px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-boxes-stacked"></i> Kelola Obat
            </a>
        </div>
    </div>

    <!-- FLASH MESSAGES -->
    @if(session('success'))
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; font-size: 13px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-circle-check" style="font-size: 18px; color: #10b981;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; font-size: 13px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-triangle-exclamation" style="font-size: 18px; color: #ef4444;"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- 5 STAT CARDS FARMASI -->
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: #eff6ff; color: #2563eb;">
                <i class="fa-solid fa-prescription-bottle-medical"></i>
            </div>
            <div class="stat-info">
                <div class="num">{{ $totalResepHariIni }}</div>
                <div class="label">Total E-Resep Hari Ini</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #fffbeb; color: #d97706;">
                <i class="fa-solid fa-cash-register"></i>
            </div>
            <div class="stat-info">
                <div class="num">{{ $menungguKasirCount }}</div>
                <div class="label">Menunggu Kasir</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #f0f9ff; color: #0284c7;">
                <i class="fa-solid fa-mortar-pestle"></i>
            </div>
            <div class="stat-info">
                <div class="num">{{ $siapRacikCount }}</div>
                <div class="label">Siap Diracing</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #f0fdf4; color: #15803d;">
                <i class="fa-solid fa-hand-holding-medical"></i>
            </div>
            <div class="stat-info">
                <div class="num">{{ $siapDiambilCount }}</div>
                <div class="label">Siap Diserahkan</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #fef2f2; color: #dc2626;">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="stat-info">
                <div class="num">{{ $stokMenipisCount }}</div>
                <div class="label">Obat Stok Menipis</div>
            </div>
        </div>
    </div>

    <!-- FILTER TABS ANTRIAN RESEP -->
    <div class="filter-tabs">
        <a href="{{ route('apoteker.resep', ['tanggal' => $tanggal, 'tab' => 'semua', 'q' => $q]) }}"
           class="tab-btn {{ $tab === 'semua' ? 'active' : '' }}">
            <i class="fa-solid fa-list-check"></i> Semua Resep
            <span class="tab-count">{{ $totalResepHariIni }}</span>
        </a>
        <a href="{{ route('apoteker.resep', ['tanggal' => $tanggal, 'tab' => 'siap_racik', 'q' => $q]) }}"
           class="tab-btn {{ $tab === 'siap_racik' ? 'active' : '' }}">
            <i class="fa-solid fa-mortar-pestle"></i> Siap Diracing
            <span class="tab-count">{{ $siapRacikCount }}</span>
        </a>
        <a href="{{ route('apoteker.resep', ['tanggal' => $tanggal, 'tab' => 'siap_diambil', 'q' => $q]) }}"
           class="tab-btn {{ $tab === 'siap_diambil' ? 'active' : '' }}">
            <i class="fa-solid fa-bell"></i> Siap Diambil
            <span class="tab-count">{{ $siapDiambilCount }}</span>
        </a>
        <a href="{{ route('apoteker.resep', ['tanggal' => $tanggal, 'tab' => 'menunggu_kasir', 'q' => $q]) }}"
           class="tab-btn {{ $tab === 'menunggu_kasir' ? 'active' : '' }}">
            <i class="fa-solid fa-clock"></i> Belum Lunas Kasir
            <span class="tab-count">{{ $menungguKasirCount }}</span>
        </a>
        <a href="{{ route('apoteker.resep', ['tanggal' => $tanggal, 'tab' => 'sudah_diserahkan', 'q' => $q]) }}"
           class="tab-btn {{ $tab === 'sudah_diserahkan' ? 'active' : '' }}">
            <i class="fa-solid fa-circle-check"></i> Sudah Diserahkan
            <span class="tab-count">{{ $sudahDiserahkanCount }}</span>
        </a>
    </div>

    <!-- DAFTAR KARTU E-RESEP -->
    @forelse($resepList as $resep)
        @php
            $pasien = $resep->pendaftaran?->pasien;
            $poli = $resep->pendaftaran?->poli;
            $transaksi = $resep->pendaftaran?->transaksi;
            $isLunas = $transaksi && $transaksi->status_pembayaran === 'lunas';
        @endphp

        <div class="resep-card">
            <!-- HEADER RESEP -->
            <div class="resep-header">
                <div>
                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                        <span class="badge-resep">{{ $resep->no_resep }}</span>
                        @if($resep->pendaftaran?->no_antrian)
                            <span class="badge-queue" style="font-size: 11px;">{{ $resep->pendaftaran->no_antrian }}</span>
                        @endif
                        <span style="font-size: 12px; color: #64748b;">
                            <i class="fa-regular fa-clock" style="margin-right: 4px;"></i> {{ $resep->created_at->format('H:i') }} WITA
                        </span>
                    </div>

                    <div style="display: flex; align-items: center; gap: 8px; margin-top: 6px; flex-wrap: wrap;">
                        <strong style="font-size: 16px; color: #0f172a;">{{ $pasien->nama_pasien ?? 'Pasien Umum' }}</strong>
                        <span style="font-size: 12px; color: #64748b;">({{ $pasien->rm ?? '-' }})</span>
                        <span style="font-size: 12px; color: #64748b;">• {{ ($pasien->jenis_kelamin ?? 'L') === 'P' ? 'Perempuan' : 'Laki-laki' }}</span>
                        <span style="font-size: 12px; color: #64748b;">• {{ $poli->nama_poli ?? 'Poliklinik' }}</span>
                        <span style="font-size: 12px; color: #64748b;">• Dokter: {{ $resep->dokter->nama ?? 'dr. Umum' }}</span>
                    </div>
                </div>

                <!-- STATUS BADGES -->
                <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                    <!-- Badge Kasir -->
                    @if($isLunas)
                        <span class="badge-kasir-lunas" title="Pasien telah menyelesaikan pembayaran di Kasir">
                            <i class="fa-solid fa-circle-check"></i> Kasir: LUNAS
                        </span>
                    @else
                        <span class="badge-kasir-belum" title="Pasien belum membayar di Kasir">
                            <i class="fa-solid fa-clock"></i> Kasir: BELUM LUNAS
                        </span>
                    @endif

                    <!-- Badge Status Resep -->
                    @if($resep->status_resep === 'menunggu_pembayaran')
                        <span class="badge-status-resep badge-menunggu-kasir">
                            <i class="fa-solid fa-hourglass-half"></i> Menunggu Kasir
                        </span>
                    @elseif($resep->status_resep === 'siap_racik')
                        <span class="badge-status-resep badge-siap-racik">
                            <i class="fa-solid fa-mortar-pestle"></i> Siap Diracing
                        </span>
                    @elseif($resep->status_resep === 'siap_diambil')
                        <span class="badge-status-resep badge-siap-diambil">
                            <i class="fa-solid fa-bell"></i> Siap Diambil
                        </span>
                    @elseif($resep->status_resep === 'sudah_diserahkan')
                        <span class="badge-status-resep badge-selesai-diserahkan">
                            <i class="fa-solid fa-circle-check"></i> Diserahkan
                        </span>
                    @endif
                </div>
            </div>

            <!-- TABEL OBAT DALAM RESEP -->
            <div style="overflow-x: auto;">
                <table class="obat-table">
                    <thead>
                        <tr>
                            <th style="width: 28%;">NAMA OBAT & KODE</th>
                            <th style="width: 15%;">JUMLAH</th>
                            <th style="width: 32%;">ATURAN PAKAI / DOSIS</th>
                            <th style="width: 15%;">STOK GUDANG</th>
                            <th style="width: 10%; text-align: right;">SUBTOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($resep->details as $item)
                            @php
                                $stokCukup = $item->obat && $item->obat->stok >= $item->jumlah;
                            @endphp
                            <tr>
                                <td>
                                    <strong style="display: block;">{{ $item->obat->nama_obat ?? 'Item Obat' }}</strong>
                                    <span style="font-size: 11px; color: #64748b; font-family: monospace;">{{ $item->obat->kode_obat ?? '-' }}</span>
                                </td>
                                <td>
                                    <span style="font-weight: 700; font-size: 13px;">{{ $item->jumlah }}</span> {{ $item->obat->satuan ?? 'satuan' }}
                                </td>
                                <td>
                                    <span style="color: #0369a1; font-weight: 600; background: #f0f9ff; padding: 3px 8px; border-radius: 6px; border: 1px solid #e0f2fe; display: inline-block;">
                                        <i class="fa-solid fa-clock-rotate-left" style="font-size: 10px; margin-right: 4px;"></i> {{ $item->aturan_pakai ?? 'Sesuai anjuran' }}
                                    </span>
                                </td>
                                <td>
                                    @if($stokCukup)
                                        <span style="color: #15803d; font-size: 11.5px; font-weight: 600;">
                                            <i class="fa-solid fa-check"></i> {{ $item->obat->stok }} {{ $item->obat->satuan }}
                                        </span>
                                    @else
                                        <span style="color: #b91c1c; font-size: 11.5px; font-weight: 700;">
                                            <i class="fa-solid fa-triangle-exclamation"></i> Sisa {{ $item->obat->stok ?? 0 }} (Kurang!)
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align: right; font-weight: 600;">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- CATATAN DOKTER & ACTION BUTTONS -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px; flex-wrap: wrap; gap: 12px;">
                <div style="font-size: 12px; color: #64748b; max-width: 500px;">
                    @if($resep->catatan_dokter)
                        <i class="fa-solid fa-comment-medical" style="color: #0284c7; margin-right: 4px;"></i>
                        <strong>Catatan Dokter:</strong> "{{ $resep->catatan_dokter }}"
                    @endif
                </div>

                <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                    <!-- TOTAL BIAYA OBAT -->
                    <div style="margin-right: 12px; text-align: right;">
                        <span style="font-size: 11px; color: #64748b; display: block;">Total Resep Obat:</span>
                        <strong style="font-size: 15px; color: #059669;">Rp {{ number_format($resep->total_harga_obat, 0, ',', '.') }}</strong>
                    </div>

                    <!-- TOMBOL CETAK ETIKET -->
                    <a href="{{ route('apoteker.etiket', $resep->id_resep) }}" target="_blank"
                       style="background: #ffffff; color: #1e293b; border: 1px solid #cbd5e1; padding: 8px 14px; border-radius: 10px; font-size: 12px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: 0.2s;"
                       title="Cetak Stiker Etiket Farmasi">
                        <i class="fa-solid fa-print"></i> Cetak Etiket
                    </a>

                    <!-- AKSI 1: TANDAI SELESAI RACIK -->
                    @if($resep->status_resep === 'siap_racik')
                        <form action="{{ route('apoteker.racik', $resep->id_resep) }}" method="POST" style="margin: 0;">
                            @csrf
                            <button type="submit" style="background: #0284c7; color: white; border: none; padding: 8px 16px; border-radius: 10px; font-size: 12px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 10px rgba(2, 132, 199, 0.25);">
                                <i class="fa-solid fa-mortar-pestle"></i> Selesai Racik (Siap Diambil)
                            </button>
                        </form>
                    @endif

                    <!-- AKSI 2: SERAHKAN OBAT KE PASIEN -->
                    @if($resep->status_resep === 'siap_diambil' || ($resep->status_resep === 'siap_racik' && $isLunas))
                        <form action="{{ route('apoteker.serahkan', $resep->id_resep) }}" method="POST" style="margin: 0;"
                              onsubmit="return confirm('Apakah Anda yakin ingin menyerahkan obat resep ini kepada pasien {{ $pasien->nama_pasien ?? '' }}? Stok obat akan otomatis dipotong.');">
                            @csrf
                            <button type="submit" style="background: #059669; color: white; border: none; padding: 8px 16px; border-radius: 10px; font-size: 12px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 10px rgba(5, 150, 105, 0.25);">
                                <i class="fa-solid fa-hand-holding-medical"></i> Serahkan Obat & Selesai
                            </button>
                        </form>
                    @elseif($resep->status_resep === 'menunggu_pembayaran')
                        <button type="button" disabled style="background: #f1f5f9; color: #94a3b8; border: 1px solid #e2e8f0; padding: 8px 14px; border-radius: 10px; font-size: 12px; font-weight: 600; cursor: not-allowed;" title="Pasien harus melunasi tagihan di loket kasir terlebih dahulu">
                            <i class="fa-solid fa-lock" style="margin-right: 4px;"></i> Menunggu Kasir
                        </button>
                    @elseif($resep->status_resep === 'sudah_diserahkan')
                        <span style="font-size: 12px; color: #047857; font-weight: 600; background: #ecfdf5; padding: 6px 12px; border-radius: 8px; border: 1px solid #a7f3d0; display: inline-flex; align-items: center; gap: 5px;">
                            <i class="fa-solid fa-circle-check"></i> Telah Diserahkan
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="content-box" style="text-align: center; padding: 50px 20px;">
            <div style="width: 60px; height: 60px; border-radius: 50%; background: #eff6ff; color: #3b82f6; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 16px;">
                <i class="fa-solid fa-prescription-bottle-medical"></i>
            </div>
            <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 0 0 6px;">Tidak Ada Resep Ditemukan</h3>
            <p style="font-size: 13px; color: #64748b; margin: 0;">
                Tidak ada antrean resep yang sesuai dengan filter atau tanggal yang Anda pilih.
            </p>
        </div>
    @endforelse

</div>
@endsection
