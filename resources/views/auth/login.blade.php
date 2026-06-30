@extends('layouts.app')

@php
    $authTab = $activeAuthTab ?? (request()->routeIs('register') ? 'register' : 'login');
    $authDefaults = \App\Models\WebsitePageSetting::authPageDefaults();
    $authUi = $authDefaults;

    try {
        if (\Illuminate\Support\Facades\Schema::hasTable('website_page_settings')) {
            $savedAuthUi = \App\Models\WebsitePageSetting::current('auth')->getSetting('auth_page', []);
            $authUi = array_replace_recursive($authDefaults, is_array($savedAuthUi) ? $savedAuthUi : []);
        }
    } catch (\Throwable) {
        $authUi = $authDefaults;
    }

    $authContent = $authUi['content'];
    $authStyle = $authUi['style'];
    $authHex = fn (string $key, string $fallback) => preg_match('/^#[0-9A-Fa-f]{6}$/', (string) ($authStyle[$key] ?? ''))
        ? $authStyle[$key]
        : $fallback;
    $authRgba = static function (string $hex, int $opacity): string {
        $hex = ltrim($hex, '#');
        $opacity = max(0, min(100, $opacity)) / 100;

        return sprintf(
            'rgba(%d, %d, %d, %.2F)',
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
            $opacity
        );
    };
    $authImagePath = trim((string) ($authStyle['visual_image'] ?? 'images/restaurant-interior.png')) ?: 'images/restaurant-interior.png';
    $authVisualImage = str_starts_with($authImagePath, 'http://') || str_starts_with($authImagePath, 'https://') || str_starts_with($authImagePath, '/')
        ? $authImagePath
        : asset($authImagePath);
@endphp

@section('title', $authTab === 'register' ? 'Đăng ký' : 'Đăng nhập')

