<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Eksekutif Pelayanan RS Delta - {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logodelta1.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            background: #f1f5f9;
            color: #000;
            line-height: 1.4;
            padding: 30px 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* TOOLBAR ATAS (HANYA MUNCUL DI LAYAR BROWSER) */
        .toolbar-top {
            width: 100%;
            max-width: 210mm;
            background: #ffffff;
            border-radius: 12px;
            padding: 12px 20px;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: 1px solid #cbd5e1;
            font-family: 'Poppins', sans-serif;
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 18px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
        }
        .btn-back {
            background: #f1f5f9;
            color: #475569;
        }
        .btn-back:hover {
            background: #e2e8f0;
            color: #1e293b;
        }
        .btn-print {
            background: #1C4D8D;
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(28, 77, 141, 0.3);
        }
        .btn-print:hover {
            background: #163d70;
        }

        /* LEMBAR KERTAS A4 FORMAL */
        .paper-a4 {
            width: 100%;
            max-width: 210mm;
            min-height: 297mm;
            background: #ffffff;
            padding: 20mm 20mm 20mm 20mm;
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
            border: 1px solid #e2e8f0;
        }

        /* KOP SURAT */
        .kop-header {
            display: flex;
            align-items: center;
            gap: 18px;
            margin-bottom: 6px;
        }
        .kop-logo {
            width: 75px;
            height: 75px;
            object-fit: contain;
        }
        .kop-text {
            flex: 1;
            text-align: center;
        }
        .kop-instansi {
            font-size: 16pt;
            font-weight: bold;
            color: #000;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .kop-sub {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #222;
            margin-bottom: 2px;
        }
        .kop-alamat {
            font-size: 8.5pt;
            color: #333;
            line-height: 1.35;
        }
        .kop-garis {
            border-top: 2.5px solid #000;
            border-bottom: 1px solid #000;
            height: 3px;
            margin: 6px 0 18px 0;
        }

        /* JUDUL DOKUMEN */
        .doc-title {
            text-align: center;
            margin-bottom: 18px;
        }
        .doc-title h2 {
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .doc-title p {
            font-size: 9.5pt;
            color: #333;
            margin-top: 3px;
        }

        /* TABEL INFORMASI METADATA */
        .meta-table {
            width: 100%;
            font-size: 9.5pt;
            margin-bottom: 16px;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 3px 0;
            vertical-align: top;
        }

        /* JUDUL SUB BAGIAN */
        .section-heading {
            font-size: 10.5pt;
            font-weight: bold;
            margin: 14px 0 6px 0;
            color: #000;
        }

        /* TABEL DATA FORMAL */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            margin-bottom: 16px;
        }
        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 5px 8px;
        }
        .data-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* TANDA TANGAN */
        .ttd-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5pt;
            margin-top: 24px;
            page-break-inside: avoid;
        }
        .ttd-table td {
            text-align: center;
            vertical-align: top;
            width: 50%;
        }

        /* MEDIA PRINT (UNTUK DIALOG CETAK & SAVE AS PDF) */
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .toolbar-top {
                display: none !important;
            }
            .paper-a4 {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
            @page {
                size: A4 portrait;
                margin: 15mm 20mm 15mm 20mm;
            }
        }
    </style>
