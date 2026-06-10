<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->where('id', 1)->update(['role_id' => 1]);

        DB::table('users')
            ->whereIn('id', DB::table('employees')->select('user_id'))
            ->where('id', '!=', 1)
            ->update(['role_id' => 2]);

        DB::table('users')
            ->whereIn('id', DB::table('customers')->whereNotNull('user_id')->select('user_id'))
            ->update(['role_id' => 3]);
    }

    public function down(): void
    {
        //
    }
};
