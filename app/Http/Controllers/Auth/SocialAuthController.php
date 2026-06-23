<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'فشل تسجيل الدخول بـ Google، حاولي تاني.']);
        }

        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            Auth::login($user);
            return $this->redirectByRole($user);
        }

        $newUser           = new User();
        $newUser->name     = $googleUser->getName();
        $newUser->email    = $googleUser->getEmail();
        $newUser->password = bcrypt(\Illuminate\Support\Str::random(24));
        $newUser->role     = 'student';
        $newUser->status   = 'approved';
        $newUser->phone    = '';
        $newUser->save();

        Auth::login($newUser);
        return $this->redirectByRole($newUser);
    }

    private function redirectByRole(User $user)
    {
        return match ($user->role) {
            'admin'  => redirect()->route('admin.dashboard'),
            'owner'  => redirect()->route('owner.dashboard'),
            default  => redirect()->route('student.dashboard'),
        };
    }
}
