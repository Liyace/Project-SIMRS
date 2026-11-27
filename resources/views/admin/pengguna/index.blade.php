@extends('layouts.app')
@php $halamanAktif = 'admin_pengguna'; @endphp
@section('title', 'Manajemen Pengguna & Pemantauan Sistem - Admin IT')

@push('styles')
<style>
    .badge-role-resepsionis {
        background-color: #ecfdf5 !important;
        color: #047857 !important;
        border: 1px solid #a7f3d0 !important;
    }
    body.dark-mode .badge-role-resepsionis {
        background-color: rgba(16, 185, 129, 0.25) !important;
        color: #86efac !important;
        border-color: rgba(110, 231, 183, 0.3) !important;
    }
    /* Tombol Gear Settings & Dropdown Aksi */
    .btn-gear-action {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 13px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        background: #f8fafc;
        color: #334155;
        border: 1px solid #cbd5e1;
        transition: all 0.2s ease;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    }
    .btn-gear-action:hover {
        background: #ffffff;
        border-color: #94a3b8;
        color: #1e293b;
        box-shadow: 0 4px 12px rgba(0,0,0,0.07);
        transform: translateY(-1px);
    }
    .btn-gear-action i.fa-gear {
        font-size: 13.5px;
        color: #2563eb;
        transition: transform 0.3s ease;
    }
    .btn-gear-action:hover i.fa-gear {
        transform: rotate(60deg);
    }
    .dropdown-action-wrapper {
        position: relative;
        display: inline-block;
    }
    .dropdown-action-menu {
        position: absolute;
        right: 0;
        top: calc(100% + 6px);
        min-width: 215px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        z-index: 100;
        padding: 6px;
        text-align: left;
    }
    .dropdown-header-title {
        font-size: 10.5px;
        text-transform: uppercase;
        font-weight: 700;
        color: #94a3b8;
        padding: 6px 10px 4px;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #f1f5f9;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .dropdown-item-action {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 12px;
        border: none;
        background: transparent;
        color: #334155;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        border-radius: 8px;
        transition: all 0.15s ease;
        text-decoration: none;
        box-sizing: border-box;
    }
    .dropdown-item-action:hover {
        background: #f1f5f9;
        color: #0f172a;
    }
    .dropdown-item-action i {
        font-size: 13px;
        width: 16px;
        text-align: center;
    }
    .dropdown-item-danger:hover {
        background: #fef2f2;
        color: #dc2626;
    }
    /* Dark Mode */
    body.dark-mode .btn-gear-action {
        background: #1e293b;
        border-color: #334155;
        color: #f1f5f9;
    }
    body.dark-mode .btn-gear-action:hover {
        background: #334155;
        border-color: #475569;
    }
    body.dark-mode .btn-gear-action i.fa-gear {
        color: #60a5fa;
    }
    body.dark-mode .dropdown-action-menu {
        background: #1e293b;
        border-color: #334155;
        box-shadow: 0 10px 30px rgba(0,0,0,0.6);
    }
    body.dark-mode .dropdown-header-title {
        color: #64748b;
        border-bottom-color: #334155;
    }
    body.dark-mode .dropdown-item-action {
        color: #cbd5e1;
    }
    body.dark-mode .dropdown-item-action:hover {
        background: #334155;
        color: #ffffff;
    }
    body.dark-mode .dropdown-item-danger:hover {
        background: rgba(239, 68, 68, 0.15);
        color: #f87171;
    }
    body.dark-mode #modalAturPoliDokter .modal-box-card {
        background: #121927 !important;
        border: 1px solid #1e293b;
        color: #f1f5f9;
    }
    body.dark-mode #modalAturPoliDokter .modal-box-card h3 {
        color: #f8fafc !important;
    }
    body.dark-mode #modalAturPoliDokter .modal-box-card label {
        color: #cbd5e1 !important;
    }
    body.dark-mode #modalAturPoliDokter .modal-box-card select,
    body.dark-mode #modalAturPoliDokter .modal-box-card input {
        background: #0f172a !important;
        border-color: #334155 !important;
        color: #f8fafc !important;
    }
    body.dark-mode #modalAturPoliDokter .info-preview-box {
        background: #0f172a !important;
        border-color: #1e293b !important;
    }
    body.dark-mode #modalAturPoliDokter #modal-nama-dokter {
        color: #93c5fd !important;
    }
</style>
@endpush

