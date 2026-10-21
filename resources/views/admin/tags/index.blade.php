@extends('admin.layout')
@section('title', 'ট্যাগ')

@section('content')
<div class="row g-4">
    <div class="col-xl-3">
        <form method="POST" action="{{ route('admin.tags.store') }}" class="card"><div class="card-body p-4">
            @csrf
            <h2 class="h5 fw-bold mb-3">নতুন ট্যাগ</h2>
            <input name="name" value="{{ old('name') }}" placeholder="যেমন: বর্ষা" class="form-control mb-3 @error('name') is-invalid @enderror">
            @error('name') <div class="text-danger small mb-2">{{ $message }}</div> @enderror
            <button class="btn btn-brand w-100">যোগ করুন</button>
        </div></form>
    </div>
    <div class="col-xl-9">
        <div class="card"><div class="card-body p-4">
            <h2 class="h5 fw-bold mb-3">সব ট্যাগ ({{ $tags->count() }})</h2>
            <div class="d-flex flex-wrap gap-3">
                @forelse($tags as $t)
                    <div class="tag-pill">
                        <span class="fw-medium text-brand">#{{ $t->name }}</span>
                        <span class="badge bg-white text-secondary rounded-pill">{{ $t->posts_count }}</span>
                        <a href="{{ route('admin.tags.edit', $t) }}" class="small text-decoration-none">Edit</a>
                        <form method="POST" action="{{ route('admin.tags.destroy', $t) }}" onsubmit="return confirm('মুছবেন?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-link text-danger p-0 px-1" title="Delete"><i class="bi bi-x-circle-fill"></i></button>
                        </form>
                    </div>
                @empty
                    <p class="small text-secondary mb-0">এখনো কোনো ট্যাগ নেই।</p>
                @endforelse
            </div>
        </div></div>
    </div>
</div>
@endsection