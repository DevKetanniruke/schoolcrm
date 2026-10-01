<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentTopicPerformance extends Model
{
    use HasFactory;

    protected $table = 'student_topic_performance';

    protected $fillable = [
        'school_id',
        'student_id',
        'subject_id',
        'topic_id',
        'total_questions_attempted',
        'correct_questions_count',
        'accuracy_percentage',
        'mastery_level', // needs_improvement, developing, proficient, mastered
        'last_assessed_at',
    ];

    protected $casts = [
        'total_questions_attempted' => 'integer',
        'correct_questions_count' => 'integer',
        'accuracy_percentage' => 'decimal:2',
        'last_assessed_at' => 'datetime',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(AssessmentTopic::class, 'topic_id');
    }
}
