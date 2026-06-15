@extends('layouts.app')

@section('title', 'Giới thiệu - Nhà hàng Hoa Sen')

@section('content')
<style>
    .about-page {
        padding-bottom: 4rem;
    }

    .about-hero {
        min-height: min(560px, calc(100vh - 88px));
        display: grid;
        align-items: end;
        margin-top: -1.5rem;
        padding: clamp(2rem, 6vw, 5rem) 0;
        background:
            linear-gradient(90deg, rgba(17, 10, 6, .86) 0%, rgba(14, 59, 50, .74) 52%, rgba(14, 59, 50, .2) 100%),
            url("{{ asset('images/hero-restaurant.png') }}") center / cover;
        color: #fff;
    }

    .about-hero h1 {
        max-width: 760px;
        font-weight: 900;
        letter-spacing: .04em;
    }

    .about-hero .lead {
        max-width: 720px;
        color: rgba(255, 255, 255, .84);
    }

    .about-toc {
        position: relative;
        margin-top: -3.2rem;
        z-index: 2;
    }

    .about-toc-box {
        background: rgba(255, 250, 240, .96);
        border: 1px solid rgba(217, 164, 65, .42);
        border-radius: 8px;
        box-shadow: 0 24px 70px rgba(44, 27, 18, .16);
        overflow: hidden;
    }

    .about-toc-head {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: 1rem 1.25rem;
        background: linear-gradient(90deg, rgba(14, 59, 50, .96), rgba(90, 52, 30, .92));
        color: #fff;
    }

    .about-toc-head i {
        color: var(--gold-soft);
        font-size: 1.25rem;
    }

    .about-toc-list {
        display: grid;
        gap: .15rem;
        padding: .9rem;
        margin: 0;
        list-style: none;
        counter-reset: about-toc;
    }

    .about-toc-list li {
        counter-increment: about-toc;
    }

    .about-toc-list a {
        display: grid;
        grid-template-columns: 2.2rem 1fr;
        align-items: center;
        gap: .75rem;
        padding: .8rem .9rem;
        color: var(--wood-dark);
        text-decoration: none;
        border-radius: 8px;
        font-weight: 800;
        line-height: 1.45;
    }

    .about-toc-list a::before {
        content: counter(about-toc);
        display: inline-grid;
        place-items: center;
        width: 2.2rem;
        aspect-ratio: 1;
        border-radius: 50%;
        background: var(--green-soft);
        color: var(--green);
        font-weight: 900;
    }

    .about-toc-list a:hover {
        background: #fff;
        color: var(--green);
    }

    .about-section {
        scroll-margin-top: 104px;
        padding: clamp(3rem, 7vw, 5rem) 0;
        border-bottom: 1px solid var(--line);
    }

    .about-section:last-child {
        border-bottom: 0;
    }

    .about-kicker {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        color: var(--green);
        font-size: .8rem;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .about-icon {
        display: inline-grid;
        place-items: center;
        width: 3.35rem;
        aspect-ratio: 1;
        border-radius: 8px;
        background: var(--green);
        color: var(--gold-soft);
        box-shadow: 0 18px 38px rgba(14, 59, 50, .2);
        font-size: 1.55rem;
    }

    .about-copy {
        color: var(--muted);
        font-size: 1.06rem;
        line-height: 1.8;
    }

    .about-feature-list {
        display: grid;
        gap: .75rem;
        padding: 0;
        margin: 1.25rem 0 0;
        list-style: none;
    }

    .about-feature-list li {
        display: flex;
        align-items: flex-start;
        gap: .65rem;
        color: var(--wood-dark);
        font-weight: 700;
    }

    .about-feature-list i {
        color: var(--gold);
        line-height: 1.6;
    }

    .about-photo {
        position: relative;
        overflow: hidden;
        border-radius: 8px;
        border: 1px solid rgba(217, 164, 65, .36);
        box-shadow: 0 26px 70px rgba(44, 27, 18, .14);
    }

    .about-photo img {
        width: 100%;
        aspect-ratio: 5 / 4;
        object-fit: cover;
        display: block;
    }

    .about-photo-label {
        position: absolute;
        left: 1rem;
        right: 1rem;
        bottom: 1rem;
        display: flex;
        align-items: center;
        gap: .55rem;
        width: fit-content;
        max-width: calc(100% - 2rem);
        padding: .6rem .85rem;
        border-radius: 8px;
        background: rgba(255, 250, 240, .94);
        color: var(--green);
        font-weight: 900;
        box-shadow: 0 16px 38px rgba(44, 27, 18, .16);
    }

    .about-soft-panel {
        padding: clamp(1.25rem, 3vw, 2rem);
        border: 1px solid rgba(90, 52, 30, .14);
        border-radius: 8px;
        background: rgba(255, 255, 255, .7);
    }

    .about-contact {
        background:
            linear-gradient(135deg, rgba(14, 59, 50, .96), rgba(44, 27, 18, .9)),
            url("{{ asset('images/restaurant-interior.png') }}") center / cover;
        color: #fff;
        border-radius: 8px;
        padding: clamp(1.5rem, 5vw, 3rem);
        box-shadow: 0 28px 70px rgba(44, 27, 18, .18);
    }

    .about-contact .about-copy,
    .about-contact a:not(.btn) {
        color: rgba(255, 255, 255, .84);
    }

    .about-contact-item {
        display: flex;
        gap: .85rem;
        padding: .95rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, .18);
    }

    .about-contact-item:last-child {
        border-bottom: 0;
    }

    .about-contact-item i {
        color: var(--gold-soft);
        font-size: 1.25rem;
        line-height: 1.4;
    }

    @media (max-width: 991.98px) {
        .about-hero {
            min-height: 520px;
            margin-top: -1rem;
        }
    }

    @media (max-width: 575.98px) {
        .about-hero {
            min-height: 500px;
        }

        .about-toc {
            margin-top: -2rem;
        }

        .about-toc-list a {
            grid-template-columns: 1.9rem 1fr;
            padding: .72rem;
            font-size: .95rem;
        }

        .about-toc-list a::before {
            width: 1.9rem;
        }

        .about-photo img {
            aspect-ratio: 4 / 3;
        }
    }