@section('content')
<div style="padding-bottom: 50px;">

    <!-- HEADER TITLE & ROLE BADGE -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 25px;">
        <div>
            <h2 style="font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 4px;">
                <i class="fa-solid fa-users-gear" style="color: #1a4a8d; margin-right: 8px;"></i> Manajemen Pengguna & Keamanan Sistem
            </h2>
            <p style="font-size: 13px; color: #64748b;">
                Pusat pemantauan akun staf rumah sakit, pencarian akun pasien, pengaturan hak akses, reset password, dan pemulihan 2FA.
            </p>
        </div>
        <div style="text-align: right;">
            <span style="background: #e0e7ff; color: #3730a3; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; border: 1px solid #c7d2fe;">
                <i class="fa-solid fa-server"></i> Tim TI & SIMRS
            </span>
        </div>
    </div>

    <!-- NOTIFIKASI SUKSES PROSES RESET REQUEST PENGGUNA -->
    @if (session('status_reset_sukses'))
        @php $sr = session('status_reset_sukses'); @endphp
        <div style="background: #ffffff; border-radius: 18px; border: 1.5px solid #10b981; padding: 22px; margin-bottom: 25px; box-shadow: 0 6px 20px rgba(16, 185, 129, 0.12);">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="width: 48px; height: 48px; border-radius: 14px; background: #d1fae5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 22px;">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div>
                        <strong style="color: #065f46; font-size: 15px; display: block;">{{ $sr['message'] }}</strong>
                        <div style="margin-top: 5px; font-size: 12.5px; color: #334155; display: flex; flex-wrap: wrap; gap: 12px; align-items: center;">
                            <span>Pengguna: <strong>{{ $sr['nama'] }}</strong> (NIK: {{ $sr['nik'] }})</span>
                            @if (!empty($sr['temp_password']))
                                <span>Password Baru: <strong style="font-family: monospace; background: #f1f5f9; padding: 2px 8px; border-radius: 6px; border: 1px solid #cbd5e1; color: #0f172a; font-size: 14px;">{{ $sr['temp_password'] }}</strong></span>
                            @endif
                            @if (!empty($sr['no_hp']))
                                <span style="color: #059669;"><i class="fa-brands fa-whatsapp"></i> {{ $sr['no_hp'] }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                @if (!empty($sr['wa_url']))
                    <a href="{{ $sr['wa_url'] }}" target="_blank"
                       style="background: #25D366; color: white; padding: 11px 20px; border-radius: 12px; text-decoration: none; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3); transition: transform 0.2s;"
                       onmouseover="this.style.transform='translateY(-1px)';" onmouseout="this.style.transform='translateY(0)';">
                        <i class="fa-brands fa-whatsapp" style="font-size: 18px;"></i> Kirim Notifikasi via WhatsApp ke Pengguna
                    </a>
                @endif
            </div>
        </div>
    @endif

    <!-- NOTIFIKASI AKSI (RESET PASSWORD / RESET 2FA / TAMBAH STAF) -->
    @if (session('status_pegawai'))
        @php $sp = session('status_pegawai'); @endphp
        <div style="background: #ffffff; border-radius: 18px; border: 1px solid #bbf7d0; padding: 20px; margin-bottom: 25px; box-shadow: 0 4px 15px rgba(22, 101, 52, 0.08);">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: #dcfce7; color: #15803d; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div>
                        <strong style="color: #15803d; font-size: 14px;">{{ $sp['message'] }}</strong>
                        @if (isset($sp['temp_password']))
                            <div style="margin-top: 6px; font-size: 13px; color: #334155;">
                                Password Sementara: <strong style="font-family: monospace; background: #f1f5f9; padding: 3px 8px; border-radius: 6px; font-size: 15px; border: 1px solid #cbd5e1; color: #0f172a;">{{ $sp['temp_password'] }}</strong>
                            </div>
                        @endif
                    </div>
                </div>

                @if (!empty($sp['wa_url']))
                    <a href="{{ $sp['wa_url'] }}" target="_blank"
                       style="background: #25D366; color: white; padding: 10px 18px; border-radius: 12px; text-decoration: none; font-weight: 600; font-size: 12px; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(37, 211, 102, 0.25);">
                        <i class="fa-brands fa-whatsapp" style="font-size: 16px;"></i> Kirim Kredensial via WhatsApp ke {{ $sp['nama'] }}
                    </a>
                @endif
            </div>
        </div>
    @endif

    <!-- STATS CARDS GRID -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 18px; margin-bottom: 30px;">
        
        <div style="background: white; border-radius: 16px; padding: 18px 22px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 16px; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
            <div style="width: 46px; height: 46px; border-radius: 12px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <span style="display: block; font-size: 11px; color: #64748b; font-weight: 500;">Total Staf RS</span>
                <span style="font-size: 22px; font-weight: 700; color: #6e92e6ff;">{{ $stats['total_pegawai'] }}</span>
            </div>
        </div>

        <div style="background: white; border-radius: 16px; padding: 18px 22px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 16px; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
            <div style="width: 46px; height: 46px; border-radius: 12px; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div>
                <span style="display: block; font-size: 11px; color: #64748b; font-weight: 500;">Staf Proteksi 2FA</span>
                <span style="font-size: 22px; font-weight: 700; color: #16a34a;">{{ $stats['total_2fa'] }}</span>
            </div>
        </div>

        <div style="background: white; border-radius: 16px; padding: 18px 22px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 16px; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
            <div style="width: 46px; height: 46px; border-radius: 12px; background: #ede9fe; color: #7c3aed; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-user-doctor"></i>
            </div>
            <div>
                <span style="display: block; font-size: 11px; color: #64748b; font-weight: 500;">Dokter Spesialis</span>
                <span style="font-size: 22px; font-weight: 700; color: #7c3aed;">{{ $stats['total_dokter'] }}</span>
            </div>
        </div>

        <div style="background: white; border-radius: 16px; padding: 18px 22px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 16px; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
            <div style="width: 46px; height: 46px; border-radius: 12px; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-hospital-user"></i>
            </div>
            <div>
                <span style="display: block; font-size: 11px; color: #64748b; font-weight: 500;">Pasien Terdaftar</span>
                <span style="font-size: 22px; font-weight: 700; color: #d97706;">{{ $stats['total_pasien'] }}</span>
            </div>
        </div>

        <a href="{{ route('admin.reset') }}" style="text-decoration: none; color: inherit; display: block; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'" title="Buka Menu Khusus Permintaan Reset Password & 2FA">
            <div style="background: white; border-radius: 16px; padding: 18px 22px; border: 1.5px solid {{ $pendingResetCount > 0 ? '#fca5a5' : '#e2e8f0' }}; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 6px rgba(0,0,0,0.02); {{ $pendingResetCount > 0 ? 'background: #fff5f5;' : '' }}">
                <div style="display: flex; align-items: center; gap: 16px;">
                    <div style="width: 46px; height: 46px; border-radius: 12px; background: {{ $pendingResetCount > 0 ? '#fee2e2' : '#f1f5f9' }}; color: {{ $pendingResetCount > 0 ? '#dc2626' : '#64748b' }}; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fa-solid fa-key"></i>
                    </div>
                    <div>
                        <span style="display: block; font-size: 11px; color: #64748b; font-weight: 500;">Permintaan Reset</span>
                        <span style="font-size: 22px; font-weight: 700; color: {{ $pendingResetCount > 0 ? '#dc2626' : '#334155' }};">
                            {{ $pendingResetCount }}
                            @if($pendingResetCount > 0)
                                <span style="font-size: 10px; font-weight: 700; background: #dc2626; color: white; padding: 2px 7px; border-radius: 999px; vertical-align: middle; margin-left: 4px;">Menunggu</span>
                            @endif
                        </span>
                    </div>
                </div>
                <div style="color: #94a3b8; font-size: 13px;">
                    <i class="fa-solid fa-chevron-right"></i>
                </div>
            </div>
        </a>

    </div>

    <!-- FORM TAMBAH STAF BARU (COLLAPSIBLE / TOGGLE AKTIF) -->
    <div style="background: white; border-radius: 20px; padding: 24px; border: 1px solid #e2e8f0; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);">
        <div style="display: flex; justify-content: space-between; align-items: center; cursor: pointer;" onclick="toggleFormTambah()">
            <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 10px; margin: 0;">
                <i class="fa-solid fa-user-plus" style="color: #1a4a8d;"></i> Daftarkan Akun Staf Rumah Sakit Baru
            </h3>
            <button type="button" id="btn-toggle-form" class="simrs-btn-toggle-form">
                <i class="fa-solid fa-chevron-up" id="icon-toggle-form"></i>
                <span id="text-toggle-form">Tutup Form</span>
            </button>
        </div>

        <form id="form-tambah-pegawai" action="{{ route('admin.pengguna.store') }}" method="POST" style="padding-top: 20px; border-top: 1px solid #f1f5f9; margin-top: 16px; display: block;">
            @csrf
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 18px;">
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 5px;">NIK Pegawai (Login) *</label>
                    <input type="text" name="nik" value="{{ old('nik') }}" placeholder="Contoh: 6301010008" required
                           style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 13px;">
                </div>

                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 5px;">
                        NIP Pegawai
                        <span style="font-weight: 400; color: #94a3b8; font-size: 11px;">(Opsional)</span>
                    </label>
                    <input type="text" name="nip" value="{{ old('nip') }}" placeholder="Contoh: 19920415 201801 1 003"
                           style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 13px;">
                </div>

                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 5px;">Nama Lengkap Staf *</label>
                    <input type="text" name="nama" value="{{ old('nama') }}" placeholder="Contoh: dr. Sarah Nur, Sp.A" required
                           style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 13px;">
                </div>

                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 5px;">Role / Posisi Kerja *</label>
                    <select name="role" id="select-role-pegawai" required onchange="toggleDokterInputs()" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 13px; background: white;">
                        <option value="" disabled {{ old('role') ? '' : 'selected' }}>-- Pilih Posisi / Role --</option>
                        <option value="dokter" {{ old('role') === 'dokter' ? 'selected' : '' }}>Dokter</option>
                        <option value="perawat" {{ old('role') === 'perawat' ? 'selected' : '' }}>Perawat</option>
                        <option value="kasir" {{ old('role') === 'kasir' ? 'selected' : '' }}>Kasir (Loket Biasa)</option>
                        <option value="admin_kasir" {{ old('role') === 'admin_kasir' ? 'selected' : '' }}>Kasir Admin (Kasir Utama / Supervisor)</option>
                        <option value="loket" {{ old('role') === 'loket' ? 'selected' : '' }}>Petugas Loket Pendaftaran (Admisi)</option>
                        <option value="apoteker" {{ old('role') === 'apoteker' ? 'selected' : '' }}>Apoteker / Farmasi</option>
                        <option value="resepsionis" {{ old('role') === 'resepsionis' ? 'selected' : '' }}>Resepsionis (Customer Service & Monitoring Antrean)</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Super Admin RS</option>
                    </select>
                </div>

                <div id="wrapper-dokter-poli" style="display: {{ old('role') === 'dokter' ? 'block' : 'none' }};">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #2563eb; margin-bottom: 5px;">
                        <i class="fa-solid fa-hospital"></i> Poliklinik Bertugas (Dokter)
                    </label>
                    <select name="id_poli" id="input-poli-dokter" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid #93c5fd; font-size: 13px; background: #eff6ff;">
                        <option value="">-- Pilih Poliklinik Dokter --</option>
                        @foreach ($polis as $poli)
                            <option value="{{ $poli->id_poli }}" {{ old('id_poli') == $poli->id_poli ? 'selected' : '' }}>
                                {{ $poli->nama_poli }} ({{ $poli->kode_poli }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="wrapper-dokter-spesialisasi" style="display: {{ old('role') === 'dokter' ? 'block' : 'none' }};">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #2563eb; margin-bottom: 5px;">
                        <i class="fa-solid fa-user-doctor"></i> Bidang Spesialisasi
                    </label>
                    <input type="text" name="spesialisasi" id="input-spesialisasi-dokter" value="{{ old('spesialisasi') }}" placeholder="Contoh: Spesialis Anak / Dokter Umum"
                           style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid #93c5fd; font-size: 13px; background: #eff6ff;">
                </div>

                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 5px;">Jenis Kelamin *</label>
                    <select name="jenis_kelamin" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 13px; background: white;">
                        <option value="" disabled {{ old('jenis_kelamin') ? '' : 'selected' }}>-- Pilih Jenis Kelamin --</option>
                        <option value="L" {{ old('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 5px;">Nomor WhatsApp Staf</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp') }}" placeholder="Contoh: 081234567890"
                           style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 13px;">
                </div>

                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 5px;">Password Awal *</label>
                    <input type="text" name="password" placeholder="Contoh: Delta#123" required
                           style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 13px; font-family: monospace;">
                </div>
            </div>

            <button type="submit" style="background: #1a4a8d; color: white; padding: 11px 22px; border-radius: 12px; border: none; font-weight: 600; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-plus"></i> Simpan & Buat Akun Staf
            </button>
        </form>
    </div>

    <!-- TABEL 1: DAFTAR STAF RUMAH SAKIT -->
    <div style="background: white; border-radius: 20px; padding: 24px; border: 1px solid #e2e8f0; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;">
            <div>
                <h3 style="font-size: 16px; font-weight: 700; color: #1e293b;">Daftar Akun Staf Rumah Sakit</h3>
                <p style="font-size: 12px; color: #64748b;">Administrator IT dapat me-reset password atau me-reset 2FA jika staf lupa/kehilangan akses.</p>
            </div>
            <span class="simrs-badge-count-blue">
                {{ $pegawais->total() }} Staf Terdaftar
            </span>
        </div>

        <!-- SEARCH BAR & FILTER ROLE PEGAWAI -->
        <form method="GET" action="{{ route('admin.pengguna') }}" style="display: flex; gap: 10px; margin-bottom: 18px; flex-wrap: wrap;">
            <input type="hidden" name="q_pasien" value="{{ $qPasien }}">
            <div style="flex: 1; min-width: 220px; position: relative;">
                <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 11px; color: #94a3b8; font-size: 13px;"></i>
                <input type="text" name="q_pegawai" value="{{ $qPegawai }}" placeholder="Cari NIK, Nama Staf, Role, No. WhatsApp..."
                       class="simrs-search-input">
            </div>
            <select name="role_pegawai" class="simrs-search-select">
                <option value="">Semua Role</option>
                <option value="dokter" {{ $rolePegawai === 'dokter' ? 'selected' : '' }}>Dokter</option>
                <option value="perawat" {{ $rolePegawai === 'perawat' ? 'selected' : '' }}>Perawat</option>
                <option value="kasir" {{ $rolePegawai === 'kasir' ? 'selected' : '' }}>Kasir (Loket Biasa)</option>
                <option value="admin_kasir" {{ $rolePegawai === 'admin_kasir' ? 'selected' : '' }}>Kasir Admin (Kasir Utama)</option>
                <option value="apoteker" {{ $rolePegawai === 'apoteker' ? 'selected' : '' }}>Apoteker</option>
                <option value="loket" {{ $rolePegawai === 'loket' ? 'selected' : '' }}>Petugas Loket</option>
                <option value="resepsionis" {{ $rolePegawai === 'resepsionis' ? 'selected' : '' }}>Resepsionis</option>
                <option value="admin" {{ $rolePegawai === 'admin' ? 'selected' : '' }}>Super Admin RS</option>
            </select>
            <button type="submit" style="background: #1C4D8D; color: white; border: none; padding: 8px 16px; border-radius: 10px; font-size: 12px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-magnifying-glass"></i> Cari
            </button>
            @if ($qPegawai !== '' || $rolePegawai !== '')
                <a href="{{ route('admin.pengguna', ['q_pasien' => $qPasien]) }}" class="simrs-search-btn-reset">
                    <i class="fa-solid fa-rotate-left"></i> Reset
                </a>
            @endif
        </form>

        <div style="overflow-x: auto;">
            <table class="simrs-data-table">
                <thead>
                    <tr>
                        <th style="padding: 12px 14px;">NIK Login</th>
                        <th style="padding: 12px 14px;">Nama Staf</th>
                        <th style="padding: 12px 14px;">Role</th>
                        <th style="padding: 12px 14px;">No. WhatsApp</th>
                        <th style="padding: 12px 14px;">Status 2FA</th>
                        <th style="padding: 12px 14px; text-align: right;">Aksi Keamanan IT</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pegawais as $p)
                        <tr class="simrs-table-row">
                            <td class="cell-nik" style="padding: 14px;">
                                <div style="font-weight: 600; color: #1e293b;">{{ $p->nik }}</div>
                                <div style="font-size: 11px; color: #64748b;">NIP: {{ !empty($p->nip) ? $p->nip : '-' }}</div>
                            </td>
                            <td class="cell-nama" style="padding: 14px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <img src="{{ $p->foto_url }}" alt="{{ $p->nama }}" style="width: 34px; height: 34px; border-radius: 50%; object-fit: cover; border: 1.5px solid #cbd5e1;">
                                    <div>
                                        <span class="nama-text">{{ $p->nama }}</span>
                                        @if ($p->id_pegawai === auth()->id())
                                            <span style="font-size: 10px; background: #e0f2fe; color: #0284c7; padding: 2px 6px; border-radius: 4px; margin-left: 4px;">Anda</span>
                                        @endif
                                        @if ($p->role === 'dokter')
                                            <div style="font-size: 11px; color: #2563eb; margin-top: 3px; font-weight: 500; display: flex; align-items: center; gap: 5px;">
                                                <i class="fa-solid fa-stethoscope" style="font-size: 10px;"></i>
                                                <span>{{ $p->poli->nama_poli ?? 'Rotasi Poliklinik' }}</span>
                                                @if (!empty($p->spesialisasi))
                                                    <span style="color: #64748b;">({{ $p->spesialisasi }})</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 14px;">
                                @php
                                    $roleClean = strtolower(trim($p->role));
                                    if (in_array($roleClean, ['admin', 'administrator'])) {
                                        $roleClass = 'admin';
                                        $roleText = 'SUPER ADMIN';
                                    } elseif (in_array($roleClean, ['admin_kasir', 'kasir_utama', 'admin kasir'])) {
                                        $roleClass = 'kasir-admin';
                                        $roleText = 'KASIR ADMIN';
                                    } else {
                                        $roleClass = $roleClean;
                                        $roleText = strtoupper($roleClean);
                                    }
                                @endphp
                                <span class="badge-role badge-role-{{ $roleClass }}">
                                    {{ $roleText }}
                                </span>
                            </td>
                            <td class="cell-wa" style="padding: 14px;">
                                @if (!empty($p->no_hp))
                                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $p->no_hp)) }}" target="_blank"
                                       style="color: #16a34a; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 5px;">
                                        <i class="fa-brands fa-whatsapp"></i> {{ $p->no_hp }}
                                    </a>
                                @else
                                    <span style="color: #94a3b8; font-style: italic;">Belum diisi</span>
                                @endif
                            </td>
                            <td style="padding: 14px;">
                                @if ($p->two_factor_enabled)
                                    <span style="display: inline-flex; align-items: center; gap: 4px; color: #16a34a; font-weight: 600; font-size: 11px; background: #dcfce7; padding: 3px 8px; border-radius: 10px;">
                                        <i class="fa-solid fa-circle-check"></i> 2FA Aktif
                                    </span>
                                @else
                                    <span style="color: #94a3b8; font-size: 11px;">
                                        <i class="fa-solid fa-circle-xmark"></i> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 14px; text-align: right;">
                                <div class="dropdown-action-wrapper">
                                    <button type="button" class="btn-gear-action" onclick="toggleDropdownAction('action-menu-{{ $p->id_pegawai }}', event)" title="Pengaturan & Aksi Staf">
                                        <i class="fa-solid fa-gear"></i>
                                        <span>Aksi</span>
                                        <i class="fa-solid fa-chevron-down" style="font-size: 10px; opacity: 0.6; margin-left: 1px;"></i>
                                    </button>

                                    <div id="action-menu-{{ $p->id_pegawai }}" class="dropdown-action-menu" style="display: none;">
                                        <div class="dropdown-header-title">
                                            <i class="fa-solid fa-shield-halved"></i> Aksi Keamanan IT
                                        </div>

                                        <!-- OPSI 1: ATUR POLI (KHUSUS DOKTER) -->
                                        @if ($p->role === 'dokter')
                                            <button type="button" class="dropdown-item-action"
                                                    onclick="bukaModalAturPoli('{{ $p->id_pegawai }}', '{{ addslashes($p->nama) }}', '{{ $p->id_poli ?? '' }}', '{{ addslashes($p->spesialisasi ?? '') }}'); tutupSemuaDropdownAction();">
                                                <i class="fa-solid fa-hospital-user" style="color: #2563eb;"></i>
                                                <span>Atur Poli & Spesialisasi</span>
                                            </button>
                                        @endif

                                        <!-- OPSI 2: RESET PASSWORD -->
                                        <form action="{{ route('admin.pengguna.reset-password', $p->id_pegawai) }}" method="POST"
                                              onsubmit="return confirm('Apakah Anda yakin ingin me-reset password staf {{ $p->nama }}? Password baru sementara akan di-generate otomatis.');">
                                            @csrf
                                            <button type="submit" class="dropdown-item-action">
                                                <i class="fa-solid fa-key" style="color: #f59e0b;"></i>
                                                <span>Reset Password</span>
                                            </button>
                                        </form>

                                        <!-- OPSI 3: RESET 2FA (JIKA AKTIF) -->
                                        @if ($p->two_factor_enabled)
                                            <form action="{{ route('admin.pengguna.reset-2fa', $p->id_pegawai) }}" method="POST"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan 2FA untuk {{ $p->nama }}? Gunakan ini jika staf kehilangan HP/kode.');">
                                                @csrf
                                                <button type="submit" class="dropdown-item-action dropdown-item-danger">
                                                    <i class="fa-solid fa-shield-xmark" style="color: #ef4444;"></i>
                                                    <span>Reset 2FA</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 24px; color: #94a3b8;">
                                <i class="fa-solid fa-user-slash" style="font-size: 24px; margin-bottom: 8px; display: block;"></i>
                                Tidak ditemukan data staf yang sesuai dengan kata kunci pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINASI STAF (1-10 PER HALAMAN) -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 18px; flex-wrap: wrap; gap: 10px;">
            <div style="font-size: 12px; color: #64748b;">
                Menampilkan <strong>{{ $pegawais->firstItem() ?? 0 }}</strong> - <strong>{{ $pegawais->lastItem() ?? 0 }}</strong> dari <strong>{{ $pegawais->total() }}</strong> akun staf
            </div>
            
            @if ($pegawais->hasPages())
                <div style="display: flex; align-items: center; gap: 6px;">
                    @if ($pegawais->onFirstPage())
                        <span class="simrs-page-disabled">
                            <i class="fa-solid fa-chevron-left"></i>
                        </span>
                    @else
                        <a href="{{ $pegawais->previousPageUrl() }}" class="simrs-page-btn">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>
                    @endif

                    @for ($i = 1; $i <= $pegawais->lastPage(); $i++)
                        @if ($i == $pegawais->currentPage())
                            <span class="simrs-page-active">{{ $i }}</span>
                        @else
                            <a href="{{ $pegawais->url($i) }}" class="simrs-page-btn">{{ $i }}</a>
                        @endif
                    @endfor

                    @if ($pegawais->hasMorePages())
                        <a href="{{ $pegawais->nextPageUrl() }}" class="simrs-page-btn">
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    @else
                        <span class="simrs-page-disabled">
                            <i class="fa-solid fa-chevron-right"></i>
                        </span>
                    @endif
                </div>
            @endif
        </div>

    </div>

    <!-- TABEL 2: DAFTAR PASIEN TERDAFTAR -->
    <div style="background: white; border-radius: 20px; padding: 24px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;">
            <div>
                <h3 style="font-size: 16px; font-weight: 700; color: #1e293b;">Daftar Akun Pasien Terdaftar</h3>
                <p style="font-size: 12px; color: #64748b;">Pemantauan akun pasien mandiri yang mendaftar secara online melalui web/aplikasi.</p>
            </div>
            <span class="simrs-badge-count-amber">
                {{ $pasiens->total() }} Pasien Terdaftar
            </span>
        </div>

        <!-- SEARCH BAR PASIEN -->
        <form method="GET" action="{{ route('admin.pengguna') }}" style="display: flex; gap: 10px; margin-bottom: 18px; flex-wrap: wrap;">
            <input type="hidden" name="q_pegawai" value="{{ $qPegawai }}">
            <input type="hidden" name="role_pegawai" value="{{ $rolePegawai }}">
            <div style="flex: 1; min-width: 220px; position: relative;">
                <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 11px; color: #94a3b8; font-size: 13px;"></i>
                <input type="text" name="q_pasien" value="{{ $qPasien }}" placeholder="Cari No. RM (contoh: RM-0001), NIK KTP, Username, Nama Pasien..."
                       class="simrs-search-input">
            </div>
            <button type="submit" style="background: #1C4D8D; color: white; border: none; padding: 8px 16px; border-radius: 10px; font-size: 12px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-magnifying-glass"></i> Cari
            </button>
            @if ($qPasien !== '')
                <a href="{{ route('admin.pengguna', ['q_pegawai' => $qPegawai, 'role_pegawai' => $rolePegawai]) }}" class="simrs-search-btn-reset">
                    <i class="fa-solid fa-rotate-left"></i> Reset
                </a>
            @endif
        </form>

        <div style="overflow-x: auto;">
            <table class="simrs-data-table">
                <thead>
                    <tr>
                        <th style="padding: 12px 14px;">No. RM</th>
                        <th style="padding: 12px 14px;">NIK KTP</th>
                        <th style="padding: 12px 14px;">Username</th>
                        <th style="padding: 12px 14px;">Nama Pasien</th>
                        <th style="padding: 12px 14px;">Status Akun Portal</th>
                        <th style="padding: 12px 14px; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pasiens as $pas)
                        <tr class="simrs-table-row">
                            <td class="cell-rm" style="padding: 12px 14px;">
                                {{ $pas->rm }}
                            </td>
                            <td class="cell-nik" style="padding: 12px 14px;">
                                {{ $pas->nik }}
                            </td>
                            <td class="cell-user" style="padding: 12px 14px; font-weight: 500;">
                                {{ $pas->username ?? '-' }}
                            </td>
                            <td class="cell-nama" style="padding: 12px 14px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <img src="{{ $pas->foto_url }}" alt="{{ $pas->nama_pasien }}" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 1.5px solid #cbd5e1;">
                                    <span class="nama-text">{{ $pas->nama_pasien }}</span>
                                </div>
                            </td>
                            <td style="padding: 12px 14px;">
                                @if (!empty($pas->password_hash))
                                    <span style="display: inline-flex; align-items: center; gap: 4px; color: #16a34a; font-weight: 600; font-size: 11px; background: #dcfce7; padding: 3px 8px; border-radius: 10px;">
                                        <i class="fa-solid fa-circle-check"></i> Akun Aktif
                                    </span>
                                @else
                                    <span style="display: inline-flex; align-items: center; gap: 4px; color: #64748b; font-weight: 500; font-size: 11px; background: #f1f5f9; padding: 3px 8px; border-radius: 10px;" title="Pasien tercatat dari loket fisik (belum aktivasi akun portal)">
                                        <i class="fa-solid fa-hospital-user"></i> Walk-In (Belum Aktivasi)
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 12px 14px; text-align: right;">
                                <a href="{{ route('admin.pasien.kartu', $pas->id_pasien) }}" target="_blank"
                                   class="simrs-btn-white" style="border: 1px solid #bae6fd; background: #f0f9ff; color: #0284c7; padding: 6px 10px; border-radius: 8px; font-size: 11.5px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; margin-right: 6px;" title="Cetak Kartu Berobat (CR80 Barcode)">
                                    <i class="fa-solid fa-id-card"></i> Kartu
                                </a>

                                <form action="{{ route('admin.pasien.reset-password', $pas->id_pasien) }}" method="POST"
                                      onsubmit="return confirm('{{ !empty($pas->password_hash) ? 'Reset password akun pasien ' . $pas->nama_pasien . '?' : 'Buatkan akun dan password sementara untuk pasien ' . $pas->nama_pasien . '?' }}');" style="display: inline;">
                                    @csrf
                                    @if (!empty($pas->password_hash))
                                        <button type="submit" class="simrs-btn-reset-pw">
                                            <i class="fa-solid fa-key" style="color: #f59e0b;"></i> Reset Password
                                        </button>
                                    @else
                                        <button type="submit" class="simrs-btn-reset-pw" style="border-color: #bae6fd; background: #f0f9ff; color: #0284c7;" title="Buatkan username dan password sementara untuk pasien loket fisik ini">
                                            <i class="fa-solid fa-user-plus" style="color: #0284c7;"></i> Aktivasi Akun
                                        </button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 24px; color: #94a3b8;">
                                <i class="fa-solid fa-hospital-user" style="font-size: 24px; margin-bottom: 8px; display: block;"></i>
                                Tidak ditemukan data pasien yang sesuai dengan kata kunci pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINASI PASIEN (1-10 PER HALAMAN) -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 18px; flex-wrap: wrap; gap: 10px;">
            <div style="font-size: 12px; color: #64748b;">
                Menampilkan <strong>{{ $pasiens->firstItem() ?? 0 }}</strong> - <strong>{{ $pasiens->lastItem() ?? 0 }}</strong> dari <strong>{{ $pasiens->total() }}</strong> akun pasien
            </div>
            
            @if ($pasiens->hasPages())
                <div style="display: flex; align-items: center; gap: 6px;">
                    @if ($pasiens->onFirstPage())
                        <span class="simrs-page-disabled">
                            <i class="fa-solid fa-chevron-left"></i>
                        </span>
                    @else
                        <a href="{{ $pasiens->previousPageUrl() }}" class="simrs-page-btn">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>
                    @endif

                    @for ($i = 1; $i <= $pasiens->lastPage(); $i++)
                        @if ($i == $pasiens->currentPage())
                            <span class="simrs-page-active">{{ $i }}</span>
                        @else
                            <a href="{{ $pasiens->url($i) }}" class="simrs-page-btn">{{ $i }}</a>
                        @endif
                    @endfor

                    @if ($pasiens->hasMorePages())
                        <a href="{{ $pasiens->nextPageUrl() }}" class="simrs-page-btn">
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    @else
                        <span class="simrs-page-disabled">
                            <i class="fa-solid fa-chevron-right"></i>
                        </span>
                    @endif
                </div>
            @endif
        </div>

    </div>

