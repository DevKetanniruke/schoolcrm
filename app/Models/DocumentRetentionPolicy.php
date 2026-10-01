<?php

namespace App\Models;

use App\Core\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentRetentionPolicy extends Model
{
    use BelongsToSchool, HasFactory;

    protected $table = 'document_retention_policies';

    protected $fillable = [
        'school_id',
        'document_category',
        'retention_period_months',
        'auto_archive',
        'auto_delete',
    ];

    protected $casts = [
        'retention_period_months' => 'integer',
        'auto_archive' => 'boolean',
        'auto_delete' => 'boolean',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
