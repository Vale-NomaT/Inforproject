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
    let geoPermissionDenied = false;
    let driverMarker = null;
    let routingControl = null;
    let map = null;

    // Prepare trip data from PHP
    const trips = @json($trips);
    const csrfToken = '{{ csrf_token() }}';
    const updateLocationUrl = '{{ route("driver.location.update") }}';

    function initMap() {
        if (trips.length === 0) return;

        const mapContainer = document.getElementById('driver-route-map');
        if (!mapContainer) return;

        map = L.map('driver-route-map').setView([-20.1367, 28.6363], 13); // Default to Bulawayo

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Icons
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

        const waypoints = [];
        const bounds = L.latLngBounds();

        // Process trips to build waypoints
        // Logic: Pickups first, then Dropoffs (for Morning). 
        
        const pickups = [];
        const dropoffs = [];

        trips.forEach(trip => {
            const child = trip.child;
            if (!child) return;

            const isMorning = trip.type === 'morning';
            
            let pickupLat, pickupLng, pickupName, dropoffLat, dropoffLng, dropoffName;

            if (isMorning) {
                // Morning: Home -> School
                pickupLat = child.pickup_lat || (child.pickup_location ? child.pickup_location.lat : null);
                pickupLng = child.pickup_lng || (child.pickup_location ? child.pickup_location.lng : null);
                pickupName = child.pickup_address || (child.pickup_location ? child.pickup_location.name : 'Home');
                
                dropoffLat = child.school ? child.school.lat : null;
                dropoffLng = child.school ? child.school.lng : null;
                dropoffName = child.school ? child.school.name : 'School';
            } else {
                // Afternoon: School -> Home
                pickupLat = child.school ? child.school.lat : null;
                pickupLng = child.school ? child.school.lng : null;
                pickupName = child.school ? child.school.name : 'School';

                dropoffLat = child.pickup_lat || (child.pickup_location ? child.pickup_location.lat : null);
                dropoffLng = child.pickup_lng || (child.pickup_location ? child.pickup_location.lng : null);
                dropoffName = child.pickup_address || (child.pickup_location ? child.pickup_location.name : 'Home');
            }

            // Add Markers
            if (pickupLat && pickupLng) {
                L.marker([pickupLat, pickupLng], {icon: pickupIcon})
                    .bindPopup(`<b>Pickup: ${child.first_name}</b><br>${pickupName}`)
                    .addTo(map);
                bounds.extend([pickupLat, pickupLng]);
                
                // Store with metadata
                // We allow duplicates in list if we want to show multiple stops at same location (e.g. siblings), 
                // but for routing efficiency OSRM might merge them.
                // For now, we add all.
                pickups.push({
                    latLng: L.latLng(pickupLat, pickupLng),
                    tripId: trip.id,
                    type: 'pickup',
                    name: pickupName,
                    childName: child.first_name
                });
            }

            if (dropoffLat && dropoffLng) {
                L.marker([dropoffLat, dropoffLng], {icon: dropoffIcon})
                    .bindPopup(`<b>Dropoff: ${child.first_name}</b><br>${dropoffName}`)
                    .addTo(map);
                bounds.extend([dropoffLat, dropoffLng]);

                dropoffs.push({
                    latLng: L.latLng(dropoffLat, dropoffLng),
                    tripId: trip.id,
                    type: 'dropoff',
                    name: dropoffName,
                    childName: child.first_name
                });
            }
        });

        if (bounds.isValid()) {
            map.fitBounds(bounds, {padding: [50, 50]});
        }

        // Initialize Routing with placeholders
        window.routePoints = { pickups, dropoffs };
    }

    function updateRouting(driverLatLng) {
        if (!map) return;
        
        const points = [];
        if (driverLatLng) points.push(driverLatLng);
        
        // Add Pickups then Dropoffs
        // We need to keep track of which point corresponds to which trip to update UI
        const orderedStops = [];

        window.routePoints.pickups.forEach(p => {
            points.push(p.latLng);
            orderedStops.push(p);
        });
        window.routePoints.dropoffs.forEach(p => {
            points.push(p.latLng);
            orderedStops.push(p);
        });

        if (points.length < 2) return;

        if (routingControl) {
            map.removeControl(routingControl);
        }

        routingControl = L.Routing.control({
            waypoints: points,
            router: L.Routing.osrmv1({
                serviceUrl: 'https://router.project-osrm.org/route/v1',
                routingOptions: {
                    alternatives: true
                }
            }),
            lineOptions: {
                styles: [{color: '#6366f1', opacity: 0.8, weight: 6}]
            },
            showAlternatives: true,
            show: false, // Hide default itinerary text
            addWaypoints: false,
            draggableWaypoints: false,
            fitSelectedRoutes: false,
            createMarker: function() { return null; } 
        })
        .on('routesfound', function(e) {
            const routes = e.routes;
            if (!routes || routes.length === 0) return;

            const route = routes[0];
            const legs = route.legs; // Leg 0 is Driver -> 1st Stop

            // Update UI with ETA
            // orderedStops[i] corresponds to legs[i] (arrival at stop i)
            // Note: points has Driver + Stops. So points[0] is Driver. points[1] is Stop 1.
            // legs[0] is path from points[0] to points[1].
            
            let cumulativeTime = 0;
            let cumulativeDistance = 0;

            // Clear previous ETAs
            document.querySelectorAll('.eta-display').forEach(el => el.innerHTML = '');

            legs.forEach((leg, index) => {
                if (index >= orderedStops.length) return;

                const stop = orderedStops[index];
                cumulativeTime += leg.summary.totalTime; // seconds
                cumulativeDistance += leg.summary.totalDistance; // meters

                // Update Trip Card
                const tripCard = document.querySelector(`.card[data-trip-id="${stop.tripId}"]`);
                if (tripCard) {
                    // Create or find ETA container
                    let etaContainer = tripCard.querySelector('.eta-info');
                    if (!etaContainer) {
                        const div = document.createElement('div');
                        div.className = 'eta-info mt-2 pt-2 border-t border-slate-100 dark:border-zink-600 flex justify-between items-center text-sm';
                        tripCard.querySelector('.card-body').appendChild(div);
                        etaContainer = div;
                    }

                    const timeString = Math.round(cumulativeTime / 60) + ' min';
                    const distString = (cumulativeDistance / 1000).toFixed(1) + ' km';

                    // Determine label based on stop type
                    const label = stop.type === 'pickup' ? 'Pickup in' : 'Dropoff in';
                    
                    // We might have multiple stops for same trip (Pickup AND Dropoff in list)
                    // We should append or update specific fields. 
                    // Let's use specific classes for pickup-eta and dropoff-eta if needed, 
                    // but usually we care about the NEXT action.
                    
                    // If this is the FIRST time we see this trip in the loop, it's the Pickup (usually).
                    // Actually, we processed Pickups then Dropoffs. 
                    // So we will encounter the Pickup leg first, then the Dropoff leg later.
                    
                    // Let's create specific slots
                    let specificSlot = etaContainer.querySelector(`.eta-${stop.type}`);
                    if (!specificSlot) {
                        specificSlot = document.createElement('span');
                        specificSlot.className = `eta-${stop.type} px-2 py-1 rounded bg-slate-100 dark:bg-zink-600 text-slate-600 dark:text-zink-200`;
                        etaContainer.appendChild(specificSlot);
                    }
                    
                    specificSlot.innerHTML = `<b>${label}:</b> ${timeString} (${distString})`;
                }
            });
        })
        .addTo(map);
    }

    function showLocationError() {
        const statusEl = document.getElementById('location-status');
        if (statusEl) {
            statusEl.classList.remove('hidden');
            statusEl.innerHTML = '<span class="text-red-500"><i data-lucide="alert-circle" class="inline w-4 h-4 mr-1"></i> Location permission denied. Please enable location services on your device to track trips.</span>';
            if (window.lucide) window.lucide.createIcons();
        }
    }

    function sendTripEvent(tripId, type, latitude, longitude) {
        fetch(`/driver/trips/${tripId}/events`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
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
    }

    // Exposed to onclick handlers
    window.logEvent = function(tripId, type) {
        if (geoPermissionDenied) {
            // If denied, just send without location
            sendTripEvent(tripId, type, null, null);
            return;
        }

        if (!navigator.geolocation) {
            sendTripEvent(tripId, type, null, null);
            return;
        }

        navigator.geolocation.getCurrentPosition(position => {
            const { latitude, longitude } = position.coords;
            sendTripEvent(tripId, type, latitude, longitude);
        }, error => {
            console.warn("Location error during event log:", error);
            if (error.code === 1) {
                geoPermissionDenied = true;
                showLocationError();
            }
            // Proceed without location
            sendTripEvent(tripId, type, null, null);
        });
    };

    document.addEventListener('DOMContentLoaded', () => {
        initMap();

        // Start Location Tracking
        if (navigator.geolocation) {
            // Initial check
            navigator.geolocation.getCurrentPosition(position => {
                const { latitude, longitude } = position.coords;
                
                // Update Driver Marker
                const driverLatLng = L.latLng(latitude, longitude);
                
                if (map) {
                    const driverIcon = L.divIcon({
                        className: 'custom-div-icon',
                        html: "<div style='background-color: #f59e0b; width: 16px; height: 16px; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 6px rgba(0,0,0,0.4);'></div>",
                        iconSize: [16, 16],
                        iconAnchor: [8, 8]
                    });
                    
                    if (driverMarker) map.removeLayer(driverMarker);
                    driverMarker = L.marker(driverLatLng, {icon: driverIcon}).addTo(map).bindPopup("You");
                    
                    // Update Route
                    updateRouting(driverLatLng);
                }

                // Send to Backend
                updateBackendLocation(latitude, longitude);

            }, error => {
                console.error("Initial geolocation error:", error);
                if (error.code === 1) {
                    geoPermissionDenied = true;
                    showLocationError();
                    // Initialize route without driver location
                    updateRouting(null); 
                }
            });

            // Interval Update
            setInterval(() => {
                if (geoPermissionDenied) return;

                navigator.geolocation.getCurrentPosition(position => {
                    const { latitude, longitude } = position.coords;
                    
                    // Update UI Marker
                    if (map && driverMarker) {
                        driverMarker.setLatLng([latitude, longitude]);
                    } else if (map) {
                        // Create if missing
                        const driverIcon = L.divIcon({
                            className: 'custom-div-icon',
                            html: "<div style='background-color: #f59e0b; width: 16px; height: 16px; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 6px rgba(0,0,0,0.4);'></div>",
                            iconSize: [16, 16],
                            iconAnchor: [8, 8]
                        });
                        driverMarker = L.marker([latitude, longitude], {icon: driverIcon}).addTo(map).bindPopup("You");
                    }

                    // Send to Backend
                    updateBackendLocation(latitude, longitude);

                }, error => {
                    if (error.code === 1) {
                        geoPermissionDenied = true;
                        showLocationError();
                    }
                });
            }, 15000); // 15 seconds
        } else {
            showLocationError();
            updateRouting(null);
        }
    });

    function updateBackendLocation(lat, lng) {
        fetch(updateLocationUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ lat, lng })
        }).catch(err => console.error("Failed to update backend location", err));
    }
</script>
@endsection
