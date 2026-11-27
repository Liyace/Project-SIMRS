<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Karcis Antrean {{ $pendaftaran->no_antrian }} - RS Delta</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logodelta1.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Courier+Prime:wght@400;700&family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
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
            color: #1e293b;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .action-bar {
            margin-bottom: 20px;
            display: flex;
            gap: 12px;
        }
        .btn-print {
            background: #1a4a8d;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(26, 74, 141, 0.25);
        }
        .btn-close {
            background: #ffffff;
            color: #475569;
            border: 1px solid #cbd5e1;
            padding: 10px 18px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .ticket-wrapper {
            width: 320px;
            background: #ffffff;
            padding: 24px 20px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            text-align: center;
        }
        .hospital-header {
            border-bottom: 1.5px dashed #94a3b8;
            padding-bottom: 12px;
            margin-bottom: 14px;
        }
        .hospital-name {
            font-size: 16px;
            font-weight: 800;
            color: #1a4a8d;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .hospital-sub {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
        }
        .queue-badge-title {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .queue-number {
            font-size: 44px;
            font-weight: 900;
            color: #1a4a8d;
            line-height: 1.1;
            margin: 6px 0;
            letter-spacing: 2px;
        }
        .clinic-name {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
        }
        .doctor-name {
            font-size: 11px;
            color: #475569;
            margin-top: 2px;
        }
        .ticket-details {
            border-top: 1.5px dashed #94a3b8;
            border-bottom: 1.5px dashed #94a3b8;
            padding: 12px 0;
            margin: 14px 0;
            text-align: left;
            font-size: 11px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .detail-row:last-child {
            margin-bottom: 0;
        }
        .detail-label {
            color: #64748b;
        }
        .detail-value {
            font-weight: 600;
            color: #1e293b;
            text-align: right;
        }
        .ticket-footer {
            font-size: 10px;
            color: #64748b;
            line-height: 1.4;
        }
        .barcode-visual {
            font-family: 'Courier Prime', monospace;
            font-size: 16px;
            letter-spacing: 3px;
            font-weight: 700;
            color: #334155;
            margin: 10px 0 6px;
            user-select: none;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .action-bar {
                display: none !important;
            }
            .ticket-wrapper {
                box-shadow: none;
                border: none;
                width: 100%;
                max-width: 300px;
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <!-- TOMBOL AKSI LAYAR (DISEMBUNYIKAN SAAT CETAK) -->
    <div class="action-bar">
        <button type="button" class="btn-print" onclick="window.print()">
            <i class="fa-solid fa-print"></i> Cetak Karcis
        </button>
        <a href="{{ route('loket.index') }}" class="btn-close">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Loket
        </a>
    </div>

    <!-- STRUK KARCIS ANTREAN THERMAL -->
    <div class="ticket-wrapper">
        <div class="hospital-header">
            <div style="display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 4px;">
                <img src="{{ asset('images/logodelta1.svg') }}" alt="Logo" style="width: 24px; height: 24px;">
                <div class="hospital-name">Rumah Sakit Delta</div>
            </div>
            <div class="hospital-sub">Jl. Ahmad Yani Km. 4.5 No. 12 • Telp (0511) 789012</div>
            <div class="hospital-sub">Dukungan Pelayanan Kesehatan Terpadu</div>
        </div>

        <div class="queue-badge-title">NOMOR ANTREAN POLIKLINIK</div>
        <div class="queue-number">{{ $pendaftaran->no_antrian }}</div>
        <div class="clinic-name">{{ $pendaftaran->poli->nama_poli ?? 'Poliklinik Rawat Jalan' }}</div>
        <div class="doctor-name">{{ $pendaftaran->dokter->nama ?? 'Dokter Bertugas' }}</div>

        <div class="ticket-details">
            <div class="detail-row">
                <span class="detail-label">No. Pendaftaran:</span>
                <span class="detail-value">{{ $pendaftaran->no_pendaftaran }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">No. Rekam Medis:</span>
                <span class="detail-value" style="color: #1a4a8d; font-weight: 700;">{{ $pendaftaran->pasien->rm ?? '-' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Nama Pasien:</span>
                <span class="detail-value">{{ $pendaftaran->pasien->nama_pasien ?? '-' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Penjamin / Bayar:</span>
                <span class="detail-value" style="text-transform: uppercase;">{{ $pendaftaran->cara_bayar }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Waktu Daftar:</span>
                <span class="detail-value">{{ $pendaftaran->tanggal_daftar ? $pendaftaran->tanggal_daftar->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }} WITA</span>
            </div>
        </div>

        <div class="barcode-visual">
            ||| | | |||| | ||| |||| |
        </div>

        <div class="ticket-footer">
            <strong style="color: #0f172a; display: block; margin-bottom: 3px;">PETUNJUK PASIEN:</strong>
            1. Harap segera menuju ke <strong>Ruang Skrining TTV Perawat</strong> untuk pemeriksaan tanda-tanda vital.<br>
            2. Simpan karcis ini hingga selesai pemeriksaan dokter dan transaksi apotek/kasir.<br>
            <em>Semoga lekas sembuh!</em>
        </div>
    </div>

    <script>
        // Otomatis tawarkan dialog cetak saat halaman dibuka
        window.addEventListener('load', function() {
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
