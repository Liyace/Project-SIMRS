<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanController extends Controller
{
    /**
     * Nama-nama bulan dalam Bahasa Indonesia
     */
    public static array $namaBulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    /**
     * Tampilan utama Laporan Kasir dengan filter Harian, Bulanan, dan Tahunan
     */
    public function index(Request $request)
    {
        $laporan = $this->getLaporanData($request);

        return view('laporan.index', array_merge($laporan, [
            'daftarBulan' => self::$namaBulan,
        ]));
    }

    /**
     * Halaman cetak / export PDF resmi Rumah Sakit Delta
     */
    public function cetak(Request $request)
    {
        $laporan = $this->getLaporanData($request);
        $kasirLogin = auth()->user();

        return view('laporan.cetak', array_merge($laporan, [
            'kasirLogin' => $kasirLogin,
        ]));
    }

    /**
     * Export spreadsheet Excel (.xls / .xlsx) berformat rapi untuk akuntansi
     */
    public function exportExcel(Request $request)
    {
        $laporan = $this->getLaporanData($request);
        $kasirLogin = auth()->user();

        $periode = $laporan['periode'];
        $suffix = match ($periode) {
            'bulanan' => self::$namaBulan[$laporan['bulan']] . '_' . $laporan['tahun'],
            'tahunan' => 'Tahun_' . $laporan['tahun'],
            default   => date('Ymd', strtotime($laporan['tanggal'])),
        };

        $filename = "Laporan_Kasir_RS_Delta_{$periode}_{$suffix}.xls";

        return response()->view('laporan.excel', array_merge($laporan, [
            'kasirLogin' => $kasirLogin,
        ]))
        ->header('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
        ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
    }

    /**
     * Helper sentralisasi query filter & agregasi data laporan
     */
    private function getLaporanData(Request $request): array
    {
        $periode = $request->query('periode', 'harian');
        if (!in_array($periode, ['harian', 'bulanan', 'tahunan'])) {
            $periode = 'harian';
        }

        $tanggal = $request->query('tanggal', today()->toDateString());
        $bulan   = (int) $request->query('bulan', (int) date('n'));
        $tahun   = (int) $request->query('tahun', (int) date('Y'));

        if ($bulan < 1 || $bulan > 12) {
            $bulan = (int) date('n');
        }
        if ($tahun < 2020 || $tahun > 2035) {
            $tahun = (int) date('Y');
        }

        $query = Transaksi::with(['pendaftaran.pasien', 'pendaftaran.poli', 'layanan', 'kasir']);

        if ($periode === 'bulanan') {
            $query->where(function ($q) use ($bulan, $tahun) {
                $q->whereYear('waktu_pembayaran', $tahun)->whereMonth('waktu_pembayaran', $bulan)
                  ->orWhere(function ($sub) use ($bulan, $tahun) {
                      $sub->whereNull('waktu_pembayaran')
                          ->whereYear('created_at', $tahun)->whereMonth('created_at', $bulan);
                  });
            });
            $labelPeriode = 'Bulan ' . self::$namaBulan[$bulan] . ' ' . $tahun;
            $subTitlePeriode = 'Rekapitulasi Penerimaan Kasir Bulan ' . self::$namaBulan[$bulan] . ' ' . $tahun;
        } elseif ($periode === 'tahunan') {
            $query->where(function ($q) use ($tahun) {
                $q->whereYear('waktu_pembayaran', $tahun)
                  ->orWhere(function ($sub) use ($tahun) {
                      $sub->whereNull('waktu_pembayaran')
                          ->whereYear('created_at', $tahun);
                  });
            });
            $labelPeriode = 'Tahun ' . $tahun;
            $subTitlePeriode = 'Rekapitulasi Penerimaan Kasir Tahun ' . $tahun;
        } else {
            // Harian (default)
            $query->where(function ($q) use ($tanggal) {
                $q->whereDate('waktu_pembayaran', $tanggal)
                  ->orWhere(function ($sub) use ($tanggal) {
                      $sub->whereNull('waktu_pembayaran')
                          ->whereDate('created_at', $tanggal);
                  });
            });
            $tglObj = Carbon::parse($tanggal);
            $labelPeriode = $tglObj->translatedFormat('d F Y');
            $subTitlePeriode = 'Rekapitulasi Penerimaan Kasir Harian (' . $tglObj->format('d/m/Y') . ')';
        }

        $data = $query->orderBy('created_at', 'desc')->get();

        $totalPendapatan  = (float) $data->sum('total_bayar');
        $totalTransaksi   = $data->count();
        $totalTunai       = (float) $data->filter(fn($t) => strtoupper($t->metode_bayar ?? 'TUNAI') === 'TUNAI')->sum('total_bayar');
        $totalNonTunai    = (float) $data->filter(fn($t) => strtoupper($t->metode_bayar ?? '') !== 'TUNAI')->sum('total_bayar');
        $totalResep       = (float) $data->sum('total_resep');
        $totalLayanan     = (float) $data->sum('total_layanan');
        $totalJasaPerawat = (float) $data->sum('jasa_perawat');

        return compact(
            'data',
            'periode',
            'tanggal',
            'bulan',
            'tahun',
            'labelPeriode',
            'subTitlePeriode',
            'totalPendapatan',
            'totalTransaksi',
            'totalTunai',
            'totalNonTunai',
            'totalResep',
            'totalLayanan',
            'totalJasaPerawat'
        );
    }
}
