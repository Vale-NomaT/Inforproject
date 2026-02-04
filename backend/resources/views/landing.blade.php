<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light scroll-smooth group" data-layout="vertical" data-sidebar="light" data-sidebar-size="lg" data-mode="light" data-topbar="light" data-skin="default" data-navbar="sticky" data-content="fluid" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>SafeRide Kids</title>
        <meta content="SafeRide Kids - Safe school transport for your child" name="description">
        <meta content="SafeRide Kids" name="author">
        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ asset('assets/images/saferide-logo.svg') }}">
        
        <!-- Layout config Js -->
        <script src="{{ asset('assets/js/layout.js') }}"></script>
        <!-- SafeRide Kids CSS -->
        <link rel="stylesheet" href="{{ asset('assets/css/saferide.css') }}">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="flex flex-col min-h-screen bg-slate-50 dark:bg-zink-800 text-slate-900 dark:text-slate-50 font-public">

        <!-- Navbar -->
        <nav class="sticky top-0 z-50 w-full bg-white/80 dark:bg-zink-700/80 backdrop-blur-md border-b border-slate-200 dark:border-zink-600">
            <div class="max-w-5xl mx-auto px-6 h-16 flex items-center justify-between">
                <a href="{{ route('landing') }}" class="flex items-center gap-2">
                    <img src="{{ asset('assets/images/saferide-logo.svg') }}" alt="SafeRide Kids" class="h-8">
                    <span class="text-xl font-bold text-slate-800 dark:text-white hidden sm:block">SafeRide Kids</span>
                </a>
                <div class="flex items-center gap-4">
                    <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-custom-500 dark:hover:text-custom-400 transition-colors">
                        Log In
                    </a>
                    <a href="{{ route('register.parent') }}" class="px-4 py-2 text-sm font-medium text-white bg-custom-500 rounded-md hover:bg-custom-600 focus:outline-none focus:ring-2 focus:ring-custom-500 focus:ring-offset-2 dark:focus:ring-offset-zink-800 transition-colors">
                        Get Started
                    </a>
                </div>
            </div>
        </nav>

        <div class="flex-1 flex items-center justify-center px-6 py-10">
            <div class="max-w-5xl w-full flex flex-col lg:flex-row items-center gap-10">
                <div class="flex-1 text-center lg:text-left space-y-6">
                    <div class="space-y-3">
                        <div class="inline-flex items-center rounded-full bg-custom-100 text-custom-700 px-3 py-1 text-xs font-medium dark:bg-custom-500/20 dark:text-custom-200">
                            <span>School transport, reimagined</span>
                        </div>
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold tracking-tight text-slate-900 dark:text-white leading-tight">
                            SafeRide Kids
                        </h1>
                        <p class="text-lg text-slate-600 dark:text-slate-300 max-w-lg mx-auto lg:mx-0">
                            Safe, trusted school transport for your child. Real-time tracking, vetted drivers, and peace of mind for parents.
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 sm:justify-center lg:justify-start pt-2">
                        <a
                            href="{{ route('register.parent') }}"
                            class="inline-flex justify-center items-center px-6 py-3.5 rounded-lg bg-custom-500 text-white text-base font-medium shadow-lg shadow-custom-500/20 hover:bg-custom-600 hover:-translate-y-0.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-custom-500 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-50 dark:focus-visible:ring-offset-zink-800 transition-all duration-200"
                        >
                            <i data-lucide="user" class="w-5 h-5 mr-2"></i>
                            I’m a Parent
                        </a>
                        <a
                            href="{{ route('register.driver') }}"
                            class="inline-flex justify-center items-center px-6 py-3.5 rounded-lg bg-white text-slate-700 text-base font-medium shadow-sm border border-slate-200 hover:border-custom-300 hover:text-custom-600 hover:bg-slate-50 hover:-translate-y-0.5 dark:bg-zink-700 dark:text-slate-200 dark:border-zink-600 dark:hover:bg-zink-600 transition-all duration-200"
                        >
                            <i data-lucide="car" class="w-5 h-5 mr-2"></i>
                            I’m a Driver
                        </a>
                    </div>
                </div>
                <div class="flex-1 w-full max-w-md lg:max-w-full">
                    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-custom-500 via-custom-600 to-indigo-700 p-8 shadow-2xl min-h-[300px] flex items-end transform transition-transform hover:scale-[1.02] duration-500">
                        <div class="space-y-4 text-white relative z-10">
                            <div class="inline-flex p-3 bg-white/10 rounded-xl backdrop-blur-sm border border-white/10 mb-2">
                                <i data-lucide="shield-check" class="w-8 h-8 text-white"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold uppercase tracking-wider text-white/80 mb-1">
                                    Live Route Visibility
                                </p>
                                <h3 class="text-2xl font-bold text-white mb-2">Always Connected</h3>
                                <p class="text-base leading-relaxed text-white/90">
                                    Parents see each trip in real time. Drivers follow smart routes. Admins keep every child accounted for.
                                </p>
                            </div>
                        </div>
                        <!-- Decorative circles -->
                        <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                        <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl"></div>
                        <div class="absolute inset-0 opacity-20 bg-[radial-gradient(circle_at_top,white,transparent_70%)]"></div>
                    </div>
                </div>
            </div>
        </div>

        <footer class="bg-white dark:bg-zink-700 border-t border-slate-200 dark:border-zink-600 py-8">
            <div class="max-w-5xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-4 text-sm text-slate-500 dark:text-slate-400">
                <p class="order-2 md:order-1">
                    &copy; {{ date('Y') }} SafeRide Kids. For parents, drivers, and schools who take safety seriously.
                </p>
                <div class="order-1 md:order-2 flex items-center gap-6">
                    <a href="#" class="hover:text-custom-500 transition-colors">Privacy</a>
                    <a href="#" class="hover:text-custom-500 transition-colors">Terms</a>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('login') }}" class="flex items-center gap-1.5 text-custom-600 dark:text-custom-400 font-medium hover:text-custom-700 dark:hover:text-custom-300 transition-colors">
                            <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                            <span>Admin Login</span>
                        </a>
                        <span class="text-xs text-slate-400 dark:text-slate-500">(For verified admins only)</span>
                    </div>
                </div>
            </div>
        </footer>

        <script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
        <script src="{{ asset('assets/libs/%40popperjs/core/umd/popper.min.js') }}"></script>
        <script src="{{ asset('assets/libs/tippy.js/tippy-bundle.umd.min.js') }}"></script>
        <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
        <script src="{{ asset('assets/libs/prismjs/prism.js') }}"></script>
        <script src="{{ asset('assets/libs/lucide/umd/lucide.js') }}"></script>
        <script src="{{ asset('assets/js/saferide.bundle.js') }}"></script>
    </body>
</html>
