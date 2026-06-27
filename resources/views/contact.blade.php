@extends('layouts.app')

@section('title', 'Liên hệ - Nhà hàng Hoa Sen')

@section('content')
<style>
    .contact-page {
        padding-bottom: 3.5rem;
    }

    .contact-hero {
        margin-top: -1.5rem;
        padding: clamp(3rem, 8vw, 5.5rem) 0 clamp(4rem, 10vw, 6rem);
        background:
            linear-gradient(135deg, rgba(14, 59, 50, .9), rgba(44, 27, 18, .76)),
            url("{{ asset('images/restaurant-interior.png') }}") center / cover;
        color: #fff;
    }

    .contact-hero p {
        max-width: 720px;
        color: rgba(255, 255, 255, .84);
    }

    .contact-panel {
        margin-top: clamp(-3rem, -5vw, -2rem);
        position: relative;
        z-index: 2;
    }

    .contact-card {
        border: 1px solid rgba(217, 164, 65, .34);
        border-radius: 8px;
        background: rgba(255, 250, 240, .97);
        box-shadow: 0 28px 80px rgba(44, 27, 18, .14);
        overflow: hidden;
    }

    .contact-info-side {
        min-height: 100%;
        padding: clamp(1.3rem, 4vw, 2.25rem);
        background:
            linear-gradient(180deg, rgba(14, 59, 50, .97), rgba(44, 27, 18, .9)),
            url("{{ asset('images/hero-restaurant.png') }}") center / cover;
        color: #fff;
    }

    .contact-info-item {
        display: grid;
        grid-template-columns: 42px 1fr;
        gap: .85rem;
        padding: 1rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, .15);
    }

    .contact-info-item:last-child {
        border-bottom: 0;
    }

    .contact-info-icon,
    .footer-social {
        display: inline-grid;
        place-items: center;
        border-radius: 50%;
        background: var(--gold);
        color: var(--green);
    }

    .contact-info-icon {
        width: 42px;
        aspect-ratio: 1;
        font-size: 1.12rem;
    }

    .contact-info-item a,
    .contact-footer a {
        color: inherit;
        text-decoration: none;
    }

    .contact-form-area {
        padding: clamp(1.3rem, 4vw, 2.25rem);
    }

    .contact-form-area .form-label {
        color: var(--wood-dark);
        font-weight: 800;
    }

    .contact-control {
        min-height: 46px;
        border-color: rgba(90, 52, 30, .22);
        border-radius: 8px;
        background-color: #fff;
    }

    textarea.contact-control {
        min-height: 148px;
        resize: vertical;
    }

    .contact-control:focus {
        border-color: var(--gold);
        box-shadow: 0 0 0 .2rem rgba(217, 164, 65, .16);
    }

    .contact-map-card {
        border: 1px solid rgba(217, 164, 65, .38);
        border-radius: 8px;
        padding: .55rem;
        background: rgba(255, 250, 240, .95);
        box-shadow: 0 24px 64px rgba(44, 27, 18, .1);
    }

    .contact-map-card iframe {
        width: 100%;
        min-height: clamp(310px, 45vw, 520px);
        border: 0;
        border-radius: 8px;
        display: block;
    }

    .contact-footer {
        margin-top: 2rem;
        border-radius: 8px;
        background:
            linear-gradient(135deg, rgba(14, 59, 50, .98), rgba(44, 27, 18, .92)),
            url("{{ asset('images/hero-restaurant.png') }}") center / cover;
        color: #fff;
        overflow: hidden;
    }

    .contact-footer-inner {
        padding: clamp(1.5rem, 4vw, 2.5rem);
    }

    .contact-logo {
        display: inline-flex;
        align-items: center;
        gap: .8rem;
        color: var(--gold-soft);
    }

    .contact-logo-mark {
        display: inline-grid;
        place-items: center;
        width: 58px;
        aspect-ratio: 1;
        border: 2px solid rgba(217, 164, 65, .74);
        border-radius: 50%;
        font-size: 1.8rem;
        color: var(--gold-soft);
    }

    .footer-socials {
        display: flex;
        flex-wrap: wrap;
        gap: .55rem;
    }

    .footer-social {
        width: 40px;
        aspect-ratio: 1;
        transition: transform .2s ease, background .2s ease;
    }

    .footer-social:hover {
        transform: translateY(-2px);
        background: var(--gold-soft);
    }

    .contact-footer-bottom {
        border-top: 1px solid rgba(255, 255, 255, .14);
        padding: .9rem clamp(1.5rem, 4vw, 2.5rem);
        color: rgba(255, 255, 255, .68);
        font-size: .9rem;
    }

    @media (max-width: 991.98px) {
        .contact-hero {
            margin-top: -1rem;
        }
    }

    @media (max-width: 575.98px) {
        .contact-info-item {
            grid-template-columns: 38px 1fr;
        }

        .contact-info-icon {
            width: 38px;
        }
    }
