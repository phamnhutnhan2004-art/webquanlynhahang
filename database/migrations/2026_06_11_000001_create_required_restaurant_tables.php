<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->constrained('categories')->restrictOnDelete()->cascadeOnUpdate();
                $table->string('name', 150);
                $table->string('slug', 180)->unique();
                $table->text('description')->nullable();
                $table->decimal('price', 12, 2);
                $table->string('image')->nullable();
                $table->enum('status', ['available', 'out_of_stock', 'inactive'])->default('available');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('reservation_id')->nullable()->unique()->constrained('reservations')->nullOnDelete()->cascadeOnUpdate();
                $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete()->cascadeOnUpdate();
                $table->foreignId('table_id')->nullable()->constrained('tables')->nullOnDelete()->cascadeOnUpdate();
                $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete()->cascadeOnUpdate();
                $table->string('order_code', 40)->unique();
                $table->enum('status', ['pending', 'serving', 'completed', 'cancelled'])->default('pending');
                $table->decimal('subtotal', 12, 2)->default(0);
                $table->decimal('discount', 12, 2)->default(0);
                $table->decimal('service_fee', 12, 2)->default(0);
                $table->decimal('vat', 12, 2)->default(0);
                $table->decimal('total_amount', 12, 2)->default(0);
                $table->dateTime('ordered_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreignId('product_id')->constrained('products')->restrictOnDelete()->cascadeOnUpdate();
                $table->unsignedSmallInteger('quantity');
                $table->decimal('unit_price', 12, 2);
                $table->decimal('total_price', 12, 2);
                $table->string('note')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('chatbot_logs')) {
            Schema::create('chatbot_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete()->cascadeOnUpdate();
                $table->foreignId('reservation_id')->nullable()->constrained('reservations')->nullOnDelete()->cascadeOnUpdate();
                $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete()->cascadeOnUpdate();
                $table->string('session_id', 100)->index();
                $table->string('sender', 40);
                $table->text('message');
                $table->string('intent', 100)->nullable();
                $table->decimal('confidence', 5, 2)->nullable();
                $table->timestamps();
            });
        }

        $this->copyExistingData();
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_logs');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('products');
    }

    private function copyExistingData(): void
    {
        if (Schema::hasTable('foods') && DB::table('products')->count() === 0) {
            DB::table('products')->insertUsing(
                ['id', 'category_id', 'name', 'slug', 'description', 'price', 'image', 'status', 'created_at', 'updated_at'],
                DB::table('foods')->select([
                    'id',
                    'category_id',
                    DB::raw('food_name as name'),
                    'slug',
                    'description',
                    'price',
                    'image',
                    DB::raw("'available' as status"),
                    'created_at',
                    'updated_at',
                ])
            );
        }

        if (Schema::hasTable('reservations') && DB::table('orders')->count() === 0) {
            DB::table('orders')->insertUsing(
                [
                    'id',
                    'reservation_id',
                    'customer_id',
                    'table_id',
                    'employee_id',
                    'order_code',
                    'status',
                    'subtotal',
                    'discount',
                    'service_fee',
                    'vat',
                    'total_amount',
                    'ordered_at',
                    'created_at',
                    'updated_at',
                ],
                DB::table('reservations')
                    ->leftJoin('payments', 'payments.reservation_id', '=', 'reservations.id')
                    ->select([
                        'reservations.id',
                        DB::raw('reservations.id as reservation_id'),
                        'reservations.customer_id',
                        'reservations.table_id',
                        'reservations.employee_id',
                        DB::raw('reservations.reservation_code as order_code'),
                        DB::raw("'pending' as status"),
                        DB::raw('COALESCE(payments.subtotal, 0) as subtotal'),
                        DB::raw('COALESCE(payments.discount, 0) as discount'),
                        DB::raw('COALESCE(payments.service_fee, 0) as service_fee'),
                        DB::raw('COALESCE(payments.vat, 0) as vat'),
                        DB::raw('COALESCE(payments.total_amount, 0) as total_amount'),
                        DB::raw('reservations.reservation_time as ordered_at'),
                        'reservations.created_at',
                        'reservations.updated_at',
                    ])
            );
        }

        if (Schema::hasTable('reservation_details') && DB::table('order_items')->count() === 0) {
            DB::table('order_items')->insertUsing(
                ['id', 'order_id', 'product_id', 'quantity', 'unit_price', 'total_price', 'note', 'created_at', 'updated_at'],
                DB::table('reservation_details')->select([
                    'id',
                    DB::raw('reservation_id as order_id'),
                    DB::raw('food_id as product_id'),
                    'quantity',
                    'unit_price',
                    DB::raw('(quantity * unit_price) as total_price'),
                    'note',
                    'created_at',
                    'updated_at',
                ])
            );
        }

        if (Schema::hasTable('chatbot_histories') && DB::table('chatbot_logs')->count() === 0) {
            DB::table('chatbot_logs')->insertUsing(
                ['id', 'customer_id', 'reservation_id', 'order_id', 'session_id', 'sender', 'message', 'intent', 'confidence', 'created_at', 'updated_at'],
                DB::table('chatbot_histories')->select([
                    'id',
                    'customer_id',
                    'reservation_id',
                    DB::raw('reservation_id as order_id'),
                    'session_id',
                    'sender',
                    'message',
                    'intent',
                    'confidence',
                    'created_at',
                    'updated_at',
                ])
            );
        }
    }
};
