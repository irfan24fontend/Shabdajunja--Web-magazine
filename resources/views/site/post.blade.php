@extends('site.layout')

@php
    $desc = \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($post->excerpt)))), 160);
    $img  = $post->image_url ?: asset('images/og-default.jpg');
    $schema = [
        '@context' => 'https://schema.org', '@type' => 'Article',
        'headline' => $post->title, 'description' => $desc, 'image' => [$img],
        'datePublished' => optional($post->published_at)->toIso8601String(),
        'dateModified' => $post->updated_at->toIso8601String(),
        'author' => ['@type' => 'Person', 'name' => $post->author->name],
        'publisher' => ['@type' => 'Organization', 'name' => 'শব্দকুঞ্জ', 'logo' => ['@type' => 'ImageObject', 'url' => asset('images/og-default.jpg')]],
        'mainEntityOfPage' => url()->current(),
    ];
    $shareUrl = urlencode(url()->current());
    $shareTitle = urlencode($post->title);
@endphp

@section('title', $post->title)
@section('description', $desc)
@section('og_type', 'article')
@section('og_image', $img)

@push('head')
    <meta property="article:published_time" content="{{ optional($post->published_at)->toIso8601String() }}">
    <meta property="article:modified_time" content="{{ $post->updated_at->toIso8601String() }}">
    <meta property="article:section" content="{{ $post->category->name }}">
    <meta property="article:author" content="{{ $post->author->name }}">
    <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

