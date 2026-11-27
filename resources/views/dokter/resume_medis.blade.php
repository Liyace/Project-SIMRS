<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resume Medis Rawat Jalan - {{ $pendaftaran->pasien?->nama_pasien ?? 'Pasien' }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logodelta1.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            font-size: 12.5px;
            line-height: 1.5;
            padding: 30px 20px;
        }

        .no-print {
            max-width: 820px;
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

        .paper {
            max-width: 820px;
            margin: 0 auto;
            background: #ffffff;
            padding: 44px 50px;
            box-shadow: 0 4px 25px rgba(0,0,0,0.08);
            border-radius: 4px;
        }

        .kop-surat {
            display: flex;
            align-items: center;
            gap: 18px;
            border-bottom: 3px double #0f172a;
            padding-bottom: 14px;
            margin-bottom: 20px;
        }
        .kop-logo {
            width: 68px;
            height: 68px;
            object-fit: contain;
        }
        .kop-text h2 {
            font-size: 19px;
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
            font-size: 10.5px;
            color: #64748b;
        }

        .doc-title {
            text-align: center;
            margin-bottom: 20px;
        }
        .doc-title h3 {
            font-size: 15px;
            font-weight: 800;
            text-transform: uppercase;
            text-decoration: underline;
            color: #0f172a;
        }
        .doc-title .doc-num {
            font-size: 11.5px;
            font-weight: 600;
            color: #475569;
            font-family: monospace;
            margin-top: 3px;
        }

        .section-box {
            margin-bottom: 16px;
        }
        .section-header {
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            background: #f1f5f9;
            color: #1e293b;
            padding: 5px 10px;
            border-left: 4px solid #2563eb;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }

        .grid-2col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 3px 4px;
            font-size: 12px;
        }
        .meta-table td.lbl { width: 140px; color: #64748b; }
        .meta-table td.col { width: 10px; }
        .meta-table td.val { font-weight: 600; color: #0f172a; }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            font-size: 11.5px;
        }
        .data-table th, .data-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 10px;
        }
        .data-table th {
            background: #f8fafc;
            color: #334155;
            font-weight: 700;
        }

        .ttd-grid {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        @media print {
            body { background: #fff; padding: 0; }
            .no-print { display: none !important; }
            .paper { box-shadow: none; padding: 0; width: 100%; max-width: 100%; }
            @page { size: A4 portrait; margin: 15mm; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <div style="display: flex; align-items: center; gap: 10px;">
            <a href="{{ route('dokter.pelayanan') }}" class="btn-action btn-back">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Dokter
            </a>
            <span style="font-size: 12.5px; color: #64748b;">
                Pasien: <strong>{{ $pendaftaran->pasien?->nama_pasien ?? '-' }}</strong> ({{ $pendaftaran->no_antrian }})
            </span>
        </div>

        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" class="btn-action btn-print">
                <i class="fa-solid fa-print"></i> Cetak / Print
            </button>
            <a href="{{ route('dokter.resume-medis', $pendaftaran->id_pendaftaran) }}?download=1" class="btn-action btn-pdf">
                <i class="fa-solid fa-file-pdf"></i> Download PDF
            </a>
        </div>
    </div>

    <div class="paper">
        <!-- KOP RESMI -->
        <div class="kop-surat">
            <img src="{{ asset('images/logodelta1.svg') }}" alt="Logo Delta" class="kop-logo" style="background: #2563eb; border-radius: 10px; padding: 8px;">
            <div class="kop-text">
                <h2>RUMAH SAKIT DELTA</h2>
                <h4>INSTALASI REKAM MEDIS & PELAYANAN RAWAT JALAN</h4>
                <p>Jl. Jenderal Ahmad Yani Km. 4.5 No. 128, Banjarmasin, Kalimantan Selatan 70234<br>
                Telp: (0511) 7654321 • Surel: rekam.medis@rsdelta.co.id</p>
            </div>
        </div>

        <div class="doc-title">
            <h3>RINGKASAN RESUME MEDIS RAWAT JALAN</h3>
            <div style="font-size: 11px; color: #64748b; font-weight: 600; margin-top: 2px;">(DISCHARGE CLINICAL SUMMARY)</div>
            <div class="doc-num">NO. DOKUMEN: {{ $nomorResume }}</div>
        </div>

        <!-- BAGIAN 1: IDENTITAS PASIEN & KUNJUNGAN -->
        <div class="section-box">
            <div class="section-header">I. Data Pasien & Registrasi Kunjungan</div>
            <div class="grid-2col">
                <table class="meta-table">
                    <tr>
                        <td class="lbl">Nama Pasien</td>
                        <td class="col">:</td>
                        <td class="val">{{ $pendaftaran->pasien?->nama_pasien ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">No. Rekam Medis (RM)</td>
                        <td class="col">:</td>
                        <td class="val">{{ $pendaftaran->pasien?->rm ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">NIK Pasien</td>
                        <td class="col">:</td>
                        <td class="val">{{ $pendaftaran->pasien?->nik ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Jenis Kelamin</td>
                        <td class="col">:</td>
                        <td class="val">{{ $pendaftaran->pasien?->jenis_kelamin === 'P' ? 'Perempuan' : 'Laki-laki' }}</td>
                    </tr>
                </table>

                <table class="meta-table">
                    <tr>
                        <td class="lbl">Poliklinik Tujuan</td>
                        <td class="col">:</td>
                        <td class="val">{{ $pendaftaran->poli?->nama_poli ?? 'Poli Umum' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">No. Registrasi / Antrean</td>
                        <td class="col">:</td>
                        <td class="val">{{ $pendaftaran->no_antrian }} ({{ $pendaftaran->no_pendaftaran }})</td>
                    </tr>
                    <tr>
                        <td class="lbl">Tanggal Pelayanan</td>
                        <td class="col">:</td>
                        <td class="val">{{ $pendaftaran->tanggal_daftar ? $pendaftaran->tanggal_daftar->format('d/m/Y H:i') : '-' }} WITA</td>
                    </tr>
                    <tr>
                        <td class="lbl">Metode Pembayaran</td>
                        <td class="col">:</td>
                        <td class="val" style="text-transform: uppercase;">{{ $pendaftaran->cara_bayar ?? 'UMUM' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- BAGIAN 2: TANDA-TANDA VITAL (TTV DARI SKRINING PERAWAT) -->
        @php $ttv = $pendaftaran->screeningAwal; @endphp
        <div class="section-box">
            <div class="section-header">II. Tanda-Tanda Vital & Pemeriksaan Awal (Skrining Perawat)</div>
            @if($ttv)
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; font-size: 11.5px; background: #f8fafc; padding: 8px 12px; border-radius: 6px; border: 1px solid #e2e8f0;">
                    <div>Tekanan Darah: <strong>{{ $ttv->sistol ?? '-' }}/{{ $ttv->diastol ?? '-' }} mmHg</strong></div>
                    <div>Denyut Nadi: <strong>{{ $ttv->denyut_nadi ?? '-' }} x/mnt</strong></div>
                    <div>Suhu Tubuh: <strong>{{ $ttv->suhu_tubuh ?? '-' }} °C</strong></div>
                    <div>Laju Nafas: <strong>{{ $ttv->laju_pernapasan ?? '-' }} x/mnt</strong></div>
                    <div>Berat Badan: <strong>{{ $ttv->berat_badan ?? '-' }} kg</strong></div>
                    <div>Tinggi Badan: <strong>{{ $ttv->tinggi_badan ?? '-' }} cm</strong></div>
                    <div>Saturasi O2: <strong>{{ $ttv->saturasi_oksigen ?? '98' }}%</strong></div>
                    <div>Perawat: <strong>{{ $ttv->perawat?->nama ?? 'Perawat Poli' }}</strong></div>
                </div>
            @else
                <p style="font-size: 11px; color: #64748b; font-style: italic;">Pemeriksaan tanda vital langsung dilakukan di ruang dokter.</p>
            @endif
        </div>

        <!-- BAGIAN 3: ANAMNESIS & DIAGNOSA MEDIS DOKTER -->
        @php $rm = $pendaftaran->rekamMedis; @endphp
        <div class="section-box">
            <div class="section-header">III. Anamnesis Klinis & Diagnosa Medis Dokter</div>
            <table class="meta-table">
                <tr>
                    <td class="lbl" style="width: 180px;">Keluhan Utama (Anamnesis)</td>
                    <td class="col">:</td>
                    <td class="val">{{ $rm?->anamnesis ?? ($pendaftaran->keluhan_awal ?? 'Pemeriksaan rutin rawat jalan') }}</td>
                </tr>
                <tr>
                    <td class="lbl">Diagnosa Utama</td>
                    <td class="col">:</td>
                    <td class="val" style="color: #2563eb;">
                        {{ $rm?->diagnosa_utama ?? 'Observasi Klinis' }}
                        @if(!empty($rm?->kode_icd10))
                            <span style="font-size: 10px; background: #eff6ff; padding: 2px 6px; border-radius: 4px; border: 1px solid #bfdbfe;">
                                ICD-10: {{ $rm->kode_icd10 }}
                            </span>
                        @endif
                    </td>
                </tr>
                @if(!empty($rm?->diagnosa_sekunder))
                <tr>
                    <td class="lbl">Diagnosa Sekunder / Komorbid</td>
                    <td class="col">:</td>
                    <td class="val">{{ $rm->diagnosa_sekunder }}</td>
                </tr>
                @endif
            </table>
        </div>

        <!-- BAGIAN 4: TINDAKAN MEDIS -->
        <div class="section-box">
            <div class="section-header">IV. Tindakan Medis & Prosedur Klinis yang Diberikan</div>
            @if($rm && $rm->tindakanMedis && $rm->tindakanMedis->count() > 0)
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 8%;">No</th>
                            <th>Nama Tindakan / Prosedur Medis</th>
                            <th style="width: 15%; text-align: center;">Jumlah (Qty)</th>
                            <th style="width: 30%;">Keterangan Pelayanan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rm->tindakanMedis as $idx => $tm)
                            <tr>
                                <td style="text-align: center;">{{ $idx + 1 }}</td>
                                <td><strong>{{ $tm->layanan?->nama_layanan ?? "Tindakan Medis #{$tm->id_layanan}" }}</strong></td>
                                <td style="text-align: center;">{{ $tm->qty ?? 1 }}</td>
                                <td style="color: #64748b;">Selesai dikerjakan dokter</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="font-size: 11px; color: #64748b; font-style: italic;">Konsultasi & Pemeriksaan Fisik Poliklinik.</p>
            @endif
        </div>

        <!-- BAGIAN 5: TERAPI OBAT (E-RESEP) -->
        <div class="section-box">
            <div class="section-header">V. Terapi Medikamentosa / Resep Obat Pasien Pulang</div>
            @if($pendaftaran->resep && $pendaftaran->resep->details && $pendaftaran->resep->details->count() > 0)
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 8%;">No</th>
                            <th>Nama Obat</th>
                            <th style="width: 15%; text-align: center;">Jumlah</th>
                            <th style="width: 40%;">Aturan Pakai & Signa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendaftaran->resep->details as $idx => $d)
                            <tr>
                                <td style="text-align: center;">{{ $idx + 1 }}</td>
                                <td><strong>{{ $d->obat?->nama_obat ?? 'Obat' }}</strong></td>
                                <td style="text-align: center;">{{ $d->jumlah }} {{ $d->obat?->satuan ?? 'tab' }}</td>
                                <td style="color: #059669; font-weight: 600;">{{ $d->aturan_pakai ?? 'Sesuai petunjuk dokter' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="font-size: 11px; color: #64748b; font-style: italic;">Pasien tidak diberikan resep obat farmasi.</p>
            @endif
        </div>

        <!-- BAGIAN 6: EDUKASI & ANJURAN TINDAK LANJUT -->
        <div class="section-box">
            <div class="section-header">VI. Edukasi Pasien & Rencana Tindak Lanjut</div>
            <p style="font-size: 12px; margin-bottom: 4px;">
                <strong>Instruksi Dokter:</strong> {{ $rm?->catatan_edukasi ?? 'Istirahat teratur, minum obat tepat waktu, dan segera kembali jika gejala memberat.' }}
            </p>
        </div>

        <!-- TANDA TANGAN DOKTER & QR -->
        <div class="ttd-grid">
            <div style="font-size: 10px; color: #94a3b8; max-width: 320px;">
                * Dokumen ini merupakan resume medis resmi rawat jalan RS Delta yang diterbitkan secara elektronik dan sah digunakan untuk keperluan administrasi medis & klaim asuransi.
            </div>

            <div style="text-align: center; width: 260px;">
                <p style="font-size: 11.5px;">Banjarmasin, {{ now()->translatedFormat('d F Y') }}</p>
                <p style="font-weight: 600; font-size: 12px; margin-bottom: 6px;">Dokter Penanggung Jawab Pelayanan,</p>

                <div style="width: 75px; height: 75px; margin: 4px auto; border: 1px solid #cbd5e1; padding: 3px; border-radius: 6px; background: #fff;">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode('RESUME_MEDIS_RS_DELTA_' . $nomorResume . '_' . ($dokter->nama ?? 'Dokter')) }}" 
                         alt="QR Validasi" style="width: 100%; height: 100%; object-fit: contain;">
                </div>

                <p style="font-weight: 700; text-decoration: underline; font-size: 13px; margin-top: 4px;">
                    {{ $dokter->nama ?? 'dr. Budi Santoso, Sp.PD' }}
                </p>
                <p style="font-size: 10.5px; color: #64748b;">
                    NIP: {{ $dokter->nip ?? '19850312 201001 1 002' }}
                </p>
            </div>
        </div>

    </div>

</body>
</html>
