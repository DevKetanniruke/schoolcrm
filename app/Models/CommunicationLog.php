<?php

namespace App\Models;

use App\Core\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunicationLog extends Model
{
    use BelongsToSchool, HasFactory;

    protected $table = 'communication_logs';

    protected $fillable = [
        'communication_id',
        'school_id',
        'recipient_type',
        'recipient_id',
        'recipient_email',
        'recipient_phone',
        'channel',
        'provider_name',
        'provider_message_id',
        'delivery_status',
        'failure_reason',
        'retry_count',
        'max_retries',
        'last_attempt_at',
        'delivered_at',
        'read_at',
    ];

    protected $casts = [
        'retry_count' => 'integer',
        'max_retries' => 'integer',
        'last_attempt_at' => 'datetime',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    public function communication(): BelongsTo
    {
        return $this->belongsTo(Communication::class, 'communication_id');
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
