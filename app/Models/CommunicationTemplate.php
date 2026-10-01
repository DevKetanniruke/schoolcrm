<?php

namespace App\Models;

use App\Core\Traits\BelongsToSchool;
use App\Core\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommunicationTemplate extends Model
{
    use BelongsToSchool, HasFactory, LogsActivity;

    protected $table = 'communication_templates';

    protected $fillable = [
        'school_id',
        'title',
        'code',
        'channel',
        'category',
        'subject',
        'content_body',
        'variables_json',
        'status',
        'created_by',
    ];

    protected $casts = [
        'variables_json' => 'array',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function communications(): HasMany
    {
        return $this->hasMany(Communication::class, 'template_id');
    }
}
