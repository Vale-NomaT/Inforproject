<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\Rating;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParentTripController extends Controller
{
    public function index(Request $request, Child $child): View
    {
        if ($child->parent_id !== $request->user()->id) {
            abort(403);
        }

        $trips = Trip::with(['driver', 'events'])
            ->where('child_id', $child->id)
            ->where('status', Trip::STATUS_COMPLETED)
            ->orderByDesc('scheduled_date')
            ->get();

        $ratingsByTrip = Rating::whereIn('trip_id', $trips->pluck('id'))
            ->where('parent_id', $request->user()->id)
            ->get()
            ->keyBy('trip_id');

        return view('parent.trip-history', [
            'child' => $child,
            'trips' => $trips,
            'ratingsByTrip' => $ratingsByTrip,
        ]);
    }

    public function show(Request $request, Trip $trip): View
    {
        if ($trip->child->parent_id !== $request->user()->id) {
            abort(403);
        }

        $trip->load([
            'child.school',
            'child.pickupLocation',
            'events' => fn ($q) => $q->orderBy('created_at'),
            'rating',
        ]);

        return view('parent.trip-map', [
            'trip' => $trip,
            'child' => $trip->child,
        ]);
    }

    public function live(Request $request): \Illuminate\Http\RedirectResponse
    {
        $parent = $request->user();
        
        // 1. Check for active trips (In Progress)
        $activeTrip = Trip::whereHas('child', function ($q) use ($parent) {
                $q->where('parent_id', $parent->id);
            })
            ->whereIn('status', [Trip::STATUS_IN_PROGRESS, 'picked_up', 'arrived_dropoff']) // Check specific statuses if needed
            ->orderByDesc('updated_at')
            ->first();

        if ($activeTrip) {
            return redirect()->route('parent.trips.show', $activeTrip);
        }

        // 2. Check for today's scheduled trips
        $todayTrip = Trip::whereHas('child', function ($q) use ($parent) {
                $q->where('parent_id', $parent->id);
            })
            ->whereDate('scheduled_date', today())
            ->where('status', '!=', Trip::STATUS_COMPLETED)
            ->orderBy('scheduled_time')
            ->first();

        if ($todayTrip) {
            return redirect()->route('parent.trips.show', $todayTrip);
        }

        return redirect()->route('parent.dashboard')->with('info', 'No active or upcoming trips found for today.');
    }

    public function location(Request $request, Trip $trip): \Illuminate\Http\JsonResponse
    {
        if ($trip->child->parent_id !== $request->user()->id) {
            abort(403);
        }

        // Get location from high-performance Cache
        $location = \Illuminate\Support\Facades\Cache::get("trip_location_{$trip->id}");

        if (!$location) {
            return response()->json(['status' => 'waiting']);
        }

        return response()->json([
            'status' => 'ok',
            'lat' => $location['lat'],
            'lng' => $location['lng'],
            'updated_at' => $location['updated_at']
        ]);
    }
}
