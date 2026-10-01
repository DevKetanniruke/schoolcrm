<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdmissionApplication extends Model
{
    use HasFactory;

    protected $table = 'admission_applications';

    protected $fillable = [
        'lead_id',
        'student_id',
        'school_id',
        'class_id',
        'academic_year_id',
        'application_number',
        'application_stage',
        'submission_date',
        'decision_date',
        'reviewer_notes',
        'fee_status',
    ];

    protected $casts = [
        'submission_date' => 'date',
        'decision_date' => 'date',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(AdmissionLead::class, 'lead_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(LeadDocument::class, 'application_id');
    }
}
