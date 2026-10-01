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
        // 1. Communication Templates Table
        Schema::create('communication_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('title');
            $table->string('code')->unique(); // e.g. TPL-FEE-REMINDER, TPL-ATTENDANCE-ALERT
            $table->string('channel')->default('email'); // in_app, email, sms, whatsapp, multi_channel
            $table->string('category')->default('general'); // academic, attendance, fees, events, admissions, emergency, general
            $table->string('subject')->nullable();
            $table->text('content_body');
            $table->json('variables_json')->nullable();
            $table->string('status')->default('active'); // active, draft, archived
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 2. Communications Core Table (Dispatches & Broadcasts)
        Schema::create('communications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('template_id')->nullable()->constrained('communication_templates')->nullOnDelete();
            $table->string('title');
            $table->string('channel')->default('email'); // in_app, email, sms, whatsapp, multi_channel
            $table->string('recipient_type')->default('individual'); // individual, class, parent_group, staff_group, school_wide, admission_leads
            $table->json('target_filters_json')->nullable();
            $table->string('subject')->nullable();
            $table->text('message_body');
            $table->json('payload_data_json')->nullable();
            $table->dateTime('scheduled_at')->nullable();
            $table->string('status')->default('queued'); // draft, queued, processing, sent, partially_failed, failed, cancelled
            $table->integer('total_recipients')->default(0);
            $table->integer('success_count')->default(0);
            $table->integer('failure_count')->default(0);
            $table->dateTime('sent_at')->nullable();
            $table->timestamps();
        });

        // 3. Communication Logs & Delivery Status Tracking Table
        Schema::create('communication_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('communication_id')->constrained('communications')->cascadeOnDelete();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('recipient_type')->default('parent'); // student, parent, staff, admission_lead, user
            $table->unsignedBigInteger('recipient_id')->nullable();
            $table->string('recipient_email')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->string('channel')->default('email'); // in_app, email, sms, whatsapp
            $table->string('provider_name')->default('log'); // log, smtp, mailgun, twilio_sms, meta_whatsapp, database_in_app
            $table->string('provider_message_id')->nullable();
            $table->string('delivery_status')->default('pending'); // pending, sent, delivered, read, failed
            $table->text('failure_reason')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->dateTime('read_at')->nullable();
            $table->timestamps();
        });

        // 4. Notification Preferences Table
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('entity_type')->default('user'); // user, parent, student, staff, lead
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->boolean('channel_in_app')->default(true);
            $table->boolean('channel_email')->default(true);
            $table->boolean('channel_sms')->default(true);
            $table->boolean('channel_whatsapp')->default(true);
            $table->boolean('category_academic')->default(true);
            $table->boolean('category_attendance')->default(true);
            $table->boolean('category_fees')->default(true);
            $table->boolean('category_events')->default(true);
            $table->boolean('category_emergency')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
        Schema::dropIfExists('communication_logs');
        Schema::dropIfExists('communications');
        Schema::dropIfExists('communication_templates');
    }
};
