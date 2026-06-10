@extends('layouts.app')

@section('title', 'Đặt lại mật khẩu')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7 col-lg-5">
        <div class="card">
            <div class="card-body p-4">
                <h1 class="h3 mb-4">Đặt lại mật khẩu</h1>
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input class="form-control" type="email" name="email" value="{{ old('email', $email) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu mới</label>
                        <input class="form-control" type="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nhập lại mật khẩu mới</label>
                        <input class="form-control" type="password" name="password_confirmation" required>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Cập nhật mật khẩu</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
