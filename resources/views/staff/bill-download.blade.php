@php
    $order = $bill->order;
    $totalItems = $order?->items?->sum('quantity') ?? 0;
    $customerName = $bill->customer?->full_name ?? 'Khách lẻ';
    $tableName = $bill->table?->table_name ?? $order?->table?->table_name ?? 'Khách mang đi';
    $staffName = $order?->employee?->user?->full_name ?? $order?->employee?->user?->name ?? 'Chưa phân công';
    $cashierName = $bill->cashier?->user?->full_name ?? $bill->cashier?->user?->name ?? 'Thu ngân hệ thống';
@endphp
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Hóa đơn {{ $bill->bill_code }}</title>
    <style>
        body {
            color: #221812;
            font-family: Arial, sans-serif;
            margin: 32px;
        }

        h1, h2 {
            color: #0e3b32;
            margin: 0;
            text-align: center;
            text-transform: uppercase;
        }

        .muted {
            color: #756a5e;
            text-align: center;
        }

        .meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px 24px;
            margin: 28px 0;
        }

        .meta div,
        .total-row {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px dashed #d7c6aa;
            padding: 6px 0;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border-bottom: 1px solid #eadfcd;
            padding: 10px;
        }

        th {
            background: #f6efe0;
            text-align: left;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .totals {
            margin-left: auto;
            margin-top: 24px;
            max-width: 420px;
        }

        .grand {
            color: #0e3b32;
            font-size: 18px;
            font-weight: 700;
        }

        .thanks {
            border-top: 1px solid #eadfcd;
            font-weight: 700;
            margin-top: 32px;
            padding-top: 16px;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Nhà hàng Hoa Sen</h1>
    <p class="muted">Hóa đơn thanh toán</p>

    <section class="meta">
        <div><span>Mã hóa đơn</span><strong>{{ $bill->bill_code }}</strong></div>
        <div><span>Mã đơn hàng</span><strong>{{ $order?->order_code ?? 'Không rõ' }}</strong></div>
        <div><span>Ngày giờ thanh toán</span><strong>{{ optional($bill->paid_at)->format('d/m/Y H:i') }}</strong></div>
        <div><span>Phương thức</span><strong>{{ $bill->paymentMethodLabel() }}</strong></div>
        <div><span>Tên khách hàng</span><strong>{{ $customerName }}</strong></div>
        <div><span>Số bàn</span><strong>{{ $tableName }}</strong></div>
        <div><span>Nhân viên phục vụ</span><strong>{{ $staffName }}</strong></div>
        <div><span>Thu ngân</span><strong>{{ $cashierName }}</strong></div>
    </section>

    <table>
        <thead>
            <tr>
                <th>Tên món</th>
                <th class="center">Số lượng</th>
                <th class="right">Đơn giá</th>
                <th class="right">Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order?->items ?? [] as $item)
                <tr>
                    <td>{{ $item->product?->name ?? 'Món ăn' }}</td>
                    <td class="center">{{ $item->quantity }}</td>
                    <td class="right">{{ number_format((float) $item->unit_price, 0, ',', '.') }} VNĐ</td>
                    <td class="right">{{ number_format((float) $item->total_price, 0, ',', '.') }} VNĐ</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <section class="totals">
        <div class="total-row"><span>Tổng số món</span><strong>{{ $totalItems }}</strong></div>
        <div class="total-row"><span>Tạm tính</span><strong>{{ number_format((float) $bill->subtotal, 0, ',', '.') }} VNĐ</strong></div>
        <div class="total-row"><span>Giảm giá</span><strong>{{ number_format((float) $bill->discount, 0, ',', '.') }} VNĐ</strong></div>
        <div class="total-row"><span>Phí dịch vụ</span><strong>{{ number_format((float) $bill->service_fee, 0, ',', '.') }} VNĐ</strong></div>
        <div class="total-row"><span>Thuế VAT</span><strong>{{ number_format((float) $bill->vat, 0, ',', '.') }} VNĐ</strong></div>
        <div class="total-row grand"><span>Tổng tiền cần thanh toán</span><span>{{ number_format((float) $bill->total_amount, 0, ',', '.') }} VNĐ</span></div>
    </section>

    <p class="thanks">Cảm ơn quý khách đã sử dụng dịch vụ tại Nhà hàng Hoa Sen.</p>
</body>
</html>
