@extends('layouts.app')

@section('title', 'Liên kết đặt lại mật khẩu')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body p-4">
                <h1 class="h3">Liên kết đặt lại mật khẩu</h1>
                <p class="text-muted">Hệ thống chưa cấu hình email, nên liên kết đặt lại mật khẩu được hiển thị trực tiếp để kiểm thử trên máy cục bộ.</p>
                <a class="btn btn-primary" href="{{ $resetUrl }}">Mở trang đặt lại mật khẩu</a>
            </div>
        </div>
    </div>
</div>
@endsection
