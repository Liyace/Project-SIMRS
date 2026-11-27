<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;

class TransaksiController extends Controller
{
    public function index()
    {
        $transaksi = Transaksi::all();
        return response()->json([
            'status' => true,
            'message' => 'Data transaksi berhasil diambil',
            'data' => $transaksi
        ]);
    }
    public function show($id)
    {
        $transaksi = Transaksi::find($id);
        if ($transaksi) {
            return response()->json([
                'status' => true,
                'message' => 'Data transaksi berhasil diambil',
                'data' => $transaksi
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Data transaksi tidak ditemukan',
                'data' => null
            ], 404);
        }
    }
    public function store(Request $request)
    {
        $transaksi = Transaksi::create($request->all());
        return response()->json([
            'status' => true,
            'message' => 'Data transaksi berhasil disimpan',
            'data' => $transaksi
        ], 201);
    }
    public function update(Request $request, $id)
    {
        $transaksi = Transaksi::find($id);
        if ($transaksi) {
            $transaksi->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data transaksi berhasil diperbarui',
                'data' => $transaksi
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Data transaksi tidak ditemukan',
                'data' => null
            ], 404);
        }
    }
    public function destroy($id)
    {
        $transaksi = Transaksi::find($id);
        if ($transaksi) {
            $transaksi->delete();
            return response()->json([
                'status' => true,
                'message' => 'Data transaksi berhasil dihapus',
                'data' => null
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Data transaksi tidak ditemukan',
                'data' => null
            ], 404);
        }
    }
}
