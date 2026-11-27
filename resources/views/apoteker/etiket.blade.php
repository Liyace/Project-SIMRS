<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Etiket Obat - {{ $resep->no_resep }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logodelta1.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&family=Roboto+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            padding: 20px;
        }
        .no-print-bar {
            max-width: 800px;
            margin: 0 auto 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 14px 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid #cbd5e1;
        }
        .btn-print {
            background: #2563eb;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);
        }
        .btn-print:hover {
            background: #1d4ed8;
        }
        .etiket-grid {
            max-width: 800px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(360px, 1fr));
            gap: 20px;
        }
        /* Format Stiker Etiket Farmasi Standar (7 x 5 cm) */
        .etiket-sticker {
            background: #ffffff;
            border: 2px solid #0f172a;
            border-radius: 10px;
            padding: 16px 18px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
            position: relative;
            page-break-inside: avoid;
        }
        .etiket-header {
            text-align: center;
            border-bottom: 2px dashed #0f172a;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .rs-name {
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #0f172a;
        }
        .rs-sub {
            font-size: 9px;
            color: #475569;
            margin-top: 1px;
        }
        .etiket-meta {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin-bottom: 8px;
            color: #334155;
        }
        .etiket-patient {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 6px 10px;
            margin-bottom: 10px;
            font-size: 11.5px;
        }
        .etiket-patient strong {
            font-size: 13px;
            color: #0f172a;
        }
        .aturan-box {
            text-align: center;
            background: #eff6ff;
            border: 1.5px solid #3b82f6;
            border-radius: 8px;
            padding: 10px 8px;
            margin-bottom: 10px;
        }
        .aturan-title {
            font-size: 10px;
            font-weight: 600;
            color: #1e40af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .aturan-main {
            font-size: 16px;
            font-weight: 800;
            color: #1d4ed8;
            margin: 3px 0;
        }
        .aturan-timing {
            font-size: 11px;
            font-weight: 600;
            color: #1e3a8a;
        }
        .medicine-name {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .etiket-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 10px;
            color: #64748b;
            border-top: 1px dashed #cbd5e1;
            padding-top: 8px;
            margin-top: 8px;
        }

        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .no-print-bar {
                display: none !important;
            }
            .etiket-grid {
                max-width: 100% !important;
                display: block !important;
            }
            .etiket-sticker {
                box-shadow: none !important;
                border: 2px solid #000000 !important;
                margin-bottom: 20px !important;
                page-break-inside: avoid !important;
            }
        }
    </style>
</head>
<body>

    <!-- TOPBAR TOMBOL CETAK & KEMBALI -->
    <div class="no-print-bar">
        <div>
            <strong style="font-size: 15px; color: #1e293b; display: block;">Etiket Stiker Resep: {{ $resep->no_resep }}</strong>
            <span style="font-size: 12px; color: #64748b;">
                Pasien: <strong>{{ $resep->pendaftaran?->pasien?->nama_pasien }}</strong> ({{ $resep->pendaftaran?->pasien?->rm }}) • {{ count($resep->details) }} Item Obat
            </span>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.close();" style="background: #f1f5f9; border: 1px solid #cbd5e1; color: #475569; padding: 10px 16px; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer;">
                <i class="fa-solid fa-xmark"></i> Tutup
            </button>
            <button class="btn-print" onclick="window.print();">
                <i class="fa-solid fa-print"></i> Cetak Semua Etiket
            </button>
        </div>
    </div>

    <!-- DAFTAR STIKER ETIKET OBAT (1 STIKER PER ITEM OBAT) -->
    <div class="etiket-grid">
        @foreach($resep->details as $index => $item)
            <div class="etiket-sticker">
                <!-- HEADER STIKER -->
                <div class="etiket-header">
                    <div class="rs-name">INSTALASI FARMASI RS DELTA</div>
                    <div class="rs-sub">Jl. Brigjen Hasan Basri No. 12, Banjarmasin • Telp. (0511) 7654321</div>
                    <div class="rs-sub">Apoteker: {{ $resep->apoteker->nama ?? 'Apt. Dian Permata, S.Farm' }}</div>
                </div>

                <!-- NO RESEP & TANGGAL -->
                <div class="etiket-meta">
                    <span><strong>No:</strong> {{ $resep->no_resep }}</span>
                    <span><strong>Tgl:</strong> {{ $resep->created_at->format('d/m/Y') }}</span>
                </div>

                <!-- DATA PASIEN -->
                <div class="etiket-patient">
                    <div style="display: flex; justify-content: space-between;">
                        <strong>{{ $resep->pendaftaran?->pasien?->nama_pasien ?? 'Pasien Umum' }}</strong>
                        <span style="font-family: monospace; font-weight: 700;">{{ $resep->pendaftaran?->pasien?->rm }}</span>
                    </div>
                    <div style="color: #64748b; font-size: 10.5px; margin-top: 2px;">
                        Poli: {{ $resep->pendaftaran?->poli?->nama_poli ?? 'Poliklinik' }} • Dr: {{ $resep->dokter->nama ?? 'dr. Umum' }}
                    </div>
                </div>

                <!-- NAMA OBAT -->
                <div style="margin-bottom: 8px;">
                    <div class="medicine-name">{{ $item->obat->nama_obat ?? 'Item Obat' }}</div>
                    <div style="font-size: 11px; color: #475569;">
                        Jumlah: <strong>{{ $item->jumlah }} {{ $item->obat->satuan ?? 'satuan' }}</strong>
                    </div>
                </div>

                <!-- ATURAN PAKAI UTAMA -->
                <div class="aturan-box">
                    <div class="aturan-title">Aturan Penggunaan</div>
                    <div class="aturan-main">{{ $item->aturan_pakai ?? 'Sesuai Anjuran Dokter' }}</div>
                    <div class="aturan-timing">
                        @if(stripos($item->aturan_pakai, 'sebelum') !== false)
                            [✓] Diminum Sebelum Makan
                        @else
                            [✓] Diminum Sesudah Makan
                        @endif
                    </div>
                </div>

                <!-- FOOTER ETIKET -->
                <div class="etiket-footer">
                    <span>Simpan di tempat sejuk & kering (15-25°C)</span>
                    <span style="font-weight: 700; font-family: monospace;">DELTA-FARMA</span>
                </div>
            </div>
        @endforeach
    </div>

</body>
</html>
