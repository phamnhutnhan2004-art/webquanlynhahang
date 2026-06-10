@extends('layouts.app')

@section('title', 'Quên mật khẩu')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7 col-lg-5">
        <div class="card">
            <div class="card-body p-4">
                <h1 class="h3 mb-4">Quên mật khẩu</h1>
                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email tài khoản</label>
                        <input class="form-control" type="email" name="email" value="{{ old('email') }}" required>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Tạo liên kết đặt lại</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
