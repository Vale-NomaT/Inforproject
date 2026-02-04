<!DOCTYPE html>
<html lang="en" class="light scroll-smooth group" data-layout="vertical" data-sidebar="light" data-sidebar-size="lg" data-mode="light" data-topbar="light" data-skin="default" data-navbar="sticky" data-content="fluid" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'SafeRide Kids')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
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

<body class="flex items-center justify-center min-h-screen py-16 bg-cover bg-auth-pattern dark:bg-auth-pattern-dark font-public bg-slate-50 dark:bg-zink-800">

    <div class="mb-0 w-screen lg:w-[500px] card shadow-lg border-none shadow-slate-100 relative">
        <div class="!px-10 !py-12 card-body">
            <div class="text-center">
                <div class="mt-8 text-center">
                    <h4 class="mb-2 text-custom-500 dark:text-custom-500">@yield('heading', 'Welcome Back !')</h4>
                    <p class="text-slate-500 dark:text-zink-200">@yield('subheading', 'Sign in to continue to SafeRide Kids.')</p>
                </div>
            </div>

            <div class="mt-10">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
    <script src="{{ asset('assets/libs/%40popperjs/core/umd/popper.min.js') }}"></script>
    <script src="{{ asset('assets/libs/tippy.js/tippy-bundle.umd.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/prismjs/prism.js') }}"></script>
    <script src="{{ asset('assets/libs/lucide/umd/lucide.js') }}"></script>
    <script src="{{ asset('assets/js/saferide.bundle.js') }}"></script>
</body>
</html>