</head>
<body>

    <!-- TOOLBAR INTERAKTIF -->
    <div class="toolbar-top">
        <div style="display: flex; align-items: center; gap: 10px;">
            <a href="{{ route('admin.statistik') }}" class="btn-action btn-back">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
            </a>
            <span style="font-size: 13px; font-weight: 600; color: #1e293b;">
                Pratinjau Dokumen Laporan Eksekutif Resmi
            </span>
        </div>
        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" class="btn-action btn-print">
                <i class="fa-solid fa-print"></i> Cetak / Simpan PDF (Ctrl+P)
            </button>
        </div>
    </div>

    <!-- LEMBAR DOKUMEN CETAK RESMI -->
    <div class="paper-a4">

        <!-- KOP RESMI RUMAH SAKIT DELTA -->
        <div class="kop-header">
            <img src="{{ asset('images/logodelta-color.svg') }}" alt="Logo Delta" class="kop-logo" style="width: 78px; height: 72px; object-fit: contain;">
            <div class="kop-text">
                <div class="kop-instansi">RUMAH SAKIT DELTA BANJARMASIN</div>
                <div class="kop-sub">DEDIKASI UNTUK KESEMBUHAN • TERAKREDITASI PARIPURNA KARS</div>
                <div class="kop-alamat">
                    Jl. Brigjen Hasan Basri No. 45, Banjarmasin, Kalimantan Selatan 70123<br>
                    Telp: (0511) 330-1122 | WhatsApp: 0812-3456-7801 | Website: www.rsdelta.co.id
                </div>
            </div>
        </div>
        <div class="kop-garis"></div>

        <!-- JUDUL DOKUMEN -->
        <div class="doc-title">
            <h2>LAPORAN EKSEKUTIF INDIKATOR & REKAPITULASI PELAYANAN RS</h2>
            <p>Nomor: 001/LPE-DIR/RS-DELTA/{{ date('m/Y') }} &nbsp;|&nbsp; Periode: Bulan {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</p>
        </div>

        <!-- METADATA LAPORAN -->
        <table class="meta-table">
            <tr>
                <td style="width: 140px; font-weight: bold;">Tanggal Cetak</td>
                <td style="width: 15px;">:</td>
                <td>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y, H:i') }} WITA</td>
                <td style="width: 130px; font-weight: bold;">Status Pelaporan</td>
                <td style="width: 15px;">:</td>
                <td>Resmi / Terverifikasi</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Dicetak Oleh</td>
                <td>:</td>
                <td>{{ auth()->user()->nama ?? 'Super Admin RS' }} (Administrator IT)</td>
                <td style="font-weight: bold;">Unit Sumber Data</td>
                <td>:</td>
                <td>SIMRS Enterprise Terintegrasi</td>
            </tr>
        </table>

        <!-- BAGIAN 1: RINGKASAN INDIKATOR KINERJA UTAMA (KPI) -->
        <div class="section-heading">
            I. RINGKASAN INDIKATOR KINERJA UTAMA BULAN {{ strtoupper(\Carbon\Carbon::now()->translatedFormat('F Y')) }}
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 35px;">No</th>
                    <th>Indikator Pelayanan</th>
                    <th style="width: 170px;">Capaian Periode Ini</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center;">1</td>
                    <td><strong>Total Kunjungan Pasien Rawat Jalan</strong></td>
                    <td style="text-align: center; font-weight: bold;">{{ number_format($totalKunjunganBulanIni) }} Pasien</td>
                    <td>Rata-rata {{ number_format($totalKunjunganBulanIni / max(\Carbon\Carbon::now()->day, 1), 1) }} pasien/hari (Hari ini: {{ $totalKunjunganHariIni }} pasien)</td>
                </tr>
                <tr>
                    <td style="text-align: center;">2</td>
                    <td><strong>Total Penerimaan Kasir (Transaksi Lunas)</strong></td>
                    <td style="text-align: center; font-weight: bold;">Rp {{ number_format($totalPendapatanBulanIni, 0, ',', '.') }}</td>
                    <td>Pendapatan tagihan medis & obat farmasi berstatus lunas</td>
                </tr>
                <tr>
                    <td style="text-align: center;">3</td>
                    <td><strong>Pertambahan Pasien Baru Terdaftar</strong></td>
                    <td style="text-align: center; font-weight: bold;">{{ number_format($pasienBaruBulanIni) }} Orang</td>
                    <td>Total akumulasi master rekam medis: {{ number_format($totalPasienTerdaftar) }} pasien</td>
                </tr>
                <tr>
                    <td style="text-align: center;">4</td>
                    <td><strong>Poliklinik Teraktif (Kunjungan Tertinggi)</strong></td>
                    @php
                        $maxPoliVal = !empty($poliValues) ? max($poliValues) : 0;
                        $maxPoliKey = !empty($poliValues) ? array_search($maxPoliVal, $poliValues) : null;
                        $topPoliNama = ($maxPoliKey !== null && isset($poliLabels[$maxPoliKey])) ? $poliLabels[$maxPoliKey] : 'Poli Umum';
                    @endphp
                    <td style="text-align: center; font-weight: bold;">{{ $topPoliNama }}</td>
                    <td>Kontribusi volume pelayanan rawat jalan terbesar</td>
                </tr>
            </tbody>
        </table>

        <!-- BAGIAN 2: REKAPITULASI KUNJUNGAN PER POLIKLINIK -->
        <div class="section-heading">
            II. DISTRIBUSI KUNJUNGAN PASIEN PER POLIKLINIK
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 35px;">No</th>
                    <th style="width: 90px;">Kode Poli</th>
                    <th>Nama Poliklinik</th>
                    <th style="width: 120px;">Tarif Konsultasi</th>
                    <th style="width: 100px;">Jumlah Pasien</th>
                    <th style="width: 80px;">Rasio (%)</th>
                </tr>
            </thead>
            <tbody>
                @php $totalPasienPoli = 0; @endphp
                @forelse ($poliStats as $idx => $p)
                    @php
                        $jml = $p->pendaftarans_count ?? 0;
                        $totalPasienPoli += $jml;
                        $persen = $totalKunjunganBulanIni > 0 ? round(($jml / $totalKunjunganBulanIni) * 100, 1) : 0;
                    @endphp
                    <tr>
                        <td style="text-align: center;">{{ $idx + 1 }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $p->kode_poli }}</td>
                        <td>{{ $p->nama_poli }}</td>
                        <td style="text-align: right;">Rp {{ number_format($p->biaya_konsultasi, 0, ',', '.') }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $jml }}</td>
                        <td style="text-align: center;">{{ $persen }}%</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center;">Belum ada data kunjungan poliklinik.</td>
                    </tr>
                @endforelse
                <tr style="font-weight: bold; background-color: #f8fafc;">
                    <td colspan="4" style="text-align: right;">TOTAL KUNJUNGAN RAWAT JALAN :</td>
                    <td style="text-align: center;">{{ number_format($totalPasienPoli) }}</td>
                    <td style="text-align: center;">100%</td>
                </tr>
            </tbody>
        </table>

        <!-- BAGIAN 3: TOP 8 MORBIDITAS DIAGNOSA (ICD-10) -->
        <div class="section-heading">
            III. TOP 8 DIAGNOSA PENYAKIT TERBANYAK (MORBIDITAS ICD-10)
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 35px;">No</th>
                    <th style="width: 110px;">Kode ICD-10</th>
                    <th>Nama Diagnosa Medis Klinis</th>
                    <th style="width: 110px;">Total Kasus</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($topDiagnosa as $idx => $diag)
                    <tr>
                        <td style="text-align: center;">{{ $idx + 1 }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $diag->kode_icd10 ?? '-' }}</td>
                        <td>{{ $diag->diagnosa_utama }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $diag->total }} Kasus</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center;">Belum ada data diagnosa rekam medis klinis.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- BAGIAN 4: DISTRIBUSI PENJAMIN BAYAR -->
        <div class="section-heading">
            IV. DISTRIBUSI PENJAMIN PEMBAYARAN PASIEN
        </div>
        <table class="data-table" style="margin-bottom: 24px;">
            <thead>
                <tr>
                    <th style="width: 35px;">No</th>
                    <th>Jenis Penjamin Pembayaran</th>
                    <th style="width: 140px;">Jumlah Pasien</th>
                    <th style="width: 110px;">Persentase (%)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalPenjamin = array_sum($caraBayarValues);
                @endphp
                @foreach ($caraBayarLabels as $idx => $labelBayar)
                    @php
                        $valBayar = $caraBayarValues[$idx] ?? 0;
                        $persenBayar = $totalPenjamin > 0 ? round(($valBayar / $totalPenjamin) * 100, 1) : 0;
                    @endphp
                    <tr>
                        <td style="text-align: center;">{{ $idx + 1 }}</td>
                        <td><strong>{{ $labelBayar }}</strong></td>
                        <td style="text-align: center; font-weight: bold;">{{ number_format($valBayar) }} Pasien</td>
                        <td style="text-align: center;">{{ $persenBayar }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- LEMBAR PENGESAHAN / TANDA TANGAN RESMI -->
        <table class="ttd-table">
            <tr>
                <td>
                    Mengetahui,<br>
                    <strong>Kepala Instalasi SIMRS & Rekam Medis</strong>
                    <br><br><br><br>
                    <strong style="text-decoration: underline;">{{ auth()->user()->nama ?? 'Super Admin RS' }}</strong><br>
                    NIP: {{ auth()->user()->nip ?? '19800101 200501 1 001' }}
                </td>
                <td>
                    Banjarmasin, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    <strong>Direktur Rumah Sakit Delta</strong>
                    <br><br><br><br>
                    <strong style="text-decoration: underline;">dr. Budi Santoso, Sp.PD</strong><br>
                    NIP: 19850312 201001 1 002
                </td>
            </tr>
        </table>

    </div>

</body>
</html>
