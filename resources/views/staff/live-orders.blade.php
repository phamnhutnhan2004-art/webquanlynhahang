@extends('layouts.app')

@section('title', $title)

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
                <a class="btn btn-light" href="{{ route('staff.kitchen') }}">Bếp</a>
                <a class="btn btn-outline-light" href="{{ route('staff.cashier') }}">Thu ngân</a>
            </div>
        </div>
    </div>

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
</div>

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
            </div>
        </div>
    </article>
</template>

<script>
    (() => {
        const mode = @json($mode);
        const streamUrl = `{{ route('staff.live-orders.stream') }}?mode=${mode}`;
        const container = document.getElementById('liveOrders');
        const template = document.getElementById('orderTemplate');
        const status = document.getElementById('liveStatus');
        let source;

        const money = (value) => new Intl.NumberFormat('vi-VN').format(value || 0) + ' VND';

        const render = (orders) => {
            container.innerHTML = '';

            if (!orders.length) {
                const empty = document.createElement('div');
                empty.className = 'col-12';
                empty.innerHTML = '<div class="muted-box">Chưa có order đang xử lý.</div>';
                container.appendChild(empty);
                return;
            }

            orders.forEach((order) => {
                const node = template.content.cloneNode(true);
                node.querySelector('.order-area').textContent = order.area || 'Khu vực';
                node.querySelector('.order-code').textContent = order.order_code;
                node.querySelector('.order-time').textContent = order.ordered_at || '';
                node.querySelector('.order-status').textContent = order.status_label;
                node.querySelector('.order-table').textContent = order.table || 'Chưa chọn bàn';
                node.querySelector('.order-total').textContent = money(order.total_amount);

                const list = node.querySelector('.order-items');
                order.items.forEach((item) => {
                    const row = document.createElement('li');
                    row.className = 'list-group-item px-0';

                    const main = document.createElement('div');
                    main.className = 'd-flex justify-content-between gap-3';

                    const name = document.createElement('strong');
                    name.textContent = `${item.quantity} x ${item.name || 'Món ăn'}`;
                    main.appendChild(name);

                    if (mode === 'cashier') {
                        const price = document.createElement('span');
                        price.textContent = money(item.total_price);
                        main.appendChild(price);
                    }

                    row.appendChild(main);

                    if (item.note) {
                        const note = document.createElement('div');
                        note.className = 'small text-muted mt-1';
                        note.textContent = 'Ghi chú bếp: ' + item.note;
                        row.appendChild(note);
                    }

                    list.appendChild(row);
                });

                container.appendChild(node);
            });
        };

        const connect = () => {
            source = new EventSource(streamUrl);
            status.textContent = 'Đang kết nối';

            source.addEventListener('orders', (event) => {
                const payload = JSON.parse(event.data);
                status.textContent = 'Cập nhật ' + payload.generated_at;
                render(payload.orders || []);
            });

            source.onerror = () => {
                status.textContent = 'Đang kết nối lại';
                source.close();
                setTimeout(connect, 1800);
            };
        };

        connect();
    })();
</script>
@endsection
