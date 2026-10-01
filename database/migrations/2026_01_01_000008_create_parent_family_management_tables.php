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
        // 1. Parents / Guardians Master Table
        Schema::create('parents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('parent_code')->unique();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('alternate_phone')->nullable();
            $table->string('occupation')->nullable();
            $table->string('employer')->nullable();
            $table->decimal('annual_income', 12, 2)->nullable();
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('USA');
            $table->enum('status', ['active', 'inactive', 'archived'])->default('active');
            $table->string('avatar_path')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'status']);
            $table->index(['first_name', 'last_name']);
            $table->index('phone');
            $table->index('email');
        });

        // 2. Parent-Student Pivot Relationship Table (N-to-M)
        Schema::create('parent_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('parents')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->enum('relationship_type', [
                'father', 'mother', 'step_father', 'step_mother',
                'legal_guardian', 'grandparent', 'uncle', 'aunt', 'other'
            ])->default('legal_guardian');
            $table->boolean('is_primary_contact')->default(true);
            $table->boolean('is_emergency_contact')->default(true);
            $table->boolean('has_custody')->default(true);
            $table->boolean('financial_responsibility')->default(true);
            $table->boolean('portal_access_enabled')->default(true);
            $table->timestamps();

            $table->unique(['parent_id', 'student_id']);
        });

        // 3. Extended Family Members Network
        Schema::create('family_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('parents')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->string('name');
            $table->string('relationship');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_authorized_pickup')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 4. Parent Communication Log
        Schema::create('parent_communications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('parent_id')->constrained('parents')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('type', ['email', 'phone_call', 'in_person_meeting', 'sms', 'portal_message'])->default('phone_call');
            $table->enum('direction', ['inbound', 'outbound'])->default('outbound');
            $table->string('subject');
            $table->text('summary');
            $table->dateTime('interaction_date');
            $table->enum('status', ['completed', 'follow_up_required', 'pending'])->default('completed');
            $table->timestamps();

            $table->index(['parent_id', 'interaction_date']);
            $table->index('type');
        });

        // 5. Parent Complaints & Concerns
        Schema::create('parent_complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('parent_id')->constrained('parents')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->string('ticket_number')->unique();
            $table->enum('category', [
                'academic', 'behavior', 'facility', 'fee_billing',
                'transport', 'safety', 'general'
            ])->default('general');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->string('subject');
            $table->text('description');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('resolution_notes')->nullable();
            $table->dateTime('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'status']);
        });

        // 6. Parent Appointments (Meetings & Counseling)
        Schema::create('parent_appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('parent_id')->constrained('parents')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('type', [
                'parent_teacher_meeting', 'counseling', 'principal_meeting',
                'admission_interview', 'fee_discussion'
            ])->default('parent_teacher_meeting');
            $table->string('title');
            $table->dateTime('scheduled_at');
            $table->integer('duration_minutes')->default(30);
            $table->string('location_or_link')->nullable();
            $table->enum('status', ['scheduled', 'completed', 'cancelled', 'rescheduled'])->default('scheduled');
            $table->text('meeting_notes')->nullable();
            $table->timestamps();

            $table->index(['parent_id', 'scheduled_at']);
        });

        // 7. Parent Assigned Tasks
        Schema::create('parent_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('parent_id')->constrained('parents')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('due_date')->nullable();
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->timestamps();
        });

        // 8. Parent Notes & Annotations
        Schema::create('parent_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('parents')->cascadeOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note');
            $table->enum('category', ['general', 'academic', 'behavioral', 'financial', 'counseling'])->default('general');
            $table->boolean('is_confidential')->default(false);
            $table->timestamps();
        });

        // 9. Parent Documents
        Schema::create('parent_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('parents')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->string('title');
            $table->string('document_type'); // e.g. ID Proof, Consent Form, Custody Doc
            $table->string('file_path');
            $table->unsignedBigInteger('file_size')->default(0);
            $table->string('mime_type')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parent_documents');
        Schema::dropIfExists('parent_notes');
        Schema::dropIfExists('parent_tasks');
        Schema::dropIfExists('parent_appointments');
        Schema::dropIfExists('parent_complaints');
        Schema::dropIfExists('parent_communications');
        Schema::dropIfExists('family_members');
        Schema::dropIfExists('parent_student');
        Schema::dropIfExists('parents');
    }
};
