@extends('layouts.app')
@php $halamanAktif = 'antrian'; @endphp
@section('title', 'Antrian Kasir - Rumah Sakit Delta')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/antrian.css') }}">
@endpush

@section('content')
<div class="page-header-text">
    <h2>Antrian Kasir</h2>
    <p>Pelayanan antrian pembayaran pasien, rincian biaya tindakan, dan cetak nota kasir.</p>
</div>

    {{-- MODAL NOTA SUKSES PEMBAYARAN --}}
    <div id="modal-nota">
        <div class="modal-content">
            <i class="fa fa-check-circle" style="color:#059669; font-size:50px; margin-bottom:10px; display:block; text-align:center;"></i>
            <h3 style="text-align:center;">Pembayaran Berhasil!</h3>
            <div id="isi-nota" style="text-align:left; font-size:13px; margin:15px 0;"></div>
            <button onclick="tutupNota()" style="width:100%; padding:10px; background:#2563eb; color:white; border:none; border-radius:8px; cursor:pointer; font-family:'Poppins',sans-serif; font-weight:600;">
                OK / Selesai
            </button>
        </div>
    </div>

    <div class="antrian-container">

        {{-- PANEL 1: DAFTAR ANTRIAN PASIEN --}}
        <div class="panel">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                <h3 style="margin:0;">Daftar Antrian Pasien</h3>
                <button type="button" onclick="initAntrianApp()" title="Segarkan Antrian" style="background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe; border-radius:6px; padding:4px 10px; font-size:11px; font-weight:600; cursor:pointer;">
                    <i class="fa-solid fa-rotate"></i> Refresh
                </button>
            </div>
            <p class="sub-desc">Pilih pasien untuk memproses pembayaran loket.</p>
            <div style="overflow-x:auto;">
                <table class="table-antrian">
                    <thead>
                        <tr>
                            <th>NO. ANTRIAN</th>
                            <th>NAMA PASIEN</th>
                            <th>POLI</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody id="tabel-antrian-pasien">
                        <tr>
                            <td colspan="4" style="text-align:center; padding:20px; color:#64748b;">
                                <i class="fa fa-spinner fa-spin"></i> Memuat antrian dari sistem SIMRS...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- PANEL 2: TRANSAKSI PASIEN --}}
        <div class="panel" id="transaksi-panel">
            <h3>Transaksi Pasien</h3>
            <div id="konten-transaksi">
                <p style="color:#94a3b8; font-size:13px;">Silakan pilih pasien pada antrian di samping untuk memulai transaksi.</p>
            </div>
        </div>

        {{-- PANEL 3: RINGKASAN PEMBAYARAN --}}
        <div class="panel">
            <h3>Ringkasan Pembayaran</h3>
            <div style="font-size:14px; margin-top:15px;">
                <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                    <span>Total Layanan</span>
                    <span id="val-layanan" style="font-weight:600;">Rp 0</span>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:15px;">
                    <span>Total Obat (Resep)</span>
                    <span id="val-obat" style="font-weight:600;">Rp 0</span>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:15px;">
                    <span>Jasa Medis / Perawat</span>
                    <span id="val-jasa-medis" style="font-weight:600;">Rp 0</span>
                </div>
                <hr style="margin:10px 0; border:0; border-top:1px solid #e2e8f0;">
                <div style="display:flex; justify-content:space-between; margin:15px 0;">
                    <strong>TOTAL BAYAR</strong>
                    <strong id="val-total" style="font-size:18px; color:#0f172a;">Rp 0</strong>
                </div>

                <div style="margin-top:20px;">
                    <label style="font-size:12px; color:#64748b; font-weight:600; display:block; margin-bottom:4px;">PEMBAYARAN (TUNAI)</label>
                    <input type="number" id="input-tunai" placeholder="Masukkan nominal tunai..."
                        oninput="hitungKembalian()"
                        style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-family:'Poppins',sans-serif; box-sizing:border-box;">
                </div>

                <div style="margin-top:15px; background:#f8fafc; padding:15px; border-radius:8px; border:1px solid #e2e8f0;" id="box-kembalian">
                    <div style="font-size:12px; color:#64748b;">Kembalian</div>
                    <div id="val-kembalian" style="font-size:20px; font-weight:700; color:#0f172a;">Rp 0</div>
                </div>

                <button onclick="bayarSekarang()"
                    style="width:100%; margin-top:20px; padding:12px; background:#059669; color:#fff; border:none; border-radius:8px; cursor:pointer; font-weight:600; font-family:'Poppins',sans-serif;">
                    <i class="fa fa-print"></i> Bayar & Cetak Nota
                </button>
                <button onclick="batalTransaksi()"
                    style="width:100%; margin-top:10px; padding:10px; background:#fff; color:#e11d48; border:1px solid #fda4af; border-radius:8px; cursor:pointer; font-weight:600; font-family:'Poppins',sans-serif;">
                    <i class="fa fa-trash"></i> Batalkan Transaksi
                </button>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
