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
     * @param  string  $permission
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Cek izin permission (mendukung single atau multiple permission dipisah koma/pipe)
        $permissions = preg_split('/[,|]/', $permission);
        $hasAccess = false;
        foreach ($permissions as $p) {
            $p = trim($p);
            if (!empty($p) && auth()->user()->hasPermission($p)) {
                $hasAccess = true;
                break;
            }
        }

        if (!$hasAccess) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Anda tidak memiliki izin untuk aksi ini (' . $permission . ').'
                ], 403);
            }

            abort(403, 'Anda tidak memiliki hak akses / izin (' . $permission . ') untuk halaman ini.');
        }

        return $next($request);
    }
}
