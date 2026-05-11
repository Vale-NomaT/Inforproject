@extends('layouts.master')

@section('title', 'Admin Dashboard')

@section('content')

{{-- Page Header --}}
<div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
    <div class="grow">
        <h5 class="text-16 font-semibold text-slate-800 dark:text-zink-50">Admin Dashboard</h5>
        <p class="text-slate-500 dark:text-zink-300 text-sm mt-0.5">Welcome back, {{ auth()->user()->name }}. Here's what's happening today.</p>
    </div>
    <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-zink-300">
        <i data-lucide="calendar" class="w-4 h-4"></i>
        {{ now()->format('l, d F Y') }}
    </div>
</div>

{{-- KPI Stats Row --}}
<div class="grid grid-cols-2 gap-4 mb-5 sm:grid-cols-2 xl:grid-cols-4">

    {{-- Total Users --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center justify-center w-11 h-11 rounded-xl bg-blue-100 dark:bg-blue-500/20">
                    <i data-lucide="users" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                </div>
                <span class="text-xs font-medium px-2 py-1 rounded-full bg-green-100 text-green-600 dark:bg-green-500/20 dark:text-green-400">All time</span>
            </div>
            <h4 class="text-2xl font-bold text-slate-800 dark:text-zink-50 mb-1">{{ number_format($totalUsers) }}</h4>
            <p class="text-sm text-slate-500 dark:text-zink-300">Total Users</p>
            <div class="flex gap-3 mt-2 text-xs text-slate-400 dark:text-zink-400">
                <span>{{ $totalParents }} parents</span>
                <span>·</span>
                <span>{{ $totalDrivers }} drivers</span>
            </div>
        </div>
    </div>

    {{-- Active Trips --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center justify-center w-11 h-11 rounded-xl bg-emerald-100 dark:bg-emerald-500/20">
                    <i data-lucide="navigation" class="w-5 h-5 text-emerald-600 dark:text-emerald-400"></i>
                </div>
                @if($activeTrips > 0)
                    <span class="flex items-center gap-1 text-xs font-medium px-2 py-1 rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse inline-block"></span> Live
                    </span>
                @endif
            </div>
            <h4 class="text-2xl font-bold text-slate-800 dark:text-zink-50 mb-1">{{ $activeTrips }}</h4>
            <p class="text-sm text-slate-500 dark:text-zink-300">Active Trips</p>
            <p class="text-xs text-slate-400 dark:text-zink-400 mt-2">{{ $completedTrips }} completed total</p>
        </div>
    </div>

    {{-- Pending Drivers --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center justify-center w-11 h-11 rounded-xl bg-amber-100 dark:bg-amber-500/20">
                    <i data-lucide="user-check" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
                </div>
                @if($pendingDrivers > 0)
                    <span class="text-xs font-medium px-2 py-1 rounded-full bg-amber-100 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400">Needs review</span>
                @endif
            </div>
            <h4 class="text-2xl font-bold text-slate-800 dark:text-zink-50 mb-1">{{ $pendingDrivers }}</h4>
            <p class="text-sm text-slate-500 dark:text-zink-300">Pending Drivers</p>
            <a href="{{ route('admin.drivers.pending') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline mt-2 inline-block">Review applications →</a>
        </div>
    </div>

    {{-- Schools --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center justify-center w-11 h-11 rounded-xl bg-purple-100 dark:bg-purple-500/20">
                    <i data-lucide="school" class="w-5 h-5 text-purple-600 dark:text-purple-400"></i>
                </div>
            </div>
            <h4 class="text-2xl font-bold text-slate-800 dark:text-zink-50 mb-1">{{ $totalSchools }}</h4>
            <p class="text-sm text-slate-500 dark:text-zink-300">Registered Schools</p>
            <a href="{{ route('admin.reports.unserviced') }}" class="text-xs text-purple-600 dark:text-purple-400 hover:underline mt-2 inline-block">View unserviced →</a>
        </div>
    </div>

</div>

{{-- Main Content Grid --}}
<div class="grid grid-cols-1 gap-5 xl:grid-cols-3">

    {{-- Recent Trips (2/3 width) --}}
    <div class="xl:col-span-2">
        <div class="card border-0 shadow-sm h-full">
            <div class="card-body p-0">
                <div class="flex items-center justify-between px-5 pt-5 pb-4 border-b border-slate-100 dark:border-zink-600">
                    <div>
                        <h6 class="text-15 font-semibold text-slate-800 dark:text-zink-50">Recent Trips</h6>
                        <p class="text-xs text-slate-400 dark:text-zink-400 mt-0.5">Latest trip activity across all drivers</p>
                    </div>
                    <a href="{{ route('admin.reports.trips') }}" class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                        Export CSV <i data-lucide="download" class="w-3 h-3"></i>
                    </a>
                </div>
                <div class="overflow-x-auto">
                    @if($recentTrips->isEmpty())
                        <div class="flex flex-col items-center justify-center py-12 text-slate-400 dark:text-zink-400">
                            <i data-lucide="map" class="w-10 h-10 mb-3 opacity-40"></i>
                            <p class="text-sm">No trips recorded yet</p>
                        </div>
                    @else
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-zink-700/50">
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zink-300 uppercase tracking-wider">Driver</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zink-300 uppercase tracking-wider">Child / School</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zink-300 uppercase tracking-wider">Date</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zink-300 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-zink-600">
                                @foreach($recentTrips as $trip)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-zink-700/30 transition-colors">
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-2.5">
                                            <div class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 text-xs font-bold shrink-0">
                                                {{ strtoupper(substr($trip->driver?->name ?? '?', 0, 1)) }}
                                            </div>
                                            <span class="font-medium text-slate-700 dark:text-zink-100 text-sm">{{ $trip->driver?->name ?? '—' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="text-slate-700 dark:text-zink-100 text-sm">{{ $trip->child ? $trip->child->first_name.' '.$trip->child->last_name : '—' }}</div>
                                        <div class="text-xs text-slate-400 dark:text-zink-400">{{ $trip->child?->school?->name ?? '—' }}</div>
                                    </td>
                                    <td class="px-5 py-3.5 text-slate-500 dark:text-zink-300 text-sm whitespace-nowrap">
                                        {{ $trip->scheduled_date?->format('d M Y') ?? '—' }}
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @if($trip->status === 'completed')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span> Completed
                                            </span>
                                        @elseif($trip->status === 'in_progress')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse inline-block"></span> In Progress
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600 dark:bg-zink-600 dark:text-zink-300">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400 inline-block"></span> {{ ucfirst($trip->status) }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Right Column --}}
    <div class="flex flex-col gap-5">

        {{-- Quick Actions --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-5">
                <h6 class="text-15 font-semibold text-slate-800 dark:text-zink-50 mb-4">Quick Actions</h6>
                <div class="flex flex-col gap-2">
                    <a href="{{ route('admin.drivers.pending') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-50 dark:hover:bg-zink-700 transition-colors group">
                        <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-amber-100 dark:bg-amber-500/20 shrink-0">
                            <i data-lucide="user-check" class="w-4 h-4 text-amber-600 dark:text-amber-400"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-700 dark:text-zink-100">Pending Drivers</p>
                            <p class="text-xs text-slate-400 dark:text-zink-400">{{ $pendingDrivers }} awaiting review</p>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 dark:text-zink-500 group-hover:text-slate-500 dark:group-hover:text-zink-300 transition-colors shrink-0"></i>
                    </a>
                    <a href="{{ route('admin.live-tracking') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-50 dark:hover:bg-zink-700 transition-colors group">
                        <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 shrink-0">
                            <i data-lucide="map-pin" class="w-4 h-4 text-emerald-600 dark:text-emerald-400"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-700 dark:text-zink-100">Live Tracking</p>
                            <p class="text-xs text-slate-400 dark:text-zink-400">{{ $activeTrips }} trips in progress</p>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 dark:text-zink-500 group-hover:text-slate-500 dark:group-hover:text-zink-300 transition-colors shrink-0"></i>
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-50 dark:hover:bg-zink-700 transition-colors group">
                        <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-500/20 shrink-0">
                            <i data-lucide="users" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-700 dark:text-zink-100">Manage Users</p>
                            <p class="text-xs text-slate-400 dark:text-zink-400">{{ $totalUsers }} registered users</p>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 dark:text-zink-500 group-hover:text-slate-500 dark:group-hover:text-zink-300 transition-colors shrink-0"></i>
                    </a>
                    <a href="{{ route('admin.reports.unserviced') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-50 dark:hover:bg-zink-700 transition-colors group">
                        <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-red-100 dark:bg-red-500/20 shrink-0">
                            <i data-lucide="alert-triangle" class="w-4 h-4 text-red-500 dark:text-red-400"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-700 dark:text-zink-100">Unserviced Areas</p>
                            <p class="text-xs text-slate-400 dark:text-zink-400">Schools &amp; areas with no drivers</p>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 dark:text-zink-500 group-hover:text-slate-500 dark:group-hover:text-zink-300 transition-colors shrink-0"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Reports --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-5">
                <h6 class="text-15 font-semibold text-slate-800 dark:text-zink-50 mb-4">Export Reports</h6>
                <div class="flex flex-col gap-2">
                    <a href="{{ route('admin.reports.trips') }}" class="flex items-center justify-between p-3 rounded-lg border border-slate-100 dark:border-zink-600 hover:border-blue-300 dark:hover:border-blue-500 hover:bg-blue-50/50 dark:hover:bg-blue-500/10 transition-colors group">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="file-text" class="w-4 h-4 text-slate-400 dark:text-zink-400 group-hover:text-blue-500 transition-colors"></i>
                            <span class="text-sm text-slate-600 dark:text-zink-200">Trips Report</span>
                        </div>
                        <i data-lucide="download" class="w-3.5 h-3.5 text-slate-300 dark:text-zink-500 group-hover:text-blue-500 transition-colors"></i>
                    </a>
                    <a href="{{ route('admin.reports.signups') }}" class="flex items-center justify-between p-3 rounded-lg border border-slate-100 dark:border-zink-600 hover:border-blue-300 dark:hover:border-blue-500 hover:bg-blue-50/50 dark:hover:bg-blue-500/10 transition-colors group">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="user-plus" class="w-4 h-4 text-slate-400 dark:text-zink-400 group-hover:text-blue-500 transition-colors"></i>
                            <span class="text-sm text-slate-600 dark:text-zink-200">New Signups</span>
                        </div>
                        <i data-lucide="download" class="w-3.5 h-3.5 text-slate-300 dark:text-zink-500 group-hover:text-blue-500 transition-colors"></i>
                    </a>
                    <a href="{{ route('admin.reports.driver-performance') }}" class="flex items-center justify-between p-3 rounded-lg border border-slate-100 dark:border-zink-600 hover:border-blue-300 dark:hover:border-blue-500 hover:bg-blue-50/50 dark:hover:bg-blue-500/10 transition-colors group">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="bar-chart-2" class="w-4 h-4 text-slate-400 dark:text-zink-400 group-hover:text-blue-500 transition-colors"></i>
                            <span class="text-sm text-slate-600 dark:text-zink-200">Driver Performance</span>
                        </div>
                        <i data-lucide="download" class="w-3.5 h-3.5 text-slate-300 dark:text-zink-500 group-hover:text-blue-500 transition-colors"></i>
                    </a>
                    <a href="{{ route('admin.reports.unserviced.export') }}" class="flex items-center justify-between p-3 rounded-lg border border-slate-100 dark:border-zink-600 hover:border-red-300 dark:hover:border-red-500 hover:bg-red-50/50 dark:hover:bg-red-500/10 transition-colors group">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="map-off" class="w-4 h-4 text-slate-400 dark:text-zink-400 group-hover:text-red-500 transition-colors"></i>
                            <span class="text-sm text-slate-600 dark:text-zink-200">Unserviced Areas</span>
                        </div>
                        <i data-lucide="download" class="w-3.5 h-3.5 text-slate-300 dark:text-zink-500 group-hover:text-red-500 transition-colors"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Recent Signups --}}
<div class="card border-0 shadow-sm mt-5">
    <div class="card-body p-0">
        <div class="flex items-center justify-between px-5 pt-5 pb-4 border-b border-slate-100 dark:border-zink-600">
            <div>
                <h6 class="text-15 font-semibold text-slate-800 dark:text-zink-50">Recent Signups</h6>
                <p class="text-xs text-slate-400 dark:text-zink-400 mt-0.5">Latest users who joined the platform</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:underline">View all users →</a>
        </div>
        <div class="overflow-x-auto">
            @if($recentUsers->isEmpty())
                <div class="flex flex-col items-center justify-center py-10 text-slate-400 dark:text-zink-400">
                    <i data-lucide="users" class="w-10 h-10 mb-3 opacity-40"></i>
                    <p class="text-sm">No users yet</p>
                </div>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-zink-700/50">
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zink-300 uppercase tracking-wider">User</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zink-300 uppercase tracking-wider">Type</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zink-300 uppercase tracking-wider">Status</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zink-300 uppercase tracking-wider">Joined</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-zink-600">
                        @foreach($recentUsers as $user)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-zink-700/30 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 dark:bg-zink-600 text-slate-600 dark:text-zink-200 text-xs font-bold shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-700 dark:text-zink-100 text-sm">{{ $user->name }}</p>
                                        <p class="text-xs text-slate-400 dark:text-zink-400">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                @php
                                    $typeColors = ['parent' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400', 'driver' => 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-400', 'admin' => 'bg-slate-100 text-slate-600 dark:bg-zink-600 dark:text-zink-300'];
                                    $color = $typeColors[$user->user_type] ?? 'bg-slate-100 text-slate-600';
                                @endphp
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium {{ $color }}">{{ ucfirst($user->user_type) }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                @if($user->status === 'active')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span> Active
                                    </span>
                                @elseif($user->status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 inline-block"></span> Pending
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 inline-block"></span> {{ ucfirst($user->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-slate-500 dark:text-zink-300 text-sm whitespace-nowrap">
                                {{ $user->created_at?->format('d M Y') ?? '—' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>

@endsection
