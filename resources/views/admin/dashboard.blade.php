@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="h3 mb-1">Dashboard Admin</h1>
        <p class="text-muted mb-0">Tổng quan hệ thống quản lý nhà hàng.</p>
    </div>
    <div class="btn-group flex-wrap">
        <a class="btn btn-outline-primary" href="{{ route('admin.section', 'employees') }}">Nhân viên</a>
        <a class="btn btn-outline-primary" href="{{ route('admin.section', 'products') }}">Món ăn</a>
        <a class="btn btn-outline-primary" href="{{ route('admin.section', 'categories') }}">Danh mục</a>
        <a class="btn btn-outline-primary" href="{{ route('admin.section', 'tables') }}">Bàn ăn</a>
        <a class="btn btn-outline-primary" href="{{ route('admin.section', 'orders') }}">Đơn hàng</a>
        <a class="btn btn-outline-primary" href="{{ route('admin.section', 'stats') }}">Thống kê</a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card"><div class="card-body"><div class="text-muted">Tổng số món ăn</div><div class="display-6 fw-bold">{{ $totalProducts }}</div></div></div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card"><div class="card-body"><div class="text-muted">Tổng số đơn hàng</div><div class="display-6 fw-bold">{{ $totalOrders }}</div></div></div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card"><div class="card-body"><div class="text-muted">Tổng doanh thu</div><div class="h2 fw-bold">{{ number_format((float) $totalRevenue) }}</div><div class="small text-muted">VND</div></div></div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card"><div class="card-body"><div class="text-muted">Bàn đang sử dụng</div><div class="display-6 fw-bold">{{ $activeTables }}</div></div></div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h2 class="h5 mb-3">Đơn hàng gần đây</h2>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Mã đơn</th><th>Trạng thái</th><th>Tổng tiền</th><th>Thời gian</th></tr></thead>
                <tbody>
                @forelse($recentOrders as $order)
                    <tr>
                        <td>{{ $order->order_code }}</td>
                        <td><span class="badge text-bg-secondary">{{ $order->status }}</span></td>
                        <td>{{ number_format((float) $order->total_amount) }} VND</td>
                        <td>{{ optional($order->ordered_at)->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-muted">Chưa có đơn hàng.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
