<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalOrders = Order::count();
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total_amount');
        if (!$totalRevenue) {
            $totalRevenue = Order::sum('total_amount');
        }

        $totalCustomers = User::where('role', 'customer')->count();
        $avgOrderValue = $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0;

        $recentOrders = Order::orderBy('created_at', 'desc')->limit(8)->get();
        $lowStockProducts = Product::where('stock', '<=', 10)->orderBy('stock', 'asc')->limit(6)->get();
        $topProducts = Product::where('is_active', true)->orderBy('sales_count', 'desc')->limit(5)->get();

        $categoryCount = Category::count();
        $productCount = Product::count();

        return view('admin.dashboard', compact(
            'totalOrders',
            'totalRevenue',
            'totalCustomers',
            'avgOrderValue',
            'recentOrders',
            'lowStockProducts',
            'topProducts',
            'categoryCount',
            'productCount'
        ));
    }
}
