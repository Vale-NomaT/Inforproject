<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\Rating;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParentDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $children = Child::with([
            'school',
            'pickupLocation',
            'bookingRequests.driver.driverProfile',
            'trips' => fn($q) => $q->orderByDesc('scheduled_date')->limit(50),
        ])
        ->where('parent_id', $request->user()->id)
        ->get();

        // Per-child enrichment
        $children = $children->map(function (Child $child) use ($request) {
            $child->latestBooking       = $child->bookingRequests->first();
            $child->activeTrip          = $child->trips->firstWhere('status', Trip::STATUS_IN_PROGRESS);
            $child->latestCompletedTrip = $child->trips->where('status', Trip::STATUS_COMPLETED)->first();
            $child->totalTrips          = $child->trips->count();
            $child->completedTrips      = $child->trips->where('status', Trip::STATUS_COMPLETED)->count();

            $child->hasRating = $child->latestCompletedTrip
                ? Rating::where('trip_id', $child->latestCompletedTrip->id)
                        ->where('parent_id', $request->user()->id)
                        ->exists()
                : false;

            // Assigned driver from approved booking
            $approvedBooking = $child->bookingRequests
                ->firstWhere('status', \App\Models\BookingRequest::STATUS_APPROVED);
            $child->assignedDriver = $approvedBooking?->driver;

            return $child;
        });

        // Summary stats
        $totalTrips     = $children->sum('totalTrips');
        $activeTrips    = $children->filter(fn($c) => $c->activeTrip)->count();
        $pendingRatings = $children->filter(fn($c) => $c->latestCompletedTrip && !$c->hasRating)->count();

        return view('dashboard.parent', compact(
            'children', 'totalTrips', 'activeTrips', 'pendingRatings'
        ));
    }
}
