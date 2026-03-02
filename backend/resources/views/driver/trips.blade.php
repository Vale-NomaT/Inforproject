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

        @if (!empty($runs) && count($runs) > 0)
            <div class="card mb-5">
                <div class="card-body">
                    <h6 class="mb-3 text-sm font-semibold text-slate-900 dark:text-zink-50">Today's Route Map</h6>
                    <div id="driver-route-map" class="h-[420px] md:h-[520px] rounded-xl w-full z-0"></div>
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
                                                <button type="button" onclick="logEvent('{{ $trip->id }}', 'dropped_off')" class="text-white btn bg-green-500 border-green-500 hover:text-white hover:bg-green-600 hover:border-green-600 focus:text-white focus:bg-green-600 focus:border-green-600 focus:ring focus:ring-green-100 active:text-white active:bg-green-600 active:border-green-600 active:ring active:ring-green-100 dark:ring-green-400/20 text-xs">
                                                    Dropped Off
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
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    let geoPermissionDenied = false;
    let driverMarker = null;
    let map = null;
    let activeRunKey = null;
    let stopMarkers = [];
    let routePolylines = [];
    let routeLabels = [];
    let lastDriverLatLng = null;
    let currentRoutes = null;
    let selectedRouteIndex = 0;
    let osrmAbortController = null;

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
        selectedRouteIndex = 0;
        currentRoutes = null;

        document.querySelectorAll('details[data-run-key]').forEach(d => {
            if (d.dataset.runKey !== runKey) d.open = false;
        });

        const details = document.querySelector(`details[data-run-key="${runKey}"]`);
        if (details) details.open = true;

        renderStops();
        if (lastDriverLatLng) {
            renderRoutes(lastDriverLatLng);
        } else {
            renderRoutes(null);
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

        if (bounds.isValid()) {
            map.fitBounds(bounds, {padding: [40, 40]});
        }
    }

    function haversineKm(a, b) {
        const toRad = d => (d * Math.PI) / 180;
        const R = 6371;
        const dLat = toRad(b.lat - a.lat);
        const dLng = toRad(b.lng - a.lng);
        const s1 = Math.sin(dLat / 2);
        const s2 = Math.sin(dLng / 2);
        const q = s1 * s1 + Math.cos(toRad(a.lat)) * Math.cos(toRad(b.lat)) * s2 * s2;
        return 2 * R * Math.atan2(Math.sqrt(q), Math.sqrt(1 - q));
    }

    function buildOrderedStops(startLatLng) {
        const run = getActiveRun();
        if (!run || !run.trips) return { orderedStops: [], orderedLatLngs: [] };

        const pickups = [];
        const dropoffs = [];

        run.trips.forEach(trip => {
            const child = trip.child;
            if (!child) return;

            const pickup = pickupLatLngForTrip(trip);
            const dropoff = dropoffLatLngForTrip(trip);

            const pickupName = child.pickup_address || (child.pickup_location ? child.pickup_location.name : 'Home');
            const schoolName = child.school ? child.school.name : 'School';

            if (pickup) {
                pickups.push({
                    latLng: pickup,
                    tripId: trip.id,
                    type: 'pickup',
                    label: trip.type === 'morning' ? pickupName : schoolName,
                    childName: child.first_name
                });
            }
            if (dropoff) {
                dropoffs.push({
                    latLng: dropoff,
                    tripId: trip.id,
                    type: 'dropoff',
                    label: trip.type === 'morning' ? schoolName : pickupName,
                    childName: child.first_name
                });
            }
        });

        if (pickups.length === 0 && dropoffs.length === 0) return { orderedStops: [], orderedLatLngs: [] };

        const orderedStops = [];

        let cursor = startLatLng || pickups[0]?.latLng || dropoffs[0]?.latLng;
        const remainingPickups = pickups.slice();

        while (cursor && remainingPickups.length > 0) {
            let bestIdx = 0;
            let bestDist = Infinity;
            for (let i = 0; i < remainingPickups.length; i++) {
                const d = haversineKm(cursor, remainingPickups[i].latLng);
                if (d < bestDist) {
                    bestDist = d;
                    bestIdx = i;
                }
            }
            const next = remainingPickups.splice(bestIdx, 1)[0];
            orderedStops.push(next);
            cursor = next.latLng;
        }

        const remainingDropoffs = dropoffs.slice();
        while (cursor && remainingDropoffs.length > 0) {
            let bestIdx = 0;
            let bestDist = Infinity;
            for (let i = 0; i < remainingDropoffs.length; i++) {
                const d = haversineKm(cursor, remainingDropoffs[i].latLng);
                if (d < bestDist) {
                    bestDist = d;
                    bestIdx = i;
                }
            }
            const next = remainingDropoffs.splice(bestIdx, 1)[0];
            orderedStops.push(next);
            cursor = next.latLng;
        }

        const orderedLatLngs = orderedStops.map(s => s.latLng);
        return { orderedStops, orderedLatLngs };
    }

    function routeUrl(points) {
        const coords = points.map(p => `${p.lng},${p.lat}`).join(';');
        return `https://router.project-osrm.org/route/v1/driving/${coords}?alternatives=true&overview=full&geometries=geojson&steps=false`;
    }

    function renderRoutes(driverLatLng) {
        if (!map) return;
        clearLayers(routePolylines);
        clearLayers(routeLabels);
        currentRoutes = null;

        const run = getActiveRun();
        if (!run || !run.trips || run.trips.length === 0) return;

        const start = driverLatLng || pickupLatLngForTrip(run.trips[0]) || dropoffLatLngForTrip(run.trips[0]);
        if (!start) return;

        const { orderedStops, orderedLatLngs } = buildOrderedStops(driverLatLng || start);
        if (orderedLatLngs.length === 0) return;

        const points = [start, ...orderedLatLngs];
        if (points.length < 2) return;

        if (osrmAbortController) {
            try { osrmAbortController.abort(); } catch (e) {}
        }
        osrmAbortController = new AbortController();

        fetch(routeUrl(points), { signal: osrmAbortController.signal })
            .then(r => r.json())
            .then(data => {
                if (!data || !data.routes || data.routes.length === 0) return;
                currentRoutes = {
                    routes: data.routes,
                    orderedStops
                };
                const bestIndex = data.routes.reduce((bestIdx, route, idx) => {
                    if (bestIdx === -1) return idx;
                    return route.duration < data.routes[bestIdx].duration ? idx : bestIdx;
                }, -1);
                selectedRouteIndex = bestIndex >= 0 ? bestIndex : 0;
                drawRouteAlternatives();
                updateEtaFromSelectedRoute();
            })
            .catch(err => {
                if (err && err.name === 'AbortError') return;
                console.warn('Routing error:', err);
            });
    }

    function drawRouteAlternatives() {
        if (!map || !currentRoutes || !currentRoutes.routes) return;
        clearLayers(routePolylines);
        clearLayers(routeLabels);

        const routes = currentRoutes.routes;

        routes.forEach((route, idx) => {
            if (!route.geometry || !route.geometry.coordinates) return;

            const latLngs = route.geometry.coordinates.map(c => L.latLng(c[1], c[0]));
            const isSelected = idx === selectedRouteIndex;

            const polyline = L.polyline(latLngs, {
                color: isSelected ? '#4f46e5' : '#60a5fa',
                opacity: isSelected ? 0.9 : 0.45,
                weight: isSelected ? 7 : 5
            }).addTo(map);

            polyline.on('click', function () {
                selectedRouteIndex = idx;
                drawRouteAlternatives();
                updateEtaFromSelectedRoute();
            });

            routePolylines.push(polyline);

            const minutes = Math.round(route.duration / 60);
            const km = (route.distance / 1000).toFixed(1);
            const mid = latLngs[Math.floor(latLngs.length / 2)];

            if (mid) {
                const labelIcon = L.divIcon({
                    className: 'custom-div-icon',
                    html: `<div style="background: rgba(255,255,255,0.95); border: 1px solid rgba(148,163,184,0.9); padding: 4px 8px; border-radius: 9999px; font-size: 12px; font-weight: 700; color: #0f172a; box-shadow: 0 1px 8px rgba(0,0,0,0.08);">${minutes} min • ${km} km</div>`,
                    iconSize: [0, 0],
                    iconAnchor: [0, 0]
                });
                const labelMarker = L.marker(mid, { icon: labelIcon, interactive: false }).addTo(map);
                routeLabels.push(labelMarker);
            }
        });
    }

    function updateEtaFromSelectedRoute() {
        if (!currentRoutes || !currentRoutes.routes || !currentRoutes.orderedStops) return;
        const route = currentRoutes.routes[selectedRouteIndex];
        if (!route || !route.legs) return;

        document.querySelectorAll('.eta-display').forEach(el => el.innerHTML = '');

        let cumulativeTime = 0;
        let cumulativeDistance = 0;

        route.legs.forEach((leg, index) => {
            if (index >= currentRoutes.orderedStops.length) return;
            const stop = currentRoutes.orderedStops[index];

            cumulativeTime += leg.duration;
            cumulativeDistance += leg.distance;

            const tripCard = document.querySelector(`.card[data-trip-id="${stop.tripId}"]`);
            if (!tripCard) return;

            let etaContainer = tripCard.querySelector('.eta-info');
            if (!etaContainer) {
                const div = document.createElement('div');
                div.className = 'eta-info mt-2 pt-2 border-t border-slate-100 dark:border-zink-600 flex justify-between items-center text-sm';
                tripCard.appendChild(div);
                etaContainer = div;
            } else {
                etaContainer.innerHTML = '';
            }

            const timeString = Math.round(cumulativeTime / 60) + ' min';
            const distString = (cumulativeDistance / 1000).toFixed(1) + ' km';
            const label = stop.type === 'pickup' ? 'Pickup in' : 'Drop-off in';

            const specificSlot = document.createElement('span');
            specificSlot.className = `eta-${stop.type} px-2 py-1 rounded bg-slate-100 dark:bg-zink-600 text-slate-600 dark:text-zink-200`;
            specificSlot.innerHTML = `<b>${label}:</b> ${timeString} (${distString})`;
            etaContainer.appendChild(specificSlot);
        });
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

        document.querySelectorAll('details[data-run-key]').forEach(d => {
            d.addEventListener('toggle', () => {
                if (d.open) {
                    setActiveRun(d.dataset.runKey);
                }
            });
        });

        // Start Location Tracking
        if (navigator.geolocation) {
            // Initial check
            navigator.geolocation.getCurrentPosition(position => {
                const { latitude, longitude } = position.coords;
                
                // Update Driver Marker
                const driverLatLng = L.latLng(latitude, longitude);
                lastDriverLatLng = driverLatLng;
                
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
                    renderStops();
                    renderRoutes(driverLatLng);
                }

                // Send to Backend
                updateBackendLocation(latitude, longitude);

            }, error => {
                console.error("Initial geolocation error:", error);
                if (error.code === 1) {
                    geoPermissionDenied = true;
                    showLocationError();
                    renderStops();
                    renderRoutes(null);
                }
            });

            // Interval Update
            setInterval(() => {
                if (geoPermissionDenied) return;

                navigator.geolocation.getCurrentPosition(position => {
                    const { latitude, longitude } = position.coords;
                    lastDriverLatLng = L.latLng(latitude, longitude);
                    
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

                    renderRoutes(lastDriverLatLng);

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
            renderStops();
            renderRoutes(null);
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
