<?php

namespace App\Models;

use App\Core\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadSource extends Model
{
    use BelongsToSchool, HasFactory;

    protected $table = 'lead_sources';

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'type',
        'status',
    ];

    public function leads(): HasMany
    {
        return $this->hasMany(AdmissionLead::class, 'source_id');
    }
}
