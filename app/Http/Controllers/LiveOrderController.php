<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class LiveOrderController extends Controller
{
    public function kitchen(): View
    {
        return view('staff.live-orders', [
            'mode' => 'kitchen',
            'title' => 'Màn hình Bếp',
            'subtitle' => 'Order mới từ thiết bị di động sẽ hiện ngay cùng ghi chú chế biến.',
        ]);
    }

    public function cashier(): View
    {
        return view('staff.live-orders', [
            'mode' => 'cashier',
            'title' => 'Màn hình Thu ngân',
            'subtitle' => 'Theo dõi order đang phục vụ, tổng tiền và bàn cần xử lý.',
        ]);
    }

    public function stream(Request $request): Response
    {
        $mode = $request->query('mode', 'kitchen');

        return response()->stream(function () use ($mode): void {
            @ini_set('zlib.output_compression', '0');
            @ini_set('implicit_flush', '1');
            @set_time_limit(0);

            $lastHash = null;
            $startedAt = now();

            while (! connection_aborted() && $startedAt->diffInSeconds(now()) < 120) {
                $orders = $this->orders($mode);
                $payload = [
                    'generated_at' => now()->toDateTimeString(),
                    'orders' => $orders,
                ];
                $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
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
        $statuses = $mode === 'cashier'
            ? ['pending', 'serving', 'completed']
            : ['pending', 'serving'];

        return Order::with(['table', 'employee.user', 'items.product'])
            ->whereIn('status', $statuses)
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
            'cancelled' => 'Đã hủy',
            default => $status,
        };
    }
}
