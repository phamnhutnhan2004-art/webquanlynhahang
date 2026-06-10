@extends('layouts.app')

@section('title', 'Đăng ký')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body p-4">
                <h1 class="h3 mb-4">Đăng ký tài khoản khách hàng</h1>
                <form method="POST" action="{{ route('register.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Họ tên</label>
                            <input class="form-control" name="name" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Số điện thoại</label>
                            <input class="form-control" name="phone" value="{{ old('phone') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Email</label>
                            <input class="form-control" type="email" name="email" value="{{ old('email') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Địa chỉ</label>
                            <input class="form-control" name="address" value="{{ old('address') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mật khẩu</label>
                            <input class="form-control" type="password" name="password" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nhập lại mật khẩu</label>
                            <input class="form-control" type="password" name="password_confirmation" required>
                        </div>
                    </div>
                    <button class="btn btn-primary w-100 mt-4" type="submit">Tạo tài khoản</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
