@extends('layouts.master')

@section('title', 'Unserviced Areas Report')

@section('content')

<div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
    <div class="grow">
        <h5 class="text-16 font-semibold text-slate-800 dark:text-zink-50">Unserviced Areas Report</h5>
        <p class="text-slate-500 dark:text-zink-300 text-sm mt-0.5">Schools and pickup areas with no active drivers assigned.</p>
    </div>
    <a href="{{ route('admin.reports.unserviced.export') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
        <i data-lucide="download" class="w-4 h-4"></i>
        Export CSV
    </a>
</div>

{{-- Summary --}}
<div class="grid grid-cols-2 gap-4 mb-5">
    <div class="card border-0 shadow-sm border-l-4 border-l-red-500">
        <div class="card-body p-5 flex items-center gap-4">
            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-red-100 dark:bg-red-500/20 shrink-0">
                <i data-lucide="school" class="w-6 h-6 text-red-500 dark:text-red-400"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800 dark:text-zink-50">{{ $unservicedSchools->count() }}</p>
                <p class="text-sm text-slate-500 dark:text-zink-300">Unserviced Schools</p>
            </div>
        </div>
    </div>
    <div class="card border-0 shadow-sm border-l-4 border-l-amber-500">
        <div class="card-body p-5 flex items-center gap-4">
            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-500/20 shrink-0">
                <i data-lucide="map-pin" class="w-6 h-6 text-amber-500 dark:text-amber-400"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800 dark:text-zink-50">{{ $unservicedLocations->count() }}</p>
                <p class="text-sm text-slate-500 dark:text-zink-300">Unserviced Pickup Areas</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 gap-5 xl:grid-cols-2">

    {{-- Unserviced Schools --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="flex items-center justify-between px-5 pt-5 pb-4 border-b border-slate-100 dark:border-zink-600">
                <div class="flex items-center gap-2">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-red-100 dark:bg-red-500/20">
                        <i data-lucide="school" class="w-4 h-4 text-red-500 dark:text-red-400"></i>
                    </div>
                    <div>
                        <h6 class="text-15 font-semibold text-slate-800 dark:text-zink-50">Schools Without Drivers</h6>
                        <p class="text-xs text-slate-400 dark:text-zink-400">No active driver is assigned to these schools</p>
                    </div>
                </div>
                <span class="text-sm font-bold text-red-500">{{ $unservicedSchools->count() }}</span>
            </div>

            @if($unservicedSchools->isEmpty())
                <div class="flex flex-col items-center justify-center py-12 text-slate-400 dark:text-zink-400">
                    <i data-lucide="check-circle" class="w-10 h-10 mb-3 text-green-400 opacity-70"></i>
                    <p class="text-sm font-medium text-green-600 dark:text-green-400">All schools are covered!</p>
                    <p class="text-xs mt-1">Every school has at least one active driver.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-zink-700/50">
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zink-300 uppercase tracking-wider">School Name</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zink-300 uppercase tracking-wider">City</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zink-300 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-zink-600">
                            @foreach($unservicedSchools as $school)
                            <tr class="hover:bg-red-50/30 dark:hover:bg-red-500/5 transition-colors">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex items-center justify-center w-7 h-7 rounded-lg bg-red-100 dark:bg-red-500/20 shrink-0">
                                            <i data-lucide="school" class="w-3.5 h-3.5 text-red-500 dark:text-red-400"></i>
                                        </div>
                                        <span class="font-medium text-slate-700 dark:text-zink-100">{{ $school->name }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-slate-500 dark:text-zink-300">{{ $school->city ?? '—' }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400">
                                        <i data-lucide="alert-circle" class="w-3 h-3"></i> No Driver
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Unserviced Pickup Locations --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="flex items-center justify-between px-5 pt-5 pb-4 border-b border-slate-100 dark:border-zink-600">
                <div class="flex items-center gap-2">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-500/20">
                        <i data-lucide="map-pin" class="w-4 h-4 text-amber-500 dark:text-amber-400"></i>
                    </div>
                    <div>
                        <h6 class="text-15 font-semibold text-slate-800 dark:text-zink-50">Pickup Areas Without Drivers</h6>
                        <p class="text-xs text-slate-400 dark:text-zink-400">No active driver covers these pickup areas</p>
                    </div>
                </div>
                <span class="text-sm font-bold text-amber-500">{{ $unservicedLocations->count() }}</span>
            </div>

            @if($unservicedLocations->isEmpty())
                <div class="flex flex-col items-center justify-center py-12 text-slate-400 dark:text-zink-400">
                    <i data-lucide="check-circle" class="w-10 h-10 mb-3 text-green-400 opacity-70"></i>
                    <p class="text-sm font-medium text-green-600 dark:text-green-400">All pickup areas are covered!</p>
                    <p class="text-xs mt-1">Every area has at least one active driver.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-zink-700/50">
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zink-300 uppercase tracking-wider">Area Name</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zink-300 uppercase tracking-wider">City</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zink-300 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-zink-600">
                            @foreach($unservicedLocations as $location)
                            <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-500/5 transition-colors">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex items-center justify-center w-7 h-7 rounded-lg bg-amber-100 dark:bg-amber-500/20 shrink-0">
                                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-amber-500 dark:text-amber-400"></i>
                                        </div>
                                        <span class="font-medium text-slate-700 dark:text-zink-100">{{ $location->name }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-slate-500 dark:text-zink-300">{{ $location->city ?? '—' }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400">
                                        <i data-lucide="alert-circle" class="w-3 h-3"></i> No Driver
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</div>

@endsection
