<?php

/**
 * این اسکریپت فقط فایل‌های مسیر resources/views/admin را اصلاح می‌کند
 * و مسیر themes/admin را دست‌نخورده باقی می‌گذارد.
 */

$basePath = __DIR__ . '/resources/views/admin';

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($basePath)
);

$changedFiles = [];

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'blade.php') {
        $path = $file->getRealPath();
        $content = file_get_contents($path);

        if (strpos($content, "@extends('layouts.admin')") !== false) {
            $updated = str_replace(
                "@extends('layouts.admin')",
                "@extends('themes.admin.layouts.master')",
                $content
            );

            file_put_contents($path, $updated);
            $changedFiles[] = $path;
        }
    }
}

if (count($changedFiles) > 0) {
    echo "🎯 فایل‌های اصلاح‌شده:\n";
    foreach ($changedFiles as $path) {
        echo "✔ $path\n";
    }
    echo "\n✅ تمام فایل‌های مسیر admin با موفقیت اصلاح شدند.\n";
} else {
    echo "ℹ️ هیچ فایلی برای اصلاح پیدا نشد.\n";
}
