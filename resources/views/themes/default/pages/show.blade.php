@extends('themes.default.layouts.shop')
@section('content')

    {{-- پیام‌های فرم تماس --}}
    @if(session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger mt-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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
