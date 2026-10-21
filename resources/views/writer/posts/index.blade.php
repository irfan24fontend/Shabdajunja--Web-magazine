@extends('writer.layout')
@section('title', 'আমার লেখা')

@section('content')
@php
    $tabs  = ['' => 'সব', 'published' => 'প্রকাশিত', 'pending' => 'রিভিউয়ে', 'draft' => 'ড্রাফট'];
    $badge = ['published' => 'bg-success-subtle text-success-emphasis', 'pending' => 'bg-warning-subtle text-warning-emphasis', 'draft' => 'bg-secondary-subtle text-secondary-emphasis'];
    $label = ['published' => 'প্রকাশিত', 'pending' => 'রিভিউয়ে', 'draft' => 'ড্রাফট'];
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <ul class="nav nav-pills bg-white rounded-3 p-1 border">
        @foreach($tabs as $key => $name)
            <li class="nav-item">
                <a href="{{ route('writer.posts.index', array_filter(['status' => $key])) }}"
                   class="nav-link py-1 {{ (string) request('status') === (string) $key ? 'active bg-brand' : 'text-dark' }}">
                    {{ $name }} <small class="opacity-75">({{ $key === '' ? $counts->sum() : ($counts[$key] ?? 0) }})</small>
                </a>
            </li>
        @endforeach
    </ul>
    <a href="{{ route('writer.posts.create') }}" class="btn btn-brand">+ নতুন লেখা</a>
</div>

<div class="card"><div class="table-responsive">
    <table class="tbl">
        <thead><tr><th>লেখা</th><th>ক্যাটাগরি</th><th>অবস্থা</th><th>পাঠ</th><th></th></tr></thead>
        <tbody>
        @forelse($posts as $post)
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-3">
                        @if($post->image_url)
                            <img src="{{ $post->image_url }}" class="thumb-sm" style="width:56px;height:56px" alt="">
                        @else
                            <div class="thumb-sm thumb-sm-ph" style="width:56px;height:56px">শ</div>
                        @endif
                        <div style="min-width:0">
                            @if($post->status !== 'published')
                                <a href="{{ route('writer.posts.edit', $post) }}" class="fw-medium text-dark text-decoration-none clamp2">{{ $post->title }}</a>
                            @else
                                <a href="{{ route('post.show', $post->slug) }}" target="_blank" class="fw-medium text-dark text-decoration-none clamp2">{{ $post->title }}</a>
                            @endif
                            <div class="small text-secondary">{{ $post->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                </td>
                <td><span class="pill bg-brand-soft text-brand">{{ $post->category->name }}</span></td>
                <td><span class="pill {{ $badge[$post->status] }}">{{ $label[$post->status] }}</span></td>
                <td class="small text-secondary"><i class="bi bi-eye"></i> {{ $post->views }}</td>
                <td class="text-end text-nowrap">
                    @if($post->status === 'published')
                        <a href="{{ route('post.show', $post->slug) }}" target="_blank" class="btn btn-sm btn-light border">দেখুন ↗</a>
                    @else
                        <a href="{{ route('writer.posts.edit', $post) }}" class="btn btn-sm btn-outline-brand">সম্পাদনা</a>
                        <form method="POST" action="{{ route('writer.posts.destroy', $post) }}" class="d-inline" onsubmit="return confirm('লেখাটি মুছে ফেলবেন?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">মুছুন</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-center text-secondary py-5">
                কোনো লেখা নেই।
                <a href="{{ route('writer.posts.create') }}" class="text-brand fw-semibold">প্রথম লেখাটি শুরু করুন →</a>
            </td></tr>
        @endforelse
        </tbody>
    </table>
</div></div>

<div class="mt-4">{{ $posts->links() }}</div>

{{-- Writer info box --}}
<div class="card mt-4 border-0 bg-brand-soft">
    <div class="card-body p-4 d-flex gap-3 align-items-start">
        <i class="bi bi-info-circle-fill fs-4 text-brand mt-1"></i>
        <div>
            <div class="fw-semibold text-brand mb-1">লেখক হিসেবে আপনি যা করতে পারবেন</div>
            <ul class="small text-secondary mb-0 ps-3">
                <li>লেখা তৈরি করুন এবং <strong>রিভিউয়ে পাঠান</strong> — Admin অনুমোদনের পর সাইটে প্রকাশিত হবে।</li>
                <li>Draft ও রিভিউয়ে থাকা লেখা সম্পাদনা ও মুছতে পারবেন।</li>
                <li>প্রকাশিত লেখা সম্পাদনা করতে Admin-এর সাহায্য নিন।</li>
            </ul>
        </div>
    </div>
</div>
@endsection
