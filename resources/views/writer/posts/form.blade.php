@extends('writer.layout')
@section('title', $post->exists ? 'লেখা সম্পাদনা' : 'নতুন লেখা')

@section('content')
@php
    $selectedTags = collect(old('tags', $post->exists ? $post->tags->pluck('id')->all() : []))->map(fn ($v) => (int) $v);
    $statusText = ['draft' => 'ড্রাফট', 'pending' => 'রিভিউয়ে', 'published' => 'প্রকাশিত'];
    $isLive = $post->exists && $post->status === 'published';
    $currentCat = old('category_id', $post->category_id ?? $categories->first()?->id);
@endphp

<form id="post-form" method="POST" enctype="multipart/form-data"
      action="{{ $post->exists ? route('writer.posts.update', $post) : route('writer.posts.store') }}">
    @csrf
    @if($post->exists) @method('PUT') @endif

    {{-- অ্যাকশন বার --}}
    <div class="actionbar mb-4">
        <a href="{{ route('writer.posts.index') }}">← আমার লেখা</a>
        @if($post->exists)
            <span class="pill bg-white text-dark" style="font-size:.78rem">
                {{ $statusText[$post->status] ?? 'নতুন' }}
            </span>
        @endif
        <div class="ms-auto d-flex flex-wrap gap-2">
            <button name="status" value="draft" class="btn btn-line btn-sm">ড্রাফট সেভ</button>
            <button name="status" value="pending" class="btn btn-publish">রিভিউয়ে পাঠান</button>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>কিছু তথ্য ঠিক নেই। নিচের লাল লেখাগুলো দেখুন।
        </div>
    @endif

    <div class="row g-4">
        {{-- বাম কলাম --}}
        <div class="col-xl-8">

            {{-- শিরোনাম --}}
            <div class="card overflow-hidden mb-4">
                <div class="panel-head"><i>১</i> শিরোনাম</div>
                <div class="p-4">
                    <input name="title" value="{{ old('title', $post->title) }}"
                           placeholder="লেখার শিরোনাম লিখুন"
                           class="form-control title-input @error('title') is-invalid @enderror">
                    @error('title')<div class="invalid-feedback fw-medium">{{ $message }}</div>@enderror

                    {{-- Slug --}}
                    <div class="mt-3 d-flex flex-wrap align-items-center gap-2 hint" data-slug>
                        <span class="fw-semibold text-dark">লিংক:</span>
                        <span>{{ url('/post') }}/</span>
                        <span data-slug-text class="text-brand fw-semibold">{{ old('slug', $post->slug) ?: 'শিরোনাম থেকে অটো হবে' }}</span>
                        <input data-slug-input name="slug" value="{{ old('slug', $post->slug) }}"
                               class="form-control d-none" style="width:260px">
                        <button type="button" data-slug-btn class="btn btn-sm border fw-semibold" style="font-size:.78rem">বদলান</button>
                    </div>
                    @error('slug')<p class="text-danger small mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- মূল লেখা --}}
            <div class="card overflow-hidden mb-4">
                <div class="panel-head"><i>২</i> মূল লেখা</div>
                <div class="p-3">
                    <x-editor name="body" :value="old('body', $post->body)" />
                    @error('body')<p class="text-danger small mt-2 fw-medium">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- সারসংক্ষেপ --}}
            <div class="card overflow-hidden mb-4">
                <div class="panel-head"><i>৩</i> সারসংক্ষেপ <span class="fw-normal opacity-75" style="font-size:.8rem">(ঐচ্ছিক)</span></div>
                <div class="p-4">
                    <p class="hint mb-2">হোমপেজ ও Facebook/WhatsApp শেয়ারে এটি দেখাবে। খালি রাখলে লেখা থেকে অটো নেবে।</p>
                    <textarea name="excerpt" rows="3" class="form-control @error('excerpt') is-invalid @enderror">{{ old('excerpt', $post->excerpt) }}</textarea>
                    @error('excerpt')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- ডান কলাম --}}
        <div class="col-xl-4">

            {{-- ক্যাটাগরি --}}
            <div class="card overflow-hidden mb-4">
                <div class="panel-head"><i>৪</i> ক্যাটাগরি</div>
                <div class="p-3" style="max-height:240px;overflow-y:auto">
                    @foreach($categories as $c)
                        <label class="d-flex align-items-center gap-3 py-2 px-2 rounded cursor-pointer" style="cursor:pointer">
                            <input type="radio" name="category_id" value="{{ $c->id }}"
                                   @checked($currentCat == $c->id) class="form-check-input mt-0" style="width:1.1rem;height:1.1rem">
                            <span class="fw-medium">{{ $c->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('category_id')<p class="text-danger small px-3 pb-2">{{ $message }}</p>@enderror
            </div>

            {{-- ট্যাগ --}}
            <div class="card overflow-hidden mb-4">
                <div class="panel-head"><i>৫</i> ট্যাগ</div>
                <div class="p-3">
                    @if($tags->isEmpty())
                        <p class="small text-secondary mb-0">কোনো ট্যাগ নেই।</p>
                    @else
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($tags as $t)
                                <label class="tag-chip" style="cursor:pointer">
                                    <input type="checkbox" name="tags[]" value="{{ $t->id }}"
                                           @checked($selectedTags->contains($t->id))>
                                    <span>{{ $t->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- কভার ছবি --}}
            <div class="card overflow-hidden mb-4">
                <div class="panel-head"><i>৬</i> কভার ছবি</div>
                <div class="p-3" data-cover>
                    @if($post->image_url)
                        <img data-cover-img src="{{ $post->image_url }}" class="img-fluid rounded mb-3 w-100" style="max-height:180px;object-fit:cover" alt="">
                    @else
                        <img data-cover-img src="" class="img-fluid rounded mb-3 w-100 d-none" style="max-height:180px;object-fit:cover" alt="">
                    @endif
                    <label class="cover-drop d-block">
                        <span data-cover-label>{{ $post->image_url ? 'ছবি বদলান' : '+ ছবি বেছে নিন' }}</span>
                        <input type="file" name="featured_image" accept="image/*" class="d-none">
                    </label>
                    <p class="hint mt-2">সর্বোচ্চ ২ MB। সেরা মাপ ১২০০×৬৩০।</p>
                    @error('featured_image')<p class="text-danger small mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Submit note --}}
            <div class="alert alert-info border-0 small">
                <i class="bi bi-info-circle me-1"></i>
                <strong>রিভিউয়ে পাঠান</strong> চাপলে Admin-এর কাছে পাঠানো হবে। অনুমোদনের পর সাইটে প্রকাশিত হবে।
            </div>

            @if($post->exists && $post->status !== 'published')
                <form method="POST" action="{{ route('writer.posts.destroy', $post) }}"
                      onsubmit="return confirm('লেখাটি মুছে ফেলবেন?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger w-100">
                        <i class="bi bi-trash me-1"></i>লেখাটি মুছুন
                    </button>
                </form>
            @endif
        </div>
    </div>
</form>
@endsection
