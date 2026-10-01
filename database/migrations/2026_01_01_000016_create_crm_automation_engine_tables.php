<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. CRM Automation Rules Table
        Schema::create('crm_automation_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('name');
            $table->string('trigger_event'); // e.g. lead.created, followup.overdue, document.missing, enquiry.submitted
            $table->json('conditions_json')->nullable();
            $table->json('actions_json');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['school_id', 'trigger_event', 'status']);
        });

        // 2. CRM Automation Audit Logs Table
        Schema::create('crm_automation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('rule_id')->nullable()->constrained('crm_automation_rules')->cascadeOnDelete();
            $table->string('trigger_event');
            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id');
            $table->string('execution_status'); // success, condition_unmet, blocked_by_auth, failed
            $table->json('actions_executed_json')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('executed_at');
            $table->timestamps();

            $table->index(['school_id', 'execution_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_automation_logs');
        Schema::dropIfExists('crm_automation_rules');
    }
};
