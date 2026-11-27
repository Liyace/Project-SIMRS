<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuitansi Pembayaran - {{ $transaksi->no_invoice }} - RS Delta</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logodelta-color.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Poppins', Arial, sans-serif; }
        body { background: #f1f5f9; color: #1e293b; padding: 25px 15px; font-size: 13px; line-height: 1.5; }
        
        .no-print {
            max-width: 760px;
            margin: 0 auto 20px auto;
            background: #ffffff;
            border-radius: 12px;
            padding: 14px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            flex-wrap: wrap;
            gap: 10px;
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 18px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: 0.2s;
        }
        .btn-print { background: #1C4D8D; color: #fff; }
        .btn-print:hover { background: #143869; }
        .btn-back { background: #f1f5f9; color: #475569; }
        .btn-back:hover { background: #e2e8f0; }

        .receipt-card {
            max-width: 760px;
            margin: 0 auto;
            background: #ffffff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
        }

        .header-kop {
            display: flex;
            align-items: center;
            gap: 18px;
            border-bottom: 2px solid #1C4D8D;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }
        .header-kop img { width: 68px; height: 68px; object-contain: fit; }
        .header-title h1 { font-size: 20px; font-weight: 800; color: #1C4D8D; letter-spacing: 0.5px; }
        .header-title p { font-size: 11px; color: #64748b; line-height: 1.4; margin-top: 2px; }

        .title-doc {
            text-align: center;
            margin-bottom: 24px;
        }
        .title-doc h2 { font-size: 16px; font-weight: 700; color: #0f172a; text-transform: uppercase; letter-spacing: 1px; }
        .title-doc span { font-size: 12px; color: #64748b; font-family: 'Roboto Mono', monospace; font-weight: 600; }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            background: #f8fafc;
            padding: 16px 20px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            margin-bottom: 24px;
            font-size: 12.5px;
        }
        .meta-item { display: flex; justify-content: space-between; }
        .meta-label { color: #64748b; }
        .meta-value { font-weight: 600; color: #0f172a; }

        table.billing-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        table.billing-table th {
            background: #f1f5f9;
            color: #475569;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 14px;
            text-align: left;
            border-bottom: 2px solid #cbd5e1;
        }
        table.billing-table td {
            padding: 12px 14px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 12.5px;
        }
        table.billing-table tr:last-child td {
            border-bottom: 2px solid #cbd5e1;
        }

        .summary-box {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 30px;
        }
        .summary-table { width: 320px; font-size: 13px; }
        .summary-row { display: flex; justify-content: space-between; padding: 6px 0; }
        .summary-row.total {
            border-top: 2px solid #1C4D8D;
            padding-top: 10px;
            font-size: 15px;
            font-weight: 800;
            color: #1C4D8D;
        }

        .stamp-box {
            display: inline-block;
            border: 2px solid #10b981;
            color: #059669;
            font-weight: 800;
            text-transform: uppercase;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 13px;
            letter-spacing: 1.5px;
            transform: rotate(-3deg);
        }

        .signature-area {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 40px;
            padding-top: 20px;
        }
        .signature-box { text-align: center; width: 220px; }
        .signature-space { height: 60px; }

        @media print {
            body { background: #fff; padding: 0; }
            .no-print { display: none !important; }
            .receipt-card { border: none; box-shadow: none; padding: 20px 0; }
        }
    </style>
</head>
<body>

    <!-- ACTION BAR -->
    <div class="no-print">
        <a href="{{ route('pasien.riwayat.detail', $pendaftaran->id_pendaftaran) }}" class="btn-action btn-back">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Rekam Medis
        </a>
        <button onclick="window.print()" class="btn-action btn-print">
            <i class="fa-solid fa-print"></i> Cetak Dokumen Kuitansi (Ctrl+P)
        </button>
    </div>

    <!-- RECEIPT DOCUMENT -->
    <div class="receipt-card">
        
        <!-- KOP RUMAH SAKIT -->
        <div class="header-kop">
            <img src="{{ asset('images/logodelta-color.svg') }}" alt="Logo RS Delta">
            <div class="header-title">
                <h1>RUMAH SAKIT DELTA BANJARMASIN</h1>
                <p>
                    Jl. Brigjen Hasan Basri No. 45, Banjarmasin, Kalimantan Selatan 70123<br>
                    Telp: (0511) 330-1122 | WhatsApp: 0812-3456-7801 | Website: www.rsdelta.co.id
                </p>
            </div>
        </div>

        <!-- TITLE -->
        <div class="title-doc">
            <h2>Kuitansi Pembayaran Pasien Rawat Jalan</h2>
            <span>No. Invoice: {{ $transaksi->no_invoice }}</span>
        </div>

        <!-- METADATA -->
        <div class="meta-grid">
            <div class="meta-item">
                <span class="meta-label">No. Rekam Medis:</span>
                <span class="meta-value">{{ $pendaftaran->pasien->rm ?? '-' }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Tanggal Pelayanan:</span>
                <span class="meta-value">{{ $pendaftaran->tanggal_daftar ? $pendaftaran->tanggal_daftar->format('d/m/Y') : '-' }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Nama Pasien:</span>
                <span class="meta-value">{{ $pendaftaran->pasien->nama_pasien ?? '-' }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Poliklinik Tujuan:</span>
                <span class="meta-value">{{ $pendaftaran->poli->nama_poli ?? '-' }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Dokter Pemeriksa:</span>
                <span class="meta-value">{{ $pendaftaran->dokter->nama ?? '-' }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Metode Pembayaran:</span>
                <span class="meta-value">{{ strtoupper($transaksi->metode_bayar ?? 'TUNAI') }}</span>
            </div>
        </div>

        <!-- TABEL RINCIAN BIAYA -->
        <table class="billing-table">
            <thead>
                <tr>
                    <th style="width: 8%;">No</th>
                    <th style="width: 62%;">Uraian Layanan & Komponen Biaya</th>
                    <th style="width: 30%; text-align: right;">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>
                        <strong>Konsultasi & Pemeriksaan Dokter ({{ $pendaftaran->poli->nama_poli ?? 'Poliklinik' }})</strong>
                        <div style="font-size: 11px; color: #64748b;">Administrasi pendaftaran dan pemeriksaan medis dasar</div>
                    </td>
                    <td style="text-align: right; font-weight: 600;">
                        Rp {{ number_format($transaksi->total_layanan, 0, ',', '.') }}
                    </td>
                </tr>

                @if ($transaksi->jasa_perawat > 0)
                    <tr>
                        <td>2</td>
                        <td>
                            <strong>Asuhan Keperawatan & Skrining Vital Signs (TTV)</strong>
                            <div style="font-size: 11px; color: #64748b;">Pemeriksaan tekanan darah, suhu tubuh, berat/tinggi badan</div>
                        </td>
                        <td style="text-align: right; font-weight: 600;">
                            Rp {{ number_format($transaksi->jasa_perawat, 0, ',', '.') }}
                        </td>
                    </tr>
                @endif

                @if ($transaksi->total_resep > 0)
                    <tr>
                        <td>3</td>
                        <td>
                            <strong>Resep Obat Farmasi (No. Resep: {{ $pendaftaran->resep->no_resep ?? '-' }})</strong>
                            <div style="font-size: 11px; color: #64748b;">Paket peresepan obat oral/racikan sesuai instruksi dokter</div>
                        </td>
                        <td style="text-align: right; font-weight: 600;">
                            Rp {{ number_format($transaksi->total_resep, 0, ',', '.') }}
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- TOTAL SUMMARY -->
        <div class="summary-box">
            <div class="summary-table">
                <div class="summary-row total">
                    <span>TOTAL DIBAYAR:</span>
                    <span>Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row" style="font-size: 12px; color: #64748b;">
                    <span>Jumlah Uang Diserahkan:</span>
                    <span>Rp {{ number_format($transaksi->jumlah_dibayar ?? $transaksi->total_bayar, 0, ',', '.') }}</span>
                </div>
                @if (($transaksi->kembalian ?? 0) > 0)
                    <div class="summary-row" style="font-size: 12px; color: #64748b;">
                        <span>Kembalian:</span>
                        <span>Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- FOOTER TANDA TANGAN -->
        <div class="signature-area">
            <div>
                <div class="stamp-box">
                    <i class="fa-solid fa-check-circle"></i> LUNAS
                </div>
                <div style="font-size: 11px; color: #64748b; margin-top: 8px;">
                    Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WITA
                </div>
            </div>

            <div class="signature-box">
                <div style="font-size: 12px; color: #475569;">Banjarmasin, {{ $transaksi->waktu_pembayaran ? $transaksi->waktu_pembayaran->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}</div>
                <div style="font-size: 12px; font-weight: 600; color: #0f172a;">Kasir Rumah Sakit Delta,</div>
                <div class="signature-space"></div>
                <div style="font-weight: 700; text-decoration: underline; color: #0f172a;">{{ $transaksi->kasir->nama ?? 'Kasir RS Delta' }}</div>
                <div style="font-size: 10px; color: #64748b;">Unit Layanan Keuangan Kasir</div>
            </div>
        </div>

    </div>

</body>
</html>
