@extends('layouts.app')
@php $halamanAktif = 'admin_backup'; @endphp
@section('title', 'Backup Database Sistem - Super Admin RS')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/kelola.css') }}">
    <style>
        .backup-grid-header {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        .backup-stat-card {
            background: white;
            border-radius: 16px;
            padding: 18px 20px;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        }
        .backup-action-box {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-radius: 18px;
            padding: 24px;
            color: white;
            margin-bottom: 26px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            border-left: 5px solid #0284c7;
        }

        /* Dark Mode */
        body.dark-mode .backup-stat-card {
            background: #1e293b;
            border-color: #334155;
            color: #f1f5f9;
        }
    </style>
@endpush

@section('content')
<div class="kelola-container" style="max-width: 1200px; margin: 0 auto; padding: 24px 20px;">

    <!-- TOP TITLE -->
    <div style="margin-bottom: 22px;">
        <h1 style="font-size: 22px; font-weight: 800; color: #1e293b; margin: 0 0 4px 0; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-database" style="color: #0284c7;"></i>
            Pusat Cadangan & Backup Database RS Delta
        </h1>
        <p style="font-size: 13px; color: #64748b; margin: 0;">
            Amankan seluruh data klinis, rekam medis, transaksi keuangan, dan konfigurasi master sistem dengan backup SQL terenkripsi.
        </p>
    </div>

    <!-- NOTIFIKASI HASIL BACKUP -->
    @if (session('status_backup'))
        <div style="padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; font-size: 13.5px; font-weight: 500; display: flex; align-items: center; gap: 10px;
            background: {{ session('status_backup.type') === 'success' ? '#dcfce7' : '#fee2e2' }};
            color: {{ session('status_backup.type') === 'success' ? '#15803d' : '#b91c1c' }};
            border: 1px solid {{ session('status_backup.type') === 'success' ? '#bbf7d0' : '#fecaca' }};">
            <i class="fa-solid {{ session('status_backup.type') === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' }}" style="font-size: 18px;"></i>
            <div>{{ session('status_backup.message') }}</div>
        </div>
    @endif

    <!-- QUICK STATS -->
    <div class="backup-grid-header">
        <div class="backup-stat-card">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-server"></i>
            </div>
            <div>
                <span style="font-size: 11px; color: #64748b; font-weight: 500; display: block;">Database Aktif</span>
                <span style="font-size: 18px; font-weight: 700; color: #6e92e6ff;">{{ strtoupper($dbName) }}</span>
            </div>
        </div>

        <div class="backup-stat-card">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-file-code"></i>
            </div>
            <div>
                <span style="font-size: 11px; color: #64748b; font-weight: 500; display: block;">File Backup Tersimpan</span>
                <span style="font-size: 18px; font-weight: 700; color: #16a34a;">{{ count($backups) }} File</span>
            </div>
        </div>

        <div class="backup-stat-card">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div>
                <span style="font-size: 11px; color: #64748b; font-weight: 500; display: block;">Format Cadangan</span>
                <span style="font-size: 18px; font-weight: 700; color: #d97706;">SQL Universal</span>
            </div>
        </div>
    </div>

    <!-- ACTION HERO BANNER -->
    <div class="backup-action-box">
        <div>
            <h3 style="font-size: 16px; font-weight: 700; margin: 0 0 6px 0; color: #38bdf8;">
                <i class="fa-solid fa-cloud-arrow-down"></i> Buat Salinan Data Sekarang
            </h3>
            <p style="font-size: 12.5px; color: #94a3b8; margin: 0; max-width: 600px;">
                Sistem akan membungkus seluruh tabel pendaftaran, rekam medis, e-resep, obat, tarif, dan staf pegawai menjadi satu berkas cadangan SQL murni.
            </p>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <!-- Tombol 1: Simpan di Server -->
            <form action="{{ route('admin.backup.generate') }}" method="POST">
                @csrf
                <button type="submit" class="btn-tambah" style="background: #0284c7; color: white; border: none; padding: 10px 18px;">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Backup di Server
                </button>
            </form>

            <!-- Tombol 2: Unduh Langsung ke Komputer -->
            <a href="{{ route('admin.backup.download-instant') }}" class="btn-tambah" style="background: #10b981; color: white; border: none; padding: 10px 18px; text-decoration: none;">
                <i class="fa-solid fa-download"></i> Unduh Langsung (.SQL)
            </a>
        </div>
    </div>

    <!-- DAFTAR FILE BACKUP DI SERVER -->
    <div style="background: white; border-radius: 20px; padding: 24px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
            <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 0;">
                Riwayat Berkas Backup di Server
            </h3>
            <span style="font-size: 12px; color: #64748b;">
                Lokasi: <code>storage/app/backups/</code>
            </span>
        </div>

        <div style="overflow-x: auto;">
            <table class="simrs-data-table">
                <thead>
                    <tr>
                        <th style="padding: 12px 14px;">Nama Berkas Backup</th>
                        <th style="padding: 12px 14px;">Waktu Pembuatan</th>
                        <th style="padding: 12px 14px;">Ukuran File</th>
                        <th style="padding: 12px 14px; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($backups as $b)
                        <tr class="simrs-table-row">
                            <td style="padding: 12px 14px; font-weight: 600; color: #1e293b; font-family: monospace;">
                                <i class="fa-solid fa-file-code" style="color: #0284c7; margin-right: 6px;"></i>
                                {{ $b['filename'] }}
                            </td>
                            <td style="padding: 12px 14px; color: #475569; font-size: 12.5px;">
                                {{ $b['created_at']->translatedFormat('d M Y, H:i:s') }}
                                <span style="font-size: 11px; color: #94a3b8;">({{ $b['created_at']->diffForHumans() }})</span>
                            </td>
                            <td style="padding: 12px 14px;">
                                <span style="display: inline-block; background: #f1f5f9; color: #334155; font-size: 11.5px; font-weight: 600; padding: 2px 8px; border-radius: 6px;">
                                    {{ $b['size'] }}
                                </span>
                            </td>
                            <td style="padding: 12px 14px; text-align: right;">
                                <div style="display: inline-flex; gap: 8px; align-items: center;">
                                    <a href="{{ route('admin.backup.download-file', $b['filename']) }}" 
                                       class="btn-action-edit" style="color: #0284c7; background: #e0f2fe; padding: 6px 12px; border-radius: 8px; text-decoration: none; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px;">
                                        <i class="fa-solid fa-download"></i> Unduh
                                    </a>

                                    <form action="{{ route('admin.backup.delete-file', $b['filename']) }}" method="POST"
                                          onsubmit="return confirm('Hapus berkas backup {{ $b['filename'] }}?');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background: #fee2e2; color: #ef4444; border: none; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 5px;">
                                            <i class="fa-solid fa-trash-can"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 32px; color: #94a3b8;">
                                <i class="fa-solid fa-database" style="font-size: 32px; margin-bottom: 10px; display: block; opacity: 0.5;"></i>
                                Belum ada berkas backup yang tersimpan di server. Klik tombol di atas untuk membuat cadangan baru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
