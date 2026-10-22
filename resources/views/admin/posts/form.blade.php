@extends('admin.layout')
@section('title', $post->exists ? 'লেখা সম্পাদনা' : 'নতুন লেখা')

@section('content')
@php
    $selectedTags = collect(old('tags', $post->exists ? $post->tags->pluck('id')->all() : []))->map(fn ($v) => (int) $v);
    $statusText   = ['draft' => 'ড্রাফট', 'pending' => 'রিভিউয়ে', 'published' => 'প্রকাশিত'];
    $pillCls      = ['draft' => 'bg-secondary-subtle text-secondary-emphasis', 'pending' => 'bg-warning-subtle text-warning-emphasis', 'published' => 'bg-success-subtle text-success-emphasis'];
    $isLive       = $post->exists && $post->status === 'published';
    $hasDate      = $post->exists && $post->published_at;
    $currentCat   = old('category_id', $post->category_id ?? $categories->first()?->id);
@endphp

<form id="post-form" method="POST" enctype="multipart/form-data"
      action="{{ $post->exists ? route('admin.posts.update', $post) : route('admin.posts.store') }}">
    @csrf
    @if($post->exists) @method('PUT') @endif

    {{-- অ্যাকশন বার --}}
    <div class="actionbar mb-4">
        <a href="{{ route('admin.posts.index') }}">← সব লেখা</a>
        @if($post->exists)
            <span class="pill {{ $pillCls[$post->status] }}">{{ $statusText[$post->status] }}</span>
        @else
            <span class="hint">নতুন লেখা</span>
        @endif

        <div class="ms-auto d-flex flex-wrap gap-2">
            @if($post->exists)
                <a href="{{ route('post.show', $post->slug) }}" target="_blank" class="btn btn-line btn-sm">প্রিভিউ ↗</a>
            @endif
            @if($isLive)
                <button name="status" value="draft" class="btn btn-line btn-sm">ড্রাফটে নিন</button>
                <button name="status" value="published" class="btn btn-publish">আপডেট করুন</button>
            @else
                <button name="status" value="draft" class="btn btn-line btn-sm">ড্রাফট সেভ</button>
                <button name="status" value="pending" class="btn btn-line btn-sm">রিভিউয়ে পাঠান</button>
                <button name="status" value="published" class="btn btn-publish">প্রকাশ করুন</button>
            @endif
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
                    <p class="hint mb-2">হোমপেজ, সার্চ ও Facebook/WhatsApp শেয়ারে এটি দেখাবে। খালি রাখলে লেখা থেকে অটো নেবে।</p>
                    <textarea name="excerpt" rows="3" class="form-control @error('excerpt') is-invalid @enderror">{{ old('excerpt', $post->excerpt) }}</textarea>
                    @error('excerpt')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- ডান কলাম --}}
        <div class="col-xl-4">

            {{-- প্রকাশের সময় --}}
            <div class="card overflow-hidden mb-4">
                <div class="panel-head"><i>৪</i> প্রকাশের সময়</div>
                <div class="p-4" data-sched data-on="{{ $hasDate && $post->published_at->isFuture() ? '1' : '0' }}">
                    <p class="small fw-semibold mb-2" data-sched-now>
                        @if($hasDate) {{ $post->published_at->format('d/m/Y, h:i A') }} @else এখনই প্রকাশ হবে @endif
                    </p>
                    <div data-sched-box class="d-none">
                        <input type="datetime-local" name="published_at"
                               value="{{ old('published_at', optional($post->published_at)->format('Y-m-d\TH:i')) }}"
                               class="form-control form-control-sm" disabled>
                        <p class="hint mt-1">ভবিষ্যতের সময় দিলে ওই সময়ে সাইটে দেখা যাবে।</p>
                    </div>
                    <button type="button" data-sched-btn class="btn btn-link p-0 small fw-semibold text-brand text-decoration-none mt-1">
                        🕒 সময় ঠিক করুন
                    </button>
                    @error('published_at')<p class="text-danger small mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- ক্যাটাগরি --}}
            <div class="card overflow-hidden mb-4">
                <div class="panel-head"><i>৫</i> ক্যাটাগরি</div>
                <div class="p-3" style="max-height:240px;overflow-y:auto">
                    @foreach($categories as $c)
                        <label class="d-flex align-items-center gap-3 py-2 px-2 rounded" style="cursor:pointer">
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
                <div class="panel-head"><i>৬</i> ট্যাগ</div>
                <div class="p-3">
                    @if($tags->isEmpty())
                        <p class="small text-secondary mb-0">
                            কোনো ট্যাগ নেই।
                            <a href="{{ route('admin.tags.index') }}" class="text-brand fw-semibold">যোগ করুন</a>
                        </p>
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
                <div class="panel-head"><i>৭</i> কভার ছবি</div>
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

            @if($post->exists)
                <button type="button"
                        onclick="if(confirm('লেখাটি মুছে ফেলবেন?')){document.getElementById('delete-form').submit();}"
                        class="btn btn-outline-danger w-100">
                    <i class="bi bi-trash me-1"></i>লেখাটি মুছুন
                </button>
            @endif
        </div>
    </div>
</form>

{{-- Delete form OUTSIDE main form --}}
@if($post->exists)
<form id="delete-form" method="POST" action="{{ route('admin.posts.destroy', $post) }}" class="d-none">
    @csrf @method('DELETE')
</form>
@endif
@endsection
