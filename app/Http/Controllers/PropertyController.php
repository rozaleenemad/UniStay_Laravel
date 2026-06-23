<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    public function create()
    {
        if (Auth::check() && Auth::user()->status === 'pending') {
            abort(403, 'Your account is pending approval.');
        }

        return view('owner.properties.create');
    }

    public function store(Request $request)
    {
        if (Auth::user()->status === 'pending') {
            abort(403, 'Your account is pending approval.');
        }

        // Only approved owners may create listings
        abort_if(!Auth::user()->isOwner() || !Auth::user()->isApproved(), 403);

        $validated = $request->validate([
            'price'              => 'required|numeric|min:0|max:9999999',
            'governorate'        => 'required|string|max:100',
            'location'           => 'required|string|max:255',
            'proximity'          => 'nullable|integer|min:0|max:10000',
            'floor'              => 'required|integer|min:0|max:200',
            'bedrooms'           => 'required|integer|min:1|max:20',
            'bathrooms'          => 'required|integer|min:1|max:20',
            'gender_type'        => 'required|string|in:male,female',
            'is_furnished'       => 'nullable|boolean',
            'utilities_included' => 'nullable|boolean',
            'available_from'     => 'nullable|date|after_or_equal:today',
            'description'        => 'nullable|string|max:2000',
            'property_images'    => 'required|array|min:1|max:10',
            'property_images.*'  => 'image|mimes:jpg,jpeg,png|max:4096',
        ]);

        $uploadedImages = [];
        foreach ($request->file('property_images') as $image) {
            $uploadedImages[] = $image->store('properties', 'public');
        }

        Property::create([
            'user_id'            => Auth::id(),
            'governorate'        => $validated['governorate'],
            'price'              => $validated['price'],
            'location'           => $validated['location'],
            'proximity'          => $validated['proximity'] ?? 0,
            'floor'              => $validated['floor'],
            'bedrooms'           => $validated['bedrooms'],
            'bathrooms'          => $validated['bathrooms'],
            'gender_type'        => $validated['gender_type'],
            'is_furnished'       => $request->boolean('is_furnished'),
            'utilities_included' => $request->boolean('utilities_included'),
            'available_from'     => $validated['available_from'] ?? null,
            'description'        => $validated['description'] ?? null,
            'images'             => $uploadedImages,
            'status'             => 'pending',
        ]);

        return redirect()->route('owner.dashboard')
            ->with('success', 'Property listed successfully. It will appear after admin approval.');
    }

    public function edit(Property $property)
    {
        // Only the owning user may edit
        abort_if($property->user_id !== Auth::id(), 403);

        // Rented properties cannot be edited
        abort_if($property->isRented(), 403, 'Rented properties cannot be edited.');

        return view('owner.properties.edit', compact('property'));
    }

    public function update(Request $request, Property $property)
    {
        abort_if($property->user_id !== Auth::id(), 403);
        abort_if($property->isRented(), 403, 'Rented properties cannot be edited.');

        $validated = $request->validate([
            'price'              => 'required|numeric|min:0|max:9999999',
            'governorate'        => 'required|string|max:100',
            'location'           => 'required|string|max:255',
            'proximity'          => 'required|integer|min:0|max:10000',
            'floor'              => 'required|integer|min:0|max:200',
            'bedrooms'           => 'required|integer|min:1|max:20',
            'bathrooms'          => 'required|integer|min:1|max:20',
            'gender_type'        => 'required|string|in:male,female',
            'is_furnished'       => 'nullable|boolean',
            'utilities_included' => 'nullable|boolean',
            'available_from'     => 'nullable|date',
            'description'        => 'nullable|string|max:2000',
            'property_images'    => 'nullable|array|max:10',
            'property_images.*'  => 'image|mimes:jpg,jpeg,png|max:4096',
        ]);

        $data = [
            'price'              => $validated['price'],
            'governorate'        => $validated['governorate'],
            'location'           => $validated['location'],
            'proximity'          => $validated['proximity'],
            'floor'              => $validated['floor'],
            'bedrooms'           => $validated['bedrooms'],
            'bathrooms'          => $validated['bathrooms'],
            'gender_type'        => $validated['gender_type'],
            'description'        => $validated['description'] ?? null,
            'available_from'     => $validated['available_from'] ?? null,
            'is_furnished'       => $request->boolean('is_furnished'),
            'utilities_included' => $request->boolean('utilities_included'),
            'status'             => 'pending',
        ];

        if ($request->hasFile('property_images')) {
            if ($property->images) {
                foreach ($property->images as $old) {
                    Storage::disk('public')->delete($old);
                }
            }
            $newImages = [];
            foreach ($request->file('property_images') as $image) {
                $newImages[] = $image->store('properties', 'public');
            }
            $data['images'] = $newImages;
        }

        $property->update($data);

        return redirect()->route('owner.dashboard')
            ->with('success', 'Property updated. It is now pending re-review by admin.');
    }

    public function destroy(Property $property)
    {
        abort_if($property->user_id !== Auth::id(), 403);

        abort_if($property->status === 'rented' || $property->isRented(), 403, 'Rented properties cannot be deleted. Contact admin.');

        if ($property->images) {
            foreach ($property->images as $img) {
                Storage::disk('public')->delete($img);
            }
        }
        $property->delete();

        return redirect()->route('owner.dashboard')->with('success', 'Property deleted successfully.');
    }


    public function markAsRented(Property $property)
    {
        abort_if($property->user_id !== Auth::id(), 403);

        abort_if($property->status !== 'approved' && !$property->isApproved(), 403, 'Only approved properties can be marked as rented.');

        $property->update([
            'status' => 'rented',
            'rented_at' => now(),
        ]);

        return redirect()->back()->with('success', 'تم تحديث حالة العقار إلى مؤجر بنجاح!');
    }

    public function activateByOwner(Property $property)
    {
        abort_if($property->user_id !== Auth::id(), 403);

        abort_if($property->status !== 'rented', 403, 'Only rented properties can be reactivated.');

        $property->update([
            'status' => 'pending',
            'rented_at' => null,
        ]);

        return redirect()->back()->with('success', 'تم إرسال العقار لمراجعة الأدمن لإعادة تفعيله.');
    }
}
