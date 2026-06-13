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

        .chatbot-launcher {
            position: fixed;
            right: 1rem;
            bottom: 1rem;
            z-index: 1040;
            width: 58px;
            height: 58px;
            border-radius: 50%;
            border: 0;
            background: var(--green);
            color: #fff;
            box-shadow: 0 18px 42px rgba(14, 59, 50, .32);
            font-weight: 900;
        }

        .chatbot-panel {
            position: fixed;
            right: 1rem;
            bottom: 5.4rem;
            z-index: 1040;
            display: none;
            width: min(380px, calc(100vw - 2rem));
            max-height: min(680px, calc(100vh - 7rem));
            overflow: hidden;
            background: #fffaf0;
            border: 1px solid var(--line);
            border-radius: 8px;
            box-shadow: 0 24px 70px rgba(44, 27, 18, .22);
        }

        .chatbot-panel.is-open {
            display: grid;
            grid-template-rows: auto minmax(180px, 1fr) auto;
        }

        .chatbot-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            padding: .9rem 1rem;
            background: var(--green);
            color: #fff;
        }

        .chatbot-head strong {
            display: block;
            line-height: 1.1;
        }

        .chatbot-head span {
            color: rgba(255, 255, 255, .76);
            font-size: .82rem;
        }

        .chatbot-close {
            width: 34px;
            height: 34px;
            border: 1px solid rgba(255, 255, 255, .32);
            border-radius: 50%;
            background: transparent;
            color: #fff;
            font-size: 1.2rem;
            line-height: 1;
        }

        .chatbot-messages {
            display: flex;
            flex-direction: column;
            gap: .65rem;
            overflow-y: auto;
            padding: 1rem;
        }

        .chatbot-message {
            width: fit-content;
            max-width: 88%;
            border-radius: 8px;
            padding: .65rem .75rem;
            white-space: pre-line;
            line-height: 1.45;
        }

        .chatbot-message.bot {
            align-self: flex-start;
            background: #fff;
            border: 1px solid var(--line);
        }

        .chatbot-message.user {
            align-self: flex-end;
            background: var(--green);
            color: #fff;
        }

        .chatbot-body {
            border-top: 1px solid var(--line);
            padding: .85rem;
            background: rgba(255, 255, 255, .72);
        }

        .chatbot-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .45rem;
            margin-bottom: .75rem;
        }

        .chatbot-booking {
            display: none;
            grid-template-columns: 1fr 1fr;
            gap: .5rem;
            margin-bottom: .75rem;
        }

        .chatbot-booking.is-open {
            display: grid;
        }

        .chatbot-booking .wide {
            grid-column: 1 / -1;
        }

        .chatbot-compose {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: .5rem;
        }

        @media (max-width: 991.98px) {
            .hero-full {
                min-height: 78vh;
                margin-top: -1rem;
            }
        }

        @media (max-width: 575.98px) {
            .chatbot-launcher {
                right: .75rem;
                bottom: .75rem;
            }

            .chatbot-panel {
                right: .75rem;
                bottom: 4.9rem;
                width: calc(100vw - 1.5rem);
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
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}" href="{{ route('staff.dashboard') }}">Nhân viên</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('staff.kitchen') ? 'active' : '' }}" href="{{ route('staff.kitchen') }}">Bếp</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('staff.cashier') ? 'active' : '' }}" href="{{ route('staff.cashier') }}">Thu ngân</a></li>
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

