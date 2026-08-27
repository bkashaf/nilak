@extends('themes.default.layouts.master')

@section('content')

    {{-- عنوان صفحه --}}
    <h1 class="page-title">{{ $page->title }}</h1>

    {{-- محتوای ثابت صفحه (در صورت وجود) --}}
    @if(!empty($page->content))
        <div class="page-content">
            {!! $page->content !!}
        </div>
    @endif

    {{-- نمایش بلوک‌های صفحه‌ساز --}}
    @if(!empty($blocks))
        @foreach($blocks as $block)
            @includeIf('themes.default.blocks.' . $block->type, [
                'data' => $block->data,
                'block' => $block
            ])
        @endforeach
    @endif

@endsection
