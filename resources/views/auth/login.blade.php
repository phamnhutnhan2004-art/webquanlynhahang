@extends('layouts.app')

@php
    $authTab = $activeAuthTab ?? (request()->routeIs('register') ? 'register' : 'login');
@endphp

@section('title', $authTab === 'register' ? 'Đăng ký' : 'Đăng nhập')

@section('content')
<style>
    .auth-page {
        min-height: calc(100vh - 92px);
        display: grid;
        align-items: center;
        padding: clamp(2rem, 6vw, 4.5rem) 0;
    }

    .auth-shell {
        overflow: hidden;
        border: 1px solid rgba(217, 164, 65, .34);
        border-radius: 8px;
        background: rgba(255, 250, 240, .96);
        box-shadow: 0 30px 80px rgba(44, 27, 18, .14);
    }

    .auth-visual {
        min-height: 100%;
        padding: clamp(1.5rem, 4vw, 3rem);
        background:
            linear-gradient(135deg, rgba(14, 59, 50, .95), rgba(44, 27, 18, .84)),
            url("{{ asset('images/restaurant-interior.png') }}") center / cover;
        color: #fff;
    }

    .auth-visual p {
        color: rgba(255, 255, 255, .8);
    }

    .auth-badge {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .45rem .7rem;
        border: 1px solid rgba(217, 164, 65, .42);
        border-radius: 999px;
        color: var(--gold-soft);
        font-size: .78rem;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .auth-benefits {
        display: grid;
        gap: .85rem;
        margin-top: clamp(2rem, 6vw, 5rem);
    }

    .auth-benefit {
        display: flex;
        align-items: flex-start;
        gap: .7rem;
        padding-top: .85rem;
        border-top: 1px solid rgba(255, 255, 255, .16);
    }

    .auth-benefit i {
        color: var(--gold-soft);
        font-size: 1.15rem;
        line-height: 1.5;
    }

    .auth-main {
        padding: clamp(1.35rem, 4vw, 2.5rem);
        background:
            linear-gradient(180deg, rgba(255, 255, 255, .9), rgba(255, 250, 240, .96));
    }

    .auth-tabs {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: .45rem;
        padding: .35rem;
        border: 1px solid var(--line);
        border-radius: 8px;
        background: rgba(246, 239, 224, .8);
    }

    .auth-tab {
        border: 0;
        border-radius: 8px;
        padding: .78rem .9rem;
        background: transparent;
        color: var(--wood-dark);
        font-weight: 900;
        transition: background .2s ease, color .2s ease, box-shadow .2s ease, transform .2s ease;
    }

    .auth-tab:hover {
        transform: translateY(-1px);
        color: var(--green);
    }

    .auth-tab.is-active {
        background: var(--green);
        color: #fff;
        box-shadow: 0 12px 28px rgba(14, 59, 50, .22);
    }

    .auth-panels {
        position: relative;
        margin-top: 1.35rem;
    }

    .auth-panel {
        display: none;
        opacity: 0;
        transform: translateY(10px);
    }

    .auth-panel.is-active {
        display: block;
        animation: authFadeIn .28s ease forwards;
    }

    @keyframes authFadeIn {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .auth-form-label {
        font-weight: 800;
        color: var(--wood-dark);
    }

    .auth-control {
        min-height: 46px;
        border-color: rgba(90, 52, 30, .22);
        border-radius: 8px;
        background-color: #fff;
    }

    .auth-control:focus {
        border-color: var(--gold);
        box-shadow: 0 0 0 .2rem rgba(217, 164, 65, .16);
    }

    .auth-link {
        color: var(--green);
        font-weight: 800;
        text-decoration: none;
    }

    .auth-link:hover {
        color: var(--wood);
    }

    .auth-submit {
        min-height: 48px;
    }

    @media (max-width: 991.98px) {
        .auth-page {
            align-items: start;
            padding-top: 2rem;
        }

        .auth-visual {
            min-height: 340px;
        }
    }

    @media (max-width: 575.98px) {
        .auth-shell {
            margin-inline: .25rem;
        }

        .auth-main {
            padding: 1.1rem;
        }

        .auth-tab {
            padding-inline: .55rem;
            font-size: .95rem;
        }
    }
</style>

<section class="auth-page">
    <div class="container">
        <div class="auth-shell">
            <div class="row g-0">
                <div class="col-lg-5">
                    <aside class="auth-visual h-100">
                        <span class="auth-badge"><i class="bi bi-flower1" aria-hidden="true"></i> Nhà hàng Hoa Sen</span>
                        <h1 class="display-6 fw-bold mt-4 mb-3">Đặt bàn nhanh, thưởng thức trọn vị Việt.</h1>
                        <p class="fs-5 mb-0">Đăng nhập hoặc tạo tài khoản để đặt bàn, theo dõi lịch hẹn và nhận hỗ trợ từ nhà hàng.</p>

                        <div class="auth-benefits">
                            <div class="auth-benefit">
                                <i class="bi bi-calendar2-check-fill" aria-hidden="true"></i>
                                <div>
                                    <strong class="d-block">Quản lý đặt bàn</strong>
                                    <span class="text-white-50">Theo dõi thông tin đặt bàn trong tài khoản khách hàng.</span>
                                </div>
                            </div>
                            <div class="auth-benefit">
                                <i class="bi bi-chat-dots-fill" aria-hidden="true"></i>
                                <div>
                                    <strong class="d-block">Hỗ trợ nhanh</strong>
                                    <span class="text-white-50">Chatbot và nhân viên luôn sẵn sàng tư vấn món ăn.</span>
                                </div>
                            </div>
                            <div class="auth-benefit">
                                <i class="bi bi-shield-check" aria-hidden="true"></i>
                                <div>
                                    <strong class="d-block">Thông tin bảo mật</strong>
                                    <span class="text-white-50">Tài khoản được dùng cho đặt bàn và chăm sóc khách hàng.</span>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>

                <div class="col-lg-7">
                    <div class="auth-main">
                        <div class="mb-4">
                            <div class="eyebrow mb-2">Tài khoản khách hàng</div>
                            <h2 class="h1 fw-bold mb-2">Chào mừng đến với Hoa Sen</h2>
                            <p class="text-muted mb-0">Một khu vực duy nhất cho đăng nhập và đăng ký.</p>
                        </div>

                        <div class="auth-tabs" role="tablist" aria-label="Đăng nhập và đăng ký">
                            <button class="auth-tab {{ $authTab === 'login' ? 'is-active' : '' }}" type="button" id="loginTab" role="tab" aria-controls="loginPanel" aria-selected="{{ $authTab === 'login' ? 'true' : 'false' }}" data-auth-tab="login">
                                Đăng nhập
                            </button>
                            <button class="auth-tab {{ $authTab === 'register' ? 'is-active' : '' }}" type="button" id="registerTab" role="tab" aria-controls="registerPanel" aria-selected="{{ $authTab === 'register' ? 'true' : 'false' }}" data-auth-tab="register">
                                Đăng ký
                            </button>
                        </div>

                        <div class="auth-panels">
                            <section class="auth-panel {{ $authTab === 'login' ? 'is-active' : '' }}" id="loginPanel" role="tabpanel" aria-labelledby="loginTab" data-auth-panel="login">
                                <form method="POST" action="{{ route('login.store') }}" novalidate>
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label auth-form-label" for="loginInput">Email hoặc Số điện thoại</label>
                                        <input class="form-control auth-control @error('login') is-invalid @enderror" id="loginInput" name="login" value="{{ old('login') }}" autocomplete="username" required {{ $authTab === 'login' ? 'autofocus' : '' }}>
                                        @error('login')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label auth-form-label" for="loginPassword">Mật khẩu</label>
                                        <input class="form-control auth-control @error('password') is-invalid @enderror" id="loginPassword" type="password" name="password" autocomplete="current-password" required>
                                        @if($authTab === 'login')
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        @endif
                                    </div>

                                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                                        </div>
                                        <a class="auth-link" href="{{ route('password.request') }}">Quên mật khẩu?</a>
                                    </div>

                                    <button class="btn btn-primary auth-submit w-100" type="submit">
                                        <i class="bi bi-box-arrow-in-right me-1" aria-hidden="true"></i> Đăng nhập
                                    </button>
                                </form>
                            </section>

                            <section class="auth-panel {{ $authTab === 'register' ? 'is-active' : '' }}" id="registerPanel" role="tabpanel" aria-labelledby="registerTab" data-auth-panel="register">
                                <form method="POST" action="{{ route('register.store') }}" novalidate>
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label auth-form-label" for="registerName">Họ và tên</label>
                                            <input class="form-control auth-control @error('name') is-invalid @enderror" id="registerName" name="name" value="{{ old('name') }}" autocomplete="name" required {{ $authTab === 'register' ? 'autofocus' : '' }}>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label auth-form-label" for="registerPhone">Số điện thoại</label>
                                            <input class="form-control auth-control @error('phone') is-invalid @enderror" id="registerPhone" name="phone" value="{{ old('phone') }}" autocomplete="tel" required>
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label auth-form-label" for="registerEmail">Email</label>
                                            <input class="form-control auth-control @error('email') is-invalid @enderror" id="registerEmail" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label auth-form-label" for="registerAddress">Địa chỉ</label>
                                            <input class="form-control auth-control @error('address') is-invalid @enderror" id="registerAddress" name="address" value="{{ old('address') }}" autocomplete="street-address">
                                            @error('address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label auth-form-label" for="registerPassword">Mật khẩu</label>
                                            <input class="form-control auth-control @error('password') is-invalid @enderror" id="registerPassword" type="password" name="password" autocomplete="new-password" required>
                                            @if($authTab === 'register')
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            @endif
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label auth-form-label" for="registerPasswordConfirm">Xác nhận mật khẩu</label>
                                            <input class="form-control auth-control" id="registerPasswordConfirm" type="password" name="password_confirmation" autocomplete="new-password" required>
                                        </div>
                                    </div>

                                    <button class="btn btn-primary auth-submit w-100 mt-4" type="submit">
                                        <i class="bi bi-person-plus-fill me-1" aria-hidden="true"></i> Đăng ký
                                    </button>
                                </form>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    (() => {
        const tabs = document.querySelectorAll('[data-auth-tab]');
        const panels = document.querySelectorAll('[data-auth-panel]');

        const activate = (name) => {
            tabs.forEach((tab) => {
                const active = tab.dataset.authTab === name;
                tab.classList.toggle('is-active', active);
                tab.setAttribute('aria-selected', active ? 'true' : 'false');
            });

            panels.forEach((panel) => {
                panel.classList.toggle('is-active', panel.dataset.authPanel === name);
            });
        };

        tabs.forEach((tab) => {
            tab.addEventListener('click', () => activate(tab.dataset.authTab));
        });
    })();
</script>
@endsection
