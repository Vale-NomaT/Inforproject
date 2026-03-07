@extends('layouts.master')

@section('content')
<div class="flex flex-col gap-4 mb-5 md:flex-row md:items-center md:justify-between">
    <div>
        <h5 class="text-16">Pending Drivers</h5>
        <p class="text-slate-500 dark:text-zink-200">Review and approve drivers before they can accept bookings.</p>
    </div>
</div>



<div class="card">
    <div class="card-body">
        @if ($drivers->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-200 bg-white px-6 py-10 text-center dark:bg-zink-700 dark:border-zink-500">
                <h2 class="text-lg sm:text-xl font-semibold text-slate-900 dark:text-slate-100">
                    No pending drivers
                </h2>
                <p class="mt-2 text-sm sm:text-base text-slate-600 dark:text-slate-300">
                    Once new drivers register, they will appear here for review.
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap">
                    <thead class="bg-slate-100 dark:bg-zink-600">
                        <tr>
                            <th class="px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 ltr:text-left rtl:text-right text-slate-500 dark:text-zink-200">Name / DOB</th>
                            <th class="px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 ltr:text-left rtl:text-right text-slate-500 dark:text-zink-200">Contact</th>
                            <th class="px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 ltr:text-left rtl:text-right text-slate-500 dark:text-zink-200">Vehicle Details</th>
                            <th class="px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 ltr:text-left rtl:text-right text-slate-500 dark:text-zink-200">Documents</th>
                            <th class="px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 ltr:text-right rtl:text-left text-slate-500 dark:text-zink-200">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($drivers as $driver)
                            @php $profile = $driver->driverProfile; @endphp
                            <tr>
                                <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 dark:text-zink-100">
                                    <div class="font-medium">{{ $driver->name }}</div>
                                    @if ($profile && $profile->date_of_birth)
                                        <div class="text-xs text-slate-500">DOB: {{ $profile->date_of_birth }}</div>
                                    @endif
                                </td>
                                <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 dark:text-zink-100">
                                    <div class="text-sm">{{ $driver->email }}</div>
                                </td>
                                <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 dark:text-zink-100">
                                    @if ($profile)
                                        <div class="font-medium">{{ $profile->vehicle_make }} {{ $profile->vehicle_model }}</div>
                                        <div class="text-xs text-slate-500">Plate: {{ $profile->license_plate }}</div>
                                        <div class="text-xs text-slate-500">Capacity: {{ $profile->max_child_capacity }} kids</div>
                                    @else
                                        <span class="text-slate-400">Not provided</span>
                                    @endif
                                </td>
                                <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                    @if ($profile)
                                        <div class="flex flex-col gap-1">
                                            @if($profile->license_file_path)
                                                <a href="{{ Storage::url($profile->license_file_path) }}" target="_blank" class="text-xs font-medium text-custom-500 hover:text-custom-600 flex items-center gap-1">
                                                    <i data-lucide="file-text" class="size-3"></i> License
                                                </a>
                                            @endif
                                            @if($profile->vehicle_registration_file_path)
                                                <a href="{{ Storage::url($profile->vehicle_registration_file_path) }}" target="_blank" class="text-xs font-medium text-custom-500 hover:text-custom-600 flex items-center gap-1">
                                                    <i data-lucide="car" class="size-3"></i> Registration
                                                </a>
                                            @endif
                                            @if($profile->gov_id_file_path)
                                                <a href="{{ Storage::url($profile->gov_id_file_path) }}" target="_blank" class="text-xs font-medium text-custom-500 hover:text-custom-600 flex items-center gap-1">
                                                    <i data-lucide="credit-card" class="size-3"></i> Gov ID
                                                </a>
                                            @endif
                                            <div class="text-xs text-slate-500 mt-1">
                                                License #: {{ $profile->license_number }}
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-slate-400">No documents</span>
                                    @endif
                                </td>
                                <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 text-right">
                                    <div class="flex justify-end gap-2">
                                        <form method="POST" action="{{ route('admin.drivers.approve', ['driver' => $driver->id]) }}">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 text-xs text-white bg-green-500 rounded-md hover:bg-green-600 transition-colors">Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.drivers.reject', ['driver' => $driver->id]) }}" class="flex items-center gap-2">
                                            @csrf
                                            <input type="text" name="reason" placeholder="Reason" class="w-24 px-2 py-1 text-xs border border-slate-200 rounded-md dark:bg-zink-700 dark:border-zink-500 dark:text-zink-100">
                                            <button type="submit" class="px-3 py-1 text-xs text-white bg-red-500 rounded-md hover:bg-red-600 transition-colors">Reject</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
