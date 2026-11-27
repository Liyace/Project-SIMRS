<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use Illuminate\Http\Request;

class KelolaController extends Controller
{
    // GET /admin/layanan
    public function index()
    {
        $layananList = Layanan::orderBy('id_layanan')->get();

        return view('kelola.index', compact('layananList'));
    }

    // POST /admin/layanan
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_layanan'  => 'required|string|max:255',
            'harga_layanan' => 'required|numeric|min:0',
        ]);

        $layanan = Layanan::create($data);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Layanan baru berhasil ditambahkan.',
                'data'    => $layanan,
            ]);
        }

        return redirect()->route('admin.layanan')->with('success', 'Layanan baru berhasil ditambahkan.');
    }

    // PUT /admin/layanan/{layanan}
    public function update(Request $request, Layanan $layanan)
    {
        $data = $request->validate([
            'nama_layanan'  => 'required|string|max:255',
            'harga_layanan' => 'required|numeric|min:0',
        ]);

        $layanan->update($data);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Layanan berhasil diperbarui.',
                'data'    => $layanan,
            ]);
        }

        return redirect()->route('admin.layanan')->with('success', 'Layanan berhasil diperbarui.');
    }

    // DELETE /admin/layanan/{layanan}
    public function destroy(Request $request, Layanan $layanan)
    {
        $nama = $layanan->nama_layanan;
        $layanan->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Layanan '{$nama}' berhasil dihapus.",
            ]);
        }

        return redirect()->route('admin.layanan')->with('success', "Layanan '{$nama}' berhasil dihapus.");
    }
}
