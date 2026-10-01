<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssessmentQuestion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'school_id',
        'subject_id',
        'topic_id',
        'created_by',
        'question_text',
        'question_type', // mcq, true_false, single_answer, multiple_answer, short_answer
        'difficulty',    // easy, medium, hard
        'marks',
        'correct_answer',
        'explanation',
        'source_reference',
        'is_ai_generated',
        'review_status',  // pending, approved, rejected
        'reviewed_by',
        'reviewed_at',
        'status',
    ];

    protected $casts = [
        'marks' => 'decimal:2',
        'is_ai_generated' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(AssessmentTopic::class, 'topic_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function options(): HasMany
    {
        return $this->hasMany(AssessmentQuestionOption::class, 'question_id')->orderBy('display_order');
    }

    public function assessments(): BelongsToMany
    {
        return $this->belongsToMany(Assessment::class, 'assessment_question_items', 'question_id', 'assessment_id')
            ->withPivot('marks', 'display_order')
            ->withTimestamps();
    }
}
