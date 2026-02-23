@extends('layouts.master')

@section('content')
<div class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)] min-h-[calc(100vh_-_theme('spacing.header')_*_3)] group-data-[layout=horizontal]:min-h-[calc(100vh_-_theme('spacing.header')_*_1.3)]">
    <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">
        <div class="flex flex-col gap-4 mb-5 md:flex-row md:items-center md:justify-between">
            <div>
                <h5 class="text-16">Scheduled Trips</h5>
                <p class="text-slate-500 dark:text-zink-200">Your upcoming school runs. Start your route when you are ready.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('driver.dashboard') }}" class="text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">Back to Dashboard</a>
            </div>
        </div>

        @if (session('status'))
            <div class="px-4 py-3 mb-5 text-sm text-green-500 border border-green-200 rounded-md bg-green-50 dark:bg-green-400/20 dark:border-green-500/50">
                {{ session('status') }}
            </div>
        @endif

        <div id="location-status" class="hidden px-4 py-3 mb-5 text-sm text-blue-500 border border-blue-200 rounded-md bg-blue-50 dark:bg-blue-400/20 dark:border-blue-500/50">
            Updating location...
        </div>

        @if (!$trips->isEmpty())
            <div class="card mb-5">
                <div class="card-body">
                    <h6 class="mb-3 text-sm font-semibold text-slate-900 dark:text-zink-50">Today's Route Map</h6>
                    <div id="driver-route-map" class="h-[420px] md:h-[520px] rounded-xl w-full z-0"></div>
                </div>
            </div>
        @endif

        @if ($trips->isEmpty())
            <div class="card">
                <div class="card-body text-center py-10">
                    <h5 class="text-16 mb-2">No scheduled trips</h5>
                    <p class="text-slate-500 dark:text-zink-200">Approved bookings will automatically create trips for upcoming school days.</p>
                </div>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($trips as $trip)
                    <div class="card" data-trip-id="{{ $trip->id }}" data-status="{{ $trip->status }}">
                        <div class="card-body">
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <h6 class="text-15 font-semibold text-slate-900 dark:text-zink-50">
                                            {{ $trip->child ? $trip->child->first_name . ' ' . $trip->child->last_name : 'Child' }}
                                        </h6>
                                        @php
                                            $isMorning = $trip->type === 'morning';
                                            $typeLabel = $isMorning ? 'Morning Run' : 'Afternoon Run';
                                            $typeClass = $isMorning 
                                                ? 'bg-orange-100 text-orange-500 border-orange-200 dark:bg-orange-500/20 dark:border-orange-500/20' 
                                                : 'bg-purple-100 text-purple-500 border-purple-200 dark:bg-purple-500/20 dark:border-purple-500/20';
                                        @endphp
                                        <span class="px-2 py-0.5 text-xs font-medium rounded border {{ $typeClass }}">
                                            {{ $typeLabel }}
                                        </span>
                                    </div>
                                    <p class="text-slate-500 dark:text-zink-200 mb-1">
                                        Date: <span class="font-medium text-slate-800 dark:text-zink-50">
                                            {{ $trip->scheduled_date->format('l, M d, Y') }}
                                            @if($trip->child && $trip->child->school_start_time && $isMorning)
                                                at {{ \Carbon\Carbon::parse($trip->child->school_start_time)->format('H:i') }}
                                            @elseif($trip->child && $trip->child->school_end_time && !$isMorning)
                                                at {{ \Carbon\Carbon::parse($trip->child->school_end_time)->format('H:i') }}
                                            @endif
                                        </span>
                                    </p>
                                    @if ($trip->child && $trip->child->pickupLocation && $trip->child->school)
                                        @php
                                            $pickupName = $trip->child->pickup_address ?? $trip->child->pickupLocation->name;
                                        @endphp
                                        <p class="text-slate-500 dark:text-zink-200">
                                            Route: 
                                            <span class="font-medium text-slate-800 dark:text-zink-50">
                                                @if($isMorning)
                                                    {{ $pickupName }} &rarr; {{ $trip->child->school->name }}
                                                @else
                                                    {{ $trip->child->school->name }} &rarr; {{ $pickupName }}
                                                @endif
                                            </span>
                                        </p>
                                    @endif
                                </div>
                                <div class="flex flex-col items-end gap-2">
                                    @if ($trip->status === \App\Models\Trip::STATUS_SCHEDULED)
                                        <span class="px-2.5 py-0.5 text-xs inline-block font-medium rounded border border-yellow-200 bg-yellow-100 text-yellow-500 dark:bg-yellow-500/20 dark:border-yellow-500/20">
                                            Scheduled
                                        </span>
                                    @elseif ($trip->status === \App\Models\Trip::STATUS_IN_PROGRESS)
                                        <span class="px-2.5 py-0.5 text-xs inline-block font-medium rounded border border-green-200 bg-green-100 text-green-500 dark:bg-green-500/20 dark:border-green-500/20">
                                            In Progress
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 text-xs inline-block font-medium rounded border border-slate-200 bg-slate-100 text-slate-500 dark:bg-slate-500/20 dark:border-slate-500/20 dark:text-zink-200">
                                            Completed
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if ($trip->status === \App\Models\Trip::STATUS_SCHEDULED)
                                <div class="mt-4 flex justify-end">
                                    <form method="POST" action="{{ route('driver.trips.start', ['trip' => $trip->id]) }}">
                                        @csrf
                                        <button type="submit" class="text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">
                                            Start Trip
                                        </button>
                                    </form>
                                </div>
                            @elseif ($trip->status === \App\Models\Trip::STATUS_IN_PROGRESS)
                                <div class="mt-4 flex flex-wrap gap-2 justify-end">
                                    <button type="button" onclick="logEvent('{{ $trip->id }}', 'arrived')" class="text-white btn bg-yellow-500 border-yellow-500 hover:text-white hover:bg-yellow-600 hover:border-yellow-600 focus:text-white focus:bg-yellow-600 focus:border-yellow-600 focus:ring focus:ring-yellow-100 active:text-white active:bg-yellow-600 active:border-yellow-600 active:ring active:ring-yellow-100 dark:ring-yellow-400/20 text-xs">
                                        Arrived at Pickup
                                    </button>
                                    <button type="button" onclick="logEvent('{{ $trip->id }}', 'picked_up')" class="text-white btn bg-purple-500 border-purple-500 hover:text-white hover:bg-purple-600 hover:border-purple-600 focus:text-white focus:bg-purple-600 focus:border-purple-600 focus:ring focus:ring-purple-100 active:text-white active:bg-purple-600 active:border-purple-600 active:ring active:ring-purple-100 dark:ring-purple-400/20 text-xs">
                                        Picked Up
                                    </button>
                                    <button type="button" onclick="logEvent('{{ $trip->id }}', 'arrived_dropoff')" class="text-white btn bg-orange-500 border-orange-500 hover:text-white hover:bg-orange-600 hover:border-orange-600 focus:text-white focus:bg-orange-600 focus:border-orange-600 focus:ring focus:ring-orange-100 active:text-white active:bg-orange-600 active:border-orange-600 active:ring active:ring-orange-100 dark:ring-orange-400/20 text-xs">
                                        Arrived at Drop-off
                                    </button>
                                    <button type="button" onclick="logEvent('{{ $trip->id }}', 'dropped_off')" class="text-white btn bg-green-500 border-green-500 hover:text-white hover:bg-green-600 hover:border-green-600 focus:text-white focus:bg-green-600 focus:border-green-600 focus:ring focus:ring-green-100 active:text-white active:bg-green-600 active:border-green-600 active:ring active:ring-green-100 dark:ring-green-400/20 text-xs">
                                        Dropped Off
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@endsection

