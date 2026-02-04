<?php

namespace App\Http\Controllers;

use App\Models\DriverPerformanceScore;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminReportController extends Controller
{
    public function trips(Request $request): StreamedResponse
    {
        $date = $request->query('date');

        $query = Trip::with(['child', 'driver'])
            ->orderBy('scheduled_date');

        if ($date) {
            $query->whereDate('scheduled_date', $date);
        }

        $filename = 'trips_'.($date ?: 'all').'.csv';

        $response = new StreamedResponse(function () use ($query) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Trip ID',
                'Date',
                'Status',
                'Driver Name',
                'Driver Email',
                'Child Name',
                'School',
                'Pricing Tier',
                'Distance Km',
            ]);

            $query->chunk(200, function ($trips) use ($handle) {
                foreach ($trips as $trip) {
                    $child = $trip->child;
                    $driver = $trip->driver;

                    fputcsv($handle, [
                        $trip->id,
                        $trip->scheduled_date ? $trip->scheduled_date->format('Y-m-d') : '',
                        $trip->status,
                        $driver ? $driver->name : '',
                        $driver ? $driver->email : '',
                        $child ? ($child->first_name.' '.$child->last_name) : '',
                        $child && $child->school ? $child->school->name : '',
                        $trip->pricing_tier,
                        $trip->distance_km,
                    ]);
                }
            });

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'"');

        return $response;
    }

    public function signups(Request $request): StreamedResponse
    {
        $date = $request->query('date');

        $query = User::orderBy('created_at');

        if ($date) {
            $query->whereDate('created_at', $date);
        }

        $filename = 'signups_'.($date ?: 'all').'.csv';

        $response = new StreamedResponse(function () use ($query) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'User ID',
                'Name',
                'Email',
                'Type',
                'Status',
                'Created At',
            ]);

            $query->chunk(200, function ($users) use ($handle) {
                foreach ($users as $user) {
                    fputcsv($handle, [
                        $user->id,
                        $user->name,
                        $user->email,
                        $user->user_type,
                        $user->status,
                        $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : '',
                    ]);
                }
            });

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'"');

        return $response;
    }

    public function driverPerformance(Request $request): StreamedResponse
    {
        $date = $request->query('date');

        $query = DriverPerformanceScore::with(['driver'])
            ->orderBy('calculated_at', 'desc');

        if ($date) {
            $query->whereDate('calculated_at', $date);
        }

        $filename = 'driver_performance_'.($date ?: 'all').'.csv';

        $response = new StreamedResponse(function () use ($query) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Driver ID',
                'Driver Name',
                'Driver Email',
                'Score',
                'Average Rating',
                'Reliability',
                'Punctuality',
                'Calculated At',
            ]);

            $query->chunk(200, function ($scores) use ($handle) {
                foreach ($scores as $score) {
                    $driver = $score->driver;

                    fputcsv($handle, [
                        $score->driver_id,
                        $driver ? $driver->name : '',
                        $driver ? $driver->email : '',
                        $score->score,
                        $score->avg_rating,
                        $score->reliability,
                        $score->punctuality,
                        $score->calculated_at ? $score->calculated_at->format('Y-m-d H:i:s') : '',
                    ]);
                }
            });

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'"');

        return $response;
    }
}