</style>

<div class="about-page">
    <section class="about-hero">
        <div class="container">
            <nav class="mb-4 small" aria-label="breadcrumb">
                <a class="text-white text-decoration-none" href="{{ route('home') }}">Trang chủ</a>
                <span class="mx-2 text-white-50">/</span>
                <span class="text-white-50">Giới thiệu</span>
            </nav>

            <div class="eyebrow mb-3">Ẩm thực sân vườn và hải sản tươi sống</div>
            <h1 class="display-3 mb-3">NHÀ HÀNG HOA SEN</h1>
            <p class="lead mb-0">Không gian xanh thoáng mát, món ăn đồng quê đậm vị miền Tây và hải sản được chọn ngay tại bể cho những bữa ăn gia đình, gặp mặt bạn bè và tiệc nhóm trọn vẹn.</p>
        </div>
    </section>

    <section class="about-toc" aria-label="Mục lục trang giới thiệu">
        <div class="container">
            <div class="about-toc-box">
                <div class="about-toc-head">
                    <i class="bi bi-list-stars" aria-hidden="true"></i>
                    <div>
                        <div class="fw-bold text-uppercase">Mục lục</div>
                        <div class="small text-white-50">Chọn nội dung để cuộn đến phần cần xem</div>
                    </div>
                </div>

                <ol class="about-toc-list">
                    <li><a href="#nha-hang-hoa-sen">Nhà hàng Hoa Sen - Chuyên hải sản tươi sống và các món đồng quê.</a></li>
                    <li><a href="#hai-san-tuoi-song">Hải sản tươi sống - Chọn ngay tại bể.</a></li>
                    <li><a href="#mon-an-dong-que">Món ăn đồng quê - Hương vị miền Tây.</a></li>
                    <li><a href="#khong-gian-san-vuon">Không gian sân vườn thoáng mát.</a></li>
                    <li><a href="#lien-he-dat-ban">Liên hệ ngay để đặt bàn.</a></li>
                </ol>
            </div>
        </div>
    </section>

    <section id="nha-hang-hoa-sen" class="about-section">
        <div class="container">
            <div class="row align-items-center g-4 g-xl-5">
                <div class="col-lg-6">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="about-icon"><i class="bi bi-flower1" aria-hidden="true"></i></span>
                        <div>
                            <div class="about-kicker">Giới thiệu</div>
                            <h2 class="h1 fw-bold mb-0">Nhà hàng Hoa Sen - Chuyên hải sản tươi sống và các món đồng quê.</h2>
                        </div>
                    </div>
                    <p class="about-copy mb-0">Nhà hàng Hoa Sen mang tinh thần ẩm thực Việt gần gũi: nguyên liệu tươi, cách nêm nếm hài hòa và phong cách phục vụ chu đáo. Thực khách có thể dùng bữa trong không gian sân vườn thư thái, chọn món hải sản tại bể và thưởng thức những món đồng quê quen thuộc được trình bày chỉn chu.</p>
                    <div class="about-soft-panel mt-4">
                        <div class="row g-3">
                            <div class="col-sm-4">
                                <strong class="d-block h3 mb-1 text-success">100%</strong>
                                <span class="text-muted">Nguyên liệu chọn lọc</span>
                            </div>
                            <div class="col-sm-4">
                                <strong class="d-block h3 mb-1 text-success">5+</strong>
                                <span class="text-muted">Không gian phục vụ</span>
                            </div>
                            <div class="col-sm-4">
                                <strong class="d-block h3 mb-1 text-success">24/7</strong>
                                <span class="text-muted">Hỗ trợ đặt bàn online</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <figure class="about-photo mb-0">
                        <img src="{{ asset('images/restaurant-interior.png') }}" alt="Không gian phục vụ của Nhà hàng Hoa Sen">
                        <figcaption class="about-photo-label"><i class="bi bi-stars" aria-hidden="true"></i> Ấm cúng, hiện đại, gần gũi</figcaption>
                    </figure>
                </div>
            </div>
        </div>
    </section>

    <section id="hai-san-tuoi-song" class="about-section">
        <div class="container">
            <div class="row align-items-center g-4 g-xl-5 flex-lg-row-reverse">
                <div class="col-lg-6">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="about-icon"><i class="bi bi-water" aria-hidden="true"></i></span>
                        <div>
                            <div class="about-kicker">Hải sản tươi sống</div>
                            <h2 class="h1 fw-bold mb-0">Hải sản tươi sống - Chọn ngay tại bể.</h2>
                        </div>
                    </div>
                    <p class="about-copy">Nguồn hải sản được tuyển chọn mỗi ngày, giữ độ tươi trước khi chế biến để món ăn có vị ngọt tự nhiên. Khách có thể chọn trực tiếp theo khẩu vị, số lượng và cách chế biến phù hợp với bữa ăn.</p>
                    <ul class="about-feature-list">
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>Tôm, cua, cá, nghêu và các loại hải sản theo mùa.</span></li>
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>Chế biến nhanh, giữ trọn độ tươi và hương vị nguyên bản.</span></li>
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>Phù hợp cho bữa gia đình, tiệc nhóm và tiếp khách.</span></li>
                    </ul>
                </div>

                <div class="col-lg-6">
                    <figure class="about-photo mb-0">
                        <img src="{{ asset('images/ca-chep-sot-cai-xanh.png') }}" alt="Món hải sản tươi sống tại Nhà hàng Hoa Sen">
                        <figcaption class="about-photo-label"><i class="bi bi-droplet-fill" aria-hidden="true"></i> Tươi ngon từ khâu chọn nguyên liệu</figcaption>
                    </figure>
                </div>
            </div>
        </div>
    </section>

    <section id="mon-an-dong-que" class="about-section">
        <div class="container">
            <div class="row align-items-center g-4 g-xl-5">
                <div class="col-lg-6">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="about-icon"><i class="bi bi-basket2-fill" aria-hidden="true"></i></span>
                        <div>
                            <div class="about-kicker">Món ăn đồng quê</div>
                            <h2 class="h1 fw-bold mb-0">Món ăn đồng quê - Hương vị miền Tây.</h2>
                        </div>
                    </div>
                    <p class="about-copy">Những món ăn đồng quê được nấu theo tinh thần miền Tây: mộc mạc, đậm đà và dễ ăn. Từ cá kho, rau vườn, gà ta đến lẩu dân dã, mỗi món đều hướng đến cảm giác thân quen như một bữa cơm nhà.</p>
                    <ul class="about-feature-list">
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>Gia vị cân bằng, hợp khẩu vị nhiều thế hệ trong gia đình.</span></li>
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>Thực đơn đa dạng cho bữa trưa, bữa tối và tiệc thân mật.</span></li>
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>Nguyên liệu quen thuộc nhưng được trình bày gọn gàng, đẹp mắt.</span></li>
                    </ul>
                </div>

                <div class="col-lg-6">
                    <figure class="about-photo mb-0">
                        <img src="{{ asset('images/ga-xao-cay.png') }}" alt="Món ăn đồng quê hương vị miền Tây">
                        <figcaption class="about-photo-label"><i class="bi bi-heart-fill" aria-hidden="true"></i> Đậm vị miền Tây trong từng món</figcaption>
                    </figure>
                </div>
            </div>
        </div>
    </section>

    <section id="khong-gian-san-vuon" class="about-section">
        <div class="container">
            <div class="row align-items-center g-4 g-xl-5 flex-lg-row-reverse">
                <div class="col-lg-6">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="about-icon"><i class="bi bi-tree-fill" aria-hidden="true"></i></span>
                        <div>
                            <div class="about-kicker">Không gian sân vườn</div>
                            <h2 class="h1 fw-bold mb-0">Không gian sân vườn thoáng mát.</h2>
                        </div>
                    </div>
                    <p class="about-copy">Không gian được bố trí thoáng, nhiều mảng xanh và ánh sáng ấm để thực khách có cảm giác thư giãn trong suốt bữa ăn. Khu vực bàn linh hoạt cho nhóm nhỏ, gia đình hoặc những buổi gặp mặt cần sự riêng tư vừa đủ.</p>
                    <ul class="about-feature-list">
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>Sân vườn thoáng, phù hợp chụp ảnh và dùng bữa cuối tuần.</span></li>
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>Bàn nhóm, bàn gia đình và khu tiếp khách được sắp xếp linh hoạt.</span></li>
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>Không khí gần gũi, sạch sẽ và dễ chịu trên cả điện thoại lẫn máy tính.</span></li>
                    </ul>
                </div>

                <div class="col-lg-6">
                    <figure class="about-photo mb-0">
                        <img src="{{ asset('images/hero-restaurant.png') }}" alt="Không gian sân vườn thoáng mát của nhà hàng">
                        <figcaption class="about-photo-label"><i class="bi bi-brightness-alt-high-fill" aria-hidden="true"></i> Thoáng mát cho bữa ăn trọn vẹn</figcaption>
                    </figure>
                </div>
            </div>
        </div>
    </section>

    <section id="lien-he-dat-ban" class="about-section pb-0">
        <div class="container">
            <div class="about-contact">
                <div class="row align-items-center g-4">
                    <div class="col-lg-7">
                        <div class="about-kicker text-warning mb-2"><i class="bi bi-telephone-outbound-fill" aria-hidden="true"></i> Liên hệ và đặt bàn</div>
                        <h2 class="h1 fw-bold mb-3">Liên hệ ngay để đặt bàn.</h2>
                        <p class="about-copy mb-0">Đội ngũ Nhà hàng Hoa Sen luôn sẵn sàng tư vấn món ăn, giữ bàn theo khung giờ phù hợp và hỗ trợ chuẩn bị không gian cho bữa ăn của bạn.</p>
                    </div>
                    <div class="col-lg-5">
                        <div class="about-contact-item">
                            <i class="bi bi-geo-alt-fill" aria-hidden="true"></i>
                            <div>
                                <strong class="d-block">Địa chỉ nhà hàng</strong>
                                <span>100k Đ. Võ Văn Kiệt, Phường Long Châu, Vĩnh Long.</span>
                            </div>
                        </div>
                        <div class="about-contact-item">
                            <i class="bi bi-telephone-fill" aria-hidden="true"></i>
                            <div>
                                <strong class="d-block">Số điện thoại</strong>
                                <a class="text-decoration-none" href="tel:0918118544">0918 118 544</a>
                            </div>
                        </div>
                        <div class="about-contact-item">
                            <i class="bi bi-envelope-fill" aria-hidden="true"></i>
                            <div>
                                <strong class="d-block">Email</strong>
                                <a class="text-decoration-none" href="mailto:huynhhuukhiem1978vl@gmail.com">huynhhuukhiem1978vl@gmail.com</a>
                            </div>
                        </div>

                        @guest
                            <a class="btn btn-primary btn-lg w-100 mt-3" href="{{ route('register') }}">Đặt bàn ngay</a>
                        @else
                            <a class="btn btn-primary btn-lg w-100 mt-3" href="{{ auth()->user()->isCustomer() ? route('customer.dashboard') : route('home') }}">Đặt bàn ngay</a>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
