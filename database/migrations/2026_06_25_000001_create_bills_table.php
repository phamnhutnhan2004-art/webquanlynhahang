<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE orders MODIFY status ENUM('pending', 'serving', 'completed', 'cancelled', 'paid') NOT NULL DEFAULT 'pending'");

        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained('orders')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('cashier_id')->nullable()->constrained('employees')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('table_id')->nullable()->constrained('tables')->nullOnDelete()->cascadeOnUpdate();
            $table->string('bill_code', 40)->unique();
            $table->enum('payment_method', ['cash', 'bank_transfer', 'qr', 'e_wallet']);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('service_fee', 12, 2)->default(0);
            $table->decimal('vat', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->timestamp('paid_at')->nullable();
            $table->enum('status', ['paid', 'cancelled', 'refunded'])->default('paid');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bills');

        DB::statement("ALTER TABLE orders MODIFY status ENUM('pending', 'serving', 'completed', 'cancelled') NOT NULL DEFAULT 'pending'");
    }
};
