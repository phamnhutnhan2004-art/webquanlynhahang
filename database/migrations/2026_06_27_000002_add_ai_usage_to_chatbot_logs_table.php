<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('chatbot_logs')) {
            return;
        }

        Schema::table('chatbot_logs', function (Blueprint $table): void {
            if (! Schema::hasColumn('chatbot_logs', 'model')) {
                $table->string('model', 80)->nullable()->after('confidence');
            }

            if (! Schema::hasColumn('chatbot_logs', 'prompt_tokens')) {
                $table->unsignedInteger('prompt_tokens')->nullable()->after('model');
            }

            if (! Schema::hasColumn('chatbot_logs', 'completion_tokens')) {
                $table->unsignedInteger('completion_tokens')->nullable()->after('prompt_tokens');
            }

            if (! Schema::hasColumn('chatbot_logs', 'total_tokens')) {
                $table->unsignedInteger('total_tokens')->nullable()->after('completion_tokens');
            }

            if (! Schema::hasColumn('chatbot_logs', 'metadata')) {
                $table->json('metadata')->nullable()->after('total_tokens');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('chatbot_logs')) {
            return;
        }

        Schema::table('chatbot_logs', function (Blueprint $table): void {
            foreach (['metadata', 'total_tokens', 'completion_tokens', 'prompt_tokens', 'model'] as $column) {
                if (Schema::hasColumn('chatbot_logs', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
