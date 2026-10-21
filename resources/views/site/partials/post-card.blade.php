<article class="card post-card h-100">
    <div class="position-relative">
        @if($post->image_url)
            <a href="{{ route('post.show', $post->slug) }}">
                <img src="{{ $post->image_url }}" class="thumb w-100" style="height:170px;object-fit:cover" alt="{{ $post->title }}">
            </a>
        @else
            <div class="thumb-ph" style="height:170px">শ</div>
        @endif
        <a href="{{ route('category.show', $post->category) }}" class="cat-badge text-decoration-none">{{ $post->category->name }}</a>
    </div>
    <div class="card-body d-flex flex-column p-3">
        <h3 class="h5 font-serif fw-bold mb-2 lh-sm clamp2">
            <a href="{{ route('post.show', $post->slug) }}" class="text-dark text-decoration-none">{{ $post->title }}</a>
        </h3>
        <p class="small text-secondary mb-3 clamp2">{{ $post->excerpt }}</p>
        <div class="small text-secondary mt-auto">
            <a href="{{ route('author.show', $post->author) }}" class="text-secondary text-decoration-none fw-medium">{{ $post->author->name }}</a>
            · {{ $post->bn_date }} · {{ $post->reading_time }} মিনিট
        </div>
    </div>
</article>
