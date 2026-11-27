<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Pengganti blok "PROTEKSI HAK AKSES PER ROLE" di index.php lama, yang dulu
 * hard-code seperti ini:
 *
 *   $halaman_khusus_admin = ['kelola', 'laporan', 'cetak'];
 *   $halaman_khusus_kasir = ['antrian'];
 *   if ($role_sekarang === 'Kasir' && in_array($page, $halaman_khusus_admin)) { ...tendang... }
 *
 * Sekarang jadi middleware dengan parameter, dipakai di route seperti:
 *   Route::get('/kelola', ...)->middleware('role:Admin Kasir');
 *   Route::get('/antrian', ...)->middleware('role:Kasir');
 *
 * Bisa terima lebih dari satu role yang diizinkan, dipisah koma:
 *   ->middleware('role:Admin Kasir,Kasir')
 */
class BatasiAksesRole
{
    public function handle(Request $request, Closure $next, string ...$rolesDiizinkan)
    {
        $roleSekarang = strtolower(session('role_aktif', ''));
        $user = auth()->user();
        $isKasirUtama = session('is_kasir_utama') ?? ($user && method_exists($user, 'isKasirUtama') ? $user->isKasirUtama() : false);

        // Pecah parameter jika dipisah koma (misal: "role:kasir,admin kasir")
        $rolesNormalized = [];
        foreach ($rolesDiizinkan as $r) {
            foreach (explode(',', $r) as $part) {
                $rolesNormalized[] = strtolower(trim($part));
            }
        }

        // Cek kecocokan role dasar
        $isAuthorized = in_array($roleSekarang, $rolesNormalized, true);

        // Kasir Utama / Admin Kasir otomatis diizinkan mengakses rute operasional 'kasir'
        if ($isKasirUtama && in_array('kasir', $rolesNormalized, true)) {
            $isAuthorized = true;
        }

        // Jika rute ini KHUSUS untuk kasir utama / admin kasir (tidak mengizinkan 'kasir' biasa)
        $onlyForKasirUtama = (in_array('kasir_utama', $rolesNormalized, true)
                           || in_array('admin_kasir', $rolesNormalized, true)
                           || in_array('admin kasir', $rolesNormalized, true))
                           && !in_array('kasir', $rolesNormalized, true);

        if ($onlyForKasirUtama && !$isKasirUtama) {
            $isAuthorized = false;
        }

        if (!$isAuthorized) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak: Anda tidak memiliki wewenang untuk mengakses menu tersebut.');
        }

        return $next($request);
    }
}
