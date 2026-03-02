<?php

namespace App\Http\Controllers;

use App\Models\BookingRequest;
use App\Models\Child;
use App\Models\User;
use App\Notifications\NewBookingNotification;
use App\Services\EligibleDriverService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

use Illuminate\Support\Facades\DB;

class ParentDriverController extends Controller
{
    protected EligibleDriverService $eligibleDriverService;

    public function __construct(
        EligibleDriverService $eligibleDriverService
    ) {
        $this->eligibleDriverService = $eligibleDriverService;
    }

    public function show(Request $request, Child $child): View
    {
        if ($request->user()->id !== $child->parent_id) {
            abort(403);
        }

        $child->load(['pickupLocation', 'school']);

        $drivers = $this->eligibleDriverService->getEligibleDriversWithPricing($child);

        return view('parent.drivers', [
            'child' => $child,
            'drivers' => $drivers,
        ]);
    }

    public function store(Request $request, Child $child): RedirectResponse
    {
        if ($request->user()->id !== $child->parent_id) {
            abort(403);
        }

        $request->validate([
            'driver_id' => ['required', 'integer'],
        ]);
        $driverId = (int) $request->input('driver_id');

        $eligibleDrivers = $this->eligibleDriverService->getEligibleDriversWithPricing($child);

        $selectedEntry = $eligibleDrivers->first(function ($entry) use ($driverId) {
            return $entry['driver']->id === $driverId;
        });

        if (! $selectedEntry) {
            abort(403);
        }

        $booking = DB::transaction(function () use ($request, $driverId, $child, $selectedEntry) {
            // Lock the child record to prevent race conditions
            Child::where('id', $child->id)->lockForUpdate()->first();

            $existingBooking = BookingRequest::where('parent_id', $request->user()->id)
                ->where('driver_id', $driverId)
                ->where('child_id', $child->id)
                ->where('status', BookingRequest::STATUS_PENDING)
                ->first();

            if ($existingBooking) {
                return $existingBooking;
            }

            return BookingRequest::create([
                'parent_id' => $request->user()->id,
                'driver_id' => $driverId,
                'child_id' => $child->id,
                'status' => BookingRequest::STATUS_PENDING,
                'pricing_tier' => $selectedEntry['tier'],
                'created_at' => now(),
            ]);
        });

        // Only notify if it was a newly created booking (check wasRecentlyCreated)
        if ($booking->wasRecentlyCreated) {
            $driverUser = User::where('id', $driverId)
                ->where('user_type', 'driver')
                ->first();

            if ($driverUser) {
                $driverUser->notify(new NewBookingNotification(
                    $request->user()->name,
                    $child->first_name . ' ' . $child->last_name,
                    $booking->id
                ));
            }
        }

        return back()->with('status', 'Request sent! Awaiting driver confirmation.');
    }
}
