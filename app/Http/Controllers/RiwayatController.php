<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->query('tanggal');
        $q = trim($request->query('q', ''));

        $query = Transaksi::with(['pendaftaran.pasien', 'layanan', 'kasir'])
            ->where('status_pembayaran', 'lunas');

        if (!empty($tanggal)) {
            $query->where(function ($sub) use ($tanggal) {
                $sub->whereDate('waktu_pembayaran', $tanggal)
                    ->orWhere(function ($fallback) use ($tanggal) {
                        $fallback->whereNull('waktu_pembayaran')
                                 ->whereDate('created_at', $tanggal);
                    });
            });
        }

        if (!empty($q)) {
            $query->where(function ($sub) use ($q) {
                $sub->whereHas('pendaftaran.pasien', function ($p) use ($q) {
                    $p->where('nama_pasien', 'like', "%{$q}%")
                      ->orWhere('rm', 'like', "%{$q}%")
                      ->orWhere('nik', 'like', "%{$q}%");
                })->orWhere('no_invoice', 'like', "%{$q}%");
            });
        }

        $riwayat = $query->orderByDesc('id_transaksi')
            ->paginate(15)
            ->withQueryString();

        return view('riwayat.index', compact('riwayat', 'tanggal', 'q'));
    }
}
