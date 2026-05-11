<?php

namespace App\Console\Commands;

use App\Models\Child;
use App\Models\Trip;
use App\Models\TripEvent;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SimulateTrip extends Command
{
    protected $signature   = 'trip:simulate
                                {--driver=3 : Driver user ID (default: Driver A)}
                                {--child=1  : Child ID (default: Emma)}
                                {--delay=3  : Seconds between each event}';

    protected $description = 'Simulate a full trip lifecycle: scheduled → started → arrived → picked_up → dropped_off';

    // Bulawayo route: Bulawayo City Hall → Whitestone School
    // Approximate GPS waypoints along the route
    private array $routeWaypoints = [
        ['lat' => -20.1500, 'lng' => 28.5800, 'label' => 'Bulawayo City Hall (pickup)'],
        ['lat' => -20.1480, 'lng' => 28.5850, 'label' => 'Fife Street'],
        ['lat' => -20.1450, 'lng' => 28.5900, 'label' => 'Lobengula Street'],
        ['lat' => -20.1400, 'lng' => 28.5950, 'label' => 'Turning onto Hillside Rd'],
        ['lat' => -20.1350, 'lng' => 28.6000, 'label' => 'Hillside Road'],
        ['lat' => -20.1300, 'lng' => 28.6050, 'label' => 'Approaching Whitestone'],
        ['lat' => -20.1250, 'lng' => 28.6100, 'label' => 'Whitestone School (drop-off)'],
    ];

    public function handle(): int
    {
        $driverId = (int) $this->option('driver');
        $childId  = (int) $this->option('child');
        $delay    = (int) $this->option('delay');

        $driver = User::find($driverId);
        $child  = Child::with(['school', 'pickupLocation'])->find($childId);

        if (! $driver || $driver->user_type !== 'driver') {
            $this->error("Driver ID {$driverId} not found or is not a driver.");
            return 1;
        }

        if (! $child) {
            $this->error("Child ID {$childId} not found.");
            return 1;
        }

        $this->info('');
        $this->info('╔══════════════════════════════════════════════════╗');
        $this->info('║         SafeRide Kids — Trip Simulator           ║');
        $this->info('╚══════════════════════════════════════════════════╝');
        $this->info('');
        $this->line("  🚗  Driver  : <fg=cyan>{$driver->name}</>");
        $this->line("  👦  Child   : <fg=cyan>{$child->first_name} {$child->last_name}</>");
        $this->line("  🏫  School  : <fg=cyan>{$child->school?->name}</>");
        $this->line("  📍  Pickup  : <fg=cyan>{$child->pickupLocation?->name}</>");
        $this->line("  ⏱   Delay   : <fg=cyan>{$delay}s between events</>");
        $this->info('');

        // ── 1. Create trip ──────────────────────────────────────────
        $this->step('1', 'Creating trip for today…');

        $trip = Trip::create([
            'driver_id'     => $driver->id,
            'child_id'      => $child->id,
            'scheduled_date'=> now()->toDateString(),
            'scheduled_time'=> $child->school_start_time ?? '07:30:00',
            'type'          => Trip::TYPE_MORNING,
            'status'        => Trip::STATUS_SCHEDULED,
            'pricing_tier'  => 1,
            'distance_km'   => 12.0,
        ]);

        $this->success("  Trip #{$trip->id} created — status: <fg=yellow>scheduled</>");
        $this->pause($delay);

        // ── 2. Start trip ───────────────────────────────────────────
        $this->step('2', 'Driver starts the trip…');

        $trip->status = Trip::STATUS_IN_PROGRESS;
        $trip->save();

        $startCoord = $this->routeWaypoints[0];
        TripEvent::create([
            'trip_id'    => $trip->id,
            'type'       => 'started',
            'lat'        => $startCoord['lat'],
            'lng'        => $startCoord['lng'],
            'created_at' => now(),
        ]);
        $this->updateCache($trip->id, $startCoord['lat'], $startCoord['lng']);
        $this->success("  Status: <fg=green>in_progress</> | Location: {$startCoord['label']}");
        $this->pause($delay);

        // ── 3. Drive to pickup — broadcast location updates ─────────
        $this->step('3', 'Driving to pickup location…');

        foreach (array_slice($this->routeWaypoints, 1, 2) as $wp) {
            $this->updateCache($trip->id, $wp['lat'], $wp['lng']);
            $this->line("  📡 Location update → {$wp['label']} ({$wp['lat']}, {$wp['lng']})");
            $this->pause(max(1, (int)($delay / 2)));
        }

        // ── 4. Arrived at pickup ────────────────────────────────────
        $this->step('4', 'Driver arrived at pickup point…');

        $arrivedCoord = $this->routeWaypoints[0];
        TripEvent::create([
            'trip_id'    => $trip->id,
            'type'       => 'arrived',
            'lat'        => $arrivedCoord['lat'],
            'lng'        => $arrivedCoord['lng'],
            'created_at' => now(),
        ]);
        $this->updateCache($trip->id, $arrivedCoord['lat'], $arrivedCoord['lng']);
        $this->success("  Event: <fg=cyan>arrived</> at {$arrivedCoord['label']}");
        $this->pause($delay);

        // ── 5. Child picked up ──────────────────────────────────────
        $this->step('5', 'Child picked up…');

        TripEvent::create([
            'trip_id'    => $trip->id,
            'type'       => 'picked_up',
            'lat'        => $arrivedCoord['lat'],
            'lng'        => $arrivedCoord['lng'],
            'created_at' => now(),
        ]);
        $this->success("  Event: <fg=cyan>picked_up</> — {$child->first_name} is in the vehicle");
        $this->pause($delay);

        // ── 6. Drive to school — broadcast location updates ─────────
        $this->step('6', 'Driving to school…');

        foreach (array_slice($this->routeWaypoints, 2, 4) as $wp) {
            $this->updateCache($trip->id, $wp['lat'], $wp['lng']);
            $this->line("  📡 Location update → {$wp['label']} ({$wp['lat']}, {$wp['lng']})");
            $this->pause(max(1, (int)($delay / 2)));
        }

        // ── 7. Arrived at school ────────────────────────────────────
        $this->step('7', 'Arrived at school drop-off…');

        $dropCoord = end($this->routeWaypoints);
        TripEvent::create([
            'trip_id'    => $trip->id,
            'type'       => 'arrived_dropoff',
            'lat'        => $dropCoord['lat'],
            'lng'        => $dropCoord['lng'],
            'created_at' => now(),
        ]);
        $this->updateCache($trip->id, $dropCoord['lat'], $dropCoord['lng']);
        $this->success("  Event: <fg=cyan>arrived_dropoff</> at {$dropCoord['label']}");
        $this->pause($delay);

        // ── 8. Child dropped off — complete trip ────────────────────
        $this->step('8', 'Child dropped off — completing trip…');

        $dropEvent = TripEvent::create([
            'trip_id'    => $trip->id,
            'type'       => 'dropped_off',
            'lat'        => $dropCoord['lat'],
            'lng'        => $dropCoord['lng'],
            'created_at' => now(),
        ]);

        $trip->status       = Trip::STATUS_COMPLETED;
        $trip->completed_at = now();
        $trip->is_on_time   = true;
        $trip->save();

        Cache::forget("trip_location_{$trip->id}");

        $this->success("  Event: <fg=cyan>dropped_off</> — trip complete ✓");
        $this->info('');
        $this->info('╔══════════════════════════════════════════════════╗');
        $this->info('║   ✅  Trip simulation completed successfully!    ║');
        $this->info('╚══════════════════════════════════════════════════╝');
        $this->info('');
        $this->line("  Trip ID   : <fg=green>#{$trip->id}</>");
        $this->line("  Driver    : <fg=green>{$driver->name}</>");
        $this->line("  Child     : <fg=green>{$child->first_name} {$child->last_name}</>");
        $this->line("  Status    : <fg=green>completed</>");
        $this->line("  On time   : <fg=green>yes</>");
        $this->line("  Events    : <fg=green>".TripEvent::where('trip_id', $trip->id)->count()." logged</>");
        $this->info('');
        $this->line("  View in admin: <fg=cyan>http://127.0.0.1:8000/admin/dashboard</>");
        $this->info('');

        return 0;
    }

    private function step(string $num, string $message): void
    {
        $this->info("  ── Step {$num}: {$message}");
    }

    private function success(string $message): void
    {
        $this->line("  ✓ {$message}");
    }

    private function pause(int $seconds): void
    {
        if ($seconds > 0) {
            $this->line("  <fg=gray>  ⏳ waiting {$seconds}s…</>");
            sleep($seconds);
        }
    }

    private function updateCache(int $tripId, float $lat, float $lng): void
    {
        Cache::put("trip_location_{$tripId}", [
            'lat'        => $lat,
            'lng'        => $lng,
            'updated_at' => now()->timestamp,
        ], 300);
    }
}