<script>
    const API_DATA_ANTRIAN = "{{ route('antrian.data') }}";
    const API_PROSES_BAYAR = "{{ route('antrian.bayar') }}";
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '{{ csrf_token() }}';

    let dataAntrianGlobal = [];
    let dataLayananMaster = [];

    let layananDipilih     = [];
    let totalLayananGlobal = 0;
    let totalObatGlobal    = 0;
    let totalAkhir         = 0;
    let pasienAktifData    = null;
    let namaPasienAktif    = "";
    let idPasienAktif      = "";
    let idPendaftaranAktif = "";

    // Inisialisasi awal aplikasi
    initAntrianApp();

    async function initAntrianApp() {
        const tbody = document.getElementById('tabel-antrian-pasien');
        tbody.innerHTML = `<tr><td colspan="4" style="text-align:center; padding:20px; color:#64748b;"><i class="fa fa-spinner fa-spin"></i> Memuat antrian dari sistem SIMRS...</td></tr>`;

        try {
            const response = await fetch(API_DATA_ANTRIAN, {
                headers: {
                    'Accept': 'application/json'
                }
            });
            const result = await response.json();

            if (result.success) {
                dataAntrianGlobal = result.antrian || [];
                dataLayananMaster = result.layanan || [];
                renderAntrianTable(dataAntrianGlobal);

                // Auto-pilih pasien jika ada parameter URL 'pilih' (dari Dashboard Kasir)
                const urlParams = new URLSearchParams(window.location.search);
                const idPilih = urlParams.get('pilih');
                if (idPilih) {
                    pilihPasien(idPilih);
                }
            } else {
                tbody.innerHTML = `<tr><td colspan="4" style="color:#ef4444; text-align:center; padding:20px;">Gagal mengambil antrian dari sistem: ${result.message || 'Error'}</td></tr>`;
            }
        } catch (err) {
            console.error("Gagal memuat antrian lokal:", err);
            tbody.innerHTML = `<tr><td colspan="4" style="color:#ef4444; text-align:center; padding:20px;">Gagal mengoneksikan data antrian sistem lokal.</td></tr>`;
        }
    }

    function renderAntrianTable(list) {
        const tbody = document.getElementById('tabel-antrian-pasien');

        if (!list || list.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" style="text-align:center; padding:25px; color:#94a3b8;"><i class="fa-solid fa-circle-check" style="color:#10b981; font-size:24px; display:block; margin-bottom:8px;"></i>Semua antrian pasien hari ini telah diselesaikan.</td></tr>`;
            return;
        }

        let rows = '';
        list.forEach(item => {
            const isMenungguKasir = item.status_antrian === 'menunggu_kasir';
            const statusBadge = isMenungguKasir
                ? `<span style="background:#dcfce7; color:#15803d; font-size:10px; font-weight:700; padding:2px 6px; border-radius:4px; margin-left:4px;">SIAP BAYAR</span>`
                : '';

            rows += `<tr>
                <td class="text-blue" style="font-weight:700;">${item.no_antrian}</td>
                <td>
                    <strong>${item.nama_pasien}</strong>
                    <div style="font-size:11px; color:#64748b;">${item.no_rm} ${statusBadge}</div>
                </td>
                <td class="text-poli" style="font-weight:500;">${item.nama_poli}</td>
                <td>
                    <button class="btn-pilih" onclick="pilihPasien(${item.id_pendaftaran})">
                        Pilih
                    </button>
                </td>
            </tr>`;
        });

        tbody.innerHTML = rows;
    }

    function pilihPasien(idPendaftaran) {
        const item = dataAntrianGlobal.find(a => String(a.id_pendaftaran) === String(idPendaftaran));
        if (!item) return;

        pasienAktifData    = item;
        namaPasienAktif    = item.nama_pasien;
        idPasienAktif      = item.no_rm;
        idPendaftaranAktif = item.id_pendaftaran;
        // Otomatis isi layanan & tindakan medis yang sudah diinput oleh dokter di poli
        layananDipilih     = (item.layanan_dokter && item.layanan_dokter.length > 0)
            ? item.layanan_dokter.map(l => ({ id: l.id, nama: l.nama, harga: parseInt(l.harga), sumber: l.sumber || 'dokter' }))
            : [];
        totalObatGlobal    = item.total_obat || 0;

        const displayIdResep = item.id_resep || "-";
        const biayaPerawat = 50000;

        let rowsObat = '';
        if (item.obat && item.obat.length > 0) {
            item.obat.forEach((o, index) => {
                rowsObat += `<tr>
                    <td>${index + 1}</td>
                    <td><strong>${o.nama_obat}</strong></td>
                    <td>${o.jumlah}</td>
                    <td>Rp ${o.subtotal.toLocaleString('id-ID')}</td>
                </tr>`;
            });
        } else {
            rowsObat = `<tr><td colspan="4" style="text-align:center; color:#94a3b8; padding:12px;">Tidak ada resep obat untuk kunjungan pasien ini.</td></tr>`;
        }

        document.getElementById('konten-transaksi').innerHTML = `
            <div class="section-title">1. DATA PASIEN</div>
            <div class="info-grid">
                <div class="info-item">Nama : <strong>${item.nama_pasien}</strong></div>
                <div class="info-item">Poli : <strong>${item.nama_poli}</strong></div>
                <div class="info-item">No. RM : <strong>${item.no_rm}</strong></div>
                <div class="info-item">No. Antrian : <strong style="color:#2563eb;">${item.no_antrian}</strong></div>
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:15px; margin-bottom:6px;">
                <div class="section-title" style="margin:0;">2. TINDAKAN MEDIS DOKTER (POLIKLINIK)</div>
                <span style="font-size:11px; background:#eff6ff; color:#2563eb; padding:3px 10px; border-radius:20px; font-weight:600; border:1px solid #bfdbfe;">
                    <i class="fa-solid fa-user-doctor"></i> Otomatis dari Rekam Medis Poli
                </span>
            </div>
            <table class="table-transaksi">
                <thead>
                    <tr>
                        <th style="width:40px;">NO.</th>
                        <th>NAMA TINDAKAN MEDIS</th>
                        <th>TARIF (RP)</th>
                        <th style="width:110px; text-align:center;">OTORISASI</th>
                    </tr>
                </thead>
                <tbody id="body-layanan-transaksi"></tbody>
            </table>

            <div class="section-title" style="margin-top:15px;">3. RESEP OBAT DOKTER (${displayIdResep})</div>
            <table class="table-transaksi">
                <thead>
                    <tr><th>NO.</th><th>NAMA OBAT</th><th>JML</th><th>SUBTOTAL</th></tr>
                </thead>
                <tbody id="body-layanan-transaksi-obat">
                    ${rowsObat}
                    <tr class="subtotal-row">
                        <td colspan="3" style="text-align:left; padding:10px;">Subtotal Obat Resep</td>
                        <td style="text-align:right; padding:10px; font-weight:700;">Rp ${totalObatGlobal.toLocaleString('id-ID')}</td>
                    </tr>
                </tbody>
            </table>

            <div class="section-title" style="margin-top:15px;">4. JASA MEDIS KLINIK</div>
            <table class="table-transaksi">
                <thead>
                    <tr>
                        <th>NO.</th>
                        <th>JASA</th>
                        <th>SUBTOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Jasa Pemeriksaan & Tindakan Keperawatan</td>
                        <td>Rp ${biayaPerawat.toLocaleString('id-ID')}</td>
                    </tr>
                </tbody>
            </table>

            <div class="info-note" style="margin-top:15px;">
                <i class="fa fa-circle-check" style="color:#10b981;"></i> Data otomatis sinkron dengan rekam medis dokter dan farmasi RS Delta.
            </div>
        `;

        updateTabelLayanan();
    }

    function updateTabelLayanan() {
        const tbody = document.getElementById('body-layanan-transaksi');
        totalLayananGlobal = 0;
        let rows = '';

        if (tbody) {
            if (layananDipilih.length > 0) {
                layananDipilih.forEach((item, index) => {
                    totalLayananGlobal += parseInt(item.harga);
                    rows += `<tr>
                        <td>${index + 1}</td>
                        <td><strong>${item.nama}</strong></td>
                        <td>Rp ${parseInt(item.harga).toLocaleString('id-ID')}</td>
                        <td style="text-align:center;">
                            <span style="background:#eff6ff; color:#2563eb; font-size:10px; font-weight:700; padding:3px 8px; border-radius:12px; border:1px solid #bfdbfe; display:inline-block;">
                                <i class="fa-solid fa-user-doctor"></i> DOKTER POLI
                            </span>
                        </td>
                    </tr>`;
                });
            } else {
                rows = `<tr><td colspan="4" style="text-align:center; color:#94a3b8; padding:15px;">Pemeriksaan rutin dokter poliklinik (tidak ada tindakan medis invasif).</td></tr>`;
            }

            tbody.innerHTML = rows + `<tr class="subtotal-row">
                <td colspan="2" style="text-align:left; padding:10px;">Subtotal Tindakan Medis Dokter</td>
                <td colspan="2" style="text-align:right; padding:10px; font-weight:700;">Rp ${totalLayananGlobal.toLocaleString('id-ID')}</td>
            </tr>`;
        }

        const jasaPerawatStatis = 50000;
        totalAkhir = totalLayananGlobal + totalObatGlobal + jasaPerawatStatis;

        document.getElementById('val-jasa-medis').innerText = 'Rp ' + jasaPerawatStatis.toLocaleString('id-ID');
        document.getElementById('val-layanan').innerText    = 'Rp ' + totalLayananGlobal.toLocaleString('id-ID');
        document.getElementById('val-obat').innerText       = 'Rp ' + totalObatGlobal.toLocaleString('id-ID');
        document.getElementById('val-total').innerText      = 'Rp ' + totalAkhir.toLocaleString('id-ID');
        document.getElementById('input-tunai').value        = '';
        document.getElementById('val-kembalian').innerText  = 'Rp 0';
    }

    function hapusLayanan(index) {
        layananDipilih.splice(index, 1);
        updateTabelLayanan();
    }

    function hitungKembalian() {
        const tunai = parseInt(document.getElementById('input-tunai').value) || 0;
        const kembalian = tunai - totalAkhir;
        document.getElementById('val-kembalian').innerText = 'Rp ' + (kembalian >= 0 ? kembalian.toLocaleString('id-ID') : '0');
    }

    async function bayarSekarang() {
        if (!namaPasienAktif || !idPendaftaranAktif) {
            alert("Silakan pilih pasien di daftar antrian terlebih dahulu!");
            return;
        }

        const tunai = parseInt(document.getElementById('input-tunai').value) || 0;
        if (tunai < totalAkhir) {
            alert(`Jumlah uang tunai tidak mencukupi! Total biaya adalah Rp ${totalAkhir.toLocaleString('id-ID')}`);
            return;
        }

        const payload = {
            id_pendaftaran: idPendaftaranAktif,
            id_layanan: layananDipilih.length > 0 ? (layananDipilih[0].id || null) : null,
            total_bayar: totalAkhir,
            total_layanan: totalLayananGlobal,
            total_resep: totalObatGlobal,
            jasa_perawat: 50000,
            jumlah_dibayar: tunai,
            metode_bayar: "TUNAI"
        };

        try {
            const response = await fetch(API_PROSES_BAYAR, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            if (response.ok && result.success) {
                const kembalian = tunai - totalAkhir;
                document.getElementById('isi-nota').innerHTML = `
                    <div class="nota-box" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px; margin-bottom:12px;">
                        <p style="margin:4px 0;"><strong>No. Invoice:</strong> <span style="color:#2563eb; font-weight:700;">${result.data.no_invoice}</span></p>
                        <p style="margin:4px 0;"><strong>Pasien:</strong> ${namaPasienAktif} (${idPasienAktif})</p>
                        <p style="margin:4px 0;"><strong>Total Transaksi:</strong> <span style="color:#059669; font-weight:700;">Rp ${totalAkhir.toLocaleString('id-ID')}</span></p>
                        <p style="margin:4px 0;"><strong>Uang Diterima:</strong> Rp ${tunai.toLocaleString('id-ID')}</p>
                        <p style="margin:4px 0;"><strong>Kembalian:</strong> Rp ${kembalian.toLocaleString('id-ID')}</p>
                    </div>
                    <div style="color: #065f46; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 10px; border-radius: 8px; text-align: center; font-weight: 600; font-size: 13px;">
                        <i class="fa fa-check-circle" style="color:#10b981;"></i> Pembayaran Berhasil Disimpan ke Sistem SIMRS
                    </div>
                `;
                document.getElementById('modal-nota').style.display = 'flex';
            } else {
                alert("Gagal memproses pembayaran: " + (result.message || "Terjadi kesalahan pada server."));
            }
        } catch (error) {
            console.error("Gagal mengirim transaksi:", error);
            alert("Terjadi kesalahan jaringan saat memproses transaksi pembayaran.");
        }
    }

    function tutupNota() {
        document.getElementById('modal-nota').style.display = 'none';
        batalTransaksi();
        initAntrianApp();
    }

    function batalTransaksi() {
        layananDipilih     = [];
        totalObatGlobal    = 0;
        totalLayananGlobal = 0;
        totalAkhir         = 0;
        pasienAktifData    = null;
        namaPasienAktif    = "";
        idPasienAktif      = "";
        idPendaftaranAktif = "";

        document.getElementById('konten-transaksi').innerHTML =
            '<p style="color:#94a3b8; font-size:13px;">Silakan pilih pasien pada antrian di samping untuk memulai transaksi.</p>';
        document.getElementById('val-layanan').innerText    = 'Rp 0';
        document.getElementById('val-obat').innerText       = 'Rp 0';
        document.getElementById('val-jasa-medis').innerText = 'Rp 0';
        document.getElementById('val-total').innerText      = 'Rp 0';
        document.getElementById('input-tunai').value        = '';
        document.getElementById('val-kembalian').innerText  = 'Rp 0';
    }
</script>
@endpush
