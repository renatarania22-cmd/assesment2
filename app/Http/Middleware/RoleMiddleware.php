<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check() || !in_array(Auth::user()->role, $roles)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Akses ditolak (403). Anda tidak memiliki wewenang untuk tindakan ini.'
                ], 403);
            }
            abort(403, 'Akses ditolak (403). Anda tidak memiliki wewenang untuk tindakan ini.');
        }

        return $next($request);
    }
}