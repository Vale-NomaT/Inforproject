@extends('layouts.master')

@section('content')
<div class="flex flex-col gap-4 mb-5 md:flex-row md:items-center md:justify-between">
    <div>
        <h5 class="text-16">Trip History</h5>
        <p class="text-slate-500 dark:text-zink-200">Completed school runs and earnings for each child you serve.</p>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if ($trips->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-200 bg-white px-6 py-10 text-center dark:bg-zink-700 dark:border-zink-500">
                <h2 class="text-lg sm:text-xl font-semibold text-slate-900 dark:text-slate-100">
                    No completed trips yet
                </h2>
                <p class="mt-2 text-sm sm:text-base text-slate-600 dark:text-slate-300">
                    Once you start completing trips, your history and earnings will appear here.
                </p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($trips as $trip)
                    @php
                        $child = $trip->child;
                        $childLabel = $child
                            ? $child->first_name . ' ' . (mb_substr($child->last_name, 0, 1) . '.')
                            : 'Child';
                        $earnings = null;
                        if ($trip->pricing_tier === 1) {
                            $earnings = 28;
                        } elseif ($trip->pricing_tier === 2) {
                            $earnings = 45;
                        }
                    @endphp
                    <div class="flex flex-col rounded-2xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm dark:bg-zink-700 dark:border-zink-500">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-base sm:text-lg font-semibold text-slate-900 dark:text-zink-100">
                                    {{ $childLabel }}
                                </h2>
                                <p class="mt-1 text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                                    Date:
                                    <span class="font-medium dark:text-zink-200">
                                        {{ $trip->scheduled_date->format('Y-m-d') }}
                                    </span>
                                </p>
                                @if ($child && $child->school)
                                    <p class="mt-1 text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                                        School:
                                        <span class="font-medium dark:text-zink-200">
                                            {{ $child->school->name }}
                                        </span>
                                    </p>
                                @endif
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                @if ($earnings !== null)
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">
                                        Earnings: ${{ number_format($earnings, 2) }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700 dark:bg-zink-600 dark:text-zink-200">
                                        Earnings pending
                                    </span>
                                @endif
                                @if ($trip->pricing_tier)
                                    <span class="inline-flex items-center rounded-full bg-slate-50 px-3 py-1 text-xs font-medium text-slate-600 dark:bg-zink-600 dark:text-zink-200">
                                        Tier {{ $trip->pricing_tier }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
