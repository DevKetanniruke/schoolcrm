<?php

namespace App\Models;

use App\Core\Traits\BelongsToSchool;
use App\Core\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campus extends Model
{
    use HasFactory, SoftDeletes, LogsActivity, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'location',
        'phone',
        'status',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
