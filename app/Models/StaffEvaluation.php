<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffEvaluation extends Model
{
    use HasFactory;

    protected $table = 'staff_evaluations';

    protected $fillable = [
        'staff_id',
        'evaluator_id',
        'review_period',
        'rating',
        'feedback',
        'goals',
        'evaluation_date',
    ];

    protected $casts = [
        'rating' => 'integer',
        'evaluation_date' => 'date',
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(StaffProfile::class, 'staff_id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }
}
