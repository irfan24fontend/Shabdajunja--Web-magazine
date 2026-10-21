@extends('admin.layout')
@section('title', 'Role পরিবর্তন')

@section('content')
<div class="card" style="max-width:520px">
    <div class="panel-head"><i class="bi bi-shield-fill" style="font-size:.85rem"></i> Role পরিবর্তন</div>
    <div class="card-body p-4">
        <div class="d-flex align-items-center gap-3 mb-4">
            <img src="{{ $user->avatar_url }}" class="rounded-circle" width="56" height="56" alt="">
            <div>
                <div class="font-serif fw-bold fs-5">{{ $user->name }}</div>
                <div class="small text-secondary">{{ $user->email }}</div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf @method('PUT')
            @php
                $roles = [
                    'admin'  => ['Admin',  'bi-shield-fill text-danger',   'সব কিছু নিয়ন্ত্রণ — লেখা প্রকাশ, ইউজার ম্যানেজ'],
                    'editor' => ['Editor', 'bi-pencil-fill text-warning',   'লেখা অনুমোদন ও সম্পাদনা করতে পারবে'],
                    'writer' => ['Writer', 'bi-pen-fill text-brand',        'লেখা জমা দিতে পারবে, অনুমোদনের পর প্রকাশ হবে'],
                    'reader' => ['Reader', 'bi-eye-fill text-secondary',    'শুধু মন্তব্য করতে পারবে'],
                ];
            @endphp
            @foreach($roles as $v => [$label, $icon, $desc])
                <label class="d-flex align-items-start gap-3 border rounded-3 p-3 mb-2 @if($user->role === $v) border-brand @endif"
                       style="cursor:pointer">
                    <input type="radio" name="role" value="{{ $v }}"
                           class="form-check-input mt-1 flex-shrink-0"
                           @checked($user->role === $v)>
                    <div>
                        <div class="fw-semibold"><i class="bi {{ $icon }} me-1"></i>{{ $label }}</div>
                        <div class="small text-secondary">{{ $desc }}</div>
                    </div>
                </label>
            @endforeach

            @if($user->id === auth()->id())
                <div class="alert alert-warning border-0 small mt-2 py-2">
                    <i class="bi bi-exclamation-triangle me-1"></i>নিজের role পরিবর্তন করা যাবে না।
                </div>
            @endif

            <div class="d-flex gap-2 mt-3">
                <button class="btn btn-brand" @if($user->id === auth()->id()) disabled @endif>সেভ করুন</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-light border">বাতিল</a>
            </div>
        </form>
    </div>
</div>
@endsection
