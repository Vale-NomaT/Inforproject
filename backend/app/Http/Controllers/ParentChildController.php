<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\Location;
use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParentChildController extends Controller
{
    public function create(): View
    {
        $schools = School::orderBy('name')->get();
        $pickupLocations = Location::where('is_active', true)->orderBy('name')->get();

        return view('dashboard.children.create', [
            'schools' => $schools,
            'pickupLocations' => $pickupLocations,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date', 'before_or_equal:-4 years', 'after:-15 years'],
            'school_id' => ['required', 'exists:schools,id'],
            'pickup_location_id' => ['required', 'exists:locations,id'],
            'custom_lat' => ['nullable', 'numeric'],
            'custom_lng' => ['nullable', 'numeric'],
            'custom_location_name' => ['nullable', 'string'],
            'relationship' => ['required', 'string', 'in:Mother,Father,Guardian,Grandparent,Other'],
            'school_start_time' => ['required', 'date_format:H:i'],
            'school_end_time' => ['required', 'date_format:H:i', 'after:school_start_time'],
            'grade' => ['nullable', 'string', 'in:ECD A,ECD B,Grade 1,Grade 2,Grade 3,Grade 4,Grade 5,Grade 6,Grade 7'],
            'medical_notes' => ['nullable', 'string'],
        ]);

        $pickupLocation = Location::find($validated['pickup_location_id']);
        $customLat = $request->input('custom_lat');
        $customLng = $request->input('custom_lng');

        $child = new Child();
        $child->fill($validated);
        $child->pickup_lat = is_numeric($customLat) ? (float) $customLat : ($pickupLocation ? $pickupLocation->lat : null);
        $child->pickup_lng = is_numeric($customLng) ? (float) $customLng : ($pickupLocation ? $pickupLocation->lng : null);
        $child->pickup_address = $validated['custom_location_name'] ?? ($pickupLocation ? $pickupLocation->name : 'Home');
        
        $child->parent_id = $request->user()->id;
        $child->save();

        return redirect()->route('parent.dashboard')
            ->with('status', 'Child added successfully!');
    }

    public function edit(Child $child): View
    {
        if ($child->parent_id !== request()->user()->id) {
            abort(403);
        }

        $schools = School::orderBy('name')->get();
        $pickupLocations = Location::where('is_active', true)->orderBy('name')->get();

        return view('dashboard.children.edit', [
            'child' => $child,
            'schools' => $schools,
            'pickupLocations' => $pickupLocations,
        ]);
    }

    public function update(Request $request, Child $child): RedirectResponse
    {
        if ($child->parent_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date', 'before_or_equal:-4 years', 'after:-15 years'],
            'school_id' => ['required', 'exists:schools,id'],
            'pickup_location_id' => ['required', 'exists:locations,id'],
            'custom_lat' => ['nullable', 'numeric'],
            'custom_lng' => ['nullable', 'numeric'],
            'custom_location_name' => ['nullable', 'string'],
            'relationship' => ['required', 'string', 'in:Mother,Father,Guardian,Grandparent,Other'],
            'school_start_time' => ['required', 'date_format:H:i'],
            'school_end_time' => ['required', 'date_format:H:i', 'after:school_start_time'],
            'grade' => ['nullable', 'string', 'in:ECD A,ECD B,Grade 1,Grade 2,Grade 3,Grade 4,Grade 5,Grade 6,Grade 7'],
            'medical_notes' => ['nullable', 'string'],
        ]);

        $pickupLocation = Location::find($validated['pickup_location_id']);
        $customLat = $request->input('custom_lat');
        $customLng = $request->input('custom_lng');

        $child->fill($validated);
        if (is_numeric($customLat)) {
            $child->pickup_lat = (float) $customLat;
        } elseif ($pickupLocation) {
            $child->pickup_lat = $pickupLocation->lat;
        }

        if (is_numeric($customLng)) {
            $child->pickup_lng = (float) $customLng;
        } elseif ($pickupLocation) {
            $child->pickup_lng = $pickupLocation->lng;
        }

        $child->pickup_address = $validated['custom_location_name'] ?? ($pickupLocation ? $pickupLocation->name : ($child->pickup_address ?? 'Home'));

        $child->save();

        return redirect()->route('parent.dashboard')
            ->with('status', 'Child details updated successfully!');
    }
}
