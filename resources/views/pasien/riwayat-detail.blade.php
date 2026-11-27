<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Rekam Medis Kunjungan {{ $pendaftaran->no_antrian }} - RS Delta</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logodelta1.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Roboto+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * { font-family: 'Poppins', sans-serif; }
        .font-mono { font-family: 'Roboto Mono', monospace; }
    </style>
</head>
<body class="bg-[#f0f6fd] min-h-screen text-slate-700 flex flex-col">

    <!-- NAVBAR TERPADU PASIEN -->
    @include('partials.navbar-pasien', ['active' => 'riwayat'])

    <!-- MAIN CONTENT -->
    <main class="max-w-6xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 flex-1 space-y-6">

        <!-- HEADER KUNJUNGAN -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-[#e2edfa] flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2.5">
                    <span class="text-xs font-black uppercase tracking-wider px-3 py-1 rounded-full bg-blue-50 text-[#1a4a8d] border border-blue-200">
                        {{ $pendaftaran->no_antrian }}
                    </span>
                    <span class="text-xs font-semibold px-3 py-1 rounded-full bg-slate-100 text-slate-700">
                        {{ $pendaftaran->no_pendaftaran }}
                    </span>
                    @php
                        $st = $pendaftaran->status_antrian;
                        $stBg = 'bg-emerald-100 text-emerald-800';
                        $stText = 'Pelayanan Selesai';
                        if ($st === 'menunggu_skrining') { $stBg = 'bg-amber-100 text-amber-800'; $stText = 'Menunggu Skrining'; }
                        elseif ($st === 'menunggu_dokter') { $stBg = 'bg-blue-100 text-blue-800'; $stText = 'Menunggu Dokter'; }
                        elseif ($st === 'sedang_diperiksa') { $stBg = 'bg-indigo-100 text-indigo-800'; $stText = 'Sedang Diperiksa'; }
                        elseif ($st === 'menunggu_kasir') { $stBg = 'bg-purple-100 text-purple-800'; $stText = 'Menuju Kasir'; }
                        elseif ($st === 'menunggu_obat') { $stBg = 'bg-cyan-100 text-cyan-800'; $stText = 'Menunggu Obat Farmasi'; }
                        elseif ($st === 'dibatalkan') { $stBg = 'bg-rose-100 text-rose-800'; $stText = 'Dibatalkan'; }
                    @endphp
                    <span class="text-xs font-bold px-3 py-1 rounded-full {{ $stBg }}">
                        {{ $stText }}
                    </span>
                </div>

                <h1 class="text-xl sm:text-2xl font-bold text-slate-800">
                    Rekam Pelayanan Medis: {{ $pendaftaran->poli->nama_poli ?? 'Poliklinik' }}
                </h1>

                <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500">
                    <span><i class="fa-regular fa-calendar text-[#1a4a8d] mr-1"></i> {{ $pendaftaran->tanggal_daftar ? $pendaftaran->tanggal_daftar->format('d F Y • H:i') : '-' }} WITA</span>
                    <span>•</span>
                    <span><i class="fa-solid fa-user-doctor text-[#1a4a8d] mr-1"></i> {{ $pendaftaran->dokter->nama ?? 'Dokter Bertugas' }}</span>
                    <span>•</span>
                    <span><i class="fa-solid fa-credit-card text-[#1a4a8d] mr-1"></i> Penjamin: <strong class="uppercase text-slate-700">{{ $pendaftaran->cara_bayar }}</strong></span>
                </div>
            </div>

            <!-- AKSI CEPAT DOKUMEN -->
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('pasien.komparasi', ['kunjungan_a' => $pendaftaran->id_pendaftaran]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow transition">
                    <i class="fa-solid fa-code-compare"></i>
                    <span>Bandingkan Riwayat</span>
                </a>

                @if ($pendaftaran->rekamMedis)
                    <a href="{{ route('pasien.riwayat.resume-medis', $pendaftaran->id_pendaftaran) }}" target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-[#1a4a8d] hover:bg-[#143869] text-white font-bold text-xs shadow transition">
                        <i class="fa-solid fa-file-medical"></i>
                        <span>Resume Medis (PDF)</span>
                    </a>
                @endif

                @if ($pendaftaran->transaksi && $pendaftaran->transaksi->status_pembayaran === 'lunas')
                    <a href="{{ route('pasien.riwayat.kuitansi', $pendaftaran->id_pendaftaran) }}" target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow transition">
                        <i class="fa-solid fa-receipt"></i>
                        <span>Kuitansi Kasir</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- GRID 4 PILAR PELAYANAN MEDIS RS DELTA -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

            <!-- PILAR 1: SKRINING TANDA VITAL PERAWAT -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-[#e2edfa] space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-heart-pulse"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-800">1. Skrining Tanda Vital (TTV)</h2>
                            <p class="text-xs text-slate-400">Pemeriksaan fisik awal perawat</p>
                        </div>
                    </div>
                    @if ($pendaftaran->screeningAwal)
                        <span class="text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                            <i class="fa-solid fa-check mr-1"></i> Terverifikasi
                        </span>
                    @else
                        <span class="text-[11px] font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full border border-amber-200">
                            Menunggu Skrining
                        </span>
                    @endif
                </div>

                @if ($screening = $pendaftaran->screeningAwal)
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 text-center">
                            <span class="text-[10px] text-slate-400 block font-medium">Tekanan Darah</span>
                            <span class="text-base font-bold text-slate-800 block mt-0.5">{{ $screening->sistol }}/{{ $screening->diastol }}</span>
                            <span class="text-[9px] text-slate-400">mmHg</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 text-center">
                            <span class="text-[10px] text-slate-400 block font-medium">Suhu Tubuh</span>
                            <span class="text-base font-bold text-slate-800 block mt-0.5">{{ $screening->suhu_tubuh }}</span>
                            <span class="text-[9px] text-slate-400">°Celsius</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 text-center">
                            <span class="text-[10px] text-slate-400 block font-medium">Denyut Nadi</span>
                            <span class="text-base font-bold text-slate-800 block mt-0.5">{{ $screening->denyut_nadi ?? '-' }}</span>
                            <span class="text-[9px] text-slate-400">kali/menit</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 text-center">
                            <span class="text-[10px] text-slate-400 block font-medium">Berat / Tinggi</span>
                            <span class="text-base font-bold text-slate-800 block mt-0.5">{{ $screening->berat_badan ?? '-' }}<span class="text-xs font-normal"> kg</span></span>
                            <span class="text-[9px] text-slate-400">{{ $screening->tinggi_badan ?? '-' }} cm</span>
                        </div>
                    </div>

                    @if ($screening->catatan_perawat)
                        <div class="p-3.5 rounded-2xl bg-blue-50/60 border border-blue-100 text-xs text-slate-700">
                            <strong class="text-[#1a4a8d] block mb-1">Catatan Skrining Perawat:</strong>
                            <p class="leading-relaxed">{{ $screening->catatan_perawat }}</p>
                        </div>
                    @endif

                    <div class="text-[11px] text-slate-400 flex items-center justify-between pt-1">
                        <span>Pemeriksa: <strong class="text-slate-600">{{ $screening->perawat->nama ?? 'Perawat Bertugas' }}</strong></span>
                        <span>Diperiksa: {{ $screening->created_at ? $screening->created_at->format('H:i') : '-' }} WITA</span>
                    </div>
                @else
                    <div class="py-8 text-center text-xs text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                        <i class="fa-solid fa-hourglass-half text-2xl text-slate-300 mb-2 block"></i>
                        Pasien belum menjalani skrining vital signs perawat untuk kunjungan ini.
                    </div>
                @endif
            </div>

            <!-- PILAR 2: PEMERIKSAAN & DIAGNOSA DOKTER -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-[#e2edfa] space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-blue-50 text-[#1a4a8d] flex items-center justify-center text-lg">
                            <i class="fa-solid fa-stethoscope"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-800">2. Diagnosa & Rekam Medis Dokter</h2>
                            <p class="text-xs text-slate-400">{{ $pendaftaran->dokter->nama ?? 'Dokter Poliklinik' }}</p>
                        </div>
                    </div>
                    @if ($pendaftaran->rekamMedis)
                        <span class="text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                            <i class="fa-solid fa-check mr-1"></i> Diperiksa
                        </span>
                    @else
                        <span class="text-[11px] font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full border border-amber-200">
                            Menunggu Dokter
                        </span>
                    @endif
                </div>

                @if ($rm = $pendaftaran->rekamMedis)
                    <div class="space-y-3 text-xs">
                        <!-- ANAMNESIS / KELUHAN -->
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                            <span class="text-slate-400 text-[10px] font-semibold uppercase tracking-wider block mb-1">Anamnesis / Keluhan Klinis</span>
                            <p class="font-medium text-slate-800 leading-relaxed">{{ $rm->anamnesis ?? ($pendaftaran->keluhan_awal ?? 'Tidak ada catatan keluhan.') }}</p>
                        </div>

                        <!-- DIAGNOSA UTAMA ICD-10 -->
                        <div class="p-3.5 rounded-2xl bg-blue-50/70 border border-blue-200/80">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-[#1a4a8d] text-[10px] font-bold uppercase tracking-wider">Diagnosa Utama (ICD-10)</span>
                                @if ($rm->kode_icd10)
                                    <span class="font-mono text-xs font-bold px-2 py-0.5 rounded-lg bg-[#1a4a8d] text-white">
                                        {{ $rm->kode_icd10 }}
                                    </span>
                                @endif
                            </div>
                            <h4 class="text-sm font-bold text-slate-800 mt-1">{{ $rm->diagnosa_utama }}</h4>
                            @if ($rm->diagnosa_sekunder)
                                <p class="text-[11px] text-slate-500 mt-1">Diagnosa Sekunder: {{ $rm->diagnosa_sekunder }}</p>
                            @endif
                        </div>

                        <!-- TINDAKAN MEDIS -->
                        @if ($rm->tindakanMedis && $rm->tindakanMedis->count() > 0)
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-400 text-[10px] font-semibold uppercase tracking-wider block mb-1.5">Tindakan Medis yang Diberikan</span>
                                <ul class="space-y-1">
                                    @foreach ($rm->tindakanMedis as $tm)
                                        <li class="flex items-center justify-between py-1 border-b border-slate-200/50 last:border-0">
                                            <span class="font-medium text-slate-800">{{ $tm->layanan->nama_layanan ?? 'Tindakan Medis' }} (x{{ $tm->qty }})</span>
                                            <span class="text-slate-500">Rp {{ number_format($tm->subtotal, 0, ',', '.') }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- EDUKASI DOKTER -->
                        @if ($rm->catatan_edukasi)
                            <div class="p-3.5 rounded-2xl bg-amber-50/60 border border-amber-200 text-amber-950">
                                <strong class="block text-amber-900 text-[11px] mb-1"><i class="fa-solid fa-lightbulb mr-1"></i> Edukasi & Rencana Tindak Lanjut Dokter:</strong>
                                <p class="leading-relaxed">{{ $rm->catatan_edukasi }}</p>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="py-8 text-center text-xs text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                        <i class="fa-solid fa-user-doctor text-2xl text-slate-300 mb-2 block"></i>
                        Pemeriksaan klinis dokter belum diinput atau sedang berlangsung di ruang periksa.
                    </div>
                @endif
            </div>

            <!-- PILAR 3: E-RESEP & FARMASI OBAT -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-[#e2edfa] space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-pills"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-800">3. Resep & Obat Farmasi</h2>
                            <p class="text-xs text-slate-400">Instruksi pemakaian obat dari instalasi farmasi</p>
                        </div>
                    </div>
                    @if ($pendaftaran->resep)
                        @php
                            $rst = $pendaftaran->resep->status_resep;
                            $rstBg = ($rst === 'selesai' || $rst === 'diserahkan') ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-cyan-50 text-cyan-700 border-cyan-200';
                            $rstLabel = ($rst === 'selesai' || $rst === 'diserahkan') ? 'Obat Diserahkan' : 'Sedang Diracik';
                        @endphp
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full border {{ $rstBg }}">
                            {{ $rstLabel }}
                        </span>
                    @else
                        <span class="text-[11px] font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full">
                            Tidak Ada Resep
                        </span>
                    @endif
                </div>

                @if ($resep = $pendaftaran->resep)
                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-xs text-slate-500 px-1">
                            <span>No. Resep: <strong class="font-mono text-slate-800">{{ $resep->no_resep }}</strong></span>
                            <span>Apoteker: <strong class="text-slate-800">{{ $resep->apoteker->nama ?? 'Apt. Instalasi Farmasi' }}</strong></span>
                        </div>

                        <!-- DAFTAR ITEM OBAT -->
                        <div class="divide-y divide-slate-100 rounded-2xl border border-slate-100 overflow-hidden">
                            @forelse ($resep->details as $item)
                                <div class="p-3.5 bg-slate-50/50 hover:bg-slate-50 transition flex items-start justify-between gap-3 text-xs">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-slate-800">{{ $item->obat->nama_obat ?? 'Item Obat' }}</span>
                                            <span class="text-[10px] px-2 py-0.5 rounded-md bg-white border border-slate-200 text-slate-500 font-medium">
                                                {{ $item->jumlah }} {{ $item->obat->satuan ?? 'tab' }}
                                            </span>
                                        </div>
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 font-semibold text-[11px]">
                                            <i class="fa-solid fa-clock-rotate-left text-emerald-600"></i>
                                            <span>Aturan Pakai: <strong>{{ $item->aturan_pakai ?? 'Sesuai petunjuk dokter' }}</strong></span>
                                        </div>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <span class="text-slate-500 block">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="p-4 text-center text-xs text-slate-400">
                                    Detail item obat belum ditambahkan.
                                </div>
                            @endforelse
                        </div>

                        @if ($resep->catatan_dokter)
                            <div class="p-3 rounded-xl bg-slate-50 text-[11px] text-slate-600 border border-slate-200/80">
                                <strong class="text-slate-800">Catatan Khusus Dokter:</strong> {{ $resep->catatan_dokter }}
                            </div>
                        @endif
                    </div>
                @else
                    <div class="py-8 text-center text-xs text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                        <i class="fa-solid fa-prescription-bottle-medical text-2xl text-slate-300 mb-2 block"></i>
                        Kunjungan ini tidak memerlukan peresepan obat atau belum diterbitkan oleh dokter.
                    </div>
                @endif
            </div>

            <!-- PILAR 4: BILLING & KUITANSI KASIR -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-[#e2edfa] space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-cash-register"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-800">4. Pembayaran & Kuitansi Kasir</h2>
                            <p class="text-xs text-slate-400">Rincian administrasi dan pelunasan biaya</p>
                        </div>
                    </div>
                    @if ($pendaftaran->transaksi && $pendaftaran->transaksi->status_pembayaran === 'lunas')
                        <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                            <i class="fa-solid fa-circle-check mr-1"></i> LUNAS
                        </span>
                    @else
                        <span class="text-[11px] font-bold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-full border border-amber-200">
                            Belum Lunas
                        </span>
                    @endif
                </div>

                @if ($trx = $pendaftaran->transaksi)
                    <div class="space-y-3 text-xs">
                        <div class="flex items-center justify-between py-1 border-b border-slate-100">
                            <span class="text-slate-400">Nomor Invoice:</span>
                            <span class="font-mono font-bold text-slate-800">{{ $trx->no_invoice }}</span>
                        </div>
                        <div class="flex items-center justify-between py-1 border-b border-slate-100">
                            <span class="text-slate-400">Metode Pembayaran:</span>
                            <span class="font-bold uppercase text-[#1a4a8d]">{{ $trx->metode_bayar ?? 'TUNAI' }}</span>
                        </div>
                        <div class="flex items-center justify-between py-1 border-b border-slate-100">
                            <span class="text-slate-400">Waktu Pembayaran:</span>
                            <span class="font-medium text-slate-700">{{ $trx->waktu_pembayaran ? $trx->waktu_pembayaran->format('d/m/Y H:i') : '-' }} WITA</span>
                        </div>

                        <!-- RINCIAN TAGIHAN -->
                        <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100 space-y-2 mt-2">
                            <div class="flex justify-between text-slate-600">
                                <span>Biaya Layanan & Tindakan Poli:</span>
                                <span class="font-semibold text-slate-800">Rp {{ number_format($trx->total_layanan, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span>Biaya Resep Obat Farmasi:</span>
                                <span class="font-semibold text-slate-800">Rp {{ number_format($trx->total_resep, 0, ',', '.') }}</span>
                            </div>
                            @if ($trx->jasa_perawat > 0)
                                <div class="flex justify-between text-slate-600">
                                    <span>Jasa Asuhan Keperawatan:</span>
                                    <span class="font-semibold text-slate-800">Rp {{ number_format($trx->jasa_perawat, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="pt-2 border-t border-slate-200 flex justify-between text-sm font-bold text-[#1a4a8d]">
                                <span>Total Tagihan:</span>
                                <span>Rp {{ number_format($trx->total_bayar, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="pt-2 flex justify-between items-center">
                            <span class="text-[11px] text-slate-400">Kasir: <strong class="text-slate-600">{{ $trx->kasir->nama ?? 'Kasir RS' }}</strong></span>
                            <a href="{{ route('pasien.riwayat.kuitansi', $pendaftaran->id_pendaftaran) }}" target="_blank"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-purple-50 text-purple-700 hover:bg-purple-100 font-bold text-xs border border-purple-200 transition">
                                <i class="fa-solid fa-print"></i> Cetak Kuitansi
                            </a>
                        </div>
                    </div>
                @else
                    <div class="py-8 text-center text-xs text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                        <i class="fa-solid fa-receipt text-2xl text-slate-300 mb-2 block"></i>
                        Tagihan administrasi dan kuitansi pembayaran belum dibuat oleh kasir.
                    </div>
                @endif
            </div>

        </div>

    </main>

    <!-- FOOTER -->
    <footer class="py-5 text-center text-xs text-slate-400 border-t border-slate-200/60 bg-white">
        © 2026 Rumah Sakit Delta • Sistem Informasi Manajemen Rumah Sakit Terpadu
    </footer>

</body>
</html>
