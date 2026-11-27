<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use App\Models\Poli;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ResepsionisController extends Controller
{
    /**
     * Dashboard Resepsionis: Monitoring End-to-End Pasien & Voice Caller
     */
    public function index(Request $request)
    {
        $tanggal = $request->query('tanggal', today()->toDateString());
        $tahap = $request->query('tahap', 'semua');
        $idPoli = $request->query('poli', 'semua');
        $q = trim($request->query('q', ''));

        $query = Pendaftaran::with([
            'pasien',
            'poli',
            'dokter',
            'screening',
            'rekamMedis',
            'resep.details.obat',
            'transaksi',
        ])
        ->whereDate('tanggal_daftar', $tanggal);

        // Filter Poli
        if ($idPoli !== 'semua' && !empty($idPoli)) {
            $query->where('id_poli', $idPoli);
        }

        // Filter Pencarian
        if (!empty($q)) {
            $query->where(function ($sub) use ($q) {
                $sub->where('no_antrian', 'like', "%{$q}%")
                    ->orWhere('no_pendaftaran', 'like', "%{$q}%")
                    ->orWhereHas('pasien', function ($p) use ($q) {
                        $p->where('nama_pasien', 'like', "%{$q}%")
                          ->orWhere('rm', 'like', "%{$q}%")
                          ->orWhere('nik', 'like', "%{$q}%");
                    });
            });
        }

        // Filter Tahap Antrean
        if ($tahap === 'skrining') {
            $query->where('status_antrian', 'menunggu_skrining');
        } elseif ($tahap === 'dokter') {
            $query->whereIn('status_antrian', ['menunggu_dokter', 'sedang_diperiksa']);
        } elseif ($tahap === 'kasir') {
            $query->where('status_antrian', 'menunggu_kasir');
        } elseif ($tahap === 'farmasi') {
            $query->where('status_antrian', 'menunggu_obat');
        } elseif ($tahap === 'selesai') {
            $query->where('status_antrian', 'selesai');
        }

        // Paginasi 10 pasien per halaman
        $pasienJourney = $query->orderBy('no_antrian', 'asc')
            ->paginate(10)
            ->withQueryString();

        // Olah Detail Status Journey Tiap Pasien pada Halaman Ini
        $pasienJourney->through(function ($p) {
            return $this->formatPatientJourney($p);
        });

        // Daftar pasien aktif hari ini untuk dropdown Panel Panggilan Suara (tanpa batas halaman)
        $pasienPanggilan = Pendaftaran::with(['pasien', 'poli', 'dokter'])
            ->whereDate('tanggal_daftar', $tanggal)
            ->whereNotIn('status_antrian', ['selesai', 'dibatalkan'])
            ->orderBy('no_antrian', 'asc')
            ->get()
            ->map(function ($p) {
                return $this->formatPatientJourney($p);
            });

        // Hitung Statistik Hari Ini
        $baseQuery = Pendaftaran::whereDate('tanggal_daftar', $tanggal);
        $totalTerdaftar = (clone $baseQuery)->count();
        $totalSkrining = (clone $baseQuery)->where('status_antrian', 'menunggu_skrining')->count();
        $totalDokter = (clone $baseQuery)->whereIn('status_antrian', ['menunggu_dokter', 'sedang_diperiksa'])->count();
        $totalKasir = (clone $baseQuery)->where('status_antrian', 'menunggu_kasir')->count();
        $totalFarmasi = (clone $baseQuery)->where('status_antrian', 'menunggu_obat')->count();
        $totalSelesai = (clone $baseQuery)->where('status_antrian', 'selesai')->count();

        $daftarPoli = Poli::all();
        $panggilanTerbaru = Cache::get('simrs_antrean_panggilan_terbaru');

        return view('resepsionis.index', compact(
            'pasienJourney',
            'pasienPanggilan',
            'tanggal',
            'tahap',
            'idPoli',
            'q',
            'totalTerdaftar',
            'totalSkrining',
            'totalDokter',
            'totalKasir',
            'totalFarmasi',
            'totalSelesai',
            'daftarPoli',
            'panggilanTerbaru'
        ));
    }

    /**
     * Tampilan Layar Display TV Mode CCTV (Mini Preview dengan Panel Keterangan)
     */
    public function layarTv(Request $request)
    {
        $tanggal = today()->toDateString();
        $baseQuery = Pendaftaran::whereDate('tanggal_daftar', $tanggal);

        $totalTerdaftar = (clone $baseQuery)->count();
        $totalSkrining = (clone $baseQuery)->where('status_antrian', 'menunggu_skrining')->count();
        $totalDokter = (clone $baseQuery)->whereIn('status_antrian', ['menunggu_dokter', 'sedang_diperiksa'])->count();
        $totalKasir = (clone $baseQuery)->where('status_antrian', 'menunggu_kasir')->count();
        $totalFarmasi = (clone $baseQuery)->where('status_antrian', 'menunggu_obat')->count();
        $totalSelesai = (clone $baseQuery)->where('status_antrian', 'selesai')->count();

        $panggilanTerbaru = Cache::get('simrs_antrean_panggilan_terbaru');
        $riwayatPanggilan = Cache::get('simrs_antrean_riwayat_panggilan', []);

        // Daftar pasien aktif hari ini untuk panggil cepat langsung dari layar CCTV
        $pasienPanggilan = Pendaftaran::with(['pasien', 'poli', 'dokter'])
            ->whereDate('tanggal_daftar', $tanggal)
            ->whereNotIn('status_antrian', ['selesai', 'dibatalkan'])
            ->orderBy('no_antrian', 'asc')
            ->get()
            ->map(function ($p) {
                return $this->formatPatientJourney($p);
            });

        return view('resepsionis.layar-tv', compact(
            'totalTerdaftar',
            'totalSkrining',
            'totalDokter',
            'totalKasir',
            'totalFarmasi',
            'totalSelesai',
            'panggilanTerbaru',
            'riwayatPanggilan',
            'pasienPanggilan'
        ));
    }

    /**
     * Endpoint Data Realtime Live Polling untuk Resepsionis
     */
    public function dataJourney(Request $request)
    {
        $tanggal = $request->query('tanggal', today()->toDateString());

        $pasienList = Pendaftaran::with([
            'pasien',
            'poli',
            'dokter',
            'screening',
            'rekamMedis',
            'resep',
            'transaksi',
        ])
        ->whereDate('tanggal_daftar', $tanggal)
        ->orderBy('no_antrian', 'asc')
        ->get();

        $journey = $pasienList->map(function ($p) {
            return $this->formatPatientJourney($p);
        });

        return response()->json([
            'success' => true,
            'count'   => $journey->count(),
            'data'    => $journey,
        ]);
    }

    /**
     * Trigger Panggilan Suara Antrean RS (Voice Caller + Chime)
     */
    public function panggilAntrean(Request $request)
    {
        $validated = $request->validate([
            'no_antrian'  => 'required|string',
            'nama_pasien' => 'required|string',
            'tujuan'      => 'required|string',
        ]);

        $noAntrian = strtoupper(trim($validated['no_antrian']));
        $namaPasien = trim($validated['nama_pasien']);
        $tujuan = trim($validated['tujuan']);

        // Format ucapan Bahasa Indonesia yang alami, sopan, dan jelas
        $huruf = preg_replace('/[^A-Za-z]/', '', $noAntrian);
        $angka = preg_replace('/[^0-9]/', '', $noAntrian);

        $mapAngka = [
            '0' => 'kosong', '1' => 'satu', '2' => 'dua', '3' => 'tiga', '4' => 'empat',
            '5' => 'lima', '6' => 'enam', '7' => 'tujuh', '8' => 'delapan', '9' => 'sembilan',
        ];
        $angkaKata = [];
        foreach (str_split($angka) as $d) {
            $angkaKata[] = $mapAngka[$d] ?? $d;
        }

        $hurufSpelled = implode(', ', str_split($huruf));
        $angkaSpelled = implode(', ', $angkaKata);

        $speechText = "Nomor antrean, {$hurufSpelled}, {$angkaSpelled}. {$namaPasien}. Silakan menuju ke {$tujuan}.";

        $panggilan = [
            'id_panggilan' => uniqid('CALL-'),
            'no_antrian'   => $noAntrian,
            'nama_pasien'  => $namaPasien,
            'tujuan'       => $tujuan,
            'suara_teks'   => $speechText,
            'dipanggil_oleh'=> auth()->user()->nama ?? 'Resepsionis',
            'waktu'        => now()->format('H:i:s'),
            'timestamp'    => now()->timestamp,
        ];

        // Simpan ke Cache agar Layar Display TV menangkap panggilan realtime
        Cache::put('simrs_antrean_panggilan_terbaru', $panggilan, 60);

        // Simpan riwayat panggilan terakhir (maksimal 8 panggilan)
        $riwayat = Cache::get('simrs_antrean_riwayat_panggilan', []);
        array_unshift($riwayat, $panggilan);
        $riwayat = array_slice($riwayat, 0, 8);
        Cache::put('simrs_antrean_riwayat_panggilan', $riwayat, 3600 * 12);

        return response()->json([
            'success'   => true,
            'message'   => "Panggilan antrean {$noAntrian} berhasil disiarkan!",
            'panggilan' => $panggilan,
        ]);
    }

    /**
     * Endpoint Status Panggilan Terkini (Polling oleh TV Display)
     */
    public function statusPanggilan()
    {
        $panggilan = Cache::get('simrs_antrean_panggilan_terbaru');

        return response()->json([
            'has_call'  => !is_null($panggilan),
            'panggilan' => $panggilan,
        ]);
    }

    /**
     * Endpoint Audio Suara Alami Bahasa Indonesia (TTS Stream Jernih)
     */
    public function streamSuara(Request $request)
    {
        $teks = trim($request->query('q', ''));
        if (empty($teks)) {
            return response()->noContent();
        }

        $teks = mb_substr($teks, 0, 250);
        $cacheKey = 'tts_audio_' . md5($teks);

        $audioData = Cache::remember($cacheKey, 86400 * 7, function () use ($teks) {
            $url = 'https://translate.google.com/translate_tts?ie=UTF-8&tl=id&client=tw-ob&q=' . urlencode($teks);

            $ctx = stream_context_create([
                'http' => [
                    'timeout' => 5,
                    'header'  => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n",
                ],
            ]);

            $content = @file_get_contents($url, false, $ctx);
            return $content ?: null;
        });

        if (!$audioData) {
            return response()->json(['error' => 'Audio tidak tersedia'], 404);
        }

        return response($audioData, 200, [
            'Content-Type'   => 'audio/mpeg',
            'Content-Length' => strlen($audioData),
            'Cache-Control'  => 'public, max-age=604800',
            'Accept-Ranges'  => 'bytes',
        ]);
    }

    /**
     * Halaman Publik Display Monitor TV Ruang Tunggu RS Delta
     */
    public function displayTv(Request $request)
    {
        $tanggal = today()->toDateString();

        // 1. Antrean Sedang Dilayani di tiap Pos
        $posSkrining = Pendaftaran::with(['pasien', 'poli'])
            ->whereDate('tanggal_daftar', $tanggal)
            ->where('status_antrian', 'menunggu_skrining')
            ->orderBy('no_antrian', 'asc')
            ->limit(5)
            ->get();

        $posDokter = Pendaftaran::with(['pasien', 'poli', 'dokter'])
            ->whereDate('tanggal_daftar', $tanggal)
            ->whereIn('status_antrian', ['menunggu_dokter', 'sedang_diperiksa'])
            ->orderBy('no_antrian', 'asc')
            ->limit(5)
            ->get();

        $posKasir = Pendaftaran::with(['pasien', 'poli'])
            ->whereDate('tanggal_daftar', $tanggal)
            ->where('status_antrian', 'menunggu_kasir')
            ->orderBy('no_antrian', 'asc')
            ->limit(5)
            ->get();

        $posFarmasi = Pendaftaran::with(['pasien', 'poli', 'resep'])
            ->whereDate('tanggal_daftar', $tanggal)
            ->where('status_antrian', 'menunggu_obat')
            ->orderBy('no_antrian', 'asc')
            ->limit(5)
            ->get();

        $panggilanTerbaru = Cache::get('simrs_antrean_panggilan_terbaru');

        return view('monitoring.display', compact(
            'posSkrining',
            'posDokter',
            'posKasir',
            'posFarmasi',
            'panggilanTerbaru'
        ));
    }

    /**
     * Helper Formatter Tahap Journey Pasien
     */
    private function formatPatientJourney($p)
    {
        $status = $p->status_antrian;

        // Tentukan Index Tahap (1: Loket, 2: TTV, 3: Dokter, 4: Kasir, 5: Farmasi, 6: Selesai)
        $stepIndex = 1;
        $stepLabel = 'Admisi Loket';
        $badgeClass = 'badge-loket';
        $badgeText = 'Terdaftar';
        $posisiAktif = 'Loket Admisi RS';
        $iconClass = 'fa-id-card';

        if ($status === 'menunggu_skrining') {
            $stepIndex = 2;
            $stepLabel = 'Skrining TTV Perawat';
            $badgeClass = 'badge-skrining';
            $badgeText = 'Menunggu TTV';
            $posisiAktif = 'Ruang Skrining Perawat';
            $iconClass = 'fa-heart-pulse';
        } elseif ($status === 'menunggu_dokter') {
            $stepIndex = 3;
            $stepLabel = 'Pemeriksaan Dokter';
            $badgeClass = 'badge-dokter';
            $badgeText = 'Menunggu Dokter';
            $posisiAktif = ($p->poli?->nama_poli ?? 'Poliklinik') . ' (' . ($p->dokter?->nama ?? 'dr. Jaga') . ')';
            $iconClass = 'fa-user-doctor';
        } elseif ($status === 'sedang_diperiksa') {
            $stepIndex = 3;
            $stepLabel = 'Pemeriksaan Dokter';
            $badgeClass = 'badge-dokter-active';
            $badgeText = 'Sedang Diperiksa';
            $posisiAktif = ($p->poli?->nama_poli ?? 'Poliklinik') . ' (' . ($p->dokter?->nama ?? 'dr. Jaga') . ')';
            $iconClass = 'fa-stethoscope';
        } elseif ($status === 'menunggu_kasir') {
            $stepIndex = 4;
            $stepLabel = 'Kasir & Pembayaran';
            $badgeClass = 'badge-kasir';
            $badgeText = 'Menunggu Kasir';
            $posisiAktif = 'Loket Kasir Pembayaran';
            $iconClass = 'fa-cash-register';
        } elseif ($status === 'menunggu_obat') {
            $stepIndex = 5;
            $stepLabel = 'Pengambilan Obat';
            $badgeClass = 'badge-farmasi';
            $badgeText = 'Menunggu Obat';
            $posisiAktif = 'Instalasi Farmasi / Apotek';
            $iconClass = 'fa-prescription-bottle-medical';
        } elseif ($status === 'selesai') {
            $stepIndex = 6;
            $stepLabel = 'Pelayanan Selesai';
            $badgeClass = 'badge-selesai';
            $badgeText = 'Selesai';
            $posisiAktif = 'Tuntas Pelayanan';
            $iconClass = 'fa-circle-check';
        } elseif ($status === 'dibatalkan') {
            $stepIndex = 0;
            $stepLabel = 'Dibatalkan';
            $badgeClass = 'badge-batal';
            $badgeText = 'Dibatalkan';
            $posisiAktif = 'Batal';
            $iconClass = 'fa-circle-xmark';
        }

        return (object) [
            'id_pendaftaran' => $p->id_pendaftaran,
            'no_pendaftaran' => $p->no_pendaftaran,
            'no_antrian'     => $p->no_antrian,
            'nama_pasien'    => $p->pasien?->nama_pasien ?? 'Pasien Umum',
            'rm'             => $p->pasien?->rm ?? '-',
            'nik'            => $p->pasien?->nik ?? '-',
            'jenis_kelamin'  => $p->pasien?->jenis_kelamin ?? 'L',
            'cara_bayar'     => strtoupper($p->cara_bayar ?? 'UMUM'),
            'nama_poli'      => $p->poli?->nama_poli ?? 'Poli Umum',
            'nama_dokter'    => $p->dokter?->nama ?? 'dr. Umum',
            'waktu_daftar'   => $p->created_at ? $p->created_at->format('H:i') : '-',
            'status_antrian' => $status,
            'step_index'     => $stepIndex,
            'step_label'     => $stepLabel,
            'badge_class'    => $badgeClass,
            'badge_text'     => $badgeText,
            'posisi_aktif'   => $posisiAktif,
            'icon_class'     => $iconClass,
            'has_resep'      => !is_null($p->resep),
            'has_transaksi'  => !is_null($p->transaksi),
            'is_lunas'       => $p->transaksi && $p->transaksi->status_pembayaran === 'lunas',
        ];
    }

    /**
     * Polling Notifikasi Real-time Pengawasan Antrean Resepsionis
     */
    public function getNotifikasi()
    {
        $today = today()->toDateString();
        $baseQuery = Pendaftaran::whereDate('tanggal_daftar', $today);

        $totalTerdaftar = (clone $baseQuery)->count();
        $totalSkrining = (clone $baseQuery)->where('status_antrian', 'menunggu_skrining')->count();
        $totalDokter = (clone $baseQuery)->whereIn('status_antrian', ['menunggu_dokter', 'sedang_diperiksa'])->count();
        $totalKasir = (clone $baseQuery)->where('status_antrian', 'menunggu_kasir')->count();
        $totalFarmasi = (clone $baseQuery)->where('status_antrian', 'menunggu_obat')->count();
        $totalSelesai = (clone $baseQuery)->where('status_antrian', 'selesai')->count();

        // Deteksi potensi bottleneck / penumpukan (> 5 antrean belum tertangani)
        $bottlenecks = [];
        if ($totalSkrining >= 5) {
            $bottlenecks[] = [
                'pos'    => 'Skrining Perawat',
                'count'  => $totalSkrining,
                'pesan'  => "Penumpukan {$totalSkrining} pasien menunggu skrining TTV",
                'level'  => 'warning',
            ];
        }
        if ($totalDokter >= 5) {
            $bottlenecks[] = [
                'pos'    => 'Pemeriksaan Dokter',
                'count'  => $totalDokter,
                'pesan'  => "Penumpukan {$totalDokter} pasien menunggu pemeriksaan dokter",
                'level'  => 'warning',
            ];
        }
        if ($totalKasir >= 5) {
            $bottlenecks[] = [
                'pos'    => 'Loket Kasir',
                'count'  => $totalKasir,
                'pesan'  => "Penumpukan {$totalKasir} pasien menunggu pembayaran tagihan",
                'level'  => 'warning',
            ];
        }
        if ($totalFarmasi >= 5) {
            $bottlenecks[] = [
                'pos'    => 'Farmasi Obat',
                'count'  => $totalFarmasi,
                'pesan'  => "Penumpukan {$totalFarmasi} resep obat menunggu penyerahan",
                'level'  => 'warning',
            ];
        }

        $panggilanTerbaru = Cache::get('simrs_antrean_panggilan_terbaru');

        return response()->json([
            'success'          => true,
            'total_terdaftar'  => $totalTerdaftar,
            'total_skrining'   => $totalSkrining,
            'total_dokter'     => $totalDokter,
            'total_kasir'      => $totalKasir,
            'total_farmasi'    => $totalFarmasi,
            'total_selesai'    => $totalSelesai,
            'bottlenecks'      => $bottlenecks,
            'total_peringatan' => count($bottlenecks),
            'panggilan_terbaru'=> $panggilanTerbaru,
            'timestamp'        => now()->timestamp,
        ]);
    }
}
