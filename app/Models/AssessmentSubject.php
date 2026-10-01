<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'subject_id',
        'topic_id',
        'weightage',
    ];

    protected $casts = [
        'weightage' => 'decimal:2',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
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
