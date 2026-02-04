<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Trip;
use App\Models\BookingRequest;

class DriverDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $driverId = Auth::id();

        $trips = Trip::with(['child.school'])
            ->where('driver_id', $driverId)
            ->whereDate('scheduled_date', now())
            ->get();

        $pendingBookingsCount = BookingRequest::where('driver_id', $driverId)
            ->where('status', BookingRequest::STATUS_PENDING)
            ->count();

        return view('dashboard.driver', compact('trips', 'pendingBookingsCount'));
    }
}
