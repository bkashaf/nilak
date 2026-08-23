<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(): View
    {
        $stats = [
            'users' => User::count(),
            'products' => Product::count(),
            'orders' => Order::count(),
            'sales' => Order::whereIn('status', ['paid', 'shipped', 'delivered'])->sum('total_amount'),
            'pending_payments' => Payment::whereIn('status', ['pending', 'initiated', 'pending_review'])->count(),
        ];

        $recentOrders = Order::with('user')->latest()->limit(10)->get();

        return view('themes.admin.reports.index', compact('stats', 'recentOrders'));
    }
}
