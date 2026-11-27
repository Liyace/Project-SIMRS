@extends('layouts.app')
@php $halamanAktif = 'admin_layanan'; @endphp
@section('title', 'Master Tarif & Layanan Medis - Super Admin RS')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/kelola.css') }}">
    <style>
        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 14px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
        }
        .modal-content h3 {
            margin-bottom: 1.5rem;
            font-weight: 600;
            color: #1e293b;
        }
        .form-group {
            margin-bottom: 1.25rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            font-size: 0.9rem;
            color: #475569;
        }
        .form-group input {
            width: 100%;
            padding: 0.65rem 0.75rem;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            outline: none;
            box-sizing: border-box;
            transition: border-color 0.2s;
        }
        .form-group input:focus {
            border-color: #0ea5e9;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15);
        }
        .modal-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            margin-top: 2rem;
        }
        .btn-save {
            background: #2563eb;
            color: white;
            border: none;
            padding: 0.65rem 1.25rem;
            border-radius: 8px;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
        }
        .btn-cancel {
            background: #e2e8f0;
            color: #475569;
            border: none;
            padding: 0.65rem 1.25rem;
            border-radius: 8px;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
        }
        .btn-add-layanan, .btn-action {
            border: none;
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
<div style="padding-bottom: 50px;">
    <!-- HEADER TITLE & BADGE -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 25px; flex-wrap: wrap; gap: 12px;">
        <div>
            <h2 style="font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 4px;">
                <i class="fa-solid fa-notes-medical" style="color: #2563eb; margin-right: 8px;"></i> Master Tarif & Layanan Medis
            </h2>
            <p style="font-size: 13px; color: #64748b;">
                Kelola data master layanan medis, tindakan klinis, dan tarif biaya resmi Rumah Sakit Delta.
            </p>
        </div>
        <div style="text-align: right;">
            <span style="background: #e0e7ff; color: #3730a3; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; border: 1px solid #c7d2fe;">
                <i class="fa-solid fa-server"></i> Super Admin RS
            </span>
        </div>
    </div>

    @php
        $list = $layananList ?? \App\Models\Layanan::orderBy('id_layanan')->get();
        $totalData = $list->count();
    @endphp

    <div class="section-container">
        <div class="section-header">
            <h2 class="section-title">Daftar Layanan Medis & Tarif</h2>
            <button class="btn-add-layanan" id="btnTambahLayanan">
                <i class="fa-solid fa-plus"></i> Tambah Layanan
            </button>
        </div>

        <div style="overflow-x: auto; width: 100%;">
            <table class="kasir-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 70px;">NO.</th>
                        <th class="text-left">NAMA LAYANAN</th>
                        <th class="text-right" style="width: 220px;">HARGA (RP)</th>
                        <th class="text-center" style="width: 200px;">AKSI</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse ($list as $index => $layanan)
                        <tr data-id="{{ $layanan->id_layanan }}">
                            <td class="text-center font-bold" style="color: #64748b;">{{ $index + 1 }}</td>
                            <td class="text-left cell-service-name">{{ $layanan->nama_layanan }}</td>
                            <td class="text-right cell-price">Rp {{ number_format($layanan->harga_layanan, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <div class="action-buttons">
                                    <button class="btn-action btn-edit" data-id="{{ $layanan->id_layanan }}" data-nama="{{ $layanan->nama_layanan }}" data-harga="{{ $layanan->harga_layanan }}">
                                        <i class="fa-regular fa-pen-to-square"></i> Edit
                                    </button>
                                    <button class="btn-action btn-delete" data-id="{{ $layanan->id_layanan }}">
                                        <i class="fa-regular fa-trash-can"></i> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="rowEmpty">
                            <td colspan="4" class="text-center text-muted" style="padding: 30px;">
                                <i class="fa-solid fa-circle-exclamation" style="font-size: 28px; margin-bottom: 8px; color: #94a3b8; display: block;"></i>
                                Tidak ada data layanan yang tersedia.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <div>
                Menampilkan <strong>1 - {{ $totalData }}</strong> dari <strong>{{ $totalData }}</strong> layanan
            </div>
        </div>
    </div>
</div>

<div id="modalLayanan" class="modal">
    <div class="modal-content">
        <h3 id="modalTitle">Tambah Layanan</h3>
        <form id="formLayanan">
            <input type="hidden" id="layananId" name="id">
            <div class="form-group">
                <label>Nama Layanan</label>
                <input type="text" id="namaLayanan" name="nama_layanan" required placeholder="Masukkan nama layanan...">
            </div>
            <div class="form-group">
                <label>Harga (Rp)</label>
                <input type="number" id="hargaLayanan" name="harga_layanan" required min="0" step="1000" placeholder="Contoh: 50000">
            </div>
            <div class="modal-buttons">
                <button type="button" class="btn-cancel" id="btnCancelModal">Batal</button>
                <button type="submit" class="btn-save">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const API_BASE_URL = "{{ url('/admin/layanan') }}";
    const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '{{ csrf_token() }}';
    const modal = document.getElementById('modalLayanan');
    const form = document.getElementById('formLayanan');
    const modalTitle = document.getElementById('modalTitle');
    const layananId = document.getElementById('layananId');
    const namaLayanan = document.getElementById('namaLayanan');
    const hargaLayanan = document.getElementById('hargaLayanan');
    
    let currentMode = 'tambah';
    
    document.getElementById('btnTambahLayanan').addEventListener('click', () => {
        currentMode = 'tambah';
        modalTitle.innerText = 'Tambah Layanan';
        form.reset();
        layananId.value = '';
        modal.style.display = 'flex';
    });
    
    document.querySelector('#tableBody').addEventListener('click', (e) => {
        const btnEdit = e.target.closest('.btn-edit');
        if (btnEdit) {
            e.preventDefault();
            const id = btnEdit.dataset.id;
            const nama = btnEdit.dataset.nama;
            const harga = btnEdit.dataset.harga;
            
            currentMode = 'edit';
            modalTitle.innerText = 'Edit Layanan';
            layananId.value = id;
            namaLayanan.value = nama;
            hargaLayanan.value = Math.round(parseFloat(harga));
            modal.style.display = 'flex';
        }
        
        const btnDelete = e.target.closest('.btn-delete');
        if (btnDelete) {
            e.preventDefault();
            const id = btnDelete.dataset.id;
            if (id && confirm('Yakin ingin menghapus layanan ini?')) {
                hapusLayanan(id);
            }
        }
    });
    
    function tutupModal() {
        modal.style.display = 'none';
    }
    document.getElementById('btnCancelModal').addEventListener('click', tutupModal);
    window.addEventListener('click', (e) => {
        if (e.target === modal) tutupModal();
    });
    
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const id = layananId.value;
        const nama = namaLayanan.value.trim();
        const harga = parseInt(hargaLayanan.value);
        
        if (!nama || harga < 0) {
            alert('Nama dan harga harus diisi dengan benar');
            return;
        }
        
        let payload = {
            nama_layanan: nama,
            harga_layanan: harga
        };
        
        let url = API_BASE_URL;
        let method = 'POST';
        
        if (currentMode === 'edit') {
            method = 'PUT';
            url = `${API_BASE_URL}/${id}`;
        }
        
        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            });
            
            const result = await response.json();
            if (response.ok && (result.success || result.success === undefined)) {
                alert(result.message || 'Berhasil menyimpan data');
                location.reload();
            } else {
                alert('Gagal: ' + (result.message || 'Terjadi kesalahan pada server'));
            }
        } catch (err) {
            alert('Error koneksi: ' + err.message);
        }
    });
    
    async function hapusLayanan(id) {
        const url = `${API_BASE_URL}/${id}`;
        
        try {
            const response = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            
            const result = await response.json();
            if (response.ok && (result.success || result.success === undefined)) {
                alert(result.message || 'Layanan berhasil dihapus');
                location.reload();
            } else {
                alert('Gagal hapus: ' + (result.message || 'Terjadi kesalahan pada server'));
            }
        } catch (err) {
            alert('Error koneksi: ' + err.message);
        }
    }
</script>
@endpush
