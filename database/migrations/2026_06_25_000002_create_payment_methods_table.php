<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('payment_methods')) {
            Schema::create('payment_methods', function (Blueprint $table) {
                $table->id();
                $table->enum('method_key', ['cash', 'bank_transfer', 'qr', 'e_wallet']);
                $table->string('display_name', 120);
                $table->string('bank_name', 120)->nullable();
                $table->string('account_holder', 150)->nullable();
                $table->string('account_number', 80)->nullable();
                $table->string('transfer_content_template', 180)->default('THANHTOAN_[ORDER_CODE]');
                $table->string('qr_image')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamps();

                $table->index(['method_key', 'is_active']);
            });
        }

        if (! Schema::hasColumn('bills', 'payment_method_id')) {
            Schema::table('bills', function (Blueprint $table) {
                $table->foreignId('payment_method_id')
                    ->nullable()
                    ->after('table_id')
                    ->constrained('payment_methods')
                    ->nullOnDelete()
                    ->cascadeOnUpdate();
            });
        }

        if (DB::table('payment_methods')->exists()) {
            return;
        }

        DB::table('payment_methods')->insert([
            [
                'method_key' => 'cash',
                'display_name' => 'Tiền mặt',
                'bank_name' => null,
                'account_holder' => null,
                'account_number' => null,
                'transfer_content_template' => 'THANHTOAN_[ORDER_CODE]',
                'qr_image' => null,
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'method_key' => 'bank_transfer',
                'display_name' => 'Chuyển khoản ngân hàng',
                'bank_name' => 'VCB',
                'account_holder' => 'PHAM NHUT NHAN',
                'account_number' => '9789661781',
                'transfer_content_template' => 'THANHTOAN_[ORDER_CODE]',
                'qr_image' => null,
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'method_key' => 'qr',
                'display_name' => 'Quét mã QR',
                'bank_name' => 'VCB',
                'account_holder' => 'PHAM NHUT NHAN',
                'account_number' => '9789661781',
                'transfer_content_template' => 'THANHTOAN_[ORDER_CODE]',
                'qr_image' => null,
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'method_key' => 'e_wallet',
                'display_name' => 'Ví điện tử',
                'bank_name' => null,
                'account_holder' => null,
                'account_number' => null,
                'transfer_content_template' => 'THANHTOAN_[ORDER_CODE]',
                'qr_image' => null,
                'is_active' => false,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('bills', 'payment_method_id')) {
            Schema::table('bills', function (Blueprint $table) {
                $table->dropConstrainedForeignId('payment_method_id');
            });
        }

        Schema::dropIfExists('payment_methods');
    }
};
