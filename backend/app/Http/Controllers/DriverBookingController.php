<?php

namespace App\Http\Controllers;

use App\Events\BookingStatusUpdated;
use App\Models\BookingRequest;
use App\Models\DriverProfile;
use App\Models\Trip;
use App\Services\ResendEmailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;

class DriverBookingController extends Controller
{
    protected ResendEmailService $resendEmailService;

    public function __construct(ResendEmailService $resendEmailService)
    {
        $this->resendEmailService = $resendEmailService;
    }

    public function index(Request $request): View
    {
        $bookings = BookingRequest::where('driver_id', $request->user()->id)
            ->where('status', BookingRequest::STATUS_PENDING)
            ->with(['child.school', 'child.pickupLocation', 'parent.parentProfile'])
            ->orderByDesc('created_at')
            ->get();

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
                Trip::firstOrCreate([
                    'driver_id' => $booking->driver_id,
                    'child_id' => $booking->child_id,
                    'scheduled_date' => $date->format('Y-m-d'),
                ], [
                    'status' => Trip::STATUS_SCHEDULED,
                    'pricing_tier' => $booking->pricing_tier,
                ]);
            }
        }

        Event::dispatch(new BookingStatusUpdated($booking));

        $parent = $booking->parent;

        if ($parent) {
            $subject = 'Your booking request has been approved';
            $html = '<p>Your booking request for your child has been approved by the driver.</p>';

            $this->resendEmailService->send($parent->email, $subject, $html);
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
            $subject = 'Your booking request has been declined';
            $html = '<p>Your booking request for your child has been declined by the driver.</p>';

            $this->resendEmailService->send($parent->email, $subject, $html);
        }

        return back()->with('status', 'Booking declined.');
    }
}
