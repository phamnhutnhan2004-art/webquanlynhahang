@extends('layouts.app')

@section('title', 'Đặt tiệc tại nhà - Nhà hàng Hoa Sen')

@section('content')
<style>
    .party-page {
        padding-bottom: 4rem;
    }

    .party-hero {
        margin-top: -1.5rem;
        min-height: min(650px, calc(100vh - 82px));
        display: grid;
        align-items: end;
        padding: clamp(2.5rem, 7vw, 5.5rem) 0;
        background:
            linear-gradient(90deg, rgba(17, 10, 6, .86), rgba(14, 59, 50, .68) 52%, rgba(14, 59, 50, .2)),
            url("{{ asset('images/hero-restaurant.png') }}") center / cover;
        color: #fff;
    }

    .party-hero p {
        max-width: 760px;
        color: rgba(255, 255, 255, .84);
    }

    .party-hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: .75rem;
    }

    .party-section {
        padding: clamp(3rem, 7vw, 5rem) 0;
        border-bottom: 1px solid var(--line);
    }

    .party-section:last-child {
        border-bottom: 0;
    }

    .party-service-card,
    .party-combo-card,
    .party-food-card,
    .party-form-shell,
    .party-review {
        border: 1px solid rgba(90, 52, 30, .14);
        border-radius: 8px;
        background: rgba(255, 255, 255, .82);
        box-shadow: 0 18px 48px rgba(44, 27, 18, .08);
    }

    .party-service-card {
        height: 100%;
        padding: 1.25rem;
        transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
    }

    .party-service-card:hover,
    .party-combo-card:hover,
    .party-food-card:hover {
        transform: translateY(-4px);
        border-color: rgba(217, 164, 65, .55);
        box-shadow: 0 24px 62px rgba(44, 27, 18, .14);
    }

    .party-icon {
        display: inline-grid;
        place-items: center;
        width: 46px;
        aspect-ratio: 1;
        border-radius: 8px;
        background: var(--green);
        color: var(--gold-soft);
        font-size: 1.25rem;
    }

    .party-gallery-img {
        width: 100%;
        aspect-ratio: 16 / 11;
        object-fit: cover;
        display: block;
        border-radius: 8px;
        box-shadow: 0 18px 45px rgba(44, 27, 18, .12);
    }

    .party-combo-card {
        height: 100%;
        padding: 1.25rem;
        cursor: pointer;
        transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
    }

    .party-combo-card.is-selected {
        border-color: var(--gold);
        background: linear-gradient(180deg, #fff, #fff8e8);
    }

    .party-food-card {
        height: 100%;
        overflow: hidden;
        transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
    }

    .party-food-img {
        width: 100%;
        aspect-ratio: 4 / 3;
        object-fit: cover;
        display: block;
    }

    .party-food-body {
        padding: 1rem;
    }

    .party-food-check {
        display: flex;
        align-items: center;
        gap: .65rem;
    }

    .party-qty {
        width: 86px;
    }

    .party-form-shell {
        overflow: hidden;
    }

    .party-form-head {
        padding: clamp(1.25rem, 3vw, 2rem);
        background:
            linear-gradient(135deg, rgba(14, 59, 50, .98), rgba(44, 27, 18, .9)),
            url("{{ asset('images/restaurant-interior.png') }}") center / cover;
        color: #fff;
    }

    .party-form-head p {
        color: rgba(255, 255, 255, .78);
    }

    .party-form-body {
        padding: clamp(1.25rem, 3vw, 2rem);
    }

    .party-form-body .form-label {
        color: var(--wood-dark);
        font-weight: 800;
    }

    .party-control {
        min-height: 46px;
        border-color: rgba(90, 52, 30, .22);
        border-radius: 8px;
    }

    textarea.party-control {
        min-height: 118px;
        resize: vertical;
    }

    .party-control:focus {
        border-color: var(--gold);
        box-shadow: 0 0 0 .2rem rgba(217, 164, 65, .16);
    }

    .party-summary {
        position: sticky;
        top: 98px;
        border-radius: 8px;
        background: var(--green);
        color: #fff;
        padding: 1.25rem;
        box-shadow: 0 24px 64px rgba(14, 59, 50, .22);
    }

    .party-summary .text-muted {
        color: rgba(255, 255, 255, .72) !important;
    }

    .party-total {
        color: var(--gold-soft);
        font-size: clamp(1.8rem, 4vw, 2.45rem);
        font-weight: 900;
    }

    .party-review {
        height: 100%;
        padding: 1.25rem;
    }

    @media (max-width: 991.98px) {
        .party-hero {
            margin-top: -1rem;
            min-height: 560px;
        }

        .party-summary {
            position: static;
        }
    }

    @media (max-width: 575.98px) {
        .party-food-check {
            align-items: flex-start;
        }

        .party-qty {
            width: 74px;
        }
    }
</style>

@php
    $partyTypes = [
        ['name' => 'Tiệc sinh nhật', 'icon' => 'bi-cake2-fill'],
        ['name' => 'Tiệc gia đình', 'icon' => 'bi-house-heart-fill'],
        ['name' => 'Tiệc thôi nôi', 'icon' => 'bi-balloon-heart-fill'],
        ['name' => 'Tiệc liên hoan', 'icon' => 'bi-people-fill'],
        ['name' => 'Tiệc công ty', 'icon' => 'bi-briefcase-fill'],
        ['name' => 'Tiệc tất niên', 'icon' => 'bi-stars'],
        ['name' => 'Tiệc cưới nhỏ', 'icon' => 'bi-gem'],
    ];

    $comboJson = collect($combos)->map(fn ($combo) => [
        'name' => $combo['name'],
        'productIds' => $combo['product_ids'],
    ]);
@endphp

<div class="party-page">
    <section class="party-hero">
        <div class="container">
            <nav class="mb-4 small" aria-label="breadcrumb">
                <a class="text-white text-decoration-none" href="{{ route('home') }}">Trang chủ</a>
                <span class="mx-2 text-white-50">/</span>
                <span class="text-white-50">Đặt tiệc tại nhà</span>
            </nav>

            <div class="eyebrow mb-3">Dịch vụ tận nơi</div>
            <h1 class="display-3 fw-bold mb-3">Đặt tiệc tại nhà cùng Nhà hàng Hoa Sen</h1>
            <p class="lead mb-4">Từ món ăn, nhân sự phục vụ đến lịch tổ chức, Hoa Sen hỗ trợ chuẩn bị buổi tiệc tại nhà chỉn chu như một nhà hàng thu nhỏ cho gia đình, công ty và bạn bè.</p>
            <div class="party-hero-actions">
                <a class="btn btn-primary btn-lg" href="#party-form">Gửi yêu cầu</a>
                <a class="btn btn-outline-light btn-lg" href="#party-menu">Chọn thực đơn</a>
            </div>
        </div>
    </section>

    <section class="party-section">
        <div class="container">
            <div class="section-title">
                <div>
                    <div class="eyebrow">Dịch vụ phù hợp</div>
                    <h2 class="h1 mb-0">Các buổi tiệc Hoa Sen có thể phục vụ</h2>
                </div>
            </div>
            <div class="row g-3">
                @foreach($partyTypes as $type)
                    <div class="col-sm-6 col-lg-4 col-xl-3">
                        <article class="party-service-card">
                            <span class="party-icon mb-3"><i class="bi {{ $type['icon'] }}" aria-hidden="true"></i></span>
                            <h3 class="h5 fw-bold">{{ $type['name'] }}</h3>
                            <p class="text-muted mb-0">Tư vấn thực đơn, số lượng món và cách phục vụ phù hợp không gian tại nhà.</p>
                        </article>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="party-section pt-0">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-lg-5">
                    <div class="eyebrow mb-2">Hình ảnh dịch vụ</div>
                    <h2 class="h1 fw-bold mb-3">Món ngon, bày biện đẹp, phục vụ tận nơi.</h2>
                    <p class="text-muted fs-5 mb-0">Đội ngũ Hoa Sen chuẩn bị món ăn theo khẩu vị Việt, ưu tiên nguyên liệu tươi và trình bày gọn gàng để buổi tiệc tại nhà vẫn có cảm giác chuyên nghiệp.</p>
                </div>
                <div class="col-lg-7">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <img class="party-gallery-img" src="{{ asset('images/ga-xao-cay.png') }}" alt="Món ăn phục vụ tiệc tại nhà">
                        </div>
                        <div class="col-sm-6">
                            <img class="party-gallery-img" src="{{ asset('images/ca-chep-sot-cai-xanh.png') }}" alt="Món chính cho buổi tiệc">
                        </div>
                        <div class="col-12">
                            <img class="party-gallery-img" src="{{ asset('images/restaurant-interior.png') }}" alt="Không gian tiệc được Hoa Sen phục vụ">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="party-menu" class="party-section pt-0">
        <div class="container">
            <div class="section-title">
                <div>
                    <div class="eyebrow">Chọn thực đơn</div>
                    <h2 class="h1 mb-0">Combo gợi ý và món ăn cho buổi tiệc</h2>
                </div>
            </div>

            <div class="row g-3 mb-4">
                @foreach($combos as $index => $combo)
                    <div class="col-md-6 col-xl-4">
                        <button class="party-combo-card text-start w-100" type="button" data-combo-index="{{ $index }}">
                            <div class="d-flex justify-content-between gap-3 mb-2">
                                <h3 class="h5 fw-bold mb-0">{{ $combo['name'] }}</h3>
                                <span class="status-badge">{{ $combo['guests'] }}</span>
                            </div>
                            <p class="text-muted mb-0">{{ $combo['description'] }}</p>
                        </button>
                    </div>
                @endforeach
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="row g-4">
                        @forelse($products as $product)
                            @php
                                $oldQuantity = (int) old("selected_products.{$product->id}", 0);
                            @endphp
                            <div class="col-md-6">
                                <article class="party-food-card" data-food-card>
                                    <img class="party-food-img" src="{{ $product->image_url }}" alt="{{ $product->name }}">
                                    <div class="party-food-body">
                                        <div class="small text-muted">{{ $product->category?->name }}</div>
                                        <h3 class="h5 fw-bold mt-1">{{ $product->name }}</h3>
                                        <p class="text-muted">{{ $product->description }}</p>
                                        <div class="d-flex justify-content-between align-items-center gap-2">
                                            <label class="party-food-check">
                                                <input class="form-check-input" type="checkbox" data-party-check data-product-id="{{ $product->id }}" data-price="{{ (float) $product->price }}" @checked($oldQuantity > 0)>
                                                <span class="fw-bold">{{ number_format((float) $product->price) }} VNĐ</span>
                                            </label>
                                            <input class="form-control party-control party-qty" type="number" name="selected_products[{{ $product->id }}]" form="homePartyForm" min="0" max="99" value="{{ $oldQuantity }}" data-party-qty data-product-id="{{ $product->id }}" aria-label="Số lượng {{ $product->name }}">
                                        </div>
                                    </div>
                                </article>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="muted-box">Chưa có món ăn để chọn cho dịch vụ đặt tiệc.</div>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="col-lg-4">
                    <aside class="party-summary">
                        <div class="eyebrow mb-2">Tạm tính</div>
                        <h3 class="h4 fw-bold mb-3">Chi phí dự kiến</h3>
                        <div class="party-total" id="partyTotal">0 VNĐ</div>
                        <p class="text-muted mb-3">Giá tạm tính theo món đã chọn, chưa bao gồm phụ phí vận chuyển hoặc yêu cầu setup đặc biệt.</p>
                        <div class="d-flex justify-content-between border-top border-light border-opacity-25 pt-3">
                            <span>Món đã chọn</span>
                            <strong id="partySelectedCount">0</strong>
                        </div>
                        <a class="btn btn-primary w-100 mt-4" href="#party-form">Tiếp tục gửi yêu cầu</a>
                    </aside>
                </div>
            </div>
        </div>
    </section>

    <section id="party-form" class="party-section pt-0">
        <div class="container">
            <div class="party-form-shell">
                <div class="party-form-head">
                    <div class="eyebrow mb-2">Đăng ký dịch vụ</div>
                    <h2 class="h1 fw-bold mb-2">Gửi yêu cầu đặt tiệc tại nhà</h2>
                    <p class="mb-0">Nhà hàng sẽ kiểm tra lịch, xác nhận thực đơn và báo chi phí chính thức trước khi triển khai.</p>
                </div>
                <div class="party-form-body">
                    <form id="homePartyForm" method="POST" action="{{ route('home-parties.store') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="fullName">Họ và tên</label>
                                <input class="form-control party-control @error('full_name') is-invalid @enderror" id="fullName" name="full_name" value="{{ old('full_name', auth()->user()?->name) }}" autocomplete="name" required>
                                @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="phone">Số điện thoại</label>
                                <input class="form-control party-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', auth()->user()?->phone) }}" autocomplete="tel" required>
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="email">Email</label>
                                <input class="form-control party-control @error('email') is-invalid @enderror" id="email" type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" autocomplete="email">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="address">Địa chỉ tổ chức tiệc</label>
                                <input class="form-control party-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', auth()->user()?->address) }}" autocomplete="street-address" required>
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" for="eventDate">Ngày tổ chức</label>
                                <input class="form-control party-control @error('event_date') is-invalid @enderror" id="eventDate" type="date" name="event_date" min="{{ date('Y-m-d') }}" value="{{ old('event_date') }}" required>
                                @error('event_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" for="eventTime">Giờ tổ chức</label>
                                <input class="form-control party-control @error('event_time') is-invalid @enderror" id="eventTime" type="time" name="event_time" value="{{ old('event_time') }}" required>
                                @error('event_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" for="guestQuantity">Số lượng khách</label>
                                <input class="form-control party-control @error('guest_quantity') is-invalid @enderror" id="guestQuantity" type="number" name="guest_quantity" min="5" max="500" value="{{ old('guest_quantity', 20) }}" required>
                                @error('guest_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" for="partyType">Loại tiệc</label>
                                <select class="form-select party-control @error('party_type') is-invalid @enderror" id="partyType" name="party_type" required>
                                    @foreach($partyTypes as $type)
                                        <option value="{{ $type['name'] }}" @selected(old('party_type') === $type['name'])>{{ $type['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('party_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="note">Ghi chú</label>
                                <textarea class="form-control party-control @error('note') is-invalid @enderror" id="note" name="note" placeholder="Ví dụ: cần bàn buffet, khu vực phục vụ ngoài sân, món ít cay...">{{ old('note') }}</textarea>
                                @error('note')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            @error('selected_products')
                                <div class="col-12"><div class="alert alert-danger mb-0">{{ $message }}</div></div>
                            @enderror
                        </div>

                        <div class="d-flex flex-wrap gap-2 mt-4">
                            <button class="btn btn-primary btn-lg px-4" type="submit">
                                <i class="bi bi-send-fill me-1" aria-hidden="true"></i> Gửi yêu cầu
                            </button>
                            <button class="btn btn-outline-primary btn-lg px-4" type="reset" id="partyReset">
                                <i class="bi bi-arrow-counterclockwise me-1" aria-hidden="true"></i> Nhập lại
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="party-section pt-0">
        <div class="container">
            <div class="section-title">
                <div>
                    <div class="eyebrow">Đánh giá</div>
                    <h2 class="h1 mb-0">Khách hàng nói gì về dịch vụ?</h2>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-md-4"><div class="party-review"><strong class="d-block mb-2">Tiệc sinh nhật gọn gàng</strong><p class="text-muted mb-0">Món ăn lên đúng giờ, nhân viên hỗ trợ bày bàn rất nhanh và lịch sự.</p></div></div>
                <div class="col-md-4"><div class="party-review"><strong class="d-block mb-2">Hợp tiệc gia đình</strong><p class="text-muted mb-0">Thực đơn dễ ăn, người lớn tuổi và trẻ nhỏ đều dùng được. Phần tư vấn rất kỹ.</p></div></div>
                <div class="col-md-4"><div class="party-review"><strong class="d-block mb-2">Phục vụ chuyên nghiệp</strong><p class="text-muted mb-0">Chi phí rõ ràng, món ăn đẹp mắt, buổi tiệc công ty diễn ra đúng kế hoạch.</p></div></div>
            </div>
        </div>
    </section>
</div>

<script>
    (() => {
        const combos = @json($comboJson);
        const checks = [...document.querySelectorAll('[data-party-check]')];
        const quantities = [...document.querySelectorAll('[data-party-qty]')];
        const comboButtons = [...document.querySelectorAll('[data-combo-index]')];
        const total = document.getElementById('partyTotal');
        const count = document.getElementById('partySelectedCount');
        const reset = document.getElementById('partyReset');
        const currency = new Intl.NumberFormat('vi-VN');

        const quantityFor = (id) => quantities.find((input) => input.dataset.productId === String(id));
        const checkFor = (id) => checks.find((input) => input.dataset.productId === String(id));

        const sync = () => {
            let sum = 0;
            let selected = 0;

            checks.forEach((check) => {
                const qty = quantityFor(check.dataset.productId);
                const quantity = Number(qty?.value || 0);
                check.checked = quantity > 0;

                if (quantity > 0) {
                    selected += 1;
                    sum += Number(check.dataset.price || 0) * quantity;
                }
            });

            total.textContent = `${currency.format(sum)} VNĐ`;
            count.textContent = selected;
        };

        checks.forEach((check) => {
            check.addEventListener('change', () => {
                const qty = quantityFor(check.dataset.productId);
                qty.value = check.checked ? Math.max(1, Number(qty.value || 1)) : 0;
                sync();
            });
        });

        quantities.forEach((input) => {
            input.addEventListener('input', sync);
        });

        comboButtons.forEach((button) => {
            button.addEventListener('click', () => {
                comboButtons.forEach((item) => item.classList.remove('is-selected'));
                button.classList.add('is-selected');

                quantities.forEach((input) => input.value = 0);
                const combo = combos[Number(button.dataset.comboIndex)];

                combo.productIds.forEach((id) => {
                    const qty = quantityFor(id);

                    if (qty) {
                        qty.value = 1;
                    }
                });

                sync();
            });
        });

        reset?.addEventListener('click', () => {
            setTimeout(() => {
                comboButtons.forEach((item) => item.classList.remove('is-selected'));
                sync();
            }, 0);
        });

        sync();
    })();
</script>
@endsection