<section class="chatbot-panel" id="chatbotPanel" aria-live="polite">
    <div class="chatbot-head">
        <div>
            <strong>Chatbot hỗ trợ</strong>
            <span>Giờ mở cửa, thực đơn, đặt bàn</span>
        </div>
        <button class="chatbot-close" type="button" id="chatbotClose" aria-label="Đóng chatbot">&times;</button>
    </div>

    <div class="chatbot-messages" id="chatbotMessages"></div>

    <div class="chatbot-body">
        <div class="chatbot-actions">
            <button class="btn btn-outline-primary btn-sm" type="button" data-chatbot-message="Giờ mở cửa">Giờ mở cửa</button>
            <button class="btn btn-outline-primary btn-sm" type="button" data-chatbot-message="Gửi thực đơn">Thực đơn</button>
            <button class="btn btn-outline-primary btn-sm" type="button" data-chatbot-message="Món cay">Món cay</button>
            <button class="btn btn-outline-primary btn-sm" type="button" data-chatbot-message="Đồ uống">Đồ uống</button>
            <button class="btn btn-outline-primary btn-sm" type="button" data-chatbot-message="Món hải sản">Hải sản</button>
            <button class="btn btn-outline-primary btn-sm" type="button" data-chatbot-message="Món bán chạy">Bán chạy</button>
            <button class="btn btn-primary btn-sm" type="button" id="chatbotBookingToggle">Đặt bàn</button>
        </div>

        <form class="chatbot-booking" id="chatbotBookingForm">
            <input class="form-control form-control-sm wide" name="customer_name" placeholder="Tên khách" autocomplete="name" required>
            <input class="form-control form-control-sm wide" name="phone" placeholder="Số điện thoại" autocomplete="tel" required>
            <input class="form-control form-control-sm" name="number_of_guests" type="number" min="1" max="30" placeholder="Số khách" required>
            <input class="form-control form-control-sm" name="reservation_time" type="datetime-local" required>
            <button class="btn btn-primary btn-sm wide" type="submit">Gửi đặt bàn</button>
        </form>

        <form class="chatbot-compose" id="chatbotForm">
            <input class="form-control form-control-sm" id="chatbotInput" name="message" placeholder="Nhập tin nhắn..." autocomplete="off">
            <button class="btn btn-primary btn-sm" type="submit">Gửi</button>
        </form>
    </div>
</section>

<button class="chatbot-launcher" type="button" id="chatbotLauncher" aria-label="Mở chatbot">Chat</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (() => {
        const panel = document.getElementById('chatbotPanel');
        const launcher = document.getElementById('chatbotLauncher');
        const close = document.getElementById('chatbotClose');
        const messages = document.getElementById('chatbotMessages');
        const form = document.getElementById('chatbotForm');
        const input = document.getElementById('chatbotInput');
        const bookingToggle = document.getElementById('chatbotBookingToggle');
        const bookingForm = document.getElementById('chatbotBookingForm');
        const endpoint = '{{ url('/api/chatbot/message') }}';
        const sessionKey = 'restaurant_world_chatbot_session';
        const newSessionId = () => window.crypto?.randomUUID?.() || `${Date.now()}-${Math.random().toString(16).slice(2)}`;
        const sessionId = localStorage.getItem(sessionKey) || newSessionId();

        localStorage.setItem(sessionKey, sessionId);

        const addMessage = (text, sender = 'bot') => {
            const bubble = document.createElement('div');
            bubble.className = `chatbot-message ${sender}`;
            bubble.textContent = text;
            messages.appendChild(bubble);
            messages.scrollTop = messages.scrollHeight;
        };

        const sendPayload = async (payload, visibleText = payload.message) => {
            if (visibleText) {
                addMessage(visibleText, 'user');
            }

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ session_id: sessionId, ...payload }),
                });

                const data = await response.json();
                addMessage(data.reply || 'Chatbot chưa nhận được phản hồi phù hợp.');
            } catch (error) {
                addMessage('Kết nối chatbot đang gián đoạn. Bạn vui lòng thử lại sau.');
            }
        };

        const openPanel = () => {
            panel.classList.add('is-open');

            if (! messages.childElementCount) {
                addMessage('Xin chào, mình có thể hỗ trợ giờ mở cửa, thực đơn và đặt bàn tự động.');
            }
        };

        launcher.addEventListener('click', openPanel);
        close.addEventListener('click', () => panel.classList.remove('is-open'));

        document.querySelectorAll('[data-chatbot-message]').forEach((button) => {
            button.addEventListener('click', () => sendPayload({ message: button.dataset.chatbotMessage }));
        });

        bookingToggle.addEventListener('click', () => {
            bookingForm.classList.toggle('is-open');
        });

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            const message = input.value.trim();

            if (! message) {
                return;
            }

            input.value = '';
            sendPayload({ message });
        });

        bookingForm.addEventListener('submit', (event) => {
            event.preventDefault();
            const fields = Object.fromEntries(new FormData(bookingForm).entries());

            sendPayload({
                intent: 'dat_ban',
                message: 'Đặt bàn qua chatbot',
                parameters: fields,
            }, `Đặt bàn ${fields.number_of_guests} khách lúc ${fields.reservation_time}`);

            bookingForm.reset();
            bookingForm.classList.remove('is-open');
        });
    })();
</script>
</body>
</html>
