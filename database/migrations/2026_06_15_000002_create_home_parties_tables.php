<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_parties', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('assigned_employee_id')->nullable()->constrained('employees')->nullOnDelete()->cascadeOnUpdate();
            $table->string('full_name', 120);
            $table->string('phone', 20);
            $table->string('email', 150)->nullable();
            $table->string('address');
            $table->date('event_date');
            $table->time('event_time');
            $table->unsignedSmallInteger('guest_quantity');
            $table->string('party_type', 80);
            $table->text('note')->nullable();
            $table->decimal('total_price', 12, 2)->default(0);
            $table->enum('status', [
                'chờ xác nhận',
                'đã xác nhận',
                'đang chuẩn bị',
                'đang phục vụ',
                'hoàn thành',
                'đã hủy',
            ])->default('chờ xác nhận');
            $table->timestamps();
        });

        Schema::create('home_party_details', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('home_party_id')->constrained('home_parties')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('food_id')->constrained('products')->restrictOnDelete()->cascadeOnUpdate();
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->decimal('price', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_party_details');
        Schema::dropIfExists('home_parties');
    }
};
