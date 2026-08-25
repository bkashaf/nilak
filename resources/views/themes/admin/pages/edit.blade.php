@extends('themes.admin.layouts.master')

@section('title', 'ویرایش صفحه')

@section('content')
    <div class="mb-4">
        <h1 class="h2 mb-1">ویرایش صفحه</h1>
        <p class="text-muted mb-0">{{ $page->title }} - منتشر شود یعنی صفحه عمومی باشد؛ نمایش در منو یعنی لینک صفحه در ناوبری نمایش داده شود.</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.pages.update', $page) }}" class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-md-6">
                    <label class="form-label" for="title">عنوان صفحه</label>
                    <input id="title" name="title" class="form-control" required value="{{ old('title', $page->title) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="slug">Slug</label>
                    <input id="slug" name="slug" class="form-control" dir="ltr" required value="{{ old('slug', $page->slug) }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="menu_order">ترتیب نمایش در منو</label>
                    <input id="menu_order" name="menu_order" type="number" min="0" class="form-control" value="{{ old('menu_order', $page->menu_order) }}">
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check">
                        <input id="is_published" name="is_published" value="1" type="checkbox" class="form-check-input" @checked(old('is_published', $page->is_published))>
                        <label class="form-check-label" for="is_published">منتشر شود</label>
                    </div>
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check">
                        <input id="show_in_menu" name="show_in_menu" value="1" type="checkbox" class="form-check-input" @checked(old('show_in_menu', $page->show_in_menu))>
                        <label class="form-check-label" for="show_in_menu">در منو نمایش داده شود</label>
                    </div>
                </div>

                <div class="col-12">
                    <div class="alert alert-light border small mb-0">
                        اگر صفحه منتشر نباشد، حتی با فعال بودن نمایش در منو، برای کاربر نهایی نمایش داده نمی شود.
                    </div>
                </div>

                @include('themes.admin.pages._editor', ['contentValue' => old('content', $page->content)])

                <div class="col-12 d-flex gap-2">
                    <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary">بازگشت</a>
                    <button class="btn btn-primary">ذخیره تغییرات</button>
                </div>
            </form>
        </div>
    </div>
@endsection
