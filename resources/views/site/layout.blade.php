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

<header class="site-header sticky-top">
    <div class="site-topline">
        <div class="container d-flex justify-content-between align-items-center">
            <span>সাহিত্য · সংস্কৃতি · মনন</span>
            <span class="d-none d-sm-inline">শব্দের ভেতরেই আমাদের বসবাস</span>
        </div>
    </div>

    <div class="site-masthead bg-white">
        <div class="container site-masthead-inner">
            <a href="{{ route('home') }}" class="site-brand" aria-label="শব্দকুঞ্জ প্রচ্ছদ">
                <img src="https://i.postimg.cc/Y0jJNnsx/Swabdakunja-Logo-01-(1).png"
                     class="site-brand-logo" alt="শব্দকুঞ্জ — সাহিত্যের অনলাইন আঙিনা">
            </a>

            <div class="site-masthead-note d-none d-lg-block">
                <span class="site-note-rule"></span>
                <span>পড়ি · লিখি · ভাবি</span>
                <span class="site-note-rule"></span>
            </div>

            <div class="site-actions">
                <form action="{{ route('search') }}" class="site-search d-none d-md-flex">
                    <i class="bi bi-search" aria-hidden="true"></i>
                    <input name="q" value="{{ request('q') }}" placeholder="লেখা খুঁজুন..." aria-label="লেখা খুঁজুন">
                </form>
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-brand d-none d-sm-inline-flex">অ্যাডমিন</a>
                    @endif
                    <div class="dropdown">
                        <button class="btn btn-sm site-user-button dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ auth()->user()->avatar_url }}" class="rounded-circle" width="28" height="28" alt="">
                            <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <li><a class="dropdown-item" href="{{ route('writer.posts.index') }}">আমার লেখা</a></li>
                            <li><a class="dropdown-item" href="{{ route('writer.posts.create') }}">নতুন লেখা</a></li>
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">প্রোফাইল</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><form method="POST" action="{{ route('logout') }}">@csrf<button class="dropdown-item text-danger">লগআউট</button></form></li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="site-login d-none d-sm-inline-flex">লগইন</a>
                    <a href="{{ route('register') }}" class="btn btn-sm btn-brand site-join">লেখক হোন <i class="bi bi-arrow-up-right"></i></a>
                @endauth
                <button class="site-menu-toggle d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#mobMenu" aria-label="মেনু খুলুন"><i class="bi bi-list"></i></button>
            </div>
        </div>
    </div>

    <nav class="site-category-bar d-none d-lg-block" aria-label="প্রধান বিভাগ">
        <div class="container">
            <div class="site-category-nav">
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'on' : '' }}"><i class="bi bi-house-door"></i> প্রচ্ছদ</a>
                @foreach($navCategories as $c)
                    <a href="{{ route('category.show', $c) }}" class="{{ request()->is('category/' . $c->slug) ? 'on' : '' }}">{{ $c->name }}</a>
                @endforeach
            </div>
        </div>
    </nav>
</header>

<div class="offcanvas offcanvas-end site-mobile-menu" tabindex="-1" id="mobMenu" aria-labelledby="mobMenuLabel">
    <div class="offcanvas-header">
        <a href="{{ route('home') }}" class="mobile-menu-brand" id="mobMenuLabel" aria-label="শব্দকুঞ্জ প্রচ্ছদ">
            <img src="https://i.postimg.cc/Y0jJNnsx/Swabdakunja-Logo-01-(1).png" alt="শব্দকুঞ্জ" class="mobile-menu-logo">
        </a>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="বন্ধ করুন"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('search') }}" class="site-mobile-search mb-3"><i class="bi bi-search"></i><input name="q" value="{{ request('q') }}" placeholder="লেখা খুঁজুন..." aria-label="লেখা খুঁজুন"></form>
        <a href="{{ route('home') }}" class="site-mobile-link {{ request()->routeIs('home') ? 'on' : '' }}"><i class="bi bi-house-door"></i> প্রচ্ছদ</a>
        @foreach($navCategories as $c)
            <a href="{{ route('category.show', $c) }}" class="site-mobile-link {{ request()->is('category/' . $c->slug) ? 'on' : '' }}">{{ $c->name }}<i class="bi bi-arrow-up-left"></i></a>
        @endforeach
        @guest
            <div class="site-mobile-account"><a href="{{ route('login') }}">লগইন</a><a href="{{ route('register') }}" class="btn btn-brand">লেখক হোন</a></div>
        @endguest
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