@extends('themes.default.layouts.shop')

@section('title', $page->title)

@section('content')
    <style>
        .page-content {
            line-height: 1.95;
            color: #1f2937;
            font-size: 1rem;
        }
        .page-content .lead {
            font-size: 1.15rem;
            color: #374151;
        }
        .page-content .full-bleed {
            margin-inline: calc(50% - 50vw);
            width: 100vw;
        }
        .page-content .image-frame {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 8px;
            background: #fff;
        }
        .page-content .image-shadow {
            box-shadow: 0 8px 20px rgba(0, 0, 0, .12);
        }
        .page-content .two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
            align-items: start;
            margin-bottom: 1rem;
        }
        .page-content .two-col .box {
            padding: 12px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            background: #fff;
        }
        .page-content table {
            border-collapse: collapse;
            width: 100%;
            margin: 1rem 0;
            background: #fff;
        }
        .page-content th,
        .page-content td {
            border: 1px solid #e5e7eb;
            padding: 10px;
            vertical-align: top;
        }
        .page-content img {
            max-width: 100%;
            height: auto;
        }
        @media (max-width: 767.98px) {
            .page-content .two-col {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <article class="card shadow-sm border-0">
        <div class="card-body p-4 p-md-5">
            <h1 class="h2 mb-3">{{ $page->title }}</h1>
            <div class="content-body page-content">{!! $page->content ?? '' !!}</div>
        </div>
    </article>
@endsection
