<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'lead_status_histories';

    protected $fillable = [
        'lead_id',
        'changed_by',
        'from_stage',
        'to_stage',
        'reason',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(AdmissionLead::class, 'lead_id');
    }

    public function modifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
