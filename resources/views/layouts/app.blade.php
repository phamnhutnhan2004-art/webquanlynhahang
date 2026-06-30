<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Nhà hàng Hoa Sen')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;600;700;800;900&family=Inter:wght@400;600;700;800;900&family=Montserrat:wght@400;600;700;800;900&family=Open+Sans:wght@400;600;700;800&family=Poppins:wght@400;600;700;800;900&family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
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
            --text-strong: #111111;
            --text-body: #221812;
            --text-muted-strong: #4a4036;
            --text-on-dark: #ffffff;
            --text-on-dark-soft: rgba(255, 255, 255, .94);
            --text-on-dark-muted: rgba(255, 255, 255, .88);
            --line: rgba(90, 52, 30, .16);
        }

        html { scroll-behavior: smooth; }

        body {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--ivory), #f3eadb 50%, #fffaf0);
            color: var(--ink);
            font-family: "Segoe UI", system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
        }

        p,
        li {
            line-height: 1.68;
        }

        .text-muted {
            color: var(--text-muted-strong) !important;
        }

        .text-white-50 {
            color: var(--text-on-dark-muted) !important;
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

        .navbar .nav-link.active {
            color: #edf50b !important;
        }

        .navbar .dropdown-menu {
            border: 1px solid rgba(217, 164, 65, .28);
            border-radius: 8px;
            background: rgba(255, 250, 240, .98);
            box-shadow: 0 18px 45px rgba(44, 27, 18, .16);
        }

        .navbar .dropdown-item {
            color: var(--wood-dark);
            font-weight: 800;
        }

        .navbar .dropdown-item:hover,
        .navbar .dropdown-item:focus {
            background: var(--green-soft);
            color: var(--green);
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
            color: var(--text-body);
            box-shadow: 0 18px 45px rgba(44, 27, 18, .08);
            overflow: hidden;
        }

        .card p,
        .card li,
        .card .small {
            color: var(--text-muted-strong);
        }

        .card h1,
        .card h2,
        .card h3,
        .card h4,
        .card h5,
        .card h6,
        .card strong {
            color: var(--text-strong);
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
            color: var(--text-on-dark-soft);
            font-weight: 600;
            line-height: 1.7;
        }

        .hero-eyebrow {
            margin-bottom: 1rem;
            color: rgba(245, 245, 141, 0.87);
            font-size: .72rem;
            line-height: 1.2;
            letter-spacing: .07em;
        }

        .hero-title {
            max-width: 780px;
            margin: 0 0 1.35rem;
            color: #fff;
            font-size: 5.8rem;
            font-weight: 950;
            line-height: .95;
            letter-spacing: 0;
            text-shadow: 0 18px 42px rgba(0, 0, 0, .34);
        }

        .about-hero > .container > .eyebrow,
        .contact-hero > .container > .eyebrow,
        .party-hero > .container > .eyebrow,
        .menu-category-hero > .container > .eyebrow,
        .product-detail-hero > .container > .eyebrow {
            margin-bottom: 1rem !important;
            color: rgba(234, 241, 155, 0.9);
            font-size: .72rem;
            line-height: 1.2;
            letter-spacing: .07em;
            font-weight: 900;
        }

        .about-hero > .container > h1,
        .contact-hero > .container > h1,
        .party-hero > .container > h1,
        .menu-category-hero > .container > h1,
        .product-detail-hero > .container > h1 {
            max-width: 980px;
            margin-bottom: 1.35rem !important;
            color: var(--gold-soft);
            font-size: 5.8rem;
            font-weight: 950 !important;
            line-height: .95;
            letter-spacing: 0;
            text-shadow: 0 18px 42px rgba(0, 0, 0, .34);
        }

        .hero-description {
            max-width: 640px;
            margin-bottom: 2rem;
            font-size: 1.18rem;
            line-height: 1.65;
        }

        .hero-actions {
            align-items: center;
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

        .hero-full,
        .page-hero,
        .about-hero,
        .contact-hero,
        .party-hero,
        .menu-page-hero,
        .gallery-page-hero,
        .home-intro-band,
        .service-preview-band,
        .review-band,
        .about-contact,
        .contact-info-side,
        .contact-footer,
        .contact-band,
        .store-map-section,
        .party-form-head,
        .party-summary,
        .gallery-feature-band,
        .gallery-video-band {
            color: var(--text-on-dark);
        }

        .hero-full :where(p, li, .lead, .small, .text-muted, .text-white-50),
        .page-hero :where(p, li, .lead, .small, .text-muted, .text-white-50),
        .about-hero :where(p, li, .lead, .small, .text-muted, .text-white-50),
        .contact-hero :where(p, li, .lead, .small, .text-muted, .text-white-50),
        .party-hero :where(p, li, .lead, .small, .text-muted, .text-white-50),
        .menu-page-hero :where(p, li, .lead, .small, .text-muted, .text-white-50),
        .gallery-page-hero :where(p, li, .lead, .small, .text-muted, .text-white-50),
        .home-intro-band :where(p, li, .lead, .small, .text-muted, .text-white-50),
        .service-preview-band :where(p, li, .lead, .small, .text-muted, .text-white-50),
        .review-band :where(p, li, .lead, .small, .text-muted, .text-white-50),
        .about-contact :where(p, li, .lead, .small, .text-muted, .text-white-50, .about-copy),
        .contact-info-side :where(p, li, .lead, .small, .text-muted, .text-white-50),
        .contact-footer :where(p, li, .lead, .small, .text-muted, .text-white-50),
        .contact-band :where(p, li, .lead, .small, .text-muted, .text-white-50),
        .store-map-section :where(p, li, .lead, .small, .text-muted, .text-white-50),
        .party-form-head :where(p, li, .lead, .small, .text-muted, .text-white-50),
        .party-summary :where(p, li, .lead, .small, .text-muted, .text-white-50),
        .gallery-feature-band :where(p, li, .lead, .small, .text-muted, .text-white-50),
        .gallery-video-band :where(p, li, .lead, .small, .text-muted, .text-white-50) {
            color: var(--text-on-dark-soft) !important;
            font-weight: 650;
            line-height: 1.7;
        }

        .hero-full :where(h1, h2, h3, h4, h5, h6, strong),
        .page-hero :where(h1, h2, h3, h4, h5, h6, strong),
        .about-hero :where(h1, h2, h3, h4, h5, h6, strong),
        .contact-hero :where(h1, h2, h3, h4, h5, h6, strong),
        .party-hero :where(h1, h2, h3, h4, h5, h6, strong),
        .menu-page-hero :where(h1, h2, h3, h4, h5, h6, strong),
        .gallery-page-hero :where(h1, h2, h3, h4, h5, h6, strong),
        .home-intro-band :where(h1, h2, h3, h4, h5, h6, strong),
        .service-preview-band :where(h1, h2, h3, h4, h5, h6, strong),
        .review-band :where(h1, h2, h3, h4, h5, h6, strong),
        .about-contact :where(h1, h2, h3, h4, h5, h6, strong),
        .contact-info-side :where(h1, h2, h3, h4, h5, h6, strong),
        .contact-footer :where(h1, h2, h3, h4, h5, h6, strong),
        .contact-band :where(h1, h2, h3, h4, h5, h6, strong),
        .store-map-section :where(h1, h2, h3, h4, h5, h6, strong),
        .party-form-head :where(h1, h2, h3, h4, h5, h6, strong),
        .party-summary :where(h1, h2, h3, h4, h5, h6, strong),
        .gallery-feature-band :where(h1, h2, h3, h4, h5, h6, strong),
        .gallery-video-band :where(h1, h2, h3, h4, h5, h6, strong) {
            color: var(--text-on-dark);
        }

        .page-hero h1,
        .about-hero h1,
        .contact-hero h1,
        .party-hero h1,
        .auth-visual h1 {
            font-size: 3.8rem;
            font-weight: 950;
            line-height: 1.02;
            letter-spacing: 0;
        }

        .eyebrow {
            color: #f2f794df;
            font-size: .88rem;
            font-weight: 900;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .hero-full .eyebrow,
        .page-hero .eyebrow,
        .about-hero .eyebrow,
        .contact-hero .eyebrow,
        .party-hero .eyebrow,
        .auth-visual .eyebrow,
        .contact-info-side .eyebrow,
        .party-form-head .eyebrow,
        .party-summary .eyebrow,
        .about-contact .eyebrow {
            color: var(--gold-soft);
        }

        .hero-full .hero-eyebrow {
            color: var(--gold-soft);
            max-width: 780px;
            font-size: 3.0rem;
            font-weight: 950;
            line-height: .95;
            letter-spacing: 0;
            text-shadow: 0 18px 42px rgba(0, 0, 0, .34);
        }

        .hero-full .hero-title {
            color: var(--gold-soft);
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
            font-size: 2.15rem;
            font-weight: 900;
            line-height: 1.15;
            letter-spacing: 0;
        }

        .home-section-title {
            color: var(--wood-dark);
            font-size: 2.65rem;
            font-weight: 950;
            line-height: 1.12;
            letter-spacing: 0;
        }

        .contact-band .home-section-title {
            color: #ffff07;
        }

        .store-map-section .section-title .home-section-title {
            color: #fff;
        }

        .store-contact-title.home-section-title {
            margin-bottom: 1.35rem;
            color: var(--gold-soft);
            text-shadow: 0 3px 12px rgba(0, 0, 0, .28);
        }

        .card-body > h1.h3,
        .card-body > h2.h5,
        .contact-info-side h2,
        .contact-form-area .section-title h2,
        .party-section h2,
        .party-form-head h2,
        .about-section h2,
        .about-contact h2 {
            font-size: 2.15rem;
            font-weight: 950;
            line-height: 1.16;
            letter-spacing: 0;
        }

        .contact-info-side h2,
        .party-form-head h2,
        .about-contact h2 {
            color: #fff;
        }

        .card-body > h1.h3,
        .card-body > h2.h5 {
            color: var(--wood-dark);
        }

        .contact-footer h3 {
            font-size: 1.35rem;
            line-height: 1.25;
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

        .hero-stats {
            max-width: 430px;
            margin-left: auto;
        }

        .hero-stat-card {
            width: min(132px, 100%);
            min-height: 94px;
            border-color: rgba(246, 223, 157, .58);
            color: #fff;
            box-shadow: 0 20px 48px rgba(0, 0, 0, .24);
        }

        .hero-stat-card .card-body {
            min-height: 94px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: .35rem;
            padding: .85rem .95rem;
        }

        .hero-stat-card .stat-value {
            color: currentColor;
            font-size: 2.05rem;
            line-height: .95;
        }

        .hero-stat-label {
            display: flex;
            align-items: center;
            gap: .35rem;
            color: rgba(255, 255, 255, .9);
            font-size: .78rem;
            font-weight: 900;
            line-height: 1.15;
            white-space: nowrap;
        }

        .hero-stat-label i {
            flex: 0 0 auto;
            font-size: .92rem;
        }

        .hero-stat-card-gold {
            background: linear-gradient(135deg, #f0bd55 0%, #f6df9d 100%);
            color: var(--wood-dark);
        }

        .hero-stat-card-gold .hero-stat-label {
            color: rgba(44, 27, 18, .78);
        }

        .hero-stat-card-green {
            background: linear-gradient(135deg, #0e3b32 0%, #17705c 100%);
        }

        .hero-stat-card-coral {
            background: linear-gradient(135deg, #b54335 0%, #e15e45 100%);
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

        .menu-category-chip {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            text-decoration: none;
            transition: background .2s ease, color .2s ease, transform .2s ease, box-shadow .2s ease;
        }

        .menu-category-chip:hover,
        .menu-category-chip:focus {
            background: var(--green);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 14px 32px rgba(14, 59, 50, .18);
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

        .store-map-section {
            margin-top: 1.5rem;
            background:
                linear-gradient(rgba(14, 59, 50, .92), rgba(14, 59, 50, .92)),
                url("{{ asset('images/restaurant-interior.png') }}") center / cover;
            border-radius: 8px;
            padding: clamp(1rem, 2.5vw, 1.6rem);
            color: #fff;
        }

        .store-contact-info {
            max-width: 720px;
            padding: clamp(.5rem, 2vw, 1rem) 0 clamp(1.25rem, 3vw, 2rem);
        }

        .store-contact-title {
            color: #fff01a;
            font-family: "Brush Script MT", "Segoe Script", cursive;
            font-size: clamp(2.4rem, 8vw, 4rem);
            font-weight: 900;
            line-height: 1;
            margin-bottom: clamp(1.2rem, 3vw, 2rem);
            text-shadow: 0 3px 0 rgba(0, 0, 0, .12);
        }

        .store-contact-list {
            display: grid;
            gap: .85rem;
            margin: 0;
            font-size: clamp(1rem, 2.3vw, 1.12rem);
            font-weight: 800;
            line-height: 1.7;
        }

        .store-contact-item {
            display: flex;
            align-items: flex-start;
            gap: .55rem;
        }

        .store-contact-item i {
            flex: 0 0 auto;
            color: #fff01a;
            font-size: 1.08em;
            line-height: 1.65;
        }

        .store-contact-item a {
            color: #fff;
            text-decoration: none;
        }

        .store-contact-item a:hover {
            color: var(--gold-soft);
        }

        .store-socials {
            display: flex;
            flex-wrap: wrap;
            gap: .45rem;
            margin-top: clamp(1.5rem, 4vw, 2.4rem);
        }

        .store-social-icon {
            display: inline-grid;
            place-items: center;
            width: clamp(2.1rem, 7vw, 2.5rem);
            aspect-ratio: 1;
            border-radius: 50%;
            background: #ffc21a;
            color: var(--green);
            font-size: clamp(1.25rem, 4vw, 1.5rem);
            line-height: 1;
        }

        .store-map-shell {
            position: relative;
            padding: clamp(.5rem, 1.4vw, .85rem);
            border: 2px solid rgba(217, 164, 65, .86);
        }

        .store-map-shell::before {
            content: "";
            position: absolute;
            inset: .35rem;
            border: 1px solid rgba(217, 164, 65, .76);
            z-index: 2;
            pointer-events: none;
        }

        .store-map-frame {
            position: relative;
            z-index: 1;
            min-height: clamp(280px, 44vw, 520px);
            background: #e9ecef;
        }

        .store-map-frame iframe {
            width: 100%;
            height: 100%;
            min-height: inherit;
            border: 0;
            display: block;
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
            line-height: 1.45;
            overflow-wrap: anywhere;
        }

        .chatbot-message p:last-child {
            margin-bottom: 0;
        }

        .chatbot-message ul {
            margin: .35rem 0 0;
            padding-left: 1.1rem;
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

        .chatbot-thinking {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            color: #7b6752;
            font-size: .86rem;
            font-weight: 700;
        }

        .chatbot-thinking i {
            width: .42rem;
            aspect-ratio: 1;
            border-radius: 50%;
            background: currentColor;
            animation: chatbotPulse 1s infinite ease-in-out;
        }

        .chatbot-thinking i:nth-child(2) {
            animation-delay: .15s;
        }

        .chatbot-thinking i:nth-child(3) {
            animation-delay: .3s;
        }

        @keyframes chatbotPulse {
            0%, 80%, 100% { opacity: .35; transform: translateY(0); }
            40% { opacity: 1; transform: translateY(-3px); }
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

            .hero-title {
                max-width: 640px;
                font-size: 4.4rem;
            }

            .hero-full .hero-eyebrow {
                max-width: 640px;
                font-size: 4.4rem;
            }

            .about-hero > .container > h1,
            .contact-hero > .container > h1,
            .party-hero > .container > h1,
            .menu-category-hero > .container > h1,
            .product-detail-hero > .container > h1 {
                max-width: 760px;
                font-size: 4.4rem;
            }

            .hero-description {
                max-width: 560px;
                font-size: 1.08rem;
            }

            .page-hero h1,
            .about-hero h1,
            .contact-hero h1,
            .party-hero h1,
            .auth-visual h1 {
                font-size: 3rem;
            }

            .section-title h2,
            .card-body > h1.h3,
            .card-body > h2.h5,
            .contact-info-side h2,
            .contact-form-area .section-title h2,
            .party-section h2,
            .party-form-head h2,
            .about-section h2,
            .about-contact h2 {
                font-size: 1.85rem;
            }

            .home-section-title {
                font-size: 2.25rem;
            }

            .hero-stat-card .stat-value {
                font-size: 1.8rem;
            }

        }

        @media (max-width: 575.98px) {
            .hero-full {
                padding: 2rem 1rem 2.5rem;
            }

            .hero-eyebrow {
                margin-bottom: .85rem;
                font-size: .68rem;
            }

            .hero-title {
                margin-bottom: 1rem;
                font-size: 3.15rem;
                line-height: 1;
            }

            .hero-full .hero-eyebrow {
                margin-bottom: 1rem;
                font-size: 3.15rem;
                line-height: 1;
            }

            .about-hero > .container > .eyebrow,
            .contact-hero > .container > .eyebrow,
            .party-hero > .container > .eyebrow,
            .menu-category-hero > .container > .eyebrow,
            .product-detail-hero > .container > .eyebrow {
                margin-bottom: .85rem !important;
                font-size: .68rem;
            }

            .about-hero > .container > h1,
            .contact-hero > .container > h1,
            .party-hero > .container > h1,
            .menu-category-hero > .container > h1,
            .product-detail-hero > .container > h1 {
                margin-bottom: 1rem !important;
                font-size: 3.15rem;
                line-height: 1;
            }

            .hero-description {
                margin-bottom: 1.45rem;
                font-size: 1rem;
                line-height: 1.55;
            }

            .section-title {
                align-items: flex-start;
                flex-direction: column;
            }

            .page-hero h1,
            .about-hero h1,
            .contact-hero h1,
            .party-hero h1,
            .auth-visual h1 {
                font-size: 2.35rem;
                line-height: 1.08;
            }

            .eyebrow {
                font-size: .78rem;
            }

            .section-title h2,
            .card-body > h1.h3,
            .card-body > h2.h5,
            .contact-info-side h2,
            .contact-form-area .section-title h2,
            .party-section h2,
            .party-form-head h2,
            .about-section h2,
            .about-contact h2 {
                font-size: 1.55rem;
                line-height: 1.18;
            }

            .home-section-title {
                font-size: 1.85rem;
                line-height: 1.16;
            }

            .hero-stat-card .stat-value {
                font-size: 1.55rem;
            }

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
    <link href="{{ asset('css/hoa-sen-ui.css') }}?v={{ is_file(public_path('css/hoa-sen-ui.css')) ? filemtime(public_path('css/hoa-sen-ui.css')) : '1' }}" rel="stylesheet">
    @php
        $themeSetting = null;
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('theme_settings')) {
                $themeSetting = \App\Models\ThemeSetting::current();

                if (\Illuminate\Support\Facades\Schema::hasTable('website_page_settings')) {
                    $pageKey = \App\Models\WebsitePageSetting::keyForRequest(request());
                    $pageTheme = \App\Models\WebsitePageSetting::current($pageKey)->getSetting('theme', []);

                    if (is_array($pageTheme) && $pageTheme !== []) {
                        $themeSetting = (new \App\Models\ThemeSetting())->forceFill([
                            'settings' => array_replace_recursive($themeSetting->resolvedSettings(), $pageTheme),
                        ]);
                    }
                }
            }
        } catch (\Throwable) {
            $themeSetting = null;
        }
    @endphp
    @if($themeSetting)
        <style data-admin-theme-settings>
            :root {
                --theme-primary-title: {{ $themeSetting->cssColor('primary_title') }};
                --theme-secondary-title: {{ $themeSetting->cssColor('secondary_title') }};
                --theme-menu-background: {{ $themeSetting->cssColor('menu_background') }};
                --theme-background: {{ $themeSetting->cssColor('background') }};
                --theme-button-background: {{ $themeSetting->cssColor('button_background') }};
                --theme-button-text: {{ $themeSetting->cssColor('button_text') }};
                --theme-text: {{ $themeSetting->cssColor('text') }};
                --theme-footer-background: {{ $themeSetting->cssColor('footer_background') }};
                --theme-button-hover-background: {{ $themeSetting->cssColor('button_hover_background') }};
                --theme-menu-hover: {{ $themeSetting->cssColor('menu_hover') }};
                --gold: var(--theme-button-background);
                --gold-soft: var(--theme-primary-title);
                --ink: var(--theme-text);
                --muted: var(--theme-text);
                --green: var(--theme-menu-background);
                --theme-font-heading: {!! $themeSetting->cssFontFamily('heading') !!};
                --theme-font-body: {!! $themeSetting->cssFontFamily('body') !!};
                --theme-font-menu: {!! $themeSetting->cssFontFamily('menu') !!};
                --theme-hero-title-size: {{ $themeSetting->cssInt('font_sizes.hero_title', 32, 112) }}px;
                --theme-section-title-size: {{ $themeSetting->cssInt('font_sizes.section_title', 20, 72) }}px;
                --theme-body-size: {{ $themeSetting->cssInt('font_sizes.body', 12, 24) }}px;
                --theme-menu-size: {{ $themeSetting->cssInt('font_sizes.menu', 10, 22) }}px;
                --theme-button-size: {{ $themeSetting->cssInt('font_sizes.button', 12, 24) }}px;
                --theme-banner-height: {{ $themeSetting->cssInt('banner.height', 320, 900) }}px;
                --theme-banner-overlay: {{ $themeSetting->cssInt('banner.overlay_opacity', 0, 95) / 100 }};
                --theme-banner-padding-y: {{ $themeSetting->cssInt('banner.padding_y', 24, 160) }}px;
                --theme-banner-align-items: {{ $themeSetting->bannerAlignItems() }};
                --theme-content-align: {{ $themeSetting->contentTextAlign() }};
                --theme-content-justify: {{ $themeSetting->contentJustify() }};
                --theme-button-radius: {{ $themeSetting->cssInt('button.radius', 0, 40) }}px;
                --theme-button-shadow: {{ $themeSetting->buttonShadow() }};
                --theme-button-hover-transform: {{ $themeSetting->buttonHoverTransform() }};
                --theme-page-margin: {{ $themeSetting->cssInt('spacing.page_margin', 0, 80) }}px;
                --theme-section-padding: {{ $themeSetting->cssInt('spacing.section_padding', 20, 140) }}px;
                --theme-section-gap: {{ $themeSetting->cssInt('spacing.section_gap', 8, 80) }}px;
                --theme-title-content-gap: {{ $themeSetting->cssInt('spacing.title_content_gap', 4, 56) }}px;
            }

            body {
                background: var(--theme-background);
                color: var(--theme-text);
                font-family: var(--theme-font-body);
                font-size: var(--theme-body-size);
                margin: var(--theme-page-margin);
            }

            body .text-muted {
                color: var(--text-muted-strong) !important;
            }

            body .text-white-50 {
                color: var(--text-on-dark-muted) !important;
            }

            .navbar {
                background: color-mix(in srgb, var(--theme-menu-background) 94%, transparent);
            }

            .navbar-brand,
            .nav-link {
                font-family: var(--theme-font-menu);
                font-size: var(--theme-menu-size);
            }

            .nav-link.active,
            .nav-link:hover,
            .navbar .dropdown-item:hover,
            .navbar .dropdown-item:focus {
                color: var(--theme-menu-hover) !important;
            }

            .hero-full,
            .about-hero,
            .contact-hero,
            .party-hero,
            .menu-category-hero,
            .product-detail-hero {
                min-height: min(var(--theme-banner-height), calc(100vh - 72px));
                align-items: var(--theme-banner-align-items);
                padding-block: var(--theme-banner-padding-y);
                text-align: var(--theme-content-align);
                box-shadow: inset 0 0 0 9999px rgba(0, 0, 0, var(--theme-banner-overlay));
            }

            .hero-full .row {
                justify-content: var(--theme-content-justify);
            }

            .hero-actions,
            .party-hero-actions {
                justify-content: var(--theme-content-justify);
            }

            .hero-title,
            .hero-full .hero-eyebrow,
            .about-hero > .container > h1,
            .contact-hero > .container > h1,
            .party-hero > .container > h1,
            .menu-category-hero > .container > h1,
            .product-detail-hero > .container > h1 {
                color: var(--theme-primary-title) !important;
                font-family: var(--theme-font-heading);
                font-size: var(--theme-hero-title-size);
            }

            .eyebrow,
            .about-hero > .container > .eyebrow,
            .contact-hero > .container > .eyebrow,
            .party-hero > .container > .eyebrow,
            .menu-category-hero > .container > .eyebrow,
            .product-detail-hero > .container > .eyebrow {
                color: var(--theme-secondary-title) !important;
                font-family: var(--theme-font-heading);
            }

            .home-section-title,
            .section-title h2,
            .about-section h2,
            .contact-info-side h2,
            .contact-form-area .section-title h2,
            .party-section h2,
            .party-form-head h2 {
                color: var(--theme-primary-title);
                font-family: var(--theme-font-heading);
                font-size: var(--theme-section-title-size);
            }

            .section-pad,
            .about-section,
            .party-section {
                padding-block: var(--theme-section-padding);
            }

            .section-title {
                margin-bottom: var(--theme-title-content-gap);
            }

            .row.g-4,
            .row.g-3 {
                --bs-gutter-y: var(--theme-section-gap);
            }

            .btn {
                border-radius: var(--theme-button-radius);
                font-size: var(--theme-button-size);
                box-shadow: var(--theme-button-shadow);
            }

            .btn-primary {
                background: var(--theme-button-background);
                border-color: var(--theme-button-background);
                color: var(--theme-button-text);
            }

            .btn-primary:hover {
                background: var(--theme-button-hover-background);
                border-color: var(--theme-button-hover-background);
                color: var(--theme-button-text);
                transform: var(--theme-button-hover-transform);
            }

            .contact-footer,
            .contact-band,
            .about-contact,
            .store-map-section {
                background-color: var(--theme-footer-background);
            }

            .hero-full :where(p, li, .lead, .small, .text-muted, .text-white-50),
            .page-hero :where(p, li, .lead, .small, .text-muted, .text-white-50),
            .about-hero :where(p, li, .lead, .small, .text-muted, .text-white-50),
            .contact-hero :where(p, li, .lead, .small, .text-muted, .text-white-50),
            .party-hero :where(p, li, .lead, .small, .text-muted, .text-white-50),
            .menu-page-hero :where(p, li, .lead, .small, .text-muted, .text-white-50),
            .gallery-page-hero :where(p, li, .lead, .small, .text-muted, .text-white-50),
            .home-intro-band :where(p, li, .lead, .small, .text-muted, .text-white-50),
            .service-preview-band :where(p, li, .lead, .small, .text-muted, .text-white-50),
            .review-band :where(p, li, .lead, .small, .text-muted, .text-white-50),
            .about-contact :where(p, li, .lead, .small, .text-muted, .text-white-50, .about-copy),
            .contact-info-side :where(p, li, .lead, .small, .text-muted, .text-white-50),
            .contact-footer :where(p, li, .lead, .small, .text-muted, .text-white-50),
            .contact-band :where(p, li, .lead, .small, .text-muted, .text-white-50),
            .store-map-section :where(p, li, .lead, .small, .text-muted, .text-white-50),
            .party-form-head :where(p, li, .lead, .small, .text-muted, .text-white-50),
            .party-summary :where(p, li, .lead, .small, .text-muted, .text-white-50) {
                color: var(--text-on-dark-soft) !important;
                font-weight: 650;
            }
        </style>
    @endif
</head>
<body>
<div class="page-loader" data-page-loader aria-label="Đang tải trang" role="status">
    <div class="loader-card">
        <div class="loader-logo"><i class="bi bi-flower1" aria-hidden="true"></i></div>
        <div class="loader-title">Nhà hàng Hoa Sen</div>
        <div class="loader-ring" aria-hidden="true"></div>
    </div>
</div>
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">Nhà hàng Hoa Sen</a>
        <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="Mở menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Trang chủ</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">Giới thiệu</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('menu', 'menu.category', 'products.show') ? 'active' : '' }}" href="{{ route('menu') }}">Thực đơn</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('home-parties.show') ? 'active' : '' }}" href="{{ route('home-parties.show') }}">Đặt tiệc tại nhà</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('gallery') ? 'active' : '' }}" href="{{ route('gallery') }}">Hình ảnh</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Liên hệ</a></li>
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
                    <a class="btn btn-primary btn-sm" href="{{ route('reservations.create') }}">Đặt bàn</a>
                @else
                    <span class="small d-none d-md-inline">{{ auth()->user()->name }}</span>
                    <a class="btn btn-primary btn-sm" href="{{ route('reservations.create') }}">Đặt bàn</a>
                    <a class="btn btn-outline-light btn-sm" href="{{ route('account.show') }}">Tài khoản của tôi</a>
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

        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm">{{ session('error') }}</div>
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
<script src="{{ asset('js/hoa-sen-ui.js') }}?v={{ is_file(public_path('js/hoa-sen-ui.js')) ? filemtime(public_path('js/hoa-sen-ui.js')) : '1' }}"></script>
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
        const sessionKey = 'restaurant_hoa_sen_chatbot_session';
        const newSessionId = () => window.crypto?.randomUUID?.() || `${Date.now()}-${Math.random().toString(16).slice(2)}`;
        const sessionId = localStorage.getItem(sessionKey) || newSessionId();

        localStorage.setItem(sessionKey, sessionId);

        const escapeHtml = (value) => (value || '').toString().replace(/[&<>"']/g, (char) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;',
        })[char]);

        const formatMessage = (text) => {
            const escaped = escapeHtml(text)
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/^- (.*)$/gm, '<li>$1</li>')
                .replace(/(<li>.*<\/li>)/gs, '<ul>$1</ul>')
                .replace(/\n/g, '<br>');

            return escaped;
        };

        const addMessage = (text, sender = 'bot') => {
            const bubble = document.createElement('div');
            bubble.className = `chatbot-message ${sender}`;
            bubble.innerHTML = formatMessage(text);
            messages.appendChild(bubble);
            messages.scrollTop = messages.scrollHeight;

            return bubble;
        };

        const addThinking = () => {
            const bubble = document.createElement('div');
            bubble.className = 'chatbot-message bot';
            bubble.innerHTML = '<span class="chatbot-thinking">AI đang suy nghĩ <i></i><i></i><i></i></span>';
            messages.appendChild(bubble);
            messages.scrollTop = messages.scrollHeight;

            return bubble;
        };

        const sendPayload = async (payload, visibleText = payload.message) => {
            if (visibleText) {
                addMessage(visibleText, 'user');
            }

            const thinking = addThinking();
            form.querySelector('button[type="submit"]').disabled = true;

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
                thinking.remove();
                addMessage(data.reply || 'Chatbot chưa nhận được phản hồi phù hợp.');
            } catch (error) {
                thinking.remove();
                addMessage('Kết nối chatbot đang gián đoạn. Bạn vui lòng thử lại sau.');
            } finally {
                form.querySelector('button[type="submit"]').disabled = false;
                input.focus();
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
