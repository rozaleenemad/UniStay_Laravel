<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\MaintenanceRequest;

class DashboardController extends Controller
{
    public function index()
    {
    
        $requests = MaintenanceRequest::where('status', 'pending')
            ->with(['booking.user'])
            ->get();

        return view('maintenance.dashboard', compact('requests'));
    }
}