</div>

<!-- MODAL ATUR PENEMPATAN POLIKLINIK DOKTER -->
<div id="modalAturPoliDokter" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 9999;">
    <div class="modal-box-card" style="background: white; border-radius: 18px; width: 90%; max-width: 480px; padding: 24px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-hospital-user" style="color: #2563eb;"></i> Atur Penempatan Poliklinik Dokter
            </h3>
            <button type="button" onclick="tutupModalAturPoli()" style="background: none; border: none; font-size: 18px; color: #94a3b8; cursor: pointer;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="formAturPoliDokter" method="POST" action="">
            @csrf
            <div class="info-preview-box" style="background: #f8fafc; border-radius: 12px; padding: 12px 14px; margin-bottom: 16px; border: 1px solid #e2e8f0;">
                <div style="font-size: 11px; color: #64748b; font-weight: 500;">Nama Dokter:</div>
                <div id="modal-nama-dokter" style="font-size: 14px; font-weight: 700; color: #1e293b; margin-top: 2px;"></div>
            </div>

            <div style="margin-bottom: 14px;">
                <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 6px;">
                    Penempatan Poliklinik *
                </label>
                <select name="id_poli" id="modal-select-poli" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 13px; background: white;">
                    <option value="">-- Rotasi Antar-Poliklinik / Belum Ditentukan --</option>
                    @foreach ($polis as $poli)
                        <option value="{{ $poli->id_poli }}">
                            {{ $poli->nama_poli }} ({{ $poli->kode_poli }})
                        </option>
                    @endforeach
                </select>
                <p style="font-size: 11px; color: #64748b; margin: 5px 0 0;">
                    Pasien yang mendaftar ke poli ini akan dapat memilih dokter ini di loket & portal online.
                </p>
            </div>

            <div style="margin-bottom: 22px;">
                <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 6px;">
                    Bidang Spesialisasi / Gelar Medis
                </label>
                <input type="text" name="spesialisasi" id="modal-input-spesialisasi" placeholder="Contoh: Spesialis Konservasi Gigi / Spesialis Anak"
                       style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 13px;">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="tutupModalAturPoli()" style="padding: 9px 16px; border-radius: 10px; border: 1px solid #cbd5e1; background: #fff; font-size: 12.5px; font-weight: 600; color: #64748b; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="padding: 9px 20px; border-radius: 10px; border: none; background: #2563eb; font-size: 12.5px; font-weight: 600; color: white; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Penempatan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleDropdownAction(menuId, event) {
        if (event) event.stopPropagation();
        const menu = document.getElementById(menuId);
        if (!menu) return;

        const isCurrentlyOpen = menu.style.display === 'block';
        tutupSemuaDropdownAction();

        if (!isCurrentlyOpen) {
            menu.style.display = 'block';
            // Hindari terpotong di tepi bawah viewport
            const rect = menu.getBoundingClientRect();
            if (rect.bottom > window.innerHeight) {
                menu.style.top = 'auto';
                menu.style.bottom = 'calc(100% + 4px)';
            } else {
                menu.style.top = 'calc(100% + 6px)';
                menu.style.bottom = 'auto';
            }
        }
    }

    function tutupSemuaDropdownAction() {
        document.querySelectorAll('.dropdown-action-menu').forEach(m => {
            m.style.display = 'none';
        });
    }

    // Tutup dropdown saat klik di luar
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown-action-wrapper')) {
            tutupSemuaDropdownAction();
        }
    });

    function bukaModalAturPoli(idPegawai, namaDokter, idPoli, spesialisasi) {
        const modal = document.getElementById('modalAturPoliDokter');
        const form = document.getElementById('formAturPoliDokter');
        const elNama = document.getElementById('modal-nama-dokter');
        const elPoli = document.getElementById('modal-select-poli');
        const elSpesialisasi = document.getElementById('modal-input-spesialisasi');

        if (!modal || !form) return;

        form.action = `/admin/pengguna/${idPegawai}/atur-poli`;
        elNama.textContent = namaDokter;
        elPoli.value = idPoli || '';
        elSpesialisasi.value = spesialisasi || '';

        modal.style.display = 'flex';
    }

    function tutupModalAturPoli() {
        const modal = document.getElementById('modalAturPoliDokter');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    // Tutup jika klik latar belakang gelap
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('modalAturPoliDokter');
        if (modal && e.target === modal) {
            tutupModalAturPoli();
        }
    });

    function toggleDokterInputs() {
        const selectRole = document.getElementById('select-role-pegawai');
        const wrapPoli = document.getElementById('wrapper-dokter-poli');
        const wrapSpesialisasi = document.getElementById('wrapper-dokter-spesialisasi');
        if (!selectRole || !wrapPoli || !wrapSpesialisasi) return;

        const isDokter = selectRole.value === 'dokter';
        wrapPoli.style.display = isDokter ? 'block' : 'none';
        wrapSpesialisasi.style.display = isDokter ? 'block' : 'none';
    }

    function toggleFormTambah() {
        const form = document.getElementById('form-tambah-pegawai');
        const icon = document.getElementById('icon-toggle-form');
        const label = document.getElementById('text-toggle-form');
        if (!form) return;

        if (form.style.display === 'none') {
            form.style.display = 'block';
            if (icon) icon.className = 'fa-solid fa-chevron-up';
            if (label) label.textContent = 'Tutup Form';
        } else {
            form.style.display = 'none';
            if (icon) icon.className = 'fa-solid fa-chevron-down';
            if (label) label.textContent = 'Buka Form';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleDokterInputs();
    });
</script>
@endsection
