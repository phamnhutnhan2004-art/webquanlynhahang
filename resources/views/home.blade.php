@extends('layouts.app')

@section('title', 'Nhà hàng Hoa Sen')

@section('content')
<section class="hero-full">
    <div class="container">
        <div class="row align-items-end g-4">
            <div class="col-lg-7">
                <div class="eyebrow mb-3">Ẩm thực Việt cao cấp</div>
                <h1 class="display-3 fw-black fw-bold mb-3">Nhà hàng Hoa Sen</h1>
                <p class="lead mb-4">Không gian sang trọng, thực đơn chọn lọc và hệ thống đặt bàn trực tuyến dành cho những bữa ăn đáng nhớ.</p>
                <div class="d-flex flex-wrap gap-2">
                    @guest
                        <a class="btn btn-primary btn-lg" href="{{ route('register') }}">Đặt bàn nhanh</a>
                        <a class="btn btn-outline-light btn-lg" href="#mon-an">Xem thực đơn</a>
                    @else
                        <a class="btn btn-primary btn-lg" href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : (auth()->user()->isStaff() ? route('staff.dashboard') : route('customer.dashboard')) }}">Vào dashboard</a>
                        <a class="btn btn-outline-light btn-lg" href="#menu-hinh-anh">Menu nhà hàng</a>
                    @endguest
                </div>
            </div>
            <div class="col-lg-5">
                <div class="row g-3">
                    <div class="col-4">
                        <div class="card stat-card"><div class="card-body">
                            <div class="text-muted">Món bán</div>
                            <div class="stat-value">{{ $totalProducts }}</div>
                        </div></div>
                    </div>
                    <div class="col-4">
                        <div class="card stat-card"><div class="card-body">
                            <div class="text-muted">Bàn trống</div>
                            <div class="stat-value">{{ $availableTables }}</div>
                        </div></div>
                    </div>
                    <div class="col-4">
                        <div class="card stat-card"><div class="card-body">
                            <div class="text-muted">Đặt bàn</div>
                            <div class="stat-value">{{ $totalReservations }}</div>
                        </div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="gioi-thieu" class="section-pad">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <img class="gallery-img rounded-2 shadow" src="{{ asset('images/restaurant-interior.png') }}" alt="Không gian nhà hàng Hoa Sen">
            </div>
            <div class="col-lg-6">
                <div class="eyebrow mb-2">Giới thiệu</div>
                <h2 class="display-6 fw-bold mb-3">Không gian ấm cúng, phục vụ chỉn chu, món ăn đậm vị Việt.</h2>
                <p class="text-muted fs-5">Nhà hàng Hoa Sen kết hợp phong cách gỗ ấm, ánh sáng vàng và mảng xanh tự nhiên để tạo nên trải nghiệm sang trọng nhưng gần gũi. Website hỗ trợ khách xem menu, đặt bàn và theo dõi lịch đặt tiện lợi.</p>
                <div class="d-flex flex-wrap gap-2 mt-3">
                    <span class="status-badge">Hải sản tươi</span>
                    <span class="status-badge">Tiệc gia đình</span>
                    <span class="status-badge">Đặt bàn online</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="danh-muc" class="section-pad pt-0">
    <div class="container">
        <div class="section-title">
            <div>
                <div class="eyebrow">Danh mục</div>
                <h2 class="h1 mb-0">Danh mục món ăn</h2>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @forelse($categories as $category)
                <span class="status-badge">{{ $category->name }}: {{ $category->products_count }} món</span>
            @empty
                <span class="text-muted">Chưa có danh mục.</span>
            @endforelse
        </div>
    </div>
</section>

