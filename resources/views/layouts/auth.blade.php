<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'অ্যাকাউন্ট') | শব্দকুঞ্জ</title>

    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Noto+Serif+Bengali:wght@400;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-vh-100 d-flex flex-column" style="background:var(--brand-50)">

<main class="flex-grow-1 d-flex align-items-center justify-content-center py-5 px-3">
    <div class="w-100" style="max-width:440px">
        <a href="{{ route('home') }}" class="d-block text-center text-decoration-none mb-4">
            <span class="font-serif fs-2 fw-bold text-brand">শব্দকুঞ্জ</span>
            <small class="d-block text-secondary">সাহিত্যের অনলাইন আঙিনা</small>
        </a>

        <div class="card border-0 p-4 p-sm-5">
            @yield('content')
        </div>

        <p class="text-center mt-4 mb-0 small">
            <a href="{{ route('home') }}" class="text-decoration-none text-secondary">← হোমে ফিরে যান</a>
        </p>
    </div>
</main>

</body>
</html>