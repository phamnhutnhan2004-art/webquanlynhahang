@extends('layouts.app')

@section('title', 'Nhân viên')

@section('content')
<h1 class="h3 mb-4">Dashboard Nhân viên</h1>
<div class="row g-3">
    <div class="col-lg-6">
        <div class="card h-100"><div class="card-body">
            <h2 class="h5">Đơn hàng mới</h2>
            <div class="table-responsive"><table class="table table-sm table-hover mb-0">
                <thead><tr><th>Mã đơn</th><th>Trạng thái</th><th>Tổng tiền</th></tr></thead>
                <tbody>@forelse($orders as $order)<tr><td>{{ $order->order_code }}</td><td>{{ $order->status }}</td><td>{{ number_format((float) $order->total_amount) }}</td></tr>@empty<tr><td colspan="3" class="text-muted">Chưa có đơn hàng.</td></tr>@endforelse</tbody>
            </table></div>
        </div></div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100"><div class="card-body">
            <h2 class="h5">Đặt bàn</h2>
            <div class="table-responsive"><table class="table table-sm table-hover mb-0">
                <thead><tr><th>Mã đặt bàn</th><th>Số khách</th><th>Trạng thái</th></tr></thead>
                <tbody>@forelse($reservations as $reservation)<tr><td>{{ $reservation->reservation_code }}</td><td>{{ $reservation->number_of_guests }}</td><td>{{ $reservation->status }}</td></tr>@empty<tr><td colspan="3" class="text-muted">Chưa có đặt bàn.</td></tr>@endforelse</tbody>
            </table></div>
        </div></div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100"><div class="card-body">
            <h2 class="h5">Khach hang</h2>
            <ul class="list-group list-group-flush">@forelse($customers as $customer)<li class="list-group-item px-0">{{ $customer->full_name }} - {{ $customer->phone }}</li>@empty<li class="list-group-item px-0 text-muted">Chưa có khách hàng.</li>@endforelse</ul>
        </div></div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100"><div class="card-body">
            <h2 class="h5">Thông tin món ăn</h2>
            <ul class="list-group list-group-flush">@forelse($products as $product)<li class="list-group-item px-0 d-flex justify-content-between"><span>{{ $product->name }}</span><strong>{{ number_format((float) $product->price) }}</strong></li>@empty<li class="list-group-item px-0 text-muted">Chưa có món ăn.</li>@endforelse</ul>
        </div></div>
    </div>
</div>
@endsection
