<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\RestaurantTable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MobileOrderController extends Controller
{
    public function tables(Request $request): JsonResponse
    {
        if ($blocked = $this->rejectInvalidToken($request)) {
            return $blocked;
        }

        $tables = RestaurantTable::with(['orders' => fn ($query) => $query
            ->whereIn('status', ['pending', 'serving'])
            ->latest()
            ->limit(1)])
            ->orderBy('area')
            ->orderBy('table_code')
            ->get()
            ->map(fn (RestaurantTable $table) => [
                'id' => $table->id,
                'code' => $table->table_code,
                'name' => $table->table_name,
                'area' => $table->area,
                'seats' => $table->seats,
                'status' => $table->status,
                'active_order' => $table->orders->first()?->only(['id', 'order_code', 'status', 'total_amount']),
            ]);

        return response()->json(['data' => $tables]);
    }

    public function openTable(Request $request, RestaurantTable $table): JsonResponse
    {
        if ($blocked = $this->rejectInvalidToken($request)) {
            return $blocked;
        }

        $updated = DB::transaction(function () use ($table): RestaurantTable {
            $lockedTable = RestaurantTable::whereKey($table->id)->lockForUpdate()->firstOrFail();

            abort_if($lockedTable->status === 'bảo trì', 409, 'Bàn đang bảo trì, không thể mở bàn.');
            abort_if($lockedTable->status === 'đã đặt', 409, 'Bàn đã được đặt trước, vui lòng kiểm tra lịch đặt bàn.');

            if ($lockedTable->status === 'trống') {
                $lockedTable->update(['status' => 'đang phục vụ']);
            }

            return $lockedTable->fresh();
        });

        return response()->json([
            'message' => 'Đã mở bàn trên thiết bị di động.',
            'data' => [
                'id' => $updated->id,
                'code' => $updated->table_code,
                'name' => $updated->table_name,
                'area' => $updated->area,
                'seats' => $updated->seats,
                'status' => $updated->status,
            ],
        ]);
    }

    public function products(Request $request): JsonResponse
    {
        if ($blocked = $this->rejectInvalidToken($request)) {
            return $blocked;
        }

        $products = Product::with('category:id,name')
            ->where('status', 'available')
            ->orderBy('name')
            ->get(['id', 'category_id', 'name', 'price', 'description', 'image', 'status'])
            ->map(fn (Product $product) => [
                'id' => $product->id,
                'category' => $product->category?->name,
                'name' => $product->name,
                'description' => $product->description,
                'price' => (float) $product->price,
                'image_url' => $product->image_url,
                'status' => $product->status,
            ]);

        return response()->json(['data' => $products]);
    }

    public function storeOrder(Request $request): JsonResponse
    {
        if ($blocked = $this->rejectInvalidToken($request)) {
            return $blocked;
        }

        $data = $request->validate([
            'table_id' => ['required', 'integer', 'exists:tables,id'],
            'employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', Rule::exists('products', 'id')->where('status', 'available')],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:50'],
            'items.*.note' => ['nullable', 'string', 'max:255'],
        ]);

        $order = DB::transaction(function () use ($data): Order {
            $table = RestaurantTable::whereKey($data['table_id'])->lockForUpdate()->firstOrFail();

            abort_if($table->status === 'bảo trì', 409, 'Bàn đang bảo trì, không thể gửi order.');

            $productIds = collect($data['items'])->pluck('product_id')->unique()->values();
            $products = Product::whereIn('id', $productIds)->where('status', 'available')->get()->keyBy('id');

            abort_if($products->count() !== $productIds->count(), 422, 'Một số món đã tạm hết hoặc không tồn tại.');

            $subtotal = collect($data['items'])->sum(function (array $item) use ($products): float {
                return (float) $products[$item['product_id']]->price * (int) $item['quantity'];
            });

            $order = Order::create([
                'table_id' => $table->id,
                'employee_id' => $data['employee_id'] ?? null,
                'order_code' => 'OD'.now()->format('YmdHis').Str::upper(Str::random(4)),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'discount' => 0,
                'service_fee' => 0,
                'vat' => 0,
                'total_amount' => $subtotal,
                'ordered_at' => now(),
            ]);

            foreach ($data['items'] as $item) {
                $product = $products[$item['product_id']];
                $quantity = (int) $item['quantity'];

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'total_price' => (float) $product->price * $quantity,
                    'note' => $item['note'] ?? null,
                ]);
            }

            $table->update(['status' => 'đang phục vụ']);

            return $order->load(['table', 'employee.user', 'items.product']);
        });

        return response()->json([
            'message' => 'Order đã được gửi lên Bếp và Thu ngân.',
            'data' => $this->orderResource($order),
        ], 201);
    }

    private function orderResource(Order $order): array
    {
        return [
            'id' => $order->id,
            'order_code' => $order->order_code,
            'status' => $order->status,
            'table' => [
                'id' => $order->table?->id,
                'name' => $order->table?->table_name,
                'area' => $order->table?->area,
            ],
            'employee' => $order->employee?->user?->name ?? $order->employee?->user?->full_name,
            'subtotal' => (float) $order->subtotal,
            'total_amount' => (float) $order->total_amount,
            'ordered_at' => optional($order->ordered_at)->toDateTimeString(),
            'items' => $order->items->map(fn ($item) => [
                'product_id' => $item->product_id,
                'name' => $item->product?->name,
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'total_price' => (float) $item->total_price,
                'note' => $item->note,
            ])->values(),
        ];
    }

    private function rejectInvalidToken(Request $request): ?JsonResponse
    {
        $token = env('MOBILE_API_TOKEN');

        if (! $token) {
            return null;
        }

        $incoming = $request->bearerToken() ?: $request->header('X-Mobile-Token');

        return hash_equals($token, (string) $incoming)
            ? null
            : response()->json(['message' => 'Mobile API token không hợp lệ.'], 401);
    }
}
