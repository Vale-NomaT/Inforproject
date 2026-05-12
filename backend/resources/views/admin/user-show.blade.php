@extends('layouts.master')

@section('title', 'User Details – ' . $user->name)

@section('content')

{{-- Breadcrumb --}}
<div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
    <div class="grow">
        <h5 class="text-16 font-semibold text-slate-800 dark:text-zink-50">User Details</h5>
        <p class="text-slate-500 dark:text-zink-300 text-sm mt-0.5">
            <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:underline dark:text-blue-400">All Users</a>
            <span class="mx-1.5 text-slate-300 dark:text-zink-500">/</span>
            {{ $user->name }}
        </p>
    </div>
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 dark:bg-zink-700 dark:border-zink-600 dark:text-zink-200 dark:hover:bg-zink-600 transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Users
    </a>
</div>

<div class="grid grid-cols-1 gap-5 xl:grid-cols-3">

    {{-- ── LEFT: Profile Card ── --}}
    <div class="flex flex-col gap-5">

        {{-- Identity card --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-6">
                {{-- Avatar + name --}}
                <div class="flex flex-col items-center text-center mb-5">
                    <div class="flex items-center justify-center w-20 h-20 rounded-full text-2xl font-bold mb-3
                        @if($user->user_type === 'driver') bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-300
                        @elseif($user->user_type === 'parent') bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300
                        @else bg-slate-100 text-slate-600 dark:bg-zink-600 dark:text-zink-200 @endif">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <h5 class="text-lg font-bold text-slate-800 dark:text-zink-50">{{ $user->name }}</h5>
                    <p class="text-sm text-slate-500 dark:text-zink-300">{{ $user->email }}</p>
                    <div class="flex items-center gap-2 mt-2">
                        {{-- Type badge --}}
                        @php
                            $typeColor = match($user->user_type) {
                                'driver' => 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-400',
                                'parent' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400',
                                default  => 'bg-slate-100 text-slate-600 dark:bg-zink-600 dark:text-zink-300',
                            };
                        @endphp
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $typeColor }}">
                            {{ ucfirst($user->user_type) }}
                        </span>
                        {{-- Status badge --}}
                        @if($user->status === 'active')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span> Active
                            </span>
                        @elseif($user->status === 'pending')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 inline-block"></span> Pending
                            </span>
                        @elseif($user->status === 'suspended')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 inline-block"></span> Suspended
                            </span>
                        @else
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-500 dark:bg-zink-600 dark:text-zink-300">{{ ucfirst($user->status) }}</span>
                        @endif
                    </div>
                </div>

                {{-- Basic info rows --}}
                <div class="divide-y divide-slate-100 dark:divide-zink-600">
                    <div class="flex justify-between py-2.5 text-sm">
                        <span class="text-slate-500 dark:text-zink-400">Joined</span>
                        <span class="font-medium text-slate-700 dark:text-zink-100">{{ $user->created_at?->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between py-2.5 text-sm">
                        <span class="text-slate-500 dark:text-zink-400">Email verified</span>
                        <span class="font-medium text-slate-700 dark:text-zink-100">
                            {{ $user->email_verified_at ? $user->email_verified_at->format('d M Y') : '—' }}
                        </span>
                    </div>
                    <div class="flex justify-between py-2.5 text-sm">
                        <span class="text-slate-500 dark:text-zink-400">Total trips</span>
                        <span class="font-medium text-slate-700 dark:text-zink-100">{{ $tripCount }}</span>
                    </div>
                    @if($user->user_type === 'driver' && $avgRating)
                    <div class="flex justify-between py-2.5 text-sm">
                        <span class="text-slate-500 dark:text-zink-400">Avg rating</span>
                        <span class="font-medium text-amber-500">★ {{ number_format($avgRating, 1) }} / 5</span>
                    </div>
                    @endif
                    @if($user->status_reason)
                    <div class="flex justify-between py-2.5 text-sm">
                        <span class="text-slate-500 dark:text-zink-400">Status reason</span>
                        <span class="font-medium text-red-600 dark:text-red-400 text-right max-w-[60%]">{{ $user->status_reason }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-5">
                <h6 class="text-sm font-semibold text-slate-700 dark:text-zink-100 mb-3">Actions</h6>

                @if($user->user_type === 'driver' && $user->status === 'pending')
                <form method="POST" action="{{ route('admin.drivers.approve', $user) }}" class="mb-2">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                        <i data-lucide="check-circle" class="w-4 h-4"></i> Approve Driver
                    </button>
                </form>
                @endif

                @if($user->status !== 'suspended')
                <form method="POST" action="{{ route('admin.users.suspend', $user) }}" class="flex flex-col gap-2">
                    @csrf
                    <input type="text" name="reason" placeholder="Reason for suspension…"
                        class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg dark:bg-zink-700 dark:border-zink-600 dark:text-zink-100 focus:outline-none focus:border-blue-400">
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-600 transition-colors">
                        <i data-lucide="ban" class="w-4 h-4"></i> Suspend User
                    </button>
                </form>
                @else
                <p class="text-sm text-center text-slate-400 dark:text-zink-400 py-2">This user is suspended.</p>
                @endif
            </div>
        </div>

    </div>

    {{-- ── RIGHT: Role-specific details ── --}}
    <div class="xl:col-span-2 flex flex-col gap-5">

        {{-- ════ DRIVER DETAILS ════ --}}
        @if($user->user_type === 'driver')
            @php $profile = $user->driverProfile; @endphp

            {{-- Vehicle --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="flex items-center gap-3 px-5 pt-5 pb-4 border-b border-slate-100 dark:border-zink-600">
                        <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-purple-100 dark:bg-purple-500/20">
                            <i data-lucide="car" class="w-4 h-4 text-purple-600 dark:text-purple-400"></i>
                        </div>
                        <div>
                            <h6 class="text-15 font-semibold text-slate-800 dark:text-zink-50">Vehicle Information</h6>
                            <p class="text-xs text-slate-400 dark:text-zink-400">Registered vehicle details</p>
                        </div>
                    </div>
                    @if($profile)
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-0 divide-x divide-y divide-slate-100 dark:divide-zink-600">
                        @php
                            $vehicleFields = [
                                ['label' => 'Make', 'value' => $profile->vehicle_make],
                                ['label' => 'Model', 'value' => $profile->vehicle_model],
                                ['label' => 'Year', 'value' => $profile->vehicle_year],
                                ['label' => 'Color', 'value' => $profile->vehicle_color],
                                ['label' => 'License Plate', 'value' => $profile->license_plate],
                                ['label' => 'Vehicle Type', 'value' => ucfirst($profile->vehicle_type ?? '—')],
                                ['label' => 'Max Capacity', 'value' => ($profile->max_child_capacity ?? '—') . ' children'],
                                ['label' => 'License No.', 'value' => $profile->license_number],
                                ['label' => 'Gov ID No.', 'value' => $profile->gov_id_number],
                            ];
                        @endphp
                        @foreach($vehicleFields as $field)
                        <div class="px-5 py-3.5">
                            <p class="text-xs text-slate-400 dark:text-zink-400 mb-0.5">{{ $field['label'] }}</p>
                            <p class="text-sm font-semibold text-slate-700 dark:text-zink-100">{{ $field['value'] ?? '—' }}</p>
                        </div>
                        @endforeach
                    </div>

                    {{-- Documents --}}
                    @if($profile->license_file_path || $profile->vehicle_registration_file_path || $profile->gov_id_file_path)
                    <div class="px-5 py-4 border-t border-slate-100 dark:border-zink-600">
                        <p class="text-xs font-semibold text-slate-500 dark:text-zink-300 uppercase tracking-wider mb-3">Uploaded Documents</p>
                        <div class="flex flex-wrap gap-2">
                            @if($profile->license_file_path)
                            <a href="{{ Storage::url($profile->license_file_path) }}" target="_blank"
                                class="inline-flex items-center gap-2 px-3 py-2 text-xs font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 dark:bg-blue-500/10 dark:border-blue-500/30 dark:text-blue-400 transition-colors">
                                <i data-lucide="file-text" class="w-3.5 h-3.5"></i> Driver's License
                            </a>
                            @endif
                            @if($profile->vehicle_registration_file_path)
                            <a href="{{ Storage::url($profile->vehicle_registration_file_path) }}" target="_blank"
                                class="inline-flex items-center gap-2 px-3 py-2 text-xs font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 dark:bg-blue-500/10 dark:border-blue-500/30 dark:text-blue-400 transition-colors">
                                <i data-lucide="car" class="w-3.5 h-3.5"></i> Vehicle Registration
                            </a>
                            @endif
                            @if($profile->gov_id_file_path)
                            <a href="{{ Storage::url($profile->gov_id_file_path) }}" target="_blank"
                                class="inline-flex items-center gap-2 px-3 py-2 text-xs font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 dark:bg-blue-500/10 dark:border-blue-500/30 dark:text-blue-400 transition-colors">
                                <i data-lucide="credit-card" class="w-3.5 h-3.5"></i> Government ID
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    @else
                    <div class="flex flex-col items-center justify-center py-10 text-slate-400 dark:text-zink-400">
                        <i data-lucide="car" class="w-8 h-8 mb-2 opacity-30"></i>
                        <p class="text-sm">No vehicle profile found</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Service Areas --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="flex items-center gap-3 px-5 pt-5 pb-4 border-b border-slate-100 dark:border-zink-600">
                        <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-emerald-100 dark:bg-emerald-500/20">
                            <i data-lucide="map-pin" class="w-4 h-4 text-emerald-600 dark:text-emerald-400"></i>
                        </div>
                        <div>
                            <h6 class="text-15 font-semibold text-slate-800 dark:text-zink-50">Service Areas &amp; Schools</h6>
                            <p class="text-xs text-slate-400 dark:text-zink-400">Pickup areas and schools this driver covers</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x divide-slate-100 dark:divide-zink-600">
                        {{-- Pickup Locations --}}
                        <div class="p-5">
                            <p class="text-xs font-semibold text-slate-500 dark:text-zink-300 uppercase tracking-wider mb-3">Pickup Locations</p>
                            @if($profile && $profile->locations->count())
                                <div class="flex flex-col gap-2">
                                    @foreach($profile->locations as $loc)
                                    <div class="flex items-center gap-2.5 p-2.5 rounded-lg bg-slate-50 dark:bg-zink-700/50">
                                        <div class="flex items-center justify-center w-7 h-7 rounded-md bg-emerald-100 dark:bg-emerald-500/20 shrink-0">
                                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-slate-700 dark:text-zink-100">{{ $loc->name }}</p>
                                            <p class="text-xs text-slate-400 dark:text-zink-400">{{ $loc->city ?? '' }}</p>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-slate-400 dark:text-zink-400 italic">No pickup areas assigned</p>
                            @endif
                        </div>
                        {{-- Schools --}}
                        <div class="p-5">
                            <p class="text-xs font-semibold text-slate-500 dark:text-zink-300 uppercase tracking-wider mb-3">Schools Served</p>
                            @if($profile && $profile->schools->count())
                                <div class="flex flex-col gap-2">
                                    @foreach($profile->schools as $school)
                                    <div class="flex items-center gap-2.5 p-2.5 rounded-lg bg-slate-50 dark:bg-zink-700/50">
                                        <div class="flex items-center justify-center w-7 h-7 rounded-md bg-blue-100 dark:bg-blue-500/20 shrink-0">
                                            <i data-lucide="school" class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-slate-700 dark:text-zink-100">{{ $school->name }}</p>
                                            <p class="text-xs text-slate-400 dark:text-zink-400">{{ $school->city ?? '' }}</p>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-slate-400 dark:text-zink-400 italic">No schools assigned</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        {{-- ════ PARENT DETAILS ════ --}}
        @elseif($user->user_type === 'parent')
            @php $parentProfile = $user->parentProfile; @endphp

            {{-- Parent contact info --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="flex items-center gap-3 px-5 pt-5 pb-4 border-b border-slate-100 dark:border-zink-600">
                        <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-500/20">
                            <i data-lucide="user" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div>
                            <h6 class="text-15 font-semibold text-slate-800 dark:text-zink-50">Parent Profile</h6>
                            <p class="text-xs text-slate-400 dark:text-zink-400">Contact and address information</p>
                        </div>
                    </div>
                    @if($parentProfile)
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-0 divide-x divide-y divide-slate-100 dark:divide-zink-600">
                        @php
                            $parentFields = [
                                ['label' => 'Phone', 'value' => $parentProfile->phone],
                                ['label' => 'Secondary Phone', 'value' => $parentProfile->secondary_phone],
                                ['label' => 'Relationship', 'value' => $parentProfile->relationship_to_child],
                                ['label' => 'Street', 'value' => $parentProfile->address_street],
                                ['label' => 'City', 'value' => $parentProfile->address_city],
                                ['label' => 'Country', 'value' => $parentProfile->address_country],
                            ];
                        @endphp
                        @foreach($parentFields as $field)
                        <div class="px-5 py-3.5">
                            <p class="text-xs text-slate-400 dark:text-zink-400 mb-0.5">{{ $field['label'] }}</p>
                            <p class="text-sm font-semibold text-slate-700 dark:text-zink-100">{{ $field['value'] ?? '—' }}</p>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="flex flex-col items-center justify-center py-10 text-slate-400 dark:text-zink-400">
                        <i data-lucide="user" class="w-8 h-8 mb-2 opacity-30"></i>
                        <p class="text-sm">No parent profile found</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Children --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="flex items-center justify-between px-5 pt-5 pb-4 border-b border-slate-100 dark:border-zink-600">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-pink-100 dark:bg-pink-500/20">
                                <i data-lucide="users" class="w-4 h-4 text-pink-600 dark:text-pink-400"></i>
                            </div>
                            <div>
                                <h6 class="text-15 font-semibold text-slate-800 dark:text-zink-50">Children</h6>
                                <p class="text-xs text-slate-400 dark:text-zink-400">Registered children under this parent</p>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-slate-500 dark:text-zink-300">
                            {{ $parentProfile?->children->count() ?? 0 }}
                        </span>
                    </div>

                    @if($parentProfile && $parentProfile->children->count())
                        <div class="divide-y divide-slate-100 dark:divide-zink-600">
                            @foreach($parentProfile->children as $child)
                            <div class="p-5">
                                <div class="flex items-start gap-4">
                                    <div class="flex items-center justify-center w-11 h-11 rounded-full bg-pink-100 dark:bg-pink-500/20 text-pink-700 dark:text-pink-300 font-bold text-base shrink-0">
                                        {{ strtoupper(substr($child->first_name, 0, 1)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-3">
                                            <h6 class="font-semibold text-slate-800 dark:text-zink-50">{{ $child->first_name }} {{ $child->last_name }}</h6>
                                            @if($child->grade)
                                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-600 dark:bg-zink-600 dark:text-zink-300">Grade {{ $child->grade }}</span>
                                            @endif
                                        </div>
                                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-2">
                                            <div>
                                                <p class="text-xs text-slate-400 dark:text-zink-400">Date of Birth</p>
                                                <p class="text-sm font-medium text-slate-700 dark:text-zink-100">{{ $child->date_of_birth ?? '—' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-400 dark:text-zink-400">Relationship</p>
                                                <p class="text-sm font-medium text-slate-700 dark:text-zink-100">{{ $child->relationship ?? '—' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-400 dark:text-zink-400">School</p>
                                                <p class="text-sm font-medium text-slate-700 dark:text-zink-100">{{ $child->school?->name ?? '—' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-400 dark:text-zink-400">School Start</p>
                                                <p class="text-sm font-medium text-slate-700 dark:text-zink-100">{{ $child->school_start_time ?? '—' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-400 dark:text-zink-400">School End</p>
                                                <p class="text-sm font-medium text-slate-700 dark:text-zink-100">{{ $child->school_end_time ?? '—' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-400 dark:text-zink-400">Pickup Area</p>
                                                <p class="text-sm font-medium text-slate-700 dark:text-zink-100">{{ $child->pickupLocation?->name ?? '—' }}</p>
                                            </div>
                                            @if($child->pickup_address)
                                            <div class="col-span-2 sm:col-span-3">
                                                <p class="text-xs text-slate-400 dark:text-zink-400">Pickup Address</p>
                                                <p class="text-sm font-medium text-slate-700 dark:text-zink-100">{{ $child->pickup_address }}</p>
                                            </div>
                                            @endif
                                            @if($child->medical_notes)
                                            <div class="col-span-2 sm:col-span-3">
                                                <p class="text-xs text-slate-400 dark:text-zink-400">Medical Notes</p>
                                                <p class="text-sm font-medium text-red-600 dark:text-red-400">{{ $child->medical_notes }}</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-10 text-slate-400 dark:text-zink-400">
                            <i data-lucide="users" class="w-8 h-8 mb-2 opacity-30"></i>
                            <p class="text-sm">No children registered yet</p>
                        </div>
                    @endif
                </div>
            </div>

        {{-- ════ ADMIN DETAILS ════ --}}
        @else
            <div class="card border-0 shadow-sm">
                <div class="card-body flex flex-col items-center justify-center py-16 text-slate-400 dark:text-zink-400">
                    <i data-lucide="shield" class="w-12 h-12 mb-3 opacity-30"></i>
                    <p class="text-base font-medium text-slate-600 dark:text-zink-200">Administrator Account</p>
                    <p class="text-sm mt-1">This user has full platform access.</p>
                </div>
            </div>
        @endif

    </div>{{-- end right col --}}
</div>

@endsection
