@extends('admin.layout')
@section('title', 'ড্যাশবোর্ড')

@section('content')
@php
    $bn = fn ($n) => strtr((string) $n, ['0'=>'০','1'=>'১','2'=>'২','3'=>'৩','4'=>'৪','5'=>'৫','6'=>'৬','7'=>'৭','8'=>'৮','9'=>'৯']);
    $h = now()->hour;
    $greet = $h < 5 ? 'শুভ রাত্রি' : ($h < 12 ? 'সুপ্রভাত' : ($h < 16 ? 'শুভ দুপুর' : ($h < 19 ? 'শুভ অপরাহ্ন' : 'শুভ সন্ধ্যা')));

    $cards = [
        ['মোট লেখা', $totalPosts, 'bi-file-earmark-text', 'g-green', 'এই সপ্তাহে +' . $bn($newThisWeek)],
        ['মোট পাঠ', $totalViews, 'bi-eye', 'g-blue', 'প্রকাশিত ' . $bn($publishedPosts) . 'টি লেখায়'],
        ['রিভিউয়ের অপেক্ষায়', $pendingCount, 'bi-hourglass-split', 'g-amber', $pendingCount ? 'দ্রুত দেখুন' : 'সব ঠিক আছে'],
        ['অপেক্ষমাণ মন্তব্য', $pendingComments, 'bi-chat-dots', 'g-rose', $pendingComments ? 'অনুমোদন দরকার' : 'সব ঠিক আছে'],
        ['মোট ইউজার', $usersCount, 'bi-people', 'g-violet', 'নিবন্ধিত সদস্য'],
    ];

    $W = 640; $H = 200; $pad = 12;
    $n = count($chart['labels']);
    $max = max(1, max($chart['posts']), max($chart['comments']));
    $pts = fn ($arr) => collect($arr)->map(fn ($v, $i) => [
        round($pad + $i * (($W - 2 * $pad) / ($n - 1)), 1),
        round($H - $pad - ($v / $max) * ($H - 2 * $pad), 1),
    ]);
    $str = fn ($p) => $p->map(fn ($c) => $c[0] . ',' . $c[1])->implode(' ');
    $pp = $pts($chart['posts']);
    $cp = $pts($chart['comments']);
    $area = $str($pp) . ' ' . ($W - $pad) . ",$H $pad,$H";

    $badge = ['published' => 'bg-success-subtle text-success-emphasis', 'pending' => 'bg-warning-subtle text-warning-emphasis', 'draft' => 'bg-secondary-subtle text-secondary-emphasis'];
    $label = ['published' => 'প্রকাশিত', 'pending' => 'রিভিউয়ে', 'draft' => 'ড্রাফট'];
    $catMax = max(1, $topCategories->max('posts_count'));
    $bars = ['bg-success', 'bg-primary', 'bg-warning', 'bg-info', 'bg-danger'];
@endphp

{{-- Welcome --}}
<div class="rounded-4 text-white p-4 p-md-5 mb-4 position-relative overflow-hidden" style="background:linear-gradient(135deg,#0b2f27,#0f4a3d 55%,#1f7a65)">
    <div class="position-absolute rounded-circle" style="width:220px;height:220px;right:-50px;top:-50px;background:rgba(255,255,255,.1)"></div>
    <div class="position-relative d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <div class="small opacity-75">{{ $greet }} 👋</div>
            <div class="font-serif fs-2 fw-bold">{{ auth()->user()->name }}</div>
            <div class="small opacity-75">
                @if($pendingCount || $pendingComments)
                    আপনার কাছে {{ $bn($pendingCount) }}টি লেখা ও {{ $bn($pendingComments) }}টি মন্তব্য অপেক্ষায় আছে।
                @else
                    সবকিছু আপ-টু-ডেট। নতুন কিছু লিখে ফেলুন!
                @endif
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.posts.create') }}" class="btn btn-light fw-semibold text-brand">+ নতুন লেখা</a>
            <a href="{{ route('admin.posts.index', ['status' => 'pending']) }}" class="btn btn-outline-light">রিভিউ কিউ</a>
            <a href="{{ route('admin.comments.index', ['filter' => 'pending']) }}" class="btn btn-outline-light">মন্তব্য</a>
        </div>
    </div>
