<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\User;
use App\Models\School;
use App\Models\DriverLocation;
use App\Models\BookingRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $totalUsers      = User::count();
        $totalParents    = User::where('user_type', 'parent')->count();
        $totalDrivers    = User::where('user_type', 'driver')->where('status', 'active')->count();
        $pendingDrivers  = User::where('user_type', 'driver')->where('status', 'pending')->count();
        $totalTrips      = Trip::count();
        $activeTrips     = Trip::where('status', Trip::STATUS_IN_PROGRESS)->count();
        $completedTrips  = Trip::where('status', Trip::STATUS_COMPLETED)->count();
        $totalSchools    = School::count();

        // Recent trips (last 10)
        $recentTrips = Trip::with(['driver', 'child.school'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Recent signups (last 8)
        $recentUsers = User::orderBy('created_at', 'desc')->limit(8)->get();

        // Pending driver applications
        $pendingDriversList = User::where('user_type', 'driver')
            ->where('status', 'pending')
            ->with('driverProfile')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.admin', compact(
            'totalUsers', 'totalParents', 'totalDrivers', 'pendingDrivers',
            'totalTrips', 'activeTrips', 'completedTrips', 'totalSchools',
            'recentTrips', 'recentUsers', 'pendingDriversList'
        ));
    }

    public function liveTracking(): View
    {
        // All active drivers with their latest location and current trip
        $activeDrivers = User::where('user_type', 'driver')
            ->where('status', 'active')
            ->with([
                'driverProfile',
                'driverProfile.locations',
                'driverProfile.schools',
            ])
            ->get()
            ->map(function ($driver) {
                $driver->currentTrip = Trip::where('driver_id', $driver->id)
                    ->where('status', Trip::STATUS_IN_PROGRESS)
                    ->with(['child.school', 'events' => fn($q) => $q->latest()->limit(1)])
                    ->first();

                $driver->latestLocation = \App\Models\TripEvent::whereHas('trip', fn($q) => $q->where('driver_id', $driver->id))
                    ->whereNotNull('lat')
                    ->latest()
                    ->first();

                return $driver;
            });

        $totalActive = $activeDrivers->whereNotNull('currentTrip')->count();

        return view('admin.live-tracking', compact('activeDrivers', 'totalActive'));
    }
}
