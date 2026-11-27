<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salinan Resep - {{ $resep->no_resep }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logodelta1.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Playfair+Display:ital,wght@1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            font-size: 13px;
            line-height: 1.6;
            padding: 30px 20px;
        }

        .no-print {
            max-width: 680px;
            margin: 0 auto 20px auto;
            background: #ffffff;
            border-radius: 12px;
            padding: 14px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            flex-wrap: wrap;
            gap: 12px;
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: 0.2s;
        }
        .btn-print { background: #2563eb; color: #fff; }
        .btn-print:hover { background: #1d4ed8; }
        .btn-pdf { background: #059669; color: #fff; }
        .btn-pdf:hover { background: #047857; }
        .btn-back { background: #f1f5f9; color: #475569; }
        .btn-back:hover { background: #e2e8f0; }

        /* KERTAS RESEP FORMAT KLASIK STANDAR FARMASI */
        .paper-resep {
            max-width: 680px;
            margin: 0 auto;
            background: #ffffff;
            padding: 40px 48px;
            box-shadow: 0 4px 25px rgba(0,0,0,0.08);
            border-radius: 4px;
            border-top: 6px solid #2563eb;
            position: relative;
        }

        .kop-farmasi {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .kop-farmasi h2 {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.5px;
        }
        .kop-farmasi h4 {
            font-size: 13px;
            font-weight: 700;
            color: #2563eb;
        }
        .kop-farmasi p {
            font-size: 11px;
            color: #64748b;
        }

        .title-copy {
            text-align: center;
            margin-bottom: 20px;
        }
        .title-copy h3 {
            font-size: 15px;
            font-weight: 800;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .title-copy span {
            font-size: 11px;
            color: #64748b;
            font-style: italic;
        }

        .meta-resep {
            width: 100%;
            margin-bottom: 20px;
            font-size: 12.5px;
        }
        .meta-resep td {
            padding: 4px 0;
            vertical-align: top;
        }
        .meta-resep td.lbl { width: 140px; color: #64748b; }
        .meta-resep td.col { width: 12px; }
        .meta-resep td.val { font-weight: 600; color: #0f172a; }

        .recipe-list {
            margin: 24px 0;
            border-top: 1px dashed #cbd5e1;
            border-bottom: 1px dashed #cbd5e1;
            padding: 16px 0;
        }
        .recipe-item {
            margin-bottom: 16px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .recipe-r {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            font-weight: 700;
            color: #1e3a8a;
            line-height: 1;
            margin-right: 12px;
            user-select: none;
        }
        .recipe-content {
            flex: 1;
        }
        .recipe-name {
            font-weight: 700;
            font-size: 13.5px;
            color: #0f172a;
        }
        .recipe-signa {
            font-size: 12px;
            font-style: italic;
            color: #059669;
            margin-top: 2px;
        }
        .recipe-det {
            font-family: monospace;
            font-size: 12px;
            font-weight: 800;
            color: #2563eb;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            padding: 2px 8px;
            border-radius: 4px;
        }

        .pcc-stamp {
            display: inline-block;
            border: 2px solid #b91c1c;
            color: #b91c1c;
            padding: 4px 12px;
            font-weight: 800;
            font-size: 11px;
            letter-spacing: 1px;
            border-radius: 4px;
            transform: rotate(-6deg);
            margin-top: 10px;
        }

        .ttd-farmasi-grid {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        @media print {
            body { background: #fff; padding: 0; }
            .no-print { display: none !important; }
            .paper-resep { box-shadow: none; border-top: none; padding: 0; width: 100%; max-width: 100%; }
            @page { size: A5 portrait; margin: 15mm; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <div style="display: flex; align-items: center; gap: 10px;">
            <a href="{{ route('apoteker.riwayat') }}" class="btn-action btn-back">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Riwayat Farmasi
            </a>
            <span style="font-size: 12.5px; color: #64748b;">
                Resep: <strong>{{ $resep->no_resep }}</strong>
            </span>
        </div>

        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" class="btn-action btn-print">
                <i class="fa-solid fa-print"></i> Cetak / Print
            </button>
            <a href="{{ route('apoteker.copy-resep', $resep->id_resep) }}?download=1" class="btn-action btn-pdf">
                <i class="fa-solid fa-file-pdf"></i> Download PDF
            </a>
        </div>
    </div>

    <div class="paper-resep">
        <div class="kop-farmasi">
            <h2>INSTALASI FARMASI RS DELTA</h2>
            <h4>APOTEK PELAYANAN RAWAT JALAN & KLINIK</h4>
            <p>Jl. Jenderal Ahmad Yani Km. 4.5 No. 128, Banjarmasin • Telp: (0511) 7654321<br>
            Apoteker Penanggung Jawab: Apt. Dian Permata, S.Farm (SIPA: 19931105/SIPA-63.71/2021/2004)</p>
        </div>

        <div class="title-copy">
            <h3>SALINAN RESEP</h3>
            <span>(APOGRAPH / COPY RESEP)</span>
        </div>

        <table class="meta-resep">
            <tr>
                <td class="lbl">Resep Asli Dokter</td>
                <td class="col">:</td>
                <td class="val">{{ $resep->dokter?->nama ?? 'dr. Budi Santoso, Sp.PD' }}</td>
            </tr>
            <tr>
                <td class="lbl">Ditulis Tanggal</td>
                <td class="col">:</td>
                <td class="val">{{ $resep->created_at ? $resep->created_at->format('d/m/Y') : '-' }}</td>
            </tr>
            <tr>
                <td class="lbl">No. Resep</td>
                <td class="col">:</td>
                <td class="val" style="font-family: monospace;">{{ $resep->no_resep }}</td>
            </tr>
            <tr>
                <td class="lbl">Pro / Untuk Pasien</td>
                <td class="col">:</td>
                <td class="val">{{ $resep->pendaftaran?->pasien?->nama_pasien ?? '-' }} (RM: {{ $resep->pendaftaran?->pasien?->rm ?? '-' }})</td>
            </tr>
            <tr>
                <td class="lbl">Poliklinik Asal</td>
                <td class="col">:</td>
                <td class="val">{{ $resep->pendaftaran?->poli?->nama_poli ?? 'Poli Umum' }}</td>
            </tr>
        </table>

        <!-- DAFTAR R/ RESEP -->
        <div class="recipe-list">
            @forelse($resep->details as $d)
                <div class="recipe-item">
                    <div style="display: flex; align-items: flex-start;">
                        <span class="recipe-r">R/</span>
                        <div class="recipe-content">
                            <div class="recipe-name">
                                {{ $d->obat?->nama_obat ?? 'Obat' }} &nbsp;—&nbsp; No. {{ $d->jumlah }} {{ $d->obat?->satuan ?? 'Tab' }}
                            </div>
                            <div class="recipe-signa">
                                S. {{ $d->aturan_pakai ?? 'Sesuai petunjuk dokter' }}
                            </div>
                        </div>
                    </div>
                    <div class="recipe-det">
                        {{ $resep->status_resep === 'selesai' ? 'det' : 'ne det' }}
                    </div>
                </div>
            @empty
                <p style="text-align: center; color: #94a3b8; font-style: italic;">Tidak ada rincian obat.</p>
            @endforelse
        </div>

        <!-- STAMPEL & TANDA TANGAN -->
        <div class="ttd-farmasi-grid">
            <div>
                <div class="pcc-stamp">
                    P. C. C.<br>
                    <span style="font-size: 8px; font-weight: normal;">(Pro Copie Conforme)</span>
                </div>
                <div style="font-size: 10px; color: #64748b; margin-top: 6px;">
                    *Salinan sesuai dengan resep aslinya
                </div>
            </div>

            <div style="text-align: center; width: 220px;">
                <p style="font-size: 11.5px;">Banjarmasin, {{ now()->translatedFormat('d F Y') }}</p>
                <p style="font-weight: 600; font-size: 12px; margin-bottom: 6px;">Apoteker,</p>

                <div style="width: 65px; height: 65px; margin: 4px auto; border: 1px solid #cbd5e1; padding: 2px; border-radius: 6px; background: #fff;">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode('COPY_RESEP_RS_DELTA_' . $resep->no_resep . '_APOTEKER') }}" 
                         alt="QR Validasi" style="width: 100%; height: 100%; object-fit: contain;">
                </div>

                <p style="font-weight: 700; text-decoration: underline; font-size: 12px; margin-top: 4px;">
                    Apt. Dian Permata, S.Farm
                </p>
                <p style="font-size: 9.5px; color: #64748b;">
                    SIPA: 19931105/SIPA-63.71/2021/2004
                </p>
            </div>
        </div>

    </div>

</body>
</html>
