<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\User;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminController extends Controller
{

    public function index()
    {
        $userCounts = User::select('role', 'status', \DB::raw('count(*) as total'))
            ->whereIn('role', ['student', 'owner', 'maintenance'])
            ->groupBy('role', 'status')
            ->get();

        $propCounts = Property::select('status', \DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        $genderCounts = Property::select('gender_type', \DB::raw('count(*) as total'))
            ->groupBy('gender_type')
            ->get();

        $totalStudents = $userCounts->where('role', 'student')->sum('total');
        $totalOwners   = $userCounts->where('role', 'owner')->sum('total');
        $pendingOwners = $userCounts->where('role', 'owner')->where('status', 'pending')->first()->total ?? 0;
        $approvedOwners = $userCounts->where('role', 'owner')->where('status', 'approved')->first()->total ?? 0;
        $rejectedOwners = $userCounts->where('role', 'owner')->where('status', 'rejected')->first()->total ?? 0;
        $totalMaintenance = $userCounts->where('role', 'maintenance')->sum('total');

        $totalProperties    = $propCounts->sum('total');
        $approvedProperties = $propCounts->where('status', 'approved')->first()->total ?? 0;
        $pendingProperties  = $propCounts->where('status', 'pending')->first()->total ?? 0;
        $rejectedProperties = $propCounts->where('status', 'rejected')->first()->total ?? 0;
        $rentedProperties   = $propCounts->where('status', 'rented')->first()->total ?? 0;

        $malePropertiesCount   = $genderCounts->where('gender_type', 'male')->first()->total ?? 0;
        $femalePropertiesCount = $genderCounts->where('gender_type', 'female')->first()->total ?? 0;

        $totalBookings = Booking::count();
        $totalReviews  = Review::count();
        $pendingTotal = $pendingOwners + $pendingProperties;

        $months = $monthlyStudents = $monthlyOwners = $monthlyMaintenance = [];
        $stats = User::select('role', \DB::raw('MONTH(created_at) as m, YEAR(created_at) as y, count(*) as c'))
            ->where('created_at', '>=', Carbon::now()->subMonths(5))
            ->groupBy('role', 'y', 'm')
            ->get();

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M');
            $monthlyStudents[] = $stats->where('role', 'student')->where('y', $date->year)->where('m', $date->month)->sum('c');
            $monthlyOwners[] = $stats->where('role', 'owner')->where('y', $date->year)->where('m', $date->month)->sum('c');
            $monthlyMaintenance[] = $stats->where('role', 'maintenance')->where('y', $date->year)->where('m', $date->month)->sum('c');
        }

        $owners = User::where('role', 'owner')->withCount('properties')->latest()->paginate(10, ['*'], 'owners_page');;
        $pendingPropertiesList = Property::with('owner')->whereIn('status', ['pending', 'approved', 'rejected'])->latest()->paginate(10, ['*'], 'props_page');;
        $rentedPropertiesList = Property::with('owner')->where('status', 'rented')->latest()->get();
        $ownersForChart = User::where('role', 'owner')->get();
        return view('admin.dashboard', compact(
            'totalStudents',
            'totalOwners',
            'pendingOwners',
            'approvedOwners',
            'rejectedOwners',
            'totalMaintenance',
            'totalProperties',
            'approvedProperties',
            'pendingProperties',
            'rejectedProperties',
            'rentedProperties',
            'malePropertiesCount',
            'femalePropertiesCount',
            'totalBookings',
            'totalReviews',
            'pendingTotal',
            'months',
            'monthlyStudents',
            'monthlyOwners',
            'monthlyMaintenance',
            'owners',
            'pendingPropertiesList',
            'rentedPropertiesList',
            'ownersForChart'
        ));
    }
    public function approveProperty($id)
    {
        $property = Property::findOrFail($id);
        $property->update(['status' => 'approved', 'rented_at' => null]);
        return redirect()->route('admin.dashboard')->with('success', 'Property approved and is now live.');
    }

    public function rejectProperty($id)
    {
        Property::findOrFail($id)->update(['status' => 'rejected']);
        return redirect()->route('admin.dashboard')->with('error', 'Property has been rejected.');
    }


    public function approveOwner(User $user)
    {
        abort_if(!$user->isOwner(), 422, 'User is not an owner.');
        $user->status = 'approved';
        $user->save();
        return redirect()->route('admin.dashboard')->with('success', 'Owner approved successfully.');
    }

    public function rejectOwner(User $user)
    {
        abort_if(!$user->isOwner(), 422, 'User is not an owner.');
        $user->status = 'rejected';
        $user->save();
        return redirect()->route('admin.dashboard')->with('error', 'Owner has been rejected.');
    }

    public function impersonate($id)
    {
        $user = User::findOrFail($id);

        abort_if(!$user->canBeImpersonated(), 403, 'This user cannot be impersonated.');

        auth()->user()->impersonate($user);

        return match ($user->role) {
            'owner'       => redirect('/owner/properties'),
            'maintenance' => redirect()->route('maintenance.dashboard'),
            default       => redirect()->route('student.dashboard'),
        };
    }
}
