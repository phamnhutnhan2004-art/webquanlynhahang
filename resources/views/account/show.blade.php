@extends('layouts.app')

@section('title', 'Tài khoản của tôi')

@section('content')
<style>
    .account-page {
        padding: clamp(2rem, 6vw, 4.5rem) 0;
    }

    .account-card {
        border: 1px solid rgba(217, 164, 65, .28);
        border-radius: 8px;
        background: rgba(255, 250, 240, .97);
        box-shadow: 0 24px 70px rgba(44, 27, 18, .12);
        overflow: hidden;
    }

    .account-head {
        padding: clamp(1.4rem, 4vw, 2.2rem);
        background:
            linear-gradient(135deg, rgba(14, 59, 50, .96), rgba(44, 27, 18, .82)),
            url("{{ asset('images/restaurant-interior.png') }}") center / cover;
        color: #fff;
    }

    .account-body {
        padding: clamp(1.25rem, 4vw, 2.25rem);
    }

    .info-tile {
        border-radius: 8px;
        background: rgba(14, 59, 50, .08);
        padding: 1rem;
        height: 100%;
    }

    .password-wrap {
        position: relative;
    }

    .password-wrap .form-control {
        padding-right: 3rem;
    }

    .password-toggle {
        position: absolute;
        top: 50%;
        right: .55rem;
        transform: translateY(-50%);
        border: 0;
        background: transparent;
        color: var(--green);
        width: 2rem;
        height: 2rem;
    }
</style>

<section class="account-page">
    <div class="container">
        <div class="account-card">
            <div class="account-head">
                <div class="eyebrow mb-2">Tài khoản của tôi</div>
                <h1 class="display-6 fw-bold mb-2">{{ $user->name }}</h1>
                <p class="mb-0 text-white-50">{{ $user->role?->name }} · {{ $user->status }}</p>
            </div>

            <div class="account-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="info-tile">
                            <div class="fw-bold">Email</div>
                            <div>{{ $user->email }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-tile">
                            <div class="fw-bold">Số điện thoại</div>
                            <div>{{ $user->phone }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-tile">
                            <div class="fw-bold">Ngày tạo</div>
                            <div>{{ $user->created_at?->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-lg-7">
                        <h2 class="h4 fw-bold mb-3">Cập nhật thông tin</h2>
                        <form method="POST" action="{{ route('account.update') }}" class="row g-3">
                            @csrf
                            @method('PUT')
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Họ và tên</label>
                                <input class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email</label>
                                <input class="form-control" type="email" name="email" value="{{ old('email', $user->email) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Số điện thoại</label>
                                <input class="form-control" name="phone" value="{{ old('phone', $user->phone) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Địa chỉ</label>
                                <input class="form-control" name="address" value="{{ old('address', $user->address) }}">
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary" type="submit"><i class="bi bi-save me-1"></i>Lưu thông tin</button>
                            </div>
                        </form>
                    </div>

                    <div class="col-lg-5">
                        <h2 class="h4 fw-bold mb-3">Đổi mật khẩu</h2>
                        <form method="POST" action="{{ route('account.password') }}" class="d-grid gap-3">
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="form-label fw-bold">Mật khẩu hiện tại</label>
                                <div class="password-wrap">
                                    <input class="form-control" type="password" name="current_password" required autocomplete="current-password">
                                    <button class="password-toggle" type="button" data-password-toggle aria-label="Hiện mật khẩu"><i class="bi bi-eye"></i></button>
                                </div>
                            </div>
                            <div>
                                <label class="form-label fw-bold">Mật khẩu mới</label>
                                <div class="password-wrap">
                                    <input class="form-control" type="password" name="password" required minlength="8" autocomplete="new-password">
                                    <button class="password-toggle" type="button" data-password-toggle aria-label="Hiện mật khẩu"><i class="bi bi-eye"></i></button>
                                </div>
                            </div>
                            <div>
                                <label class="form-label fw-bold">Xác nhận mật khẩu</label>
                                <div class="password-wrap">
                                    <input class="form-control" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password">
                                    <button class="password-toggle" type="button" data-password-toggle aria-label="Hiện mật khẩu"><i class="bi bi-eye"></i></button>
                                </div>
                            </div>
                            <button class="btn btn-primary" type="submit"><i class="bi bi-key me-1"></i>Đổi mật khẩu</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const input = button.parentElement.querySelector('input');
            const visible = input.type === 'text';
            input.type = visible ? 'password' : 'text';
            button.innerHTML = visible ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
        });
    });
</script>
@endsection
