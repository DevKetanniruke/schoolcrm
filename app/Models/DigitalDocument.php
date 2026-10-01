<?php

namespace App\Models;

use App\Core\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DigitalDocument extends Model
{
    use BelongsToSchool, HasFactory, SoftDeletes;

    protected $table = 'digital_documents';

    protected $fillable = [
        'school_id',
        'documentable_type',
        'documentable_id',
        'title',
        'category',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'version',
        'expiry_date',
        'verification_status',
        'verified_by',
        'verified_at',
        'verification_notes',
        'retention_until_date',
        'is_confidential',
        'uploaded_by',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'retention_until_date' => 'date',
        'verified_at' => 'datetime',
        'is_confidential' => 'boolean',
        'version' => 'integer',
        'file_size' => 'integer',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class, 'document_id')->orderBy('version_number', 'desc');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(DocumentActivityLog::class, 'document_id')->orderBy('created_at', 'desc');
    }
}
