<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_communication_drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            
            // Input parameters
            $table->string('topic');
            $table->string('audience')->default('all_parents');
            $table->string('tone')->default('formal');
            $table->string('language')->default('en'); // en, mr, hi
            $table->text('important_info')->nullable();
            
            // AI Generated Multi-Channel Content Formats
            $table->text('short_notification')->nullable();
            $table->text('detailed_message')->nullable();
            $table->string('email_subject')->nullable();
            $table->text('email_body')->nullable();
            $table->text('sms_version')->nullable();
            $table->text('whatsapp_version')->nullable();
            
            // Approval Workflow & Safeguards
            $table->string('approval_status')->default('draft'); // draft, pending_approval, approved, rejected, sent
            $table->boolean('require_approval')->default(true);
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Dispatch Meta
            $table->json('target_filters_json')->nullable();
            $table->foreignId('communication_id')->nullable()->constrained('communications')->onDelete('set null');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_communication_drafts');
    }
};
