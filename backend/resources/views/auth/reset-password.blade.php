@extends('layouts.auth')

@section('title', 'Reset Password')
@section('heading', 'Reset Password')
@section('subheading', 'Create a new password for your account.')

@section('content')
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        @if ($request->route('token'))
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
        @else
            <div>
                <x-input-label for="token" :value="__('OTP Code')" />
                <x-text-input id="token" class="block mt-1 w-full" type="text" name="token" :value="old('token')" required autofocus />
                <x-input-error :messages="$errors->get('token')" class="mt-2" />
            </div>
        @endif

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Reset Password') }}
            </x-primary-button>
        </div>
    </form>
@endsection
