@extends('admin.layout')
@section('title', 'ক্যাটাগরি')

@section('content')
@php $tints = ['g-green', 'g-blue', 'g-amber', 'g-violet', 'g-rose', 'g-blue', 'g-green']; @endphp
<div class="row g-4">
    <div class="col-xl-3">
        <form method="POST" action="{{ route('admin.categories.store') }}" class="card"><div class="card-body p-4">
            @csrf
            <h2 class="h5 fw-bold mb-3">নতুন ক্যাটাগরি</h2>
            <div class="mb-3">
                <label class="form-label fw-semibold small">নাম</label>
                <input name="name" value="{{ old('name') }}" placeholder="যেমন: গল্প" class="form-control @error('name') is-invalid @enderror">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold small">বিবরণ <span class="text-secondary fw-normal">(ঐচ্ছিক)</span></label>
                <textarea name="description" rows="3" class="form-control">{{ old('description') }}</textarea>
            </div>
            <button class="btn btn-brand w-100">যোগ করুন</button>
        </div></form>
    </div>
    <div class="col-xl-9">
        <div class="row row-cols-1 row-cols-md-2 row-cols-xxl-3 g-4">
        @foreach($categories as $c)
            <div class="col"><div class="card h-100">
                <div class="cat-tile {{ $tints[$loop->index % 7] }}">
                    <div class="font-serif fs-5 fw-bold">{{ $c->name }}</div>
                    <span class="badge rounded-pill" style="background:rgba(255,255,255,.25)">{{ $c->posts_count }}টি</span>
                </div>
                <div class="card-body">
                    <div class="small text-secondary text-truncate">/{{ $c->slug }}</div>
                    <p class="small mb-3 clamp2" style="min-height:2.6em">{{ $c->description ?: 'কোনো বিবরণ নেই' }}</p>
                    <div class="d-flex gap-2 border-top pt-3">
                        <a href="{{ route('admin.categories.edit', $c) }}" class="btn btn-sm btn-outline-brand">Edit</a>
                        <form method="POST" action="{{ route('admin.categories.destroy', $c) }}" onsubmit="return confirm('মুছবেন?')">
                            @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div></div>
        @endforeach
        </div>
    </div>
</div>
@endsection