<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. AI Conversations Table
        Schema::create('ai_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('capability'); // school_assistant, admission_lead_assistant, etc.
            $table->string('title')->default('AI Chat Session');
            $table->json('messages_json');
            $table->timestamps();

            $table->index(['school_id', 'user_id']);
        });

        // 2. AI Usage Logs Table
        Schema::create('ai_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('provider'); // local_heuristic, openai, anthropic, gemini
            $table->string('capability');
            $table->integer('prompt_tokens')->default(0);
            $table->integer('completion_tokens')->default(0);
            $table->integer('total_tokens')->default(0);
            $table->decimal('cost_estimate', 10, 4)->default(0.0000);
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_usage_logs');
        Schema::dropIfExists('ai_conversations');
    }
};
