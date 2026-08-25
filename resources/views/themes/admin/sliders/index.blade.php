@extends('themes.admin.layouts.master')

@section('title', 'مدیریت اسلایدر')

@section('content')
    <div class="mb-4">
        <h1 class="h2 mb-1">مدیریت اسلایدر</h1>
        <p class="text-muted mb-0">اسلایدر چندزبانه با تصویر دسکتاپ/موبایل، پیش نمایش و تنظیم کادر تصویر</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="alert alert-info">
        <div class="fw-semibold mb-2">راهنمای تصویر پیشنهادی</div>
        <ul class="mb-0">
            <li>اسلایدر دسکتاپ: پیشنهادی 1920x720 پیکسل، حداقل 1200x500، حداکثر 3MB.</li>
            <li>اسلایدر موبایل: پیشنهادی 1080x1350 پیکسل، حداقل 700x700، حداکثر 2MB.</li>
            <li>فرمت مناسب: JPG یا WebP برای حجم کمتر و سرعت بهتر.</li>
            <li>آپلود تصویر موبایل اختیاری است؛ اگر آپلود نکنید سیستم همان تصویر اصلی را برای موبایل با کادر هوشمند نمایش می دهد.</li>
            <li>پس از انتخاب تصویر، با اسلایدرهای موقعیت کادر می توانید نقطه تمرکز تصویر را تنظیم کنید.</li>
        </ul>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h5 mb-3">افزودن اسلاید جدید</h2>

            <form method="POST" action="{{ route('admin.sliders.store') }}" enctype="multipart/form-data" class="row g-3" data-slider-form>
                @csrf

                <div class="col-md-3">
                    <label class="form-label">کلید نمایش</label>
                    <select name="key" class="form-select" required>
                        <option value="home_hero">home_hero (صفحه خانه)</option>
                        <option value="shop_hero">shop_hero (صفحه فروشگاه)</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">زبان اسلاید</label>
                    <select name="locale" class="form-select">
                        <option value="">همه زبان ها (Fallback)</option>
                        <option value="fa">فارسی</option>
                        <option value="en">English</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">ترتیب</label>
                    <input name="position" type="number" min="0" class="form-control" value="0">
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked id="is_active_new">
                        <label class="form-check-label" for="is_active_new">فعال</label>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label">عنوان</label>
                    <input name="title" class="form-control" placeholder="عنوان اسلاید">
                </div>

                <div class="col-md-4">
                    <label class="form-label">زیرعنوان</label>
                    <input name="subtitle" class="form-control" placeholder="زیرعنوان">
                </div>

                <div class="col-md-4">
                    <label class="form-label">توضیح کوتاه</label>
                    <input name="description" class="form-control" placeholder="توضیح">
                </div>

                <div class="col-md-6">
                    <label class="form-label">تصویر دسکتاپ</label>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="form-control" data-preview-input="desktop">
                </div>

                <div class="col-md-6">
                    <label class="form-label">تصویر موبایل (اختیاری - Override)</label>
                    <input type="file" name="mobile_image" accept="image/jpeg,image/png,image/webp" class="form-control" data-preview-input="mobile">
                </div>

                <div class="col-md-6">
                    <label class="form-label">متن لینک</label>
                    <input name="link_text" class="form-control" placeholder="مثلا: مشاهده محصولات">
                </div>

                <div class="col-md-6">
                    <label class="form-label">آدرس لینک</label>
                    <input name="link_url" class="form-control" placeholder="/shop یا https://...">
                </div>

                <div class="col-md-6">
                    <label class="form-label">کادر تصویر دسکتاپ: محور افقی</label>
                    <input name="focal_x" type="range" min="0" max="100" value="50" class="form-range" data-pos-input="desktop-x">
                    <label class="form-label">کادر تصویر دسکتاپ: محور عمودی</label>
                    <input name="focal_y" type="range" min="0" max="100" value="50" class="form-range" data-pos-input="desktop-y">
                </div>

                <div class="col-md-6">
                    <label class="form-label">کادر تصویر موبایل: محور افقی</label>
                    <input name="mobile_focal_x" type="range" min="0" max="100" value="50" class="form-range" data-pos-input="mobile-x">
                    <label class="form-label">کادر تصویر موبایل: محور عمودی</label>
                    <input name="mobile_focal_y" type="range" min="0" max="100" value="50" class="form-range" data-pos-input="mobile-y">
                </div>

                <div class="col-md-6">
                    <div class="border rounded p-2">
                        <div class="small text-muted mb-2">پیش نمایش دسکتاپ</div>
                        <div class="bg-dark rounded overflow-hidden" style="height: 180px;">
                            <img data-preview="desktop" alt="Desktop preview" style="width:100%;height:100%;object-fit:cover;object-position:50% 50%;display:block;">
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded p-2">
                        <div class="small text-muted mb-2">پیش نمایش موبایل</div>
                        <div class="bg-dark rounded overflow-hidden" style="height: 240px; max-width: 170px;">
                            <img data-preview="mobile" alt="Mobile preview" style="width:100%;height:100%;object-fit:cover;object-position:50% 50%;display:block;">
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <button class="btn btn-primary">افزودن اسلاید</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>کلید</th>
                        <th>زبان</th>
                        <th>عنوان</th>
                        <th>تصویر</th>
                        <th>ترتیب</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sliders as $slider)
                        <tr>
                            <td>{{ $slider->key }}</td>
                            <td>{{ $slider->locale ?: 'all' }}</td>
                            <td>{{ $slider->title ?? '—' }}</td>
                            <td>
                                @if($slider->image_path)
                                    <img src="{{ asset($slider->image_path) }}" alt="{{ $slider->title ?? 'slide' }}" style="width:120px;height:48px;object-fit:cover;object-position:{{ $slider->focal_x ?? 50 }}% {{ $slider->focal_y ?? 50 }}%;">
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $slider->position }}</td>
                            <td>{{ $slider->is_active ? 'فعال' : 'غیرفعال' }}</td>
                            <td>
                                <details>
                                    <summary class="btn btn-sm btn-outline-primary">ویرایش</summary>
                                    <form method="POST" action="{{ route('admin.sliders.update', $slider) }}" enctype="multipart/form-data" class="row g-2 mt-2">
                                        @csrf
                                        @method('PUT')
                                        <div class="col-12">
                                            <select name="key" class="form-select form-select-sm">
                                                <option value="home_hero" @selected($slider->key === 'home_hero')>home_hero</option>
                                                <option value="shop_hero" @selected($slider->key === 'shop_hero')>shop_hero</option>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <select name="locale" class="form-select form-select-sm">
                                                <option value="" @selected(!$slider->locale)>همه زبان ها</option>
                                                <option value="fa" @selected($slider->locale === 'fa')>فارسی</option>
                                                <option value="en" @selected($slider->locale === 'en')>English</option>
                                            </select>
                                        </div>
                                        <div class="col-12"><input name="title" class="form-control form-control-sm" value="{{ $slider->title }}" placeholder="عنوان"></div>
                                        <div class="col-12"><input name="subtitle" class="form-control form-control-sm" value="{{ $slider->subtitle }}" placeholder="زیرعنوان"></div>
                                        <div class="col-12"><input name="description" class="form-control form-control-sm" value="{{ $slider->description }}" placeholder="توضیح"></div>
                                        <div class="col-12"><input name="link_text" class="form-control form-control-sm" value="{{ $slider->link_text }}" placeholder="متن لینک"></div>
                                        <div class="col-12"><input name="link_url" class="form-control form-control-sm" value="{{ $slider->link_url }}" placeholder="لینک"></div>
                                        <div class="col-12"><input type="file" name="image" class="form-control form-control-sm" accept="image/jpeg,image/png,image/webp"></div>
                                        <div class="col-12"><input type="file" name="mobile_image" class="form-control form-control-sm" accept="image/jpeg,image/png,image/webp"></div>
                                        <div class="col-6"><label class="small">Focal X</label><input name="focal_x" type="number" min="0" max="100" class="form-control form-control-sm" value="{{ $slider->focal_x ?? 50 }}"></div>
                                        <div class="col-6"><label class="small">Focal Y</label><input name="focal_y" type="number" min="0" max="100" class="form-control form-control-sm" value="{{ $slider->focal_y ?? 50 }}"></div>
                                        <div class="col-6"><label class="small">Mobile X</label><input name="mobile_focal_x" type="number" min="0" max="100" class="form-control form-control-sm" value="{{ $slider->mobile_focal_x ?? 50 }}"></div>
                                        <div class="col-6"><label class="small">Mobile Y</label><input name="mobile_focal_y" type="number" min="0" max="100" class="form-control form-control-sm" value="{{ $slider->mobile_focal_y ?? 50 }}"></div>
                                        <div class="col-6"><input name="position" type="number" min="0" class="form-control form-control-sm" value="{{ $slider->position }}"></div>
                                        <div class="col-6 d-flex align-items-center">
                                            <div class="form-check">
                                                <input id="active-{{ $slider->id }}" class="form-check-input" type="checkbox" name="is_active" value="1" @checked($slider->is_active)>
                                                <label for="active-{{ $slider->id }}" class="form-check-label">فعال</label>
                                            </div>
                                        </div>
                                        <div class="col-12 d-flex gap-2">
                                            <button class="btn btn-sm btn-success">ذخیره</button>
                                    </form>
                                            <form method="POST" action="{{ route('admin.sliders.destroy', $slider) }}" onsubmit="return confirm('حذف اسلاید انجام شود؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">حذف</button>
                                            </form>
                                        </div>
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">اسلایدی تعریف نشده است.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('[data-slider-form]');
        if (!form) return;

        const previewDesktop = form.querySelector('[data-preview="desktop"]');
        const previewMobile = form.querySelector('[data-preview="mobile"]');

        const desktopInput = form.querySelector('[data-preview-input="desktop"]');
        const mobileInput = form.querySelector('[data-preview-input="mobile"]');

        const desktopX = form.querySelector('[data-pos-input="desktop-x"]');
        const desktopY = form.querySelector('[data-pos-input="desktop-y"]');
        const mobileX = form.querySelector('[data-pos-input="mobile-x"]');
        const mobileY = form.querySelector('[data-pos-input="mobile-y"]');

        function bindFile(input, preview) {
            input?.addEventListener('change', function () {
                const file = this.files && this.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            });
        }

        function updatePosition() {
            if (previewDesktop) {
                previewDesktop.style.objectPosition = `${desktopX.value}% ${desktopY.value}%`;
            }
            if (previewMobile) {
                previewMobile.style.objectPosition = `${mobileX.value}% ${mobileY.value}%`;
            }
        }

        bindFile(desktopInput, previewDesktop);
        bindFile(mobileInput, previewMobile);

        [desktopX, desktopY, mobileX, mobileY].forEach(function (el) {
            el?.addEventListener('input', updatePosition);
        });

        updatePosition();
    });
    </script>
@endsection
