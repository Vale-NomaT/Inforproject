@extends('layouts.master')

@section('content')
<div class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)] min-h-[calc(100vh_-_theme('spacing.header')_*_3)] group-data-[layout=horizontal]:min-h-[calc(100vh_-_theme('spacing.header')_*_1.3)]">
    <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">
        
        <div class="flex flex-col gap-4 mb-6 md:flex-row md:items-center md:justify-between">
            <div>
                <h5 class="text-xl font-bold text-slate-900 dark:text-zink-50">Available Drivers</h5>
                <p class="text-slate-500 dark:text-zink-200 mt-1">Select a trusted driver for <span class="font-semibold text-slate-800 dark:text-zink-100">{{ $child->first_name }}</span></p>
            </div>
            <div>
                <a href="{{ route('parent.dashboard') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium transition-all duration-200 border rounded-lg text-slate-600 bg-white border-slate-300 hover:bg-slate-50 hover:text-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-200 dark:bg-zink-700 dark:border-zink-500 dark:text-zink-200 dark:hover:bg-zink-600">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Dashboard
                </a>
            </div>
        </div>

        @if (session('status'))
            <div class="p-4 mb-6 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-500/20 dark:text-green-400" role="alert">
                <div class="flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                    <span class="font-medium">{{ session('status') }}</span>
                </div>
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-12">
            <!-- Route Info Card -->
            <div class="lg:col-span-12">
                <div class="card bg-slate-50 border-slate-200 dark:bg-zink-700/50 dark:border-zink-500">
                    <div class="card-body p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-white shadow-sm text-custom-500 dark:bg-zink-600 dark:text-custom-400">
                                <i data-lucide="map-pin" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-zink-300">Route Details</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="font-semibold text-slate-800 dark:text-zink-50">{{ $child->pickupLocation->name ?? 'Unknown Location' }}</span>
                                    <i data-lucide="arrow-right" class="w-4 h-4 text-slate-400"></i>
                                    <span class="font-semibold text-slate-800 dark:text-zink-50">{{ $child->school->name ?? 'Unknown School' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-6 text-sm">
                            <div class="text-center sm:text-right">
                                <span class="block text-xs text-slate-500 dark:text-zink-300">Drop-off by</span>
                                <span class="font-medium text-slate-800 dark:text-zink-50">{{ \Carbon\Carbon::parse($child->school_start_time)->format('g:i A') }}</span>
                            </div>
                            <div class="w-px h-8 bg-slate-200 dark:bg-zink-500 hidden sm:block"></div>
                            <div class="text-center sm:text-left">
                                <span class="block text-xs text-slate-500 dark:text-zink-300">Pick-up at</span>
                                <span class="font-medium text-slate-800 dark:text-zink-50">{{ \Carbon\Carbon::parse($child->school_end_time)->format('g:i A') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($drivers->isEmpty())
                <div class="lg:col-span-12">
                    <div class="flex flex-col items-center justify-center py-16 text-center bg-white border border-dashed rounded-2xl border-slate-300 dark:bg-zink-700 dark:border-zink-500">
                        <div class="flex items-center justify-center w-20 h-20 mb-6 rounded-full bg-slate-50 dark:bg-zink-600">
                            <i data-lucide="car" class="w-10 h-10 text-slate-400 dark:text-zink-300"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-zink-50">No drivers available yet</h3>
                        <p class="max-w-md mt-2 text-slate-500 dark:text-zink-200">We couldn't find any drivers matching your route and time requirements. We're constantly onboarding new drivers.</p>
                        <button type="button" class="mt-6 btn bg-custom-500 border-custom-500 hover:bg-custom-600 hover:border-custom-600 text-white">
                            Notify me when drivers join
                        </button>
                    </div>
                </div>
            @else
                @foreach ($drivers as $entry)
                    @php
                        $driver = $entry['driver'];
                        $names = explode(' ', $driver->user->name);
                        $initials = substr($names[0] ?? '', 0, 1) . substr($names[1] ?? '', 0, 1);
                    @endphp
                    <div class="lg:col-span-4 md:col-span-6 col-span-12">
                        <div class="card h-full hover:shadow-lg transition-shadow duration-300 border border-slate-200 dark:border-zink-500">
                            <div class="card-body p-6 flex flex-col h-full">
                                <div class="flex items-start justify-between mb-6">
                                    <div class="flex items-center gap-4">
                                        <div class="relative">
                                            <div class="flex items-center justify-center w-14 h-14 rounded-full bg-custom-100 text-custom-600 text-xl font-bold dark:bg-custom-500/20 dark:text-custom-400">
                                                {{ $initials }}
                                            </div>
                                            <div class="absolute -bottom-1 -right-1 flex items-center justify-center w-6 h-6 bg-green-500 border-2 border-white rounded-full dark:border-zink-700" title="Verified Driver">
                                                <i data-lucide="check" class="w-3 h-3 text-white"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="text-lg font-bold text-slate-900 dark:text-zink-50">
                                                {{ $driver->user->name }}
                                            </h6>
                                            <div class="flex items-center gap-1 mt-0.5">
                                                <i data-lucide="star" class="w-3.5 h-3.5 text-amber-400 fill-amber-400"></i>
                                                <span class="text-sm font-medium text-slate-700 dark:text-zink-100">{{ $entry['performance_score'] ?? 'New' }}</span>
                                                <span class="text-xs text-slate-500 dark:text-zink-300">(120 trips)</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end">
                                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-zink-300">Tier {{ $entry['tier'] }}</span>
                                        <span class="text-lg font-bold text-slate-900 dark:text-zink-50">${{ $entry['price'] }}</span>
                                        <span class="text-xs text-slate-500 dark:text-zink-300">/month</span>
                                    </div>
                                </div>

                                <div class="space-y-4 mb-6 flex-grow">
                                    <div class="flex items-start gap-3 p-3 rounded-lg bg-slate-50 dark:bg-zink-600/50">
                                        <i data-lucide="car-front" class="w-5 h-5 text-slate-500 dark:text-zink-300 mt-0.5"></i>
                                        <div>
                                            <p class="text-sm font-medium text-slate-900 dark:text-zink-50">
                                                {{ $driver->vehicle_make }} {{ $driver->vehicle_model }}
                                            </p>
                                            <p class="text-xs text-slate-500 dark:text-zink-300 capitalize">
                                                {{ $driver->vehicle_color }} • {{ $driver->license_plate }}
                                            </p>
                                            <p class="text-xs text-slate-500 dark:text-zink-300 mt-1">
                                                Capacity: {{ $driver->max_child_capacity }} • <span class="text-green-600 dark:text-green-400 font-medium">{{ $entry['free_seats'] }} seats left</span>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap gap-2">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-md bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400">
                                            <i data-lucide="shield-check" class="w-3 h-3"></i> Background Checked
                                        </span>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-md bg-purple-50 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400">
                                            <i data-lucide="award" class="w-3 h-3"></i> Top Rated
                                        </span>
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('parent.children.drivers.store', ['child' => $child->id]) }}" class="mt-auto">
                                    @csrf
                                    <input type="hidden" name="driver_id" value="{{ $driver->id }}">
                                    <button type="submit" class="w-full flex items-center justify-center gap-2 btn bg-custom-500 border-custom-500 hover:bg-custom-600 hover:border-custom-600 text-white shadow-md shadow-custom-500/20 py-2.5 font-medium transition-all hover:-translate-y-0.5">
                                        <span>Request Booking</span>
                                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection
