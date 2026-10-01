<?php

namespace App\Models;

use App\Core\Traits\BelongsToSchool;
use App\Core\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffProfile extends Model
{
    use BelongsToSchool, HasFactory, LogsActivity, SoftDeletes;

    protected $table = 'staff_profiles';

    protected $fillable = [
        'school_id',
        'user_id',
        'department_id',
        'employee_code',
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'date_of_birth',
        'email',
        'phone',
        'alternate_phone',
        'role_type',
        'designation',
        'qualification',
        'experience_years',
        'joining_date',
        'employment_type',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'status',
        'avatar_path',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'joining_date' => 'date',
        'experience_years' => 'integer',
    ];

    public function getFullNameAttribute(): string
    {
        return trim(implode(' ', array_filter([$this->first_name, $this->middle_name, $this->last_name])));
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'teacher_subjects', 'staff_id', 'subject_id')
            ->withPivot(['academic_year_id', 'is_primary'])
            ->withTimestamps();
    }

    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'teacher_class_assignments', 'staff_id', 'class_id')
            ->withPivot(['division_id', 'academic_year_id', 'assignment_role', 'is_class_teacher'])
            ->withTimestamps();
    }

    public function classAssignments(): HasMany
    {
        return $this->hasMany(TeacherClassAssignment::class, 'staff_id');
    }

    public function subjectAssignments(): HasMany
    {
        return $this->hasMany(TeacherSubject::class, 'staff_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(StaffDocument::class, 'staff_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(StaffNote::class, 'staff_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(StaffTask::class, 'staff_id');
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(StaffEvaluation::class, 'staff_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(StaffSchedule::class, 'staff_id');
    }
}
