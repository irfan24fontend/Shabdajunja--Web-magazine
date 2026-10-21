@extends('site.layout')
@section('title', 'প্রচ্ছদ')

@section('content')
<section class="site-hero">
    <div class="container py-5">
        <div class="py-md-4" style="max-width:640px">
            <div class="small opacity-75">সাহিত্যের ডিজিটাল আসর</div>
            <h1 class="font-serif display-3 fw-bolder mt-1">শব্দকুঞ্জ</h1>
            <div class="font-serif fs-5 mt-2">শব্দের ভুবনে, চিন্তার ডানায়</div>
            <p class="mt-3 opacity-75">কবিতা, গল্প, উপন্যাস, প্রবন্ধ, বই রিভিউ ও সাহিত্য আলোচনার একটি সচেতন ও সৃজনশীল প্ল্যাটফর্ম।</p>
            @auth
                <a href="{{ route('writer.posts.create') }}" class="btn px-4 text-white" style="background:var(--accent)">নতুন লেখা লিখুন →</a>
            @else
                <a href="{{ route('register') }}" class="btn px-4 text-white" style="background:var(--accent)">লেখক হিসেবে যোগ দিন →</a>
            @endauth
        </div>
    </div>
</section>

<div class="container mt-5">
    <div class="row g-4">
        <div class="col-lg-8">
            <h2 class="sec-title h3 mb-4">সাম্প্রতিক প্রকাশনা</h2>

            @if($featured)
            <article class="card mb-4 overflow-hidden"><div class="row g-0">
                @if($featured->image_url)
                    <div class="col-md-6 position-relative">
                        <a href="{{ route('post.show', $featured->slug) }}"><img src="{{ $featured->image_url }}" class="w-100 h-100" style="object-fit:cover;min-height:240px" alt=""></a>
                        <span class="cat-badge" style="background:var(--accent)">{{ $featured->category->name }}</span>
                    </div>
                @endif
                <div class="{{ $featured->image_url ? 'col-md-6' : 'col-12' }}"><div class="card-body p-4 d-flex flex-column justify-content-center h-100">
                    @unless($featured->image_url)<span class="small fw-medium" style="color:var(--accent)">{{ $featured->category->name }}</span>@endunless
                    <h3 class="font-serif fw-bold h3"><a href="{{ route('post.show', $featured->slug) }}" class="text-dark text-decoration-none">{{ $featured->title }}</a></h3>
                    <p class="text-secondary">{{ \Illuminate\Support\Str::limit($featured->excerpt, 160) }}</p>
                    <div class="d-flex align-items-center gap-2 small text-secondary">
                        <img src="{{ $featured->author->avatar_url }}" class="rounded-circle" width="30" height="30" alt="">
                        <a href="{{ route('author.show', $featured->author) }}" class="text-dark text-decoration-none">{{ $featured->author->name }}</a>
                        <span>· {{ $featured->bn_date }} · {{ $featured->reading_time }} মিনিট</span>
                    </div>
                </div></div>
            </div></article>
            @endif

            <div class="row row-cols-1 row-cols-sm-2 g-4">
                @forelse($latest as $post)
                    <div class="col">@include('site.partials.post-card', ['post' => $post])</div>
                @empty
                    <p class="text-secondary">এখনো কোনো লেখা প্রকাশিত হয়নি।</p>
                @endforelse
            </div>
            <div class="mt-4">{{ $latest->links() }}</div>
        </div>

        <aside class="col-lg-4">
            <div class="card mb-4"><div class="card-body p-4">
                <h3 class="sec-title h5 mb-3">জনপ্রিয় লেখা</h3>
                @forelse($popular as $i => $p)
                    <a href="{{ route('post.show', $p->slug) }}" class="d-flex gap-3 py-2 text-decoration-none text-dark {{ !$loop->last ? 'border-bottom' : '' }}">
                        <span class="font-serif fs-3 fw-bold" style="color:var(--brand-100)">{{ $i + 1 }}</span>
                        <div><div class="fw-medium">{{ $p->title }}</div><div class="small text-secondary">{{ $p->views }} বার পড়া হয়েছে</div></div>
                    </a>
                @empty
                    <p class="small text-secondary mb-0">কিছু নেই।</p>
                @endforelse
            </div></div>

            <div class="quote-card">
                <div class="font-serif fs-3 fw-bold lh-sm">বই হোক<br>জীবনের<br>সেরা সঙ্গী</div>
                <div class="small opacity-75 mt-3">— শব্দকুঞ্জ</div>
            </div>
        </aside>
    </div>
</div>

<section class="container mt-5">
    <h2 class="sec-title h3 mb-4">বিভাগসমূহ</h2>
    @php $tints = ['danger', 'success', 'primary', 'info', 'warning', 'secondary', 'dark']; @endphp
    <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-3">
        @foreach($navCategories as $i => $c)
            <div class="col">
                <a href="{{ route('category.show', $c) }}" class="d-block text-center text-decoration-none rounded-4 p-4 fw-bold font-serif bg-{{ $tints[$i % 7] }}-subtle text-{{ $tints[$i % 7] }}-emphasis">{{ $c->name }}</a>
            </div>
        @endforeach
    </div>
</section>

@foreach($sections as $section)
<section class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="sec-title h3 mb-0">{{ $section->name }}</h2>
        <a href="{{ route('category.show', $section) }}" class="small text-decoration-none">সব দেখুন →</a>
    </div>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
        @foreach($section->latestPosts as $post)
            @php $post->setRelation('category', $section); @endphp
            <div class="col">@include('site.partials.post-card', ['post' => $post])</div>
        @endforeach
    </div>
</section>
@endforeach
@endsection