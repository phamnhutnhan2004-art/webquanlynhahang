<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('roles')->where('id', 1)->update([
            'name' => 'Admin',
            'description' => 'Toàn quyền hệ thống',
            'updated_at' => now(),
        ]);

        DB::table('roles')->where('id', 2)->update([
            'name' => 'Nhân viên',
            'description' => 'Tạo đơn hàng, đặt bàn và quản lý khách hàng',
            'updated_at' => now(),
        ]);

        DB::table('roles')->where('id', 3)->update([
            'name' => 'Khách hàng',
            'description' => 'Xem menu, đặt bàn, đặt món và chatbot',
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        //
    }
};
