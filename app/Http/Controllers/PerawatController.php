<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use App\Models\ScreeningAwal;
use Illuminate\Http\Request;

class PerawatController extends Controller
{
    /**
     * Tampilan antrean pasien poliklinik yang menunggu skrining awal TTV oleh Perawat
     */
    public function index(Request $request)
    {
        $tanggal = $request->query('tanggal', today()->toDateString());

        // Antrean pasien yang menunggu skrining tanda vital (dengan paginasi 5 data per halaman)
        $antreanMenunggu = Pendaftaran::with(['pasien', 'poli'])
            ->where('status_antrian', 'menunggu_skrining')
            ->whereDate('tanggal_daftar', $tanggal)
            ->orderBy('id_pendaftaran', 'asc')
            ->paginate(5, ['*'], 'page_antrean')
            ->withQueryString();

        // Riwayat pasien yang sudah diskrining hari ini (dengan paginasi 10 data per halaman)
        $riwayatSkrining = Pendaftaran::with(['pasien', 'poli', 'screeningAwal.perawat'])
            ->whereHas('screeningAwal')
            ->whereDate('tanggal_daftar', $tanggal)
            ->orderByDesc('id_pendaftaran')
            ->paginate(10, ['*'], 'page_riwayat')
            ->withQueryString();

        // Hitung total data harian tanpa terpotong paginasi
        $totalMenunggu = Pendaftaran::where('status_antrian', 'menunggu_skrining')
            ->whereDate('tanggal_daftar', $tanggal)
            ->count();
        $totalSelesai = Pendaftaran::whereHas('screeningAwal')
            ->whereDate('tanggal_daftar', $tanggal)
            ->count();

        $stats = [
            'menunggu' => $totalMenunggu,
            'selesai'  => $totalSelesai,
            'total'    => $totalMenunggu + $totalSelesai,
        ];

        // Deteksi jika perawat mengklik pasien langsung dari notifikasi topbar (?pilih=ID)
        $pasienTerpilih = null;
        if ($request->filled('pilih')) {
            $pilihModel = Pendaftaran::with(['pasien', 'poli'])->find($request->query('pilih'));
            if ($pilihModel && $pilihModel->status_antrian === 'menunggu_skrining') {
                $pasienTerpilih = [
                    'id'         => $pilihModel->id_pendaftaran,
                    'no_antrian' => $pilihModel->no_antrian,
                    'nama'       => $pilihModel->pasien?->nama_pasien ?? 'Pasien',
                    'rm'         => $pilihModel->pasien?->rm ?? '-',
                    'poli'       => $pilihModel->poli?->nama_poli ?? 'Poli Umum',
                    'keluhan'    => $pilihModel->keluhan_awal ?? '',
                    'umur'       => $pilihModel->pasien?->tanggal_lahir ? \Carbon\Carbon::parse($pilihModel->pasien->tanggal_lahir)->age . ' tahun' : '-',
                ];
            }
        }

        return view('perawat.skrining', compact('antreanMenunggu', 'riwayatSkrining', 'stats', 'tanggal', 'pasienTerpilih'));
    }

    /**
     * Simpan data pemeriksaan tanda-tanda vital (TTV) dan teruskan ke Dokter
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_pendaftaran'  => 'required|exists:pendaftaran,id_pendaftaran',
            'sistol'          => 'required|numeric|min:50|max:260',
            'diastol'         => 'required|numeric|min:30|max:180',
            'suhu_tubuh'      => 'required|numeric|min:30|max:45',
            'berat_badan'     => 'required|numeric|min:1|max:300',
            'tinggi_badan'    => 'required|numeric|min:20|max:250',
            'denyut_nadi'     => 'required|numeric|min:30|max:220',
            'catatan_perawat' => 'nullable|string|max:500',
        ], [
            'sistol.required'       => 'Nilai sistol (tensi atas) wajib diisi.',
            'diastol.required'      => 'Nilai diastol (tensi bawah) wajib diisi.',
            'suhu_tubuh.required'   => 'Suhu tubuh pasien wajib diisi.',
            'berat_badan.required'  => 'Berat badan pasien wajib diisi.',
            'tinggi_badan.required' => 'Tinggi badan pasien wajib diisi.',
            'denyut_nadi.required'  => 'Denyut nadi pasien wajib diisi.',
        ]);

        $pendaftaran = Pendaftaran::with('pasien')->findOrFail($data['id_pendaftaran']);

        ScreeningAwal::updateOrCreate(
            ['id_pendaftaran' => $pendaftaran->id_pendaftaran],
            [
                'id_perawat'      => auth()->id(),
                'sistol'          => $data['sistol'],
                'diastol'         => $data['diastol'],
                'suhu_tubuh'      => $data['suhu_tubuh'],
                'berat_badan'     => $data['berat_badan'],
                'tinggi_badan'    => $data['tinggi_badan'],
                'denyut_nadi'     => $data['denyut_nadi'],
                'catatan_perawat' => $data['catatan_perawat'] ?? null,
            ]
        );

        // Alihkan status antrean pendaftaran ke Dokter
        $pendaftaran->update(['status_antrian' => 'menunggu_dokter']);

        $namaPasien = $pendaftaran->pasien?->nama_pasien ?? 'Pasien';

        return redirect()->route('perawat.skrining')
            ->with('status_skrining', "TTV pasien {$namaPasien} ({$pendaftaran->no_antrian}) berhasil disimpan dan diteruskan ke antrean Dokter!");
    }

    /**
     * Endpoint API internal: Mengambil notifikasi antrean pasien yang menunggu skrining TTV
     */
    public function getNotifikasi(Request $request)
    {
        $tanggal = $request->query('tanggal', today()->toDateString());

        $pasienList = Pendaftaran::with(['pasien', 'poli'])
            ->where('status_antrian', 'menunggu_skrining')
            ->whereDate('tanggal_daftar', $tanggal)
            ->orderBy('id_pendaftaran', 'asc')
            ->get()
            ->map(function ($p) {
                return [
                    'id_pendaftaran' => $p->id_pendaftaran,
                    'no_antrian'     => $p->no_antrian,
                    'nama_pasien'    => $p->pasien?->nama_pasien ?? 'Pasien',
                    'no_rm'          => $p->pasien?->rm ?? '-',
                    'nama_poli'      => $p->poli?->nama_poli ?? 'Poli Umum',
                    'keluhan'        => $p->keluhan_awal ?? 'Tidak ada keluhan khusus',
                    'waktu_daftar'   => $p->tanggal_daftar ? $p->tanggal_daftar->format('H:i') : '-',
                    'url_skrining'   => route('perawat.skrining', ['pilih' => $p->id_pendaftaran]),
                ];
            });

        return response()->json([
            'success'   => true,
            'count'     => $pasienList->count(),
            'pasien'    => $pasienList,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
