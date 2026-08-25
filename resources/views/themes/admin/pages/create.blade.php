@extends('themes.admin.layouts.master')

@section('title', 'ایجاد صفحه')

@section('content')
    <div class="mb-4">
        <h1 class="h2 mb-1">ایجاد صفحه</h1>
        <p class="text-muted mb-0">منتشر شود یعنی صفحه قابل مشاهده عمومی باشد؛ نمایش در منو یعنی لینک صفحه در ناوبری سایت نمایش داده شود.</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.pages.store') }}" class="row g-3">
                @csrf

                <div class="col-md-6">
                    <label class="form-label" for="title">عنوان صفحه</label>
                    <input id="title" name="title" class="form-control" required value="{{ old('title') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="slug">Slug (اختیاری)</label>
                    <input id="slug" name="slug" class="form-control" dir="ltr" value="{{ old('slug') }}" placeholder="about-us">
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="menu_order">ترتیب نمایش در منو</label>
                    <input id="menu_order" name="menu_order" type="number" min="0" class="form-control" value="{{ old('menu_order', 0) }}">
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check">
                        <input id="is_published" name="is_published" value="1" type="checkbox" class="form-check-input" @checked(old('is_published'))>
                        <label class="form-check-label" for="is_published">منتشر شود</label>
                    </div>
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check">
                        <input id="show_in_menu" name="show_in_menu" value="1" type="checkbox" class="form-check-input" @checked(old('show_in_menu'))>
                        <label class="form-check-label" for="show_in_menu">در منو نمایش داده شود</label>
                    </div>
                </div>

                <div class="col-12">
                    <div class="alert alert-light border small mb-0">
                        صفحه می تواند منتشر باشد ولی در منو نمایش داده نشود (مثلا فقط از طریق لینک مستقیم یا فوتر قابل دسترسی باشد).
                    </div>
                </div>

                @include('themes.admin.pages._editor', ['contentValue' => old('content')])

                <div class="col-12 d-flex gap-2">
                    <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary">بازگشت</a>
                    <button class="btn btn-primary">ذخیره صفحه</button>
                </div>
            </form>
        </div>
    </div>
@endsection
