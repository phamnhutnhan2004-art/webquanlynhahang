<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ai_chatbot_settings')) {
            Schema::create('ai_chatbot_settings', function (Blueprint $table): void {
                $table->id();
                $table->boolean('is_enabled')->default(true);
                $table->string('provider', 40)->default('gemini');
                $table->string('model', 80)->default('gemini-2.5-flash');
                $table->text('encrypted_api_key')->nullable();
                $table->longText('system_prompt');
                $table->decimal('temperature', 3, 2)->default(0.40);
                $table->unsignedSmallInteger('max_output_tokens')->default(900);
                $table->timestamp('last_checked_at')->nullable();
                $table->string('last_status', 40)->nullable();
                $table->text('last_error')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_chatbot_settings');
    }
};
