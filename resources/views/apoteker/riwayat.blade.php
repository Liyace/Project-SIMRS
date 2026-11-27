@extends('layouts.app')
@php $halamanAktif = 'apoteker_riwayat'; @endphp
@section('title', 'Riwayat Penyerahan Resep Obat - Rumah Sakit Delta')

@push('styles')
<style>
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .stat-info .num {
        font-size: 22px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.2;
    }
    .stat-info .label {
        font-size: 12px;
        color: #64748b;
        margin-top: 2px;
    }
    .content-box {
        background: #ffffff;
        border-radius: 20px;
        padding: 24px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        margin-bottom: 24px;
    }
    .table-custom {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .table-custom th {
        background: #f8fafc;
        color: #475569;
        font-weight: 600;
        padding: 12px 14px;
        text-align: left;
        border-bottom: 1px solid #e2e8f0;
        font-size: 12px;
    }
    .table-custom td {
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9;
        color: #1e293b;
    }
    .table-custom tr:hover td {
        background: #f8fafc;
    }

    /* Dark Mode Overrides */
    body.dark-mode .input-date-wrapper {
        background-color: #0b0f19 !important;
        border-color: #1e293b !important;
    }
    body.dark-mode .input-date-wrapper span {
        color: #94a3b8 !important;
    }
    body.dark-mode .input-date-wrapper input {
        color: #f8fafc !important;
        background-color: transparent !important;
    }
    body.dark-mode .table-custom th {
        background: #0b0f19 !important;
        color: #94a3b8 !important;
        border-bottom-color: #1e293b !important;
    }
    body.dark-mode .table-custom td {
        border-bottom-color: #1e293b !important;
        color: #f1f5f9 !important;
    }
    body.dark-mode .table-custom tr:hover td {
        background: #0b0f19 !important;
    }
</style>
@endpush

@section('content')
<div class="content-body" style="padding-bottom: 40px;">

    <!-- HEADER TITLE & DATE RANGE FILTER -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 14px;">
        <div>
            <h1 style="font-size: 22px; font-weight: 700; color: #1e293b; margin: 0;">Riwayat Penyerahan Resep Obat</h1>
            <p style="font-size: 13px; color: #64748b; margin: 4px 0 0;">
                Arsip resep dokter yang telah diserahkan kepada pasien beserta rincian petugas apoteker yang melayani.
            </p>
        </div>

        <form action="{{ route('apoteker.riwayat') }}" method="GET" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <div class="input-date-wrapper" style="position: relative; display: flex; align-items: center; background: #fff; border: 1px solid #cbd5e1; border-radius: 10px; padding: 6px 12px;">
                    <span style="font-size: 11.5px; font-weight: 600; color: #64748b; margin-right: 6px;">Dari:</span>
                    <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai }}"
                           style="border: none; outline: none; font-size: 12.5px; color: #1e293b; background: transparent; cursor: pointer;">
                </div>

                <span style="font-size: 11px; font-weight: 600; color: #64748b; display: inline-flex; align-items: center; justify-content: center; padding: 4px 8px; border-radius: 8px; background: #f1f5f9; border: 1px solid #e2e8f0;">
                    s/d
                </span>

                <div class="input-date-wrapper" style="position: relative; display: flex; align-items: center; background: #fff; border: 1px solid #cbd5e1; border-radius: 10px; padding: 6px 12px;">
                    <span style="font-size: 11.5px; font-weight: 600; color: #64748b; margin-right: 6px;">Sampai:</span>
                    <input type="date" name="tanggal_selesai" value="{{ $tanggalSelesai }}"
                           style="border: none; outline: none; font-size: 12.5px; color: #1e293b; background: transparent; cursor: pointer;">
                </div>
            </div>

            <div style="position: relative;">
                <input type="text" name="q" value="{{ $q }}" placeholder="Cari Resep / Pasien..."
                       style="padding: 8px 12px 8px 32px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 12.5px; width: 190px;">
                <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 11px; top: 11px; color: #94a3b8; font-size: 12px;"></i>
            </div>

            <button type="submit" style="background: #2563eb; color: white; border: none; padding: 8px 14px; border-radius: 8px; font-weight: 600; font-size: 12px; cursor: pointer;">
                Filter
            </button>

            @if(!empty($q))
                <a href="{{ route('apoteker.riwayat', ['tanggal_mulai' => $tanggalMulai, 'tanggal_selesai' => $tanggalSelesai]) }}" class="simrs-btn-white" style="padding: 8px 12px; font-size: 12px;" title="Reset">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            @endif
        </form>
    </div>

    <!-- 2 STAT CARDS SUMMARY -->
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: #ecfdf5; color: #10b981;">
                <i class="fa-solid fa-clipboard-check"></i>
            </div>
            <div class="stat-info">
                <div class="num">{{ $totalDiserahkanPeriode }} Resep</div>
                <div class="label">Total Resep Telah Diserahkan</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #eff6ff; color: #2563eb;">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
            <div class="stat-info">
                <div class="num">Rp {{ number_format($totalNominalObat, 0, ',', '.') }}</div>
                <div class="label">Akumulasi Nilai Obat Diserahkan</div>
            </div>
        </div>
    </div>

    <!-- TABEL RIWAYAT PENYERAHAN -->
    <div class="content-box">
        <div style="overflow-x: auto;">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th style="width: 15%;">NO. RESEP</th>
                        <th style="width: 14%;">WAKTU SERAH</th>
                        <th style="width: 22%;">PASIEN & REKAM MEDIS</th>
                        <th style="width: 24%;">RINCIAN OBAT</th>
                        <th style="width: 13%;">APOTEKER</th>
                        <th style="width: 12%; text-align: right;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayatList as $r)
                        @php
                            $pasien = $r->pendaftaran?->pasien;
                        @endphp
                        <tr>
                            <td>
                                <span style="font-family: monospace; font-weight: 700; color: #2563eb;">
                                    {{ $r->no_resep }}
                                </span>
                                <div style="font-size: 11px; color: #64748b;">
                                    Total: Rp {{ number_format($r->total_harga_obat, 0, ',', '.') }}
                                </div>
                            </td>
                            <td>
                                <div>{{ $r->updated_at->format('d/m/Y') }}</div>
                                <div style="font-size: 11px; color: #64748b;">{{ $r->updated_at->format('H:i') }} WITA</div>
                            </td>
                            <td>
                                <strong style="font-size: 13.5px;">{{ $pasien->nama_pasien ?? 'Pasien Umum' }}</strong>
                                <div style="font-size: 11px; color: #64748b;">
                                    {{ $pasien->rm ?? '-' }} • {{ $r->pendaftaran?->poli?->nama_poli ?? 'Poli' }}
                                </div>
                            </td>
                            <td>
                                <ul style="margin: 0; padding-left: 16px; font-size: 12px; color: #334155;">
                                    @foreach($r->details as $d)
                                        <li>
                                            <strong>{{ $d->obat->nama_obat ?? 'Obat' }}</strong> ({{ $d->jumlah }} {{ $d->obat->satuan ?? '' }})
                                            <span style="font-size: 11px; color: #64748b;">- {{ $d->aturan_pakai }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                <div style="font-weight: 600; font-size: 12.5px;">{{ $r->apoteker->nama ?? 'Apoteker RS' }}</div>
                                <div style="font-size: 11px; color: #64748b;">Farmasi RS Delta</div>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="{{ route('apoteker.copy-resep', $r->id_resep) }}" target="_blank"
                                   style="background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; padding: 6px 12px; border-radius: 8px; font-size: 11.5px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; margin-right: 4px;"
                                   title="Salinan Resep Dokter (Copy Resep / Apograph)">
                                    <i class="fa-solid fa-file-lines"></i> Copy Resep
                                </a>
                                <a href="{{ route('apoteker.etiket', $r->id_resep) }}" target="_blank"
                                   style="background: #f1f5f9; color: #1e293b; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 8px; font-size: 11.5px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;"
                                   title="Cetak Ulang Etiket">
                                    <i class="fa-solid fa-print"></i> Etiket
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px 14px; color: #64748b;">
                                Belum ada riwayat penyerahan obat pada rentang tanggal yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($riwayatList->hasPages())
            <div style="margin-top: 20px;">
                {{ $riwayatList->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
