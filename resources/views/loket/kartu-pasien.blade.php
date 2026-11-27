<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Pasien - {{ $pasien->nama_pasien }} ({{ $pasien->rm }})</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logodelta1.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Share+Tech+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: #f1f5f9;
            color: #0f172a;
            padding: 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        /* TOOLBAR */
        .toolbar {
            width: 100%;
            max-width: 780px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding: 12px 18px;
            background: white;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }
        .btn-back {
            background: #f1f5f9;
            color: #475569;
        }
        .btn-back:hover {
            background: #e2e8f0;
        }
        .btn-print {
            background: #1C4D8D;
            color: white;
        }
        .btn-print:hover {
            background: #163d70;
        }

        /* CONTAINER KARTU */
        .cards-wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 28px;
            justify-content: center;
        }

        /* STANDAR CR80: 85.6mm x 53.98mm (Rasio 1.586, di layar ~420px x 265px) */
        .id-card {
            width: 420px;
            height: 265px;
            border-radius: 14px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            background: #ffffff;
            border: 1px solid #cbd5e1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* SISI DEPAN KARTU */
        .card-front {
            background: linear-gradient(135deg, #ffffff 0%, #f0f7ff 60%, #e0f2fe 100%);
            padding: 14px 18px;
            border-top: 4px solid #1C4D8D;
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1.5px solid #0284c7;
            padding-bottom: 8px;
        }
        .card-logo {
            width: 38px;
            height: 38px;
            object-fit: contain;
        }
        .card-title h2 {
            font-size: 14px;
            font-weight: 800;
            color: #1C4D8D;
            letter-spacing: 0.5px;
            line-height: 1.1;
        }
        .card-title p {
            font-size: 9px;
            color: #0284c7;
            font-weight: 600;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }

        .card-body {
            display: flex;
            gap: 12px;
            margin-top: 8px;
            align-items: center;
        }
        .card-avatar-box {
            width: 68px;
            height: 82px;
            border-radius: 8px;
            border: 1.5px solid #0284c7;
            overflow: hidden;
            background: #fff;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card-avatar-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .card-details {
            flex: 1;
            font-size: 11px;
            line-height: 1.35;
        }
        .card-nama {
            font-size: 13.5px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 270px;
        }
        .card-rm-badge {
            display: inline-block;
            background: #1C4D8D;
            color: #ffffff;
            font-family: 'Share Tech Mono', monospace;
            font-size: 14px;
            font-weight: 700;
            padding: 1px 8px;
            border-radius: 4px;
            margin-bottom: 4px;
            letter-spacing: 1px;
        }
        .detail-row {
            display: flex;
            color: #334155;
            font-size: 10.5px;
        }
        .detail-label {
            width: 70px;
            color: #64748b;
            font-weight: 500;
        }

        .card-footer {
            margin-top: 4px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 3px 6px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .barcode-svg-wrap {
            width: 100%;
            max-height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .barcode-caption {
            font-family: 'Share Tech Mono', monospace;
            font-size: 10px;
            color: #0f172a;
            letter-spacing: 2px;
            font-weight: 600;
            margin-top: 1px;
        }

        /* SISI BELAKANG KARTU */
        .card-back {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: #f1f5f9;
            padding: 16px 20px;
            border-top: 4px solid #38bdf8;
        }
        .card-back-title {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #38bdf8;
            text-transform: uppercase;
            margin-bottom: 8px;
            border-bottom: 1px solid #334155;
            padding-bottom: 4px;
        }
        .card-rules {
            font-size: 9.5px;
            line-height: 1.45;
            color: #cbd5e1;
            margin-bottom: 12px;
        }
        .card-rules li {
            margin-left: 14px;
            margin-bottom: 4px;
        }
        .card-back-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid #334155;
            padding-top: 8px;
        }
        .card-back-contact {
            font-size: 9px;
            color: #94a3b8;
            line-height: 1.35;
        }
        .card-back-qr {
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 6px;
            padding: 3px;
        }
        .card-back-qr img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        /* PRINT STYLES */
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .toolbar {
                display: none !important;
            }
            .cards-wrapper {
                gap: 15mm;
            }
            .id-card {
                box-shadow: none;
                border: 0.5px solid #94a3b8;
                page-break-inside: avoid;
            }
            @page {
                size: A4 portrait;
                margin: 15mm;
            }
        }
    </style>
</head>
<body>

    <!-- TOOLBAR -->
    <div class="toolbar">
        <div style="display: flex; align-items: center; gap: 10px;">
            <a href="javascript:history.back()" class="btn-action btn-back">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <span style="font-size: 13px; font-weight: 600; color: #1e293b;">
                Kartu Berobat Pasien RS Delta
            </span>
        </div>
        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" class="btn-action btn-print">
                <i class="fa-solid fa-print"></i> Cetak Kartu (CR80)
            </button>
        </div>
    </div>

    <!-- CARDS DISPLAY (DEPAN & BELAKANG) -->
    <div class="cards-wrapper">

        <!-- SISI DEPAN (FRONT) -->
        <div class="id-card card-front">
            <div class="card-header">
                <img src="{{ asset('images/logodelta-color.svg') }}" alt="Logo Delta" class="card-logo">
                <div class="card-title">
                    <h2>RUMAH SAKIT DELTA</h2>
                    <p>Kartu Identitas Berobat Pasien</p>
                </div>
            </div>

            <div class="card-body">
                <div class="card-avatar-box">
                    <img src="{{ $pasien->foto_url ?? asset('images/avatar_male.jpg') }}" alt="{{ $pasien->nama_pasien }}">
                </div>
                <div class="card-details">
                    <div class="card-rm-badge">{{ $pasien->rm }}</div>
                    <div class="card-nama">{{ $pasien->nama_pasien }}</div>
                    
                    <div class="detail-row">
                        <span class="detail-label">NIK KTP</span>
                        <span>: {{ $pasien->nik }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Tgl. Lahir</span>
                        <span>: {{ $pasien->tanggal_lahir ? \Carbon\Carbon::parse($pasien->tanggal_lahir)->translatedFormat('d F Y') : '-' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Gender</span>
                        <span>: {{ $pasien->jenis_kelamin === 'P' ? 'Perempuan' : 'Laki-laki' }}</span>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="barcode-svg-wrap">
                    {!! $barcodeSvg !!}
                </div>
                <div class="barcode-caption">{{ $pasien->rm }}</div>
            </div>
        </div>

        <!-- SISI BELAKANG (BACK) -->
        <div class="id-card card-back">
            <div>
                <div class="card-back-title">
                    <i class="fa-solid fa-circle-info"></i> Ketentuan Penggunaan Kartu
                </div>
                <ul class="card-rules">
                    <li>Kartu ini adalah identitas resmi rekam medis pasien di RS Delta.</li>
                    <li>Wajib dibawa setiap kali datang berobat ke loket admisi pendaftaran.</li>
                    <li>Kartu ini tidak dapat dipindahtangankan kepada orang lain.</li>
                    <li>Bila kartu hilang atau rusak, segera lapor ke bagian admisi loket RS.</li>
                </ul>
            </div>

            <div class="card-back-footer">
                <div class="card-back-contact">
                    <strong>RUMAH SAKIT DELTA</strong><br>
                    Jl. Brigjen Hasan Basri No. 45, Banjarmasin<br>
                    Emergency Call: (0511) 330-1122 | WhatsApp: 0812-3456-7801
                </div>
                <div class="card-back-qr">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode('RSDELTA_PASIEN_' . $pasien->rm . '_' . $pasien->nik) }}" alt="QR Pasien">
                </div>
            </div>
        </div>

    </div>

</body>
</html>
