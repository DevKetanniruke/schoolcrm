<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'student_status_history';

    protected $fillable = [
        'student_id',
        'changed_by',
        'from_status',
        'to_status',
        'reason',
    ];

    public function getOldStatusAttribute(): ?string
    {
        return $this->from_status;
    }

    public function setOldStatusAttribute($value): void
    {
        $this->attributes['from_status'] = $value;
    }

    public function getNewStatusAttribute(): ?string
    {
        return $this->to_status;
    }

    public function setNewStatusAttribute($value): void
    {
        $this->attributes['to_status'] = $value;
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function modifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

}
