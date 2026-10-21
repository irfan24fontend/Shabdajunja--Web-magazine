@props(['name' => 'body', 'value' => '', 'placeholder' => 'এখানে লিখুন...'])

@php
    $svg = fn ($d) => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="' . $d . '"/></svg>';

    $icons = [
        'bullet'        => 'M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01',
        'ordered'       => 'M10 6h11M10 12h11M10 18h11M4 5l1-1v6M3.5 14.5h2.5l-2.5 3h2.5',
        'quote'         => 'M7 8h4v4a4 4 0 01-4 4M15 8h4v4a4 4 0 01-4 4',
        'align-left'    => 'M3 6h18M3 12h12M3 18h16',
        'align-center'  => 'M3 6h18M6 12h12M4 18h16',
        'align-right'   => 'M3 6h18M9 12h12M5 18h16',
        'align-justify' => 'M3 6h18M3 12h18M3 18h18',
        'link'          => 'M10 14a4 4 0 005.66 0l3-3a4 4 0 00-5.66-5.66l-1 1M14 10a4 4 0 00-5.66 0l-3 3a4 4 0 005.66 5.66l1-1',
        'unlink'        => 'M10 14a4 4 0 005.66 0l3-3a4 4 0 00-5.66-5.66l-1 1M14 10a4 4 0 00-5.66 0l-3 3a4 4 0 005.66 5.66l1-1M4 4l16 16',
        'hr'            => 'M3 12h18M7 7h.01M12 7h.01M17 7h.01M7 17h.01M12 17h.01M17 17h.01',
        'full'          => 'M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5',
        'undo'          => 'M9 14L4 9l5-5M4 9h10a6 6 0 010 12h-3',
        'redo'          => 'M15 14l5-5-5-5M20 9H10a6 6 0 000 12h3',
        'image'         => 'M3 5h18v14H3zM3 16l5-5 4 4 3-3 6 6M8.5 9.5h.01',
        'more'          => 'M12 5h.01M12 12h.01M12 19h.01',
    ];

    // [cmd, label (html), তথ্য]
    $row1 = [
        ['bold',  '<b>B</b>', 'গাঢ় (Bold)'],
        ['italic', '<i style="font-family:serif">I</i>', 'বাঁকা (Italic)'],
        ['strike', '<s>S</s>', 'কাটা দাগ (Strikethrough)'],
        '|',
        ['bullet',  $svg($icons['bullet']),  'বুলেট তালিকা'],
        ['ordered', $svg($icons['ordered']), 'নম্বর দেওয়া তালিকা'],
        ['quote',   $svg($icons['quote']),   'উদ্ধৃতি (Blockquote)'],
        '|',
        ['align-left',   $svg($icons['align-left']),   'বামে সাজান'],
        ['align-center', $svg($icons['align-center']), 'মাঝে সাজান'],
        ['align-right',  $svg($icons['align-right']),  'ডানে সাজান'],
        '|',
        ['link',   $svg($icons['link']),   'লিংক যোগ করুন'],
        ['unlink', $svg($icons['unlink']), 'লিংক সরান'],
        ['hr',     $svg($icons['hr']),     'বিভাজক রেখা'],
        '|',
        ['full', $svg($icons['full']), 'ফুলস্ক্রিন'],
    ];

    $row2 = [
        ['underline', '<u>U</u>', 'আন্ডারলাইন'],
        ['align-justify', $svg($icons['align-justify']), 'দুই পাশে সমান'],
        ['highlight', '<span class="hl-demo">হা</span>', 'হাইলাইট (হলুদ দাগ)'],
        'COLOR',
        '|',
        ['clear', '<span style="font-size:.8rem">Tx</span>', 'সব ফরম্যাট মুছুন'],
        '|',
        ['undo', $svg($icons['undo']), 'আগের অবস্থায় (Undo)'],
        ['redo', $svg($icons['redo']), 'আবার করুন (Redo)'],
    ];
@endphp

<div class="rte" data-editor data-upload="{{ route('upload.image') }}" data-placeholder="{{ $placeholder }}">
    <input type="hidden" name="{{ $name }}" value="{{ $value }}">

    {{-- উপরের বার: মিডিয়া + ট্যাব --}}
    <div class="rte-top">
        <button type="button" data-cmd="image" class="rte-media" data-tip="লেখার ভেতরে ছবি যোগ করুন">
            {!! $svg($icons['image']) !!} মিডিয়া যোগ করুন
        </button>
        <div class="rte-tabs">
            <button type="button" data-tab="visual" class="is-on">ভিজ্যুয়াল</button>
            <button type="button" data-tab="html">HTML</button>
        </div>
    </div>

    {{-- টুলবার --}}
    <div class="rte-bar" data-bar>
        <div class="rte-row">
            <select data-block data-tip="লেখার ধরন (শিরোনাম / সাধারণ / কবিতা)">
                <option value="paragraph">সাধারণ লেখা</option>
                <option value="h2">বড় শিরোনাম</option>
                <option value="h3">মাঝারি শিরোনাম</option>
                <option value="h4">ছোট শিরোনাম</option>
                <option value="poem">কবিতার লাইন</option>
            </select>
            <span class="rte-sep"></span>

            @foreach($row1 as $b)
                @if($b === '|') <span class="rte-sep"></span>
                @else <button type="button" data-cmd="{{ $b[0] }}" data-tip="{{ $b[2] }}">{!! $b[1] !!}</button>
                @endif
            @endforeach

            <button type="button" data-more data-tip="আরও টুল দেখান / লুকান" class="rte-more ml-auto">{!! $svg($icons['more']) !!}</button>
        </div>

        <div class="rte-row rte-row2" data-row2>
            @foreach($row2 as $b)
                @if($b === '|') <span class="rte-sep"></span>
                @elseif($b === 'COLOR')
                    <label class="rte-color" data-tip="লেখার রং বদলান">
                        <span>A</span><input type="color" data-color value="#0f4a3d">
                    </label>
                    <button type="button" data-color-reset data-tip="রং মুছুন">রং মুছুন</button>
                @else
                    <button type="button" data-cmd="{{ $b[0] }}" data-tip="{{ $b[2] }}">{!! $b[1] !!}</button>
                @endif
            @endforeach
        </div>
    </div>

    <input type="file" accept="image/*" hidden data-file>

    <div class="rte-body" data-surface></div>
    <textarea class="rte-html" data-html hidden spellcheck="false"></textarea>

    <div class="rte-foot">
        <span data-count>শব্দ সংখ্যা: ০</span>
        <span>নতুন লাইন: Shift + Enter</span>
    </div>
</div>