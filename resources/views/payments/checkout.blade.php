@extends('layouts.app')

@section('title', 'Thanh toán '.$order->order_code)

@php
    $customerName = $order->customer?->full_name ?? 'Khách lẻ';
    $tableName = $order->table?->table_name ?? 'Khách mang đi';
    $totalItems = $order->items->sum('quantity');
    $methodPayload = $paymentMethods->map(fn ($method) => [
        'id' => $method->id,
        'method_key' => $method->method_key,
        'display_name' => $method->display_name,
        'bank_name' => $method->bank_name,
        'account_holder' => $method->account_holder,
        'account_number' => $method->account_number,
        'transfer_content' => $method->transferContentFor($order),
        'qr_image_url' => $method->qr_image_url,
    ])->values();
@endphp

@section('content')
<div class="container">
    <div class="page-hero mb-4">
        <div class="eyebrow mb-2">Thanh toán đơn hàng</div>
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
            <div>
                <h1 class="display-6 fw-bold mb-2">{{ $order->order_code }}</h1>
                <p class="lead mb-0">Kiểm tra món ăn, chọn phương thức thanh toán và xác nhận khi đã thu tiền.</p>
            </div>
            <a class="btn btn-outline-light" href="{{ $isStaffCheckout ? route('staff.cashier') : route('customer.dashboard') }}">
                <i class="bi bi-arrow-left" aria-hidden="true"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="row g-4">
        <section class="col-xl-7">
            <div class="card h-100">
                <div class="card-body">
                    <div class="section-title">
                        <div>
                            <div class="eyebrow">Thông tin đơn hàng</div>
                            <h2 class="h5 mb-0">Chi tiết thanh toán</h2>
                        </div>
                        <span class="status-badge">{{ $order->status === 'paid' ? 'Đã thanh toán' : 'Chờ thanh toán' }}</span>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="muted-box h-100">
                                <div class="small text-muted">Mã đơn hàng</div>
                                <strong>{{ $order->order_code }}</strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="muted-box h-100">
                                <div class="small text-muted">Khách hàng</div>
                                <strong>{{ $customerName }}</strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="muted-box h-100">
                                <div class="small text-muted">Số bàn</div>
                                <strong>{{ $tableName }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Tên món</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-end">Đơn giá</th>
                                    <th class="text-end">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
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

                    <div class="ms-auto mt-3" style="max-width: 420px;">
                        <div class="d-flex justify-content-between py-1"><span>Tổng số món</span><strong>{{ $totalItems }}</strong></div>
                        <div class="d-flex justify-content-between py-1"><span>Tạm tính</span><strong>{{ number_format((float) $order->subtotal, 0, ',', '.') }} VNĐ</strong></div>
                        <div class="d-flex justify-content-between py-1"><span>Giảm giá</span><strong>{{ number_format((float) $order->discount, 0, ',', '.') }} VNĐ</strong></div>
                        <div class="d-flex justify-content-between py-1"><span>Phí dịch vụ</span><strong>{{ number_format((float) $order->service_fee, 0, ',', '.') }} VNĐ</strong></div>
                        <div class="d-flex justify-content-between py-1"><span>Thuế VAT</span><strong>{{ number_format((float) $order->vat, 0, ',', '.') }} VNĐ</strong></div>
                        <div class="d-flex justify-content-between align-items-center mt-2 pt-3 border-top fs-5">
                            <span class="fw-bold">Tổng tiền cần thanh toán</span>
                            <strong class="gold-text">{{ number_format((float) $order->total_amount, 0, ',', '.') }} VNĐ</strong>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <aside class="col-xl-5">
            <div class="card">
                <div class="card-body">
                    <div class="section-title">
                        <div>
                            <div class="eyebrow">Phương thức</div>
                            <h2 class="h5 mb-0">Chọn cách thanh toán</h2>
                        </div>
                    </div>

                    @if($paymentMethods->isEmpty())
                        <div class="alert alert-warning border-0">Admin chưa bật phương thức thanh toán nào.</div>
                    @else
                        <form method="POST" action="{{ $isStaffCheckout ? route('staff.orders.pay', $order) : '#' }}" id="checkoutForm">
                            @csrf
                            <div class="d-grid gap-2 mb-3">
                                @foreach($paymentMethods as $method)
                                    <label class="muted-box d-flex align-items-center gap-3 mb-0">
                                        <input class="form-check-input mt-0" type="radio" name="{{ $isStaffCheckout ? 'payment_method_id' : 'preview_payment_method_id' }}" value="{{ $method->id }}" data-payment-choice @checked($loop->first)>
                                        <span class="flex-grow-1">
                                            <strong>{{ $method->display_name }}</strong>
                                            <span class="d-block small text-muted">{{ $method->methodLabel() }}</span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>

                            <div class="muted-box mb-3" id="paymentDetail"></div>

                            @if($isStaffCheckout)
                                <div class="d-grid gap-2">
                                    <button class="btn btn-primary" type="submit" name="action" value="pay">
                                        <i class="bi bi-check2-circle" aria-hidden="true"></i> Xác nhận đã thanh toán
                                    </button>
                                    <button class="btn btn-outline-primary" type="submit" name="action" value="pay_print">
                                        <i class="bi bi-printer" aria-hidden="true"></i> Xác nhận và In hóa đơn
                                    </button>
                                </div>
                            @else
                                <div class="alert alert-info border-0 mb-0">
                                    Sau khi chuyển khoản hoặc quét QR, vui lòng báo Thu ngân để xác nhận thanh toán trên hệ thống.
                                </div>
                            @endif
                        </form>
                    @endif
                </div>
            </div>
        </aside>
    </div>
</div>

<script>
    (() => {
        const methods = @json($methodPayload);
        const detail = document.getElementById('paymentDetail');
        const choices = document.querySelectorAll('[data-payment-choice]');

        const render = (method) => {
            if (!detail || !method) {
                return;
            }

            if (method.method_key === 'cash') {
                detail.innerHTML = '<strong>Tiền mặt</strong><p class="mb-0 text-muted">Thu ngân thu tiền trực tiếp tại quầy hoặc tại bàn.</p>';
                return;
            }

            const bankInfo = `
                <div class="d-grid gap-2">
                    <div class="d-flex justify-content-between gap-3"><span>Ngân hàng</span><strong>${method.bank_name || '-'}</strong></div>
                    <div class="d-flex justify-content-between gap-3"><span>Chủ tài khoản</span><strong>${method.account_holder || '-'}</strong></div>
                    <div class="d-flex justify-content-between gap-3"><span>Số tài khoản</span><strong>${method.account_number || '-'}</strong></div>
                    <div class="d-flex justify-content-between gap-3"><span>Nội dung</span><strong>${method.transfer_content || '-'}</strong></div>
                </div>
            `;

            if (method.method_key === 'qr') {
                detail.innerHTML = `
                    ${method.qr_image_url
                        ? `<img class="img-fluid rounded mb-3 border" src="${method.qr_image_url}" alt="Mã QR thanh toán">`
                        : '<div class="alert alert-warning border-0">Admin chưa tải ảnh QR cho tài khoản này.</div>'}
                    ${bankInfo}
                `;
                return;
            }

            if (method.method_key === 'e_wallet') {
                detail.innerHTML = `<strong>Ví điện tử</strong>${bankInfo}`;
                return;
            }

            detail.innerHTML = bankInfo;
        };

        choices.forEach((choice) => {
            choice.addEventListener('change', () => {
                render(methods.find((method) => Number(method.id) === Number(choice.value)));
            });
        });

        render(methods[0]);
    })();
</script>
@endsection
