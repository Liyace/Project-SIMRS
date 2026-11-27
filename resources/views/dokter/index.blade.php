@extends('layouts.app')
@php $halamanAktif = 'dokter_pelayanan'; @endphp
@section('title', 'Pelayanan Poliklinik Dokter - Rumah Sakit Delta')

@push('styles')
<style>
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
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
        font-size: 11.5px;
        text-transform: uppercase;
    }
    .table-custom td {
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .table-custom tr:hover td {
        background: #f8fafc;
    }
    .btn-periksa {
        background: #2563eb;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    .btn-periksa:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);
    }
    /* Dark Mode */
    body.dark-mode .stat-card,
    body.dark-mode .content-box {
        background: #121927;
        border-color: #1e293b;
    }
    body.dark-mode .stat-info .num,
    body.dark-mode h3 {
        color: #f1f5f9;
    }
    body.dark-mode .table-custom th {
        background: #0b0f19;
        color: #94a3b8;
        border-color: #1e293b;
    }
    body.dark-mode .table-custom td {
        border-color: #1e293b;
        color: #cbd5e1;
    }
    body.dark-mode .table-custom tr:hover td {
        background: #0b0f19;
    }
</style>
@endpush

@section('content')
<div class="page-header-text">
    <h2>Pelayanan Poliklinik Dokter</h2>
    <p>Pemeriksaan rekam medis, penegakan diagnosa ICD-10, tindakan klinis, dan penerbitan e-resep obat pasien.</p>
</div>

@if (session('status_pemeriksaan'))
    <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 14px 18px; border-radius: 14px; margin-bottom: 20px; font-size: 13px; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-circle-check" style="font-size: 18px; color: #10b981;"></i>
        <span>{{ session('status_pemeriksaan') }}</span>
    </div>
@endif

{{-- KARTU DOKTER & FILTER POLIKLINIK --}}
<div class="content-box" style="padding: 14px 20px; margin-bottom: 20px; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 14px;">
    <div style="display: flex; align-items: center; gap: 12px;">
        <div style="width: 42px; height: 42px; border-radius: 12px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 18px;">
            <i class="fa-solid fa-user-doctor"></i>
        </div>
        <div>
            <div style="font-size: 13.5px; font-weight: 700; color: #1e293b;">
                {{ auth()->user()->nama ?? 'Dokter Pemeriksa' }}
            </div>
            <div style="font-size: 12px; color: #64748b;">
                @if ($dokterPoliAktif)
                    <span style="color: #2563eb; font-weight: 600;">{{ $dokterPoliAktif->nama_poli }}</span>
                    @if (!empty(auth()->user()->spesialisasi))
                        • {{ auth()->user()->spesialisasi }}
                    @endif
                @else
                    <span style="color: #64748b;">Rotasi Antar-Poliklinik</span>
                @endif
            </div>
        </div>
    </div>

    <form method="GET" action="{{ route('dokter.pelayanan') }}" style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
        <input type="hidden" name="tanggal" value="{{ $tanggal }}">
        <label for="filter-poli" style="font-size: 12px; font-weight: 600; color: #475569;">Filter Poliklinik:</label>
        <select name="poli" id="filter-poli" onchange="this.form.submit()" style="padding: 7px 12px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 12.5px; background: #fff; min-width: 180px; font-weight: 500;">
            <option value="semua" {{ $filterPoli === 'semua' ? 'selected' : '' }}>Semua Poliklinik</option>
            @foreach ($polis as $poli)
                <option value="{{ $poli->id_poli }}" {{ (string)$filterPoli === (string)$poli->id_poli ? 'selected' : '' }}>
                    {{ $poli->nama_poli }} ({{ $poli->kode_poli }})
                </option>
            @endforeach
        </select>
        @if ($filterPoli && $filterPoli !== 'semua')
            <a href="{{ route('dokter.pelayanan', ['tanggal' => $tanggal, 'poli' => 'semua']) }}" style="font-size: 11.5px; color: #64748b; text-decoration: underline;">
                Tampilkan Semua
            </a>
        @endif
    </form>
</div>

{{-- KARTU STATISTIK DOKTER --}}
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(37, 99, 235, 0.15); color: #2563eb;">
            <i class="fa-solid fa-user-injured"></i>
        </div>
        <div class="stat-info">
            <div class="num">{{ $stats['menunggu'] }}</div>
            <div class="label">Pasien Siap Diperiksa</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(16, 185, 129, 0.15); color: #059669;">
            <i class="fa-solid fa-notes-medical"></i>
        </div>
        <div class="stat-info">
            <div class="num">{{ $stats['selesai'] }}</div>
            <div class="label">Selesai Diperiksa Hari Ini</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(99, 102, 241, 0.15); color: #4f46e5;">
            <i class="fa-solid fa-hospital-user"></i>
        </div>
        <div class="stat-info">
            <div class="num">{{ $stats['total'] }}</div>
            <div class="label">Total Kunjungan Pasien</div>
        </div>
    </div>
</div>

{{-- TABEL ANTREAN PASIEN SIAP DIPERIKSA DOKTER --}}
<div class="content-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <div>
            <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 0;">Antrean Pasien Menunggu Pemeriksaan</h3>
            <p style="font-size: 12px; color: #64748b; margin: 2px 0 0;">Pasien yang telah melalui skrining TTV oleh perawat dan siap diperiksa di ruang dokter.</p>
        </div>
        <span class="badge" style="background:#eff6ff; color:#2563eb; font-weight:700; padding:4px 12px; font-size:11.5px; border-radius:12px; border:1px solid #bfdbfe;">
            {{ $antreanMenunggu->total() }} Pasien Antre
        </span>
    </div>

    <div style="overflow-x: auto;">
        <table class="table-custom">
            <thead>
                <tr>
                    <th style="width: 100px;">No. Antrian</th>
                    <th>Nama Pasien</th>
                    <th>Poliklinik</th>
                    <th>Hasil TTV (Perawat)</th>
                    <th>Keluhan Awal</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($antreanMenunggu as $p)
                    @php
                        $ttv = $p->screeningAwal;
                    @endphp
                    <tr>
                        <td style="font-weight: 700; color: #2563eb; font-size: 14px;">
                            {{ $p->no_antrian }}
                        </td>
                        <td>
                            <strong>{{ $p->pasien?->nama_pasien ?? 'Pasien' }}</strong>
                            <div style="font-size: 11px; color: #64748b;">
                                {{ $p->pasien?->rm ?? '-' }} • {{ $p->pasien?->jenis_kelamin === 'P' ? 'Perempuan' : 'Laki-laki' }}
                            </div>
                        </td>
                        <td style="color: #475569; font-size: 12.5px;">
                            {{ $p->poli?->nama_poli ?? 'Poli Umum' }}
                        </td>
                        <td style="font-size: 12px;">
                            @if ($ttv)
                                <div><strong style="color: #0f172a;">TD:</strong> {{ $ttv->sistol }}/{{ $ttv->diastol }} mmHg • <strong style="color: #0f172a;">S:</strong> {{ $ttv->suhu_tubuh }}°C</div>
                                <div style="font-size: 11px; color: #64748b;">Nadi: {{ $ttv->denyut_nadi }} bpm • BB/TB: {{ $ttv->berat_badan }}kg / {{ $ttv->tinggi_badan }}cm</div>
                            @else
                                <span style="color: #94a3b8; font-style: italic;">Belum diskrining</span>
                            @endif
                        </td>
                        <td style="font-size: 12px; color: #475569; max-width: 220px;">
                            {{ $p->keluhan_awal ?? '-' }}
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('dokter.periksa', $p->id_pendaftaran) }}" class="btn-periksa" title="Buka form rekam medis dan periksa pasien ini">
                                <i class="fa-solid fa-stethoscope"></i> Periksa Pasien
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 36px 14px; color: #94a3b8;">
                            <i class="fa-solid fa-user-check" style="font-size: 32px; color: #10b981; margin-bottom: 8px; display: block;"></i>
                            Tidak ada pasien yang menunggu pemeriksaan dokter saat ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Navigasi Paginasi Antrean Dokter --}}
    {{ $antreanMenunggu->links('partials.pagination') }}
