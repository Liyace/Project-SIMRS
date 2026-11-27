@extends('layouts.app')
@php $halamanAktif = 'apoteker_obat'; @endphp
@section('title', 'Kelola Inventori & Stok Obat - Rumah Sakit Delta')

@push('styles')
<style>
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
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
    .filter-tabs {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }
    .tab-btn {
        padding: 8px 16px;
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none;
        color: #64748b;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .tab-btn:hover {
        background: #f1f5f9;
        color: #1e293b;
    }
    .tab-btn.active {
        background: #0284c7;
        color: #ffffff;
        border-color: #0284c7;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
    }
    .badge-stok {
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .badge-stok-aman {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #86efac;
    }
    .badge-stok-menipis {
        background: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }
    .badge-stok-habis {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fca5a5;
    }

    /* Modal styling */
    .modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
        z-index: 999;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .modal-card {
        background: #ffffff;
        border-radius: 20px;
        max-width: 520px;
        width: 100%;
        padding: 24px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    }

    /* Dark Mode Overrides */
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
    body.dark-mode .tab-btn {
        background: #0b0f19 !important;
        border-color: #1e293b !important;
        color: #94a3b8 !important;
    }
    body.dark-mode .tab-btn.active {
        background: #0284c7 !important;
        color: #ffffff !important;
    }
    body.dark-mode .modal-card {
        background: #121927 !important;
        border: 1px solid #1e293b !important;
        color: #f1f5f9 !important;
    }
</style>
@endpush

@section('content')
<div class="content-body" style="padding-bottom: 40px;">

    <!-- HEADER TITLE & ACTIONS -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 14px;">
        <div>
            <h1 style="font-size: 22px; font-weight: 700; color: #1e293b; margin: 0;">Kelola Obat & Stok Farmasi</h1>
            <p style="font-size: 13px; color: #64748b; margin: 4px 0 0;">
                Inventori katalog obat, pemantauan batas stok minimum, dan pencatatan stok masuk dari distributor.
            </p>
        </div>

        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <!-- FORM PENCARIAN -->
            <form action="{{ route('apoteker.obat') }}" method="GET" style="display: flex; gap: 8px;">
                <input type="hidden" name="status" value="{{ $status }}">
                <div style="position: relative;">
                    <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama / kode obat..."
                           style="padding: 8px 12px 8px 34px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 12.5px; width: 220px;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 11px; color: #94a3b8; font-size: 12px;"></i>
                </div>
                @if(!empty($q))
                    <a href="{{ route('apoteker.obat', ['status' => $status]) }}" class="simrs-btn-white" style="padding: 8px 12px; font-size: 12px;" title="Reset Pencarian">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </form>

            <!-- TOMBOL EXPORT EXCEL & PDF -->
            <a href="{{ route('apoteker.obat.stok-excel', ['status' => $status]) }}"
               style="background: #10b981; color: white; border: none; padding: 8px 14px; border-radius: 10px; font-size: 12px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.25);"
               title="Export Laporan Stok Opname ke Microsoft Excel (.xlsx)">
                <i class="fa-solid fa-file-excel"></i> Export Excel
            </a>

            <a href="{{ route('apoteker.obat.stok-pdf', ['status' => $status]) }}" target="_blank"
               style="background: #ef4444; color: white; border: none; padding: 8px 14px; border-radius: 10px; font-size: 12px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 10px rgba(239, 68, 68, 0.25);"
               title="Cetak Laporan Stok Opname & Mutasi Farmasi (PDF)">
                <i class="fa-solid fa-file-pdf"></i> Cetak PDF
            </a>

            <!-- TOMBOL TAMBAH OBAT BARU -->
            <button type="button" onclick="bukaModalTambahObat()"
                    style="background: #2563eb; color: white; border: none; padding: 8px 16px; border-radius: 10px; font-size: 12px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25);">
                <i class="fa-solid fa-plus"></i> Tambah Obat Baru
            </button>
        </div>
    </div>

    <!-- FLASH MESSAGES -->
    @if(session('success'))
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; font-size: 13px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-circle-check" style="font-size: 18px; color: #10b981;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; font-size: 13px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 4 STAT CARDS INVENTORI -->
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: #eff6ff; color: #2563eb;">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div class="stat-info">
                <div class="num">{{ $totalObat }}</div>
                <div class="label">Total Master Obat</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #ecfdf5; color: #10b981;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="stat-info">
                <div class="num">{{ $totalAman }}</div>
                <div class="label">Stok Aman</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #fffbeb; color: #d97706;">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="stat-info">
                <div class="num">{{ $totalMenipis }}</div>
                <div class="label">Stok Menipis (<= Batas Min)</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #fef2f2; color: #dc2626;">
                <i class="fa-solid fa-ban"></i>
            </div>
            <div class="stat-info">
                <div class="num">{{ $totalHabis }}</div>
                <div class="label">Stok Habis (0)</div>
            </div>
        </div>
    </div>

    <!-- FILTER STATUS STOK -->
    <div class="filter-tabs">
        <a href="{{ route('apoteker.obat', ['q' => $q, 'status' => 'semua']) }}"
           class="tab-btn {{ $status === 'semua' ? 'active' : '' }}">
            Semua Obat ({{ $totalObat }})
        </a>
        <a href="{{ route('apoteker.obat', ['q' => $q, 'status' => 'menipis']) }}"
           class="tab-btn {{ $status === 'menipis' ? 'active' : '' }}">
            <i class="fa-solid fa-triangle-exclamation"></i> Menipis ({{ $totalMenipis }})
        </a>
        <a href="{{ route('apoteker.obat', ['q' => $q, 'status' => 'habis']) }}"
           class="tab-btn {{ $status === 'habis' ? 'active' : '' }}">
            <i class="fa-solid fa-ban"></i> Habis ({{ $totalHabis }})
        </a>
        <a href="{{ route('apoteker.obat', ['q' => $q, 'status' => 'aman']) }}"
           class="tab-btn {{ $status === 'aman' ? 'active' : '' }}">
            <i class="fa-solid fa-check"></i> Aman ({{ $totalAman }})
        </a>
    </div>

    <!-- TABEL MASTER OBAT -->
    <div class="content-box">
        <div style="overflow-x: auto;">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th style="width: 14%;">KODE OBAT</th>
                        <th style="width: 26%;">NAMA OBAT</th>
                        <th style="width: 10%;">SATUAN</th>
                        <th style="width: 14%;">HARGA JUAL</th>
                        <th style="width: 12%;">STOK SAAT INI</th>
                        <th style="width: 12%;">STATUS</th>
                        <th style="width: 12%; text-align: center;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($obatList as $o)
                        @php
                            if ($o->stok <= 0) {
                                $stokClass = 'badge-stok-habis';
                                $stokText = 'Habis';
                                $stokIcon = 'fa-ban';
                            } elseif ($o->stok <= $o->stok_minimum) {
                                $stokClass = 'badge-stok-menipis';
                                $stokText = 'Menipis';
                                $stokIcon = 'fa-triangle-exclamation';
                            } else {
                                $stokClass = 'badge-stok-aman';
                                $stokText = 'Aman';
                                $stokIcon = 'fa-check';
                            }
                        @endphp
                        <tr>
                            <td>
                                <span style="font-family: monospace; font-weight: 700; color: #2563eb; background: #eff6ff; padding: 2px 7px; border-radius: 6px;">
                                    {{ $o->kode_obat }}
                                </span>
                            </td>
                            <td>
                                <strong style="font-size: 13.5px;">{{ $o->nama_obat }}</strong>
                                <div style="font-size: 11px; color: #64748b;">
                                    Beli: Rp {{ number_format($o->harga_beli ?? 0, 0, ',', '.') }}
                                </div>
                            </td>
                            <td>
                                <span style="text-transform: capitalize; color: #475569;">{{ $o->satuan }}</span>
                            </td>
                            <td>
                                <strong style="color: #059669; font-size: 13.5px;">
                                    Rp {{ number_format($o->harga_obat, 0, ',', '.') }}
                                </strong>
                            </td>
                            <td>
                                <strong style="font-size: 14px;">{{ $o->stok }}</strong>
                                <span style="font-size: 11px; color: #64748b;">(Min: {{ $o->stok_minimum }})</span>
                            </td>
                            <td>
                                <span class="badge-stok {{ $stokClass }}">
                                    <i class="fa-solid {{ $stokIcon }}"></i> {{ $stokText }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <button type="button" onclick="bukaModalTambahStok({{ $o->id_obat }}, '{{ addslashes($o->nama_obat) }}', '{{ $o->satuan }}', {{ $o->stok }}, {{ $o->harga_beli ?? 0 }}, {{ $o->harga_obat }})"
                                        style="background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; padding: 6px 12px; border-radius: 8px; font-size: 11.5px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;"
                                        title="Tambah Stok Masuk">
                                    <i class="fa-solid fa-plus"></i> Stok
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px 14px; color: #64748b;">
                                Tidak ada data obat yang sesuai dengan pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($obatList->hasPages())
            <div style="margin-top: 20px;">
                {{ $obatList->links() }}
            </div>
        @endif
    </div>

</div>

<!-- MODAL 1: TAMBAH OBAT BARU -->
<div class="modal-backdrop" id="modalTambahObat">
    <div class="modal-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
            <h3 style="font-size: 16px; font-weight: 700; margin: 0;">Tambah Master Obat Baru</h3>
            <button type="button" onclick="tutupModalTambahObat()" style="border: none; background: transparent; font-size: 18px; cursor: pointer; color: #94a3b8;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('apoteker.obat.store') }}" method="POST">
            @csrf
            <div style="margin-bottom: 14px;">
                <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px;">Nama Obat *</label>
                <input type="text" name="nama_obat" required placeholder="Contoh: Paracetamol 500mg Tablet"
                       style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px;">Kode Obat (Opsional)</label>
                    <input type="text" name="kode_obat" placeholder="Otomatis jika kosong"
                           style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;">
                </div>
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px;">Satuan *</label>
                    <select name="satuan" required style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;">
                        <option value="tablet">Tablet</option>
                        <option value="kapsul">Kapsul</option>
                        <option value="botol">Botol (Sirup)</option>
                        <option value="tube">Tube (Salep)</option>
                        <option value="ampul">Ampul (Injeksi)</option>
                        <option value="sachet">Sachet</option>
                        <option value="strip">Strip</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px;">Harga Beli (Rp)</label>
                    <input type="number" name="harga_beli" min="0" placeholder="0"
                           style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;">
                </div>
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px;">Harga Jual Pasien (Rp) *</label>
                    <input type="number" name="harga_obat" required min="0" placeholder="Contoh: 5000"
                           style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px;">Stok Awal *</label>
                    <input type="number" name="stok" required min="0" placeholder="Contoh: 100"
                           style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;">
                </div>
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px;">Batas Stok Minimum</label>
                    <input type="number" name="stok_minimum" min="0" value="10"
                           style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="tutupModalTambahObat()" class="simrs-btn-white" style="padding: 10px 16px;">Batal</button>
                <button type="submit" style="background: #2563eb; color: white; border: none; padding: 10px 18px; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    <i class="fa-solid fa-floppy-disk" style="margin-right: 4px;"></i> Simpan Obat
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 2: TAMBAH STOK MASUK -->
<div class="modal-backdrop" id="modalTambahStok">
    <div class="modal-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
            <div>
                <h3 style="font-size: 16px; font-weight: 700; margin: 0;">Tambah Stok Masuk</h3>
                <span style="font-size: 12px; color: #64748b;" id="stokModalNamaObat">-</span>
            </div>
            <button type="button" onclick="tutupModalTambahStok()" style="border: none; background: transparent; font-size: 18px; cursor: pointer; color: #94a3b8;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="formTambahStok" method="POST">
            @csrf
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px; margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between; font-size: 12.5px;">
                    <span style="color: #64748b;">Stok Saat Ini:</span>
                    <strong style="font-size: 14px;" id="stokModalSaatIni">0</strong>
                </div>
            </div>

            <div style="margin-bottom: 14px;">
                <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px;">Jumlah Stok Tambahan Masuk *</label>
                <input type="number" name="tambah_stok" required min="1" placeholder="Contoh: 50"
                       style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 14px; font-weight: 700;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px;">Perbarui Harga Beli (Rp)</label>
                    <input type="number" name="harga_beli" id="stokModalHargaBeli" min="0"
                           style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;">
                </div>
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px;">Perbarui Harga Jual (Rp)</label>
                    <input type="number" name="harga_obat" id="stokModalHargaJual" min="0"
                           style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="tutupModalTambahStok()" class="simrs-btn-white" style="padding: 10px 16px;">Batal</button>
                <button type="submit" style="background: #059669; color: white; border: none; padding: 10px 18px; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    <i class="fa-solid fa-plus" style="margin-right: 4px;"></i> Tambah Stok
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function bukaModalTambahObat() {
        document.getElementById('modalTambahObat').style.display = 'flex';
    }
    function tutupModalTambahObat() {
        document.getElementById('modalTambahObat').style.display = 'none';
    }
    function bukaModalTambahStok(id, nama, satuan, stok, beli, jual) {
        document.getElementById('formTambahStok').action = `/apoteker/obat/${id}/stok`;
        document.getElementById('stokModalNamaObat').innerText = nama;
        document.getElementById('stokModalSaatIni').innerText = `${stok} ${satuan}`;
        document.getElementById('stokModalHargaBeli').value = beli || '';
        document.getElementById('stokModalHargaJual').value = jual || '';
        document.getElementById('modalTambahStok').style.display = 'flex';
    }
    function tutupModalTambahStok() {
        document.getElementById('modalTambahStok').style.display = 'none';
    }
</script>
@endsection
