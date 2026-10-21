@extends('admin.layout')
@section('title', 'লেখা')

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
                <a href="{{ route('admin.posts.index', array_filter(['status' => $key, 'q' => request('q')])) }}"
                   class="nav-link py-1 {{ (string) request('status') === (string) $key ? 'active bg-brand' : 'text-dark' }}">
                    {{ $name }} <small class="opacity-75">({{ $key === '' ? $counts->sum() : ($counts[$key] ?? 0) }})</small>
                </a>
            </li>
        @endforeach
    </ul>
    <div class="d-flex gap-2">
        <form method="GET" class="d-flex gap-2">
            <input type="hidden" name="status" value="{{ request('status') }}">
            <input name="q" value="{{ request('q') }}" placeholder="শিরোনাম খুঁজুন" class="form-control" style="width:220px">
            <button class="btn btn-light border">খুঁজুন</button>
        </form>
        <a href="{{ route('admin.posts.create') }}" class="btn btn-brand">+ নতুন লেখা</a>
    </div>
</div>

<div class="card"><div class="table-responsive">
    <table class="tbl">
        <thead><tr><th>লেখা</th><th>ক্যাটাগরি</th><th>Status</th><th>পাঠ</th><th></th></tr></thead>
        <tbody>
        @forelse($posts as $post)
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-3">
                        @if($post->image_url)<img src="{{ $post->image_url }}" class="thumb-sm" style="width:56px;height:56px" alt="">
                        @else<div class="thumb-sm thumb-sm-ph" style="width:56px;height:56px">শ</div>@endif
                        <div style="min-width:0">
                            <a href="{{ route('admin.posts.edit', $post) }}" class="fw-medium text-dark text-decoration-none clamp2">{{ $post->title }}</a>
                            <div class="small text-secondary">{{ $post->author->name }} · {{ $post->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                </td>
                <td><span class="pill bg-brand-soft text-brand">{{ $post->category->name }}</span></td>
                <td><span class="pill {{ $badge[$post->status] }}">{{ $label[$post->status] }}</span></td>
                <td class="small text-secondary"><i class="bi bi-eye"></i> {{ $post->views }}</td>
                <td class="text-end text-nowrap">
                    @if($post->status !== 'published')
                        <form method="POST" action="{{ route('admin.posts.publish', $post) }}" class="d-inline">@csrf @method('PATCH')
                            <button class="btn btn-brand btn-sm">Publish</button>
                        </form>
                    @else
                        <a href="{{ route('post.show', $post->slug) }}" target="_blank" class="btn btn-sm btn-light border">দেখুন</a>
                    @endif
                    <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-sm btn-outline-brand">Edit</a>
                    <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" class="d-inline" onsubmit="return confirm('সত্যিই মুছবেন?')">
                        @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-center text-secondary py-5">কোনো লেখা নেই।</td></tr>
        @endforelse
        </tbody>
    </table>
</div></div>

<div class="mt-4">{{ $posts->links() }}</div>
@endsection