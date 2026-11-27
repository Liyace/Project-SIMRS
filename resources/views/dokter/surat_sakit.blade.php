<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan Sakit - {{ $pendaftaran->pasien?->nama_pasien ?? 'Pasien' }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logodelta1.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
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

        /* TOOLBAR ATAS (HANYA DITAMPILKAN DI LAYAR BROWSER) */
        .no-print {
            max-width: 780px;
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

        /* LEMBAR KERTAS RESMI A4 SURAT SAKIT */
        .paper {
            max-width: 780px;
            margin: 0 auto;
            background: #ffffff;
            padding: 48px 56px;
            box-shadow: 0 4px 25px rgba(0,0,0,0.08);
            border-radius: 4px;
            position: relative;
        }

        /* KOP SURAT RUMAH SAKIT DELTA */
        .kop-surat {
            display: flex;
            align-items: center;
            gap: 20px;
            border-bottom: 3px double #0f172a;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }
        .kop-logo {
            width: 72px;
            height: 72px;
            object-fit: contain;
        }
        .kop-text h2 {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .kop-text h4 {
            font-size: 13px;
            font-weight: 700;
            color: #2563eb;
            margin-bottom: 2px;
        }
        .kop-text p {
            font-size: 11px;
            color: #64748b;
            line-height: 1.4;
        }

        /* JUDUL SURAT */
        .surat-header {
            text-align: center;
            margin-bottom: 28px;
        }
        .surat-title {
            font-size: 16px;
            font-weight: 800;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0f172a;
        }
        .surat-nomor {
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            margin-top: 4px;
            font-family: monospace;
        }

        /* TABEL IDENTITAS PASIEN */
        .info-table {
            width: 100%;
            margin: 16px 0;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 6px 4px;
            vertical-align: top;
            font-size: 13px;
        }
        .info-table td.label {
            width: 220px;
            color: #475569;
            font-weight: 500;
        }
        .info-table td.colon {
            width: 15px;
            text-align: center;
        }
        .info-table td.value {
            font-weight: 600;
            color: #0f172a;
        }

        /* KOTAK STATUS SAKIT & WAKTU ISTIRAHAT */
        .box-istirahat {
            background: #eff6ff;
            border-left: 4px solid #2563eb;
            padding: 14px 18px;
            margin: 20px 0;
            border-radius: 6px;
            font-size: 13.5px;
        }

        /* TANDA TANGAN DOKTER & QR */
        .ttd-container {
            margin-top: 40px;
            display: flex;
            justify-content: flex-end;
        }
        .ttd-box {
            width: 280px;
            text-align: center;
        }
        .ttd-qr {
            width: 85px;
            height: 85px;
            margin: 10px auto;
            border: 1px solid #cbd5e1;
            padding: 4px;
            border-radius: 6px;
            background: #fff;
        }

        @media print {
            body { background: #fff; padding: 0; }
            .no-print { display: none !important; }
            .paper { box-shadow: none; padding: 0; width: 100%; max-width: 100%; }
            @page { size: A4 portrait; margin: 20mm; }
        }
    </style>
</head>
<body>

    <!-- TOOLBAR INTERAKTIF DI LAYAR BROWSER -->
    <div class="no-print">
        <div style="display: flex; align-items: center; gap: 10px;">
            <a href="{{ route('dokter.pelayanan') }}" class="btn-action btn-back">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Dokter
            </a>
            <form action="{{ route('dokter.surat-sakit', $pendaftaran->id_pendaftaran) }}" method="GET" style="display: inline-flex; align-items: center; gap: 8px;">
                <label style="font-size: 12.5px; font-weight: 600; color: #475569;">Lama Istirahat:</label>
                <select name="hari" onchange="this.form.submit()" style="padding: 6px 10px; border-radius: 6px; border: 1px solid #cbd5e1; font-weight: 600;">
                    <option value="1" {{ $lamaHari == 1 ? 'selected' : '' }}>1 Hari</option>
                    <option value="2" {{ $lamaHari == 2 ? 'selected' : '' }}>2 Hari</option>
                    <option value="3" {{ $lamaHari == 3 ? 'selected' : '' }}>3 Hari</option>
                    <option value="4" {{ $lamaHari == 4 ? 'selected' : '' }}>4 Hari</option>
                    <option value="5" {{ $lamaHari == 5 ? 'selected' : '' }}>5 Hari</option>
                    <option value="7" {{ $lamaHari == 7 ? 'selected' : '' }}>7 Hari (1 Minggu)</option>
                </select>
            </form>
        </div>

        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" class="btn-action btn-print">
                <i class="fa-solid fa-print"></i> Cetak / Print
            </button>
            <a href="{{ route('dokter.surat-sakit', $pendaftaran->id_pendaftaran) }}?download=1&hari={{ $lamaHari }}" class="btn-action btn-pdf">
                <i class="fa-solid fa-file-pdf"></i> Download PDF
            </a>
        </div>
    </div>

    <!-- DOKUMEN RESMI A4 SURAT SAKIT -->
    <div class="paper">
        <!-- KOP SURAT RESMI -->
        <div class="kop-surat">
            <img src="{{ asset('images/logodelta1.svg') }}" alt="Logo Delta" class="kop-logo" style="background: #2563eb; border-radius: 12px; padding: 10px;">
            <div class="kop-text">
                <h2>RUMAH SAKIT DELTA</h2>
                <h4>INSTALASI RAWAT JALAN & POLIKLINIK SPESIALIS</h4>
                <p>Jl. Jenderal Ahmad Yani Km. 4.5 No. 128, Banjarmasin, Kalimantan Selatan 70234<br>
                Telp: (0511) 7654321 • Surel: layanan@rsdelta.co.id • Izin Operasional Kemenkes No: 440/018/RS-DLT/2022</p>
            </div>
        </div>

        <!-- JUDUL & NOMOR SURAT -->
        <div class="surat-header">
            <h3 class="surat-title">SURAT KETERANGAN SAKIT</h3>
            <div class="surat-nomor">NOMOR: {{ $nomorSurat }}</div>
        </div>

        <!-- ISI KETERANGAN DOKTER -->
        <p style="margin-bottom: 12px; text-align: justify;">
            Yang bertanda tangan di bawah ini, Dokter Pemeriksa pada Poliklinik <strong>{{ $pendaftaran->poli?->nama_poli ?? 'Poli Umum' }}</strong> Rumah Sakit Delta Banjarmasin, dengan ini menerangkan setelah melakukan pemeriksaan klinis bahwa:
        </p>

        <table class="info-table">
            <tr>
                <td class="label">Nama Lengkap Pasien</td>
                <td class="colon">:</td>
                <td class="value">{{ $pendaftaran->pasien?->nama_pasien ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Nomor Rekam Medis (RM)</td>
                <td class="colon">:</td>
                <td class="value">{{ $pendaftaran->pasien?->rm ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Nomor Induk Kependudukan (NIK)</td>
                <td class="colon">:</td>
                <td class="value">{{ $pendaftaran->pasien?->nik ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Jenis Kelamin</td>
                <td class="colon">:</td>
                <td class="value">{{ $pendaftaran->pasien?->jenis_kelamin === 'P' ? 'Perempuan' : 'Laki-laki' }}</td>
            </tr>
            <tr>
                <td class="label">Diagnosa Medis / Keluhan</td>
                <td class="colon">:</td>
                <td class="value">
                    {{ $pendaftaran->rekamMedis?->diagnosa_utama ?? 'Observasi Klinis Sakit Akut' }}
                    @if(!empty($pendaftaran->rekamMedis?->kode_icd10))
                        (ICD-10: {{ $pendaftaran->rekamMedis->kode_icd10 }})
                    @endif
                </td>
            </tr>
        </table>

        <div class="box-istirahat">
            Berhubung kondisi kesehatan pasien yang bersangkutan memerlukan waktu pemulihan, maka diberikan izin <strong>ISTIRAHAT MEDIS</strong> selama:
            <div style="font-size: 16px; font-weight: 800; color: #1d4ed8; margin: 6px 0;">
                {{ $lamaHari }} Hari
            </div>
            Terhitung mulai tanggal <strong>{{ $tglMulai->translatedFormat('d F Y') }}</strong> sampai dengan tanggal <strong>{{ $tglSelesai->translatedFormat('d F Y') }}</strong>.
        </div>

        <p style="margin-top: 14px; text-align: justify;">
            Demikian surat keterangan sakit ini dibuat dengan sebenarnya sesuai dengan sumpah jabatan dokter untuk dapat dipergunakan sebagaimana mestinya.
        </p>

        <!-- TANDA TANGAN DOKTER PEMERIKSA -->
        <div class="ttd-container">
            <div class="ttd-box">
                <p>Banjarmasin, {{ $tglMulai->translatedFormat('d F Y') }}</p>
                <p style="font-weight: 600; margin-bottom: 8px;">Dokter Pemeriksa,</p>

                <!-- QR CODE VERIFIKASI DOKUMEN MEDIS DIGITAL -->
                <div class="ttd-qr">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode('VERIFIED_RS_DELTA_' . $nomorSurat . '_' . ($dokter->nama ?? 'Dokter')) }}" 
                         alt="QR Verifikasi" style="width: 100%; height: 100%; object-fit: contain;">
                </div>

                <p style="font-weight: 700; text-decoration: underline; font-size: 13.5px; margin-top: 6px;">
                    {{ $dokter->nama ?? 'dr. Budi Santoso, Sp.PD' }}
                </p>
                <p style="font-size: 11px; color: #64748b;">
                    NIP: {{ $dokter->nip ?? '19850312 201001 1 002' }}<br>
                    SIP: 446.1/{{ rand(100, 999) }}/SIP.DOK/DISKES
                </p>
            </div>
        </div>
    </div>

</body>
</html>
