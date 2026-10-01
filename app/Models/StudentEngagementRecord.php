<?php

namespace App\Models;

use App\Core\Traits\BelongsToSchool;
use App\Core\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentEngagementRecord extends Model
{
    use BelongsToSchool, HasFactory, LogsActivity;

    protected $table = 'student_engagement_records';

    protected $fillable = [
        'school_id',
        'student_id',
        'attendance_percentage',
        'attendance_days_present',
        'attendance_days_absent',
        'attendance_trend',
        'assignment_completion_rate',
        'assignment_submissions_count',
        'assignment_pending_count',
        'communication_response_rate',
        'communication_interactions_count',
        'academic_gpa_score',
        'academic_trend',
        'participation_score',
        'extracurricular_activities_count',
        'support_requests_count',
        'open_support_tickets_count',
        'engagement_score',
        'overall_status',
        'underlying_data_json',
        'last_evaluated_at',
    ];

    protected $casts = [
        'attendance_percentage' => 'float',
        'assignment_completion_rate' => 'float',
        'communication_response_rate' => 'float',
        'academic_gpa_score' => 'float',
        'participation_score' => 'float',
        'engagement_score' => 'float',
        'underlying_data_json' => 'array',
        'last_evaluated_at' => 'datetime',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get neutral non-stigmatizing status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->overall_status) {
            'needs_attention' => 'Needs Attention',
            'followup_recommended' => 'Follow-up Recommended',
            default => 'Normal',
        };
    }
}
