<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class MaintenanceDiscoveryController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'maintenance')->where('status', 'approved');

        $query->when($request->filled('maintenance_type'), function ($q) use ($request) {
            return $q->where('maintenance_type', $request->maintenance_type);
        });

        $query->when($request->filled('governorate'), function ($q) use ($request) {
            return $q->whereRaw('LOWER(governorate) = ?', [strtolower($request->governorate)]);
        });

        $technicians = $query->get();

        return view('student.maintenance.index', compact('technicians'));
    }
}
