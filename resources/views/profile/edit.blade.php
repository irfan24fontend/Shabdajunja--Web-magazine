@extends('writer.layout')
@section('title', 'প্রোফাইল')

@section('content')

@if(session('status') === 'profile-updated')
    <div class="alert alert-success d-flex align-items-center gap-2 border-0 shadow-sm mb-4">
        <i class="bi bi-check-circle-fill"></i> প্রোফাইল আপডেট হয়েছে।
    </div>
@endif

<div class="row g-4">
    {{-- তথ্য আপডেট --}}
    <div class="col-lg-7">
        <div class="card overflow-hidden mb-4">
            <div class="panel-head"><i class="bi bi-person-fill" style="font-size:.85rem"></i> ব্যক্তিগত তথ্য</div>
            <div class="p-4">
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf @method('PATCH')

                    {{-- Avatar --}}
                    <div class="mb-4 d-flex align-items-center gap-4">
                        <img src="{{ $user->avatar_url }}" class="rounded-circle" width="72" height="72"
                             style="object-fit:cover;border:3px solid var(--brand-100)" alt="">
                        <div>
                            <label class="form-label fw-semibold small mb-1">প্রোফাইল ছবি</label>
                            <input type="file" name="avatar" accept="image/*" class="form-control form-control-sm @error('avatar') is-invalid @enderror">
                            <div class="hint mt-1">সর্বোচ্চ ১ MB। jpg/png/webp।</div>
                            @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- নাম --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">নাম</label>
                        <input name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus
                               class="form-control @error('name') is-invalid @enderror">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- ইমেইল --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">ইমেইল</label>
                        <input name="email" type="email" value="{{ old('email', $user->email) }}" required
                               class="form-control @error('email') is-invalid @enderror">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <div class="mt-2 small text-warning-emphasis">
                                ইমেইল যাচাই হয়নি।
                                <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="d-inline">@csrf</form>
                                <button form="send-verification" class="btn btn-sm btn-link p-0">যাচাই লিংক পাঠান</button>
                            </div>
                        @endif
                    </div>

                    {{-- Bio --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold small">পরিচিতি <span class="fw-normal text-secondary">(ঐচ্ছিক)</span></label>
                        <textarea name="bio" rows="4" maxlength="500"
                                  placeholder="নিজের সম্পর্কে কিছু লিখুন — এটি সাইটে আপনার লেখক পাতায় দেখাবে।"
                                  class="form-control @error('bio') is-invalid @enderror">{{ old('bio', $user->bio) }}</textarea>
                        @error('bio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="hint mt-1">সর্বোচ্চ ৫০০ অক্ষর।</div>
                    </div>

                    <button class="btn btn-brand px-4">সেভ করুন</button>
                </form>
            </div>
        </div>

        {{-- পাসওয়ার্ড --}}
        <div class="card overflow-hidden mb-4">
            <div class="panel-head"><i class="bi bi-lock-fill" style="font-size:.85rem"></i> পাসওয়ার্ড পরিবর্তন</div>
            <div class="p-4">
                @if(session('status') === 'password-updated')
                    <div class="alert alert-success border-0 small py-2 mb-3">পাসওয়ার্ড আপডেট হয়েছে।</div>
                @endif
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">বর্তমান পাসওয়ার্ড</label>
                        <input name="current_password" type="password"
                               class="form-control @error('current_password', 'updatePassword') is-invalid @enderror">
                        @error('current_password', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">নতুন পাসওয়ার্ড</label>
                        <input name="password" type="password"
                               class="form-control @error('password', 'updatePassword') is-invalid @enderror">
                        @error('password', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold small">পাসওয়ার্ড নিশ্চিত করুন</label>
                        <input name="password_confirmation" type="password" class="form-control">
                    </div>
                    <button class="btn btn-brand px-4">পাসওয়ার্ড পরিবর্তন করুন</button>
                </form>
            </div>
        </div>
    </div>

    {{-- ডান: অ্যাকাউন্ট তথ্য + বিপজ্জনক জোন --}}
    <div class="col-lg-5">
        {{-- অ্যাকাউন্ট তথ্য --}}
        <div class="card overflow-hidden mb-4">
            <div class="panel-head"><i class="bi bi-shield-check" style="font-size:.85rem"></i> অ্যাকাউন্ট তথ্য</div>
            <div class="p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="{{ $user->avatar_url }}" class="rounded-circle" width="56" height="56"
                         style="object-fit:cover" alt="">
                    <div>
                        <div class="fw-bold font-serif fs-5">{{ $user->name }}</div>
                        <div class="small text-secondary">{{ $user->email }}</div>
                    </div>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <span class="pill bg-brand-soft text-brand">
                        @php
                            $roles = ['admin' => 'Admin', 'editor' => 'Editor', 'writer' => 'Writer', 'reader' => 'Reader'];
                        @endphp
                        {{ $roles[$user->role] ?? $user->role }}
                    </span>
                    @if($user->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-brand">Admin প্যানেল →</a>
                    @endif
                </div>
                @if($user->bio)
                    <p class="small text-secondary mt-3 mb-0">{{ $user->bio }}</p>
                @endif
                <div class="hint mt-3">যোগ দিয়েছেন: {{ $user->created_at->format('d/m/Y') }}</div>
                <a href="{{ route('author.show', $user) }}" target="_blank" class="btn btn-sm btn-light border mt-3">
                    <i class="bi bi-person-lines-fill me-1"></i>লেখক পাতা দেখুন ↗
                </a>
            </div>
        </div>

        {{-- অ্যাকাউন্ট মুছুন --}}
        <div class="card overflow-hidden border-danger-subtle">
            <div class="panel-head" style="background:#b91c1c"><i class="bi bi-exclamation-triangle-fill" style="font-size:.85rem"></i> বিপজ্জনক জোন</div>
            <div class="p-4">
                <p class="small text-secondary mb-3">অ্যাকাউন্ট মুছে ফেললে আপনার সব তথ্য স্থায়ীভাবে চলে যাবে।</p>
                <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    অ্যাকাউন্ট মুছুন
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Delete Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold text-danger">অ্যাকাউন্ট মুছে ফেলুন</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf @method('DELETE')
                <div class="modal-body">
                    <p class="text-secondary small">একবার অ্যাকাউন্ট মুছলে ফিরিয়ে আনা যাবে না। নিশ্চিত করতে পাসওয়ার্ড দিন।</p>
                    <input name="password" type="password" placeholder="পাসওয়ার্ড"
                           class="form-control @error('password', 'userDeletion') is-invalid @enderror">
                    @error('password', 'userDeletion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">বাতিল</button>
                    <button class="btn btn-danger">অ্যাকাউন্ট মুছুন</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($errors->hasBag('userDeletion'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var el = document.getElementById('deleteModal');
        new bootstrap.Modal(el).show();
    });
</script>
@endif
@endsection