<section id="mon-an" class="section-pad pt-0">
    <div class="container">
        <div class="section-title">
            <div>
                <div class="eyebrow">Thực đơn</div>
                <h2 class="h1 mb-0">Món ăn nổi bật</h2>
            </div>
            @guest
                <a class="btn btn-outline-primary" href="{{ route('login') }}">Đăng nhập để đặt bàn</a>
            @else
                <a class="btn btn-outline-primary" href="{{ route('customer.dashboard') }}">Đặt bàn nhanh</a>
            @endguest
        </div>
        <div class="row g-4">
            @forelse($products as $product)
                <div class="col-md-6 col-xl-4">
                    <article class="card food-card h-100">
                        <img class="food-img" src="{{ $product->image_url }}" alt="{{ $product->name }}">
                        <div class="card-body d-flex flex-column">
                            <div class="small text-muted">{{ $product->category?->name }}</div>
                            <h3 class="h5 mt-2">{{ $product->name }}</h3>
                            <p class="text-muted flex-grow-1">{{ $product->description }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <strong class="gold-text">{{ number_format((float) $product->price) }} VNĐ</strong>
                                <span class="status-badge">{{ $product->status === 'available' ? 'Đang bán' : $product->status }}</span>
                            </div>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12"><div class="muted-box">Chưa có sản phẩm trong menu.</div></div>
            @endforelse
        </div>
    </div>
</section>

<section id="menu-hinh-anh" class="section-pad pt-0">
    <div class="container">
        <div class="section-title">
            <div>
                <div class="eyebrow">Menu Nhà Hàng</div>
                <h2 class="h1 mb-0">Menu hình ảnh</h2>
            </div>
        </div>
        <div class="row g-4">
            @forelse($menuGalleries as $menu)
                <div class="col-md-6 col-xl-3">
                    <article class="card media-card h-100">
                        @if(str_ends_with(strtolower($menu->image), '.pdf'))
                            <div class="muted-box h-100 d-flex flex-column justify-content-center">
                                <h3 class="h5">{{ $menu->title }}</h3>
                                <p class="text-muted">{{ $menu->description }}</p>
                                <a class="btn btn-outline-primary" href="{{ $menu->image_url }}" target="_blank">Xem PDF</a>
                            </div>
                        @else
                            <img class="media-img" src="{{ $menu->image_url }}" alt="{{ $menu->title }}">
                            <div class="card-body">
                                <h3 class="h5">{{ $menu->title }}</h3>
                                <p class="text-muted mb-0">{{ $menu->description }}</p>
                            </div>
                        @endif
                    </article>
                </div>
            @empty
                <div class="col-12"><div class="muted-box">Chưa có menu hình ảnh.</div></div>
            @endforelse
        </div>
    </div>
</section>

<section id="hinh-anh" class="section-pad pt-0">
    <div class="container">
        <div class="section-title">
            <div>
                <div class="eyebrow">Không gian</div>
                <h2 class="h1 mb-0">Hình ảnh nhà hàng</h2>
            </div>
        </div>
        <div class="row g-3">
            @forelse($galleryImages as $image)
                <div class="col-md-6 col-xl-4">
                    <article class="card media-card">
                        <img class="gallery-img" src="{{ $image->image_url }}" alt="{{ $image->title }}">
                        <div class="card-body"><strong>{{ $image->title }}</strong></div>
                    </article>
                </div>
            @empty
                <div class="col-12"><div class="muted-box">Chưa có ảnh nhà hàng.</div></div>
            @endforelse
        </div>
    </div>
</section>

<section id="lien-he" class="section-pad pt-0">
    <div class="container">
        <div class="contact-band">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <div class="eyebrow mb-2">Liên hệ</div>
                    <h2 class="h1 fw-bold mb-3">Sẵn sàng cho bữa ăn tiếp theo?</h2>
                    <p class="fs-5 mb-2">Địa chỉ: 100k Đ. Võ Văn Kiệt, Phường Long Châu, Vĩnh Long</p>
                    <p class="fs-5 mb-0">Hotline: 0789661781</p>
                </div>
                <div class="col-lg-4 d-grid">
                    @guest
                        <a class="btn btn-primary btn-lg" href="{{ route('register') }}">Đặt bàn ngay</a>
                    @else
                        <a class="btn btn-primary btn-lg" href="{{ route('customer.dashboard') }}">Mở trang đặt bàn</a>
                    @endguest
                </div>
            </div>
        </div>

        <div class="store-map-section">
            <div class="store-contact-info">
                <h2 class="store-contact-title">Thông Tin Liên Hệ</h2>

                <div class="store-contact-list">
                    <div class="store-contact-item">
                        <i class="bi bi-telephone-fill" aria-hidden="true"></i>
                        <div>Hotline: <a href="tel:0789661781">0789661781</a></div>
                    </div>
                    <div class="store-contact-item">
                        <i class="bi bi-envelope-fill" aria-hidden="true"></i>
                        <div>Email: <a href="mailto:phamnhutnhan2004@gmail.com">phamnhutnhan2004@gmail.com</a></div>
                    </div>
                    <div class="store-contact-item">
                        <i class="bi bi-globe2" aria-hidden="true"></i>
                        <div>Website: <a href="https://amthucaosen.com/" target="_blank" rel="noopener">https://amthucaosen.com/</a></div>
                    </div>
                    <div class="store-contact-item">
                        <i class="bi bi-geo-alt-fill" aria-hidden="true"></i>
                        <div>Địa chỉ: 100k Đ. Võ Văn Kiệt, Phường Long Châu, Vĩnh Long.</div>
                    </div>
                </div>

                <div class="store-socials" aria-label="Mạng xã hội">
                    <span class="store-social-icon"><i class="bi bi-facebook" aria-hidden="true"></i></span>
                    <span class="store-social-icon"><i class="bi bi-instagram" aria-hidden="true"></i></span>
                    <span class="store-social-icon"><i class="bi bi-tiktok" aria-hidden="true"></i></span>
                    <span class="store-social-icon"><i class="bi bi-youtube" aria-hidden="true"></i></span>
                </div>
            </div>

            <div class="section-title align-items-center mb-3">
                <div>
                    <div class="eyebrow">Vị trí</div>
                    <h2 class="h1 mb-0 text-white">Bản đồ cửa hàng</h2>
                </div>
            </div>

            <div class="store-map-shell">
                <div class="ratio ratio-21x9 store-map-frame">
                    <iframe
                        src="https://www.google.com/maps?q=%C3%82m%20Th%E1%BB%B1c%20Ao%20Sen%20100K%20%C4%90.%20V%C3%B5%20V%C4%83n%20Ki%E1%BB%87t%2C%20Long%20Ch%C3%A2u%2C%20V%C4%A9nh%20Long%2C%20Vi%E1%BB%87t%20Nam&output=embed"
                        title="Bản đồ cửa hàng"
                        allowfullscreen
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
