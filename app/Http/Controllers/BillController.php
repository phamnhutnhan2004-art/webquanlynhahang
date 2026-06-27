<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Order;
use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class BillController extends Controller
{
    public function checkout(Request $request, Order $order): View|RedirectResponse
    {
        $this->authorizeOrderAccess($request, $order);

        $order = $order->load(['bill', 'customer', 'table', 'employee.user', 'items.product']);

        if ($order->bill) {
            return redirect()
                ->route($request->routeIs('customer.*') ? 'customer.bills.show' : 'staff.bills.show', $order->bill)
                ->with('status', 'Đơn hàng này đã được thanh toán.');
        }

        return view('payments.checkout', [
            'order' => $order,
            'paymentMethods' => PaymentMethod::where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(),
            'isStaffCheckout' => $request->routeIs('staff.*'),
        ]);
    }

    public function store(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'action' => ['nullable', 'in:pay,pay_print'],
        ], [
            'payment_method_id.required' => 'Vui lòng chọn phương thức thanh toán.',
            'payment_method_id.exists' => 'Phương thức thanh toán không hợp lệ hoặc đã bị tắt.',
        ]);

        try {
            $bill = DB::transaction(function () use ($request, $order, $data): Bill {
                $paymentMethod = PaymentMethod::whereKey($data['payment_method_id'])
                    ->where('is_active', true)
                    ->lockForUpdate()
                    ->first();

                if (! $paymentMethod) {
                    throw new \RuntimeException('Phương thức thanh toán này đang bị tắt.');
                }

                $lockedOrder = Order::query()
                    ->with(['bill', 'items.product', 'table', 'customer'])
                    ->whereKey($order->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($lockedOrder->bill || $lockedOrder->status === 'paid') {
                    throw new \RuntimeException('Đơn hàng này đã được thanh toán, không thể thanh toán lại.');
                }

                if ($lockedOrder->items->isEmpty()) {
                    throw new \RuntimeException('Đơn hàng chưa có món ăn nên chưa thể thanh toán.');
                }

                $cashierId = $request->user()?->employee?->id;

                $bill = Bill::create([
                    'order_id' => $lockedOrder->id,
                    'cashier_id' => $cashierId,
                    'customer_id' => $lockedOrder->customer_id,
                    'table_id' => $lockedOrder->table_id,
                    'payment_method_id' => $paymentMethod->id,
                    'bill_code' => $this->makeBillCode(),
                    'payment_method' => $paymentMethod->method_key,
                    'subtotal' => $lockedOrder->subtotal,
                    'discount' => $lockedOrder->discount,
                    'service_fee' => $lockedOrder->service_fee,
                    'vat' => $lockedOrder->vat,
                    'total_amount' => $lockedOrder->total_amount,
                    'paid_at' => now(),
                    'status' => 'paid',
                ]);

                $lockedOrder->update(['status' => 'paid']);
                $lockedOrder->table?->update(['status' => 'trống']);

                return $bill;
            });
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        if (($data['action'] ?? 'pay') === 'pay_print') {
            return redirect()
                ->route('staff.bills.show', $bill)
                ->with('status', 'Đã thanh toán thành công. Bạn có thể in hóa đơn cho khách.');
        }

        return redirect()
            ->route('staff.cashier')
            ->with('status', 'Đã cập nhật thanh toán thành công.');
    }

    public function show(Request $request, Bill $bill): View
    {
        $this->authorizeBillAccess($request, $bill);

        return view('staff.bill', [
            'bill' => $this->loadBill($bill),
        ]);
    }

    public function download(Request $request, Bill $bill): Response
    {
        $this->authorizeBillAccess($request, $bill);

        $bill = $this->loadBill($bill);
        $fileName = 'hoa-don-'.$bill->bill_code.'.html';

        return response()
            ->view('staff.bill-download', ['bill' => $bill])
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="'.$fileName.'"');
    }

    private function loadBill(Bill $bill): Bill
    {
        return $bill->load([
            'order.items.product',
            'order.employee.user',
            'cashier.user',
            'customer',
            'table',
            'paymentMethod',
        ]);
    }

    private function authorizeOrderAccess(Request $request, Order $order): void
    {
        if ($request->routeIs('staff.*')) {
            return;
        }

        $customerId = $request->user()?->customer?->id;

        abort_unless($customerId && (int) $order->customer_id === (int) $customerId, 403);
    }

    private function authorizeBillAccess(Request $request, Bill $bill): void
    {
        if ($request->routeIs('staff.*')) {
            return;
        }

        $customerId = $request->user()?->customer?->id;

        abort_unless($customerId && (int) $bill->customer_id === (int) $customerId, 403);
    }

    private function makeBillCode(): string
    {
        do {
            $code = 'HD'.now()->format('YmdHis').Str::upper(Str::random(4));
        } while (Bill::where('bill_code', $code)->exists());

        return $code;
    }
}
