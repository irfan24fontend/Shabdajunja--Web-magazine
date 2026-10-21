<article class="post-row d-flex gap-3 py-3 border-bottom">
    <a href="{{ route('post.show', $post->slug) }}">
        @if($post->image_url)<img src="{{ $post->image_url }}" class="row-thumb" alt="">
        @else<div class="row-thumb row-thumb-ph">শ</div>@endif
    </a>
    <div style="min-width:0">
        <a href="{{ route('category.show', $post->category) }}" class="small text-decoration-none fw-medium" style="color:var(--accent)">{{ $post->category->name }}</a>
        <h2 class="h5 font-serif fw-bold mt-1"><a href="{{ route('post.show', $post->slug) }}">{{ $post->title }}</a></h2>
        <div class="small text-secondary">
            <a href="{{ route('author.show', $post->author) }}" class="text-secondary text-decoration-none">{{ $post->author->name }}</a>
            · {{ $post->bn_date }} · {{ $post->reading_time }} মিনিট পড়ুন
        </div>
        <p class="small text-secondary mt-2 mb-0 d-none d-sm-block clamp2">{{ \Illuminate\Support\Str::limit($post->excerpt, 150) }}</p>
    </div>
</article>