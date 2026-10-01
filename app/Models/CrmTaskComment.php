<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmTaskComment extends Model
{
    use HasFactory;

    protected $table = 'crm_task_comments';

    protected $fillable = [
        'task_id',
        'user_id',
        'comment',
        'attachment_path',
        'attachment_name',
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
