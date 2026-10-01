<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assessment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'class_id',
        'division_id',
        'subject_id',
        'created_by',
        'title',
        'code',
        'type', // weekly_test, practice_test, revision_test
        'week_number',
        'duration_minutes',
        'total_questions',
        'total_marks',
        'passing_marks',
        'difficulty', // easy, medium, hard, mixed
        'instructions',
        'start_date',
        'end_date',
        'status', // draft, generated, review_required, published, active, completed, archived
    ];

    protected $casts = [
        'week_number' => 'integer',
        'duration_minutes' => 'integer',
        'total_questions' => 'integer',
        'total_marks' => 'decimal:2',
        'passing_marks' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assessmentSubjects(): HasMany
    {
        return $this->hasMany(AssessmentSubject::class);
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(AssessmentQuestion::class, 'assessment_question_items', 'assessment_id', 'question_id')
            ->withPivot('marks', 'display_order')
            ->orderBy('pivot_display_order')
            ->withTimestamps();
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(AssessmentAssignment::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(AssessmentAttempt::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(AssessmentResult::class);
    }
}
