@extends('layouts.master')

@section('content')
        <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center md:justify-between">
            <div class="grow">
                <h5 class="text-16">Parent Dashboard</h5>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('parent.children.create') }}" class="text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">
                    <i class="align-bottom ri-add-line me-1"></i> Add Child
                </a>
                <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                    <li class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                        <a href="#!" class="text-slate-400 dark:text-zink-200">Dashboards</a>
                    </li>
                    <li class="text-slate-700 dark:text-zink-100">
                        Parent
                    </li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @if ($children->isEmpty())
                    <div class="rounded-2xl border border-dashed border-slate-200 bg-white px-6 py-10 text-center dark:bg-zink-700 dark:border-zink-500">
                        <h2 class="text-lg sm:text-xl font-semibold text-slate-900 dark:text-zink-50">
                            No children registered yet
                        </h2>
                        <p class="mt-2 text-sm sm:text-base text-slate-600 dark:text-zink-200">
                            Once your children are added to SafeRide Kids, you will be able to request trusted drivers for their school routes.
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('parent.children.create') }}" class="text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">
                                <i class="align-bottom ri-add-line me-1"></i> Add Your First Child
                            </a>
                        </div>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($children as $child)
                            @php
                                $latestBooking = $child->bookingRequests->first();
                                $statusLabel = null;
                                $statusStyle = 'bg-slate-100 text-slate-700 dark:bg-zink-600 dark:text-zink-200';

                                if ($latestBooking) {
                                    if ($latestBooking->status === \App\Models\BookingRequest::STATUS_PENDING) {
                                        $statusLabel = 'Pending';
                                        $statusStyle = 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-500';
                                    } elseif ($latestBooking->status === \App\Models\BookingRequest::STATUS_APPROVED) {
                                        $statusLabel = 'Confirmed';
                                        $statusStyle = 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-500';
                                    } elseif ($latestBooking->status === \App\Models\BookingRequest::STATUS_DECLINED) {
                                        $statusLabel = 'Declined';
                                        $statusStyle = 'bg-rose-50 text-rose-700 dark:bg-red-500/10 dark:text-red-500';
                                    }
                                }
                            @endphp
                            @php
                                $latestCompletedTrip = $child->trips->where('status', \App\Models\Trip::STATUS_COMPLETED)->sortByDesc('scheduled_date')->first();
                                $activeTrip = $child->trips->where('status', \App\Models\Trip::STATUS_IN_PROGRESS)->first();
                                $hasRating = null;
                                if ($latestCompletedTrip) {
                                    $hasRating = \App\Models\Rating::where('trip_id', $latestCompletedTrip->id)
                                        ->where('parent_id', auth()->id())
                                        ->exists();
                                }
                            @endphp
                            <div class="flex flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-all duration-300 hover:shadow-md dark:bg-zink-700 dark:border-zink-500">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                                    <div class="flex items-center gap-4">
                                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-500 dark:bg-zink-600 dark:text-zink-200">
                                            <i data-lucide="user" class="h-7 w-7"></i>
                                        </div>
                                        <div>
                                            <h2 class="text-xl font-bold text-slate-900 dark:text-zink-50">
                                                {{ $child->first_name }} {{ $child->last_name }}
                                            </h2>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="inline-flex items-center justify-center h-5 px-2.5 text-xs font-semibold text-slate-500 bg-slate-100 rounded-md dark:bg-zink-600 dark:text-zink-200">
                                                    {{ \Carbon\Carbon::parse($child->date_of_birth)->age }} years old
                                                </span>
                                                @if($child->grade)
                                                    <span class="inline-flex items-center justify-center h-5 px-2.5 text-xs font-semibold text-blue-500 bg-blue-100 rounded-md dark:bg-blue-500/20 dark:text-blue-200">
                                                        {{ $child->grade }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="self-start sm:self-center">
                                        @if ($statusLabel)
                                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wide border {{ $statusStyle }}">
                                                {{ $statusLabel }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wide bg-slate-100 text-slate-500 border border-slate-200 dark:bg-zink-600 dark:text-zink-300 dark:border-zink-500">
                                                No Booking Yet
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 p-5 bg-slate-50 rounded-xl border border-slate-100 dark:bg-zink-600/30 dark:border-zink-500/50">
                                    @if ($child->school)
                                        <div class="flex flex-col gap-1.5">
                                            <div class="flex items-center gap-1.5 text-slate-500 dark:text-zink-300">
                                                <i data-lucide="graduation-cap" class="w-3.5 h-3.5"></i>
                                                <span class="text-xs font-semibold uppercase tracking-wider">School</span>
                                            </div>
                                            <span class="text-sm font-semibold text-slate-900 dark:text-zink-50 truncate" title="{{ $child->school->name }}">
                                                {{ $child->school->name }}
                                            </span>
                                        </div>
                                    @endif
                                    
                                    @if ($child->pickupLocation)
                                        <div class="flex flex-col gap-1.5">
                                            <div class="flex items-center gap-1.5 text-slate-500 dark:text-zink-300">
                                                <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                                                <span class="text-xs font-semibold uppercase tracking-wider">Pickup Zone</span>
                                            </div>
                                            <span class="text-sm font-semibold text-slate-900 dark:text-zink-50 truncate" title="{{ $child->pickupLocation->name }}">
                                                {{ $child->pickupLocation->name }}
                                            </span>
                                        </div>
                                    @endif

                                    @if($child->school_start_time)
                                        <div class="flex flex-col gap-1.5">
                                            <div class="flex items-center gap-1.5 text-slate-500 dark:text-zink-300">
                                                <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                                <span class="text-xs font-semibold uppercase tracking-wider">Start Time</span>
                                            </div>
                                            <span class="text-sm font-semibold text-slate-900 dark:text-zink-50">
                                                {{ \Carbon\Carbon::parse($child->school_start_time)->format('g:i A') }}
                                            </span>
                                        </div>
                                    @endif

                                    @if($child->school_end_time)
                                        <div class="flex flex-col gap-1.5">
                                            <div class="flex items-center gap-1.5 text-slate-500 dark:text-zink-300">
                                                <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                                <span class="text-xs font-semibold uppercase tracking-wider">End Time</span>
                                            </div>
                                            <span class="text-sm font-semibold text-slate-900 dark:text-zink-50">
                                                {{ \Carbon\Carbon::parse($child->school_end_time)->format('g:i A') }}
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-6 flex flex-wrap items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-zink-600">
                                    @if ($activeTrip)
                                        <a
                                            href="{{ route('parent.trips.show', ['trip' => $activeTrip->id]) }}"
                                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-green-500 border border-transparent rounded-lg shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 animate-pulse"
                                        >
                                            <i data-lucide="map-pin" class="w-4 h-4 mr-2"></i> Track Live Trip
                                        </a>
                                    @endif

                                    <a
                                        href="{{ route('parent.children.edit', ['child' => $child->id]) }}"
                                        class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-custom-500 dark:bg-zink-700 dark:text-zink-100 dark:border-zink-500 dark:hover:bg-zink-600"
                                    >
                                        Edit Details
                                    </a>

                                    <a
                                        href="{{ route('parent.children.trips.index', ['child' => $child->id]) }}"
                                        class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-custom-500 dark:bg-zink-700 dark:text-zink-100 dark:border-zink-500 dark:hover:bg-zink-600"
                                    >
                                        Trip History
                                    </a>

                                    @if ($latestCompletedTrip && ! $hasRating)
                                        <a
                                            href="{{ route('parent.trips.rate.create', ['trip' => $latestCompletedTrip->id]) }}"
                                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg shadow-sm hover:bg-emerald-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 dark:bg-emerald-500/10 dark:text-emerald-500 dark:border-emerald-500/50"
                                        >
                                            Rate Trip
                                        </a>
                                    @endif

                                    <a
                                        href="{{ route('parent.children.drivers.show', ['child' => $child->id]) }}"
                                        class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-custom-500 border border-transparent rounded-lg shadow-sm hover:bg-custom-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-custom-500"
                                    >
                                        @if ($statusLabel)
                                            Update Driver
                                        @else
                                            Select Driver
                                        @endif
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
@endsection
