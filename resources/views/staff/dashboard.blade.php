@extends('layouts.app')

@section('title', 'Nhân viên')

@php
    $kitchenLabels = [
        'pending' => 'Chờ chế biến',
        'cooking' => 'Đang chế biến',
        'completed' => 'Đã hoàn thành',
        'served' => 'Đã phục vụ',
    ];
    $orderLabels = [
        'pending' => 'Chờ xử lý',
        'serving' => 'Đang phục vụ',
        'completed' => 'Hoàn thành',
        'paid' => 'Đã thanh toán',
        'cancelled' => 'Đã hủy',
    ];
@endphp

@section('content')
<div class="container">
    <div class="page-hero mb-4">
        <div class="eyebrow mb-2">Dashboard nhân viên</div>
        <h1 class="display-6 fw-bold mb-2">Nhận gọi món, gửi xuống bếp và phục vụ khách đúng lúc.</h1>
        <p class="lead mb-0">Quy trình vận hành: Khách hàng → Nhân viên → Đầu bếp → Nhân viên → Khách hàng.</p>
    </div>

    <div class="d-flex flex-wrap gap-2 mb-4">
        <a class="btn btn-primary" href="{{ route('staff.kitchen') }}"><i class="bi bi-egg-fried me-1"></i>Dashboard đầu bếp</a>
        <a class="btn btn-outline-primary" href="{{ route('staff.cashier') }}"><i class="bi bi-receipt me-1"></i>Màn hình thu ngân</a>
    </div>

    <section class="mb-4" id="kitchenNotifications">
        @forelse($completedKitchenOrders as $kitchenOrder)
            <div class="alert alert-success border-0 shadow-sm d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <strong>{{ $kitchenOrder->items->sum('quantity') }} món ăn đã hoàn thành, vui lòng mang lên cho khách.</strong>
                    <div class="small">Đơn {{ $kitchenOrder->order?->order_code }} · {{ $kitchenOrder->order?->table?->table_name ?? 'Chưa chọn bàn' }}</div>
                </div>
                <form method="POST" action="{{ route('staff.kitchen-orders.serve', $kitchenOrder) }}">
                    @csrf
                    @method('PATCH')
                    <button class="btn btn-primary btn-sm" type="submit">Xác nhận đã phục vụ</button>
                </form>
            </div>
        @empty
            <div class="muted-box">Chưa có món hoàn thành cần phục vụ.</div>
        @endforelse
    </section>

    <section class="section-pad pt-0 pb-4">
        <div class="section-title">
            <div>
                <div class="eyebrow">Bàn ăn</div>
                <h2 class="h4 mb-0">Theo dõi 10 bàn ăn đang vận hành</h2>
            </div>
        </div>
        <div class="row g-3">
            @forelse($tables as $table)
                @php
                    $activeOrder = $table->activeOrders->first();
                    $currentItems = $activeOrder?->items ?? collect();
                @endphp
                <div class="col-md-6 col-xl-4">
                    <article class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                <div>
                                    <div class="eyebrow">{{ $table->area }}</div>
                                    <h3 class="h5 fw-bold mb-1">{{ $table->table_name }}</h3>
                                    <div class="text-muted">{{ $table->seats }} ghế · {{ $table->table_code }}</div>
                                </div>
                                <span class="status-badge">{{ ucfirst($table->status) }}</span>
                            </div>
                            <div class="muted-box">
                                @if($currentItems->isNotEmpty())
                                    <strong class="d-block mb-2">Món đang có</strong>
                                    <div class="d-grid gap-1">
                                        @foreach($currentItems->take(4) as $orderItem)
                                            <span>{{ $orderItem->quantity }} x {{ $orderItem->product?->name }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    Bàn hiện chưa có món.
                                @endif
                            </div>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12"><div class="muted-box">Chưa có bàn ăn.</div></div>
            @endforelse
        </div>
    </section>

    <div class="row g-4">
        <section class="col-xl-7">
            <div class="card h-100">
                <div class="card-body">
                    <div class="section-title">
                        <div>
                            <div class="eyebrow">Gọi món</div>
                            <h2 class="h5 mb-0">Đơn hàng cần xử lý</h2>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr><th>Mã đơn</th><th>Bàn</th><th>Món ăn</th><th>Bếp</th><th></th></tr>
                            </thead>
                            <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td>
                                        <strong>{{ $order->order_code }}</strong>
                                        <div class="small text-muted">{{ number_format((float) $order->total_amount) }} VNĐ</div>
                                    </td>
                                    <td>{{ $order->table?->table_name ?? 'Chưa chọn bàn' }}</td>
                                    <td>
                                        <div class="d-grid gap-1">
                                            @foreach($order->items->take(3) as $item)
                                                <span>{{ $item->quantity }} x {{ $item->product?->name }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>
                                        @if($order->kitchenOrder)
                                            <span class="status-badge">{{ $kitchenLabels[$order->kitchenOrder->status] ?? $order->kitchenOrder->status }}</span>
                                        @else
                                            <span class="text-muted">Chưa gửi bếp</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if(! $order->kitchenOrder)
                                            <form method="POST" action="{{ route('staff.kitchen-orders.send', $order) }}">
                                                @csrf
                                                <button class="btn btn-primary btn-sm" type="submit">Gửi xuống bếp</button>
                                            </form>
                                        @elseif($order->kitchenOrder->status === 'completed')
                                            <form method="POST" action="{{ route('staff.kitchen-orders.serve', $order->kitchenOrder) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button class="btn btn-primary btn-sm" type="submit">Đã phục vụ</button>
                                            </form>
                                        @else
                                            <span class="small text-muted">Đang chờ bếp</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-muted">Chưa có đơn hàng.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <section class="col-xl-5">
            <div class="card h-100">
                <div class="card-body">
                    <div class="section-title">
                        <div>
                            <div class="eyebrow">Đặt bàn</div>
                            <h2 class="h5 mb-0">Yêu cầu mới</h2>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light"><tr><th>Mã</th><th>Khách</th><th>Trạng thái</th><th></th></tr></thead>
                            <tbody>
                            @forelse($reservations as $reservation)
                                <tr>
                                    <td>
                                        <strong>{{ $reservation->reservation_code }}</strong>
                                        <div class="small text-muted">{{ $reservation->table?->table_name ?? 'Chưa chọn bàn' }}</div>
                                    </td>
                                    <td>
                                        {{ $reservation->customerName() ?: '-' }}
                                        <div class="small text-muted">{{ $reservation->customer_type ?? ($reservation->customer_id ? 'khách thành viên' : 'khách tiềm năng') }} · {{ $reservation->number_of_guests }} khách</div>
                                    </td>
                                    <td><span class="status-badge">{{ $reservation->status }}</span></td>
                                    <td>
                                        <form method="POST" action="{{ route('staff.reservations.update-status', $reservation) }}" class="d-flex gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <select class="form-select form-select-sm" name="status">
                                                @foreach(['chờ xác nhận', 'đã xác nhận', 'đã hủy', 'hoàn thành'] as $status)
                                                    <option value="{{ $status }}" @selected($reservation->status === $status)>{{ $status }}</option>
                                                @endforeach
                                            </select>
                                            <button class="btn btn-sm btn-primary" type="submit">Lưu</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-muted">Chưa có đặt bàn.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
    (() => {
        const endpoint = @json(route('staff.kitchen-orders.notifications'));
        const box = document.getElementById('kitchenNotifications');

        const renderNotifications = (items) => {
            if (!items.length) {
                box.innerHTML = '<div class="muted-box">Chưa có món hoàn thành cần phục vụ.</div>';
                return;
            }

            box.innerHTML = items.map((item) => `
                <div class="alert alert-success border-0 shadow-sm d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <strong>${item.message}</strong>
                        <div class="small">Đơn ${item.order_code || '-'} · ${item.table || 'Chưa chọn bàn'}</div>
                    </div>
                    <a class="btn btn-primary btn-sm" href="{{ route('staff.dashboard') }}">Kiểm tra đơn</a>
                </div>
            `).join('');
        };

        const poll = async () => {
            try {
                const response = await fetch(endpoint, { headers: { Accept: 'application/json' } });
                const payload = await response.json();
                renderNotifications(payload.data || []);
            } catch (error) {
                // Giữ nội dung hiện tại nếu mạng nội bộ chập chờn.
            }
        };

        window.setInterval(poll, 5000);
    })();
</script>
@endsection
