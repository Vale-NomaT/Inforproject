@extends('layouts.master')

@section('content')
<div class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)] min-h-[calc(100vh_-_theme('spacing.header')_*_3)] group-data-[layout=horizontal]:min-h-[calc(100vh_-_theme('spacing.header')_*_1.3)]">
    <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">
        <div class="flex flex-col gap-4 mb-5 md:flex-row md:items-center md:justify-between">
            <div>
                <h5 class="text-16 font-bold text-slate-900 dark:text-zink-50">Bookings & Passengers</h5>
            </div>
            <div class="flex items-center gap-3">
                <div class="px-3 py-1.5 bg-white dark:bg-zink-700 border border-slate-200 dark:border-zink-500 rounded-md shadow-sm flex items-center gap-3">
                    <div class="flex flex-col">
                        <span class="text-[10px] uppercase font-bold text-slate-500 dark:text-zink-200 leading-none mb-1">Seats Left</span>
                        <div class="flex items-baseline gap-0.5 leading-none">
                            <span class="text-base font-bold {{ $seatsLeft > 0 ? 'text-green-500' : 'text-red-500' }}">{{ $seatsLeft }}</span>
                            <span class="text-[10px] text-slate-400">/ {{ $totalCapacity }}</span>
                        </div>
                    </div>
                    <div class="w-8 h-8 rounded-full {{ $seatsLeft > 0 ? 'bg-green-100 text-green-500 dark:bg-green-500/20' : 'bg-red-100 text-red-500 dark:bg-red-500/20' }} flex items-center justify-center">
                        <i data-lucide="armchair" class="w-4 h-4"></i>
                    </div>
                </div>

                <a href="{{ route('driver.dashboard') }}" class="text-slate-500 btn hover:text-custom-500 focus:text-custom-500 active:text-custom-500 dark:text-zink-200 dark:hover:text-custom-500">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-1 inline"></i> Back
                </a>
            </div>
        </div>

        @if (session('status'))
            <div class="px-4 py-3 mb-5 text-sm text-green-500 border border-green-200 rounded-md bg-green-50 dark:bg-green-400/20 dark:border-green-500/50 flex items-center">
                <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i> {{ session('status') }}
            </div>
        @endif

        <!-- Pending Requests Section -->
        <div class="mb-8">
            <h5 class="mb-4 text-15 font-bold text-slate-900 dark:text-zink-50">Pending Requests <span class="ml-2 inline-flex items-center justify-center w-[22px] h-[22px] text-xs font-medium rounded-full bg-custom-100 text-custom-500 dark:bg-custom-500/20 dark:text-custom-500">{{ $bookings->count() }}</span></h5>
            
            @if ($bookings->isEmpty())
                <div class="card h-[200px] flex items-center justify-center border-dashed border-2 border-slate-200 dark:border-zink-500 bg-transparent shadow-none">
                    <div class="text-center">
                        <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-zink-600 mx-auto flex items-center justify-center mb-3">
                            <i data-lucide="inbox" class="w-6 h-6 text-slate-400"></i>
                        </div>
                        <h6 class="text-14 font-semibold text-slate-900 dark:text-zink-50">No pending requests</h6>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                    @foreach ($bookings as $booking)
                        @php
                            $initials = 'CH';
                            if ($booking->child) {
                                $initials = substr($booking->child->first_name, 0, 1) . substr($booking->child->last_name, 0, 1);
                            }
                            
                            $age = 'N/A';
                            if ($booking->child && $booking->child->date_of_birth) {
                                $age = \Carbon\Carbon::parse($booking->child->date_of_birth)->age;
                            }
                            
                            $price = $booking->pricing_tier == 1 ? 28 : 45;
                        @endphp
                        <div class="card bg-white dark:bg-zink-700 border border-slate-200 dark:border-zink-500 shadow-sm rounded-lg overflow-hidden flex flex-col h-full">
                            <!-- Compact Header -->
                            <div class="p-4 border-b border-slate-100 dark:border-zink-600 flex items-center justify-between gap-2">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-full bg-custom-100 text-custom-500 flex-shrink-0 flex items-center justify-center text-sm font-bold border border-custom-200 dark:bg-custom-500/20 dark:border-custom-500/30">
                                        {{ $initials }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h6 class="text-15 font-bold text-slate-900 dark:text-zink-50 truncate" title="{{ $booking->child ? $booking->child->first_name . ' ' . $booking->child->last_name : 'Unknown' }}">
                                            {{ $booking->child ? $booking->child->first_name . ' ' . $booking->child->last_name : 'Unknown' }}
                                        </h6>
                                        <p class="text-xs text-slate-500 dark:text-zink-200 truncate">
                                            @if($age !== 'N/A') {{ $age }}y @endif
                                            @if($booking->child && $booking->child->grade) • Gr {{ $booking->child->grade }} @endif
                                        </p>
                                    </div>
                                </div>
                                <span class="flex-shrink-0 text-xs font-bold text-yellow-600 dark:text-yellow-400 bg-yellow-100 dark:bg-yellow-500/20 px-2 py-1 rounded">
                                    PENDING
                                </span>
                            </div>

                            <div class="p-4 flex-grow flex flex-col gap-4">
                                <!-- Compact Route -->
                                <div class="relative pl-10 border-l-2 border-dashed border-slate-300 dark:border-zink-500 space-y-4">
                                    <div class="relative">
                                        <span class="absolute -left-[47px] w-3 h-3 rounded-full bg-white dark:bg-zink-700 border-2 border-custom-500 box-content"></span>
                                        <p class="text-sm font-semibold text-slate-900 dark:text-zink-50 truncate" title="{{ $booking->child && $booking->child->pickupLocation ? $booking->child->pickupLocation->name : '' }}">
                                            {{ $booking->child && $booking->child->pickupLocation ? Str::limit($booking->child->pickupLocation->name, 25) : 'Unknown' }}
                                        </p>
                                        <p class="text-[10px] uppercase text-slate-500 dark:text-zink-300">Pickup</p>
                                    </div>
                                    <div class="relative">
                                        <span class="absolute -left-[47px] w-3 h-3 rounded-full bg-white dark:bg-zink-700 border-2 border-red-500 box-content"></span>
                                        <p class="text-sm font-semibold text-slate-900 dark:text-zink-50 truncate" title="{{ $booking->child && $booking->child->school ? $booking->child->school->name : '' }}">
                                            {{ $booking->child && $booking->child->school ? Str::limit($booking->child->school->name, 25) : 'Unknown' }}
                                        </p>
                                        <p class="text-[10px] uppercase text-slate-500 dark:text-zink-300">
                                            Drop-off
                                            @if($booking->child && $booking->child->school_start_time)
                                                • <span class="text-slate-600 dark:text-zink-200">{{ \Carbon\Carbon::parse($booking->child->school_start_time)->format('h:i A') }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <!-- Compact Info Grid -->
                                <div class="bg-slate-50 dark:bg-zink-600/30 rounded p-3 grid grid-cols-2 gap-2 text-xs">
                                    <div class="min-w-0">
                                        <p class="text-slate-500 dark:text-zink-300 mb-0.5">Parent</p>
                                        <p class="font-medium text-slate-900 dark:text-zink-50 truncate" title="{{ $booking->parent ? $booking->parent->name : 'N/A' }}">
                                            {{ $booking->parent ? $booking->parent->name : 'N/A' }}
                                        </p>
                                    </div>
                                    <div class="text-right min-w-0">
                                        <p class="text-slate-500 dark:text-zink-300 mb-0.5">Contact</p>
                                        @if($booking->parent && $booking->parent->parentProfile && $booking->parent->parentProfile->phone)
                                            <a href="tel:{{ $booking->parent->parentProfile->phone }}" class="text-custom-500 hover:underline truncate inline-block max-w-full">
                                                {{ $booking->parent->parentProfile->phone }}
                                            </a>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </div>
                                    <div class="col-span-2 pt-2 border-t border-slate-200 dark:border-zink-500/50 flex justify-between items-center">
                                        <span class="text-slate-500 dark:text-zink-300">Tier {{ $booking->pricing_tier }}</span>
                                        <span class="font-bold text-green-600 dark:text-green-400 text-sm">${{ $price }}/mo</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer Actions -->
                            <div class="p-3 border-t border-slate-100 dark:border-zink-600 grid grid-cols-2 gap-3 bg-slate-50/50 dark:bg-zink-700/30">
                                <form method="POST" action="{{ route('driver.bookings.decline', ['booking' => $booking->id]) }}" class="w-full">
                                    @csrf
                                    <button type="submit" class="w-full py-2 px-3 rounded text-xs font-medium text-slate-700 dark:text-zink-100 bg-white dark:bg-zink-600 border border-slate-300 dark:border-zink-500 hover:bg-slate-50 dark:hover:bg-zink-500 transition-colors">
                                        Decline
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('driver.bookings.approve', ['booking' => $booking->id]) }}" class="w-full">
                                    @csrf
                                    <button type="submit" class="w-full py-2 px-3 rounded text-xs font-medium text-white bg-custom-500 border border-custom-500 hover:bg-custom-600 hover:border-custom-600 transition-colors shadow-sm">
                                        Approve
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Approved Passengers Section -->
        <div>
            <h5 class="mb-4 text-15 font-bold text-slate-900 dark:text-zink-50">My Passengers <span class="ml-2 inline-flex items-center justify-center w-[22px] h-[22px] text-xs font-medium rounded-full bg-green-100 text-green-500 dark:bg-green-500/20 dark:text-green-500">{{ $approvedBookings->count() }}</span></h5>

            @if ($approvedBookings->isEmpty())
                <div class="card h-[200px] flex items-center justify-center border-dashed border-2 border-slate-200 dark:border-zink-500 bg-transparent shadow-none">
                    <div class="text-center">
                        <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-zink-600 mx-auto flex items-center justify-center mb-3">
                            <i data-lucide="users" class="w-6 h-6 text-slate-400"></i>
                        </div>
                        <h6 class="text-14 font-semibold text-slate-900 dark:text-zink-50">No passengers yet</h6>
                        <p class="text-slate-500 dark:text-zink-200">Approved bookings will appear here.</p>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                    @foreach ($approvedBookings as $booking)
                        @php
                            $initials = 'CH';
                            if ($booking->child) {
                                $initials = substr($booking->child->first_name, 0, 1) . substr($booking->child->last_name, 0, 1);
                            }
                            
                            $age = 'N/A';
                            if ($booking->child && $booking->child->date_of_birth) {
                                $age = \Carbon\Carbon::parse($booking->child->date_of_birth)->age;
                            }
                            
                            $price = $booking->pricing_tier == 1 ? 28 : 45;
                        @endphp
                        <div class="card bg-white dark:bg-zink-700 border border-slate-200 dark:border-zink-500 shadow-sm rounded-lg overflow-hidden flex flex-col h-full">
                            <!-- Compact Header -->
                            <div class="p-4 border-b border-slate-100 dark:border-zink-600 flex items-center justify-between gap-2">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-full bg-green-100 text-green-500 flex-shrink-0 flex items-center justify-center text-sm font-bold border border-green-200 dark:bg-green-500/20 dark:border-green-500/30">
                                        {{ $initials }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h6 class="text-15 font-bold text-slate-900 dark:text-zink-50 truncate" title="{{ $booking->child ? $booking->child->first_name . ' ' . $booking->child->last_name : 'Unknown' }}">
                                            {{ $booking->child ? $booking->child->first_name . ' ' . $booking->child->last_name : 'Unknown' }}
                                        </h6>
                                        <p class="text-xs text-slate-500 dark:text-zink-200 truncate">
                                            @if($age !== 'N/A') {{ $age }}y @endif
                                            @if($booking->child && $booking->child->grade) • Gr {{ $booking->child->grade }} @endif
                                        </p>
                                    </div>
                                </div>
                                <span class="flex-shrink-0 text-xs font-bold text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-500/20 px-2 py-1 rounded">
                                    APPROVED
                                </span>
                            </div>

                            <div class="p-4 flex-grow flex flex-col gap-4">
                                <!-- Compact Route -->
                                <div class="relative pl-10 border-l-2 border-dashed border-slate-300 dark:border-zink-500 space-y-4">
                                    <div class="relative">
                                        <span class="absolute -left-[47px] w-3 h-3 rounded-full bg-white dark:bg-zink-700 border-2 border-custom-500 box-content"></span>
                                        <p class="text-sm font-semibold text-slate-900 dark:text-zink-50 truncate" title="{{ $booking->child && $booking->child->pickupLocation ? $booking->child->pickupLocation->name : '' }}">
                                            {{ $booking->child && $booking->child->pickupLocation ? Str::limit($booking->child->pickupLocation->name, 25) : 'Unknown' }}
                                        </p>
                                        <p class="text-[10px] uppercase text-slate-500 dark:text-zink-300">Pickup</p>
                                    </div>
                                    <div class="relative">
                                        <span class="absolute -left-[47px] w-3 h-3 rounded-full bg-white dark:bg-zink-700 border-2 border-red-500 box-content"></span>
                                        <p class="text-sm font-semibold text-slate-900 dark:text-zink-50 truncate" title="{{ $booking->child && $booking->child->school ? $booking->child->school->name : '' }}">
                                            {{ $booking->child && $booking->child->school ? Str::limit($booking->child->school->name, 25) : 'Unknown' }}
                                        </p>
                                        <p class="text-[10px] uppercase text-slate-500 dark:text-zink-300">
                                            Drop-off
                                            @if($booking->child && $booking->child->school_start_time)
                                                • <span class="text-slate-600 dark:text-zink-200">{{ \Carbon\Carbon::parse($booking->child->school_start_time)->format('h:i A') }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <!-- Compact Info Grid -->
                                <div class="bg-slate-50 dark:bg-zink-600/30 rounded p-3 grid grid-cols-2 gap-2 text-xs">
                                    <div class="min-w-0">
                                        <p class="text-slate-500 dark:text-zink-300 mb-0.5">Parent</p>
                                        <p class="font-medium text-slate-900 dark:text-zink-50 truncate" title="{{ $booking->parent ? $booking->parent->name : 'N/A' }}">
                                            {{ $booking->parent ? $booking->parent->name : 'N/A' }}
                                        </p>
                                    </div>
                                    <div class="text-right min-w-0">
                                        <p class="text-slate-500 dark:text-zink-300 mb-0.5">Contact</p>
                                        @if($booking->parent && $booking->parent->parentProfile && $booking->parent->parentProfile->phone)
                                            <a href="tel:{{ $booking->parent->parentProfile->phone }}" class="text-custom-500 hover:underline truncate inline-block max-w-full">
                                                {{ $booking->parent->parentProfile->phone }}
                                            </a>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </div>
                                    <div class="col-span-2 pt-2 border-t border-slate-200 dark:border-zink-500/50 flex justify-between items-center">
                                        <span class="text-slate-500 dark:text-zink-300">Tier {{ $booking->pricing_tier }}</span>
                                        <span class="font-bold text-green-600 dark:text-green-400 text-sm">${{ $price }}/mo</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection