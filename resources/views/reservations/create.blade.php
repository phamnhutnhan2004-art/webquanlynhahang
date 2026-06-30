@extends('layouts.app')

@section('title', 'Đặt bàn - Nhà hàng Hoa Sen')

@section('content')
<style>
    .booking-page {
        padding-bottom: 3rem;
    }

    .booking-hero {
        min-height: 320px;
        display: grid;
        align-items: end;
        padding: clamp(2.5rem, 7vw, 5rem) 0;
        background:
            linear-gradient(90deg, rgba(14, 59, 50, .9), rgba(44, 27, 18, .62)),
            url("{{ asset('images/restaurant-interior.png') }}") center / cover;
        color: #fff;
    }

    .booking-hero .eyebrow {
        color: var(--gold-soft);
    }

    .booking-hero-title {
        color: var(--gold-soft);
    }

    .booking-hero p {
        max-width: 680px;
        color: rgba(255, 255, 255, .94);
        font-weight: 650;
    }

    .booking-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.05fr) minmax(320px, .95fr);
        gap: 1rem;
        align-items: start;
        margin-top: -3rem;
    }

    .booking-panel,
    .booking-info {
        border: 1px solid rgba(217, 164, 65, .32);
        border-radius: 8px;
        background: #fffaf0;
        box-shadow: 0 24px 60px rgba(44, 27, 18, .12);
    }

    .booking-panel {
        padding: clamp(1.25rem, 3vw, 2rem);
        color: #221812;
    }

    .booking-panel .eyebrow {
        color: #b98516;
    }

    .booking-panel h2,
    .booking-panel .form-label,
    .booking-info-body strong {
        color: #111111;
    }

    .booking-panel-title {
        color: #111111;
        font-size: 1.75rem;
        font-weight: 900;
        line-height: 1.2;
    }

    .booking-panel .form-control,
    .booking-panel .form-select {
        color: #221812;
        background-color: #ffffff;
    }

    .booking-panel .form-control::placeholder {
        color: #756a5e;
    }

    .booking-info {
        overflow: hidden;
    }

    .booking-info-head {
        padding: 1.25rem;
        background: #0e3b32;
        color: #fff;
    }

    .booking-info-body {
        display: grid;
        gap: .85rem;
        padding: 1.25rem;
        color: #221812;
    }

    .booking-info-body .text-muted {
        color: #4a4036 !important;
    }

    .booking-note {
        display: flex;
        gap: .75rem;
        padding: .9rem 1rem;
        border-radius: 8px;
        background: rgba(14, 59, 50, .08);
        color: var(--wood-dark);
        font-weight: 700;
    }

    .booking-note i {
        color: var(--green);
    }

    @media (max-width: 991.98px) {
        .booking-grid {
            grid-template-columns: 1fr;
            margin-top: 1rem;
        }
    }
</style>

@php
    $user = auth()->user();
    $customer = $user?->customer;
    $displayName = $customer?->full_name ?? $user?->name ?? $user?->full_name;
    $displayPhone = $customer?->phone ?? $user?->phone;
    $displayEmail = $customer?->email ?? $user?->email;
@endphp

<div class="booking-page">
    <section class="booking-hero">
        <div class="container">
            <h1 class="display-5 fw-bold mb-3 booking-hero-title">Đặt bàn tại Nhà hàng Hoa Sen</h1>
            <p class="lead mb-0">Khi đăng ký tài khoản, bạn có thể dễ dàng lưu lại lịch sử đặt bàn, quản lý thông tin tiện lợi và nhận nhiều ưu đãi hấp dẫn từ Nhà hàng Hoa Sen.
.</p>
        </div>
    </section>

    <section class="container">
        <div class="booking-grid">
            <div class="booking-panel">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                    <div>
                        <h2 class="booking-panel-title mb-1">Thông tin đặt bàn</h2>
                        <div class="h3 fw-bold mb-0">Gửi yêu cầu</div>
                    </div>
                    @guest
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('login') }}">Đăng nhập</a>
                    @endguest
                </div>

                @guest
                    <div class="booking-note mb-3">
                        <i class="bi bi-person-check-fill" aria-hidden="true"></i>
                        <span>Đăng nhập để lưu lại lịch sử đặt bàn và nhận nhiều tiện ích hơn, nhưng bạn vẫn có thể đặt bàn mà không cần đăng nhập.
