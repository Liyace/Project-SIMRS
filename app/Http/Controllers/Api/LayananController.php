<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Layanan;

class LayananController extends Controller
{
    public function index()
    {
        $layanan = Layanan::all();
        return response()->json([
            'status' => true,
            'message' => 'Data layanan berhasil diambil',
            'data' => $layanan
        ]);
    }
    public function show($id)
    {
        $layanan = Layanan::find($id);
        if ($layanan) {
            return response()->json([
                'status' => true,
                'message' => 'Data layanan berhasil diambil',
                'data' => $layanan
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Data layanan tidak ditemukan',
                'data' => null
            ], 404);
        }
    }
    public function store(Request $request)
    {
        $layanan = Layanan::create($request->all());
        return response()->json([
            'status' => true,
            'message' => 'Data layanan berhasil ditambahkan',
            'data' => $layanan
        ], 201);
    }
    public function update(Request $request, $id)
    {
        $layanan = Layanan::find($id);
        if ($layanan) {
            $layanan->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data layanan berhasil diperbarui',
                'data' => $layanan
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Data layanan tidak ditemukan',
                'data' => null
            ], 404);
        }
    }
    public function destroy($id)
    {
        $layanan = Layanan::find($id);
        if ($layanan) {
            $layanan->delete();
            return response()->json([
                'status' => true,
                'message' => 'Data layanan berhasil dihapus',
                'data' => null
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Data layanan tidak ditemukan',
                'data' => null
            ], 404);
        }
    }
}
