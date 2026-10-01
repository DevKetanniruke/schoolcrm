<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmTaskActivity extends Model
{
    use HasFactory;

    protected $table = 'crm_task_activities';

    protected $fillable = [
        'task_id',
        'user_id',
        'activity_type',
        'description',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(CrmTask::class, 'task_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
