@extends('layouts.master')

@section('title', 'Parent Dashboard')

@php
/**
 * Color palette — each child gets a distinct theme.
 * Add more entries if a parent has more than 6 children.
 */
$palette = [
    [
        'accent'      => '#2563eb', // blue
        'bg'          => '#eff6ff',
        'border'      => '#bfdbfe',
        'avatar_bg'   => '#dbeafe',
        'avatar_text' => '#1d4ed8',
        'badge_bg'    => '#dbeafe',
        'badge_text'  => '#1e40af',
        'btn'         => '#2563eb',
        'btn_hover'   => '#1d4ed8',
        'tag'         => 'blue',
    ],
    [
        'accent'      => '#7c3aed', // violet
        'bg'          => '#f5f3ff',
        'border'      => '#ddd6fe',
        'avatar_bg'   => '#ede9fe',
        'avatar_text' => '#6d28d9',
        'badge_bg'    => '#ede9fe',
        'badge_text'  => '#5b21b6',
        'btn'         => '#7c3aed',
        'btn_hover'   => '#6d28d9',
        'tag'         => 'violet',
    ],
    [
        'accent'      => '#0891b2', // cyan
        'bg'          => '#ecfeff',
        'border'      => '#a5f3fc',
        'avatar_bg'   => '#cffafe',
        'avatar_text' => '#0e7490',
        'badge_bg'    => '#cffafe',
        'badge_text'  => '#155e75',
        'btn'         => '#0891b2',
        'btn_hover'   => '#0e7490',
        'tag'         => 'cyan',
    ],
    [
        'accent'      => '#d97706', // amber
        'bg'          => '#fffbeb',
        'border'      => '#fde68a',
        'avatar_bg'   => '#fef3c7',
        'avatar_text' => '#b45309',
        'badge_bg'    => '#fef3c7',
        'badge_text'  => '#92400e',
        'btn'         => '#d97706',
        'btn_hover'   => '#b45309',
        'tag'         => 'amber',
    ],
    [
        'accent'      => '#059669', // emerald
        'bg'          => '#ecfdf5',
        'border'      => '#a7f3d0',
        'avatar_bg'   => '#d1fae5',
        'avatar_text' => '#047857',
        'badge_bg'    => '#d1fae5',
        'badge_text'  => '#065f46',
        'btn'         => '#059669',
        'btn_hover'   => '#047857',
        'tag'         => 'emerald',
    ],
    [
        'accent'      => '#db2777', // pink
        'bg'          => '#fdf2f8',
        'border'      => '#fbcfe8',
        'avatar_bg'   => '#fce7f3',
        'avatar_text' => '#be185d',
        'badge_bg'    => '#fce7f3',
        'badge_text'  => '#9d174d',
        'btn'         => '#db2777',
        'btn_hover'   => '#be185d',
        'tag'         => 'pink',
    ],
];
@endphp

@section('content')

{{-- ── Page Header ── --}}
<div class="flex flex-col gap-2 py-4 md:flex-row md:items-center md:justify-between print:hidden">
    <div>
        <h5 class="text-16 font-semibold text-slate-800 dark:text-zink-50">
            Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }}, {{ explode(' ', auth()->user()->name)[0] }} 👋
        </h5>
        <p class="text-sm text-slate-500 dark:text-zink-300 mt-0.5">Here's an overview of your children's school transport.</p>
    </div>
    <a href="{{ route('parent.children.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white rounded-lg transition-colors"
       style="background:#2563eb;">
        <i data-lucide="plus" class="w-4 h-4"></i> Add Child
    </a>
</div>

