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

class ParentProfile extends Model
{
    use BelongsToSchool, HasFactory, LogsActivity, SoftDeletes;

    protected $table = 'parents';

    protected $fillable = [
        'school_id',
        'user_id',
        'parent_code',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'phone',
        'alternate_phone',
        'occupation',
        'employer',
        'annual_income',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'status',
        'avatar_path',
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

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'parent_student', 'parent_id', 'student_id')
            ->withPivot([
                'relationship_type',
                'is_primary_contact',
                'is_emergency_contact',
                'has_custody',
                'financial_responsibility',
                'portal_access_enabled',
            ])
            ->withTimestamps();
    }

    public function familyMembers(): HasMany
    {
        return $this->hasMany(FamilyMember::class, 'parent_id');
    }

    public function communications(): HasMany
    {
        return $this->hasMany(ParentCommunication::class, 'parent_id');
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(ParentComplaint::class, 'parent_id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(ParentAppointment::class, 'parent_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(ParentTask::class, 'parent_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ParentNote::class, 'parent_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ParentDocument::class, 'parent_id');
    }
}
