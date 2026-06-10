<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ChatbotLog;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\RestaurantTable;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DatabaseOverviewController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'message' => 'Cơ sở dữ liệu hệ thống quản lý nhà hàng đã sẵn sàng.',
            'record_counts' => [
                'roles' => Role::count(),
                'users' => User::count(),
                'customers' => Customer::count(),
                'employees' => Employee::count(),
                'tables' => RestaurantTable::count(),
                'categories' => Category::count(),
                'products' => Product::count(),
                'reservations' => Reservation::count(),
                'orders' => Order::count(),
                'order_items' => OrderItem::count(),
                'payments' => Payment::count(),
                'chatbot_logs' => ChatbotLog::count(),
            ],
            'categories' => Category::query()
                ->select('id', 'name', 'description', 'status')
                ->withCount('products')
                ->get(),
            'sample_products' => Product::query()
                ->select('id', 'category_id', 'name', 'price', 'status')
                ->with('category:id,name')
                ->limit(5)
                ->get(),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
