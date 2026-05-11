@extends('layouts.master')

@section('title', 'Live Tracking')

@section('content')

<div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
    <div class="grow">
        <h5 class="text-16 font-semibold text-slate-800 dark:text-zink-50">Live Tracking</h5>
        <p class="text-slate-500 dark:text-zink-300 text-sm mt-0.5">Real-time location of all registered drivers per trip.</p>
    </div>
    <div class="flex items-center gap-2">
        <span class="flex items-center gap-1.5 text-sm text-emerald-600 dark:text-emerald-400 font-medium">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse inline-block"></span>
            {{ $totalActive }} driver{{ $totalActive !== 1 ? 's' : '' }} on a trip now
        </span>
    </div>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4 flex items-center gap-3">
            <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-500/20 shrink-0">
                <i data-lucide="users" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-slate-800 dark:text-zink-50">{{ $activeDrivers->count() }}</p>
                <p class="text-xs text-slate-500 dark:text-zink-300">Active Drivers</p>
            </div>
        </div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4 flex items-center gap-3">
            <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 shrink-0">
                <i data-lucide="navigation" class="w-5 h-5 text-emerald-600 dark:text-emerald-400"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-slate-800 dark:text-zink-50">{{ $totalActive }}</p>
                <p class="text-xs text-slate-500 dark:text-zink-300">On Trip Now</p>
            </div>
        </div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4 flex items-center gap-3">
            <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-slate-100 dark:bg-zink-600 shrink-0">
                <i data-lucide="clock" class="w-5 h-5 text-slate-500 dark:text-zink-300"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-slate-800 dark:text-zink-50">{{ $activeDrivers->count() - $totalActive }}</p>
                <p class="text-xs text-slate-500 dark:text-zink-300">Available</p>
            </div>
        </div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4 flex items-center gap-3">
            <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-500/20 shrink-0">
                <i data-lucide="refresh-cw" class="w-5 h-5 text-purple-600 dark:text-purple-400"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-700 dark:text-zink-100">Last updated</p>
                <p class="text-xs text-slate-500 dark:text-zink-300">{{ now()->format('H:i:s') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Driver Cards Grid --}}
@if($activeDrivers->isEmpty())
    <div class="card border-0 shadow-sm">
        <div class="card-body flex flex-col items-center justify-center py-16 text-slate-400 dark:text-zink-400">
            <i data-lucide="map" class="w-14 h-14 mb-4 opacity-30"></i>
            <h6 class="text-base font-medium text-slate-600 dark:text-zink-200 mb-1">No active drivers</h6>
            <p class="text-sm">Drivers will appear here once they are approved and active.</p>
        </div>
    </div>
@else
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach($activeDrivers as $driver)
        @php $profile = $driver->driverProfile; $trip = $driver->currentTrip; $loc = $driver->latestLocation; @endphp
        <div class="card border-0 shadow-sm hover:shadow-md transition-shadow">
            <div class="card-body p-5">

                {{-- Driver Header --}}
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <div class="flex items-center justify-center w-11 h-11 rounded-full bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300 font-bold text-base shrink-0">
                                {{ strtoupper(substr($driver->name, 0, 1)) }}
                            </div>
                            @if($trip)
                                <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-emerald-500 border-2 border-white dark:border-zink-700"></span>
                            @else
                                <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-slate-300 dark:bg-zink-500 border-2 border-white dark:border-zink-700"></span>
                            @endif
                        </div>
                        <div>
                            <p class="font-semibold text-slate-800 dark:text-zink-50 text-sm">{{ $driver->name }}</p>
                            <p class="text-xs text-slate-400 dark:text-zink-400">{{ $driver->email }}</p>
                        </div>
                    </div>
                    @if($trip)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse inline-block"></span> On Trip
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-500 dark:bg-zink-600 dark:text-zink-300">
                            Available
                        </span>
                    @endif
                </div>

                {{-- Vehicle Info --}}
                @if($profile)
                <div class="flex items-center gap-2 mb-4 p-3 rounded-lg bg-slate-50 dark:bg-zink-700/50">
                    <i data-lucide="car" class="w-4 h-4 text-slate-400 dark:text-zink-400 shrink-0"></i>
                    <div class="text-xs text-slate-600 dark:text-zink-200">
                        <span class="font-medium">{{ $profile->vehicle_make }} {{ $profile->vehicle_model }}</span>
                        <span class="text-slate-400 dark:text-zink-400"> · {{ $profile->license_plate }}</span>
                        <span class="text-slate-400 dark:text-zink-400"> · {{ $profile->max_child_capacity }} seats</span>
                    </div>
                </div>
                @endif

                {{-- Current Trip --}}
                @if($trip)
                <div class="border border-emerald-200 dark:border-emerald-500/30 rounded-lg p-3 mb-3 bg-emerald-50/50 dark:bg-emerald-500/5">
                    <p class="text-xs font-semibold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider mb-2">Current Trip</p>
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2 text-xs text-slate-600 dark:text-zink-200">
                            <i data-lucide="user" class="w-3.5 h-3.5 text-slate-400 shrink-0"></i>
                            <span>{{ $trip->child ? $trip->child->first_name.' '.$trip->child->last_name : '—' }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-600 dark:text-zink-200">
                            <i data-lucide="school" class="w-3.5 h-3.5 text-slate-400 shrink-0"></i>
                            <span>{{ $trip->child?->school?->name ?? '—' }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-600 dark:text-zink-200">
                            <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400 shrink-0"></i>
                            <span>Started {{ $trip->updated_at?->diffForHumans() ?? '—' }}</span>
                        </div>
                        @if($loc && $loc->lat && $loc->lng)
                        <div class="flex items-center gap-2 text-xs text-slate-600 dark:text-zink-200">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-emerald-500 shrink-0"></i>
                            <span class="font-mono">{{ number_format($loc->lat, 5) }}, {{ number_format($loc->lng, 5) }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @else
                <div class="border border-dashed border-slate-200 dark:border-zink-600 rounded-lg p-3 mb-3 text-center">
                    <p class="text-xs text-slate-400 dark:text-zink-400">No active trip at the moment</p>
                </div>
                @endif

                {{-- Service Areas --}}
                @if($profile && ($profile->schools->count() > 0 || $profile->locations->count() > 0))
                <div class="mt-1">
                    <p class="text-xs font-medium text-slate-500 dark:text-zink-300 mb-1.5">Service Areas</p>
                    <div class="flex flex-wrap gap-1">
                        @foreach($profile->schools->take(3) as $school)
                            <span class="inline-flex px-2 py-0.5 rounded text-xs bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400 border border-blue-100 dark:border-blue-500/20">{{ $school->name }}</span>
                        @endforeach
                        @foreach($profile->locations->take(2) as $location)
                            <span class="inline-flex px-2 py-0.5 rounded text-xs bg-slate-100 text-slate-500 dark:bg-zink-600 dark:text-zink-300">{{ $location->name }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>
        </div>
        @endforeach
    </div>
@endif

{{-- Auto-refresh note --}}
<div class="mt-4 text-center text-xs text-slate-400 dark:text-zink-500">
    <i data-lucide="info" class="w-3 h-3 inline-block mr-1"></i>
    Page refreshes every 30 seconds. Last loaded at {{ now()->format('H:i:s') }}.
</div>

@push('scripts')
<script>
    // Auto-refresh every 30 seconds
    setTimeout(() => window.location.reload(), 30000);
</script>
@endpush

@endsection
