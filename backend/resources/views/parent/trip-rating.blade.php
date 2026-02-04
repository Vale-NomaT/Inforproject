@extends('layouts.master')

@section('content')
<div class="flex flex-col gap-4 mb-5 md:flex-row md:items-center md:justify-between">
    <div>
        <h5 class="text-16">Rate Your Driver</h5>
        <p class="text-slate-500 dark:text-zink-200">How was the trip for {{ $child->first_name }} {{ $child->last_name }}?</p>
    </div>
    <div>
        <a href="{{ route('parent.dashboard') }}" class="text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">Back to Dashboard</a>
    </div>
</div>

<div class="card max-w-xl mx-auto">
    <div class="card-body">
        <div class="mb-4 text-sm text-slate-700 dark:text-zink-100">
            <p>
                Trip date: <span class="font-medium text-slate-900 dark:text-zink-50">{{ $trip->scheduled_date->format('Y-m-d') }}</span>
            </p>
        </div>

        <form method="POST" action="{{ route('parent.trips.rate.store', ['trip' => $trip->id]) }}" class="space-y-4">
            @csrf

            <div>
                <label for="rating" class="block text-sm font-medium text-slate-800 dark:text-zink-100 mb-2">Overall rating</label>
                <select id="rating" name="rating" class="form-select border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200" required>
                    <option value="">Select a rating</option>
                    @for ($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}">{{ $i }} star{{ $i === 1 ? '' : 's' }}</option>
                    @endfor
                </select>
            </div>

            <div>
                <label for="comment" class="block text-sm font-medium text-slate-800 dark:text-zink-100 mb-2">Additional feedback (optional)</label>
                <textarea id="comment" name="comment" rows="4" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200" placeholder="Share anything that would help us keep rides safe and smooth.">{{ old('comment') }}</textarea>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">
                    Submit Rating
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
