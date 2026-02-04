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
}
