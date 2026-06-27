<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tables')) {
            DB::statement("ALTER TABLE tables MODIFY status ENUM('trống','đang phục vụ','đã đặt','bảo trì','đang sử dụng','đang dọn dẹp') NOT NULL DEFAULT 'trống'");

            DB::table('tables')->where('status', 'đang phục vụ')->update(['status' => 'đang sử dụng']);
            DB::table('tables')->where('status', 'bảo trì')->update(['status' => 'đang dọn dẹp']);

            DB::statement("ALTER TABLE tables MODIFY status ENUM('trống','đã đặt','đang sử dụng','đang dọn dẹp') NOT NULL DEFAULT 'trống'");
        }

        if (! Schema::hasTable('kitchen_orders')) {
            Schema::create('kitchen_orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->unique()->constrained('orders')->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreignId('staff_id')->nullable()->constrained('employees')->nullOnDelete()->cascadeOnUpdate();
                $table->foreignId('chef_id')->nullable()->constrained('employees')->nullOnDelete()->cascadeOnUpdate();
                $table->enum('status', ['pending', 'cooking', 'completed', 'served'])->default('pending');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('kitchen_order_items')) {
            Schema::create('kitchen_order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('kitchen_order_id')->constrained('kitchen_orders')->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreignId('food_id')->constrained('products')->restrictOnDelete()->cascadeOnUpdate();
                $table->unsignedSmallInteger('quantity');
                $table->enum('status', ['pending', 'cooking', 'completed', 'served'])->default('pending');
                $table->timestamps();

                $table->index(['status', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('kitchen_order_items');
        Schema::dropIfExists('kitchen_orders');

        if (Schema::hasTable('tables')) {
            DB::statement("ALTER TABLE tables MODIFY status ENUM('trống','đã đặt','đang sử dụng','đang dọn dẹp','đang phục vụ','bảo trì') NOT NULL DEFAULT 'trống'");

            DB::table('tables')->where('status', 'đang sử dụng')->update(['status' => 'đang phục vụ']);
            DB::table('tables')->where('status', 'đang dọn dẹp')->update(['status' => 'bảo trì']);

            DB::statement("ALTER TABLE tables MODIFY status ENUM('trống','đang phục vụ','đã đặt','bảo trì') NOT NULL DEFAULT 'trống'");
        }
    }
};
