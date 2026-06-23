<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        // Block rejected owners before they reach any dashboard
        if ($user->isOwner() && $user->status === 'rejected') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->withErrors([
                'email' => 'Your account has been rejected. Please contact support.',
            ]);
        }

        return match ($user->role) {
            'admin'       => redirect()->route('admin.dashboard'),
            'owner'       => redirect()->route('owner.dashboard'),
            'maintenance' => redirect()->route('maintenance.dashboard'),
            default       => redirect()->route('student.dashboard'),
        };
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $user = Socialite::driver('google')->user();

        $findUser = User::where('google_id', $user->id)->first();

        if ($findUser) {
            Auth::login($findUser);
        } else {
            $newUser = User::create([
                'name' => $user->name,
                'email' => $user->email,
                'google_id' => $user->id,
                'password' => encrypt('123456dummy')
            ]);
            Auth::login($newUser);
        }
        return redirect('/dashboard');
    }
}
