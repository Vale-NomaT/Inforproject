<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\DriverProfile;

class DriverDocumentController extends Controller
{
    public function edit()
    {
        $driver = Auth::user();
        $profile = $driver->driverProfile;
        return view('driver.documents.edit', compact('driver', 'profile'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'license_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'registration_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'gov_id_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $user = Auth::user();
        $profile = $user->driverProfile;

        if (!$profile) {
             return redirect()->back()->with('error', 'Driver profile not found.');
        }

        $updated = false;

        if ($request->hasFile('license_file')) {
            if ($profile->license_file_path) {
                Storage::disk('public')->delete($profile->license_file_path);
            }
            $path = $request->file('license_file')->store('documents', 'public');
            $profile->license_file_path = $path;
            $updated = true;
        }

        if ($request->hasFile('registration_file')) {
            if ($profile->vehicle_registration_file_path) {
                Storage::disk('public')->delete($profile->vehicle_registration_file_path);
            }
            $path = $request->file('registration_file')->store('documents', 'public');
            $profile->vehicle_registration_file_path = $path;
            $updated = true;
        }
        
        if ($request->hasFile('gov_id_file')) {
            if ($profile->gov_id_file_path) {
                Storage::disk('public')->delete($profile->gov_id_file_path);
            }
            $path = $request->file('gov_id_file')->store('documents', 'public');
            $profile->gov_id_file_path = $path;
            $updated = true;
        }

        if ($updated) {
            $profile->save();
            
            // Reset status to pending if it was rejected so admin can review again
            if ($user->status === 'rejected') {
                $user->status = 'pending';
                $user->status_reason = null;
                $user->save();
            }
            
            return redirect()->route('driver.dashboard')->with('success', 'Documents updated successfully. Your application is under review.');
        }

        return redirect()->back()->with('info', 'No documents were uploaded.');
    }
}
