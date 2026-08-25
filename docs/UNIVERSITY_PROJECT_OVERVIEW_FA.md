# معرفی جامع پروژه دانشگاهی Nilak

## 1) معرفی پروژه

Nilak یک پلتفرم فروشگاه آنلاین مبتنی بر Laravel است که با رویکرد مهندسی نرم افزار ماژولار توسعه داده شده است. تمرکز پروژه بر پایداری فرایند سفارش و پرداخت، قابلیت توسعه بلندمدت، و فراهم کردن تجربه کاربری دو زبانه (فارسی/انگلیسی) است.

این پروژه به صورت متن باز منتشر شده و در GitHub در دسترس عموم قرار دارد تا زمینه مشارکت، گزارش اشکال، و توسعه جمعی فراهم باشد.

## 2) اهداف مهندسی

- طراحی معماری لایه بندی شده با تفکیک منطق دامنه از کنترلرها
- پیاده سازی چرخه کامل سفارش، پرداخت و پیگیری
- تضمین سازگاری موجودی انبار با وضعیت پرداخت
- ایجاد پنل مدیریتی قابل اتکا برای عملیات فروشگاه
- ارائه مسیر نصب واقعی برای هاست اشتراکی (cPanel + MySQL)

## 3) فناوری ها و چارچوب

- زبان و فریم ورک: PHP 8.2 + Laravel 12
- احراز هویت API: Laravel Sanctum
- پایگاه داده: MySQL
- قالب و رابط: Blade + Vite
- آزمون: PHPUnit Feature Tests

## 4) معماری و ساختار فنی

### 4-1) لایه دامنه (Domain Layer)

منطق اصلی کسب و کار در مسیر زیر قرار دارد:

- app/Domain/Cart
- app/Domain/Order
- app/Domain/Payment
- app/Domain/Inventory

نمونه های مهم:

- app/Domain/Order/OrderService.php
- app/Domain/Payment/Services/PaymentService.php
- app/Domain/Payment/Services/PaymentStatusService.php
- app/Domain/Inventory/InventoryReservationService.php

### 4-2) لایه ارائه و ورودی ها

- کنترلرهای فرانت: app/Http/Controllers/Front
- کنترلرهای ادمین: app/Http/Controllers/Admin
- کنترلرهای API: app/Http/Controllers/Api

مسیرهای اصلی:

- routes/web.php
- routes/admin.php
- routes/api.php

### 4-3) مدل داده و موجودیت ها

مدل های کلیدی:

- app/Models/User.php
- app/Models/Product.php
- app/Models/Order.php
- app/Models/Payment.php
- app/Models/PaymentMethod.php
- app/Models/Slider.php
- app/Models/Page.php

مهاجرت های مهم:

- database/migrations/2026_08_23_000008_harden_payment_lifecycle.php
- database/migrations/2026_08_23_000009_add_reserved_stock_to_products_table.php
- database/migrations/2026_08_23_000010_add_inventory_status_to_orders_table.php
- database/migrations/2026_08_23_000011_create_bank_receipts_table.php
- database/migrations/2026_08_23_000012_create_refunds_and_order_status_histories.php
- database/migrations/2026_08_25_000015_create_pages_table.php
- database/migrations/2026_08_25_000016_enhance_sliders_for_locale_and_images.php

## 5) قابلیت های فعلی پروژه

### 5-1) فروشگاه و تجربه کاربر

- صفحه خانه و فروشگاه با اسلایدر چندزبانه
- صفحه محصول، سبد خرید، تسویه حساب، پیگیری سفارش
- انتخاب آدرس از پروفایل یا ثبت آدرس جدید در checkout
- نمایش اطلاعات بانکی روش receipt براساس JSON تنظیمات

فایل های مرجع:

- resources/views/themes/default/home.blade.php
- resources/views/themes/default/shop.blade.php
- resources/views/themes/default/checkout.blade.php
- resources/views/themes/default/order-tracking.blade.php
- resources/views/themes/default/layouts/shop.blade.php

### 5-2) پرداخت و سفارش

