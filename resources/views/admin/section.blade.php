@extends('layouts.app')

@section('title', 'Quản lý ' . $section)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Quản lý {{ str_replace('_', ' ', $section) }}</h1>
        <p class="text-muted mb-0">Khu vực dành cho Admin.</p>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('admin.dashboard') }}">Về dashboard</a>
</div>

@if($section === 'stats')
    <div class="card">
        <div class="card-body">
            <h2 class="h5 mb-3">Doanh thu theo ngày</h2>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>Ngày</th><th>Doanh thu</th></tr></thead>
                    <tbody>
                    @forelse($revenueByDay as $row)
                        <tr><td>{{ $row->date }}</td><td>{{ number_format((float) $row->revenue) }} VND</td></tr>
                    @empty
                        <tr><td colspan="2" class="text-muted">Chưa có dữ liệu thống kê.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@else
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    @if($section === 'employees')
                        <thead class="table-light"><tr><th>Mã NV</th><th>Họ tên</th><th>Vị trí</th><th>Ca làm</th><th>Lương</th></tr></thead>
                        <tbody>@forelse($items as $item)<tr><td>{{ $item->employee_code }}</td><td>{{ $item->user?->name ?? $item->user?->full_name }}</td><td>{{ $item->position }}</td><td>{{ $item->shift }}</td><td>{{ number_format((float) $item->salary) }}</td></tr>@empty<tr><td class="text-muted">Chưa có dữ liệu.</td></tr>@endforelse</tbody>
                    @elseif($section === 'products')
                        <thead class="table-light"><tr><th>Tên món</th><th>Danh mục</th><th>Giá</th><th>Trạng thái</th></tr></thead>
                        <tbody>@forelse($items as $item)<tr><td>{{ $item->name }}</td><td>{{ $item->category?->name }}</td><td>{{ number_format((float) $item->price) }} VND</td><td>{{ $item->status }}</td></tr>@empty<tr><td class="text-muted">Chưa có dữ liệu.</td></tr>@endforelse</tbody>
                    @elseif($section === 'categories')
                        <thead class="table-light"><tr><th>Tên danh mục</th><th>Mô tả</th><th>Số món</th><th>Trạng thái</th></tr></thead>
                        <tbody>@forelse($items as $item)<tr><td>{{ $item->name }}</td><td>{{ $item->description }}</td><td>{{ $item->products_count }}</td><td>{{ $item->status }}</td></tr>@empty<tr><td class="text-muted">Chưa có dữ liệu.</td></tr>@endforelse</tbody>
                    @elseif($section === 'tables')
                        <thead class="table-light"><tr><th>Mã bàn</th><th>Tên bàn</th><th>Khu vực</th><th>Số ghế</th><th>Trạng thái</th></tr></thead>
                        <tbody>@forelse($items as $item)<tr><td>{{ $item->table_code }}</td><td>{{ $item->table_name }}</td><td>{{ $item->area }}</td><td>{{ $item->seats }}</td><td>{{ $item->status }}</td></tr>@empty<tr><td class="text-muted">Chưa có dữ liệu.</td></tr>@endforelse</tbody>
                    @elseif($section === 'orders')
                        <thead class="table-light"><tr><th>Mã đơn</th><th>Khách hàng</th><th>Bàn</th><th>Số món</th><th>Tổng tiền</th><th>Trạng thái</th></tr></thead>
                        <tbody>@forelse($items as $item)<tr><td>{{ $item->order_code }}</td><td>{{ $item->customer?->full_name }}</td><td>{{ $item->table?->table_name }}</td><td>{{ $item->items->count() }}</td><td>{{ number_format((float) $item->total_amount) }} VND</td><td>{{ $item->status }}</td></tr>@empty<tr><td class="text-muted">Chưa có dữ liệu.</td></tr>@endforelse</tbody>
                    @endif
                </table>
            </div>
        </div>
    </div>
@endif
@endsection
