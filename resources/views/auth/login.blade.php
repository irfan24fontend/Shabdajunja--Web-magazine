@extends('layouts.auth')
@section('title', 'লগইন')

@section('content')
<h1 class="font-serif h2 fw-bold">লগইন করুন</h1>
<p class="text-secondary mb-4">আপনার অ্যাকাউন্টে প্রবেশ করুন।</p>

@if(session('status'))<div class="alert alert-success border-0">{{ session('status') }}</div>@endif

<form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label fw-semibold small">ইমেইল</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus class="form-control form-control-lg @error('email') is-invalid @enderror">
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="mb-3">
        <label class="form-label fw-semibold small">পাসওয়ার্ড</label>
        <input type="password" name="password" required class="form-control form-control-lg @error('password') is-invalid @enderror">
        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="d-flex justify-content-between align-items-center mb-4 small">
        <label class="mb-0"><input type="checkbox" name="remember" class="form-check-input me-1"> মনে রাখুন</label>
        @if(Route::has('password.request'))<a href="{{ route('password.request') }}" class="text-decoration-none">পাসওয়ার্ড ভুলে গেছেন?</a>@endif
    </div>
    <button class="btn btn-brand btn-lg w-100">লগইন</button>
</form>
<p class="text-center mt-4 mb-0 small">অ্যাকাউন্ট নেই? <a href="{{ route('register') }}" class="fw-semibold text-decoration-none">রেজিস্টার করুন</a></p>
@endsection