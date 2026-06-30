<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('email');
            }

            if (! Schema::hasColumn('users', 'otp_code')) {
                $table->string('otp_code', 6)->nullable()->after('remember_token');
            }

            if (! Schema::hasColumn('users', 'otp_expired_at')) {
                $table->timestamp('otp_expired_at')->nullable()->after('otp_code');
            }
        });

        DB::table('users')
            ->whereNull('email_verified_at')
            ->update(['email_verified_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (Schema::hasColumn('users', 'otp_expired_at')) {
                $table->dropColumn('otp_expired_at');
            }

            if (Schema::hasColumn('users', 'otp_code')) {
                $table->dropColumn('otp_code');
            }

            if (Schema::hasColumn('users', 'email_verified_at')) {
                $table->dropColumn('email_verified_at');
            }
        });
    }
};
