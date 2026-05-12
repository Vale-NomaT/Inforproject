<?php

namespace App\Http\Controllers;

use App\Models\BookingRequest;
use App\Models\Child;
use App\Models\Trip;
use App\Models\TripAbsence;
use App\Models\User;
use App\Notifications\AbsenceNotification;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ParentAbsenceController extends Controller
{
    /** Show all absences for a child */
    public function index(Request $request, Child $child): \Illuminate\View\View
    {
        $this->authorizeChild($child, $request->user()->id);

        $absences = TripAbsence::where('child_id', $child->id)
            ->orderByDesc('start_date')
            ->get();

        return view('parent.absences', compact('child', 'absences'));
    }

    /** Store a new absence — cancel matching trips + notify driver */
    public function store(Request $request, Child $child): RedirectResponse
    {
        $this->authorizeChild($child, $request->user()->id);

        $data = $request->validate([
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date'   => ['required', 'date', 'after_or_equal:start_date'],
            'run_type'   => ['required', 'in:morning,afternoon,both'],
            'reason'     => ['nullable', 'string', 'max:500'],
        ]);

        $absence = TripAbsence::create([
            'child_id'   => $child->id,
            'parent_id'  => $request->user()->id,
            'start_date' => $data['start_date'],
            'end_date'   => $data['end_date'],
            'run_type'   => $data['run_type'],
            'reason'     => $data['reason'] ?? null,
        ]);

        // Cancel all matching scheduled trips in the date range
        $this->cancelTrips($child, $absence);

        // Notify the assigned driver
        $this->notifyDriver($child, $absence, true);

        return redirect()->route('parent.dashboard')
            ->with('status', "Absence reported for {$child->first_name}. Your driver has been notified.");
    }

    /** Reinstate (delete) an absence — restore trips + notify driver */
    public function destroy(Request $request, Child $child, TripAbsence $absence): RedirectResponse
    {
        $this->authorizeChild($child, $request->user()->id);

        if ($absence->child_id !== $child->id) {
            abort(403);
        }

        // Restore cancelled trips back to scheduled
        $this->reinstateTrips($child, $absence);

        // Notify driver of reinstatement
        $this->notifyDriver($child, $absence, false);

        $absence->delete();

        return redirect()->route('parent.dashboard')
            ->with('status', "Absence for {$child->first_name} has been cancelled. Your driver has been notified.");
    }

    // ── Private helpers ──────────────────────────────────────────────

    private function cancelTrips(Child $child, TripAbsence $absence): void
    {
        $period = CarbonPeriod::create($absence->start_date, $absence->end_date);

        foreach ($period as $date) {
            if (!$date->isWeekday()) continue;

            $query = Trip::where('child_id', $child->id)
                ->whereDate('scheduled_date', $date->toDateString())
                ->where('status', Trip::STATUS_SCHEDULED);

            if ($absence->run_type !== 'both') {
                $query->where('type', $absence->run_type);
            }

            $query->update(['status' => 'cancelled']);
        }
    }

    private function reinstateTrips(Child $child, TripAbsence $absence): void
    {
        $period = CarbonPeriod::create($absence->start_date, $absence->end_date);

        foreach ($period as $date) {
            if (!$date->isWeekday()) continue;

            // Only reinstate future trips (don't touch past dates)
            if ($date->isPast() && !$date->isToday()) continue;

            $query = Trip::where('child_id', $child->id)
                ->whereDate('scheduled_date', $date->toDateString())
                ->where('status', 'cancelled');

            if ($absence->run_type !== 'both') {
                $query->where('type', $absence->run_type);
            }

            $query->update(['status' => Trip::STATUS_SCHEDULED]);
        }
    }

    private function notifyDriver(Child $child, TripAbsence $absence, bool $isCancellation): void
    {
        // Find the active approved booking driver for this child
        $booking = BookingRequest::where('child_id', $child->id)
            ->where('status', BookingRequest::STATUS_APPROVED)
            ->latest()
            ->first();

        if (!$booking) return;

        $driver = User::find($booking->driver_id);
        if (!$driver) return;

        try {
            $driver->notify(new AbsenceNotification(
                $absence,
                $child->first_name . ' ' . $child->last_name,
                $isCancellation
            ));

            if ($isCancellation) {
                $absence->update(['driver_notified_at' => now()]);
            }
        } catch (\Exception $e) {
            Log::error('AbsenceNotification failed: ' . $e->getMessage());
        }
    }

    private function authorizeChild(Child $child, int $parentId): void
    {
        if ($child->parent_id !== $parentId) {
            abort(403);
        }
    }
}
