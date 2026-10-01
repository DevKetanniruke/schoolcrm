<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_engagement_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            
            // Configurable Engagement Indicators
            $table->float('attendance_percentage')->default(100.0);
            $table->unsignedInteger('attendance_days_present')->default(0);
            $table->unsignedInteger('attendance_days_absent')->default(0);
            $table->string('attendance_trend')->default('stable'); // improving, stable, declining
            
            $table->float('assignment_completion_rate')->default(100.0);
            $table->unsignedInteger('assignment_submissions_count')->default(0);
            $table->unsignedInteger('assignment_pending_count')->default(0);
            
            $table->float('communication_response_rate')->default(100.0);
            $table->unsignedInteger('communication_interactions_count')->default(0);
            
            $table->float('academic_gpa_score')->default(85.0);
            $table->string('academic_trend')->default('stable'); // improving, stable, declining
            
            $table->float('participation_score')->default(80.0);
            $table->unsignedInteger('extracurricular_activities_count')->default(0);
            
            $table->unsignedInteger('support_requests_count')->default(0);
            $table->unsignedInteger('open_support_tickets_count')->default(0);
            
            // Computed Engagement Metric & Neutral Non-Stigmatizing Status
            $table->float('engagement_score')->default(85.0);
            $table->string('overall_status')->default('normal'); // normal, needs_attention, followup_recommended
            
            // Granular Underlying Evidence & Historical Breakdown
            $table->json('underlying_data_json')->nullable();
            $table->timestamp('last_evaluated_at')->nullable();
            
            $table->timestamps();

            $table->unique(['school_id', 'student_id']);
            $table->index(['school_id', 'overall_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_engagement_records');
    }
};
