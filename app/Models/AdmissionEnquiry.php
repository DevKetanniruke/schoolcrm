<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdmissionEnquiry extends Model
{
    use HasFactory;

    protected $table = 'admission_enquiries';

    protected $fillable = [
        'lead_id',
        'answered_by',
        'inquiry_type',
        'question',
        'response',
        'inquiry_date',
    ];

    protected $casts = [
        'inquiry_date' => 'datetime',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(AdmissionLead::class, 'lead_id');
    }

    public function answerer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'answered_by');
    }

    public function answeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'answered_by');
    }
}
