@extends('layouts.auth')

@section('title', 'Forgot Password')
@section('heading', 'Forgot Password?')
@section('subheading', 'Enter your email to reset your password.')

@section('content')
    <div class="mb-4 text-sm text-gray-600 dark:text-zink-200">
        {{ __('Forgot your password? No problem. Enter your email and we will send you a one-time code (OTP) to reset your password.') }}
    </div>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Send OTP') }}
            </x-primary-button>
        </div>
    </form>
@endsection
