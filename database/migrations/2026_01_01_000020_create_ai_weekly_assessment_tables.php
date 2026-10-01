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
        // 1. Assessment Topics / Chapters
        Schema::create('assessment_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('class_id')->nullable()->constrained('school_classes')->nullOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->integer('display_order')->default(1);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'subject_id', 'status'], 'assessment_topics_idx');
        });

        // 2. Assessments (Weekly Test, Practice Test, Revision Test, etc.)
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->foreignId('class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('division_id')->nullable()->constrained('divisions')->nullOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('code')->nullable();
            $table->enum('type', ['weekly_test', 'practice_test', 'revision_test'])->default('weekly_test');
            $table->integer('week_number')->nullable();
            $table->integer('duration_minutes')->default(30);
            $table->integer('total_questions')->default(0);
            $table->decimal('total_marks', 8, 2)->default(0.00);
            $table->decimal('passing_marks', 8, 2)->default(0.00);
            $table->enum('difficulty', ['easy', 'medium', 'hard', 'mixed'])->default('medium');
            $table->text('instructions')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->enum('status', [
                'draft', 'generated', 'review_required', 'published', 'active', 'completed', 'archived'
            ])->default('draft');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'class_id', 'subject_id', 'status'], 'assessments_lookup_idx');
        });

        // 3. Assessment Subjects & Topic Weightage Mapping
        Schema::create('assessment_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('topic_id')->nullable()->constrained('assessment_topics')->nullOnDelete();
            $table->decimal('weightage', 5, 2)->default(100.00);
            $table->timestamps();
        });

        // 4. Assessment Questions (Question Bank)
        Schema::create('assessment_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('topic_id')->nullable()->constrained('assessment_topics')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('question_text');
            $table->enum('question_type', [
                'mcq', 'true_false', 'single_answer', 'multiple_answer', 'short_answer'
            ])->default('mcq');
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->decimal('marks', 5, 2)->default(1.00);
            $table->text('correct_answer')->nullable();
            $table->text('explanation')->nullable();
            $table->string('source_reference')->nullable();
            $table->boolean('is_ai_generated')->default(false);
            $table->enum('review_status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('reviewed_at')->nullable();
            $table->enum('status', ['active', 'archived'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'subject_id', 'topic_id', 'question_type'], 'questions_lookup_idx');
        });

        // 5. Assessment Question Options (for MCQ / True-False / Multiple Answer)
        Schema::create('assessment_question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('assessment_questions')->cascadeOnDelete();
            $table->string('option_key', 10)->default('A'); // A, B, C, D
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->text('explanation')->nullable();
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->index(['question_id', 'is_correct'], 'q_options_idx');
        });

        // 6. Assessment Question Items Pivot (Linking Questions to Assessments)
        Schema::create('assessment_question_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('assessment_questions')->cascadeOnDelete();
            $table->decimal('marks', 5, 2)->default(1.00);
            $table->integer('display_order')->default(1);
            $table->timestamps();

            $table->unique(['assessment_id', 'question_id']);
        });

        // 7. Assessment Assignments
        Schema::create('assessment_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('division_id')->nullable()->constrained('divisions')->nullOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete();
            $table->dateTime('due_at')->nullable();
            $table->integer('max_attempts')->default(1);
            $table->enum('status', ['assigned', 'partially_completed', 'completed', 'expired'])->default('assigned');
            $table->timestamps();

            $table->index(['assessment_id', 'school_id', 'class_id', 'status'], 'assignments_lookup_idx');
        });

        // 8. Assessment Attempts
        Schema::create('assessment_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete();
            $table->foreignId('assignment_id')->nullable()->constrained('assessment_assignments')->nullOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->integer('attempt_number')->default(1);
            $table->dateTime('started_at');
            $table->dateTime('submitted_at')->nullable();
            $table->enum('status', ['in_progress', 'submitted', 'graded', 'expired'])->default('in_progress');
            $table->integer('total_questions')->default(0);
            $table->integer('answered_questions')->default(0);
            $table->decimal('total_marks_obtained', 8, 2)->default(0.00);
            $table->decimal('total_marks_possible', 8, 2)->default(0.00);
            $table->decimal('percentage', 5, 2)->default(0.00);
            $table->boolean('is_passed')->default(false);
            $table->string('ip_address')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['assessment_id', 'student_id', 'school_id', 'status'], 'attempts_lookup_idx');
        });

        // 9. Assessment Answers
        Schema::create('assessment_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('assessment_attempts')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('assessment_questions')->cascadeOnDelete();
            $table->json('selected_option_ids')->nullable();
            $table->text('given_answer_text')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->decimal('marks_obtained', 5, 2)->default(0.00);
            $table->text('feedback')->nullable();
            $table->dateTime('answered_at')->nullable();
            $table->timestamps();

            $table->index(['attempt_id', 'question_id', 'is_correct'], 'answers_lookup_idx');
        });

        // 10. Assessment Results Summary
        Schema::create('assessment_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('assessment_attempts')->cascadeOnDelete();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->decimal('score', 8, 2)->default(0.00);
            $table->decimal('percentage', 5, 2)->default(0.00);
            $table->string('grade', 10)->nullable();
            $table->boolean('is_passed')->default(false);
            $table->integer('rank')->nullable();
            $table->text('teacher_comments')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->timestamps();

            $table->index(['attempt_id', 'assessment_id', 'student_id'], 'results_lookup_idx');
        });

        // 11. Student Topic Performance Aggregation
        Schema::create('student_topic_performance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('topic_id')->constrained('assessment_topics')->cascadeOnDelete();
            $table->integer('total_questions_attempted')->default(0);
            $table->integer('correct_questions_count')->default(0);
            $table->decimal('accuracy_percentage', 5, 2)->default(0.00);
            $table->enum('mastery_level', [
                'needs_improvement', 'developing', 'proficient', 'mastered'
            ])->default('developing');
            $table->dateTime('last_assessed_at')->nullable();
            $table->timestamps();

            $table->unique(['school_id', 'student_id', 'subject_id', 'topic_id'], 'student_topic_unique');
            $table->index(['student_id', 'topic_id', 'mastery_level'], 'stp_lookup_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_topic_performance');
        Schema::dropIfExists('assessment_results');
        Schema::dropIfExists('assessment_answers');
        Schema::dropIfExists('assessment_attempts');
        Schema::dropIfExists('assessment_assignments');
        Schema::dropIfExists('assessment_question_items');
        Schema::dropIfExists('assessment_question_options');
        Schema::dropIfExists('assessment_questions');
        Schema::dropIfExists('assessment_subjects');
        Schema::dropIfExists('assessments');
        Schema::dropIfExists('assessment_topics');
    }
};
