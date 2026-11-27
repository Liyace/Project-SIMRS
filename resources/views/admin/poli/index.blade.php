@extends('layouts.app')
@php $halamanAktif = 'admin_poli'; @endphp
@section('title', 'Master Ruangan & Poliklinik - Super Admin RS')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/kelola.css') }}">
    <style>
        .modal-poli {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .modal-poli-content {
            background: white;
            padding: 2rem;
            border-radius: 16px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
        }
        body.dark-mode .modal-poli-content {
            background: #121927;
            border: 1px solid #1e293b;
            color: #f1f5f9;
        }
        body.dark-mode .modal-poli-content h3 {
            color: #f8fafc;
        }
        body.dark-mode .modal-poli-content label {
            color: #cbd5e1;
        }
        body.dark-mode .modal-poli-content input {
            background-color: #0f172a;
            border-color: #334155;
            color: #f8fafc;
        }
    </style>
@endpush

@section('content')
<div style="padding-bottom: 50px;">

    <!-- HEADER TITLE & BADGE -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 25px; flex-wrap: wrap; gap: 12px;">
        <div>
            <h2 style="font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 4px;">
                <i class="fa-solid fa-hospital" style="color: #2563eb; margin-right: 8px;"></i> Master Ruangan & Poliklinik
            </h2>
            <p style="font-size: 13px; color: #64748b;">
                Kelola daftar poliklinik pelayanan medis, kode poli antrian, dan tarif standar biaya konsultasi dokter di Rumah Sakit Delta.
            </p>
        </div>
        <div style="text-align: right;">
            <span style="background: #e0e7ff; color: #3730a3; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; border: 1px solid #c7d2fe;">
                <i class="fa-solid fa-server"></i> Super Admin RS
            </span>
        </div>
    </div>

    <!-- FLASH MESSAGE NOTIFIKASI -->
    @if (session('success'))
        <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-size: 13px;">
            <i class="fa-solid fa-circle-check" style="color: #10b981; font-size: 18px;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-size: 13px;">
            <i class="fa-solid fa-circle-exclamation" style="color: #ef4444; font-size: 18px;"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; font-size: 13px;">
            <strong style="display: block; margin-bottom: 6px;"><i class="fa-solid fa-triangle-exclamation"></i> Terdapat kesalahan validasi:</strong>
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- STATS CARDS GRID -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; margin-bottom: 25px;">
        <div class="section-container" style="padding: 18px 22px; margin: 0; display: flex; align-items: center; gap: 16px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 22px;">
                <i class="fa-solid fa-hospital-user"></i>
            </div>
            <div>
                <span style="display: block; font-size: 11px; color: #64748b; font-weight: 500;">Total Poliklinik</span>
                <span style="font-size: 22px; font-weight: 700; color: #6e92e6ff;">{{ $stats['total_poli'] }} Poli</span>
            </div>
        </div>

        <div class="section-container" style="padding: 18px 22px; margin: 0; display: flex; align-items: center; gap: 16px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 22px;">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
            <div>
                <span style="display: block; font-size: 11px; color: #64748b; font-weight: 500;">Rata-rata Tarif Konsultasi</span>
                <span style="font-size: 22px; font-weight: 700; color: #059669;">Rp {{ number_format($stats['rata_biaya'], 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="section-container" style="padding: 18px 22px; margin: 0; display: flex; align-items: center; gap: 16px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 22px;">
                <i class="fa-solid fa-users-line"></i>
            </div>
            <div>
                <span style="display: block; font-size: 11px; color: #64748b; font-weight: 500;">Total Kunjungan Pasien</span>
                <span style="font-size: 22px; font-weight: 700; color: #6e92e6ff;">{{ $stats['total_kunjungan'] }} Kunjungan</span>
            </div>
        </div>
    </div>

    <!-- TABEL KARTU POLIKLINIK -->
    <div class="section-container">
        <div class="section-header">
            <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                <h2 class="section-title">Daftar Poliklinik & Ruang Pelayanan</h2>
                <form method="GET" action="{{ route('admin.poli') }}" style="display: flex; align-items: center; gap: 8px;">
                    <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama atau kode poli..."
                           style="padding: 7px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 12px; outline: none; width: 220px;">
                    @if ($q)
                        <a href="{{ route('admin.poli') }}" style="color: #64748b; font-size: 12px; text-decoration: none; padding: 7px 10px;">Reset</a>
                    @endif
                    <button type="submit" class="btn-filter-submit" style="padding: 7px 12px; font-size: 12px;">
                        <i class="fa-solid fa-magnifying-glass"></i> Cari
                    </button>
                </form>
            </div>
            <button class="btn-add-layanan" id="btnTambahPoli">
                <i class="fa-solid fa-plus"></i> Tambah Poliklinik
            </button>
        </div>

        <div style="overflow-x: auto; width: 100%;">
            <table class="kasir-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 60px;">NO.</th>
                        <th class="text-center" style="width: 120px;">KODE POLI</th>
                        <th class="text-left">NAMA POLIKLINIK</th>
                        <th class="text-left" style="min-width: 220px;">DOKTER BERTUGAS</th>
                        <th class="text-right" style="width: 180px;">BIAYA KONSULTASI</th>
                        <th class="text-center" style="width: 160px;">TOTAL KUNJUNGAN</th>
                        <th class="text-center" style="width: 160px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($polis as $index => $p)
                        <tr>
                            <td class="text-center font-bold" style="color: #64748b;">
                                {{ $polis->firstItem() + $index }}
                            </td>
                            <td class="text-center">
                                <span style="background: #eff6ff; color: #2563eb; font-weight: 700; padding: 4px 10px; border-radius: 6px; font-size: 12px; border: 1px solid #bfdbfe; font-family: monospace;">
                                    {{ $p->kode_poli ?? '-' }}
                                </span>
                            </td>
                            <td class="text-left cell-service-name">
                                {{ $p->nama_poli }}
                            </td>
                            <td class="text-left">
                                @if ($p->dokters->count() > 0)
                                    <div style="display: flex; flex-direction: column; gap: 5px;">
                                        @foreach ($p->dokters as $doc)
                                            <div style="display: inline-flex; align-items: center; gap: 6px; font-size: 11.5px; background: #eff6ff; color: #1e40af; padding: 3px 8px; border-radius: 6px; border: 1px solid #dbeafe; width: fit-content;">
                                                <i class="fa-solid fa-user-doctor" style="font-size: 10px; color: #3b82f6;"></i>
                                                <span style="font-weight: 600;">{{ $doc->nama }}</span>
                                                @if (!empty($doc->spesialisasi))
                                                    <span style="color: #64748b; font-size: 10.5px;">({{ $doc->spesialisasi }})</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span style="color: #94a3b8; font-size: 11.5px; font-style: italic;">
                                        <i class="fa-solid fa-circle-info" style="margin-right: 3px;"></i> Belum ada dokter
                                    </span>
                                @endif
                            </td>
                            <td class="text-right cell-price">
                                Rp {{ number_format($p->biaya_konsultasi, 0, ',', '.') }}
                            </td>
                            <td class="text-center font-bold" style="color: #475569;">
                                {{ $p->pendaftaran_count }} Pasien
                            </td>
                            <td class="text-center">
                                <div class="action-buttons">
                                    <button type="button" class="btn-action btn-edit"
                                            onclick="bukaModalEdit('{{ $p->id_poli }}', '{{ addslashes($p->kode_poli) }}', '{{ addslashes($p->nama_poli) }}', '{{ (int)$p->biaya_konsultasi }}', {{ json_encode($p->dokters->pluck('id_pegawai')) }})">
                                        <i class="fa-regular fa-pen-to-square"></i> Edit
                                    </button>
                                    <form method="POST" action="{{ route('admin.poli.destroy', $p->id_poli) }}"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus poliklinik {{ $p->nama_poli }}?')" style="margin: 0; display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-delete">
                                            <i class="fa-regular fa-trash-can"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted" style="padding: 35px 0;">
                                <i class="fa-solid fa-hospital-user" style="font-size: 32px; margin-bottom: 10px; color: #cbd5e1; display: block;"></i>
                                Tidak ada data poliklinik yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($polis->hasPages())
            <div style="margin-top: 25px; display: flex; justify-content: center;">
                {{ $polis->links() }}
            </div>
        @endif
    </div>

</div>

<!-- MODAL TAMBAH POLIKLINIK -->
<div id="modalTambahPoli" class="modal-poli">
    <div class="modal-poli-content">
        <h3 style="margin-top: 0; margin-bottom: 1.5rem; font-weight: 600;">Tambah Poliklinik Baru</h3>
        <form method="POST" action="{{ route('admin.poli.store') }}">
            @csrf
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.9rem; color: #475569;">Kode Poli (Maks. 5 Karakter Huruf)</label>
                <input type="text" name="kode_poli" required placeholder="Contoh: UMU, GIG, THT, MAT" maxlength="10"
                       style="width: 100%; padding: 0.65rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; text-transform: uppercase;">
            </div>
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.9rem; color: #475569;">Nama Poliklinik</label>
                <input type="text" name="nama_poli" required placeholder="Contoh: Poli Spesialis THT"
                       style="width: 100%; padding: 0.65rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem;">
            </div>
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.9rem; color: #475569;">Biaya Konsultasi (Rp)</label>
                <input type="number" name="biaya_konsultasi" required min="0" step="1000" placeholder="Contoh: 75000"
                       style="width: 100%; padding: 0.65rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem;">
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.9rem; color: #475569;">
                    <i class="fa-solid fa-user-doctor" style="color: #2563eb;"></i> Tugaskan Dokter ke Poli Ini (Opsional)
                </label>
                <div style="max-height: 140px; overflow-y: auto; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 10px; background: #fff;">
                    @forelse ($semuaDokters as $doc)
                        <label style="display: flex; align-items: center; gap: 8px; font-size: 12.5px; padding: 4px 6px; cursor: pointer; border-radius: 6px;">
                            <input type="checkbox" name="dokter_ids[]" value="{{ $doc->id_pegawai }}">
                            <span style="font-weight: 500;">{{ $doc->nama }}</span>
                            @if (!empty($doc->spesialisasi))
                                <span style="color: #64748b; font-size: 11px;">({{ $doc->spesialisasi }})</span>
                            @endif
                        </label>
                    @empty
                        <span style="color: #94a3b8; font-size: 12px; font-style: italic;">Belum ada dokter terdaftar</span>
                    @endforelse
                </div>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" class="btn-cancel" onclick="tutupModalTambah()">Batal</button>
                <button type="submit" class="btn-save">Simpan Poliklinik</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT POLIKLINIK -->
<div id="modalEditPoli" class="modal-poli">
    <div class="modal-poli-content">
        <h3 style="margin-top: 0; margin-bottom: 1.5rem; font-weight: 600;">Edit Poliklinik</h3>
        <form id="formEditPoli" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.9rem; color: #475569;">Kode Poli</label>
                <input type="text" id="edit_kode_poli" name="kode_poli" required maxlength="10"
                       style="width: 100%; padding: 0.65rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; text-transform: uppercase;">
            </div>
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.9rem; color: #475569;">Nama Poliklinik</label>
                <input type="text" id="edit_nama_poli" name="nama_poli" required
                       style="width: 100%; padding: 0.65rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem;">
            </div>
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.9rem; color: #475569;">Biaya Konsultasi (Rp)</label>
                <input type="number" id="edit_biaya_konsultasi" name="biaya_konsultasi" required min="0" step="1000"
                       style="width: 100%; padding: 0.65rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem;">
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.9rem; color: #475569;">
                    <i class="fa-solid fa-user-doctor" style="color: #2563eb;"></i> Dokter Bertugas di Poliklinik Ini
                </label>
                <div style="max-height: 140px; overflow-y: auto; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 10px; background: #fff;">
                    @forelse ($semuaDokters as $doc)
                        <label style="display: flex; align-items: center; gap: 8px; font-size: 12.5px; padding: 4px 6px; cursor: pointer; border-radius: 6px;">
                            <input type="checkbox" name="dokter_ids[]" value="{{ $doc->id_pegawai }}" class="checkbox-edit-dokter">
                            <span style="font-weight: 500;">{{ $doc->nama }}</span>
                            @if (!empty($doc->spesialisasi))
                                <span style="color: #64748b; font-size: 11px;">({{ $doc->spesialisasi }})</span>
                            @endif
                        </label>
                    @empty
                        <span style="color: #94a3b8; font-size: 12px; font-style: italic;">Belum ada dokter terdaftar</span>
                    @endforelse
                </div>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" class="btn-cancel" onclick="tutupModalEdit()">Batal</button>
                <button type="submit" class="btn-save">Perbarui Poliklinik</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const modalTambah = document.getElementById('modalTambahPoli');
    const modalEdit = document.getElementById('modalEditPoli');
    const formEdit = document.getElementById('formEditPoli');

    document.getElementById('btnTambahPoli').addEventListener('click', () => {
        modalTambah.style.display = 'flex';
    });

    function tutupModalTambah() {
        modalTambah.style.display = 'none';
    }

    function tutupModalEdit() {
        modalEdit.style.display = 'none';
    }

    function bukaModalEdit(id, kode, nama, biaya, dokterIds = []) {
        formEdit.action = `/admin/poli/${id}`;
        document.getElementById('edit_kode_poli').value = kode;
        document.getElementById('edit_nama_poli').value = nama;
        document.getElementById('edit_biaya_konsultasi').value = biaya;

        const parsedIds = Array.isArray(dokterIds) ? dokterIds.map(String) : [];
        document.querySelectorAll('.checkbox-edit-dokter').forEach(cb => {
            cb.checked = parsedIds.includes(String(cb.value));
        });

        modalEdit.style.display = 'flex';
    }

    window.addEventListener('click', (e) => {
        if (e.target === modalTambah) tutupModalTambah();
        if (e.target === modalEdit) tutupModalEdit();
    });
</script>
@endpush
