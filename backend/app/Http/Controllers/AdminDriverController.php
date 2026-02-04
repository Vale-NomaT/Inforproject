<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDriverController extends Controller
{
    public function indexPending(Request $request): View
    {
        $drivers = User::where('user_type', 'driver')
            ->where('status', 'pending')
            ->with('driverProfile')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.drivers-pending', [
            'drivers' => $drivers,
        ]);
    }

    public function approve(Request $request, User $driver): RedirectResponse
    {
        if ($driver->user_type !== 'driver') {
            abort(404);
        }

        $driver->status = 'active';
        $driver->status_reason = null;
        $driver->save();

        return redirect()->route('admin.drivers.pending')
            ->with('status', 'Driver approved.');
    }

    public function reject(Request $request, User $driver): RedirectResponse
    {
        if ($driver->user_type !== 'driver') {
            abort(404);
        }

        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $driver->status = 'suspended';
        $driver->status_reason = $data['reason'] ?? null;
        $driver->save();

        return redirect()->route('admin.drivers.pending')
            ->with('status', 'Driver rejected.');
    }
}
