<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMaintenance
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isMaintenance()) {
            abort(403, 'Unauthorized. Maintenance technicians only.');
        }

        return $next($request);
    }
}
