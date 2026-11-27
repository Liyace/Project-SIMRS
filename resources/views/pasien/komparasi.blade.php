<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Komparasi Riwayat Berobat Pasien - RS Delta</title>
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
    @include('partials.navbar-pasien', ['active' => 'komparasi'])

    <!-- MAIN CONTAINER -->
    <main class="max-w-6xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 flex-1 space-y-6">

        <!-- TITLE & DROPDOWN PEMILIH KOMPARASI -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-[#e2edfa]">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-100">
                <div>
                    <span class="text-xs font-semibold px-3 py-1 rounded-full bg-blue-50 text-[#1a4a8d] border border-blue-200">
                        Evaluasi Progres Kesehatan Pribadi
                    </span>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-800 mt-2">
                        Perbandingan Riwayat Berobat Saya Sendiri
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Membandingkan perkembangan kondisi medis, tensi, keluhan, dan resep obat antara kunjungan terdahulu dengan kunjungan terkini Anda sendiri di RS Delta.
                    </p>
                </div>
                <div class="bg-blue-50/80 border border-blue-200 rounded-2xl px-4 py-3 text-right hidden sm:block">
                    <span class="text-[11px] text-slate-500 block">Pasien (Diri Sendiri):</span>
                    <span class="text-sm font-bold text-[#1a4a8d]">{{ $pasien->nama_pasien }}</span>
                    <span class="text-[10px] text-slate-400 block font-mono">No. RM: {{ $pasien->rm }}</span>
                </div>
            </div>

            <!-- FORM SELEKSI 2 KUNJUNGAN PRIBADI -->
            <form action="{{ route('pasien.komparasi') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end mt-6">
                <div class="md:col-span-5 space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#1a4a8d] inline-block mr-1"></span> Kunjungan Terkini / Terbaru (Diri Sendiri):
                    </label>
                    <select name="kunjungan_a" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-medium focus:ring-2 focus:ring-blue-400 bg-white">
                        @foreach ($daftarKunjungan as $k)
                            <option value="{{ $k->id_pendaftaran }}" {{ $idA == $k->id_pendaftaran ? 'selected' : '' }}>
                                {{ $k->no_antrian }} • {{ $k->poli->nama_poli ?? 'Poli' }} ({{ $k->tanggal_daftar ? $k->tanggal_daftar->format('d M Y') : '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-5 space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">
                        <span class="w-2.5 h-2.5 rounded-full bg-purple-600 inline-block mr-1"></span> Kunjungan Sebelumnya / Lampau (Diri Sendiri):
                    </label>
                    <select name="kunjungan_b" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-medium focus:ring-2 focus:ring-purple-400 bg-white">
                        @foreach ($daftarKunjungan as $k)
                            <option value="{{ $k->id_pendaftaran }}" {{ $idB == $k->id_pendaftaran ? 'selected' : '' }}>
                                {{ $k->no_antrian }} • {{ $k->poli->nama_poli ?? 'Poli' }} ({{ $k->tanggal_daftar ? $k->tanggal_daftar->format('d M Y') : '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <button type="submit" 
                            class="w-full py-2.5 px-4 rounded-xl bg-[#1a4a8d] hover:bg-[#143869] text-white text-xs font-bold shadow transition flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-code-compare"></i> Bandingkan
                    </button>
                </div>
            </form>
        </div>

        @if ($kunjunganA && $kunjunganB)
            @php
                $ttvA = $kunjunganA->screeningAwal;
                $ttvB = $kunjunganB->screeningAwal;
            @endphp

            @if ($ttvA && $ttvB)
                <!-- RINGKASAN PERKEMBANGAN KESEHATAN PRIBADI -->
                <div class="bg-white rounded-3xl p-5 border border-[#e2edfa] shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg flex-shrink-0">
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-slate-800 block">Evaluasi Perubahan Tubuh Pribadi</span>
                            <span class="text-[11px] text-slate-500">Perbandingan riwayat lampau &rarr; riwayat terkini Anda</span>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 text-xs font-semibold">
                        @php
                            $sistolDiff = $ttvA->sistol - $ttvB->sistol;
                        @endphp
                        <div class="px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="text-slate-400 text-[10px] block">Perubahan Tensi:</span>
                            <span class="{{ $sistolDiff <= 0 ? 'text-emerald-600' : 'text-amber-600' }}">
                                {{ $sistolDiff > 0 ? "+{$sistolDiff}" : $sistolDiff }} mmHg
                                ({{ $ttvB->sistol }} &rarr; {{ $ttvA->sistol }})
                            </span>
                        </div>

                        @if($ttvA->berat_badan && $ttvB->berat_badan)
                            @php $bbDiff = round($ttvA->berat_badan - $ttvB->berat_badan, 1); @endphp
                            <div class="px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200">
                                <span class="text-slate-400 text-[10px] block">Berat Badan:</span>
                                <span class="text-slate-700">
                                    {{ $bbDiff > 0 ? "+{$bbDiff}" : $bbDiff }} kg
                                    ({{ $ttvB->berat_badan }} &rarr; {{ $ttvA->berat_badan }})
                                </span>
                            </div>
                        @endif

                        @php $suhuDiff = round($ttvA->suhu_tubuh - $ttvB->suhu_tubuh, 1); @endphp
                        <div class="px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="text-slate-400 text-[10px] block">Suhu Tubuh:</span>
                            <span class="{{ $suhuDiff <= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $suhuDiff > 0 ? "+{$suhuDiff}" : $suhuDiff }} °C
                                ({{ $ttvB->suhu_tubuh }} &rarr; {{ $ttvA->suhu_tubuh }})
                            </span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- TABEL / CARD SIDE-BY-SIDE KOMPARASI -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- KOLOM KUNJUNGAN TERKINI (DIRI SENDIRI) -->
                <div class="space-y-6">
                    <div class="bg-gradient-to-r from-[#1a4a8d] to-[#20559f] text-white rounded-3xl p-6 shadow-md border border-blue-400/30">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[10px] font-extrabold uppercase tracking-widest px-2.5 py-0.5 rounded-full bg-white/20 text-white">
                                    Kunjungan Terkini Anda
                                </span>
                                <h2 class="text-xl font-black mt-2">{{ $kunjunganA->no_antrian }} • {{ $kunjunganA->poli->nama_poli ?? 'Poli' }}</h2>
                                <p class="text-xs text-blue-100 mt-0.5">{{ $kunjunganA->tanggal_daftar ? $kunjunganA->tanggal_daftar->format('d F Y • H:i') : '-' }} WITA</p>
                            </div>
                            <a href="{{ route('pasien.riwayat.detail', $kunjunganA->id_pendaftaran) }}" target="_blank"
                               class="px-3 py-1.5 rounded-xl bg-white text-[#1a4a8d] font-bold text-xs hover:bg-blue-50 transition shadow">
                                Buka Rekam Medis
                            </a>
                        </div>
                    </div>

                    <!-- 1. TANDA VITAL KUNJUNGAN A -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-[#e2edfa] space-y-3">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-[#1a4a8d] flex items-center gap-2">
                            <i class="fa-solid fa-heart-pulse text-rose-500"></i> Tanda-Tanda Vital (TTV)
                        </h3>
                        @if ($ttvA = $kunjunganA->screeningAwal)
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                                    <span class="text-[10px] text-slate-400 block">Tekanan Darah:</span>
                                    <span class="text-base font-bold text-slate-800">{{ $ttvA->sistol }}/{{ $ttvA->diastol }} mmHg</span>
                                </div>
                                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                                    <span class="text-[10px] text-slate-400 block">Suhu Tubuh:</span>
                                    <span class="text-base font-bold text-slate-800">{{ $ttvA->suhu_tubuh }} °C</span>
                                </div>
                                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                                    <span class="text-[10px] text-slate-400 block">Denyut Nadi:</span>
                                    <span class="text-base font-bold text-slate-800">{{ $ttvA->denyut_nadi ?? '-' }} bpm</span>
                                </div>
                                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                                    <span class="text-[10px] text-slate-400 block">Berat Badan:</span>
                                    <span class="text-base font-bold text-slate-800">{{ $ttvA->berat_badan ?? '-' }} kg</span>
                                </div>
                            </div>
                        @else
                            <div class="p-4 text-center text-xs text-slate-400 bg-slate-50 rounded-2xl">Data TTV belum dicatat</div>
                        @endif
                    </div>

                    <!-- 2. DIAGNOSA DOKTER KUNJUNGAN A -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-[#e2edfa] space-y-3">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-[#1a4a8d] flex items-center gap-2">
                            <i class="fa-solid fa-user-doctor text-blue-500"></i> Diagnosa & Keluhan Klinis
                        </h3>
                        @if ($rmA = $kunjunganA->rekamMedis)
                            <div class="space-y-2 text-xs">
                                <div class="p-3 bg-blue-50/60 rounded-2xl border border-blue-100">
                                    <span class="text-[10px] font-bold text-[#1a4a8d] block uppercase">Diagnosa Utama (ICD-10)</span>
                                    <span class="font-bold text-slate-800 text-sm mt-0.5 block">{{ $rmA->diagnosa_utama }}</span>
                                    @if ($rmA->kode_icd10)
                                        <span class="font-mono text-[10px] font-bold text-blue-700 bg-white px-2 py-0.5 rounded border border-blue-200 mt-1 inline-block">
                                            Kode: {{ $rmA->kode_icd10 }}
                                        </span>
                                    @endif
                                </div>
                                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                                    <span class="text-[10px] text-slate-400 block font-medium">Anamnesis / Keluhan:</span>
                                    <p class="text-slate-700 mt-0.5">{{ $rmA->anamnesis ?? ($kunjunganA->keluhan_awal ?? '-') }}</p>
                                </div>
                                @if ($rmA->catatan_edukasi)
                                    <div class="p-3 bg-amber-50/60 rounded-2xl border border-amber-100 text-amber-900">
                                        <span class="text-[10px] font-bold block uppercase">Instruksi Dokter:</span>
                                        <p class="mt-0.5">{{ $rmA->catatan_edukasi }}</p>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="p-4 text-center text-xs text-slate-400 bg-slate-50 rounded-2xl">Belum ada diagnosa dokter</div>
                        @endif
                    </div>

                    <!-- 3. RESEP OBAT KUNJUNGAN A -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-[#e2edfa] space-y-3">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-[#1a4a8d] flex items-center gap-2">
                            <i class="fa-solid fa-pills text-emerald-500"></i> Resep & Terapi Obat
                        </h3>
                        @if ($kunjunganA->resep && $kunjunganA->resep->details->count() > 0)
                            <div class="divide-y divide-slate-100 border border-slate-100 rounded-2xl overflow-hidden text-xs">
                                @foreach ($kunjunganA->resep->details as $item)
                                    <div class="p-3 bg-slate-50/50 flex justify-between items-center">
                                        <div>
                                            <span class="font-bold text-slate-800">{{ $item->obat->nama_obat ?? 'Obat' }} (x{{ $item->jumlah }})</span>
                                            <span class="text-[11px] text-emerald-700 block font-medium">{{ $item->aturan_pakai }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-4 text-center text-xs text-slate-400 bg-slate-50 rounded-2xl">Tidak ada resep obat</div>
                        @endif
                    </div>
                </div>

                <!-- KOLOM KUNJUNGAN SEBELUMNYA (DIRI SENDIRI) -->
                <div class="space-y-6">
                    <div class="bg-gradient-to-r from-purple-700 to-indigo-800 text-white rounded-3xl p-6 shadow-md border border-purple-400/30">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[10px] font-extrabold uppercase tracking-widest px-2.5 py-0.5 rounded-full bg-white/20 text-white">
                                    Kunjungan Sebelumnya Anda
                                </span>
                                <h2 class="text-xl font-black mt-2">{{ $kunjunganB->no_antrian }} • {{ $kunjunganB->poli->nama_poli ?? 'Poli' }}</h2>
                                <p class="text-xs text-purple-100 mt-0.5">{{ $kunjunganB->tanggal_daftar ? $kunjunganB->tanggal_daftar->format('d F Y • H:i') : '-' }} WITA</p>
                            </div>
                            <a href="{{ route('pasien.riwayat.detail', $kunjunganB->id_pendaftaran) }}" target="_blank"
                               class="px-3 py-1.5 rounded-xl bg-white text-purple-800 font-bold text-xs hover:bg-purple-50 transition shadow">
                                Buka Rekam Medis
                            </a>
                        </div>
                    </div>

                    <!-- 1. TANDA VITAL KUNJUNGAN B -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-[#e2edfa] space-y-3">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-purple-700 flex items-center gap-2">
                            <i class="fa-solid fa-heart-pulse text-rose-500"></i> Tanda-Tanda Vital (TTV)
                        </h3>
                        @if ($ttvB = $kunjunganB->screeningAwal)
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                                    <span class="text-[10px] text-slate-400 block">Tekanan Darah:</span>
                                    <span class="text-base font-bold text-slate-800">{{ $ttvB->sistol }}/{{ $ttvB->diastol }} mmHg</span>
                                </div>
                                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                                    <span class="text-[10px] text-slate-400 block">Suhu Tubuh:</span>
                                    <span class="text-base font-bold text-slate-800">{{ $ttvB->suhu_tubuh }} °C</span>
                                </div>
                                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                                    <span class="text-[10px] text-slate-400 block">Denyut Nadi:</span>
                                    <span class="text-base font-bold text-slate-800">{{ $ttvB->denyut_nadi ?? '-' }} bpm</span>
                                </div>
                                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                                    <span class="text-[10px] text-slate-400 block">Berat Badan:</span>
                                    <span class="text-base font-bold text-slate-800">{{ $ttvB->berat_badan ?? '-' }} kg</span>
                                </div>
                            </div>
                        @else
                            <div class="p-4 text-center text-xs text-slate-400 bg-slate-50 rounded-2xl">Data TTV belum dicatat</div>
                        @endif
                    </div>

                    <!-- 2. DIAGNOSA DOKTER KUNJUNGAN B -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-[#e2edfa] space-y-3">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-purple-700 flex items-center gap-2">
                            <i class="fa-solid fa-user-doctor text-purple-600"></i> Diagnosa & Keluhan Klinis
                        </h3>
                        @if ($rmB = $kunjunganB->rekamMedis)
                            <div class="space-y-2 text-xs">
                                <div class="p-3 bg-purple-50/60 rounded-2xl border border-purple-100">
                                    <span class="text-[10px] font-bold text-purple-800 block uppercase">Diagnosa Utama (ICD-10)</span>
                                    <span class="font-bold text-slate-800 text-sm mt-0.5 block">{{ $rmB->diagnosa_utama }}</span>
                                    @if ($rmB->kode_icd10)
                                        <span class="font-mono text-[10px] font-bold text-purple-700 bg-white px-2 py-0.5 rounded border border-purple-200 mt-1 inline-block">
                                            Kode: {{ $rmB->kode_icd10 }}
                                        </span>
                                    @endif
                                </div>
                                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                                    <span class="text-[10px] text-slate-400 block font-medium">Anamnesis / Keluhan:</span>
                                    <p class="text-slate-700 mt-0.5">{{ $rmB->anamnesis ?? ($kunjunganB->keluhan_awal ?? '-') }}</p>
                                </div>
                                @if ($rmB->catatan_edukasi)
                                    <div class="p-3 bg-amber-50/60 rounded-2xl border border-amber-100 text-amber-900">
                                        <span class="text-[10px] font-bold block uppercase">Instruksi Dokter:</span>
                                        <p class="mt-0.5">{{ $rmB->catatan_edukasi }}</p>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="p-4 text-center text-xs text-slate-400 bg-slate-50 rounded-2xl">Belum ada diagnosa dokter</div>
                        @endif
                    </div>

                    <!-- 3. RESEP OBAT KUNJUNGAN B -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-[#e2edfa] space-y-3">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-purple-700 flex items-center gap-2">
                            <i class="fa-solid fa-pills text-emerald-500"></i> Resep & Terapi Obat
                        </h3>
                        @if ($kunjunganB->resep && $kunjunganB->resep->details->count() > 0)
                            <div class="divide-y divide-slate-100 border border-slate-100 rounded-2xl overflow-hidden text-xs">
                                @foreach ($kunjunganB->resep->details as $item)
                                    <div class="p-3 bg-slate-50/50 flex justify-between items-center">
                                        <div>
                                            <span class="font-bold text-slate-800">{{ $item->obat->nama_obat ?? 'Obat' }} (x{{ $item->jumlah }})</span>
                                            <span class="text-[11px] text-emerald-700 block font-medium">{{ $item->aturan_pakai }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-4 text-center text-xs text-slate-400 bg-slate-50 rounded-2xl">Tidak ada resep obat</div>
                        @endif
                    </div>
                </div>

            </div>
        @else
            <div class="bg-white rounded-3xl p-12 text-center border border-[#e2edfa] shadow-sm">
                <i class="fa-solid fa-code-compare text-4xl text-slate-300 mb-3 block"></i>
                <h3 class="text-base font-bold text-slate-700">Pilih 2 Kunjungan untuk Dibandingkan</h3>
                <p class="text-xs text-slate-400 mt-1">Gunakan formulir pemilih di atas untuk memilih riwayat berobat yang ingin Anda evaluasi.</p>
            </div>
        @endif

    </main>

    <!-- FOOTER -->
    <footer class="py-5 text-center text-xs text-slate-400 border-t border-slate-200/60 bg-white">
        © 2026 Rumah Sakit Delta • Sistem Informasi Manajemen Rumah Sakit Terpadu
    </footer>

</body>
</html>
