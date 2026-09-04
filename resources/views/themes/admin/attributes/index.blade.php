@extends('themes.admin.layouts.master')

@section('title', 'ویژگی‌های محصولات')

@section('content')
    <div class="mb-4">
        <h1 class="h2 mb-1">ویژگی‌های محصولات</h1>
        <p class="text-muted mb-0">ویژگی‌هایی مانند رنگ و سایز را یک‌بار تعریف کنید و سپس برای هر محصول چند مقدار انتخاب کنید.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h5 mb-3">افزودن ویژگی</h2>

            <form method="POST" action="{{ route('admin.attributes.store') }}" class="row g-3">
                @csrf

                <div class="col-md-4">
                    <label class="form-label" for="name">نام ویژگی</label>
                    <input id="name" name="name" class="form-control" placeholder="مثلاً رنگ" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="slug">Slug انگلیسی</label>
                    <input id="slug" name="slug" class="form-control" dir="ltr" placeholder="color">
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="type">نوع</label>
                    <select id="type" name="type" class="form-select" required>
                        <option value="select">انتخابی</option>
                        <option value="text">متنی</option>
                        <option value="number">عددی</option>
                        <option value="boolean">بله/خیر</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="selection_mode">حالت انتخاب</label>
                    <select id="selection_mode" name="selection_mode" class="form-select" required>
                        <option value="single">تک‌انتخاب</option>
                        <option value="multiple">چندانتخاب</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="display_mode">نوع نمایش</label>
                    <select id="display_mode" name="display_mode" class="form-select" required>
                        <option value="dropdown">لیست</option>
                        <option value="swatch">رنگی</option>
                        <option value="chip">چیپ</option>
                        <option value="toggle">بله/خیر</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="config">Config JSON</label>
                    <input id="config" name="config" class="form-control" dir="ltr" placeholder='{"hint":"optional"}'>
                </div>

                <div class="col-md-6">
                    <div class="form-check">
                        <input id="is_filterable" name="is_filterable" value="1" type="checkbox" class="form-check-input">
                        <label for="is_filterable" class="form-check-label">در فیلتر فروشگاه نمایش داده شود</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-check">
                        <input id="is_required" name="is_required" value="1" type="checkbox" class="form-check-input">
                        <label for="is_required" class="form-check-label">در تعریف محصول مهم است</label>
                    </div>
                </div>

                <div class="col-12">
                    <button class="btn btn-primary">افزودن ویژگی</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        @forelse($attributes as $attribute)
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h2 class="h5 mb-1">{{ $attribute->name }}</h2>
                                <div class="small text-muted" dir="ltr">{{ $attribute->slug }}</div>
                            </div>
                            <div class="text-end small">
                                <div>نوع: {{ $attribute->type }}</div>
                                <div>انتخاب: {{ $attribute->selection_mode === 'multiple' ? 'چندگانه' : 'تکی' }}</div>
                                <div>نمایش: {{ $attribute->display_mode }}</div>
                            </div>
                        </div>

                        <ul class="list-group list-group-flush mb-3">
                            @forelse($attribute->values as $value)
                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-2">
                                        @if($value->normalized_color_hex)
                                            <span style="display:inline-block;width:18px;height:18px;border-radius:4px;border:1px solid #ced4da;background:{{ $value->normalized_color_hex }};"></span>
                                        @endif
                                        <span>{{ $value->value }}</span>
                                    </div>
                                    <div class="small text-muted" dir="ltr">{{ $value->slug }}</div>
                                </li>
                            @empty
                                <li class="list-group-item px-0 text-muted">مقداری ثبت نشده است.</li>
                            @endforelse
                        </ul>

                        <form method="POST" action="{{ route('admin.attributes.values.store', $attribute) }}" class="row g-2">
                            @csrf

                            <div class="col-md-4">
                                <input name="value" class="form-control" placeholder="مقدار جدید" required>
                            </div>

                            <div class="col-md-3">
                                <input name="slug" class="form-control" dir="ltr" placeholder="slug">
                            </div>

                            <div class="col-md-2">
                                <input name="color_hex" class="form-control form-control-color" type="color" value="#000000">
                            </div>

                            <div class="col-md-3">
                                <input name="meta" class="form-control" dir="ltr" placeholder='{"code":"A1"}'>
                            </div>

                            <div class="col-12">
                                <button class="btn btn-outline-primary">افزودن مقدار</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">هنوز ویژگی‌ای تعریف نشده است.</div>
            </div>
        @endforelse
    </div>
@endsection