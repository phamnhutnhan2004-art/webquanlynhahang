@extends('layouts.app')

@section('title', 'Nhà hàng Hoa Sen')

@section('content')
<style>
    .home-summary-page {
        color: var(--wood-dark);
    }

    .home-summary-page .summary-lead {
        color: rgba(255, 255, 255, .96);
        font-weight: 650;
    }

    .home-intro-band,
    .service-preview-band,
    .review-band {
        background:
            linear-gradient(135deg, rgba(14, 59, 50, .96), rgba(44, 27, 18, .9)),
            url("{{ asset('images/restaurant-interior.png') }}") center / cover;
        color: #fff;
        border-radius: 8px;
        padding: clamp(1.5rem, 4vw, 3rem);
        box-shadow: 0 26px 70px rgba(44, 27, 18, .16);
    }

    .home-summary-page .home-section-title {
        color: var(--gold-soft);
    }

    .home-summary-page .section-title h2 {
        color: var(--wood-dark);
    }

    .home-summary-page .section-title p,
    .home-summary-page .card p,
    .home-summary-page .card .small {
        color: #4f463c !important;
    }

    .home-kicker-large {
        color: var(--gold-soft) !important;
        font-size: clamp(2.1rem, 4vw, 3.45rem);
        font-weight: 950;
        line-height: 1.04;
        letter-spacing: 0;
        text-transform: none;
    }

    .home-intro-title {
        font-size: clamp(1.8rem, 3vw, 2.65rem);
        line-height: 1.12;
    }

    .home-feature-title {
        color: var(--gold-soft) !important;
        font-size: clamp(1.45rem, 2.5vw, 2.15rem);
        font-weight: 900;
        line-height: 1.15;
    }

    .home-feature-subtitle {
        color: #fff !important;
        font-weight: 700;
    }

    .home-section-kicker {
        color: var(--gold-soft) !important;
        font-size: clamp(1.65rem, 3vw, 2.65rem);
        font-weight: 950;
        line-height: 1.06;
        letter-spacing: 0;
        text-transform: none;
    }

    .home-section-heading {
        color: var(--gold-soft) !important;
        font-size: clamp(1.4rem, 2.3vw, 2rem);
        font-weight: 900;
        line-height: 1.15;
    }

    .home-section-copy {
        color: #fff !important;
        font-weight: 700;
    }

    .home-intro-band p,
    .service-preview-band p,
    .review-band p {
        color: rgba(255, 255, 255, .94) !important;
        font-weight: 650;
        line-height: 1.72;
    }

    .home-service-item,
    .home-review-item {
        color: #fff;
    }

    .home-service-item p,
    .home-review-item p {
        color: rgba(255, 255, 255, .94) !important;
    }

    .home-feature-card {
        height: 100%;
        border-color: rgba(217, 164, 65, .36);
        background: rgba(255, 250, 240, .98);
    }

    .home-service-item,
    .home-review-item {
        min-height: 100%;
        border: 1px solid rgba(246, 223, 157, .32);
        border-radius: 8px;
        padding: 1rem;
        background: rgba(255, 255, 255, .08);
    }

    .home-service-item i,
    .home-review-item i {
        color: var(--gold-soft);
        font-size: 1.6rem;
    }

    .home-gallery-card {
        position: relative;
        overflow: hidden;
        border-radius: 8px;
        border: 1px solid rgba(217, 164, 65, .38);
        background: var(--green);
    }

    .home-gallery-card img {
        width: 100%;
        aspect-ratio: 16 / 11;
        object-fit: cover;
        display: block;
        transition: transform .25s ease;
    }

    .home-gallery-card:hover img {
        transform: scale(1.04);
    }

    .home-gallery-caption {
        position: absolute;
        left: .75rem;
        right: .75rem;
        bottom: .75rem;
        padding: .55rem .7rem;
        border-radius: 8px;
        background: rgba(14, 59, 50, .9);
        color: #fff;
        font-weight: 900;
    }
</style>

