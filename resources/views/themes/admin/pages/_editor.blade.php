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
    <div class="form-text">امکانات حرفه ای: RTL/LTR، Justify، تصویر تمام عرض، چیدمان دو ستونه، جدول، کادر و سایه تصویر.</div>
</div>
