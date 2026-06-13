@extends('layouts.app')

@section('title', 'Nhân viên')

@section('content')
<div class="container">
<div class="page-hero mb-4">
    <div class="eyebrow mb-2">Dashboard nhân viên</div>
    <h1 class="display-6 fw-bold mb-2">Theo dõi đặt bàn, đơn hàng và khách hàng.</h1>
    <p class="lead mb-0">Cập nhật trạng thái ngay tại bảng để dữ liệu vận hành luôn mới.</p>
</div>

<div class="d-flex flex-wrap gap-2 mb-4">
    <a class="btn btn-primary" href="{{ route('staff.kitchen') }}">Mở màn hình Bếp</a>
    <a class="btn btn-outline-primary" href="{{ route('staff.cashier') }}">Mở màn hình Thu ngân</a>
</div>

<div class="row g-4">
    <section class="col-xl-7">
        <div class="card h-100">
            <div class="card-body">
                <div class="app-section-title">
                    <div>
                        <h2 class="h5 mb-1">Đặt bàn mới</h2>
                        <p class="text-muted mb-0">Xác nhận, hủy hoặc hoàn thành yêu cầu.</p>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Mã</th><th>Khách</th><th>Thời gian</th><th>Trạng thái</th><th></th></tr></thead>
                        <tbody>
                        @forelse($reservations as $reservation)
                            <tr>
                                <td>
                                    <strong>{{ $reservation->reservation_code }}</strong>
                                    <div class="small text-muted">{{ $reservation->table?->table_name ?? 'Chưa chọn bàn' }}</div>
                                </td>
                                <td>{{ $reservation->customer?->full_name }}<div class="small text-muted">{{ $reservation->number_of_guests }} khách</div></td>
                                <td>{{ optional($reservation->reservation_time)->format('d/m/Y H:i') }}</td>
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
                            <tr><td colspan="5" class="text-muted">Chưa có đặt bàn.</td></tr>
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
                <div class="app-section-title">
                    <div>
                        <h2 class="h5 mb-1">Đơn hàng</h2>
                        <p class="text-muted mb-0">Cập nhật tiến độ phục vụ.</p>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Mã đơn</th><th>Tổng tiền</th><th>Trạng thái</th></tr></thead>
                        <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>
                                    <strong>{{ $order->order_code }}</strong>
                                    <div class="small text-muted">{{ $order->table?->table_name }}</div>
                                </td>
                                <td>{{ number_format((float) $order->total_amount) }} VND</td>
                                <td>
                                    <form method="POST" action="{{ route('staff.orders.update-status', $order) }}" class="d-flex gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <select class="form-select form-select-sm" name="status">
                                            @foreach(['pending' => 'Chờ xử lý', 'serving' => 'Đang phục vụ', 'completed' => 'Hoàn thành', 'cancelled' => 'Đã hủy'] as $value => $label)
                                                <option value="{{ $value }}" @selected($order->status === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-sm btn-primary" type="submit">Lưu</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-muted">Chưa có đơn hàng.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <section class="col-lg-6">
        <div class="card h-100"><div class="card-body">
            <h2 class="h5 mb-3">Khách hàng gần đây</h2>
            <ul class="list-group list-group-flush">
                @forelse($customers as $customer)
                    <li class="list-group-item px-0 d-flex justify-content-between gap-3">
                        <span>{{ $customer->full_name }}</span>
                        <span class="text-muted">{{ $customer->phone }}</span>
                    </li>
                @empty
                    <li class="list-group-item px-0 text-muted">Chưa có khách hàng.</li>
                @endforelse
            </ul>
        </div></div>
    </section>

    <section class="col-lg-6">
        <div class="card h-100"><div class="card-body">
            <h2 class="h5 mb-3">Món ăn</h2>
            <ul class="list-group list-group-flush">
                @forelse($products as $product)
                    <li class="list-group-item px-0 d-flex justify-content-between gap-3">
                        <span>{{ $product->name }}</span>
                        <strong>{{ number_format((float) $product->price) }}</strong>
                    </li>
                @empty
                    <li class="list-group-item px-0 text-muted">Chưa có món ăn.</li>
                @endforelse
            </ul>
        </div></div>
    </section>
</div>
</div>
@endsection
