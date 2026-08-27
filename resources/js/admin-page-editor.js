import tinymce from 'tinymce';
import 'tinymce/icons/default';
import 'tinymce/themes/silver';
import 'tinymce/models/dom';

import 'tinymce/plugins/anchor';
import 'tinymce/plugins/autolink';
import 'tinymce/plugins/charmap';
import 'tinymce/plugins/code';
import 'tinymce/plugins/fullscreen';
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
    const isRtl = document.documentElement.getAttribute('dir') === 'rtl';

    try {
        tinymce.remove();
    } catch {
        // Keep textarea usable if removing prior editors fails.
    }

    tinymce.init({
        selector: '.js-page-editor',
        license_key: 'gpl',
        promotion: false,
        branding: false,
        readonly: false,
        height: 620,
        menubar: 'file edit view insert format table tools help',
        skin: false,
        content_css: false,
        toolbar_mode: 'sliding',
        plugins: 'anchor autolink charmap code fullscreen image link lists media preview searchreplace table visualblocks wordcount directionality autoresize',
        toolbar: 'undo redo | blocks fontsize | bold italic underline forecolor backcolor | alignleft aligncenter alignright alignjustify | ltr rtl | bullist numlist outdent indent | link image media table snippets | removeformat code fullscreen preview',
        content_style: `
            body{font-family:Vazirmatn,sans-serif;padding:16px;line-height:1.9;color:#1f2937}
            body{direction:${isRtl ? 'rtl' : 'ltr'};text-align:${isRtl ? 'right' : 'left'}}
            .lead{font-size:1.2rem;color:#374151}
            .content-wrap{max-width:1200px;margin-inline:auto}
            .full-bleed{margin-inline:calc(50% - 50vw);width:100vw}
            .image-frame{border:1px solid #e5e7eb;border-radius:12px;padding:8px;background:#fff}
            .image-shadow{box-shadow:0 8px 20px rgba(0,0,0,.12)}
            .two-col{display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;align-items:start}
            .two-col .box{padding:12px;border:1px solid #e5e7eb;border-radius:10px}
            .nl-block{box-sizing:border-box;color:inherit;font-family:inherit;margin:0 0 1rem}
            .nl-block *{box-sizing:border-box}
            .nl-two-col{display:grid;grid-template-columns:1fr 1fr;gap:1rem;align-items:start}
            .nl-two-col .nl-box{padding:12px;border:1px solid #e5e7eb;border-radius:10px;background:#fff}
            .nl-banner{border-radius:14px;padding:24px 20px;background:linear-gradient(120deg,#0f172a,#1d4ed8);color:#fff}
            .nl-banner .nl-banner-kicker{opacity:.9;font-size:.9rem;margin-bottom:.35rem}
            .nl-banner .nl-banner-title{font-size:1.5rem;line-height:1.4;margin:0 0 .5rem}
            .nl-btn{display:inline-block;padding:.65rem 1rem;border-radius:10px;background:#fff;color:#0f172a;text-decoration:none;font-weight:700}
            .nl-btn-outline{display:inline-block;padding:.65rem 1rem;border-radius:10px;border:1px solid #cbd5e1;color:inherit;text-decoration:none;font-weight:700}
            .nl-trust-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:.75rem}
            .nl-trust-item{padding:10px;border:1px solid #e5e7eb;border-radius:10px;text-align:center;background:#fff}
            .nl-testimonials{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.75rem}
            .nl-quote{border:1px solid #e5e7eb;border-radius:12px;padding:14px;background:#fff}
            .nl-faq details{border:1px solid #e5e7eb;border-radius:10px;padding:10px 12px;background:#fff;margin-bottom:.5rem}
            .nl-faq summary{cursor:pointer;font-weight:700}
            .nl-product-feature{display:grid;grid-template-columns:minmax(0,1.1fr) minmax(0,.9fr);gap:1rem;align-items:stretch;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;background:#fff}
            .nl-product-feature .nl-media img{width:100%;height:100%;min-height:260px;object-fit:cover;display:block}
            .nl-product-feature .nl-body{padding:18px}
            .nl-product-feature .nl-price{font-size:1.25rem;font-weight:800;margin:.35rem 0 1rem}
            .nl-categories{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.75rem}
            .nl-cat-card{position:relative;overflow:hidden;border-radius:12px;min-height:170px;background:#0f172a;color:#fff;text-decoration:none;display:block}
            .nl-cat-card img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:.7}
            .nl-cat-card span{position:absolute;inset-inline:12px;bottom:12px;z-index:2;font-weight:700}
            @media(max-width:760px){.two-col{grid-template-columns:1fr}}
            @media(max-width:760px){.nl-two-col,.nl-trust-grid,.nl-testimonials,.nl-categories,.nl-product-feature{grid-template-columns:1fr}}
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

                if (body) {
                    body.setAttribute('dir', isRtl ? 'rtl' : 'ltr');
                }

                if (isRtl) {
                    editor.execCommand('mceDirectionRTL');
                } else {
                    editor.execCommand('mceDirectionLTR');
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
                    editor.insertContent('<section class="nl-block nl-two-col"><div class="nl-box"><h3>ستون اول</h3><p>متن ستون اول...</p></div><div class="nl-box"><h3>ستون دوم</h3><p>متن/تصویر/جدول ستون دوم...</p></div></section><p></p>');
                }
            },
            {
                type: 'menuitem',
                text: 'تصویر تمام عرض',
                onAction: function () {
                    editor.insertContent('<figure class="nl-block full-bleed image-shadow"><img src="https://placehold.co/1920x720" alt="hero" style="width:100%;height:auto;display:block;"></figure><p></p>');
                }
            },
            {
                type: 'menuitem',
                text: 'تصویر با کادر و سایه نرم',
                onAction: function () {
                    editor.insertContent('<figure class="nl-block image-frame image-shadow"><img src="https://placehold.co/1200x700" alt="content" style="width:100%;height:auto;display:block;border-radius:8px;"><figcaption style="margin-top:8px;color:#6b7280">توضیح تصویر</figcaption></figure><p></p>');
                }
            },
            {
                type: 'menuitem',
                text: 'بنر تبلیغاتی + دکمه CTA',
                onAction: function () {
                    editor.insertContent('<section class="nl-block nl-banner"><div class="nl-banner-kicker">پیشنهاد ویژه</div><h2 class="nl-banner-title">عنوان کمپین یا تخفیف</h2><p>توضیح کوتاه برای جلب توجه کاربر و افزایش نرخ تبدیل.</p><p><a href="#" class="nl-btn">مشاهده محصولات</a></p></section><p></p>');
                }
            },
            {
                type: 'menuitem',
                text: 'اعتمادسازی (آیکن/مزیت‌ها)',
                onAction: function () {
                    editor.insertContent('<section class="nl-block"><h3>چرا از ما خرید کنید؟</h3><div class="nl-trust-grid"><div class="nl-trust-item"><strong>ارسال سریع</strong><p>تحویل در کوتاه ترین زمان</p></div><div class="nl-trust-item"><strong>پرداخت امن</strong><p>درگاه معتبر و رسید بانکی</p></div><div class="nl-trust-item"><strong>ضمانت اصالت</strong><p>کالاهای اصلی و باکیفیت</p></div><div class="nl-trust-item"><strong>پشتیبانی پاسخگو</strong><p>قبل و بعد از خرید</p></div></div></section><p></p>');
                }
            },
            {
                type: 'menuitem',
                text: 'نظرات مشتریان',
                onAction: function () {
                    editor.insertContent('<section class="nl-block"><h3>نظر مشتریان</h3><div class="nl-testimonials"><blockquote class="nl-quote"><p>کیفیت و بسته بندی عالی بود.</p><cite>مشتری ۱</cite></blockquote><blockquote class="nl-quote"><p>ارسال سریع و پشتیبانی خوب.</p><cite>مشتری ۲</cite></blockquote><blockquote class="nl-quote"><p>حتما دوباره خرید می کنم.</p><cite>مشتری ۳</cite></blockquote></div></section><p></p>');
                }
            },
            {
                type: 'menuitem',
                text: 'سوالات متداول (FAQ)',
                onAction: function () {
                    editor.insertContent('<section class="nl-block nl-faq"><h3>سوالات متداول</h3><details><summary>زمان ارسال سفارش چقدر است؟</summary><p>معمولا 1 تا 3 روز کاری.</p></details><details><summary>امکان مرجوعی وجود دارد؟</summary><p>بله، طبق قوانین بازگشت کالا.</p></details><details><summary>روش های پرداخت چیست؟</summary><p>پرداخت آنلاین، پرداخت در محل، یا رسید بانکی.</p></details></section><p></p>');
                }
            },
            {
                type: 'menuitem',
                text: 'محصول ویژه (Landing)',
                onAction: function () {
                    editor.insertContent('<section class="nl-block nl-product-feature"><div class="nl-media"><img src="https://placehold.co/900x700" alt="featured product"></div><div class="nl-body"><div class="nl-banner-kicker">پیشنهاد ویژه</div><h3>نام محصول ویژه</h3><p>توضیح کوتاه و متقاعدکننده برای معرفی ویژگی یا مزیت محصول.</p><div class="nl-price">۲,۴۹۰,۰۰۰ تومان</div><p><a href="#" class="nl-btn">خرید محصول</a> <a href="#" class="nl-btn-outline">جزئیات بیشتر</a></p></div></section><p></p>');
                }
            },
            {
                type: 'menuitem',
                text: 'دسته بندی تصویری (Landing)',
                onAction: function () {
                    editor.insertContent('<section class="nl-block"><h3>خرید بر اساس دسته بندی</h3><div class="nl-categories"><a class="nl-cat-card" href="#"><img src="https://placehold.co/640x420" alt="دسته ۱"><span>زنانه</span></a><a class="nl-cat-card" href="#"><img src="https://placehold.co/640x420" alt="دسته ۲"><span>مردانه</span></a><a class="nl-cat-card" href="#"><img src="https://placehold.co/640x420" alt="دسته ۳"><span>اکسسوری</span></a></div></section><p></p>');
                }
            },
            // 🔹 بلوک جدید برای صفحه‌ساز نیلک
            {
                type: 'menuitem',
                text: 'محصولات ویژه (Grid)',
                onAction: function () {
                    editor.insertContent('<section class="nl-block"><h3>محصولات ویژه</h3><div class="product-grid"><div class="product-item"><img src="https://placehold.co/400x400" alt="محصول ۱"><h4>محصول ۱</h4><p class="price">۲۵۰,۰۰۰ تومان</p></div><div class="product-item"><img src="https://placehold.co/400x400" alt="محصول ۲"><h4>محصول ۲</h4><p class="price">۳۲۰,۰۰۰ تومان</p></div><div class="product-item"><img src="https://placehold.co/400x400" alt="محصول ۳"><h4>محصول ۳</h4><p class="price">۴۱۰,۰۰۰ تومان</p></div></div></section><p></p>');
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
    }).catch(() => {
        editorEl.removeAttribute('readonly');
        editorEl.disabled = false;
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPageEditor);
} else {
    initPageEditor();
}
