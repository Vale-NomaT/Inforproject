<?php

namespace App\Http\Controllers;

use App\Events\BookingStatusUpdated;
use App\Models\BookingRequest;
use App\Models\DriverProfile;
use App\Models\Trip;
use App\Notifications\BookingStatusNotification;
use App\Services\PricingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;

class DriverBookingController extends Controller
{
    protected PricingService $pricingService;

    public function __construct(PricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    public function index(Request $request): View
    {
        $bookings = BookingRequest::where('driver_id', $request->user()->id)
            ->where('status', BookingRequest::STATUS_PENDING)
            ->with(['child.school', 'child.pickupLocation', 'parent.parentProfile'])
            ->orderByDesc('created_at')
            ->get();

        // Correct pricing for pending bookings if needed (e.g. if distance calc was fixed)
        foreach ($bookings as $booking) {
            if ($booking->child && $booking->child->pickupLocation && $booking->child->school) {
                try {
                    $pricing = $this->pricingService->determinePricing(
                        $booking->child->pickupLocation,
                        $booking->child->school
                    );
                    
                    if ($booking->pricing_tier !== $pricing['tier']) {
                        $booking->pricing_tier = $pricing['tier'];
                        $booking->save(); // Update DB record so approval uses correct tier
                    }
                } catch (\Exception $e) {
                    // Ignore calculation errors, keep existing tier
                }
            }
        }

        $approvedBookings = BookingRequest::where('driver_id', $request->user()->id)
            ->where('status', BookingRequest::STATUS_APPROVED)
            ->with(['child.school', 'child.pickupLocation', 'parent.parentProfile'])
            ->orderByDesc('responded_at')
            ->get();

        $driverProfile = DriverProfile::find($request->user()->id);
        $totalCapacity = $driverProfile ? $driverProfile->max_child_capacity : 0;
        $seatsLeft = max(0, $totalCapacity - $approvedBookings->count());

        return view('driver.bookings', [
            'bookings' => $bookings,
            'approvedBookings' => $approvedBookings,
            'seatsLeft' => $seatsLeft,
            'totalCapacity' => $totalCapacity,
        ]);
    }

    public function approve(Request $request, BookingRequest $booking): RedirectResponse
    {
        if ($booking->driver_id !== $request->user()->id) {
            abort(403);
        }

        if ($booking->status !== BookingRequest::STATUS_PENDING) {
            return back();
        }

        $booking->status = BookingRequest::STATUS_APPROVED;
        $booking->responded_at = now();
        $booking->save();

        // Generate trips for the next 30 days (weekdays only)
        $startDate = now();
        $endDate = now()->addDays(30);
        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $date) {
            if ($date->isWeekday()) {
                // Morning Trip (Home -> School)
                Trip::firstOrCreate([
                    'driver_id' => $booking->driver_id,
                    'child_id' => $booking->child_id,
                    'scheduled_date' => $date->format('Y-m-d'),
                    'type' => Trip::TYPE_MORNING,
                ], [
                    'status' => Trip::STATUS_SCHEDULED,
                    'pricing_tier' => $booking->pricing_tier,
                ]);

                // Afternoon Trip (School -> Home)
                Trip::firstOrCreate([
                    'driver_id' => $booking->driver_id,
                    'child_id' => $booking->child_id,
                    'scheduled_date' => $date->format('Y-m-d'),
                    'type' => Trip::TYPE_AFTERNOON,
                ], [
                    'status' => Trip::STATUS_SCHEDULED,
                    'pricing_tier' => $booking->pricing_tier,
                ]);
            }
        }

        Event::dispatch(new BookingStatusUpdated($booking));

        $parent = $booking->parent;

        if ($parent) {
            $parent->notify(new BookingStatusNotification(
                'approved',
                $request->user()->name,
                $booking->id
            ));
        }

        return back()->with('status', 'Booking approved.');
    }

    public function decline(Request $request, BookingRequest $booking): RedirectResponse
    {
        if ($booking->driver_id !== $request->user()->id) {
            abort(403);
        }

        if ($booking->status !== BookingRequest::STATUS_PENDING) {
            return back();
        }

        $booking->status = BookingRequest::STATUS_DECLINED;
        $booking->responded_at = now();
        $booking->save();

        Event::dispatch(new BookingStatusUpdated($booking));

        $parent = $booking->parent;

        if ($parent) {
            $parent->notify(new BookingStatusNotification(
                'declined',
                $request->user()->name,
                $booking->id
            ));
        }

        return back()->with('status', 'Booking declined.');
    }
}
