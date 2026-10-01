<?php

namespace App\Models;

use App\Core\Traits\BelongsToSchool;
use App\Core\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmAutomationRule extends Model
{
    use BelongsToSchool, HasFactory, LogsActivity;

    protected $table = 'crm_automation_rules';

    protected $fillable = [
        'school_id',
        'name',
        'trigger_event',
        'conditions_json',
        'actions_json',
        'status',
        'created_by',
    ];

    protected $casts = [
        'conditions_json' => 'array',
        'actions_json' => 'array',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(CrmAutomationLog::class, 'rule_id');
    }
}
