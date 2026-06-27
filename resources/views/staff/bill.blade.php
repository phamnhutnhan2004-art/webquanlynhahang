@extends('layouts.app')

@section('title', 'Hóa đơn '.$bill->bill_code)

@php
    $order = $bill->order;
    $totalItems = $order?->items?->sum('quantity') ?? 0;
    $customerName = $bill->customer?->full_name ?? 'Khách lẻ';
    $tableName = $bill->table?->table_name ?? $order?->table?->table_name ?? 'Khách mang đi';
    $staffName = $order?->employee?->user?->full_name ?? $order?->employee?->user?->name ?? 'Chưa phân công';
    $cashierName = $bill->cashier?->user?->full_name ?? $bill->cashier?->user?->name ?? 'Thu ngân hệ thống';
    $backRoute = request()->routeIs('customer.*') ? route('customer.dashboard') : route('staff.cashier');
    $backLabel = request()->routeIs('customer.*') ? 'Quay lại tài khoản' : 'Quay lại Thu ngân';
@endphp

@section('content')
<style>
    .bill-shell {
        max-width: 920px;
        margin: 0 auto 3rem;
    }

    .bill-print {
        background: #fff;
        border: 1px solid rgba(90, 52, 30, .16);
        border-radius: 8px;
        box-shadow: 0 18px 45px rgba(44, 27, 18, .08);
        padding: clamp(1.25rem, 4vw, 2.5rem);
    }

    .bill-brand {
        text-align: center;
        border-bottom: 2px solid rgba(217, 164, 65, .45);
        padding-bottom: 1rem;
        margin-bottom: 1.5rem;
    }

    .bill-brand h1 {
        color: var(--green);
        font-weight: 900;
        text-transform: uppercase;
    }

    .bill-meta {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .75rem 1.25rem;
    }

    .bill-meta-item {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        border-bottom: 1px dashed rgba(90, 52, 30, .16);
        padding-bottom: .4rem;
    }

    .bill-total-row {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        padding: .35rem 0;
    }

    .bill-grand-total {
        border-top: 2px solid rgba(217, 164, 65, .45);
        color: var(--green);
        font-size: 1.25rem;
        font-weight: 900;
        margin-top: .5rem;
        padding-top: .75rem;
    }

    .bill-thanks {
        border-top: 1px solid rgba(90, 52, 30, .16);
        color: var(--wood-dark);
        font-weight: 800;
        margin-top: 1.5rem;
        padding-top: 1rem;
        text-align: center;
    }

    @media (max-width: 575.98px) {
        .bill-meta {
            grid-template-columns: 1fr;
        }
    }

    @media print {
        body {
            background: #fff !important;
        }

        .navbar,
        .page-loader,
        .chatbot-panel,
        .chatbot-launcher,
        .bill-actions,
        main > .container:first-child {
            display: none !important;
        }

        main.container-fluid,
        .container,
        .bill-shell {
            width: 100% !important;
            max-width: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .bill-print {
            border: 0 !important;
            box-shadow: none !important;
            padding: 0 !important;
        }
    }
</style>

<div class="container bill-shell">
    <div class="bill-actions d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <a class="btn btn-outline-primary" href="{{ $backRoute }}">
            <i class="bi bi-arrow-left" aria-hidden="true"></i> {{ $backLabel }}
        </a>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-primary" type="button" onclick="window.print()">
                <i class="bi bi-printer" aria-hidden="true"></i> In hóa đơn / Xuất PDF
            </button>
            <a class="btn btn-outline-primary" href="{{ route('staff.bills.download', $bill) }}">
                <i class="bi bi-download" aria-hidden="true"></i> Tải xuống hóa đơn
            </a>
        </div>
    </div>

    <article class="bill-print">
        <header class="bill-brand">
            <div class="eyebrow">Hóa đơn thanh toán</div>
            <h1 class="h3 mb-1">Nhà hàng Hoa Sen</h1>
            <div class="text-muted">Cảm ơn quý khách đã tin tưởng và sử dụng dịch vụ</div>
        </header>

        <section class="bill-meta mb-4">
            <div class="bill-meta-item"><span>Mã hóa đơn</span><strong>{{ $bill->bill_code }}</strong></div>
            <div class="bill-meta-item"><span>Mã đơn hàng</span><strong>{{ $order?->order_code ?? 'Không rõ' }}</strong></div>
            <div class="bill-meta-item"><span>Ngày giờ thanh toán</span><strong>{{ optional($bill->paid_at)->format('d/m/Y H:i') }}</strong></div>
            <div class="bill-meta-item"><span>Phương thức</span><strong>{{ $bill->paymentMethodLabel() }}</strong></div>
            <div class="bill-meta-item"><span>Tên khách hàng</span><strong>{{ $customerName }}</strong></div>
            <div class="bill-meta-item"><span>Số bàn</span><strong>{{ $tableName }}</strong></div>
            <div class="bill-meta-item"><span>Nhân viên phục vụ</span><strong>{{ $staffName }}</strong></div>
            <div class="bill-meta-item"><span>Thu ngân</span><strong>{{ $cashierName }}</strong></div>
        </section>

        <div class="table-responsive mb-4">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Tên món</th>
                        <th class="text-center">Số lượng</th>
                        <th class="text-end">Đơn giá</th>
                        <th class="text-end">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order?->items ?? [] as $item)
                        <tr>
                            <td>{{ $item->product?->name ?? 'Món ăn' }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">{{ number_format((float) $item->unit_price, 0, ',', '.') }} VNĐ</td>
                            <td class="text-end fw-bold">{{ number_format((float) $item->total_price, 0, ',', '.') }} VNĐ</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <section class="ms-auto" style="max-width: 420px;">
            <div class="bill-total-row"><span>Tổng số món</span><strong>{{ $totalItems }}</strong></div>
            <div class="bill-total-row"><span>Tạm tính</span><strong>{{ number_format((float) $bill->subtotal, 0, ',', '.') }} VNĐ</strong></div>
            <div class="bill-total-row"><span>Giảm giá</span><strong>{{ number_format((float) $bill->discount, 0, ',', '.') }} VNĐ</strong></div>
            <div class="bill-total-row"><span>Phí dịch vụ</span><strong>{{ number_format((float) $bill->service_fee, 0, ',', '.') }} VNĐ</strong></div>
            <div class="bill-total-row"><span>Thuế VAT</span><strong>{{ number_format((float) $bill->vat, 0, ',', '.') }} VNĐ</strong></div>
            <div class="bill-total-row bill-grand-total"><span>Tổng tiền cần thanh toán</span><span>{{ number_format((float) $bill->total_amount, 0, ',', '.') }} VNĐ</span></div>
        </section>

        <footer class="bill-thanks">
            Cảm ơn quý khách đã sử dụng dịch vụ tại Nhà hàng Hoa Sen.
        </footer>
    </article>
</div>
@endsection
