<?php

namespace App\Http\Controllers;

use App\Events\TripEventBroadcasted;
use App\Events\TripLocationUpdated;
use App\Models\Trip;
use App\Models\TripEvent;
use App\Models\User;
use App\Notifications\TripUpdateNotification;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;

class DriverTripController extends Controller
{
    public function index(Request $request): View
    {
        $trips = Trip::with(['child.school', 'child.pickupLocation', 'events'])
            ->where('driver_id', $request->user()->id)
            ->where(function ($query) {
                $query->whereIn('status', [Trip::STATUS_SCHEDULED, Trip::STATUS_IN_PROGRESS])
                      ->orWhere(function ($q) {
                          $q->where('status', Trip::STATUS_COMPLETED)
                            ->whereDate('scheduled_date', now());
                      });
            })
            ->orderBy('scheduled_date')
            ->orderByDesc('type') // Morning first
            ->get();

        $runs = $trips
            ->groupBy(function (Trip $trip) {
                $date = $trip->scheduled_date ? $trip->scheduled_date->format('Y-m-d') : 'unknown';

                return $date.'|'.$trip->type;
            })
            ->map(function ($group, string $key) {
                [$date, $type] = array_pad(explode('|', $key, 2), 2, null);

                $allCompleted = $group->every(fn (Trip $trip) => $trip->status === Trip::STATUS_COMPLETED);
                $hasInProgress = $group->contains(fn (Trip $trip) => $trip->status === Trip::STATUS_IN_PROGRESS);
                $anyCompleted = $group->contains(fn (Trip $trip) => $trip->status === Trip::STATUS_COMPLETED);

                $status = Trip::STATUS_SCHEDULED;
                if ($allCompleted) {
                    $status = Trip::STATUS_COMPLETED;
                } elseif ($hasInProgress || $anyCompleted) {
                    $status = Trip::STATUS_IN_PROGRESS;
                }

                return [
                    'key' => $key,
                    'date' => $date,
                    'type' => $type,
                    'status' => $status,
                    'trips' => $group->values(),
                ];
            })
            ->values();

        return view('driver.trips', [
            'runs' => $runs,
            'trips' => $trips,
        ]);
    }

    public function map(Request $request): View
    {
        $trips = Trip::with(['child.school', 'child.pickupLocation', 'events'])
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

        // Removed check for active trip to allow multiple concurrent trips (multi-pickup support)
        /*
        $activeTrip = Trip::where('driver_id', $request->user()->id)
            ->where('status', Trip::STATUS_IN_PROGRESS)
            ->exists();

        if ($activeTrip) {
            return back()->with('error', 'You already have a trip in progress. Please complete it before starting a new one.');
        }
        */

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

        $etaMins = $trip->distance_km ? ceil($trip->distance_km * 2) : null; // Approx 30km/h
        $etaMsg = $etaMins ? " ETA: ~{$etaMins} mins." : "";
        $this->notifyParent($trip, 'Driver has started the trip and is en route.' . $etaMsg);

        return back()->with('status', 'Trip started. Tracking is now active.');
    }

    public function startRun(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'type' => ['required', 'in:'.Trip::TYPE_MORNING.','.Trip::TYPE_AFTERNOON],
        ]);

        $trips = Trip::with(['child'])
            ->where('driver_id', $request->user()->id)
            ->whereDate('scheduled_date', $data['date'])
            ->where('type', $data['type'])
            ->where('status', Trip::STATUS_SCHEDULED)
            ->get();

        if ($trips->isEmpty()) {
            return back();
        }

        $childNames = [];

        foreach ($trips as $trip) {
            $trip->status = Trip::STATUS_IN_PROGRESS;
            $trip->save();

            if ($trip->child) {
                $childNames[] = $trip->child->first_name;
            }

            $event = TripEvent::create([
                'trip_id' => $trip->id,
                'type' => 'started',
                'created_at' => now(),
            ]);

            Event::dispatch(new TripEventBroadcasted($event));

            $etaMins = $trip->distance_km ? ceil($trip->distance_km * 2) : null;
            $etaMsg = $etaMins ? " ETA: ~{$etaMins} mins." : "";
            $this->notifyParent($trip, 'Driver has started the trip and is en route.' . $etaMsg);
        }

        $message = 'Route started.';
        if (!empty($childNames)) {
            $namesStr = implode(', ', array_slice($childNames, 0, 3));
            if (count($childNames) > 3) {
                $namesStr .= ' and ' . (count($childNames) - 3) . ' others';
            }
            $message .= " Picking up: $namesStr.";
        }

        return back()->with('status', $message . ' Tracking is now active.');
    }

    /**
     * Update location for ALL in-progress trips for this driver.
     * This allows the driver to broadcast their location to all relevant parents simultaneously.
     */
    public function updateDriverLocation(Request $request): JsonResponse
    {
        $data = $request->validate([
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
        ]);

        $activeTrips = Trip::where('driver_id', $request->user()->id)
            ->where('status', Trip::STATUS_IN_PROGRESS)
            ->get();

        foreach ($activeTrips as $trip) {
            Event::dispatch(new TripLocationUpdated($trip, (float) $data['lat'], (float) $data['lng']));
        }

        return response()->json([
            'status' => 'ok',
            'updated_count' => $activeTrips->count()
        ]);
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
            $trip->completed_at = $event->created_at;
            $trip->is_on_time = $this->calculateIsOnTime($trip, $event);
            $trip->save();
        }

        Event::dispatch(new TripEventBroadcasted($event));

        if ($data['type'] === 'arrived') {
            $this->notifyParent($trip, 'Driver has arrived at the pickup location.');
        } elseif ($data['type'] === 'picked_up') {
            $this->notifyParent($trip, 'Your child has been picked up and is en route to the destination.');
        } elseif ($data['type'] === 'arrived_dropoff') {
            $this->notifyParent($trip, 'Driver has arrived at the drop-off location.');
        } elseif ($data['type'] === 'dropped_off') {
            $this->notifyParent($trip, 'Your child has been dropped off safely.');
        }

        return response()->json([
            'status' => 'ok',
        ]);
    }

    private function calculateIsOnTime(Trip $trip, TripEvent $dropOffEvent): ?bool
    {
        $trip->loadMissing('child');
        $child = $trip->child;

        if (! $child || ! $trip->scheduled_date) {
            return null;
        }

        if ($trip->type === Trip::TYPE_MORNING && $child->school_start_time) {
            $expected = Carbon::parse($trip->scheduled_date->toDateString().' '.$child->school_start_time);

            return $dropOffEvent->created_at?->lte($expected);
        }

        if ($trip->type === Trip::TYPE_AFTERNOON && $child->school_end_time) {
            $expected = Carbon::parse($trip->scheduled_date->toDateString().' '.$child->school_end_time);

            $pickedUpEvent = TripEvent::where('trip_id', $trip->id)
                ->where('type', 'picked_up')
                ->orderByDesc('created_at')
                ->first();

            $actual = $pickedUpEvent?->created_at ?? $dropOffEvent->created_at;

            return $actual?->lte($expected);
        }

        return null;
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

        $parentUser->notify(new TripUpdateNotification($message, $trip->id, $child->first_name . ' ' . $child->last_name));
    }
}
