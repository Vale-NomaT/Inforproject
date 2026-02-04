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
        $pickupLocations = Location::orderBy('name')->get();

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
            'pickup_location_id' => ['nullable', 'exists:locations,id', 'required_without:custom_lat'],
            'custom_lat' => ['nullable', 'numeric', 'required_without:pickup_location_id'],
            'custom_lng' => ['nullable', 'numeric', 'required_with:custom_lat'],
            'custom_location_name' => ['nullable', 'string', 'required_with:custom_lat'],
            'relationship' => ['required', 'string', 'in:Mother,Father,Guardian,Grandparent,Other'],
            'school_start_time' => ['required', 'date_format:H:i'],
            'school_end_time' => ['required', 'date_format:H:i', 'after:school_start_time'],
            'grade' => ['nullable', 'string', 'in:ECD A,ECD B,Grade 1,Grade 2,Grade 3,Grade 4,Grade 5,Grade 6,Grade 7'],
            'medical_notes' => ['nullable', 'string'],
        ]);

        if (empty($validated['pickup_location_id']) && !empty($validated['custom_lat'])) {
            $location = Location::create([
                'name' => $validated['custom_location_name'],
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => $validated['custom_lat'],
                'lng' => $validated['custom_lng'],
                'is_active' => true,
            ]);
            $validated['pickup_location_id'] = $location->id;
        }

        $child = new Child($validated);
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
        $pickupLocations = Location::orderBy('name')->get();

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
            'pickup_location_id' => ['nullable', 'exists:locations,id', 'required_without:custom_lat'],
            'custom_lat' => ['nullable', 'numeric', 'required_without:pickup_location_id'],
            'custom_lng' => ['nullable', 'numeric', 'required_with:custom_lat'],
            'custom_location_name' => ['nullable', 'string', 'required_with:custom_lat'],
            'relationship' => ['required', 'string', 'in:Mother,Father,Guardian,Grandparent,Other'],
            'school_start_time' => ['required', 'date_format:H:i'],
            'school_end_time' => ['required', 'date_format:H:i', 'after:school_start_time'],
            'grade' => ['nullable', 'string', 'in:ECD A,ECD B,Grade 1,Grade 2,Grade 3,Grade 4,Grade 5,Grade 6,Grade 7'],
            'medical_notes' => ['nullable', 'string'],
        ]);

        if (empty($validated['pickup_location_id']) && !empty($validated['custom_lat'])) {
            $location = Location::create([
                'name' => $validated['custom_location_name'],
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => $validated['custom_lat'],
                'lng' => $validated['custom_lng'],
                'is_active' => true,
            ]);
            $validated['pickup_location_id'] = $location->id;
        }

        $child->update($validated);

        return redirect()->route('parent.dashboard')
            ->with('status', 'Child details updated successfully!');
    }
}
