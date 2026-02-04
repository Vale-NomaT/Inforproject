@extends('layouts.master')

@section('content')
        <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
            <div class="grow">
                <h5 class="text-16">Admin Dashboard</h5>
            </div>
            <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                <li class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                    <a href="#!" class="text-slate-400 dark:text-zink-200">Dashboards</a>
                </li>
                <li class="text-slate-700 dark:text-zink-100">
                    Admin
                </li>
            </ul>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <a
                href="{{ route('admin.drivers.pending') }}"
                class="flex flex-col justify-between rounded-2xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm hover:border-blue-500 hover:shadow-md transition dark:bg-zink-700 dark:border-zink-500"
            >
                <div>
                    <h2 class="text-base sm:text-lg font-semibold text-slate-900 dark:text-zink-50">
                        Pending Drivers
                    </h2>
                    <p class="mt-1 text-sm text-slate-600 dark:text-zink-200">
                        Approve or reject new driver applications.
                    </p>
                </div>
                <span class="mt-4 inline-flex items-center text-sm font-medium text-blue-600">
                    Review drivers
                    <span class="ml-1">&rarr;</span>
                </span>
            </a>

            <a
                href="{{ route('admin.users.index') }}"
                class="flex flex-col justify-between rounded-2xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm hover:border-blue-500 hover:shadow-md transition dark:bg-zink-700 dark:border-zink-500"
            >
                <div>
                    <h2 class="text-base sm:text-lg font-semibold text-slate-900 dark:text-zink-50">
                        Users
                    </h2>
                    <p class="mt-1 text-sm text-slate-600 dark:text-zink-200">
                        View all parents, drivers, and admins.
                    </p>
                </div>
                <span class="mt-4 inline-flex items-center text-sm font-medium text-blue-600">
                    Manage users
                    <span class="ml-1">&rarr;</span>
                </span>
            </a>

            <div class="flex flex-col rounded-2xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm dark:bg-zink-700 dark:border-zink-500">
                <div>
                    <h2 class="text-base sm:text-lg font-semibold text-slate-900 dark:text-zink-50">
                        Reports
                    </h2>
                    <p class="mt-1 text-sm text-slate-600 dark:text-zink-200">
                        Export CSV data for trips, signups, and driver performance.
                    </p>
                </div>
                <div class="mt-4 space-y-2 text-sm">
                    <a
                        href="{{ route('admin.reports.trips') }}"
                        class="inline-flex items-center text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
                    >
                        Trips CSV
                        <span class="ml-1">&darr;</span>
                    </a>
                    <a
                        href="{{ route('admin.reports.signups') }}"
                        class="inline-flex items-center text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
                    >
                        New signups CSV
                        <span class="ml-1">&darr;</span>
                    </a>
                    <a
                        href="{{ route('admin.reports.driver-performance') }}"
                        class="inline-flex items-center text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
                    >
                        Driver performance CSV
                        <span class="ml-1">&darr;</span>
                    </a>
                </div>
            </div>
        </div>
@endsection