</div>

{{-- Stat cards --}}
<div class="row row-cols-2 row-cols-lg-3 row-cols-xl-5 g-3 mb-4">
    @foreach($cards as [$name, $value, $icon, $grad, $sub])
        <div class="col">
            <div class="stat-card {{ $grad }} h-100">
                <div class="position-relative d-flex justify-content-between">
                    <div>
                        <div class="small opacity-90">{{ $name }}</div>
                        <div class="font-serif fs-1 fw-bold lh-1 mt-2">{{ $bn($value) }}</div>
                    </div>
                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width:40px;height:40px;background:rgba(255,255,255,.2)"><i class="bi {{ $icon }} fs-5"></i></div>
                </div>
                <div class="position-relative small opacity-90 mt-3">{{ $sub }}</div>
            </div>
        </div>
    @endforeach
</div>

{{-- Chart + categories --}}
<div class="row g-4 mb-4">
    <div class="col-xl-8">
        <div class="card h-100"><div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between mb-3">
                <div><h2 class="h5 fw-bold mb-0">কার্যক্রম</h2><div class="small text-secondary">শেষ ১৪ দিনের নতুন লেখা ও মন্তব্য</div></div>
                <div class="small d-flex gap-3">
                    <span><i class="bi bi-circle-fill" style="color:#14594a"></i> লেখা</span>
                    <span><i class="bi bi-circle-fill" style="color:#d83a2e"></i> মন্তব্য</span>
                </div>
            </div>
            <svg viewBox="0 0 {{ $W }} {{ $H + 24 }}" class="w-100 h-auto">
                <defs><linearGradient id="gA" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#14594a" stop-opacity=".35"/><stop offset="100%" stop-color="#14594a" stop-opacity="0"/>
                </linearGradient></defs>
                @foreach([0, .25, .5, .75, 1] as $g)
                    @php $y = $pad + $g * ($H - 2 * $pad); @endphp
                    <line x1="{{ $pad }}" x2="{{ $W - $pad }}" y1="{{ $y }}" y2="{{ $y }}" stroke="#e5e7eb" stroke-dasharray="4 4"/>
                @endforeach
                <polygon points="{{ $area }}" fill="url(#gA)"/>
                <polyline points="{{ $str($pp) }}" fill="none" stroke="#14594a" stroke-width="3" stroke-linejoin="round" stroke-linecap="round"/>
                <polyline points="{{ $str($cp) }}" fill="none" stroke="#d83a2e" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round"/>
                @foreach($pp as $c)<circle cx="{{ $c[0] }}" cy="{{ $c[1] }}" r="3.5" fill="#fff" stroke="#14594a" stroke-width="2"/>@endforeach
                @foreach($cp as $c)<circle cx="{{ $c[0] }}" cy="{{ $c[1] }}" r="3" fill="#fff" stroke="#d83a2e" stroke-width="2"/>@endforeach
                @foreach($chart['labels'] as $i => $l)
                    @if($i % 2 === 0)<text x="{{ $pp[$i][0] }}" y="{{ $H + 16 }}" text-anchor="middle" font-size="11" fill="#9ca3af">{{ $l }}</text>@endif
                @endforeach
            </svg>
        </div></div>
    </div>
    <div class="col-xl-4">
        <div class="card h-100"><div class="card-body p-4">
            <h2 class="h5 fw-bold mb-4">জনপ্রিয় বিভাগ</h2>
            @forelse($topCategories as $c)
                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1"><span class="fw-medium">{{ $c->name }}</span><span class="text-secondary">{{ $bn($c->posts_count) }}টি</span></div>
                    <div class="progress" style="height:10px"><div class="progress-bar {{ $bars[$loop->index % 5] }}" style="width:{{ max(4, $c->posts_count / $catMax * 100) }}%"></div></div>
                </div>
            @empty
                <p class="small text-secondary">এখনো কোনো ক্যাটাগরি নেই।</p>
            @endforelse
        </div></div>
    </div>
</div>

