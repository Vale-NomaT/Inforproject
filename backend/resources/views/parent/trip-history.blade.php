@extends('layouts.master')

@section('content')
<div class="flex flex-col gap-4 mb-5 md:flex-row md:items-center md:justify-between">
    <div>
        <h5 class="text-16">Trip History</h5>
        <p class="text-slate-500 dark:text-zink-200">History for {{ $child->first_name }} {{ $child->last_name }}</p>
    </div>
    <div>
        <a href="{{ route('parent.dashboard') }}" class="text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">Back to Dashboard</a>
    </div>
</div>

@if ($child->school)
    <div class="mb-5">
        <p class="text-slate-500 dark:text-zink-200">
            School: <span class="font-medium text-slate-800 dark:text-zink-50">{{ $child->school->name }}</span>
        </p>
    </div>
@endif

@if ($trips->isEmpty())
    <div class="card">
        <div class="card-body text-center py-10">
            <h5 class="text-16 mb-2">No completed trips yet</h5>
            <p class="text-slate-500 dark:text-zink-200">Once your child has completed rides with SafeRide Kids, you will see a full history here.</p>
        </div>
    </div>
@else
    <div class="space-y-4">
        @foreach ($trips as $trip)
            @php
                $rating = $ratingsByTrip->get($trip->id);
            @endphp
            <div class="card">
                <div class="card-body">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                        <div>
                            <p class="text-slate-500 dark:text-zink-200 mb-1">
                                Date: <span class="font-medium text-slate-800 dark:text-zink-50">{{ $trip->scheduled_date->format('Y-m-d') }}</span>
                            </p>
                            @if ($child->pickupLocation && $child->school)
                                <p class="text-slate-500 dark:text-zink-200 mb-1">
                                    Route: <span class="font-medium text-slate-800 dark:text-zink-50">{{ $child->pickupLocation->name }} &rarr; {{ $child->school->name }}</span>
                                </p>
                            @endif
                            @if ($trip->driver)
                                <p class="text-slate-500 dark:text-zink-200">
                                    Driver: <span class="font-medium text-slate-800 dark:text-zink-50">{{ $trip->driver->name }}</span>
                                </p>
                            @endif
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            @if ($rating)
                                <span class="px-2.5 py-0.5 text-xs inline-block font-medium rounded border border-green-200 bg-green-100 text-green-500 dark:bg-green-500/20 dark:border-green-500/20">
                                    Rated {{ $rating->rating }} / 5
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 text-xs inline-block font-medium rounded border border-slate-200 bg-slate-100 text-slate-500 dark:bg-slate-500/20 dark:border-slate-500/20 dark:text-zink-200">
                                    Not yet rated
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                        @if ($rating && $rating->comment)
                            <p class="text-slate-500 dark:text-zink-200 italic">
                                “{{ $rating->comment }}”
                            </p>
                        @else
                            <div></div>
                        @endif
                        
                        <div class="flex gap-2 justify-end">
                            <a href="{{ route('parent.trips.show', ['trip' => $trip->id]) }}" class="text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20 text-xs">
                                View Timeline
                            </a>
                            @if (! $rating)
                                <a href="{{ route('parent.trips.rate.create', ['trip' => $trip->id]) }}" class="text-white btn bg-yellow-500 border-yellow-500 hover:text-white hover:bg-yellow-600 hover:border-yellow-600 focus:text-white focus:bg-yellow-600 focus:border-yellow-600 focus:ring focus:ring-yellow-100 active:text-white active:bg-yellow-600 active:border-yellow-600 active:ring active:ring-yellow-100 dark:ring-yellow-400/20 text-xs">
                                    Rate Driver
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
