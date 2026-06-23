<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Property;
use Illuminate\Http\Request;

class StudentMaintenanceController extends Controller
{
    public function index(Request $request)
    {
        // 1. جلب العقارات للـ Tab الأول في داش بورد الطالب (فقط المعتمدة)
        $properties = Property::publiclyVisible()->latest()->get();

        // 2. بناء الاستعلام لجلب الفنيين المعتمدين
        $techQuery = User::where('role', 'maintenance')
            ->where('status', 'approved');

        // 🛠️ الحل: الفلترة على مستوى الـ Server-side لو التيست أو المستخدم بعت محافظة محددة
        if ($request->filled('governorate')) {
            $governorate = strtolower($request->query('governorate'));
            $techQuery->whereRaw('LOWER(governorate) = ?', [$governorate]);
        }

        $technicians = $techQuery->latest()->get();

        // 3. تمرير البيانات الصحيحة والمطابقة لأسماء المتغيرات في الـ Blade
        return view('student.maintenance.index', [
            'properties' => $properties,
            'technicians' => $technicians,
            'maintenanceTypes' => \App\Http\Controllers\Auth\MaintenanceRegisterController::$maintenanceTypes,
            'governorates' => \App\Http\Controllers\Auth\MaintenanceRegisterController::$governorates,
        ]);
    }

    public function showMaintenanceProviders(Request $request)
    {
        $governorate = $request->query('governorate');
        $type = $request->query('maintenance_type');

        $providers = User::where('role', 'maintenance')
            ->when($governorate, function ($query, $governorate) {
                return $query->where('governorate', $governorate);
            })
            ->when($type, function ($query, $type) {
                return $query->where('maintenance_type', $type);
            })
            ->latest()
            ->get();

        $governoratesList = User::where('role', 'maintenance')->whereNotNull('governorate')->distinct()->pluck('governorate');
        $typesList = User::where('role', 'maintenance')->whereNotNull('maintenance_type')->distinct()->pluck('maintenance_type');

        return view('student.maintenance_list', compact('providers', 'governoratesList', 'typesList'));
    }
}
