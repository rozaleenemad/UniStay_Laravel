<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\MaintenanceRegisterController;
use App\Http\Controllers\Maintenance\DashboardController;
use App\Http\Controllers\StudentMaintenanceController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\RequestController;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ── Public ────────────────────────────────────────────────────────────
Route::get('/', fn() => view('welcome'));

Route::get('/maintenance/register',  [MaintenanceRegisterController::class, 'showRegistrationForm'])->name('maintenance.register.form');
Route::post('/maintenance/register', [MaintenanceRegisterController::class, 'register'])->name('maintenance.register');

Route::get('/test-data', function () {
    return response()->json(\App\Models\MaintenanceRequest::all());
});

// ── Google OAuth (يجب أن تكون خارج الـ auth لتكون متاحة للجميع) ─────
Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');


// ── Authenticated ─────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isOwner()) {
            return $user->status === 'pending'
                ? redirect()->route('owner.pending')
                : redirect()->route('owner.dashboard');
        } elseif ($user->role === 'maintenance') {
            return redirect()->route('maintenance.dashboard');
        }

        return redirect()->route('student.dashboard');
    })->name('dashboard');

    // ── Student ───────────────────────────────────────────────────────
    Route::middleware('student')->group(function () {
        Route::get('/student/dashboard', function () {
            $gender = request('gender');
            $query = Property::with('owner')->where('status', 'approved')->latest();

            if ($gender && in_array($gender, ['male', 'female'])) {
                $query->where('gender_type', $gender);
            }

            // $properties = $query->get();
            $properties = $query->paginate(40);
            $maintenanceProviders = \App\Models\User::where('role', 'maintenance')->where('status', 'approved')->latest()->get();

            return view('student.dashboard', compact('properties', 'maintenanceProviders'));
        })->name('student.dashboard');

        Route::get('/student/maintenance', [StudentMaintenanceController::class, 'index'])->name('student.maintenance.index');
    });


    // ── Owner ─────────────────────────────────────────────────────────
    Route::get('/owner/pending', function () {
        $canAccess = Auth::user()->isOwner() || (method_exists(Auth::user(), 'isImpersonating') && Auth::user()->isImpersonating());
        abort_if(!$canAccess, 403);
        return view('owner.pending');
    })->name('owner.pending');

    $ownerMiddleware = (Auth::check() && method_exists(Auth::user(), 'isImpersonating') && Auth::user()->isImpersonating())
        ? 'auth'
        : 'owner.active';

    Route::middleware($ownerMiddleware)->group(function () {
        Route::get('/owner/dashboard', function () {
            $canAccess = Auth::user()->isOwner() || (method_exists(Auth::user(), 'isImpersonating') && Auth::user()->isImpersonating());
            abort_if(!$canAccess, 403);
            $properties = Property::where('user_id', Auth::id())->latest()->get();
            return view('owner.dashboard', [
                'properties' => $properties,
                'totalProperties' => $properties->count(),
                'activeBookings' => 0,
            ]);
        })->name('owner.dashboard');

        Route::get('/owner/properties/create', [PropertyController::class, 'create'])->name('owner.properties.create');
        Route::post('/owner/properties', [PropertyController::class, 'store'])->name('owner.properties.store');
        Route::get('/owner/properties/{property}/edit', [PropertyController::class, 'edit'])->name('owner.properties.edit');
        Route::put('/owner/properties/{property}', [PropertyController::class, 'update'])->name('owner.properties.update');
        Route::delete('/owner/properties/{property}', [PropertyController::class, 'destroy'])->name('owner.properties.destroy');

        Route::patch('/owner/properties/{property}/mark-rented', [PropertyController::class, 'markAsRented'])->name('owner.properties.mark-rented');
        Route::patch('/owner/properties/{property}/activate', [PropertyController::class, 'activateByOwner'])->name('owner.properties.activate');
        Route::get('/owner/maintenance', [App\Http\Controllers\MaintenanceDiscoveryController::class, 'index'])->name('owner.maintenance.index');
    });

    // ── Maintenance ───────────────────────────────────────────────────
    Route::middleware('maintenance')->group(function () {
        Route::get('/maintenance/dashboard', [DashboardController::class, 'index'])->name('maintenance.dashboard');
    });

    // ── Admin ─────────────────────────────────────────────────────────
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::patch('/owners/{user}/approve', [AdminController::class, 'approveOwner'])->name('admin.approve-owner');
        Route::patch('/owners/{user}/reject', [AdminController::class, 'rejectOwner'])->name('admin.reject-owner');
        Route::post('/properties/{id}/approve', [AdminController::class, 'approveProperty'])->name('admin.properties.approve');
        Route::post('/properties/{id}/reject', [AdminController::class, 'rejectProperty'])->name('admin.properties.reject');
        Route::get('/impersonate/{user}', [AdminController::class, 'impersonate'])->name('admin.impersonate');
    });

    // ── Profile ───────────────────────────────────────────────────────
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
