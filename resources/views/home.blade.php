@extends('layouts.app')

@section('title', 'Nhà hàng World')

@section('content')
<div class="row align-items-center g-4 mb-4">
    <div class="col-lg-7">
        <h1 class="display-6 fw-bold mb-3">Website Quản lý Nhà hàng World</h1>
        <p class="lead text-muted mb-0">Quản lý menu, đặt bàn, đơn hàng, thanh toán và chatbot hỗ trợ khách hàng.</p>
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-body">
                <h2 class="h5">Danh mục món ăn</h2>
                <div class="d-flex flex-wrap gap-2">
                    @forelse($categories as $category)
                        <span class="badge text-bg-light border">{{ $category->name }}: {{ $category->products_count }}</span>
                    @empty
                        <span class="text-muted">Chưa có danh mục.</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    @forelse($products as $product)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="small text-muted">{{ $product->category?->name }}</div>
                    <h2 class="h5 mt-2">{{ $product->name }}</h2>
                    <p class="text-muted">{{ $product->description }}</p>
                    <div class="fw-bold">{{ number_format((float) $product->price) }} VND</div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info">Chưa có sản phẩm trong menu.</div>
        </div>
    @endforelse
</div>
@endsection
