<table>
    <thead>
        <tr>
            <th colspan="9" style="font-size: 16px; font-weight: bold; text-align: center; height: 30px;">
                LAPORAN INVENTORI DAN STOK OPNAME FARMASI
            </th>
        </tr>
        <tr>
            <th colspan="9" style="font-size: 13px; font-weight: bold; text-align: center; color: #2563eb;">
                RUMAH SAKIT DELTA BANJARMASIN
            </th>
        </tr>
        <tr>
            <th colspan="9" style="font-size: 11px; text-align: center; color: #64748b; height: 20px;">
                Waktu Unduh: {{ $tanggalCetak }} WITA - Filter: {{ ucfirst($filter) }}
            </th>
        </tr>
        <tr><th colspan="9"></th></tr>
        <tr style="background-color: #1e3a8a; color: #ffffff; font-weight: bold; text-align: center;">
            <th style="background-color: #1e40af; color: #ffffff; border: 1px solid #000000; text-align: center; width: 6px;">NO</th>
            <th style="background-color: #1e40af; color: #ffffff; border: 1px solid #000000; text-align: center; width: 15px;">KODE OBAT</th>
            <th style="background-color: #1e40af; color: #ffffff; border: 1px solid #000000; width: 32px;">NAMA OBAT DAN SEDIAAN</th>
            <th style="background-color: #1e40af; color: #ffffff; border: 1px solid #000000; text-align: center; width: 14px;">SATUAN</th>
            <th style="background-color: #1e40af; color: #ffffff; border: 1px solid #000000; text-align: right; width: 18px;">HARGA SATUAN (RP)</th>
            <th style="background-color: #1e40af; color: #ffffff; border: 1px solid #000000; text-align: center; width: 14px;">STOK MIN</th>
            <th style="background-color: #1e40af; color: #ffffff; border: 1px solid #000000; text-align: center; width: 14px;">STOK FISIK</th>
            <th style="background-color: #1e40af; color: #ffffff; border: 1px solid #000000; text-align: center; width: 16px;">STATUS STOK</th>
            <th style="background-color: #1e40af; color: #ffffff; border: 1px solid #000000; text-align: right; width: 22px;">VALUASI ASET (RP)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($obatList as $idx => $obat)
            @php
                $subtotalAset = (float) ($obat->stok * $obat->harga_obat);
                $statusText = 'Aman';
                $statusColor = '#15803d';
                if ($obat->stok <= 0) {
                    $statusText = 'Habis';
                    $statusColor = '#b91c1c';
                } elseif ($obat->stok <= $obat->stok_minimum) {
                    $statusText = 'Menipis';
                    $statusColor = '#b45309';
                }
            @endphp
            <tr>
                <td style="text-align: center; border: 1px solid #cbd5e1;">{{ $idx + 1 }}</td>
                <td style="text-align: center; border: 1px solid #cbd5e1; font-family: monospace;">{{ $obat->kode_obat ?? '-' }}</td>
                <td style="border: 1px solid #cbd5e1; font-weight: bold;">{{ $obat->nama_obat }}</td>
                <td style="text-align: center; border: 1px solid #cbd5e1;">{{ $obat->satuan ?? 'pcs' }}</td>
                <td style="text-align: right; border: 1px solid #cbd5e1;">{{ number_format($obat->harga_obat, 0, ',', '.') }}</td>
                <td style="text-align: center; border: 1px solid #cbd5e1;">{{ $obat->stok_minimum ?? 10 }}</td>
                <td style="text-align: center; border: 1px solid #cbd5e1; font-weight: bold;">{{ $obat->stok }}</td>
                <td style="text-align: center; border: 1px solid #cbd5e1; color: {{ $statusColor }}; font-weight: bold;">{{ $statusText }}</td>
                <td style="text-align: right; border: 1px solid #cbd5e1; font-weight: bold;">{{ number_format($subtotalAset, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        <tr style="background-color: #f1f5f9; font-weight: bold;">
            <td colspan="6" style="text-align: right; border: 2px solid #000000; font-weight: bold;">TOTAL KESELURUHAN:</td>
            <td style="text-align: center; border: 2px solid #000000; font-weight: bold;">{{ number_format($totalFisik, 0, ',', '.') }}</td>
            <td style="text-align: center; border: 2px solid #000000;">{{ $totalItem }} Item</td>
            <td style="text-align: right; border: 2px solid #000000; font-weight: bold; color: #1e40af;">Rp {{ number_format($totalValuasi, 0, ',', '.') }}</td>
        </tr>
    </tbody>
</table>
