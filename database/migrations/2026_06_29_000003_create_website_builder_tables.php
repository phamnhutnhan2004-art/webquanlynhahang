<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('website_page_settings')) {
            Schema::create('website_page_settings', function (Blueprint $table): void {
                $table->id();
                $table->string('page_key', 80)->unique();
                $table->string('title', 160);
                $table->json('settings');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('website_menu_items')) {
            Schema::create('website_menu_items', function (Blueprint $table): void {
                $table->id();
                $table->string('label', 120);
                $table->string('url', 255)->nullable();
                $table->string('route_name', 120)->nullable();
                $table->string('icon', 80)->nullable();
                $table->unsignedSmallInteger('sort_order')->default(10);
                $table->boolean('is_visible')->default(true);
                $table->string('target', 20)->default('_self');
                $table->timestamps();
            });
        }

        $now = now();
        foreach ($this->pageDefaults() as $key => $page) {
            DB::table('website_page_settings')->updateOrInsert(
                ['page_key' => $key],
                [
                    'title' => $page['title'],
                    'settings' => json_encode($page['settings'], JSON_UNESCAPED_UNICODE),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        foreach ($this->menuDefaults() as $item) {
            DB::table('website_menu_items')->updateOrInsert(
                ['label' => $item['label']],
                $item + [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('website_menu_items');
        Schema::dropIfExists('website_page_settings');
    }

    private function pageDefaults(): array
    {
        return [
            'home' => [
                'title' => 'Trang chủ',
                'settings' => $this->settings('Ẩm Thực Việt Cao Cấp', 'NHÀ HÀNG HOA SEN', 'Không gian sang trọng, thực đơn chọn lọc và hệ thống đặt bàn trực tuyến dành cho những bữa ăn đáng nhớ.', 'Đặt bàn nhanh', '#mon-an', 'Xem thực đơn', 'images/restaurant-interior.png', [
                    'gioi-thieu' => 'Giới thiệu',
                    'danh-muc' => 'Danh mục món ăn',
                    'mon-an' => 'Món nổi bật',
                    'menu-hinh-anh' => 'Hình ảnh món ăn',
                    'hinh-anh' => 'Hình ảnh',
                    'lien-he' => 'Liên hệ',
                    'chatbot' => 'Chatbot',
                ]),
            ],
            'about' => [
                'title' => 'Giới thiệu',
                'settings' => $this->settings('', 'NHÀ HÀNG HOA SEN', 'Không gian xanh thoáng mát, món ăn đồng quê đậm vị miền Tây và hải sản được chọn ngay tại bể cho những bữa ăn gia đình, gặp mặt bạn bè và tiệc nhóm trọn vẹn.', 'Đặt bàn', '#lien-he-dat-ban', 'Xem thực đơn', '/#mon-an', 'images/hero-restaurant.png', [
                    'toc' => 'Mục lục',
                    'nha-hang-hoa-sen' => 'Nhà hàng Hoa Sen',
                    'hai-san-tuoi-song' => 'Hải sản tươi sống',
                    'mon-an-dong-que' => 'Món ăn đồng quê',
                    'khong-gian-san-vuon' => 'Không gian sân vườn',
                    'lien-he-dat-ban' => 'Liên hệ đặt bàn',
                ]),
            ],
            'menu' => [
                'title' => 'Thực đơn',
                'settings' => $this->settings('Danh mục món ăn', 'THỰC ĐƠN HOA SEN', 'Khám phá các món ăn đang phục vụ tại Nhà hàng Hoa Sen.', 'Đặt bàn', '/dang-ky', 'Về trang chủ', '/', 'images/restaurant-interior.png', [
                    'menu-filter' => 'Bộ lọc thực đơn',
                    'menu-products' => 'Danh sách món ăn',
                ]),
            ],
            'booking' => [
                'title' => 'Đặt bàn',
                'settings' => $this->settings('Đặt bàn', 'ĐẶT BÀN NHÀ HÀNG HOA SEN', 'Chọn thời gian, số khách và gửi yêu cầu đặt bàn nhanh chóng.', 'Đăng ký tài khoản', '/dang-ky', 'Liên hệ', '/lien-he', 'images/restaurant-interior.png', []),
            ],
            'home-party' => [
                'title' => 'Đặt tiệc tại nhà',
                'settings' => $this->settings('Dịch vụ tận nơi', 'Đặt tiệc tại nhà cùng Nhà hàng Hoa Sen', 'Từ món ăn, nhân sự phục vụ đến lịch tổ chức, Hoa Sen hỗ trợ chuẩn bị buổi tiệc tại nhà chỉn chu như một nhà hàng thu nhỏ cho gia đình, công ty và bạn bè.', 'Gửi yêu cầu', '#party-form', 'Chọn thực đơn', '#party-menu', 'images/hero-restaurant.png', [
                    'party-service' => 'Dịch vụ',
                    'party-gallery' => 'Hình ảnh tiệc',
                    'party-menu' => 'Chọn thực đơn',
                    'party-form' => 'Form đặt tiệc',
                    'party-review' => 'Đánh giá khách hàng',
                ]),
            ],
            'gallery' => [
                'title' => 'Hình ảnh',
                'settings' => $this->settings('Không gian Hoa Sen', 'HÌNH ẢNH NHÀ HÀNG', 'Theo dõi không gian, món ăn và các khoảnh khắc tại Nhà hàng Hoa Sen.', 'Xem ảnh', '/#hinh-anh', 'Liên hệ', '/lien-he', 'images/restaurant-interior.png', []),
            ],
            'news' => [
                'title' => 'Tin tức',
                'settings' => $this->settings('Tin tức', 'TIN TỨC HOA SEN', 'Cập nhật thông báo, ưu đãi và hoạt động mới nhất của nhà hàng.', 'Liên hệ', '/lien-he', 'Trang chủ', '/', 'images/restaurant-interior.png', []),
            ],
            'contact' => [
                'title' => 'Liên hệ',
                'settings' => $this->settings('Kết nối với nhà hàng', 'LIÊN HỆ NHÀ HÀNG HOA SEN', 'Gửi yêu cầu đặt bàn, góp ý dịch vụ hoặc trao đổi về tiệc nhóm. Đội ngũ Hoa Sen sẽ phản hồi nhanh để chuẩn bị trải nghiệm chu đáo nhất cho anh/chị.', 'Gửi liên hệ', '#contactForm', 'Gọi hotline', 'tel:0789661781', 'images/restaurant-interior.png', [
                    'contact-info' => 'Thông tin liên hệ',
                    'contact-form' => 'Form liên hệ',
                    'contact-map' => 'Bản đồ',
                    'contact-footer' => 'Footer liên hệ',
                ]),
            ],
            'chatbot' => [
                'title' => 'Chatbot',
                'settings' => $this->settings('Trợ lý AI', 'CHATBOT NHÀ HÀNG HOA SEN', 'Hỗ trợ tư vấn món ăn, đặt bàn và thông tin nhà hàng ngay trên website.', 'Mở chatbot', '#chatbotPanel', 'Xem menu', '/#mon-an', 'images/restaurant-interior.png', [
                    'chatbot' => 'Khung chatbot',
                ]),
            ],
            'header' => [
                'title' => 'Header',
                'settings' => $this->settings('', 'Nhà hàng Hoa Sen', 'Ẩm thực sân vườn và hải sản tươi sống.', 'Đặt bàn', '/dang-ky', 'Đăng nhập', '/dang-nhap', 'images/restaurant-interior.png', []),
            ],
            'footer' => [
                'title' => 'Footer',
                'settings' => $this->settings('Nhà hàng Hoa Sen', 'Không gian sân vườn và hải sản tươi sống', 'Địa chỉ: 100k Đ. Võ Văn Kiệt, Phường Long Châu, Vĩnh Long. Hotline: 0789661781. Email: phamnhutnhan2004@gmail.com', 'Liên hệ', '/lien-he', 'Đặt bàn', '/dang-ky', 'images/hero-restaurant.png', []),
            ],
        ];
    }

    private function settings(string $subtitle, string $title, string $content, string $primaryLabel, string $primaryUrl, string $secondaryLabel, string|array $secondaryUrl, string|array|null $image = null, array $sections = []): array
    {
        if (is_array($image)) {
            $sections = $image;
            $image = (string) $secondaryUrl;
            $secondaryUrl = '/';
        }

        $image ??= 'images/restaurant-interior.png';

        return [
            'banner' => [
                'image' => $image,
                'subtitle' => $subtitle,
                'title' => $title,
                'content' => $content,
                'primary_button_label' => $primaryLabel,
                'primary_button_url' => $primaryUrl,
                'secondary_button_label' => $secondaryLabel,
                'secondary_button_url' => (string) $secondaryUrl,
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
            'sections' => collect($sections)->map(fn ($label, $key) => [
                'label' => $label,
                'visible' => true,
                'order' => 10,
            ])->all(),
        ];
    }

    private function menuDefaults(): array
    {
        return [
            ['label' => 'Trang chủ', 'route_name' => 'home', 'url' => null, 'icon' => 'bi-house', 'sort_order' => 10, 'is_visible' => true, 'target' => '_self'],
            ['label' => 'Giới thiệu', 'route_name' => 'about', 'url' => null, 'icon' => 'bi-info-circle', 'sort_order' => 20, 'is_visible' => true, 'target' => '_self'],
            ['label' => 'Thực đơn', 'route_name' => null, 'url' => '/#mon-an', 'icon' => 'bi-egg-fried', 'sort_order' => 30, 'is_visible' => true, 'target' => '_self'],
            ['label' => 'Đặt tiệc tại nhà', 'route_name' => 'home-parties.show', 'url' => null, 'icon' => 'bi-stars', 'sort_order' => 40, 'is_visible' => true, 'target' => '_self'],
            ['label' => 'Hình ảnh', 'route_name' => null, 'url' => '/#hinh-anh', 'icon' => 'bi-images', 'sort_order' => 50, 'is_visible' => true, 'target' => '_self'],
            ['label' => 'Liên hệ', 'route_name' => 'contact', 'url' => null, 'icon' => 'bi-telephone', 'sort_order' => 60, 'is_visible' => true, 'target' => '_self'],
        ];
    }
};
