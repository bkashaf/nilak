<?php

namespace App\Http\Controllers\Installer;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use PDO;
use Throwable;

class InstallerController extends Controller
{
    public function welcome()
    {
        $resumeUrl = $this->determineResumeUrl();

        return view('installer.welcome', compact('resumeUrl'));
    }

    public function resume()
    {
        return redirect()->to($this->determineResumeUrl());
    }

    public function requirements()
    {
        $requiredExtensions = (array) config('installer.required_extensions', []);
        $requiredPaths = (array) config('installer.required_writable_paths', []);

        $checks = [
            [
                'label' => 'PHP Version >= 8.2',
                'level' => 'required',
                'status' => version_compare(PHP_VERSION, '8.2.0', '>='),
                'detail' => PHP_VERSION,
            ],
        ];

        foreach ($requiredExtensions as $ext) {
            $checks[] = [
                'label' => 'Extension: ' . $ext,
                'level' => 'required',
                'status' => extension_loaded($ext),
                'detail' => extension_loaded($ext) ? 'Loaded' : 'Missing',
            ];
        }

        foreach ($requiredPaths as $path) {
            $absolute = base_path($path);
            $checks[] = [
                'label' => 'Writable: ' . $path,
                'level' => 'required',
                'status' => is_writable($absolute),
                'detail' => $absolute,
            ];
        }

        $sslActive = request()->isSecure() || strtolower((string) request()->header('x-forwarded-proto')) === 'https';
        $checks[] = [
            'label' => 'SSL (HTTPS) فعال باشد',
            'level' => 'warning',
            'status' => $sslActive,
            'detail' => $sslActive
                ? 'HTTPS detected'
                : 'HTTPS تشخیص داده نشد. برای محیط Production توصیه می شود SSL فعال باشد.',
        ];

        $documentRoot = realpath((string) request()->server('DOCUMENT_ROOT')) ?: '';
        $publicRoot = realpath(public_path()) ?: '';
        $docRootOk = $documentRoot !== '' && $publicRoot !== ''
            && str_replace('\\', '/', $documentRoot) === str_replace('\\', '/', $publicRoot);

        $checks[] = [
            'label' => 'Document Root روی پوشه public باشد',
            'level' => 'warning',
            'status' => $docRootOk,
            'detail' => 'Detected: ' . ($documentRoot ?: 'unknown') . ' | Expected: ' . ($publicRoot ?: 'unknown'),
        ];

        $allOk = collect($checks)
            ->where('level', 'required')
            ->every(fn ($check) => (bool) $check['status']);
        $warningCount = collect($checks)
            ->where('level', 'warning')
            ->where('status', false)
            ->count();

        session(['installer.requirements_passed' => $allOk]);

        return view('installer.requirements', compact('checks', 'allOk', 'warningCount'));
    }

    public function database()
    {
        $defaults = session('installer.db', [
            'db_host' => env('DB_HOST', '127.0.0.1'),
            'db_port' => env('DB_PORT', '3306'),
            'db_database' => env('DB_DATABASE', ''),
            'db_username' => env('DB_USERNAME', ''),
            'db_password' => '',
            'app_url' => env('APP_URL', ''),
            'admin_name' => 'Administrator',
            'admin_email' => 'admin@example.com',
        ]);

        return view('installer.database', compact('defaults'));
    }

    public function databaseTest(Request $request)
    {
        $data = $request->validate([
            'db_host' => ['required', 'string', 'max:200'],
            'db_port' => ['required', 'integer', 'between:1,65535'],
            'db_database' => ['required', 'string', 'max:200'],
            'db_username' => ['required', 'string', 'max:200'],
            'db_password' => ['nullable', 'string', 'max:200'],
            'app_url' => ['required', 'url', 'max:200'],
            'admin_name' => ['required', 'string', 'max:120'],
            'admin_email' => ['required', 'email', 'max:191'],
            'admin_mobile' => ['required', 'string', 'max:20'],
            'admin_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $result = [
            'ok' => false,
            'message' => '',
        ];

        try {
            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $data['db_host'], $data['db_port'], $data['db_database']);
            $pdo = new PDO($dsn, $data['db_username'], (string) ($data['db_password'] ?? ''));
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->query('SELECT 1');

            $result['ok'] = true;
            $result['message'] = 'اتصال پایگاه داده با موفقیت برقرار شد.';
        } catch (Throwable $e) {
            $result['message'] = 'خطا در اتصال پایگاه داده: ' . $e->getMessage();
        }

        session(['installer.db' => $data, 'installer.result' => $result]);

        return redirect()->route('install.store-settings');
    }

    public function storeSettings()
    {
        abort_unless(is_array(session('installer.db')) && (session('installer.result.ok') === true), 404);

        $defaults = session('installer.store', [
            'store_name' => env('APP_NAME', 'Nilak Store'),
            'default_locale' => env('APP_LOCALE', 'fa'),
            'timezone' => env('APP_TIMEZONE', 'Asia/Tehran'),
            'currency_label' => 'تومان',
            'store_logo_path' => null,
        ]);

        return view('installer.store-settings', compact('defaults'));
    }

