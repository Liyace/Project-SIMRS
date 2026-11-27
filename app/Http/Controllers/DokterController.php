<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\Obat;
use App\Models\Pendaftaran;
use App\Models\RekamMedis;
use App\Models\Resep;
use App\Models\ResepDetail;
use App\Models\TindakanMedis;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DokterController extends Controller
{
    /**
     * Dashboard & Antrean Pelayanan Pasien Poliklinik oleh Dokter
     */
    public function index(Request $request)
    {
        $tanggal = $request->query('tanggal', today()->toDateString());
        $user = auth()->user();

        // Ambil daftar poliklinik untuk switcher filter
        $polis = \App\Models\Poli::orderBy('nama_poli')->get();

        // Tentukan filter poli aktif:
        // Default ke id_poli dokter jika ada dan parameter 'poli' tidak disertakan.
        // Jika parameter 'poli' bernilai 'semua', tampilkan seluruh poli tanpa filter.
        $filterPoli = $request->query('poli');
        if ($filterPoli === null && $user && $user->id_poli) {
            $filterPoli = (string) $user->id_poli;
        }

        // Query Antrean Menunggu
        $queryMenunggu = Pendaftaran::with(['pasien', 'poli', 'screeningAwal.perawat'])
            ->whereIn('status_antrian', ['menunggu_dokter', 'sedang_diperiksa'])
            ->whereDate('tanggal_daftar', $tanggal);

        // Query Riwayat Selesai
        $querySelesai = Pendaftaran::with(['pasien', 'poli', 'rekamMedis.tindakanMedis.layanan', 'resep.details.obat'])
            ->whereIn('status_antrian', ['menunggu_kasir', 'menunggu_obat', 'selesai'])
            ->whereDate('tanggal_daftar', $tanggal);

        if ($filterPoli && $filterPoli !== 'semua') {
            $queryMenunggu->where(function ($q) use ($filterPoli, $user) {
                $q->where('id_poli', $filterPoli);
                if ($user) {
                    $q->orWhere('id_dokter', $user->id_pegawai);
                }
            });

            $querySelesai->where(function ($q) use ($filterPoli, $user) {
                $q->where('id_poli', $filterPoli);
                if ($user) {
                    $q->orWhere('id_dokter', $user->id_pegawai);
                }
            });
        }

        $antreanMenunggu = $queryMenunggu
            ->orderBy('id_pendaftaran', 'asc')
            ->paginate(5, ['*'], 'page_antrean')
            ->withQueryString();

        $riwayatSelesai = $querySelesai
            ->orderByDesc('id_pendaftaran')
            ->paginate(10, ['*'], 'page_riwayat')
            ->withQueryString();

        $totalMenungguQuery = Pendaftaran::whereIn('status_antrian', ['menunggu_dokter', 'sedang_diperiksa'])
            ->whereDate('tanggal_daftar', $tanggal);
        $totalSelesaiQuery = Pendaftaran::whereIn('status_antrian', ['menunggu_kasir', 'menunggu_obat', 'selesai'])
            ->whereDate('tanggal_daftar', $tanggal);

        if ($filterPoli && $filterPoli !== 'semua') {
            $totalMenungguQuery->where(function ($q) use ($filterPoli, $user) {
                $q->where('id_poli', $filterPoli);
                if ($user) {
                    $q->orWhere('id_dokter', $user->id_pegawai);
                }
            });
            $totalSelesaiQuery->where(function ($q) use ($filterPoli, $user) {
                $q->where('id_poli', $filterPoli);
                if ($user) {
                    $q->orWhere('id_dokter', $user->id_pegawai);
                }
            });
        }

        $totalMenunggu = $totalMenungguQuery->count();
        $totalSelesai = $totalSelesaiQuery->count();

        $stats = [
            'menunggu' => $totalMenunggu,
            'selesai'  => $totalSelesai,
            'total'    => $totalMenunggu + $totalSelesai,
        ];

        $dokterPoliAktif = $user ? $user->poli : null;

        return view('dokter.index', compact('antreanMenunggu', 'riwayatSelesai', 'stats', 'tanggal', 'polis', 'filterPoli', 'dokterPoliAktif'));
    }

    /**
     * Halaman form pemeriksaan klinis, rekam medis, tindakan medis & e-resep
     */
    public function periksa($idPendaftaran)
    {
        $pendaftaran = Pendaftaran::with(['pasien', 'poli', 'screeningAwal.perawat', 'rekamMedis.tindakanMedis', 'resep.details'])
            ->findOrFail($idPendaftaran);

        // Ubah status antrean menjadi 'sedang_diperiksa' jika masih 'menunggu_dokter'
        if ($pendaftaran->status_antrian === 'menunggu_dokter') {
            $pendaftaran->update(['status_antrian' => 'sedang_diperiksa']);
        }

        $masterLayanan = Layanan::orderBy('nama_layanan')->get();
        $masterObat    = Obat::where('stok', '>', 0)->orderBy('nama_obat')->get();

        // Hitung BMI jika data TTV tersedia
        $screening = $pendaftaran->screeningAwal;
        $bmiData = null;
        if ($screening && $screening->berat_badan > 0 && $screening->tinggi_badan > 0) {
            $tbMeter = $screening->tinggi_badan / 100;
            $bmi = round($screening->berat_badan / ($tbMeter * $tbMeter), 1);
            $kategori = match (true) {
                $bmi < 18.5 => ['status' => 'Berat Badan Kurang (Underweight)', 'color' => '#3b82f6'],
                $bmi <= 24.9 => ['status' => 'Berat Badan Normal (Ideal)', 'color' => '#10b981'],
                $bmi <= 29.9 => ['status' => 'Berat Badan Berlebih (Overweight)', 'color' => '#f59e0b'],
                default     => ['status' => 'Obesitas (Perlu Perhatian)', 'color' => '#ef4444'],
            };
            $bmiData = [
                'nilai'    => $bmi,
                'status'   => $kategori['status'],
                'color'    => $kategori['color'],
            ];
        }

        return view('dokter.periksa', compact('pendaftaran', 'masterLayanan', 'masterObat', 'bmiData'));
    }

    /**
     * Simpan hasil pemeriksaan dokter (Rekam Medis, Tindakan Medis & E-Resep)
     * Otomatis memindahkan status antrean ke 'menunggu_kasir'
     */
    public function simpanPemeriksaan(Request $request, $idPendaftaran)
    {
        $pendaftaran = Pendaftaran::with('pasien')->findOrFail($idPendaftaran);

        $data = $request->validate([
            'anamnesis'         => 'required|string|max:2000',
            'diagnosa_utama'    => 'required|string|max:255',
            'kode_icd10'        => 'nullable|string|max:20',
            'diagnosa_sekunder' => 'nullable|string|max:255',
            'catatan_edukasi'   => 'nullable|string|max:2000',
            'tindakan'          => 'nullable|array',
            'tindakan.*'        => 'exists:layanan,id_layanan',
            'obat'              => 'nullable|array',
            'obat.*.id_obat'    => 'required_with:obat|exists:obat,id_obat',
            'obat.*.jumlah'     => 'required_with:obat|integer|min:1',
            'obat.*.aturan'     => 'required_with:obat|string|max:100',
            'catatan_resep'     => 'nullable|string|max:500',
        ], [
            'anamnesis.required'      => 'Anamnesis / keluhan klinis wajib diisi oleh dokter.',
            'diagnosa_utama.required' => 'Diagnosa utama wajib ditentukan oleh dokter.',
        ]);

        DB::beginTransaction();
        try {
            // 1. Simpan Rekam Medis Dokter
            $rekamMedis = RekamMedis::updateOrCreate(
                ['id_pendaftaran' => $pendaftaran->id_pendaftaran],
                [
                    'id_dokter'         => auth()->id(),
                    'anamnesis'         => $data['anamnesis'],
                    'diagnosa_utama'    => $data['diagnosa_utama'],
                    'kode_icd10'        => !empty($data['kode_icd10']) ? strtoupper(trim($data['kode_icd10'])) : null,
                    'diagnosa_sekunder' => $data['diagnosa_sekunder'] ?? null,
                    'terapi_tindakan'   => $data['diagnosa_utama'] . ' - Diberikan terapi medikamentosa',
                    'catatan_edukasi'   => $data['catatan_edukasi'] ?? null,
                ]
            );

            // 2. Simpan Tindakan Medis Dokter
            // Bersihkan tindakan lama jika ada revisi
            TindakanMedis::where('id_rekam_medis', $rekamMedis->id_rekam_medis)->delete();

            if (!empty($data['tindakan'])) {
                foreach ($data['tindakan'] as $idLayanan) {
                    $layanan = Layanan::find($idLayanan);
                    if ($layanan) {
                        TindakanMedis::create([
                            'id_rekam_medis' => $rekamMedis->id_rekam_medis,
                            'id_layanan'     => $layanan->id_layanan,
                            'qty'            => 1,
                            'tarif_saat_ini' => $layanan->harga_layanan,
                            'subtotal'       => $layanan->harga_layanan,
                        ]);
                    }
                }
            }

            // 3. Simpan E-Resep Obat Dokter
            if (!empty($data['obat']) && count($data['obat']) > 0) {
                $noResep = 'RSP-' . date('Ymd') . '-' . str_pad($pendaftaran->id_pendaftaran, 3, '0', STR_PAD_LEFT);
                $totalHargaObat = 0;

                $resep = Resep::updateOrCreate(
                    ['id_pendaftaran' => $pendaftaran->id_pendaftaran],
                    [
                        'no_resep'         => $noResep,
                        'id_dokter'        => auth()->id(),
                        'total_harga_obat' => 0,
                        'status_resep'     => 'menunggu_pembayaran',
                        'catatan_dokter'   => $data['catatan_resep'] ?? 'Diminum sesuai aturan pakai.',
                    ]
                );

                // Bersihkan detail resep lama jika ada revisi
                ResepDetail::where('id_resep', $resep->id_resep)->delete();

                foreach ($data['obat'] as $itemObat) {
                    $obatModel = Obat::find($itemObat['id_obat']);
                    if ($obatModel) {
                        $harga = (float) $obatModel->harga_obat;
                        $jml = (int) $itemObat['jumlah'];
                        $subtotal = $harga * $jml;
                        $totalHargaObat += $subtotal;

                        ResepDetail::create([
                            'id_resep'     => $resep->id_resep,
                            'id_obat'      => $obatModel->id_obat,
                            'jumlah'       => $jml,
                            'aturan_pakai' => $itemObat['aturan'],
                            'harga_satuan' => $harga,
                            'subtotal'     => $subtotal,
                        ]);
                    }
                }

                $resep->update(['total_harga_obat' => $totalHargaObat]);
            }

            // 4. Update status antrean pendaftaran ke Loket Kasir
            $pendaftaran->update(['status_antrian' => 'menunggu_kasir']);

            DB::commit();

            $namaPasien = $pendaftaran->pasien?->nama_pasien ?? 'Pasien';

            return redirect()->route('dokter.pelayanan')
                ->with('status_pemeriksaan', "Pemeriksaan pasien {$namaPasien} ({$pendaftaran->no_antrian}) selesai! Rekam medis, tindakan, dan e-resep otomatis diteruskan ke loket kasir.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Gagal menyimpan pemeriksaan: ' . $e->getMessage()]);
        }
    }

    /**
     * Endpoint API internal: Mengambil notifikasi antrean pasien yang sudah selesai diskrining perawat dan siap diperiksa dokter
     */
    public function getNotifikasi(Request $request)
    {
        $tanggal = $request->query('tanggal', today()->toDateString());
        $user = auth()->user();
        $filterPoli = $request->query('poli');

        $query = Pendaftaran::with(['pasien', 'poli', 'screeningAwal'])
            ->whereIn('status_antrian', ['menunggu_dokter', 'sedang_diperiksa'])
            ->whereDate('tanggal_daftar', $tanggal);

        if ($filterPoli && $filterPoli !== 'semua') {
            $query->where(function ($q) use ($filterPoli, $user) {
                $q->where('id_poli', $filterPoli);
                if ($user) {
                    $q->orWhere('id_dokter', $user->id_pegawai);
                }
            });
        } elseif (!$request->has('poli') && $user && $user->id_poli) {
            $query->where(function ($q) use ($user) {
                $q->where('id_poli', $user->id_poli)
                  ->orWhere('id_dokter', $user->id_pegawai);
            });
        }

        $pasienList = $query->orderBy('id_pendaftaran', 'asc')
            ->get()
            ->map(function ($p) {
                $ttv = $p->screeningAwal;
                $ttvSummary = $ttv ? "TD: {$ttv->sistol}/{$ttv->diastol} • S: {$ttv->suhu_tubuh}°C" : 'Menunggu pemeriksaan';
                return [
                    'id_pendaftaran' => $p->id_pendaftaran,
                    'no_antrian'     => $p->no_antrian,
                    'nama_pasien'    => $p->pasien?->nama_pasien ?? 'Pasien',
                    'no_rm'          => $p->pasien?->rm ?? '-',
                    'nama_poli'      => $p->poli?->nama_poli ?? 'Poli Umum',
                    'keluhan'        => $p->keluhan_awal ?? 'Tidak ada keluhan khusus',
                    'ttv'            => $ttvSummary,
                    'waktu_daftar'   => $p->tanggal_daftar ? $p->tanggal_daftar->format('H:i') : '-',
                    'url_periksa'    => route('dokter.periksa', $p->id_pendaftaran),
                ];
            });

        return response()->json([
            'success'   => true,
            'count'     => $pasienList->count(),
            'pasien'    => $pasienList,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Cetak Surat Keterangan Sakit / Istirahat Medis (PDF & Web Print)
     */
    public function cetakSuratSakit($id, Request $request)
    {
        $pendaftaran = Pendaftaran::with([
            'pasien',
            'poli',
            'dokter',
            'rekamMedis',
        ])->findOrFail($id);

        $lamaHari = (int) $request->query('hari', 3);
        if ($lamaHari < 1) $lamaHari = 1;
        if ($lamaHari > 14) $lamaHari = 14;

        $tglMulaiStr = $request->query('mulai', $pendaftaran->tanggal_daftar ? Carbon::parse($pendaftaran->tanggal_daftar)->toDateString() : today()->toDateString());
        $tglMulai = Carbon::parse($tglMulaiStr);
        $tglSelesai = (clone $tglMulai)->addDays($lamaHari - 1);

        $nomorSurat = '440/SKD/' . $tglMulai->format('m/Y') . '/' . str_pad($pendaftaran->id_pendaftaran, 4, '0', STR_PAD_LEFT);
        $dokter = $pendaftaran->dokter ?? auth()->user();

        $data = compact(
            'pendaftaran',
            'lamaHari',
            'tglMulai',
            'tglSelesai',
            'nomorSurat',
            'dokter'
        );

        if ($request->query('download') === '1' || $request->query('pdf') === '1') {
            $pdf = Pdf::loadView('dokter.surat_sakit', array_merge($data, ['isPdf' => true]))->setPaper('a4', 'portrait');
            $safeName = preg_replace('/[^A-Za-z0-9_-]/', '_', $pendaftaran->pasien?->nama_pasien ?? 'Pasien');
            return $pdf->download("Surat_Sakit_{$safeName}.pdf");
        }

        return view('dokter.surat_sakit', array_merge($data, ['isPdf' => false]));
    }

    /**
     * Cetak Ringkasan Resume Medis Rawat Jalan (Discharge Summary PDF)
     */
    public function cetakResumeMedis($id, Request $request)
    {
        $pendaftaran = Pendaftaran::with([
            'pasien',
            'poli',
            'dokter',
            'screeningAwal.perawat',
            'rekamMedis.tindakanMedis.layanan',
            'resep.details.obat',
        ])->findOrFail($id);

        $nomorResume = 'RMJ/' . date('Y/m') . '/' . str_pad($pendaftaran->id_pendaftaran, 4, '0', STR_PAD_LEFT);
        $dokter = $pendaftaran->dokter ?? auth()->user();

        $data = compact('pendaftaran', 'nomorResume', 'dokter');

        if ($request->query('download') === '1' || $request->query('pdf') === '1') {
            $pdf = Pdf::loadView('dokter.resume_medis', array_merge($data, ['isPdf' => true]))->setPaper('a4', 'portrait');
            $safeName = preg_replace('/[^A-Za-z0-9_-]/', '_', $pendaftaran->pasien?->nama_pasien ?? 'Pasien');
            return $pdf->download("Resume_Medis_{$safeName}.pdf");
        }

        return view('dokter.resume_medis', array_merge($data, ['isPdf' => false]));
    }
}