</div>

{{-- TABEL RIWAYAT PASIEN SELESAI DIPERIKSA DOKTER HARI INI --}}
<div class="content-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <div>
            <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 0;">Riwayat Pasien Selesai Hari Ini</h3>
            <p style="font-size: 12px; color: #64748b; margin: 2px 0 0;">Pasien yang telah selesai diperiksa dan tagihan/resepnya diteruskan ke kasir/apotek.</p>
        </div>
        <span class="badge" style="background:#ecfdf5; color:#059669; font-weight:700; padding:4px 12px; font-size:11.5px; border-radius:12px; border:1px solid #a7f3d0;">
            {{ $riwayatSelesai->total() }} Pasien Selesai
        </span>
    </div>

    <div style="overflow-x: auto;">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>No. Antrian</th>
                    <th>Nama Pasien</th>
                    <th>Poliklinik</th>
                    <th>Diagnosa Utama</th>
                    <th>Tindakan Medis</th>
                    <th>Resep Obat</th>
                    <th>Status Pasien</th>
                    <th style="text-align: center;">Dokumen Medis</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($riwayatSelesai as $r)
                    @php
                        $rm = $r->rekamMedis;
                        $tindakanNames = $rm?->tindakanMedis?->map(fn($t) => $t->layanan?->nama_layanan)->filter()->implode(', ') ?: '-';
                        $resepObatNames = $r->resep?->details?->map(fn($d) => ($d->obat?->nama_obat ?? 'Obat') . ' (' . $d->jumlah . ')')->implode(', ') ?: 'Tanpa Resep';
                    @endphp
                    <tr>
                        <td style="font-weight: 700; color: #2563eb;">{{ $r->no_antrian }}</td>
                        <td>
                            <strong>{{ $r->pasien?->nama_pasien ?? 'Pasien' }}</strong>
                            <div style="font-size: 11px; color: #64748b;">{{ $r->pasien?->rm ?? '-' }}</div>
                        </td>
                        <td style="color: #475569; font-size: 12px;">{{ $r->poli?->nama_poli ?? 'Poli Umum' }}</td>
                        <td>
                            <strong style="color: #0f172a;">{{ $rm?->diagnosa_utama ?? '-' }}</strong>
                            @if (!empty($rm?->kode_icd10))
                                <span style="font-size: 10px; background: #eff6ff; color: #2563eb; padding: 2px 6px; border-radius: 4px; border: 1px solid #bfdbfe;">{{ $rm->kode_icd10 }}</span>
                            @endif
                        </td>
                        <td style="font-size: 12px; color: #475569;">{{ Str::limit($tindakanNames, 35) }}</td>
                        <td style="font-size: 12px; color: #059669;">
                            <i class="fa-solid fa-prescription" style="margin-right: 4px;"></i> {{ Str::limit($resepObatNames, 35) }}
                        </td>
                        <td>
                            @php
                                $badgeColor = match($r->status_antrian) {
                                    'menunggu_kasir' => ['bg' => '#fef3c7', 'text' => '#b45309', 'label' => 'Menunggu Kasir'],
                                    'menunggu_obat'  => ['bg' => '#dbeafe', 'text' => '#1e40af', 'label' => 'Menunggu Obat'],
                                    'selesai'        => ['bg' => '#dcfce7', 'text' => '#15803d', 'label' => 'Selesai'],
                                    default          => ['bg' => '#f1f5f9', 'text' => '#475569', 'label' => $r->status_antrian],
                                    };
                            @endphp
                            <span style="background: {{ $badgeColor['bg'] }}; color: {{ $badgeColor['text'] }}; font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 6px;">
                                {{ $badgeColor['label'] }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <div style="display: inline-flex; gap: 4px;">
                                <a href="{{ route('dokter.surat-sakit', $r->id_pendaftaran) }}" target="_blank" title="Cetak Surat Izin Sakit Pasien" style="background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fa-solid fa-file-medical"></i> Sakit
                                </a>
                                <a href="{{ route('dokter.resume-medis', $r->id_pendaftaran) }}" target="_blank" title="Cetak Resume Medis Pasien" style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fa-solid fa-notes-medical"></i> Resume
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 24px; color: #94a3b8;">
                            Belum ada riwayat pasien yang selesai diperiksa dokter hari ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Navigasi Paginasi Riwayat Selesai Dokter --}}
    {{ $riwayatSelesai->links('partials.pagination') }}
</div>
@endsection
