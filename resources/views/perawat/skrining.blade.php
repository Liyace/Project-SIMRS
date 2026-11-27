@extends('layouts.app')
@php $halamanAktif = 'perawat_skrining'; @endphp
@section('title', 'Skrining TTV Poliklinik - Rumah Sakit Delta')

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
    .grid-layout {
        display: grid;
        grid-template-columns: 1.1fr 1fr;
        gap: 24px;
        align-items: start;
    }
    @media (max-width: 1024px) {
        .grid-layout {
            grid-template-columns: 1fr;
        }
    }
    .content-box {
        background: #ffffff;
        border-radius: 20px;
        padding: 24px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
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
    .btn-action-pilih {
        background: #2563eb;
        color: white;
        border: none;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    .btn-action-pilih:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }
    .form-group {
        margin-bottom: 16px;
    }
    .form-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 6px;
    }
    .form-control {
        width: 100%;
        padding: 10px 14px;
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        font-size: 13px;
        font-family: inherit;
        outline: none;
        transition: border-color 0.2s;
    }
    .form-control:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }
    .bmi-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        margin-top: 6px;
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
    body.dark-mode .form-control {
        background: #0b0f19;
        border-color: #334155;
        color: #f1f5f9;
    }
    body.dark-mode .form-label {
        color: #94a3b8;
    }
</style>
@endpush

@section('content')
<div class="page-header-text">
    <h2>Skrining TTV Poliklinik</h2>
    <p>Pemeriksaan Tanda-Tanda Vital (TTV) dan anamnesis awal oleh Perawat sebelum pasien diperiksa Dokter.</p>
</div>

{{-- NOTIFIKASI SUKSES / ERROR --}}
@if (session('status_skrining'))
    <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 14px 18px; border-radius: 14px; margin-bottom: 20px; font-size: 13px; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-circle-check" style="font-size: 18px; color: #10b981;"></i>
        <span>{{ session('status_skrining') }}</span>
    </div>
@endif

@if ($errors->any())
    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 14px 18px; border-radius: 14px; margin-bottom: 20px; font-size: 13px;">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach ($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- KARTU STATISTIK PERAWAT --}}
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(245, 158, 11, 0.15); color: #d97706;">
            <i class="fa-solid fa-user-clock"></i>
        </div>
        <div class="stat-info">
            <div class="num">{{ $stats['menunggu'] }}</div>
            <div class="label">Menunggu Skrining TTV</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(16, 185, 129, 0.15); color: #059669;">
            <i class="fa-solid fa-clipboard-check"></i>
        </div>
        <div class="stat-info">
            <div class="num">{{ $stats['selesai'] }}</div>
            <div class="label">Selesai Skrining Hari Ini</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(37, 99, 235, 0.15); color: #2563eb;">
            <i class="fa-solid fa-stethoscope"></i>
        </div>
        <div class="stat-info">
            <div class="num">{{ $stats['total'] }}</div>
            <div class="label">Total Pasien Poliklinik</div>
        </div>
    </div>
</div>

