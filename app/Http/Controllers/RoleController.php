<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Pengganti pages/daftar-role.php + blok `$page === 'proses-role'` di index.php lama.
 */
class RoleController extends Controller
{
    // GET /daftar-role -> Bypassed langsung ke modul kerja pengguna
    public function tampilkan()
    {
        $role = strtolower(auth()->user()->role ?? 'kasir');
        session(['role_aktif' => $role]);

        if (in_array($role, ['admin', 'administrator'])) {
            return redirect()->route('admin.pengguna');
        }

        if ($role === 'perawat') {
            return redirect()->route('perawat.skrining');
        }

        if ($role === 'dokter') {
            return redirect()->route('dokter.pelayanan');
        }

        if ($role === 'loket') {
            return redirect()->route('loket.index');
        }

        if ($role === 'apoteker') {
            return redirect()->route('apoteker.resep');
        }

        if ($role === 'resepsionis') {
            return redirect()->route('resepsionis.index');
        }

        return redirect()->route('dashboard');
    }

    // GET /proses-role?role=... -> dulu blok "proses-role" di index.php
    public function proses(Request $request)
    {
        $role = strtolower($request->query('role', auth()->user()->role ?? 'kasir'));
        session(['role_aktif' => $role]);

        if (in_array($role, ['admin', 'administrator'])) {
            return redirect()->route('admin.pengguna');
        }

        if ($role === 'perawat') {
            return redirect()->route('perawat.skrining');
        }

        if ($role === 'dokter') {
            return redirect()->route('dokter.pelayanan');
        }

        if ($role === 'loket') {
            return redirect()->route('loket.index');
        }

        if ($role === 'apoteker') {
            return redirect()->route('apoteker.resep');
        }

        if ($role === 'resepsionis') {
            return redirect()->route('resepsionis.index');
        }

        return redirect()->route('dashboard');
    }
}
