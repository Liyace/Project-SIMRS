<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Tampilkan Dashboard Kasir dengan data harian, akumulasi bulanan,
     * serta daftar antrean pasien yang belum bayar beserta aksi kasir.
     */
    public function index(Request $request)
    {
        // Proteksi: Alihkan staf non-kasir ke modul kerja masing-masing jika membuka /dashboard
        $roleAktif = strtolower(session('role_aktif', auth()->user()->role ?? 'kasir'));
        if (in_array($roleAktif, ['admin', 'administrator'])) {
            return redirect()->route('admin.pengguna');
        }
        if ($roleAktif === 'perawat') {
            return redirect()->route('perawat.skrining');
        }
        if ($roleAktif === 'dokter') {
            return redirect()->route('dokter.pelayanan');
        }
        if ($roleAktif === 'loket') {
            return redirect()->route('loket.index');
        }
        if ($roleAktif === 'apoteker') {
            return redirect()->route('apoteker.resep');
        }
        if ($roleAktif === 'resepsionis') {
            return redirect()->route('resepsionis.index');
        }

        $tanggalDipilih = $request->query('tanggal', today()->toDateString());
        $tglCarbon = Carbon::parse($tanggalDipilih);

        // 1. Transaksi pada tanggal yang dipilih (Fokus Harian)
        $queryTransaksi = Transaksi::with(['pendaftaran.pasien', 'pendaftaran.poli', 'layanan', 'kasir'])
            ->where(function ($q) use ($tanggalDipilih) {
                $q->whereDate('waktu_pembayaran', $tanggalDipilih)
                  ->orWhere(function ($sub) use ($tanggalDipilih) {
                      $sub->whereNull('waktu_pembayaran')
                          ->whereDate('created_at', $tanggalDipilih);
                  });
            });

        $transaksiList = (clone $queryTransaksi)->orderBy('created_at', 'desc')->get();

        // 2. Statistik Harian Utama
        $totalTransaksi  = $transaksiList->count();
        $totalPendapatan = (float) $transaksiList->sum('total_bayar');
        $transaksiLunas  = $transaksiList->where('status_pembayaran', 'lunas')->count();

        // 3. Rincian Metode Pembayaran Harian
        $totalTunai    = (float) $transaksiList->filter(fn($t) => strtoupper($t->metode_bayar ?? 'TUNAI') === 'TUNAI')->sum('total_bayar');
        $totalNonTunai = $totalPendapatan - $totalTunai;

        // 4. Daftar Antrean Pasien yang Belum Bayar (Menunggu Kasir pada tanggal dipilih)
        $pasienMenungguKasir = Pendaftaran::with(['pasien', 'poli', 'resep.details.obat', 'rekamMedis.tindakanMedis.layanan'])
            ->where('status_antrian', 'menunggu_kasir')
            ->whereDate('tanggal_daftar', $tanggalDipilih)
            ->whereDoesntHave('transaksi', fn($t) => $t->where('status_pembayaran', 'lunas'))
            ->orderBy('no_antrian', 'asc')
            ->get();

        $sisaAntreanKasir = $pasienMenungguKasir->count();
        $totalPasienAntri = $transaksiLunas + $sisaAntreanKasir;

        // 5. Akumulasi Bulan Berjalan (Month-to-Date / MTD)
        $bulanAngka     = $tglCarbon->month;
        $tahunAngka     = $tglCarbon->year;
        $namaBulanTahun = LaporanController::$namaBulan[$bulanAngka] . ' ' . $tahunAngka;

        $queryBulanan = Transaksi::where('status_pembayaran', 'lunas')
            ->where(function ($q) use ($bulanAngka, $tahunAngka) {
                $q->whereYear('waktu_pembayaran', $tahunAngka)->whereMonth('waktu_pembayaran', $bulanAngka)
                  ->orWhere(function ($sub) use ($bulanAngka, $tahunAngka) {
                      $sub->whereNull('waktu_pembayaran')
                          ->whereYear('created_at', $tahunAngka)->whereMonth('created_at', $bulanAngka);
                  });
            });

        $totalPendapatanBulanIni = (float) $queryBulanan->sum('total_bayar');
        $totalTransaksiBulanIni  = $queryBulanan->count();

        return view('dashboard.index', compact(
            'tanggalDipilih',
            'transaksiList',
            'totalTransaksi',
            'totalPendapatan',
            'transaksiLunas',
            'totalTunai',
            'totalNonTunai',
            'pasienMenungguKasir',
            'sisaAntreanKasir',
            'totalPasienAntri',
            'namaBulanTahun',
            'totalPendapatanBulanIni',
            'totalTransaksiBulanIni'
        ));
    }
}
