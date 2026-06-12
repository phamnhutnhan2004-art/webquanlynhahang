<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Nhà hàng World')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --ivory: #fffaf0;
            --cream: #f6efe0;
            --wood: #5a341e;
            --wood-dark: #2c1b12;
            --green: #0e3b32;
            --green-soft: #e7f0ea;
            --gold: #d9a441;
            --gold-soft: #f6df9d;
            --ink: #221812;
            --muted: #756a5e;
            --line: rgba(90, 52, 30, .16);
        }

        html { scroll-behavior: smooth; }

        body {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--ivory), #f3eadb 50%, #fffaf0);
            color: var(--ink);
            font-family: "Segoe UI", system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .navbar {
            background: rgba(14, 59, 50, .94);
            border-bottom: 1px solid rgba(217, 164, 65, .32);
            backdrop-filter: blur(14px);
            box-shadow: 0 14px 34px rgba(18, 11, 7, .18);
        }

        .navbar-brand,
        .nav-link,
        .navbar .small {
            color: #fff !important;
        }

        .navbar-brand {
            font-weight: 900;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .nav-link {
            font-weight: 800;
            text-transform: uppercase;
            font-size: .86rem;
            letter-spacing: 0;
            opacity: .88;
        }

        .nav-link.active,
        .nav-link:hover {
            color: var(--gold-soft) !important;
            opacity: 1;
        }

        .btn {
            border-radius: 8px;
            font-weight: 800;
        }

        .btn-primary {
            background: var(--gold);
            border-color: var(--gold);
            color: var(--wood-dark);
        }

        .btn-primary:hover {
            background: #f0bd55;
            border-color: #f0bd55;
            color: var(--wood-dark);
            transform: translateY(-1px);
        }

        .btn-outline-primary {
            border-color: var(--green);
            color: var(--green);
        }

        .btn-outline-primary:hover {
            background: var(--green);
            border-color: var(--green);
            color: #fff;
        }

        .card {
            border: 1px solid var(--line);
            border-radius: 8px;
            box-shadow: 0 18px 45px rgba(44, 27, 18, .08);
            overflow: hidden;
        }

        .hero-full {
            min-height: calc(100vh - 72px);
            display: grid;
            align-items: end;
            margin: -1.5rem 0 4rem;
            padding: clamp(2rem, 6vw, 5.5rem);
            border-radius: 0 0 8px 8px;
            background:
                linear-gradient(90deg, rgba(17, 10, 6, .82) 0%, rgba(14, 59, 50, .58) 44%, rgba(14, 59, 50, .12) 100%),
                url("{{ asset('images/hero-restaurant.png') }}") center / cover;
            color: #fff;
        }

        .hero-full p,
        .page-hero p {
            color: rgba(255, 255, 255, .82);
        }

        .page-hero {
            border: 1px solid rgba(217, 164, 65, .3);
            border-radius: 8px;
            padding: clamp(24px, 5vw, 48px);
            background:
                linear-gradient(135deg, rgba(44, 27, 18, .94), rgba(14, 59, 50, .9)),
                url("{{ asset('images/restaurant-interior.png') }}") center / cover;
            color: #fff;
            overflow: hidden;
        }

        .eyebrow {
            color: var(--gold-soft);
            font-size: .78rem;
            font-weight: 900;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .section-pad {
            padding: 3.5rem 0;
        }

        .section-title {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.4rem;
        }

        .section-title h2 {
            color: var(--wood-dark);
            font-weight: 900;
        }

        .stat-card {
            min-height: 128px;
            background: rgba(255, 255, 255, .92);
        }

        .stat-value {
            font-size: clamp(1.9rem, 4vw, 2.45rem);
            font-weight: 900;
            color: var(--green);
        }

        .food-card,
        .media-card {
            transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
        }

        .food-card:hover,
        .media-card:hover {
            transform: translateY(-6px);
            border-color: rgba(217, 164, 65, .6);
            box-shadow: 0 26px 65px rgba(44, 27, 18, .16);
        }

        .food-img,
        .media-img {
            width: 100%;
            aspect-ratio: 4 / 3;
            object-fit: cover;
            display: block;
        }

        .gallery-img {
            width: 100%;
            aspect-ratio: 16 / 10;
            object-fit: cover;
            display: block;
        }

        .status-badge {
            border-radius: 999px;
            padding: .4rem .65rem;
            background: var(--green-soft);
            color: var(--green);
            font-weight: 800;
            white-space: nowrap;
        }

        .gold-text { color: var(--gold); }

        .muted-box {
            background: rgba(255, 255, 255, .72);
            border: 1px dashed var(--line);
            border-radius: 8px;
            padding: 1rem;
        }

        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }

        .contact-band {
            background: var(--green);
            color: #fff;
            border-radius: 8px;
            padding: clamp(1.5rem, 4vw, 3rem);
        }

        .contact-band a,
        .contact-band .text-muted {
            color: rgba(255, 255, 255, .78) !important;
        }

        @media (max-width: 991.98px) {
            .hero-full {
                min-height: 78vh;
                margin-top: -1rem;
            }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">Nhà hàng World</a>
        <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="Mở menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Trang chủ</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#mon-an">Thực đơn</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#menu-hinh-anh">Menu nhà hàng</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#hinh-anh">Hình ảnh</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#lien-he">Liên hệ</a></li>
                @auth
                    @if(auth()->user()->isAdmin())
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Quản trị</a></li>
                    @elseif(auth()->user()->isStaff())
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('staff.*') ? 'active' : '' }}" href="{{ route('staff.dashboard') }}">Nhân viên</a></li>
                    @else
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('customer.*') ? 'active' : '' }}" href="{{ route('customer.dashboard') }}">Tài khoản</a></li>
                    @endif
                @endauth
            </ul>
            <div class="d-flex gap-2 align-items-center">
                @guest
                    <a class="btn btn-outline-light btn-sm" href="{{ route('login') }}">Đăng nhập</a>
                    <a class="btn btn-primary btn-sm" href="{{ route('register') }}">Đặt bàn</a>
                @else
                    <span class="small d-none d-md-inline">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-outline-light btn-sm" type="submit">Đăng xuất</button>
                    </form>
                @endguest
            </div>
        </div>
    </div>
</nav>

<main class="container-fluid px-0">
    <div class="container pt-4">
        @if(session('status'))
            <div class="alert alert-success border-0 shadow-sm">{{ session('status') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
