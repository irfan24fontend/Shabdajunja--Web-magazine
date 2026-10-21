<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'আমার লেখা') - শব্দকুঞ্জ</title>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Noto+Serif+Bengali:wght@400;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin">

<div class="side-backdrop" data-side-toggle></div>

<aside class="admin-side">
    <a href="{{ route('home') }}" class="side-brand">
        <div class="logo">শ</div>
        <div>
            <div class="text-white fw-bold fs-5 lh-1 font-serif">শব্দকুঞ্জ</div>
            <div style="font-size:.7rem;color:#aed3c7" class="mt-1">লেখকের প্যানেল</div>
        </div>
    </a>

    <nav class="flex-grow-1 overflow-auto px-3 pb-3">
        <div class="side-group">লেখা</div>
        <a href="{{ route('writer.posts.index') }}" class="side-link {{ request()->routeIs('writer.posts.index') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-text"></i><span>আমার লেখা</span>
        </a>
        <a href="{{ route('writer.posts.create') }}" class="side-link {{ request()->routeIs('writer.posts.create') ? 'active' : '' }}">
            <i class="bi bi-plus-circle"></i><span>নতুন লেখা</span>
        </a>

        <div class="side-group mt-3">অ্যাকাউন্ট</div>
        <a href="{{ route('profile.edit') }}" class="side-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
            <i class="bi bi-person-circle"></i><span>প্রোফাইল</span>
        </a>
    </nav>

    <div class="px-3 pb-3">
        <a href="{{ route('writer.posts.create') }}" class="side-cta">
            <div class="fw-bold font-serif">নতুন লেখা</div>
            <div style="font-size:.78rem;opacity:.9">এখনই একটি লেখা শুরু করুন →</div>
        </a>
        <a href="{{ route('home') }}" target="_blank" class="d-block text-center mt-3 text-decoration-none" style="font-size:.8rem;color:#aed3c7">সাইট দেখুন ↗</a>
    </div>
</aside>

<div class="admin-main">
    <header class="admin-top">
        <button class="btn btn-light d-lg-none" data-side-toggle aria-label="menu"><i class="bi bi-list fs-5"></i></button>
        <h1 class="h5 fw-bold mb-0 text-truncate">@yield('title', 'আমার লেখা')</h1>
        <div class="d-flex align-items-center gap-2 ms-auto">
            <div class="dropdown">
                <button class="btn btn-light rounded-pill d-flex align-items-center gap-2 py-1 ps-1" data-bs-toggle="dropdown">
                    <img src="{{ auth()->user()->avatar_url }}" class="rounded-circle" width="32" height="32" alt="">
                    <span class="d-none d-sm-inline fw-medium">{{ auth()->user()->name }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}">প্রোফাইল</a></li>
                    <li><a class="dropdown-item" href="{{ route('home') }}" target="_blank">সাইট দেখুন ↗</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">@csrf
                            <button class="dropdown-item text-danger">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <main class="admin-content">
        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center gap-2 border-0 shadow-sm"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger d-flex align-items-center gap-2 border-0 shadow-sm"><i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}</div>
        @endif
        @yield('content')
    </main>
</div>
</body>
</html>
