<?php

namespace App\Http\Controllers;

use App\Models\Pasien;
use App\Models\Pegawai;
use App\Models\Pendaftaran;
use App\Models\Poli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PasienPortalController extends Controller
{
    /**
     * Dashboard Portal Pasien Mandiri
     */
    public function index()
    {
        if (session('user_type') !== 'pasien') {
            return redirect()->route('login');
        }

        $pasien = Pasien::find(session('id_pasien'));
        if (!$pasien) {
            return redirect()->route('login');
        }

        $polis = Poli::orderBy('nama_poli')->get();
        $dokters = Pegawai::where('role', 'dokter')->orderBy('nama')->get();

        // Cek antrean aktif pasien hari ini
        $antreanAktif = Pendaftaran::with(['poli', 'dokter', 'screeningAwal', 'rekamMedis', 'transaksi'])
            ->where('id_pasien', $pasien->id_pasien)
            ->whereDate('tanggal_daftar', today())
            ->whereNotIn('status_antrian', ['dibatalkan'])
            ->latest('id_pendaftaran')
            ->first();

        // Riwayat kunjungan sebelumnya
        $totalKunjungan = Pendaftaran::where('id_pasien', $pasien->id_pasien)->count();
        $riwayatKunjungan = Pendaftaran::with(['poli', 'dokter'])
            ->where('id_pasien', $pasien->id_pasien)
            ->latest('id_pendaftaran')
            ->limit(10)
            ->get();

        // Generate Barcode SVG untuk tampilan kartu digital di dashboard
        $barcodeSvg = \App\Services\BarcodeService::generateCode39Svg($pasien->rm, 36, 1.6);

        return view('pasien.dashboard', compact('pasien', 'polis', 'dokters', 'antreanAktif', 'riwayatKunjungan', 'totalKunjungan', 'barcodeSvg'));
    }

    /**
     * Cetak Kartu Identitas Pasien Berobat (CR80) Mandiri Pasien
     */
    public function cetakKartu()
    {
        if (session('user_type') !== 'pasien') {
            return redirect()->route('login');
        }

        $pasien = Pasien::findOrFail(session('id_pasien'));
        $barcodeSvg = \App\Services\BarcodeService::generateCode39Svg($pasien->rm, 44, 2);

        return view('loket.kartu-pasien', compact('pasien', 'barcodeSvg'));
    }

    /**
     * Pendaftaran Antrean Mandiri Pasien Online untuk Hari Ini
     */
    public function storeAntrean(Request $request)
    {
        if (session('user_type') !== 'pasien') {
            return redirect()->route('login');
        }

        $pasien = Pasien::findOrFail(session('id_pasien'));

        // Cek apakah pasien sudah memiliki antrean aktif yang belum selesai hari ini
        $adaAntrean = Pendaftaran::where('id_pasien', $pasien->id_pasien)
            ->whereDate('tanggal_daftar', today())
            ->whereIn('status_antrian', ['menunggu_skrining', 'menunggu_dokter', 'sedang_diperiksa', 'menunggu_kasir', 'menunggu_obat'])
            ->first();

        if ($adaAntrean) {
            return back()->with('error', "Anda masih memiliki antrean aktif ({$adaAntrean->no_antrian}) yang sedang berjalan hari ini.");
        }

        $validated = $request->validate([
            'id_poli'      => 'required|exists:poli,id_poli',
            'id_dokter'    => 'nullable|exists:pegawai,id_pegawai',
            'cara_bayar'   => 'required|in:umum,bpjs,asuransi',
            'keluhan_awal' => 'nullable|string|max:500',
        ], [
            'id_poli.required' => 'Silakan pilih poliklinik tujuan berobat Anda.',
        ]);

        $poli = Poli::findOrFail($validated['id_poli']);

        // Generate No. Pendaftaran: REG-YYYYMMDD-XXX
        $prefixReg = 'REG-' . date('Ymd') . '-';
        $todayRegCount = Pendaftaran::whereDate('tanggal_daftar', today())->count() + 1;
        $noPendaftaran = $prefixReg . str_pad($todayRegCount, 3, '0', STR_PAD_LEFT);
        while (Pendaftaran::where('no_pendaftaran', $noPendaftaran)->exists()) {
            $todayRegCount++;
            $noPendaftaran = $prefixReg . str_pad($todayRegCount, 3, '0', STR_PAD_LEFT);
        }

        // Generate Kode Antrean
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

        $pendaftaran = Pendaftaran::create([
            'no_pendaftaran' => $noPendaftaran,
            'id_pasien'      => $pasien->id_pasien,
            'id_poli'        => $poli->id_poli,
            'id_dokter'      => $validated['id_dokter'] ?? null,
            'no_antrian'     => $noAntrian,
            'tanggal_daftar' => now(),
            'cara_bayar'     => $validated['cara_bayar'],
            'status_antrian' => 'menunggu_skrining',
            'keluhan_awal'   => $validated['keluhan_awal'] ?? null,
        ]);

        return redirect()->route('pasien.dashboard')
            ->with('success', "Pendaftaran antrean mandiri berhasil! Nomor antrean Anda: {$noAntrian}. Silakan datang ke RS dan menuju Ruang Skrining Perawat.");
    }

    /**
     * Cetak Tiket Antrean Digital Pasien
     */
    public function cetakTiket($id)
    {
        if (session('user_type') !== 'pasien') {
            return redirect()->route('login');
        }

        $pendaftaran = Pendaftaran::with(['pasien', 'poli', 'dokter'])
            ->where('id_pasien', session('id_pasien'))
            ->findOrFail($id);

        return view('loket.karcis', compact('pendaftaran'));
    }

    /**
     * Polling Notifikasi & Status Real-time Khusus Portal Pasien
     */
    public function getNotifikasi(Request $request)
    {
        if (session('user_type') !== 'pasien') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $idPasien = session('id_pasien');
        $antreanAktif = Pendaftaran::with(['poli', 'dokter', 'transaksi', 'resep'])
            ->where('id_pasien', $idPasien)
            ->whereDate('tanggal_daftar', today())
            ->whereNotIn('status_antrian', ['dibatalkan'])
            ->latest('id_pendaftaran')
            ->first();

        if (!$antreanAktif) {
            return response()->json([
                'success'    => true,
                'ada_antrean'=> false,
                'message'    => 'Tidak ada antrean aktif hari ini.',
            ]);
        }

        // Hitung sisa antrean di depan pasien dalam poli yang sama
        $sisaDiDepan = Pendaftaran::where('id_poli', $antreanAktif->id_poli)
            ->whereDate('tanggal_daftar', today())
            ->where('status_antrian', $antreanAktif->status_antrian)
            ->where('id_pendaftaran', '<', $antreanAktif->id_pendaftaran)
            ->count();

        // Cek apakah nomor antrean pasien ini sedang dipanggil oleh Resepsionis
        $panggilanTerbaru = Cache::get('simrs_antrean_panggilan_terbaru');
        $sedangDipanggil = false;
        $tujuanPanggilan = null;
        $suaraTeks = null;

        if ($panggilanTerbaru && isset($panggilanTerbaru['no_antrian'])) {
            if ($panggilanTerbaru['no_antrian'] === $antreanAktif->no_antrian) {
                // Hanya aktif jika panggilan terjadi dalam 90 detik terakhir
                $waktuPanggilan = $panggilanTerbaru['timestamp'] ?? 0;
                if ((now()->timestamp - $waktuPanggilan) <= 90) {
                    $sedangDipanggil = true;
                    $tujuanPanggilan = $panggilanTerbaru['tujuan'] ?? 'Loket Pelayanan';
                    $suaraTeks = $panggilanTerbaru['suara_teks'] ?? null;
                }
            }
        }

        $status = $antreanAktif->status_antrian;
        $stepIndex = match ($status) {
            'menunggu_skrining' => 2,
            'menunggu_dokter', 'sedang_diperiksa' => 3,
            'menunggu_kasir' => 4,
            'menunggu_obat' => 5,
            'selesai' => 6,
            default => 1,
        };

        $statusTextMap = [
            'menunggu_skrining' => 'Menunggu Skrining TTV Perawat',
            'menunggu_dokter'   => 'Menunggu Pemeriksaan Dokter ' . ($antreanAktif->poli->nama_poli ?? ''),
            'sedang_diperiksa'  => 'Sedang Diperiksa Dokter di Ruang Periksa',
            'menunggu_kasir'    => 'Pemeriksaan Selesai, Menuju Loket Kasir Pembayaran',
            'menunggu_obat'     => 'Pembayaran Lunas, Menunggu Penyerahan Obat di Farmasi',
            'selesai'           => 'Seluruh Rangkaian Pelayanan Medis Selesai',
        ];

        $statusLabel = $statusTextMap[$status] ?? 'Sedang Diproses';

        // Rekomendasi pesan informatif
        if ($sedangDipanggil) {
            $pesan = "NOMOR ANTREAN ANDA SEDANG DIPANGGIL! Segera menuju ke {$tujuanPanggilan}.";
        } elseif ($status === 'selesai') {
            $pesan = "Terima kasih telah berobat di RS Delta. Semoga lekas sembuh!";
        } elseif ($status === 'menunggu_obat') {
            $pesan = "Resep obat Anda sedang diproses oleh tim Farmasi. Harap menunggu di depan loket Apotek.";
        } elseif ($sisaDiDepan === 0) {
            $pesan = "Anda berada di urutan terdepan! Harap bersiap di dekat ruangan.";
        } elseif ($sisaDiDepan <= 2) {
            $pesan = "Giliran Anda sebentar lagi! Sisa {$sisaDiDepan} pasien di depan Anda.";
        } else {
            $pesan = "Status: {$statusLabel}. Sisa {$sisaDiDepan} pasien mendahului Anda.";
        }

        return response()->json([
            'success'            => true,
            'ada_antrean'        => true,
            'id_pendaftaran'     => $antreanAktif->id_pendaftaran,
            'no_antrian'         => $antreanAktif->no_antrian,
            'status_antrian'     => $status,
            'status_label'       => $statusLabel,
            'step_index'         => $stepIndex,
            'sisa_antrean'       => $sisaDiDepan,
            'sedang_dipanggil'   => $sedangDipanggil,
            'tujuan_panggilan'   => $tujuanPanggilan,
            'suara_teks'         => $suaraTeks,
            'pesan_notifikasi'   => $pesan,
            'nama_poli'          => $antreanAktif->poli->nama_poli ?? '-',
            'nama_dokter'        => $antreanAktif->dokter->nama ?? '-',
            'timestamp'          => now()->timestamp,
        ]);
    }

    /**
     * Tampilkan Detail Rekam Medis & Riwayat Pelayanan Terpadu Pasien
     * Menampilkan: Hasil TTV Perawat, Diagnosa & Tindakan Dokter, E-Resep Obat Farmasi, dan Billing Kasir
     */
    public function detailRiwayat($id)
    {
        if (session('user_type') !== 'pasien') {
            return redirect()->route('login');
        }

        $idPasien = session('id_pasien');
        $pasien = Pasien::findOrFail($idPasien);

        $pendaftaran = Pendaftaran::with([
            'poli',
            'dokter',
            'screeningAwal.perawat',
            'rekamMedis.dokter',
            'rekamMedis.tindakanMedis.layanan',
            'resep.apoteker',
            'resep.details.obat',
            'transaksi.kasir',
            'transaksi.layanan'
        ])
        ->where('id_pasien', $idPasien)
        ->findOrFail($id);

        return view('pasien.riwayat-detail', compact('pasien', 'pendaftaran'));
    }

    /**
     * Halaman Arsip Daftar Seluruh Riwayat Rekam Medis Pasien
     */
    public function riwayatSemua(Request $request)
    {
        if (session('user_type') !== 'pasien') {
            return redirect()->route('login');
        }

        $idPasien = session('id_pasien');
        $pasien = Pasien::findOrFail($idPasien);

        $q = trim($request->query('q', ''));
        $filterPoli = $request->query('poli');

        $query = Pendaftaran::with([
            'poli',
            'dokter',
            'screeningAwal',
            'rekamMedis',
            'resep.details.obat',
            'transaksi'
        ])
        ->where('id_pasien', $idPasien);

        if (!empty($filterPoli)) {
            $query->where('id_poli', $filterPoli);
        }

        if (!empty($q)) {
            $query->where(function ($sub) use ($q) {
                $sub->where('no_antrian', 'like', "%{$q}%")
                    ->orWhere('no_pendaftaran', 'like', "%{$q}%")
                    ->orWhere('keluhan_awal', 'like', "%{$q}%")
                    ->orWhereHas('poli', function ($p) use ($q) {
                        $p->where('nama_poli', 'like', "%{$q}%");
                    })
                    ->orWhereHas('dokter', function ($d) use ($q) {
                        $d->where('nama', 'like', "%{$q}%");
                    })
                    ->orWhereHas('rekamMedis', function ($r) use ($q) {
                        $r->where('diagnosa_utama', 'like', "%{$q}%")
                          ->orWhere('kode_icd10', 'like', "%{$q}%");
                    });
            });
        }

        $riwayatList = $query->orderByDesc('tanggal_daftar')->paginate(10)->withQueryString();
        $polis = Poli::orderBy('nama_poli')->get();

        // Statistik ringkas perkembangan kesehatan pasien
        $totalKunjungan = Pendaftaran::where('id_pasien', $idPasien)->count();
        $totalSelesai = Pendaftaran::where('id_pasien', $idPasien)->where('status_antrian', 'selesai')->count();
        $kunjunganTerakhir = Pendaftaran::with(['screeningAwal', 'rekamMedis'])
            ->where('id_pasien', $idPasien)
            ->latest('id_pendaftaran')
            ->first();

        return view('pasien.riwayat', compact('pasien', 'riwayatList', 'polis', 'q', 'filterPoli', 'totalKunjungan', 'totalSelesai', 'kunjunganTerakhir'));
    }

    /**
     * Halaman Komparasi / Perbandingan Antar Kunjungan Berobat Pasien (Side-by-Side Progression)
     */
    public function komparasi(Request $request)
    {
        if (session('user_type') !== 'pasien') {
            return redirect()->route('login');
        }

        $idPasien = session('id_pasien');
        $pasien = Pasien::findOrFail($idPasien);

        // Ambil seluruh kunjungan untuk dropdown pilihan komparasi
        $daftarKunjungan = Pendaftaran::with(['poli', 'dokter', 'rekamMedis'])
            ->where('id_pasien', $idPasien)
            ->orderByDesc('tanggal_daftar')
            ->get();

        $idA = $request->query('kunjungan_a');
        $idB = $request->query('kunjungan_b');

        // Jika belum dipilih, defaultkan kunjungan_a = kunjungan terbaru, kunjungan_b = kunjungan sebelumnya
        if (!$idA && $daftarKunjungan->count() > 0) {
            $idA = $daftarKunjungan->first()->id_pendaftaran;
        }
        if (!$idB && $daftarKunjungan->count() > 1) {
            $idB = $daftarKunjungan->skip(1)->first()->id_pendaftaran;
        }

        $kunjunganA = null;
        $kunjunganB = null;

        if ($idA) {
            $kunjunganA = Pendaftaran::with([
                'poli',
                'dokter',
                'screeningAwal.perawat',
                'rekamMedis.dokter',
                'rekamMedis.tindakanMedis.layanan',
                'resep.details.obat',
                'transaksi'
            ])
            ->where('id_pasien', $idPasien)
            ->find($idA);
        }

        if ($idB) {
            $kunjunganB = Pendaftaran::with([
                'poli',
                'dokter',
                'screeningAwal.perawat',
                'rekamMedis.dokter',
                'rekamMedis.tindakanMedis.layanan',
                'resep.details.obat',
                'transaksi'
            ])
            ->where('id_pasien', $idPasien)
            ->find($idB);
        }

        return view('pasien.komparasi', compact('pasien', 'daftarKunjungan', 'kunjunganA', 'kunjunganB', 'idA', 'idB'));
    }

    /**
     * Cetak atau Unduh Dokumen Resmi Resume Medis Rawat Jalan Milik Pasien
     */
    public function cetakResumeMedis($id, Request $request)
    {
        if (session('user_type') !== 'pasien') {
            return redirect()->route('login');
        }

        $idPasien = session('id_pasien');
        $pendaftaran = Pendaftaran::with([
            'pasien',
            'poli',
            'dokter',
            'screeningAwal.perawat',
            'rekamMedis.tindakanMedis.layanan',
            'resep.details.obat',
        ])
        ->where('id_pasien', $idPasien)
        ->findOrFail($id);

        $nomorResume = 'RMJ/' . date('Y/m') . '/' . str_pad($pendaftaran->id_pendaftaran, 4, '0', STR_PAD_LEFT);
        $dokter = $pendaftaran->dokter;

        $data = compact('pendaftaran', 'nomorResume', 'dokter');

        if ($request->query('download') === '1' || $request->query('pdf') === '1') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('dokter.resume_medis', array_merge($data, ['isPdf' => true]))->setPaper('a4', 'portrait');
            $safeName = preg_replace('/[^A-Za-z0-9_-]/', '_', $pendaftaran->pasien?->nama_pasien ?? 'Pasien');
            return $pdf->download("Resume_Medis_{$safeName}.pdf");
        }

        return view('dokter.resume_medis', array_merge($data, ['isPdf' => false]));
    }

    /**
     * Cetak Bukti Pembayaran / Kuitansi Kasir Pasien
     */
    public function cetakKuitansi($id)
    {
        if (session('user_type') !== 'pasien') {
            return redirect()->route('login');
        }

        $idPasien = session('id_pasien');
        $pendaftaran = Pendaftaran::with([
            'pasien',
            'poli',
            'dokter',
            'transaksi.kasir',
            'transaksi.layanan',
            'rekamMedis.tindakanMedis.layanan',
            'resep.details.obat',
        ])
        ->where('id_pasien', $idPasien)
        ->findOrFail($id);

        $transaksi = $pendaftaran->transaksi;
        if (!$transaksi) {
            return back()->with('error', 'Kunjungan ini belum memiliki catatan transaksi kasir.');
        }

        return view('pasien.kuitansi', compact('pendaftaran', 'transaksi'));
    }

    /**
     * Perbarui Profil dan Data Kontak/Akun Pasien Mandiri
     */
    public function updateProfil(Request $request)
    {
        if (session('user_type') !== 'pasien') {
            return redirect()->route('login');
        }

        $idPasien = session('id_pasien');
        $pasien = Pasien::findOrFail($idPasien);

        $validated = $request->validate([
            'nama_pasien' => 'required|string|max:100',
            'username'    => 'nullable|string|max:50|unique:pasien,username,' . $pasien->id_pasien . ',id_pasien',
            'no_hp'       => 'nullable|string|max:20',
            'alamat'      => 'nullable|string|max:255',
            'password'    => 'nullable|string|min:4|confirmed',
        ], [
            'nama_pasien.required' => 'Nama lengkap pasien wajib diisi.',
            'username.unique'      => 'Username ini sudah digunakan oleh akun lain.',
            'password.min'         => 'Password baru minimal 4 karakter.',
            'password.confirmed'   => 'Konfirmasi password baru tidak cocok.',
        ]);

        $pasien->nama_pasien = $validated['nama_pasien'];
        if (isset($validated['username'])) {
            $pasien->username = $validated['username'];
        }
        $pasien->no_hp = $validated['no_hp'] ?? null;
        $pasien->alamat = $validated['alamat'] ?? null;

        if (!empty($validated['password'])) {
            $pasien->password_hash = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }

        $pasien->save();

        session(['nama_pasien' => $pasien->nama_pasien]);

        return back()->with('success', 'Profil dan informasi akun Anda berhasil diperbarui!');
    }
}
