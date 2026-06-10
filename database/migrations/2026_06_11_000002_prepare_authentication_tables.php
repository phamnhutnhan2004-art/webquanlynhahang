<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'name')) {
                $table->string('name', 120)->nullable()->after('id');
            }

            if (! Schema::hasColumn('users', 'address')) {
                $table->string('address')->nullable()->after('phone');
            }

            if (! Schema::hasColumn('users', 'remember_token')) {
                $table->rememberToken()->after('password');
            }
        });

        DB::table('users')
            ->whereNull('name')
            ->update(['name' => DB::raw('full_name')]);

        DB::table('roles')->updateOrInsert(
            ['id' => 1],
            ['name' => 'Admin', 'description' => 'Toàn quyền hệ thống', 'created_at' => now(), 'updated_at' => now()]
        );

        DB::table('roles')->updateOrInsert(
            ['id' => 2],
            ['name' => 'Nhân viên', 'description' => 'Xử lý đơn hàng, đặt bàn và khách hàng', 'created_at' => now(), 'updated_at' => now()]
        );

        DB::table('users')->whereIn('role_id', [4])->update(['role_id' => 2]);
        DB::table('users')->whereIn('role_id', [5])->update(['role_id' => 3]);
        DB::table('roles')->whereIn('id', [4, 5])->delete();

        DB::table('roles')->updateOrInsert(
            ['id' => 3],
            ['name' => 'Khách hàng', 'description' => 'Xem menu, đặt bàn, đặt món và chatbot', 'created_at' => now(), 'updated_at' => now()]
        );

        if (! Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'remember_token')) {
                $table->dropColumn('remember_token');
            }

            if (Schema::hasColumn('users', 'address')) {
                $table->dropColumn('address');
            }

            if (Schema::hasColumn('users', 'name')) {
                $table->dropColumn('name');
            }
        });
    }
};
