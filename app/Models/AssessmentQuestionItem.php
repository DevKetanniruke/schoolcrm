<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentQuestionItem extends Model
{
    use HasFactory;

    protected $table = 'assessment_question_items';

    protected $fillable = [
        'assessment_id',
        'question_id',
        'marks',
        'display_order',
    ];

    protected $casts = [
        'marks' => 'decimal:2',
        'display_order' => 'integer',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(AssessmentQuestion::class);
    }
}
