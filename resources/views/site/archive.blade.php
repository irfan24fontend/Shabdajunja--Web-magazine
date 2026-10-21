@extends('site.layout')
@section('title', $heading)
@section('description', ($sub ?? null) ?: $heading . ' | শব্দকুঞ্জ এর লেখাসমূহ')

@section('content')
@php
    $isCategory = isset($category);
    $tags       = $tags ?? collect();
    $sort       = $sort ?? 'latest';
    $activeTag  = $activeTag ?? null;
@endphp

{{-- ব্যানার --}}
<section class="page-banner py-5">
    <div class="container">
        <div class="small opacity-75 mb-2">
            <a href="{{ route('home') }}" class="text-white text-decoration-none">হোম</a> › {{ $heading }}
        </div>
        <h1 class="font-serif display-6 fw-bold mb-2">{{ $heading }}</h1>
        @if(isset($sub) && $sub)
            <p class="opacity-90 mb-2">{{ $sub }}</p>
        @endif
        <p class="small opacity-70 mb-0">মোট {{ $posts->total() }}টি লেখা</p>
    </div>
</section>

<div class="container py-5">
    <div class="row g-4">
        {{-- মূল তালিকা --}}
        <div class="col-lg-8">

            {{-- ট্যাগ ফিল্টার (শুধু category page এ) --}}
            @if($isCategory && $tags->isNotEmpty())
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('category.show', ['category' => $category, 'sort' => $sort === 'popular' ? 'popular' : null]) }}"
                           class="chip {{ !$activeTag ? 'on' : '' }}">সব</a>
                        @foreach($tags as $t)
                            <a href="{{ route('category.show', ['category' => $category, 'tag' => $t->slug, 'sort' => $sort === 'popular' ? 'popular' : null]) }}"
                               class="chip {{ $activeTag === $t->slug ? 'on' : '' }}">{{ $t->name }}</a>
                        @endforeach
                    </div>
                    <div class="d-flex gap-3">
                        <a href="{{ route('category.show', ['category' => $category, 'tag' => $activeTag]) }}"
                           class="tab-link {{ $sort === 'latest' ? 'on' : '' }}">নতুন</a>
                        <a href="{{ route('category.show', ['category' => $category, 'tag' => $activeTag, 'sort' => 'popular']) }}"
                           class="tab-link {{ $sort === 'popular' ? 'on' : '' }}">জনপ্রিয়</a>
                    </div>
                </div>
            @endif

            @forelse($posts as $post)
                @include('site.partials.post-row', ['post' => $post])
            @empty
                <div class="text-center py-5 text-secondary">
                    <i class="bi bi-search fs-1 d-block mb-3 opacity-50"></i>
                    কোনো লেখা পাওয়া যায়নি।
                </div>
            @endforelse

            <div class="mt-4">{{ $posts->links() }}</div>
        </div>

        {{-- সাইডবার --}}
        <aside class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body p-4">
                    <h3 class="sec-title h5 mb-3">বিভাগসমূহ</h3>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($navCategories as $c)
                            <a href="{{ route('category.show', $c) }}"
                               class="chip {{ isset($category) && $category->id === $c->id ? 'on' : '' }}">{{ $c->name }}</a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="quote-card">
                <div class="font-serif fs-3 fw-bold lh-sm">বই হোক<br>জীবনের<br>সেরা সঙ্গী</div>
                <div class="small opacity-70 mt-3">— শব্দকুঞ্জ</div>
            </div>
        </aside>
    </div>
</div>
@endsection
