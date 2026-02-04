@extends('layouts.auth')

@section('title', 'Login')
@section('heading', 'Welcome Back !')
@section('subheading', 'Sign in to continue to SafeRide Kids.')

@section('content')
    <form action="{{ route('login') }}" method="POST" class="mt-4">
        @csrf
        <div class="mb-3">
            <label for="email" class="inline-block mb-2 text-base font-medium text-slate-500 dark:text-zink-200">Email Address</label>
            <input type="email" id="email" name="email" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 w-full rounded-md py-2 px-3" placeholder="Enter your email" value="{{ old('email') }}" required autofocus>
            @error('email')
                <div class="mt-1 text-sm text-red-500">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <div class="flex justify-between">
                <label for="password" class="inline-block mb-2 text-base font-medium text-slate-500 dark:text-zink-200">Password</label>
                <a href="" class="text-slate-500 dark:text-zink-200 text-sm hover:text-custom-500">Forgot Password?</a>
            </div>
            <input type="password" id="password" name="password" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 w-full rounded-md py-2 px-3" placeholder="Enter password" required>
            @error('password')
                <div class="mt-1 text-sm text-red-500">{{ $message }}</div>
            @enderror
        </div>
        <div class="flex items-center gap-2 mb-3">
            <input id="remember_me" type="checkbox" class="border rounded-sm appearance-none cursor-pointer size-4 bg-slate-100 border-slate-200 dark:bg-zink-600 dark:border-zink-500 checked:bg-custom-500 checked:border-custom-500 dark:checked:bg-custom-500 dark:checked:border-custom-500 checked:disabled:bg-custom-400 checked:disabled:border-custom-400" name="remember">
            <label for="remember_me" class="align-middle cursor-pointer text-slate-500 dark:text-zink-200">Remember me</label>
        </div>
        <div class="mt-10">
            <button type="submit" class="w-full text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">Sign In</button>
        </div>

        <div class="mt-10 text-center">
            <p class="mb-0 text-slate-500 dark:text-zink-200">Don't have an account ? <a href="{{ route('landing') }}" class="font-semibold underline transition-all duration-150 ease-linear text-slate-500 dark:text-zink-200 hover:text-custom-500 dark:hover:text-custom-500"> Create Account</a> </p>
        </div>
    </form>
@endsection
