<?php

namespace App\Http\Controllers;

use App\Models\KitchenOrder;
use App\Models\KitchenOrderItem;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class KitchenOrderController extends Controller
{
    public function send(Request $request, Order $order): RedirectResponse
    {
        if (! $order->items()->exists()) {
            return back()->with('error', 'Đơn hàng chưa có món ăn để gửi xuống bếp.');
        }

        $employeeId = $request->user()?->employee?->id;

        DB::transaction(function () use ($order, $employeeId): void {
            $order->loadMissing(['items.product', 'table']);

            $kitchenOrder = KitchenOrder::firstOrCreate(
                ['order_id' => $order->id],
                ['staff_id' => $employeeId, 'status' => 'pending']
            );

            foreach ($order->items as $item) {
                KitchenOrderItem::updateOrCreate(
                    [
                        'kitchen_order_id' => $kitchenOrder->id,
                        'food_id' => $item->product_id,
                    ],
                    [
                        'quantity' => $item->quantity,
                        'status' => 'pending',
                    ]
                );
            }

            $order->update([
                'employee_id' => $employeeId ?? $order->employee_id,
                'status' => 'pending',
            ]);

            $order->table?->update(['status' => 'đang sử dụng']);
            $kitchenOrder->refreshStatusFromItems();
        });

        return back()->with('status', 'Đã gửi đơn hàng xuống bếp.');
    }

    public function updateItemStatus(Request $request, KitchenOrderItem $item): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['pending', 'cooking', 'completed'])],
        ]);

        DB::transaction(function () use ($item, $data, $request): void {
            $item->update(['status' => $data['status']]);

            $kitchenOrder = $item->kitchenOrder()->with('items')->firstOrFail();
            $kitchenOrder->update(['chef_id' => $request->user()?->employee?->id ?? $kitchenOrder->chef_id]);
            $kitchenOrder->refreshStatusFromItems();
        });

        return back()->with('status', 'Đã cập nhật trạng thái món ăn.');
    }

    public function serve(Request $request, KitchenOrder $kitchenOrder): RedirectResponse
    {
        if ($kitchenOrder->items()->where('status', '!=', 'completed')->exists()) {
            return back()->with('error', 'Chỉ có thể xác nhận phục vụ khi toàn bộ món đã hoàn thành.');
        }

        DB::transaction(function () use ($kitchenOrder, $request): void {
            $kitchenOrder->loadMissing(['items', 'order.table']);

            $kitchenOrder->items()->update(['status' => 'served']);
            $kitchenOrder->update([
                'staff_id' => $request->user()?->employee?->id ?? $kitchenOrder->staff_id,
                'status' => 'served',
            ]);

            $kitchenOrder->order?->update(['status' => 'serving']);
            $kitchenOrder->order?->table?->update(['status' => 'đang sử dụng']);
        });

        return back()->with('status', 'Đã xác nhận nhân viên phục vụ món cho khách.');
    }

    public function notifications(): JsonResponse
    {
        $orders = KitchenOrder::with(['order.table', 'items.food'])
            ->where('status', 'completed')
            ->latest('updated_at')
            ->limit(10)
            ->get()
            ->map(fn (KitchenOrder $kitchenOrder) => [
                'id' => $kitchenOrder->id,
                'order_code' => $kitchenOrder->order?->order_code,
                'table' => $kitchenOrder->order?->table?->table_name,
                'message' => $kitchenOrder->items->sum('quantity').' món ăn đã hoàn thành, vui lòng mang lên cho khách.',
            ])
            ->values();

        return response()->json(['data' => $orders]);
    }
}
