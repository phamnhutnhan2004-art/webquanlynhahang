@extends('layouts.app')

@section('title', $category->name.' - Thực đơn')

@section('content')
<style>
    .menu-category-hero {
        margin-top: -1.5rem;
        padding: clamp(3rem, 7vw, 5.5rem) 0;
        background:
            linear-gradient(90deg, rgba(17, 10, 6, .88), rgba(14, 59, 50, .72) 54%, rgba(14, 59, 50, .22)),
            var(--menu-category-image) center / cover;
        color: #fff;
    }

    .menu-category-hero p {
        max-width: 720px;
        color: rgba(255, 255, 255, .96);
        font-weight: 650;
        line-height: 1.72;
    }

    .menu-product-card p,
    .menu-product-card .small {
        color: #4a4036 !important;
        font-weight: 600;
        line-height: 1.68;
    }

    .menu-filter-panel {
        margin-top: -2.5rem;
        position: relative;
        z-index: 2;
    }

    .menu-filter-card {
        border: 1px solid rgba(217, 164, 65, .34);
        border-radius: 8px;
        background: rgba(255, 250, 240, .98);
        box-shadow: 0 24px 70px rgba(44, 27, 18, .14);
        padding: clamp(1rem, 3vw, 1.4rem);
    }

    .menu-product-card {
        height: 100%;
    }

    .menu-product-card .food-img {
        aspect-ratio: 16 / 11;
    }

    .menu-price {
        color: var(--green);
        font-size: 1.1rem;
        font-weight: 950;
    }
</style>

@php
    $orderUrl = auth()->check() && auth()->user()->isCustomer()
        ? route('customer.dashboard')
        : route('login');
@endphp

<section class="menu-category-hero" style="--menu-category-image: url('{{ $bannerImage }}');">
    <div class="container">
        <nav class="mb-4 small" aria-label="breadcrumb">
            <a class="text-white text-decoration-none" href="{{ route('home') }}">Trang chủ</a>
            <span class="mx-2 text-white-50">/</span>
            <a class="text-white text-decoration-none" href="{{ route('menu') }}">Thực đơn</a>
            <span class="mx-2 text-white-50">/</span>
            <span class="text-white-50">{{ $category->name }}</span>
        </nav>

        <div class="eyebrow mb-3">Danh mục món ăn</div>
        <h1 class="display-4 fw-bold mb-3">{{ $category->name }}</h1>
        <p class="lead mb-0">{{ $category->description ?: 'Khám phá các món ăn đang phục vụ trong danh mục này tại Nhà hàng Hoa Sen.' }}</p>
    </div>
</section>

<section class="menu-filter-panel">
    <div class="container">
        <div class="menu-filter-card">
            <form class="row g-3 align-items-end" method="GET" action="{{ route('menu.category', $category->menu_slug) }}">
                <div class="col-lg-5">
                    <label class="form-label fw-bold" for="menuSearch">Tìm món trong {{ $category->name }}</label>
                    <input class="form-control" id="menuSearch" name="q" value="{{ $searchTerm }}" placeholder="Nhập tên món hoặc mô tả">
                </div>
                <div class="col-md-6 col-lg-3">
                    <label class="form-label fw-bold" for="menuSort">Sắp xếp</label>
                    <select class="form-select" id="menuSort" name="sort">
                        <option value="latest" @selected($selectedSort === 'latest')>Mới nhất</option>
                        <option value="name" @selected($selectedSort === 'name')>Tên món A-Z</option>
                        <option value="price_asc" @selected($selectedSort === 'price_asc')>Giá tăng dần</option>
                        <option value="price_desc" @selected($selectedSort === 'price_desc')>Giá giảm dần</option>
                    </select>
                </div>
                <div class="col-md-6 col-lg-4 d-flex flex-wrap gap-2">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search me-1" aria-hidden="true"></i>Tìm kiếm</button>
                    <a class="btn btn-outline-primary" href="{{ route('menu.category', $category->menu_slug) }}">Xóa lọc</a>
                </div>
            </form>
        </div>
    </div>
</section>

<section class="section-pad">
    <div class="container">
        <div class="section-title">
            <div>
                <h2 class="home-section-title mb-1">{{ $category->name }}</h2>
                <p class="text-muted mb-0">{{ $products->total() }} món đang bán</p>
            </div>
            <a class="btn btn-outline-primary" href="{{ route('menu') }}">
                <i class="bi bi-grid-3x3-gap me-1" aria-hidden="true"></i>Danh mục khác
            </a>
        </div>

        <div class="d-flex flex-wrap gap-2 mb-4">
            @foreach($categories as $menuCategory)
                <a class="status-badge menu-category-chip {{ $menuCategory->id === $category->id ? 'bg-success text-white' : '' }}" href="{{ route('menu.category', $menuCategory->menu_slug) }}">
                    {{ $menuCategory->name }}: {{ $menuCategory->products_count }} món
                </a>
            @endforeach
        </div>

        <div class="row g-4">
            @forelse($products as $product)
                <div class="col-md-6 col-xl-4">
                    <article class="card food-card menu-product-card">
                        <img class="food-img" src="{{ $product->image_url }}" alt="{{ $product->name }}">
                        <div class="card-body d-flex flex-column">
                            <div class="small text-muted">{{ $product->category?->name }}</div>
                            <h3 class="h5 fw-bold mt-2">{{ $product->name }}</h3>
                            <p class="text-muted flex-grow-1">{{ Str::limit($product->description ?: 'Món ăn đang được phục vụ tại Nhà hàng Hoa Sen.', 120) }}</p>
                            <div class="d-flex justify-content-between align-items-center gap-2 mb-3">
                                <span class="menu-price">{{ number_format((float) $product->price, 0, ',', '.') }} VNĐ</span>
                                <span class="status-badge">Đang bán</span>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <a class="btn btn-outline-primary flex-grow-1" href="{{ route('products.show', $product->slug) }}">Xem chi tiết</a>
                                <a class="btn btn-primary flex-grow-1" href="{{ $orderUrl }}">Đặt món</a>
                            </div>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <div class="muted-box">Không tìm thấy món phù hợp trong danh mục này.</div>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    </div>
</section>
@endsection