@section('content')
<div class="container py-4">
    <div class="small text-secondary mb-3">
        <a href="{{ route('home') }}" class="text-secondary text-decoration-none">হোম</a> ›
        <a href="{{ route('category.show', $post->category) }}" class="text-secondary text-decoration-none">{{ $post->category->name }}</a> ›
        <span>{{ $post->title }}</span>
    </div>

    @if($post->status !== 'published')
        <div class="alert alert-warning">এটি প্রিভিউ, লেখাটি এখনো প্রকাশিত হয়নি।</div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8" style="min-width:0">

            <div class="hero-img">
                @if($post->image_url)<img src="{{ $post->image_url }}" alt="{{ $post->title }}">@else<div style="height:260px"></div>@endif
                <div class="cap">
                    <span class="badge bg-brand align-self-start mb-2">{{ $post->category->name }}</span>
                    <h1 class="font-serif fw-bolder display-6">{{ $post->title }}</h1>
                </div>
            </div>

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ $post->author->avatar_url }}" class="rounded-circle" width="44" height="44" alt="">
                    <div>
                        <a href="{{ route('author.show', $post->author) }}" class="fw-medium text-dark text-decoration-none">{{ $post->author->name }}</a>
                        <div class="small text-secondary">লেখক</div>
                    </div>
                </div>
                <div class="small text-secondary d-flex flex-wrap gap-3">
                    <span><i class="bi bi-calendar3"></i> {{ $post->bn_date }}</span>
                    <span><i class="bi bi-clock"></i> {{ $post->reading_time }} মিনিট পাঠ</span>
                    <span><i class="bi bi-eye"></i> {{ $post->views }} পাঠক</span>
                </div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2 mt-3 pb-3 border-bottom">
                <button type="button" class="btn btn-brand btn-sm rounded-pill px-3"
                        onclick="if(navigator.share){navigator.share({title: document.title, url: location.href})}else{navigator.clipboard.writeText(location.href); this.textContent='লিংক কপি হয়েছে ✓'}">
                    <i class="bi bi-share"></i> শেয়ার করুন</button>
                <a target="_blank" rel="noopener" class="share-btn" style="background:#1877f2" title="Facebook" href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}"><i class="bi bi-facebook"></i></a>
                <a target="_blank" rel="noopener" class="share-btn" style="background:#000" title="X" href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareTitle }}"><i class="bi bi-twitter-x"></i></a>
                <a target="_blank" rel="noopener" class="share-btn" style="background:#25d366" title="WhatsApp" href="https://wa.me/?text={{ $shareTitle }}%20{{ $shareUrl }}"><i class="bi bi-whatsapp"></i></a>
                <a target="_blank" rel="noopener" class="share-btn" style="background:#0ea5e9" title="Telegram" href="https://t.me/share/url?url={{ $shareUrl }}&text={{ $shareTitle }}"><i class="bi bi-telegram"></i></a>
            </div>

            <div class="article-body mt-4">{!! $post->safe_body !!}</div>

            @if($post->tags->isNotEmpty())
                <div class="d-flex flex-wrap gap-2 mt-4">
                    @foreach($post->tags as $tag)<a href="{{ route('tag.show', $tag) }}" class="chip" style="background:var(--brand-50);color:var(--brand-700)">{{ $tag->name }}</a>@endforeach
                </div>
            @endif

            @if($post->author->bio)
                <div class="d-flex gap-3 bg-light rounded-4 p-3 mt-4 small">
                    <img src="{{ $post->author->avatar_url }}" class="rounded-circle" width="48" height="48" alt="">
                    <div><div class="fw-semibold">{{ $post->author->name }}</div><p class="text-secondary mb-0">{{ $post->author->bio }}</p></div>
                </div>
            @endif

            <section class="mt-5">
                <h2 class="font-serif h3 fw-bold mb-3">মন্তব্য ({{ $comments->count() + $comments->sum(fn ($c) => $c->replies->count()) }})</h2>

                @if($post->status === 'published')
                <form method="POST" action="{{ route('post.comment', $post->slug) }}" class="mb-4">
                    @csrf
                    @guest
                        <input name="guest_name" value="{{ old('guest_name') }}" placeholder="আপনার নাম" class="form-control mb-2 @error('guest_name') is-invalid @enderror">
                        @error('guest_name') <div class="text-danger small mb-2">{{ $message }}</div> @enderror
                    @endguest
                    <textarea name="body" rows="3" placeholder="আপনার মন্তব্য লিখুন..." class="form-control mb-2 @error('body') is-invalid @enderror">{{ old('body') }}</textarea>
                    @error('body') <div class="text-danger small mb-2">{{ $message }}</div> @enderror
                    <button class="btn btn-brand">মন্তব্য করুন</button>
                </form>
                @endif

                @foreach($comments as $c)
                    <div class="d-flex gap-3 mb-4">
                        <div class="avatar-letter">{{ mb_substr($c->display_name, 0, 1) }}</div>
                        <div class="flex-grow-1">
                            <div class="small"><span class="fw-medium">{{ $c->display_name }}</span> <span class="text-secondary">· {{ $c->created_at->diffForHumans() }}</span></div>
                            <p class="mb-1" style="white-space:pre-line">{{ $c->body }}</p>
                            @foreach($c->replies as $r)
                                <div class="ps-3 mt-2" style="border-left:2px solid var(--brand-100)">
                                    <div class="small"><span class="fw-medium">{{ $r->display_name }}</span> <span class="text-secondary">· {{ $r->created_at->diffForHumans() }}</span></div>
                                    <p class="mb-0" style="white-space:pre-line">{{ $r->body }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </section>
        </div>

        <aside class="col-lg-4">
            @if($related->isNotEmpty())
            <div class="card mb-4"><div class="card-body p-4">
                <h3 class="sec-title h5 mb-3">সম্পর্কিত লেখা</h3>
                @foreach($related as $p)
                    <a href="{{ route('post.show', $p->slug) }}" class="d-flex gap-3 py-2 text-decoration-none text-dark {{ !$loop->last ? 'border-bottom' : '' }}">
                        @if($p->image_url)<img src="{{ $p->image_url }}" class="thumb-sm" style="width:64px;height:64px" alt="">@endif
                        <div><div class="fw-medium">{{ $p->title }}</div><div class="small text-secondary">{{ $p->bn_date }}</div></div>
                    </a>
                @endforeach
            </div></div>
            @endif
            <div class="quote-card">
                <div class="font-serif fs-3 fw-bold lh-sm">বই হোক<br>জীবনের<br>সেরা সঙ্গী</div>
                <div class="small opacity-75 mt-3">— শব্দকুঞ্জ</div>
            </div>
        </aside>
    </div>
</div>
@endsection