<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SiteClosedMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $isClosed = Cache::get('site_closed_mode', false);

        if ($isClosed) {
            // 1. Selalu izinkan halaman login & logout agar admin bisa masuk ke sistem
            if ($request->is('login') || $request->is('logout') || $request->is('login*')) {
                return $next($request);
            }

            // 2. CARA PALING AMPUH: Jika user sudah login DAN role-nya adalah 'admin',
            // Jangan gembok mereka, biarkan mereka akses URL apa saja dengan bebas!
            if (auth()->check() && auth()->user()->role === 'admin') {
                return $next($request);
            }

            // 3. (Opsional Tambahan) Jika kamu belum login tapi sedang mengakses URL admin/manage-exams,
            // biarkan lewat dulu supaya nanti diarahkan ke halaman login oleh middleware auth bawaan
            if ($request->is('manage-exams*') || $request->is('admin*')) {
                return $next($request);
            }

            // Jika bukan admin, kunci dan lempar ke halaman penangguhan
            return response()->view('closed.index'); 
        }

        return $next($request);
    }
}