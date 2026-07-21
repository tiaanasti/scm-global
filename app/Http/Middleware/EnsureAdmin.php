<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || ($user->role ?? 'user') !== 'admin') {
            abort(403, 'Halaman admin hanya dapat diakses oleh administrator.');
        }

        return $next($request);
    }
}