</style>

<div class="contact-page">
    <section class="contact-hero">
        <div class="container">
            <nav class="mb-4 small" aria-label="breadcrumb">
                <a class="text-white text-decoration-none" href="{{ route('home') }}">Trang chủ</a>
                <span class="mx-2 text-white-50">/</span>
                <span class="text-white-50">Liên hệ</span>
            </nav>

            <div class="eyebrow mb-3">Kết nối với nhà hàng</div>
            <h1 class="display-4 fw-bold mb-3">LIÊN HỆ NHÀ HÀNG HOA SEN</h1>
            <p class="lead mb-0">Gửi yêu cầu đặt bàn, góp ý dịch vụ hoặc trao đổi về tiệc nhóm. Đội ngũ Hoa Sen sẽ phản hồi nhanh để chuẩn bị trải nghiệm chu đáo nhất cho anh/chị.</p>
        </div>
    </section>

    <section class="contact-panel">
        <div class="container">
            <div class="contact-card">
                <div class="row g-0">
                    <div class="col-lg-5">
                        <aside class="contact-info-side">
                            <div class="eyebrow mb-2">Thông tin liên hệ</div>
                            <h2 class="h1 fw-bold mb-3">Luôn sẵn sàng phục vụ</h2>
                            <p class="text-white-50 mb-4">Anh/chị có thể gọi trực tiếp hoặc gửi biểu mẫu để nhà hàng hỗ trợ giữ bàn, tư vấn món ăn và chuẩn bị không gian phù hợp.</p>

                            <div class="contact-info-item">
                                <span class="contact-info-icon"><i class="bi bi-telephone-fill" aria-hidden="true"></i></span>
                                <div>
                                    <strong class="d-block">Số điện thoại</strong>
                                    <a href="tel:0789661781">0789661781</a>
                                </div>
                            </div>
                            <div class="contact-info-item">
                                <span class="contact-info-icon"><i class="bi bi-envelope-fill" aria-hidden="true"></i></span>
                                <div>
                                    <strong class="d-block">Email</strong>
                                    <a href="mailto:phamnhutnhan2004@gmail.com">phamnhutnhan2004@gmail.com</a>
                                </div>
                            </div>
                            <div class="contact-info-item">
                                <span class="contact-info-icon"><i class="bi bi-globe2" aria-hidden="true"></i></span>
                                <div>
                                    <strong class="d-block">Website</strong>
                                    <a href="https://nhahanghoasen.com/" target="_blank" rel="noopener">nhahanghoasen.com</a>
                                </div>
                            </div>
                            <div class="contact-info-item">
                                <span class="contact-info-icon"><i class="bi bi-geo-alt-fill" aria-hidden="true"></i></span>
                                <div>
                                    <strong class="d-block">Địa chỉ nhà hàng</strong>
                                    <span>100k Đ. Võ Văn Kiệt, Phường Long Châu, Vĩnh Long.</span>
                                </div>
                            </div>
                        </aside>
                    </div>

                    <div class="col-lg-7">
                        <div class="contact-form-area">
                            <div class="section-title mb-3">
                                <div>
                                    <div class="eyebrow">Gửi liên hệ</div>
                                    <h2 class="h1 mb-0">Nhà hàng có thể hỗ trợ gì cho anh/chị?</h2>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('contact.store') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label" for="fullName">Họ và tên</label>
                                        <input class="form-control contact-control @error('full_name') is-invalid @enderror" id="fullName" name="full_name" value="{{ old('full_name') }}" autocomplete="name" required>
                                        @error('full_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="phone">Số điện thoại</label>
                                        <input class="form-control contact-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" autocomplete="tel" required>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="address">Địa chỉ</label>
                                        <input class="form-control contact-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address') }}" autocomplete="street-address">
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="email">Email</label>
                                        <input class="form-control contact-control @error('email') is-invalid @enderror" id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label" for="message">Nội dung</label>
                                        <textarea class="form-control contact-control @error('message') is-invalid @enderror" id="message" name="message" required>{{ old('message') }}</textarea>
                                        @error('message')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label" for="attachment">Đính kèm hình ảnh <span class="text-muted fw-normal">(tùy chọn)</span></label>
                                        <input class="form-control contact-control @error('attachment') is-invalid @enderror" id="attachment" type="file" name="attachment" accept="image/*">
                                        @error('attachment')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap gap-2 mt-4">
                                    <button class="btn btn-primary px-4" type="submit">
                                        <i class="bi bi-send-fill me-1" aria-hidden="true"></i> Gửi
                                    </button>
                                    <button class="btn btn-outline-primary px-4" type="reset">
                                        <i class="bi bi-arrow-counterclockwise me-1" aria-hidden="true"></i> Nhập lại
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="contact-footer">
                <div class="contact-footer-inner">
                    <div class="row g-4 align-items-start">
                        <div class="col-lg-4">
                            <div class="contact-logo mb-3">
                                <span class="contact-logo-mark"><i class="bi bi-flower1" aria-hidden="true"></i></span>
                                <div>
                                    <strong class="d-block h4 mb-0">Nhà hàng Hoa Sen</strong>
                                    <span class="text-white-50">Ẩm thực sân vườn cao cấp</span>
                                </div>
                            </div>
                            <p class="text-white-50 mb-0">Không gian xanh thoáng mát, hải sản tươi sống và món đồng quê miền Tây được phục vụ trong phong cách chỉn chu, thân thiện.</p>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <h3 class="h5 fw-bold text-warning mb-3">Thông tin liên hệ</h3>
                            <div class="d-grid gap-2 text-white-50">
                                <span><i class="bi bi-telephone-fill text-warning me-2" aria-hidden="true"></i>0789661781</span>
                                <span><i class="bi bi-envelope-fill text-warning me-2" aria-hidden="true"></i>phamnhutnhan2004@gmail.com</span>
                                <span><i class="bi bi-geo-alt-fill text-warning me-2" aria-hidden="true"></i>100k Đ. Võ Văn Kiệt, Phường Long Châu, Vĩnh Long</span>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <h3 class="h5 fw-bold text-warning mb-3">Kết nối với chúng tôi</h3>
                            <div class="footer-socials" aria-label="Mạng xã hội">
                                <span class="footer-social" role="img" aria-label="Facebook"><i class="bi bi-facebook" aria-hidden="true"></i></span>
                                <span class="footer-social" role="img" aria-label="Instagram"><i class="bi bi-instagram" aria-hidden="true"></i></span>
                                <span class="footer-social" role="img" aria-label="TikTok"><i class="bi bi-tiktok" aria-hidden="true"></i></span>
                                <span class="footer-social" role="img" aria-label="YouTube"><i class="bi bi-youtube" aria-hidden="true"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="contact-footer-bottom d-flex flex-wrap justify-content-between gap-2">
                    <span>Copyright © {{ date('Y') }} Nhà hàng Hoa Sen.</span>
                    <span>Phục vụ tận tâm - món ngon trọn vị.</span>
                </div>
            </footer>

            <section class="mt-4" aria-label="Bản đồ nhà hàng">
                <div class="section-title mb-3">
                    <div>
                        <div class="eyebrow">Bản đồ</div>
                        <h2 class="h1 mb-0">Tìm đường đến Nhà hàng Hoa Sen</h2>
                    </div>
                </div>
                <div class="contact-map-card">
                    <iframe
                        src="https://www.google.com/maps?q=%C3%82m%20Th%E1%BB%B1c%20Ao%20Sen%20100K%20%C4%90.%20V%C3%B5%20V%C4%83n%20Ki%E1%BB%87t%2C%20Long%20Ch%C3%A2u%2C%20V%C4%A9nh%20Long%2C%20Vi%E1%BB%87t%20Nam&output=embed"
                        title="Bản đồ Nhà hàng Hoa Sen"
                        allowfullscreen
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </section>
        </div>
    </section>
</div>
@endsection
