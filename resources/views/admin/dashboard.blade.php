@extends('layouts.admin')

@section('title', 'Bảng điều khiển - Quản trị Nhà hàng World')

@php
    $money = fn ($value) => number_format((float) $value, 0, ',', '.').' VNĐ';
    $monthlyMax = max(1, (float) ($monthlyRevenue->max('value') ?: 1));
    $dailyMax = max(1, (float) ($dailyOrders->max('value') ?: 1));
    $topMax = max(1, (float) ($topProducts->max('value') ?: 1));
    $tableMax = max(1, (float) ($tableStatuses->max('value') ?: 1));
@endphp

@section('content')
<section class="admin-page-head">
    <div>
        <div class="admin-kicker">Tổng quan vận hành</div>
        <h1 class="admin-title">Bảng điều khiển Admin</h1>
        <p class="admin-subtitle">Theo dõi nhanh doanh thu, đơn hàng, bàn ăn, khách hàng và các nghiệp vụ chính của Website Quản lý Nhà hàng World.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-primary" href="{{ route('admin.section', 'products') }}"><i class="bi bi-plus-lg me-1"></i>Thêm món</a>
        <a class="btn btn-outline-secondary" href="{{ route('admin.section', 'stats') }}"><i class="bi bi-graph-up me-1"></i>Xem báo cáo</a>
    </div>
</section>

<section class="stat-grid" aria-label="Thống kê tổng quan">
    <article class="stat-tile">
        <div class="stat-icon green"><i class="bi bi-egg-fried"></i></div>
        <div><div class="stat-label">Tổng số món ăn</div><div class="stat-value">{{ $totalProducts }}</div></div>
    </article>
    <article class="stat-tile">
        <div class="stat-icon blue"><i class="bi bi-receipt-cutoff"></i></div>
        <div><div class="stat-label">Tổng số đơn hàng</div><div class="stat-value">{{ $totalOrders }}</div></div>
    </article>
    <article class="stat-tile">
        <div class="stat-icon gold"><i class="bi bi-cash-stack"></i></div>
        <div><div class="stat-label">Tổng doanh thu</div><div class="stat-value">{{ $money($totalRevenue) }}</div></div>
    </article>
    <article class="stat-tile">
        <div class="stat-icon coral"><i class="bi bi-people"></i></div>
        <div><div class="stat-label">Tổng số khách hàng</div><div class="stat-value">{{ $totalCustomers }}</div></div>
    </article>
    <article class="stat-tile">
        <div class="stat-icon green"><i class="bi bi-grid-3x3-gap"></i></div>
        <div><div class="stat-label">Tổng số bàn ăn</div><div class="stat-value">{{ $totalTables }}</div></div>
    </article>
    <article class="stat-tile">
        <div class="stat-icon blue"><i class="bi bi-calendar-check"></i></div>
        <div><div class="stat-label">Tổng số đơn đặt bàn</div><div class="stat-value">{{ $totalReservations }}</div></div>
    </article>
    <article class="stat-tile">
        <div class="stat-icon gold"><i class="bi bi-stars"></i></div>
        <div><div class="stat-label">Tổng số đơn đặt tiệc tại nhà</div><div class="stat-value">{{ $totalHomeParties }}</div></div>
    </article>
    <article class="stat-tile">
        <div class="stat-icon coral"><i class="bi bi-person-workspace"></i></div>
        <div><div class="stat-label">Số bàn đang sử dụng</div><div class="stat-value">{{ $activeTables }}</div></div>
    </article>
</section>

