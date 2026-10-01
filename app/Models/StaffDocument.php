<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffDocument extends Model
{
    use HasFactory;

    protected $table = 'staff_documents';

    protected $fillable = [
        'staff_id',
        'title',
        'document_type',
        'file_path',
        'file_size',
        'mime_type',
        'verification_status',
        'expiry_date',
        'uploaded_by',
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(StaffProfile::class, 'staff_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
