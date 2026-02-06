@extends('layouts.master')

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
<style>
    #pickup_map { height: 300px; width: 100%; z-index: 1; position: relative; }
    .map-center-pin {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -100%); /* Point to the exact center */
        z-index: 1000; /* Above map tiles */
        pointer-events: none; /* Let clicks pass through to map if needed */
    }
</style>
@endpush

@section('content')
        <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center md:justify-between">
            <div class="grow">
                <h5 class="text-16">Add Child</h5>
            </div>
            <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                <li class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                    <a href="{{ route('parent.dashboard') }}" class="text-slate-400 dark:text-zink-200">Dashboard</a>
                </li>
                <li class="text-slate-700 dark:text-zink-100">
                    Add Child
                </li>
            </ul>
        </div>

        <div class="card">
            <div class="card-body max-h-[70vh] overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-track]:bg-slate-100 [&::-webkit-scrollbar-thumb]:bg-slate-300 dark:[&::-webkit-scrollbar-track]:bg-zink-700 dark:[&::-webkit-scrollbar-thumb]:bg-zink-500">
                <form method="POST" action="{{ route('parent.children.store') }}" class="space-y-4">
                    @csrf

                    <!-- Personal Information Section -->
                    <div class="mb-4">
                        <h6 class="mb-4 text-15 text-slate-500 dark:text-zink-200">Personal Information</h6>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="first_name" class="inline-block mb-2 text-base font-medium">First Name</label>
                                <input type="text" id="first_name" name="first_name" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200" placeholder="First Name" value="{{ old('first_name') }}" required>
                                @error('first_name')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="last_name" class="inline-block mb-2 text-base font-medium">Last Name</label>
                                <input type="text" id="last_name" name="last_name" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200" placeholder="Last Name" value="{{ old('last_name') }}" required>
                                @error('last_name')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 mt-4 sm:grid-cols-2">
                            <div>
                                <label for="date_of_birth" class="inline-block mb-2 text-base font-medium">Date of Birth</label>
                                <input type="date" id="date_of_birth" name="date_of_birth" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200" value="{{ old('date_of_birth') }}" required>
                                @error('date_of_birth')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="relationship" class="inline-block mb-2 text-base font-medium">Relationship to Child</label>
                                <select id="relationship" name="relationship" class="form-select border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200" required>
                                    <option value="">Select Relationship</option>
                                    <option value="Mother" {{ old('relationship') == 'Mother' ? 'selected' : '' }}>Mother</option>
                                    <option value="Father" {{ old('relationship') == 'Father' ? 'selected' : '' }}>Father</option>
                                    <option value="Guardian" {{ old('relationship') == 'Guardian' ? 'selected' : '' }}>Guardian</option>
                                    <option value="Grandparent" {{ old('relationship') == 'Grandparent' ? 'selected' : '' }}>Grandparent</option>
                                    <option value="Other" {{ old('relationship') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('relationship')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- School Information Section -->
                    <div class="mb-4 border-t border-slate-200 pt-4 dark:border-zink-500">
                        <h6 class="mb-4 text-15 text-slate-500 dark:text-zink-200">School Information</h6>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="school_id" class="inline-block mb-2 text-base font-medium">School</label>
                                <select id="school_id" name="school_id" class="form-select border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200" required>
                                    <option value="">Select School</option>
                                    @foreach($schools as $school)
                                        <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                                    @endforeach
                                </select>
                                @error('school_id')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="grade" class="inline-block mb-2 text-base font-medium">Grade</label>
                                <select id="grade" name="grade" class="form-select border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
                                    <option value="">Select Grade</option>
                                    @foreach(['ECD A', 'ECD B', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6', 'Grade 7'] as $grade)
                                        <option value="{{ $grade }}" {{ old('grade') == $grade ? 'selected' : '' }}>{{ $grade }}</option>
                                    @endforeach
                                </select>
                                @error('grade')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="inline-block mb-2 text-base font-medium">Pickup Location</label>
                            
                            <div class="flex flex-wrap gap-4 mb-3">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="pickup_type" value="existing" class="w-4 h-4 text-custom-500 border-slate-200 focus:ring-custom-500 dark:bg-zink-700 dark:border-zink-500" {{ old('pickup_type', 'existing') == 'existing' ? 'checked' : '' }} onchange="togglePickupType()">
                                    <span class="ml-2 text-slate-700 dark:text-zink-200">Select Existing Zone</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="pickup_type" value="custom" class="w-4 h-4 text-custom-500 border-slate-200 focus:ring-custom-500 dark:bg-zink-700 dark:border-zink-500" {{ old('pickup_type') == 'custom' ? 'checked' : '' }} onchange="togglePickupType()">
                                    <span class="ml-2 text-slate-700 dark:text-zink-200">Pick on Map / Current Location</span>
                                </label>
                            </div>

                            <div id="existing_pickup_container" class="{{ old('pickup_type', 'existing') == 'existing' ? '' : 'hidden' }}">
                                <select id="pickup_location_id" name="pickup_location_id" class="form-select border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
                                    <option value="">Select Pickup Location</option>
                                    @foreach($pickupLocations as $location)
                                        <option value="{{ $location->id }}" {{ old('pickup_location_id') == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                                    @endforeach
                                </select>
                                @error('pickup_location_id')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div id="custom_pickup_container" class="{{ old('pickup_type') == 'custom' ? '' : 'hidden' }} space-y-3">
                                <div>
                                    <label for="custom_location_name" class="inline-block mb-1 text-sm font-medium text-slate-500 dark:text-zink-200">Location Name (e.g. Home, Grandma's)</label>
                                    <input type="text" id="custom_location_name" name="custom_location_name" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200" placeholder="Enter a name for this location" value="{{ old('custom_location_name') }}">
                                    @error('custom_location_name')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="useCurrentLocation()" class="px-3 py-1.5 text-sm text-white bg-blue-500 rounded hover:bg-blue-600 transition-colors flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                        Use My Current Location
                                    </button>
                                    <span id="location_status" class="text-sm text-slate-500"></span>
                                </div>

                                <div id="pickup_map" class="w-full h-64 rounded-lg border border-slate-200 dark:border-zink-500 z-0">
                                    <!-- Center Pin Icon -->
                                    <div class="map-center-pin">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="#ef4444" stroke="#7f1d1d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin drop-shadow-lg"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3" fill="white"/></svg>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-500">Drag the map to position the pin at your pickup location.</p>

                                <input type="hidden" id="custom_lat" name="custom_lat" value="{{ old('custom_lat') }}">
                                <input type="hidden" id="custom_lng" name="custom_lng" value="{{ old('custom_lng') }}">
                                @error('custom_lat')
                                    <p class="mt-1 text-sm text-red-500">Please select a location on the map.</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 mt-4 sm:grid-cols-2">
                            <div>
                                <label for="school_start_time" class="inline-block mb-2 text-base font-medium">School Start Time</label>
                                <input type="time" id="school_start_time" name="school_start_time" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200" value="{{ old('school_start_time') }}" required>
                                @error('school_start_time')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="school_end_time" class="inline-block mb-2 text-base font-medium">School End Time</label>
                                <input type="time" id="school_end_time" name="school_end_time" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200" value="{{ old('school_end_time') }}" required>
                                @error('school_end_time')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information Section -->
                    <div class="mb-4 border-t border-slate-200 pt-4 dark:border-zink-500">
                        <h6 class="mb-4 text-15 text-slate-500 dark:text-zink-200">Additional Information</h6>
                        <div>
                            <label for="medical_notes" class="inline-block mb-2 text-base font-medium">Additional Notes (Optional)</label>
                            <textarea id="medical_notes" name="medical_notes" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200" rows="3">{{ old('medical_notes') }}</textarea>
                            @error('medical_notes')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('parent.dashboard') }}" class="text-slate-500 bg-white border-slate-100 hover:text-white hover:bg-slate-600 hover:border-slate-600 focus:text-white focus:bg-slate-600 focus:border-slate-600 focus:ring focus:ring-slate-100 active:text-white active:bg-slate-600 active:border-slate-600 active:ring active:ring-slate-100 dark:bg-zink-700 dark:hover:bg-slate-500 dark:ring-slate-400/20 dark:focus:bg-slate-500 btn">Cancel</a>
                        <button type="submit" class="text-white bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20 btn">Add Child</button>
                    </div>
                </form>
            </div>
        </div>
@endsection

@section('script')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<script>
    let pickupMap;
    let geocoder;

    function initPickupMap() {
        // Default to Bulawayo if no location selected
        const defaultLat = -20.1500;
        const defaultLng = 28.5833;
        
        if (!pickupMap) {
            pickupMap = L.map('pickup_map').setView([defaultLat, defaultLng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(pickupMap);

            // Initialize Geocoder
            geocoder = L.Control.Geocoder.nominatim();

            // Add Search Control (but we'll use it to move the map, not just drop a marker)
            const searchControl = L.Control.geocoder({
                defaultMarkGeocode: false,
                collapsed: false, /* Keep search bar open for easier access */
                geocoder: geocoder,
                placeholder: "Search for address...",
                errorMessage: "Nothing found."
            })
            .on('markgeocode', function(e) {
                const latlng = e.geocode.center;
                pickupMap.setView(latlng, 17);
                // Trigger update manually since moveend will fire
            })
            .addTo(pickupMap);

            // Listen for map movement to update coordinates
            pickupMap.on('moveend', function() {
                updateLocationFromMap();
            });

            // Initial update
            updateLocationFromMap();
        }
        
        // Invalidate size to ensure map renders correctly if it was hidden
        setTimeout(() => {
            pickupMap.invalidateSize();
            // If we have existing values, center map there
            const lat = document.getElementById('custom_lat').value;
            const lng = document.getElementById('custom_lng').value;
            if (lat && lng) {
                pickupMap.setView([lat, lng], 17);
            } else {
                // Auto-detect location if no value set (User Request: "map should be already showing the live location")
                useCurrentLocation();
            }
        }, 100);
    }

    function updateLocationFromMap() {
        const center = pickupMap.getCenter();
        document.getElementById('custom_lat').value = center.lat;
        document.getElementById('custom_lng').value = center.lng;
        
        // Reverse Geocoding to get address
        geocoder.reverse(center, pickupMap.options.crs.scale(pickupMap.getZoom()), function(results) {
            const r = results[0];
            if (r) {
                const nameInput = document.getElementById('custom_location_name');
                // Only auto-fill if empty or previously auto-filled (we can't easily track "previously auto-filled" without extra state, so let's just fill if empty for now to avoid overwriting user edits too aggressively, OR overwrite if it looks like an address)
                // For better UX: Let's show the address in a helper text or just update the input if it's empty.
                if (!nameInput.value || nameInput.getAttribute('data-autofilled') === 'true') {
                    nameInput.value = r.name || r.html || 'Selected Location';
                    nameInput.setAttribute('data-autofilled', 'true');
                }
            }
        });
    }

    // Handle manual input change to stop overwriting
    document.getElementById('custom_location_name')?.addEventListener('input', function() {
        this.setAttribute('data-autofilled', 'false');
    });

    function setPickupLocation(lat, lng) {
        // Legacy function support (used by useCurrentLocation)
        if (pickupMap) {
            pickupMap.setView([lat, lng], 17);
        }
    }

    function togglePickupType() {
        const type = document.querySelector('input[name="pickup_type"]:checked').value;
        const existingContainer = document.getElementById('existing_pickup_container');
        const customContainer = document.getElementById('custom_pickup_container');
        const pickupSelect = document.getElementById('pickup_location_id');
        const customNameInput = document.getElementById('custom_location_name');

        if (type === 'existing') {
            existingContainer.classList.remove('hidden');
            customContainer.classList.add('hidden');
            // Enable validation for existing
            if(pickupSelect) pickupSelect.required = true;
            if(customNameInput) customNameInput.required = false;
        } else {
            existingContainer.classList.add('hidden');
            customContainer.classList.remove('hidden');
            if(pickupSelect) {
                pickupSelect.value = ''; // Clear selection
                pickupSelect.required = false;
            }
            if(customNameInput) customNameInput.required = true;
            initPickupMap();
        }
    }

    function useCurrentLocation() {
        const status = document.getElementById('location_status');
        status.textContent = 'Fetching...';

        if (!navigator.geolocation) {
            status.textContent = 'Geolocation is not supported by your browser';
            return;
        }

        navigator.geolocation.getCurrentPosition((position) => {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            status.textContent = 'Location found!';
            
            // Ensure map is initialized
            if (!pickupMap) initPickupMap();
            
            pickupMap.setView([lat, lng], 17);
        }, () => {
            status.textContent = 'Unable to retrieve your location';
        });
    }

    // Initialize if custom is selected on load (e.g. after validation error)
    document.addEventListener('DOMContentLoaded', function() {
        if (document.querySelector('input[name="pickup_type"]:checked').value === 'custom') {
            initPickupMap();
        }
    });
</script>
@endsection
