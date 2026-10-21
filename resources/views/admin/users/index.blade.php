@extends('admin.layout')
@section('title', 'ইউজার')

@section('content')
<form method="GET" class="d-flex gap-2 mb-4" style="max-width:480px">
    <input name="q" value="{{ request('q') }}" placeholder="নাম বা ইমেইল দিয়ে খুঁজুন" class="form-control">
    <button class="btn btn-brand">খুঁজুন</button>
</form>

<div class="card"><div class="table-responsive">
    <table class="tbl">
        <thead><tr><th>ইউজার</th><th>Role</th><th>লেখা</th><th>যোগ দিয়েছেন</th><th></th></tr></thead>
        <tbody>
        @foreach($users as $u)
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ $u->avatar_url }}" class="rounded-circle" width="40" height="40" alt="">
                        <div>
                            <div class="fw-medium">{{ $u->name }} @if($u->id === auth()->id())<small class="text-secondary">(আপনি)</small>@endif</div>
                            <div class="small text-secondary">{{ $u->email }}</div>
                        </div>
                    </div>
                </td>
                <td><span class="pill {{ $u->role === 'admin' ? 'bg-brand-soft text-brand' : 'bg-secondary-subtle text-secondary-emphasis' }}">{{ $u->role === 'admin' ? 'Admin' : 'User' }}</span></td>
                <td class="small">{{ $u->posts_count }}টি</td>
                <td class="small text-secondary">{{ $u->created_at->diffForHumans() }}</td>
                <td class="text-end text-nowrap">
                    <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-outline-brand">Role</a>
                    @if($u->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="d-inline" onsubmit="return confirm('ইউজার ও তার সব লেখা মুছে যাবে। নিশ্চিত?')">
                            @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div></div>

<div class="mt-4">{{ $users->links() }}</div>
@endsection