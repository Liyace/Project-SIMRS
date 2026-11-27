<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pasien;
use App\Models\Pendaftaran;
use App\Models\Poli;
use App\Models\RekamMedis;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminStatistikController extends Controller
{
    /**
     * Dashboard Ringkasan & Grafik Statistik Eksekutif RS Delta
     */
    public function index(Request $request)
    {
        $data = $this->getStatistikData();
        return view('admin.statistik.index', $data);
    }

    /**
     * Tampilkan Halaman Cetak Dokumen Formal Laporan Eksekutif RS Delta
     */
    public function cetakLaporan(Request $request)
    {
        $data = $this->getStatistikData();
        return view('admin.statistik.cetak', $data);
    }

    /**
     * Ambil data statistik klinis dan operasional RS Delta
     */
    protected function getStatistikData(): array
    {
        $bulanSekarang = Carbon::now()->month;
        $tahunSekarang = Carbon::now()->year;

        // 1. KPI Cards Utama (Bulan Ini)
        $totalKunjunganBulanIni = Pendaftaran::whereMonth('tanggal_daftar', $bulanSekarang)
            ->whereYear('tanggal_daftar', $tahunSekarang)
            ->count();

        $totalKunjunganHariIni = Pendaftaran::whereDate('tanggal_daftar', today())->count();

        $totalPendapatanBulanIni = Transaksi::where('status_pembayaran', 'lunas')
            ->whereMonth('created_at', $bulanSekarang)
            ->whereYear('created_at', $tahunSekarang)
            ->sum('total_bayar');

        $pasienBaruBulanIni = Pasien::whereMonth('created_at', $bulanSekarang)
            ->whereYear('created_at', $tahunSekarang)
            ->count();

        $totalPasienTerdaftar = Pasien::count();

        // 2. GRAFIK 1: Tren Kunjungan Harian (14 Hari Terakhir)
        $trenHariLabels = [];
        $trenHariValues = [];
        for ($i = 13; $i >= 0; $i--) {
            $tgl = Carbon::today()->subDays($i);
            $trenHariLabels[] = $tgl->format('d M');
            $jml = Pendaftaran::whereDate('tanggal_daftar', $tgl->toDateString())->count();
            $trenHariValues[] = $jml;
        }

        // 3. GRAFIK 2: Distribusi Pasien per Poliklinik (Bulan Ini)
        $poliStats = Poli::withCount(['pendaftarans' => function ($q) use ($bulanSekarang, $tahunSekarang) {
            $q->whereMonth('tanggal_daftar', $bulanSekarang)
              ->whereYear('tanggal_daftar', $tahunSekarang);
        }])->get();

        $poliLabels = $poliStats->pluck('nama_poli')->toArray();
        $poliValues = $poliStats->pluck('pendaftarans_count')->toArray();

        // 4. GRAFIK 3: Top 8 Diagnosa Penyakit Terbanyak (ICD-10)
        $topDiagnosa = RekamMedis::select('diagnosa_utama', 'kode_icd10', DB::raw('count(*) as total'))
            ->whereNotNull('diagnosa_utama')
            ->groupBy('diagnosa_utama', 'kode_icd10')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $diagnosaLabels = [];
        $diagnosaValues = [];
        foreach ($topDiagnosa as $d) {
            $label = $d->diagnosa_utama;
            if (!empty($d->kode_icd10)) {
                $label .= ' (' . $d->kode_icd10 . ')';
            }
            $diagnosaLabels[] = strlen($label) > 28 ? substr($label, 0, 26) . '..' : $label;
            $diagnosaValues[] = $d->total;
        }

        // 5. GRAFIK 4: Komposisi Cara Bayar (Umum vs BPJS vs Asuransi)
        $caraBayarStats = Pendaftaran::select('cara_bayar', DB::raw('count(*) as total'))
            ->groupBy('cara_bayar')
            ->get()
            ->pluck('total', 'cara_bayar')
            ->toArray();

        $caraBayarLabels = ['Pasien Umum', 'BPJS Kesehatan', 'Asuransi Swasta'];
        $caraBayarValues = [
            $caraBayarStats['umum'] ?? 0,
            $caraBayarStats['bpjs'] ?? 0,
            $caraBayarStats['asuransi'] ?? 0,
        ];

        // 6. GRAFIK 5: Tren Pendapatan 6 Bulan Terakhir
        $bulanLabels = [];
        $pendapatanValues = [];
        for ($m = 5; $m >= 0; $m--) {
            $date = Carbon::now()->subMonths($m);
            $bulanLabels[] = $date->translatedFormat('M Y');
            $sum = Transaksi::where('status_pembayaran', 'lunas')
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('total_bayar');
            $pendapatanValues[] = (float) $sum;
        }

        return [
            'totalKunjunganBulanIni'  => $totalKunjunganBulanIni,
            'totalKunjunganHariIni'    => $totalKunjunganHariIni,
            'totalPendapatanBulanIni' => $totalPendapatanBulanIni,
            'pasienBaruBulanIni'      => $pasienBaruBulanIni,
            'totalPasienTerdaftar'    => $totalPasienTerdaftar,
            'trenHariLabels'          => $trenHariLabels,
            'trenHariValues'          => $trenHariValues,
            'poliStats'               => $poliStats,
            'poliLabels'              => $poliLabels,
            'poliValues'              => $poliValues,
            'topDiagnosa'             => $topDiagnosa,
            'diagnosaLabels'          => $diagnosaLabels,
            'diagnosaValues'          => $diagnosaValues,
            'caraBayarStats'          => $caraBayarStats,
            'caraBayarLabels'         => $caraBayarLabels,
            'caraBayarValues'         => $caraBayarValues,
            'bulanLabels'             => $bulanLabels,
            'pendapatanValues'        => $pendapatanValues,
        ];
    }
}
