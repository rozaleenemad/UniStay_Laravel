<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class OwnerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role === 'owner') {
            return $next($request);
        }


        if (Auth::check() && method_exists(Auth::user(), 'isImpersonating') && Auth::user()->isImpersonating()) {
            return $next($request);
        }

        abort(403, 'Unauthorized action.');
    }
}
