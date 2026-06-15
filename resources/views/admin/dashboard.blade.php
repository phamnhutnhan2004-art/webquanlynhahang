@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container">
<section class="page-hero mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
        <div>
            <div class="eyebrow mb-2">Quản trị hệ thống</div>
            <h1 class="display-6 fw-bold mb-2">Tổng quan vận hành Nhà hàng Hoa Sen</h1>
            <p class="lead mb-0">Theo dõi dữ liệu chính và đi nhanh tới các khu vực quản lý.</p>
        </div>
        <div class="btn-group flex-wrap">
            <a class="btn btn-light" href="{{ route('admin.section', 'products') }}">Món ăn</a>
            <a class="btn btn-outline-light" href="{{ route('admin.section', 'tables') }}">Bàn ăn</a>
            <a class="btn btn-outline-light" href="{{ route('admin.section', 'orders') }}">Đơn hàng</a>
            <a class="btn btn-outline-light" href="{{ route('admin.section', 'home-parties') }}">Đặt tiệc</a>
            <a class="btn btn-outline-light" href="{{ route('admin.section', 'menu-galleries') }}">Menu ảnh</a>
        </div>
    </div>
</section>

<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card"><div class="card-body">
            <div class="text-muted">Tổng số món ăn</div>
            <div class="stat-value">{{ $totalProducts }}</div>
        </div></div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card"><div class="card-body">
            <div class="text-muted">Tổng số đơn hàng</div>
            <div class="stat-value">{{ $totalOrders }}</div>
        </div></div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card"><div class="card-body">
            <div class="text-muted">Doanh thu ghi nhận</div>
            <div class="h2 fw-bold mb-0">{{ number_format((float) $totalRevenue) }}</div>
            <div class="small text-muted">VND</div>
        </div></div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card"><div class="card-body">
            <div class="text-muted">Đặt bàn chờ xác nhận</div>
            <div class="stat-value">{{ $pendingReservations }}</div>
        </div></div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card"><div class="card-body">
            <div class="text-muted">Tổng số đơn đặt tiệc</div>
            <div class="stat-value">{{ $totalHomeParties }}</div>
        </div></div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card"><div class="card-body">
            <div class="text-muted">Doanh thu đặt tiệc</div>
            <div class="h2 fw-bold mb-0">{{ number_format((float) $homePartyRevenue) }}</div>
            <div class="small text-muted">VND</div>
        </div></div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card"><div class="card-body">
            <div class="text-muted">Khách đã phục vụ</div>
            <div class="stat-value">{{ $homePartyGuests }}</div>
        </div></div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card"><div class="card-body">
            <div class="text-muted">Tiệc đang diễn ra</div>
            <div class="stat-value">{{ $activeHomeParties }}</div>
        </div></div>
    </div>
</div>

<div class="row g-4">
    <section class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="app-section-title">
                    <div>
                        <h2 class="h5 mb-1">Đơn hàng gần đây</h2>
                        <p class="text-muted mb-0">Các đơn mới nhất trong hệ thống.</p>
                    </div>
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.section', 'orders') }}">Xem tất cả</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Mã đơn</th><th>Khách</th><th>Bàn</th><th>Tổng tiền</th><th>Trạng thái</th></tr></thead>
                        <tbody>
                        @forelse($recentOrders as $order)
                            <tr>
                                <td><strong>{{ $order->order_code }}</strong></td>
                                <td>{{ $order->customer?->full_name ?? 'Khách lẻ' }}</td>
                                <td>{{ $order->table?->table_name ?? '-' }}</td>
                                <td>{{ number_format((float) $order->total_amount) }} VND</td>
                                <td><span class="status-badge">{{ $order->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-muted">Chưa có đơn hàng.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <div class="app-section-title">
                    <div>
                        <h2 class="h5 mb-1">Đơn đặt tiệc tại nhà gần đây</h2>
                        <p class="text-muted mb-0">Theo dõi lịch tổ chức và trạng thái xử lý.</p>
                    </div>
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.section', 'home-parties') }}">Quản lý đặt tiệc</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Khách</th><th>Ngày tiệc</th><th>Số khách</th><th>Tổng tiền</th><th>Trạng thái</th></tr></thead>
                        <tbody>
                        @forelse($recentHomeParties as $party)
                            <tr>
                                <td><strong>{{ $party->full_name }}</strong><div class="small text-muted">{{ $party->phone }}</div></td>
                                <td>{{ $party->event_date?->format('d/m/Y') }} {{ $party->event_time?->format('H:i') }}</td>
                                <td>{{ $party->guest_quantity }}</td>
                                <td>{{ number_format((float) $party->total_price) }} VND</td>
                                <td><span class="status-badge">{{ $party->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-muted">Chưa có đơn đặt tiệc tại nhà.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <section class="col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h5 mb-3">Quản lý nhanh</h2>
                <div class="d-grid gap-2">
                    <a class="btn btn-outline-primary text-start" href="{{ route('admin.section', 'categories') }}">Danh mục món ăn</a>
                    <a class="btn btn-outline-primary text-start" href="{{ route('admin.section', 'products') }}">Món ăn / sản phẩm</a>
                    <a class="btn btn-outline-primary text-start" href="{{ route('admin.section', 'tables') }}">Bàn ăn</a>
                    <a class="btn btn-outline-primary text-start" href="{{ route('admin.section', 'home-parties') }}">Đặt tiệc tại nhà</a>
                    <a class="btn btn-outline-primary text-start" href="{{ route('admin.section', 'menu-galleries') }}">Menu hình ảnh</a>
                    <a class="btn btn-outline-primary text-start" href="{{ route('admin.section', 'gallery-images') }}">Thư viện ảnh</a>
                    <a class="btn btn-outline-primary text-start" href="{{ route('admin.section', 'employees') }}">Nhân viên</a>
                    <a class="btn btn-outline-primary text-start" href="{{ route('admin.section', 'stats') }}">Thống kê doanh thu</a>
                </div>
            </div>
        </div>
    </section>
</div>
</div>
@endsection
