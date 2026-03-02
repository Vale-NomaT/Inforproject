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



        <div id="location-status" class="hidden px-4 py-3 mb-5 text-sm text-blue-500 border border-blue-200 rounded-md bg-blue-50 dark:bg-blue-400/20 dark:border-blue-500/50">
            Updating location...
        </div>

        @if (!empty($runs) && count($runs) > 0)
            <div class="card mb-5">
                <div class="card-body relative">
                    <h6 class="mb-3 text-sm font-semibold text-slate-900 dark:text-zink-50">Today's Route Map</h6>
                    <div id="driver-route-map" class="h-[420px] md:h-[520px] rounded-xl w-full z-0"></div>
                    
                    <!-- Route Summary Overlay -->
                    <div id="route-summary" class="absolute top-14 left-1/2 transform -translate-x-1/2 z-[400] bg-white/90 dark:bg-zinc-800/90 backdrop-blur-sm px-4 py-2 rounded-full shadow-lg border border-zinc-200 dark:border-zinc-700 hidden flex items-center gap-4 transition-all duration-300">
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-slate-500 uppercase font-bold tracking-wider">Dist</span>
                            <span id="summary-dist" class="font-bold text-slate-800 dark:text-white text-lg">0 km</span>
                        </div>
                        <div class="w-px h-6 bg-slate-300 dark:bg-zinc-600"></div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-slate-500 uppercase font-bold tracking-wider">ETA</span>
                            <span id="summary-time" class="font-bold text-slate-800 dark:text-white text-lg">0 min</span>
                        </div>
                        <div class="w-px h-6 bg-slate-300 dark:bg-zinc-600"></div>
                        <button id="voice-toggle" class="p-1 rounded-full hover:bg-slate-100 dark:hover:bg-zinc-700 transition-colors" onclick="toggleVoice()">
                            <svg id="voice-on-icon" class="w-5 h-5 text-slate-700 dark:text-slate-200 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path></svg>
                            <svg id="voice-off-icon" class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"></path></svg>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        @if (empty($runs) || count($runs) === 0)
            <div class="card">
                <div class="card-body text-center py-10">
                    <h5 class="text-16 mb-2">No scheduled trips</h5>
                    <p class="text-slate-500 dark:text-zink-200">Approved bookings will automatically create trips for upcoming school days.</p>
                </div>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($runs as $run)
                    @php
                        $runType = $run['type'];
                        $runDate = $run['date'];
                        $runLabel = ($runType === \App\Models\Trip::TYPE_MORNING ? 'Morning Run' : 'Afternoon Run').' • '.\Carbon\Carbon::parse($runDate)->format('D, M d, Y');
                        $runTypeClass = $runType === \App\Models\Trip::TYPE_MORNING
                            ? 'bg-orange-100 text-orange-500 border-orange-200 dark:bg-orange-500/20 dark:border-orange-500/20'
                            : 'bg-purple-100 text-purple-500 border-purple-200 dark:bg-purple-500/20 dark:border-purple-500/20';
                        $runStatus = $run['status'];
                        $runStatusClass = $runStatus === \App\Models\Trip::STATUS_IN_PROGRESS
                            ? 'border-green-200 bg-green-100 text-green-500 dark:bg-green-500/20 dark:border-green-500/20'
                            : 'border-yellow-200 bg-yellow-100 text-yellow-500 dark:bg-yellow-500/20 dark:border-yellow-500/20';
                    @endphp

                    <details class="card" data-run-key="{{ $run['key'] }}">
                        <summary class="card-body cursor-pointer list-none">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                                <div class="flex flex-col gap-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h6 class="text-15 font-semibold text-slate-900 dark:text-zink-50">Trip {{ $run['key'] }}</h6>
                                        <span class="px-2 py-0.5 text-xs font-medium rounded border {{ $runTypeClass }}">{{ $runType === \App\Models\Trip::TYPE_MORNING ? 'Morning Run' : 'Afternoon Run' }}</span>
                                        <span class="px-2.5 py-0.5 text-xs inline-block font-medium rounded border {{ $runStatusClass }}">{{ $runStatus === \App\Models\Trip::STATUS_IN_PROGRESS ? 'In Progress' : 'Scheduled' }}</span>
                                    </div>
                                    <p class="text-slate-500 dark:text-zink-200 text-sm">
                                        {{ $runLabel }} • {{ count($run['trips']) }} {{ count($run['trips']) === 1 ? 'child' : 'children' }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-2 md:justify-end">
                                    @if ($runStatus === \App\Models\Trip::STATUS_SCHEDULED)
                                        <form method="POST" action="{{ route('driver.runs.start') }}">
                                            @csrf
                                            <input type="hidden" name="date" value="{{ $runDate }}">
                                            <input type="hidden" name="type" value="{{ $runType }}">
                                            <button type="submit" class="text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">
                                                Start Trip
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </summary>

                        <div class="px-5 pb-5 pt-0">
                            <div class="grid grid-cols-1 gap-3">
                                @foreach ($run['trips'] as $trip)
                                    @php
                                        $isMorning = $trip->type === \App\Models\Trip::TYPE_MORNING;
                                        $pickupName = $trip->child && $trip->child->pickupLocation ? ($trip->child->pickup_address ?? $trip->child->pickupLocation->name) : 'Home';
                                        $schoolName = $trip->child && $trip->child->school ? $trip->child->school->name : 'School';
                                    @endphp
                                    <div class="rounded-xl border border-slate-200 bg-white p-4 dark:bg-zink-700 dark:border-zink-500 card" data-trip-id="{{ $trip->id }}" data-status="{{ $trip->status }}">
                                        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                                            <div>
                                                <h6 class="text-15 font-semibold text-slate-900 dark:text-zink-50">
                                                    {{ $trip->child ? $trip->child->first_name . ' ' . $trip->child->last_name : 'Child' }}
                                                </h6>
                                                <p class="text-slate-500 dark:text-zink-200 text-sm">
                                                    Route:
                                                    <span class="font-medium text-slate-800 dark:text-zink-50">
                                                        @if($isMorning)
                                                            {{ $pickupName }} &rarr; {{ $schoolName }}
                                                        @else
                                                            {{ $schoolName }} &rarr; {{ $pickupName }}
                                                        @endif
                                                    </span>
                                                </p>
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
                                                @endif
                                            </div>
                                        </div>

                                        @if ($trip->status === \App\Models\Trip::STATUS_IN_PROGRESS)
                                            <div class="mt-3 flex flex-wrap gap-2 justify-end">
                                                <button type="button" onclick="logEvent('{{ $trip->id }}', 'arrived')" class="text-white btn bg-yellow-500 border-yellow-500 hover:text-white hover:bg-yellow-600 hover:border-yellow-600 focus:text-white focus:bg-yellow-600 focus:border-yellow-600 focus:ring focus:ring-yellow-100 active:text-white active:bg-yellow-600 active:border-yellow-600 active:ring active:ring-yellow-100 dark:ring-yellow-400/20 text-xs">
                                                    Arrived at Pickup
                                                </button>
                                                <button type="button" onclick="logEvent('{{ $trip->id }}', 'picked_up')" class="text-white btn bg-purple-500 border-purple-500 hover:text-white hover:bg-purple-600 hover:border-purple-600 focus:text-white focus:bg-purple-600 focus:border-purple-600 focus:ring focus:ring-purple-100 active:text-white active:bg-purple-600 active:border-purple-600 active:ring active:ring-purple-100 dark:ring-purple-400/20 text-xs">
                                                    Picked Up
                                                </button>
                                                <button type="button" onclick="logEvent('{{ $trip->id }}', 'arrived_dropoff')" class="text-white btn bg-orange-500 border-orange-500 hover:text-white hover:bg-orange-600 hover:border-orange-600 focus:text-white focus:bg-orange-600 focus:border-orange-600 focus:ring focus:ring-orange-100 active:text-white active:bg-orange-600 active:border-orange-600 active:ring active:ring-orange-100 dark:ring-orange-400/20 text-xs">
                                                    Arrived at Drop-off
                                                </button>
                                                <button type="button" onclick="logEvent('{{ $trip->id }}', 'dropped_off')" class="text-white btn bg-green-500 border-green-500 hover:text-white hover:bg-green-600 hover:border-green-600 focus:text-white focus:bg-green-600 focus:border-green-600 focus:ring focus:ring-green-100 active:text-white active:bg-green-600 active:border-green-600 active:ring active:ring-green-100 dark:ring-green-400/20 text-sm px-4 py-2 font-bold shadow-md">
                                                    <i data-lucide="check-circle" class="w-4 h-4 inline-block mr-1"></i> Complete Trip
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </details>
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
    let map = null;
    let activeRunKey = null;
    let stopMarkers = [];
    let routingControl = null;
    let lastDriverLatLng = null;
    let lastBackendUpdate = 0;
    let voiceEnabled = false;
    let lastSpokenKey = ''; // Track unique instruction+distanceBucket combo
    
    // Car Icon Definition
    const carIcon = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/3202/3202926.png', // Generic Car Icon
        iconSize: [40, 40],
        iconAnchor: [20, 20],
        popupAnchor: [0, -20]
    });

    const runs = @json($runs);
    const csrfToken = '{{ csrf_token() }}';
    const updateLocationUrl = '{{ route("driver.location.update") }}';

    function initMap() {
        if (!runs || runs.length === 0) return;

        const mapContainer = document.getElementById('driver-route-map');
        if (!mapContainer) return;

        map = L.map('driver-route-map').setView([-20.1367, 28.6363], 13); // Default to Bulawayo

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const firstRun = runs.find(r => r.status === 'in_progress') || runs[0];
        if (firstRun) {
            setActiveRun(firstRun.key);
        }
    }

    function setActiveRun(runKey) {
        activeRunKey = runKey;
        
        document.querySelectorAll('details[data-run-key]').forEach(d => {
            if (d.dataset.runKey !== runKey) d.open = false;
        });

        const details = document.querySelector(`details[data-run-key="${runKey}"]`);
        if (details) details.open = true;

        renderStops();
        
        // If we have a location, start routing to the next stop
        if (lastDriverLatLng) {
            updateNavigation();
        }
    }

    function getActiveRun() {
        if (!activeRunKey) return null;
        return runs.find(r => r.key === activeRunKey) || null;
    }

    function clearLayers(list) {
        if (!map) return;
        list.forEach(layer => {
            try { map.removeLayer(layer); } catch (e) {}
        });
        list.length = 0;
    }

    function pickupLatLngForTrip(trip) {
        const child = trip.child;
        if (!child) return null;
        const isMorning = trip.type === 'morning';

        let lat = null;
        let lng = null;

        if (isMorning) {
            lat = child.pickup_lat || (child.pickup_location ? child.pickup_location.lat : null);
            lng = child.pickup_lng || (child.pickup_location ? child.pickup_location.lng : null);
        } else {
            lat = child.school ? child.school.lat : null;
            lng = child.school ? child.school.lng : null;
        }

        if (!lat || !lng) return null;
        return L.latLng(parseFloat(lat), parseFloat(lng));
    }

    function dropoffLatLngForTrip(trip) {
        const child = trip.child;
        if (!child) return null;
        const isMorning = trip.type === 'morning';

        let lat = null;
        let lng = null;

        if (isMorning) {
            lat = child.school ? child.school.lat : null;
            lng = child.school ? child.school.lng : null;
        } else {
            lat = child.pickup_lat || (child.pickup_location ? child.pickup_location.lat : null);
            lng = child.pickup_lng || (child.pickup_location ? child.pickup_location.lng : null);
        }

        if (!lat || !lng) return null;
        return L.latLng(parseFloat(lat), parseFloat(lng));
    }

    function renderStops() {
        if (!map) return;
        clearLayers(stopMarkers);

        const run = getActiveRun();
        if (!run || !run.trips) return;

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

        const bounds = L.latLngBounds();

        run.trips.forEach(trip => {
            const child = trip.child;
            if (!child) return;

            const pickup = pickupLatLngForTrip(trip);
            const dropoff = dropoffLatLngForTrip(trip);

            const pickupName = child.pickup_address || (child.pickup_location ? child.pickup_location.name : 'Home');
            const schoolName = child.school ? child.school.name : 'School';

            if (pickup) {
                const marker = L.marker(pickup, {icon: pickupIcon})
                    .bindPopup(`<b>Pickup: ${child.first_name}</b><br>${trip.type === 'morning' ? pickupName : schoolName}`);
                marker.addTo(map);
                stopMarkers.push(marker);
                bounds.extend(pickup);
            }

            if (dropoff) {
                const marker = L.marker(dropoff, {icon: dropoffIcon})
                    .bindPopup(`<b>Drop-off: ${child.first_name}</b><br>${trip.type === 'morning' ? schoolName : pickupName}`);
                marker.addTo(map);
                stopMarkers.push(marker);
                bounds.extend(dropoff);
            }
        });

        if (lastDriverLatLng) bounds.extend(lastDriverLatLng);

        if (bounds.isValid() && !routingControl) {
            map.fitBounds(bounds, {padding: [40, 40]});
        }
    }

    // Determine the next immediate stop based on simple distance or trip order
    // In a real app, this should follow the optimized route sequence
    function getNextStop(driverLatLng) {
        const run = getActiveRun();
        if (!run || !run.trips) return null;

        let bestStop = null;
        let minKey = Infinity; // Use simple index logic or distance

        // Simplistic logic: Find the nearest pickup or dropoff that hasn't been visited?
        // Since we don't track "visited" state in frontend efficiently without reloading,
        // we'll just target the nearest stop in the list for now.
        // A better approach: The driver should select "Navigate to" a specific trip, 
        // but for now, we'll route to the first trip's pickup or dropoff.
        
        if (run.trips.length > 0) {
            const trip = run.trips[0];
            // If morning, go to pickup, then dropoff. 
            // This is a simplification. The user logic usually involves multiple stops.
            // Let's just route to the FIRST pickup of the FIRST trip for demo purposes, 
            // or the nearest stop.
            
            const pickup = pickupLatLngForTrip(trip);
            if (pickup) return pickup;
        }
        return null;
    }

    function toggleVoice() {
        voiceEnabled = !voiceEnabled;
        const onIcon = document.getElementById('voice-on-icon');
        const offIcon = document.getElementById('voice-off-icon');
        const btn = document.getElementById('voice-toggle');
        
        if (voiceEnabled) {
            onIcon.classList.remove('hidden');
            offIcon.classList.add('hidden');
            btn.classList.add('text-blue-600', 'bg-blue-50');
            speak("Voice navigation started");
        } else {
            onIcon.classList.add('hidden');
            offIcon.classList.remove('hidden');
            btn.classList.remove('text-blue-600', 'bg-blue-50');
            window.speechSynthesis.cancel();
        }
    }

    function speak(text) {
        if (!voiceEnabled || !('speechSynthesis' in window)) return;
        
        window.speechSynthesis.cancel();
        
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'en-US';
        utterance.rate = 1.0;
        window.speechSynthesis.speak(utterance);
    }

    function updateNavigation() {
        if (!map || !lastDriverLatLng) return;

        const run = getActiveRun();
        if (!run || !run.trips || run.trips.length === 0) return;

        // Target: Let's try to find the nearest stop to drive to
        // For simplicity in this demo, we route to the first trip's relevant point
        // In a real multi-stop scenario, we'd have a queue of stops.
        const target = getNextStop(lastDriverLatLng);
        if (!target) return;

        if (!routingControl) {
            routingControl = L.Routing.control({
                waypoints: [
                    lastDriverLatLng,
                    target
                ],
                router: L.Routing.osrmv1({
                    serviceUrl: 'https://router.project-osrm.org/route/v1'
                }),
                lineOptions: {
                    styles: [{ color: '#3b82f6', opacity: 0.8, weight: 6 }]
                },
                show: true, // Show directions panel
                addWaypoints: false,
                draggableWaypoints: false,
                createMarker: function() { return null; } // Don't create default markers, we have our own
            })
            .on('routesfound', function(e) {
                const routes = e.routes;
                if (routes && routes.length > 0) {
                    const route = routes[0];
                    const summary = route.summary;
                    
                    // Update overlay
                    const summaryEl = document.getElementById('route-summary');
                    const distEl = document.getElementById('summary-dist');
                    const timeEl = document.getElementById('summary-time');
                    
                    if (summaryEl && distEl && timeEl) {
                        const distKm = (summary.totalDistance / 1000).toFixed(1);
                        const timeMin = Math.round(summary.totalTime / 60);
                        
                        distEl.textContent = `${distKm} km`;
                        timeEl.textContent = `${timeMin} min`;
                        summaryEl.classList.remove('hidden');
                        summaryEl.classList.add('flex');
                    }

                    const instructions = route.instructions;
                    if (instructions && instructions.length > 0) {
                        const nextInstruction = instructions[0];
                        const dist = nextInstruction.distance;
                        const action = nextInstruction.text;
                        
                        // Distance buckets for voice guidance
                        let bucket = 'far';
                        if (dist < 40) bucket = 'now';
                        else if (dist < 200) bucket = 'soon';
                        else if (dist < 600) bucket = 'approach';
                        
                        // Unique key for this instruction state
                        const currentKey = `${action}|${bucket}`;
                        
                        if (currentKey !== lastSpokenKey && bucket !== 'far') {
                            let textToSpeak = '';
                            
                            if (bucket === 'now') {
                                textToSpeak = action; // e.g. "Turn right onto Main St"
                            } else {
                                textToSpeak = `In ${Math.round(dist)} meters, ${action}`;
                            }
                            
                            speak(textToSpeak);
                            lastSpokenKey = currentKey;
                        }
                    }
                }
            })
            .addTo(map);
        } else {
            // Update start point to current driver location
            // We keep the end point same until logic changes it
            routingControl.setWaypoints([
                lastDriverLatLng,
                target
            ]);
        }
    }

    function showLocationError() {
        const statusEl = document.getElementById('location-status');
        if (statusEl) {
            statusEl.classList.remove('hidden');
            statusEl.innerHTML = '<span class="text-red-500"><i data-lucide="alert-circle" class="inline w-4 h-4 mr-1"></i> Location permission denied. Please enable location services.</span>';
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

    window.logEvent = function(tripId, type) {
        if (geoPermissionDenied || !navigator.geolocation) {
            sendTripEvent(tripId, type, null, null);
            return;
        }

        navigator.geolocation.getCurrentPosition(position => {
            const { latitude, longitude } = position.coords;
            sendTripEvent(tripId, type, latitude, longitude);
        }, error => {
            sendTripEvent(tripId, type, null, null);
        });
    };

    function updateBackendLocation(lat, lng) {
        const now = Date.now();
        // Throttle updates to every 10 seconds
        if (now - lastBackendUpdate < 10000) return;
        
        lastBackendUpdate = now;
        fetch(updateLocationUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ lat, lng })
        }).catch(err => console.error("Failed to update backend location", err));
    }

    document.addEventListener('DOMContentLoaded', () => {
        initMap();

        document.querySelectorAll('details[data-run-key]').forEach(d => {
            d.addEventListener('toggle', () => {
                if (d.open) {
                    setActiveRun(d.dataset.runKey);
                }
            });
        });

        // Use watchPosition for real-time updates
        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(position => {
                const { latitude, longitude } = position.coords;
                lastDriverLatLng = L.latLng(latitude, longitude);

                // Update Driver Marker
                if (map) {
                    if (driverMarker) {
                        driverMarker.setLatLng(lastDriverLatLng);
                        driverMarker.setRotationAngle && driverMarker.setRotationAngle(position.coords.heading || 0);
                    } else {
                        driverMarker = L.marker(lastDriverLatLng, {icon: carIcon}).addTo(map).bindPopup("You");
                    }
                    
                    // Center map on driver if moving fast? Optional.
                    // map.setView(lastDriverLatLng); 
                }

                // Update Navigation (Voice & Route)
                updateNavigation();

                // Send to Backend (Throttled)
                updateBackendLocation(latitude, longitude);

            }, error => {
                if (error.code === 1) {
                    geoPermissionDenied = true;
                    showLocationError();
                }
            }, {
                enableHighAccuracy: true,
                maximumAge: 0,
                timeout: 5000
            });
        } else {
            showLocationError();
        }
    });
</script>
@endsection