{{-- ── Summary Stats ── --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4 flex items-center gap-3">
            <div class="flex items-center justify-center w-10 h-10 rounded-xl shrink-0" style="background:#eff6ff;">
                <i data-lucide="users" class="w-5 h-5" style="color:#2563eb;"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-slate-800 dark:text-zink-50" id="children-count">{{ $children->count() }}</p>
                <p class="text-xs text-slate-500 dark:text-zink-300">Children</p>
            </div>
        </div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4 flex items-center gap-3">
            <div class="flex items-center justify-center w-10 h-10 rounded-xl shrink-0" style="background:#ecfdf5;">
                <i data-lucide="navigation" class="w-5 h-5" style="color:#059669;"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-slate-800 dark:text-zink-50">{{ $activeTrips }}</p>
                <p class="text-xs text-slate-500 dark:text-zink-300">Active Trips</p>
            </div>
        </div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4 flex items-center gap-3">
            <div class="flex items-center justify-center w-10 h-10 rounded-xl shrink-0" style="background:#f5f3ff;">
                <i data-lucide="map" class="w-5 h-5" style="color:#7c3aed;"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-slate-800 dark:text-zink-50">{{ $totalTrips }}</p>
                <p class="text-xs text-slate-500 dark:text-zink-300">Total Trips</p>
            </div>
        </div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4 flex items-center gap-3">
            <div class="flex items-center justify-center w-10 h-10 rounded-xl shrink-0" style="background:#fffbeb;">
                <i data-lucide="star" class="w-5 h-5" style="color:#d97706;"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-slate-800 dark:text-zink-50">{{ $pendingRatings }}</p>
                <p class="text-xs text-slate-500 dark:text-zink-300">Pending Ratings</p>
            </div>
        </div>
    </div>
</div>

{{-- ── Children Cards ── --}}
@if ($children->isEmpty())
    <div class="card border-0 shadow-sm">
        <div class="card-body flex flex-col items-center justify-center py-20 text-center">
            <div class="flex items-center justify-center w-20 h-20 rounded-full mb-5" style="background:#eff6ff;">
                <i data-lucide="users" class="w-10 h-10" style="color:#2563eb;"></i>
            </div>
            <h5 class="text-lg font-bold text-slate-800 dark:text-zink-50 mb-2">No children registered yet</h5>
            <p class="text-sm text-slate-500 dark:text-zink-300 max-w-sm mb-6">
                Add your children to SafeRide Kids to start booking trusted, verified drivers for their school routes.
            </p>
            <a href="{{ route('parent.children.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-lg"
               style="background:#2563eb;">
                <i data-lucide="plus" class="w-4 h-4"></i> Add Your First Child
            </a>
        </div>
    </div>
@else
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2" id="children-grid">
        @foreach ($children as $index => $child)
        @php
            $c = $palette[$index % count($palette)];

            // Booking status
            $booking = $child->latestBooking;
            $bookingLabel = null;
            $bookingBg    = '#f1f5f9';
            $bookingColor = '#64748b';
            if ($booking) {
                if ($booking->status === \App\Models\BookingRequest::STATUS_APPROVED) {
                    $bookingLabel = 'Driver Confirmed';
                    $bookingBg    = '#dcfce7';
                    $bookingColor = '#15803d';
                } elseif ($booking->status === \App\Models\BookingRequest::STATUS_PENDING) {
                    $bookingLabel = 'Booking Pending';
                    $bookingBg    = '#fef9c3';
                    $bookingColor = '#a16207';
                } elseif ($booking->status === \App\Models\BookingRequest::STATUS_DECLINED) {
                    $bookingLabel = 'Booking Declined';
                    $bookingBg    = '#fee2e2';
                    $bookingColor = '#b91c1c';
                }
            }
        @endphp

        <div class="card border-0 shadow-sm overflow-hidden hover:shadow-md transition-shadow child-card"
             data-search="{{ strtolower($child->first_name . ' ' . $child->last_name . ' ' . ($child->school?->name ?? '') . ' ' . ($child->pickupLocation?->name ?? '') . ' ' . ($child->grade ?? '') . ' ' . ($child->assignedDriver?->name ?? '')) }}"
             style="border-top: 4px solid {{ $c['accent'] }};">
            <div class="card-body p-0">

                {{-- ── Card Header ── --}}
                <div class="flex items-start justify-between p-5 pb-4" style="background:{{ $c['bg'] }};">
                    <div class="flex items-center gap-4">
                        {{-- Avatar --}}
                        <div class="flex items-center justify-center w-14 h-14 rounded-2xl text-xl font-bold shrink-0"
                             style="background:{{ $c['avatar_bg'] }}; color:{{ $c['avatar_text'] }};">
                            {{ strtoupper(substr($child->first_name, 0, 1)) }}
                        </div>
                        <div>
                            <h5 class="text-base font-bold text-slate-800 dark:text-zink-50">
                                {{ $child->first_name }} {{ $child->last_name }}
                            </h5>
                            <div class="flex items-center gap-2 mt-1 flex-wrap">
                                @if($child->date_of_birth)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium"
                                      style="background:{{ $c['badge_bg'] }}; color:{{ $c['badge_text'] }};">
                                    {{ \Carbon\Carbon::parse($child->date_of_birth)->age }} yrs
                                </span>
                                @endif
                                @if($child->grade)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium"
                                      style="background:{{ $c['badge_bg'] }}; color:{{ $c['badge_text'] }};">
                                    Grade {{ $child->grade }}
                                </span>
                                @endif
                                {{-- Active trip pulse --}}
                                @if($child->activeTrip)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-semibold bg-green-100 text-green-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse inline-block"></span> On Trip
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    {{-- Booking status badge --}}
                    @if($bookingLabel)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold shrink-0"
                          style="background:{{ $bookingBg }}; color:{{ $bookingColor }};">
                        {{ $bookingLabel }}
                    </span>
                    @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-500 shrink-0">
                        No Booking
                    </span>
                    @endif
                </div>

                {{-- ── Info Grid ── --}}
                <div class="grid grid-cols-2 gap-0 divide-x divide-y divide-slate-100 dark:divide-zink-600 border-t border-slate-100 dark:border-zink-600">
                    <div class="p-4">
                        <p class="text-xs text-slate-400 dark:text-zink-400 mb-1 flex items-center gap-1">
                            <i data-lucide="school" class="w-3 h-3"></i> School
                        </p>
                        <p class="text-sm font-semibold text-slate-700 dark:text-zink-100 truncate">
                            {{ $child->school?->name ?? '—' }}
                        </p>
                    </div>
                    <div class="p-4">
                        <p class="text-xs text-slate-400 dark:text-zink-400 mb-1 flex items-center gap-1">
                            <i data-lucide="map-pin" class="w-3 h-3"></i> Pickup Area
                        </p>
                        <p class="text-sm font-semibold text-slate-700 dark:text-zink-100 truncate">
                            {{ $child->pickupLocation?->name ?? '—' }}
                        </p>
                    </div>
                    <div class="p-4">
                        <p class="text-xs text-slate-400 dark:text-zink-400 mb-1 flex items-center gap-1">
                            <i data-lucide="clock" class="w-3 h-3"></i> School Hours
                        </p>
                        <p class="text-sm font-semibold text-slate-700 dark:text-zink-100">
                            @if($child->school_start_time && $child->school_end_time)
                                {{ \Carbon\Carbon::parse($child->school_start_time)->format('g:i A') }}
                                – {{ \Carbon\Carbon::parse($child->school_end_time)->format('g:i A') }}
                            @else
                                —
                            @endif
                        </p>
                    </div>
                    <div class="p-4">
                        <p class="text-xs text-slate-400 dark:text-zink-400 mb-1 flex items-center gap-1">
                            <i data-lucide="car" class="w-3 h-3"></i> Assigned Driver
                        </p>
                        <p class="text-sm font-semibold text-slate-700 dark:text-zink-100 truncate">
                            {{ $child->assignedDriver?->name ?? '—' }}
                        </p>
                    </div>
                </div>

                {{-- ── Trip Stats Bar ── --}}
                <div class="flex items-center gap-6 px-5 py-3 border-t border-slate-100 dark:border-zink-600"
                     style="background:{{ $c['bg'] }};">
                    <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-zink-300">
                        <i data-lucide="check-circle" class="w-3.5 h-3.5" style="color:{{ $c['accent'] }};"></i>
                        <span><strong class="text-slate-700 dark:text-zink-100">{{ $child->completedTrips }}</strong> completed</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-zink-300">
                        <i data-lucide="map" class="w-3.5 h-3.5" style="color:{{ $c['accent'] }};"></i>
                        <span><strong class="text-slate-700 dark:text-zink-100">{{ $child->totalTrips }}</strong> total trips</span>
                    </div>
                    @if($child->latestCompletedTrip && !$child->hasRating)
                    <div class="flex items-center gap-1.5 text-xs text-amber-600 font-medium ml-auto">
                        <i data-lucide="star" class="w-3.5 h-3.5"></i> Rate last trip
                    </div>
                    @endif
                </div>

                {{-- ── Action Buttons ── --}}
                <div class="flex flex-wrap items-center gap-2 px-5 py-4 border-t border-slate-100 dark:border-zink-600">

                    @if($child->activeTrip)
                    <a href="{{ route('parent.trips.show', $child->activeTrip->id) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-white rounded-lg animate-pulse"
                       style="background:#16a34a;">
                        <i data-lucide="navigation" class="w-3.5 h-3.5"></i> Track Live
                    </a>
                    @endif

                    <a href="{{ route('parent.children.drivers.show', $child->id) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-white rounded-lg transition-colors"
                       style="background:{{ $c['btn'] }};">
                        <i data-lucide="user-check" class="w-3.5 h-3.5"></i>
                        {{ $bookingLabel ? 'Change Driver' : 'Select Driver' }}
                    </a>

                    <a href="{{ route('parent.children.trips.index', $child->id) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 dark:bg-zink-700 dark:text-zink-200 dark:border-zink-600 transition-colors">
                        <i data-lucide="list" class="w-3.5 h-3.5"></i> Trip History
                    </a>

                    <a href="{{ route('parent.children.edit', $child->id) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 dark:bg-zink-700 dark:text-zink-200 dark:border-zink-600 transition-colors">
                        <i data-lucide="edit-2" class="w-3.5 h-3.5"></i> Edit
                    </a>

                    <a href="{{ route('parent.absences.index', $child->id) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 rounded-lg hover:bg-amber-100 dark:bg-amber-500/10 dark:border-amber-500/30 dark:text-amber-400 transition-colors">
                        <i data-lucide="calendar-x" class="w-3.5 h-3.5"></i> Report Absence
                    </a>

                    @if($child->latestCompletedTrip && !$child->hasRating)
                    <a href="{{ route('parent.trips.rate.create', $child->latestCompletedTrip->id) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 rounded-lg hover:bg-amber-100 transition-colors ml-auto">
                        <i data-lucide="star" class="w-3.5 h-3.5"></i> Rate Trip
                    </a>
                    @endif

                </div>

            </div>
        </div>
        @endforeach
    </div>
@endif

@endsection

@push('scripts')
<script>
(function () {
    const input = document.getElementById('topbar-search');
    if (!input) return;

    // Update placeholder to be contextual
    input.placeholder = 'Search children, schools, drivers…';

    const grid        = document.getElementById('children-grid');
    const emptyState  = document.getElementById('search-empty');

    // Create a "no results" message node
    const noResults = document.createElement('div');
    noResults.id = 'search-empty';
    noResults.className = 'col-span-2 hidden';
    noResults.innerHTML = `
        <div class="card border-0 shadow-sm">
            <div class="card-body flex flex-col items-center justify-center py-14 text-slate-400 dark:text-zink-400">
                <i data-lucide="search-x" class="w-10 h-10 mb-3 opacity-30"></i>
                <p class="text-sm font-medium text-slate-500 dark:text-zink-300">No children match your search</p>
                <p class="text-xs mt-1" id="search-term-label"></p>
            </div>
        </div>`;
    if (grid) grid.appendChild(noResults);

    input.addEventListener('input', function () {
        const query = this.value.trim().toLowerCase();
        const cards = document.querySelectorAll('.child-card');
        let visible = 0;

        cards.forEach(card => {
            const haystack = card.dataset.search || '';
            const match = !query || haystack.includes(query);
            card.style.display = match ? '' : 'none';
            if (match) visible++;
        });

        // Show/hide no-results message
        const noResultsEl = document.getElementById('search-empty');
        if (noResultsEl) {
            noResultsEl.classList.toggle('hidden', visible > 0 || !query);
            const label = document.getElementById('search-term-label');
            if (label) label.textContent = query ? `No results for "${query}"` : '';
            // Re-init lucide icons inside the injected node
            if (visible === 0 && query && typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }

        // Update the summary stat card (children count)
        const countEl = document.querySelector('#children-count');
        if (countEl) {
            countEl.textContent = query ? visible : {{ $children->count() }};
        }
    });

    // Clear search on Escape
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            this.value = '';
            this.dispatchEvent(new Event('input'));
            this.blur();
        }
    });
})();
</script>
@endpush
