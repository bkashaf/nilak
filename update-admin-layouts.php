<?php

/**
 * این اسکریپت تمام فایل‌های بخش مدیریت را پیدا می‌کند
 * و دستور اشتباه @extends('layouts.admin')
 * را به مسیر صحیح @extends('themes.admin.layouts.master')
 * تبدیل می‌کند.
 */

$basePath = __DIR__ . '/resources/views/admin';

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($basePath)
);

foreach ($iterator as $file) {

    if ($file->isFile() && $file->getExtension() === 'blade.php') {

        $path = $file->getRealPath();
        $content = file_get_contents($path);

        // اگر فایل شامل layout اشتباه باشد، اصلاح می‌کنیم
        if (strpos($content, "@extends('layouts.admin')") !== false) {

            $updated = str_replace(
                "@extends('layouts.admin')",
                "@extends('themes.admin.layouts.master')",
                $content
            );

            file_put_contents($path, $updated);

            echo "✔ اصلاح شد: $path\n";
        }
    }
}

echo "\n🎉 تمام فایل‌ها با موفقیت اصلاح شدند.\n";
