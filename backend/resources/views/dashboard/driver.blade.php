@extends('layouts.master')

@section('content')
        <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
            <div class="grow">
                <h5 class="text-16">Driver Dashboard</h5>
            </div>
            <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                <li class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                    <a href="#!" class="text-slate-400 dark:text-zink-200">Dashboards</a>
                </li>
                <li class="text-slate-700 dark:text-zink-100">
                    Driver
                </li>
            </ul>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-base sm:text-lg font-semibold text-slate-900 dark:text-zink-50">
                            Today’s Trips
                        </h2>
                    </div>
                    @if ($trips->isEmpty())
                        <p class="text-slate-500 dark:text-zink-200">No trips scheduled for today.</p>
                    @else
                        <ul class="flex flex-col gap-4">
                            @foreach ($trips as $trip)
                                <li class="flex flex-col gap-2 p-3 rounded-md bg-slate-50 dark:bg-zink-600">
                                    <div class="flex items-center justify-between">
                                        <h6 class="font-medium text-slate-800 dark:text-zink-50">
                                            {{ $trip->child->first_name }} {{ $trip->child->last_name }}
                                        </h6>
                                        <span class="px-2.5 py-0.5 text-xs font-medium rounded border border-transparent
                                            @if ($trip->status === 'in_progress')
                                                bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-500
                                            @else
                                                bg-slate-100 text-slate-800 dark:bg-slate-500/20 dark:text-slate-300
                                            @endif">
                                            {{ ucfirst(str_replace('_', ' ', $trip->status)) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-slate-500 dark:text-zink-200">
                                        <span class="font-medium">School:</span> {{ $trip->child->school->name ?? 'N/A' }}
                                    </p>
                                    <p class="text-sm text-slate-500 dark:text-zink-200">
                                        <span class="font-medium">Date:</span> {{ \Carbon\Carbon::parse($trip->scheduled_date)->format('M d, Y') }}
                                    </p>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <a
                href="{{ route('driver.bookings.index') }}"
                class="flex flex-col justify-between rounded-2xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm hover:border-blue-500 hover:shadow-md transition dark:bg-zink-700 dark:border-zink-500 relative"
            >
                <div>
                    <h2 class="text-base sm:text-lg font-semibold text-slate-900 dark:text-zink-50">
                        Pending Requests
                    </h2>
                    <p class="mt-1 text-sm text-slate-600 dark:text-zink-200">
                        @if(isset($pendingBookingsCount) && $pendingBookingsCount > 0)
                            You have <span class="font-bold text-red-500">{{ $pendingBookingsCount }}</span> new request{{ $pendingBookingsCount > 1 ? 's' : '' }}.
                        @else
                            No new requests at the moment.
                        @endif
                    </p>
                </div>
                @if(isset($pendingBookingsCount) && $pendingBookingsCount > 0)
                    <span class="absolute top-6 right-6 flex h-3 w-3">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                    </span>
                @endif
                <span class="mt-4 inline-flex items-center text-sm font-medium text-blue-600">
                    Review requests
                    <span class="ml-1">&rarr;</span>
                </span>
            </a>

            <a
                href="{{ route('driver.trips.history') }}"
                class="flex flex-col justify-between rounded-2xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm hover:border-blue-500 hover:shadow-md transition dark:bg-zink-700 dark:border-zink-500"
            >
                <div>
                    <h2 class="text-base sm:text-lg font-semibold text-slate-900 dark:text-zink-50">
                        Trip History
                    </h2>
                    <p class="mt-1 text-sm text-slate-600 dark:text-zink-200">
                        Review completed trips and earnings per child.
                    </p>
                </div>
                <span class="mt-4 inline-flex items-center text-sm font-medium text-blue-600">
                    View history
                    <span class="ml-1">&rarr;</span>
                </span>
            </a>

            <a
                href="{{ route('driver.service.edit') }}"
                class="flex flex-col justify-between rounded-2xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm hover:border-blue-500 hover:shadow-md transition dark:bg-zink-700 dark:border-zink-500"
            >
                <div>
                    <h2 class="text-base sm:text-lg font-semibold text-slate-900 dark:text-zink-50">
                        Service Definition
                    </h2>
                    <p class="mt-1 text-sm text-slate-600 dark:text-zink-200">
                        Manage your pickup zones and approved schools.
                    </p>
                </div>
                <span class="mt-4 inline-flex items-center text-sm font-medium text-blue-600">
                    Manage Service Area
                    <span class="ml-1">&rarr;</span>
                </span>
            </a>
        </div>
@endsection
