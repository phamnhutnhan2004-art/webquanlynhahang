<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\RestaurantTable;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function home(): View
    {
        return view('home', [
            'products' => Product::with('category:id,name')->latest()->limit(6)->get(),
            'categories' => Category::withCount('products')->get(),
        ]);
    }

    public function admin(): View
    {
        return view('admin.dashboard', [
            'totalProducts' => Product::count(),
            'totalOrders' => Order::count(),
            'totalRevenue' => Payment::sum('total_amount'),
            'activeTables' => RestaurantTable::whereNotIn('status', ['trống', 'trong'])->count(),
            'recentOrders' => Order::latest()->limit(5)->get(),
        ]);
    }

    public function adminSection(string $section): View
    {
        abort_unless(in_array($section, ['employees', 'products', 'categories', 'tables', 'orders', 'stats'], true), 404);

        $data = match ($section) {
            'employees' => ['items' => Employee::with('user')->get()],
            'products' => ['items' => Product::with('category')->get()],
            'categories' => ['items' => Category::withCount('products')->get()],
            'tables' => ['items' => RestaurantTable::all()],
            'orders' => ['items' => Order::with(['customer', 'table', 'employee', 'items.product'])->get()],
            'stats' => [
                'revenueByDay' => Payment::query()
                    ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as revenue'))
                    ->groupBy(DB::raw('DATE(created_at)'))
                    ->orderBy('date')
                    ->get(),
            ],
        };

        return view('admin.section', ['section' => $section] + $data);
    }

    public function staff(): View
    {
        return view('staff.dashboard', [
            'orders' => Order::latest()->limit(10)->get(),
            'reservations' => Reservation::latest()->limit(10)->get(),
            'customers' => Customer::latest()->limit(10)->get(),
            'products' => Product::with('category')->limit(10)->get(),
        ]);
    }

    public function customer(): View
    {
        return view('customer.dashboard', [
            'products' => Product::with('category')->latest()->get(),
            'reservations' => Reservation::where('customer_id', auth()->user()?->customer?->id)->latest()->get(),
        ]);
    }
}
