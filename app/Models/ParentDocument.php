<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParentDocument extends Model
{
    use HasFactory;

    protected $table = 'parent_documents';

    protected $fillable = [
        'parent_id',
        'student_id',
        'title',
        'document_type',
        'file_path',
        'file_size',
        'mime_type',
        'uploaded_by',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentProfile::class, 'parent_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
