@extends('layouts.app')

@section('title', $title)

@php
    $kitchenLabels = [
        'pending' => 'Chờ chế biến',
        'cooking' => 'Đang chế biến',
        'completed' => 'Đã hoàn thành',
        'served' => 'Đã phục vụ',
    ];
    $columns = [
        'pending' => ['title' => 'Danh sách món đang chờ', 'icon' => 'bi-hourglass-split'],
        'cooking' => ['title' => 'Danh sách món đang làm', 'icon' => 'bi-fire'],
        'completed' => ['title' => 'Danh sách món đã hoàn thành', 'icon' => 'bi-check2-circle'],
    ];
    $paymentMethods = [
        'cash' => 'Tiền mặt',
        'bank_transfer' => 'Chuyển khoản',
        'qr' => 'Quét mã QR',
        'e_wallet' => 'Ví điện tử',
    ];
@endphp

@section('content')
<div class="container">
    <div class="page-hero mb-4">
        <div class="eyebrow mb-2">Tối ưu hóa phục vụ</div>
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
            <div>
                <h1 class="display-6 fw-bold mb-2">{{ $title }}</h1>
                <p class="lead mb-0">{{ $subtitle }}</p>
            </div>
            <div class="d-flex gap-2">
                <a class="btn btn-light" href="{{ route('staff.dashboard') }}">Nhân viên</a>
                <a class="btn btn-outline-light" href="{{ route('staff.cashier') }}">Thu ngân</a>
            </div>
        </div>
    </div>

    @if($mode === 'kitchen')
        <section class="row g-3 mb-4">
            @foreach($columns as $status => $column)
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body d-flex align-items-center gap-3">
                            <span class="status-badge"><i class="bi {{ $column['icon'] }}" aria-hidden="true"></i></span>
                            <div>
                                <div class="text-muted">{{ $column['title'] }}</div>
                                <div class="stat-value">{{ (int) ($statusCounts[$status] ?? 0) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </section>

        <section class="row g-4">
            @foreach($columns as $status => $column)
                <div class="col-xl-4">
                    <div class="section-title">
                        <div>
                            <div class="eyebrow">{{ $kitchenLabels[$status] }}</div>
                            <h2 class="h5 mb-0">{{ $column['title'] }}</h2>
                        </div>
                    </div>

                    <div class="d-grid gap-3">
                        @forelse($kitchenOrders->where('status', $status) as $kitchenOrder)
                            <article class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                        <div>
                                            <div class="eyebrow">{{ $kitchenOrder->order?->table?->area ?? 'Khu vực' }}</div>
                                            <h3 class="h5 fw-bold mb-1">{{ $kitchenOrder->order?->order_code }}</h3>
                                            <div class="text-muted">{{ $kitchenOrder->order?->table?->table_name ?? 'Chưa chọn bàn' }}</div>
                                        </div>
                                        <span class="status-badge">{{ $kitchenLabels[$kitchenOrder->status] ?? $kitchenOrder->status }}</span>
                                    </div>

                                    <div class="list-group list-group-flush">
                                        @foreach($kitchenOrder->items as $item)
                                            <div class="list-group-item px-0">
                                                <div class="d-flex justify-content-between align-items-start gap-3">
                                                    <div>
                                                        <strong>{{ $item->quantity }} x {{ $item->food?->name }}</strong>
                                                        <div class="small text-muted">{{ $item->food?->category?->name }}</div>
                                                    </div>
                                                    <span class="status-pill">{{ $kitchenLabels[$item->status] ?? $item->status }}</span>
                                                </div>
                                                @if($item->status !== 'completed')
                                                    <form method="POST" action="{{ route('staff.kitchen-items.update-status', $item) }}" class="d-flex flex-wrap gap-2 mt-2">
                                                        @csrf
                                                        @method('PATCH')
                                                        <select class="form-select form-select-sm" name="status">
                                                            <option value="pending" @selected($item->status === 'pending')>Chờ chế biến</option>
                                                            <option value="cooking" @selected($item->status === 'cooking')>Đang chế biến</option>
                                                            <option value="completed" @selected($item->status === 'completed')>Đã hoàn thành</option>
                                                        </select>
                                                        <button class="btn btn-primary btn-sm" type="submit">Cập nhật</button>
                                                    </form>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="muted-box">Chưa có món ở trạng thái này.</div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </section>
    @else
        <section class="row g-3 mb-4">
            <div class="col-sm-6 col-xl">
                <div class="card h-100 stat-card">
                    <div class="card-body">
                        <div class="text-muted">Hóa đơn hôm nay</div>
                        <div class="stat-value">{{ (int) ($cashierStats['today_bills'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl">
                <div class="card h-100 stat-card">
                    <div class="card-body">
                        <div class="text-muted">Doanh thu hôm nay</div>
                        <div class="stat-value fs-4">{{ number_format((float) ($cashierStats['today_revenue'] ?? 0), 0, ',', '.') }} VNĐ</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl">
                <div class="card h-100 stat-card">
                    <div class="card-body">
                        <div class="text-muted">Doanh thu tháng này</div>
                        <div class="stat-value fs-4">{{ number_format((float) ($cashierStats['month_revenue'] ?? 0), 0, ',', '.') }} VNĐ</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl">
                <div class="card h-100 stat-card">
                    <div class="card-body">
                        <div class="text-muted">Đơn đã thanh toán</div>
                        <div class="stat-value">{{ (int) ($cashierStats['paid_orders'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl">
                <div class="card h-100 stat-card">
                    <div class="card-body">
                        <div class="text-muted">Đơn chưa thanh toán</div>
                        <div class="stat-value">{{ (int) ($cashierStats['unpaid_orders'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl">
                <div class="card h-100 stat-card">
                    <div class="card-body">
                        <div class="text-muted">Đơn đã hoàn tất</div>
                        <div class="stat-value">{{ (int) ($cashierStats['completed_orders'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </section>

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
            <div>
                <h2 class="h4 fw-bold mb-1">Order trực tiếp</h2>
                <p class="text-muted mb-0">Dữ liệu được cập nhật tự động khi nhân viên gửi order từ thiết bị di động.</p>
            </div>
            <span class="status-badge" id="liveStatus">Đang kết nối</span>
        </div>

        <div class="row g-3" id="liveOrders">
            <div class="col-12">
                <div class="muted-box">Đang tải order...</div>
            </div>
        </div>

        <section class="mt-5">
            <div class="section-title">
                <div>
                    <div class="eyebrow">Lịch sử thanh toán</div>
                    <h2 class="h4 mb-0">Hóa đơn gần nhất</h2>
                </div>
            </div>

            <div class="card">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Mã hóa đơn</th>
                                <th>Đơn hàng</th>
                                <th>Bàn</th>
                                <th>Phương thức</th>
                                <th>Tổng tiền</th>
                                <th>Thời gian</th>
                                <th class="text-end">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBills ?? [] as $bill)
                                <tr>
                                    <td class="fw-bold">{{ $bill->bill_code }}</td>
                                    <td>{{ $bill->order?->order_code ?? 'Không rõ' }}</td>
                                    <td>{{ $bill->table?->table_name ?? $bill->order?->table?->table_name ?? 'Khách mang đi' }}</td>
                                    <td>{{ $paymentMethods[$bill->payment_method] ?? $bill->payment_method }}</td>
                                    <td class="fw-bold">{{ number_format((float) $bill->total_amount, 0, ',', '.') }} VNĐ</td>
                                    <td>{{ optional($bill->paid_at)->format('d/m/Y H:i') }}</td>
                                    <td class="text-end">
                                        <div class="d-inline-flex flex-wrap gap-2 justify-content-end">
                                            <a class="btn btn-primary btn-sm" href="{{ route('staff.bills.show', $bill) }}">
                                                <i class="bi bi-printer" aria-hidden="true"></i> In hóa đơn
                                            </a>
                                            <a class="btn btn-outline-primary btn-sm" href="{{ route('staff.bills.download', $bill) }}">
                                                <i class="bi bi-download" aria-hidden="true"></i> Tải xuống
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="muted-box border-0">Chưa có hóa đơn nào.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <template id="orderTemplate">
            <article class="col-md-6 col-xl-4 live-order-item">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                            <div>
                                <div class="eyebrow text-uppercase order-area"></div>
                                <h3 class="h5 fw-bold mb-1 order-code"></h3>
                                <div class="text-muted order-time"></div>
                            </div>
                            <span class="status-badge order-status"></span>
                        </div>
                        <div class="d-flex justify-content-between gap-3 mb-3">
                            <div>
                                <div class="small text-muted">Bàn</div>
                                <strong class="order-table"></strong>
                            </div>
                            <div class="text-end">
                                <div class="small text-muted">Tổng tiền</div>
                                <strong class="order-total"></strong>
                            </div>
                        </div>
                        <ul class="list-group list-group-flush order-items"></ul>
                        <div class="order-payment mt-3 pt-3 border-top"></div>
                    </div>
                </div>
            </article>
        </template>

        <script>
            (() => {
                const mode = @json($mode);
                const initialOrders = @json($initialOrders ?? []);
                const streamUrl = `{{ route('staff.live-orders.stream') }}?mode=${mode}&transport=json`;
                const checkoutUrlTemplate = @json(route('staff.orders.checkout', ['order' => '__ORDER_ID__']));
                const container = document.getElementById('liveOrders');
                const template = document.getElementById('orderTemplate');
                const status = document.getElementById('liveStatus');
                let lastHash = '';

                const money = (value) => new Intl.NumberFormat('vi-VN').format(value || 0) + ' VNĐ';

                const render = (orders) => {
                    container.innerHTML = '';

                    if (!orders.length) {
                        const empty = document.createElement('div');
                        empty.className = 'col-12';
                        empty.innerHTML = '<div class="muted-box">Chưa có order nào.</div>';
                        container.appendChild(empty);
                        return;
                    }

                    orders.forEach((order) => {
                        const node = template.content.cloneNode(true);
                        node.querySelector('.order-area').textContent = order.area || 'Khu vực';
                        node.querySelector('.order-code').textContent = order.order_code || 'Chưa có mã đơn';
                        node.querySelector('.order-time').textContent = order.ordered_at || '';
                        node.querySelector('.order-status').textContent = order.status_label || order.status || 'Đang xử lý';
                        node.querySelector('.order-table').textContent = order.table || 'Chưa chọn bàn';
                        node.querySelector('.order-total').textContent = money(order.total_amount);

                        const list = node.querySelector('.order-items');
                        (order.items || []).forEach((item) => {
                            const row = document.createElement('li');
                            row.className = 'list-group-item px-0';

                            const main = document.createElement('div');
                            main.className = 'd-flex justify-content-between gap-3';

                            const name = document.createElement('strong');
                            name.textContent = `${item.quantity} x ${item.name || 'Món ăn'}`;
                            main.appendChild(name);

                            const price = document.createElement('span');
                            price.textContent = money(item.total_price);
                            main.appendChild(price);

                            row.appendChild(main);

                            if (item.note) {
                                const note = document.createElement('div');
                                note.className = 'small text-muted mt-1';
                                note.textContent = 'Ghi chú bếp: ' + item.note;
                                row.appendChild(note);
                            }

                            list.appendChild(row);
                        });

                        const payment = node.querySelector('.order-payment');
                        payment.innerHTML = `
                            <a class="btn btn-primary btn-sm w-100" href="${checkoutUrlTemplate.replace('__ORDER_ID__', order.id)}">
                                <i class="bi bi-credit-card" aria-hidden="true"></i> Thanh toán
                            </a>
                        `;

                        container.appendChild(node);
                    });
                };

                const poll = async () => {
                    try {
                        const response = await fetch(streamUrl, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}`);
                        }

                        const payload = await response.json();
                        const orders = payload.orders || [];
                        const hash = JSON.stringify(orders);

                        status.textContent = 'Cập nhật ' + (payload.generated_at || '');

                        if (hash !== lastHash) {
                            lastHash = hash;
                            render(orders);
                        }
                    } catch (error) {
                        status.textContent = 'Mất kết nối, đang thử lại';
                    }
                };

                render(initialOrders);
                status.textContent = 'Đang cập nhật tự động';
                poll();
                window.setInterval(poll, 3000);
            })();
        </script>
    @endif
</div>
@endsection