<div class="home-summary-page">
    <section class="hero-full">
        <div class="container">
            <div class="row align-items-end g-4">
                <div class="col-lg-7">
                    <div class="eyebrow hero-eyebrow">Ẩm thực Việt cao cấp</div>
                    <h1 class="hero-title">NHÀ HÀNG HOA SEN</h1>
                    <p class="lead hero-description summary-lead">Không gian sân vườn ấm cúng, món Việt đậm vị và hệ thống đặt bàn trực tuyến cho những bữa ăn gia đình, gặp mặt bạn bè và tiệc nhóm.</p>
                    <div class="d-flex flex-wrap gap-3 hero-actions">
                        <a class="btn btn-primary btn-lg" href="{{ route('reservations.create') }}">Đặt bàn nhanh</a>
                        <a class="btn btn-outline-light btn-lg" href="{{ route('menu') }}">Xem thực đơn</a>
                        <a class="btn btn-outline-light btn-lg" href="{{ route('contact') }}">Liên hệ</a>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="row g-2 hero-stats justify-content-lg-end">
                        <div class="col-4 col-lg-auto">
                            <div class="card stat-card hero-stat-card hero-stat-card-gold"><div class="card-body">
                                <div class="hero-stat-label"><i class="bi bi-egg-fried" aria-hidden="true"></i>Món bán</div>
                                <div class="stat-value">{{ $totalProducts }}</div>
                            </div></div>
                        </div>
                        <div class="col-4 col-lg-auto">
                            <div class="card stat-card hero-stat-card hero-stat-card-green"><div class="card-body">
                                <div class="hero-stat-label"><i class="bi bi-grid-3x3-gap" aria-hidden="true"></i>Bàn trống</div>
                                <div class="stat-value">{{ $availableTables }}</div>
                            </div></div>
                        </div>
                        <div class="col-4 col-lg-auto">
                            <div class="card stat-card hero-stat-card hero-stat-card-coral"><div class="card-body">
                                <div class="hero-stat-label"><i class="bi bi-calendar-check" aria-hidden="true"></i>Đặt bàn</div>
                                <div class="stat-value">{{ $totalReservations }}</div>
                            </div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-pad pt-0">
        <div class="container">
            <div class="home-intro-band">
                <div class="row align-items-center g-4">
                    <div class="col-lg-7">
                        <div class="eyebrow home-kicker-large mb-2">Giới thiệu tổng quan</div>
                        <h2 class="home-section-title home-intro-title mb-3">Một điểm hẹn ấm cúng cho món Việt và hải sản tươi.</h2>
                        <p class="fs-5 mb-0">Hoa Sen tập trung vào nguyên liệu tươi, hương vị quen thuộc và cách phục vụ chỉn chu. Trang chủ chỉ giới thiệu nhanh, còn nội dung đầy đủ được tách ở từng trang riêng.</p>
                    </div>
                    <div class="col-lg-5">
                        <img class="gallery-img rounded-2 shadow" src="{{ asset('images/restaurant-interior.png') }}" alt="Không gian nhà hàng Hoa Sen">
                    </div>
                    <div class="col-12">
                        <a class="btn btn-primary btn-lg" href="{{ route('about') }}">Xem chi tiết</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-pad pt-0">
        <div class="container">
            <div class="section-title">
                <div>
                    <div class="eyebrow home-kicker-large">Món nổi bật</div>
                    <h2 class="home-feature-title mb-0">Gợi ý nhanh từ thực đơn</h2>
                </div>
                <a class="btn btn-outline-primary" href="{{ route('menu') }}">Xem tất cả</a>
            </div>

            <div class="row g-4">
                @forelse($products as $product)
                    <div class="col-md-6 col-xl-4">
                        <article class="card food-card home-feature-card">
                            <img class="food-img" src="{{ $product->image_url }}" alt="{{ $product->name }}" onerror="this.onerror=null;this.src='{{ $product->fallback_image_url }}';">
                            <div class="card-body d-flex flex-column">
                                <div class="small fw-bold">{{ $product->category?->name }}</div>
                                <h3 class="h5 fw-bold mt-2">{{ $product->name }}</h3>
                                <p class="flex-grow-1">{{ Str::limit($product->description ?: 'Món ăn đang được phục vụ tại Nhà hàng Hoa Sen.', 95) }}</p>
                                <div class="d-flex justify-content-between align-items-center gap-2">
                                    <strong class="gold-text">{{ number_format((float) $product->price, 0, ',', '.') }} VNĐ</strong>
                                    <a class="btn btn-outline-primary btn-sm" href="{{ route('products.show', $product->slug) }}">Chi tiết</a>
                                </div>
                            </div>
                        </article>
                    </div>
                @empty
                    <div class="col-12"><div class="muted-box">Chưa có món nổi bật để hiển thị.</div></div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="section-pad pt-0">
        <div class="container">
            <div class="service-preview-band">
                <div class="section-title align-items-start">
                    <div>
                        <div class="eyebrow home-section-kicker">Dịch vụ</div>
                        <h2 class="home-section-heading mb-0">Phục vụ linh hoạt cho nhiều nhu cầu.</h2>
                    </div>
                    <a class="btn btn-primary" href="{{ route('home-parties.show') }}">Đặt tiệc tại nhà</a>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="home-service-item">
                            <i class="bi bi-calendar2-check" aria-hidden="true"></i>
                            <h3 class="h5 fw-bold mt-3">Đặt bàn online</h3>
                            <p class="mb-0">Gửi yêu cầu giữ bàn, chọn thời gian và số khách ngay trên website.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="home-service-item">
                            <i class="bi bi-house-heart" aria-hidden="true"></i>
                            <h3 class="h5 fw-bold mt-3">Tiệc tại nhà</h3>
                            <p class="mb-0">Nhà hàng hỗ trợ chuẩn bị món, nhân sự và thực đơn cho buổi tiệc riêng.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="home-service-item">
                            <i class="bi bi-chat-dots" aria-hidden="true"></i>
                            <h3 class="h5 fw-bold mt-3">Chatbot hỗ trợ</h3>
                            <p class="mb-0">Hỏi nhanh về giờ mở cửa, món ăn, đặt bàn và các thông tin cần thiết.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-pad pt-0">
        <div class="container">
            <div class="section-title">
                <div>
                    <div class="eyebrow home-section-kicker">Hình ảnh nổi bật</div>
                    <h2 class="home-section-heading mb-0">Một vài khoảnh khắc tại Hoa Sen</h2>
                </div>
                <a class="btn btn-outline-primary" href="{{ route('gallery') }}">Xem thêm</a>
            </div>

            <div class="row g-3">
                @forelse($galleryImages as $image)
                    <div class="col-md-6 col-xl-4">
                        <a class="home-gallery-card d-block" href="{{ route('gallery') }}">
                            <img src="{{ $image->image_url }}" alt="{{ $image->title }}">
                            <span class="home-gallery-caption">{{ $image->title }}</span>
                        </a>
                    </div>
                @empty
                    <div class="col-md-6 col-xl-4">
                        <a class="home-gallery-card d-block" href="{{ route('gallery') }}">
                            <img src="{{ asset('images/hero-restaurant.png') }}" alt="Không gian Nhà hàng Hoa Sen">
                            <span class="home-gallery-caption">Không gian Hoa Sen</span>
                        </a>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <a class="home-gallery-card d-block" href="{{ route('gallery') }}">
                            <img src="{{ asset('images/restaurant-interior.png') }}" alt="Khu phục vụ nhà hàng">
                            <span class="home-gallery-caption">Khu phục vụ ấm cúng</span>
                        </a>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <a class="home-gallery-card d-block" href="{{ route('gallery') }}">
                            <img src="{{ asset('images/ca-chep-sot-cai-xanh.png') }}" alt="Món ăn tại Hoa Sen">
                            <span class="home-gallery-caption">Món ngon nổi bật</span>
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="section-pad pt-0">
        <div class="container">
            <div class="review-band">
                <div class="section-title align-items-start">
                    <div>
                        <div class="eyebrow home-section-kicker">Đánh giá khách hàng</div>
                        <h2 class="home-section-heading mb-0">Cảm nhận sau bữa ăn</h2>
                    </div>
                    <a class="btn btn-primary" href="{{ route('contact') }}">Gửi liên hệ</a>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="home-review-item">
                            <i class="bi bi-quote" aria-hidden="true"></i>
                            <p class="mt-2">Không gian thoáng, món lên nhanh và nhân viên hỗ trợ đặt bàn rất chu đáo.</p>
                            <strong>Gia đình chị Lan</strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="home-review-item">
                            <i class="bi bi-quote" aria-hidden="true"></i>
                            <p class="mt-2">Món hải sản tươi, giá rõ ràng, phù hợp cho nhóm bạn và tiệc nhỏ.</p>
                            <strong>Anh Minh</strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="home-review-item">
                            <i class="bi bi-quote" aria-hidden="true"></i>
                            <p class="mt-2">Website dễ xem thực đơn, bấm đặt bàn nhanh và có chatbot hỏi thông tin tiện lợi.</p>
                            <strong>Chị Hương</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