- پشتیبانی از روش های COD، رسید بانکی و درگاه آنلاین
- مدیریت وضعیت پرداخت و تاریخچه تغییرات
- اتصال پرداخت به رزرو/آزادسازی موجودی
- پیگیری سفارش با tracking code

فایل های مرجع:

- app/Domain/Order/OrderService.php
- app/Domain/Payment/Services/PaymentService.php
- app/Domain/Payment/Services/PaymentStatusService.php
- app/Http/Controllers/Front/PaymentCallbackController.php

### 5-3) پنل مدیریت

- مدیریت سفارش، پرداخت، روش های پرداخت
- مدیریت صفحات محتوایی (انتشار/نمایش در منو)
- مدیریت اسلایدر چندزبانه با آپلود تصویر

فایل های مرجع:

- resources/views/themes/admin/orders
- resources/views/themes/admin/payments
- resources/views/themes/admin/payment-methods/index.blade.php
- resources/views/themes/admin/pages
- resources/views/themes/admin/sliders/index.blade.php

### 5-4) نصب و استقرار

- Installer مرحله ای با بررسی پیش نیاز، تست اتصال دیتابیس و نصب نهایی
- نوشتن تنظیمات .env، اجرای migrate/seed، ایجاد ادمین اولیه، lock نصب

فایل های مرجع:

- app/Http/Controllers/Installer/InstallerController.php
- app/Http/Middleware/EnsureInstallerAccessible.php
- resources/views/installer
- config/installer.php

## 6) ویژگی های برجسته مهندسی

- تفکیک Domain Logic از لایه HTTP
- پایداری وضعیت پرداخت و تاریخچه Audit
- همزمانی منطقی موجودی در برابر پرداخت
- ساختار قابل توسعه برای درگاه های پرداخت جدید
- زیرساخت آماده برای توسعه CI/CD و استقرار واقعی

## 7) مسیر توسعه آینده

- افزودن احراز هویت پیامکی (OTP) و پنل SMS
- تکمیل سیستم کوپن، تخفیف، مالیات و ارسال پیشرفته
- توسعه تحلیل های مدیریتی و داشبورد BI
- پوشش تست عمیق تر Unit/Integration/E2E
- توسعه API عمومی مستند (OpenAPI)
- توسعه تجربه تصویر پیشرفته تر (crop tool drag-and-drop)

## 8) متن باز بودن پروژه و مشارکت

پروژه Nilak متن باز است و در GitHub برای دسترسی عمومی قرار گرفته است. مشارکت توسعه دهندگان، ارسال Pull Request، و گزارش اشکال بخشی از مسیر رسمی رشد پروژه تعریف شده است.

## 9) راهنمای الحاق نمونه کد در گزارش استاد

برای افزودن نمونه کد واقعی در گزارش، پیشنهاد می شود از فایل های زیر قطعه کد انتخاب شود:

- app/Domain/Order/OrderService.php
- app/Domain/Payment/Services/PaymentStatusService.php
- app/Http/Controllers/Front/CartController.php
- app/Support/SliderService.php
- app/Http/Controllers/Installer/InstallerController.php

در گزارش نهایی، هر قطعه کد با توضیح نقش آن در معماری (Input, Domain Logic, Persistence, Output) تحلیل شود تا کیفیت مهندسی پروژه به شکل حرفه ای نمایش داده شود.

## 10) لایسنس و انتساب توسعه دهنده

- لایسنس پروژه: GNU GPL v3.0 (یا نسخه های بعدی) با شناسه GPL-3.0-or-later
- متن کامل لایسنس در فایل LICENSE در ریشه پروژه قرار دارد.
- اطلاعات توسعه دهنده و نگه دارنده در فایل AUTHORS ثبت شده است.

نکته حقوقی:

- درخواست اخلاقی پروژه حفظ نام توسعه دهنده در اسناد و اعلان ها است.
- الزامات حقوقی بازنشر، تغییر و توزیع مجدد مطابق متن GPL در فایل LICENSE اعمال می شود.
