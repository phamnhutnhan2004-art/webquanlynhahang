@extends('layouts.app')

@section('title', 'Hình ảnh - Nhà hàng Hoa Sen')

@section('content')
<style>
    .gallery-page-hero {
        margin-top: -1.5rem;
        padding: clamp(3rem, 8vw, 6rem) 0;
        background:
            linear-gradient(90deg, rgba(17, 10, 6, .9), rgba(14, 59, 50, .74) 56%, rgba(14, 59, 50, .2)),
            url("{{ asset('images/hero-restaurant.png') }}") center / cover;
        color: #fff;
    }

    .gallery-page-hero h1 {
        color: var(--gold-soft);
        font-size: clamp(3rem, 8vw, 5.8rem);
        font-weight: 950;
        line-height: .95;
        letter-spacing: 0;
        text-shadow: 0 18px 42px rgba(0, 0, 0, .34);
    }

    .gallery-page-hero p {
        max-width: 760px;
        color: rgba(255, 255, 255, .96);
        font-weight: 650;
        line-height: 1.72;
    }

    .gallery-band {
        background:
            linear-gradient(135deg, rgba(14, 59, 50, .96), rgba(44, 27, 18, .9)),
            url("{{ asset('images/restaurant-interior.png') }}") center / cover;
        color: #fff;
        border-radius: 8px;
        padding: clamp(1.25rem, 4vw, 2rem);
    }

    .gallery-band h2,
    .gallery-band .eyebrow {
        color: var(--gold-soft);
    }

    .gallery-album-card,
    .gallery-photo-card,
    .gallery-video-card {
        height: 100%;
        border-color: rgba(217, 164, 65, .36);
        background: rgba(255, 250, 240, .98);
    }

    .gallery-album-card h3,
    .gallery-photo-card h3,
    .gallery-video-card h3 {
        color: var(--wood-dark);
    }

    .gallery-album-card p,
    .gallery-photo-card p,
    .gallery-video-card p {
        color: #4a4036;
        font-weight: 600;
        line-height: 1.68;
    }

    .gallery-band p,
    .gallery-video-placeholder p {
        color: rgba(255, 255, 255, .94) !important;
        font-weight: 650;
        line-height: 1.7;
    }

    .gallery-photo-card img,
    .gallery-album-card img {
        width: 100%;
        aspect-ratio: 16 / 10;
        object-fit: cover;
        display: block;
    }

    .gallery-video-placeholder {
        display: grid;
        place-items: center;
        min-height: 260px;
        background:
            linear-gradient(135deg, rgba(14, 59, 50, .94), rgba(44, 27, 18, .88)),
            url("{{ asset('images/restaurant-interior.png') }}") center / cover;
        color: #fff;
        text-align: center;
        padding: 2rem;
    }

    .gallery-video-placeholder i {
        color: var(--gold-soft);
        font-size: 3rem;
    }
</style>

<section class="gallery-page-hero">
    <div class="container">
        <nav class="mb-4 small" aria-label="breadcrumb">
            <a class="text-white text-decoration-none" href="{{ route('home') }}">Trang chủ</a>
            <span class="mx-2 text-white-50">/</span>
            <span class="text-white-50">Hình ảnh</span>
        </nav>


        <h1 class="mb-3">NHÀ HÀNG HOA SEN</h1>
        <p class="lead mb-0">Tổng hợp album thực đơn, gallery không gian và khu vực video giới thiệu nhà hàng.</p>
    </div>
</section>

