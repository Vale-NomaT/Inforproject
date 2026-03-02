<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Trip;
use App\Models\DriverPerformanceScore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class ParentRatingController extends Controller
{
    public function create(Request $request, Trip $trip): View|RedirectResponse
    {
        $child = $trip->child;

        if (! $child || $child->parent_id !== $request->user()->id) {
            abort(403);
        }

        if ($trip->status !== Trip::STATUS_COMPLETED) {
            return redirect()
                ->route('parent.children.trips.index', $child)
                ->withErrors(['trip' => 'Trip must be completed before rating.']);
        }

        $completionTime = $trip->completed_at ?? $trip->updated_at;
        if ($completionTime && $completionTime->diffInHours(now()) > 24) {
            return redirect()
                ->route('parent.children.trips.index', $child)
                ->withErrors(['trip' => 'Rating window closed. Contact support if one experiences issues.']);
        }

        $existing = Rating::where('trip_id', $trip->id)->first();

        if ($existing) {
            return redirect()
                ->route('parent.children.trips.index', $child)
                ->withErrors(['trip' => 'You have already rated this trip.']);
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
            return back()->withErrors(['trip' => 'Trip must be completed before rating.'])->withInput();
        }
        
        $completionTime = $trip->completed_at ?? $trip->updated_at;
        if ($completionTime && $completionTime->diffInHours(now()) > 24) {
            return back()
                ->withErrors(['trip' => 'Rating window closed. Contact support if one experiences issues.'])
                ->withInput();
        }

        $existing = Rating::where('trip_id', $trip->id)->first();
        if ($existing) {
            return back()->withErrors(['trip' => 'You have already rated this trip.'])->withInput();
        }

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:300'],
        ]);

        DB::transaction(function () use ($trip, $request, $data) {
            Rating::updateOrCreate(
                [
                    'trip_id' => $trip->id,
                ],
                [
                    'parent_id' => $request->user()->id,
                    'child_id' => $trip->child_id,
                    'driver_id' => $trip->driver_id,
                    'rating' => $data['rating'],
                    'comment' => $data['comment'] ?? null,
                ]
            );

            $this->updateDriverPerformance($trip->driver_id);
        });

        return redirect()
            ->route('parent.dashboard')
            ->with('status', 'Thank you for your feedback!');
    }

    private function updateDriverPerformance($driverId)
    {
        $avgRating = (float) (Rating::where('driver_id', $driverId)->avg('rating') ?? 0);

        $assigned = Trip::where('driver_id', $driverId)->count();
        $completed = Trip::where('driver_id', $driverId)->where('status', Trip::STATUS_COMPLETED)->count();
        $reliability = $assigned > 0 ? ($completed / $assigned) : 0.0;

        $onTime = Trip::where('driver_id', $driverId)
            ->where('status', Trip::STATUS_COMPLETED)
            ->where('is_on_time', true)
            ->count();
        $punctuality = $completed > 0 ? ($onTime / $completed) : 0.0;

        $normalizedRating = ($avgRating / 5) * 100;
        $score = ($normalizedRating * 0.6) + (($reliability * 100) * 0.2) + (($punctuality * 100) * 0.2);
        $score = round($score, 2);

        DriverPerformanceScore::updateOrCreate(
            ['driver_id' => $driverId],
            [
                'avg_rating' => $avgRating,
                'reliability' => $reliability,
                'punctuality' => $punctuality,
                'score' => $score,
                'calculated_at' => now(),
            ]
        );
    }
}
