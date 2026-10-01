<?php

namespace App\Models;

use App\Core\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParentAppointment extends Model
{
    use BelongsToSchool, HasFactory;

    protected $table = 'parent_appointments';

    protected $fillable = [
        'school_id',
        'parent_id',
        'student_id',
        'staff_id',
        'type',
        'title',
        'scheduled_at',
        'duration_minutes',
        'location_or_link',
        'status',
        'meeting_notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentProfile::class, 'parent_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
