<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Pendaftaran;
use App\Models\Poli;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $polis = Poli::withCount(['pendaftaran as antrian_hari_ini' => function ($q) {
            $q->whereDate('tanggal_daftar', today());
        }])->get();

        $dokters = Pegawai::where('role', 'dokter')->get();

        $totalAntrianHariIni = Pendaftaran::whereDate('tanggal_daftar', today())->count();
        $sedangDilayani = Pendaftaran::whereDate('tanggal_daftar', today())
            ->where('status_antrian', 'sedang_diperiksa')
            ->count();

        // Ambil data antrian nyata dari sistem untuk simulasi live feed mockup aplikasi mobile
        $antrianMockup = Pendaftaran::with(['pasien', 'poli', 'transaksi'])
            ->orderBy('id_pendaftaran', 'desc')
            ->take(10)
            ->get()
            ->map(function ($p) {
                $nama = $p->pasien?->nama_pasien ?? 'Pasien Delta';
                $parts = explode(' ', trim($nama));
                $namaSensor = count($parts) > 1
                    ? $parts[0] . ' ' . substr($parts[1], 0, 1) . '***'
                    : substr($nama, 0, 3) . '***';

                $statusText = match ($p->status_antrian) {
                    'menunggu_skrining' => 'Menunggu Skrining',
                    'menunggu_dokter'   => 'Menunggu Dokter',
                    'sedang_diperiksa'  => 'Sedang Diperiksa Dokter',
                    'menunggu_kasir'    => 'Menunggu Kasir',
                    'menunggu_obat'     => 'Menunggu Farmasi',
                    'selesai'           => 'Pelayanan Selesai',
                    default             => ucfirst(str_replace('_', ' ', $p->status_antrian)),
                };

                $statusColor = match ($p->status_antrian) {
                    'sedang_diperiksa'                      => 'text-emerald-300 font-bold',
                    'menunggu_dokter', 'menunggu_skrining'  => 'text-yellow-200 font-semibold',
                    'menunggu_kasir'                        => 'text-amber-300 font-semibold',
                    'menunggu_obat'                         => 'text-cyan-200 font-semibold',
                    'selesai'                               => 'text-emerald-300 font-semibold',
                    default                                 => 'text-white',
                };

                $bayarText = 'Belum Ada Tagihan';
                $bayarColor = 'text-blue-100';

                if ($p->transaksi) {
                    if ($p->transaksi->status_pembayaran === 'lunas') {
                        $bayarText = 'Lunas (' . ($p->transaksi->no_invoice ?? 'Inv #' . $p->transaksi->id_transaksi) . ')';
                        $bayarColor = 'text-emerald-300 font-bold';
                    } else {
                        $bayarText = 'Menunggu Pembayaran';
                        $bayarColor = 'text-amber-300 font-semibold';
                    }
                } elseif ($p->cara_bayar === 'bpjs') {
                    $bayarText = 'Dijamin BPJS Kesehatan';
                    $bayarColor = 'text-emerald-300 font-semibold';
                } elseif ($p->status_antrian === 'menunggu_kasir') {
                    $bayarText = 'Tagihan Kasir Tersedia';
                    $bayarColor = 'text-yellow-300 font-semibold';
                }

                return [
                    'id_pendaftaran' => $p->id_pendaftaran,
                    'no_antrian'     => $p->no_antrian,
                    'nama_pasien'    => $namaSensor,
                    'nama_lengkap'   => $p->pasien?->nama_pasien,
                    'poli'           => $p->poli?->nama_poli ?? 'Poli Umum',
                    'status_antrian' => $statusText,
                    'status_color'   => $statusColor,
                    'pembayaran'     => $bayarText,
                    'bayar_color'    => $bayarColor,
                ];
            });

        // Fallback jika database masih kosong
        if ($antrianMockup->isEmpty()) {
            $antrianMockup = collect([
                [
                    'id_pendaftaran' => 1,
                    'no_antrian'     => 'UMU-001',
                    'nama_pasien'    => 'Ahmad F***',
                    'nama_lengkap'   => 'Ahmad Fauzi',
                    'poli'           => 'Poli Umum',
                    'status_antrian' => 'Menunggu Dokter',
                    'status_color'   => 'text-yellow-200 font-semibold',
                    'pembayaran'     => 'Menunggu Kasir',
                    'bayar_color'    => 'text-amber-300 font-semibold',
                ]
            ]);
        }

        return view('landing', compact('polis', 'dokters', 'totalAntrianHariIni', 'sedangDilayani', 'antrianMockup'));
    }

    /**
     * Endpoint dinamis untuk mengambil jumlah pendaftar dan status periksa
     * berdasarkan tanggal yang diklik pada kalender interaktif.
     */
    public function statusTanggal(Request $request)
    {
        $tanggal = $request->query('tanggal', today()->toDateString());
        $isToday = $tanggal === today()->toDateString();
        $isPast = $tanggal < today()->toDateString();
        $isFuture = $tanggal > today()->toDateString();

        $total = Pendaftaran::whereDate('tanggal_daftar', $tanggal)->count();
        $sedangDiperiksa = Pendaftaran::whereDate('tanggal_daftar', $tanggal)
            ->where('status_antrian', 'sedang_diperiksa')
            ->count();
        $selesai = Pendaftaran::whereDate('tanggal_daftar', $tanggal)
            ->whereIn('status_antrian', ['menunggu_kasir', 'menunggu_obat', 'selesai'])
            ->count();
        $menunggu = Pendaftaran::whereDate('tanggal_daftar', $tanggal)
            ->whereIn('status_antrian', ['menunggu_skrining', 'menunggu_dokter'])
            ->count();

        return response()->json([
            'success'          => true,
            'tanggal'          => $tanggal,
            'is_today'         => $isToday,
            'is_past'          => $isPast,
            'is_future'        => $isFuture,
            'total_pendaftar'  => $total,
            'sedang_diperiksa' => $sedangDiperiksa,
            'selesai_diperiksa'=> $selesai,
            'menunggu'         => $menunggu,
        ]);
    }
}
