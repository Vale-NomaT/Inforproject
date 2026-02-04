@extends('layouts.auth')

@section('title', 'Register as Parent')
@section('heading', 'Create Parent Account')
@section('subheading', 'Join SafeRide Kids to manage your child\'s transport.')

@section('content')
    <form method="POST" action="/register/parent" class="mt-4">
        @csrf
        
        <div class="mb-3">
            <label for="name" class="inline-block mb-2 text-base font-medium text-slate-500 dark:text-zink-200">Name</label>
            <input type="text" id="name" name="name" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 w-full rounded-md py-2 px-3" placeholder="Enter your first name" value="{{ old('name') }}" required autofocus>
            @error('name')
                <div class="mt-1 text-sm text-red-500">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="surname" class="inline-block mb-2 text-base font-medium text-slate-500 dark:text-zink-200">Surname</label>
            <input type="text" id="surname" name="surname" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 w-full rounded-md py-2 px-3" placeholder="Enter your surname" value="{{ old('surname') }}" required>
            @error('surname')
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
            <label for="phone" class="inline-block mb-2 text-base font-medium text-slate-500 dark:text-zink-200">Phone</label>
            <input type="tel" id="phone" name="phone" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 w-full rounded-md py-2 px-3" placeholder="Enter your phone number" value="{{ old('phone') }}" required>
            @error('phone')
                <div class="mt-1 text-sm text-red-500">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="inline-block mb-2 text-base font-medium text-slate-500 dark:text-zink-200">Password</label>
            <input type="password" id="password" name="password" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 w-full rounded-md py-2 px-3" placeholder="Create password (min 8 chars)" required>
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
