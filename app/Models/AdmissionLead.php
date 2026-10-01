<?php

namespace App\Models;

use App\Core\Traits\BelongsToSchool;
use App\Core\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdmissionLead extends Model
{
    use BelongsToSchool, HasFactory, LogsActivity, SoftDeletes;

    protected $table = 'admission_leads';

    protected $fillable = [
        'school_id',
        'source_id',
        'campaign_id',
        'assigned_counselor_id',
        'grade_applying_for_id',
        'academic_year_id',
        'lead_code',
        'parent_name',
        'parent_email',
        'parent_phone',
        'alternate_phone',
        'student_name',
        'student_dob',
        'gender',
        'address',
        'city',
        'state',
        'stage',
        'status',
        'score',
        'loss_reason',
        'notes',
    ];

    protected $casts = [
        'student_dob' => 'date',
        'score' => 'integer',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(LeadSource::class, 'source_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(LeadCampaign::class, 'campaign_id');
    }

    public function counselor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_counselor_id');
    }

    public function assignedCounselor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_counselor_id');
    }

    public function gradeApplyingFor(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'grade_applying_for_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function enquiries(): HasMany
    {
        return $this->hasMany(AdmissionEnquiry::class, 'lead_id');
    }

    public function application(): HasOne
    {
        return $this->hasOne(AdmissionApplication::class, 'lead_id');
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(LeadFollowUp::class, 'lead_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(LeadDocument::class, 'lead_id');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(LeadStatusHistory::class, 'lead_id')->orderBy('created_at', 'desc');
    }
}
