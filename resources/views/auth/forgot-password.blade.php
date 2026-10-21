@extends('layouts.auth')
@section('title', 'পাসওয়ার্ড রিসেট')

@section('content')
<h1 class="font-serif h2 fw-bold">পাসওয়ার্ড ভুলে গেছেন?</h1>
<p class="text-secondary mb-4">ইমেইল দিন, রিসেট লিংক পাঠানো হবে।</p>
@if(session('status'))<div class="alert alert-success border-0">{{ session('status') }}</div>@endif
<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="ইমেইল" class="form-control form-control-lg mb-1 @error('email') is-invalid @enderror">
    @error('email') <div class="text-danger small mb-2">{{ $message }}</div> @enderror
    <button class="btn btn-brand btn-lg w-100 mt-3">রিসেট লিংক পাঠান</button>
</form>
<p class="text-center mt-4 mb-0 small"><a href="{{ route('login') }}" class="text-decoration-none">← লগইনে ফিরুন</a></p>
@endsection