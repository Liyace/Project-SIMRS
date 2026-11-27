<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kasir RS Delta - {{ $labelPeriode }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logodelta1.svg') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        /* ── RESET & PRINT STYLES ── */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11pt;
            color: #1e293b;
            background: #f8fafc;
            padding: 20px;
        }

        .paper-container {
            max-width: 950px;
            margin: 0 auto;
            background: #ffffff;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
        }

        /* ── ACTION BAR (NOT PRINTED) ── */
        .action-bar {
            max-width: 950px;
            margin: 0 auto 16px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }

        .btn-print {
            background: #2563eb;
            color: #ffffff;
        }
        .btn-print:hover { background: #1d4ed8; }

        .btn-back {
            background: #f1f5f9;
            color: #475569;
        }
        .btn-back:hover { background: #e2e8f0; }

        /* ── KOP SURAT RESMI RUMAH SAKIT ── */
        .kop-surat {
            display: flex;
            align-items: center;
            gap: 20px;
            padding-bottom: 12px;
            border-bottom: 3px double #0f172a;
            margin-bottom: 24px;
        }

        .kop-logo {
            width: 75px;
            height: 75px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }

        .kop-logo img {
            width: 52px;
            height: 52px;
            object-fit: contain;
            display: block;
        }

        .kop-text {
            flex: 1;
            text-align: center;
        }

        .kop-text h1 {
            font-size: 18pt;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .kop-text h2 {
            font-size: 11pt;
            font-weight: 600;
            color: #2563eb;
            margin-bottom: 4px;
            text-transform: uppercase;
        }

        .kop-text p {
            font-size: 8.5pt;
            color: #475569;
            line-height: 1.4;
        }

        /* ── JUDUL & METADATA LAPORAN ── */
        .report-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .report-title {
            font-size: 14pt;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .report-subtitle {
            font-size: 10pt;
            font-weight: 600;
            color: #059669;
            background: #ecfdf5;
            display: inline-block;
            padding: 3px 12px;
            border-radius: 20px;
            border: 1px solid #a7f3d0;
        }

        .report-meta {
            margin-top: 8px;
            font-size: 8.5pt;
            color: #64748b;
        }

        /* ── SUMMARY CARDS / KOTAK REKAPITULASI ── */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .summary-box {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 12px;
            background: #f8fafc;
        }

        .summary-box .title {
            font-size: 8pt;
            text-transform: uppercase;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 4px;
        }

        .summary-box .value {
            font-size: 11pt;
            font-weight: 700;
            color: #0f172a;
        }

        .summary-box.highlight {
            background: #ecfdf5;
            border-color: #a7f3d0;
        }
        .summary-box.highlight .value {
            color: #059669;
        }

        /* ── TABEL TRANSAKSI ── */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
            font-size: 9pt;
        }

        .report-table th,
        .report-table td {
            border: 1px solid #cbd5e1;
            padding: 8px 6px;
        }

        .report-table th {
            background: #f1f5f9;
            color: #0f172a;
            font-weight: 700;
            text-align: center;
            font-size: 8.5pt;
            text-transform: uppercase;
        }

        .report-table tbody tr:nth-child(even) {
            background: #fcfcfc;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: 700; }

        .total-row {
            background: #e2e8f0 !important;
            font-weight: 700;
            font-size: 9.5pt;
        }

        /* ── LEMBAR PENGESAHAN / TANDA TANGAN ── */
        .signature-section {
            margin-top: 35px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }

        .signature-box {
            width: 280px;
            text-align: center;
            font-size: 9.5pt;
        }

        .signature-box .role-title {
            margin-bottom: 60px;
            color: #334155;
            font-weight: 500;
        }

        .signature-box .name {
            font-weight: 700;
            color: #0f172a;
            text-decoration: underline;
        }

        .signature-box .nip {
            font-size: 8.5pt;
            color: #64748b;
            margin-top: 2px;
        }

        /* ── MEDIA PRINT RULES ── */
        @media print {
            body {
                background: #ffffff;
                padding: 0;
                font-size: 10pt;
            }

            .action-bar {
                display: none !important;
            }

            .paper-container {
                box-shadow: none;
                padding: 0;
                max-width: 100%;
                border-radius: 0;
            }

            .kop-surat {
                border-bottom: 2px solid #000;
            }

            .kop-logo {
                background: #2563eb !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .report-table th {
                background: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .summary-box.highlight {
                background: #ecfdf5 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            @page {
                size: A4 portrait;
                margin: 15mm 12mm 15mm 12mm;
            }
        }
    </style>
</head>
<body>

    {{-- ACTION BAR (Hanya terlihat di layar komputer) --}}
    <div class="action-bar">
        <a href="{{ route('laporan', ['periode' => $periode, 'tanggal' => $tanggal, 'bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn-action btn-back">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Sistem
        </a>
        <div style="display:flex; gap:10px;">
            <a href="{{ route('laporan.excel', ['periode' => $periode, 'tanggal' => $tanggal, 'bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn-action" style="background:#059669; color:#fff;">
                <i class="fa-solid fa-file-excel"></i> Unduh Excel
            </a>
            <button onclick="window.print()" class="btn-action btn-print">
                <i class="fa-solid fa-print"></i> Cetak / Simpan PDF
            </button>
        </div>
    </div>

    {{-- DOKUMEN CETAK KERTAS RESMI --}}
    <div class="paper-container">

        {{-- KOP SURAT RESMI RS DELTA --}}
        <div class="kop-surat">
            <div class="kop-logo">
                <img src="{{ asset('images/logodelta1.svg') }}" alt="Logo RS Delta">
            </div>
            <div class="kop-text">
                <h1>RUMAH SAKIT DELTA BANJARMASIN</h1>
                <h2>Instalasi Kasir & Perbendaharaan Keuangan</h2>
                <p>
                    Jl. Brigjen H. Hasan Basri No. 12, Pangeran, Banjarmasin Utara, Kota Banjarmasin, Kalimantan Selatan 70124<br>
                    Telepon: (0511) 3301234 | Fax: (0511) 3305678 | Email: kasir@rsdelta.co.id | Website: www.rsdelta.co.id
                </p>
            </div>
        </div>

        {{-- HEADER LAPORAN --}}
        <div class="report-header">
            <div class="report-title">Laporan Rekapitulasi Penerimaan Kasir</div>
            <div class="report-subtitle">{{ $subTitlePeriode }}</div>
            <div class="report-meta">
                Dicetak pada: <strong>{{ date('d/m/Y H:i') }} WITA</strong> | Oleh Kasir: <strong>{{ $kasirLogin->nama ?? 'Kasir Admin' }}</strong>
            </div>
        </div>

        {{-- KOTAK REKAPITULASI KEUANGAN --}}
        <div class="summary-grid">
            <div class="summary-box">
                <div class="title">Total Transaksi</div>
                <div class="value">{{ $totalTransaksi }} Kuitansi</div>
            </div>
            <div class="summary-box">
                <div class="title">Penerimaan Tunai</div>
                <div class="value">Rp {{ number_format($totalTunai, 0, ',', '.') }}</div>
            </div>
            <div class="summary-box">
                <div class="title">Penerimaan QRIS / Lain</div>
                <div class="value">Rp {{ number_format($totalNonTunai, 0, ',', '.') }}</div>
            </div>
            <div class="summary-box highlight">
                <div class="title">Total Penerimaan</div>
                <div class="value">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
            </div>
        </div>

        {{-- TABEL RINCIAN TRANSAKSI --}}
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th style="width: 125px;">No. Invoice</th>
                    <th style="width: 80px;">Tgl / Jam</th>
                    <th style="width: 140px;">Nama Pasien (RM)</th>
                    <th style="width: 85px;">Poli</th>
                    <th style="width: 65px;">Metode</th>
                    <th style="width: 85px;">Resep Obat</th>
                    <th style="width: 85px;">Tindakan/Jasa</th>
                    <th style="width: 100px;">Total Bayar</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $index => $d)
                    @php
                        $pasienNama = $d->pendaftaran?->pasien?->nama_pasien ?? 'Pasien Umum';
                        $pasienRm = $d->pendaftaran?->pasien?->rm ?? '-';
                        $poliNama = $d->pendaftaran?->poli?->nama_poli ?? 'Poli Umum';
                        $waktuTampil = $d->waktu_pembayaran 
                            ? $d->waktu_pembayaran->format('d/m/Y H:i') 
                            : ($d->created_at ? $d->created_at->format('d/m/Y H:i') : '-');
                        $tindakanDanJasa = (float)($d->total_layanan ?? 0) + (float)($d->jasa_perawat ?? 0);
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="font-bold text-left" style="color:#1d4ed8;">
                            {{ $d->no_invoice ?? ('TRX-' . ($d->created_at ? $d->created_at->format('Ymd') : date('Ymd')) . '-' . str_pad($d->id_transaksi, 4, '0', STR_PAD_LEFT)) }}
                        </td>
                        <td class="text-center" style="font-size:8pt;">{{ $waktuTampil }}</td>
                        <td class="text-left">
                            <strong>{{ $pasienNama }}</strong><br>
                            <span style="font-size:7.5pt; color:#64748b;">{{ $pasienRm }}</span>
                        </td>
                        <td class="text-left" style="font-size:8pt;">{{ $poliNama }}</td>
                        <td class="text-center">
                            <span style="font-weight:600; font-size:8pt;">{{ strtoupper($d->metode_bayar ?? 'TUNAI') }}</span>
                        </td>
                        <td class="text-right">Rp {{ number_format($d->total_resep ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($tindakanDanJasa, 0, ',', '.') }}</td>
                        <td class="text-right font-bold" style="color:#0f172a;">
                            Rp {{ number_format($d->total_bayar, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center" style="padding: 25px; color:#94a3b8;">
                            Tidak ada transaksi penerimaan kasir pada periode {{ $labelPeriode }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if ($data->isNotEmpty())
                <tfoot>
                    <tr class="total-row">
                        <td colspan="6" class="text-right">TOTAL KESELURUHAN ({{ $totalTransaksi }} KUITANSI):</td>
                        <td class="text-right">Rp {{ number_format($totalResep, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($totalLayanan + $totalJasaPerawat, 0, ',', '.') }}</td>
                        <td class="text-right font-bold" style="color:#059669;">
                            Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            @endif
        </table>

        {{-- LEMBAR PENGESAHAN TANDA TANGAN RESMI --}}
        <div class="signature-section">
            <div class="signature-box">
                <div class="role-title">
                    Mengetahui,<br>
                    <strong>Kepala Bagian Keuangan RS Delta</strong>
                </div>
                <div class="name">Drs. H. Hendra Wijaya, S.E., M.M.</div>
                <div class="nip">NIP. 19780512 200312 1 002</div>
            </div>

            <div class="signature-box">
                <div class="role-title">
                    Banjarmasin, {{ date('d F Y') }}<br>
                    <strong>Kasir Admin / Supervisor Loket</strong>
                </div>
                <div class="name">{{ $kasirLogin->nama ?? 'Rahmat Kasir Utama' }}</div>
                <div class="nip">
                    @if (!empty($kasirLogin->nip))
                        NIP. {{ $kasirLogin->nip }}
                    @else
                        NIP. -
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- Script auto-print jika dibuka dengan parameter print=true --}}
    @if(request()->query('auto_print'))
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                window.print();
            });
        </script>
    @endif
</body>
</html>
