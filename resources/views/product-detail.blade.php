@extends('layouts.app')

@section('title', $product->name.' - Món ăn')

@section('content')
<style>
    .product-detail-hero {
        margin-top: -1.5rem;
        padding: clamp(3rem, 7vw, 5rem) 0;
        background:
            linear-gradient(90deg, rgba(17, 10, 6, .9), rgba(14, 59, 50, .68) 56%, rgba(14, 59, 50, .18)),
            url("{{ $product->image_url }}") center / cover;
        color: #fff;
    }

    .product-detail-hero p {
        color: rgba(255, 255, 255, .96);
        font-weight: 650;
        line-height: 1.72;
    }

    .product-detail-card .text-muted,
    .food-card .text-muted {
        color: #4a4036 !important;
        font-weight: 600;
        line-height: 1.68;
    }

    .product-detail-card {
        margin-top: -2.5rem;
        position: relative;
        z-index: 2;
    }

    .product-photo {
        width: 100%;
        aspect-ratio: 4 / 3;
        object-fit: cover;
        display: block;
    }

    .product-price-large {
        color: var(--green);
        font-size: 2.2rem;
        font-weight: 950;
        line-height: 1;
    }
</style>

@php
    $category = $product->category;
    $orderUrl = auth()->check() && auth()->user()->isCustomer()
        ? route('customer.dashboard')
        : route('login');
@endphp

<section class="product-detail-hero">
    <div class="container">
        <nav class="mb-4 small" aria-label="breadcrumb">
            <a class="text-white text-decoration-none" href="{{ route('home') }}">Trang chủ</a>
            <span class="mx-2 text-white-50">/</span>
            <a class="text-white text-decoration-none" href="{{ route('menu') }}">Thực đơn</a>
            @if($category)
                <span class="mx-2 text-white-50">/</span>
                <a class="text-white text-decoration-none" href="{{ route('menu.category', $category->menu_slug) }}">{{ $category->name }}</a>
            @endif
            <span class="mx-2 text-white-50">/</span>
            <span class="text-white-50">{{ $product->name }}</span>
        </nav>

        <div class="eyebrow mb-3">{{ $category?->name ?? 'Món ăn' }}</div>
        <h1 class="display-4 fw-bold mb-3">{{ $product->name }}</h1>
        <p class="lead mb-0">{{ $product->description ?: 'Món ăn đang được phục vụ tại Nhà hàng Hoa Sen.' }}</p>
    </div>
</section>

<section class="product-detail-card">
    <div class="container">
        <div class="card">
            <div class="row g-0">
                <div class="col-lg-5">
                    <img class="product-photo" src="{{ $product->image_url }}" alt="{{ $product->name }}">
                </div>
                <div class="col-lg-7">
                    <div class="card-body p-4 p-lg-5">
                        <span class="status-badge">{{ $category?->name ?? 'Thực đơn' }}</span>
                        <h2 class="home-section-title mt-3 mb-3">{{ $product->name }}</h2>
                        <p class="text-muted fs-5">{{ $product->description ?: 'Món ăn được chế biến theo phong cách Hoa Sen, phù hợp cho bữa ăn gia đình và đặt bàn tại nhà hàng.' }}</p>
                        <div class="product-price-large mb-4">{{ number_format((float) $product->price, 0, ',', '.') }} VNĐ</div>
                        <div class="d-flex flex-wrap gap-2">
                            <a class="btn btn-primary btn-lg" href="{{ $orderUrl }}">
                                <i class="bi bi-bag-check me-1" aria-hidden="true"></i>Đặt món
                            </a>
                            @if($category)
                                <a class="btn btn-outline-primary btn-lg" href="{{ route('menu.category', $category->menu_slug) }}">
                                    Xem thêm {{ $category->name }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@if($relatedProducts->isNotEmpty())
    <section class="section-pad">
        <div class="container">
            <div class="section-title">
                <div>
                    <h2 class="home-section-title mb-0">Món cùng danh mục</h2>
                </div>
            </div>

            <div class="row g-4">
                @foreach($relatedProducts as $related)
                    <div class="col-md-4">
                        <article class="card food-card h-100">
                            <img class="food-img" src="{{ $related->image_url }}" alt="{{ $related->name }}">
                            <div class="card-body">
                                <h3 class="h5 fw-bold">{{ $related->name }}</h3>
                                <p class="text-muted">{{ Str::limit($related->description ?: 'Món ăn đang bán tại Hoa Sen.', 90) }}</p>
                                <div class="d-flex justify-content-between align-items-center gap-2">
                                    <strong class="gold-text">{{ number_format((float) $related->price, 0, ',', '.') }} VNĐ</strong>
                                    <a class="btn btn-outline-primary btn-sm" href="{{ route('products.show', $related->slug) }}">Xem chi tiết</a>
                                </div>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
@endsection
