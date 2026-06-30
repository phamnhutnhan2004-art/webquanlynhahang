<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Quản trị - Website Quản lý Nhà hàng Hoa Sen')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;600;700;800;900&family=Inter:wght@400;600;700;800;900&family=Montserrat:wght@400;600;700;800;900&family=Open+Sans:wght@400;600;700;800&family=Poppins:wght@400;600;700;800;900&family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --admin-bg: #f5f7fb;
            --admin-panel: #ffffff;
            --admin-sidebar: #103b34;
            --admin-sidebar-deep: #092721;
            --admin-accent: #d9a441;
            --admin-ink: #17201d;
            --admin-muted: #4f5b56;
            --admin-line: #e3e8ee;
            --admin-blue: #2563eb;
            --admin-coral: #e76f51;
            --admin-teal: #0f766e;
            --admin-radius: 8px;
            --admin-shadow: 0 18px 42px rgba(15, 34, 29, .08);
        }

        body {
            min-height: 100vh;
            margin: 0;
            background: var(--admin-bg);
            color: var(--admin-ink);
            font-family: "Segoe UI", system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.58;
        }

        p,
        li {
            line-height: 1.68;
        }

        .text-muted {
            color: var(--admin-muted) !important;
            font-weight: 650;
        }

        a { text-decoration: none; }

        .admin-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 280px minmax(0, 1fr);
        }

        .admin-sidebar {
            position: sticky;
            top: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            background: linear-gradient(180deg, var(--admin-sidebar), var(--admin-sidebar-deep));
            color: #fff;
            border-right: 1px solid rgba(255, 255, 255, .08);
            z-index: 1030;
        }

        .admin-brand {
            display: flex;
            align-items: center;
            gap: .8rem;
            padding: 1.2rem 1.15rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, .1);
        }

        .admin-brand-mark {
            width: 42px;
            aspect-ratio: 1;
            display: grid;
            place-items: center;
            border-radius: var(--admin-radius);
            background: var(--admin-accent);
            color: #1d1308;
            font-size: 1.35rem;
        }

        .admin-brand-title {
            font-size: .98rem;
            font-weight: 900;
            line-height: 1.2;
        }

        .admin-brand-subtitle {
            color: rgba(255, 255, 255, .9);
            font-size: .78rem;
            font-weight: 700;
        }

        .admin-menu {
            flex: 1;
            overflow-y: auto;
            padding: .75rem;
        }

        .admin-menu-link {
            display: flex;
            align-items: center;
            gap: .75rem;
            min-height: 42px;
            padding: .58rem .75rem;
            border-radius: var(--admin-radius);
            color: rgba(255, 255, 255, .92);
            font-weight: 750;
            font-size: .92rem;
        }

        .admin-menu-link i {
            width: 1.25rem;
            color: rgba(217, 164, 65, .94);
            font-size: 1.05rem;
            text-align: center;
        }

        .admin-menu-link:hover,
        .admin-menu-link.is-active {
            background: rgba(255, 255, 255, .12);
            color: #fff;
        }

        .admin-menu-link.is-active {
            box-shadow: inset 3px 0 0 var(--admin-accent);
        }

        .admin-main {
            min-width: 0;
            display: grid;
            grid-template-rows: auto 1fr auto;
        }

        .admin-topbar {
            position: sticky;
            top: 0;
            z-index: 1020;
            min-height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: .9rem 1.25rem;
            background: rgba(255, 255, 255, .92);
            border-bottom: 1px solid var(--admin-line);
            backdrop-filter: blur(14px);
        }

        .admin-page {
            padding: 1.25rem;
        }

        .admin-footer {
            padding: 1rem 1.25rem 1.4rem;
            color: var(--admin-muted);
            font-size: .88rem;
        }

        .admin-page-head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .admin-kicker {
            color: var(--admin-teal);
            font-size: .78rem;
            font-weight: 900;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .admin-title {
            margin: .2rem 0 0;
            font-size: clamp(1.45rem, 2.5vw, 2rem);
            font-weight: 900;
            letter-spacing: 0;
        }

        .admin-subtitle {
            margin: .35rem 0 0;
            color: var(--admin-muted);
            max-width: 680px;
        }

        .admin-card {
            background: var(--admin-panel);
            border: 1px solid var(--admin-line);
            border-radius: var(--admin-radius);
            color: var(--admin-ink);
            box-shadow: var(--admin-shadow);
        }

        .admin-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem 1rem .85rem;
            border-bottom: 1px solid var(--admin-line);
        }

        .admin-card-body {
            padding: 1rem;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .stat-tile {
            display: grid;
            grid-template-columns: auto 1fr;
            align-items: center;
            gap: .85rem;
            min-height: 126px;
            padding: 1rem;
            background: #fff;
            border: 1px solid var(--admin-line);
            border-radius: var(--admin-radius);
            box-shadow: var(--admin-shadow);
        }

        .stat-icon {
            width: 48px;
            aspect-ratio: 1;
            display: grid;
            place-items: center;
            border-radius: var(--admin-radius);
            color: #fff;
            font-size: 1.25rem;
        }

        .stat-icon.green { background: var(--admin-teal); }
        .stat-icon.gold { background: var(--admin-accent); color: #1f1607; }
        .stat-icon.blue { background: var(--admin-blue); }
        .stat-icon.coral { background: var(--admin-coral); }

        .stat-label {
            color: var(--admin-muted);
            font-weight: 850;
            font-size: .86rem;
        }

        .stat-value {
            margin-top: .15rem;
            font-size: clamp(1.35rem, 3vw, 2rem);
            font-weight: 950;
            line-height: 1.1;
        }

        .chart-card {
            min-height: 320px;
        }

        .bar-chart {
            display: grid;
            grid-template-columns: repeat(var(--bars, 6), minmax(32px, 1fr));
            align-items: end;
            gap: .7rem;
            min-height: 220px;
            padding-top: .75rem;
        }

        .chart-bar {
            display: grid;
            align-items: end;
            justify-items: center;
            gap: .45rem;
            height: 100%;
            color: var(--admin-muted);
            font-size: .78rem;
            font-weight: 850;
        }

        .chart-fill {
            width: 100%;
            max-width: 46px;
            min-height: 12px;
            height: calc(var(--height, 0) * 1%);
            border-radius: 8px 8px 3px 3px;
            background: linear-gradient(180deg, var(--admin-teal), #31b59d);
        }

        .chart-fill.alt {
            background: linear-gradient(180deg, var(--admin-blue), #79a7ff);
        }

        .progress-list {
            display: grid;
            gap: .85rem;
            padding-top: .4rem;
        }

        .progress-row {
            display: grid;
            gap: .4rem;
        }

        .progress-meta {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            color: var(--admin-muted);
            font-size: .88rem;
            font-weight: 850;
        }

        .progress-track {
            height: 10px;
            overflow: hidden;
            border-radius: 999px;
            background: #edf1f6;
        }

        .progress-fill {
            height: 100%;
            width: calc(var(--width, 0) * 1%);
            border-radius: inherit;
            background: linear-gradient(90deg, var(--admin-accent), var(--admin-coral));
        }

        .admin-table-toolbar {
            display: grid;
            grid-template-columns: minmax(220px, 1fr) 180px auto;
            gap: .75rem;
            margin-bottom: .9rem;
        }

        .admin-table th {
            color: #4b5753;
            font-size: .78rem;
            text-transform: uppercase;
            white-space: nowrap;
            background: #f8fafc;
            cursor: pointer;
        }

        .admin-table td {
            vertical-align: middle;
        }

        .admin-table .action-cell {
            width: 1%;
            white-space: nowrap;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .35rem .55rem;
            border-radius: 999px;
            background: #e9f6f2;
            color: #0f766e;
            font-size: .8rem;
            font-weight: 850;
            white-space: nowrap;
        }

        .table-status-form {
            display: grid;
            grid-template-columns: minmax(145px, 1fr) 38px;
            gap: .4rem;
            align-items: center;
            min-width: 205px;
        }

        .table-status-form .btn {
            width: 38px;
            aspect-ratio: 1;
            padding: 0;
            display: inline-grid;
            place-items: center;
        }

        .avatar {
            width: 42px;
            aspect-ratio: 1;
            display: grid;
            place-items: center;
            border-radius: 50%;
            background: var(--admin-sidebar);
            color: #fff;
            font-weight: 950;
        }

        .thumb {
            width: 54px;
            aspect-ratio: 1;
            object-fit: cover;
            border-radius: var(--admin-radius);
            border: 1px solid var(--admin-line);
        }

        .soft-note {
            padding: 1rem;
            border: 1px dashed var(--admin-line);
            border-radius: var(--admin-radius);
            background: #fbfcfe;
            color: var(--admin-muted);
            font-weight: 750;
            line-height: 1.65;
        }

        @media (max-width: 1199.98px) {
            .stat-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 991.98px) {
            .admin-shell { grid-template-columns: 1fr; }

            .admin-sidebar {
                position: fixed;
                inset: 0 auto 0 0;
                width: min(300px, calc(100vw - 2rem));
                transform: translateX(-102%);
                transition: transform .2s ease;
            }

            .admin-sidebar.is-open { transform: translateX(0); }

            .admin-overlay {
                position: fixed;
                inset: 0;
                display: none;
                background: rgba(3, 10, 8, .42);
                z-index: 1025;
            }

            .admin-overlay.is-open { display: block; }
        }

        @media (max-width: 767.98px) {
            .admin-page { padding: 1rem .75rem; }
            .admin-page-head { align-items: stretch; flex-direction: column; }
            .stat-grid { grid-template-columns: 1fr; }
            .admin-table-toolbar { grid-template-columns: 1fr; }
            .admin-topbar { padding-inline: .75rem; }
        }
    </style>
    @stack('styles')
    <link href="{{ asset('css/hoa-sen-ui.css') }}?v={{ is_file(public_path('css/hoa-sen-ui.css')) ? filemtime(public_path('css/hoa-sen-ui.css')) : '1' }}" rel="stylesheet">
</head>
<body>
<div class="page-loader" data-page-loader aria-label="Đang tải trang" role="status">
    <div class="loader-card">
        <div class="loader-logo"><i class="bi bi-shop" aria-hidden="true"></i></div>
        <div class="loader-title">Quản trị Hoa Sen</div>
        <div class="loader-ring" aria-hidden="true"></div>
    </div>
</div>
@php
    $adminSections = [
        ['label' => 'Bảng điều khiển', 'icon' => 'bi-speedometer2', 'route' => route('admin.dashboard'), 'active' => request()->routeIs('admin.dashboard')],
        ['label' => 'Quản lý món ăn', 'icon' => 'bi-egg-fried', 'route' => route('admin.section', 'products'), 'active' => request()->is('admin/products')],
        ['label' => 'Quản lý danh mục', 'icon' => 'bi-folder2-open', 'route' => route('admin.section', 'categories'), 'active' => request()->is('admin/categories')],
        ['label' => 'Quản lý bàn ăn', 'icon' => 'bi-grid-3x3-gap', 'route' => route('admin.section', 'tables'), 'active' => request()->is('admin/tables')],
        ['label' => 'Quản lý đặt bàn', 'icon' => 'bi-calendar-check', 'route' => route('admin.section', 'reservations'), 'active' => request()->is('admin/reservations')],
        ['label' => 'Quản lý đặt tiệc tại nhà', 'icon' => 'bi-stars', 'route' => route('admin.section', 'home-parties'), 'active' => request()->is('admin/home-parties')],
        ['label' => 'Quản lý đơn hàng', 'icon' => 'bi-receipt-cutoff', 'route' => route('admin.section', 'orders'), 'active' => request()->is('admin/orders')],
        ['label' => 'Quản lý tài khoản', 'icon' => 'bi-person-gear', 'route' => route('admin.section', 'accounts'), 'active' => request()->is('admin/accounts')],
        ['label' => 'Quản lý khách hàng', 'icon' => 'bi-people', 'route' => route('admin.section', 'customers'), 'active' => request()->is('admin/customers')],
        ['label' => 'Quản lý nhân viên', 'icon' => 'bi-person-badge', 'route' => route('admin.section', 'employees'), 'active' => request()->is('admin/employees')],
        ['label' => 'Quản lý thanh toán', 'icon' => 'bi-credit-card-2-front', 'route' => route('admin.section', 'payments'), 'active' => request()->is('admin/payments')],
        ['label' => 'Quản lý phương thức thanh toán', 'icon' => 'bi-qr-code', 'route' => route('admin.section', 'payment-methods'), 'active' => request()->is('admin/payment-methods')],
        ['label' => 'Quản lý Chatbot', 'icon' => 'bi-robot', 'route' => route('admin.section', 'chatbot'), 'active' => request()->is('admin/chatbot')],
        ['label' => 'Cấu hình AI Chatbot', 'icon' => 'bi-cpu', 'route' => route('admin.section', 'ai-chatbot'), 'active' => request()->is('admin/ai-chatbot')],
        ['label' => 'Cài đặt giao diện', 'icon' => 'bi-palette', 'route' => route('admin.section', 'theme-settings'), 'active' => request()->is('admin/theme-settings')],
        ['label' => 'Giao diện đăng nhập', 'icon' => 'bi-window-sidebar', 'route' => route('admin.section', 'auth-interface'), 'active' => request()->is('admin/auth-interface')],
        ['label' => 'Quản lý thư viện ảnh', 'icon' => 'bi-images', 'route' => route('admin.section', 'gallery-images'), 'active' => request()->is('admin/gallery-images')],
        ['label' => 'Quản lý tin tức', 'icon' => 'bi-newspaper', 'route' => route('admin.section', 'news'), 'active' => request()->is('admin/news')],
        ['label' => 'Thống kê và báo cáo', 'icon' => 'bi-graph-up-arrow', 'route' => route('admin.section', 'stats'), 'active' => request()->is('admin/stats')],
        ['label' => 'Tài khoản của tôi', 'icon' => 'bi-person-circle', 'route' => route('account.show'), 'active' => request()->routeIs('account.*')],
    ];
@endphp

<div class="admin-overlay" data-admin-sidebar-close></div>
<div class="admin-shell">
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="admin-brand">
            <div class="admin-brand-mark"><i class="bi bi-shop"></i></div>
            <div>
                <div class="admin-brand-title">Nhà hàng Hoa Sen</div>
                <div class="admin-brand-subtitle">Bảng quản trị</div>
            </div>
        </div>

        <nav class="admin-menu" aria-label="Menu quản trị">
            @foreach($adminSections as $item)
                <a class="admin-menu-link {{ $item['active'] ? 'is-active' : '' }}" href="{{ $item['route'] }}">
                    <i class="bi {{ $item['icon'] }}"></i>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach

            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button class="admin-menu-link border-0 w-100 text-start" type="submit">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Đăng xuất</span>
                </button>
            </form>
        </nav>
    </aside>

    <div class="admin-main">
        <header class="admin-topbar">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-outline-secondary d-lg-none" type="button" data-admin-sidebar-toggle aria-label="Mở menu quản trị">
                    <i class="bi bi-list"></i>
                </button>
                <div>
                    <div class="fw-black fw-bold">Quản trị hệ thống</div>
                    <div class="small text-muted">Website Quản lý Nhà hàng Hoa Sen</div>
                </div>
            </div>

            <div class="dropdown">
                <button class="btn border-0 d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</span>
                    <span class="text-start d-none d-sm-block">
                        <span class="d-block fw-bold">{{ auth()->user()->name ?? 'Quản trị viên' }}</span>
                        <span class="d-block small text-muted">Admin</span>
                    </span>
                    <i class="bi bi-chevron-down small text-muted"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <li><a class="dropdown-item" href="{{ route('admin.section', 'settings') }}#ho-so"><i class="bi bi-person me-2"></i>Hồ sơ cá nhân</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.section', 'settings') }}#doi-mat-khau"><i class="bi bi-shield-lock me-2"></i>Đổi mật khẩu</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="dropdown-item text-danger" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</button>
                        </form>
                    </li>
                </ul>
            </div>
        </header>

        <main class="admin-page">
            @if(session('status'))
                <div class="alert alert-success border-0 shadow-sm">{{ session('status') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger border-0 shadow-sm">{{ session('error') }}</div>
            @endif

            @if(isset($errors) && $errors->any())
                <div class="alert alert-danger border-0 shadow-sm">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="admin-footer">
            © {{ date('Y') }} Website Quản lý Nhà hàng Hoa Sen. Giao diện quản trị tối ưu cho vận hành nhà hàng.
        </footer>
    </div>
</div>

<div class="modal fade" id="adminDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h2 class="modal-title h5">Chi tiết dữ liệu</h2>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body" id="adminDetailBody"></div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/hoa-sen-ui.js') }}?v={{ is_file(public_path('js/hoa-sen-ui.js')) ? filemtime(public_path('js/hoa-sen-ui.js')) : '1' }}"></script>
<script>
    (() => {
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.querySelector('[data-admin-sidebar-close]');
        const openSidebar = () => {
            sidebar?.classList.add('is-open');
            overlay?.classList.add('is-open');
        };
        const closeSidebar = () => {
            sidebar?.classList.remove('is-open');
            overlay?.classList.remove('is-open');
        };

        document.querySelector('[data-admin-sidebar-toggle]')?.addEventListener('click', openSidebar);
        overlay?.addEventListener('click', closeSidebar);

        document.querySelectorAll('[data-admin-table]').forEach((scope) => {
            const table = scope.querySelector('table');
            const tbody = table?.querySelector('tbody');
            const search = scope.querySelector('[data-table-search]');
            const filters = Array.from(scope.querySelectorAll('[data-table-filter]'));
            const pageSize = scope.querySelector('[data-table-size]');
            const pager = scope.querySelector('[data-table-pager]');
            const rows = Array.from(tbody?.querySelectorAll('tr') || []);
            let currentPage = 1;
            let sortIndex = -1;
            let sortDirection = 1;

            const normalize = (text) => (text || '').toString().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');

            const render = () => {
                const keyword = normalize(search?.value);
                const limit = Number(pageSize?.value || 10);
                let visible = rows.filter((row) => {
                    const matchesSearch = !keyword || normalize(row.innerText).includes(keyword);
                    const matchesFilters = filters.every((filter) => {
                        const value = filter.value || '';
                        const key = filter.dataset.filterKey || 'status';

                        return !value || row.dataset[key] === value;
                    });

                    return matchesSearch && matchesFilters;
                });

                if (sortIndex >= 0) {
                    visible = visible.sort((a, b) => {
                        const left = normalize(a.children[sortIndex]?.innerText);
                        const right = normalize(b.children[sortIndex]?.innerText);
                        return left.localeCompare(right, 'vi', { numeric: true }) * sortDirection;
                    });
                }

                const totalPages = Math.max(1, Math.ceil(visible.length / limit));
                currentPage = Math.min(currentPage, totalPages);
                const start = (currentPage - 1) * limit;
                const pageRows = visible.slice(start, start + limit);

                rows.forEach((row) => row.classList.add('d-none'));
                pageRows.forEach((row) => row.classList.remove('d-none'));

                if (pager) {
                    pager.innerHTML = '';
                    for (let page = 1; page <= totalPages; page++) {
                        const button = document.createElement('button');
                        button.type = 'button';
                        button.className = `btn btn-sm ${page === currentPage ? 'btn-primary' : 'btn-outline-secondary'}`;
                        button.textContent = page;
                        button.addEventListener('click', () => {
                            currentPage = page;
                            render();
                        });
                        pager.appendChild(button);
                    }
                }
            };

            table?.querySelectorAll('thead th').forEach((header, index) => {
                if (header.dataset.noSort !== undefined) {
                    return;
                }

                header.addEventListener('click', () => {
                    sortDirection = sortIndex === index ? sortDirection * -1 : 1;
                    sortIndex = index;
                    render();
                });
            });

            [search, pageSize, ...filters].forEach((input) => input?.addEventListener('input', () => {
                currentPage = 1;
                render();
            }));

            render();
        });

        const detailModal = document.getElementById('adminDetailModal');
        const detailBody = document.getElementById('adminDetailBody');

        document.querySelectorAll('[data-detail]').forEach((button) => {
            button.addEventListener('click', () => {
                const payload = JSON.parse(button.dataset.detail || '{}');
                detailBody.innerHTML = Object.entries(payload).map(([label, value]) => `
                    <div class="row py-2 border-bottom">
                        <div class="col-sm-4 fw-bold">${label}</div>
                        <div class="col-sm-8">${value || '-'}</div>
                    </div>
                `).join('');
                bootstrap.Modal.getOrCreateInstance(detailModal).show();
            });
        });
    })();
</script>
@stack('scripts')
</body>
</html>
