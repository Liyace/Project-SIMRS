<?php

namespace App\Http\Controllers;

use App\Exports\StokObatExport;
use App\Models\Obat;
use App\Models\Pendaftaran;
use App\Models\Resep;
use App\Models\ResepDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ApotekerController extends Controller
{
    /**
     * Halaman Utama Antrean E-Resep Farmasi
     */
    public function index(Request $request)
    {
        $tanggal = $request->query('tanggal', today()->toDateString());
        $tab = $request->query('tab', 'semua');
        $q = trim($request->query('q', ''));

        $query = Resep::with([
            'pendaftaran.pasien',
            'pendaftaran.poli',
            'dokter',
            'apoteker',
            'details.obat',
            'pendaftaran.transaksi',
        ]);

        // Filter berdasarkan tanggal
        $query->whereDate('created_at', $tanggal);

        // Filter Pencarian
        if (!empty($q)) {
            $query->where(function ($sub) use ($q) {
                $sub->where('no_resep', 'like', "%{$q}%")
                    ->orWhereHas('pendaftaran.pasien', function ($p) use ($q) {
                        $p->where('nama_pasien', 'like', "%{$q}%")
                          ->orWhere('rm', 'like', "%{$q}%")
                          ->orWhere('nik', 'like', "%{$q}%");
                    });
            });
        }

        // Filter Status Tab
        if ($tab === 'menunggu_kasir') {
            $query->where('status_resep', 'menunggu_pembayaran');
        } elseif ($tab === 'siap_racik') {
            $query->where('status_resep', 'siap_racik');
        } elseif ($tab === 'siap_diambil') {
            $query->where('status_resep', 'siap_diambil');
        } elseif ($tab === 'sudah_diserahkan') {
            $query->where('status_resep', 'sudah_diserahkan');
        }

        $resepList = $query->orderByDesc('id_resep')->get();

        // Hitung Statistik Hari Ini
        $totalResepHariIni = Resep::whereDate('created_at', $tanggal)->count();
        $menungguKasirCount = Resep::whereDate('created_at', $tanggal)->where('status_resep', 'menunggu_pembayaran')->count();
        $siapRacikCount = Resep::whereDate('created_at', $tanggal)->where('status_resep', 'siap_racik')->count();
        $siapDiambilCount = Resep::whereDate('created_at', $tanggal)->where('status_resep', 'siap_diambil')->count();
        $sudahDiserahkanCount = Resep::whereDate('created_at', $tanggal)->where('status_resep', 'sudah_diserahkan')->count();
        $stokMenipisCount = Obat::whereColumn('stok', '<=', 'stok_minimum')->count();

        return view('apoteker.resep', compact(
            'resepList',
            'tanggal',
            'tab',
            'q',
            'totalResepHariIni',
            'menungguKasirCount',
            'siapRacikCount',
            'siapDiambilCount',
            'sudahDiserahkanCount',
            'stokMenipisCount'
        ));
    }

    /**
     * Endpoint Notifikasi Realtime untuk Apoteker (Polling Topbar)
     */
    public function getNotifikasi()
    {
        $siapRacik = Resep::with(['pendaftaran.pasien', 'pendaftaran.poli'])
            ->whereDate('created_at', today())
            ->whereIn('status_resep', ['siap_racik', 'siap_diambil'])
            ->orderByDesc('id_resep')
            ->limit(10)
            ->get();

        $stokMenipis = Obat::whereColumn('stok', '<=', 'stok_minimum')
            ->limit(5)
            ->get();

        return response()->json([
            'count_resep'    => $siapRacik->count(),
            'count_menipis'  => $stokMenipis->count(),
            'total_notif'    => $siapRacik->count() + $stokMenipis->count(),
            'resep_antrean'  => $siapRacik->map(fn($r) => [
                'id_resep'     => $r->id_resep,
                'no_resep'     => $r->no_resep,
                'status_resep' => $r->status_resep,
                'nama_pasien'  => $r->pendaftaran?->pasien?->nama_pasien ?? '-',
                'rm'           => $r->pendaftaran?->pasien?->rm ?? '-',
                'poli'         => $r->pendaftaran?->poli?->nama_poli ?? '-',
                'waktu'        => $r->created_at->format('H:i'),
            ]),
            'obat_menipis'   => $stokMenipis->map(fn($o) => [
                'nama_obat'    => $o->nama_obat,
                'stok'         => $o->stok,
                'stok_minimum' => $o->stok_minimum,
                'satuan'       => $o->satuan,
            ]),
        ]);
    }

    /**
     * Tandai Resep Selesai Diracing (Siap Diambil Pasien)
     */
    public function prosesRacik($id)
    {
        $resep = Resep::findOrFail($id);

        $resep->update([
            'status_resep' => 'siap_diambil',
            'id_apoteker'  => auth()->id(),
        ]);

        return back()->with('success', "Resep {$resep->no_resep} telah selesai diracik dan siap diserahkan kepada pasien!");
    }

    /**
     * Penyerahan Obat ke Pasien (Validasi Lunas Kasir & Potong Stok)
     */
    public function serahkanObat($id)
    {
        $resep = Resep::with(['details.obat', 'pendaftaran.transaksi', 'pendaftaran.pasien'])->findOrFail($id);

        // 1. Verifikasi Pembayaran di Kasir Wajib LUNAS
        $transaksi = $resep->pendaftaran?->transaksi;
        if (!$transaksi || $transaksi->status_pembayaran !== 'lunas') {
            return back()->with('error', "Gagal serahkan obat! Pasien {$resep->pendaftaran?->pasien?->nama_pasien} belum menyelesaikan transaksi pembayaran di Loket Kasir.");
        }

        // 2. Verifikasi Ketersediaan Stok Obat
        foreach ($resep->details as $item) {
            if ($item->obat && $item->obat->stok < $item->jumlah) {
                return back()->with('error', "Stok obat '{$item->obat->nama_obat}' tidak mencukupi! Tersedia: {$item->obat->stok}, dibutuhkan: {$item->jumlah}. Harap tambah stok terlebih dahulu.");
            }
        }

        DB::beginTransaction();
        try {
            // Potong stok obat
            foreach ($resep->details as $item) {
                if ($item->obat) {
                    $item->obat->decrement('stok', $item->jumlah);
                }
            }

            // Update status resep
            $resep->update([
                'status_resep' => 'sudah_diserahkan',
                'id_apoteker'  => auth()->id(),
            ]);

            // Selesaikan journey antrian pasien di RS
            if ($resep->pendaftaran) {
                $resep->pendaftaran->update(['status_antrian' => 'selesai']);
            }

            DB::commit();

            return back()->with('success', "Obat resep {$resep->no_resep} berhasil diserahkan kepada pasien {$resep->pendaftaran?->pasien?->nama_pasien}. Stok gudang farmasi telah diperbarui secara otomatis.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', "Terjadi kesalahan saat penyerahan obat: " . $e->getMessage());
        }
    }

    /**
     * Cetak Etiket Stiker Resep Obat (Format Stiker / Kertas Etiket Farmasi)
     */
    public function cetakEtiket($id)
    {
        $resep = Resep::with([
            'pendaftaran.pasien',
            'pendaftaran.poli',
            'dokter',
            'apoteker',
            'details.obat',
        ])->findOrFail($id);

        return view('apoteker.etiket', compact('resep'));
    }

    /**
     * Manajemen Inventori & Stok Obat Farmasi
     */
    public function obat(Request $request)
    {
        $q = trim($request->query('q', ''));
        $status = $request->query('status', 'semua');

        $query = Obat::query();

        if (!empty($q)) {
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_obat', 'like', "%{$q}%")
                    ->orWhere('kode_obat', 'like', "%{$q}%");
            });
        }

        if ($status === 'menipis') {
            $query->whereColumn('stok', '<=', 'stok_minimum')->where('stok', '>', 0);
        } elseif ($status === 'habis') {
            $query->where('stok', '<=', 0);
        } elseif ($status === 'aman') {
            $query->whereColumn('stok', '>', 'stok_minimum');
        }

        $obatList = $query->orderBy('nama_obat', 'asc')->paginate(15)->withQueryString();

        $totalObat = Obat::count();
        $totalAman = Obat::whereColumn('stok', '>', 'stok_minimum')->count();
        $totalMenipis = Obat::whereColumn('stok', '<=', 'stok_minimum')->where('stok', '>', 0)->count();
        $totalHabis = Obat::where('stok', '<=', 0)->count();

        return view('apoteker.obat', compact(
            'obatList',
            'q',
            'status',
            'totalObat',
            'totalAman',
            'totalMenipis',
            'totalHabis'
        ));
    }

    /**
     * Tambah Master Obat Baru
     */
    public function obatStore(Request $request)
    {
        $validated = $request->validate([
            'kode_obat'    => 'nullable|string|max:50|unique:obat,kode_obat',
            'nama_obat'    => 'required|string|max:255',
            'satuan'       => 'required|string|max:50',
            'harga_beli'   => 'nullable|numeric|min:0',
            'harga_obat'   => 'required|numeric|min:0',
            'stok'         => 'required|integer|min:0',
            'stok_minimum' => 'nullable|integer|min:0',
        ]);

        if (empty($validated['kode_obat'])) {
            $count = Obat::count() + 1;
            $validated['kode_obat'] = 'OBT-' . str_pad($count, 3, '0', STR_PAD_LEFT);
            while (Obat::where('kode_obat', $validated['kode_obat'])->exists()) {
                $count++;
                $validated['kode_obat'] = 'OBT-' . str_pad($count, 3, '0', STR_PAD_LEFT);
            }
        }

        $validated['stok_minimum'] = $validated['stok_minimum'] ?? 10;
        $validated['harga_beli']   = $validated['harga_beli'] ?? 0;

        Obat::create($validated);

        return back()->with('success', "Obat baru '{$validated['nama_obat']}' ({$validated['kode_obat']}) berhasil ditambahkan ke inventori!");
    }

    /**
     * Tambah / Perbarui Stok Obat Masuk
     */
    public function obatUpdateStok(Request $request, $id)
    {
        $obat = Obat::findOrFail($id);

        $request->validate([
            'tambah_stok'  => 'required|integer|min:1',
            'harga_beli'   => 'nullable|numeric|min:0',
            'harga_obat'   => 'nullable|numeric|min:0',
        ]);

        $stokLama = $obat->stok;
        $tambah = (int) $request->tambah_stok;
        $obat->stok = $stokLama + $tambah;

        if ($request->filled('harga_beli')) {
            $obat->harga_beli = $request->harga_beli;
        }
        if ($request->filled('harga_obat')) {
            $obat->harga_obat = $request->harga_obat;
        }

        $obat->save();

        return back()->with('success', "Stok obat '{$obat->nama_obat}' berhasil ditambah sebanyak +{$tambah} {$obat->satuan}. Total stok sekarang: {$obat->stok} {$obat->satuan}.");
    }

    /**
     * Riwayat Penyerahan Resep Obat Farmasi
     */
    public function riwayat(Request $request)
    {
        $tanggalMulai = $request->query('tanggal_mulai', today()->subDays(7)->toDateString());
        $tanggalSelesai = $request->query('tanggal_selesai', today()->toDateString());
        $q = trim($request->query('q', ''));

        $query = Resep::with([
            'pendaftaran.pasien',
            'pendaftaran.poli',
            'dokter',
            'apoteker',
            'details.obat',
            'pendaftaran.transaksi',
        ])
        ->where('status_resep', 'sudah_diserahkan')
        ->whereDate('updated_at', '>=', $tanggalMulai)
        ->whereDate('updated_at', '<=', $tanggalSelesai);

        if (!empty($q)) {
            $query->where(function ($sub) use ($q) {
                $sub->where('no_resep', 'like', "%{$q}%")
                    ->orWhereHas('pendaftaran.pasien', function ($p) use ($q) {
                        $p->where('nama_pasien', 'like', "%{$q}%")
                          ->orWhere('rm', 'like', "%{$q}%");
                    });
            });
        }

        $riwayatList = $query->orderByDesc('updated_at')->paginate(20)->withQueryString();

        $totalDiserahkanPeriode = (clone $query)->count();
        $totalNominalObat = (float) (clone $query)->sum('total_harga_obat');

        return view('apoteker.riwayat', compact(
            'riwayatList',
            'tanggalMulai',
            'tanggalSelesai',
            'q',
            'totalDiserahkanPeriode',
            'totalNominalObat'
        ));
    }

    /**
     * Cetak Salinan Resep Dokter (Copy Resep / Apograph)
     */
    public function cetakCopyResep($id, Request $request)
    {
        $resep = Resep::with([
            'pendaftaran.pasien',
            'pendaftaran.poli',
            'dokter',
            'apoteker',
            'details.obat',
        ])->findOrFail($id);

        if ($request->query('download') || $request->query('pdf')) {
            $pdf = Pdf::loadView('apoteker.copy_resep', [
                'resep' => $resep,
                'isPdf' => true,
            ])->setPaper('a5', 'portrait');

            $cleanNoResep = str_replace(['/', '\\', ' '], '-', $resep->no_resep);
            $filename = 'Copy_Resep_' . $cleanNoResep . '.pdf';
            return $pdf->download($filename);
        }

        return view('apoteker.copy_resep', [
            'resep' => $resep,
            'isPdf' => false,
        ]);
    }

    /**
     * Cetak Laporan Stok Opname & Mutasi Farmasi (PDF & Browser Print)
     */
    public function cetakStokPdf(Request $request)
    {
        $status = $request->query('status', 'semua');
        $query = Obat::query();

        if ($status === 'menipis') {
            $query->whereColumn('stok', '<=', 'stok_minimum')->where('stok', '>', 0);
        } elseif ($status === 'habis') {
            $query->where('stok', '<=', 0);
        } elseif ($status === 'aman') {
            $query->whereColumn('stok', '>', 'stok_minimum');
        }

        $obatList = $query->orderBy('nama_obat', 'asc')->get();

        $totalItem = $obatList->count();
        $totalFisik = $obatList->sum('stok');
        $totalValuasi = $obatList->sum(function ($item) {
            return (float) ($item->stok * $item->harga_obat);
        });

        $data = [
            'obatList'     => $obatList,
            'status'       => $status,
            'filter'       => $status,
            'totalItem'    => $totalItem,
            'totalFisik'   => $totalFisik,
            'totalValuasi' => $totalValuasi,
            'tanggalCetak' => now()->translatedFormat('d F Y H:i'),
            'isPdf'        => false,
        ];

        if ($request->query('download') || $request->query('pdf')) {
            $data['isPdf'] = true;
            $pdf = Pdf::loadView('apoteker.stok_pdf', $data)->setPaper('a4', 'landscape');
            $filename = 'Laporan_Stok_Farmasi_RS_Delta_' . date('Ymd_His') . '.pdf';
            return $pdf->download($filename);
        }

        return view('apoteker.stok_pdf', $data);
    }

    /**
     * Export Laporan Stok Obat ke Excel (.xlsx)
     */
    public function exportStokExcel(Request $request)
    {
        $status = $request->query('status', 'semua');
        $filename = 'Laporan_Stok_Farmasi_RS_Delta_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new StokObatExport($status), $filename);
    }
}