@section('script')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
<script>
    @if (!$trips->isEmpty())
        const map = L.map('driver-route-map').setView([0, 0], 2);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const bounds = L.latLngBounds();
        const pickupPoints = [];
        const schoolPoints = [];

        const pickupIcon = L.divIcon({
            className: 'custom-div-icon',
            html: "<div style='background-color: #22c55e; width: 12px; height: 12px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 4px rgba(0,0,0,0.3);'></div>",
            iconSize: [12, 12],
            iconAnchor: [6, 6]
        });

        const dropoffIcon = L.divIcon({
            className: 'custom-div-icon',
            html: "<div style='background-color: #ef4444; width: 12px; height: 12px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 4px rgba(0,0,0,0.3);'></div>",
            iconSize: [12, 12],
            iconAnchor: [6, 6]
        });

        @foreach ($trips as $trip)
            @php
                $child = $trip->child;
                $homeLat = $child->pickup_lat ?? ($child->pickupLocation ? $child->pickupLocation->lat : null);
                $homeLng = $child->pickup_lng ?? ($child->pickupLocation ? $child->pickupLocation->lng : null);
                $homeName = $child->pickup_address ?? ($child->pickupLocation ? $child->pickupLocation->name : 'Home');
                
                $schoolLat = $child->school->lat ?? null;
                $schoolLng = $child->school->lng ?? null;
                $schoolName = $child->school->name ?? null;

                $isMorning = $trip->type === 'morning';

                if ($isMorning) {
                    $startLat = $homeLat;
                    $startLng = $homeLng;
                    $startName = $homeName;
                    $startDesc = "Pickup: " . ($child->first_name ?? 'Child');
                    
                    $endLat = $schoolLat;
                    $endLng = $schoolLng;
                    $endName = $schoolName;
                    $endDesc = "Drop-off: " . ($child->school->name ?? 'School');
                } else {
                    $startLat = $schoolLat;
                    $startLng = $schoolLng;
                    $startName = $schoolName;
                    $startDesc = "Pickup: " . ($child->first_name ?? 'Child') . " (School)";

                    $endLat = $homeLat;
                    $endLng = $homeLng;
                    $endName = $homeName;
                    $endDesc = "Drop-off: " . ($child->first_name ?? 'Child') . " (Home)";
                }
            @endphp

            @if ($startLat && $startLng)
                {
                    const lat = {{ $startLat }};
                    const lng = {{ $startLng }};
                    const marker = L.marker([lat, lng], {icon: pickupIcon}).addTo(map);
                    marker.bindPopup(`
                        <strong>{{ $startDesc }}</strong><br>
                        {{ $startName }}<br>
                        <a href='https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}' target='_blank'>Get Directions</a>
                    `);
                    bounds.extend([lat, lng]);
                    
                    const exists = pickupPoints.some(p => p.lat === lat && p.lng === lng);
                    if (!exists) {
                        pickupPoints.push(L.latLng(lat, lng));
                    }
                }
            @endif

            @if ($endLat && $endLng)
                {
                    const lat = {{ $endLat }};
                    const lng = {{ $endLng }};
                    const marker = L.marker([lat, lng], {icon: dropoffIcon}).addTo(map);
                    marker.bindPopup(`
                        <strong>{{ $endDesc }}</strong><br>
                        {{ $endName }}
                    `);
                    bounds.extend([lat, lng]);
                    
                    const exists = schoolPoints.some(p => p.lat === lat && p.lng === lng);
                    if (!exists) {
                        schoolPoints.push(L.latLng(lat, lng));
                    }
                }
            @endif
        @endforeach

        if (bounds.isValid()) {
            map.fitBounds(bounds, {padding: [50, 50]});
        } else {
            map.setView([-26.2041, 28.0473], 12);
        }

        let routingControl = null;

        function initRouting(startPoint) {
            const waypoints = [];
            
            if (startPoint) {
                waypoints.push(startPoint);
            }

            pickupPoints.forEach(p => waypoints.push(p));
            schoolPoints.forEach(p => waypoints.push(p));

            if (waypoints.length < 2) return;

            if (routingControl) {
                map.removeControl(routingControl);
            }

            routingControl = L.Routing.control({
                waypoints: waypoints,
                router: L.Routing.osrmv1({
                    serviceUrl: 'https://router.project-osrm.org/route/v1'
                }),
                lineOptions: {
                    styles: [{color: '#6366f1', opacity: 0.8, weight: 6}]
                },
                show: false,
                addWaypoints: false,
                draggableWaypoints: false,
                fitSelectedRoutes: false
            }).addTo(map);
        }

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(position => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                const driverIcon = L.divIcon({
                    className: 'custom-div-icon',
                    html: "<div style='background-color: #f59e0b; width: 16px; height: 16px; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 6px rgba(0,0,0,0.4);'></div>",
                    iconSize: [16, 16],
                    iconAnchor: [8, 8]
                });

                L.marker([lat, lng], {icon: driverIcon}).addTo(map)
                    .bindPopup("You are here").openPopup();
                
                bounds.extend([lat, lng]);
                map.fitBounds(bounds, {padding: [50, 50]});

                initRouting(L.latLng(lat, lng));
            }, (error) => {
                console.error("Geolocation error:", error);
                initRouting(null);
            });
        } else {
            initRouting(null);
        }
    @endif

    function logEvent(tripId, type) {
        if (!navigator.geolocation) {
            alert('Geolocation is not supported by your browser');
            return;
        }

        navigator.geolocation.getCurrentPosition(position => {
            const { latitude, longitude } = position.coords;

            fetch(`/driver/trips/${tripId}/events`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    type: type,
                    lat: latitude,
                    lng: longitude
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'ok') {
                    window.location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
        }, error => {
            alert('Unable to retrieve your location');
            console.error(error);
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const activeTrips = document.querySelectorAll('[data-status="in_progress"]');
        
        if (activeTrips.length > 0) {
            const statusEl = document.getElementById('location-status');
            if (statusEl) statusEl.classList.remove('hidden');

            setInterval(() => {
                activeTrips.forEach(card => {
                    const tripId = card.dataset.tripId;
                    
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(position => {
                            const { latitude, longitude } = position.coords;
                            
                            fetch(`/driver/trips/${tripId}/location`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    lat: latitude,
                                    lng: longitude
                                })
                            }).catch(console.error);
                        });
                    }
                });
            }, 15000);
        }
    });
</script>
@endsection
