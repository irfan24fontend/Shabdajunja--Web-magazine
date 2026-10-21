@extends('layouts.auth')
@section('title', 'নতুন পাসওয়ার্ড')

@section('content')
<h1 class="font-serif h2 fw-bold mb-4">নতুন পাসওয়ার্ড দিন</h1>
<form method="POST" action="{{ route('password.store') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">
    <div class="mb-3">
        <input type="email" name="email" value="{{ old('email', $request->email) }}" required class="form-control form-control-lg @error('email') is-invalid @enderror">
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="mb-3">
        <input type="password" name="password" placeholder="নতুন পাসওয়ার্ড" required class="form-control form-control-lg @error('password') is-invalid @enderror">
        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <input type="password" name="password_confirmation" placeholder="আবার লিখুন" required class="form-control form-control-lg mb-4">
    <button class="btn btn-brand btn-lg w-100">পাসওয়ার্ড বদলান</button>
</form>
@endsection