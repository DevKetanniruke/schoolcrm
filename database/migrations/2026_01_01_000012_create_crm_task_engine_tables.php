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
        // 1. CRM Tasks table
        Schema::create('crm_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            
            // Polymorphic relation to Student, ParentProfile, AdmissionLead, StaffProfile, School, Application, Complaint, etc.
            $table->nullableMorphs('taskable');
            
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled', 'overdue'])->default('pending');
            
            $table->timestamp('due_at');
            $table->timestamp('completed_at')->nullable();
            
            $table->boolean('is_recurring')->default(false);
            $table->enum('recurrence_pattern', ['daily', 'weekly', 'monthly'])->nullable();
            $table->date('recurrence_end_date')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'status']);
            $table->index(['assigned_to', 'due_at']);
            $table->index('due_at');
        });

        // 2. Task Comments table
        Schema::create('crm_task_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('crm_tasks')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('comment');
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();
            $table->timestamps();
        });

        // 3. Task Activities table (Audit log of history actions)
        Schema::create('crm_task_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('crm_tasks')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('activity_type'); // created, status_changed, assigned, priority_changed, comment_added, reminder_sent, overdue_flagged
            $table->string('description');
            $table->json('properties')->nullable();
            $table->timestamps();
        });

        // 4. Task Reminders table
        Schema::create('crm_task_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('crm_tasks')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('remind_at');
            $table->enum('channel', ['in_app', 'email', 'sms', 'whatsapp'])->default('in_app');
            $table->boolean('is_sent')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['is_sent', 'remind_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_task_reminders');
        Schema::dropIfExists('crm_task_activities');
        Schema::dropIfExists('crm_task_comments');
        Schema::dropIfExists('crm_tasks');
    }
};
