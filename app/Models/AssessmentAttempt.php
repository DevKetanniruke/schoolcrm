<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssessmentAttempt extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'assessment_id',
        'assignment_id',
        'student_id',
        'school_id',
        'attempt_number',
        'started_at',
        'submitted_at',
        'status', // in_progress, submitted, graded, expired
        'total_questions',
        'answered_questions',
        'total_marks_obtained',
        'total_marks_possible',
        'percentage',
        'is_passed',
        'ip_address',
    ];

    protected $casts = [
        'attempt_number' => 'integer',
        'total_questions' => 'integer',
        'answered_questions' => 'integer',
        'total_marks_obtained' => 'decimal:2',
        'total_marks_possible' => 'decimal:2',
        'percentage' => 'decimal:2',
        'is_passed' => 'boolean',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(AssessmentAssignment::class, 'assignment_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AssessmentAnswer::class, 'attempt_id');
    }

    public function result(): HasOne
    {
        return $this->hasOne(AssessmentResult::class, 'attempt_id');
    }
}
