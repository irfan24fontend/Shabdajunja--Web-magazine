@extends('admin.layout')
@section('title', 'ক্যাটাগরি সম্পাদনা')

@section('content')
<form method="POST" action="{{ route('admin.categories.update', $category) }}" class="card" style="max-width:560px"><div class="card-body p-4">
    @csrf @method('PUT')
    <div class="mb-3">
        <label class="form-label fw-semibold small">নাম</label>
        <input name="name" value="{{ old('name', $category->name) }}" class="form-control @error('name') is-invalid @enderror">
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="mb-3">
        <label class="form-label fw-semibold small">Slug</label>
        <input name="slug" value="{{ old('slug', $category->slug) }}" class="form-control @error('slug') is-invalid @enderror">
        @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="mb-3">
        <label class="form-label fw-semibold small">বিবরণ</label>
        <textarea name="description" rows="3" class="form-control">{{ old('description', $category->description) }}</textarea>
    </div>
    <button class="btn btn-brand">Save</button>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-light border">বাতিল</a>
</div></form>
@endsection