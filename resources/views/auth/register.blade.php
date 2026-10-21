@extends('layouts.auth')
@section('title', 'রেজিস্টার')

@section('content')
<h1 class="font-serif h2 fw-bold">নতুন অ্যাকাউন্ট</h1>
<p class="text-secondary mb-4">মন্তব্য করতে ও লেখা জমা দিতে যোগ দিন।</p>

<form method="POST" action="{{ route('register') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label fw-semibold small">নাম</label>
        <input name="name" value="{{ old('name') }}" required autofocus class="form-control form-control-lg @error('name') is-invalid @enderror">
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="mb-3">
        <label class="form-label fw-semibold small">ইমেইল</label>
        <input type="email" name="email" value="{{ old('email') }}" required class="form-control form-control-lg @error('email') is-invalid @enderror">
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="mb-3">
        <label class="form-label fw-semibold small">পাসওয়ার্ড</label>
        <input type="password" name="password" required class="form-control form-control-lg @error('password') is-invalid @enderror">
        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="mb-4">
        <label class="form-label fw-semibold small">পাসওয়ার্ড আবার লিখুন</label>
        <input type="password" name="password_confirmation" required class="form-control form-control-lg">
    </div>
    <button class="btn btn-brand btn-lg w-100">রেজিস্টার</button>
</form>
<p class="text-center mt-4 mb-0 small">আগে থেকেই অ্যাকাউন্ট আছে? <a href="{{ route('login') }}" class="fw-semibold text-decoration-none">লগইন</a></p>
@endsection