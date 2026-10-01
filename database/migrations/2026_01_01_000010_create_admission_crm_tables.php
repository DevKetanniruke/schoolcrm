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
        // 1. Lead Sources Master Table
        Schema::create('lead_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('name'); // Website, Referral, Social Media, Open House, Walk-in, Education Fair
            $table->string('code')->nullable();
            $table->string('type')->default('digital'); // digital, offline, referral, event
            $table->string('status')->default('active');
            $table->timestamps();
        });

        // 2. Lead Campaigns Master Table
        Schema::create('lead_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('name'); // Fall 2026 Admissions Drive, Open House Spring, etc.
            $table->string('code')->unique();
            $table->decimal('budget', 12, 2)->default(0.00);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('active'); // draft, active, completed, paused
            $table->timestamps();
        });

        // 3. Admission Leads Core Table
        Schema::create('admission_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('source_id')->nullable()->constrained('lead_sources')->nullOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained('lead_campaigns')->nullOnDelete();
            $table->foreignId('assigned_counselor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('grade_applying_for_id')->nullable()->constrained('school_classes')->nullOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->string('lead_code')->unique(); // LEAD-2026-001
            $table->string('parent_name');
            $table->string('parent_email');
            $table->string('parent_phone');
            $table->string('alternate_phone')->nullable();
            $table->string('student_name');
            $table->date('student_dob')->nullable();
            $table->string('gender')->default('other');
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('stage')->default('new_lead');
            // Stages: new_lead, contacted, counselling, campus_visit, application_started, documents_pending, application_submitted, under_review, approved, admission_confirmed, lost
            $table->string('status')->default('active'); // active, qualified, disqualified, converted, lost
            $table->integer('score')->default(50); // Lead priority score 0-100
            $table->text('loss_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. Admission Enquiries Table
        Schema::create('admission_enquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('admission_leads')->cascadeOnDelete();
            $table->foreignId('answered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('inquiry_type')->default('general'); // fee_structure, curriculum, transport, hostel, scholarship
            $table->text('question');
            $table->text('response')->nullable();
            $table->dateTime('inquiry_date');
            $table->timestamps();
        });

        // 5. Admission Applications Table
        Schema::create('admission_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('admission_leads')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('class_id')->nullable()->constrained('school_classes')->nullOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->string('application_number')->unique(); // APP-2026-001
            $table->string('application_stage')->default('draft'); // draft, submitted, under_review, interview_scheduled, approved, rejected, enrolled
            $table->date('submission_date')->nullable();
            $table->date('decision_date')->nullable();
            $table->text('reviewer_notes')->nullable();
            $table->string('fee_status')->default('pending'); // pending, paid, waived
            $table->timestamps();
        });

        // 6. Lead Follow-ups & Activities Table
        Schema::create('lead_follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('admission_leads')->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type')->default('phone_call'); // phone_call, email, meeting, campus_visit, whatsapp
            $table->string('subject');
            $table->text('notes')->nullable();
            $table->dateTime('scheduled_at');
            $table->date('due_date')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->string('status')->default('pending'); // pending, completed, overdue, cancelled
            $table->timestamps();
        });

        // 7. Lead Documents Table
        Schema::create('lead_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('admission_leads')->cascadeOnDelete();
            $table->foreignId('application_id')->nullable()->constrained('admission_applications')->nullOnDelete();
            $table->string('title');
            $table->string('document_type'); // birth_certificate, report_card, passport_photo, transfer_certificate, medical_form
            $table->string('file_path');
            $table->unsignedBigInteger('file_size')->default(0);
            $table->string('mime_type')->default('application/pdf');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 8. Lead Stage Status Histories Table
        Schema::create('lead_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('admission_leads')->cascadeOnDelete();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('from_stage')->nullable();
            $table->string('to_stage');
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_status_histories');
        Schema::dropIfExists('lead_documents');
        Schema::dropIfExists('lead_follow_ups');
        Schema::dropIfExists('admission_applications');
        Schema::dropIfExists('admission_enquiries');
        Schema::dropIfExists('admission_leads');
        Schema::dropIfExists('lead_campaigns');
        Schema::dropIfExists('lead_sources');
    }
};