    public function storeSettingsSave(Request $request)
    {
        abort_unless(is_array(session('installer.db')) && (session('installer.result.ok') === true), 404);

        $data = $request->validate([
            'store_name' => ['required', 'string', 'max:150'],
            'default_locale' => ['required', 'in:fa,en'],
            'timezone' => ['required', 'string', 'max:80'],
            'currency_label' => ['required', 'string', 'max:30'],
            'store_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
        ], [
            'store_logo.image' => 'فایل لوگو معتبر نیست.',
            'store_logo.max' => 'حجم لوگو نباید بیشتر از 2 مگابایت باشد.',
        ]);

        $existingStore = session('installer.store', []);
        $logoPath = $existingStore['store_logo_path'] ?? null;

        if ($request->hasFile('store_logo')) {
            $logoStored = $request->file('store_logo')->store('installer-store', 'public');
            $logoPath = 'storage/' . $logoStored;
        }

        session(['installer.store' => [
            'store_name' => $data['store_name'],
            'default_locale' => $data['default_locale'],
            'timezone' => $data['timezone'],
            'currency_label' => $data['currency_label'],
            'store_logo_path' => $logoPath,
        ]]);

        return redirect()->route('install.summary');
    }

    public function summary()
    {
        $db = session('installer.db');
        $result = session('installer.result');
        $store = session('installer.store');

        abort_unless(is_array($db) && is_array($store), 404);

        $envPreview = [
            'APP_NAME="' . addslashes($store['store_name']) . '"',
            'APP_ENV=production',
            'APP_DEBUG=false',
            'APP_URL=' . $db['app_url'],
            'APP_LOCALE=' . $store['default_locale'],
            'APP_FALLBACK_LOCALE=' . $store['default_locale'],
            'APP_TIMEZONE=' . $store['timezone'],
            'DB_CONNECTION=mysql',
            'DB_HOST=' . $db['db_host'],
            'DB_PORT=' . $db['db_port'],
            'DB_DATABASE=' . $db['db_database'],
            'DB_USERNAME=' . $db['db_username'],
            'DB_PASSWORD=' . ($db['db_password'] !== '' ? '*** hidden ***' : ''),
            'ADMIN_EMAIL=' . $db['admin_email'],
        ];

        return view('installer.summary', [
            'db' => $db,
            'store' => $store,
            'result' => $result,
            'envPreview' => $envPreview,
        ]);
    }

