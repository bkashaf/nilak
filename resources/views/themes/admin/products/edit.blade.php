@extends('themes.admin.layouts.master')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>ویرایش محصول</h2>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">بازگشت</a>
</div>

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>خطا!</strong> لطفاً موارد زیر را بررسی کنید:
        <ul class="mt-2 mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="product-form">
    @csrf
    @method('PUT')

    <div class="card mb-4">
        <div class="card-header">اطلاعات اصلی</div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">نام فارسی *</label>
                <input type="text" name="name_fa" class="form-control" value="{{ old('name_fa', $product->translation('fa')?->name ?? $product->name) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">نام انگلیسی</label>
                <input type="text" name="name_en" class="form-control" dir="ltr" value="{{ old('name_en', $product->translation('en')?->name) }}">
            </div>

            <div class="mb-3">
                <label class="form-label">اسلاگ</label>
                <input type="text" name="slug" class="form-control" value="{{ old('slug', $product->slug) }}">
            </div>

            <div class="mb-3">
                <label class="form-label">توضیح کوتاه فارسی</label>
                <textarea name="short_description_fa" class="form-control" rows="2">{{ old('short_description_fa', $product->translation('fa')?->short_description ?? $product->short_description) }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">توضیح کوتاه انگلیسی</label>
                <textarea name="short_description_en" class="form-control" rows="2" dir="ltr">{{ old('short_description_en', $product->translation('en')?->short_description) }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">توضیحات کامل فارسی</label>
                <textarea name="description_fa" class="form-control" rows="5">{{ old('description_fa', $product->translation('fa')?->description ?? $product->description) }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">توضیحات کامل انگلیسی</label>
                <textarea name="description_en" class="form-control" rows="5" dir="ltr">{{ old('description_en', $product->translation('en')?->description) }}</textarea>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">قیمت و موجودی</div>
        <div class="card-body">
            <div class="admin-price-grid mb-4">
                <div class="admin-price-card">
                    <span class="admin-price-card-label">قیمت فعلی فروش</span>
                    <div class="admin-price-card-value">{{ number_format($product->price) }} تومان</div>
                </div>

                <div class="admin-price-card">
                    <span class="admin-price-card-label">وضعیت تخفیف</span>
                    <div class="admin-price-card-value">
                        @if($product->has_discount)
                            {{ $product->discount_percent }}٪ تخفیف
                        @else
                            بدون تخفیف
                        @endif
                    </div>
                </div>
            </div>

            @if($product->latestPriceHistory)
                <div class="alert alert-light border">
                    آخرین تغییر قیمت:
                    {{ number_format($product->latestPriceHistory->new_price) }} تومان
                    @if($product->latestPriceHistory->reason)
                        - {{ $product->latestPriceHistory->reason }}
                    @endif
                </div>
            @endif

            <div class="mb-3">
                <label class="form-label">قیمت فعلی *</label>
                <input type="number" name="price" class="form-control" value="{{ old('price', $product->price) }}" min="0" step="0.01" required>
            </div>

            <div class="mb-3">
                <label class="form-label">قیمت قبل از تخفیف</label>
                <input type="number" name="compare_price" class="form-control" value="{{ old('compare_price', $product->compare_price) }}" min="0" step="0.01">
            </div>

            <div class="mb-3">
                <label class="form-label">دلیل تغییر قیمت</label>
                <input type="text" name="price_change_reason" class="form-control" value="{{ old('price_change_reason') }}" maxlength="255">
            </div>

            <div class="mb-3">
                <label class="form-label">موجودی *</label>
                <input type="number" name="stock" class="form-control" value="{{ old('stock', $product->stock) }}" min="0" required>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">دسته‌بندی و وضعیت</div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">دسته‌بندی</label>
                <select name="category_id" class="form-select">
                    <option value="">— بدون دسته‌بندی —</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ (string) old('category_id', $product->category_id) === (string) $cat->id ? 'selected' : '' }}>
                            {{ $cat->localized_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">فعال باشد</label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_featured">محصول ویژه</label>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">ویژگی‌های محصول</div>
        <div class="card-body">
            @forelse($attributes as $attribute)
                @php
                    $selectedValues = collect(old('attribute_values.' . $attribute->id, $selectedAttributeValuesByAttribute[$attribute->id] ?? []))
                        ->map(fn ($id) => (int) $id)
                        ->all();
                @endphp

                <div class="mb-4 border rounded p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <div class="fw-bold">{{ $attribute->name }}</div>
                            <div class="small text-muted">
                                {{ $attribute->selection_mode === 'multiple' ? 'چند انتخاب مجاز است' : 'فقط یک انتخاب مجاز است' }}
                            </div>
                        </div>
                    </div>

                    @if($attribute->values->isEmpty())
                        <div class="text-muted">برای این ویژگی هنوز مقداری تعریف نشده است.</div>
                    @else
                        <div class="row g-2">
                            @foreach($attribute->values as $value)
                                <div class="col-md-4">
                                    <label class="border rounded p-2 d-flex align-items-center gap-2 w-100">
                                        <input
                                            type="{{ $attribute->selection_mode === 'multiple' ? 'checkbox' : 'radio' }}"
                                            name="attribute_values[{{ $attribute->id }}]{{ $attribute->selection_mode === 'multiple' ? '[]' : '[]' }}"
                                            value="{{ $value->id }}"
                                            @checked(in_array($value->id, $selectedValues, true))
                                        >
                                        @if($value->normalized_color_hex)
                                            <span style="display:inline-block;width:18px;height:18px;border-radius:4px;border:1px solid #ced4da;background:{{ $value->normalized_color_hex }};"></span>
                                        @endif
                                        <span>{{ $value->value }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <p class="text-muted mb-0">هنوز ویژگی‌ای تعریف نشده است.</p>
            @endforelse
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">اطلاعات اضافی</div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Meta (JSON)</label>
                <textarea name="meta" class="form-control" rows="3">{{ old('meta', $product->meta ? json_encode($product->meta, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : '') }}</textarea>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">تصاویر محصول</div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">آپلود تصاویر جدید</label>
                <input type="file" name="images[]" class="form-control" multiple>
            </div>

            <hr>

            <h5>تصاویر فعلی</h5>

            @if($product->images->count())
                <div class="row">
                    @foreach($product->images as $img)
                        <div class="col-md-3 text-center mb-3">
                            <img
                                src="{{ asset('storage/' . $img->path) }}"
                                class="img-thumbnail mb-2"
                                style="width:120px; height:120px; object-fit:cover;"
                                alt="{{ $product->localized_name }}"
                            >

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="primary_image_id" value="{{ $img->id }}" {{ $img->is_primary ? 'checked' : '' }}>
                                <label class="form-check-label">تصویر اصلی</label>
                            </div>

                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="remove_images[]" value="{{ $img->id }}">
                                <label class="form-check-label text-danger">حذف تصویر</label>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted mb-0">تصویری برای این محصول ثبت نشده است.</p>
            @endif
        </div>
    </div>

    <button type="submit" class="btn btn-success">ذخیره تغییرات</button>
</form>

@endsection