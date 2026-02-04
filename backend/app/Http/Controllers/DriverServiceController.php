<?php

namespace App\Http\Controllers;

use App\Models\DriverProfile;
use App\Models\Location;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DriverServiceController extends Controller
{
    /**
     * Show the form for editing the driver's service areas and schools.
     */
    public function edit(): View
    {
        $driverProfile = DriverProfile::with(['locations', 'schools'])->find(Auth::id());
        
        // If driver profile doesn't exist (shouldn't happen if logged in as driver), handle gracefully
        if (!$driverProfile) {
            abort(404, 'Driver profile not found.');
        }

        $locations = Location::where('is_active', true)->orderBy('name')->get();
        $schools = School::where('is_active', true)->orderBy('name')->get();

        return view('dashboard.driver.service-area', compact('driverProfile', 'locations', 'schools'));
    }

    /**
     * Update the driver's service areas and schools.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'locations' => ['array'],
            'locations.*' => ['exists:locations,id'],
            'schools' => ['array'],
            'schools.*' => ['exists:schools,id'],
        ]);

        $driverProfile = DriverProfile::find(Auth::id());

        if (!$driverProfile) {
            abort(404, 'Driver profile not found.');
        }

        // Sync locations
        $driverProfile->locations()->sync($request->input('locations', []));

        // Sync schools
        $driverProfile->schools()->sync($request->input('schools', []));

        return redirect()->route('driver.service.edit')->with('success', 'Service areas updated successfully.');
    }
}
