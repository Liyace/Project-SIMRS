<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Kasir RS Delta - {{ $labelPeriode }}</title>
    <style>
        .kop-title {
            font-size: 16pt;
            font-weight: bold;
            color: #1e3a8a;
            text-align: center;
        }
        .kop-sub {
            font-size: 11pt;
            color: #059669;
            font-weight: bold;
            text-align: center;
        }
        .kop-meta {
            font-size: 9pt;
            color: #475569;
            text-align: center;
        }
        .table-header {
            background-color: #2563eb;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
            border: 0.5pt solid #1e3a8a;
            height: 30px;
        }
        .data-cell {
            border: 0.5pt solid #cbd5e1;
            padding: 5px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .number-fmt { mso-number-format: "\#\,\#\#0"; }
        .date-fmt { mso-number-format: "dd\/mm\/yyyy\ hh\:mm"; }
        .text-fmt { mso-number-format: "\@"; }
        .total-cell {
            background-color: #f1f5f9;
            font-weight: bold;
            border: 1pt solid #94a3b8;
        }
        .summary-header {
            background-color: #ecfdf5;
            font-weight: bold;
            color: #065f46;
        }
    </style>
</head>
<body>
    <table>
        <!-- KOP LAPORAN -->
        <tr>
            <td colspan="11" class="kop-title">RUMAH SAKIT DELTA BANJARMASIN</td>
        </tr>
        <tr>
            <td colspan="11" class="kop-sub">LAPORAN REKAPITULASI PENERIMAAN KASIR</td>
        </tr>
        <tr>
            <td colspan="11" class="kop-meta">Periode: {{ $subTitlePeriode }} | Dicetak: {{ date('d/m/Y H:i') }} WITA | Oleh: {{ $kasirLogin->nama ?? 'Kasir Admin' }}</td>
        </tr>
        <tr><td></td></tr>

        <!-- RINGKASAN EKSEKUTIF -->
        <tr>
            <td colspan="3" class="summary-header data-cell">Total Transaksi (Kuitansi)</td>
            <td class="data-cell text-right number-fmt font-bold">{{ $totalTransaksi }}</td>
        </tr>
        <tr>
            <td colspan="3" class="summary-header data-cell">Penerimaan Tunai (Rp)</td>
            <td class="data-cell text-right number-fmt font-bold">{{ $totalTunai }}</td>
        </tr>
        <tr>
            <td colspan="3" class="summary-header data-cell">Penerimaan Non-Tunai / QRIS (Rp)</td>
            <td class="data-cell text-right number-fmt font-bold">{{ $totalNonTunai }}</td>
        </tr>
        <tr>
            <td colspan="3" class="summary-header data-cell" style="background-color:#dcfce7; color:#15803d;">TOTAL PENERIMAAN KASIR (Rp)</td>
            <td class="data-cell text-right number-fmt font-bold" style="background-color:#dcfce7; color:#15803d; font-size:12pt;">{{ $totalPendapatan }}</td>
        </tr>
        <tr><td></td></tr>

        <!-- TABEL RINCIAN -->
        <thead>
            <tr>
                <th class="table-header" style="width:40px;">No</th>
                <th class="table-header" style="width:160px;">No. Invoice</th>
                <th class="table-header" style="width:140px;">Waktu Bayar</th>
                <th class="table-header" style="width:180px;">Nama Pasien</th>
                <th class="table-header" style="width:100px;">No. RM</th>
                <th class="table-header" style="width:130px;">Poliklinik</th>
                <th class="table-header" style="width:130px;">Kasir</th>
                <th class="table-header" style="width:100px;">Metode</th>
                <th class="table-header" style="width:120px;">Resep Obat</th>
                <th class="table-header" style="width:130px;">Tindakan & Jasa</th>
                <th class="table-header" style="width:140px;">Total Bayar</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $index => $d)
                @php
                    $pasienNama = $d->pendaftaran?->pasien?->nama_pasien ?? 'Pasien Umum';
                    $pasienRm = $d->pendaftaran?->pasien?->rm ?? '-';
                    $poliNama = $d->pendaftaran?->poli?->nama_poli ?? 'Poli Umum';
                    $kasirNama = $d->kasir?->nama ?? 'Kasir';
                    $waktuTampil = $d->waktu_pembayaran 
                        ? $d->waktu_pembayaran->format('d/m/Y H:i') 
                        : ($d->created_at ? $d->created_at->format('d/m/Y H:i') : '-');
                    $tindakanDanJasa = (float)($d->total_layanan ?? 0) + (float)($d->jasa_perawat ?? 0);
                @endphp
                <tr>
                    <td class="data-cell text-center">{{ $index + 1 }}</td>
                    <td class="data-cell text-left text-fmt font-bold">{{ $d->no_invoice ?? ('TRX-' . ($d->created_at ? $d->created_at->format('Ymd') : date('Ymd')) . '-' . str_pad($d->id_transaksi, 4, '0', STR_PAD_LEFT)) }}</td>
                    <td class="data-cell text-center date-fmt">{{ $waktuTampil }}</td>
                    <td class="data-cell text-left text-fmt">{{ $pasienNama }}</td>
                    <td class="data-cell text-center text-fmt">{{ $pasienRm }}</td>
                    <td class="data-cell text-left text-fmt">{{ $poliNama }}</td>
                    <td class="data-cell text-left text-fmt">{{ $kasirNama }}</td>
                    <td class="data-cell text-center text-fmt">{{ strtoupper($d->metode_bayar ?? 'TUNAI') }}</td>
                    <td class="data-cell text-right number-fmt">{{ $d->total_resep ?? 0 }}</td>
                    <td class="data-cell text-right number-fmt">{{ $tindakanDanJasa }}</td>
                    <td class="data-cell text-right number-fmt font-bold">{{ $d->total_bayar }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="data-cell text-center" style="color:#94a3b8; height:40px;">Tidak ada data transaksi pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        @if ($data->isNotEmpty())
            <tfoot>
                <tr>
                    <td colspan="8" class="total-cell text-right">TOTAL KESELURUHAN (Rp):</td>
                    <td class="total-cell text-right number-fmt">{{ $totalResep }}</td>
                    <td class="total-cell text-right number-fmt">{{ $totalLayanan + $totalJasaPerawat }}</td>
                    <td class="total-cell text-right number-fmt" style="color:#059669;">{{ $totalPendapatan }}</td>
                </tr>
            </tfoot>
        @endif
        <tr><td></td></tr>

        <!-- PENGESAHAN TANDA TANGAN -->
        <tr>
            <td colspan="3" class="text-center font-bold">Mengetahui,</td>
            <td colspan="5"></td>
            <td colspan="3" class="text-center font-bold">Banjarmasin, {{ date('d F Y') }}</td>
        </tr>
        <tr>
            <td colspan="3" class="text-center">Kepala Bagian Keuangan RS Delta</td>
            <td colspan="5"></td>
            <td colspan="3" class="text-center">Kasir Admin / Supervisor Loket</td>
        </tr>
        <tr><td style="height:40px;"></td></tr>
        <tr>
            <td colspan="3" class="text-center font-bold" style="text-decoration:underline;">Drs. H. Hendra Wijaya, S.E., M.M.</td>
            <td colspan="5"></td>
            <td colspan="3" class="text-center font-bold" style="text-decoration:underline;">{{ $kasirLogin->nama ?? 'Rahmat Kasir Utama' }}</td>
        </tr>
        <tr>
            <td colspan="3" class="text-center">NIP. 19780512 200312 1 002</td>
            <td colspan="5"></td>
            <td colspan="3" class="text-center">
                @if (!empty($kasirLogin->nip))
                    NIP. {{ $kasirLogin->nip }}
                @else
                    NIP. -
                @endif
            </td>
        </tr>
    </table>
</body>
</html>
