<?php

namespace App\Http\Middleware;

use Closure;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$permissions
     * @return mixed
     */
    public function handle($request, Closure $next, ...$permissions)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Kumpulkan semua permission yang diizinkan (mendukung variadic maupun pemisah koma/pipe)
        $permissionList = [];
        foreach ($permissions as $p) {
            foreach (preg_split('/[,|]/', (string) $p) as $item) {
                $item = trim($item);
                if (!empty($item)) {
                    $permissionList[] = $item;
                }
            }
        }

        // Cek apakah user memiliki salah satu dari permission yang diizinkan
        $hasAccess = false;
        foreach ($permissionList as $p) {
            if (auth()->user()->hasPermission($p)) {
                $hasAccess = true;
                break;
            }
        }

        if (!$hasAccess) {
            $required = implode(' atau ', $permissionList);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Anda tidak memiliki izin untuk aksi ini (' . $required . ').'
                ], 403);
            }

            abort(403, 'Anda tidak memiliki hak akses / izin (' . $required . ') untuk halaman ini.');
        }

        return $next($request);
    }
}
