<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Pengganti bagian ini di index.php lama:
 *
 *   if (!in_array($page, $public_pages) && !isset($_SESSION['role_aktif'])) {
 *       header("Location: index.php?page=daftar-role");
 *   }
 *
 * Dipasang di routes/web.php lewat ->middleware('role.aktif')
 */

class PastikanRoleAktif
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('role_aktif')) {
            if (auth()->check()) {
                session(['role_aktif' => strtolower(auth()->user()->role ?? 'kasir')]);
            } else {
                return redirect()->route('login');
            }
        }

        return $next($request);
    }
}
