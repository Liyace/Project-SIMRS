<?php

namespace App\Http\Controllers;

use App\Models\Pasien;
use App\Models\Pegawai;
use App\Models\Pendaftaran;
use App\Models\Poli;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LoketController extends Controller
{
    /**
     * Tampilkan Halaman Utama Loket Pendaftaran Walk-In
     */
    public function index(Request $request)
    {
        $polis = Poli::orderBy('nama_poli')->get();
        $dokters = Pegawai::where('role', 'dokter')->orderBy('nama')->get();

        $search = $request->query('cari');
        $filterPoli = $request->query('poli');

        $query = Pendaftaran::with(['pasien', 'poli', 'dokter'])
            ->whereDate('tanggal_daftar', today());

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('no_antrian', 'like', "%{$search}%")
                  ->orWhere('no_pendaftaran', 'like', "%{$search}%")
                  ->orWhereHas('pasien', function ($qp) use ($search) {
                      $qp->where('nama_pasien', 'like', "%{$search}%")
                         ->orWhere('rm', 'like', "%{$search}%")
                         ->orWhere('nik', 'like', "%{$search}%");
                  });
            });
        }

        if ($filterPoli) {
            $query->where('id_poli', $filterPoli);
        }

        $pendaftarans = $query->latest('id_pendaftaran')->paginate(10)->withQueryString();

        // Statistik Hari Ini
        $totalHariIni = Pendaftaran::whereDate('tanggal_daftar', today())->count();
        $menungguSkrining = Pendaftaran::whereDate('tanggal_daftar', today())
            ->where('status_antrian', 'menunggu_skrining')
            ->count();
        $pasienUmum = Pendaftaran::whereDate('tanggal_daftar', today())
            ->where('cara_bayar', 'umum')
            ->count();
        $pasienBpjs = Pendaftaran::whereDate('tanggal_daftar', today())
            ->where('cara_bayar', 'bpjs')
            ->count();

        return view('loket.index', compact(
            'polis',
            'dokters',
            'pendaftarans',
            'totalHariIni',
            'menungguSkrining',
            'pasienUmum',
            'pasienBpjs',
            'search',
            'filterPoli'
        ));
    }

    /**
     * Endpoint AJAX untuk mencari data pasien (NIK, RM, Nama)
     */
    public function cariPasien(Request $request)
    {
        $q = trim($request->query('q', ''));

        $query = Pasien::query();

        if (strlen($q) >= 1) {
            $query->where(function ($sub) use ($q) {
                $sub->where('rm', 'like', "%{$q}%")
                    ->orWhere('nik', 'like', "%{$q}%")
                    ->orWhere('nama_pasien', 'like', "%{$q}%");
            });
        }

        $pasiens = $query->orderBy('nama_pasien', 'asc')
            ->limit(15)
            ->get()
            ->map(function ($p) {
                $usia = $p->tanggal_lahir ? Carbon::parse($p->tanggal_lahir)->age . ' th' : '-';
                return [
                    'id_pasien'     => $p->id_pasien,
                    'rm'            => $p->rm,
                    'nik'           => $p->nik,
                    'nama_pasien'   => $p->nama_pasien,
                    'jenis_kelamin' => $p->jenis_kelamin,
                    'tanggal_lahir' => $p->tanggal_lahir ? Carbon::parse($p->tanggal_lahir)->format('d/m/Y') : '-',
                    'usia'          => $usia,
                    'no_hp'         => $p->no_hp ?? '-',
                    'alamat'        => $p->alamat ?? '-',
                ];
            });

        return response()->json($pasiens);
    }

    /**
     * Proses Pendaftaran Pasien Baru / Lama ke Poliklinik
     */
    public function store(Request $request)
    {
        $tipePasien = $request->input('tipe_pasien', 'lama');

        if ($tipePasien === 'baru') {
            $validated = $request->validate([
                'nik'           => 'required|digits:16|unique:pasien,nik',
                'nama_pasien'   => 'required|string|max:150',
                'jenis_kelamin' => 'required|in:L,P',
                'tanggal_lahir' => 'nullable|date',
                'no_hp'         => 'nullable|string|max:20',
                'alamat'        => 'nullable|string|max:255',
                'id_poli'       => 'required|exists:poli,id_poli',
                'id_dokter'     => 'nullable|exists:pegawai,id_pegawai',
                'cara_bayar'    => 'required|in:umum,bpjs,asuransi',
                'keluhan_awal'  => 'nullable|string|max:500',
            ], [
                'nik.required'    => 'NIK wajib diisi 16 digit.',
                'nik.digits'      => 'NIK harus tepat 16 digit angka.',
                'nik.unique'      => 'NIK ini sudah terdaftar sebagai pasien.',
                'nama_pasien.required' => 'Nama lengkap pasien wajib diisi.',
                'id_poli.required' => 'Poliklinik tujuan wajib dipilih.',
            ]);

            // Auto-generate nomor Rekam Medis (RM)
            $countPasien = Pasien::count() + 1;
            $rm = 'RM-' . str_pad($countPasien, 4, '0', STR_PAD_LEFT);
            while (Pasien::where('rm', $rm)->exists()) {
                $countPasien++;
                $rm = 'RM-' . str_pad($countPasien, 4, '0', STR_PAD_LEFT);
            }

            // Pendaftaran loket fisik murni untuk pencatatan rekam medis klinis
            // Pasien TIDAK otomatis dibuatkan password web (username & password_hash = null demi privasi & keamanan data medis)
            $pasien = Pasien::create([
                'rm'            => $rm,
                'nik'           => $validated['nik'],
                'username'      => null,
                'nama_pasien'   => $validated['nama_pasien'],
                'password_hash' => null,
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
                'no_hp'         => $validated['no_hp'] ?? null,
                'alamat'        => $validated['alamat'] ?? null,
            ]);

            $idPasien = $pasien->id_pasien;
        } else {
            $validated = $request->validate([
                'id_pasien'    => 'required|exists:pasien,id_pasien',
                'id_poli'      => 'required|exists:poli,id_poli',
                'id_dokter'    => 'nullable|exists:pegawai,id_pegawai',
                'cara_bayar'   => 'required|in:umum,bpjs,asuransi',
                'keluhan_awal' => 'nullable|string|max:500',
            ], [
                'id_pasien.required' => 'Silakan cari dan pilih pasien lama terlebih dahulu.',
                'id_poli.required'   => 'Poliklinik tujuan wajib dipilih.',
            ]);

            $idPasien = $validated['id_pasien'];
            $pasien = Pasien::findOrFail($idPasien);
        }

        // Generate No. Pendaftaran harian: REG-YYYYMMDD-XXX
        $prefixReg = 'REG-' . date('Ymd') . '-';
        $todayRegCount = Pendaftaran::whereDate('tanggal_daftar', today())->count() + 1;
        $noPendaftaran = $prefixReg . str_pad($todayRegCount, 3, '0', STR_PAD_LEFT);
        while (Pendaftaran::where('no_pendaftaran', $noPendaftaran)->exists()) {
            $todayRegCount++;
            $noPendaftaran = $prefixReg . str_pad($todayRegCount, 3, '0', STR_PAD_LEFT);
        }

        // Generate No. Antrean Poliklinik: misal UMU-001, GIG-002, ANK-003
        $poli = Poli::findOrFail($validated['id_poli']);
        $kodePoli = !empty($poli->kode_poli) 
            ? strtoupper(substr($poli->kode_poli, 0, 3)) 
            : strtoupper(substr(str_replace('Poli ', '', $poli->nama_poli), 0, 3));

        if (strlen($kodePoli) < 3) {
            $kodePoli = str_pad($kodePoli, 3, 'X');
        }

        $poliCountToday = Pendaftaran::whereDate('tanggal_daftar', today())
            ->where('id_poli', $poli->id_poli)
            ->count() + 1;

        $noAntrian = $kodePoli . '-' . str_pad($poliCountToday, 3, '0', STR_PAD_LEFT);
        while (Pendaftaran::whereDate('tanggal_daftar', today())->where('id_poli', $poli->id_poli)->where('no_antrian', $noAntrian)->exists()) {
            $poliCountToday++;
            $noAntrian = $kodePoli . '-' . str_pad($poliCountToday, 3, '0', STR_PAD_LEFT);
        }

        // Simpan pendaftaran ke antrean dengan status awal 'menunggu_skrining'
        $pendaftaran = Pendaftaran::create([
            'no_pendaftaran' => $noPendaftaran,
            'id_pasien'      => $idPasien,
            'id_poli'        => $poli->id_poli,
            'id_dokter'      => $validated['id_dokter'] ?? null,
            'no_antrian'     => $noAntrian,
            'tanggal_daftar' => now(),
            'cara_bayar'     => $validated['cara_bayar'],
            'status_antrian' => 'menunggu_skrining',
            'keluhan_awal'   => $validated['keluhan_awal'] ?? null,
        ]);

        return redirect()->route('loket.index')
            ->with('success', "Pendaftaran pasien {$pasien->nama_pasien} ({$noAntrian}) berhasil disimpan! Pasien otomatis masuk ke antrean Skrining Perawat.")
            ->with('cetak_id', $pendaftaran->id_pendaftaran)
            ->with('cetak_antrian', $noAntrian);
    }

    /**
     * Tampilkan Tiket / Karcis Antrean Poliklinik (Format Struk Siap Cetak)
     */
    public function cetakKarcis($id)
    {
        $pendaftaran = Pendaftaran::with(['pasien', 'poli', 'dokter'])->findOrFail($id);

        return view('loket.karcis', compact('pendaftaran'));
    }

    /**
     * Cetak Kartu Identitas Pasien Berobat (Ukuran Standar ID Card CR80 dengan Barcode Code 39)
     */
    public function cetakKartuPasien($id)
    {
        $pasien = Pasien::findOrFail($id);
        $barcodeSvg = \App\Services\BarcodeService::generateCode39Svg($pasien->rm, 44, 2);

        return view('loket.kartu-pasien', compact('pasien', 'barcodeSvg'));
    }

    /**
     * Pembatalan pendaftaran bila pasien batal atau salah input (sebelum diskrining)
     */
    public function batal($id)
    {
        $pendaftaran = Pendaftaran::findOrFail($id);

        if ($pendaftaran->status_antrian !== 'menunggu_skrining') {
            return back()->with('error', 'Pasien sudah memasuki tahapan skrining/pemeriksaan, pendaftaran tidak dapat dibatalkan dari loket.');
        }

        $pendaftaran->update(['status_antrian' => 'dibatalkan']);

        return back()->with('success', "Pendaftaran antrean {$pendaftaran->no_antrian} berhasil dibatalkan.");
    }

    /**
     * Polling Notifikasi Real-time Petugas Loket Pendaftaran
     */
    public function getNotifikasi()
    {
        $today = today();

        $totalHariIni = Pendaftaran::whereDate('tanggal_daftar', $today)->count();
        $menungguSkrining = Pendaftaran::whereDate('tanggal_daftar', $today)
            ->where('status_antrian', 'menunggu_skrining')
            ->count();

        // 5 pendaftaran terbaru hari ini
        $terbaru = Pendaftaran::with(['pasien', 'poli', 'dokter'])
            ->whereDate('tanggal_daftar', $today)
            ->orderByDesc('id_pendaftaran')
            ->limit(5)
            ->get()
            ->map(function ($p) {
                return [
                    'id_pendaftaran' => $p->id_pendaftaran,
                    'no_antrian'     => $p->no_antrian,
                    'no_pendaftaran' => $p->no_pendaftaran,
                    'nama_pasien'    => $p->pasien?->nama_pasien ?? 'Pasien',
                    'nama_poli'      => $p->poli?->nama_poli ?? 'Poli Umum',
                    'cara_bayar'     => strtoupper($p->cara_bayar ?? 'UMUM'),
                    'status_antrian' => $p->status_antrian,
                    'waktu'          => $p->tanggal_daftar ? $p->tanggal_daftar->format('H:i') : '-',
                    'url_karcis'     => route('loket.karcis', $p->id_pendaftaran),
                ];
            });

        return response()->json([
            'success'           => true,
            'total_hari_ini'    => $totalHariIni,
            'menunggu_skrining' => $menungguSkrining,
            'count'             => $totalHariIni,
            'pendaftaran'       => $terbaru,
            'timestamp'         => now()->timestamp,
        ]);
    }
}
