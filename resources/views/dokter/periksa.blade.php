@extends('layouts.app')
@php $halamanAktif = 'dokter_pelayanan'; @endphp
@section('title', 'Pemeriksaan Pasien ' . ($pendaftaran->pasien?->nama_pasien ?? 'Pasien') . ' - RS Delta')

@push('styles')
<style>
    .patient-header-banner {
        background: linear-gradient(135deg, #1e3a8a, #2563eb);
        color: #ffffff;
        border-radius: 20px;
        padding: 24px 28px;
        margin-bottom: 24px;
        box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.3);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }
    .patient-main-info h2 {
        font-size: 20px;
        font-weight: 700;
        margin: 0 0 6px 0;
        letter-spacing: 0.3px;
    }
    .patient-badges {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        font-size: 12px;
    }
    .patient-badge-item {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(8px);
        padding: 4px 12px;
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    .exam-section {
        background: #ffffff;
        border-radius: 20px;
        padding: 24px 28px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        margin-bottom: 24px;
    }
    .section-head {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
    }
    .section-head-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }
    .section-head-title {
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }
    .section-head-sub {
        font-size: 11.5px;
        color: #64748b;
        margin: 2px 0 0;
    }
    .ttv-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 14px;
        margin-bottom: 16px;
    }
    .ttv-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 14px;
        text-align: center;
    }
    .ttv-card .label {
        font-size: 11px;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
    }
    .ttv-card .val {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        margin-top: 4px;
    }
    .form-group {
        margin-bottom: 16px;
    }
    .form-label {
        display: block;
        font-size: 12.5px;
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
    .tindakan-item-label {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s;
        margin-bottom: 8px;
        background: #f8fafc;
    }
    .tindakan-item-label:hover {
        background: #eff6ff;
        border-color: #bfdbfe;
    }
    .resep-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        margin-bottom: 14px;
    }
    .resep-table th {
        background: #f8fafc;
        padding: 10px 12px;
        text-align: left;
        border-bottom: 1px solid #e2e8f0;
        font-size: 11px;
        color: #475569;
        text-transform: uppercase;
    }
    .resep-table td {
        padding: 8px 10px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .btn-add-obat {
        background: #eff6ff;
        color: #2563eb;
        border: 1px dashed #3b82f6;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    .btn-add-obat:hover {
        background: #2563eb;
        color: #ffffff;
    }
    .btn-del-row {
        background: transparent;
        color: #ef4444;
        border: none;
        cursor: pointer;
        font-size: 14px;
        padding: 4px;
    }
    .btn-del-row:hover {
        color: #b91c1c;
    }
    /* Dark Mode */
    body.dark-mode .exam-section {
        background: #121927;
        border-color: #1e293b;
    }
    body.dark-mode .section-head-title {
        color: #f1f5f9;
    }
    body.dark-mode .ttv-card,
    body.dark-mode .tindakan-item-label {
        background: #0b0f19;
        border-color: #1e293b;
    }
    body.dark-mode .ttv-card .val {
        color: #f1f5f9;
    }
    body.dark-mode .form-control {
        background: #0b0f19;
        border-color: #334155;
        color: #f1f5f9;
    }
    body.dark-mode .form-label {
        color: #94a3b8;
    }
    body.dark-mode .resep-table th {
        background: #0b0f19;
        color: #94a3b8;
        border-color: #1e293b;
    }
    body.dark-mode .resep-table td {
        border-color: #1e293b;
        color: #cbd5e1;
    }
</style>
@endpush

@section('content')
{{-- BANNER IDENTITAS PASIEN --}}
<div class="patient-header-banner">
    <div class="patient-main-info">
        <div style="font-size: 12px; opacity: 0.9; margin-bottom: 2px;">PASIEN POLIKLINIK SEDANG DIPERIKSA</div>
        <h2>{{ $pendaftaran->pasien?->nama_pasien ?? 'Pasien' }}</h2>
        <div class="patient-badges">
            <span class="patient-badge-item"><i class="fa-solid fa-ticket"></i> Antrean: <strong>{{ $pendaftaran->no_antrian }}</strong></span>
            <span class="patient-badge-item"><i class="fa-solid fa-id-card"></i> RM: <strong>{{ $pendaftaran->pasien?->rm ?? '-' }}</strong></span>
            <span class="patient-badge-item"><i class="fa-solid fa-hospital"></i> Poli: <strong>{{ $pendaftaran->poli?->nama_poli ?? 'Poli Umum' }}</strong></span>
            <span class="patient-badge-item"><i class="fa-solid fa-user"></i> Gender: {{ $pendaftaran->pasien?->jenis_kelamin === 'P' ? 'Perempuan' : 'Laki-laki' }}</span>
            @if ($pendaftaran->pasien?->tanggal_lahir)
                <span class="patient-badge-item"><i class="fa-solid fa-cake-candles"></i> Usia: {{ \Carbon\Carbon::parse($pendaftaran->pasien->tanggal_lahir)->age }} tahun</span>
            @endif
        </div>
    </div>
    <div>
        <a href="{{ route('dokter.pelayanan') }}" style="background: rgba(255,255,255,0.2); color: white; padding: 10px 18px; border-radius: 12px; font-size: 12px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; border: 1px solid rgba(255,255,255,0.4);">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Antrean
        </a>
    </div>
</div>

@if ($errors->any())
    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 14px 18px; border-radius: 14px; margin-bottom: 20px; font-size: 13px;">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach ($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('dokter.periksa.store', $pendaftaran->id_pendaftaran) }}" method="POST">
    @csrf

    {{-- SECTION 1: HASIL SKRINING TANDA-TANDA VITAL (PERAWAT) --}}
    <div class="exam-section">
        <div class="section-head">
            <div class="section-head-icon" style="background: #ecfdf5; color: #059669;">
                <i class="fa-solid fa-heart-pulse"></i>
            </div>
            <div>
                <h3 class="section-head-title">1. Hasil Skrining TTV (Oleh Perawat)</h3>
                <p class="section-head-sub">Data fisik tanda-tanda vital yang diukur oleh perawat sebelum masuk ke ruang dokter.</p>
            </div>
        </div>

        @php
            $ttv = $pendaftaran->screeningAwal;
        @endphp

        @if ($ttv)
            <div class="ttv-grid">
                <div class="ttv-card">
                    <div class="label">Tekanan Darah (TD)</div>
                    <div class="val" style="color: #2563eb;">{{ $ttv->sistol }}/{{ $ttv->diastol }} <span style="font-size:11px; font-weight:normal;">mmHg</span></div>
                </div>
                <div class="ttv-card">
                    <div class="label">Suhu Tubuh</div>
                    <div class="val" style="color: #059669;">{{ $ttv->suhu_tubuh }} <span style="font-size:11px; font-weight:normal;">°C</span></div>
                </div>
                <div class="ttv-card">
                    <div class="label">Denyut Nadi</div>
                    <div class="val" style="color: #d97706;">{{ $ttv->denyut_nadi }} <span style="font-size:11px; font-weight:normal;">bpm</span></div>
                </div>
                <div class="ttv-card">
                    <div class="label">Berat Badan</div>
                    <div class="val">{{ $ttv->berat_badan }} <span style="font-size:11px; font-weight:normal;">kg</span></div>
                </div>
                <div class="ttv-card">
                    <div class="label">Tinggi Badan</div>
                    <div class="val">{{ $ttv->tinggi_badan }} <span style="font-size:11px; font-weight:normal;">cm</span></div>
                </div>
                @if ($bmiData)
                    <div class="ttv-card" style="border-color: {{ $bmiData['color'] }};">
                        <div class="label">Indeks Massa Tubuh</div>
                        <div class="val" style="color: {{ $bmiData['color'] }}; font-size: 15px;">{{ $bmiData['nilai'] }}</div>
                        <div style="font-size: 9.5px; font-weight: 600; color: {{ $bmiData['color'] }}; margin-top: 2px;">{{ $bmiData['status'] }}</div>
                    </div>
                @endif
            </div>

            @if (!empty($ttv->catatan_perawat))
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px 14px; font-size: 12.5px; color: #475569;">
                    <strong style="color: #0f172a;"><i class="fa-solid fa-notes-medical" style="color: #2563eb;"></i> Catatan Perawat / Keluhan Awal:</strong>
                    {{ $ttv->catatan_perawat }}
                </div>
            @endif
        @else
            <div style="padding: 16px; background: #fffbeb; border: 1px solid #fef3c7; border-radius: 10px; color: #b45309; font-size: 12.5px;">
                <i class="fa-solid fa-triangle-exclamation"></i> Pasien belum diskrining oleh perawat. Dokter dapat melanjutkan pemeriksaan langsung.
            </div>
        @endif
    </div>

    {{-- SECTION 2: PEMERIKSAAN KLINIS & REKAM MEDIS --}}
    <div class="exam-section">
        <div class="section-head">
            <div class="section-head-icon" style="background: #eff6ff; color: #2563eb;">
                <i class="fa-solid fa-stethoscope"></i>
            </div>
            <div>
                <h3 class="section-head-title">2. Pemeriksaan Klinis & Rekam Medis Dokter</h3>
                <p class="section-head-sub">Tuliskan anamnesis, diagnosa ICD-10, dan catatan edukasi pasien.</p>
            </div>
        </div>

        @php
            $rmExisting = $pendaftaran->rekamMedis;
        @endphp

        {{-- Anamnesis --}}
        <div class="form-group">
            <label class="form-label">Anamnesis / Riwayat Penyakit & Keluhan Utama *</label>
            <textarea name="anamnesis" rows="3" required class="form-control" placeholder="Contoh: Pasien mengeluh nyeri tenggorokan dan batuk kering sejak 3 hari yang lalu, demam sumeng-sumeng...">{{ old('anamnesis', $rmExisting?->anamnesis ?? $pendaftaran->keluhan_awal) }}</textarea>
        </div>

        {{-- Diagnosa Utama & Kode ICD-10 --}}
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 14px;">
            <div class="form-group">
                <label class="form-label">Diagnosa Utama *</label>
                <input type="text" name="diagnosa_utama" required class="form-control" 
                       value="{{ old('diagnosa_utama', $rmExisting?->diagnosa_utama ?? '') }}" 
                       placeholder="Contoh: ISPA (Infeksi Saluran Pernapasan Akut)">
            </div>
            <div class="form-group">
                <label class="form-label">Kode ICD-10 (Opsional)</label>
                <input type="text" name="kode_icd10" class="form-control" 
                       value="{{ old('kode_icd10', $rmExisting?->kode_icd10 ?? '') }}" 
                       placeholder="Contoh: J06.9">
            </div>
        </div>

        {{-- Diagnosa Sekunder & Edukasi --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
            <div class="form-group">
                <label class="form-label">Diagnosa Sekunder (Opsional)</label>
                <input type="text" name="diagnosa_sekunder" class="form-control" 
                       value="{{ old('diagnosa_sekunder', $rmExisting?->diagnosa_sekunder ?? '') }}" 
                       placeholder="Contoh: Rhinitis Akut, Faringitis">
            </div>
            <div class="form-group">
                <label class="form-label">Catatan Edukasi / Nasehat Pasien</label>
                <input type="text" name="catatan_edukasi" class="form-control" 
                       value="{{ old('catatan_edukasi', $rmExisting?->catatan_edukasi ?? '') }}" 
                       placeholder="Contoh: Istirahat cukup, hindari makanan berminyak & es">
            </div>
        </div>
    </div>

    {{-- SECTION 3: TINDAKAN MEDIS DOKTER --}}
    <div class="exam-section">
        <div class="section-head">
            <div class="section-head-icon" style="background: #fdf2f8; color: #db2777;">
                <i class="fa-solid fa-syringe"></i>
            </div>
            <div>
                <h3 class="section-head-title">3. Tindakan Medis yang Diberikan</h3>
                <p class="section-head-sub">Pilih tindakan klinis yang dikerjakan. Tarif tindakan otomatis masuk ke tagihan loket kasir.</p>
            </div>
        </div>

        @php
            $tindakanTerpilih = $rmExisting?->tindakanMedis?->pluck('id_layanan')->toArray() ?? [1]; // default Konsultasi Poli Umum
        @endphp

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 10px;">
            @foreach ($masterLayanan as $layanan)
                <label class="tindakan-item-label">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <input type="checkbox" name="tindakan[]" value="{{ $layanan->id_layanan }}" 
                               {{ in_array($layanan->id_layanan, old('tindakan', $tindakanTerpilih)) ? 'checked' : '' }}
                               style="width: 16px; height: 16px; accent-color: #2563eb;">
                        <span style="font-weight: 600; color: #1e293b; font-size: 13px;">{{ $layanan->nama_layanan }}</span>
                    </div>
                    <span style="font-weight: 700; color: #059669; font-size: 12.5px;">
                        Rp {{ number_format($layanan->harga_layanan, 0, ',', '.') }}
                    </span>
                </label>
            @endforeach
        </div>
    </div>

    {{-- SECTION 4: E-RESEP OBAT DOKTER --}}
    <div class="exam-section">
        <div class="section-head" style="justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div class="section-head-icon" style="background: #ecfdf5; color: #10b981;">
                    <i class="fa-solid fa-prescription-bottle-medical"></i>
                </div>
                <div>
                    <h3 class="section-head-title">4. E-Resep Obat Dokter</h3>
                    <p class="section-head-sub">Tuliskan resep obat untuk diteruskan ke loket pembayaran kasir dan instalasi farmasi.</p>
                </div>
            </div>
            <button type="button" class="btn-add-obat" onclick="tambahBarisObat()">
                <i class="fa-solid fa-plus"></i> Tambah Obat
            </button>
        </div>

        <table class="resep-table">
            <thead>
                <tr>
                    <th style="min-width: 200px;">Nama Obat</th>
                    <th style="width: 100px;">Jumlah</th>
                    <th>Aturan Pakai / Signa</th>
                    <th style="width: 50px; text-align: center;">Hapus</th>
                </tr>
            </thead>
            <tbody id="body-resep-obat">
                {{-- Baris default obat pertama --}}
                <tr class="row-obat">
                    <td>
                        <select name="obat[0][id_obat]" class="form-control" style="font-size: 12.5px;">
                            <option value="">-- Pilih Obat --</option>
                            @foreach ($masterObat as $o)
                                <option value="{{ $o->id_obat }}">{{ $o->nama_obat }} (Stok: {{ $o->stok }} {{ $o->satuan }}) - Rp {{ number_format($o->harga_obat, 0, ',', '.') }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" name="obat[0][jumlah]" class="form-control" value="10" min="1" placeholder="Jml">
                    </td>
                    <td>
                        <input type="text" name="obat[0][aturan]" class="form-control" value="3 x 1 tablet sesudah makan" placeholder="Contoh: 3 x 1 tablet sesudah makan">
                    </td>
                    <td style="text-align: center;">
                        <button type="button" class="btn-del-row" onclick="hapusBarisObat(this)" title="Hapus obat ini"><i class="fa-solid fa-trash"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="form-group" style="margin-top: 14px;">
            <label class="form-label">Catatan Tambahan untuk Apoteker / Pasien (Opsional)</label>
            <input type="text" name="catatan_resep" class="form-control" placeholder="Contoh: Antibiotik harus dihabiskan, paracetamol bila demam saja">
        </div>
    </div>

    {{-- TOMBOL SUBMIT PEMERIKSAAN DOKTER --}}
    <div style="display: flex; justify-content: flex-end; gap: 12px; margin-bottom: 40px;">
        <a href="{{ route('dokter.pelayanan') }}" style="background: #f1f5f9; color: #475569; padding: 12px 24px; border-radius: 12px; font-size: 13.5px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
            Batal
        </a>
        <button type="submit" style="background: #059669; color: white; border: none; padding: 12px 30px; border-radius: 12px; font-size: 14px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 15px rgba(5, 150, 105, 0.35);">
            <i class="fa-solid fa-check-circle"></i> Selesai Pemeriksaan & Terbitkan Resep ke Kasir
        </button>
    </div>
</form>

@push('scripts')
<script>
    let obatIndex = 1;
    const masterObatOptions = `
        <option value="">-- Pilih Obat --</option>
        @foreach ($masterObat as $o)
            <option value="{{ $o->id_obat }}">{{ $o->nama_obat }} (Stok: {{ $o->stok }} {{ $o->satuan }}) - Rp {{ number_format($o->harga_obat, 0, ',', '.') }}</option>
        @endforeach
    `;

    function tambahBarisObat() {
        const tbody = document.getElementById('body-resep-obat');
        const tr = document.createElement('tr');
        tr.className = 'row-obat';
        tr.innerHTML = `
            <td>
                <select name="obat[${obatIndex}][id_obat]" class="form-control" style="font-size: 12.5px;">
                    ${masterObatOptions}
                </select>
            </td>
            <td>
                <input type="number" name="obat[${obatIndex}][jumlah]" class="form-control" value="10" min="1" placeholder="Jml">
            </td>
            <td>
                <input type="text" name="obat[${obatIndex}][aturan]" class="form-control" value="3 x 1 tablet sesudah makan" placeholder="Contoh: 3 x 1 tablet sesudah makan">
            </td>
            <td style="text-align: center;">
                <button type="button" class="btn-del-row" onclick="hapusBarisObat(this)" title="Hapus obat ini"><i class="fa-solid fa-trash"></i></button>
            </td>
        `;
        tbody.appendChild(tr);
        obatIndex++;
    }

    function hapusBarisObat(btn) {
        const row = btn.closest('tr');
        if (row) {
            row.remove();
        }
    }
</script>
@endpush
@endsection
