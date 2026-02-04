@extends('layouts.auth')

@section('title', 'Register as Driver')
@section('heading', 'Create Driver Account')
@section('subheading', 'Join SafeRide Kids as a trusted driver.')

@section('content')
    <form method="POST" action="/register/driver" class="mt-4">
        @csrf

        <div class="mb-3">
            <label for="name" class="inline-block mb-2 text-base font-medium text-slate-500 dark:text-zink-200">Full Name</label>
            <input type="text" id="name" name="name" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 w-full rounded-md py-2 px-3" placeholder="Enter your full name" value="{{ old('name') }}" required autofocus>
            @error('name')
                <div class="mt-1 text-sm text-red-500">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="inline-block mb-2 text-base font-medium text-slate-500 dark:text-zink-200">Email Address</label>
            <input type="email" id="email" name="email" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 w-full rounded-md py-2 px-3" placeholder="Enter your email" value="{{ old('email') }}" required>
            @error('email')
                <div class="mt-1 text-sm text-red-500">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="date_of_birth" class="inline-block mb-2 text-base font-medium text-slate-500 dark:text-zink-200">Date of Birth</label>
            <input type="date" id="date_of_birth" name="date_of_birth" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 w-full rounded-md py-2 px-3" value="{{ old('date_of_birth') }}" max="{{ date('Y-m-d', strtotime('-18 years')) }}" required>
            @error('date_of_birth')
                <div class="mt-1 text-sm text-red-500">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="license_number" class="inline-block mb-2 text-base font-medium text-slate-500 dark:text-zink-200">License Number</label>
            <input type="text" id="license_number" name="license_number" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 w-full rounded-md py-2 px-3" placeholder="Driver's License Number" value="{{ old('license_number') }}" required>
            @error('license_number')
                <div class="mt-1 text-sm text-red-500">{{ $message }}</div>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
            <div>
                <label for="vehicle_make" class="inline-block mb-2 text-base font-medium text-slate-500 dark:text-zink-200">Vehicle Make</label>
                <input type="text" id="vehicle_make" name="vehicle_make" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 w-full rounded-md py-2 px-3" placeholder="e.g. Toyota" value="{{ old('vehicle_make') }}" required>
                @error('vehicle_make')
                    <div class="mt-1 text-sm text-red-500">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label for="vehicle_model" class="inline-block mb-2 text-base font-medium text-slate-500 dark:text-zink-200">Vehicle Model</label>
                <input type="text" id="vehicle_model" name="vehicle_model" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 w-full rounded-md py-2 px-3" placeholder="e.g. Camry" value="{{ old('vehicle_model') }}" required>
                @error('vehicle_model')
                    <div class="mt-1 text-sm text-red-500">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="license_plate" class="inline-block mb-2 text-base font-medium text-slate-500 dark:text-zink-200">Car Plate Number</label>
            <input type="text" id="license_plate" name="license_plate" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 w-full rounded-md py-2 px-3" placeholder="e.g. ABC-123" value="{{ old('license_plate') }}" required>
            @error('license_plate')
                <div class="mt-1 text-sm text-red-500">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="max_child_capacity" class="inline-block mb-2 text-base font-medium text-slate-500 dark:text-zink-200">Max Child Capacity</label>
            <input type="number" id="max_child_capacity" name="max_child_capacity" min="1" max="50" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 w-full rounded-md py-2 px-3" placeholder="e.g. 4" value="{{ old('max_child_capacity') }}" required>
            @error('max_child_capacity')
                <div class="mt-1 text-sm text-red-500">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="inline-block mb-2 text-base font-medium text-slate-500 dark:text-zink-200">Password</label>
            <input type="password" id="password" name="password" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 w-full rounded-md py-2 px-3" placeholder="Create password" required>
            @error('password')
                <div class="mt-1 text-sm text-red-500">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="inline-block mb-2 text-base font-medium text-slate-500 dark:text-zink-200">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 w-full rounded-md py-2 px-3" placeholder="Confirm password" required>
        </div>

        <div class="mt-10">
            <button type="submit" class="w-full text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">Register</button>
        </div>

        <div class="mt-10 text-center">
            <p class="mb-0 text-slate-500 dark:text-zink-200">Already have an account ? <a href="{{ route('login') }}" class="font-semibold underline transition-all duration-150 ease-linear text-slate-500 dark:text-zink-200 hover:text-custom-500 dark:hover:text-custom-500"> Login</a> </p>
        </div>
    </form>
@endsection
