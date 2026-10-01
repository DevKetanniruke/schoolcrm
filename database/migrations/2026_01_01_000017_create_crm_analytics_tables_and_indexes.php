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
        // 1. Student Attendances Table for granular daily/monthly attendance trend analytics
        if (! Schema::hasTable('student_attendances')) {
            Schema::create('student_attendances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
                $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
                $table->foreignId('class_id')->nullable()->constrained('school_classes')->nullOnDelete();
                $table->date('attendance_date');
                $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('present');
                $table->text('remarks')->nullable();
                $table->timestamps();

                $table->index(['school_id', 'attendance_date', 'status']);
                $table->index(['class_id', 'attendance_date']);
            });
        }

        // 2. Pre-computed Analytics Snapshots Table for fast cache pre-warming and background jobs
        if (! Schema::hasTable('crm_analytics_snapshots')) {
            Schema::create('crm_analytics_snapshots', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->default(1)->constrained('schools')->cascadeOnDelete();
                $table->string('dashboard_type'); // executive, admissions, communication, student_engagement, parent_engagement, staff_tasks
                $table->string('filter_hash')->index();
                $table->json('metrics_json');
                $table->timestamp('calculated_at');
                $table->timestamps();

                $table->unique(['school_id', 'dashboard_type', 'filter_hash'], 'uniq_crm_analytics_snapshot');
            });
        }

        // 3. Performance Composite Indexes on core tables for optimized analytics aggregation
        Schema::table('admission_leads', function (Blueprint $table) {
            $table->index(['school_id', 'created_at'], 'idx_leads_school_created');
            $table->index(['school_id', 'stage'], 'idx_leads_school_stage');
        });

        Schema::table('admission_applications', function (Blueprint $table) {
            $table->index(['school_id', 'created_at'], 'idx_apps_school_created');
        });

        Schema::table('communications', function (Blueprint $table) {
            $table->index(['school_id', 'created_at'], 'idx_comm_school_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('communications', function (Blueprint $table) {
            $table->dropIndex('idx_comm_school_created');
        });

        Schema::table('admission_applications', function (Blueprint $table) {
            $table->dropIndex('idx_apps_school_created');
        });

        Schema::table('admission_leads', function (Blueprint $table) {
            $table->dropIndex('idx_leads_school_stage');
            $table->dropIndex('idx_leads_school_created');
        });

        Schema::dropIfExists('crm_analytics_snapshots');
        Schema::dropIfExists('student_attendances');
    }
};
