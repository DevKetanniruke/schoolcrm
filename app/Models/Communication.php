<?php

namespace App\Models;

use App\Core\Traits\BelongsToSchool;
use App\Core\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Communication extends Model
{
    use BelongsToSchool, HasFactory, LogsActivity;

    protected $table = 'communications';

    protected $fillable = [
        'school_id',
        'sender_id',
        'template_id',
        'title',
        'channel',
        'recipient_type',
        'target_filters_json',
        'subject',
        'message_body',
        'payload_data_json',
        'scheduled_at',
        'status',
        'total_recipients',
        'success_count',
        'failure_count',
        'sent_at',
    ];

    protected $casts = [
        'target_filters_json' => 'array',
        'payload_data_json' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(CommunicationTemplate::class, 'template_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(CommunicationLog::class, 'communication_id');
    }
}
