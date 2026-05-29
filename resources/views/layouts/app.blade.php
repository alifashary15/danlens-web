<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Home') &mdash; DanLens</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        @font-face {
            font-family: 'Utendo';
            src: url('/fonts/Utendo-Regular.ttf') format('truetype');
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Utendo';
            src: url('/fonts/Utendo-Bold.ttf') format('truetype');
            font-weight: 700;
            font-style: normal;
            font-display: swap;
        }
    </style>

    <style>
        :root {
            --matcha-deep:   #2e5240;
            --matcha:        #4a7c59;
            --matcha-mid:    #6b9e7a;
            --matcha-light:  #a8c5b0;
            --matcha-pale:   #d6e8db;
            --matcha-ghost:  #f0f7f2;
            --white:         #ffffff;
            --ink:           #1a2e24;
            --ink-mid:       #3d5a4a;
            --ink-soft:      #6b8a78;
            --border:        #ddeee4;
            --shadow-sm:     0 2px 8px rgba(46,82,64,.07);
            --shadow-md:     0 6px 24px rgba(46,82,64,.12);
            --shadow-lg:     0 16px 48px rgba(46,82,64,.16);
            --radius-sm:     8px;
            --radius-md:     14px;
            --radius-lg:     22px;
            --font-display:  'Utendo', Georgia, serif;
            --font-body:     'DM Sans', system-ui, sans-serif;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            font-family: var(--font-body);
            font-size: 15px;
            line-height: 1.65;
            color: var(--ink);
            background: var(--white);
            -webkit-font-smoothing: antialiased;
        }
        a { color: inherit; text-decoration: none; }

        /* ── NAVBAR ── */
        .navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 900;
            height: 64px;
            display: flex;
            align-items: center;
            padding: 0 40px;
            background: rgba(255,255,255,.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            transition: box-shadow .3s;
        }
        .navbar.scrolled { box-shadow: var(--shadow-sm); }

        .navbar-brand {
            font-family: var(--font-display);
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--matcha-deep);
            letter-spacing: -.01em;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .navbar-links {
            margin-left: auto;
            display: flex;
            gap: 6px;
            list-style: none;
        }
        .navbar-links a {
            display: block;
            padding: 7px 18px;
            border-radius: 100px;
            font-size: .875rem;
            font-weight: 500;
            color: var(--ink-mid);
            transition: background .2s, color .2s;
        }
        .navbar-links a:hover { background: var(--matcha-ghost); color: var(--matcha-deep); }
        .navbar-links a.active {
            background: var(--matcha-pale);
            color: var(--matcha-deep);
            font-weight: 600;
        }

        /* ── HAMBURGER ── */
        .navbar-toggle {
            display: none;
            margin-left: auto;
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px;
            border-radius: var(--radius-sm);
            color: var(--ink);
            transition: background .2s;
        }
        .navbar-toggle:hover { background: var(--matcha-ghost); }
        .navbar-toggle svg { display: block; }

        /* ── MOBILE MENU ── */
        .mobile-menu {
            display: none;
            position: fixed;
            top: 64px; left: 0; right: 0;
            background: rgba(255,255,255,.97);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            z-index: 899;
            padding: 12px 20px 20px;
            flex-direction: column;
            gap: 4px;
            box-shadow: var(--shadow-md);
        }
        .mobile-menu.open { display: flex; }
        .mobile-menu a {
            display: block;
            padding: 12px 16px;
            border-radius: var(--radius-sm);
            font-size: .95rem;
            font-weight: 500;
            color: var(--ink-mid);
            transition: background .2s, color .2s;
        }
        .mobile-menu a:hover { background: var(--matcha-ghost); color: var(--matcha-deep); }
        .mobile-menu a.active {
            background: var(--matcha-pale);
            color: var(--matcha-deep);
            font-weight: 600;
        }

        /* ── PAGE CONTENT ── */
        .page-content { padding-top: 64px; min-height: 100vh; }

        /* ── FOOTER ── */
        .footer {
            background: var(--matcha-deep);
            color: rgba(255,255,255,.7);
            text-align: center;
            padding: 28px 40px;
            font-size: .82rem;
            letter-spacing: .02em;
        }
        .footer strong { color: var(--white); }

        /* ── UTILITY ── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 26px;
            border-radius: 100px;
            font-family: var(--font-body);
            font-size: .875rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform .18s, box-shadow .18s, background .18s;
            border: none;
        }
        .btn:hover { transform: translateY(-1px); }
        .btn-primary {
            background: var(--matcha);
            color: var(--white);
            box-shadow: 0 4px 16px rgba(74,124,89,.3);
        }
        .btn-primary:hover {
            background: var(--matcha-deep);
            box-shadow: 0 6px 22px rgba(74,124,89,.4);
        }
        .btn-outline {
            background: transparent;
            color: var(--matcha-deep);
            border: 1.5px solid var(--matcha-light);
        }
        .btn-outline:hover {
            background: var(--matcha-ghost);
            border-color: var(--matcha-mid);
        }

        .badge {
            display: inline-block;
            padding: 3px 11px;
            border-radius: 100px;
            font-size: .75rem;
            font-weight: 600;
            letter-spacing: .03em;
        }
        .badge-kuliner        { background: #fef3c7; color: #92400e; }
        .badge-wisata         { background: #dbeafe; color: #1e40af; }
        .badge-kesehatan      { background: #fce7f3; color: #9d174d; }
        .badge-kemasyarakatan { background: #ede9fe; color: #4c1d95; }
        .badge-transportasi   { background: #d1fae5; color: #065f46; }
        .badge-default        { background: var(--matcha-pale); color: var(--matcha-deep); }

        /* ── RESPONSIVE ── */
        @media (max-width: 640px) {
            .navbar { padding: 0 20px; }
            .navbar-links { display: none; }
            .navbar-toggle { display: block; }
            .footer { padding: 20px; }
        }
    </style>

    @stack('styles')
