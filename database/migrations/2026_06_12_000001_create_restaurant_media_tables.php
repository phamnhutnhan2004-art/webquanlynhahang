<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('menu_galleries')) {
            Schema::create('menu_galleries', function (Blueprint $table) {
                $table->id();
                $table->string('title', 150);
                $table->string('description')->nullable();
                $table->string('image');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('gallery_images')) {
            Schema::create('gallery_images', function (Blueprint $table) {
                $table->id();
                $table->string('title', 150);
                $table->string('image');
                $table->timestamps();
            });
        }

        if (DB::table('menu_galleries')->count() === 0) {
            DB::table('menu_galleries')->insert([
                [
                    'title' => 'Menu Hải sản',
                    'description' => 'Các món hải sản và món chính nổi bật của nhà hàng.',
                    'image' => 'images/ca-chep-sot-cai-xanh.png',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'title' => 'Menu Món cay',
                    'description' => 'Gợi ý món cay dùng cho bữa tối và tiệc nhóm.',
                    'image' => 'images/ga-xao-cay.png',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'title' => 'Menu Tiệc cưới',
                    'description' => 'Không gian và thực đơn phù hợp tiệc gia đình, tiệc cưới.',
                    'image' => 'images/restaurant-interior.png',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        if (DB::table('gallery_images')->count() === 0) {
            DB::table('gallery_images')->insert([
                [
                    'title' => 'Không gian mặt tiền',
                    'image' => 'images/hero-restaurant.png',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'title' => 'Không gian bàn tiệc',
                    'image' => 'images/restaurant-interior.png',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'title' => 'Gà xào cay',
                    'image' => 'images/ga-xao-cay.png',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'title' => 'Cá chép sốt cải xanh',
                    'image' => 'images/ca-chep-sot-cai-xanh.png',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_images');
        Schema::dropIfExists('menu_galleries');
    }
};
