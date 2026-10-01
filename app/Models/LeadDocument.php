<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadDocument extends Model
{
    use HasFactory;

    protected $table = 'lead_documents';

    protected $fillable = [
        'lead_id',
        'application_id',
        'title',
        'document_type',
        'file_path',
        'file_size',
        'mime_type',
        'uploaded_by',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(AdmissionLead::class, 'lead_id');
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(AdmissionApplication::class, 'application_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
