<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Stok Opname Farmasi - RS Delta</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logodelta1.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.4;
            padding: 24px 20px;
        }

        .no-print {
            max-width: 1000px;
            margin: 0 auto 16px auto;
            background: #ffffff;
            border-radius: 12px;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            flex-wrap: wrap;
            gap: 10px;
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: 0.2s;
        }
        .btn-print { background: #2563eb; color: #fff; }
        .btn-print:hover { background: #1d4ed8; }
        .btn-pdf { background: #dc2626; color: #fff; }
        .btn-pdf:hover { background: #b91c1c; }
        .btn-excel { background: #059669; color: #fff; }
        .btn-excel:hover { background: #047857; }
        .btn-back { background: #f1f5f9; color: #475569; }
        .btn-back:hover { background: #e2e8f0; }

        .paper {
            max-width: 1000px;
            margin: 0 auto;
            background: #ffffff;
            padding: 36px 44px;
            box-shadow: 0 4px 25px rgba(0,0,0,0.08);
            border-radius: 4px;
        }

        .kop-surat {
            display: flex;
            align-items: center;
            gap: 16px;
            border-bottom: 3px double #0f172a;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .kop-logo {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }
        .kop-text h2 {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
        }
        .kop-text h4 {
            font-size: 12px;
            font-weight: 700;
            color: #2563eb;
        }
        .kop-text p {
            font-size: 10px;
            color: #64748b;
        }

        .doc-title {
            text-align: center;
            margin-bottom: 16px;
        }
        .doc-title h3 {
            font-size: 15px;
            font-weight: 800;
            text-transform: uppercase;
            text-decoration: underline;
            color: #0f172a;
        }
        .doc-title .doc-num {
            font-size: 11px;
            color: #64748b;
            margin-top: 3px;
        }

        .summary-boxes {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 16px;
        }
        .sum-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 10px 14px;
            border-radius: 8px;
        }
        .sum-box .lbl { font-size: 10.5px; color: #64748b; font-weight: 600; text-transform: uppercase; }
        .sum-box .val { font-size: 16px; font-weight: 800; color: #0f172a; margin-top: 2px; }

        .table-stok {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        .table-stok th, .table-stok td {
            border: 1px solid #cbd5e1;
            padding: 5px 8px;
        }
        .table-stok th {
            background: #1e3a8a;
            color: #ffffff;
            font-weight: 700;
            text-align: center;
        }

        .ttd-grid {
            margin-top: 24px;
            display: flex;
            justify-content: flex-end;
        }

        @media print {
            body { background: #fff; padding: 0; }
            .no-print { display: none !important; }
            .paper { box-shadow: none; padding: 0; width: 100%; max-width: 100%; }
            @page { size: A4 landscape; margin: 12mm; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <div style="display: flex; align-items: center; gap: 8px;">
            <a href="{{ route('apoteker.obat') }}" class="btn-action btn-back">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Stok Farmasi
            </a>
            <span style="font-size: 12px; color: #64748b;">
                Filter: <strong>{{ ucfirst($filter) }}</strong> ({{ $totalItem }} Item Obat)
            </span>
        </div>

        <div style="display: flex; gap: 6px;">
            <button onclick="window.print()" class="btn-action btn-print">
                <i class="fa-solid fa-print"></i> Cetak / Print
            </button>
            <a href="{{ route('apoteker.obat.stok-excel') }}?status={{ $filter }}" class="btn-action btn-excel">
                <i class="fa-solid fa-file-excel"></i> Export Excel (.xlsx)
            </a>
            <a href="{{ route('apoteker.obat.stok-pdf') }}?download=1&status={{ $filter }}" class="btn-action btn-pdf">
                <i class="fa-solid fa-file-pdf"></i> Download PDF
            </a>
        </div>
    </div>

    <div class="paper">
        <div class="kop-surat">
            <img src="{{ asset('images/logodelta1.svg') }}" alt="Logo Delta" class="kop-logo" style="background: #2563eb; border-radius: 8px; padding: 6px;">
            <div class="kop-text">
                <h2>RUMAH SAKIT DELTA</h2>
                <h4>INSTALASI FARMASI & MANAJEMEN LOGISTIK MEDIS</h4>
                <p>Jl. Jenderal Ahmad Yani Km. 4.5 No. 128, Banjarmasin, Kalimantan Selatan 70234 • Telp: (0511) 7654321</p>
            </div>
        </div>

        <div class="doc-title">
            <h3>LAPORAN INVENTORI & STOK OPNAME FARMASI</h3>
            <div class="doc-num">Tanggal Cetak: {{ now()->translatedFormat('d F Y H:i') }} WITA • Dicetak Oleh: {{ auth()->user()->nama ?? 'Apoteker' }}</div>
        </div>

        <div class="summary-boxes">
            <div class="sum-box">
                <div class="lbl">Total Item Master Obat</div>
                <div class="val">{{ number_format($totalItem, 0, ',', '.') }} Item</div>
            </div>
            <div class="sum-box">
                <div class="lbl">Total Kuantitas Fisik</div>
                <div class="val">{{ number_format($totalFisik, 0, ',', '.') }} Unit</div>
            </div>
            <div class="sum-box" style="border-left: 4px solid #2563eb;">
                <div class="lbl">Valuasi Aset Persediaan</div>
                <div class="val" style="color: #2563eb;">Rp {{ number_format($totalValuasi, 0, ',', '.') }}</div>
            </div>
        </div>

        <table class="table-stok">
            <thead>
                <tr>
                    <th style="width: 4%;">NO</th>
                    <th style="width: 12%;">KODE OBAT</th>
                    <th>NAMA OBAT & SEDIAAN</th>
                    <th style="width: 10%;">SATUAN</th>
                    <th style="width: 13%;">HARGA SATUAN</th>
                    <th style="width: 9%;">STOK MIN</th>
                    <th style="width: 9%;">STOK FISIK</th>
                    <th style="width: 11%;">STATUS</th>
                    <th style="width: 15%;">TOTAL NILAI ASET</th>
                </tr>
            </thead>
            <tbody>
                @foreach($obatList as $idx => $obat)
                    @php
                        $subtotalAset = (float) ($obat->stok * $obat->harga_obat);
                        $statusBadge = match(true) {
                            $obat->stok <= 0 => ['bg' => '#fee2e2', 'color' => '#b91c1c', 'text' => 'Habis'],
                            $obat->stok <= $obat->stok_minimum => ['bg' => '#fef3c7', 'color' => '#b45309', 'text' => 'Menipis'],
                            default => ['bg' => '#dcfce7', 'color' => '#15803d', 'text' => 'Aman'],
                        };
                    @endphp
                    <tr>
                        <td style="text-align: center;">{{ $idx + 1 }}</td>
                        <td style="text-align: center; font-family: monospace;">{{ $obat->kode_obat ?? '-' }}</td>
                        <td><strong>{{ $obat->nama_obat }}</strong></td>
                        <td style="text-align: center;">{{ $obat->satuan ?? 'pcs' }}</td>
                        <td style="text-align: right;">Rp {{ number_format($obat->harga_obat, 0, ',', '.') }}</td>
                        <td style="text-align: center;">{{ $obat->stok_minimum ?? 10 }}</td>
                        <td style="text-align: center; font-weight: 700;">{{ $obat->stok }}</td>
                        <td style="text-align: center;">
                            <span style="background: {{ $statusBadge['bg'] }}; color: {{ $statusBadge['color'] }}; padding: 2px 6px; border-radius: 4px; font-weight: 700; font-size: 10px;">
                                {{ $statusBadge['text'] }}
                            </span>
                        </td>
                        <td style="text-align: right; font-weight: 700;">Rp {{ number_format($subtotalAset, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr style="background: #f1f5f9; font-weight: 700;">
                    <td colspan="6" style="text-align: right;">TOTAL KESELURUHAN:</td>
                    <td style="text-align: center;">{{ number_format($totalFisik, 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ $totalItem }} Item</td>
                    <td style="text-align: right; color: #1e40af; font-size: 12px;">Rp {{ number_format($totalValuasi, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="ttd-grid">
            <div style="text-align: center; width: 260px;">
                <p>Banjarmasin, {{ now()->translatedFormat('d F Y') }}</p>
                <p style="font-weight: 600; margin-bottom: 8px;">Apoteker Penanggung Jawab (APJ),</p>

                <div style="width: 70px; height: 70px; margin: 4px auto; border: 1px solid #cbd5e1; padding: 2px; border-radius: 6px; background: #fff;">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode('STOK_OPNAME_RS_DELTA_' . date('Ymd') . '_' . (auth()->user()->nama ?? 'Apoteker')) }}" 
                         alt="QR Validasi" style="width: 100%; height: 100%; object-fit: contain;">
                </div>

                <p style="font-weight: 700; text-decoration: underline; font-size: 12.5px; margin-top: 4px;">
                    Apt. Dian Permata, S.Farm
                </p>
                <p style="font-size: 10px; color: #64748b;">
                    SIPA: 19931105/SIPA-63.71/2021/2004<br>
                    NIP: 19931105 201903 2 004
                </p>
            </div>
        </div>
    </div>

</body>
</html>
