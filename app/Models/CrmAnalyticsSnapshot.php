<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmAnalyticsSnapshot extends Model
{
    use HasFactory;

    protected $table = 'crm_analytics_snapshots';

    protected $fillable = [
        'school_id',
        'dashboard_type',
        'filter_hash',
        'metrics_json',
        'calculated_at',
    ];

    protected $casts = [
        'metrics_json' => 'array',
        'calculated_at' => 'datetime',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
