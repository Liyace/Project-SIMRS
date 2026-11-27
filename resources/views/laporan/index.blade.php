@extends('layouts.app')
@php $halamanAktif = 'laporan'; @endphp
@section('title', 'Laporan Kasir - Rumah Sakit Delta')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/riwayat.css') }}">
    <style>
        .cards-grid-laporan {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 25px;
        }

        @media (max-width: 1280px) {
            .cards-grid-laporan {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 680px) {
            .cards-grid-laporan {
                grid-template-columns: 1fr;
            }
        }

        .period-tabs {
            display: flex;
            width: 100%;
            background: #f1f5f9;
            padding: 3px;
            border-radius: 10px;
            gap: 3px;
            margin-bottom: 10px;
            border: 1px solid #e2e8f0;
        }

        .tab-item {
            flex: 1;
            justify-content: center;
            padding: 6px 10px;
            border-radius: 7px;
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .tab-item:hover {
            color: #2563eb;
        }

        .tab-item.active {
            background: #ffffff;
            color: #2563eb;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06);
        }

        .btn-export {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }

        .btn-export-pdf {
            background: #2563eb;
            color: #ffffff;
        }
        .btn-export-pdf:hover { background: #1d4ed8; }

        .btn-export-excel {
            background: #059669;
            color: #ffffff;
        }
        .btn-export-excel:hover { background: #047857; }

        /* Mode Gelap */
        body.dark-mode .period-tabs {
            background: #0b0f19;
            border-color: #1e293b;
        }

        body.dark-mode .tab-item {
            color: #94a3b8;
        }

        body.dark-mode .tab-item:hover {
            color: #60a5fa;
        }

        body.dark-mode .tab-item.active {
            background: #1e293b;
            color: #93c5fd;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }
    </style>
@endpush

@section('content')
<div class="page-header-text">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
        <div>
            <h2>Laporan Kasir</h2>
            <p>Laporan Keuangan Kasir & rekapitulasi penerimaan harian, bulanan, dan tahunan Rumah Sakit Delta.</p>
        </div>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            {{-- Tombol Ekspor Excel --}}
            <a href="{{ route('laporan.excel', ['periode' => $periode, 'tanggal' => $tanggal, 'bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn-export btn-export-excel" title="Unduh Spreadsheet Excel">
                <i class="fa-solid fa-file-excel"></i> Ekspor Excel
            </a>
            {{-- Tombol Cetak / PDF --}}
            <a href="{{ route('laporan.cetak', ['periode' => $periode, 'tanggal' => $tanggal, 'bulan' => $bulan, 'tahun' => $tahun]) }}" target="_blank" class="btn-export btn-export-pdf" title="Cetak Dokumen Resmi Berkop RS">
                <i class="fa-solid fa-print"></i> Cetak / PDF
            </a>
        </div>
    </div>
</div>

{{-- Baris Filter Periode & Stat Cards (4 Kolom Sejajar) --}}
<div class="cards-grid-laporan">
    
    {{-- Card 1: Filter Periode dengan Tab Switcher --}}
    <div class="filter-container" style="padding:16px; margin:0; flex-direction:column; justify-content:center; align-items:stretch; gap:8px;">
        {{-- Tab Switcher --}}
        <div class="period-tabs">
            <a href="{{ route('laporan', ['periode' => 'harian', 'tanggal' => $tanggal]) }}" class="tab-item {{ $periode === 'harian' ? 'active' : '' }}">
                <i class="fa-solid fa-calendar-day"></i> Harian
            </a>
            <a href="{{ route('laporan', ['periode' => 'bulanan', 'bulan' => $bulan, 'tahun' => $tahun]) }}" class="tab-item {{ $periode === 'bulanan' ? 'active' : '' }}">
                <i class="fa-solid fa-calendar-week"></i> Bulanan
            </a>
            <a href="{{ route('laporan', ['periode' => 'tahunan', 'tahun' => $tahun]) }}" class="tab-item {{ $periode === 'tahunan' ? 'active' : '' }}">
                <i class="fa-solid fa-calendar"></i> Tahunan
            </a>
        </div>

        {{-- Form Filter dinamis berdasarkan Tab --}}
        <form method="GET" action="{{ route('laporan') }}" style="display:flex; gap:8px; align-items:center;">
            <input type="hidden" name="periode" value="{{ $periode }}">

            @if ($periode === 'bulanan')
                {{-- Filter Bulanan --}}
                <select name="bulan" class="form-control" style="flex:1.2;">
                    @foreach ($daftarBulan as $num => $nama)
                        <option value="{{ $num }}" {{ (int)$bulan === $num ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
                <select name="tahun" class="form-control" style="flex:1;">
                    @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" {{ (int)$tahun === $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            @elseif ($periode === 'tahunan')
                {{-- Filter Tahunan --}}
                <select name="tahun" class="form-control" style="flex:1;">
                    @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" {{ (int)$tahun === $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                    @endfor
                </select>
            @else
                {{-- Filter Harian (Default) --}}
                <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control" style="flex:1;">
            @endif

            <button type="submit" class="btn-filter-submit" style="padding: 10px 16px;" title="Terapkan Filter">
                <i class="fa-solid fa-filter"></i>
            </button>
        </form>
    </div>

    {{-- Card Stat 1: Total Transaksi --}}
    <div class="section-container" style="padding:16px; margin:0; display:flex; align-items:center; gap:14px;">
        <div style="width:48px; height:48px; border-radius:12px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center; font-size:1.4rem;">
            <i class="fa-solid fa-cash-register"></i>
        </div>
        <div>
            <p style="margin:0; font-size:0.85rem; color:#64748b; font-weight:500;">Transaksi ({{ $labelPeriode }})</p>
            <h3 style="margin:4px 0 0; font-size:1.35rem; font-weight:700; color:var(--text-color, #1e293b);">{{ $totalTransaksi }} <span style="font-size:0.85rem; font-weight:400; color:#94a3b8;">Kuitansi</span></h3>
        </div>
    </div>

    {{-- Card Stat 2: Total Pendapatan Kasir --}}
    <div class="section-container" style="padding:16px; margin:0; display:flex; align-items:center; gap:14px;">
        <div style="width:48px; height:48px; border-radius:12px; background:#ecfdf5; color:#059669; display:flex; align-items:center; justify-content:center; font-size:1.4rem;">
            <i class="fa-solid fa-wallet"></i>
        </div>
        <div>
            <p style="margin:0; font-size:0.85rem; color:#64748b; font-weight:500;">Total Pemasukan Kasir</p>
            <h3 style="margin:4px 0 0; font-size:1.35rem; font-weight:700; color:#059669;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
        </div>
    </div>

    {{-- Card Stat 3: Rincian Tunai vs Non-Tunai --}}
    <div class="section-container" style="padding:16px; margin:0; display:flex; align-items:center; gap:14px;">
        <div style="width:48px; height:48px; border-radius:12px; background:#fef3c7; color:#d97706; display:flex; align-items:center; justify-content:center; font-size:1.4rem;">
            <i class="fa-solid fa-money-bill-transfer"></i>
        </div>
        <div style="flex:1;">
            <p style="margin:0; font-size:0.8rem; color:#64748b; font-weight:500;">Metode Pembayaran</p>
            <div style="display:flex; justify-content:space-between; font-size:0.85rem; margin-top:3px;">
                <span style="color:#64748b;">Tunai:</span>
                <strong style="color:var(--text-color, #1e293b);">Rp {{ number_format($totalTunai, 0, ',', '.') }}</strong>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:0.85rem;">
                <span style="color:#64748b;">QRIS/Lain:</span>
                <strong style="color:var(--text-color, #1e293b);">Rp {{ number_format($totalNonTunai, 0, ',', '.') }}</strong>
            </div>
        </div>
    </div>

</div>

{{-- Tabel Rincian Transaksi --}}
<div class="section-container" style="padding: 20px 25px 25px 25px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; flex-wrap:wrap; gap:8px;">
        <h3 style="margin:0; font-size:1.1rem; font-weight:700; color:var(--text-color, #1e293b);">
            Rincian Penerimaan Kasir &mdash; <span style="color:#2563eb;">{{ $labelPeriode }}</span>
        </h3>
        <span style="font-size:0.85rem; color:#64748b;">Menampilkan {{ $totalTransaksi }} data</span>
    </div>

    <div style="overflow-x:auto;">
        <table class="kasir-table">
            <thead>
                <tr>
                    <th class="text-left" style="width:160px;">NO. TRANSAKSI</th>
                    <th class="text-left" style="width:130px;">TANGGAL & JAM</th>
                    <th class="text-left" style="width:180px;">PASIEN / POLI</th>
                    <th class="text-left" style="width:120px;">KASIR</th>
                    <th class="text-center" style="width:95px;">METODE</th>
                    <th class="text-right" style="width:120px;">RESEP OBAT</th>
                    <th class="text-right" style="width:130px;">TINDAKAN & JASA</th>
                    <th class="text-right" style="width:140px;">TOTAL DIBAYAR</th>
                    <th class="text-center" style="width:90px;">STATUS</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $d)
                    @php
                        $pasienNama = $d->pendaftaran?->pasien?->nama_pasien ?? 'Pasien Umum';
                        $pasienRm = $d->pendaftaran?->pasien?->rm ?? '-';
                        $poliNama = $d->pendaftaran?->poli?->nama_poli ?? 'Poli Umum';
                        $kasirNama = $d->kasir?->nama ?? 'Kasir Loket';
                        $waktuTampil = $d->waktu_pembayaran 
                            ? $d->waktu_pembayaran->format('d/m/Y H:i') 
                            : ($d->created_at ? $d->created_at->format('d/m/Y H:i') : '-');
                        $tindakanDanJasa = (float)($d->total_layanan ?? 0) + (float)($d->jasa_perawat ?? 0);
                    @endphp
                    <tr>
                        <td class="text-left font-bold" style="color: #2563eb;">
                            {{ $d->no_invoice ?? ('TRX-' . ($d->created_at ? $d->created_at->format('Ymd') : date('Ymd')) . '-' . str_pad($d->id_transaksi, 4, '0', STR_PAD_LEFT)) }}
                        </td>
                        <td class="text-left text-muted">
                            {{ $waktuTampil }}
                        </td>
                        <td class="text-left">
                            <span class="cell-pasien-nama">{{ $pasienNama }}</span>
                            <div style="font-size:11px; color:#64748b;">{{ $pasienRm }} &bull; {{ $poliNama }}</div>
                        </td>
                        <td class="text-left text-muted" style="font-size:12px;">
                            {{ $kasirNama }}
                        </td>
                        <td class="text-center">
                            <span class="badge badge-metode" style="background:#f1f5f9; color:#475569; font-size:11px;">
                                {{ strtoupper($d->metode_bayar ?? 'TUNAI') }}
                            </span>
                        </td>
                        <td class="text-right">
                            Rp {{ number_format($d->total_resep ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="text-right">
                            Rp {{ number_format($tindakanDanJasa, 0, ',', '.') }}
                        </td>
                        <td class="text-right font-bold" style="color:#059669;">
                            Rp {{ number_format($d->total_bayar, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            <span class="badge" style="background:#dcfce7; color:#15803d; font-weight:600;">
                                Lunas
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted" style="padding:45px 0;">
                            <i class="fa-solid fa-calendar-xmark" style="font-size:36px; margin-bottom:12px; color:#cbd5e1; display:block;"></i>
                            Tidak ada transaksi penerimaan kasir pada periode {{ $labelPeriode }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if ($data->isNotEmpty())
                <tfoot>
                    <tr style="background:var(--bg-tfoot, #f8fafc); font-weight:700; border-top:2px solid #e2e8f0;">
                        <td colspan="5" class="text-right" style="padding:14px; font-size:13px;">TOTAL KESELURUHAN ({{ $totalTransaksi }} KUITANSI):</td>
                        <td class="text-right" style="padding:14px;">Rp {{ number_format($totalResep, 0, ',', '.') }}</td>
                        <td class="text-right" style="padding:14px;">Rp {{ number_format($totalLayanan + $totalJasaPerawat, 0, ',', '.') }}</td>
                        <td class="text-right" style="padding:14px; font-size:14px; color:#059669;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
