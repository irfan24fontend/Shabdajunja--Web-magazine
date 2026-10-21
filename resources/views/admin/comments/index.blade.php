@extends('admin.layout')
@section('title', 'মন্তব্য')

@section('content')
@php $tabs = ['' => 'সব', 'pending' => 'অপেক্ষমাণ', 'approved' => 'অনুমোদিত']; @endphp

<ul class="nav nav-pills bg-white rounded-3 p-1 border d-inline-flex mb-4">
    @foreach($tabs as $key => $name)
        <li class="nav-item">
            <a href="{{ route('admin.comments.index', array_filter(['filter' => $key])) }}"
               class="nav-link py-1 {{ (string) request('filter') === (string) $key ? 'active bg-brand' : 'text-dark' }}">{{ $name }}</a>
        </li>
    @endforeach
</ul>

@forelse($comments as $c)
    <div class="card mb-3"><div class="card-body d-flex gap-3">
        <div class="avatar-letter" style="background:var(--brand-700);color:#fff">{{ mb_substr($c->display_name, 0, 1) }}</div>
        <div class="flex-grow-1" style="min-width:0">
            <div class="d-flex flex-wrap align-items-center gap-2 small">
                <span class="fw-semibold">{{ $c->display_name }}</span>
                <span class="text-secondary">·</span>
                <a href="{{ route('post.show', $c->post->slug) }}" target="_blank" class="text-decoration-none text-truncate" style="max-width:320px">{{ $c->post->title }}</a>
                <span class="text-secondary">{{ $c->created_at->diffForHumans() }}</span>
                @if($c->approved)<span class="pill bg-success-subtle text-success-emphasis">অনুমোদিত</span>
                @else<span class="pill bg-warning-subtle text-warning-emphasis">অপেক্ষমাণ</span>@endif
            </div>
            <p class="mt-2 mb-0" style="white-space:pre-line">{{ $c->body }}</p>
        </div>
        <div class="d-flex flex-column gap-2">
            @unless($c->approved)
                <form method="POST" action="{{ route('admin.comments.approve', $c) }}">@csrf @method('PATCH')
                    <button class="btn btn-brand btn-sm w-100">Approve</button></form>
            @endunless
            <form method="POST" action="{{ route('admin.comments.destroy', $c) }}" onsubmit="return confirm('মুছবেন?')">@csrf @method('DELETE')
                <button class="btn btn-outline-danger btn-sm w-100">Delete</button></form>
        </div>
    </div></div>
@empty
    <div class="card"><div class="card-body text-center py-5"><div class="fs-1">💬</div><p class="text-secondary mb-0">কোনো মন্তব্য নেই।</p></div></div>
@endforelse

<div class="mt-4">{{ $comments->links() }}</div>
@endsection