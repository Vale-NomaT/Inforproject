<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Trip;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParentRatingController extends Controller
{
    public function create(Request $request, Trip $trip): View
    {
        $child = $trip->child;

        if (! $child || $child->parent_id !== $request->user()->id) {
            abort(403);
        }

        if ($trip->status !== Trip::STATUS_COMPLETED) {
            abort(404);
        }

        $existing = Rating::where('trip_id', $trip->id)
            ->where('parent_id', $request->user()->id)
            ->first();

        if ($existing) {
            abort(404);
        }

        $trip->load('driver');

        return view('parent.trip-rating', [
            'trip' => $trip,
            'child' => $child,
        ]);
    }

    public function store(Request $request, Trip $trip): RedirectResponse
    {
        $child = $trip->child;

        if (! $child || $child->parent_id !== $request->user()->id) {
            abort(403);
        }

        if ($trip->status !== Trip::STATUS_COMPLETED) {
            abort(404);
        }

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        Rating::updateOrCreate(
            [
                'trip_id' => $trip->id,
                'parent_id' => $request->user()->id,
            ],
            [
                'driver_id' => $trip->driver_id,
                'rating' => $data['rating'],
                'comment' => $data['comment'] ?? null,
            ]
        );

        return redirect()->route('parent.dashboard')->with('status', 'Thanks for rating your driver.');
    }
}
