<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table): void {
            if (Schema::hasColumn('reservations', 'customer_id')) {
                $table->dropForeign(['customer_id']);
            }
        });

        DB::statement('ALTER TABLE reservations MODIFY customer_id BIGINT UNSIGNED NULL');

        Schema::table('reservations', function (Blueprint $table): void {
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete()->cascadeOnUpdate();

            if (! Schema::hasColumn('reservations', 'guest_name')) {
                $table->string('guest_name', 150)->nullable()->after('customer_id');
            }

            if (! Schema::hasColumn('reservations', 'guest_phone')) {
                $table->string('guest_phone', 30)->nullable()->after('guest_name');
            }

            if (! Schema::hasColumn('reservations', 'guest_email')) {
                $table->string('guest_email', 150)->nullable()->after('guest_phone');
            }

            if (! Schema::hasColumn('reservations', 'customer_type')) {
                $table->enum('customer_type', ['khách thành viên', 'khách tiềm năng'])->default('khách tiềm năng')->after('guest_email');
            }

            if (! Schema::hasColumn('reservations', 'confirmation_sent_at')) {
                $table->timestamp('confirmation_sent_at')->nullable()->after('status');
            }
        });

        DB::table('reservations')
            ->leftJoin('customers', 'reservations.customer_id', '=', 'customers.id')
            ->whereNotNull('reservations.customer_id')
            ->update([
                'reservations.guest_name' => DB::raw('COALESCE(reservations.guest_name, customers.full_name)'),
                'reservations.guest_phone' => DB::raw('COALESCE(reservations.guest_phone, customers.phone)'),
                'reservations.guest_email' => DB::raw('COALESCE(reservations.guest_email, customers.email)'),
                'reservations.customer_type' => 'khách thành viên',
            ]);
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table): void {
            if (Schema::hasColumn('reservations', 'confirmation_sent_at')) {
                $table->dropColumn('confirmation_sent_at');
            }

            if (Schema::hasColumn('reservations', 'customer_type')) {
                $table->dropColumn('customer_type');
            }

            if (Schema::hasColumn('reservations', 'guest_email')) {
                $table->dropColumn('guest_email');
            }

            if (Schema::hasColumn('reservations', 'guest_phone')) {
                $table->dropColumn('guest_phone');
            }

            if (Schema::hasColumn('reservations', 'guest_name')) {
                $table->dropColumn('guest_name');
            }
        });
    }
};
