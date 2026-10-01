<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentActivityLog extends Model
{
    use HasFactory;

    protected $table = 'document_activity_logs';

    protected $fillable = [
        'document_id',
        'user_id',
        'activity_type',
        'description',
        'ip_address',
        'user_agent',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(DigitalDocument::class, 'document_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
