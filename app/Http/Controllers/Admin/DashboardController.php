<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * DashboardController constructor
     *
     * اعمال middleware برای احراز هویت و بررسی نقش admin.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * نمایش داشبورد ادمین
     *
     * داده‌های آماری و لیست کاربران اخیر را آماده و به ویو پاس می‌دهد.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        try {
            // تعداد کل کاربران (کش‌شده برای بهبود کارایی)
            $totalUsers = Cache::remember('dashboard.total_users', 60, function () {
                return User::count();
            });

            // تعداد ادمین‌ها (اگر مدل نقش دارید)
            $adminsCount = Cache::remember('dashboard.admins_count', 60, function () {
                // اگر رابطه roles وجود دارد و نام نقش 'admin' است
                if (method_exists(User::class, 'roles')) {
                    return User::whereHas('roles', function ($q) {
                        $q->where('name', 'admin');
                    })->count();
                }
                return 0;
            });

            // کاربران جدید (آخرین 10 کاربر)
            $recentUsers = User::orderBy('created_at', 'desc')
                ->limit(10)
                ->get(['id', 'name', 'email', 'created_at']);

            // توزیع ثبت‌نام در 7 روز گذشته (برای نمودار کوچک)
            $from = Carbon::now()->subDays(6)->startOfDay();
            $usersPerDay = Cache::remember('dashboard.users_per_day', 60, function () use ($from) {
                $days = [];
                for ($i = 0; $i < 7; $i++) {
                    $day = $from->copy()->addDays($i);
                    $days[$day->format('Y-m-d')] = 0;
                }

                $rows = User::selectRaw('DATE(created_at) as day, COUNT(*) as count')
                    ->where('created_at', '>=', $from)
                    ->groupBy('day')
                    ->orderBy('day')
                    ->get();

                foreach ($rows as $row) {
                    $days[$row->day] = (int) $row->count;
                }

                return $days;
            });

            // صفحه‌بندی کاربران برای نمایش جدول (اختیاری)
            $users = User::orderBy('id', 'desc')->paginate(10);

            return view('themes.admin.dashboard.index', compact(
                'totalUsers',
                'adminsCount',
                'recentUsers',
                'usersPerDay',
                'users'
            ));
        } catch (\Throwable $e) {
            // لاگ خطا و نمایش پیام دوستانه به کاربر
            Log::error('DashboardController@index error: '.$e->getMessage(), [
                'exception' => $e,
            ]);

            // بازگرداندن ویو با مقادیر پیش‌فرض تا صفحه خطا ندهد
            return view('themes.admin.dashboard.index', [
                'totalUsers'  => 0,
                'adminsCount' => 0,
                'recentUsers' => collect(),
                'usersPerDay' => [],
                'users'       => User::orderBy('id', 'desc')->paginate(10),
            ]);
        }
    }
}
