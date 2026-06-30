@extends('layouts.app')

@section('title', 'Xác thực Email')

@section('content')
<style>
    .verify-page {
        min-height: calc(100vh - 92px);
        display: grid;
        align-items: center;
        padding: clamp(2rem, 6vw, 4.5rem) 0;
    }

    .verify-card {
        max-width: 680px;
        margin: 0 auto;
        overflow: hidden;
        border: 1px solid rgba(217, 164, 65, .34);
        border-radius: 8px;
        background: rgba(255, 250, 240, .97);
        box-shadow: 0 30px 80px rgba(44, 27, 18, .14);
    }

    .verify-head {
        padding: clamp(1.4rem, 4vw, 2.2rem);
        background:
            linear-gradient(135deg, rgba(14, 59, 50, .96), rgba(44, 27, 18, .86)),
            url("{{ asset('images/restaurant-interior.png') }}") center / cover;
        color: #fff;
    }

    .verify-badge {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        color: var(--gold-soft);
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
        font-size: .78rem;
    }

    .verify-body {
        padding: clamp(1.25rem, 4vw, 2.25rem);
    }

    .otp-input {
        height: 58px;
        border-radius: 8px;
        border-color: rgba(90, 52, 30, .24);
        text-align: center;
        font-size: 1.8rem;
        font-weight: 900;
        letter-spacing: .42em;
        color: var(--green);
    }

    .otp-input:focus {
        border-color: var(--gold);
        box-shadow: 0 0 0 .2rem rgba(217, 164, 65, .16);
    }

    .verify-note {
        border-radius: 8px;
        background: rgba(14, 59, 50, .08);
        color: var(--wood-dark);
        padding: .9rem 1rem;
    }
</style>

<section class="verify-page">
    <div class="container">
        <div class="verify-card">
            <div class="verify-head">
                <span class="verify-badge"><i class="bi bi-shield-check" aria-hidden="true"></i> Xác thực Email</span>
                <h1 class="display-6 fw-bold mt-3 mb-2">Nhập mã OTP từ email</h1>
                <p class="mb-0 text-white-50">Mã xác thực gồm 6 chữ số và chỉ có hiệu lực trong 5 phút.</p>
            </div>

            <div class="verify-body">
                @if(session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="verify-note mb-4">
                    Mã đã được gửi đến <strong>{{ $email }}</strong>. Vui lòng kiểm tra hộp thư đến hoặc thư rác.
                </div>

                <form method="POST" action="{{ route('verification.verify') }}" class="d-grid gap-3">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">

                    <div>
                        <label class="form-label fw-bold" for="otpCode">Mã xác thực</label>
                        <input
                            class="form-control otp-input @error('otp_code') is-invalid @enderror"
                            id="otpCode"
                            name="otp_code"
                            inputmode="numeric"
                            maxlength="6"
                            pattern="[0-9]{6}"
                            autocomplete="one-time-code"
                            value="{{ old('otp_code') }}"
                            required
                            autofocus
                        >
                        @error('otp_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button class="btn btn-primary btn-lg" type="submit">
                        <i class="bi bi-check2-circle me-1" aria-hidden="true"></i> Xác thực tài khoản
                    </button>
                </form>

                <form method="POST" action="{{ route('verification.resend') }}" class="mt-3 text-center">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">
                    <button class="btn btn-link fw-bold text-decoration-none" type="submit">
                        Gửi lại mã xác thực
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
