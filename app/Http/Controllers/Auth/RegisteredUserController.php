<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'phone'            => ['required', 'string', 'max:20'],
            'role'             => ['required', 'string', 'in:student,owner,maintenance'],
            'governorate'      => $request->role === 'maintenance' ? 'required|string|max:255' : 'nullable',
            'location'         => $request->role === 'maintenance' ? 'required|string|max:255' : 'nullable',
            'maintenance_type' => $request->role === 'maintenance' ? 'required|string|max:255' : 'nullable',
            'password'         => ['required', 'confirmed', Rules\Password::defaults()],
            'national_id'      => ['required_if:role,owner', 'nullable', 'string', 'size:14'],
            'id_card_image'    => ['required_if:role,owner', 'nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $idCardPath = null;
        if ($request->hasFile('id_card_image') && $request->role === 'owner') {
            $idCardPath = $request->file('id_card_image')->store('id_cards', 'public');
        }

        // Owners start as pending — students are auto-approved
        $status = $request->role === 'owner' ? 'pending' : 'approved';

        // role and status are set explicitly — NOT via mass assignment
        $user = new User();
        $user->name         = $request->name;
        $user->email        = $request->email;
        $user->phone        = $request->phone;
        $user->password     = Hash::make($request->password);
        $user->role         = $request->role;
        $user->status       = $status;
        $user->national_id  = $request->role === 'owner' ? $request->national_id : null;
        $user->id_card_image = $idCardPath;

        $user->governorate      = $request->role === 'maintenance' ? $request->governorate : null;
        $user->location         = $request->role === 'maintenance' ? $request->location : null;
        $user->maintenance_type = $request->role === 'maintenance' ? $request->maintenance_type : null;
        $user->save();

        event(new Registered($user));
        Auth::login($user);

        return match ($user->role) {
            'owner'   => redirect()->route('owner.pending'),
            'maintenance' => redirect()->route('maintenance.dashboard'),
            'admin'   => redirect()->route('admin.dashboard'),
            default   => redirect()->route('student.dashboard'),
        };
    }
}
