@extends('layouts.app')

@section('title', 'Khách hàng')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="h3 mb-1">Khu vực khách hàng</h1>
        <p class="text-muted mb-0">Xem menu, đặt bàn, đặt món và sử dụng chatbot hỗ trợ.</p>
    </div>
    <button class="btn btn-primary" type="button" disabled>Chatbot hỗ trợ</button>
</div>

<div class="row g-3">
    @forelse($products as $product)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="small text-muted">{{ $product->category?->name }}</div>
                    <h2 class="h5 mt-2">{{ $product->name }}</h2>
                    <p class="text-muted">{{ $product->description }}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <strong>{{ number_format((float) $product->price) }} VND</strong>
                        <button class="btn btn-outline-primary btn-sm" disabled>Đặt món</button>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12"><div class="alert alert-info">Chưa có menu.</div></div>
    @endforelse
</div>
@endsection
