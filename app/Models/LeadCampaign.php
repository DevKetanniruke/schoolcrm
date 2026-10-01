<?php

namespace App\Models;

use App\Core\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadCampaign extends Model
{
    use BelongsToSchool, HasFactory;

    protected $table = 'lead_campaigns';

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'budget',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function leads(): HasMany
    {
        return $this->hasMany(AdmissionLead::class, 'campaign_id');
    }
}
