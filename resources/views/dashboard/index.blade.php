@extends('layouts.app')
@php $halamanAktif = 'dashboard'; @endphp
@section('title', 'Dashboard Kasir - Rumah Sakit Delta')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/riwayat.css') }}">
    <style>
        .cards-grid-four {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 22px;
            display: flex;
            align-items: flex-start;
            gap: 18px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.05);
        }

        .card-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .icon-blue { background: #eff6ff; color: #2563eb; }
        .icon-green { background: #ecfdf5; color: #059669; }
        .icon-orange { background: #fff7ed; color: #ea580c; }

        .card-info { flex: 1; }
        .card-label { font-size: 13px; color: #64748b; margin: 0 0 4px 0; font-weight: 500; }
        .card-num { font-size: 24px; font-weight: 700; color: #0f172a; margin: 0; }
        .card-amount { font-size: 24px; font-weight: 700; color: #059669; margin: 0; }
        .sub-text { font-size: 12px; font-weight: 500; color: #94a3b8; }

        .stat-badge-mtd {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11.5px;
            font-weight: 500;
            color: #64748b;
            margin-top: 8px;
            padding: 3px 8px;
            background: #f8fafc;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }

        .stat-badge-mtd i {
            font-size: 10px;
            color: #059669;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 768px) {
            .summary-grid { grid-template-columns: 1fr; }
        }

        .summary-box-card {
            background: #f8fafc;
            padding: 18px 22px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13.5px;
        }

        .summary-row:last-child { border-bottom: none; }
        .summary-row .label { color: #64748b; }
        .summary-row .value { color: #0f172a; }

        .btn-today-shortcut {
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 8px 14px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }
        .btn-today-shortcut:hover {
            background: #2563eb;
            color: #ffffff;
        }

        .btn-bayar-antrian {
            background: #2563eb;
            color: #ffffff;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .btn-bayar-antrian:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.3);
        }

        /* Mode Gelap */
        body.dark-mode .stat-card {
            background: #121927;
            border-color: #1e293b;
        }
        body.dark-mode .card-num { color: #f1f5f9; }
        body.dark-mode .card-label { color: #94a3b8; }
        body.dark-mode .stat-badge-mtd {
            background: #0b0f19;
            border-color: #1e293b;
            color: #94a3b8;
        }
        body.dark-mode .stat-badge-mtd i { color: #34d399; }
        body.dark-mode .summary-box-card {
            background: #0b0f19;
            border-color: #1e293b;
        }
        body.dark-mode .summary-row { border-bottom-color: #1e293b; }
        body.dark-mode .summary-row .label { color: #94a3b8; }
        body.dark-mode .summary-row .value { color: #f1f5f9; }
        body.dark-mode .icon-blue { background: rgba(37, 99, 235, 0.15); color: #60a5fa; }
        body.dark-mode .icon-green { background: rgba(5, 150, 105, 0.15); color: #34d399; }
        body.dark-mode .icon-orange { background: rgba(234, 88, 12, 0.15); color: #fb923c; }
        body.dark-mode .btn-today-shortcut {
            background: #1e293b;
            border-color: #334155;
            color: #93c5fd;
        }
        body.dark-mode .btn-today-shortcut:hover {
            background: #2563eb;
            color: #ffffff;
        }
    </style>
@endpush

@section('content')
<div class="page-header-text">
    <h2>Dashboard Kasir</h2>
    <p>Ringkasan transaksi pembayaran, status loket kasir, dan pendapatan harian Rumah Sakit Delta.</p>
</div>

{{-- Baris Filter Tanggal Dashboard --}}
<form method="GET" action="{{ route('dashboard') }}" class="filter-container" style="padding: 16px 22px; margin-bottom: 25px; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px;">
    <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
        <div class="form-group" style="margin: 0;">
            <label for="filter-tanggal" style="font-size: 13px; font-weight: 600;">Pilih Tanggal Dashboard</label>
            <div class="input-with-icon" style="max-width: 240px;">
                <input type="date" id="filter-tanggal" name="tanggal" value="{{ $tanggalDipilih }}" class="form-control" onchange="this.form.submit()">
            </div>
        </div>
        @if ($tanggalDipilih !== today()->toDateString())
            <a href="{{ route('dashboard') }}" class="btn-today-shortcut" title="Kembali ke transaksi hari ini">
                <i class="fa-solid fa-calendar-day"></i> Hari Ini
            </a>
        @endif
    </div>
    <div style="display: flex; gap: 10px;">
        <button type="submit" class="btn-filter-submit" style="padding: 10px 20px;">
            <i class="fa-solid fa-rotate"></i> Refresh Data
        </button>
    </div>
</form>

{{-- Kartu Statistik Utama dengan Indikator Harian & Bulanan (MTD) --}}
<div class="cards-grid-four">
    {{-- Card 1: Total Transaksi --}}
    <div class="stat-card">
        <div class="card-icon icon-blue">
            <i class="fa-solid fa-cash-register"></i>
        </div>
        <div class="card-info">
            <p class="card-label">Total Transaksi</p>
            <h3 class="card-num">{{ $totalTransaksi }} <span class="sub-text">Hari Ini</span></h3>
            <div class="stat-badge-mtd" title="Akumulasi seluruh transaksi di bulan berjalan">
                <i class="fa-solid fa-arrow-trend-up"></i> Bulan Ini: <strong>{{ $totalTransaksiBulanIni }}</strong> kuitansi
            </div>
        </div>
    </div>

    {{-- Card 2: Total Pendapatan --}}
    <div class="stat-card">
        <div class="card-icon icon-green">
            <i class="fa-solid fa-credit-card"></i>
        </div>
        <div class="card-info">
            <p class="card-label">Total Pendapatan</p>
            <h3 class="card-amount">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
            <div class="stat-badge-mtd" title="Total pendapatan berjalan bulan {{ $namaBulanTahun }}">
                <i class="fa-solid fa-calendar-check"></i> {{ $namaBulanTahun }}: <strong>Rp {{ number_format($totalPendapatanBulanIni, 0, ',', '.') }}</strong>
            </div>
        </div>
    </div>

    {{-- Card 3: Transaksi Berhasil & Antrean --}}
    <div class="stat-card">
        <div class="card-icon icon-orange">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div class="card-info">
            <p class="card-label">Transaksi Berhasil</p>
            <h3 class="card-num">
                {{ $transaksiLunas }}{{ $totalPasienAntri > 0 ? '/' . $totalPasienAntri : '' }}
                <span class="sub-text">Pasien Lunas</span>
            </h3>
            <div class="stat-badge-mtd" style="color: #ea580c;" title="Jumlah pasien yang saat ini masih menunggu di loket kasir">
                <i class="fa-solid fa-user-clock" style="color: #ea580c;"></i> Sisa antrean: <strong>{{ $sisaAntreanKasir }}</strong> pasien
            </div>
        </div>
    </div>
</div>

{{-- DAFTAR PASIEN BELUM BAYAR (AKSI LANGSUNG KE KASIR) --}}
<div class="section-container" style="padding: 22px 25px; margin-bottom: 25px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; flex-wrap:wrap; gap:12px;">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:#fff7ed; color:#ea580c; display:flex; align-items:center; justify-content:center; font-size:18px;">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div>
                <h2 class="section-title" style="margin:0; font-size:1.15rem; font-weight:700;">
                    Antrean Pasien Menunggu Pembayaran
                </h2>
                <p style="margin:2px 0 0 0; font-size:12.5px; color:#64748b;">
                    Pasien yang telah selesai diperiksa dokter dan menunggu proses pembayaran loket kasir.
                </p>
            </div>
        </div>
        <span class="badge" style="background:#ffedd5; color:#c2410c; font-weight:700; padding:6px 14px; font-size:12px; border-radius:20px;">
            {{ $sisaAntreanKasir }} Pasien Siap Bayar
        </span>
    </div>

    <div style="overflow-x:auto;">
        <table class="kasir-table">
            <thead>
                <tr>
                    <th class="text-left" style="width: 130px;">NO. ANTRIAN</th>
                    <th class="text-left" style="min-width: 170px;">NAMA PASIEN</th>
                    <th class="text-left" style="width: 150px;">POLIKLINIK</th>
                    <th class="text-left" style="min-width: 180px;">RESEP & TINDAKAN</th>
                    <th class="text-center" style="width: 130px;">STATUS</th>
                    <th class="text-center" style="width: 150px;">AKSI KASIR</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pasienMenungguKasir as $antri)
                    @php
                        $namaP = $antri->pasien?->nama_pasien ?? 'Pasien Umum';
                        $rmP = $antri->pasien?->rm ?? '-';
                        $poliP = $antri->poli?->nama_poli ?? 'Poli Umum';
                        $resep = $antri->resep;
                        $obatList = [];
                        if ($resep && $resep->details) {
                            foreach ($resep->details as $d) {
                                $obatList[] = ($d->obat?->nama_obat ?? 'Obat') . ' (' . $d->jumlah . ')';
                            }
                        }

                        $tindakanList = [];
                        if ($antri->rekamMedis && $antri->rekamMedis->tindakanMedis) {
                            foreach ($antri->rekamMedis->tindakanMedis as $tm) {
                                if ($tm->layanan) {
                                    $tindakanList[] = $tm->layanan->nama_layanan;
                                }
                            }
                        }

                        $itemsDesc = [];
                        if (count($tindakanList) > 0) {
                            $itemsDesc[] = implode(', ', $tindakanList);
                        }
                        if (count($obatList) > 0) {
                            $itemsDesc[] = implode(', ', $obatList);
                        }
                        $displayDesc = count($itemsDesc) > 0 ? implode(' • ', $itemsDesc) : 'Pemeriksaan Dokter Poli';
                    @endphp
                    <tr>
                        <td class="text-left font-bold" style="color:#2563eb; font-size:14px;">
                            {{ $antri->no_antrian }}
                        </td>
                        <td class="text-left">
                            <span class="cell-pasien-nama">{{ $namaP }}</span>
                            <div style="font-size:11px; color:#64748b;">{{ $rmP }}</div>
                        </td>
                        <td class="text-left text-muted" style="font-size:12.5px;">
                            {{ $poliP }}
                        </td>
                        <td class="text-left" style="font-size:12px; color:#475569;">
                            <i class="fa-solid fa-notes-medical" style="color:#059669; margin-right:4px;"></i>
                            {{ Str::limit($displayDesc, 55) }}
                        </td>
                        <td class="text-center">
                            <span class="badge" style="background:#fef3c7; color:#b45309; font-weight:600;">
                                Menunggu Kasir
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('antrian', ['pilih' => $antri->id_pendaftaran]) }}" class="btn-bayar-antrian" title="Lanjut proses pembayaran kasir untuk pasien ini">
                                Layani Kasir <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted" style="padding: 30px 0;">
                            <i class="fa-solid fa-circle-check" style="font-size: 32px; margin-bottom: 8px; color: #10b981; display: block;"></i>
                            Semua antrean kasir saat ini telah diselesaikan. Tidak ada pasien yang menunggu pembayaran.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Tabel Transaksi Terbaru Terfilter --}}
<div class="section-container" style="padding: 22px 25px; margin-bottom: 25px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; flex-wrap:wrap; gap:10px;">
        <h2 class="section-title" style="margin:0; font-size:1.1rem; font-weight:700;">
            Transaksi Tanggal <span style="color:#2563eb;">{{ date('d F Y', strtotime($tanggalDipilih)) }}</span>
        </h2>
        <span style="font-size:13px; color:#64748b;">{{ $totalTransaksi }} data tercatat di sistem</span>
    </div>

    <div style="overflow-x:auto;">
        <table class="kasir-table">
            <thead>
                <tr>
                    <th class="text-left" style="width: 170px;">NO. TRANSAKSI</th>
                    <th class="text-left" style="width: 130px;">WAKTU</th>
                    <th class="text-left" style="min-width: 160px;">PASIEN & POLI</th>
                    <th class="text-right" style="width: 130px;">RESEP OBAT</th>
                    <th class="text-right" style="width: 140px;">TINDAKAN & JASA</th>
                    <th class="text-right" style="width: 150px;">TOTAL BAYAR</th>
                    <th class="text-center" style="width: 110px;">STATUS</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transaksiList as $row)
                    @php
                        $noTrx = $row->no_invoice ?? ('TRX-' . ($row->created_at ? $row->created_at->format('Ymd') : date('Ymd')) . '-' . str_pad($row->id_transaksi, 4, '0', STR_PAD_LEFT));
                        $waktuFormat = $row->waktu_pembayaran 
                            ? $row->waktu_pembayaran->format('d M Y, H:i') 
                            : ($row->created_at ? $row->created_at->format('d M Y, H:i') : '-');
                        $namaPasien = $row->pendaftaran?->pasien?->nama_pasien ?? 'Pasien Umum';
                        $namaPoli = $row->pendaftaran?->poli?->nama_poli ?? 'Poli Umum';
                        $tindakanDanJasa = (float)($row->total_layanan ?? 0) + (float)($row->jasa_perawat ?? 0);
                    @endphp
                    <tr>
                        <td class="text-left font-bold" style="color: #2563eb;">{{ $noTrx }}</td>
                        <td class="text-left text-muted" style="font-size:12px;">{{ $waktuFormat }}</td>
                        <td class="text-left">
                            <span class="cell-pasien-nama">{{ $namaPasien }}</span>
                            <div style="font-size:11px; color:#64748b;">{{ $row->pendaftaran?->pasien?->rm ?? '-' }} &bull; {{ $namaPoli }}</div>
                        </td>
                        <td class="text-right">Rp {{ number_format($row->total_resep ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($tindakanDanJasa, 0, ',', '.') }}</td>
                        <td class="text-right font-bold" style="color: #059669;">Rp {{ number_format($row->total_bayar, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <span class="badge" style="background: #dcfce7; color: #15803d; font-weight:600;">
                                {{ ucfirst($row->status_pembayaran ?? 'Lunas') }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted" style="padding: 40px 0;">
                            <i class="fa-solid fa-receipt" style="font-size: 36px; margin-bottom: 12px; color: #cbd5e1; display: block;"></i>
                            Tidak ada data transaksi pembayaran pada tanggal {{ date('d M Y', strtotime($tanggalDipilih)) }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Ringkasan Pendapatan & Antrean --}}
<div class="section-container" style="padding: 22px 25px;">
    <div class="section-header" style="margin-bottom:15px;">
        <h2 class="section-title" style="margin:0; font-size:1.1rem; font-weight:700;">Ringkasan Pendapatan & Antrean</h2>
    </div>
    <div class="summary-grid">
        <div class="summary-box-card summary-left">
            <div class="summary-row">
                <span class="label">Total Pendapatan Terfilter</span>
                <span class="value font-bold" style="color:#059669; font-size:15px;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</span>
            </div>
            <div class="summary-row">
                <span class="label">Penerimaan Metode Tunai</span>
                <span class="value font-bold">Rp {{ number_format($totalTunai, 0, ',', '.') }}</span>
            </div>
            <div class="summary-row">
                <span class="label">Penerimaan QRIS / Non-Tunai</span>
                <span class="value font-bold">Rp {{ number_format($totalNonTunai, 0, ',', '.') }}</span>
            </div>
            <div class="summary-row">
                <span class="label">Akumulasi {{ $namaBulanTahun }}</span>
                <span class="value font-bold" style="color:#059669;">Rp {{ number_format($totalPendapatanBulanIni, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="summary-box-card summary-right">
            <div class="summary-row">
                <span class="label">Jumlah Transaksi Terproses</span>
                <span class="value font-bold">{{ $transaksiLunas }} Transaksi</span>
            </div> 
            <div class="summary-row">
                <span class="label">Total Antrean Terbayar Hari Ini</span>
                <span class="value font-bold" style="color:#2563eb;">{{ $transaksiLunas }} Pasien</span>
            </div>
            <div class="summary-row">
                <span class="label">Total Transaksi Bulan Ini</span>
                <span class="value font-bold" style="color:#2563eb;">{{ $totalTransaksiBulanIni }} Kuitansi</span>
            </div>
            <div class="summary-row">
                <span class="label">Antrean Menunggu Kasir Saat Ini</span>
                <span class="value font-bold" style="color:#ea580c;">{{ $sisaAntreanKasir }} Pasien</span>
            </div>
        </div>
    </div>
</div>
@endsection
