@extends('themes.admin.layouts.master')

@section('title', 'ویژگی‌های محصولات')

@section('content')
    <div class="mb-4">
        <h1 class="h2 mb-1">ویژگی‌های محصولات</h1>
        <p class="text-muted mb-0">ویژگی‌هایی مانند رنگ و سایز را یک‌بار تعریف کنید و در محصولات انتخاب کنید.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h5">افزودن ویژگی</h2>
            <form method="POST" action="{{ route('admin.attributes.store') }}" class="row g-3">
                @csrf
                <div class="col-md-4"><label class="form-label" for="name">نام ویژگی</label><input id="name" name="name" class="form-control" placeholder="مثلاً رنگ" required></div>
                <div class="col-md-4"><label class="form-label" for="slug">Slug انگلیسی</label><input id="slug" name="slug" class="form-control" dir="ltr" placeholder="color"></div>
                <div class="col-md-4"><label class="form-label" for="type">نوع</label><select id="type" name="type" class="form-select"><option value="select">انتخابی</option><option value="text">متنی</option><option value="number">عددی</option><option value="boolean">بله/خیر</option></select></div>
                <div class="col-12"><div class="form-check"><input id="is_filterable" name="is_filterable" value="1" type="checkbox" class="form-check-input"><label for="is_filterable" class="form-check-label">در فیلتر فروشگاه نمایش داده شود</label></div></div>
                <div class="col-12"><button class="btn btn-primary">افزودن ویژگی</button></div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        @forelse($attributes as $attribute)
            <div class="col-lg-6"><div class="card shadow-sm h-100"><div class="card-body"><h2 class="h5">{{ $attribute->name }} <small class="text-muted">({{ $attribute->slug }})</small></h2><ul class="list-group list-group-flush mb-3">@forelse($attribute->values as $value)<li class="list-group-item px-0">{{ $value->value }} <small class="text-muted">{{ $value->slug }}</small></li>@empty<li class="list-group-item px-0 text-muted">مقداری ثبت نشده است.</li>@endforelse</ul><form method="POST" action="{{ route('admin.attributes.values.store', $attribute) }}" class="row g-2">@csrf<div class="col"><input name="value" class="form-control" placeholder="مقدار جدید" required></div><div class="col"><input name="slug" class="form-control" dir="ltr" placeholder="slug"></div><div class="col-auto"><button class="btn btn-outline-primary">افزودن مقدار</button></div></form></div></div></div>
        @empty
            <div class="col-12"><div class="alert alert-info">هنوز ویژگی‌ای تعریف نشده است.</div></div>
        @endforelse
    </div>
@endsection
