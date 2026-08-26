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
        .page-content .nl-block {
            box-sizing: border-box;
            color: inherit;
            font-family: inherit;
            margin-bottom: 1rem;
        }
        .page-content .nl-block * {
            box-sizing: border-box;
        }
        .page-content .nl-two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            align-items: start;
        }
        .page-content .nl-two-col .nl-box {
            padding: 12px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            background: #fff;
        }
        .page-content .nl-banner {
            border-radius: 14px;
            padding: 24px 20px;
            background: linear-gradient(120deg, #0f172a, #1d4ed8);
            color: #fff;
        }
        .page-content .nl-banner .nl-banner-kicker {
            opacity: .9;
            font-size: .9rem;
            margin-bottom: .35rem;
        }
        .page-content .nl-banner .nl-banner-title {
            font-size: 1.5rem;
            line-height: 1.4;
            margin: 0 0 .5rem;
        }
        .page-content .nl-btn {
            display: inline-block;
            padding: .65rem 1rem;
            border-radius: 10px;
            background: #fff;
            color: #0f172a;
            text-decoration: none;
            font-weight: 700;
        }
        .page-content .nl-btn-outline {
            display: inline-block;
            padding: .65rem 1rem;
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            color: inherit;
            text-decoration: none;
            font-weight: 700;
        }
        .page-content .nl-trust-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .75rem;
        }
        .page-content .nl-trust-item {
            padding: 10px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            text-align: center;
            background: #fff;
        }
        .page-content .nl-testimonials {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .75rem;
        }
        .page-content .nl-quote {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 14px;
            background: #fff;
            margin: 0;
        }
        .page-content .nl-faq details {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 10px 12px;
            background: #fff;
            margin-bottom: .5rem;
        }
        .page-content .nl-faq summary {
            cursor: pointer;
            font-weight: 700;
        }
        .page-content .nl-product-feature {
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(0, .9fr);
            gap: 1rem;
            align-items: stretch;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            overflow: hidden;
            background: #fff;
        }
        .page-content .nl-product-feature .nl-media img {
            width: 100%;
            height: 100%;
            min-height: 260px;
            object-fit: cover;
            display: block;
        }
        .page-content .nl-product-feature .nl-body {
            padding: 18px;
        }
        .page-content .nl-product-feature .nl-price {
            font-size: 1.25rem;
            font-weight: 800;
            margin: .35rem 0 1rem;
        }
        .page-content .nl-categories {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .75rem;
        }
        .page-content .nl-cat-card {
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            min-height: 170px;
            background: #0f172a;
            color: #fff;
            text-decoration: none;
            display: block;
        }
        .page-content .nl-cat-card img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: .7;
        }
        .page-content .nl-cat-card span {
            position: absolute;
            inset-inline: 12px;
            bottom: 12px;
            z-index: 2;
            font-weight: 700;
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
            .page-content .nl-two-col,
            .page-content .nl-trust-grid,
            .page-content .nl-testimonials,
            .page-content .nl-categories,
            .page-content .nl-product-feature {
                grid-template-columns: 1fr;
            }
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
