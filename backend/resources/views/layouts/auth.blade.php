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

<body style="display:flex; align-items:center; justify-content:center; min-height:100vh; margin:0; padding:24px; font-family:'Inter',sans-serif; position:relative; overflow:hidden;">

    <!-- Background image -->
    <div style="position:absolute; inset:0; background-image:url('{{ asset('nomazano.png') }}'); background-size:cover; background-position:center top; background-repeat:no-repeat; z-index:0;"></div>

    <!-- Gradient overlay -->
    <div style="position:absolute; inset:0; background:linear-gradient(135deg, rgba(240,244,255,0.82) 0%, rgba(219,234,254,0.65) 40%, rgba(191,219,254,0.35) 70%, rgba(147,197,253,0.15) 100%); z-index:1;"></div>

    <!-- Login card -->
    <div style="position:relative; z-index:10; width:100%; max-width:460px; background:rgba(255,255,255,0.93); backdrop-filter:blur(14px); border-radius:18px; box-shadow:0 24px 60px rgba(0,0,0,0.15); padding:48px 44px;">

        <!-- Logo -->
        <div style="display:flex; align-items:center; justify-content:center; gap:10px; margin-bottom:28px;">
            <div style="width:36px; height:36px; background:#2563eb; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <span style="font-size:18px; font-weight:700; color:#111827;">SafeRide Kids</span>
        </div>

        <div style="text-align:center; margin-bottom:32px;">
            <h4 class="text-custom-500" style="margin-bottom:6px; font-size:22px; font-weight:700;">@yield('heading', 'Welcome Back!')</h4>
            <p style="color:#64748b; font-size:14px; margin:0;">@yield('subheading', 'Sign in to continue to SafeRide Kids.')</p>
        </div>

        @yield('content')

    </div>

    <script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
    <script src="{{ asset('assets/libs/%40popperjs/core/umd/popper.min.js') }}"></script>
    <script src="{{ asset('assets/libs/tippy.js/tippy-bundle.umd.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/prismjs/prism.js') }}"></script>
    <script src="{{ asset('assets/libs/lucide/umd/lucide.js') }}"></script>
    <script src="{{ asset('assets/js/saferide.bundle.js') }}"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            @if(session('success'))
                Toast.fire({
                    icon: 'success',
                    title: "{{ session('success') }}"
                });
            @endif

            @if(session('error'))
                Toast.fire({
                    icon: 'error',
                    title: "{{ session('error') }}"
                });
            @endif

            @if(session('status'))
                Toast.fire({
                    icon: 'info',
                    title: "{{ session('status') }}"
                });
            @endif

            @if($errors->any())
                Toast.fire({
                    icon: 'error',
                    title: "{{ $errors->first() }}"
                });
            @endif
        });
    </script>
</body>
</html>
