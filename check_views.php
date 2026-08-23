<?php
require __DIR__ . "/vendor/autoload.php";
$app = require __DIR__ . "/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo view()->exists("themes.default.home") ? "true\n" : "false\n";
echo view()->exists("themes.default.home.home") ? "true\n" : "false\n";
echo view()->exists("themes.default.layouts.shop") ? "true\n" : "false\n";
