<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckOwnerStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user || !$user->isOwner()) {
            abort(403, 'Owner access only.');
        }

        if ($user->status === 'pending') {
            return redirect()->route('owner.pending');
        }

        if ($user->status === 'rejected') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->withErrors([
                'email' => 'Your account has been rejected. Please contact support.',
            ]);
        }

        return $next($request);
    }
}
