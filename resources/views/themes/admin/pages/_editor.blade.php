@vite(['resources/js/admin-page-editor.js'])

<div class="col-12">
    <label class="form-label" for="content">محتوا</label>
    <textarea
        id="content"
        name="content"
        rows="16"
        dir="{{ app()->getLocale() === 'fa' ? 'rtl' : 'ltr' }}"
        class="form-control js-page-editor"
        data-upload-url="{{ route('admin.pages.upload-image') }}"
        data-csrf-token="{{ csrf_token() }}"
    >{{ $contentValue ?? '' }}</textarea>

        <div class="alert alert-light border mt-3 mb-0 py-2 px-3 small" role="alert">
        <strong>راهنمای سریع بلوک های آماده:</strong>
        از نوار ابزار، گزینه «بلوک آماده» را بزنید و بلوک موردنظر را درج کنید.
        بلوک ها به صورت واکنش گرا هستند؛ در موبایل ستون ها تک ستونه می شوند.
        فقط بلوک «تصویر تمام عرض» تا لبه صفحه باز می شود و بقیه بلوک ها داخل عرض محتوای صفحه نمایش داده می شوند.
    </div>  
</div>

