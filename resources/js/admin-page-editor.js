import tinymce from 'tinymce/tinymce';
import 'tinymce/icons/default';
import 'tinymce/themes/silver';
import 'tinymce/models/dom';

import 'tinymce/plugins/anchor';
import 'tinymce/plugins/autolink';
import 'tinymce/plugins/charmap';
import 'tinymce/plugins/code';
import 'tinymce/plugins/fullscreen';
import 'tinymce/plugins/help';
import 'tinymce/plugins/image';
import 'tinymce/plugins/link';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/media';
import 'tinymce/plugins/preview';
import 'tinymce/plugins/searchreplace';
import 'tinymce/plugins/table';
import 'tinymce/plugins/visualblocks';
import 'tinymce/plugins/wordcount';
import 'tinymce/plugins/directionality';
import 'tinymce/plugins/autoresize';

import 'tinymce/skins/ui/oxide/skin.min.css';
import 'tinymce/skins/content/default/content.min.css';

function initPageEditor() {
    const editorEl = document.querySelector('.js-page-editor');
    if (!editorEl) {
        return;
    }

    const uploadUrl = editorEl.dataset.uploadUrl;
    const csrfToken = editorEl.dataset.csrfToken;

    tinymce.remove('.js-page-editor');

    tinymce.init({
        selector: '.js-page-editor',
        height: 620,
        menubar: 'file edit view insert format table tools help',
        plugins: 'anchor autolink charmap code fullscreen help image link lists media preview searchreplace table visualblocks wordcount directionality autoresize',
        toolbar: 'undo redo | blocks fontsize | bold italic underline forecolor backcolor | alignleft aligncenter alignright alignjustify | ltr rtl | bullist numlist outdent indent | link image media table snippets | removeformat code fullscreen preview',
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
        images_upload_url: uploadUrl,
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

            editor.ui.registry.addMenuButton('snippets', {
                text: 'بلوک آماده',
                fetch: function (callback) {
                    callback([
                        {
                            type: 'menuitem',
                            text: 'بخش دو ستونه (متن + تصویر/جدول)',
                            onAction: function () {
                                editor.insertContent('<section class="two-col"><div class="box"><h3>ستون اول</h3><p>متن ستون اول...</p></div><div class="box"><h3>ستون دوم</h3><p>متن/تصویر/جدول ستون دوم...</p></div></section><p></p>');
                            }
                        },
                        {
                            type: 'menuitem',
                            text: 'تصویر تمام عرض',
                            onAction: function () {
                                editor.insertContent('<figure class="full-bleed image-shadow"><img src="https://placehold.co/1920x720" alt="hero" style="width:100%;height:auto;display:block;"></figure><p></p>');
                            }
                        },
                        {
                            type: 'menuitem',
                            text: 'تصویر با کادر و سایه نرم',
                            onAction: function () {
                                editor.insertContent('<figure class="image-frame image-shadow"><img src="https://placehold.co/1200x700" alt="content" style="width:100%;height:auto;display:block;border-radius:8px;"><figcaption style="margin-top:8px;color:#6b7280">توضیح تصویر</figcaption></figure><p></p>');
                            }
                        }
                    ]);
                }
            });
        },
        images_upload_handler: (blobInfo) => new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', uploadUrl);
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken || '');

            xhr.onload = function () {
                if (xhr.status < 200 || xhr.status >= 300) {
                    reject('HTTP Error: ' + xhr.status);
                    return;
                }

                let json;
                try {
                    json = JSON.parse(xhr.responseText);
                } catch {
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
        templates: []
    });
}

document.addEventListener('DOMContentLoaded', initPageEditor);
