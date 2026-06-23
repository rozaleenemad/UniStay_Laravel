<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class MaintenanceRegisterController extends Controller
{
    // قيم ثابتة (slugs) موحدة بين فورم التسجيل وفلتر الطالب
    public static $maintenanceTypes = [
        'plumbing'         => 'سباكة',
        'electricity'      => 'كهرباء',
        'carpentry'        => 'نجارة',
        'air_conditioning' => 'تكييفات',
        'painting'         => 'نقاشة',
    ];

    public static $governorates = [
        'cairo'        => 'القاهرة',
        'giza'         => 'الجيزة',
        'alexandria'   => 'الإسكندرية',
        'assiut'       => 'أسيوط',
        'qena'         => 'قنا',
        'sohag'        => 'سوهاج',
        'luxor'        => 'الأقصر',
        'aswan'        => 'أسوان',
        'minya'        => 'المنيا',
        'beni_suef'    => 'بني سويف',
        'fayoum'       => 'الفيوم',
        'sharqia'      => 'الشرقية',
        'dakahlia'     => 'الدقهلية',
        'gharbia'      => 'الغربية',
        'monufia'      => 'المنوفية',
        'qalyubia'     => 'القليوبية',
        'beheira'      => 'البحيرة',
        'kafr_el_sheikh' => 'كفر الشيخ',
        'damietta'     => 'دمياط',
        'port_said'    => 'بورسعيد',
        'ismailia'     => 'الإسماعيلية',
        'suez'         => 'السويس',
        'north_sinai'  => 'شمال سيناء',
        'south_sinai'  => 'جنوب سيناء',
        'red_sea'      => 'البحر الأحمر',
        'new_valley'   => 'الوادي الجديد',
        'matrouh'      => 'مطروح',
    ];

    public function showRegistrationForm()
    {
        return view('auth.maintenance-register', [
            'maintenanceTypes' => self::$maintenanceTypes,
            'governorates'     => self::$governorates,
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|string|email|max:255|unique:users',
            'password'         => 'required|string|min:8|confirmed',
            'phone'            => 'required|string|max:20',
            'location'         => 'nullable|string|max:255',
            'governorate'      => 'required|string|in:' . implode(',', array_keys(self::$governorates)),
            'maintenance_type' => 'required|string|in:' . implode(',', array_keys(self::$maintenanceTypes)),
        ]);

     
        $user = new User();
        $user->name             = $request->name;
        $user->email            = $request->email;
        $user->password         = Hash::make($request->password);
        $user->phone            = $request->phone;
        $user->location         = $request->location;
        $user->governorate      = $request->governorate;
        $user->maintenance_type = $request->maintenance_type;
        $user->role             = 'maintenance';
        $user->status           = 'approved';
        $user->save();

        Auth::login($user);

        return redirect()->route('maintenance.dashboard');
    }
}
