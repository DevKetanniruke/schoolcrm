<?php

namespace App\Models;

use App\Core\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmAutomationLog extends Model
{
    use BelongsToSchool, HasFactory;

    protected $table = 'crm_automation_logs';

    protected $fillable = [
        'school_id',
        'rule_id',
        'trigger_event',
        'entity_type',
        'entity_id',
        'execution_status',
        'actions_executed_json',
        'error_message',
        'executed_at',
    ];

    protected $casts = [
        'actions_executed_json' => 'array',
        'executed_at' => 'datetime',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(CrmAutomationRule::class, 'rule_id');
    }
}
