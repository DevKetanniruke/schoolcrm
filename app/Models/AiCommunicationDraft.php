<?php

namespace App\Models;

use App\Core\Traits\BelongsToSchool;
use App\Core\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiCommunicationDraft extends Model
{
    use BelongsToSchool, HasFactory, LogsActivity;

    protected $table = 'ai_communication_drafts';

    protected $fillable = [
        'school_id',
        'author_id',
        'topic',
        'audience',
        'tone',
        'language',
        'important_info',
        'short_notification',
        'detailed_message',
        'email_subject',
        'email_body',
        'sms_version',
        'whatsapp_version',
        'approval_status',
        'require_approval',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'target_filters_json',
        'communication_id',
    ];

    protected $casts = [
        'require_approval' => 'boolean',
        'approved_at' => 'datetime',
        'target_filters_json' => 'array',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function communication(): BelongsTo
    {
        return $this->belongsTo(Communication::class, 'communication_id');
    }

    public function isApproved(): bool
    {
        return $this->approval_status === 'approved' || $this->approval_status === 'sent';
    }

    public function isPendingApproval(): bool
    {
        return $this->approval_status === 'pending_approval' || $this->approval_status === 'draft';
    }
}
