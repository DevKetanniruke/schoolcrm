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
        // 1. Staff Profiles Table
        Schema::create('staff_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->string('employee_code')->unique();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('gender')->default('other');
            $table->date('date_of_birth')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('alternate_phone')->nullable();
            $table->string('role_type')->default('teacher'); // teacher, principal, counselor, hr, accountant, admin_staff, maintenance
            $table->string('designation')->nullable(); // Senior Physics Teacher, HR Lead, etc.
            $table->string('qualification')->nullable(); // M.Sc Physics, B.Ed, Ph.D
            $table->integer('experience_years')->default(0);
            $table->date('joining_date')->nullable();
            $table->string('employment_type')->default('full_time'); // full_time, part_time, contract, visiting
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('USA');
            $table->string('status')->default('active'); // active, inactive, on_leave, terminated
            $table->string('avatar_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Teacher Subjects Mapping Pivot
        Schema::create('teacher_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff_profiles')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->boolean('is_primary')->default(true);
            $table->timestamps();
        });

        // 3. Teacher Class Assignments Mapping Pivot
        Schema::create('teacher_class_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff_profiles')->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('division_id')->nullable()->constrained('divisions')->nullOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->string('assignment_role')->default('subject_teacher'); // class_teacher, subject_teacher, co_teacher
            $table->boolean('is_class_teacher')->default(false);
            $table->timestamps();
        });

        // 4. Staff Documents Table
        Schema::create('staff_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff_profiles')->cascadeOnDelete();
            $table->string('title');
            $table->string('document_type'); // resume, degree_certificate, identity_proof, background_check, employment_contract, certification
            $table->string('file_path');
            $table->unsignedBigInteger('file_size')->default(0);
            $table->string('mime_type')->default('application/pdf');
            $table->string('verification_status')->default('verified'); // pending, verified, rejected
            $table->date('expiry_date')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 5. Staff Administrative & Performance Notes Table
        Schema::create('staff_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff_profiles')->cascadeOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note');
            $table->string('category')->default('general'); // general, performance, commendation, disciplinary, training
            $table->boolean('is_confidential')->default(false);
            $table->timestamps();
        });

        // 6. Staff Tasks Table
        Schema::create('staff_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff_profiles')->cascadeOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('due_date')->nullable();
            $table->string('priority')->default('medium'); // low, medium, high, urgent
            $table->string('status')->default('pending'); // pending, in_progress, completed, deferred
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // 7. Staff Performance Evaluations Table
        Schema::create('staff_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff_profiles')->cascadeOnDelete();
            $table->foreignId('evaluator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('review_period'); // Q1 2024, Annual 2024-2025, etc.
            $table->unsignedTinyInteger('rating')->default(5); // 1-5 scale rating
            $table->text('feedback');
            $table->text('goals')->nullable();
            $table->date('evaluation_date');
            $table->timestamps();
        });

        // 8. Staff Timetable Schedules Table
        Schema::create('staff_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff_profiles')->cascadeOnDelete();
            $table->foreignId('class_id')->nullable()->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->cascadeOnDelete();
            $table->string('day_of_week'); // monday, tuesday, wednesday, thursday, friday
            $table->string('start_time'); // 08:30
            $table->string('end_time'); // 09:30
            $table->string('room_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_schedules');
        Schema::dropIfExists('staff_evaluations');
        Schema::dropIfExists('staff_tasks');
        Schema::dropIfExists('staff_notes');
        Schema::dropIfExists('staff_documents');
        Schema::dropIfExists('teacher_class_assignments');
        Schema::dropIfExists('teacher_subjects');
        Schema::dropIfExists('staff_profiles');
    }
};