</span>
                    </div>
                @else
                    <div class="booking-note mb-3">
                        <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                        <span>Hệ thống sẽ dùng thông tin tài khoản: {{ $displayName }} · {{ $displayPhone }}{{ $displayEmail ? ' · '.$displayEmail : '' }}</span>
                    </div>
                @endguest

                <form method="POST" action="{{ route('reservations.store') }}" class="row g-3">
                    @csrf

                    @guest
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="fullName">Họ và tên</label>
                            <input class="form-control @error('full_name') is-invalid @enderror" id="fullName" name="full_name" value="{{ old('full_name') }}" required maxlength="150" autocomplete="name">
                            @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="phone">Số điện thoại</label>
                            <input class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" required maxlength="30" autocomplete="tel">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold" for="email">Email {{ $reservationEmailRequired ? '' : '(không bắt buộc)' }}</label>
                            <input class="form-control @error('email') is-invalid @enderror" id="email" type="email" name="email" value="{{ old('email') }}" @required($reservationEmailRequired) maxlength="150" autocomplete="email">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    @endguest

                    <div class="col-md-6">
                        <label class="form-label fw-bold" for="guestCount">Số lượng khách</label>
                        <input class="form-control @error('number_of_guests') is-invalid @enderror" id="guestCount" type="number" name="number_of_guests" min="1" max="30" value="{{ old('number_of_guests', 2) }}" required>
                        @error('number_of_guests')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold" for="tableId">Bàn</label>
                        <select class="form-select @error('table_id') is-invalid @enderror" id="tableId" name="table_id">
                            <option value="">Để nhân viên sắp xếp</option>
                            @foreach($tables as $table)
                                <option value="{{ $table->id }}" @selected((string) old('table_id') === (string) $table->id)>{{ $table->table_name }} - {{ $table->area }} - {{ $table->seats }} ghế</option>
                            @endforeach
                        </select>
                        @error('table_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold" for="reservationDate">Ngày đặt</label>
                        <input class="form-control @error('reservation_date') is-invalid @enderror" id="reservationDate" type="date" name="reservation_date" min="{{ now()->toDateString() }}" value="{{ old('reservation_date') }}" required>
                        @error('reservation_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold" for="reservationHour">Giờ đặt</label>
                        <input class="form-control @error('reservation_hour') is-invalid @enderror" id="reservationHour" type="time" name="reservation_hour" value="{{ old('reservation_hour') }}" required>
                        @error('reservation_hour')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold" for="note">Ghi chú</label>
                        <textarea class="form-control @error('note') is-invalid @enderror" id="note" name="note" rows="3" maxlength="255" placeholder="Ví dụ: cần ghế trẻ em, ưu tiên gần cửa sổ">{{ old('note') }}</textarea>
                        @error('note')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <button class="btn btn-primary btn-lg w-100" type="submit">
                            <i class="bi bi-calendar2-check me-1" aria-hidden="true"></i>Đặt bàn
                        </button>
                    </div>
                </form>
            </div>

            <aside class="booking-info">
                <div class="booking-info-head">
                    <div class="eyebrow mb-2">Xác nhận</div>
                    <h2 class="h4 fw-bold mb-0">Sau khi gửi yêu cầu</h2>
                </div>
                <div class="booking-info-body">
                    <div><strong>1. Đặt bàn thành công</strong><br><span class="text-muted">Hệ thống lưu phiếu đặt bàn ngay cả khi chưa đăng nhập.</span></div>
                    <div><strong>2. Email xác nhận</strong><br><span class="text-muted">Nếu có email, nhà hàng sẽ gửi thông tin đặt bàn cho khách.</span></div>
                    <div><strong>3. Nhân viên xử lý</strong><br><span class="text-muted">Nhân viên phân biệt khách thành viên và khách tiềm năng trong màn hình quản lý.</span></div>
                    <div><strong>4. Lịch sử đặt bàn</strong><br><span class="text-muted">Chỉ tài khoản đăng nhập mới xem được lịch sử của mình.</span></div>
                </div>
            </aside>
        </div>
    </section>
</div>
@endsection
