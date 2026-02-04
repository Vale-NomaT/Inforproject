<?php

namespace App\Http\Controllers;

use App\Models\BookingRequest;
use App\Models\Child;
use App\Models\User;
use App\Services\EligibleDriverService;
use App\Services\ResendEmailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParentDriverController extends Controller
{
    protected EligibleDriverService $eligibleDriverService;

    protected ResendEmailService $resendEmailService;

    public function __construct(
        EligibleDriverService $eligibleDriverService,
        ResendEmailService $resendEmailService
    ) {
        $this->eligibleDriverService = $eligibleDriverService;
        $this->resendEmailService = $resendEmailService;
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

        $booking = BookingRequest::where('parent_id', $request->user()->id)
            ->where('driver_id', $driverId)
            ->where('child_id', $child->id)
            ->where('status', BookingRequest::STATUS_PENDING)
            ->first();

        if (! $booking) {
            $booking = BookingRequest::create([
                'parent_id' => $request->user()->id,
                'driver_id' => $driverId,
                'child_id' => $child->id,
                'status' => BookingRequest::STATUS_PENDING,
                'pricing_tier' => $selectedEntry['tier'],
                'created_at' => now(),
            ]);
        }

        $driverUser = User::where('id', $driverId)
            ->where('user_type', 'driver')
            ->first();

        if ($driverUser) {
            $subject = 'New booking request for '.$child->first_name.' '.$child->last_name;
            $html = '<p>You have a new booking request from a parent for '.e($child->first_name.' '.$child->last_name).'.</p>';
            $html .= '<p>Please sign in to SafeRide Kids to approve or decline this request.</p>';

            $this->resendEmailService->send($driverUser->email, $subject, $html);
        }

        return back()->with('status', 'Request sent! Awaiting driver confirmation.');
    }
}
