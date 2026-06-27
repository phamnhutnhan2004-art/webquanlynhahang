<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\KitchenOrder;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class LiveOrderController extends Controller
{
    public function kitchen(): View
    {
        return view('staff.live-orders', [
            'mode' => 'kitchen',
            'title' => 'Dashboard đầu bếp',
            'subtitle' => 'Theo dõi món chờ chế biến, món đang làm và món đã hoàn thành để nhân viên phục vụ đúng lúc.',
            'kitchenOrders' => KitchenOrder::with(['order.table', 'staff.user', 'chef.user', 'items.food.category'])
                ->whereIn('status', ['pending', 'cooking', 'completed'])
                ->latest()
                ->get(),
            'statusCounts' => KitchenOrder::query()
                ->selectRaw('status, COUNT(*) as total')
                ->whereIn('status', ['pending', 'cooking', 'completed'])
                ->groupBy('status')
                ->pluck('total', 'status'),
        ]);
    }

    public function cashier(): View
    {
        return view('staff.live-orders', [
            'mode' => 'cashier',
            'title' => 'Màn hình Thu ngân',
            'subtitle' => 'Theo dõi order đang phục vụ, tổng tiền và bàn cần xử lý.',
            'initialOrders' => $this->orders('cashier'),
            'cashierStats' => $this->cashierStats(),
            'recentBills' => Bill::with(['order.table', 'customer', 'cashier.user'])
                ->latest('paid_at')
                ->limit(8)
                ->get(),
        ]);
    }

    public function stream(Request $request): Response
    {
        $mode = $request->query('mode', 'kitchen');
        $payload = fn () => [
            'generated_at' => now()->toDateTimeString(),
            'orders' => $this->orders($mode),
        ];

        if ($request->expectsJson() || $request->query('transport') === 'json') {
            return response()->json($payload());
        }

        return response()->stream(function () use ($payload): void {
            @ini_set('zlib.output_compression', '0');
            @ini_set('implicit_flush', '1');
            @set_time_limit(0);

            $lastHash = null;
            $startedAt = now();

            while (! connection_aborted() && $startedAt->diffInSeconds(now()) < 120) {
                $json = json_encode($payload(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                $hash = md5((string) $json);

                if ($hash !== $lastHash) {
                    echo "event: orders\n";
                    echo 'data: '.$json."\n\n";
                    $lastHash = $hash;

                    if (ob_get_level() > 0) {
                        ob_flush();
                    }

                    flush();
                }

                sleep(2);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache, no-transform',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    private function orders(string $mode): array
    {
        if ($mode === 'kitchen') {
            return KitchenOrder::with(['order.table', 'staff.user', 'chef.user', 'items.food'])
                ->whereIn('status', ['pending', 'cooking', 'completed'])
                ->latest()
                ->limit(30)
                ->get()
                ->map(fn (KitchenOrder $kitchenOrder) => [
                    'id' => $kitchenOrder->id,
                    'order_code' => $kitchenOrder->order?->order_code,
                    'status' => $kitchenOrder->status,
                    'status_label' => $this->kitchenStatusLabel($kitchenOrder->status),
                    'table' => $kitchenOrder->order?->table?->table_name,
                    'area' => $kitchenOrder->order?->table?->area,
                    'employee' => $kitchenOrder->staff?->user?->name ?? $kitchenOrder->staff?->user?->full_name,
                    'total_amount' => (float) ($kitchenOrder->order?->total_amount ?? 0),
                    'ordered_at' => optional($kitchenOrder->created_at)->format('d/m/Y H:i'),
                    'items' => $kitchenOrder->items->map(fn ($item) => [
                        'name' => $item->food?->name,
                        'quantity' => $item->quantity,
                        'note' => $this->kitchenStatusLabel($item->status),
                        'total_price' => 0,
                    ])->values(),
                ])
                ->values()
                ->all();
        }

        $statuses = $mode === 'cashier'
            ? ['pending', 'serving', 'completed']
            : ['pending', 'serving'];

        return Order::with(['table', 'employee.user', 'items.product', 'bill'])
            ->whereIn('status', $statuses)
            ->when($mode === 'cashier', fn ($query) => $query->whereDoesntHave('bill'))
            ->latest('ordered_at')
            ->limit(30)
            ->get()
            ->map(fn (Order $order) => [
                'id' => $order->id,
                'order_code' => $order->order_code,
                'status' => $order->status,
                'status_label' => $this->statusLabel($order->status),
                'table' => $order->table?->table_name,
                'area' => $order->table?->area,
                'employee' => $order->employee?->user?->name ?? $order->employee?->user?->full_name,
                'total_amount' => (float) $order->total_amount,
                'ordered_at' => optional($order->ordered_at)->format('d/m/Y H:i'),
                'bill_id' => $order->bill?->id,
                'is_paid' => (bool) $order->bill || $order->status === 'paid',
                'items' => $order->items->map(fn ($item) => [
                    'name' => $item->product?->name,
                    'quantity' => $item->quantity,
                    'note' => $item->note,
                    'total_price' => (float) $item->total_price,
                ])->values(),
            ])
            ->values()
            ->all();
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            'pending' => 'Chờ xử lý',
            'serving' => 'Đang phục vụ',
            'completed' => 'Hoàn thành',
            'paid' => 'Đã thanh toán',
            'cancelled' => 'Đã hủy',
            default => $status,
        };
    }

    private function cashierStats(): array
    {
        return [
            'today_bills' => Bill::whereDate('paid_at', today())->count(),
            'today_revenue' => (float) Bill::whereDate('paid_at', today())->sum('total_amount'),
            'month_revenue' => (float) Bill::whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('total_amount'),
            'paid_orders' => Bill::count(),
            'unpaid_orders' => Order::whereIn('status', ['pending', 'serving', 'completed'])
                ->whereDoesntHave('bill')
                ->count(),
            'completed_orders' => Order::where('status', 'paid')->whereHas('bill')->count(),
        ];
    }

    private function kitchenStatusLabel(string $status): string
    {
        return match ($status) {
            'pending' => 'Chờ chế biến',
            'cooking' => 'Đang chế biến',
            'completed' => 'Đã hoàn thành',
            'served' => 'Đã phục vụ',
            default => $status,
        };
    }
}
