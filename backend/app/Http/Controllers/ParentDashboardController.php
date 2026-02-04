<?php

namespace App\Http\Controllers;

use App\Models\Child;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParentDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $children = Child::with([
            'school',
            'pickupLocation',
            'bookingRequests' => function ($query) {
                $query->orderByDesc('created_at');
            },
            'trips' => function ($query) {
                $query->orderByDesc('scheduled_date');
            },
        ])
            ->where('parent_id', $request->user()->id)
            ->get();

        return view('dashboard.parent', [
            'children' => $children,
        ]);
    }
}