{{-- Recent + pending --}}
<div class="row g-4 mb-4">
    <div class="col-xl-8">
        <div class="card h-100"><div class="card-body p-4">
            <div class="d-flex justify-content-between mb-2">
                <h2 class="h5 fw-bold">সাম্প্রতিক লেখা</h2>
                <a href="{{ route('admin.posts.index') }}" class="small text-decoration-none">সব দেখুন →</a>
            </div>
            @forelse($recentPosts as $p)
                <div class="d-flex align-items-center gap-3 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    @if($p->image_url)<img src="{{ $p->image_url }}" class="thumb-sm" alt="">@else<div class="thumb-sm thumb-sm-ph">শ</div>@endif
                    <div class="flex-grow-1 min-w-0" style="min-width:0">
                        <a href="{{ route('admin.posts.edit', $p) }}" class="fw-medium text-dark text-decoration-none d-block text-truncate">{{ $p->title }}</a>
                        <div class="small text-secondary">{{ $p->author->name }} · {{ $p->category->name }} · {{ $p->created_at->diffForHumans() }}</div>
                    </div>
                    <span class="pill {{ $badge[$p->status] }}">{{ $label[$p->status] }}</span>
                </div>
            @empty
                <p class="text-center text-secondary py-4 mb-0">এখনো কোনো লেখা নেই।</p>
            @endforelse
        </div></div>
    </div>
    <div class="col-xl-4">
        <div class="card h-100"><div class="card-body p-4">
            <h2 class="h5 fw-bold mb-2">রিভিউয়ের অপেক্ষায়
                @if($pendingCount)<span class="badge bg-warning text-dark rounded-pill ms-1">{{ $bn($pendingCount) }}</span>@endif
            </h2>
            @forelse($pendingPosts as $p)
                <div class="d-flex align-items-center justify-content-between gap-2 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div style="min-width:0">
                        <a href="{{ route('admin.posts.edit', $p) }}" class="fw-medium text-dark text-decoration-none d-block text-truncate">{{ $p->title }}</a>
                        <div class="small text-secondary">{{ $p->author->name }}</div>
                    </div>
                    <form method="POST" action="{{ route('admin.posts.publish', $p) }}">@csrf @method('PATCH')
                        <button class="btn btn-brand btn-sm">Publish</button>
                    </form>
                </div>
            @empty
                <div class="text-center py-4"><div class="fs-1">✅</div><p class="small text-secondary mb-0">রিভিউয়ের জন্য কোনো লেখা নেই।</p></div>
            @endforelse
        </div></div>
    </div>
</div>

{{-- Popular + comments --}}
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card h-100"><div class="card-body p-4">
            <h2 class="h5 fw-bold mb-2">সবচেয়ে বেশি পঠিত</h2>
            @forelse($popular as $p)
                <div class="d-flex align-items-center gap-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <span class="rank">{{ $bn($loop->iteration) }}</span>
                    <a href="{{ route('post.show', $p->slug) }}" target="_blank" class="flex-grow-1 text-dark text-decoration-none text-truncate">{{ $p->title }}</a>
                    <span class="small text-secondary text-nowrap"><i class="bi bi-eye"></i> {{ $bn($p->views) }}</span>
                </div>
            @empty
                <p class="small text-secondary">এখনো কিছু নেই।</p>
            @endforelse
        </div></div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100"><div class="card-body p-4">
            <div class="d-flex justify-content-between mb-2">
                <h2 class="h5 fw-bold">সাম্প্রতিক মন্তব্য</h2>
                <a href="{{ route('admin.comments.index') }}" class="small text-decoration-none">সব দেখুন →</a>
            </div>
            @forelse($recentComments as $c)
                <div class="d-flex gap-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="avatar-letter">{{ mb_substr($c->display_name, 0, 1) }}</div>
                    <div style="min-width:0">
                        <div class="small"><span class="fw-medium">{{ $c->display_name }}</span>
                            <span class="text-secondary">· {{ \Illuminate\Support\Str::limit($c->post->title, 24) }}</span>
                            @unless($c->approved)<span class="pill bg-warning-subtle text-warning-emphasis ms-1">অপেক্ষমাণ</span>@endunless
                        </div>
                        <div class="small text-secondary text-truncate">{{ $c->body }}</div>
                    </div>
                </div>
            @empty
                <p class="small text-secondary">এখনো কোনো মন্তব্য নেই।</p>
            @endforelse
        </div></div>
    </div>
</div>
@endsection