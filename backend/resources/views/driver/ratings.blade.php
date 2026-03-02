@extends('layouts.master')

@section('content')
<div class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)] min-h-[calc(100vh_-_theme('spacing.header')_*_3)] group-data-[layout=horizontal]:min-h-[calc(100vh_-_theme('spacing.header')_*_1.3)]">
    <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">
        <div class="flex flex-col gap-4 mb-5 md:flex-row md:items-center md:justify-between">
            <div>
                <h5 class="text-16">My Ratings & Reviews</h5>
                <p class="text-slate-500 dark:text-zink-200">Feedback from parents.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('driver.dashboard') }}" class="text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">Back to Dashboard</a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-4">
            <div class="card text-center p-5">
                <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full bg-yellow-100 text-yellow-500 dark:bg-yellow-500/20">
                    <i data-lucide="star" class="w-8 h-8 fill-yellow-500"></i>
                </div>
                <h5 class="mb-1 text-2xl font-bold">{{ number_format($averageRating, 1) }}</h5>
                <p class="text-slate-500 dark:text-zink-200">Average Rating</p>
            </div>
            <div class="card text-center p-5">
                <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full bg-blue-100 text-blue-500 dark:bg-blue-500/20">
                    <i data-lucide="message-square" class="w-8 h-8"></i>
                </div>
                <h5 class="mb-1 text-2xl font-bold">{{ $totalRatings }}</h5>
                <p class="text-slate-500 dark:text-zink-200">Total Reviews</p>
            </div>
        </div>

        <div class="mt-5">
            <h6 class="mb-3 text-15 text-slate-900 dark:text-zink-50">Recent Reviews</h6>
            @if($ratings->count() > 0)
                <div class="grid grid-cols-1 gap-4">
                    @foreach($ratings as $rating)
                        <div class="card p-4">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-lg">
                                        {{ substr($rating->parent->name ?? 'P', 0, 1) }}
                                    </div>
                                </div>
                                <div class="flex-grow">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h6 class="font-semibold text-slate-900 dark:text-zink-50">{{ $rating->parent->name ?? 'Parent' }}</h6>
                                            <p class="text-xs text-slate-500 dark:text-zink-200">
                                                Trip: {{ $rating->trip->scheduled_date->format('M d, Y') }} - 
                                                {{ ucfirst($rating->trip->type) }}
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i data-lucide="star" class="w-4 h-4 {{ $i <= $rating->rating ? 'text-yellow-500 fill-yellow-500' : 'text-slate-300 dark:text-zink-500' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                    <p class="mt-2 text-slate-600 dark:text-zink-100">{{ $rating->comment }}</p>
                                    <p class="mt-2 text-xs text-slate-400 dark:text-zink-300">{{ $rating->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="card p-10 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center dark:bg-zink-600">
                        <i data-lucide="inbox" class="w-8 h-8 text-slate-400 dark:text-zink-200"></i>
                    </div>
                    <h5 class="mb-2 text-16 font-semibold text-slate-900 dark:text-zink-50">No reviews yet</h5>
                    <p class="text-slate-500 dark:text-zink-200">Reviews from parents will appear here after you complete trips.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
