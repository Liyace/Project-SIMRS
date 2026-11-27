<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Rekam Medis Pasien - Rumah Sakit Delta</title>
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

    <!-- MAIN CONTAINER -->
    <main class="max-w-6xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 flex-1 space-y-6">

        <!-- HEADER & STATISTIK PASIEN -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-[#e2edfa] flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <span class="text-xs font-semibold px-3 py-1 rounded-full bg-blue-50 text-[#1a4a8d] border border-blue-200">
                    Arsip Rekam Medis Elektronik (RME)
                </span>
                <h1 class="text-2xl font-bold text-slate-800 mt-2">
                    Riwayat Seluruh Kunjungan Berobat
                </h1>
                <p class="text-xs text-slate-500 mt-1">
                    Catatan lengkap dari pemeriksaan perawat, dokter, apotek farmasi, hingga billing kasir.
                </p>
            </div>

            <!-- ACTION: BANDINGKAN REKAM MEDIS -->
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('pasien.komparasi') }}" 
                   class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-[#1a4a8d] hover:bg-[#143869] text-white font-bold text-xs shadow-lg transition">
                    <i class="fa-solid fa-code-compare"></i>
                    <span>Bandingkan Riwayat Pengobatan</span>
                </a>
            </div>
        </div>

        <!-- 3 KPI SUMMARY CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl p-5 border border-[#e2edfa] shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-[#1a4a8d] flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fa-solid fa-hospital-user"></i>
                </div>
                <div>
                    <span class="text-[11px] text-slate-400 block font-medium">Total Kunjungan Terdaftar</span>
                    <span class="text-xl font-bold text-slate-800">{{ $totalKunjungan }} Kali Berobat</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-[#e2edfa] shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <span class="text-[11px] text-slate-400 block font-medium">Pelayanan Selesai & Lunas</span>
                    <span class="text-xl font-bold text-slate-800">{{ $totalSelesai }} Kunjungan</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-[#e2edfa] shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fa-solid fa-heart-pulse"></i>
                </div>
                <div>
                    <span class="text-[11px] text-slate-400 block font-medium">Tanda Vital Terakhir</span>
                    <span class="text-xl font-bold text-slate-800">
                        @if($kunjunganTerakhir && $kunjunganTerakhir->screeningAwal)
                            {{ $kunjunganTerakhir->screeningAwal->sistol }}/{{ $kunjunganTerakhir->screeningAwal->diastol }} <span class="text-xs font-normal text-slate-500">mmHg</span>
                        @else
                            -
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- FILTER & PENCARIAN -->
        <div class="bg-white rounded-2xl p-5 border border-[#e2edfa] shadow-sm">
            <form action="{{ route('pasien.riwayat') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3">
                <div class="relative flex-1 w-full">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="q" value="{{ $q }}" 
                           placeholder="Cari keluhan, diagnosa ICD-10, poliklinik, atau nama dokter..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-400 focus:outline-none">
                </div>

                <div class="w-full sm:w-64">
                    <select name="poli" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-400 bg-white focus:outline-none">
                        <option value="">-- Semua Poliklinik --</option>
                        @foreach ($polis as $poli)
                            <option value="{{ $poli->id_poli }}" {{ $filterPoli == $poli->id_poli ? 'selected' : '' }}>
                                {{ $poli->nama_poli }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button type="submit" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-[#1a4a8d] hover:bg-[#143869] text-white text-xs font-bold transition">
                        Filter
                    </button>
                    @if ($q || $filterPoli)
                        <a href="{{ route('pasien.riwayat') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- DAFTAR KUNJUNGAN PASIEN -->
        <div class="space-y-4">
            @forelse ($riwayatList as $rw)
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-[#e2edfa] hover:border-blue-300 transition duration-200">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="text-sm font-black text-[#1a4a8d] px-3 py-1 rounded-xl bg-blue-50 border border-blue-200">
                                {{ $rw->no_antrian }}
                            </span>
                            <span class="text-xs font-semibold text-slate-500">
                                {{ $rw->no_pendaftaran }}
                            </span>
                            <span class="text-xs font-semibold text-slate-400">•</span>
                            <span class="text-xs font-bold text-slate-800">
                                {{ $rw->poli->nama_poli ?? 'Poliklinik' }}
                            </span>
                            <span class="text-xs font-semibold text-slate-400">•</span>
                            <span class="text-xs text-slate-500">
                                {{ $rw->tanggal_daftar ? $rw->tanggal_daftar->format('d F Y • H:i') : '-' }} WITA
                            </span>
                        </div>

                        <div>
                            @php
                                $rst = $rw->status_antrian;
                                $badgeBg = 'bg-emerald-100 text-emerald-800';
                                $badgeLabel = 'Pelayanan Selesai';
                                if ($rst === 'menunggu_skrining') { $badgeBg = 'bg-amber-100 text-amber-800'; $badgeLabel = 'Menunggu Skrining'; }
                                elseif (in_array($rst, ['menunggu_dokter', 'sedang_diperiksa'])) { $badgeBg = 'bg-blue-100 text-blue-800'; $badgeLabel = 'Pemeriksaan Dokter'; }
                                elseif ($rst === 'menunggu_kasir') { $badgeBg = 'bg-purple-100 text-purple-800'; $badgeLabel = 'Menuju Kasir'; }
                                elseif ($rst === 'menunggu_obat') { $badgeBg = 'bg-cyan-100 text-cyan-800'; $badgeLabel = 'Menunggu Obat'; }
                                elseif ($rst === 'dibatalkan') { $badgeBg = 'bg-rose-100 text-rose-800'; $badgeLabel = 'Dibatalkan'; }
                            @endphp
                            <span class="text-xs font-bold px-3 py-1 rounded-full {{ $badgeBg }}">
                                {{ $badgeLabel }}
                            </span>
                        </div>
                    </div>

                    <!-- RINGKASAN MEDIS KUNJUNGAN -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 py-4 text-xs">
                        
                        <!-- DOKTER & KELUHAN -->
                        <div class="space-y-1">
                            <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Dokter Pemeriksa</span>
                            <span class="font-bold text-slate-800 block">{{ $rw->dokter->nama ?? 'Dokter Bertugas' }}</span>
                            <p class="text-slate-500 text-[11px] truncate" title="{{ $rw->keluhan_awal }}">
                                Keluhan: {{ $rw->keluhan_awal ?? '-' }}
                            </p>
                        </div>

                        <!-- TANDA VITAL (TTV) -->
                        <div class="space-y-1">
                            <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Tanda Vital (TTV)</span>
                            @if ($rw->screeningAwal)
                                <span class="font-semibold text-slate-800 block">TD: {{ $rw->screeningAwal->sistol }}/{{ $rw->screeningAwal->diastol }} mmHg</span>
                                <span class="text-slate-500 text-[11px] block">Suhu: {{ $rw->screeningAwal->suhu_tubuh }}°C • Nadi: {{ $rw->screeningAwal->denyut_nadi ?? '-' }} bpm</span>
                            @else
                                <span class="text-slate-400 italic">Belum diskrining</span>
                            @endif
                        </div>

                        <!-- DIAGNOSA ICD-10 -->
                        <div class="space-y-1">
                            <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Diagnosa Medis</span>
                            @if ($rw->rekamMedis)
                                <div class="flex items-center gap-1.5">
                                    @if ($rw->rekamMedis->kode_icd10)
                                        <span class="font-mono text-[10px] font-bold px-1.5 py-0.5 rounded bg-blue-50 text-[#1a4a8d] border border-blue-200">
                                            {{ $rw->rekamMedis->kode_icd10 }}
                                        </span>
                                    @endif
                                    <span class="font-semibold text-slate-800 truncate" title="{{ $rw->rekamMedis->diagnosa_utama }}">
                                        {{ $rw->rekamMedis->diagnosa_utama }}
                                    </span>
                                </div>
                            @else
                                <span class="text-slate-400 italic">Menunggu diagnosa</span>
                            @endif
                        </div>

                        <!-- BILLING & RESEP -->
                        <div class="space-y-1">
                            <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Status Pembayaran</span>
                            @if ($rw->transaksi && $rw->transaksi->status_pembayaran === 'lunas')
                                <span class="font-bold text-emerald-700 block">
                                    Rp {{ number_format($rw->transaksi->total_bayar, 0, ',', '.') }}
                                    <span class="text-[10px] font-normal uppercase bg-emerald-50 px-2 py-0.5 rounded text-emerald-700 border border-emerald-200">Lunas</span>
                                </span>
                            @else
                                <span class="text-slate-400 italic">Belum ada transaksi</span>
                            @endif
                        </div>

                    </div>

                    <!-- FOOTER AKSI -->
                    <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-3 text-xs text-slate-500">
                            @if($rw->resep && $rw->resep->details->count() > 0)
                                <span><i class="fa-solid fa-pills text-emerald-600 mr-1"></i> {{ $rw->resep->details->count() }} Macam Obat</span>
                            @endif
                            @if($rw->rekamMedis)
                                <span><i class="fa-solid fa-file-waveform text-blue-600 mr-1"></i> Resume Medis Tersedia</span>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('pasien.komparasi', ['kunjungan_a' => $rw->id_pendaftaran]) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition">
                                <i class="fa-solid fa-code-compare"></i> Bandingkan
                            </a>
                            <a href="{{ route('pasien.riwayat.detail', $rw->id_pendaftaran) }}"
                               class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-xl bg-[#1a4a8d] hover:bg-[#143869] text-white font-bold text-xs shadow-sm transition">
                                <span>Detail Rekam Medis</span>
                                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>

                </div>
            @empty
                <div class="bg-white rounded-3xl p-12 text-center border border-[#e2edfa] shadow-sm">
                    <i class="fa-solid fa-folder-open text-4xl text-slate-300 mb-3 block"></i>
                    <h3 class="text-base font-bold text-slate-700">Belum Ada Riwayat Kunjungan</h3>
                    <p class="text-xs text-slate-400 mt-1">Anda belum memiliki riwayat pemeriksaan atau kata kunci pencarian tidak ditemukan.</p>
                </div>
            @endforelse
        </div>

        <!-- PAGINATION -->
        <div class="pt-2">
            {{ $riwayatList->links() }}
        </div>

    </main>

    <!-- FOOTER -->
    <footer class="py-5 text-center text-xs text-slate-400 border-t border-slate-200/60 bg-white">
        © 2026 Rumah Sakit Delta • Sistem Informasi Manajemen Rumah Sakit Terpadu
    </footer>

</body>
</html>
