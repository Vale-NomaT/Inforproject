<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DriverRatingController extends Controller
{
    public function index(Request $request): View
    {
        $ratings = Rating::with(['parent', 'child', 'trip'])
            ->where('driver_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get();

        $averageRating = $ratings->avg('rating');
        $totalRatings = $ratings->count();

        return view('driver.ratings', [
            'ratings' => $ratings,
            'averageRating' => $averageRating,
            'totalRatings' => $totalRatings,
        ]);
    }
}
