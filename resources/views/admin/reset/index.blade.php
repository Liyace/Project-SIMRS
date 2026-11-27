@extends('layouts.app')

@section('title', 'Permintaan Reset Password & 2FA - RS Delta')

@section('content')
<div style="padding-bottom: 40px;">

    <!-- HEADER TITLE & BADGE (KOMPAK SESUAI STANDAR SIMRS) -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 700; color: #1e293b; margin: 0 0 4px 0; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-key" style="color: #1C4D8D;"></i> Permintaan Reset Password & 2FA
            </h2>
            <p style="font-size: 13px; color: #64748b; margin: 0;">
                Pusat pemulihan akses akun, kata sandi sementara baru, dan pembukaan proteksi 2FA untuk staf RS & pasien.
            </p>
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
            <a href="{{ route('admin.pengguna') }}" class="simrs-btn-outline" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; color: #475569; background: white; border: 1px solid #cbd5e1;">
                <i class="fa-solid fa-users-gear"></i> Kelola Pengguna
            </a>
            <span style="background: #e0e7ff; color: #3730a3; padding: 6px 12px; border-radius: 20px; font-size: 11.5px; font-weight: 700; text-transform: uppercase; border: 1px solid #c7d2fe;">
                <i class="fa-solid fa-server"></i> Tim TI & SIMRS
            </span>
        </div>
    </div>

    <!-- FLASH NOTIFIKASI SUKSES RESET (KOMPAK & DIRECT WHATSAPP) -->
    @if (session('status_reset_sukses'))
        @php $sr = session('status_reset_sukses'); @endphp
        <div style="background: #ffffff; border-radius: 12px; border: 1.5px solid #10b981; padding: 14px 18px; margin-bottom: 18px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.1);">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-circle-check" style="color: #059669; font-size: 20px;"></i>
                    <div style="font-size: 13px; color: #334155;">
                        <strong style="color: #065f46;">{{ $sr['message'] }}</strong>
                        <span style="margin-left: 8px; color: #64748b;">(Akun: <strong>{{ $sr['nama'] }}</strong> - NIK: {{ $sr['nik'] }})</span>
                        @if (!empty($sr['temp_password']))
                            <span style="margin-left: 8px;">Password Baru: <strong style="font-family: monospace; background: #f1f5f9; padding: 2px 6px; border-radius: 4px; border: 1px solid #cbd5e1; color: #0f172a;">{{ $sr['temp_password'] }}</strong></span>
                        @endif
                    </div>
                </div>

                @if (!empty($sr['wa_url']))
                    <a href="{{ $sr['wa_url'] }}" target="_blank"
                       style="background: #25D366; color: white; padding: 7px 14px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 12px; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 6px rgba(37, 211, 102, 0.25);">
                        <i class="fa-brands fa-whatsapp" style="font-size: 14px;"></i> Kirim via WhatsApp
                    </a>
                @endif
            </div>
        </div>
    @endif

    @if (session('status_pegawai'))
        @php $sp = session('status_pegawai'); @endphp
        <div style="background: #ffffff; border-radius: 12px; border: 1px solid #bbf7d0; padding: 12px 16px; margin-bottom: 18px;">
            <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #15803d;">
                <i class="fa-solid fa-circle-check"></i>
                <strong>{{ $sp['message'] }}</strong>
            </div>
        </div>
    @endif

    <!-- CONTAINER UTAMA -->
    <div style="background: white; border-radius: 14px; padding: 18px 20px; border: 1px solid #e2e8f0; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);">
        
        <!-- TOOLBAR: FILTER STATUS TABS & PENCARIAN (KOMPAK & EFISIEN) -->
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 16px; padding-bottom: 14px; border-bottom: 1px solid #f1f5f9;">
            
            <!-- Filter Status Tabs -->
            <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                <a href="{{ route('admin.reset', ['status' => 'semua', 'tipe' => $tipe, 'q' => $q]) }}" 
                   style="padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; text-decoration: none; transition: all 0.2s; {{ $status === 'semua' ? 'background: #1C4D8D; color: white;' : 'background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0;' }}">
                    Semua ({{ $stats['total'] }})
                </a>
                <a href="{{ route('admin.reset', ['status' => 'menunggu', 'tipe' => $tipe, 'q' => $q]) }}" 
                   style="padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; text-decoration: none; transition: all 0.2s; display: inline-flex; align-items: center; gap: 5px; {{ $status === 'menunggu' ? 'background: #dc2626; color: white;' : 'background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3;' }}">
                    <i class="fa-solid fa-clock" style="font-size: 10px;"></i> Menunggu ({{ $stats['menunggu'] }})
                </a>
                <a href="{{ route('admin.reset', ['status' => 'selesai', 'tipe' => $tipe, 'q' => $q]) }}" 
                   style="padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; text-decoration: none; transition: all 0.2s; {{ $status === 'selesai' ? 'background: #16a34a; color: white;' : 'background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0;' }}">
                    Selesai ({{ $stats['selesai'] }})
                </a>
                <a href="{{ route('admin.reset', ['status' => 'ditolak', 'tipe' => $tipe, 'q' => $q]) }}" 
                   style="padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; text-decoration: none; transition: all 0.2s; {{ $status === 'ditolak' ? 'background: #475569; color: white;' : 'background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0;' }}">
                    Ditolak ({{ $stats['ditolak'] }})
                </a>
            </div>

            <!-- Filter Pencarian & Tipe Akun -->
            <form action="{{ route('admin.reset') }}" method="GET" style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                <input type="hidden" name="status" value="{{ $status }}">
                
                <select name="tipe" onchange="this.form.submit()" style="padding: 6px 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 12px; color: #334155; background: white; outline: none; cursor: pointer;">
                    <option value="semua" {{ $tipe === 'semua' ? 'selected' : '' }}>Semua Akun</option>
                    <option value="pegawai" {{ $tipe === 'pegawai' ? 'selected' : '' }}>Staf RS Saja</option>
                    <option value="pasien" {{ $tipe === 'pasien' ? 'selected' : '' }}>Pasien Saja</option>
                </select>

                <div style="position: relative;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 11px;"></i>
                    <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama, NIK, No. HP..."
                           style="padding: 6px 10px 6px 28px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 12px; color: #1e293b; width: 190px; outline: none;">
                </div>

                <button type="submit" style="background: #1C4D8D; color: white; border: none; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer;">
                    Cari
                </button>

                @if(!empty($q) || $tipe !== 'semua' || $status !== 'semua')
                    <a href="{{ route('admin.reset') }}" style="color: #64748b; font-size: 12px; text-decoration: none; padding: 5px 8px;" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i> Reset
                    </a>
                @endif
            </form>

        </div>

        <!-- TABEL DATA PERMINTAAN RESET (KOMPAK & PROPOSIONAL) -->
        @if ($permintaanResets->count() > 0)
            <div style="overflow-x: auto;">
                <table class="simrs-data-table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr>
                            <th style="padding: 9px 12px; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">Pemohon</th>
                            <th style="padding: 9px 12px; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">Tipe</th>
                            <th style="padding: 9px 12px; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">Kendala</th>
                            <th style="padding: 9px 12px; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">No. WhatsApp / HP</th>
                            <th style="padding: 9px 12px; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">Catatan</th>
                            <th style="padding: 9px 12px; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">Waktu Pengajuan</th>
                            <th style="padding: 9px 12px; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: center; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">Status</th>
                            <th style="padding: 9px 12px; font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: right; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($permintaanResets as $req)
                            <tr class="simrs-table-row" style="{{ $req->status === 'menunggu' ? 'background: #fffafa;' : '' }}">
                                <td style="padding: 10px 12px; border-bottom: 1px solid #f1f5f9;">
                                    <div style="font-weight: 700; color: #1e293b; font-size: 13px;">{{ $req->nama }}</div>
                                    <div style="font-size: 10.5px; color: #64748b; font-family: monospace;">NIK: {{ $req->nik }}</div>
                                </td>
                                <td style="padding: 10px 12px; border-bottom: 1px solid #f1f5f9;">
                                    @if ($req->tipe_pengguna === 'pegawai')
                                        <span style="font-size: 11px; font-weight: 700; background: #e0f2fe; color: #0369a1; padding: 2px 7px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px;">
                                            <i class="fa-solid fa-user-doctor"></i> Staf
                                        </span>
                                    @else
                                        <span style="font-size: 11px; font-weight: 700; background: #fef3c7; color: #b45309; padding: 2px 7px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px;">
                                            <i class="fa-solid fa-hospital-user"></i> Pasien
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 10px 12px; border-bottom: 1px solid #f1f5f9;">
                                    @php
                                        $badgeBg = '#f1f5f9'; $badgeColor = '#334155';
                                        if ($req->tipe_permintaan === 'password') {
                                            $badgeBg = '#fee2e2'; $badgeColor = '#b91c1c';
                                        } elseif ($req->tipe_permintaan === '2fa') {
                                            $badgeBg = '#e0e7ff'; $badgeColor = '#4338ca';
                                        } elseif ($req->tipe_permintaan === 'keduanya') {
                                            $badgeBg = '#fae8ff'; $badgeColor = '#86198f';
                                        }
                                    @endphp
                                    <span style="font-size: 11px; font-weight: 700; background: {{ $badgeBg }}; color: {{ $badgeColor }}; padding: 2px 7px; border-radius: 6px;">
                                        {{ $req->label_tipe_permintaan }}
                                    </span>
                                </td>
                                <td style="padding: 10px 12px; border-bottom: 1px solid #f1f5f9;">
                                    @if (!empty($req->no_hp))
                                        <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $req->no_hp)) }}" 
                                           target="_blank" 
                                           style="font-weight: 600; color: #047857; font-size: 12px; display: inline-flex; align-items: center; gap: 5px; text-decoration: none;"
                                           title="Buka Chat WhatsApp">
                                            <i class="fa-brands fa-whatsapp" style="font-size: 13px;"></i> {{ $req->no_hp }}
                                        </a>
                                    @else
                                        <span style="color: #94a3b8; font-size: 11px;">-</span>
                                    @endif
                                </td>
                                <td style="padding: 10px 12px; border-bottom: 1px solid #f1f5f9; max-width: 200px;">
                                    <div style="font-size: 11.5px; color: #475569; line-height: 1.3;" title="{{ $req->catatan }}">
                                        {{ $req->catatan ?? 'Tidak ada catatan' }}
                                    </div>
                                    @if (!empty($req->catatan_admin))
                                        <div style="font-size: 10px; color: #64748b; margin-top: 3px; background: #f8fafc; padding: 2px 5px; border-radius: 4px;">
                                            <em>Catatan: {{ $req->catatan_admin }}</em>
                                        </div>
                                    @endif
                                </td>
                                <td style="padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-size: 11.5px; color: #64748b;">
                                    <div>{{ $req->created_at ? $req->created_at->translatedFormat('d M Y, H:i') : '-' }}</div>
                                    <div style="font-size: 10px; color: #94a3b8;">{{ $req->created_at ? $req->created_at->diffForHumans() : '' }}</div>
                                </td>
                                <td style="padding: 10px 12px; border-bottom: 1px solid #f1f5f9; text-align: center;">
                                    @if ($req->status === 'menunggu')
                                        <span style="font-size: 10.5px; font-weight: 700; background: #fee2e2; color: #dc2626; padding: 2px 8px; border-radius: 999px; border: 1px solid #fca5a5;">
                                            Menunggu
                                        </span>
                                    @elseif ($req->status === 'selesai')
                                        <span style="font-size: 10.5px; font-weight: 700; background: #dcfce7; color: #16a34a; padding: 2px 8px; border-radius: 999px;">
                                            <i class="fa-solid fa-check"></i> Selesai
                                        </span>
                                    @else
                                        <span style="font-size: 10.5px; font-weight: 700; background: #f1f5f9; color: #64748b; padding: 2px 8px; border-radius: 999px;">
                                            Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 10px 12px; border-bottom: 1px solid #f1f5f9; text-align: right;">
                                    @if ($req->status === 'menunggu')
                                        <div style="display: inline-flex; gap: 6px;">
                                            <form action="{{ route('admin.reset.proses', $req->id_permintaan) }}" method="POST" onsubmit="return confirm('Proses reset {{ $req->label_tipe_permintaan }} untuk {{ $req->nama }}?');">
                                                @csrf
                                                <button type="submit" style="background: #1C4D8D; color: white; border: none; padding: 5px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                                    <i class="fa-solid fa-wand-magic-sparkles"></i> Proses & Reset
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.reset.tolak', $req->id_permintaan) }}" method="POST" onsubmit="return confirm('Tolak permohonan reset untuk {{ $req->nama }}?');">
                                                @csrf
                                                <button type="submit" style="background: #f8fafc; color: #64748b; border: 1px solid #cbd5e1; padding: 5px 8px; border-radius: 6px; font-size: 11px; font-weight: 600; cursor: pointer;">
                                                    Tolak
                                                </button>
                                            </form>
                                        </div>
                                    @elseif ($req->status === 'selesai')
                                        <div style="font-size: 11px; color: #64748b;">
                                            <i class="fa-solid fa-user-check" style="color: #10b981;"></i> {{ $req->adminPemroses->nama ?? 'Admin' }}
                                            <div style="font-size: 10px; color: #94a3b8;">{{ $req->diproses_pada ? \Carbon\Carbon::parse($req->diproses_pada)->translatedFormat('d M, H:i') : '' }}</div>
                                        </div>
                                    @else
                                        <div style="font-size: 11px; color: #94a3b8;">
                                            Ditolak
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 14px;">
                {{ $permintaanResets->links('partials.pagination') }}
            </div>
        @else
            <div style="text-align: center; padding: 30px 16px; color: #94a3b8;">
                <i class="fa-solid fa-circle-check" style="font-size: 26px; color: #10b981; display: block; margin-bottom: 6px;"></i>
                <div style="font-size: 13px; font-weight: 600; color: #1e293b;">Tidak Ada Permintaan Reset</div>
                <p style="font-size: 12px; color: #64748b; margin: 4px auto 0; max-width: 400px;">
                    @if (!empty($q) || $status !== 'semua' || $tipe !== 'semua')
                        Tidak ditemukan permohonan yang sesuai filter.
                    @else
                        Saat ini tidak ada permohonan reset akun yang menunggu tindakan.
                    @endif
                </p>
                @if (!empty($q) || $status !== 'semua' || $tipe !== 'semua')
                    <div style="margin-top: 10px;">
                        <a href="{{ route('admin.reset') }}" style="text-decoration: none; padding: 5px 12px; border-radius: 6px; font-size: 11.5px; font-weight: 600; color: #1C4D8D; border: 1px solid #1C4D8D; display: inline-block;">
                            Lihat Semua
                        </a>
                    </div>
                @endif
            </div>
        @endif

    </div>

</div>
@endsection
