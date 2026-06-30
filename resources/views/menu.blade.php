@extends('layouts.app')

@section('title', 'Thực đơn - Nhà hàng Hoa Sen')

@section('content')
<style>
    .menu-page-hero {
        margin-top: -1.5rem;
        padding: clamp(3rem, 8vw, 6rem) 0;
        background:
            linear-gradient(90deg, rgba(17, 10, 6, .9), rgba(14, 59, 50, .72) 56%, rgba(14, 59, 50, .2)),
            url("{{ asset('images/ca-chep-sot-cai-xanh.png') }}") center / cover;
        color: #fff;
    }

    .menu-page-hero p {
        max-width: 760px;
        color: rgba(255, 255, 255, .96);
        font-weight: 650;
        line-height: 1.72;
    }

    .menu-page-hero h1 {
        color: var(--gold-soft);
        font-size: clamp(3rem, 8vw, 5.8rem);
        font-weight: 950;
        line-height: .95;
        letter-spacing: 0;
        text-shadow: 0 18px 42px rgba(0, 0, 0, .34);
    }

    .menu-page-filter {
        margin-top: -2.5rem;
        position: relative;
        z-index: 2;
    }

    .menu-page-filter-card {
        border: 1px solid rgba(217, 164, 65, .38);
        border-radius: 8px;
        background: rgba(255, 250, 240, .98);
        box-shadow: 0 24px 70px rgba(44, 27, 18, .14);
        padding: clamp(1rem, 3vw, 1.45rem);
    }

    .menu-page-filter-card label,
    .menu-page-filter-card .form-label {
        color: var(--wood-dark);
        font-weight: 900;
    }

    .menu-category-panel {
        border-radius: 8px;
        padding: 1rem;
        background: var(--green);
        color: #fff;
    }

    .menu-category-panel h2 {
        color: var(--gold-soft);
    }

    .menu-product-card {
        height: 100%;
        background: rgba(255, 250, 240, .98);
    }

    .menu-product-card h3 {
        color: var(--wood-dark);
    }

    .menu-product-card p,
    .menu-product-card .small {
        color: #4f463c !important;
        font-weight: 600;
        line-height: 1.68;
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

<section class="menu-page-hero">
    <div class="container">
        <nav class="mb-4 small" aria-label="breadcrumb">
            <a class="text-white text-decoration-none" href="{{ route('home') }}">Trang chủ</a>
            <span class="mx-2 text-white-50">/</span>
            <span class="text-white-50">Thực đơn</span>
        </nav>

        <h1 class="mb-3">THỰC ĐƠN HOA SEN</h1>
        <p class="lead mb-0">Khám phá danh mục món ăn, tìm kiếm theo tên món, lọc theo nhóm và mở trang chi tiết riêng cho từng món.</p>
    </div>
</section>

<section class="menu-page-filter">
    <div class="container">
        <div class="menu-page-filter-card">
            <form class="row g-3 align-items-end" method="GET" action="{{ route('menu') }}">
                <div class="col-lg-4">
                    <label class="form-label" for="menuSearch">Tìm kiếm món ăn</label>
                    <input class="form-control" id="menuSearch" name="q" value="{{ $searchTerm }}" placeholder="Nhập tên món, mô tả hoặc danh mục">
                </div>
                <div class="col-md-6 col-lg-3">
                    <label class="form-label" for="menuCategory">Lọc danh mục</label>
                    <select class="form-select" id="menuCategory" name="category">
                        <option value="">Tất cả danh mục</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->menu_slug }}" @selected($selectedCategory === $category->menu_slug)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 col-lg-3">
                    <label class="form-label" for="menuSort">Sắp xếp</label>
                    <select class="form-select" id="menuSort" name="sort">
                        <option value="latest" @selected($selectedSort === 'latest')>Mới nhất</option>
                        <option value="name" @selected($selectedSort === 'name')>Tên món A-Z</option>
                        <option value="price_asc" @selected($selectedSort === 'price_asc')>Giá tăng dần</option>
                        <option value="price_desc" @selected($selectedSort === 'price_desc')>Giá giảm dần</option>
                    </select>
                </div>
                <div class="col-lg-2 d-flex flex-wrap gap-2">
                    <button class="btn btn-primary flex-grow-1" type="submit">
                        <i class="bi bi-search me-1" aria-hidden="true"></i>Tìm
                    </button>
                    <a class="btn btn-outline-primary flex-grow-1" href="{{ route('menu') }}">Xóa</a>
                </div>
            </form>
        </div>
    </div>
</section>

<section class="section-pad">
    <div class="container">
        <div class="menu-category-panel mb-4">
            <div class="row align-items-center g-3">
                <div class="col-lg-4">
                    <div class="eyebrow mb-2">Danh mục món ăn</div>
                    <h2 class="h3 fw-bold mb-0">Chọn nhanh theo nhóm món</h2>
                </div>
                <div class="col-lg-8">
                    <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                        @forelse($categories as $category)
                            <a class="status-badge menu-category-chip" href="{{ route('menu.category', $category->menu_slug) }}">
                                {{ $category->name }}: {{ $category->products_count }} món
                            </a>
                        @empty
                            <span class="text-white-50">Chưa có danh mục món ăn.</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="section-title">
            <div>
                <div class="eyebrow">Danh sách món</div>
                <h2 class="mb-0">{{ $products->total() }} món đang phục vụ</h2>
            </div>
        </div>

        <div class="row g-4">
            @forelse($products as $product)
                <div class="col-md-6 col-xl-3">
                    <article class="card food-card menu-product-card">
                        <img class="food-img" src="{{ $product->image_url }}" alt="{{ $product->name }}" onerror="this.onerror=null;this.src='{{ $product->fallback_image_url }}';">
                        <div class="card-body d-flex flex-column">
                            <div class="small fw-bold">{{ $product->category?->name }}</div>
                            <h3 class="h5 fw-bold mt-2">{{ $product->name }}</h3>
                            <p class="flex-grow-1">{{ Str::limit($product->description ?: 'Món ăn đang được phục vụ tại Nhà hàng Hoa Sen.', 105) }}</p>
                            <div class="d-flex justify-content-between align-items-center gap-2 mb-3">
                                <span class="menu-price">{{ number_format((float) $product->price, 0, ',', '.') }} VNĐ</span>
                                <span class="status-badge">Đang bán</span>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <a class="btn btn-outline-primary flex-grow-1" href="{{ route('products.show', $product->slug) }}">Chi tiết</a>
                                <a class="btn btn-primary flex-grow-1" href="{{ $orderUrl }}">Đặt món</a>
                            </div>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <div class="muted-box">Không tìm thấy món phù hợp với bộ lọc hiện tại.</div>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    </div>
</section>
@endsection
