<?php

namespace App\Models;

use App\Core\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    use BelongsToSchool, HasFactory;

    protected $table = 'notification_preferences';

    protected $fillable = [
        'school_id',
        'user_id',
        'entity_type',
        'entity_id',
        'channel_in_app',
        'channel_email',
        'channel_sms',
        'channel_whatsapp',
        'channel_push',
        'category_academic',
        'category_attendance',
        'category_fees',
        'category_events',
        'category_emergency',
    ];

    protected $casts = [
        'channel_in_app' => 'boolean',
        'channel_email' => 'boolean',
        'channel_sms' => 'boolean',
        'channel_whatsapp' => 'boolean',
        'channel_push' => 'boolean',
        'category_academic' => 'boolean',
        'category_attendance' => 'boolean',
        'category_fees' => 'boolean',
        'category_events' => 'boolean',
        'category_emergency' => 'boolean',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
