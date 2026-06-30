<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class WebsitePageSetting extends Model
{
    protected $fillable = ['page_key', 'title', 'settings'];

    protected $casts = ['settings' => 'array'];

    public const PAGE_OPTIONS = [
        'home' => 'Trang chủ',
        'about' => 'Giới thiệu',
        'menu' => 'Thực đơn',
        'booking' => 'Đặt bàn',
        'home-party' => 'Đặt tiệc tại nhà',
        'contact' => 'Liên hệ',
        'account' => 'Tài khoản của tôi',
        'auth' => 'Đăng nhập / đăng ký',
        'customer' => 'Khu khách hàng',
        'staff' => 'Khu nhân viên',
    ];

    public static function defaultSettings(string $pageKey): array
    {
        $title = self::PAGE_OPTIONS[$pageKey] ?? 'Trang website';

        return [
            'banner' => [
                'image' => 'images/restaurant-interior.png',
                'subtitle' => $title,
                'title' => mb_strtoupper($title, 'UTF-8'),
                'content' => 'Nội dung giới thiệu có thể chỉnh trong Dashboard Admin.',
                'primary_button_label' => 'Tìm hiểu thêm',
                'primary_button_url' => '/',
                'secondary_button_label' => 'Liên hệ',
                'secondary_button_url' => '/lien-he',
                'position' => 'left',
                'height' => 560,
                'overlay' => 72,
            ],
            'style' => [
                'background_color' => '#fffaf0',
                'text_color' => '#221812',
                'accent_color' => '#d9a441',
                'font_family' => 'Be Vietnam Pro',
                'title_size' => 72,
                'content_size' => 18,
                'radius' => 8,
                'shadow' => true,
                'spacing' => 56,
                'width' => 1180,
                'animation' => 'fade-up',
            ],
            'button' => [
                'background' => '#d9a441',
                'text' => '#2c1b12',
                'hover' => '#f0bd55',
                'radius' => 8,
                'shadow' => true,
                'icon' => 'bi-arrow-right',
                'size' => 16,
            ],
            'text' => [
                'slogan' => 'Ẩm thực sân vườn và hải sản tươi sống',
                'hotline' => '0789661781',
                'address' => '100k Đ. Võ Văn Kiệt, Phường Long Châu, Vĩnh Long',
                'email' => 'phamnhutnhan2004@gmail.com',
                'footer' => 'Nhà hàng Hoa Sen. Không gian xanh, món Việt đậm vị và phục vụ tận tâm.',
            ],
            'auth_page' => self::authPageDefaults(),
            'sections' => [],
        ];
    }

    public static function authPageDefaults(): array
    {
        return [
            'content' => [
                'badge' => 'Nhà hàng Hoa Sen',
                'visual_title' => 'Đặt bàn nhanh, thưởng thức trọn vị Việt.',
                'visual_description' => 'Đăng nhập hoặc tạo tài khoản để đặt bàn, theo dõi lịch hẹn và nhận hỗ trợ từ nhà hàng.',
                'eyebrow' => 'Tài khoản khách hàng',
                'heading' => 'Chào mừng đến với Hoa Sen',
                'description' => '',
                'login_tab' => 'Đăng nhập',
                'register_tab' => 'Đăng ký',
                'login_button' => 'Đăng nhập',
                'register_button' => 'Tạo tài khoản',
                'benefits' => [
                    ['icon' => 'bi-calendar2-check-fill', 'title' => 'Quản lý đặt bàn', 'text' => 'Theo dõi thông tin đặt bàn trong tài khoản khách hàng.'],
                    ['icon' => 'bi-chat-dots-fill', 'title' => 'Hỗ trợ nhanh', 'text' => 'Chatbot và nhân viên luôn sẵn sàng tư vấn món ăn.'],
                    ['icon' => 'bi-shield-check', 'title' => 'Thông tin bảo mật', 'text' => 'Tài khoản được dùng cho đặt bàn và chăm sóc khách hàng.'],
                ],
            ],
            'style' => [
                'background_color' => '#449759',
                'shell_background' => '#fffaf0',
                'panel_background' => '#ffffff',
                'heading_color' => '#111111',
                'body_color' => '#221812',
                'muted_color' => '#4a4036',
                'visual_text_color' => '#ffffff',
                'accent_color' => '#f6df9d',
                'link_color' => '#0e3b32',
                'tab_background' => '#f6efe0',
                'tab_text' => '#2c1b12',
                'tab_active_background' => '#0e3b32',
                'tab_active_text' => '#ffffff',
                'button_background' => '#d9a441',
                'button_text' => '#2c1b12',
                'button_hover' => '#f0bd55',
                'input_border' => '#d9c6a8',
                'border_color' => '#d9a441',
                'visual_image' => 'images/restaurant-interior.png',
                'visual_overlay_start' => '#0e3b32',
                'visual_overlay_end' => '#2c1b12',
                'visual_overlay_opacity' => 88,
                'radius' => 8,
            ],
        ];
    }

    public static function current(string $pageKey): self
    {
        return self::firstOrCreate(
            ['page_key' => $pageKey],
            ['title' => self::PAGE_OPTIONS[$pageKey] ?? $pageKey, 'settings' => self::defaultSettings($pageKey)]
        );
    }

    public static function keyForRequest(Request $request): string
    {
        return match (true) {
            $request->routeIs('home') => 'home',
            $request->routeIs('about') => 'about',
            $request->routeIs('menu.category', 'products.show') => 'menu',
            $request->routeIs('register', 'register.store', 'login', 'login.store', 'password.*', 'verification.*') => 'auth',
            $request->routeIs('customer.dashboard', 'customer.reservations.*') => 'booking',
            $request->routeIs('home-parties.*') => 'home-party',
            $request->routeIs('contact') => 'contact',
            $request->routeIs('account.*') => 'account',
            $request->routeIs('customer.*') => 'customer',
            $request->routeIs('staff.*') => 'staff',
            default => 'home',
        };
    }

    public function resolvedSettings(): array
    {
        return array_replace_recursive(self::defaultSettings($this->page_key), $this->settings ?? []);
    }

    public function getSetting(string $key, mixed $default = null): mixed
    {
        return Arr::get($this->resolvedSettings(), $key, $default);
    }

    public function imageUrl(?string $path = null): string
    {
        $path ??= (string) $this->getSetting('banner.image', 'images/restaurant-interior.png');

        if ($path === '') {
            return asset('images/restaurant-interior.png');
        }

        if (str_starts_with($path, 'website-builder/')) {
            return Storage::url($path);
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset($path);
    }

    public function resetToDefaults(): void
    {
        $this->forceFill(['settings' => self::defaultSettings($this->page_key)])->save();
    }
}
