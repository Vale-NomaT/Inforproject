@extends('layouts.master')

@section('content')
        <div class="flex flex-col gap-4 mb-5 md:flex-row md:items-center md:justify-between">
            <div>
                <h5 class="text-16">Route Map</h5>
                <p class="text-slate-500 dark:text-zink-200">Overview of all pickups and drop-offs for today.</p>
            </div>
            <div>
                <a href="{{ route('driver.trips.index') }}" class="text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">Back to List</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div id="map" class="h-[600px] rounded-xl w-full z-0"></div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
            @foreach ($trips as $trip)
                <div class="card p-4 border border-slate-200 dark:border-zink-500">
                    <h6 class="font-semibold text-slate-800 dark:text-zink-50">{{ $trip->child->first_name }} {{ $trip->child->last_name }}</h6>
                    <div class="mt-2 space-y-2 text-sm text-slate-500 dark:text-zink-200">
                        @if ($trip->child->pickupLocation)
                        <div class="flex items-start gap-2">
                            <i data-lucide="map-pin" class="w-4 h-4 mt-0.5 text-green-500"></i>
                            <div>
                                <span class="font-medium text-slate-700 dark:text-zink-100">Pickup:</span> {{ $trip->child->pickupLocation->name }}
                                <br>
                                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $trip->child->pickupLocation->lat }},{{ $trip->child->pickupLocation->lng }}" target="_blank" class="text-xs text-blue-500 hover:underline">Get Directions</a>
                            </div>
                        </div>
                        @endif
                        @if ($trip->child->school)
                        <div class="flex items-start gap-2">
                            <i data-lucide="graduation-cap" class="w-4 h-4 mt-0.5 text-blue-500"></i>
                            <div>
                                <span class="font-medium text-slate-700 dark:text-zink-100">Drop-off:</span> {{ $trip->child->school->name }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@section('script')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
<script>
    const map = L.map('map').setView([0, 0], 2);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const bounds = L.latLngBounds();
    const pickupPoints = [];
    const schoolPoints = [];

    // Custom Icons
    const pickupIcon = L.divIcon({
        className: 'custom-div-icon',
        html: "<div style='background-color: #22c55e; width: 12px; height: 12px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 4px rgba(0,0,0,0.3);'></div>",
        iconSize: [12, 12],
        iconAnchor: [6, 6]
    });

    const schoolIcon = L.divIcon({
        className: 'custom-div-icon',
        html: "<div style='background-color: #3b82f6; width: 12px; height: 12px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 4px rgba(0,0,0,0.3);'></div>",
        iconSize: [12, 12],
        iconAnchor: [6, 6]
    });

    @foreach ($trips as $trip)
        @if ($trip->child->pickupLocation && $trip->child->pickupLocation->lat && $trip->child->pickupLocation->lng)
            {
                const lat = {{ $trip->child->pickupLocation->lat }};
                const lng = {{ $trip->child->pickupLocation->lng }};
                const marker = L.marker([lat, lng], {icon: pickupIcon}).addTo(map);
                marker.bindPopup(`
                    <strong>Pickup: {{ $trip->child->first_name }}</strong><br>
                    {{ $trip->child->pickupLocation->name }}<br>
                    <a href='https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}' target='_blank'>Get Directions</a>
                `);
                bounds.extend([lat, lng]);
                pickupPoints.push(L.latLng(lat, lng));
            }
        @endif

        @if ($trip->child->school && $trip->child->school->lat && $trip->child->school->lng)
            {
                const lat = {{ $trip->child->school->lat }};
                const lng = {{ $trip->child->school->lng }};
                const marker = L.marker([lat, lng], {icon: schoolIcon}).addTo(map);
                marker.bindPopup(`
                    <strong>School: {{ $trip->child->school->name }}</strong><br>
                    Drop-off for {{ $trip->child->first_name }}
                `);
                bounds.extend([lat, lng]);
                
                // Avoid adding duplicate school points for routing if multiple kids go to same school
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
        // Default fallback if no valid locations
        map.setView([-26.2041, 28.0473], 12); // Johannesburg default
    }

    // Routing Control
    let routingControl = null;

    function initRouting(startPoint) {
        const waypoints = [];
        
        if (startPoint) {
            waypoints.push(startPoint);
        }

        // Add pickups then schools
        pickupPoints.forEach(p => waypoints.push(p));
        schoolPoints.forEach(p => waypoints.push(p));

        if (waypoints.length < 2) return; // Need at least 2 points for a route

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
            show: false, // Hide the turn-by-turn instructions by default to save space
            addWaypoints: false,
            draggableWaypoints: false,
            fitSelectedRoutes: false
        }).addTo(map);
    }

    // Driver Current Location
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

            // Init routing starting from driver location
            initRouting(L.latLng(lat, lng));

        }, (error) => {
            console.error("Geolocation error:", error);
            // Init routing without driver location (start from first pickup)
            initRouting(null);
        });
    } else {
        initRouting(null);
    }
</script>
@endsection
