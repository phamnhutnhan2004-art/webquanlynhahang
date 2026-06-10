<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60)->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('full_name', 120);
            $table->string('email', 150)->unique();
            $table->string('phone', 20)->unique();
            $table->string('password');
            $table->enum('status', ['đang hoạt động', 'tạm khóa'])->default('đang hoạt động');
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->string('full_name', 120);
            $table->string('phone', 20)->unique();
            $table->string('email', 150)->nullable();
            $table->string('address')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('employee_code', 30)->unique();
            $table->string('position', 80);
            $table->string('shift', 80);
            $table->decimal('salary', 12, 2)->default(0);
            $table->date('hire_date');
            $table->enum('status', ['đang làm', 'tạm nghỉ', 'đã nghỉ'])->default('đang làm');
            $table->timestamps();
        });

        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->string('table_code', 30)->unique();
            $table->string('table_name', 80);
            $table->string('area', 80);
            $table->unsignedTinyInteger('seats');
            $table->enum('status', ['trống', 'đang phục vụ', 'đã đặt', 'bảo trì'])->default('trống');
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('description')->nullable();
            $table->enum('status', ['hiển thị', 'ẩn'])->default('hiển thị');
            $table->timestamps();
        });

        Schema::create('foods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('food_name', 150);
            $table->string('slug', 180)->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->string('image')->nullable();
            $table->unsignedTinyInteger('spicy_level')->default(0);
            $table->unsignedTinyInteger('preparation_time')->default(10);
            $table->enum('status', ['đang bán', 'tạm hết', 'ngừng bán'])->default('đang bán');
            $table->timestamps();
        });

        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('table_id')->nullable()->constrained('tables')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete()->cascadeOnUpdate();
            $table->string('reservation_code', 40)->unique();
            $table->dateTime('reservation_time');
            $table->unsignedTinyInteger('number_of_guests');
            $table->string('note')->nullable();
            $table->enum('source', ['website', 'android', 'chatbot', 'điện thoại', 'trực tiếp'])->default('website');
            $table->enum('status', ['chờ xác nhận', 'đã xác nhận', 'đã hủy', 'hoàn thành'])->default('chờ xác nhận');
            $table->timestamps();
        });

        Schema::create('reservation_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('food_id')->constrained('foods')->restrictOnDelete()->cascadeOnUpdate();
            $table->unsignedSmallInteger('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->string('note')->nullable();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete()->cascadeOnUpdate();
            $table->string('payment_code', 40)->unique();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('service_fee', 12, 2)->default(0);
            $table->decimal('vat', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->enum('payment_method', ['tiền mặt', 'chuyển khoản', 'thẻ ngân hàng', 'ví điện tử'])->default('tiền mặt');
            $table->enum('payment_status', ['chưa thanh toán', 'đã thanh toán', 'đã hoàn tiền'])->default('chưa thanh toán');
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('chatbot_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('reservation_id')->nullable()->constrained('reservations')->nullOnDelete()->cascadeOnUpdate();
            $table->string('session_id', 100)->index();
            $table->enum('sender', ['khách hàng', 'chatbot', 'nhân viên']);
            $table->text('message');
            $table->string('intent', 100)->nullable();
            $table->decimal('confidence', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_histories');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('reservation_details');
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('foods');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('tables');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
    }
};
