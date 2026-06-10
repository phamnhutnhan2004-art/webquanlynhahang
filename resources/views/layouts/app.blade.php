<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Nhà hàng World')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f6f7fb; }
        .navbar { box-shadow: 0 2px 16px rgba(15, 23, 42, .08); }
        .card { border: 0; border-radius: 8px; box-shadow: 0 8px 24px rgba(15, 23, 42, .08); }
        .stat-card { min-height: 132px; }
        .table-responsive { border-radius: 8px; overflow: hidden; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">Nhà hàng World</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Menu</a></li>
                @auth
                    @if(auth()->user()->isAdmin())
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Admin</a></li>
                    @elseif(auth()->user()->isStaff())
                        <li class="nav-item"><a class="nav-link" href="{{ route('staff.dashboard') }}">Nhân viên</a></li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('customer.dashboard') }}">Tài khoản</a></li>
                    @endif
                @endauth
            </ul>
            <div class="d-flex gap-2 align-items-center">
                @guest
                    <a class="btn btn-outline-primary btn-sm" href="{{ route('login') }}">Đăng nhập</a>
                    <a class="btn btn-primary btn-sm" href="{{ route('register') }}">Đăng ký</a>
                @else
                    <span class="small text-muted d-none d-md-inline">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-outline-danger btn-sm" type="submit">Đăng xuất</button>
                    </form>
                @endguest
            </div>
        </div>
    </div>
</nav>

<main class="container py-4">
    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
