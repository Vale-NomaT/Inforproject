<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SafeRide Kids – Safe School Transport</title>
    <meta name="description" content="SafeRide Kids - Safe, trusted school transport for your child.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        *, *::before, *::after { font-family: 'Inter', sans-serif; box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #f0f4ff; min-height: 100vh; display: flex; flex-direction: column; }
    </style>
</head>
<body>

    <!-- ── HERO (full-bleed, photo fills right half) ── -->
    <section style="position:relative; min-height:100vh; overflow:hidden; display:flex; flex-direction:column;">

        <!-- Background colour behind the image -->
        <div style="position:absolute; inset:0; background:#f0f4ff;"></div>

        <!-- Photo pinned to the right half, fully contained so nothing is cropped -->
        <img
            src="{{ asset('nomazano.png') }}"
            alt="Mother and child"
            style="
                position:absolute;
                right:0; top:0;
                width:62%;
                height:100%;
                object-fit:contain;
                object-position:right top;
            "
        >

        <!-- White fade from left so text stays readable -->
        <div style="
            position:absolute; inset:0;
            background: linear-gradient(
                to right,
                rgba(240,244,255,1)    0%,
                rgba(240,244,255,1)    32%,
                rgba(240,244,255,0.75) 42%,
                rgba(240,244,255,0.2)  52%,
                rgba(240,244,255,0)    62%
            );
        "></div>

        <!-- ── NAVBAR ── -->
        <nav style="position:relative; z-index:20; width:100%;">
            <div style="max-width:1200px; margin:0 auto; padding:0 32px; height:64px; display:flex; align-items:center; justify-content:space-between;">
                <!-- Logo -->
                <a href="{{ route('landing') }}" style="display:flex; align-items:center; gap:10px; text-decoration:none;">
                    <div style="width:36px; height:36px; background:#2563eb; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                    </div>
                    <span style="font-size:18px; font-weight:700; color:#111827;">SafeRide Kids</span>
                </a>
                <!-- Nav actions -->
                <div style="display:flex; align-items:center; gap:16px;">
                    <a href="{{ route('login') }}" style="font-size:14px; font-weight:600; color:#2563eb; text-decoration:none; padding:9px 22px; border-radius:8px; border:2px solid #2563eb; white-space:nowrap;">Log In</a>
                    <a href="{{ route('register.parent') }}" style="font-size:14px; font-weight:600; color:#fff; background:#2563eb; padding:9px 22px; border-radius:8px; text-decoration:none; white-space:nowrap; border:2px solid #2563eb;">Get Started</a>
                </div>
            </div>
        </nav>

        <!-- ── HERO CONTENT ── -->
        <div style="position:relative; z-index:10; flex:1; display:flex; flex-direction:column; justify-content:center; max-width:1200px; margin:0 auto; padding:40px 32px 160px; width:100%;">

            <div style="max-width:500px;">

                <!-- Badge -->
                <div style="display:inline-flex; align-items:center; background:rgba(255,255,255,0.85); border:1px solid #dbeafe; color:#1d4ed8; font-size:12px; font-weight:500; padding:5px 14px; border-radius:20px; margin-bottom:22px; backdrop-filter:blur(4px);">
                    School transport, reimagined
                </div>

                <!-- Headline -->
                <h1 style="font-size:clamp(38px,5vw,58px); font-weight:800; line-height:1.08; color:#0f172a; margin-bottom:18px; letter-spacing:-1px;">
                    Safe rides.<br>
                    Stronger <span style="color:#2563eb;">peace of mind.</span>
                </h1>

                <!-- Sub-copy -->
                <p style="font-size:15px; color:#475569; line-height:1.75; margin-bottom:36px; max-width:360px;">
                    Safe, trusted school transport for your child.<br>
                    Real-time tracking, vetted drivers, and complete<br>
                    peace of mind for parents.
                </p>

                <!-- CTA buttons -->
                <div style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:52px;">
                    <a href="{{ route('register.parent') }}" style="display:inline-flex; align-items:center; gap:8px; background:#2563eb; color:#fff; font-size:15px; font-weight:600; padding:13px 26px; border-radius:10px; text-decoration:none; box-shadow:0 4px 16px rgba(37,99,235,0.35);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        I'm a Parent
                    </a>
                    <a href="{{ route('register.driver') }}" style="display:inline-flex; align-items:center; gap:8px; background:#fff; color:#374151; font-size:15px; font-weight:600; padding:13px 26px; border-radius:10px; text-decoration:none; border:1.5px solid #e2e8f0; box-shadow:0 1px 4px rgba(0,0,0,0.06);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                        I'm a Driver
                    </a>
                </div>

                <!-- Feature badges row -->
                <div style="display:flex; gap:32px; flex-wrap:wrap;">

                    <div style="display:flex; align-items:flex-start; gap:10px;">
                        <div style="width:34px; height:34px; background:rgba(255,255,255,0.85); border:1px solid #e2e8f0; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; backdrop-filter:blur(4px);">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </div>
                        <div>
                            <div style="font-size:13px; font-weight:700; color:#0f172a;">Verified &amp; Trusted</div>
                            <div style="font-size:12px; color:#64748b; line-height:1.5; margin-top:2px;">Every driver is background<br>checked and verified.</div>
                        </div>
                    </div>

                    <div style="display:flex; align-items:flex-start; gap:10px;">
                        <div style="width:34px; height:34px; background:rgba(255,255,255,0.85); border:1px solid #e2e8f0; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; backdrop-filter:blur(4px);">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        </div>
                        <div>
                            <div style="font-size:13px; font-weight:700; color:#0f172a;">Live Tracking</div>
                            <div style="font-size:12px; color:#64748b; line-height:1.5; margin-top:2px;">See your child's trip in<br>real time.</div>
                        </div>
                    </div>

                    <div style="display:flex; align-items:flex-start; gap:10px;">
                        <div style="width:34px; height:34px; background:rgba(255,255,255,0.85); border:1px solid #e2e8f0; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; backdrop-filter:blur(4px);">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                        </div>
                        <div>
                            <div style="font-size:13px; font-weight:700; color:#0f172a;">Smart Notifications</div>
                            <div style="font-size:12px; color:#64748b; line-height:1.5; margin-top:2px;">Stay updated with instant<br>alerts and updates.</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- ── STATS BAR (white card pinned to bottom of hero) ── -->
        <div style="position:absolute; bottom:24px; left:50%; transform:translateX(-50%); width:calc(100% - 64px); max-width:900px; z-index:20;">
            <div style="background:#fff; border-radius:16px; box-shadow:0 8px 40px rgba(0,0,0,0.12); padding:24px 40px; display:flex; align-items:center; justify-content:space-around; flex-wrap:wrap; gap:20px;">

                <div style="display:flex; align-items:center; gap:14px;">
                    <div style="width:44px; height:44px; background:#eff6ff; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div>
                        <div style="font-size:22px; font-weight:800; color:#0f172a; line-height:1;">10,000+</div>
                        <div style="font-size:13px; color:#64748b; margin-top:3px;">Happy Families</div>
                    </div>
                </div>

                <div style="width:1px; height:36px; background:#e2e8f0;"></div>

                <div style="display:flex; align-items:center; gap:14px;">
                    <div style="width:44px; height:44px; background:#eff6ff; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
                    </div>
                    <div>
                        <div style="font-size:22px; font-weight:800; color:#0f172a; line-height:1;">5,000+</div>
                        <div style="font-size:13px; color:#64748b; margin-top:3px;">Verified Drivers</div>
                    </div>
                </div>

                <div style="width:1px; height:36px; background:#e2e8f0;"></div>

                <div style="display:flex; align-items:center; gap:14px;">
                    <div style="width:44px; height:44px; background:#eff6ff; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                    </div>
                    <div>
                        <div style="font-size:22px; font-weight:800; color:#0f172a; line-height:1;">25,000+</div>
                        <div style="font-size:13px; color:#64748b; margin-top:3px;">Safe Trips Completed</div>
                    </div>
                </div>

                <div style="width:1px; height:36px; background:#e2e8f0;"></div>

                <div style="display:flex; align-items:center; gap:14px;">
                    <div style="width:44px; height:44px; background:#eff6ff; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    </div>
                    <div>
                        <div style="font-size:22px; font-weight:800; color:#0f172a; line-height:1;">4.8/5</div>
                        <div style="font-size:13px; color:#64748b; margin-top:3px;">Parent Rating</div>
                    </div>
                </div>

            </div>
        </div>

    </section>

    <!-- ── FOOTER ── -->
    <footer style="background:#fff; border-top:1px solid #e5e7eb; padding:18px 32px;">
        <div style="max-width:1200px; margin:0 auto; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
            <p style="font-size:13px; color:#9ca3af;">&copy; {{ date('Y') }} SafeRide Kids. All rights reserved.</p>
            <div style="display:flex; gap:20px; font-size:13px;">
                <a href="#" style="color:#6b7280; text-decoration:none;">Privacy</a>
                <a href="#" style="color:#6b7280; text-decoration:none;">Terms</a>
                <a href="{{ route('login') }}" style="color:#2563eb; font-weight:500; text-decoration:none;">Admin Login</a>
            </div>
        </div>
    </footer>

</body>
</html>
