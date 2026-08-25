<div class="col-12">
    <label class="form-label" for="content">محتوا</label>
    <textarea id="content" name="content" rows="16" class="form-control js-page-editor">{{ $contentValue ?? '' }}</textarea>
    <div class="form-text">امکانات حرفه ای: RTL/LTR، Justify، تصویر تمام عرض، چیدمان دو ستونه، جدول، کادر و سایه تصویر.</div>
</div>

<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
(function () {
    if (typeof tinymce === 'undefined') return;

    tinymce.remove('.js-page-editor');
    tinymce.init({
        selector: '.js-page-editor',
        height: 620,
        menubar: 'file edit view insert format table tools help',
        plugins: 'anchor autolink charmap code fullscreen help image link lists media preview searchreplace table visualblocks wordcount directionality autoresize template',
        toolbar: 'undo redo | blocks fontsize | bold italic underline forecolor backcolor | alignleft aligncenter alignright alignjustify | ltr rtl | bullist numlist outdent indent | link image media table template | removeformat code fullscreen preview',
        content_css: false,
        content_style: `
            body{font-family:Vazirmatn,sans-serif;padding:16px;line-height:1.9;color:#1f2937}
            .lead{font-size:1.2rem;color:#374151}
            .content-wrap{max-width:1200px;margin-inline:auto}
            .full-bleed{margin-inline:calc(50% - 50vw);width:100vw}
            .image-frame{border:1px solid #e5e7eb;border-radius:12px;padding:8px;background:#fff}
            .image-shadow{box-shadow:0 8px 20px rgba(0,0,0,.12)}
            .two-col{display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;align-items:start}
            .two-col .box{padding:12px;border:1px solid #e5e7eb;border-radius:10px}
            @media(max-width:760px){.two-col{grid-template-columns:1fr}}
            table{border-collapse:collapse;width:100%}
            table td,table th{border:1px solid #e5e7eb;padding:8px}
        `,
        images_upload_url: '{{ route('admin.pages.upload-image') }}',
        automatic_uploads: true,
        images_upload_credentials: true,
        convert_urls: false,
        setup: function (editor) {
            editor.on('init', function () {
                const body = editor.getBody();
                if (body && !body.classList.contains('content-wrap')) {
                    body.classList.add('content-wrap');
                }
            });
        },
        images_upload_handler: (blobInfo) => new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '{{ route('admin.pages.upload-image') }}');
            xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

            xhr.onload = function () {
                if (xhr.status < 200 || xhr.status >= 300) {
                    reject('HTTP Error: ' + xhr.status);
                    return;
                }

                let json;
                try {
                    json = JSON.parse(xhr.responseText);
                } catch (e) {
                    reject('Invalid JSON response');
                    return;
                }

                if (!json || typeof json.location !== 'string') {
                    reject('Invalid upload response');
                    return;
                }

                resolve(json.location);
            };

            xhr.onerror = function () {
                reject('Image upload failed due to a network error.');
            };

            const formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());
            xhr.send(formData);
        }),
        templates: [
            {
                title: 'بخش دو ستونه (متن + تصویر/جدول)',
                description: 'چیدمان دو ستون با فاصله استاندارد',
                content: '<section class="two-col"><div class="box"><h3>ستون اول</h3><p>متن ستون اول...</p></div><div class="box"><h3>ستون دوم</h3><p>متن/تصویر/جدول ستون دوم...</p></div></section><p></p>'
            },
            {
                title: 'تصویر تمام عرض',
                description: 'نمایش تصویر به صورت full width',
                content: '<figure class="full-bleed image-shadow"><img src="https://placehold.co/1920x720" alt="hero" style="width:100%;height:auto;display:block;"></figure><p></p>'
            },
            {
                title: 'تصویر با کادر و سایه نرم',
                description: 'قاب استاندارد برای تصویر در متن',
                content: '<figure class="image-frame image-shadow"><img src="https://placehold.co/1200x700" alt="content" style="width:100%;height:auto;display:block;border-radius:8px;"><figcaption style="margin-top:8px;color:#6b7280">توضیح تصویر</figcaption></figure><p></p>'
            }
        ]
    });
})();
</script>