    public function run(Request $request)
    {
        $request->validate([
            'confirm_apply' => ['required', 'accepted'],
        ], [
            'confirm_apply.accepted' => 'برای ادامه نصب، تایید نهایی را فعال کنید.',
        ]);

        $db = session('installer.db');
        $result = session('installer.result');
        $store = session('installer.store');

        if (! is_array($db) || ! is_array($store) || ! (($result['ok'] ?? false) === true)) {
            return redirect()->route('install.database')->with('error', 'ابتدا تست اتصال پایگاه داده را با موفقیت انجام دهید.');
        }

        $lockFile = storage_path('app/installed.lock');
        if (file_exists($lockFile)) {
            return redirect()->route('home')->with('warning', 'سیستم قبلا نصب شده است.');
        }

        $report = [];
        $backupPath = null;

        try {
            $backupPath = $this->prepareEnvFile($report);
            $this->writeEnvValues($db, $store, $report);

            $this->runArtisan('key:generate --force', $report);
            $this->runArtisan('migrate --force', $report);
            $this->runArtisan('db:seed --class=Database\\Seeders\\RolePermissionSeeder --force', $report, allowFailure: true);
            $this->applyStoreSettings($store, $report);
            $this->createAdminUser($db, $report);
            $this->runArtisan('storage:link', $report, allowFailure: true);
            $this->runArtisan('optimize:clear', $report);

            File::ensureDirectoryExists(dirname($lockFile));
            File::put($lockFile, json_encode([
                'installed_at' => now()->toDateTimeString(),
                'app_url' => $db['app_url'],
                'db_database' => $db['db_database'],
                'store_name' => $store['store_name'] ?? null,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            session()->forget(['installer.db', 'installer.result', 'installer.store', 'installer.requirements_passed']);

            return view('installer.completed', [
                'report' => $report,
            ]);
        } catch (Throwable $e) {
            $this->restoreEnvBackup($backupPath, $report);
            $report[] = [
                'step' => 'error',
                'ok' => false,
                'message' => $e->getMessage(),
            ];

            return view('installer.completed', [
                'report' => $report,
                'hasError' => true,
            ]);
        }
    }

    private function prepareEnvFile(array &$report): ?string
    {
        $envPath = base_path('.env');
        $envExamplePath = base_path('.env.example');

        if (! File::exists($envPath)) {
            if (! File::exists($envExamplePath)) {
                throw new \RuntimeException('فایل .env.example یافت نشد.');
            }

            File::copy($envExamplePath, $envPath);
            $report[] = ['step' => 'env-create', 'ok' => true, 'message' => 'فایل .env از روی .env.example ایجاد شد.'];
            return null;
        }

        $backupPath = base_path('.env.backup.' . now()->format('Ymd_His'));
        File::copy($envPath, $backupPath);
        $report[] = ['step' => 'env-backup', 'ok' => true, 'message' => 'از فایل .env نسخه پشتیبان تهیه شد: ' . basename($backupPath)];

        return $backupPath;
    }

    private function writeEnvValues(array $db, array $store, array &$report): void
    {
        $envPath = base_path('.env');
        $content = File::get($envPath);

        $pairs = [
            'APP_NAME' => '"' . addslashes($store['store_name']) . '"',
            'APP_ENV' => 'production',
            'APP_DEBUG' => 'false',
            'APP_URL' => $db['app_url'],
            'APP_LOCALE' => $store['default_locale'],
            'APP_FALLBACK_LOCALE' => $store['default_locale'],
            'APP_TIMEZONE' => $store['timezone'],
            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => $db['db_host'],
            'DB_PORT' => (string) $db['db_port'],
            'DB_DATABASE' => $db['db_database'],
            'DB_USERNAME' => $db['db_username'],
            'DB_PASSWORD' => (string) ($db['db_password'] ?? ''),
        ];

        foreach ($pairs as $key => $value) {
            $pattern = '/^' . preg_quote($key, '/') . '=.*$/m';
            $line = $key . '=' . $value;

            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, $line, $content);
            } else {
                $content .= PHP_EOL . $line;
            }
        }

        File::put($envPath, $content);
        $report[] = ['step' => 'env-write', 'ok' => true, 'message' => 'تنظیمات اصلی در .env اعمال شد.'];
    }

    private function applyStoreSettings(array $store, array &$report): void
    {
        if (! Schema::hasTable('settings')) {
            $report[] = [
                'step' => 'store-settings',
                'ok' => false,
                'message' => 'جدول settings موجود نیست؛ ذخیره تنظیمات فروشگاه انجام نشد.',
            ];

            return;
        }

        Setting::updateOrCreate(['key' => 'store_name'], ['value' => $store['store_name']]);
        Setting::updateOrCreate(['key' => 'default_locale'], ['value' => $store['default_locale']]);
        Setting::updateOrCreate(['key' => 'currency_label'], ['value' => $store['currency_label']]);
        Setting::updateOrCreate(['key' => 'store_timezone'], ['value' => $store['timezone']]);

        if (! empty($store['store_logo_path'])) {
            Setting::updateOrCreate(['key' => 'store_logo'], ['value' => $store['store_logo_path']]);
        }

        $report[] = [
            'step' => 'store-settings',
            'ok' => true,
            'message' => 'تنظیمات فروشگاه ذخیره شد.',
        ];
    }

    private function determineResumeUrl(): string
    {
        $db = session('installer.db');
        $result = session('installer.result');
        $store = session('installer.store');
        $requirementsPassed = (bool) session('installer.requirements_passed', false);

        if (is_array($db) && is_array($store) && (($result['ok'] ?? false) === true)) {
            return route('install.summary');
        }

        if (is_array($db) && (($result['ok'] ?? false) === true)) {
            return route('install.store-settings');
        }

        if ($requirementsPassed || is_array($db)) {
            return route('install.database');
        }

        return route('install.requirements');
    }

    private function runArtisan(string $command, array &$report, bool $allowFailure = false): void
    {
        $exitCode = Artisan::call($command);
        $output = trim(Artisan::output());

        $ok = $exitCode === 0 || $allowFailure;
        $report[] = [
            'step' => 'artisan:' . $command,
            'ok' => $ok,
            'message' => $ok ? 'اجرا شد.' : 'ناموفق.',
            'output' => $output,
        ];

        if (! $ok) {
            throw new \RuntimeException('خطا در اجرای دستور: ' . $command . PHP_EOL . $output);
        }
    }

    private function createAdminUser(array $db, array &$report): void
    {
        $adminRole = Role::query()->firstOrCreate(
            ['name' => 'admin'],
            ['label' => 'مدیر کل']
        );

        $admin = User::query()->updateOrCreate(
            ['email' => $db['admin_email']],
            [
                'name' => $db['admin_name'],
                'mobile' => $db['admin_mobile'],
                'password' => Hash::make($db['admin_password']),
                'status' => 1,
            ]
        );

        $admin->roles()->syncWithoutDetaching([$adminRole->id]);

        $report[] = [
            'step' => 'admin-create',
            'ok' => true,
            'message' => 'کاربر مدیر با ایمیل ' . $db['admin_email'] . ' ایجاد/به روز شد.',
        ];
    }

    private function restoreEnvBackup(?string $backupPath, array &$report): void
    {
        if (! $backupPath || ! File::exists($backupPath)) {
            return;
        }

        File::copy($backupPath, base_path('.env'));
        $report[] = [
            'step' => 'env-rollback',
            'ok' => true,
            'message' => 'به دلیل خطا، فایل .env از نسخه پشتیبان بازیابی شد.',
        ];
    }
}
