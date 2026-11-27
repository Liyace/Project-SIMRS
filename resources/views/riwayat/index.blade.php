@extends('layouts.app')
@php $halamanAktif = 'riwayat'; @endphp
@section('title', 'Riwayat Transaksi - Rumah Sakit Delta')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/riwayat.css') }}">
@endpush

@section('content')
<div class="page-header-text">
    <h2>Riwayat Transaksi</h2>
    <p>Daftar semua transaksi yang telah dilakukan.</p>
</div>

{{-- Baris Filter: Pilih Tanggal, Cari Pasien, Tombol Reset & Cari --}}
<form method="GET" action="{{ route('riwayat') }}" class="filter-container">
    <div class="form-group" style="flex: 1; min-width: 200px; max-width: 280px;">
        <label for="filter-tanggal">Pilih Tanggal</label>
        <div class="input-with-icon">
            <input type="date" id="filter-tanggal" name="tanggal" value="{{ $tanggal }}" class="form-control">
        </div>
    </div>

    <div class="form-group" style="flex: 2; min-width: 250px;">
        <label for="filter-cari">Cari Pasien</label>
        <div class="input-with-icon">
            <input type="text" id="filter-cari" name="q" value="{{ $q }}" placeholder="Masukkan nama pasien..." class="form-control">
            <i class="fa-solid fa-magnifying-glass"></i>
        </div>
    </div>

    <div class="filter-buttons">
        <a href="{{ route('riwayat') }}" class="btn-filter-reset">Reset</a>
        <button type="submit" class="btn-filter-submit">
            <i class="fa-solid fa-magnifying-glass"></i> Cari
        </button>
    </div>
</form>

{{-- Kartu Tabel Daftar Transaksi --}}
<div class="section-container" style="padding: 20px 25px 25px 25px;">
    <div style="overflow-x: auto;">
        <table class="kasir-table">
            <thead>
                <tr>
                    <th class="text-left" style="width: 170px;">NO. TRANSAKSI</th>
                    <th class="text-left" style="width: 130px;">TANGGAL</th>
                    <th class="text-left" style="min-width: 160px;">PASIEN</th>
                    <th class="text-left" style="min-width: 180px;">LAYANAN</th>
                    <th class="text-right" style="width: 150px;">TOTAL (RP)</th>
                    <th class="text-center" style="width: 120px;">METODE</th>
                    <th class="text-center" style="width: 120px;">STATUS</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($riwayat as $r)
                    @php
                        $noTrx = $r->no_invoice ?? ('TRX-' . ($r->created_at ? $r->created_at->format('Ymd') : date('Ymd')) . '-' . str_pad($r->id_transaksi, 4, '0', STR_PAD_LEFT));
                        $tgl = $r->waktu_pembayaran ? $r->waktu_pembayaran->format('d/m/Y') : ($r->created_at ? $r->created_at->format('d/m/Y') : '-');
                        $pasienNama = $r->pendaftaran->pasien->nama_pasien ?? ($r->pendaftaran->nama_pasien ?? 'Pasien Umum');
                        $layananNama = $r->layanan->nama_layanan ?? ($r->pendaftaran->layanan->nama_layanan ?? 'Pemeriksaan Dokter & Resep');
                        $totalBayar = number_format($r->total_bayar ?? 0, 0, ',', '.');
                        $metode = strtoupper($r->metode_bayar ?? 'TUNAI');
                    @endphp
                    <tr>
                        <td class="text-left font-bold" style="color: #2563eb;">{{ $noTrx }}</td>
                        <td class="text-left text-muted">{{ $tgl }}</td>
                        <td class="text-left font-bold cell-pasien-nama">{{ $pasienNama }}</td>
                        <td class="text-left">{{ $layananNama }}</td>
                        <td class="text-right font-bold" style="color: #059669;">Rp {{ $totalBayar }}</td>
                        <td class="text-center">
                            <span class="badge badge-metode" style="background: #f1f5f9; color: #475569;">
                                {{ $metode }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge" style="background: #dcfce7; color: #15803d;">
                                Lunas
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted" style="padding: 40px 0;">
                            <i class="fa-solid fa-receipt" style="font-size: 36px; margin-bottom: 12px; color: #cbd5e1; display: block;"></i>
                            Tidak ada data transaksi yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($riwayat->hasPages())
        <div style="margin-top: 25px; display: flex; justify-content: center;">
            {{ $riwayat->links() }}
        </div>
    @endif
</div>
@endsection