<section class="section-pad">
    <div class="container">
        <div class="gallery-band mb-4">
            <div class="row align-items-center g-3">
                <div class="col-lg-8">

                    <h2 class="h1 fw-bold mb-2">Menu hình ảnh và tài liệu món ăn</h2>

                </div>
                <div class="col-lg-4 text-lg-end">
                    <a class="btn btn-primary" href="{{ route('menu') }}">Mở thực đơn</a>
                </div>
            </div>
        </div>

        <div class="row g-4">
            @forelse($menuGalleries as $menu)
                <div class="col-md-6 col-xl-3">
                    <article class="card gallery-album-card">
                        @if(str_ends_with(strtolower($menu->image), '.pdf'))
                            <div class="gallery-video-placeholder">
                                <div>
                                    <i class="bi bi-file-earmark-pdf" aria-hidden="true"></i>
                                    <h3 class="h5 fw-bold mt-3">{{ $menu->title }}</h3>
                                    <p>{{ $menu->description }}</p>
                                    <a class="btn btn-primary" href="{{ $menu->image_url }}" target="_blank">Xem PDF</a>
                                </div>
                            </div>
                        @else
                            <img src="{{ $menu->image_url }}" alt="{{ $menu->title }}" onerror="this.onerror=null;this.src='{{ asset('images/restaurant-interior.png') }}';">
                            <div class="card-body">
                                <h3 class="h5 fw-bold">{{ $menu->title }}</h3>
                                <p class="mb-0">{{ $menu->description ?: 'Album hình ảnh món ăn tại Nhà hàng Hoa Sen.' }}</p>
                            </div>
                        @endif
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <div class="muted-box">Chưa có album hình ảnh được tải lên.</div>
                </div>
            @endforelse
        </div>
    </div>
</section>

<section class="section-pad pt-0">
    <div class="container">
        <div class="section-title">
            <div>
                <div class="eyebrow">Gallery</div>
                <h2 class="mb-0">Không gian và món ăn</h2>
                <p class="text-muted mb-0">Danh sách ảnh đầy đủ được quản lý riêng trong hệ thống.</p>
            </div>
        </div>

        <div class="row g-4">
            @forelse($galleryImages as $image)
                <div class="col-md-6 col-xl-4">
                    <article class="card gallery-photo-card">
                        <img src="{{ $image->image_url }}" alt="{{ $image->title }}">
                        <div class="card-body">
                            <h3 class="h5 fw-bold mb-0">{{ $image->title }}</h3>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-md-6 col-xl-4">
                    <article class="card gallery-photo-card">
                        <img src="{{ asset('images/hero-restaurant.png') }}" alt="Không gian Nhà hàng Hoa Sen">
                        <div class="card-body"><h3 class="h5 fw-bold mb-0">Không gian Nhà hàng Hoa Sen</h3></div>
                    </article>
                </div>
                <div class="col-md-6 col-xl-4">
                    <article class="card gallery-photo-card">
                        <img src="{{ asset('images/restaurant-interior.png') }}" alt="Khu phục vụ nhà hàng">
                        <div class="card-body"><h3 class="h5 fw-bold mb-0">Khu phục vụ ấm cúng</h3></div>
                    </article>
                </div>
                <div class="col-md-6 col-xl-4">
                    <article class="card gallery-photo-card">
                        <img src="{{ asset('images/ga-xao-cay.png') }}" alt="Món ăn tại Hoa Sen">
                        <div class="card-body"><h3 class="h5 fw-bold mb-0">Món ngon nổi bật</h3></div>
                    </article>
                </div>
            @endforelse
        </div>
    </div>
</section>

<section class="section-pad pt-0">
    <div class="container">
        <div class="section-title">
            <div>
                <div class="eyebrow">Video</div>
                <h2 class="mb-0">Video giới thiệu</h2>
                <p class="text-muted mb-0">Khu vực dành riêng cho video nhà hàng, tách khỏi trang chủ.</p>
            </div>
        </div>

        <div class="card gallery-video-card">
            <div class="gallery-video-placeholder">
                <div>
                    <i class="bi bi-play-circle-fill" aria-hidden="true"></i>
                    <h3 class="h4 fw-bold mt-3">Video Nhà hàng Hoa Sen</h3>
                    <p class="mb-0">Có thể bổ sung video thực tế hoặc đường dẫn YouTube từ phần quản trị trong bước tiếp theo.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
