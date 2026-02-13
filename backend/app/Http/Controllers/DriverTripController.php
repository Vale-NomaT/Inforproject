<?php

namespace App\Http\Controllers;

use App\Events\TripEventBroadcasted;
use App\Events\TripLocationUpdated;
use App\Models\Trip;
use App\Models\TripEvent;
use App\Models\User;
use App\Services\ResendEmailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;

class DriverTripController extends Controller
{
    protected ResendEmailService $resendEmailService;

    public function __construct(ResendEmailService $resendEmailService)
    {
        $this->resendEmailService = $resendEmailService;
    }

    public function index(Request $request): View
    {
        $trips = Trip::with(['child.school', 'child.pickupLocation'])
            ->where('driver_id', $request->user()->id)
            ->whereIn('status', [Trip::STATUS_SCHEDULED, Trip::STATUS_IN_PROGRESS])
            ->orderBy('scheduled_date')
            ->orderByDesc('type') // Morning first
            ->get();

        return view('driver.trips', compact('trips'));
    }

    public function map(Request $request): View
    {
        $trips = Trip::with(['child.school', 'child.pickupLocation'])
            ->where('driver_id', $request->user()->id)
            ->whereIn('status', [Trip::STATUS_SCHEDULED, Trip::STATUS_IN_PROGRESS])
            ->whereDate('scheduled_date', now())
            ->orderByDesc('type') // Morning first
            ->get();

        return view('driver.map', compact('trips'));
    }

    public function history(Request $request): View
    {
        $trips = Trip::with(['child.school'])
            ->where('driver_id', $request->user()->id)
            ->where('status', Trip::STATUS_COMPLETED)
            ->orderByDesc('scheduled_date')
            ->get();

        return view('driver.trip-history', [
            'trips' => $trips,
        ]);
    }

    public function start(Request $request, Trip $trip): RedirectResponse
    {
        if ($trip->driver_id !== $request->user()->id) {
            abort(403);
        }

        // Check if driver already has an active trip
        $activeTrip = Trip::where('driver_id', $request->user()->id)
            ->where('status', Trip::STATUS_IN_PROGRESS)
            ->exists();

        if ($activeTrip) {
            return back()->with('error', 'You already have a trip in progress. Please complete it before starting a new one.');
        }

        if ($trip->status !== Trip::STATUS_SCHEDULED) {
            return back();
        }

        $trip->status = Trip::STATUS_IN_PROGRESS;
        $trip->save();

        $event = TripEvent::create([
            'trip_id' => $trip->id,
            'type' => 'started',
            'created_at' => now(),
        ]);

        Event::dispatch(new TripEventBroadcasted($event));

        $this->notifyParent($trip, 'Driver has started the trip.');

        return back()->with('status', 'Trip started. Tracking is now active.');
    }

    public function updateLocation(Request $request, Trip $trip): JsonResponse
    {
        if ($trip->driver_id !== $request->user()->id) {
            abort(403);
        }

        $data = $request->validate([
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
        ]);

        Event::dispatch(new TripLocationUpdated($trip, (float) $data['lat'], (float) $data['lng']));

        return response()->json([
            'status' => 'ok',
        ]);
    }

    public function logEvent(Request $request, Trip $trip): JsonResponse
    {
        if ($trip->driver_id !== $request->user()->id) {
            abort(403);
        }

        $data = $request->validate([
            'type' => ['required', 'in:started,arrived,picked_up,arrived_dropoff,dropped_off'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
        ]);

        $event = TripEvent::create([
            'trip_id' => $trip->id,
            'type' => $data['type'],
            'created_at' => now(),
            'lat' => array_key_exists('lat', $data) ? $data['lat'] : null,
            'lng' => array_key_exists('lng', $data) ? $data['lng'] : null,
        ]);

        if ($data['type'] === 'dropped_off') {
            $trip->status = Trip::STATUS_COMPLETED;
            $trip->save();
        }

        Event::dispatch(new TripEventBroadcasted($event));

        if ($data['type'] === 'arrived') {
            $this->notifyParent($trip, 'Driver has arrived at the pickup location.');
        } elseif ($data['type'] === 'picked_up') {
            $this->notifyParent($trip, 'Your child has been picked up.');
        } elseif ($data['type'] === 'arrived_dropoff') {
            $this->notifyParent($trip, 'Driver has arrived at the drop-off location.');
        } elseif ($data['type'] === 'dropped_off') {
            $this->notifyParent($trip, 'Your child has been dropped off.');
        }

        return response()->json([
            'status' => 'ok',
        ]);
    }

    protected function notifyParent(Trip $trip, string $message): void
    {
        $child = $trip->child;

        if (! $child) {
            return;
        }

        $parentUser = User::find($child->parent_id);

        if (! $parentUser) {
            return;
        }

        $subject = 'Trip update for '.$child->first_name.' '.$child->last_name;
        $html = '<p>'.e($message).'</p>';

        $this->resendEmailService->send($parentUser->email, $subject, $html);
    }
}
