<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('reservations', 'customer_type')) {
            return;
        }

        DB::statement("ALTER TABLE reservations MODIFY customer_type ENUM('thành viên','khách thành viên','khách tiềm năng') NOT NULL DEFAULT 'khách tiềm năng'");
        DB::table('reservations')->where('customer_type', 'thành viên')->update(['customer_type' => 'khách thành viên']);
        DB::table('reservations')->whereNotNull('customer_id')->update(['customer_type' => 'khách thành viên']);
        DB::statement("ALTER TABLE reservations MODIFY customer_type ENUM('khách thành viên','khách tiềm năng') NOT NULL DEFAULT 'khách tiềm năng'");
    }

    public function down(): void
    {
        if (! Schema::hasColumn('reservations', 'customer_type')) {
            return;
        }

        DB::statement("ALTER TABLE reservations MODIFY customer_type ENUM('thành viên','khách tiềm năng') NOT NULL DEFAULT 'khách tiềm năng'");
        DB::table('reservations')->where('customer_type', 'khách thành viên')->update(['customer_type' => 'thành viên']);
    }
};