<div class="grid-layout">
    
    {{-- PANEL KIRI: DAFTAR ANTRIAN PASIEN MENUNGGU SKRINING --}}
    <div class="content-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div>
                <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 0;">Antrean Pasien Poliklinik</h3>
                <p style="font-size: 12px; color: #64748b; margin: 2px 0 0;">Pilih pasien untuk memulai input tanda vital.</p>
            </div>
            <span class="badge" style="background:#eff6ff; color:#2563eb; font-weight:700; padding:4px 10px; font-size:11px; border-radius:12px; border:1px solid #bfdbfe;">
                {{ $antreanMenunggu->total() }} Pasien
            </span>
        </div>

        <div style="overflow-x: auto;">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th style="width: 90px;">No. Antrian</th>
                        <th>Nama Pasien</th>
                        <th>Poliklinik</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($antreanMenunggu as $p)
                        <tr>
                            <td style="font-weight: 700; color: #2563eb;">
                                {{ $p->no_antrian }}
                            </td>
                            <td>
                                <strong>{{ $p->pasien?->nama_pasien ?? 'Pasien' }}</strong>
                                <div style="font-size: 11px; color: #64748b;">
                                    {{ $p->pasien?->rm ?? '-' }} • {{ $p->pasien?->jenis_kelamin === 'P' ? 'Perempuan' : 'Laki-laki' }}
                                </div>
                                @if (!empty($p->keluhan_awal))
                                    <div style="font-size: 11px; color: #059669; margin-top: 2px;">
                                        <i class="fa-regular fa-comment-dots"></i> {{ Str::limit($p->keluhan_awal, 35) }}
                                    </div>
                                @endif
                            </td>
                            <td style="color: #475569; font-size: 12px;">
                                {{ $p->poli?->nama_poli ?? 'Poli Umum' }}
                            </td>
                            <td style="text-align: right;">
                                <button type="button" class="btn-action-pilih" 
                                    onclick="pilihPasienSkrining({{ json_encode([
                                        'id' => $p->id_pendaftaran,
                                        'no_antrian' => $p->no_antrian,
                                        'nama' => $p->pasien?->nama_pasien ?? 'Pasien',
                                        'rm' => $p->pasien?->rm ?? '-',
                                        'poli' => $p->poli?->nama_poli ?? 'Poli Umum',
                                        'keluhan' => $p->keluhan_awal ?? '',
                                        'umur' => $p->pasien?->tanggal_lahir ? \Carbon\Carbon::parse($p->pasien->tanggal_lahir)->age . ' tahun' : '-'
                                    ]) }})">
                                    Skrining <i class="fa-solid fa-arrow-right"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 32px 14px; color: #94a3b8;">
                                <i class="fa-solid fa-circle-check" style="font-size: 28px; color: #10b981; margin-bottom: 8px; display: block;"></i>
                                Tidak ada antrean pasien yang menunggu skrining saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Navigasi Paginasi Antrean Pasien --}}
        {{ $antreanMenunggu->links('partials.pagination') }}
    </div>

    {{-- PANEL KANAN: FORM INPUT TANDA-TANDA VITAL (TTV) --}}
    <div class="content-box">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9;">
            <div style="width: 36px; height: 36px; border-radius: 10px; background: #eef4ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                <i class="fa-solid fa-heart-pulse"></i>
            </div>
            <div>
                <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 0;">Form Pengukuran TTV</h3>
                <p style="font-size: 11.5px; color: #64748b; margin: 2px 0 0;">Input tanda vital pasien lalu teruskan ke ruang dokter.</p>
            </div>
        </div>

        <div id="box-pilih-pasien-placeholder" style="padding: 30px 16px; text-align: center; color: #94a3b8; border: 2px dashed #cbd5e1; border-radius: 14px;">
            <i class="fa-solid fa-hand-pointer" style="font-size: 28px; margin-bottom: 10px; display: block; color: #94a3b8;"></i>
            Silakan klik tombol <strong>"Skrining"</strong> pada antrean pasien di sebelah kiri untuk mengisi data tanda vital.
        </div>

        <form id="form-ttv-perawat" action="{{ route('perawat.skrining.store') }}" method="POST" style="display: none;">
            @csrf
            <input type="hidden" name="id_pendaftaran" id="input_id_pendaftaran">

            {{-- Kartu Info Pasien Aktif --}}
            <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 12px 14px; margin-bottom: 18px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span id="label-pasien-antrian" style="font-size: 14px; font-weight: 700; color: #1d4ed8;">-</span>
                    <span id="label-pasien-poli" style="font-size: 11px; background: #ffffff; color: #1d4ed8; padding: 2px 8px; border-radius: 8px; font-weight: 600;">-</span>
                </div>
                <div style="font-size: 13.5px; font-weight: 600; color: #0f172a; margin-top: 4px;" id="label-pasien-nama">-</div>
                <div style="font-size: 11.5px; color: #475569;" id="label-pasien-meta">-</div>
            </div>

            {{-- Row 1: Tensi Tekanan Darah (Sistol / Diastol) --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div class="form-group">
                    <label class="form-label">Sistol (Tensi Atas) *</label>
                    <div class="input-with-unit-wrapper">
                        <input type="number" name="sistol" id="input_sistol" required placeholder="Contoh: 120" min="50" max="260" class="form-control input-with-unit">
                        <span class="unit-badge-suffix">mmHg</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Diastol (Tensi Bawah) *</label>
                    <div class="input-with-unit-wrapper">
                        <input type="number" name="diastol" id="input_diastol" required placeholder="Contoh: 80" min="30" max="180" class="form-control input-with-unit">
                        <span class="unit-badge-suffix">mmHg</span>
                    </div>
                </div>
            </div>

            {{-- Row 2: Suhu Tubuh & Denyut Nadi --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div class="form-group">
                    <label class="form-label">Suhu Tubuh *</label>
                    <div class="input-with-unit-wrapper">
                        <input type="number" step="0.1" name="suhu_tubuh" id="input_suhu" required placeholder="Contoh: 36.5" min="30" max="45" class="form-control input-with-unit">
                        <span class="unit-badge-suffix">°C</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Denyut Nadi *</label>
                    <div class="input-with-unit-wrapper">
                        <input type="number" name="denyut_nadi" id="input_nadi" required placeholder="Contoh: 80" min="30" max="220" class="form-control input-with-unit">
                        <span class="unit-badge-suffix">bpm</span>
                    </div>
                </div>
            </div>

            {{-- Row 3: Berat Badan & Tinggi Badan (Live BMI) --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div class="form-group">
                    <label class="form-label">Berat Badan *</label>
                    <div class="input-with-unit-wrapper">
                        <input type="number" step="0.1" name="berat_badan" id="input_bb" required placeholder="Contoh: 60" min="1" max="300" class="form-control input-with-unit" oninput="hitungLiveBMI()">
                        <span class="unit-badge-suffix">kg</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Tinggi Badan *</label>
                    <div class="input-with-unit-wrapper">
                        <input type="number" step="0.1" name="tinggi_badan" id="input_tb" required placeholder="Contoh: 165" min="20" max="250" class="form-control input-with-unit" oninput="hitungLiveBMI()">
                        <span class="unit-badge-suffix">cm</span>
                    </div>
                </div>
            </div>

            {{-- Badge Indikator BMI Otomatis --}}
            <div id="box-bmi-calc" style="display: none; margin-bottom: 16px;">
                <span class="bmi-badge" id="badge-bmi-result">BMI: 0 (Normal)</span>
            </div>

            {{-- Row 4: Catatan / Keluhan Perawat --}}
            <div class="form-group">
                <label class="form-label">Catatan Perawat / Keluhan Awal Pasien</label>
                <textarea name="catatan_perawat" id="input_catatan" rows="3" class="form-control" placeholder="Catatan kondisi fisik pasien, riwayat alergi, atau keluhan yang disampaikan..."></textarea>
            </div>

            <button type="submit" style="width: 100%; background: #059669; color: white; border: none; padding: 12px 18px; border-radius: 12px; font-size: 13.5px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25);">
                <i class="fa-solid fa-paper-plane"></i> Simpan Skrining & Teruskan ke Dokter
            </button>
        </form>
    </div>

</div>

{{-- BAGIAN BAWAH: RIWAYAT PASIEN SELESAI SKRINING HARI INI --}}
<div class="content-box" style="margin-top: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <div>
            <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 0;">Riwayat Pasien Selesai Skrining Hari Ini</h3>
            <p style="font-size: 12px; color: #64748b; margin: 2px 0 0;">Daftar pasien yang tanda vitalnya telah dicatat dan diteruskan ke dokter.</p>
        </div>
        <span class="badge" style="background:#ecfdf5; color:#059669; font-weight:700; padding:4px 12px; font-size:11px; border-radius:12px; border:1px solid #a7f3d0;">
            {{ $riwayatSkrining->total() }} Pasien Terlayani
        </span>
    </div>

    <div style="overflow-x: auto;">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>No. Antrian</th>
                    <th>Nama Pasien</th>
                    <th>Poliklinik</th>
                    <th>Tensi (TD)</th>
                    <th>Suhu</th>
                    <th>Nadi</th>
                    <th>BB / TB</th>
                    <th>Status Antrean</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($riwayatSkrining as $r)
                    @php
                        $ttv = $r->screeningAwal;
                    @endphp
                    <tr>
                        <td style="font-weight: 700; color: #2563eb;">{{ $r->no_antrian }}</td>
                        <td>
                            <strong>{{ $r->pasien?->nama_pasien ?? 'Pasien' }}</strong>
                            <div style="font-size: 11px; color: #64748b;">{{ $r->pasien?->rm ?? '-' }}</div>
                        </td>
                        <td style="color: #475569; font-size: 12px;">{{ $r->poli?->nama_poli ?? 'Poli Umum' }}</td>
                        <td><strong>{{ $ttv?->sistol ?? '-' }}/{{ $ttv?->diastol ?? '-' }}</strong> mmHg</td>
                        <td>{{ $ttv?->suhu_tubuh ?? '-' }} °C</td>
                        <td>{{ $ttv?->denyut_nadi ?? '-' }} bpm</td>
                        <td>{{ $ttv?->berat_badan ?? '-' }} kg / {{ $ttv?->tinggi_badan ?? '-' }} cm</td>
                        <td>
                            @php
                                $badgeColor = match($r->status_antrian) {
                                    'menunggu_dokter' => ['bg' => '#fef3c7', 'text' => '#b45309', 'label' => 'Menunggu Dokter'],
                                    'sedang_diperiksa' => ['bg' => '#e0e7ff', 'text' => '#4338ca', 'label' => 'Sedang Diperiksa'],
                                    'menunggu_kasir' => ['bg' => '#dbeafe', 'text' => '#1e40af', 'label' => 'Menunggu Kasir'],
                                    'selesai' => ['bg' => '#dcfce7', 'text' => '#15803d', 'label' => 'Selesai'],
                                    default => ['bg' => '#f1f5f9', 'text' => '#475569', 'label' => $r->status_antrian],
                                };
                            @endphp
                            <span style="background: {{ $badgeColor['bg'] }}; color: {{ $badgeColor['text'] }}; font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 6px;">
                                {{ $badgeColor['label'] }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 24px; color: #94a3b8;">
                            Belum ada riwayat skrining pasien yang selesai hari ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Navigasi Paginasi Riwayat Skrining --}}
    {{ $riwayatSkrining->links('partials.pagination') }}
</div>

@push('scripts')
<script>
    function pilihPasienSkrining(pasien) {
        document.getElementById('box-pilih-pasien-placeholder').style.display = 'none';
        const form = document.getElementById('form-ttv-perawat');
        form.style.display = 'block';

        document.getElementById('input_id_pendaftaran').value = pasien.id;
        document.getElementById('label-pasien-antrian').textContent = pasien.no_antrian;
        document.getElementById('label-pasien-poli').textContent = pasien.poli;
        document.getElementById('label-pasien-nama').textContent = pasien.nama;
        document.getElementById('label-pasien-meta').textContent = `No. RM: ${pasien.rm} • Usia: ${pasien.umur}`;
        document.getElementById('input_catatan').value = pasien.keluhan || '';

        // Reset field pengukuran
        document.getElementById('input_sistol').value = '120';
        document.getElementById('input_diastol').value = '80';
        document.getElementById('input_suhu').value = '36.5';
        document.getElementById('input_nadi').value = '80';
        document.getElementById('input_bb').value = '';
        document.getElementById('input_tb').value = '';
        document.getElementById('box-bmi-calc').style.display = 'none';

        // Scroll halus ke form
        form.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function hitungLiveBMI() {
        const bb = parseFloat(document.getElementById('input_bb').value);
        const tb = parseFloat(document.getElementById('input_tb').value);
        const boxBmi = document.getElementById('box-bmi-calc');
        const badgeBmi = document.getElementById('badge-bmi-result');

        if (bb > 0 && tb > 0) {
            const tbMeter = tb / 100;
            const bmi = (bb / (tbMeter * tbMeter)).toFixed(1);
            let status = 'Normal (Ideal)';
            let bg = '#dcfce7';
            let color = '#15803d';

            if (bmi < 18.5) {
                status = 'Underweight (Kurang)';
                bg = '#dbeafe';
                color = '#1d4ed8';
            } else if (bmi >= 25 && bmi < 30) {
                status = 'Overweight (Berlebih)';
                bg = '#fef3c7';
                color = '#b45309';
            } else if (bmi >= 30) {
                status = 'Obesitas';
                bg = '#fee2e2';
                color = '#b91c1c';
            }

            badgeBmi.textContent = `Indeks Massa Tubuh (BMI): ${bmi} — ${status}`;
            badgeBmi.style.background = bg;
            badgeBmi.style.color = color;
            boxBmi.style.display = 'block';
        } else {
            boxBmi.style.display = 'none';
        }
    }

    @if (!empty($pasienTerpilih))
        document.addEventListener('DOMContentLoaded', function() {
            pilihPasienSkrining(@json($pasienTerpilih));
        });
    @endif
</script>
@endpush
@endsection