</head>
<body class="{{ request()->routeIs('maps') ? 'maps-page' : '' }}">

<nav class="navbar" id="mainNav">
    <a href="{{ route('home') }}" class="navbar-brand">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="color:var(--matcha)">
            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
            <circle cx="12" cy="13" r="4"/>
        </svg>
        DanLens
    </a>

    <ul class="navbar-links">
        <li>
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
                Home
            </a>
        </li>
        <li>
            <a href="{{ route('maps') }}" class="{{ request()->routeIs('maps') ? 'active' : '' }}">
                Maps
            </a>
        </li>
    </ul>

    <button class="navbar-toggle" id="navToggle" aria-label="Toggle menu">
        <svg id="iconHamburger" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
        <svg id="iconClose" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display:none">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
</nav>

<div class="mobile-menu" id="mobileMenu">
    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">🏠 Home</a>
    <a href="{{ route('maps') }}" class="{{ request()->routeIs('maps') ? 'active' : '' }}">📍 Maps</a>
</div>

<main class="page-content">
    @yield('content')
</main>

<footer class="footer">
    &copy; {{ date('Y') }} <strong>DanLens</strong> &mdash; Sistem Informasi Geografis Kota Medan
</footer>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    const nav = document.getElementById('mainNav');
    window.addEventListener('scroll', () => {
        nav.classList.toggle('scrolled', window.scrollY > 10);
    });

    const toggle       = document.getElementById('navToggle');
    const mobileMenu   = document.getElementById('mobileMenu');
    const iconHamburger = document.getElementById('iconHamburger');
    const iconClose    = document.getElementById('iconClose');

    toggle.addEventListener('click', () => {
        const isOpen = mobileMenu.classList.toggle('open');
        iconHamburger.style.display = isOpen ? 'none' : 'block';
        iconClose.style.display     = isOpen ? 'block' : 'none';
    });

    mobileMenu.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            mobileMenu.classList.remove('open');
            iconHamburger.style.display = 'block';
            iconClose.style.display     = 'none';
        });
    });
</script>

@stack('scripts')
</body>
</html>