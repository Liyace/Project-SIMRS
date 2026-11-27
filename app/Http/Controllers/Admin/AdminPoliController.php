<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Poli;
use Illuminate\Http\Request;

class AdminPoliController extends Controller
{
    /**
     * Tampilkan daftar Master Ruangan & Poliklinik RS Delta
     */
    public function index(Request $request)
    {
        $q = trim($request->input('q', ''));

        $query = Poli::withCount('pendaftaran')->with('dokters');
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('kode_poli', 'like', "%{$q}%")
                  ->orWhere('nama_poli', 'like', "%{$q}%");
            });
        }

        $polis = $query->orderBy('id_poli', 'asc')->paginate(10)->withQueryString();

        $semuaDokters = Pegawai::where('role', 'dokter')->orderBy('nama')->get();

        $stats = [
            'total_poli'        => Poli::count(),
            'rata_biaya'        => Poli::avg('biaya_konsultasi') ?? 0,
            'total_kunjungan'   => \App\Models\Pendaftaran::count(),
        ];

        return view('admin.poli.index', compact('polis', 'stats', 'q', 'semuaDokters'));
    }

    /**
     * Tambah Poliklinik Baru
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'kode_poli'        => 'required|string|max:10|uppercase|unique:poli,kode_poli',
            'nama_poli'        => 'required|string|max:100',
            'biaya_konsultasi' => 'required|numeric|min:0',
            'dokter_ids'       => 'nullable|array',
            'dokter_ids.*'     => 'exists:pegawai,id_pegawai',
        ], [
            'kode_poli.required' => 'Kode poliklinik wajib diisi.',
            'kode_poli.unique'   => 'Kode poliklinik sudah digunakan.',
            'nama_poli.required' => 'Nama poliklinik wajib diisi.',
            'biaya_konsultasi.required' => 'Biaya konsultasi wajib diisi.',
            'biaya_konsultasi.min' => 'Biaya konsultasi minimal Rp 0.',
        ]);

        $data['kode_poli'] = strtoupper(trim($data['kode_poli']));
        $poli = Poli::create([
            'kode_poli'        => $data['kode_poli'],
            'nama_poli'        => $data['nama_poli'],
            'biaya_konsultasi' => $data['biaya_konsultasi'],
        ]);

        if (!empty($data['dokter_ids'])) {
            Pegawai::where('role', 'dokter')->whereIn('id_pegawai', $data['dokter_ids'])->update(['id_poli' => $poli->id_poli]);
        }

        return redirect()->route('admin.poli')->with('success', "Poliklinik '{$poli->nama_poli}' berhasil ditambahkan.");
    }

    /**
     * Perbarui Data Poliklinik
     */
    public function update(Request $request, Poli $poli)
    {
        $data = $request->validate([
            'kode_poli'        => 'required|string|max:10|uppercase|unique:poli,kode_poli,' . $poli->id_poli . ',id_poli',
            'nama_poli'        => 'required|string|max:100',
            'biaya_konsultasi' => 'required|numeric|min:0',
            'dokter_ids'       => 'nullable|array',
            'dokter_ids.*'     => 'exists:pegawai,id_pegawai',
        ], [
            'kode_poli.required' => 'Kode poliklinik wajib diisi.',
            'kode_poli.unique'   => 'Kode poliklinik sudah digunakan.',
            'nama_poli.required' => 'Nama poliklinik wajib diisi.',
            'biaya_konsultasi.required' => 'Biaya konsultasi wajib diisi.',
            'biaya_konsultasi.min' => 'Biaya konsultasi minimal Rp 0.',
        ]);

        $data['kode_poli'] = strtoupper(trim($data['kode_poli']));
        $poli->update([
            'kode_poli'        => $data['kode_poli'],
            'nama_poli'        => $data['nama_poli'],
            'biaya_konsultasi' => $data['biaya_konsultasi'],
        ]);

        if ($request->has('dokter_ids')) {
            $selectedDokterIds = $data['dokter_ids'] ?? [];
            // Lepas dokter yang sebelumnya di poli ini tapi sekarang tidak dipilih
            Pegawai::where('id_poli', $poli->id_poli)->whereNotIn('id_pegawai', $selectedDokterIds)->update(['id_poli' => null]);
            // Tetapkan dokter terpilih ke poli ini
            if (!empty($selectedDokterIds)) {
                Pegawai::whereIn('id_pegawai', $selectedDokterIds)->update(['id_poli' => $poli->id_poli]);
            }
        }

        return redirect()->route('admin.poli')->with('success', "Data poliklinik '{$poli->nama_poli}' berhasil diperbarui.");
    }

    /**
     * Hapus Poliklinik (Aman dengan pengecekan relasi pendaftaran)
     */
    public function destroy(Poli $poli)
    {
        $kunjunganCount = $poli->pendaftaran()->count();
        if ($kunjunganCount > 0) {
            return redirect()->route('admin.poli')->with('error', "Gagal menghapus: Poliklinik '{$poli->nama_poli}' memiliki {$kunjunganCount} riwayat antrian/pendaftaran pasien.");
        }

        $poli->delete();

        return redirect()->route('admin.poli')->with('success', "Poliklinik '{$poli->nama_poli}' berhasil dihapus.");
    }
}
