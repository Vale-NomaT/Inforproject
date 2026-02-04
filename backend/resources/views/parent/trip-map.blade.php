@extends('layouts.master')

@section('content')
<div class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)] min-h-[calc(100vh_-_theme('spacing.header')_*_3)] group-data-[layout=horizontal]:min-h-[calc(100vh_-_theme('spacing.header')_*_1.3)]">
    <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">
        <div class="flex flex-col gap-4 mb-5 md:flex-row md:items-center md:justify-between">
            <div>
                <h5 class="text-16">Live Trip Tracking</h5>
                <p class="text-slate-500 dark:text-zink-200">Tracking trip for {{ $child->first_name }} {{ $child->last_name }}</p>
            </div>
            <div>
                <a href="{{ route('parent.dashboard') }}" class="text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">Back to Dashboard</a>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)]">
            <div class="card">
                <div class="card-body">
                    @if ($child->pickupLocation && $child->school)
                        <p class="mb-4 text-slate-500 dark:text-zink-200">
                            Route: <span class="font-medium text-slate-800 dark:text-zink-50">{{ $child->pickupLocation->name }} &rarr; {{ $child->school->name }}</span>
                        </p>
                    @endif

                    @if ($child->school_start_time)
                        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg dark:bg-blue-500/10 dark:border-blue-500/20">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-500 dark:bg-blue-500/20">
                                    <i data-lucide="clock" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500 dark:text-zink-200 uppercase tracking-wide font-semibold">School Start Time</p>
                                    <h6 class="text-base font-bold text-slate-900 dark:text-zink-50">
                                        {{ \Carbon\Carbon::parse($child->school_start_time)->format('h:i A') }}
                                    </h6>
                                    <p class="text-xs text-slate-400 dark:text-zink-300">
                                        Estimated Pickup: {{ \Carbon\Carbon::parse($child->school_start_time)->subMinutes(45)->format('h:i A') }} - {{ \Carbon\Carbon::parse($child->school_start_time)->subMinutes(30)->format('h:i A') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div id="map" class="h-80 sm:h-96 rounded-xl w-full z-0"></div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    @if ($trip->status === 'completed' && !$trip->rating)
                        <div class="mb-4 p-4 bg-custom-50 border border-custom-200 rounded-lg dark:bg-custom-400/20 dark:border-custom-500/20">
                            <h6 class="text-custom-500 mb-2 font-semibold">Trip Completed</h6>
                            <p class="text-sm text-slate-500 dark:text-zink-200 mb-3">Please rate your experience with the driver.</p>
                            <a href="{{ route('parent.trips.rate.create', $trip) }}" class="text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20 w-full">
                                Rate This Trip
                            </a>
                        </div>
                    @elseif ($trip->rating)
                        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg dark:bg-green-400/20 dark:border-green-500/20">
                            <h6 class="text-green-500 mb-2 font-semibold">Trip Rated</h6>
                            <p class="text-sm text-slate-500 dark:text-zink-200">You rated this trip: <span class="font-bold">{{ $trip->rating->rating }}/5</span></p>
                        </div>
                    @endif

                    <h6 class="mb-4 text-15">Trip Timeline</h6>

                    @if ($trip->events->isEmpty())
                        <p class="text-slate-500 dark:text-zink-200">
                            No events logged yet for this trip. As your driver starts, arrives, picks up, and drops off, you will see each step here.
                        </p>
                    @else
                        <ol class="relative border-l border-slate-200 dark:border-zink-500 ml-3 space-y-6">
                            @foreach ($trip->events as $event)
                                <li class="ml-6">
                                    <span class="absolute flex items-center justify-center w-6 h-6 rounded-full -left-3 ring-4 ring-white dark:ring-zink-700
                                        @if ($event->type === 'started') bg-custom-500
                                        @elseif ($event->type === 'arrived') bg-yellow-500
                                        @elseif ($event->type === 'picked_up') bg-green-500
                                        @elseif ($event->type === 'dropped_off') bg-slate-500
                                        @else bg-slate-300
                                        @endif
                                    ">
                                    </span>
                                    <h6 class="mb-1 text-sm font-semibold text-slate-900 dark:text-zink-50">
                                        {{ ucfirst(str_replace('_', ' ', $event->type)) }}
                                    </h6>
                                    <time class="block mb-2 text-xs font-normal leading-none text-slate-400 dark:text-zink-200">
                                        {{ $event->created_at }}
                                    </time>
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const map = L.map('map');

    const tiles = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors',
    });

    tiles.addTo(map);

    let marker = null;
    let pathLine = null;

    @if ($child->pickupLocation && $child->pickupLocation->lat && $child->pickupLocation->lng)
    const initialLat = {{ (float) $child->pickupLocation->lat }};
    const initialLng = {{ (float) $child->pickupLocation->lng }};
    map.setView([initialLat, initialLng], 13);
    marker = L.marker([initialLat, initialLng]).addTo(map);
    @else
    map.setView([0, 0], 2);
    @endif

    const pathPoints = [];

    @foreach ($trip->events as $event)
        @if ($event->lat !== null && $event->lng !== null)
            pathPoints.push([{{ (float) $event->lat }}, {{ (float) $event->lng }}]);
        @endif
    @endforeach

    if (pathPoints.length > 0) {
        pathLine = L.polyline(pathPoints, {color: '#2563eb', weight: 4}).addTo(map);
        const lastPoint = pathPoints[pathPoints.length - 1];
        if (!marker) {
            marker = L.marker(lastPoint).addTo(map);
        } else {
            marker.setLatLng(lastPoint);
        }
        map.fitBounds(pathLine.getBounds(), {padding: [24, 24]});
    }

    if (window.Echo) {
        window.Echo.private('trips.{{ $trip->id }}')
            .listen('.trip.location.updated', function (e) {
                const lat = parseFloat(e.lat);
                const lng = parseFloat(e.lng);

                if (Number.isNaN(lat) || Number.isNaN(lng)) {
                    return;
                }

                if (!marker) {
                    marker = L.marker([lat, lng]).addTo(map);
                } else {
                    marker.setLatLng([lat, lng]);
                }

                if (pathLine) {
                    const existing = pathLine.getLatLngs();
                    existing.push([lat, lng]);
                    pathLine.setLatLngs(existing);
                } else {
                    pathLine = L.polyline([[lat, lng]], {color: '#2563eb', weight: 4}).addTo(map);
                }

                map.setView([lat, lng], map.getZoom());
            });

        window.Echo.private('parents.{{ auth()->id() }}')
            .listen('.trip.event', function (e) {
                if (e.trip_id != {{ $trip->id }}) return;

                // Update Timeline
                const list = document.querySelector('ol');
                if (!list) {
                     // If list doesn't exist (empty state), reload page to init structure or create it dynamically.
                     // For simplicity, we reload if it was empty
                     window.location.reload();
                     return;
                }

                const item = document.createElement('li');
                item.className = 'ml-6';
                
                let colorClass = 'bg-slate-300';
                if (e.type === 'started') colorClass = 'bg-custom-500';
                else if (e.type === 'arrived') colorClass = 'bg-yellow-500';
                else if (e.type === 'picked_up') colorClass = 'bg-green-500';
                else if (e.type === 'dropped_off') colorClass = 'bg-slate-500';

                const formattedType = e.type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());

                item.innerHTML = `
                    <span class="absolute flex items-center justify-center w-6 h-6 rounded-full -left-3 ring-4 ring-white dark:ring-zink-700 ${colorClass}">
                    </span>
                    <h6 class="mb-1 text-sm font-semibold text-slate-900 dark:text-zink-50">
                        ${formattedType}
                    </h6>
                    <time class="block mb-2 text-xs font-normal leading-none text-slate-400 dark:text-zink-200">
                        ${new Date(e.created_at).toLocaleString()}
                    </time>
                `;
                list.appendChild(item);
                
                // If dropped_off, maybe show rate button?
                if (e.type === 'dropped_off') {
                    // Refresh to show rate button if backend supports it
                    setTimeout(() => window.location.reload(), 2000);
                }
            });
    }
</script>
@endsection
