@extends('layouts.master')

@section('content')
<div class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)] min-h-[calc(100vh_-_theme('spacing.header')_*_3)] group-data-[layout=horizontal]:min-h-[calc(100vh_-_theme('spacing.header')_*_1.3)]">
    <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">
        <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
            <div class="grow">
                <h5 class="text-16">Service Area & Schools</h5>
            </div>
            <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                <li class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                    <a href="{{ route('driver.dashboard') }}" class="text-slate-400 dark:text-zink-200">Dashboard</a>
                </li>
                <li class="text-slate-700 dark:text-zink-100">
                    Service Definition
                </li>
            </ul>
        </div>

        @if (session('success'))
            <div class="mb-4 p-4 text-green-700 bg-green-100 rounded-md border border-green-200 dark:bg-green-500/20 dark:text-green-500 dark:border-green-500/20">
                {{ session('success') }}
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <p class="mb-4 text-slate-500 dark:text-zink-200">
                    Define where you operate. Parents in these zones and schools will be able to see you.
                </p>

                <form action="{{ route('driver.service.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Pickup Zones Section -->
                    <div class="mb-6">
                        <h6 class="mb-3 text-15 text-slate-800 dark:text-zink-50 font-semibold">1. Pickup Zones (Locations)</h6>
                        <p class="mb-3 text-sm text-slate-500 dark:text-zink-200">Select the zones you are willing to drive to.</p>
                        
                        <div class="mt-3">
                            <select 
                                name="locations[]" 
                                id="locations" 
                                data-choices 
                                data-choices-removeItem 
                                multiple 
                                class="form-select border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500"
                            >
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}" {{ $driverProfile->locations->contains($location->id) ? 'selected' : '' }}>
                                        {{ $location->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if($locations->isEmpty())
                                <p class="mt-2 text-sm text-slate-500 dark:text-zink-200">No active locations found.</p>
                            @endif
                        </div>
                    </div>

                    <div class="border-t border-slate-200 dark:border-zink-500 my-6"></div>

                    <!-- Schools Section -->
                    <div class="mb-6">
                        <h6 class="mb-3 text-15 text-slate-800 dark:text-zink-50 font-semibold">2. Schools</h6>
                        <p class="mb-3 text-sm text-slate-500 dark:text-zink-200">Select the schools you are willing to drop off at / pick up from.</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            @forelse($schools as $school)
                                <div class="relative flex items-start">
                                    <div class="flex items-center h-5">
                                        <input 
                                            id="school_{{ $school->id }}" 
                                            name="schools[]" 
                                            value="{{ $school->id }}" 
                                            type="checkbox" 
                                            class="form-checkbox border-slate-200 dark:border-zink-500 focus:border-custom-500 focus:ring-custom-500 text-custom-500 rounded"
                                            {{ $driverProfile->schools->contains($school->id) ? 'checked' : '' }}
                                        >
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="school_{{ $school->id }}" class="font-medium text-slate-700 dark:text-zink-100 select-none cursor-pointer">
                                            {{ $school->name }}
                                        </label>
                                        <p class="text-slate-500 dark:text-zink-200 text-xs">{{ $school->city }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-slate-500 dark:text-zink-200 col-span-full">No active schools found.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="submit" class="text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