<div class="row g-3 mb-3">
    <section class="col-xl-6">
        <div class="admin-card chart-card h-100">
            <div class="admin-card-header">
                <div>
                    <h2 class="h5 mb-1">Doanh thu theo tháng</h2>
                    <div class="small text-muted">6 tháng gần nhất</div>
                </div>
                <i class="bi bi-bar-chart-line text-success fs-4"></i>
            </div>
            <div class="admin-card-body">
                <div class="bar-chart" style="--bars: {{ $monthlyRevenue->count() }}">
                    @foreach($monthlyRevenue as $row)
                        <div class="chart-bar" title="{{ $money($row['value']) }}">
                            <div class="chart-fill" style="--height: {{ max(5, round(($row['value'] / $monthlyMax) * 100)) }}"></div>
                            <span>{{ $row['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="col-xl-6">
        <div class="admin-card chart-card h-100">
            <div class="admin-card-header">
                <div>
                    <h2 class="h5 mb-1">Đơn hàng theo ngày</h2>
                    <div class="small text-muted">7 ngày gần nhất</div>
                </div>
                <i class="bi bi-activity text-primary fs-4"></i>
            </div>
            <div class="admin-card-body">
                <div class="bar-chart" style="--bars: {{ $dailyOrders->count() }}">
                    @foreach($dailyOrders as $row)
                        <div class="chart-bar" title="{{ $row['value'] }} đơn hàng">
                            <div class="chart-fill alt" style="--height: {{ max(5, round(($row['value'] / $dailyMax) * 100)) }}"></div>
                            <span>{{ $row['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</div>

<div class="row g-3 mb-3">
    <section class="col-xl-6">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <div>
                    <h2 class="h5 mb-1">Top 5 món ăn bán chạy</h2>
                    <div class="small text-muted">Xếp theo số lượng trong đơn hàng</div>
                </div>
                <i class="bi bi-award text-warning fs-4"></i>
            </div>
            <div class="admin-card-body">
                <div class="progress-list">
                    @forelse($topProducts as $row)
                        <div class="progress-row">
                            <div class="progress-meta"><span>{{ $row['label'] }}</span><span>{{ $row['value'] }} món</span></div>
                            <div class="progress-track"><div class="progress-fill" style="--width: {{ max(6, round(($row['value'] / $topMax) * 100)) }}"></div></div>
                        </div>
                    @empty
                        <div class="soft-note">Chưa có dữ liệu bán chạy.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <section class="col-xl-6">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <div>
                    <h2 class="h5 mb-1">Trạng thái bàn ăn</h2>
                    <div class="small text-muted">Tổng hợp theo trạng thái hiện tại</div>
                </div>
                <i class="bi bi-pie-chart text-danger fs-4"></i>
            </div>
            <div class="admin-card-body">
                <div class="progress-list">
                    @forelse($tableStatuses as $row)
                        <div class="progress-row">
                            <div class="progress-meta"><span>{{ ucfirst($row['label']) }}</span><span>{{ $row['value'] }} bàn</span></div>
                            <div class="progress-track"><div class="progress-fill" style="--width: {{ max(6, round(($row['value'] / $tableMax) * 100)) }}"></div></div>
                        </div>
                    @empty
                        <div class="soft-note">Chưa có dữ liệu bàn ăn.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
</div>

<div class="row g-3">
    <section class="col-xl-7">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <div>
                    <h2 class="h5 mb-1">Đơn hàng gần đây</h2>
                    <div class="small text-muted">Các đơn mới nhất trong hệ thống</div>
                </div>
                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.section', 'orders') }}">Xem tất cả</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover admin-table mb-0">
                    <thead><tr><th>Mã đơn</th><th>Khách hàng</th><th>Bàn</th><th>Tổng tiền</th><th>Trạng thái</th></tr></thead>
                    <tbody>
                    @forelse($recentOrders as $order)
                        <tr>
                            <td><strong>{{ $order->order_code }}</strong></td>
                            <td>{{ $order->customer?->full_name ?? 'Khách lẻ' }}</td>
                            <td>{{ $order->table?->table_name ?? '-' }}</td>
                            <td>{{ $money($order->total_amount) }}</td>
                            <td><span class="status-pill">{{ $order->status }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-muted">Chưa có đơn hàng.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="col-xl-5">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <div>
                    <h2 class="h5 mb-1">Đặt tiệc tại nhà gần đây</h2>
                    <div class="small text-muted">Lịch tiệc cần theo dõi</div>
                </div>
                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.section', 'home-parties') }}">Quản lý</a>
            </div>
            <div class="admin-card-body">
                <div class="d-grid gap-3">
                    @forelse($recentHomeParties as $party)
                        <article class="d-flex justify-content-between gap-3 border-bottom pb-3">
                            <div>
                                <strong>{{ $party->full_name }}</strong>
                                <div class="small text-muted">{{ $party->phone }} · {{ $party->guest_quantity }} khách</div>
                                <div class="small">{{ $party->event_date?->format('d/m/Y') }} {{ $party->event_time?->format('H:i') }}</div>
                            </div>
                            <span class="status-pill align-self-start">{{ $party->status }}</span>
                        </article>
                    @empty
                        <div class="soft-note">Chưa có đơn đặt tiệc tại nhà.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