@section('content')
<style>
    .auth-page {
        --auth-page-bg: {{ $authHex('background_color', '#fffaf0') }};
        --auth-shell-bg: {{ $authHex('shell_background', '#fffaf0') }};
        --auth-panel-bg: {{ $authHex('panel_background', '#ffffff') }};
        --auth-heading-color: {{ $authHex('heading_color', '#111111') }};
        --auth-body-color: {{ $authHex('body_color', '#221812') }};
        --auth-muted-color: {{ $authHex('muted_color', '#4a4036') }};
        --auth-visual-text: {{ $authHex('visual_text_color', '#ffffff') }};
        --auth-accent: {{ $authHex('accent_color', '#f6df9d') }};
        --auth-link: {{ $authHex('link_color', '#0e3b32') }};
        --auth-tab-bg: {{ $authHex('tab_background', '#f6efe0') }};
        --auth-tab-text: {{ $authHex('tab_text', '#2c1b12') }};
        --auth-tab-active-bg: {{ $authHex('tab_active_background', '#0e3b32') }};
        --auth-tab-active-text: {{ $authHex('tab_active_text', '#ffffff') }};
        --auth-button-bg: {{ $authHex('button_background', '#d9a441') }};
        --auth-button-text: {{ $authHex('button_text', '#2c1b12') }};
        --auth-button-hover: {{ $authHex('button_hover', '#f0bd55') }};
        --auth-input-border: {{ $authHex('input_border', '#d9c6a8') }};
        --auth-border: {{ $authHex('border_color', '#d9a441') }};
        --auth-radius: {{ max(0, min(24, (int) ($authStyle['radius'] ?? 8))) }}px;
        --auth-overlay-start: {{ $authRgba($authHex('visual_overlay_start', '#0e3b32'), (int) ($authStyle['visual_overlay_opacity'] ?? 88)) }};
        --auth-overlay-end: {{ $authRgba($authHex('visual_overlay_end', '#2c1b12'), (int) ($authStyle['visual_overlay_opacity'] ?? 88)) }};
        min-height: calc(100vh - 92px);
        display: grid;
        align-items: center;
        padding: clamp(2rem, 6vw, 4.5rem) 0;
        background: var(--auth-page-bg);
    }

    .auth-shell {
        overflow: hidden;
        border: 1px solid color-mix(in srgb, var(--auth-border) 52%, transparent);
        border-radius: var(--auth-radius);
        background: var(--auth-shell-bg);
        box-shadow: 0 30px 80px rgba(44, 27, 18, .14);
    }

    .auth-visual {
        min-height: 100%;
        padding: clamp(1.5rem, 4vw, 3rem);
        background:
            linear-gradient(135deg, var(--auth-overlay-start), var(--auth-overlay-end)),
            url("{{ $authVisualImage }}") center / cover;
        color: var(--auth-visual-text);
    }

    .auth-visual p {
        color: color-mix(in srgb, var(--auth-visual-text) 94%, transparent);
        font-weight: 650;
        line-height: 1.7;
    }

    .auth-visual h1 {
        color: var(--auth-accent);
        font-size: calc(1.375rem + 1.5vw);
        line-height: 1.15;
    }

    @media (min-width: 1200px) {
        .auth-visual h1 {
            font-size: 2.5rem;
        }
    }

    .auth-badge {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .45rem .7rem;
        border: 1px solid color-mix(in srgb, var(--auth-accent) 58%, transparent);
        border-radius: 999px;
        color: var(--auth-accent);
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
        color: var(--auth-accent);
        font-size: 1.15rem;
        line-height: 1.5;
    }

    .auth-main {
        padding: clamp(1.35rem, 4vw, 2.5rem);
        background: var(--auth-panel-bg);
        color: var(--auth-body-color);
    }

    .auth-main .eyebrow,
    .auth-main h2 {
        color: var(--auth-heading-color) !important;
    }

    .auth-main p {
        color: var(--auth-muted-color) !important;
    }

    .auth-tabs {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: .45rem;
        padding: .35rem;
        border: 1px solid var(--line);
        border-radius: var(--auth-radius);
        background: var(--auth-tab-bg);
    }

    .auth-tab {
        border: 0;
        border-radius: var(--auth-radius);
        padding: .78rem .9rem;
        background: transparent;
        color: var(--auth-tab-text);
        font-weight: 900;
        transition: background .2s ease, color .2s ease, box-shadow .2s ease, transform .2s ease;
    }

    .auth-tab:hover {
        transform: translateY(-1px);
        color: var(--auth-link);
    }

    .auth-tab.is-active {
        background: var(--auth-tab-active-bg);
        color: var(--auth-tab-active-text);
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
        color: var(--auth-body-color);
    }

    .auth-control {
        min-height: 46px;
        border-color: var(--auth-input-border);
        border-radius: var(--auth-radius);
        background-color: #fff;
    }

    .auth-control:focus {
        border-color: var(--auth-button-bg);
        box-shadow: 0 0 0 .2rem rgba(217, 164, 65, .16);
    }

    .auth-password-wrap {
        position: relative;
    }

    .auth-password-wrap .auth-control {
        padding-right: 3rem;
    }

    .auth-password-toggle {
        position: absolute;
        top: 50%;
        right: .55rem;
        transform: translateY(-50%);
        display: grid;
        place-items: center;
        width: 2rem;
        height: 2rem;
        border: 0;
        background: transparent;
        color: var(--auth-link);
    }

    .auth-link {
        color: var(--auth-link);
        font-weight: 800;
        text-decoration: none;
    }

    .auth-link:hover {
        color: var(--wood);
    }

    .auth-submit {
        min-height: 48px;
        background: var(--auth-button-bg);
        border-color: var(--auth-button-bg);
        color: var(--auth-button-text);
        border-radius: var(--auth-radius);
    }

    .auth-submit:hover,
    .auth-submit:focus {
        background: var(--auth-button-hover);
        border-color: var(--auth-button-hover);
        color: var(--auth-button-text);
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
                        <span class="auth-badge"><i class="bi bi-flower1" aria-hidden="true"></i> {{ $authContent['badge'] }}</span>
                        <h1 class="display-6 fw-bold mt-4 mb-3">{{ $authContent['visual_title'] }}</h1>
                        <p class="fs-5 mb-0">{{ $authContent['visual_description'] }}</p>

                        <div class="auth-benefits">
                            @foreach($authContent['benefits'] as $benefit)
                                <div class="auth-benefit">
                                    <i class="bi {{ $benefit['icon'] }}" aria-hidden="true"></i>
                                    <div>
                                        <strong class="d-block">{{ $benefit['title'] }}</strong>
                                        <span class="text-white-50">{{ $benefit['text'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </aside>
                </div>

                <div class="col-lg-7">
                    <div class="auth-main">
                        <div class="mb-4">
                            <div class="eyebrow mb-2">{{ $authContent['eyebrow'] }}</div>
                            <h2 class="h1 fw-bold mb-2">{{ $authContent['heading'] }}</h2>
                            <p class="text-muted mb-0">{{ $authContent['description'] }}</p>
                        </div>

                        <div class="auth-tabs" role="tablist" aria-label="{{ $authContent['login_tab'] }} và {{ $authContent['register_tab'] }}">
                            <button class="auth-tab {{ $authTab === 'login' ? 'is-active' : '' }}" type="button" id="loginTab" role="tab" aria-controls="loginPanel" aria-selected="{{ $authTab === 'login' ? 'true' : 'false' }}" data-auth-tab="login">
                                {{ $authContent['login_tab'] }}
                            </button>
                            <button class="auth-tab {{ $authTab === 'register' ? 'is-active' : '' }}" type="button" id="registerTab" role="tab" aria-controls="registerPanel" aria-selected="{{ $authTab === 'register' ? 'true' : 'false' }}" data-auth-tab="register">
                                {{ $authContent['register_tab'] }}
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
                                        <div class="auth-password-wrap">
                                            <input class="form-control auth-control @error('password') is-invalid @enderror" id="loginPassword" type="password" name="password" autocomplete="current-password" required>
                                            <button class="auth-password-toggle" type="button" data-password-toggle aria-label="Hiện mật khẩu"><i class="bi bi-eye"></i></button>
                                        </div>
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
                                        <i class="bi bi-box-arrow-in-right me-1" aria-hidden="true"></i> {{ $authContent['login_button'] }}
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
                                            <div class="auth-password-wrap">
                                                <input class="form-control auth-control @error('password') is-invalid @enderror" id="registerPassword" type="password" name="password" autocomplete="new-password" required>
                                                <button class="auth-password-toggle" type="button" data-password-toggle aria-label="Hiện mật khẩu"><i class="bi bi-eye"></i></button>
                                            </div>
                                            @if($authTab === 'register')
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            @endif
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label auth-form-label" for="registerPasswordConfirm">Xác nhận mật khẩu</label>
                                            <div class="auth-password-wrap">
                                                <input class="form-control auth-control" id="registerPasswordConfirm" type="password" name="password_confirmation" autocomplete="new-password" required>
                                                <button class="auth-password-toggle" type="button" data-password-toggle aria-label="Hiện mật khẩu"><i class="bi bi-eye"></i></button>
                                            </div>
                                        </div>
                                    </div>

                                    <button class="btn btn-primary auth-submit w-100 mt-4" type="submit">
                                        <i class="bi bi-person-plus-fill me-1" aria-hidden="true"></i> {{ $authContent['register_button'] }}
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

        document.querySelectorAll('[data-password-toggle]').forEach((button) => {
            button.addEventListener('click', () => {
                const input = button.parentElement.querySelector('input');
                const visible = input.type === 'text';
                input.type = visible ? 'password' : 'text';
                button.innerHTML = visible ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
            });
        });
    })();
</script>
@endsection
