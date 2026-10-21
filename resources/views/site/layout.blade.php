<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $siteName = 'শব্দকুঞ্জ';
        $ogTitle  = trim($__env->yieldContent('title', $siteName));
        $ogDesc   = trim($__env->yieldContent('description')) ?: 'শব্দকুঞ্জ: সাহিত্যের অনলাইন আঙিনা। গল্প, কবিতা, প্রবন্ধ, উপন্যাস ও আরও অনেক কিছু।';
        $ogImage  = trim($__env->yieldContent('og_image')) ?: asset('images/og-default.jpg');
        $ogType   = trim($__env->yieldContent('og_type')) ?: 'website';
    @endphp

    <title>{!! $ogTitle !!} | {{ $siteName }}</title>
    <meta name="description" content="{!! $ogDesc !!}">
    <link rel="canonical" href="{{ url()->current() }}">

    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:locale" content="bn_BD">
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:title" content="{!! $ogTitle !!}">
    <meta property="og:description" content="{!! $ogDesc !!}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{!! $ogTitle !!}">
    <meta name="twitter:description" content="{!! $ogDesc !!}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Noto+Serif+Bengali:wght@400;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="d-flex flex-column min-vh-100">

<header class="site-header sticky-top bg-white border-bottom">
    <div class="container d-flex align-items-center justify-content-between gap-3" style="height:68px">
        <a href="{{ route('home') }}" class="site-brand">শব্দকুঞ্জ<small>সাহিত্যের অনলাইন আঙিনা</small></a>

        <nav class="nav-site d-none d-lg-flex gap-4">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'on' : '' }}">হোম</a>
            @foreach($navCategories as $c)
                <a href="{{ route('category.show', $c) }}" class="{{ request()->is('category/' . $c->slug) ? 'on' : '' }}">{{ $c->name }}</a>
            @endforeach
        </nav>

        <div class="d-flex align-items-center gap-2">
            <form action="{{ route('search') }}" class="d-none d-md-block">
                <input name="q" value="{{ request('q') }}" placeholder="খুঁজুন..." class="form-control form-control-sm rounded-pill bg-light border-0 px-3" style="width:170px">
            </form>
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-brand">Admin</a>
                @endif
                <div class="dropdown">
                    <button class="btn btn-sm btn-light rounded-pill d-flex align-items-center gap-2 ps-1" data-bs-toggle="dropdown">
                        <img src="{{ auth()->user()->avatar_url }}" class="rounded-circle" width="26" height="26" alt="">
                        <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li><a class="dropdown-item" href="{{ route('writer.posts.index') }}">আমার লেখা</a></li>
                        <li><a class="dropdown-item" href="{{ route('writer.posts.create') }}">নতুন লেখা</a></li>
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">প্রোফাইল</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><form method="POST" action="{{ route('logout') }}">@csrf<button class="dropdown-item text-danger">Logout</button></form></li>
                    </ul>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-sm btn-light">Login</a>
                <a href="{{ route('register') }}" class="btn btn-sm btn-brand d-none d-sm-inline-block">Register</a>
            @endauth
            <button class="btn btn-light d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#mobMenu" aria-label="menu"><i class="bi bi-list fs-5"></i></button>
        </div>
    </div>
</header>

<div class="offcanvas offcanvas-start" id="mobMenu" style="background:var(--brand-900);color:#fff;width:280px">
    <div class="offcanvas-header">
        <span class="font-serif fs-4 fw-bold">শব্দকুঞ্জ</span>
        <button class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('search') }}" class="mb-3"><input name="q" class="form-control" placeholder="খুঁজুন..."></form>
        <a href="{{ route('home') }}" class="d-block py-2 text-white text-decoration-none">হোম</a>
        @foreach($navCategories as $c)
            <a href="{{ route('category.show', $c) }}" class="d-block py-2 text-white text-decoration-none">{{ $c->name }}</a>
        @endforeach
    </div>
</div>

<main class="flex-grow-1">
    @if(session('success'))
        <div class="container mt-3"><div class="alert alert-success border-0 shadow-sm mb-0">{{ session('success') }}</div></div>
    @endif
    @yield('content')
</main>

<footer class="site-footer mt-5">
    <div class="container py-5 row mx-auto align-items-center g-3">
        <div class="col-md-5">
            <div class="font-serif fs-3 fw-bold text-white">শব্দকুঞ্জ</div>
            <div class="small">সাহিত্যের অনলাইন আঙিনা</div>
        </div>
        <div class="col-md-7 d-flex flex-wrap gap-3 justify-content-md-end small">
            @foreach($navCategories as $c)<a href="{{ route('category.show', $c) }}">{{ $c->name }}</a>@endforeach
        </div>
    </div>
    <div class="text-center small py-3" style="border-top:1px solid rgba(255,255,255,.1)">
        © {{ date('Y') }} শব্দকুঞ্জ। সর্বস্বত্ব সংরক্ষিত। সাহিত্য আমাদের সবার।
    </div>
</footer>
</body>
</html>